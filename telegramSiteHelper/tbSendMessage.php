<?php

require_once("tbConfig.php"); // Подключаем конфиг


$needParams=array('tbChatHash','tbMessage'); // Обязательные входные параметры API
if(isset($_GET) OR isset($_POST) OR isset($_COOKIE)){$params=array_merge ($_COOKIE,$_POST, $_GET);}  // POST и GET сливаем воедино
foreach($needParams as $v){
if(!isset($params[$v])){echo  json_encode(array("status"=>"error", "error"=>"NEEDS_INPUT_PARAMS", "needParam"=>$v)); exit();}} //Проверяем, все ли необходимые параметры переданы..
//-----------------------------------------  API

require_once("tbDatabase.php");
$db=tbDatabase();

	
	
if($params['tbChatHash']!=null){

		$sth=$db->prepare("SELECT tbChats.chId, tbManagers.mBotChatId, tbManagers.mName FROM tbChats LEFT JOIN tbManagers ON tbManagers.mId=tbChats.chManager  WHERE tbChats.chHash=:chHash");
		$sth->execute(array(":chHash"=>$params['tbChatHash']));
		$answer=$sth->fetch();
		$chatId=$answer['mBotChatId'];
		$chId=$answer['chId'];
		$managerName=$answer['mName'];
		$chHash=$params['tbChatHash'];
		
		if($chatId==null){
		
		
		}
		
}

if($params['tbChatHash']==null OR $chatId==null){

	$sth=$db->prepare("SELECT mId, mName, mBotChatId FROM tbManagers WHERE mStatus=:mStatus");
	$sth->execute(array(":mStatus"=>1));
	$managers=array();
	
	while($a=$sth->fetch()){
		$managers[]=$a;
	}

	
	if(count($managers)>0){
			$r=rand(0,(count($managers)-1));
			$managerId=$managers[$r]['mId'];
			$chatId=$managers[$r]['mBotChatId'];
			$managerName=$managers[$r]['mName'];
	}else{
			echo json_encode(array("status"=>"error", "error"=>"NO_MANAGERS")); exit();
	}
	if($params['tbChatHash']==null){
			$chHash=uniqid("chat_");
	}else{
			$chHash=$params['tbChatHash'];
	}
	$sth=$db->prepare("INSERT INTO tbChats (chHash, chManager) VALUES (:chHash, :chManager)");
	$sth->execute(array(":chHash"=>$chHash, ":chManager"=>$managerId));
	$chId=$db->lastInsertId();
}


require_once("tbClass.php");
		
// Новый экземпляр телеграм бота
$tg = new telegramBot($tbAPIToken);
$tg->sendMessage($chatId, $params['tbMessage'] ."\r\n\r\nДля ответа перейти в чат — /chat_".$chId."\r\nПосмотреть историю — /history_".$chId."");


$sth=$db->prepare("INSERT INTO tbMessages (msgChatId, msgFrom, msgTime, msgText) VALUES (:msgChatId, :msgFrom, :msgTime, :msgText)");
$sth->execute(array(":msgChatId"=>$chId, ":msgFrom"=>"c", ":msgTime"=>time(), ":msgText"=>$params['tbMessage']));
$mId=$db->lastInsertId();


	echo json_encode(array("status"=>"ok", "managerName"=>$managerName,"tbChatHash"=>$chHash, "lastMessageId"=>$mId, "lastMessageDate"=>date("H:i:s"))); exit();

?>