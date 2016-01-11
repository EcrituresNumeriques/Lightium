<?php
include('include/config.ini.php');
include('include/head.php');
include('include/header.php');

//Filter the content to display ($_GET['action'] is set in the .htacess)

//A post is requested
if(!empty($_GET['article'])){
	$current = drawCookieTrail($lang,$_GET['cat'],$_GET['subcat'],$_GET['year'],$_GET['month'],$_GET['day']);
	drawArticle($file_db, $lang, $_GET['year'], $_GET['month'], $_GET['day'], $_GET['article'],$translation);
}
//history is required
elseif(!empty($_GET['year'])){
	if(!empty($_GET['day'])){
		$current = drawCookieTrail($lang,NULL,NULL,$_GET['year'],$_GET['month'],$_GET['day']);
		echo('<div class="flex-row-fluid flex-top wrapper">');
		drawListing($file_db, $translation, $current,$lang,'day',$_GET['year']."/".$_GET['month']."/".$_GET['day']);
	}
	elseif(!empty($_GET['month'])){
		$current = drawCookieTrail($lang,NULL,NULL,$_GET['year'],$_GET['month']);
		echo('<div class="flex-row-fluid flex-top wrapper">');
		drawListing($file_db, $translation, $current,$lang,'month',$_GET['year']."/".$_GET['month']);
	}
	else{
		$current = drawCookieTrail($lang,NULL,NULL,$_GET['year']);
		echo('<div class="flex-row-fluid flex-top wrapper">');
		drawListing($file_db, $translation, $current,$lang,'year',$_GET['year']);
	}
	echo('</div>');
}
//Sub Category
elseif(!empty($_GET['subcat'])){
  $category = $file_db->prepare("SELECT cs.id_cat, csl.id_subcat, csl.name, csl.image, csl.description FROM category_sub_lang csl JOIN category_sub cs ON csl.id_subcat = cs.id_subcat WHERE cleanString LIKE :cat AND lang LIKE :lang");
  $category->bindParam(":cat",$_GET['subcat'], SQLITE3_TEXT);
  $category->bindParam(":lang",$lang, SQLITE3_TEXT);
  $category->execute() or die('unable to find category');
  $cat = $category->fetch();
  if(!empty($cat['name'])){
    drawLead("cat".$cat['id_cat'],$cat['name'],$cat['description'],'subcat',$cat['id_subcat'],$translation,$lang);
    $current = drawCookieTrail($lang,$_GET['cat'],$_GET['subcat']);
	echo('<div class="flex-row-fluid flex-top wrapper">');
    drawListing($file_db, $translation, $current,$lang,'subcat',$cat['id_subcat']);
	echo('</div>');
  }
  else{
    echo404($_GET['subcat']);
  }
}
//Category
elseif(!empty($_GET['cat'])){
  $category = $file_db->prepare("SELECT id_cat, name, image, description FROM category_lang WHERE cleanString LIKE :cat AND lang LIKE :lang");
  $category->bindParam(":cat",$_GET['cat'], SQLITE3_TEXT);
  $category->bindParam(":lang",$lang, SQLITE3_TEXT);
  $category->execute() or die('unable to find category');
  $cat = $category->fetch();
  if(!empty($cat['name'])){
    drawLead("cat".$cat['id_cat'],$cat['name'],$cat['description'],'cat',$cat['id_cat'],$translation,$lang);
    $current = drawCookieTrail($lang,$cat['name']);
	echo('<div class="flex-row-fluid wrapper">');
    drawListing($file_db, $translation, $current,$lang,'cat',$cat['id_cat']);
	echo('</div>');
  }
  else{
    $current = drawCookieTrail($lang,$cat['name']);
    echo404($_GET['cat']);
  }
}
//Index
else{
	drawLead('index',$header['name'],$header['description'],'index',0,$translation,$lang);
	$current = drawCookieTrail($lang);
	echo('<div class="flex-row-fluid flex-top wrapper">');
	drawListing($file_db, $translation, $current,$lang,'index',NULL);
	drawCalendar($file_db, $translation);
	echo('</div>');
}

?>
<?php
include('include/footer.php');
include('include/close.php');
?>
