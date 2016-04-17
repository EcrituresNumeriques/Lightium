<?php

   //Installation process
   $query = $file_db->query("SELECT count(*) as yes FROM sqlite_master WHERE type='table' AND name='user'");
   $query->execute() or die("Could'nt exec user table check");
   $query = $query->fetch();
   if($query['yes']){


     //Check if there is an User in the database
      $query = $file_db->query("SELECT count(*) as users FROM user");
      $query->execute() or die("Could'nt exec users check");
      $query = $query->fetch();
      if($query['users'] == 0){
        //No user inputed, can't continue till user > 0 not satisfied
        if(!empty($_POST['user']) AND !empty($_POST['password'])){
          //add new user
          	$user = array(
          				array(
          					'username' => $_POST['user'],
          					'pswd' => $_POST['password']
          				)
          			);
          	$insert = $file_db->prepare("INSERT INTO user (id_user,token,username,salt,hash) VALUES (NULL,NULL,:username,:salt,:hash)");
          	$insert->bindParam(":username",$username);
          	$insert->bindParam(":salt",$salt);
          	$insert->bindParam(":hash",$hash);
          	foreach($user as $u){
          		$salt = base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
              	$password = crypt($u['pswd'], '$6$rounds=1000$'.$salt);
              	$password = explode("$",$password);
              	$hash = $password[4];
          		$username = $u['username'];
          		$insert->execute() or die('Unable to add new user');
          	}
          //then continue on the update process
        }
        else{
          //submit form for login infos
          //TODO make it more beautifull
          ?>
          <!DOCTYPE html>
          <head>
            <link href="/side/style.css" rel="stylesheet" type="text/css"></head>
          <body>
            <form action="" method="post">
            <input type="text" name="user" placeholder="<?=$translation['username']?>">
            <input type="password" name="password" placeholder="<?=$translation['password']?>">
            <input type="submit">
          <?php
          die();
        }
      }


      //Check if the website has languages defined
      $query = $file_db->query("SELECT count(*) as settings FROM settings");
      $query->execute() or die("Could'nt exec users check");
      $query = $query->fetch();
      if($query['settings'] == 0){
        if(!empty($_POST['name']) AND !empty($_POST['description']) AND !empty($_POST['title']) AND !empty($_POST['meta']) AND !empty($_POST['lang'])){
            $insert = "INSERT INTO settings (name, description, title, meta, lang) VALUES (:name,:description,:title,:meta,:lang)";
            $stmt = $file_db->prepare($insert);
            $stmt->bindParam(':name', $name, SQLITE3_TEXT);
            $stmt->bindParam(':description', $description, SQLITE3_TEXT);
            $stmt->bindParam(':title', $title, SQLITE3_TEXT);
            $stmt->bindParam(':meta', $meta, SQLITE3_TEXT);
            $stmt->bindParam(':lang', $lang, SQLITE3_TEXT);
            for ($i = 0; $i < count($_POST['lang']); $i++) {
                // Execute statement
                $description = $_POST['description'][$i];
                $name = $_POST['name'][$i];
                $lang = $_POST['lang'][$i];
                $title = $_POST['title'][$i];
                $meta = $_POST['meta'][$i];
                $stmt->execute();
            }
          //then continue on the update process
        }
        else{
          //submit form for login infos
          //TODO make it more beautifull
          ?>
          <!DOCTYPE html>
          <head>
          <script src="/side/jquery.js"></script>
          <script type="text/javascript">
            $(document).ready(function(){
              $("#addLanguage").on("click",function(){
                $("#submit").before(' <input id="languages" type="text" name="name[]" placeholder="<?=$translation['admin_settingsSiteName']?>"><textarea name="description[]" placeholder="<?=$translation['admin_settingsSiteDescription']?>"></textarea><textarea name="meta[]" placeholder="<?=$translation['admin_settingsSiteMeta']?>"></textarea><input type="text" name="title[]" placeholder="<?=$translation['admin_settingsSiteTitle']?>"><input type="text" name="lang[]" placeholder="<?=$translation['admin_settingsSiteLang']?>"><hr>');
              });
            });
          </script>
            <link href="/side/style.css" rel="stylesheet" type="text/css">
          </head>
          <body>
            <p id="addLanguage"><?=$translation['addLanguage']?></p>
            <form action="" method="post">
            <input id="submit" type="submit">
          <?php
          die();
        }


      }



      //Check if the website has a version
      $query = $file_db->query("SELECT count(*) as yes FROM sqlite_master WHERE type='table' AND name='version'");
      $query->execute() or die("Could'nt exec user table check");
      $query = $query->fetch();
      if($query['yes']){
        $version = $file_db->query("SELECT version,subversion,revision FROM version");
        $version = $version->fetch();
        //Nest every update here

        //If version prior to version 1
        if($version['version'] < 1){
            if($version['subversion'] < 2){
              if($version['revision'] < 3){
                //VERSION 0.1.3 : Add version table + null filter in the API responses + add item even if there is no subCat + Javascript correction
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 3");
              }
              if($version['revision'] < 4){
                //VERSION 0.1.4 : Add calendar table logics
                $file_db->exec("CREATE TABLE IF NOT EXISTS events (id_event INTEGER PRIMARY KEY, time INTEGER)");
                $file_db->exec("CREATE TABLE IF NOT EXISTS events_lang (id_event INTEGER, title TEXT, location TEXT, short TEXT, description TEXT, lang TEXT)");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 4");
              }
              if($version['revision'] < 5){
                $file_db->exec("ALTER TABLE item ADD COLUMN zoterokey TEXT");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 5");
              }
              if($version['revision'] < 6){
                $file_db->exec("ALTER TABLE category ADD COLUMN priority INTEGER");
                $file_db->exec("CREATE TRIGGER setPriorityOnInsert AFTER INSERT ON category FOR EACH ROW WHEN NEW.priority IS NULL BEGIN UPDATE category SET priority = NEW.id_cat WHERE id_cat = NEW.id_cat; END;");
                $file_db->exec("UPDATE category SET priority = id_cat");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 6");
              }
              if($version['revision'] < 7){
                $file_db->exec("CREATE TABLE IF NOT EXISTS plugins (id_plugin INTEGER PRIMARY KEY, file TEXT, public1 TEXT, public2 TEXT, public3 TEXT, int1 INTEGER DEFAULT 0, int2 INTEGER DEFAULT 0, int3 INTEGER DEFAULT 0,txt1 TEXT,txt2 TEXT,txt3 TEXT)");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 7");
              }
              if($version['revision'] < 8){
                $file_db->exec("ALTER TABLE events ADD COLUMN endTime INTEGER");
                $file_db->exec("ALTER TABLE events ADD COLUMN Gkey TEXT");
                $file_db->exec("ALTER TABLE events ADD COLUMN phase INTEGER");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 8");
              }
              if($version['revision'] < 9){
                $file_db->exec("ALTER TABLE settings ADD COLUMN logo TEXT");
                $file_db->exec("ALTER TABLE category ADD COLUMN template TEXT");
                $file_db->exec("ALTER TABLE category_sub ADD COLUMN template TEXT");
                $file_db->exec("ALTER TABLE category_sub ADD COLUMN maxItem INTEGER DEFAULT 10");
                $file_db->exec("CREATE TABLE IF NOT EXISTS customCSS (time INTEGER DEFAULT 0, CSS TEXT)");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 9");
              }
              if($version['revision'] < 10){
                $file_db->exec("INSERT INTO customCSS (time, CSS) VALUES (0,'')");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 10");
              }
              if($version['revision'] < 11){
                $file_db->exec("ALTER TABLE settings ADD COLUMN host TEXT");
                $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 11");
              }
              $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 0");
            }
            //Add the contact menu on
            if($version['subversion'] < 3){
              if($version['revision'] < 1){
                $file_db->exec("CREATE TABLE IF NOT EXISTS contact (id_contact INTEGER PRIMARY KEY, lang TEXT, type INTEGER, value TEXT, priority INTEGER)");
                $file_db->exec("CREATE TABLE IF NOT EXISTS contact_type (id_type INTEGER PRIMARY KEY, name TEXT, template TEXT)");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 1");
              }
              if($version['revision'] < 2){
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'E-mail','<a href=\"mailto:±VALUE±\" target=\"_blank\" class=\"contactEmail\">±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Phone','<p class=\"contactPhone\">±VALUE±</p>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Fax','<p class=\"contactFax\">±VALUE±</p>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Text','<p class=\"contactText\">±VALUE±</p>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Section','<h1 class=\"contactSection\">±VALUE±</h1>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Twitter','<a href=\"https://twitter.com/±VALUE±\" target=\"_blank\" class=\"contactTwitter\">@±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Facebook','<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactFacebook\">±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Gcalendar','<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactCalendar\">Gcalendar</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Github','<a href=\"https://github.com/±VALUE±\" target=\"_blank\" class=\"contactGithub\">±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Papyrus','<a href=\"https://papyrus.bib.umontreal.ca/xmlui/handle/±VALUE±\" target=\"_blank\" class=\"contactPapyrus\">Dépôt Papyrus</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Zotero','<a href=\"https://www.zotero.org/groups/±VALUE±\" target=\"_blank\" class=\"contactZotero\">±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Vimeo','<a href=\"https://vimeo.com/±VALUE±\" target=\"_blank\" class=\"contactVimeo\">±VALUE±</a>')");
                $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'URL','<a href=\"±VALUE±\" target=\"_blank\" class=\"contactURL\">±VALUE±</a>')");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 2");
              }
              if($version['revision'] < 3){
                $file_db->exec("CREATE TABLE IF NOT EXISTS summary (id_summary INTEGER PRIMARY KEY, id_subcat INTEGER, `group` INTEGER, rows INTEGER, priority INTEGER)");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 3");
              }
              if($version['revision'] < 4){
                  $file_db->exec("ALTER TABLE item ADD COLUMN featured INTEGER");
                  $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 4");
              }
              if($version['revision'] < 5){
                $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://www.zotero.org/groups/±VALUE±\" target=\"_blank\" class=\"contactZotero\">Zotero</a>' WHERE name = 'Zotero'");
                $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://github.com/±VALUE±\" target=\"_blank\" class=\"contactGithub\">Github</a>' WHERE name = 'Github'");
                $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://vimeo.com/±VALUE±\" target=\"_blank\" class=\"contactVimeo\">Vimeo</a>' WHERE name = 'Vimeo'");
                $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactFacebook\">Facebook</a>' WHERE name = 'Facebook'");
                $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://calendar.google.com/calendar/embed?src=±VALUE±\" target=\"_blank\" class=\"contactCalendar\">Gcalendar</a>' WHERE name = 'Gcalendar'");
                $file_db->exec("ALTER TABLE category_sub ADD COLUMN priority INTEGER DEFAULT 1");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 5");
              }
              if($version['revision'] < 6){
                $file_db->exec("CREATE TABLE IF NOT EXISTS footer (time INTEGER DEFAULT 0, footer TEXT)");
                $file_db->exec("INSERT INTO footer (time, footer) VALUES (0,'')");
                $file_db->exec("CREATE TABLE IF NOT EXISTS header (time INTEGER DEFAULT 0, header TEXT)");
                $file_db->exec("INSERT INTO header (time, header) VALUES (0,'')");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 6");
              }
              if($version['revision'] < 7){
                $file_db->exec("ALTER TABLE category_sub_lang ADD COLUMN caption TEXT");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 7");
              }
              if($version['revision'] < 8){
                $file_db->exec("ALTER TABLE item_lang ADD COLUMN caption TEXT");
                $file_db->exec("ALTER TABLE item_lang ADD COLUMN image TEXT");
                $file_db->exec("ALTER TABLE item_lang ADD COLUMN url TEXT");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 8");
              }
              if($version['revision'] < 9){
                $file_db->exec("ALTER TABLE item_lang ADD COLUMN urlTitle TEXT");
                $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 9");
              }
            }
        }


      }
      else{
        $file_db->exec("CREATE TABLE IF NOT EXISTS version (version INTEGER, subversion INTEGER, revision INTEGER)");
        $file_db->exec("INSERT INTO version (version, subversion, revision) VALUES (0,0,0)");
      }


   }
   else{

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
    $file_db->exec("CREATE TABLE IF NOT EXISTS user (id_user INTEGER PRIMARY KEY, token TEXT,username TEXT, salt TEXT, hash TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS events (id_event INTEGER PRIMARY KEY, time INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS events_lang (id_event INTEGER, title TEXT, location TEXT, short TEXT, description TEXT, lang TEXT)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS version (version INTEGER, subversion INTEGER, revision INTEGER)");
    $file_db->exec("INSERT INTO version (version, subversion, revision) VALUES (0,0,0)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 3");
    $file_db->exec("CREATE TABLE IF NOT EXISTS events (id_event INTEGER PRIMARY KEY, time INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS events_lang (id_event INTEGER, title TEXT, location TEXT, short TEXT, description TEXT, lang TEXT)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 4");
    $file_db->exec("ALTER TABLE item ADD COLUMN zoterokey TEXT");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 5");
    $file_db->exec("ALTER TABLE category ADD COLUMN priority INTEGER");
    $file_db->exec("CREATE TRIGGER setPriorityOnInsert AFTER INSERT ON category FOR EACH ROW WHEN NEW.priority IS NULL BEGIN UPDATE category SET priority = NEW.id_cat WHERE id_cat = NEW.id_cat; END;");
    $file_db->exec("UPDATE category SET priority = id_cat");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 6");
    $file_db->exec("CREATE TABLE IF NOT EXISTS plugins (id_plugin INTEGER PRIMARY KEY, file TEXT, public1 TEXT, public2 TEXT, public3 TEXT, int1 INTEGER DEFAULT 0, int2 INTEGER DEFAULT 0, int3 INTEGER DEFAULT 0,txt1 TEXT,txt2 TEXT,txt3 TEXT)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 7");
    $file_db->exec("ALTER TABLE events ADD COLUMN endTime INTEGER");
    $file_db->exec("ALTER TABLE events ADD COLUMN Gkey TEXT");
    $file_db->exec("ALTER TABLE events ADD COLUMN phase INTEGER");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 8");
    $file_db->exec("ALTER TABLE settings ADD COLUMN logo TEXT");
    $file_db->exec("ALTER TABLE category ADD COLUMN template TEXT");
    $file_db->exec("ALTER TABLE category_sub ADD COLUMN template TEXT");
    $file_db->exec("ALTER TABLE category_sub ADD COLUMN maxItem INTEGER DEFAULT 10");
    $file_db->exec("CREATE TABLE IF NOT EXISTS customCSS (time INTEGER DEFAULT 0, CSS TEXT)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 9");
    $file_db->exec("INSERT INTO customCSS (time, CSS) VALUES (0,'')");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 10");
    $file_db->exec("ALTER TABLE settings ADD COLUMN host TEXT");
    $file_db->exec("UPDATE version SET version = 0, subversion = 1, revision = 11");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 0");
    $file_db->exec("CREATE TABLE IF NOT EXISTS contact (id_contact INTEGER PRIMARY KEY, lang TEXT, type INTEGER, value TEXT, priority INTEGER)");
    $file_db->exec("CREATE TABLE IF NOT EXISTS contact_type (id_type INTEGER PRIMARY KEY, name TEXT, template TEXT)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 1");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'E-mail','<a href=\"mailto:±VALUE±\" target=\"_blank\" class=\"contactEmail\">±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Phone','<p class=\"contactPhone\">±VALUE±</p>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Fax','<p class=\"contactFax\">±VALUE±</p>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Text','<p class=\"contactText\">±VALUE±</p>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Section','<h1 class=\"contactSection\">±VALUE±</h1>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Twitter','<a href=\"https://twitter.com/±VALUE±\" target=\"_blank\" class=\"contactTwitter\">@±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Facebook','<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactFacebook\">±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Gcalendar','<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactCalendar\">Gcalendar</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Github','<a href=\"https://github.com/±VALUE±\" target=\"_blank\" class=\"contactGithub\">±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Papyrus','<a href=\"https://papyrus.bib.umontreal.ca/xmlui/handle/±VALUE±\" target=\"_blank\" class=\"contactPapyrus\">Dépôt Papyrus</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Zotero','<a href=\"https://www.zotero.org/groups/±VALUE±\" target=\"_blank\" class=\"contactZotero\">±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'Vimeo','<a href=\"https://vimeo.com/±VALUE±\" target=\"_blank\" class=\"contactVimeo\">±VALUE±</a>')");
    $file_db->exec("INSERT INTO contact_type (id_type, name, template) VALUES (NULL,'URL','<a href=\"±VALUE±\" target=\"_blank\" class=\"contactURL\">±VALUE±</a>')");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 2");
    $file_db->exec("CREATE TABLE IF NOT EXISTS summary (id_summary INTEGER PRIMARY KEY, id_subcat INTEGER, `group` INTEGER, rows INTEGER, priority INTEGER)");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 3");
    $file_db->exec("ALTER TABLE item ADD COLUMN featured INTEGER");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 4");
    $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://www.zotero.org/groups/±VALUE±\" target=\"_blank\" class=\"contactZotero\">Zotero</a>' WHERE name = 'Zotero'");
    $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://github.com/±VALUE±\" target=\"_blank\" class=\"contactGithub\">Github</a>' WHERE name = 'Github'");
    $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://vimeo.com/±VALUE±\" target=\"_blank\" class=\"contactVimeo\">Vimeo</a>' WHERE name = 'Vimeo'");
    $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://www.facebook.com/±VALUE±\" target=\"_blank\" class=\"contactFacebook\">Facebook</a>' WHERE name = 'Facebook'");
    $file_db->exec("UPDATE contact_type SET template = '<a href=\"https://calendar.google.com/calendar/embed?src=±VALUE±\" target=\"_blank\" class=\"contactCalendar\">Gcalendar</a>' WHERE name = 'Gcalendar'");
    $file_db->exec("ALTER TABLE category_sub ADD COLUMN priority INTEGER DEFAULT 1");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 5");
    $file_db->exec("CREATE TABLE IF NOT EXISTS footer (time INTEGER DEFAULT 0, footer TEXT)");
    $file_db->exec("INSERT INTO footer (time, footer) VALUES (0,'')");
    $file_db->exec("CREATE TABLE IF NOT EXISTS header (time INTEGER DEFAULT 0, header TEXT)");
    $file_db->exec("INSERT INTO header (time, header) VALUES (0,'')");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 6");
    $file_db->exec("ALTER TABLE category_sub_lang ADD COLUMN caption TEXT");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 7");
    $file_db->exec("ALTER TABLE item_lang ADD COLUMN caption TEXT");
    $file_db->exec("ALTER TABLE item_lang ADD COLUMN image TEXT");
    $file_db->exec("ALTER TABLE item_lang ADD COLUMN url TEXT");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 8");
    $file_db->exec("ALTER TABLE item_lang ADD COLUMN urlTitle TEXT");
    $file_db->exec("UPDATE version SET version = 0, subversion = 2, revision = 9");

    header("location:".$_SERVER['PHP_SELF']);
}

?>
