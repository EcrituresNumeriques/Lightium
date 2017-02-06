<!DOCTYPE html>
<html  lang="<?=$lang?>">
<head>
  <meta charset="utf-8">
<?php
  $result = $file_db->prepare('SELECT logo,name,description,title,meta,favicon FROM settings WHERE lang LIKE :lang');
  $result->bindParam(":lang",$lang);
  $result->execute() or die('AHAH');
  $header = $result->fetch();
  (!empty($header['favicon'])?:$header['favicon'] = '/img/favicon.png');
  ?>
  <title><?=$header['title']?></title>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="/side/style.css" rel="stylesheet" type="text/css">
  <link href="/<?=$lang?>/side/customCss.css" rel="stylesheet" type="text/css">
  <script src="/side/jquery.js"></script>
  <script src="/side/function.js"></script>
  <script src="/side/hyphenator.js"></script>
  <script src="/<?=$lang?>/side/customJS.js"></script>
  <link rel="icon" type="image/png" href="<?=$header['favicon']?>">
  <meta name="description" content="<?=$header['meta']?>">
  <style>
  <?php
  if(isLoged()){
  ?>
    a.admin{
      display:block;
      cursor:pointer;
    }
    <?php
  }
  else{
    ?>
    a.admin{
      display:none;
    }
  <?php
  }
  ?>
</style>
<script>
  translation = <?=JSON_encode($translation)?>;
</script>
</head>
