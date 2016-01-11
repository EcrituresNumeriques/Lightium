<?php
include('include/config.ini.php');
header('Content-type: application/json');
//treat request if admin and action is set
if(isLoged() AND !empty($_POST['action'])){
  //Check for CSFR token
  if($_POST['action'] == "languages"){
    $result = $file_db->prepare('SELECT DISTINCT(lang) FROM settings');
    $result->execute() or die('AHAH');
    $langs = array();
    foreach ($result as $language) {
      $langs[] = $language['lang'];
    }
    echo(JSON_encode($langs));
    //only request, do not show anything
    die();
  }
  elseif($_POST['action'] == "tags"){
      $result = $file_db->prepare('SELECT DISTINCT(lang) FROM settings');
      $result->execute() or die('AHAH');
      foreach ($result as $language) {
        $langs[] = $language['lang'];
      }
      $result = $file_db->prepare('SELECT DISTINCT(id_subcat),name FROM category_sub_lang WHERE lang LIKE :lang');
      $result->bindParam(":lang",$_POST['lang']);
      $result->execute() or die('AHAH');
      foreach ($result as $subcat) {
        $tags[] = $subcat['name'];
        $subcatID[] = $subcat['id_subcat'];
      }
      $response = array(
        "langs" => $langs,
        "tags" => $tags,
        "ids" => $subcatID
      );
      echo(JSON_encode($response));
      //only request, do not show anything
      die();
    }
  elseif($_POST['action'] == "newCat"){
    // TODO Add Checkings

    //create new category
    $insert = "INSERT INTO category (id_cat) VALUES (NULL)";
    $file_db->query($insert);
    $id_cat = $file_db->lastInsertId();

    //insert into category_lang
    $insert = "INSERT INTO category_lang (id_cat, name, lang, image,description,cleanstring) VALUES (:id_cat,:name,:lang,NULL,:description,:cleanstring)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(':id_cat', $id_cat, SQLITE3_INTEGER);
    $stmt->bindParam(':name', $name, SQLITE3_TEXT);
    $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
    $stmt->bindParam(':description', $description, SQLITE3_TEXT);
    $stmt->bindParam(':cleanstring', $cleanstring, SQLITE3_TEXT);

    for ($i = 0; $i < count($_POST['lang']);$i++ ){
      // Execute statement
      $name = $_POST['name'][$i];
      $lang = $_POST['lang'][$i];
      $description = $_POST['description'][$i];
      $cleanstring = cleanString($_POST['name'][$i]);
      $stmt->execute();
    }

  }
  elseif($_POST['action'] == "newSubCat"){
    // TODO Add Checkings

    //create new category
    $insert = "INSERT INTO category_sub (id_subcat, id_cat) VALUES (NULL, :cat)";
    $insert = $file_db->prepare($insert);
    $insert->bindParam(":cat",$_POST['cat']);
    $insert->execute() or die('Unable to create subCat');
    $id_subcat = $file_db->lastInsertId();

    //insert into category_lang
    $insert = "INSERT INTO category_sub_lang (id_subcat, name, lang, image,short,description,cleanstring) VALUES (:id_subcat,:name,:lang,NULL,:short,:description,:cleanstring)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(':id_subcat', $id_subcat, SQLITE3_INTEGER);
    $stmt->bindParam(':name', $name, SQLITE3_TEXT);
    $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
    $stmt->bindParam(':short', $short, SQLITE3_TEXT);
    $stmt->bindParam(':description', $description, SQLITE3_TEXT);
    $stmt->bindParam(':cleanstring', $cleanstring, SQLITE3_TEXT);

    for ($i = 0; $i < count($_POST['lang']);$i++ ){
      // Execute statement
      $name = $_POST['name'][$i];
      $lang = $_POST['lang'][$i];
      $short = $_POST['short'][$i];
      $description = $_POST['description'][$i];
      $cleanstring = cleanString($_POST['name'][$i]);
      $stmt->execute();
    }

  }
  elseif($_POST['action'] == "newItem"){
    // TODO Add Checkings

    //create new item
    $newItem = $file_db->prepare("INSERT INTO item (id_item, year, month, day, published, time) VALUES (NULL,:year,:month,:day,:time,:published)");

  	$year = date("Y");
  	$month = date("m");
  	$day = date("d");
  	$time = time();
  	$published = time();
    $newItem->bindParam(':year',$year);
  	$newItem->bindParam(':month',$month);
  	$newItem->bindParam(':day',$day);
  	$newItem->bindParam(':time',$time);
  	$newItem->bindParam(':published',$published);
    $newItem->execute() or die('Unable to add item');
    $id_item = $file_db->lastInsertId();

    //insert into item_assoc
    $addtag = $file_db->prepare("INSERT INTO item_assoc (id_item, id_subcat) VALUES (:item,:subcat)");
    for($i = 0; $i < count($_POST['tags']);$i++ ){
      $addtag->bindParam(":item",$id_item);
      $addtag->bindParam(":subcat",$_POST['tags'][$i]);
      $addtag->execute() or die();
    }

    //insert into item_lang
    $langItem = $file_db->prepare("INSERT INTO item_lang (id_item, title, short, content, cleanstring, lang) VALUES (:id_item,:title,:short,:content,:cleanstring,:lang)");
    $langItem->bindParam(':id_item',$id_item);
    $langItem->bindParam(':title',$title);
    $langItem->bindParam(':short',$short);
    $langItem->bindParam(':content',$content);
    $langItem->bindParam(':cleanstring',$cleanstring);
    $langItem->bindParam(':lang',$lang);
    for ($i = 0; $i < count($_POST['lang']);$i++ ){
      // Execute statement
      $title = $_POST['name'][$i];
  		$short = $_POST['short'][$i];
  		$content = $_POST['content'][$i];
  		$cleanstring = cleanString($_POST['name'][$i]);
  		$lang = $_POST['lang'][$i];
  		$langItem->execute() or die('Unable to add lang item');
    }
  }
  else{print_r($_REQUEST);}

}

//set User status here

//set categories here if needed

//set lead info here

//set core response

//While no AJAX by the API is done, sent back to Referer
header("location:".$_SERVER['HTTP_REFERER']);


?>
