<?php

if(!isset($_GET['lang'])){
  try{
  $checkLang = $file_db->prepare("SELECT lang FROM settings WHERE host LIKE :host");
  $checkLang->bindParam(":host",$_SERVER['HTTP_HOST'],SQLITE3_TEXT);
  $checkLang->execute() or die("Unable to retrieve lang setting");
  $lang = $checkLang->fetch();
  if(!empty($lang['lang'])){
    $lang = strtolower($lang['lang']);
    header('location:/'.$lang.'/');
  }
  else{
    header("location:/fr/");
  }
  die();
}catch(Exception $e){
  header("location:/fr/");
}
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
