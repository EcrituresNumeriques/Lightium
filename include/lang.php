<?php

if(!isset($_GET['lang'])){
  header("location:/fr/");
}
else{
  $langAllowed = array('fr','en');
  if(in_array(strtolower($_GET['lang']),$langAllowed)){
    $lang = $_GET['lang'];
	include('lang/'.strtolower($_GET['lang']).'.php');
  }
  else{
    $lang = "fr";
	include('lang/fr.php');
  }
}
?>
