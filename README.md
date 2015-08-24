# TelegramSiteHelper
Telegram Bot and chat to create WebSite Helper (PHP+JS(JQuery2)+HTML+CSS)<br>
Телеграм бот и чат для создания Чата-помощника на сайт<br>
<br>
If you try everything, but it is not working - you can write me (<a href="https://telegram.me/surzhikov">https://telegram.me/surzhikov</a>).<br>
Если вы все перепробывали, но все-равно ничего не работает, можете написать мне в телеграм, помогу чем смогу (<a href="https://telegram.me/surzhikov">https://telegram.me/surzhikov</a>).<br>
<br>
Scheme (Схема):<br>
<img src="https://habrastorage.org/files/5fa/cc9/048/5facc9048483406ab0eba3820cce44fa.png"><br>

Screenshot (скриншот):<br>
<img src="https://habrastorage.org/files/cbf/50e/458/cbf50e45825a48ce92b8eac34ba7d875.png"><br>

<h1>English manual</h1>
1) Create new bot:<br>
	1.1) Install Telegram (<a href="https://telegram.org">https://telegram.org/</a>)<br>
	1.2) Add user <a href="http://telegram.me/botfather">@BotFather</a> and register new Bot<br>
	1.3) Use «<B>/newbot</B>» to create new Bot<br>
	1.4) Use «<B>/setcommands</B>» to setup commands list. Commands list looks like:<br>
<pre>
offline - Go offline
online - Go online
exit - Quit from system
</pre>

1.5) Use «<B>/setuserpic</B>» to set user picture<br>
<br>

2) Edit configuration in «<B>telegramSiteHelper/tbConfig.php</B>»<br>
	2.1) Edit $tbRootDir var, set the path of telegramSiteHelper folder,(something like this «<B>var/www/mysite/telegramSiteHelper</B>»)<br>
	2.2) Create a password <b>$tbManagerPassword</b> — managers will use it to login<br>
	2.3) Edit <B>$tbAPIToken</B> — paste you API key here<br>
	2.4) Edit <B>$dbUse</B> — if you want use SQLite - set it to «sqlite»; if you want use MySQL - set it to «mysql»; <br>
	2.5) If you use MySQL, fill hostname, dbname, login and password vars ($mysqlHost, $mysqlDB, $mysqlLogin, $mysqlPassword)<br>
	<br>
3) Edit your site pages:<br>
	3.1) Insert code below <!--- CHAT START -!--> and <!--- CHAT ENDS -!--> from index.html to your page<br>
	3.2) Insert <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script> to your site <head><br>
	3.3) Insert <script type="text/javascript" src="iscroll.js"></script> to your site <head> (check valid path)<br>
	3.4) Insert <script type="text/javascript" src="chat.js"></script> to your site <head> (check valid path)<br>
	3.5) Edit (if you want) and insert <link rel="stylesheet" type="text/css" href="chat.css"> to your site <head> (check valid path)<br>
	3.6) If you need - translate and localize files <br>
	<br>
	
4) Upload everything to your hosting/server/VPS<br>
4.1) «<B>telegramSiteHelper/tbServer.php</B>» should run continuously, because it is a server. Add the cron job with a period of 1 every minute for this script. If the script fails, it will start again for a minute. It will be launched at the same time only one copy of the script.<br>
<br>
5) Try!<br>
<br>
6) Usage:<br>
	6.1) Add your bot to your contact list<br>
	6.2) Enter your manager password<br>
	6.3) Use command «/offline», «/online», «/exit»<br>


<br>
###########################################################################################################
<br>

<h1>Русский мануал</h1>
1) Создайте нового бота:<br>
	1.1) Установите приложение Telegram (<a href="https://telegram.org">https://telegram.org/</a>)<br>
	1.2) Добавьте пользователя <a href="http://telegram.me/botfather">@BotFather</a> <br>
	1.3) Используйте команду «<b>/newbot</b>» для создания нового бота<br>
	1.4) Используйте команду «<b>/setcommands</b>» для устанвки выпадающего списка команд. Список команд:<br>
<pre>
offline - Перейти в Оффлайн
online - Вернуться в Онлайн
exit - Выйти из системы
</pre>

1.5) Используйте команду «<b>/setuserpic</b>» для установки аватарки<br>
<br>
2) Отредактируйте конфигурацию «<B>telegramSiteHelper/tbConfig.php</B>»<br>
	2.1) В переменной <b>$tbRootDir</b> укажите путь до папки telegramSiteHelper (должно быть как-то так «var/www/mysite/telegramSiteHelper»)<br>
	2.2) Придумайте пароль <b>$tbManagerPassword</b> — его будут использовать менеджеры для входа в систему<br>
	2.3) В переменную <b>$tbAPIToken</b> вставьте Ваш API key<br>
	2.4) Отредактируйте <b>$dbUse</b> — Если Вы хотите использовать SQLite - впишите «sqlite»; Если MySQL - впишите «mysql»; <br>
	2.5) Если вы используете MySQL, укажите имя хост, имя базы, логин и пароль (<b>$mysqlHost</b>, <b>$mysqlDB</b>, <b>$mysqlLogin</b>, <b>$mysqlPassword</b>)<br>
	<br>
3) Отредактируйте страницы вашего сайта:<br>
	3.1) Вставьте весь код находящийся между <!--- CHAT START -!--> и <!--- CHAT ENDS -!--> со страницы index.html на страницу вашего сайта<br>
	3.2) Вставьте JQuery <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script> в секцию <head> вашего сайта<br>
	3.3) Вставьте <script type="text/javascript" src="iscroll.js"></script> в секцию <head> вашего сайта (проверьте правильность путей)<br>
	3.4) Вставьте <script type="text/javascript" src="chat.js"></script> в секцию <head> вашего сайта (проверьте правильность путей)<br>
	3.5) Отредактируйте (если захотите) и вставьте <link rel="stylesheet" type="text/css" href="chat.css"> в секцию <head> вашего сайта (проверьте правильность путей)<br>
	3.6) Если необходимо - локализуйте файлы на ваш язык<br>
	
<br>	
4) Загрузите все на Ваш хостинг, сервер или VPS<br>
4.1) «<B>telegramSiteHelper/tbServer.php</B>» должен работать постояно, ведь это сервер. Добавьте запуск этого скрипта в cron с периодом 1 раз в минуту. Если скрипт завершится ошибкой, он запустится в течении минуты. Единовременно будет запущена лишь одна копия скрипта. <br>
<br>
5) Проверьте!<br>
<br>
6) Использование:<br>
	6.1) Добавьте вашего бота в свой контакт лист<br>
	6.2) Введите пароль менеджера<br>
	6.3) Используйте команды «/offline», «/online», «/exit»<br>
	
	
	
