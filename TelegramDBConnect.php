<?
function connectDB(){
		$dbName="telegram.db";

		try
		{
			$db = new PDO("sqlite:".$dbName);
			$db->setAttribute(PDO::ATTR_TIMEOUT,55);   // Таймаут ожидания блокировки на запись
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC); //  Дефолтный фетч - как ассоциативный массив
			$db->exec('PRAGMA journal_mode=WAL;');
			return($db);
		}
		catch(PDOException $e)
		{
		return(false);
		}
}	
	


?>