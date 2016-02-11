<?php
include('include/config.ini.php');


$plugins = $file_db->prepare("SELECT id_plugin, file, public1, public2, public3, prive1, prive2, prive3 FROM plugins WHERE enabled = 1 ORDER BY id_plugin ASC");
$plugins->execute() or die("Unable to lookup plugins");
$plugins = $plugins->fetchAll(PDO::FETCH_ASSOC);
foreach ($plugins as $settings) {
  $settings["public1"] = "groups";
  $settings["public2"] = "322999";
  $settings["public3"] = "&itemType=book";
  //Technes : 431789
  $settings["int1"] = 122;
  $settings["int2"] = 0;
  include('plugins/'.$settings["file"].'.php');
  echo("$setting[file] executé");
}
//Google Calendar
?>
