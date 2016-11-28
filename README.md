# Ubisoft-Uplay-API-PHP
Uplay API written in PHP


OUTDATED

It is just proof of concept

# Installation

1.Download and extract into a folder.

2.Create index.php

3.Write into index.php:
```php
include("uAPI.php");
$ubi=new uapi("username or email","password",false);
```
4.You are ready to go!

# Examples

! Before every example you'll need to write
```php
include("uAPI.php");
$ubi=new uapi("username or email","password",false);
```

- Refresh authstr to make api always available
```php
$resp=$ubi->verifykey("uplay id","expected nickname of uplay id");
if($resp["error"]){
	$resp=$ubi->login();
	if($resp["error"]){
		die("Can't access Uplay API right now");
	}
}
```

- Get ID of Selected nickname
```php
$resp=$ubi->simpleusersearch(0,"Nickname");
if($resp["error"]){
	die("Can't lookup nickname");
}
else{
	echo $resp["data"];
}
```
