<body>
<header class="white-bg">
  <nav id="logo" class="flex-row-fluid flex-center wrapper pad2">
    <!--<a href="/<?=$lang?>/" class="block"><img src="/img/HDlogo.png" id="logoImg" alt="logo" class="flex0"></a>-->
    <div class="flex1"></div>
  </nav>
  <div class="black-bg">
    <nav id="menu" class="flex-row-fluid flex-center wrapper">
  	<a href="/<?=$lang?>/" class="home block pushState flex0" data-title="Chaire"><?=$translation['nav_home']?></a>
  <?php
      $result = $file_db->prepare('SELECT id_cat,name,lang,image FROM category_lang WHERE lang LIKE :lang');
      $result->bindParam(":lang",$lang);
      $result->execute() or die('AHAH');
      foreach($result as $row){
        echo('    <a href="/'.$row['lang'].'/'.cleanString($row['name']).'" class="cat'.$row['id_cat'].' block pushState flex0" data-title="'.$row['name'].' / Chaire">'.$row['name'].'</a>'."\n");
      }
      if(isLogedNC()){
        ?>    <a class="block pushState flex0 admin" id="newCat"><?=$translation['admin_newCat']?></a>
  <?php
      }
  ?>
    </nav>
  </div>
</header>
