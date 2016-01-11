<?php

   //Installation process

   $query = $file_db->query("SELECT count(*) as yes FROM sqlite_master WHERE type='table' AND name='user'");
   $query->execute() or die("Could'nt exec user table check");
   $query = $query->fetch();
   if($query['yes']){
      $query = $file_db->query("SELECT count(*) as users FROM user");
      $query->execute() or die("Could'nt exec users check");
      $query = $query->fetch();
      if($query['users'] == 0){
        //No user inputed, can't continue till user > 0 not satisfied
        if(!empty($_POST['user']) AND !empty($_POST['password'])){
          //add new user

          //then continue on the update process
        }
        else{
          //submit form for login infos
        }
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

    /**************************************
    * Set initial data                    *
    **************************************/

	$user = array(
				array(
					'username' => 'User',
					'pswd' => 'Password'
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

  //settings init

    $settings = array(
      array('name' => "Chaire de recherche",
            'description' => 'Mon texte qui va être encadré',
            'title' => 'Écritures Numériques',
            'meta' => 'ma description en meta',
            'lang' => 'FR'),

              array('name' => "Chaire de recherche",
                    'description' => 'Mon texte qui va être encadré',
                    'title' => 'Écritures Numériques',
                    'meta' => 'ma description en meta',
                    'lang' => 'EN')
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
      array('title' => "Safety check : réseaux numériques et sentiment de sécurité",
            'short' => 'La fonction de contrôle d’absence de danger (safety check) activée par Facebook lors des attentats terroristes à Paris, nous a permis d’acceder à un ultérieur niveau d’intimité, nous entrelaçant encore une fois de plus au réseau dont nous faisons partie et qui fait autant partie de nous. Le rôle assumé par la plateforme de Zuckerberg pendant cette nuit de terreur, pose autant de questions philosophiques sur la relation entre individuel et collectif que de problèmes politiques sur l’institutionnalisation d’une entreprise privée, forte d’une infrastructure transversale aux limites de l’appartenance nationale de tout en chacun et qui, aujourd’hui, hors du web n’a pas de compétiteurs dans une capacité de plus en plus cruciale : celle de nous saisir en tant que élément d’un réseau.',
            'content' => '<p align="left">
	«&nbsp;On a enterré aujourd’hui le caissier du tabac dans la fosse commune.&nbsp;Aujourd’hui, le couchant n’est pas pour lui. Mais, à cette seule pensée, et bien malgré moi, il a cessé aussi d’être pour moi...&nbsp;»</p>

<p>	Fernando Pessoa, <i>Le livre de l’intranquillité</i></p>

<p>	&nbsp;</p>

<p>	Le sentiment de sécurité est un sentiment paradoxal<u> </u>&nbsp;: on le ressent lorsqu\'on ne le ressent pas. En poser la question suffit à le briser&nbsp;: en effet, nous pensons être en sécurité seulement quand nous n’y pensons pas. Il ne faut pas la voir, la sécurité, pour pouvoir la sentir. C’est pourquoi <i>sécuriser</i> lieux et infrastructures particuliers (places, ponts, bâtiments, stades, gares etc.) avec un emploi plus ou moins massif de forces dites d’ailleurs <i>de sécurité</i>, engendre souvent l’effet contraire, voire l’inquiétude et la préoccupation de tous ceux qui peuvent se demander très naïvement&nbsp;: <i>Pourquoi y-a-t-il la police&nbsp;? Qu’est-ce qui se passe&nbsp;? </i>La principale différence entre l’accueil dans un avion et l’accueil<b> </b>dans<b> </b>un train, se mesure exactement sous le profil du sentiment de sécurité&nbsp;: dans le premier cas, des informations détaillées (y compris le mot conventionnel signalant l’éventuelle urgence) sur la conduite à observer en cas d’amerrissage, ou de toute autre situation dramatique, nous sont méticuleusement données&nbsp;;<b> </b>dans<b> </b>un train, le contrôleur ne nous demande que de présenter notre titre de voyage. Pour cette raison, le sentiment de sécurité lors d’un voyage en train est supérieur à celui d’un voyage aérien, mais il est peut-être inférieur à celui d’un voyage en voiture (car c’est à nous, et c’est nous qui conduisons) bien que la mortalité routière atteigne des chiffres accablants<a href="#sdfootnote1sym" name="sdfootnote1anc"><sup>1</sup></a>&nbsp;. Se sentir en sécurité, c’est une chose, l’être <i>réellement</i> en est une autre&nbsp;: les statistiques des incidents domestiques démontrent, elles aussi, comment on peut être <i>tranquillement</i> en danger, pour ainsi dire, tout en se trouvant chez soi<a href="#sdfootnote2sym" name="sdfootnote2anc"><sup>2</sup></a>.</p>

<p align="justify">
	Vendredi 13 novembre, lors des attaques terroristes sur Paris, la ville que j’habite avec ma copine, Facebook a activé la fonction <i>Contrôle d’absence de danger </i>(Safety check), et cela a profondément influencé notre façon de vivre l’événement et donc, dans la perspective sartrienne évoquée <a href="http://www.sens-public.org/article1170.html">ici</a> par Gérard Wormser, l’événement même. Nos comportements individuels et collectifs ne sont pas seulement des réactions et des interprétations posthumes de l’événement, mais aussi les détournements et l’appropriation à <i>posteriori</i> de l’événement même. Avant même de m’en apercevoir, après avoir su ce qui s’était passé (au Stade de France, au Carillon et à La Belle Équipe) et ce qui était encore en train de se passer, notamment au Bataclan, j’avais contacté sur Messenger mon ami Ignace, qui est un disck jokey très actif. J’avais tout de suite pensé à lui&nbsp;: il aurait pu très bien se trouver au Bataclan. Ignace était chez lui, tout comme ma copine et moi, et ainsi on s’était rassuré l’un l’autre grâce à un échange de textos. Ensuite, en allant sur Facebook, je me suis découvert comme <i>signalé en sécurité par Ignace Corso </i><i>pendant Attaques terroristes à Paris&nbsp;: </i>quelle surprise&nbsp;! Bravo Ignace&nbsp;! Quand ma mère et mon frère, quelques minutes plus tard, m’ont appelé depuis l’Italie, ils savaient déjà que j’avais été signalé en sécurité, ainsi que mes autres parents et mes amis. Bien que n’ayant aucune notion précise de ce que cette indication pouvait signifier dans des circonstances aussi tragiques, et pour nous tous effectivement inédite, elle avait au moins affaibli leur peur. La leur, donc la mienne aussi. <i>Pas de nouvelles, bonne nouvelle</i>, rappelle l’expression proverbiale qui dans la logique du réseau social, encore plus dans une situation comme celle de vendredi, est complètement bouleversée. En restant en ligne, dans la stupéfaction horrifiée par les terribles notices de journaux et télévision, avec ma copine on voyait nos contacts sur Paris se signaler, ou être signalés par quelqu’un d’autre, comme étant en sécurité. L’un après l’autre, dans un fil d’actualité extemporané de leurs statuts <i>en sécurité</i>, avec les commentaires associés de leurs amis, je voyais mes amis sains et saufs faire rapidement surface. En même temps, on se souciait des amis sur Paris manquant à l’appel&nbsp;: ceux qui n’étaient pas signalés en sécurité, où étaient-ils&nbsp;? Où étaient-elles Alessandra ou Charlotte&nbsp;? Pourquoi Anatole ne se signalait-il pas en sécurité&nbsp;? Noms et visages étaient collectés et enchaînés dans la galerie de photos profils insouciantes et pittoresques, des selfies romantiques ou ironiques qui participent de notre visage numérique. Numérique, et désormais non-numérique aussi. Facebook devenait pendant ces très longues minutes, parfois plusieurs<b> </b>heures, le lieu idéal où mesurer l’impact des attentats sur nos vies, nos habitudes, nos relations, nos sentiments en commun. L’importance de cette fonction apparaissait immédiatement cruciale et surtout, à y songer, irremplaçable&nbsp;: seul Facebook aurait pu me donner en direct l’information certifiée sur mes contacts parisiens, car c\'est seulement sur Facebook que ces relations sont formalisées et structurées. Pour une institution italienne telle que le Ministère aux Affaires Étrangères, ou le Consulat italien à Paris, je ne suis qu’un des Italiens habitant en France, le Ministère et le Consulat n’ayant aucun intérêt et aucun moyen pour connaître mon réseau d’amis. Pour l’État français, par ailleurs, je viens d’être un citoyen enregistré dès que j’ai reçu ma carte Vitale – soit à peine quelques mois, en raison de ma paresse et de divers obstacles. Je suis une personne censée habiter seule ou avec on ne sait qui à l’adresse correspondant à une attestation d\'hébergement, et qui a quand même déjà signé des contrats de travail en France. Seulement une personne, <i>tout</i> <i>juste un individu sans importance collective,</i> comme disait Céline. Le seul lien formel entre l\'Italie et la France me concernant directement c’est, à vrai dire, la Convention en vigueur entre les deux gouvernements en matière d’impôts sur les revenus, dans le but d’éviter une double imposition&nbsp;: merci beaucoup&nbsp;! Les institutions nous saisissent avant tout dans notre individualité, une individualité purement bureaucratique créée par elles-mêmes et isolée dans son abstraction auto-référentielle, l’identité étatique de notre état civil. La seule infrastructure <i>pan</i><i>op</i><i>t</i><i>i</i><i>que</i> capable de m’identifier comme faisant partie d’un réseau, en déduisant mon identité à partir de mon réseau, c’est le réseau même&nbsp;: non pas un service d’urgence mis en place par un Ministère ou par une institution quelconque mais par Facebook. La fonction activée à cause des attentats, en me renvoyant au réseau dont je fais partie et qui fait autant partie de moi, me renvoyait à moi-même, à mes pensées, à mes souvenirs, à mes peurs, aux noms et aux visages des gens que je connais. Vendredi soir, la fonction <i>Safety Chech</i> nous montrait d’une façon exemplaire comment notre singularité se crée seulement au sein d’une collectivité&nbsp;: «&nbsp;<i>là où la pensée classique voit génér</i><i>a</i><i>lement des objets isolés qu’elle met ensuite en relation, la pensée contemporaine –</i> écrit Eric Méchoulan – <i>insiste sur le fait que les objets sont avant tout de nœuds de relations, des mouvements de relations assez ralentis pour paraître immobiles</i>&nbsp;<a href="#sdfootnote3sym" name="sdfootnote3anc"><sup>3</sup></a>&nbsp;» .</p>

<p>	<br>
	<i>Avant tout de nœuds de relations</i> donc, des relations qui en tant que telles ne se limitaient pas au réseau de ceux qui étaient censés être à Paris, car l’information relative a l’état de sécurité arrivait, en forme de notification, aussi à qui n’habite pas Paris et n’était pas en danger. <i>I</i><i> vostri contatti a Parigi stanno bene</i>, <i>vos </i><i>amis</i><i> </i><i>à</i><i> </i><i>P</i><i>aris vont bien</i>, c’était l’annonce qui arrivait, sans qu’il faille même en faire la demande, à mes contacts en Italie, de loin<b> </b>la plus grande partie de mon réseau. Savoir que tout le monde nous savait en sécurité, ça lénifiait nos inquiétudes&nbsp;: d’un côté, il nous était épargné le souci d’informer tous nos parents et les plus chers de nos amis pour les tranquilliser, et de l’autre côté il déjouait la sollicitude anxiogène pour toutes les personnes dont on n’a peut-être pas le numéro de téléphone, dont parfois on ne retiendrait que confusément le nom s’il n’y avait pas un algorithme et une interface qui nous poussent à penser à eux. <i>Penser à l’autre, ce n’est plus la même chose</i> j’avais écrit <a href="http://blog.sens-public.org/peppecavallari/2014/10/17/penser-a-quelquun-ce-nest-plus-la-meme-chose/">dans un billet de blog </a>écrit avant de lire un ouvrage de Fréderic Worms, où le philosophe (me confirmant en ce que je disais d’une façon purement intuitive et naïve) soutient que penser à quelqu\'un&nbsp;est un modèle de la pensée et la condition de toutes les autres pensées&nbsp;: «&nbsp;<i>Non seulement je ne peux penser à quelque chose qu\'en relation avec quelqu\'un (au minimum parce que quelqu\'un m\'en a parlé, m\'y a introduit)</i><i>, </i><i>mais je ne peux aussi être réellement avec quelqu\'un qu\'en partageant avec</i><i> </i><i>lui quelque chose, ne serait-ce que ceci (qui n\'est pas rien)&nbsp;: ma vie</i>&nbsp;<i>&nbsp;»</i><a href="#sdfootnote4sym" name="sdfootnote4anc"><sup>4</sup></a>. Lorsque je pense à quelqu\'un, note Worms, c\'est comme si j\'étais réellement en train de lui parler, «&nbsp;<i>il y a la distance de la pensée, mais l\'expression voudrait la convertir dans la présence de la parole, adressée à un interlocuteur, la pensée étant inséparable non pas tant du langage que justement de l\'expression, qui est un mouvement</i>&nbsp;<i><a href="#sdfootnote5sym" name="sdfootnote5anc"><sup>5</sup></a></i>&nbsp;» . Cette <i>distance, </i>qui nous empêcherait de parler à quelqu\'un dès que on pense à lui, se remodèle dans une interface comme celle de Facebook, où je ne peux que penser à quelqu’un, dès que j’ai son visage et son nom sous mes yeux dans le fil d’actualité. Sur Facebook, encore plus que sur Whatsapp et Twitter, la nomenclature, l’imagerie, la <i>visagerie – </i>si je peux oser ici un néologisme –<i> </i>concrétisant mes <i>liaisons numériques</i>, externalisent mes pensées <i>pendant qu’elles sont en train d’être</i><i> pensé</i><i>es</i> et y ajoutent aussi d’autres pensées (toujours pensées <i>de</i> et <i>vers</i> quelqu’un) que je ne penserais peut-être pas. Dans un certain sens, dans l’interface où les autres vont être pensés, ils le sont déjà, car ils sont ré-présentés, c’est-à-dire présentés encore une fois par leur mots et leurs visages. Notre <i>appareil psychique</i><i> </i>se trouve ainsi au milieu d’un environnement dans lequel notre individualité se façonne et se refaçonne selon les engrenages d’un dispositif collectif et culturel&nbsp;: «&nbsp;<i>Si celui-ci [l’appareil psychique] prend sans aucun doute racine dans le cerveau, il ne s’y réduit cependant pas&nbsp;: il passe par un appareil symbolique qui n’est pas seulement situé dans le cerveau mais dans la société, à savoir dans les autres cerveaux qui sont en relation avec lui </i>&nbsp;», nous dit Bernard Stiegler en introduisant le concept de <i>transindividuation</i><a href="#sdfootnote6sym" name="sdfootnote6anc"><sup>6</sup></a>. Étant alors notre intimité en train de se conformer à la spatialité et à l’architecture relationnelle de Facebook, étant Facebook le réseau plus peuplé du monde, serait-il le sujet autorisé à produire et, ensuite, à donner des informations sensibles dans une circonstance comme celle du 13 novembre à Paris&nbsp;? Tandis que Facebook avais mis en œuvre le Safety Check, et que sur Twitter se produisaient de tonnes de témoignages écrites et aussi photographiques, le Ministère de l’Intérieur avait ouvert une très austère page pour le dépôt de témoignage en ligne, permettant en outre de prendre contact avec les enquêteurs. Tel que nous le signale Evgueny Morozov dans sa vibrante critique de l’idéologie néolibérale de la Silicon Valley&nbsp;: «&nbsp;<i>désormais, nous n’avons plus à choisir entre l’État et le marché mais entre la politique et la non-politique&nbsp;:entre un système privé d’imagination institutionnelle et politique – et dans lequel les hackers, entrepreneurs et capital-risqueurs sont la réponse par défaut à tout problème sociale – et un système où l’on recherche encore des solutions réellement politiques </i><i>(par exemple, qui, des citoyens , des entreprises ou de l’État, doit posséder quoi&nbsp;? Et selon quelles conditions<a href="#sdfootnote7sym" name="sdfootnote7anc"><sup>7</sup></a>&nbsp;?</i>).<i> </i>Faudrait-il encourager et supporter les pratiques majoritaires qui semblent avoir couronné Facebook en tant que réseau le plus large et que source d’informations fiable et <i>familiale – </i>et alors institutionnaliser le Safety Check comme un outil d’intérêt publique&nbsp;? Où bien au contraire, à travers un engagement <i>politique</i> faut-il éviter qu’une problématique sensible comme celle des informations sur notre sécurité en cas de danger soit gérée par une entreprise privée comme celles qui s’occupent des systèmes antivol&nbsp;?</p>

<p>	La veille des attentats de Paris, un attentat commis à Bourj El-Barajneh, dans la banlieue sud de Beyrouth, faisait 43 morts et centaines de blessées&nbsp;: comme le relate <a href="http://www.lemonde.fr/attaques-a-paris/article/2015/11/15/pourquoi-facebook-n-a-pas-cree-de-bouton-safety-check-pour-nous_4810469_4809495.html">un article</a> de Le Monde, deux blogueurs très populaires, Joey Ayoub et Blobaladi, ont revendiqué le droit des libanais à avoir eux aussi le Safety Check, tout comme les parisiens, en en faisant une question politique et éthique touchant aux valeurs de l’équité et de l’égalité. Zuckerberg a répondu sur son mur Facebook, en disant que le Safety Check avait été conçu pour les désastres naturels, et que seulement vendredi soir ils ont réalisé son applicabilité aux&nbsp;<i>désastres humains&nbsp;</i>. Deux éléments apparaissent de façon saillantes dans cette histoire&nbsp;: la première, c\'est qu’un outil mis en place par Facebook (qui, au delà des enjeux sociaux et philosophique des profils, est tout de même un titre boursier) soit invoqué comme une intervention humanitaire capable d’établir des conditions d’égalité entre deux communautés et deux villes catastrophées&nbsp;; la seconde est que Zuckerberg, en assumant une responsabilité<b> </b>de type<b> </b>humanitaire, ait introduit la notion de <i>désastres humains</i> comme s’il n’y avait pas à débattre sur ce qu’est un désastre humain. Combien de morts font un désastre&nbsp;? Comment mesurer l’impact d’un attentat sur la nécessité d’avoir des informations sur les conditions des personnes se trouvant dans la zone concernée&nbsp;? Et comment devrait-elle être délimitée dans son périmètre&nbsp;? Beaucoup de questions, toujours des questions&nbsp;: avons-nous le droit de répondre&nbsp;? Ou personne n’a ce droit sauf Zuckerberg, s\'il décide de s’en occuper&nbsp;? Bien évidemment, cela va dépendre des dimensions plus ou moins politiques et institutionnelles du problème. L’exigence que les informations soient centralisées et autorisées pour qu’elles soient accessibles et fiables, c’est évident&nbsp;; on pourrait penser alors à une plateforme à créer, qui ferait autorité parce qu\'investie de cette compétence par les gouvernements. Un site-web ou une application, un logiciel officiel et institutionnel capable de faire réseau, de faire hashtag au delà de l’appartenance nationale de chacun, pour qu’on puisse facilement se signaler en sécurité instantanément d’un bouton et, avec la même facilité, avoir des nouvelles de ses proches. Certes, une plateforme de ce genre n’aurait pas la familiarité qu’on a avec Facebook (la familiarité qui a fait l’autorité du Safety Check) mais elle aurait en revanche une plus solennelle officialité, à laquelle devraient concourir des facteurs technologiques et sociaux à la fois. Par exemple, permettre ou pas à quelqu’un d’autre de nous signaler en sécurité à notre insu, comme l’a fait Ignace avec moi&nbsp;? Faire ou pas&nbsp;, d’un statut exceptionnel comme <i>est </i><i>en sécurité</i> l’objet de commentaires, comme pour n’importe quel statut&nbsp;? Et encore, s’inscrire à une plateforme de ce type-là, serait-il facultatif ou obligatoire&nbsp;? Qu’elle soit 100&nbsp;% publique et <i>politique, </i>ou bien privée et donc <i>non-politique</i><i> </i>(pour formuler<b> </b>le dilemme dans les termes de Morozov), une fonction du genre Safety Check nous semble nécessaire, même en soulignant clairement les contradictions relatives. Le numérique numérise, c’est-à-dire qu’il écrit, il se fait d’écriture. L’écriture des codes, l’écriture se déclenchant à chaque commande, à chaque<i> I like </i>même<i>, </i>et enfin l’écriture de nos textes (status, tweets, billet de blog ou de forum, messages privés, etc). Le numérique nous fait agir car il nous fait écrire, nous faisons le web en écrivant, dans les interfaces où nous sommes les sujets et les objets d’une écriture conversationnelle se déguisant en oralité. Mais cette oralité, on le sait, n’est qu’une impression de labilité&nbsp;: l’outil qui a transformé la connexion à Internet de phénomène domestique sans ordre en condition permanente du corps et de l’esprit, c’est-à-dire le smartphones fabriqués pour nous faire parler, est avant tout une machine à écrire.<br>
	Une machine qui écrit, enregistre et archive, comme souligne Maurizio Ferraris dans <a href="http://www.sens-public.org/article1104.html">Âme et Ipad</a>. Écrire, donc, dire, affirmer, expliciter, rendre conscient&nbsp;: tout cela pose des problèmes lorsque on ambitionne de<b> </b>maîtriser un sentiment comme celui de sécurité, un sentiment qu’il ne faudrait pas sentir, donc surtout pas penser et mettre en mots. Que pourrait faire alors le numérique<i> </i><i>tout-écrivant </i>pour nous aider à protéger notre sentiment de sécurité&nbsp;? En continuité avec une fonction comme celle de Facebook, nous pourrions exploiter encore mieux l’effet apaisant du <i>se savoir en sécurité</i>, et donc du fait de se le dire lors d’un désastre humain ou naturel. L’interface pourrait spécifier le degré et la qualité de notre sécurité, la contextualiser encor mieux dans ses coordonnées spatio-temporelles, ou nous demander d’écrire (encore&nbsp;?!) quelque chose dès que possible, pour confirmer et compléter notre statut. Le contrôle de sécurité serait un contrôle événementiel, comme c’est le cas aujourd’hui. Mais il y a une autre option plus intrigante&nbsp;: pour augmenter notre sentiment de sécurité, à mon avis il faudrait banaliser la perception du risque et normaliser les moments extraordinaires d’urgence et danger, en les intégrant dans un contrôle de sécurité activé en permanence. J’imagine – confusément, peut-être, et d’une façon aussi provocatrice, je l’avoue – une application constante du <i>Safety Check, </i>ou une fonction pareille, activée en permanence, nous signalant en sécurité face à notre réseau tous les jours, une ou deux fois par jour même, par le biais d’un système de notifications plus souples et discrètes mais régulières. Dans l’espace de quelques semaines, l’impact psychologique d’une mise en sécurité particulière serait complètement neutralisé au profit d’une généralisation et d\'une socialisation de la conscience d’être, le plus souvent, presque toujours, en condition de sécurité. Si le besoin de se dire et de se sentir en sécurité doit devenir encore plus qu’il n’est déjà un besoin social, il faudra en assumer les conséquences et transformer l’angoisse en simple information, un flux incessant d’information quotidienne, habituelle et ponctuelle comme l’assomption de certains médicaments, qui transformant la maladie en thérapie la rendent tout à fait compatible avec la tranquillité de la routine&nbsp;: la routine dont le numérique fait partie. Ce que nous sommes en trains de revendiquer, c’est à bien voir la liberté de la routine, qui en tant que telle semble être immuable tandis qu’elle à renégocier et réadapter. Seulement dans la routine métropolitaine des sirènes normalement inaperçues des ambulances et de la police, notre sentiment de sécurité peut silencieusement se faire invisible, impalpable et ainsi réel.</p>

<p>	&nbsp;</p>

<p>	<a href="#sdfootnote1anc" name="sdfootnote1sym">1</a>&nbsp;<a href="http://www.preventionroutiere.asso.fr/Nos-publications/Statistiques-d-accidents">http://www.preventionroutiere.asso.fr/Nos-publications/Statistiques-d-accidents</a>.</p>

<p>	<a href="#sdfootnote2anc" name="sdfootnote2sym">2</a>&nbsp;<a href="http://www.planetoscope.com/mortalite/1450-deces-par-accidents-domestiques-en-france.html">http://www.planetoscope.com/mortalite/1450-deces-par-accidents-domestiques-en-france.html</a>.</p>

<p>	<a href="#sdfootnote3anc" name="sdfootnote3sym">3</a>&nbsp;E. Méchoulan, <i>D’ou nous viennent nos idées&nbsp;?,</i> Montréal, vlb éditeur, 2010, p. 39.</p>

<p>	<a href="#sdfootnote4anc" name="sdfootnote4sym">4</a> F. Worms, <i>Penser à quelqu\'un, </i>Paris, Flammarion, 2014 p. 204.</p>

<p>	<a href="#sdfootnote5anc" name="sdfootnote5sym">5</a><i> Ibidem</i>, p. 29.</p>

<p>	<a href="#sdfootnote6anc" name="sdfootnote6sym">6</a> B. Stiegler, <i>Du Psychopouvoir au Neuropouvoir, </i>dans C. Larsonneur, A. Regnauld, P. Cassou-Noguès et S. Touiza (sous la direction de), <i>Le sujet digital</i>, Dijon, Les presses du réel, 2015, p.43.</p>

<p>	<a href="#sdfootnote7anc" name="sdfootnote7sym">7</a> E. Morozov, <i>Le mirage numérique. Pour une politique du Big Data, </i>Paris, Les prairies ordinaires, 2015, p. 14.</p> ',
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

	//items association with Subcat
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,1)");
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,2)");
	$file_db->exec("INSERT INTO item_assoc (id_item, id_subcat) VALUES (1,5)");
}

?>
