<?php

/**
 * @author Kacper Serewis (k4czp3r.dev@gmail.com)
 * @copyright 2016
 * @version 1.0.0.0
 * 
 * Created 31-08-2016
 */

class uapi{
    public $uplayemail;
    public $uplaypassword;
    public $debug;
    public $useragent = "Mozilla/5.0 (Windows NT; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36";
    
    function __construct($email,$password,$debug=false){
        $this->uplayemail = $email;
        $this->uplaypassword = $password;
        $this->debug = $debug;
        $this->debugmsg("Defined<br>Mail:".$email."<br>Pass:YES<br>Debug:Yes<br>");
    }

    public function changenickname($newNickname){
        $newNickname = '{"nameOnPlatform":"'.$newNickname.'"}';
        $newNicknameLen = strlen($newNickname);
        $authStr = $this->getAuthStr();
        if($authStr["error"]){
            return array("error"=>true,
            "error_msg"=>$authStr["error_msg"]);
        }
        $uplayId = $this->getId();
        if($uplayId["error"]){
            return array("error"=>true,
            "error_msg"=>$authStr["error_msg"]);
        }
        $appid = "c5393f10-7ac7-4b4f-90fa-21f8f3451a04";
        
        
        
        $firsturl = "https://api-ubiservices.ubi.com/v2/profiles/".$uplayId["data"]."/validateUpdate";
        $secondurl = $firsturl;
        $finalurl = "https://api-ubiservices.ubi.com/v2/profiles/".$uplayId["data"];
        $val1opts=array("http"=>array("method"=>"OPTIONS",
        "header"=>"Host: api-ubiservices.ubi.com\r\n".
        //"Connection: keep-alive\r\n".
        "Access-Control-Request-Method: POST\r\n".
        "Origin: https://account-uplay.ubi.com\r\n".
        "User-Agent: ".$this->useragent."\r\n".
        "Access-Control-Request-Heades: authorization, content-type, ubi-appid\r\n".
        "Accept: */*\r\n".
        "Referer: https://account-uplay.ubi.com/en-GB/account/information\r\n".
        "Accept-Encoding: gzip, deflate, sdch, br\r\n".
        "Accept-Language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4\r\n",));
            
        $val2opts=array("http"=>array("method"=>"POST",
        "header"=>"Host: api-ubiservices.ubi.com\r\n".
       // "Connection: keep-alive\r\n".
        "Content-Lenght: ".$newNicknameLen."\r\n".
        "authorization: ".$authStr["data"]."\r\n".
        "ubi-appid: ".$appid."\r\n".
        "Origin: https://account-uplay.ubi.com\r\n".
        "User-Agent: ".$this->useragent."\r\n".
        "content-type: application/json\r\n".
        "Accept: */*\r\n".
        "Referer: https://account-uplay.ubi.com/en-GB/account/information\r\n".
        "Accept-Encoding: gzip, deflate, br\r\n".
        "Accept-Language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4\r\n",
        "content"=>$newNickname));
            
        $putopts=array("http"=>array("method"=>"PUT",
        "header"=>"Host: api-ubiservices.ubi.com\r\n".
       // "Connection: keep-alive\r\n".
        "Content-Lenght: ".$newNicknameLen."\r\n".
        "authorization: ".$authStr["data"]."\r\n".
        "ubi-appid: ".$appid."\r\n".
        "Origin: https://account-uplay.ubi.com\r\n".
        "User-Agent: ".$this->useragent."\r\n".
        "content-type: application/json\r\n".
        "Accept: */*\r\n".
        "Referer: https://account-uplay.ubi.com/en-GB/account/information\r\n".
        "Accept-Encoding: gzip, deflate, br\r\n".
        "Accept-Language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4\r\n",
        "content"=>$newNickname));
            
        $firstcontext = stream_context_create($val1opts);
        $secondcontext = stream_context_create($val2opts);
        $finalcontext = stream_context_create($putopts);
            
        $FirstubiResp = file_get_contents($firsturl,false,$firstcontext);
        $SecondubiResp = file_get_contents($secondurl,false,$secondcontext);
        $FinalubiResp = file_get_contents($finalurl,false,$finalcontext);
        if(empty($FinalubiResp)){
            return array("error"=>true,
            "error_msg"=>"Last request returned null",
            "error_data"=>$FinalubiResp);
        }
        else{
            $json = json_decode($FinalubiResp,true);
            $ip = $json['clientIp'];
            if(!empty($ip)){
                return array("error"=>false,
                "data"=>$ip,
                "data_full"=>$FinalubiResp);
            }
            else{
                return array("error"=>true,
                "error_msg"=>"Bad username",
                "error_data"=>$FinalubiResp);
            }
        }
    }
    public function simpleusersearch($mode,$string){
        if($mode == 0){$geturl = "https://public-ubiservices.ubi.com/v2/profiles?platformType=uplay&nameOnPlatform=".$string;}
        elseif($mode == 1){$geturl = "https://public-ubiservices.ubi.com/v2/profiles?profileId=".$string;}
        else{return array("error"=>true,
                "error_msg"=>"This mode doesn't exists for User Search");}
        $appId = "412802ed-8163-4642-a931-8299f209fecb";
        $authStr = $this->getAuthStr();
        $getopts = array("http"=>array("method"=>"GET",
        "header"=>"Host: uplayconnect.com\r\n" .
        "Accept: application/json, text/plain, */*\r\n" .
        "Ubi-AppId: ".$appId."\r\n" .
        "Authorization: ".$authStr["data"]."\r\n".
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36\r\n".
        "Referer: https://uplayoverlay.ubi.com/?appId=314d4fef-e568-454a-ae06-43e3bece12a6&genomeId=45366f4e-420c-4386-ac26-24b3d25f2a3b&loginType=popup&themeColor=%233F4A4C&height=40&leftTextDirection=true&locale=en-GB&minimizedOpacity=&parentUrl=https%3A%2F%2Fclub.ubi.com&userId=01e84770-c50e-402b-8132-b94016f93b77\r\n".
        "Accept-Encoding: gzip, deflate\r\n".
        "Accept-Language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4\r\n",));
        $scontext = stream_context_create($getopts);
        $ubiResp = file_get_contents($geturl,false,$scontext);
        if(empty($ubiResp)){
            return array("error"=>true,
            "error_msg"=>"Response from ubi server is empty",
            "error_data"=>$ubiResp);
        }
        else{
            $ubirJson = json_decode($ubiResp,true);
            $uNick = $ubirJson['profiles'][0]['nameOnPlatform'];
            $uId = $ubirJson['profiles'][0]['profileId'];
            if(empty($uNick) OR empty($uId)){
                return array("error"=>true,
                "error_msg"=>"User doesn't exist",
                "error_data"=>$ubiResp);
            }
            if($mode == 0){return array("error"=>false,
                    "data"=>$uId,
                    "data_full"=>$ubiResp);}
            elseif($mode == 1){return array("error"=>false,
                    "data"=>$uNick,
                    "data_full"=>$ubiResp);}
        }
        
    }
    public function login(){
        $url = "https://uplayconnect.ubi.com/ubiservices/v2/profiles/sessions";
        $appId = "c5393f10-7ac7-4b4f-90fa-21f8f3451a04";
        $uEmail = $this->uplayemail;
        $uPassword = $this->uplaypassword;
        $bAuth = base64_encode($uEmail.":".$uPassword);
        $postopts=array("http"=>array("method"=>"POST",
        "header"=>"Host: uplayconnect.com\r\n".
        "Connection: keep-alive\r\n".
        "Content-Lenght: 2\r\n".
        "Ubi-RequestedPlatformType: uplay\r\n".
        "Origin: https://uplayconnect.ubi.com\r\n".
        "Authorization: Basic ".$bAuth."\r\n".
        "Content-Type: application/json; charset=UTF-8\r\n".
        "Accept: */*\r\n".
        "Ubi-AppId: ".$appId."\r\n".
        "X-Requested-With: XMLHttpRequest\r\n".
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36\r\n".
        "Referer: https://uplayconnect.ubi.com/Default/Login?appId=".$appId."&lang=en-GB\r\n".
        "Accept-Encoding: gzip, deflate\r\n".
        "Accept-Language: nl-NL,nl;q=0.8,en-US;q=0.6,en;q=0.4\r\n",
        "content"=>"{}"));
        
        $scontext = stream_context_create($postopts);
        $ubiResp = file_get_contents($url,false,$scontext);
        if(empty($ubiResp)){
            return array("error"=>true,
            "error_msg"=>"Ubi responded with empty string",
            "error_data"=>$ubiResp);
        }
        else{
            $ubirJson = json_decode($ubiResp,true);
            $uTicket = $ubirJson['ticket'];
            $uProfileId = $ubirJson['profileId'];
            if(empty($uTicket)){
                return array("error"=>true,
                "error_msg"=>"Uplay Ticket is empty, see error_data for more",
                "error_data"=>$ubiResp);
            }
            else{
                if(empty($uProfileId)){
                    return array("error"=>true,
                    "error_msg"=>"Uplay ID is empty, see error_data for more",
                    "error_data"=>$ubiResp);
                }
            
                else{
                    $this->writeAuthStr($uTicket);
                    $this->writeId($uProfileId);
                    return array("error"=>false,
                    "data"=>$uTicket.":".$uProfileId,
                    "data_full"=>$ubiResp);
                }
            }
        }
    }
    private function debugmsg($str){
        if($this->debug){
            echo $str;
        }
    }
    private function getId(){
        $idF = fopen("ubi_id","r") or die("Error while opening profileid file");
        $id = fgets($idF);
        fclose($idF);
        if($id == NULL){
            return array("error"=>true,
            "error_msg"=>"profileid is empty");
        }
        else{
            return array("error"=>false,
            "data"=>$id);
        }
    }
    private function writeId($id){
        $idF = fopen("ubi_id","w") or die("Can't open profile id");
        try{
            fwrite($idF,$id);
            return array("error"=>false,
            "data","ok");
        }
        catch(Exception $ex){
            return array("error"=>true,
            "error_msg"=>"Error while writitng to file (".$ex.")");
        }
    }
    public function getAuthStr(){
        $prefix = "Ubi_v1 t=";
        $ticketF = fopen("ubi_authstr","r") or die("Error while opening ticket file");
        $ticket = fgets($ticketF);
        if($ticket == NULL){
            return array("error"=>true,
            "error_msg"=>"Ticket file is empty");
        }
        else{
            return array("error"=>false,
            "data"=>$prefix.$ticket);
        }
    }
    private function writeAuthStr($valToWrite){
        $ticketF = fopen("ubi_authstr","w") or die("Can't open ticket file");
        try{
            fwrite($ticketF,$valToWrite);
            return array("error"=>false,
            "data"=>"ok");
        }
        catch(Exception $ex){
            return array("error"=>true,
            "error_msg"=>"Error while writing to file  (".$ex.")");
        }
    }
    
}
?>