<?php

function tbDatabase(){
 
		
		global $dbUse;
		
 
		try
		{
			
			if($dbUse=="sqlite"){
					global $tbRootDir;
					$db = new PDO("sqlite:".$tbRootDir."/telegram.db");
			}elseif($dbUse=="mysql"){
					global $mysqlHost,$mysqlDB,$mysqlLogin,$mysqlPassword;
					$db = new PDO("mysql:host=".$mysqlHost.";dbname=".$mysqlDB,$mysqlLogin,$mysqlPassword);
			}else{
					return(false);
			}
			
			// Таймаут ожидания блокировки на запись
			$db->setAttribute(PDO::ATTR_TIMEOUT,55); 

			// Дефолтный fetch - как ассоциативный массив
			$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_BOTH); 
			
			
			if($dbUse=="sqlite"){
					$db->exec('PRAGMA journal_mode=WAL;');
			}

			
			
			// Проверим наличие нужных таблиц в базе данных
			
			if($dbUse=="sqlite"){
					$sth=$db->prepare("SELECT name FROM sqlite_master WHERE type=:type");
					$executeArray=array(":type"=>"table");
			}elseif($dbUse=="mysql"){
					$sth=$db->prepare("SHOW TABLES");
					$executeArray=array();
			}
			$sth->execute($executeArray);
			
			$tblsInDB=array();
			while($answer=$sth->fetch()){
					$tblsInDB[]=$answer[0];
			}
			
			
			$neededTables=array("tbManagers","tbChats","tbMessages");
			
			$createTables=false;
			
			foreach($neededTables as $key=>$value){
					
					if(array_search($value,$tblsInDB)===false){
			
						$createTables=true;
						break;
					}
			
			
			}
			
			if($createTables==true){
			
			
			
			
			if($dbUse=="sqlite"){
					$db->exec("
								DROP TABLE IF EXISTS tbManagers;
								CREATE TABLE tbManagers (
									mId integer NOT NULL PRIMARY KEY AUTOINCREMENT,
									mName text NULL,
									mBotChatId text NULL,
									mSiteChatId text NULL,
									mStatus integer(1) NULL
								);

								DROP TABLE IF EXISTS tbChats;
								CREATE TABLE tbChats (
									chId integer NOT NULL PRIMARY KEY AUTOINCREMENT,
									chHash text NULL,
									chManager integer(10) NULL
								);

								DROP TABLE IF EXISTS tbMessages;
								CREATE TABLE tbMessages (
									msgId integer NOT NULL PRIMARY KEY AUTOINCREMENT,
									msgChatId integer NULL,
									msgFrom text NULL,
									msgTime integer(12) NULL,
									msgText text NULL									 
								);
					");
					
			}elseif($dbUse=="mysql"){
			
					
					$db->exec("
								
					DROP TABLE IF EXISTS `tbChats`;
					CREATE TABLE IF NOT EXISTS `tbChats` (
						`chId` int(11) NOT NULL AUTO_INCREMENT,
						`chHash` varchar(50) DEFAULT NULL,
						`chManager` int(10) DEFAULT NULL,
						PRIMARY KEY (`chId`),
						KEY `chId` (`chId`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

					DROP TABLE IF EXISTS `tbManagers`;
					CREATE TABLE IF NOT EXISTS `tbManagers` (
						`mId` int(10) NOT NULL AUTO_INCREMENT,
						`mName` text,
						`mBotChatId` text,
						`mSiteChatId` text,
						`mStatus` int(1) DEFAULT NULL,
						PRIMARY KEY (`mId`),
						KEY `mId` (`mId`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


					DROP TABLE IF EXISTS `tbMessages`;
					CREATE TABLE IF NOT EXISTS `tbMessages` (
						`msgId` int(10) NOT NULL AUTO_INCREMENT,
						`msgChatId` int(10) DEFAULT NULL,
						`msgFrom` varchar(5) DEFAULT NULL,
						`msgTime` varchar(12) DEFAULT NULL,
						`msgText` text,
						PRIMARY KEY (`msgId`),
						KEY `msgId` (`msgId`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


					");

			}			
						
			
			}
			
			

			return($db);
		}
		catch(PDOException $e)
		{
		return(false);
		}
		
		
		
		
}	








?>