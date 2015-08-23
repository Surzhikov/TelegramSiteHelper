<?php

set_time_limit(60);
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);	//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true); // Игнорируем abort со стороны пользователя


require_once("tbConfig.php"); // Подключаем конфиг


$needParams=array('tbChatHash','lastMessageId'); // Обязательные входные параметры API
if(isset($_GET) OR isset($_POST) OR isset($_COOKIE)){$params=array_merge ($_COOKIE,$_POST, $_GET);}  // POST и GET сливаем воедино
foreach($needParams as $v){
if(!isset($params[$v])){echo  json_encode(array("status"=>"error", "error"=>"NEEDS_INPUT_PARAMS", "needParam"=>$v)); exit();}} //Проверяем, все ли необходимые параметры переданы..
//-----------------------------------------  API


if($params['lastMessageId']==0){


			require('tbDatabase.php');
			$db=tbDatabase();
			
			$sth=$db->prepare("SELECT chId FROM tbChats WHERE chHash=:chHash");
			$sth->execute(array(":chHash"=>$params['tbChatHash']));
			$a=$sth->fetch();
			$chId=$a['chId'];
			
			
			$sth=$db->prepare("SELECT msgId, msgTime, msgFrom, msgText FROM tbMessages WHERE msgChatId=:msgChatId AND msgId>:msgId ORDER BY msgTime");
			$sth->execute(array(":msgChatId"=>$chId, ":msgId"=>$params['lastMessageId']));
			$msgs=array();
			while($answer=$sth->fetch()){
					$answer['msgTime']=date("H:i:s",$answer['msgTime']);
					$msgs[]=$answer;
			}

		
			echo json_encode(array("status"=>"ok","msgs"=>$msgs));
			exit();


}else{

		while(true){
		$file=$tbRootDir."/chatUpdates/".$params['tbChatHash'].".manager";
		if(is_file($file)){

			require('tbDatabase.php');
			$db=tbDatabase();
			
			$sth=$db->prepare("SELECT chId FROM tbChats WHERE chHash=:chHash");
			$sth->execute(array(":chHash"=>$params['tbChatHash']));
			$a=$sth->fetch();
			$chId=$a['chId'];


			$sth=$db->prepare("SELECT  msgId, msgTime, msgFrom, msgText FROM tbMessages WHERE msgChatId=:msgChatId AND msgFrom=:msgFrom AND msgId>:msgId ORDER BY msgTime");
			$sth->execute(array(":msgChatId"=>$chId, ":msgFrom"=>"m",":msgId"=>$params['lastMessageId']));
			$msgs=array();
			while($answer=$sth->fetch()){
					$answer['msgTime']=date("H:i:s",$answer['msgTime']);
					$msgs[]=$answer;
			}

		  unlink($file);
			echo json_encode(array("status"=>"ok","msgs"=>$msgs));
			exit();
		}

		usleep(5000);
		}

}






?>