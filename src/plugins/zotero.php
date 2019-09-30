<?php


$requete = $file_db->query("SELECT id_subcat,name FROM category_sub_lang");
foreach($requete as $subcat){
  $subcat['name'] = strtolower($subcat['name']);
  $assoc[$subcat['name']] = $subcat['id_subcat'];
}
// create curl resource
$curlZotero = curl_init();
// set url
curl_setopt($curlZotero, CURLOPT_URL, 'https://api.zotero.org/'.$settings['public1'].'/'.$settings['public2'].'/items?v=3&include=citation,data&limit=100&start='.$settings['int2'].$settings['public3'].'&since='.$settings['int1']);
//return the transfer as a string
curl_setopt($curlZotero, CURLOPT_RETURNTRANSFER, 1);
// $output contains the output string
$output = curl_exec($curlZotero);
// close curl resource to free up system resources
curl_close($curlZotero);
$output = json_decode($output, true);
$zoteroFeed = array();
$lastVersion = $settings['int1'];
foreach($output as $object){
  echo("-- object--- <br>");
($object['version'] > $lastVersion ? $lastVersion = $object['version'] : $lastVersion = $lastVersion);
$content = $title = $short = $date = $tags = $key = "";
$content = "<p>".$object['links']['data']['extra']."</p>";
(empty($object['citation'])?$title = $object['data']['title']:$title = $object['citation']);
$url = $object['data']['url'];
$short = $object['data']['abstractNote'];
$key = $object['key'];
$date = $object['data']['date'];
echo($date);
$tags = array();
//filter for zoteroAPI itemType : book => Books, journalArticles => Articles, bookSection => "Book Chapters"
if(!empty($object['data']['itemType'])){
  $tags[] = "Publications";
  if($object['data']['itemType'] == "book"){
    $tags[] = "books";
  }
  elseif($object['data']['itemType'] == "journalArticle"){
    $tags[] = "Articles";
  }
  elseif($object['data']['itemType'] == "bookSection"){
    $tags[] = "Book Chapters";
  }
  elseif($object['data']['itemType'] == "presentation"){
    $tags[] = "Videos";
  }
}


foreach($object['data']['creators'] as $creator){
  $tags[] = $creator['firstName']." ".$creator['lastName'];
}
$subcat = array();
foreach($object['data']['tags'] as $tag){
  $tags[] = $tag['tag'];
}
foreach($tags as $tag){
  $tag = strtolower($tag);
    if(!empty($assoc[$tag])){
    $subcat[] = $assoc[$tag];
    }
}


  $zoteroFeed[] = array(
    "title" => $title,
    "url" => $url,
    "short" => $short,
    "content" => $content,
    "date" => $date,
    "key" => $key,
    "tags" => $tags,
    "subcat" => $subcat
  );
}


echo('<pre>');
print_r($zoteroFeed);
echo('</pre>');


// TODO Add Checkings


//create new item
$newItem = $file_db->prepare("INSERT OR IGNORE INTO item (id_item, year, month, day, published, time, zoterokey) VALUES (NULL,:year,:month,:day,:time,:published,:zoterokey)");
foreach($zoteroFeed as $item){
  echo("date : $item[date]");
  $checkZoteroKey = $file_db->prepare("SELECT id_item FROM item WHERE zoterokey LIKE :zotkey");
  $zoterokey = $item['key'];
  $checkZoteroKey->bindParam(':zotkey',$zoterokey, SQLITE3_TEXT);
  $checkZoteroKey->execute() or die('unable to get the zotekey field');
  $key = $checkZoteroKey->fetchAll();

  if(count($key)){
    $id_item = $key[0]['id_item'];
    $deleteTags = $file_db->prepare("DELETE FROM item_assoc WHERE id_item = :item");
    $deleteTags->bindParam(":item",$id_item);
    $deleteTags->execute() or die('Unable to delete old tags');
    $updateTitle = $file_db->prepare("UPDATE item_lang SET title = :title,cleanstring = :cleanstring, content = :content, short = :short,url = :url WHERE id_item = :item");
    $updateTitle->bindParam(":item",$id_item,SQLITE3_INTEGER);
    $updateTitle->bindParam(":content",$item['content'],SQLITE3_TEXT);
    $updateTitle->bindParam(':short',$item['short'], SQLITE3_TEXT);
    $updateTitle->bindParam(":title",$item['title'],SQLITE3_TEXT);
    $updateTitle->bindParam(":url",$item['url'],SQLITE3_TEXT);
    $updateTitle->bindParam(":cleanstring",$cleanstring,SQLITE3_TEXT);
    $cleanstring = cleanString($item['title']);
    $updateTitle->execute() or die('Unable to update Zotekey');
    $item['date'] = explode("-",$item['date']);
    (empty($item['date'][0])?$year = date("Y"):$year = $item['date'][0]);
    (empty($item['date'][1])?$month = 1:$month = $item['date'][1]);
    (empty($item['date'][2])?$day = 1:$day = array_shift(explode(" ",$item['date'][2])));
    unset($time);
    $time = new DateTime($year."-".$month."-".$day);
    $time = $time->getTimestamp();
    $updateItem = $file_db->prepare("UPDATE item SET `time` = :time, year = :year, month = :month, day = :day WHERE id_item = :item");
    $updateItem->bindParam(":year",$year,SQLITE3_INTEGER);
    $updateItem->bindParam(":month",$month,SQLITE3_INTEGER);
    $updateItem->bindParam(":day",$day,SQLITE3_INTEGER);
    $updateItem->bindParam(":time",$time,SQLITE3_INTEGER);
    $updateItem->bindParam(":item",$id_item,SQLITE3_INTEGER);
    //$updateItem->execute() or die('Unable to update Item');
  }
  else{
    $item['date'] = explode("-",$item['date']);
    (empty($item['date'][0])?$year = date("Y"):$year = $item['date'][0]);
    (empty($item['date'][1])?$month = 1:$month = $item['date'][1]);
    (empty($item['date'][2])?$day = 1:$day = array_shift(explode(" ",$item['date'][2])));
    unset($time);
    $time = new DateTime($year."-".$month."-".$day);
    $time = $time->getTimestamp();

    //Modification, publication time is no more the current server time time at import, but the date specified on ZOTERO, unless the zotero date is not specified
    $published = is_numeric($time)?$time:time();
    $zoterokey = $item['key'];
    $newItem->bindParam(':year',$year);
    $newItem->bindParam(':month',$month);
    $newItem->bindParam(':day',$day);
    $newItem->bindParam(':time',$time);
    $newItem->bindParam(':published',$published);
    $newItem->bindParam(':zoterokey',$zoterokey);
    $newItem->execute() or die('Unable to add item');
    $id_item = $file_db->lastInsertId();

    //insert into item_lang
    $langItem = $file_db->prepare("INSERT INTO item_lang (id_item, title, short, content, cleanstring, lang, url) VALUES (:id_item,:title,:short,:content,:cleanstring,:lang, :url)");
    $langItem->bindParam(':id_item',$id_item, SQLITE3_INTEGER);
    $langItem->bindParam(':title',$title, SQLITE3_TEXT);
    $langItem->bindParam(":url",$item['url'],SQLITE3_TEXT);
    $langItem->bindParam(':short',$short, SQLITE3_TEXT);
    $langItem->bindParam(':content',$content, SQLITE3_TEXT);
    $langItem->bindParam(':cleanstring',$cleanstring, SQLITE3_TEXT);
    $langItem->bindParam(':lang',$lang, SQLITE3_TEXT);

      // Execute statement EN
      $title = $item['title'];
      $short = $item['short'];
      $content = $item['content'];
      $cleanstring = cleanString($item['title']);
      $lang = "EN";
      $langItem->execute() or die('Unable to add lang item');

      // Execute statement EN
      $title = $item['title'];
      $short = $item['short'];
      $content = $item['content'];
      $cleanstring = cleanString($item['title']);
      $lang = "FR";
      $langItem->execute() or die('Unable to add lang item');

  }

  //insert into item_assoc
  $addtag = $file_db->prepare("INSERT INTO item_assoc (id_item, id_subcat) VALUES (:item,:subcat)");
  for($i = 0; $i < count($item['subcat']);$i++ ){
    $addtag->bindParam(":item",$id_item);
    $addtag->bindParam(":subcat",$item['subcat'][$i]);
    $addtag->execute() or die();
  }


}

$count = count($output);

if($count == 100){
  $since = $settings['int1'];
  $start = $settings['int2']+100;
}
else{
  $since = $lastVersion;
  $start = 0;
}
//update the plugin table with the new info
$updateZotero = $file_db->prepare("UPDATE plugins SET int1 = :since, int2 = :start WHERE id_plugin = :plugin");
$updateZotero->bindParam(":since",$since,SQLITE3_INTEGER);
$updateZotero->bindParam(":start",$start,SQLITE3_INTEGER);
$updateZotero->bindParam(":plugin",$settings['id_plugin'],SQLITE3_INTEGER);
$updateZotero->execute() or die("Unable to update the zoteroSinceStart");

?>
