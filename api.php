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
        $tags = array();
        $subcatID = array();
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

  //add new stuff to the database
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
    $langItem->bindParam(':id_item',$id_item, SQLITE3_INTEGER);
    $langItem->bindParam(':title',$title, SQLITE3_TEXT);
    $langItem->bindParam(':short',$short, SQLITE3_TEXT);
    $langItem->bindParam(':content',$content, SQLITE3_TEXT);
    $langItem->bindParam(':cleanstring',$cleanstring, SQLITE3_TEXT);
    $langItem->bindParam(':lang',$lang, SQLITE3_TEXT);
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
  //modify stuff in the database
  elseif($_POST['action'] == "getSettings"){
        $result = $file_db->prepare('SELECT * FROM settings');
        $result->execute() or die('AHAH');
        foreach ($result as $setting) {
          if($setting['name'] == "null" OR $setting['name'] == NULL){$setting['name'] = "";}
          if($setting['description'] == "null" OR $setting['description'] == NULL){$setting['description'] = "";}
          if($setting['meta'] == "null" OR $setting['meta'] == NULL){$setting['meta'] = "";}
          if($setting['title'] == "null" OR $setting['title'] == NULL){$setting['title'] = "";}

          $settings[] = array(
            "lang" => $setting['lang'],
            "name" => $setting['name'],
            "description" => $setting['description'],
            "meta" => $setting['meta'],
            "title" => $setting['title']
          );
        }
        echo(json_encode($settings));
        die();
  }
  elseif($_POST['action'] == "editSettings"){
      $edit = $file_db->prepare("UPDATE settings SET name = :name, description = :description, meta = :meta, title = :title WHERE lang LIKE :lang");
      $edit->bindParam(":name",$name, SQLITE3_TEXT);
      $edit->bindParam(":description",$description, SQLITE3_TEXT);
      $edit->bindParam(":meta",$meta, SQLITE3_TEXT);
      $edit->bindParam(":title",$title, SQLITE3_TEXT);
      $edit->bindParam(":lang",$lang, SQLITE3_TEXT);
      for($i=0;$i<count($_POST['lang']);$i++){
        $name = $_POST['name'][$i];
        $description = $_POST['description'][$i];
        $meta = $_POST['meta'][$i];
        $title = $_POST['title'][$i];
        $lang = $_POST['lang'][$i];
        $edit->execute() or die('Unable to edit setting');
      }
  }
  elseif($_POST['action'] == "getCat"){
          $result = $file_db->prepare('SELECT * FROM category_lang where id_cat = :cat');
          $result->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
          $result->execute() or die('AHAH');
          foreach ($result as $cat) {
            if($cat['image'] == "null" OR $cat['image'] == NULL){$cat['image'] = "";}
            if($cat['name'] == "null" OR $cat['name'] == NULL){$cat['name'] = "";}
            if($cat['description'] == "null" OR $cat['description'] == NULL){$cat['description'] = "";}
            $cats[] = array(
              "lang" => $cat['lang'],
              "name" => $cat['name'],
              "description" => $cat['description'],
              "image" => $cat['image']
            );
          }
          echo(json_encode($cats));
          die();
  }
  elseif($_POST['action'] == "editCat"){
      $edit = $file_db->prepare("UPDATE category_lang SET name = :name, description = :description, image = :image, cleanstring = :cleanString WHERE lang LIKE :lang AND id_cat = :cat");
      $edit->bindParam(":name",$name, SQLITE3_TEXT);
      $edit->bindParam(":description",$description, SQLITE3_TEXT);
      $edit->bindParam(":image",$image, SQLITE3_TEXT);
      $edit->bindParam(":lang",$lang, SQLITE3_TEXT);
      $edit->bindParam(":cleanString",$cleanString, SQLITE3_TEXT);
      $edit->bindParam(":cat",$cat, SQLITE3_INTEGER);
      for($i=0;$i<count($_POST['lang']);$i++){
        $name = $_POST['name'][$i];
        $cleanString = cleanString($_POST['name'][$i]);
        $description = $_POST['description'][$i];
        $image = $_POST['image'][$i];
        $cat = $_POST['cat'];
        $lang = $_POST['lang'][$i];
        $edit->execute() or die('Unable to edit setting');
      }
  }
  elseif($_POST['action'] == "getSubCat"){
          $result = $file_db->prepare('SELECT * FROM category_sub_lang where id_subcat = :cat');
          $result->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
          $result->execute() or die('AHAH');
          foreach ($result as $cat) {
            if($cat['image'] == "null" OR $cat['image'] == NULL){$cat['image'] = "";}
            if($cat['name'] == "null" OR $cat['name'] == NULL){$cat['name'] = "";}
            if($cat['description'] == "null" OR $cat['description'] == NULL){$cat['description'] = "";}
            if($cat['short'] == "null" OR $cat['short'] == NULL){$cat['short'] = "";}
            $cats[] = array(
              "lang" => $cat['lang'],
              "name" => $cat['name'],
              "description" => $cat['description'],
              "short" => $cat['short'],
              "image" => $cat['image']
            );
          }
          echo(json_encode($cats));
          die();
  }
  elseif($_POST['action'] == "editSubCat"){
      $edit = $file_db->prepare("UPDATE category_sub_lang SET name = :name, description = :description, short = :short, image = :image, cleanstring = :cleanString WHERE lang LIKE :lang AND id_subcat = :cat");
      $edit->bindParam(":name",$name, SQLITE3_TEXT);
      $edit->bindParam(":description",$description, SQLITE3_TEXT);
      $edit->bindParam(":short",$short, SQLITE3_TEXT);
      $edit->bindParam(":image",$image, SQLITE3_TEXT);
      $edit->bindParam(":lang",$lang, SQLITE3_TEXT);
      $edit->bindParam(":cleanString",$cleanString, SQLITE3_TEXT);
      $edit->bindParam(":cat",$cat, SQLITE3_INTEGER);
      for($i=0;$i<count($_POST['lang']);$i++){
        $name = $_POST['name'][$i];
        $cleanString = cleanString($_POST['name'][$i]);
        $description = $_POST['description'][$i];
        $short = $_POST['short'][$i];
        $image = $_POST['image'][$i];
        $cat = $_POST['cat'];
        $lang = $_POST['lang'][$i];
        $edit->execute() or die('Unable to edit setting');
      }
  }
  elseif($_POST['action'] == "getItem"){
    $result = $file_db->prepare('SELECT DISTINCT(c.id_subcat),c.name,i.id_item FROM category_sub_lang c LEFT JOIN item_assoc i ON c.id_subcat = i.id_subcat AND i.id_item = :item WHERE lang LIKE :lang');
    $result->bindParam(":lang",$_POST['lang'], SQLITE3_TEXT);
    $result->bindParam(":item",$_POST['item'], SQLITE3_INTEGER);
    $result->execute() or die('AHAH');
      $tags = array();
    foreach ($result as $subcat) {
      (empty($subcat['id_item'])?$subcat['id_item'] = "":$subcat['id_item'] = "checked");
      $cats['tags'][] = array(
        "name" => $subcat['name'],
        "id" => $subcat['id_subcat'],
        "checked" => $subcat['id_item']
      );
      }
    $result = $file_db->prepare('SELECT * FROM item_lang where id_item = :item');
    $result->bindParam(":item",$_POST['item'], SQLITE3_INTEGER);
    $result->execute() or die('AHAH');
    foreach ($result as $cat) {
      if($cat['short'] == "null" OR $cat['short'] == NULL){$cat['short'] = "";}
      if($cat['title'] == "null" OR $cat['title'] == NULL){$cat['title'] = "";}
      if($cat['content'] == "null" OR $cat['content'] == NULL){$cat['content'] = "";}
      $cats['items'][] = array(
        "lang" => $cat['lang'],
        "title" => $cat['title'],
        "content" => $cat['content'],
        "short" => $cat['short']
      );
    }
    echo(json_encode($cats));
    die();
  }
  elseif($_POST['action'] == "editItem"){
      $edit = $file_db->prepare("UPDATE item_lang SET title = :title, content = :content, short = :short, cleanstring = :cleanString WHERE lang LIKE :lang AND id_item = :item");
      $edit->bindParam(":title",$title, SQLITE3_TEXT);
      $edit->bindParam(":content",$content, SQLITE3_TEXT);
      $edit->bindParam(":short",$short, SQLITE3_TEXT);
      $edit->bindParam(":lang",$lang, SQLITE3_TEXT);
      $edit->bindParam(":cleanString",$cleanString, SQLITE3_TEXT);
      $edit->bindParam(":item",$item, SQLITE3_INTEGER);
      for($i=0;$i<count($_POST['lang']);$i++){
        $title = $_POST['title'][$i];
        $cleanString = cleanString($_POST['title'][$i]);
        $content = $_POST['content'][$i];
        $short = $_POST['short'][$i];
        $item = $_POST['item'];
        $lang = $_POST['lang'][$i];
        $edit->execute() or die('Unable to edit setting');
      }
      //insert into item_assoc
      $deltag = $file_db->prepare("DELETE FROM item_assoc WHERE id_item = :item");
      $deltag->bindParam(":item",$_POST['item']);
      $deltag->execute() or die('Unable to remove tags');
      $addtag = $file_db->prepare("INSERT INTO item_assoc (id_item, id_subcat) VALUES (:item,:subcat)");
      for($i = 0; $i < count($_POST['tags']);$i++ ){
        $addtag->bindParam(":item",$_POST['item']);
        $addtag->bindParam(":subcat",$_POST['tags'][$i]);
        $addtag->execute() or die();
      }
  }
  else{print_r($_REQUEST);die();}

}

//set User status here

//set categories here if needed

//set lead info here

//set core response

//While no AJAX by the API is done, sent back to Referer
header("location:".$_SERVER['HTTP_REFERER']);


?>
