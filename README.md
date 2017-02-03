# Ubisoft-Uplay-API-PHP
Uplay API written in PHP

Last Updated: 3rd Feb 2017

It is just proof of concept

# Installation

1.Download and extract into a folder.

2.Create index.php

3.Write into index.php:
```php
<?php
include("ubiapi.php");

$apiUplay = new ubiapi("email","password",null);
// or you can use new ubiapi(null,null,"here b64 of email:password")
$apiUplay->login();
print $apiUplay->searchUser("byid","id of user",true);
print $apiUplay->searchUser("bynick","nick of user,true);
print $apiUplay->getFriends(true);
?>
```
4.You are ready to go!
!. After using function login() you don't need to repeat it (for some time..)
