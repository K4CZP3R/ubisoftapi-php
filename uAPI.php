<?php
/**
 * @author Kacper Serewis (k4czp3r.dev@gmail.com)
 * @copyright 2017
 * @version 2.0.0.0
 * Updated at 03-Feb-2017
 */
class ubiapi{
	private $b64authcreds;
	public $http_useragent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36";
	public $http_encoding="gzip";
	public $debug=false;

	public function debugReport($content){
		if($this->debug){
			print date("h:i:s")." - ".$content."<br>";
		}
	}
	public function generateB64Creds($emailandpassword){
		$this->debugReport("B64: ".base64_encode($emailandpassword));
		return base64_encode($emailandpassword);
	}
	function __construct($credsemail,$credspass,$credsb64){
		if($credspass == null && $credsb64 != null){
			$this->debugReport("Using b64 string to login");
			$this->b64authcreds=$credsb64;
		}
		else{
			$this->debugReport("Using creds to login");
			$this->b64authcreds=$this->generateB64Creds($credsemail.":".$credspass);
		}
	}
	public function searchUser($mode,$content,$returnRaw){
		$prefixUrl = "https://api-ubiservices.ubi.com/v2/profiles?";
		if($mode == 1 || $mode == "bynick"){
			$request_url = $prefixUrl."nameOnPlatform=".$content."&platformType=uplay";
		}
		if($mode == 2 || $mode == "byid"){
			$request_url = $prefixUrl."profileId=".$content;
		}
		$this->debugReport("Request URL: ".$request_url);
		$request_header_ubiappid = "314d4fef-e568-454a-ae06-43e3bece12a6";
		$request_header_ubisessionid = "a651a618-bead-4732-b929-4a9488a21d27";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers =[
		"Accept: */*",
		"ubi-appid: ".$request_header_ubiappid,
		"ubi-sessionid: ".$request_header_ubisessionid,
		"authorization: ".$this->uplayticket(false),
		"Referer: https://club.ubisoft.com/en-US/friends",
		"Accept-Language: en-US",
		"Origin: https://club.ubisoft.com",
		"Accept-Encoding: ".$this->http_encoding,
		"User-Agent: ".$this->http_useragent,
		"Host: api-ubiservices.ubi.com",
		"Cache-Control: no-cache"];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$ubioutput = curl_exec($ch);
		curl_close($ch);

		if($this->http_encoding == "gzip"){
			$ubioutput = gzdecode($ubioutput);
		}
		$this->debugReport("Executed request (to see it, uncomment log line)");
		$this->debugReport($ubioutput);

		//idk why but sometimes gzdecoded returns null
		if(empty($ubioutput)){
			$this->debugReport("After making use of ".$this->http_encoding. "decode, string is empty, using orginal one...");
			$ubioutput=$orginaloutput;}
		if($returnRaw){
			return $ubioutput;
		}
		else{
			return json_decode($ubioutput,true);
		}

	}
	//if $returnRaw == false then it'll return array
	public function getFriends($returnRaw){
		$this->debugReport("Going to return friends array/json");
		$request_url = "https://api-ubiservices.ubi.com/v2/profiles/me/friends?locale=en-US";
		$request_header_ubiappid = "314d4fef-e568-454a-ae06-43e3bece12a6";
		$request_header_ubisessionid= "a651a618-bead-4732-b929-4a9488a21d27"; //todo: check generation of it

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers=[
		"Accept: */*",
		"ubi-appid: ".$request_header_ubiappid,
		"ubi-sessionid: ".$request_header_ubisessionid,
		"authorization: ".$this->uplayticket(false),
		"Referer: https://club.ubisoft.com/en-US/friends",
		"Accept-Language: en-US",
		"Origin: https://club.ubisoft.com",
		"Accept-Encoding: ".$this->http_encoding,
		"User-Agent: ".$this->http_useragent,
		"Host: api-ubiservices.ubi.com",
		"Cache-Control: no-cache"];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$ubioutput=curl_exec($ch);
		curl_close($ch);
		//after testing i dont see reason to include gzdecoding here, if you want to, do it

		$this->debugReport("Request Executed (to see it, uncomment next line)");
		//$this->debugReport($ubioutput);

		if($returnRaw){
			return $ubioutput;
		}
		else{
			return json_decode($ubioutput,true);
		}
		
	}
	public function login(){
		$this->debugReport("Going to login...");
		$request_url = "https://connect.ubi.com/ubiservices/v2/profiles/sessions";
		$request_header_ubiappid="314d4fef-e568-454a-ae06-43e3bece12a6";
		$request_header_authbasic=$this->b64authcreds;
		$this->debugReport("<br>url:".$request_url."<br>appid:".$request_header_ubiappid."<br>authbasic:".$request_header_authbasic);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '{"rememberMe":true}');
		$headers = [
		"Content-Type: application/json; charset=utf-8",
		"Accept: */*",
		"Ubi-AppId: ".$request_header_ubiappid,
		"Ubi-RequestedPlatformType: uplay",
		"Authorization: Basic ".$request_header_authbasic,
		"X-Requested-With: XMLHttpRequest",
		"Referer: https://connect.ubi.com/Default/Login?appId=".$request_header_ubiappid."&lang=en-US&nextUrl=https%3A%2F%2Fclub.ubisoft.com%2Flogged-in.html%3Flocale%3Den-US",
		"Accept-Language: en-US",
		"Accept-Encoding: ".$this->http_encoding,
		"User-Agent: ".$this->http_useragent,
		"Host: connect.ubi.com",
		"Content-Lenght: 19", //change this when you are changing CURLOPT_POSTFIELDS
		"Cache-Control: no-cache",
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$ubioutput = curl_exec($ch);
		$orginaloutput=$ubioutput;
		curl_close($ch);
		if($this->http_encoding == "gzip"){
			$ubioutput = gzdecode($ubioutput);
		}
		$this->debugReport("Executed request (to see it, uncomment log line)");
		//$this->debugReport($ubioutput);

		//idk why but sometimes gzdecoded returns null
		if(empty($ubioutput)){
			$this->debugReport("After making use of ".$this->http_encoding. "decode, string is empty, using orginal one...");
			$ubioutput=$orginaloutput;}

		$json = json_decode($ubioutput,true);
		$this->debugReport("Your authstring (to see it, uncomment next line of code)");
		//$this->debugReport($json['ticket']);
		$this->debugReport("Welcome, ".$json['username']);
		$this->debugReport("Your UserId is ".$json['userId']);
		$this->debugReport("You can ignore last function for saving authstring but you'll need to edit some lines of codes to disable it");
		$this->uplayticket(true,$json['ticket']);

	}
	public function uplayticket($save,$ticket=""){
		if($save){
			$this->debugReport("Saving ticket...");
			$file_ticket = fopen("api_ticket","w") or die("Can't open ticket file");
			try{
				fwrite($file_ticket, $ticket);
				return true;
			}
			catch(Exception $e){
				return false;
			}
		}
		else{
			$this->debugReport("Gonna return formated ticket");
			$prefix = "Ubi_v1 t=";
			$ticket_file = fopen("api_ticket","r") or die("Can't open ticket file");
			$ticket = fgets($ticket_file);
			return $prefix.$ticket;
		}
	}
	
}
