<?php
include('include/config.ini.php');
$plugins = $file_db->prepare("SELECT id_plugin, file, public1, public2, public3, int1, int2, int3, txt1, txt2, txt3 FROM plugins ORDER BY id_plugin ASC");
$plugins->execute() or die("Unable to lookup plugins");
$plugins = $plugins->fetchAll(PDO::FETCH_ASSOC);
foreach ($plugins as $settings) {
    //Technes : 431789
  include('plugins/'.$settings["file"].'.php');
  echo("$settings[file] executé");
}
die();
//Google Calendars
?>
