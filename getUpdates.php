<?
set_time_limit(0);
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);	//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true); // Игнорируем закрытие окна браузера с сервером


// Connect to database
require('TelegramDBConnect.php');
$db=connectDB();

// Create new Bot Object
require('TelegramBotClass.php');
require('TelegramBotConfig.php');
$bot = new TelegramBot($tokenAPI);

$f=fopen("running",'w');
$fl=flock($f, LOCK_EX | LOCK_NB);

if($fl){
//	print("I`m the only running process...<hr>");
	
	

		while(true){
		// Get lastUpdate ID

		$sth=$db->prepare("SELECT lastUpdate FROM lastUpdate");
		$sth->execute();
		$a=$sth->fetch();
		if($a['lastUpdate']==null){
				$updateIdFrom=0;
		}else{
				$updateIdFrom=intval($a['lastUpdate'])+1;
		}






				// Get Updates with 30 sec Long-Poll
				$updates=$bot->GetUpdates($updateIdFrom,null,30)->result;


				foreach($updates as $up=>$update){

					$updateId = $update->update_id;
					$chatId =  $update->message->chat->id;
					$message = $update->message->text;
					

				// echo $updateId."<br>";

					
					if(mb_substr($message,0,11)=="/newmanager"){
							
							$ch=explode(" ",$message);
							$password=intval($ch[1]);
							
							if($password==$managersPassword){
					
							$firstName=$update->message->chat->first_name;
							$lastName=$update->message->chat->last_name;
							
							// Если менеджер повторно регистрируется в системе командой /newmanager_ , сперва удалим его
							$sth=$db->prepare("DELETE FROM managers WHERE chatId=:chatId");
							$sth->execute(array(":chatId"=>$chatId));	
							
							// Добавляем менеджера в базу
							$sth=$db->prepare("INSERT INTO managers (chatId, name) VALUES (:chatId, :name)");
							$sth->execute(array(":chatId"=>$chatId, ":name"=>$firstName." ".$lastName));
							
							// И шлем ему привет
							$msgSent=$bot->SendMessage($chatId,"Здравствуйте, ".$firstName."! \r\nВ этом чате вы будете получать сообщения с сайта! Приятной работы!"); // Success!
							}else{
							
							$msgSent=$bot->SendMessage($chatId,"Неверный пароль для добавления нового менеджера!"); // Success!
							
							
							}
					
					}else	if($message=="/exit"){

									$sth=$db->prepare("UPDATE managers SET wsChat=:wsChat WHERE chatId=:chatId");
									$sth->execute(array(":chatId"=>$chatId, ":wsChat"=>null));
									$firstName=$update->message->chat->first_name;
									$msgSent=$bot->SendMessage($chatId, $firstName.", Вы вышли из чата");
						
					
					}else	if(mb_substr($message,0,6)=="/chat_"){
							
							//Set Manager to chat
							$ch=explode("/chat_",$message);
							$wsChat=intval($ch[1]);
							
							if($wsChat!=null && $wsChat!=0){
									$sth=$db->prepare("UPDATE managers SET wsChat=:wsChat WHERE chatId=:chatId");
									$sth->execute(array(":chatId"=>$chatId, ":wsChat"=>$wsChat));
									$firstName=$update->message->chat->first_name;
									$msgSent=$bot->SendMessage($chatId, $firstName.", теперь все Ваши сообщения направляются в чат #".$wsChat);
							}else{
									$msgSent=$bot->SendMessage($chatId, $firstName.", неверный номер чата!"); // Success!
							}
					
					}else{
							
							$sth=$db->prepare("SELECT count(*) as count FROM managers WHERE chatId=:chatId");
							$sth->execute(array(":chatId"=>$chatId));
							$a=$sth->fetch();
							if($a['count']==0){
									$msgSent=$bot->SendMessage($chatId, "Вы не зарегистрированны как менеджер! Введите команду \r\n/newmanager пароль\r\nдля регистрации!"); // Success!
							}else{
							
									$sth=$db->prepare("SELECT wsChat FROM managers WHERE chatId=:chatId");
									$sth->execute(array(":chatId"=>$chatId));
									$a=$sth->fetch();
									$wsChatId=$a['wsChat'];
									if($a['wsChat']==null){
											$firstName=$update->message->chat->first_name;
											$msgSent=$bot->SendMessage($chatId, $firstName.", прежде чем отправлять сообщения, надо выбрать, в какой чат их отправлять с помощью команды\r\n/chat_#чата\r\n"); // Success!
									}else{
									
											$sth=$db->prepare("SELECT wsChatHash FROM wsChat WHERE wsChatId=:wsChatId");
											$sth->execute(array(":wsChatId"=>$wsChatId));
											$a=$sth->fetch();
											$wsChatHash=$a['wsChatHash'];
											
																					
											$dbname="chatsDBs/".$wsChatHash.".db";


											// Connect to database
											require_once('chatDBConnect.php');
											$dbChat=chatDBConnect($dbname);

											$sth=$dbChat->prepare("INSERT INTO messages (mText, mCreator, mDate) VALUES (:mText, :mCreator, :mDate)");
											$sth->execute(array(":mText"=>$message, ":mCreator"=>"m", ":mDate"=>time()));
											
											$dbChat=null;
											
									}
									
							}
					
					}
					
					
					
					
					
				}





				if(isset($updateId)){
						$db->exec("DELETE FROM lastUpdate");
						$sth=$db->prepare("INSERT INTO lastUpdate (lastUpdate) VALUES (:lastUpdate)");
						$sth->execute(array(":lastUpdate"=>$updateId));
				}	

		}


}else{
	echo"Одновременно может быть запущен только один процесс.";
	exit();

}

//$q=$BotNow->Status();
//$q=$BotNow->GetUpdates()->result;
