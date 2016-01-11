<?php
//Session management
ini_set("session.cookie_secure", 0);
session_start();
if(empty($_SESSION['token'])){
		$_SESSION['token'] = base64_encode(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM));
}
if(!empty($_POST['action']) AND isEqual($_POST['action'],"login") AND !empty($_POST['username']) AND !empty($_POST['password']) AND !empty($_POST['CRSFtoken'])){
	$user = userLogin($file_db,$_POST['username'],$_POST['password'],$_POST['CRSFtoken']);
	error_log($user);
}
elseif(isLoged()){
	$user = userInfo($file_db,$_SESSION['userId']);
}

 ?>
