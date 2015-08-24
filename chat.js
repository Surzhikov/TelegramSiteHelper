$(function() {

    $("#telegramSiteHelper_chatTextBox").bind('keyup', function(e) {
        if (e.keyCode == 13) {
            msg = $("#telegramSiteHelper_chatTextBox").val();
            if (msg.length > 0) {
                sendMsgs(msg)
            }
        }
    });


    //bindChatScroll(function() {
   //     refreshChatScroll();
   // })


    StartCHAT();
});


var startByClick=0;


function StartCHAT() {

    var tbChatHash = $.cookie("tbChatHash");
    if (tbChatHash !== null) {
        getMsgs();
    } else {
			startByClick=1;
		}

}




function getMsgs() {


    var tbChatHash = $.cookie("tbChatHash");
    var lastMessageId = $("#telegramSiteHelper_lastMId").val();
    var params = {};
    params['lastMessageId'] = lastMessageId;
    params['tbChatHash'] = tbChatHash;

		console.log("getMsgs "+tbChatHash);
	

    $.ajax({
        url: "../telegramSiteHelper/tbGetMessages.php",
        type: "POST",
        dataType: "JSON",
        data: params,
        async: true,
        timeout: 59000
    }).done(function(answer) {

	console.log(answer);
	
        if (answer.status == 'ok') {

            if (answer.msgs) {
                if (answer.msgs.length > 0) {
                    if (lastMessageId != 0) {
                        $("#telegramSiteHelper_chatSound")[0].play();
                    }
                    $.each(answer.msgs, function(i, msg) {
                        var m = makeMsg(msg);
                        $("#telegramSiteHelper_lastMId").val(msg.msgId)
                        appendMessage(m);
                    });
										refreshChatScroll();
                    
                }
            }
						
						getMsgs();



            return (true);
        } else if (answer.status == 'error') { // ЕСЛИ API возвратило status=error обрабатываем ошибки.

            setTimeout(function() {
                getMsgs();
            }, 1500);
        }

    }).fail(function(jqXHR, textStatus, errorThrown) {
        setTimeout(function() {
            getMsgs();
        }, 15000);
    });



}




function sendMsgs(tbMessage) {
    var tbChatHash = $.cookie("tbChatHash");
    var params = {};
    params['tbMessage'] = tbMessage;
    params['tbChatHash'] = tbChatHash;


    $.ajax({
        url: "../telegramSiteHelper/tbSendMessage.php",
        type: "POST",
        dataType: "JSON",
        data: params,
        async: true,
        timeout: 25000
    }).done(function(answer) {

		console.log(answer);
        if (answer.status == 'ok') {
		
            $("#telegramSiteHelper_chatTextBox").val("");
            $("#telegramSiteHelper_lastMId").val(answer.lastMessageId);
            var tbChatHash = $.cookie("tbChatHash")

            if (tbChatHash == null) {
                $.cookie("tbChatHash", answer.tbChatHash);
                getMsgs();
            }

            
						/*var startByClick = $.cookie("startByClick");
            if (startByClick = 1) {
                $.cookie("startByClick", 0);
                getMsgs();
            }
						*/

            $.cookie("managerName", answer.managerName);

            var msgObj = {}
            msgObj.msgFrom = "c";
            msgObj.msgText = tbMessage;
            msgObj.msgId = answer.lastMessageId;
            msgObj.msgTime = answer.lastMessageDate;
            var m = makeMsg(msgObj);
            appendMessage(m);
						refreshChatScroll();
        } else if (answer.status == 'error') { // ЕСЛИ API возвратило status=error обрабатываем ошибки.


            alert("Возникла ошибка при отправке сообщения. Попробуйте перезагрузить страницу!");
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {

console.log(jqXHR);
        alert("Возникла ошибка при отправке сообщения. Попробуйте перезагрузить страницу!! "+ textStatus);
    });

    //####
}


 


function refreshChatScroll() {

 
    var h = $("#telegramSiteHelper_chatMessages").height();
		
		console.log(h);
		
    if (h > 300) {
        var a = 300 - h;
    } else {
        a = 1;
    }
		
		console.log(a);
   $("#telegramSiteHelper_chatWrapper").animate({ scrollTop: h }, "2");
}




function makeMsg(msgObj) {

    if (msgObj.msgFrom == "m") {
        msgObj.mCreator = "Менеджер";
        var managerName = $.cookie("managerName");
        if (managerName != null) {
            msgObj.mCreator = managerName;
        }
        msgObj.clr = "1";
    } else {
        msgObj.mCreator = "Я";
        msgObj.clr = "2";
    }

    var m = "<li class=\"msg\" id=\"msg_" + msgObj.msgId + "\">";
    m += "<b class=\"name clr" + msgObj.clr + "\">" + msgObj.mCreator + ":</b>";;
    m += "<b class=\"time\">" + msgObj.msgTime + "</b>";
    m += "<div class=\"clr\"></div>";
    m += msgObj.msgText;
    m += "</li>";
    return (m);
}




function appendMessage(msgTxt) {
		$("#telegramSiteHelper_firstChatMsg").hide();
    $("#telegramSiteHelper_chatMessages").append(msgTxt);

}




/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */


jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};