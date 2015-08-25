<?php
//>> sudo crontab -e
//* * * * * /usr/bin/php5 /var/www/***/server.php

set_time_limit(0);
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);	//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true); // Игнорируем abort со стороны пользователя


// Подключаем конфигурационный файл
// Require the configuration
require('tbConfig.php');

// Пишем лог
// Start writing log-file
$fLog=fopen($tbRootDir."/tbServer.log",'a');
fwrite($fLog, date("d.m.Y H:i:s")." — starting server... ");

 
// Открываем и пытаемся залочить файл running, тем самым даем жить только одному процессу
// Open and try to Lock «running» file, so only one process will be alive
$fR=fopen($tbRootDir."/running",'w');
$fl=flock($fR, LOCK_EX | LOCK_NB);



if($fl){
		fwrite($fLog," Ok!\r\n");
		require('tbClass.php');
		
		// Новый экземпляр телеграм бота
		$tg = new telegramBot($tbAPIToken);
		
		// Функция подключения к БД
		require('tbDatabase.php');
		
		
		/*  Сервер запущен и работает  */
		/*  Server is working now  */
		while(true){
				
				if(is_file($tbRootDir."/stopserver")){
						unlink($tbRootDir."/stopserver");
						echo"\r\nСервер остановлен по команде stopserver!";
						fwrite($fLog, date("d.m.Y H:i:s")." — Сервер остановлен по команде stopserver!\r\n");
						exit();
				}
				
				
				
				// В обычном файле мы будем хранить ID последнего обновления
				$fileOfLastUpdate=$tbRootDir."/lastUpdateId";
				if(is_file($fileOfLastUpdate)){
						$lastUpdateId=intval(file_get_contents($fileOfLastUpdate))+1;
				}else{
						file_put_contents($fileOfLastUpdate,"0");
						$lastUpdateId=0;
				}
		
				// Делаем long-poll запрос 60 секунд к серверу телеграм
				$updates = $tg->pollUpdates($lastUpdateId,60);
		
				// Обрабатываем Updates, если они есть.
				foreach($updates['result'] as $data){

			
						$updateId = $updates['result'][count($updates['result']) - 1]['update_id'];
						$message = $data['message']['text'];
						$chatId = $data['message']['chat']['id'];
						
						
						
						// Эта команда для дебага, чтобы не перезапускать apache каждый раз, когда нужно перезапустить tbServer.php
						// Отправляешь боту команду /reboot и сервер останавливается.
						// В рабочей версии - нужно закоментить этот IF(){}
						if($message=="/reboot"){
										$tg->sendMessage($chatId, 'Сервер будет перезапущен в течение минуты!');
										file_put_contents($fileOfLastUpdate,$updateId);
										exit();
						}
						
						$db=tbDatabase();

						// Проверим, авторизован ли пользователь
						$sth=$db->prepare("SELECT count(*) as count FROM tbManagers WHERE mBotChatId=:mBotChatId");
						$sth->execute(array(":mBotChatId"=>$chatId));
						$answer=$sth->fetch();
						if($answer["count"]>0){
								$isAuth=true;
						}else{
								$isAuth=false;
						}
						
						
						if($isAuth===false){
						
								if($message==$tbManagerPassword){
										$managerName=$data['message']['from']['first_name']." ".$data['message']['from']['last_name'];
										
										$sth=$db->prepare("INSERT INTO tbManagers (mName, mBotChatId, mSiteChatId, mStatus) VALUES (:mName, :mBotChatId, :mSiteChatId, :mStatus);");
										$sth->execute(array(":mName"=>$managerName, ":mBotChatId"=>$chatId, ":mSiteChatId"=>null, ":mStatus"=>1));
										$tg->sendMessage($chatId, 'Спасибо! Вы авторизованы! Теперь Вы будете получать сообщения от пользователей в этот чат.');
																				
								}else{
										
										$tg->sendMessage($chatId, 'Необходимо ввести пароль для авторизации..');	
										
								}
						
						
						}else{
						

								if($message=="/offline"){
								
										$sth=$db->prepare("UPDATE tbManagers SET mStatus=:mStatus, mSiteChatId=:mSiteChatId WHERE mBotChatId=:mBotChatId");
										$sth->execute(array(":mStatus"=>0,":mSiteChatId"=>null, ":mBotChatId"=>$chatId));
										$tg->sendMessage($chatId, "Теперь Вы не будете получать сообщения от пользователей.\r\n Чтобы вернуться в онлайн введите команду /online");	
									
								}elseif($message=="/online"){
									
										$sth=$db->prepare("UPDATE tbManagers SET mStatus=:mStatus WHERE mBotChatId=:mBotChatId");
										$sth->execute(array(":mStatus"=>1, ":mBotChatId"=>$chatId));
										$tg->sendMessage($chatId, "Вы снова будете получать сообщения от пользователей!");	
									
								}elseif($message=="/exit"){
									
										$sth=$db->prepare("DELETE FROM tbManagers WHERE mBotChatId=:mBotChatId;");
										$sth->execute(array(":mBotChatId"=>$chatId));
										$tg->sendMessage($chatId, "Вы вышли из системы. Для входа необходимо ввести пароль.");	
									
								}elseif(mb_substr($message,0,6)=="/chat_"){
										
										$chatNum=mb_substr($message,6);
										
										$sth=$db->prepare("SELECT count(*) as count FROM tbChats LEFT JOIN tbManagers ON tbManagers.mId=tbChats.chManager WHERE tbChats.chId=:chId AND 	tbManagers.mBotChatId=:mBotChatId;");
										$sth->execute(array(":chId"=>$chatNum, ":mBotChatId"=>$chatId));
										$answer=$sth->fetch();
										
										if($answer['count']!=0){
												$sth=$db->prepare("UPDATE tbManagers SET mSiteChatId=:mSiteChatId WHERE mBotChatId=:mBotChatId");
												$sth->execute(array(":mSiteChatId"=>$chatNum, ":mBotChatId"=>$chatId));
												$tg->sendMessage($chatId, "Теперь все ваши сообщения направляются в чат ".$chatNum." (/chat_".$chatNum.")");	
										}else{
												$tg->sendMessage($chatId, "Чат ".$chatNum." недосутпен!");	
										}
										
										
								}elseif(mb_substr($message,0,9)=="/history_"){
										
										$chatNum=mb_substr($message,9);
										
										$sth=$db->prepare("SELECT count(*) as count FROM tbChats LEFT JOIN tbManagers ON tbManagers.mId=tbChats.chManager WHERE tbChats.chId=:chId AND 	tbManagers.mBotChatId=:mBotChatId;");
										$sth->execute(array(":chId"=>$chatNum, ":mBotChatId"=>$chatId));
										$answer=$sth->fetch();
									
										
										if($answer['count']!=0){
												$sth=$db->prepare("SELECT msgFrom,msgTime,msgText FROM tbMessages WHERE msgChatId=:msgChatId ORDER BY msgTime");
												$sth->execute(array(":msgChatId"=>$chatNum));
												$dialog="";
												while($a=$sth->fetch()){
												if($a['msgFrom']=="m"){$from="Менеджер";}else{$from="Клиент";}
												$dialog.="— ".$from." "."(".date("d.m.Y H:i:s",$a['msgTime']).")\r\n".$a['msgText']."\r\n\r\n";
												}
												$tg->sendMessage($chatId, "Полная переписка в чате ".$chatNum." (/chat_".$chatNum.")\r\n \r\n".$dialog."\r\n\r\n Для перехода в этот чат, введите /chat_".$chatNum);
												
										}else{
												$tg->sendMessage($chatId, "Чат ".$chatNum." ведет другой менеджер!");	
										}
								}else{
									
									
										$sth=$db->prepare("SELECT tbManagers.mSiteChatId, tbChats.chHash FROM tbManagers LEFT JOIN tbChats ON tbChats.chId=tbManagers.mSiteChatId WHERE mBotChatId=:mBotChatId");
										$sth->execute(array(":mBotChatId"=>$chatId));
										$answer=$sth->fetch();
										
										if($answer['mSiteChatId']!=null){
																								
										
												
												$sth=$db->prepare("INSERT INTO tbMessages (msgChatId, msgFrom, msgTime, msgText) VALUES (:msgChatId, :msgFrom, :msgTime, :msgText)");
												$sth->execute(array(":msgChatId"=>$answer['mSiteChatId'], ":msgFrom"=>"m", ":msgTime"=>time(), ":msgText"=>$message));
												
												$file=$tbRootDir."/chatUpdates/".$answer['chHash'].".manager";
												$f=fopen($file,"w");
												fwrite($f,time());
												fclose($f);

											
										}else{
												$tg->sendMessage($chatId, "Чтобы отправить сообщение сперва надо выбрать чат! ");	
										}
								}
						
						
						
						
						
						
						
						}
					
					
					
					
					
					
					$db=null;
					// Записываем ID последнего обновления, полученного у телеграм-бота
					// Write down last update id from telegram bot
					file_put_contents($fileOfLastUpdate,$updateId);
					
				}
				/*  foreach End  */
		
		
		
		
		}
		/*  End  */
}else{
	fwrite($fLog," Error! Server is already working. Stopped!\r\n");
	echo"Error! Server is already working. Stopped";
	exit();
}
?>