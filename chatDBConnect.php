<?
function chatDBConnect($dbName){
 

		if(is_file($dbName)){
				$new=false;
		}else{
				$new=true;		
		}
 
 
		try
		{
			$db = new PDO("sqlite:".$dbName);
			$db->setAttribute(PDO::ATTR_TIMEOUT,55);   // Таймаут ожидания блокировки на запись
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC); //  Дефолтный фетч - как ассоциативный массив
			$db->exec('PRAGMA journal_mode=WAL;');
			
			if($new==true){
			$sth=$db->exec("
			DROP TABLE IF EXISTS \"messages\";
			CREATE TABLE \"messages\" (
				\"mId\" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
				\"mDate\" integer(12) NOT NULL DEFAULT '0',
				\"mCreator\" integer(6) NOT NULL DEFAULT '0',
				\"mText\" text NULL
			);
			");
			}

			return($db);
		}
		catch(PDOException $e)
		{
		return(false);
		}
}	
	
