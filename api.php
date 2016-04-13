<?php
include('include/config.ini.php');
//Custom CSS AND JS
if(!empty($_GET['action']) AND $_GET['action'] == "customCSS"){
  $customCSS = $file_db->query("Select * FROM customCSS LIMIT 0,1");
  $customCSS = $customCSS->fetch(PDO::FETCH_ASSOC);
  header('Last-Modified: '.gmdate('D, d M Y H:i:s', $customCSS['time']).' GMT', true, 200);
  header('Content-Type: text/css');
  echo($customCSS['CSS']);
  die();
}


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
    elseif($_POST['action'] == "getCSS"){
        $customCSS = $file_db->query("Select * FROM customCSS LIMIT 0,1");
        $customCSS = $customCSS->fetch(PDO::FETCH_ASSOC);
        echo(JSON_encode($customCSS));
        //only request, do not show anything
        die();
    }
    elseif($_POST['action'] == "getHeader"){
        $customHeader = $file_db->query("Select * FROM header LIMIT 0,1");
        $customHeader = $customHeader->fetch(PDO::FETCH_ASSOC);
        echo(JSON_encode($customHeader));
        //only request, do not show anything
        die();
    }
    elseif($_POST['action'] == "getFooter"){
        $customFooter = $file_db->query("Select * FROM footer LIMIT 0,1");
        $customFooter = $customFooter->fetch(PDO::FETCH_ASSOC);
        echo(JSON_encode($customFooter));
        //only request, do not show anything
        die();
    }

    elseif($_POST['action'] == "getPlugins"){
        $result = $file_db->prepare('SELECT id_plugin as id,file,public1, public2, public3 FROM plugins');
        $result->execute() or die('AHAH');
        $result = $result->fetchAll(PDO::FETCH_ASSOC);
        $plugins = array();
        foreach ($result as $plugin) {
          ($plugin['file'] != NULL ?:$plugin['file'] = $translation['admin_newlyPlugin']);
          ($plugin['public1'] != NULL ?:$plugin['public1'] = "");
          ($plugin['public2'] != NULL ?:$plugin['public2'] = "");
          ($plugin['public3'] != NULL ?:$plugin['public3'] = "");
          $plugins[] = $plugin;
        }
        echo(JSON_encode($plugins));
        //only request, do not show anything
        die();
    }
    elseif($_POST['action'] == "getContact"){
        $result = $file_db->prepare('SELECT c.id_contact as id,*,t.name as type,c.value FROM contact c LEFT JOIN contact_type t ON c.type = t.id_type ORDER BY c.priority ASC');
        $result->execute() or die('AHAH');
        $result = $result->fetchAll(PDO::FETCH_ASSOC);
        $contacts = array();
        foreach ($result as $contact) {
          ($contact['id'] != NULL ?:$contact['id'] = $translation['admin_newlyContact']);
          ($contact['type'] != NULL ?:$contact['type'] = 0);
          ($contact['value'] != NULL ?:$contact['value'] = "");
          $contacts[] = $contact;
        }
        echo(JSON_encode($contacts));
        //only request, do not show anything
        die();
    }
    elseif($_POST['action'] == "getSummary"){
        $result = $file_db->prepare('SELECT s.id_summary, s.`group`, s.priority, sl.name FROM summary s LEFT JOIN category_sub_lang sl ON sl.id_subcat = s.id_subcat AND sl.lang LIKE :lang ORDER BY `group`, priority');
        $result->bindParam(":lang",$_POST['lang']);
        $result->execute() or die('AHAH');
        $result = $result->fetchAll(PDO::FETCH_ASSOC);
        $summaries = array();
        foreach ($result as $summary) {
          ($summary['group'] != NULL ?:$summary['group'] = 1);
          ($summary['priority'] != NULL ?:$summary['priority'] = 1);
          ($summary['name'] != NULL ?:$summary['name'] = "");
          $summaries[] = $summary;
        }
        echo(JSON_encode($summaries));
        //only request, do not show anything
        die();
    }


  //add new stuff to the database


  elseif($_POST['action'] == "editCSS"){
      $customCSS = $file_db->prepare("UPDATE customCSS SET CSS = :css, time = :time");
      $customCSS->BindParam(":css",$_POST['CSS'],SQLITE3_TEXT);
      $customCSS->BindParam(":time",$time,SQLITE3_INTEGER);
      $time = time();
      $customCSS->execute() or die('Unable to change CSS');

  }
    elseif($_POST['action'] == "editHeader"){
        $customHeader = $file_db->prepare("UPDATE header SET header = :header, time = :time");
        $customHeader->BindParam(":header",$_POST['header'],SQLITE3_TEXT);
        $customHeader->BindParam(":time",$time,SQLITE3_INTEGER);
        $time = time();
        $customHeader->execute() or die('Unable to change Header');

    }
      elseif($_POST['action'] == "editFooter"){
          $customFooter = $file_db->prepare("UPDATE footer SET footer = :footer, time = :time");
          $customFooter->BindParam(":footer",$_POST['footer'],SQLITE3_TEXT);
          $customFooter->BindParam(":time",$time,SQLITE3_INTEGER);
          $time = time();
          $customFooter->execute() or die('Unable to change Footer');

      }
  elseif($_POST['action'] == "newPlugin"){
    $file_db->query("INSERT INTO plugins DEFAULT VALUES");
    $reponse['id'] = $file_db->lastInsertId();
    $reponse['file'] = "empty";
    $reponse['public1'] = "";
    $reponse['public2'] = "";
    $reponse['public3'] = "";
    $reponse['int1'] = 0;
    $reponse['int2'] = 0;
    $reponse['int3'] = 0;
    $reponse['txt1'] = "";
    $reponse['txt2'] = "";
    $reponse['txt3'] = "";
    $plugins = scandir("plugins/");
    $reponse = array("plugin" => $reponse, "pluginList" => array());
    foreach($plugins as $plugin){
      if(endsWith($plugin,".php")){
        $plugin = substr($plugin,0,-4);
        $reponse['pluginList'][] = $plugin;
      }
    }
    echo(JSON_encode($reponse));
    die();
  }
  elseif($_POST['action'] == "retrievePlugin"){
      $retrievePlugin = $file_db->prepare("SELECT id_plugin as id,file,public1, public2, public3, int1, int2, int3, txt1, txt2, txt3 FROM plugins where id_plugin = :plugin");
      $retrievePlugin->bindParam(":plugin",$_POST['id'],SQLITE3_INTEGER);
      $retrievePlugin->execute() or die('unable to retrieve Plugin');
      $reponse = $retrievePlugin->fetch(PDO::FETCH_ASSOC);
      ($reponse['public1'] == "null" OR $reponse['public1'] == NULL ? $reponse['public1'] = "":$reponse['public1'] = $reponse['public1']);
      ($reponse['public2'] == "null" OR $reponse['public2'] == NULL ? $reponse['public2'] = "":$reponse['public2'] = $reponse['public2']);
      ($reponse['public3'] == "null" OR $reponse['public3'] == NULL ? $reponse['public3'] = "":$reponse['public3'] = $reponse['public3']);
      $plugins = scandir("plugins/");
      $reponse = array("plugin" => $reponse, "pluginList" => array());
      foreach($plugins as $plugin){
        if(endsWith($plugin,".php")){
          $plugin = substr($plugin,0,-4);
          $reponse['pluginList'][] = $plugin;
        }
      }
      echo(JSON_encode($reponse));
      die();
  }
  elseif($_POST['action'] == "retrieveContact"){
      $retrievePlugin = $file_db->prepare("SELECT id_contact as id,type,value, priority FROM contact where id_contact = :contact");
      $retrievePlugin->bindParam(":contact",$_POST['id'],SQLITE3_INTEGER);
      $retrievePlugin->execute() or die('unable to retrieve Contact');
      $reponse = $retrievePlugin->fetch(PDO::FETCH_ASSOC);
      ($reponse['type'] == "null" OR $reponse['type'] == NULL ? $reponse['type'] = "":$reponse['type'] = $reponse['type']);
      ($reponse['value'] == "null" OR $reponse['value'] == NULL ? $reponse['value'] = "":$reponse['value'] = $reponse['value']);
      ($reponse['priority'] == "null" OR $reponse['priority'] == NULL ? $reponse['priority'] = "":$reponse['priority'] = $reponse['priority']);
      $contactType = $file_db->query("SELECT id_type, name, template FROM contact_type");
      $contactType = $contactType->fetchAll(PDO::FETCH_ASSOC);
      $reponse = array("contact" => $reponse, "contactList" => $contactType);
      echo(JSON_encode($reponse));
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
    //add new stuff to the database
    elseif($_POST['action'] == "newCalendar"){
      // TODO Add Checkings

      //create new category
      $insert = "INSERT INTO events (id_event,time) VALUES (NULL,:time)";
      $addEvent = $file_db->prepare($insert);
      $addEvent->bindParam(":time",$datetime);
      $date = DateTime::createFromFormat("Y-m-d H:i",$_POST['date']." ".$_POST['time']);
      $datetime = $date->getTimestamp();
      $addEvent->execute() or die('Unable to add the event');
      $id_event = $file_db->lastInsertId();

      //insert into category_lang
      $insert = "INSERT INTO events_lang (id_event, title, short, location, description, lang) VALUES (:id_event,:title,:short,:location, :description, :lang)";
      $stmt = $file_db->prepare($insert);
      $stmt->bindParam(':id_event', $id_event, SQLITE3_INTEGER);
      $stmt->bindParam(':title', $title, SQLITE3_TEXT);
      $stmt->bindParam(':short', $short, SQLITE3_TEXT);
      $stmt->bindParam(':location', $location, SQLITE3_TEXT);
      $stmt->bindParam(':description', $description, SQLITE3_TEXT);
      $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);

      for ($i = 0; $i < count($_POST['lang']);$i++ ){
        // Execute statement
        $title = $_POST['title'][$i];
        $lang = $_POST['lang'][$i];
        $description = $_POST['description'][$i];
        $location = $_POST['location'][$i];
        $short = $_POST['short'][$i];
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
    $newItem = $file_db->prepare("INSERT INTO item (id_item, year, month, day, published, time, featured) VALUES (NULL,:year,:month,:day,:time,:published,:featured)");

  	$year = date("Y");
  	$month = date("m");
  	$day = date("d");
  	$time = time();
  	$published = time();
    $featured = $_POST['featured'];
    $newItem->bindParam(':year',$year);
  	$newItem->bindParam(':month',$month);
  	$newItem->bindParam(':day',$day);
  	$newItem->bindParam(':time',$time);
    $newItem->bindParam(':published',$published);
  	$newItem->bindParam(':featured',$featured);
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
          if($setting['logo'] == "null" OR $setting['logo'] == NULL){$setting['logo'] = "";}
          if($setting['host'] == "null" OR $setting['host'] == NULL){$setting['host'] = "";}

          $settings[] = array(
            "lang" => $setting['lang'],
            "name" => $setting['name'],
            "description" => $setting['description'],
            "meta" => $setting['meta'],
            "title" => $setting['title'],
            "logo" => $setting['logo'],
            "host" => $setting['host']
          );
        }
        echo(json_encode($settings));
        die();
  }
  elseif($_POST['action'] == "editSettings"){
      $edit = $file_db->prepare("UPDATE settings SET name = :name, description = :description, meta = :meta, title = :title, logo = :logo, host = :host WHERE lang LIKE :lang");
      $edit->bindParam(":name",$name, SQLITE3_TEXT);
      $edit->bindParam(":description",$description, SQLITE3_TEXT);
      $edit->bindParam(":meta",$meta, SQLITE3_TEXT);
      $edit->bindParam(":logo",$logo, SQLITE3_TEXT);
      $edit->bindParam(":title",$title, SQLITE3_TEXT);
      $edit->bindParam(":lang",$lang, SQLITE3_TEXT);
      $edit->bindParam(":host",$host, SQLITE3_TEXT);
      for($i=0;$i<count($_POST['lang']);$i++){
        $name = $_POST['name'][$i];
        $description = $_POST['description'][$i];
        $meta = $_POST['meta'][$i];
        $title = $_POST['title'][$i];
        $logo = $_POST['logo'][$i];
        $lang = $_POST['lang'][$i];
        $host = $_POST['host'][$i];
        $edit->execute() or die('Unable to edit setting');
      }
  }
  elseif($_POST['action'] == "getCat"){
          $result = $file_db->prepare('SELECT * FROM category_lang cl LEFT JOIN category c ON c.id_cat = cl.id_cat where cl.id_cat = :cat');
          $result->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
          $result->execute() or die('AHAH');
          foreach ($result as $cat) {
            if($cat['image'] == "null" OR $cat['image'] == NULL){$cat['image'] = "";}
            if($cat['name'] == "null" OR $cat['name'] == NULL){$cat['name'] = "";}
            if($cat['description'] == "null" OR $cat['description'] == NULL){$cat['description'] = "";}
            if($cat['template'] == "null" OR $cat['template'] == NULL){$cat['template'] = "";}
            $cats[] = array(
              "lang" => $cat['lang'],
              "name" => $cat['name'],
              "description" => $cat['description'],
              "image" => $cat['image'],
              "template" => $cat['template']
            );
          }
          echo(json_encode($cats));
          die();
  }
  elseif($_POST['action'] == "editCat"){
    //Update priority
      $checkPriority = $file_db->prepare("SELECT priority FROM category WHERE id_cat = :cat");
      $checkPriority->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
      $checkPriority->execute() or die('Unable to check priority');
      $checkPriority = $checkPriority->fetch();
      //Update template
      $updateTemplate = $file_db->prepare("UPDATE category SET template = :template WHERE id_cat = :cat");
      $updateTemplate->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
      $updateTemplate->bindParam(":template",$_POST['template'],SQLITE3_TEXT);
      $updateTemplate->execute() or die('Unable to set new template');

      if($checkPriority['priority'] != $_POST['priority']){
        if($checkPriority['priority'] > $_POST['priority']){
          //New priority is higher
          $updatePriority = $file_db->prepare("UPDATE category SET priority = priority + 1 WHERE priority < :max AND priority >= :min");
          $updatePriority->bindParam(":min",$_POST['priority'],SQLITE3_INTEGER);
          $updatePriority->bindParam(":max",$checkPriority['priority'],SQLITE3_INTEGER);
        }
        else{
          //New priority is lower
          $updatePriority = $file_db->prepare("UPDATE category SET priority = priority - 1 WHERE priority <= :max AND priority > :min");
          $updatePriority->bindParam(":max",$_POST['priority'],SQLITE3_INTEGER);
          $updatePriority->bindParam(":min",$checkPriority['priority'],SQLITE3_INTEGER);
        }
        $updatePriority->execute() or die('Unable to shift priority');

        //Update the cat priority
        $priority = $file_db->prepare("UPDATE category SET priority = :priority WHERE id_cat = :cat");
        $priority->bindParam(":priority",$_POST['priority'], SQLITE3_INTEGER);
        $priority->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
        $priority->execute() or die('Unable to set new priority');
      }


    //Update info
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
          $result = $file_db->prepare('SELECT * FROM category_sub_lang csl LEFT JOIN category_sub cs ON cs.id_subcat = csl.id_subcat where csl.id_subcat = :cat');
          $result->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
          $result->execute() or die('AHAH');
          foreach ($result as $cat) {
            if($cat['image'] == "null" OR $cat['image'] == NULL){$cat['image'] = "";}
            if($cat['name'] == "null" OR $cat['name'] == NULL){$cat['name'] = "";}
            if($cat['description'] == "null" OR $cat['description'] == NULL){$cat['description'] = "";}
            if($cat['short'] == "null" OR $cat['short'] == NULL){$cat['short'] = "";}
            if($cat['template'] == "null" OR $cat['template'] == NULL){$cat['template'] = "";}
            if($cat['maxItem'] == "null" OR $cat['maxItem'] == NULL){$cat['maxItem'] = "";}
            if($cat['priority'] == "null" OR $cat['priority'] == NULL){$cat['priority'] = 1;}
            $cats[] = array(
              "lang" => $cat['lang'],
              "name" => $cat['name'],
              "description" => $cat['description'],
              "short" => $cat['short'],
              "image" => $cat['image'],
              "maxItem" => $cat['maxItem'],
              "template" => $cat['template'],
              "priority" => $cat['priority'],
            );
          }
          echo(json_encode($cats));
          die();
  }
  elseif($_POST['action'] == "editSubCat"){

      //Update template and maxItem
      $updateTemplate = $file_db->prepare("UPDATE category_sub SET template = :template, maxItem = :items, priority = :priority WHERE id_subcat = :cat");
      $updateTemplate->bindParam(":cat",$_POST['cat'], SQLITE3_INTEGER);
      $updateTemplate->bindParam(":items",$_POST['maxItem'], SQLITE3_INTEGER);
      $updateTemplate->bindParam(":priority",$_POST['priority'], SQLITE3_INTEGER);
      $updateTemplate->bindParam(":template",$_POST['template'],SQLITE3_TEXT);
      $updateTemplate->execute() or die('Unable to set new template');

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
    $info = $file_db->prepare("SELECT `time`, featured FROM item WHERE id_item = :item");
    $info->bindParam(":item",$_POST['item'], SQLITE3_INTEGER);
    $info->execute() or die('Unable to fetch info from item');
    $info = $info->fetch(PDO::FETCH_ASSOC);
    $cats['info'] = $info;
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
      $editItem = $file_db->prepare("UPDATE item SET featured = :featured, time = :time WHERE id_item = :item");
      $editItem->bindParam(":item",$_POST['item']);
      $editItem->bindParam(":featured",$_POST['featured']);
      $editItem->bindParam(":time",$datetime);
      $date = DateTime::createFromFormat("Y-m-d H:i",$_POST['date']." ".$_POST['time']);
      $datetime = $date->getTimestamp();
      $editItem->execute() or die('Unable to update general settings');

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
        $addtag->bindParam(":item",$_POST['item'],SQLITE3_INTEGER);
        $addtag->bindParam(":subcat",$_POST['tags'][$i],SQLITE3_INTEGER);
        $addtag->execute() or die();
      }
  }
  elseif($_POST['action'] == "deletePlugin"){
    $delete = $file_db->prepare("DELETE FROM plugins WHERE id_plugin = :plugin");
    $delete->bindParam(":plugin", $_POST['plugin'],SQLITE3_INTEGER);
    $delete->execute() or die('Unable to delete Plugin');
    $response = array("error" => 0);
    echo(JSON_encode($response));
    die();
  }
  elseif($_POST['action'] == "editPlugin"){
    $delete = $file_db->prepare("UPDATE plugins SET file = :file, public1 = :public1, public2 = :public2, public3 = :public3,int1 = :inte1 WHERE id_plugin = :plugin");
    if(endsWith($_POST['file'],".php")){$_POST[file] = substr($_POST['file'],0,-4);}
    $delete->bindParam(":plugin", $_POST['id_plugin'],SQLITE3_INTEGER);
    $delete->bindParam(":inte1", $_POST['int1'],SQLITE3_INTEGER);
    $delete->bindParam(":public1", $_POST['public1'],SQLITE3_TEXT);
    $delete->bindParam(":public2", $_POST['public2'],SQLITE3_TEXT);
    $delete->bindParam(":public3", $_POST['public3'],SQLITE3_TEXT);
    $delete->bindParam(":file", $_POST['file'],SQLITE3_TEXT);
    $delete->execute() or die('Unable to edit Plugin');
    $response = array("error" => 0);
  }
  elseif($_POST['action'] == "newContact"){
    $file_db->query("INSERT INTO contact DEFAULT VALUES");
    $reponse['id'] = $file_db->lastInsertId();
    $reponse['type'] = "empty";
    $reponse['value'] = "";
    $reponse['priority'] = $file_db->lastInsertId();
    $contactType = $file_db->query("SELECT id_type, name, template FROM contact_type");
    $contactType = $contactType->fetchAll(PDO::FETCH_ASSOC);
    $reponse = array("contact" => $reponse, "contactList" => $contactType);
    echo(JSON_encode($reponse));
    die();
  }
  elseif($_POST['action'] == "deleteContact"){
    $delete = $file_db->prepare("DELETE FROM contact WHERE id_contact = :contact");
    $delete->bindParam(":contact", $_POST['contact'],SQLITE3_INTEGER);
    $delete->execute() or die('Unable to delete Contact');
    $response = array("error" => 0);
    echo(JSON_encode($response));
    die();
  }
  elseif($_POST['action'] == "editContact"){
    $delete = $file_db->prepare("UPDATE contact SET type = :type, value = :value, priority = :priority WHERE id_contact = :contact");
    $delete->bindParam(":contact", $_POST['id_contact'],SQLITE3_INTEGER);
    $delete->bindParam(":value", $_POST['value'],SQLITE3_TEXT);
    $delete->bindParam(":priority", $_POST['priority'],SQLITE3_INTEGER);
    $delete->bindParam(":type", $_POST['type'],SQLITE3_INTEGER);
    $delete->execute() or die('Unable to edit Contact');
    $response = array("error" => 0);
  }
  elseif($_POST['action'] == "deleteSummary"){
    $delete = $file_db->prepare("DELETE FROM summary WHERE id_summary = :id");
    $delete->bindParam(":id", $_POST['id'],SQLITE3_INTEGER);
    $delete->execute() or die('Unable to delete Contact');
    $response = array("error" => 0);
    echo(JSON_encode($response));
    die();
  }
  elseif($_POST['action'] == "newSummary"){
    $file_db->query("INSERT INTO summary (id_subcat, `group`, priority, rows) VALUES (0, 1, 1, 3)");
    $reponse['id_summary'] = $file_db->lastInsertId();
    $reponse['name'] = "";
    $reponse['priority'] = 1;
    $reponse['rows'] = 3;
    $reponse['group'] = $file_db->lastInsertId();
    $subcatList = $file_db->prepare("SELECT id_subcat, name FROM category_sub_lang where lang LIKE :lang");
    $subcatList->bindParam(":lang",$_POST['lang']);
    $subcatList->execute() or die('Unable to fetch subcat names');
    $subcatList = $subcatList->fetchAll(PDO::FETCH_ASSOC);
    $reponse = array("summary" => $reponse, "subcatList" => $subcatList);
    echo(JSON_encode($reponse));
    die();
  }
  elseif($_POST['action'] == "getThisSummary"){
    $reponse = $file_db->prepare("SELECT s.id_summary, s.`group`, s.priority, s.rows,sl.name FROM summary s LEFT JOIN category_sub_lang sl ON sl.id_subcat = s.id_subcat AND sl.lang LIKE :lang WHERE s.id_summary = :summary");
    $reponse->bindParam(":lang", $_POST['lang']);
    $reponse->bindParam(":summary", $_POST['id']);
    $reponse->execute() or die('Unable to fetch this summary');
    $summary = $reponse->fetch(PDO::FETCH_ASSOC);
    ($summary['group'] != NULL ?:$summary['group'] = 1);
    ($summary['priority'] != NULL ?:$summary['priority'] = 1);
    ($summary['rows'] != NULL ?:$summary['rows'] = 3);
    ($summary['name'] != NULL ?:$summary['name'] = "");

    $subcatList = $file_db->prepare("SELECT id_subcat, name FROM category_sub_lang where lang LIKE :lang");
    $subcatList->bindParam(":lang",$_POST['lang']);
    $subcatList->execute() or die('Unable to fetch subcat names');
    $subcatList = $subcatList->fetchAll(PDO::FETCH_ASSOC);
    $reponse = array("summary" => $summary, "subcatList" => $subcatList);
    echo(JSON_encode($reponse));
    die();
  }
  elseif($_POST['action'] == "editSummary"){
    $delete = $file_db->prepare("UPDATE summary SET id_subcat = :subcat, `group` = :group, priority = :priority, rows = :rows WHERE id_summary = :summary");
    $delete->bindParam(":subcat", $_POST['subcat'],SQLITE3_INTEGER);
    $delete->bindParam(":group", $_POST['group'],SQLITE3_INTEGER);
    $delete->bindParam(":priority", $_POST['priority'],SQLITE3_INTEGER);
    $delete->bindParam(":rows", $_POST['rows'],SQLITE3_INTEGER);
    $delete->bindParam(":summary", $_POST['id'],SQLITE3_INTEGER);
    $delete->execute() or die('Unable to edit Contact');
    $response = array("error" => 0);
  }

  elseif($_POST['action'] == "deleteCat"){
    if($_POST['confirm'] == "DELETE"){
      $deleteCat = $file_db->prepare("DELETE FROM category WHERE id_cat = :cat;");
      $deleteCat->bindParam(":cat",$_POST['cat']);
      $deleteCat->execute() or die("Unable to remove Category");

      $deleteCatLang = $file_db->prepare("DELETE FROM category_lang WHERE id_cat = :cat;");
      $deleteCatLang->bindParam(":cat",$_POST['cat']);
      $deleteCatLang->execute() or die("Unable to remove Category");

      $getSubCat = $file_db->prepare("SELECT id_subcat FROM category_sub WHERE id_cat = :cat");
      $getSubCat->bindParam(":cat",$_POST['cat']);
      $getSubCat->execute() or die("Unable to fetch subcat");
      $subCats = $getSubCat->fetchALL(PDO::FETCH_ASSOC);
      $deleteSubCat = $file_db->prepare("DELETE FROM category_sub WHERE id_subcat = :subcat;");
      $deleteSubCatLang = $file_db->prepare("DELETE FROM category_sub_lang WHERE id_subcat = :subcat;");
      $deleteSubCatAssoc = $file_db->prepare("DELETE FROM item_assoc WHERE id_subcat = :subcat");
      $deleteSubCat->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);
      $deleteSubCatLang->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);
      $deleteSubCatAssoc->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);
      foreach ($subCats as $subCat) {
        $id_subcat = $subCat['id_subcat'];
        $deleteSubCatLang->execute() or die('Unable to remove subCat');
        $deleteSubCat->execute() or die('Unable to remove subCat');
        $deleteSubCatAssoc->execute() or die('Unable to remove subCat');
      }
    }
  }
  elseif($_POST['action'] == "deleteSubCat"){
    if($_POST['confirm'] == "DELETE"){
      $deleteSubCat = $file_db->prepare("DELETE FROM category_sub WHERE id_subcat = :subcat;");
      $deleteSubCatLang = $file_db->prepare("DELETE FROM category_sub_lang WHERE id_subcat = :subcat;");
      $deleteSubCatAssoc = $file_db->prepare("DELETE FROM item_assoc WHERE id_subcat = :subcat");
      $deleteSubCat->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);
      $deleteSubCatLang->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);
      $deleteSubCatAssoc->bindParam(":subcat",$id_subcat,SQLITE3_INTEGER);

      $id_subcat = $_POST['subCat'];
      $deleteSubCatLang->execute() or die('Unable to remove subCat');
      $deleteSubCat->execute() or die('Unable to remove subCat');
      $deleteSubCatAssoc->execute() or die('Unable to remove subCat');
    }
  }
  elseif($_POST['action'] == "deleteItem"){
    if($_POST['confirm'] == "DELETE"){
      $deleteItem = $file_db->prepare("DELETE FROM item WHERE id_item = :item;");
      $deleteItemLang = $file_db->prepare("DELETE FROM item_lang WHERE id_item = :item;");
      $deleteItemAssoc = $file_db->prepare("DELETE FROM item_assoc WHERE id_item = :item;");
      $deleteItem->bindParam(":item",$item,SQLITE3_INTEGER);
      $deleteItemLang->bindParam(":item",$item,SQLITE3_INTEGER);
      $deleteItemAssoc->bindParam(":item",$item,SQLITE3_INTEGER);

      $item = $_POST['item'];
      $deleteItem->execute() or die('Unable to remove item');
      $deleteItemLang->execute() or die('Unable to remove item');
      $deleteItemAssoc->execute() or die('Unable to remove item');
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
