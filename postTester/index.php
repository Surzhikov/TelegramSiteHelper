<?
error_reporting(E_ALL & ~E_NOTICE);

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Tester</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript" src="jquery.cookie.js"></script>
<style>
body{padding:0; margin:0; font-family:'helvetica'; color:#1a1a1a; font-size:10pt;}
.page{margin:20px auto; display:block; width:550px; padding:20px; background:#fff; border-radius:10px;}
h1{color: #7779B8;margin: 0px 0px 23px 0px;font-size: 16pt;letter-spacing: -0.5pt;}
input,select,textarea{border:1px solid #aaa; padding:6px 5px;border-radius:2px;}
input:hover,select:hover,textarea:hover{border:1px solid #233CBE;}
input:focus,select:focus,textarea:focus{border:1px solid #233CBE; outline:none;background-color: #FBFAFF;}
button{border:1px solid #ccc; padding:6px; border-radius:2px; background-color:#F0F0F0;}
button:hover{border:1px solid #ccc; padding:6px; border-radius:2px;background-color: #FBFAFF;}
button:focus{border:1px solid #233CBE; outline:none;}
#vars div{margin:5px 0;}
#vars div input{margin:0 2px;}

#answerDiv{
border:1px solid #ccc;
overflow:auto;
padding:5px;
}
</style>
</head>
<body >

<div class="page">
Адрес обращения:<br>
<input type="text" id="scriptURL" value="<?=$_COOKIE['TESTER_URL'];?>" size="50"><br><br>

Тип запроса:<br>
<select id="method">
<option value="post" <? if($_COOKIE['TESTER_type']=="post"){echo "selected";} ?>>POST</option>
<option value="get" <? if($_COOKIE['TESTER_type']=="get"){echo "selected";} ?>>GET</option>
</select><br><br>
<div id="vars">
	<?	if(isset($_COOKIE['TESTER_keys']) AND isset($_COOKIE['TESTER_values'])){	$keys=explode("|ar|",$_COOKIE['TESTER_keys']);	$values=explode("|ar|",$_COOKIE['TESTER_values']);		foreach($keys as $k=>$v){		echo"<div><input type=\"text\" class=\"key\" placeholder=\"Переменная\" value=\"".$v."\"><input type=\"text\" class=\"var\" placeholder=\"Значение переменной\" value=\"".$values[$k]."\"><button class=\"remover\">-</button></div>";				}		}			?>
</div>
<button id="addVar">+</button>
<br><br>
<button id="send">Отправить</button><br><br>
Ответ:<br>
<textarea id="answer" style="height:200px; width:540px;"></textarea><br>
<br><br>
Ответ HTML:<br>
<div id="answerDiv" style="height:200px; width:540px;"></div><br>
</div>
<script type="text/javascript">
$(document).ready(function(){$(".remover").on("click",function(){$(this).parent().remove();window.parent.reHeightFrame();});//$(".remover").on

	$("#send").click(function(){
		var URL=$("#scriptURL").val();
		var type=$("#method option:selected").val();
		if(URL==""){alert("Адрес обращения не может быть пустой!"); return false; }
		$.cookie('TESTER_URL', URL);		$.cookie('TESTER_type', type);
			var keys=[];
			$(".key").each(function(){
			if($(this).val()==""){alert("Название переменной не может быть пустой!"); return false; }
			keys.push($(this).val());
			});
			
			var values=[];
			$(".var").each(function(){
			values.push($(this).val());
			});
		
			var sendArray=array_combine(keys,values);
			$.cookie('TESTER_keys', keys.join('|ar|'));			$.cookie('TESTER_values', values.join('|ar|'));
			 $( "#answer" ).val( "..." );

			var request = $.ajax({
				  url: URL,
				  type: type,
				  data: sendArray
				});
				 
				request.done(function( msg ) {
				  $( "#answer" ).val( msg );
				  $( "#answerDiv" ).html( msg );
				});
				 
				request.fail(function( jqXHR, textStatus ) {
				  alert( "Request failed: " + textStatus );
				});


			
	});
	
	$("#addVar").click(function(){
		$("#vars").append('<div><input type="text" class="key" placeholder="Переменная"><input type="text" class="var" placeholder="Значение переменной"><button class="remover">-</button></div>');
		$(".remover").on("click",function(){
			$(this).parent().remove(); 
		}); 
	});
	
	

	
	
});

function array_combine( keys, values ) {	// Creates an array by using one array for keys and another for its values
	// 
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

	var new_array = {}, keycount=keys.length, i;

	// input sanitation
	if( !keys || !values || keys.constructor !== Array || values.constructor !== Array ){
		return false;
	}

	// number of elements does not match
	if(keycount != values.length){
		return false;
	}

	for ( i=0; i < keycount; i++ ){
		new_array[keys[i]] = values[i];
	}

	return new_array;
}

function dump(obj) {
    var out = "";
    if(obj && typeof(obj) == "object"){
        for (var i in obj) {
            out += i + ": " + obj[i] + "\n";
        }
    } else {
        out = obj;
    }
    alert(out);
}

</script>

</body>
</html>