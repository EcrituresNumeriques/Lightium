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

function drawLead($class, $name, $description,$type,$id,$translation,$lang){
  $admin = "";
  if(isLogedNC()){
    $admin = '<a id="editLead" data-type="'.$type.'" data-lang="'.$lang.'" data-cat="'.$id.'" class="admin">'.$translation['admin_changeLead'].'</a>
    ';
  }
  ?>
  <section id="chapeau" class="<?=$class?>">
    <article class="wrapper">
      <!--<nav id="goLeft"><a href=""><</a></nav>
      <nav id="goRight"><a href="">></a></nav>-->
      <h1><?=$name?></h1>
      <?=$admin?>
      <p class="hyphenate"><?=$description?></p>
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
	$query = $db->prepare("SELECT i.id_item,il.title, il.short,il.content, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.year = :year AND i.month = :month AND i.day = :day AND il.cleanstring LIKE :cleanstring LIMIT 0,1");
	$query->bindParam(':lang',$lang, SQLITE3_TEXT);
	$query->bindParam(':year',$year, SQLITE3_INTEGER);
	$query->bindParam(':month',$month, SQLITE3_INTEGER);
	$query->bindParam(':day',$day, SQLITE3_INTEGER);
	$query->bindParam(':cleanstring',$cleanstring, SQLITE3_TEXT);
	$query->execute() or die('Unable to recover article');
	$article = $query->fetch();
  $admin = "";
  if(isLogedNC()){
    $admin = '<a id="editItem" data-item="'.$article[id_item].'" data-lang="'.$lang.'" data-cleanString="'.$cleanstring.'" data-year="'.$year.'" data-month="'.$month.'" data-day="'.$day.'" class="admin">'.$translation['admin_changeArticle'].'</a>
    ';
  }
	?>
		<section id="article">
			<article class="hyphenate wrapper">
				<h1><?=$article['title']?></h1>
        <?=$admin?>
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
	<nav class="flex-row-wrap flex-start">
    <?php
    for($i = 0;$i < count($subCat);$i++){
      $url = '/'.$lang.'/'.cleanString($catName[$i]).'/'.cleanString($subCatName[$i]).'/';
      ?><a href="<?=$url?>" class="cat<?=$subCat[$i]?> block tags"><?=$subCatName[$i]?></a><?php
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
  //admin add subcat
  $admin = '<a class="admin" id="newSubCat" data-cat="'.$what.'">'.$translation['admin_newSubCat'].'</a>';
}
elseif($action == "index"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT 0,10");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->execute() or die('Unable to fetch Items');
  //admin add item
  $admin = '<a class="admin" id="newItem" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "subcat"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN item_assoc ia2 ON i.id_item = ia2.id_item AND ia2.id_subcat = :subcat JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang  GROUP BY i.id_item ORDER BY time DESC LIMIT 0,10");
  $query->bindParam(':lang',$lang, SQLITE3_TEXT);
  $query->bindParam(':subcat',$what, SQLITE3_INTEGER);
  $query->execute() or die('Unable to fetch Items');
  $admin = '<a class="admin" id="newItem" data-subcat="'.$what.'" data-lang="'.$lang.'">'.$translation['admin_newItem'].'</a>';
}
elseif($action == "day"){
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.day = :day AND i.month = :month AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
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
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.month = :month AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
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
  $query = $db->prepare("SELECT i.id_item,il.title, il.short, i.year,i.month,i.day,ia.id_subcat,group_concat(cs.id_cat,';') || '/' ||group_concat(csl.name,';') || '/' ||group_concat(cl.name,';') as subcat FROM item i JOIN item_lang il ON i.id_item = il.id_item JOIN item_assoc ia ON i.id_item = ia.id_item JOIN category_sub_lang csl ON ia.id_subcat = csl.id_subcat AND csl.lang LIKE :lang JOIN category_sub cs ON ia.id_subcat = cs.id_subcat JOIN category_lang cl ON cs.id_cat = cl.id_cat AND cl.lang LIKE :lang  WHERE i.published > 0 AND il.lang LIKE :lang AND i.year = :year GROUP BY i.id_item ORDER BY time DESC");
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
  $image = $tags = "";
  $rowCount++;
	if($action == "cat"){
		$url = $current.cleanString($row['name']);
  		(!empty($row['image']) ? $image = '<a href="'.$url.'" class="listingFloat block pushState"><img src="'.$row['image'].'"></a>' : $image = "");
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
		(!empty($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
	}
  elseif($action == "day" OR $action == "month" OR $action == "year"){
    $url = "/".$lang."/".$row['year']."/".$row['month']."/".$row['day']."/".cleanString($row['title']);
		$title = $row['title'];
		(!empty($row['subcat']) ? $tags = $row['subcat'] : $tags = "");
  }
?>
  <article class="clear">
    <?=$image?>
    <h1><a href="<?=$url?>" class="pushState"><?=$title?></a></h1>
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
  <article class="clear">
    <h1><?=$translation['listing_nothing']?></h1>
    <p><?=$translation['listing_comeBack']?></p>
  </article>
<?php
}
echo('</section>');
}

function drawCalendar($db, $translation,$lang){
	$calendar = $db->prepare("SELECT e.id_event, l.title, e.time, l.location, l.short, l.description FROM events e JOIN events_lang l ON e.id_event = l.id_event AND l.lang = :lang WHERE time > :time LIMIT 0,5");
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
				$date = date("d/m/Y h:i",$row['time']);
				?>
				<article class="clear hyphenate">
	   			<h1><?=$row['title']?></h1>
					<h2><?=$row['date']?></h2>
					<h2><?=$row['location']?></h2>
					<p><?=$row['short']?></p>
	  		</article>
				<?php
			}
			if($rowCount < 1){
			?>
			<article class="clear hyphenate">
   			<h1><?=$translation['calendar_nothing']?></h1>
  		</article>
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

?>
