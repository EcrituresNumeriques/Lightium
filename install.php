<?php
  include('include/config.ini.php');

    /**************************************
    * Create tables                       *
    **************************************/

    $file_db->exec("CREATE TABLE IF NOT EXISTS settings (name TEXT, description TEXT, title TEXT, meta TEXT,lang TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category (id_cat INTEGER PRIMARY KEY)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_sub (id_subcat INTEGER PRIMARY KEY, id_cat INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_lang (id_cat INTEGER, name TEXT, lang TEXT, image TEXT, description TEXT, cleanstring TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS category_sub_lang (id_subcat INTEGER, name TEXT, lang TEXT, image TEXT, short TEXT, description TEXT, cleanstring TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item (id_item INTEGER PRIMARY KEY, year INTEGER, month INTEGER, day INTEGER, published INTEGER, time INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_assoc (id_item INTEGER, id_subcat INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_lang (id_item INTEGER, title TEXT, short TEXT, content TEXT, cleanstring TEXT, lang TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS item_maj (id_item INTEGER, maj INTEGER, who TEXT)");

    /**************************************
    * Set initial data                    *
    **************************************/

    $cat = array("","","","","","");
    $insert = "INSERT INTO category (id_cat) VALUES (NULL)";
    $stmt = $file_db->prepare($insert);
    foreach ($cat as $c) {
      // Execute statement
      $stmt->execute();
    }

    $cat = array("1","1","1","1","1","1","2","2","2","2","2","3","3","3","3","3","4","4","4","4","4","5","5","5","6","6","6");
    $insert = "INSERT INTO category_sub (id_subcat,id_cat) VALUES (NULL,:id_cat)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(":id_cat",$c,SQLITE3_INTEGER);
    foreach ($cat as $c) {
      // Execute statement
      $stmt->execute();
    }


    $cat = array(
      array('cat' => 1,
            'name' => 'Équipe',
            'description' => 'Text pour l\'équipe',
            'lang' => 'FR'),
      array('cat' => 2,
            'name' => 'Outils',
            'description' => 'Text pour les outils',
            'lang' => 'FR'),
      array('cat' => 3,
            'name' => 'Axes',
            'description' => 'Text pour les axes',
            'lang' => 'FR'),
      array('cat' => 4,
            'name' => 'Champs',
            'description' => 'Text pour les champs',
            'lang' => 'FR'),
      array('cat' => 5,
            'name' => 'Concepts',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 6,
            'name' => 'Objets',
            'description' => 'Text pour les objets',
            'lang' => 'FR')
      );
    $insert = "INSERT INTO category_lang (id_cat, name, lang, image,description,cleanstring) VALUES (:id_cat,:name,:lang,NULL,:description,:cleanstring)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(':id_cat', $id_cat, SQLITE3_INTEGER);
    $stmt->bindParam(':name', $name, SQLITE3_TEXT);
    $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
    $stmt->bindParam(':description', $description, SQLITE3_TEXT);
    $stmt->bindParam(':cleanstring', $cleanstring, SQLITE3_TEXT);
    foreach ($cat as $c) {
      // Execute statement
      $id_cat = $c['cat'];
      $name = $c['name'];
      $lang = $c['lang'];
      $description = $c['description'];
      $cleanstring = cleanString($c['name']);
      $stmt->execute();
    }

    //subCat init
    $cat = array(
      array('cat' => 1,
            'name' => 'Marcello',
            'description' => 'Text pour les outils',
            'short' => 'cours text d\'explication',
            'lang' => 'FR'),
      array('cat' => 2,
            'name' => 'Emmanuel',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les outils',
            'lang' => 'FR'),
      array('cat' => 3,
            'name' => 'Servanne',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les axes',
            'lang' => 'FR'),
      array('cat' => 4,
            'name' => 'Marie-Christine',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les champs',
            'lang' => 'FR'),
      array('cat' => 5,
            'name' => 'Arthur',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 6,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 7,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 8,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 9,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 10,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 11,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 12,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 13,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 14,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 15,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 16,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 17,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 18,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 19,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 20,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 21,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 22,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 23,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 24,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 25,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 26,
            'name' => 'Concepts',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les concepts',
            'lang' => 'FR'),
      array('cat' => 27,
            'name' => 'Objets',
            'short' => 'cours text d\'explication',
            'description' => 'Text pour les objets',
            'lang' => 'FR')
      );
    $insert = "INSERT INTO category_sub_lang (id_subcat, name, lang, image,description,cleanstring,short) VALUES (:id_subcat,:name,:lang,NULL,:description,:cleanstring,:short)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(':id_subcat', $id_subcat, SQLITE3_INTEGER);
    $stmt->bindParam(':name', $name, SQLITE3_TEXT);
    $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
    $stmt->bindParam(':description', $description, SQLITE3_TEXT);
    $stmt->bindParam(':cleanstring', $cleanstring, SQLITE3_TEXT);
    $stmt->bindParam(':short', $short, SQLITE3_TEXT);
    foreach ($cat as $c) {
      // Execute statement
      $id_subcat = $c['cat'];
      $name = $c['name'];
      $lang = $c['lang'];
      $description = $c['description'];
      $short = $c['short'];
      $cleanstring = cleanString($c['name']);
      $stmt->execute();
    }

    //settings init

    $settings = array(
      array('name' => "Chaire de recherche",
            'description' => 'Mon texte qui va être encadré',
            'title' => 'Écritures Numériques',
            'meta' => 'ma description en meta',
            'lang' => 'FR')
          );
    $insert = "INSERT INTO settings (name, description, title, meta, lang) VALUES (:name,:description,:title,:meta,:lang)";
    $stmt = $file_db->prepare($insert);
    $stmt->bindParam(':name', $name, SQLITE3_TEXT);
    $stmt->bindParam(':description', $description, SQLITE3_TEXT);
    $stmt->bindParam(':title', $title, SQLITE3_TEXT);
    $stmt->bindParam(':meta', $meta, SQLITE3_TEXT);
    $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
    foreach ($settings as $c) {
      // Execute statement
      $description = $c['description'];
      $name = $c['name'];
      $lang = $c['lang'];
      $title = $c['title'];
      $meta = $c['meta'];
      $stmt->execute();
    }

	//items init
    //$file_db->exec("CREATE TABLE IF NOT EXISTS item (id_item INTEGER PRIMARY KEY, year INTEGER, month INTEGER, day INTEGER, published INTEGER, time INTEGER)");
    //$file_db->exec("CREATE TABLE IF NOT EXISTS item_assoc (id_item INTEGER, id_subcat INTEGER)");
    //$file_db->exec("CREATE TABLE IF NOT EXISTS item_lang (id_item INTEGER, title TEXT, short TEXT, content TEXT, cleanstring TEXT, lang TEXT");
    //$file_db->exec("CREATE TABLE IF NOT EXISTS item_update (id_item INTEGER, update INTEGER, who TEXT)");

    $items = array(
      array('title' => "Le premier item",
            'short' => 'Le résumé du texte',
            'content' => '<p>yes yes yes yes!</p>',
            'year' => '2015',
            'month' => '12',
            'day' => '13',
            'lang' => 'FR')
          );
	$newItem = $file_db->prepare("INSERT INTO item (id_item, year, month, day, published, time) VALUES (NULL,:year,:month,:day,:time,:published)");
	$newItem->bindParam(':year',$year);
	$newItem->bindParam(':month',$month);
	$newItem->bindParam(':day',$day);
	$newItem->bindParam(':time',$time);
	$newItem->bindParam(':published',$published);
    $langItem = $file_db->prepare("INSERT INTO item_lang (id_item, title, short, content, cleanstring, lang) VALUES (:id_item,:title,:short,:content,:cleanstring,:lang)");
	$langItem->bindParam(':id_item',$id_item);
	$langItem->bindParam(':title',$title);
	$langItem->bindParam(':short',$short);
	$langItem->bindParam(':content',$content);
	$langItem->bindParam(':cleanstring',$cleanstring);
	$langItem->bindParam(':lang',$lang);

	foreach($items as $item){
		$year = $item['year'];
		$month = $item['month'];
		$day = $item['day'];
		$time = time();
		$published = time();
		$newItem->execute() or die('Unable to add item');
		$id_item = $file_db->lastInsertId();
		$title = $item['title'];
		$short = $item['short'];
		$content = $item['content'];
		$cleanstring = cleanString($item['title']);	
		$lang = $item['lang'];
		$langItem->execute() or die('Unable to add lang item');
	}

	//items lang creation
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,1)");
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,2)");
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,5)");


?>
