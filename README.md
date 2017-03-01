# Ubisoft-Uplay-API-PHP

API based on https requests to Ubisoft servers. It's just proof of concept.

API DOESN'T WORK FOR NOW, Request needs "Ubi-Challenge" header

Last Update: 1st Mar 2017 (Doesn't Work)

### Downloading

To use this API you'll just need to copy ticket_file and uAPI.php

### Examples

First of all, you'll need to include api
```php
<?php
include("uAPI.php");
?>
```

Login and update Ticket in ticket_file
```php
<?php
include("uAPI.php");
$uapi = new ubiapi("email","password",null);
$apianswer=$uapi->login(1); //1 for raw, 2 for decoded raw
print $apianswer["error"]; //when 0 - everything is good!
?>
```

Update Ticket (no need to login everytime, without this function you can get banned)
```php
<?php
...
//include api and define it as $uapi
$rt = $uapi->refreshTicket("bynick","v.m.r_ipa");
if($rt["error"]){
	die( "Error: ".$rt["content"]);
}
else{
	print "No Error: ".$rt["content"];
}
```


Get friendlist
```php
<?php
...
//include api, define it as $uapi and update ticket
$fl = $uapi->getFriends();
if($fl["error"] != true){ //small check if api response is vaild
	print $fl["raw"];
}
```

Get Nickname using profileId
```php
<?php
...
//include api, define it as $uapi (and update ticket [refreshTicket])
$su = $uapi->searchUser("byid","01e84770-c50e-402b-8132-b94016f93b77");
if($su["error"] != true){
	print "profileid is: ".$su["nick"]; //you can use 'json' to get array with all info
}
```

Get profileId using Nickname
```php
<?php
...
//include api, define it as $uapi (and update ticket [refreshTicket])
$su = $uapi->searchUser("bynick","v.m.r_ipa");
if($su["error"] != true){
	print "profileid is: ".$su["uid"]; //you can use 'json' to get array with all info
}
```
## Disabling Debug Messages

To disable debug messages you'll need to change line 14 in uAPI.php

from
```
public $debug=true;
```

to
```
public $debug=false;
```
## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
