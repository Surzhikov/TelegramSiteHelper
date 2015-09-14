<?php

header('Content-Type: text/html; charset=utf-8'); // кодировка UTF-8
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
mb_internal_encoding("UTF-8");

// Корневая директория для TelegramSiteHelper
// Root dir for TelegramSiteHelper
// IN LINUX: something like this: /var/www/sitename/telegramSiteHelper 
// IN Windows (XAMPP, OpenServer, etc) C:/xampp/mysite/telegramSiteHelper
// CHECK, THERE IS NO SLASH "/" IN THE END!!!!!
$tbRootDir="/var/www/mysite/telegramSiteHelper";

// Пароль для авторизации менеджера через телеграм бота
// Password for manager auth (in Telegram bot), you must write some non-so-easy
$tbManagerPassword="123456";

// API Token, который вы получили у @BotFather
// API Token, you can get it from user @BotFather (in Telegram App)
$tbAPIToken="";

// Название языкового файла (в папке /localization)
// Name of localization file (you can find it in /localization folder)
$tbLanguageFile="en.php";

//Для использования Русского языка раскомментируйте следующие строки:
//$tbLanguageFile="ru.php"; 

// Тип базы данных: «sqlite» или «mysql»
// Database type: «sqlite» or «mysql»
$dbUse="sqlite";
 



// Если вы используете MySQL - укажите ниже хост, имя базы, логин и пароль
// If you use MySQL, write here host, dbname, login, password
$mysqlHost="localhost";
$mysqlDB="";
$mysqlLogin="";
$mysqlPassword="";





#######################################################################
// Ниже ничего исправлять не нужно!!!

// Создаем папку для обновлений чатов
if(!is_dir($tbRootDir."/chatUpdates")){
		mkdir($tbRootDir."/chatUpdates");
}



require_once($tbRootDir."/localization/".$tbLanguageFile);









?>