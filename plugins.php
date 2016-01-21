<?php
include('include/config.ini.php');
$requete = $file_db->query("SELECT id_subcat,name FROM category_sub_lang");
foreach($requete as $subcat){
  $subcat['name'] = strtolower($subcat['name']);
  $assoc[$subcat['name']] = $subcat['id_subcat'];
}

// create curl resource
$curlZotero = curl_init();
// set url
curl_setopt($curlZotero, CURLOPT_URL, "https://api.zotero.org/groups/322999/items?v=3&include=citation,data&limit=100&itemType=book");
//return the transfer as a string
curl_setopt($curlZotero, CURLOPT_RETURNTRANSFER, 1);
// $output contains the output string
$output = curl_exec($curlZotero);
// close curl resource to free up system resources
curl_close($curlZotero);
$output = json_decode($output, true);
$zoteroFeed = array();
foreach($output as $object){
$content = $title = $short = $date = $tags = $key = "";
(empty($object['links']['data']['extra'])?:$content .= $object['links']['data']['extra']."<br>");
(empty($object['citation'])?:$content .= $object['citation']."<br>");
(empty($object['data']['url'])?:$content .= $object['data']['url']."<br>");
(empty($object['links']['alternate']['href'])?:$content .= $object['links']['alternate']['href']."<br>");
$title = $object['data']['title'];
$short = $object['data']['abstractNote'];
$key = $object['key'];
$date = $object['data']['date'];
$tags = array();
$tags[] = "Livres";
$subcat = array();
foreach($object['data']['tags'] as $tag){
  $tags[] = $tag['tag'];
}
foreach($tags as $tag){
  $tag = strtolower($tag);
  $subcat[] = $assoc[$tag];
}


  $zoteroFeed[] = array(
    "title" => $title,
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

  $item['date'] = explode("-",$item['date']);
  (empty($item['date'][0])?$year = date("Y"):$year = $item['date'][0]);
  (empty($item['date'][1])?$month = date("m"):$month = $item['date'][1]);
  (empty($item['date'][2])?$day = date("d"):$day = $item['date'][2]);
  $time = time();
  $published = time();
  $zoterokey = $item['key'];
  $newItem->bindParam(':year',$year);
  $newItem->bindParam(':month',$month);
  $newItem->bindParam(':day',$day);
  $newItem->bindParam(':time',$time);
  $newItem->bindParam(':published',$published);
  $newItem->bindParam(':zoterokey',$zoterokey);
  $newItem->execute() or die('Unable to add item');
  $id_item = $file_db->lastInsertId();

  //insert into item_assoc
  $addtag = $file_db->prepare("INSERT INTO item_assoc (id_item, id_subcat) VALUES (:item,:subcat)");
  for($i = 0; $i < count($item['subcat']);$i++ ){
    $addtag->bindParam(":item",$id_item);
    $addtag->bindParam(":subcat",$item['subcat'][$i]);
    $addtag->execute() or die();
  }

  //insert into item_lang
  $langItem = $file_db->prepare("INSERT INTO item_lang (id_item, title, short, content, cleanstring, lang) VALUES (:id_item,:title,:short,:content,:cleanstring,:lang)");
  $langItem->bindParam(':id_item',$id_item, SQLITE3_INTEGER);
  $langItem->bindParam(':title',$title, SQLITE3_TEXT);
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

?>
