<?php

function inputSecurity($validate=null) {
    if ($validate == null) {
        foreach ($_REQUEST as $key => $val) {
            if (is_string($val)) {
                $_REQUEST[$key] = htmlentities($val);
            } else if (is_array($val)) {
                $_REQUEST[$key] = inputSecurity($val);
            }
        }
        foreach ($_GET as $key => $val) {
            if (is_string($val)) {
                $_GET[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
            } else if (is_array($val)) {
                $_GET[$key] = inputSecurity($val);
            }
        }
        foreach ($_POST as $key => $val) {
            if (is_string($val)) {
                $_POST[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
            } else if (is_array($val)) {
                $_POST[$key] = inputSecurity($val);
            }
        }
    } else {
        foreach ($validate as $key => $val) {
            if (is_string($val)) {
                $validate[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
            } else if (is_array($val)) {
                $validate[$key] = inputSecurity($val);
            }
            return $validate;
        }
    }
}

function isEqual($what,$toWhat){
	(isset($what) AND isset($toWhat) AND $what == $toWhat ? $return  = true : $return  = false );
	return $return;
}

function notNull($what){
	(!empty($what) ? $return  = true : $return  = false );
	return $return;
}

function cleanString($string) {
   $utf8 = array(
        '/[0]/u'   =>   'zero',
        '/[1]/u'   =>   'one',
        '/[2]/u'   =>   'two',
        '/[3]/u'   =>   'three',
        '/[4]/u'   =>   'for',
        '/[5]/u'   =>   'five',
        '/[6]/u'   =>   'six',
        '/[7]/u'   =>   'seven',
        '/[8]/u'   =>   'eight',
        '/[9]/u'   =>   'nine',
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace(array_keys($utf8), array_values($utf8), $string);
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function drawLead($class, $name, $description){
  ?>
  <section id="chapeau" class="<?=$class?>">
    <article class="wrapper">
      <!--<nav id="goLeft"><a href=""><</a></nav>
      <nav id="goRight"><a href="">></a></nav>-->
      <h1><?=$name?></h1>
      <p><?=$description?></p>
    </article>
  </section>
  <?php
}

function drawCookieTrail($lang = "",$category = "",$subCat = "",$year = "",$month = "",$day = "",$article = ""){
  $echo = "/".$lang."/";
  $echoLink = '/<a href="'.$echo.'">'.$lang.'</a>';
  if(notNull($category)){
    $echo .= cleanString($category)."/";
    $echoLink .= '/<a href="'.$echo.'">'.$category.'</a>';
  }
  if(notNull($subCat)){
    $echo .= cleanString($subCat)."/";
    $echoLink .= '/<a href="'.$echo.'">'.$subCat.'</a>';
  }
  if(notNull($year)){
    $echo .= $year."/";
    $echoLink .= '/<a href="'.$echo.'">'.$year.'</a>';
  }
  if(notNull($month)){
    $echo .= $month."/";
    $echoLink .= '/<a href="'.$echo.'">'.$month.'</a>';
  }
  if(notNull($day)){
    $echo .= $day."/";
    $echoLink .= '/<a href="'.$echo.'">'.$day.'</a>';
  }
  if(notNull($article)){
    $echo .= cleanString($article)."/";
    $echoLink .= '/<a href="'.$echo.'">'.$article.'</a>';
  }
  ?>
  <section id="cookieTrail" class="wrapper pad2">
    <p><?=$echoLink?></p>
  </section>
  <?php
  return $echo;
}

function drawArticle($db, $lang, $year, $month, $day, $cleanstring){
	$query = $db->prepare("SELECT i.id_item,il.title, il.short,il.content, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.year = :year AND i.month = :month AND i.day = :day AND il.cleanstring LIKE :cleanstring LIMIT 0,1");
	$query->bindParam(':lang',$lang, SQLITE3_TEXT);
	$query->bindParam(':year',$year, SQLITE3_INTEGER);
	$query->bindParam(':month',$month, SQLITE3_INTEGER);
	$query->bindParam(':day',$day, SQLITE3_INTEGER);
	$query->bindParam(':cleanstring',$cleanstring, SQLITE3_TEXT);
	$query->execute() or die('Unable to recover article');
	$article = $query->fetch();
	?>
		<section id="article" class="wrapper">
			<article class="hyphenate">
				<h1><?=$article['title']?></h1>
				<?=drawTags($lang,$article['subcat'])?>
				<h2><?=$article['short']?></h2>
				<?=$article['content']?>
			</article>
		</section>

<?php

}

function drawTags($lang, $tags){
	$tags = explode('/',$tags,3);
    $subCat = explode(';',$tags[0]);
    $subCatName = explode(';',$tags[1]);
    $catName = explode(';',$tags[2]);
  ?>
	<nav class="flex-row-fluid flex-center">
    <?php
    for($i = 0;$i < count($subCat);$i++){
      $url = '/'.$lang.'/'.cleanString($catName[$i]).'/'.cleanString($subCatName[$i]).'/';
      ?><a href="<?=$url?>" class="cat<?=$subCat[$i]?> block"><?=$subCatName[$i]?></a><?php
    }  ?>
    </nav>
	<?php
}

function drawListing($db, $translation, $current, $lang, $action, $what){
  echo('<section id="listing" class="flex3">');
if($action == "cat"){
  $query = $db->prepare('SELECT csl.name,csl.image,csl.short FROM category_sub_lang csl JOIN category_sub cs ON csl.id_subcat = cs.id_subcat WHERE cs.id_cat = :id_cat AND csl.lang LIKE :lang');
  $query->bindParam(":lang",$lang, SQLITE3_TEXT);
  $query->bindParam(":id_cat",$what, SQLITE3_INTEGER);
  $query->execute() or die('Unable to fetch Items');
}
elseif($action == "index"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT 0,10");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->execute() or die('Unable to fetch Items');
}
elseif($action == "subcat"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN item_assoc ia2 ON i.id_item = ia2.id_item AND ia2.id_subcat = :subcat JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT 0,10");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':subcat',$what, SQLITE3_INTEGER);
  $query->execute() or die('Unable to fetch Items');
}
  $rowCount = 0;
  foreach($query as $row){
  $image = $tags = "";
  $rowCount++;
	if($action == "cat"){
		$url = $current.cleanString($row['name']);
  		(notNull($row['image']) ? $image = '<a href="'.$url.'" class="listingFloat block pushState"><img src="'.$row['image'].'"></a>' : $image = "");
		$title = $row['name'];
	}
	elseif($action == "index"){
		$url = $current.$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(notNull($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
	}
	elseif($action == "subcat"){
		$url = $current.$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(notNull($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
	}
?>
  <article class="clear hyphenate">
    <?=$image?>
    <h1><a href="<?=$url?>" class="pushState"><?=$title?></a></h1>
    <p><?=$row['short']?></p><?php
  if(notNull($tags)){
	drawTags($lang,$tags);
	}
?>
  <div class="clear"></div>
  </article>
<?php
}
if($rowCount < 1){

?>
  <article class="clear">
    <h1><?=$translation['listing_nothing']?></h1>
    <p><?=$translation['listing_comeBack']?></p>
  </article>
<?php
}
echo('</section>');
}

function drawCalendar($db, $translation){
?>
	<section id="calendar" class="flex1">
		<h1><?=$translation['calendar_title']?></h1>
  		<article class="clear hyphenate">
   			<h1><?=$translation['calendar_nothing']?></h1>
  		</article>
	</section>	
<?php
}

function echo404($what){
  echo('not found : '.$what);
}
?>
