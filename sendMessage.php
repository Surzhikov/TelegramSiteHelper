<?

$start=time();

// Connect to database
require('TelegramDBConnect.php');
$db=connectDB();


// Create new Bot Object
require('TelegramBotClass.php');
require('TelegramBotConfig.php');
$bot = new TelegramBot($tokenAPI);


// Message & chatId from web-site
$wsMessage=$_POST['wsMessage'];


if($_COOKIE['wsChatHash']!=null){
		$wsChatHash=$_COOKIE['wsChatHash'];
		$sth=$db->prepare("SELECT wsChatId FROM wsChat WHERE wsChatHash=:wsChatHash");
		$sth->execute(array(":wsChatHash"=>$wsChatHash));
		$a=$sth->fetch();
		$wsChatId=$a['wsChatId'];
}else{
		$sth=$db->prepare("SELECT MAX(wsChatId) as max FROM wsChat");
		$sth->execute();
		$a=$sth->fetch();
		$wsChatId=intval($a['max'])+1;
		$wsChatHash=uniqid();
		$sth=$db->prepare("INSERT INTO wsChat (wsChatId, wsChatHash) VALUES (:wsChatId, :wsChatHash)");
		$sth->execute(array(":wsChatId"=>$wsChatId, ":wsChatHash"=>$wsChatHash));
		setcookie("wsChatHash", $wsChatHash, time()+(60*60*24));
}





$msg=$wsMessage."\r\n»» Для перехода в чат нажмите /chat_".$wsChatId;




$sth=$db->prepare("SELECT chatId FROM managers WHERE wsChat=:wsChat");
$sth->execute(array(":wsChat"=>$wsChatId));
$a=$sth->fetch();

if($a['chatId']==null){

	$sth=$db->prepare("SELECT chatId FROM managers");
	$sth->execute();
	$managerChat=array();
	while($a=$sth->fetch()){
		$managerChat[]=$a['chatId'];
	}
	
	$randomManagerChat=$managerChat[rand(0,count($managerChat)-1)];
	$sth=$db->prepare("UPDATE managers SET wsChat=:wsChat WHERE chatId=:chatId");
	$sth->execute(array(":wsChat"=>$wsChatId,":chatId"=>$randomManagerChat));
	

	
	$msgSent=$bot->SendMessage($randomManagerChat,$msg);
	
}else{

	$msgSent=$bot->SendMessage($a['chatId'],$msg);


}


	$dbname="chatsDBs/".$wsChatHash.".db";


	// Connect to database
	require('chatDBConnect.php');
	$db=chatDBConnect($dbname);
	
 
		 
	$sth=$db->prepare("INSERT INTO messages (mText, mCreator, mDate) VALUES (:mText, :mCreator, :mDate)");
	$sth->execute(array(":mText"=>$wsMessage, ":mCreator"=>"c", ":mDate"=>time()));
	
 
 
echo json_encode(array("status"=>"ok", "wsChatHash"=>$wsChatHash));


