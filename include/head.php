<!DOCTYPE html>
<html  lang="<?=$lang?>">
<head>
  <meta charset="utf-8">
<?php
  $result = $file_db->prepare('SELECT name,description,title,meta FROM settings WHERE lang LIKE :lang');
  $result->bindParam(":lang",$lang);
  $result->execute() or die('AHAH');
  $header = $result->fetch();
  ?>
  <title><?=$header['title']?></title>
  <link href="/side/style.css" rel="stylesheet" type="text/css" />
  <script src="/side/jquery.js"></script>
  <script src="/side/function.js"></script>
  <script src="/side/hyphenator.js"></script>
  <link rel="icon" type="image/png" href="/img/favicon.png" />
  <meta name="description" content="<?=$header['meta']?>">
</head>
