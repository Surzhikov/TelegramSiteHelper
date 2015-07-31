$(function(){

		$("#chatTextBox").bind('keyup',function(e){
				if(e.keyCode==13){
						msg=$("#chatTextBox").val();
						if(msg.length>0){
								sendMsgs(msg)
						}
				}
		});


		bindChatScroll(function(){refreshChatScroll();})

		
		StartCHAT();
});




function StartCHAT(){

	getMsgs(0);

}





chatScroll="";

function bindChatScroll(callback){

   chatScroll = new IScroll('#chatWrapper', {scrollX: false, scrollY: true, tap: true, mouseWheel: true});
   if(callback){callback();}
}

function refreshChatScroll(){

    chatScroll.refresh();
    
    var h=$("#chatMessages").height();
    if(h>300){
        var a=300-h;
    }else{
      a=1;
    }
    chatScroll.scrollTo(0, a);
}





function makeMsg(msgObj){
		var m="<li class=\"msg\" id=\"msg_"+msgObj.mId+"\">";
		m+="<b class=\"name clr"+msgObj.clr+"\">"+msgObj.mCreator+":</b>";;
		m+="<b class=\"time\">"+msgObj.mDate+"</b>";
		m+="<div class=\"clr\"></div>";

		 
		m+=msgObj.mText;
		m+="</li>";
		return(m);
}

 


function appendMessage(msgTxt){
		 
		$("#chatMessages").append(msgTxt);
		refreshChatScroll();
}

 



function getMsgs(lastSendTime){
 //console.log("getMsgs")
var lastMId=$("#lastMId").val();

	$.ajax({
		url : "getMessages.php",
		type : "POST",
		dataType : "JSON",
		data : {lastMId:lastMId,lastSendTime:lastSendTime},
		async : true,
		timeout : 25000
	}).done(function (answer) {

 
  //console.log(answer)
      

		if(answer.status == 'ok') { // ЕСЛИ API возвратило status=ok
      setTimeout(function(){getMsgs(answer.lastSendTime);},1500);
      if(answer.msgs){
      if(answer.msgs.length>0){
				if(lastMId!=0){$("#chatSound")[0].play();}
        $.each(answer.msgs,function(i,msg){
						var m=makeMsg(msg);
						$("#lastMId").val(msg.mId)
						appendMessage(m);
				});
      }
      }
    
      
			return (true);
		} else if (answer.status == 'error') { // ЕСЛИ API возвратило status=error обрабатываем ошибки.
		
			setTimeout(function(){getMsgs();},1500);}

	}).fail(function (jqXHR, textStatus, errorThrown) {
		//console.log(jqXHR);
      setTimeout(function(){getMsgs();},15000);
	});

	//####
}





function sendMsgs(wsMessage){
 //console.log("sendMsg");
    $.ajax({
      url : "sendMessage.php",
      type : "POST",
      dataType : "JSON",
      data : {wsMessage:wsMessage},
      async : true,
      timeout : 25000
    }).done(function (answer) {

					//console.log(answer);

        if (answer.status == 'ok') {
            $("#chatTextBox").val("");
            
        } else if (answer.status == 'error') { // ЕСЛИ API возвратило status=error обрабатываем ошибки.
            
            
            alert("Возникла ошибка при отправке сообщения. Попробуйте перезагрузить страницу!");
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        //console.log(jqXHR);
         
                alert("Возникла ошибка при отправке сообщения. Попробуйте перезагрузить страницу!");
      });

      //####
}










