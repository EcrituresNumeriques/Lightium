<?php

function isEqual($what,$toWhat){
	return (isset($what) AND isset($toWhat) AND $what == $toWhat ? true : false );
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

function drawLead($class, $name, $description,$type,$id,$translation,$lang,$priority = "",$image = "", $caption = ""){
  $admin = "";
	(isset($priority)?: $priority = "");
  if(isLogedNC()){
    $admin = '<a id="editLead" data-type="'.$type.'" data-lang="'.$lang.'" data-cat="'.$id.'" data-priority="'.$priority.'" class="admin">'.$translation['admin_changeLead'].'</a>
    ';
  }
	if(!empty($image)){
		if(!empty($caption)){
			$picture = '<figure><img src="'.$image.'" alt="'.htmlentities($caption).'" /><figcaption>'.$caption.'</figcaption></figure>';
		}
		else{
			$picture = '<figure><img src="'.$image.'"/></figure>';
		}
	}
	else{
		$picture = '';
	}
  ?>
  <section id="chapeau" class="<?=$class?>">
    <article class="wrapper">
      <!--<nav id="goLeft"><a href=""><</a></nav>
      <nav id="goRight"><a href="">></a></nav>-->
      <h1><?=$name?></h1>
			<?=$admin?>
      <?=$picture?>
      <h2 class="hyphenate"><?=$description?></h2>
			<div class="clear"></div>
    </article>
  </section>
  <?php
}

function drawCookieTrail($lang = "",$category = "",$subCat = "",$year = "",$month = "",$day = "",$article = ""){
  $echo = "/".$lang."/";
  $echoLink = '/<a href="'.$echo.'">'.$lang.'</a>';
  if(!empty($category)){
    $echo .= cleanString($category)."/";
    $echoLink .= '/<a href="'.$echo.'">'.$category.'</a>';
  }
  if(!empty($subCat)){
    $echo .= cleanString($subCat)."/";
    $echoLink .= '/<a href="'.$echo.'">'.$subCat.'</a>';
  }
  if(!empty($year)){
    $echo .= $year."/";
    $echoLink .= '/<a href="'.$echo.'">'.$year.'</a>';
  }
  if(!empty($month)){
    $echo .= $month."/";
    $echoLink .= '/<a href="'.$echo.'">'.$month.'</a>';
  }
  if(!empty($day)){
    $echo .= $day."/";
    $echoLink .= '/<a href="'.$echo.'">'.$day.'</a>';
  }
  if(!empty($article)){
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

function drawArticle($db, $lang, $year, $month, $day, $cleanstring,$translation){
	$query = $db->prepare("SELECT i.id_item,il.title, il.short,il.content,il.image,il.url,il.urlTitle,il.caption, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item LEFT JOIN item_assoc ia ON i.id_item = ia.id_item LEFT JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang LEFT JOIN category_sub cs ON ia.id_subcat = cs.id_subcat LEFT JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.year = :year AND i.month = :month AND i.day = :day AND il.cleanstring LIKE :cleanstring LIMIT 0,1");
	$query->bindParam(':lang',$lang, SQLITE3_TEXT);
	$query->bindParam(':year',$year, SQLITE3_INTEGER);
	$query->bindParam(':month',$month, SQLITE3_INTEGER);
	$query->bindParam(':day',$day, SQLITE3_INTEGER);
	$query->bindParam(':cleanstring',$cleanstring, SQLITE3_TEXT);
	$query->execute() or die('Unable to recover article');
	$article = $query->fetch();
  $admin = $url = "";
	if(!empty($article['url'])){
		if(!empty($article['urlTitle'])){
			$url = '<div class="inlineCenter"><a href="'.$article['url'].'" target="_black" class="downloadThis">'.$article['urlTitle'].'</a></div>';
		}
		else{
			$url = '<div class="inlineCenter"><a href="'.$article['url'].'" target="_black" class="downloadThis">'.$translation['accessThisDocument'].'</a></div>';
		}
	}

  if(isLogedNC()){
    $admin = '<a id="editItem" data-item="'.$article['id_item'].'" data-lang="'.$lang.'" data-cleanString="'.$cleanstring.'" data-year="'.$year.'" data-month="'.$month.'" data-day="'.$day.'" class="admin">'.$translation['admin_changeArticle'].'</a>
    ';
  }
	?>
		<section id="article">
			<article class="wrapper">
				<h1><?=$article['title']?></h1>
				<?=$url?>
        <?=$admin?>
				<h2 class="hyphenate"><?=$article['short']?></h2>
				<div class="hyphenate"><?=$article['content']?></div>
			<?=drawTags($lang,$article['subcat'])?>
			</article>
		</section>

<?php

}

function drawTags($lang, $tags){
	$tags = explode('#',$tags,3);
    $subCat = explode(';',$tags[0]);
    $subCatName = explode(';',$tags[1]);
    $catName = explode(';',$tags[2]);
  ?>
	<nav class="flex-row-wrap flex-start">
    <?php
    for($i = 0;$i < count($subCat);$i++){
      $url = '/'.$lang.'/'.cleanString($catName[$i]).'/'.cleanString($subCatName[$i]).'/';
      ?><a href="<?=$url?>" class="cat<?=$subCat[$i]?> block tags"><?=$subCatName[$i]?></a><?php
    }  ?>
    </nav>
	<?php
}

function drawListing($db, $translation, $current, $lang, $action, $what,$maxItem){
	(!empty($maxItem)?:$maxItem = 10);
	$pagination = false;
  echo('<section id="listing" class="flex3 '.$action.'">');
  if($action == "cat"){
  $query = $db->prepare('SELECT csl.name,csl.image,csl.short FROM category_sub_lang csl JOIN category_sub cs ON csl.id_subcat = cs.id_subcat WHERE cs.id_cat = :id_cat AND csl.lang LIKE :lang ORDER BY cs.priority ASC');
  $query->bindParam(":lang",$lang, SQLITE3_TEXT);
  $query->bindParam(":id_cat",$what, SQLITE3_INTEGER);
  $query->execute() or die('Unable to fetch Items');
  //admin add subcat
  $admin = '<a class="admin" id="newSubCat" data-cat="'.$what.'">'.$translation['admin_newSubCat'].'</a>';
}
elseif($action == "index"){
	$pages = $db->prepare("SELECT COUNT(*) as count FROM item i");
	$pages->execute() or die('Unable to fetch page Items');
	$pages = $pages->fetch();
	if(!empty($_GET['page']) OR $pages['count'] > 10){
			$pagination = true;
	}
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item LEFT JOIN item_assoc ia ON i.id_item = ia.id_item LEFT JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang LEFT JOIN category_sub cs ON ia.id_subcat = cs.id_subcat LEFT JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT :start,10");
	$query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':start',$start, SQLITE3_INTEGER);
	(!empty($_GET['page'])?$start=$pages['count'] - $_GET['page']*10:$start=0);
	drawSommaire($db,$translation,$current,$lang,$action,NULL);
  $query->execute() or die('Unable to fetch Items');
  //admin add item
  $admin = '<a class="admin" id="newItem" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "subcat"){
  $pages = $db->prepare("SELECT COUNT(*) as count FROM item i JOIN item_assoc ia2 ON i.id_item = ia2.id_item AND ia2.id_subcat = :subcat WHERE i.published > 0 ORDER BY time DESC");
  $pages->bindParam(':subcat',$what, SQLITE3_INTEGER);
  $pages->execute() or die('Unable to fetch page Items');
	$pages = $pages->fetch();
	if(!empty($_GET['page']) OR $pages['count'] > 10){
			$pagination = true;
	}
  $query = $db->prepare("SELECT i.id_item,il.title, il.short,il.image,il.caption, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN item_assoc ia2 ON i.id_item = ia2.id_item AND ia2.id_subcat = :subcat JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT :start,:maxItem");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':subcat',$what, SQLITE3_INTEGER);
	$query->bindParam(':start',$start, SQLITE3_INTEGER);
  $query->bindParam(':maxItem',$maxItem, SQLITE3_INTEGER);
	(!empty($_GET['page'])?$start=$pages['count'] - $_GET['page']*$maxItem:$start=0);
  $query->execute() or die('Unable to fetch Items');
	drawSommaire($db,$translation,$current,$lang,$action,$what);
  $admin = '<a class="admin" id="newItem" data-subcat="'.$what.'" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "day"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.day = :day AND i.month = :month AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':day',$day, SQLITE3_INTEGER);
  $query->bindParam(':month',$month, SQLITE3_INTEGER);
  $query->bindParam(':year',$year, SQLITE3_INTEGER);
  $date = split("/",$what);
  $day = $date[2];
  $month = $date[1];
  $year = $date[0];
  $query->execute() or die('Unable to fetch day archive');
  $admin = '<a class="admin" id="newItem" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "month"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.month = :month AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':month',$month, SQLITE3_INTEGER);
  $query->bindParam(':year',$year, SQLITE3_INTEGER);
  $date = split("/",$what);
  $month = $date[1];
  $year = $date[0];
  $query->execute() or die('Unable to fetch day archive');
  $admin = '<a class="admin" id="newItem" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "year"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':year',$year, SQLITE3_INTEGER);
  $date = split("/",$what);
  $year = $date[0];
  $query->execute() or die('Unable to fetch day archive');
  $admin = '<a class="admin" id="newItem" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
  if(!isLogedNC()){
    $admin = "";
  }
  else{
    echo $admin;
  }
  $rowCount = 0;
  foreach($query as $row){
  $calendarBackup = $background = $image = $tags = "";
  $rowCount++;
	if($action == "cat"){
		$url = $current.cleanString($row['name']);
		(!empty($row['image']) ? $image = '<img src="'.$row['image'].'">' : $image = "");
  	(!empty($row['image']) ? $background = 'style="background-image:url(\''.$row['image'].'\')"': $background = '');
		$title = $row['name'];
	}
	elseif($action == "index"){
		$url = $current.$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(!empty($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
	}
	elseif($action == "subcat"){
		$url = $current.$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(!empty($row['image']) ? $image = '<a href="'.$url.'"><img src="'.$row['image'].'"></a>' : $image = "");
		(!empty($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
		if(isLogedNC()){
		$calendarBackup = '<a id="editItem" data-item="'.$row['id_item'].'" data-lang="'.$lang.'" data-year="'.$row['year'].'" data-month="'.$row['month'].'" data-day="'.$row['day'].'" class="admin">EDIT</a>';
		}
	}
  elseif($action == "day" OR $action == "month" OR $action == "year"){
    $url = "/".$lang."/".$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(!empty($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
		}
?>
<?=$calendarBackup?>
  <article class="clear relative" <?=$background?>>
		<a href="<?=$url?>" class="pushState filler"></a>
    <?=$image?>
    <a href="<?=$url?>"><h1><?=$title?></h1></a>
    <p class="hyphenate"><?=$row['short']?></p><?php
  if(!empty($tags)){
	drawTags($lang,$tags);
	}
?>
  <div class="clear"></div>
  </article>
<?php
}
if($rowCount < 1){

?>
  <article class="clear nothingToDisplay">
    <h1><?=$translation['listing_nothing']?></h1>
    <p><?=$translation['listing_comeBack']?></p>
  </article>
<?php
}

//draw the pagination
if($pagination){
	echo('<nav id="pagination">');
	$selected = "";
	if($start <= 0){$selected = ' class="selected"';}
	if($maxItem==10){
	echo('<a href="'.$current.'"'.$selected.'>'.$translation['last10'].'</a>');}
	elseif($maxItem==9223372036854775807){
	echo('<a href="'.$current.'"'.$selected.'>'.$translation['lastAll'].'</a>');}
	else{
	echo('<a href="'.$current.'"'.$selected.'>'.$translation['lastX'].'</a>');}
	for($i=$pages['count'];$i > $maxItem;$i=$i-$maxItem){
		$page = floor($i/$maxItem);
		$selected = "";
		if($_GET['page'] == $page){$selected = ' class="selected"';}
		echo('<a href="'.$current.'page_'.$page.'"'.$selected.'>'.$page.'</a>');
	}
	echo('</nav>');
}
echo('</section>');

}

function drawCalendar($db, $translation,$lang){
	$calendar = $db->prepare("SELECT e.id_event, l.title, e.time, l.location, l.short, l.description FROM events e JOIN events_lang l ON e.id_event = l.id_event AND l.lang LIKE :lang WHERE e.time > :time ORDER BY e.time ASC LIMIT 0,5");
	$calendar->bindParam(":time",$time,SQLITE3_INTEGER);
	$calendar->bindParam(":lang",$lang,SQLITE3_TEXT);
	$time = time() - 60*60*24;
	$calendar->execute() or die("Couldn't open event table");
	$admin = "";
	if(isLogedNC()){
		$admin = '<a id="newCalendar" class="admin">'.$translation['admin_newCalendar'].'</a>
		';
	}
?>
	<section id="calendar" class="flex1">
		<h1><?=$translation['calendar_title']?></h1>
		<?=$admin?>
  		<?php
		  $rowCount = 0;
		  foreach($calendar as $row){
				$rowCount++;
				$date = date("d/m/Y H:i",$row['time']);
				?>
				<article class="clear hyphenate">
	   			<h1><a href="<?=$row['short']?>" target="_blank"><?=$row['title']?></a></h1>
					<h2 class="date"><?=$date?></h2>
					<h2 class="location"><?=$row['location']?></h2>
					</article>
				<?php
			}
			if($rowCount < 1){
			?>
   			<h1><?=$translation['calendar_nothing']?></h1>
  		<?php
		}
			?>
			</section>
<?php
}

function drawContact($db,$translation,$lang){
	$contact = $db->prepare("SELECT * FROM contact c LEFT JOIN contact_type t ON c.type = t.id_type ORDER BY priority ASC");
	$contact->execute() or die("Couldn't open event table");
	$admin = "";
	if(isLogedNC()){
		$admin = '<a id="editContact" class="admin">'.$translation['admin_editContact'].'</a>
		';
	}
	?>
	<section id="contact">
		<h1><?=$translation['contact_title']?></h1>
		<?=$admin?>
			<?php
			$rowCount = 0;
			foreach($contact as $row){
				$rowCount++;
				$html = str_replace("±VALUE±",$row['value'],$row['template']);
				?>
				<article>
					<?=$html?>
				</article>
				<?php
			}
			if($rowCount < 1){
			?>
				<h1><?=$translation['contact_nothing']?></h1>
			<?php
		}
			?>
	</section>
	<?php
}

function echo404($what){
  echo('<p class="wrapper">not found : '.$what.'</p>');
}

function isLoged(){
  return (!empty($_SESSION['userId']) ? true : false);
}
function isLogedNC(){
  //for non critical admin stuff (meaning they get catched anyway serverSide, and allow sign in via the API) / may be removed depending on discussion further on
  return true;
}

function userLogin($db, $username, $password, $token){
  error_log("In function");
	if(isLoged()){
		//already loged in!
		$return = false;
	}
	else{
	$login = $db->prepare("SELECT id_user, username, hash, salt FROM user WHERE username LIKE :username");
	$login->bindParam(":username",$username);
	$login->execute() or die('Unable to get userLogin');
	$i = 0;
		foreach($login as $user){
			$i++;
			if($_SESSION['token'] !=  $token){
        //Not coming from the website
				$return = false;
			}
			else{
				  $password = crypt($password, '$6$rounds=1000$'.$user['salt']);
    			$password = explode("$",$password);
    			$hash = $password[4];
				if($hash != $user['hash']){
          //bad password
					$return = false;
				}
				else{
					//login successful
					$_SESSION['userId'] = $user['id_user'];
					$return = $user;
				}
        //in any case, refresh the token
        $_SESSION['token'] = base64_encode(mcrypt_create_iv(8, MCRYPT_DEV_URANDOM));
			}
		}
		if($i === 0){
			$return = false;
		}
	}
	return $return;
}

function userInfo($db, $userId){
	$login = $db->prepare("SELECT id_user, username, hash, salt FROM user WHERE id_user = :id");
	$login->bindParam(":id",$userId);
	$login->execute() or die('Unable to get userInfo');
	$return = $login->fetch();
	return $return;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function drawLang($db,$translation,$lang){
?>
	<nav id="langSwitch" class="wrapper">
<?php
	$checkLang = $db->query("SELECT lang FROM settings");
	$checkLang = $checkLang->fetchAll();
	foreach ($checkLang as $Lang) {
		$siteLang = strtolower($Lang['lang']);
		($siteLang == $lang?$class=' class="active"':$class='');
		echo('<a href="/'.$siteLang.'/"'.$class.'>'.$siteLang.'</a>');
	}
?>
	<p><?php echo("$translation[seeInLanguage]"); ?></p>
	</nav>
<?php
}


function	drawSommaire($db,$translation,$current,$lang,$action,$what){
	//Display featured
	if($action == "index"){
			$getFeatured = $db->prepare("SELECT i.id_item,il.title, il.short,il.image, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item LEFT JOIN item_assoc ia ON i.id_item = ia.id_item LEFT JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang LEFT JOIN category_sub cs ON ia.id_subcat = cs.id_subcat LEFT JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.featured = 1 GROUP BY i.id_item ORDER BY time DESC LIMIT 0,1");
	}
	elseif($action == "subcat"){
			$getFeatured = $db->prepare("SELECT i.id_item,il.title, il.short,il.image, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '#' ||group_concat(csl.name,';') || '#' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_assoc ia2 ON ia2.id_item = i.id_item AND ia2.id_subcat = :filter JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item LEFT JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang LEFT JOIN category_sub cs ON ia.id_subcat = cs.id_subcat LEFT JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.featured = 1 GROUP BY i.id_item ORDER BY time DESC LIMIT 0,1");
			$getFeatured->bindParam(":filter",$what,SQLITE3_INTEGER);
	}
	$getFeatured->bindParam(':lang',$lang, SQLITE3_TEXT);
	$getFeatured->execute() or die('Unable to get featured article');
	$featured = $getFeatured->fetch(PDO::FETCH_ASSOC);
	(!empty($featured['subcat']) ? $tags = $featured['subcat'] : $tags = "");
	?>
		<div id="summary">
		<?php
		if($featured){
			$featured['url'] = $current.$featured['year']."/".$featured['month']."/".$featured['day']."/".cleanString($featured['title']);
			(!empty($featured['image'])?$image = '<img src="'.$featured['image'].'">':$image = "");
			(!empty($featured['image']) ? $background = 'style="background-image:url(\''.$featured['image'].'\')"': $background = '');
			?>
			<div id="featured">
			<article class="clear relative featured" <?=$background?>>
				<a href="<?=$featured['url']?>" class="pushState filler"></a>
				<h1><?=$featured['title']?></h1>
				<?=$image?>
				<p class="hyphenate"><?php if(strlen($featured['short']) > 512){echo substr($featured['short'],0,512)."...";}else{echo($featured['short']);} ?></p>
				<?php
						if(!empty($tags)){
						drawTags($lang,$tags);
						}
				?>
			<div class="clear"></div>
			</article>
			</div>
			<?php
			}
			?>
			<section id="summaryGroups">
	<?php

	//Display summary
	$checkSommaire = $db->prepare("SELECT s.id_subcat, s.`group`, s.rows, csl.name FROM summary s LEFT JOIN category_sub_lang csl ON s.id_subcat = csl.id_subcat AND csl.lang LIKE :lang ORDER BY `group`, priority");
	$checkSommaire->bindParam(":lang",$lang);
	$checkSommaire->execute() or die('Unable to get summary');
	$group = NULL;

	foreach ($checkSommaire as $row) {
		if($action == "index"){
			if($row['id_subcat'] == 0){
				$feed = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,il.image FROM item i JOIN item_lang il ON i.id_item = il.id_item WHERE i.published > 0 AND il.lang LIKE :lang AND i.featured = 1 GROUP BY i.id_item ORDER BY time DESC LIMIT 1,:maxItem");
			}
			else{
				$feed = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item AND ia.id_subcat = :subcat  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT 0,:maxItem");
			}
		}
		if($action == "subcat"){
		$feed = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item AND ia.id_subcat = :subcat JOIN item_assoc iaa ON i.id_item = iaa.id_item AND iaa.id_subcat = :filter WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC");
		$feed->bindParam(":filter",$what,SQLITE3_INTEGER);
		}

		$feed->bindParam(":lang",$lang);
		($action == "subcat"?:$feed->bindParam(":maxItem",$row['rows']));
		($row['id_subcat'] == 0 ? :$feed->bindParam(":subcat",$row['id_subcat']));
		$feed->execute() or die('Unable to retrieve summary groups');
		$feeds = $feed->fetchAll(PDO::FETCH_ASSOC);
		if(count($feeds) > 0){
		if($row['group'] != $group){
			if($group === NULL){echo('<div id="group'.$row['group'].'" class="group">');$group = $row['group'];}
			else{echo('</div><div id="group'.$row['group'].'" class="group">');$group = $row['group'];}
			}
			if(!empty($row['name'])){
		?>
		<h1><?=$row['name']?></h1>
		<?php
		}
		 foreach ($feeds as $article) {
			if($action == "index"){
			$url = $current.$article['year']."/".$article['month']."/".$article['day']."/".cleanString($article['title']);
			}
			elseif($action == "subcat"){
				$url = "/".$lang."/".$article['year']."/".$article['month']."/".$article['day']."/".cleanString($article['title']);
			}
			if($row['id_subcat'] == 0 ){
				(!empty($article['image']) ? $image = '<img src="'.$article['image'].'">' : $image = "");
		  	(!empty($article['image']) ? $background = 'style="background-image:url(\''.$article['image'].'\')"': $background = '');
		 	?>
			<article class="clear relative featured" <?=$background?>>
				<a href="<?=$url?>" class="pushState filler"></a>
				<?=$image?>
				<h1><?=$article['title']?></h1>
			<div class="clear"></div>
			</article>
<?php }
else{ ?>
			<article class="clear">
				<h1><a href="<?=$url?>" class="pushState"><?=$article['title']?></a></h1>
			</article>
			<?php
			}
		 }
		if($action == "index" AND $row['id_subcat'] != 0){
		?>
			<article class="clear">
				<h1><a href="/<?=$lang?>/find/<?=cleanString($row['name'])?>" class="pushState seeMore"><?=$translation['seeMore']?></a></h1>
			</article>
		<?php
	}
	}
	}
	if($group !== NULL){
		echo('</div>');
	}
	?>
	</section>
	</div>
	<?php
}

?>
