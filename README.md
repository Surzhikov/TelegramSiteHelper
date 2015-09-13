# TelegramSiteHelper
Telegram Bot and chat to create WebSite Helper (PHP+JS(JQuery2)+HTML+CSS)<br>
<br>
If you try everything, but it is not working - you can write me (<a href="https://telegram.me/surzhikov">https://telegram.me/surzhikov</a>).<br>
<br>
The general scheme of ideas:<br>
<img src="https://habrastorage.org/files/5fa/cc9/048/5facc9048483406ab0eba3820cce44fa.png"><br>

That's how it happens: <br>
1. Online users write to chat
2. Post it flies on your server
3. From there Telegram-bot sends it to the correct manager
4. The manager responds by Telegram
5. Bot sends a message back to chat_na_sayte

Screenshot:<br>
<img src="https://habrastorage.org/files/cbf/50e/458/cbf50e45825a48ce92b8eac34ba7d875.png"><br>

Requirement:<br>
You must've Telegram Account. If you don't have make one using <a href="https://web.telegram.org">https://web.telegram.org/</a>).

Todo:<br>
1. Download or clone this repo
2. Create a new bot. Add user <a href="http://telegram.me/botfather">@BotFather</a> and follow this step to  <a href="https://core.telegram.org/bots#create-a-new-bot">create a new bot</a><br>
<img src="https://habrastorage.org/files/6de/a35/0f7/6dea350f710b4afe9c03f94702aecf49.png"><br>
2. Edit configuration in «<B>telegramSiteHelper/tbConfig.php</B>»<br>
3. Upload everything to your hosting/server/VPS<br>
4. «<B>telegramSiteHelper/tbServer.php</B>» should run continuously, because it is a server. Add the cron job with a period of 1 every minute for this script. If the script fails, it will start again for a minute. It will be launched at the same time only one copy of the script.<br>
5. Try!<br>
6. Usage:<br>
<img src="https://habrastorage.org/files/cbf/50e/458/cbf50e45825a48ce92b8eac34ba7d875.png"><br>
	6.1) Add your bot to your contact list<br>
	6.2) Enter your manager password<br>
	6.3) Use command «/offline», «/online», «/exit»<br>

Minuses: <br>
1. Chat Who made "in haste" to start soon in the project. There are many loopholes, by which for example can be written in another chat room and peek's correspondence.
2. Now, these problems do not disturb me, because chatting assistant site have been made to transfer important and sensitive information.
3. When a manager and a lot of customers - can be confused whom to answer.
4. ... I supplement of the comments

Pros:<b> 
1. It works!
2. Free Forever and for any number of managers
3. No need extra applications only telegrams, which is for all popular platforms
4. You can rewrite and stylized chat as you want.
5. Telegram is very fast
6. ... I supplement of the comments

What can be done: <br>
1. Foolproof and work on security
2. Smart distribution system communications between managers (now the bot sends a random message to the manager)
3. Automatic responses from the bot when the manager was silent for a long time
4. Add a name and a photo manager that is responsible chatting
5. ... I supplement of the comments


In the plans: <br>
1. Admin panel with statistics 
2. Work through webhook 

Reference:
http://habrahabr.ru/post/264035/
	
