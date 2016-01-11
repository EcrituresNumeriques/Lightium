<?php

if(!isset($_GET['lang'])){
  $lang = "FR";
	include('lang/fr.php');
}
else{
  $langAllowed = array('FR');
  if(in_array($_GET['lang'],$langAllowed)){
    $lang = $_GET['lang'];
	include('lang/fr.php');
  }
  else{
    $lang = "FR";
	include('lang/fr.php');
  }
}
?>
