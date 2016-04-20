<?php

//https://clients6.google.com/calendar/v3/calendars//events?calendarId=k8np7mdjtprb4njihmhuqqeajs%40group.calendar.google.com&singleEvents=true&timeZone=America%2FToronto&maxAttendees=1&maxResults=250&sanitizeHtml=true&timeMin=2016-02-29T00%3A00%3A00-05%3A00&timeMax=2016-04-04T00%3A00%3A00-05%3A00&key=AIzaSyBNlYH01_9Hc5S1J9vuFmu2nUqBZJNAXxs

//$settings['public1'] =  "k8np7mdjtprb4njihmhuqqeajs@group.calendar.google.com";
//$settings['public2'] = "AIzaSyBNlYH01_9Hc5S1J9vuFmu2nUqBZJNAXxs";
// https://www.googleapis.com/calendar/v3/calendars/k8np7mdjtprb4njihmhuqqeajs@group.calendar.google.com/events?key=AIzaSyBNlYH01_9Hc5S1J9vuFmu2nUqBZJNAXxs

// create curl resource
$curlGCalendar = curl_init();
// set url
curl_setopt($curlGCalendar, CURLOPT_URL, 'https://www.googleapis.com/calendar/v3/calendars/'.$settings['public1'].'/events?key='.$settings['public2']);
//return the transfer as a string
curl_setopt($curlGCalendar, CURLOPT_RETURNTRANSFER, 1);
// $output contains the output string
$output = curl_exec($curlGCalendar);
// close curl resource to free up system resources
curl_close($curlGCalendar);
$output = json_decode($output, true);
foreach($output['items'] as $object){
if($object['kind'] == "calendar#event"){
  //echo $object['id']."<br>";
  //echo $object['htmlLink']."<br>";
  //echo $object['summary']."<br>";
  //echo $object['description']."<br>";
  //echo $object['location']."<br>";
  //echo $object['start']['dateTime']."<br>";
  //echo $object['end']['dateTime']."<br>";
  //echo $object['start']['date']."<br>";
  //echo $object['end']['date']."<br>";
  //echo "<br>";

  $checkGKey = $file_db->prepare("SELECT id_event FROM events WHERE Gkey LIKE :GKey");
  $checkGKey->bindParam(":GKey",$object['id'], SQLITE3_TEXT);
  $checkGKey->execute() or die('Unable to retrieve Gkey');
  $checkGKey = $checkGKey->fetch();
  if(!empty($checkGKey)){
    //update info
    $query = $file_db->prepare("UPDATE events SET `time` = :time, endTime = :endTime, phase = :phase WHERE Gkey LIKE :GKey");
    $new = false;
  }
  else{
    //Create new event
    $query = $file_db->prepare("INSERT INTO events (`id_event`, `time`, `endTime`, `GKey`, `phase`) VALUES (NULL, :time, :endTime, :GKey, :phase)");
    $new = true;
  }
  $query->bindParam(":time",$time,SQLITE3_INTEGER);
  $query->bindParam(":endTime",$endTime,SQLITE3_INTEGER);
  $query->bindParam(":GKey",$object['id'],SQLITE3_TEXT);
  $query->bindParam(":phase",$phase,SQLITE3_INTEGER);
  if(!empty($object['start']['dateTime'])){
    $date = new DateTime($object['start']['dateTime']);
    $time = $date->getTimestamp();
    $phase = 0;
  }
  else{
    $date = new DateTime($object['start']['date']);
    $time = $date->getTimestamp();
    $phase = 1;
  }
  if(!empty($object['end']['dateTime'])){
    $date = new DateTime($object['end']['dateTime']);
    $endTime = $date->getTimestamp();
    $phase = 0;
  }
  else{
    $date = new DateTime($object['end']['date']);
    $endTime = $date->getTimestamp();
    $phase = 1;
  }
  //echo("$time - $phase");
  $query->execute() or die('unable to add/alter events');




  //Add event LANG
  if($new){
  $event_id = $file_db->lastInsertId();
  $lang = $file_db->prepare("INSERT INTO events_lang (`id_event`, `title`, `location`, `short`, `description`, `lang`) VALUES (:event, :title, :location, :short, :description, :lang)");
  }
  else{
    $event_id = $checkGKey['id_event'];
    echo("Updating $event_id");
    $lang = $file_db->prepare("UPDATE events_lang SET title = :title, location = :location, description = :description, short = :short WHERE id_event = :event AND lang LIKE :lang");
  }
  $lang->bindParam(":event",$event_id,SQLITE3_INTEGER);
  $lang->bindParam(":title",$object['summary'],SQLITE3_TEXT);
  $lang->bindParam(":location",$object['location'],SQLITE3_TEXT);
  $lang->bindParam(":description",$object['description'],SQLITE3_TEXT);
  $lang->bindParam(":short",$object['htmlLink'],SQLITE3_TEXT);
  $lang->bindParam(":lang",$language,SQLITE3_TEXT);
  // Execute statement EN
  $language = "EN";
  $lang->execute() or die('Unable to add lang item');

  // Execute statement EN
  $language = "FR";
  $lang->execute() or die('Unable to add lang item');

}


}


?>
