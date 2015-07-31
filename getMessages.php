<?
header('Content-Type: text/html; charset=utf-8'); // Заголовки
header('Connection: keep-alive'); 
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(35);	//Время выполнения скрипта 35 сек
$startTime=time();

// ID Чата пользователя
if(array_key_exists("wsChatHash",$_COOKIE)){
		if($_COOKIE['wsChatHash']!=null){
				$wsChatHash=$_COOKIE['wsChatHash'];
		}else{
				echo json_encode(array("status"=>"error", "error"=>"no_wsChatHash"));
				exit();
		}
}else{
		echo json_encode(array("status"=>"error", "error"=>"no_wsChatHash"));
		exit();
}




// Connect to database and check wsChatHash
require('TelegramDBConnect.php');
$db=connectDB();
$sth=$db->prepare("SELECT count(*) as count FROM wsChat WHERE wsChatHash=:wsChatHash");
$sth->execute(array(":wsChatHash"=>$wsChatHash));
$a=$sth->fetch();
if($a['count']==0){
		setcookie("wsChatHash", null, time()+(60*60*24));
		echo json_encode(array("status"=>"error", "error"=>"bad_wsChatHash"));
		exit();		
}




$lastSendTime=0;
if(isset($_POST['lastSendTime'])){
    $lastSendTime=$_POST['lastSendTime'];
}



while(true){

  $dbname="chatsDBs/".$wsChatHash.".db";
  $dbchanged=intval(filemtime($dbname));
		if(is_file($dbname."-wal")){$dbwalchanged=intval(filemtime($dbname."-wal"));}else{$dbwalchanged=0;}
	
  $nowTime=time();
  $duaration=$nowTime-$startTime;
 
  
  if($dbchanged>$lastSendTime OR $dbwalchanged>$lastSendTime OR $duaration>30){

			// Connect to database
			require('chatDBConnect.php');
			$db=chatDBConnect($dbname);
						 
         
      $sth=$db->prepare("SELECT * FROM messages WHERE mId>:mId ORDER BY mDate ASC");
      $sth->execute(array(":mId"=>$_POST['lastMId']));
      
      
      while($a=$sth->fetch()){
          $a['mDate']=date("H:i:s d.m.Y",$a['mDate']);
         
					if($a['mCreator']=="m"){
							$a['mCreator']="Менеджер";
							$a['clr']=1;
					}else{
							$a['mCreator']="Вы";
							$a['clr']=2;
					}
 
           
          $APIanswer["msgs"][]=$a;
      
      }
     
      $lastSendTime=microtime(true);
      $APIanswer["lastSendTime"]=$lastSendTime;
      $APIanswer["status"]="ok";

      
      echo json_encode($APIanswer);    
      exit();
      
      
      
  }
  
  
    clearstatcache(true,$dbname); // чистим кеш состояния файла
		sleep(1); // спим 1 секунды
    
  }      
        
				
				
		$APIanswer["status"]="ok";
		$APIanswer["msgs"]=array();
		echo json_encode($APIanswer);
