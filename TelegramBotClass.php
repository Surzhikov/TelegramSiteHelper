<?php

class TelegramBot{
    #You can use this token to access HTTP API:
	private $TokenAPI;
	
    
	function __construct($TokenAPI){
		$this->TokenApi=$TokenAPI;
	}
    
    #Send the request
    private function SendApiBot($Url,$Data = null)
    {
        $resource = curl_init($Url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CONNECTTIMEOUT => 600,
            CURLOPT_SSL_VERIFYPEER => false );
        //SendApiBot ....
        if (!is_null($Data)) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($Data); 
        }
        curl_setopt_array($resource, $options);
        $GetBody = curl_exec($resource);
        curl_close($resource);
        $ResultJson = json_decode($GetBody);
        return $ResultJson;
    }
    
    #Task demand
	private function SendQueries($TheTask, $data=NULL){
		$TokenAPI = $this->TokenApi;
		$Consent = $this->SendApiBot("https://api.telegram.org/bot$TokenAPI/".$TheTask, $data);
		return $Consent;
	}
    //Now Status bots
	public function Status(){
		$Consent = $this->SendQueries("getme");
		return($Consent);
	}
    
	public function GetUpdates($offset= null,$limit= null,$timeout= null){
		$data = array();
		
		if($offset!=null){
				$data['offset']=$offset;
		}		
		if($limit!=null){
				$data['limit']=$limit;
		}		
		if($timeout!=null){
				$data['timeout']=$timeout;
		}
		
		$Consent = $this->SendQueries("getUpdates", $data);
		return($Consent);
	}
    
	public function SendMessage($ChatId, $TextMsg, $ReplyIDm = null, $ReplyMarkup = null){
		$data = array();
		$data["chat_id"]= $ChatId;
		$data["text"]= $TextMsg;
        
		$data["disable_web_page_preview"] = "true";
		if(isset($ReplyIDm))
			$data["reply_to_message_id"] = $ReplyIDm;
            
		if(isset($ReplyMarkup))
			$data["reply_markup"] = $ReplyMarkup;
            
		$Consent = $this->SendQueries("sendMessage", $data);
		return $Consent;
	}
    
	public function GetUserProfilePhoto($UserID, $OffSet = null, $Limit = null){
		$data = array();
		$data["user_id"] = $UserID;
		if(isset($OffSet)){
			$data["offset"] = $OffSet;
		}
		if(isset($Limit)){
			$data["limit"] = $Limit;
		}
		
		$Consent = $this->SendQueries("getUserProfilePhotos", $data);
		return $Consent;
	}
}

?>