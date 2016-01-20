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

?>
