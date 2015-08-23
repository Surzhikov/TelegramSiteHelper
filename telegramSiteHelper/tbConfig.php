<?php


// Корневая директория для TelegramSiteHelper
// Root dir for TelegramSiteHelper
$tbRootDir="var/www/telegramSiteHelper";

// Пароль для авторизации менеджера через телеграм бота
// Password for manager auth (in Telegram bot), you must write some non-so-easy
$tbManagerPassword="123456";

// API Token, который вы получили у @BotFather
// API Token, you can get it from user @BotFather (in Telegram App)
$tbAPIToken="";


// Тип базы данных: «sqlite» или «mysql»
// Database type: «sqlite» or «mysql»
$dbUse="sqlite";
 



// Если вы используете MySQL - укажите ниже хост, имя базы, логин и пароль
// If you use MySQL, write here host, dbname, login, password
$mysqlHost="localhost";
$mysqlDB="tbc";
$mysqlLogin="root";
$mysqlPassword="";





#######################################################################
// Ниже ничего исправлять не нужно!!!

// Создаем папку для обновлений чатов
if(!is_dir($tbRootDir."/chatUpdates")){
		mkdir($tbRootDir."/chatUpdates");
}











?>