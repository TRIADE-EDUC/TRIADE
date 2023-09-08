<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_includes.inc.php,v 1.179 2019-06-04 09:05:00 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path."/includes/rec_history.inc.php");

//Détournement de la page d'accueil
// au premier coup, on veut juste savoir si les vues sont impliquées
if ((!$lvl)&&(!$search_type_asked)&&($opac_first_page_params)) {
	$params_to_load=json_decode($opac_first_page_params,true);
	foreach ($params_to_load as $varname=>$value) {
		if($varname == "opac_view" && !isset($opac_view)){
			${$varname}=$value;
		}
	}
}

require_once($include_path.'/plugins.inc.php');

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	if ($opac_view) {
		if ($current_opac_view!=$opac_view*1) {
			//on change de vue donc :
			//on stocke le tri en cours pour la vue en cours
		    $_SESSION["last_sortnotices_view_".$current_opac_view]=(isset($_SESSION["last_sortnotices"]) ? $_SESSION["last_sortnotices"] : '');
			if (isset($_SESSION["last_sortnotices_view_".($opac_view*1)])) {
				//on a déjà un tri pour la nouvelle vue, on l'applique
				$_SESSION["last_sortnotices"] = $_SESSION["last_sortnotices_view_".($opac_view*1)];
			} else {
				unset($_SESSION["last_sortnotices"]);
			}
			//comparateur de facettes : on ré-initialise
			require_once($base_path.'/classes/facette_search_compare.class.php');
			facette_search_compare::session_facette_compare(null,true);
			//comparateur de facettes externes : on ré-initialise
			require_once($base_path.'/classes/facettes_external_search_compare.class.php');
			facettes_external_search_compare::session_facette_compare(null,true);
		}
	}
}

//Détournement de la page d'accueil
// là, on les applique vraiment !
if ((!$lvl)&&(!$search_type_asked)&&($opac_first_page_params)) {
	$params_to_load=json_decode($opac_first_page_params,true);
	foreach ($params_to_load as $varname=>$value) {
		${$varname}=$value;
	}
} 

if($opac_search_other_function){
	require_once($include_path."/".$opac_search_other_function);
}

if (!isset($_SESSION["nb_sortnotices"]) || !$_SESSION["nb_sortnotices"]) $_SESSION["nb_sortnotices"]=0; 

//Mettre le tri de l'étagère en session avant l'affichage du sélecteur de tris
if (($lvl=='etagere_see') && ($id)) {
	$id+=0;
	$requete="select idetagere,name,comment,id_tri from etagere where idetagere=$id";
	$resultat=pmb_mysql_query($requete);
	$r=pmb_mysql_fetch_object($resultat);
	if(($r->id_tri) && !(isset($_GET["sort"]))) {
		//Le tri est défini en gestion, on l'ajoute aux tris dispos en OPAC si nécessaire
		$res_tri = pmb_mysql_query("SELECT * FROM tris WHERE id_tri=".$r->id_tri);
		if (pmb_mysql_num_rows($res_tri)) {
			$last = "";
			$row_tri = pmb_mysql_fetch_object($res_tri);
			if ($_SESSION["nb_sortnotices"]<=0) {
				$_SESSION["sortnotices".$_SESSION["nb_sortnotices"]]=$row_tri->tri_par;
				if ($row_tri->nom_tri) {
					$_SESSION["sortnamenotices".$_SESSION["nb_sortnotices"]]=$row_tri->nom_tri;
				}
				$last = 0;
				$_SESSION["nb_sortnotices"]++;
			} else {
				$bool=false;
				for ($i=0;$i<$_SESSION["nb_sortnotices"];$i++) {
					if ($_SESSION["sortnotices".$i] == $row_tri->tri_par) {
						$bool=true;
						$last = $i;
					}
				}
				if (!$bool) {
					$_SESSION["sortnotices".$_SESSION["nb_sortnotices"]] = $row_tri->tri_par;
					if ($row_tri->nom_tri) {
						$_SESSION["sortnamenotices".$_SESSION["nb_sortnotices"]] = $row_tri->nom_tri;
					}
					$last = $_SESSION["nb_sortnotices"];
					$_SESSION["nb_sortnotices"]++;
				}
			}
			$_SESSION["last_sortnotices"]="$last";
		}
	}elseif(isset($_GET["sort"])){
		$_SESSION["last_sortnotices"]=$_GET["sort"];
	}
}

//L'usager a demandé à voir plus de résultats dans sa liste paginée
if(isset($nb_per_page_custom) && intval($nb_per_page_custom)) {
	$nb_per_page_custom=intval($nb_per_page_custom);
	$opac_nb_aut_rec_per_page = $nb_per_page_custom;
	$opac_search_results_per_page = $nb_per_page_custom;
	$opac_bull_results_per_page = $nb_per_page_custom;
 	$opac_categories_categ_rec_per_page = $nb_per_page_custom;
 	$opac_term_search_n_per_page = $nb_per_page_custom;
 	$nb_per_page = $nb_per_page_custom;
}

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/common.tpl.php");
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");

require_once($base_path."/includes/notice_affichage.inc.php");

require_once($base_path."/classes/analyse_query.class.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");

//pour la gestion des tris
require_once($base_path."/classes/sort.class.php");


// si paramétrage authentification particulière
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// autenticazione LDAP - by MaxMan
require_once($base_path."/includes/ldap_auth.inc.php");

// pour visualiser une notice issue de DSI avec une connexion auto
if(isset($code)) {		
	// pour fonction de vérification de connexion
	require_once($base_path.'/includes/empr_func.inc.php');
	$log_ok=connexion_empr();
	if($log_ok) $_SESSION["connexion_empr_auto"]=1;
}

//Premier accès ??
if ($search_type_asked) $_SESSION["search_type"]=$search_type_asked;

if(!isset($autolevel1)) $autolevel1 = '';
if (empty($_SESSION["search_type"]) || (( $lvl=="" || $lvl=="index") && $search_type_asked=="") || ($opac_autolevel2  && $autolevel1)) {
	$_SESSION["search_type"]="simple_search";
	//suppression du tableau facette
	unset($_SESSION['facette']);
	unset($_SESSION['level1']);
}

//Conserver l'endroit où on est et l'endroit où on va

//Récupération du type de recherche
$search_type=(isset($_SESSION["search_type"]) ? $_SESSION["search_type"] : '');

//Si vidage historique des recherches demandé ?
if(!isset($raz_history)) $raz_history = 0;
if ($raz_history) {
	
	require_once($base_path."/includes/history_functions.inc.php");
	
	if ((isset($_POST['cases_suppr'])) && !empty($_POST['cases_suppr'])) {
		$cases_a_suppr=$_POST['cases_suppr'];
		$t = array();
		
		//remplissage du tableau temporaire  de l'historique des recherches $t, si une recherche est sélectionnée, la valeur l'élément du tableau temporaire sera à -1 
		
		for ($i=1;$i<=$_SESSION["nb_queries"];$i++) {
			$bool=false;
			for ($j=0;$j<count($cases_a_suppr);$j++) {
				if ($i==$cases_a_suppr[$j]) {
					$bool=true;
					$j=count($cases_a_suppr);
				} else {
					$t[$i]=$i;
				}
			}
			if ($bool==true) {
				$t[$i]=-1;
			}
		}
		//parcours du tableau temporaire, et réécriture des variables de session
		
		for ($i=count($t);$i>=1;$i--) {
			if ($t[$i]=="-1") {
				$t1=array();
				$t1=suppr_histo($i,$t1);
				$t1=reorg_tableau_suppr($t1);
				$_SESSION["nb_queries"]=count($t1);
				foreach ($t1 as $key => $value) {
					if ($key!=$value) {
						$_SESSION["human_query".(string)$value]=$_SESSION["human_query".(string)$key];
						$_SESSION["notice_view".(string)$value]=$_SESSION["notice_view".(string)$key];
						$_SESSION["search_type".(string)$value]=$_SESSION["search_type".(string)$key];
						$_SESSION["user_query".(string)$value]=$_SESSION["user_query".(string)$key];
						$_SESSION["map_emprises_query".(string)$value]=$_SESSION["map_emprises_query".(string)$key];
						$_SESSION["typdoc".(string)$value]=$_SESSION["typdoc".(string)$key];
						$_SESSION["look_TITLE".(string)$value]=$_SESSION["look_TITLE".(string)$key];
	       				$_SESSION["look_AUTHOR".(string)$value]=$_SESSION["look_AUTHOR".(string)$key];
	      				$_SESSION["look_PUBLISHER".(string)$value]=$_SESSION["look_PUBLISHER".(string)$key];
	      				$_SESSION["look_TITRE_UNIFORME".(string)$value]=$_SESSION["look_TITRE_UNIFORME".(string)$key];
	       				$_SESSION["look_COLLECTION".(string)$value]=$_SESSION["look_COLLECTION".(string)$key];
	       				$_SESSION["look_SUBCOLLECTION".(string)$value]=$_SESSION["look_SUBCOLLECTION".(string)$key];
	        			$_SESSION["look_CATEGORY".(string)$value]=$_SESSION["look_CATEGORY".(string)$key];
	       				$_SESSION["look_INDEXINT".(string)$value]=$_SESSION["look_INDEXINT".(string)$key];
	       				$_SESSION["look_KEYWORDS".(string)$value]=$_SESSION["look_KEYWORDS".(string)$key];
	       				$_SESSION["look_ABSTRACT".(string)$value]=$_SESSION["look_ABSTRACT".(string)$key];
	       				$_SESSION["look_CONTENT".(string)$value]=$_SESSION["look_CONTENT".(string)$key];
	       				$_SESSION["look_CONCEPT".(string)$value]=$_SESSION["look_CONCEPT".(string)$key];
	       				$_SESSION["look_ALL".(string)$value]=$_SESSION["look_ALL".(string)$key];
	       				$_SESSION["look_DOCNUM".(string)$value]=$_SESSION["look_DOCNUM".(string)$key];
	       				$_SESSION["l_typdoc".(string)$value]=$_SESSION["l_typdoc".(string)$key];
					}
				}
			}
		}
		
		//si il ne subsiste plus d'historique de recherches, mise à null des variables de session
		if ($_SESSION["nb_queries"]==0) {
			$_SESSION["last_query"]="";
		} 
	} 
}


//Enregistrement dans historique si visualisation en mode term_search
if (($search_type=="term_search")&&($lvl=="categ_see")&&($rec_history==1)) {
	require_once($base_path."/includes/rec_history.inc.php");
	rec_history();
}
// pour les étagères et les nouveaux affichages
require_once($base_path."/includes/isbn.inc.php");
require_once($base_path."/classes/notice_affichage.class.php");
require_once($base_path."/includes/etagere_func.inc.php");
require_once($base_path."/includes/templates/etagere.tpl.php");

// RSS
require_once($base_path."/includes/includes_rss.inc.php");

// Facettes
require_once($base_path.'/classes/facette_search.class.php');

require_once($base_path.'/classes/quick_access.class.php');

if ($is_opac_included) {
	$std_header = $inclus_header ;
	$footer = $inclus_footer ;
}

//Enrichissement OPAC
if($opac_notice_enrichment){
	require_once($base_path."/classes/enrichment.class.php");
	$enrichment = new enrichment();
	$std_header = str_replace("!!enrichment_headers!!",$enrichment->getHeaders(),$std_header);
}else $std_header = str_replace("!!enrichment_headers!!","",$std_header);

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque
if ($opac_show_homeontop==1) $std_header= str_replace("!!home_on_top!!",$home_on_top,$std_header);
else $std_header= str_replace("!!home_on_top!!","",$std_header);

// mise à jour du contenu opac_biblio_main_header
$std_header= str_replace("!!main_header!!",$opac_biblio_main_header,$std_header);

// RSS
$std_header= str_replace("!!liens_rss!!",genere_link_rss(),$std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace("<!-- rss -->",$logo_rss_si_rss,$liens_bas);

if($opac_parse_html || $cms_active){
	ob_start();
}

print $std_header;

if ($time_expired==1) {
	echo "<script>alert(reverse_html_entities(\"".sprintf($msg["session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
} elseif ($time_expired==2) {
	echo "<script>alert(reverse_html_entities(\"".sprintf($msg["anonymous_session_expired"],round($opac_duration_session_auth/60))."\"));</script>";
}

//from_permalink va permettre de stocker la recherche en session même si autolevel2 = 0
if($lvl != "search_segment" && ($opac_autolevel2 || !empty($from_permalink))){
    $es=new search();
}
require_once($base_path.'/includes/navigator.inc.php');

$link_to_print_search_result = "<span class=\"printSearchResult\">
<a href='#' onClick=\"openPopUp('".$base_path."/print.php?lvl=search&current_search=".(intval($_SESSION['last_query']))."','print'); w.focus(); return false;\" title=\"".$msg["histo_print_current_page"]."\">
	<img src='".get_url_icon('print.gif')."' style='border:0px' class='align_bottom' alt=\"".$msg["histo_print_current_page"]."\"/>
</a>
</span>";

if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
	if(!isset($id)) $id =0;
	$add_cart_link="<span class=\"addCart\"><a href='cart_info.php?lvl=$lvl&id=$id' target='cart_info' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a></span>";
	$add_cart_link_spe="<span class=\"addCart\"><a href='cart_info.php?lvl=$lvl&id=$id!!spe!!' target='cart_info' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a></span>";
}

$link_to_visionneuse = "
<script type='text/javascript' >var oldAction;</script>
<span class=\"open_visionneuse\"><a href='#' onclick=\"open_visionneuse(sendToVisionneuse);return false;\" title=\"".htmlentities($msg["result_to_phototeque"], ENT_QUOTES, $charset)."\">".$msg["result_to_phototeque"]."</a></span>";

//cas général
$sendToVisionneuseByPost ="
<script type='text/javascript'>
	function sendToVisionneuse(explnum_id){
		if (typeof(explnum_id)!= 'undefined') {
			if(!document.form_values.explnum_id){
				var explnum =document.createElement('input');
				explnum.setAttribute('type','hidden');
				explnum.setAttribute('name','explnum_id');
				document.form_values.appendChild(explnum);
			}
			document.form_values.explnum_id.value = explnum_id;
		}
		oldAction=document.form_values.action;
		document.form_values.action='visionneuse.php';
		document.form_values.target='visionneuse';
		document.form_values.submit();	
	}
</script>";

//cas des autorités
$sendToVisionneuseByGet ="
<script type='text/javascript'>
	function sendToVisionneuse(explnum_id){
		document.getElementById('visionneuseIframe').src = \"visionneuse.php?mode=!!mode!!&idautorite=!!idautorite!!\"+(typeof(explnum_id) != 'undefined' ? '&explnum_id='+explnum_id : \"\");
	}
</script>";

//cas de notice display
$sendToVisionneuseNoticeDisplay ="
<script type='text/javascript'>
	function sendToVisionneuse(explnum_id){
		document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
	}
</script>";

switch($lvl) {
	case 'author_see':
		$author_type_aff=0;
		$id=$id*1;
		if($opac_congres_affichage_mode && $id) {
			$requete="select author_type from authors where author_id=".$id;
			$r_author=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_author)) {
				$author_type=pmb_mysql_result($r_author,0,0);
				if($author_type == '71' || $author_type == '72') $author_type_aff=1;
			}			
		}
		if($author_type_aff) require_once($base_path.'/includes/congres_see.inc.php');
		else require_once($base_path.'/includes/author_see.inc.php');
	break;
	case 'categ_see':
		require_once($base_path.'/includes/categ_see.inc.php');
		break;		
	case 'indexint_see':
		require_once($base_path.'/includes/indexint_see.inc.php');
		break;		
	case 'coll_see':
		require_once($base_path.'/includes/coll_see.inc.php');
		break;		
	case 'more_results':
		require_once($base_path.'/includes/more_results.inc.php');
		break;		
	case 'notice_display':
		require_once($base_path.'/includes/notice_display.inc.php');
		break;
	case 'bulletin_display':
		require_once($base_path.'/includes/bulletin_display.inc.php');
		break;			
	case 'publisher_see':
		require_once($base_path.'/includes/publisher_see.inc.php');
		break;	
	case 'titre_uniforme_see':
		require_once($base_path.'/includes/titre_uniforme_see.inc.php');
		break;		
	case 'serie_see':
		require_once($base_path.'/includes/serie_see.inc.php');
		break;		
	case 'search_result':
		require_once($base_path.'/includes/search_result.inc.php');
		break;		
	case 'subcoll_see':
		require_once($base_path.'/includes/subcoll_see.inc.php');
		break;
	case 'search_history':
		require_once($base_path.'/includes/search_history.inc.php');
		break;	
	case 'etagere_see':
		require_once($base_path.'/includes/etagere_see.inc.php');
		break;	
	case 'etageres_see':
		require_once($base_path.'/includes/etageres_see.inc.php');
		break;
	case 'show_cart':
		require_once($base_path.'/includes/show_cart.inc.php');
		break;
	case 'resa_cart':
		require_once($base_path.'/includes/resa_cart.inc.php');
		break;
	case 'show_list':
		require_once($base_path.'/includes/show_list.inc.php');
		break;
	case 'section_see':
		if ($opac_sur_location_activate==1 && empty($location))require_once($base_path.'/includes/show_sur_location.inc.php');
		else require_once($base_path.'/includes/show_localisation.inc.php');
		break;
	case 'rss_see':
		require_once($base_path.'/includes/rss_see.inc.php');
		break;
	case 'doc_command':	
		require_once($base_path.'/includes/doc_command.inc.php');
		break;
	case 'sort':
		require_once($base_path.'/includes/sort.inc.php');
		break;
	case 'lastrecords':
		require_once ($base_path.'/includes/templates/last_records.tpl.php');
		require_once ($base_path.'/includes/last_records.inc.php');
		break;
	case 'authperso_see':
		require_once($base_path.'/includes/authperso_see.inc.php');
		break;
		
	case 'information':
		// Insertion page d'information
		// Ceci permet d'afficher une page d'info supplémentaire en incluant un fichier.
		// Ce fichier s'appelle sous la forme ./index.php?lvl=information&askedpage=NOM_DE_MON_FICHIER
		// NOM_DE_MON_FICHIER peut être une URL si le serveur l'autorise
		// NOM_DE_MON_FICHIER doit être déclaré dans les paramètres de l'OPAC de PMB : 
		// $opac_authorized_information_pages, tous les noms de fichiers autorisés séparés par une virgule
		//
		// Code pour tester la validité de la page demandée. Si la page ne figure pas dans les pages demandées : rien. 
		if ($opac_authorized_information_pages) {
			$array_pages = explode(",",$opac_authorized_information_pages);
			$as=array_search($askedpage,$array_pages);
			if (($as!==null)&&($as!==false)) include ($askedpage) ;
		}
		break;
	case 'infopages':
		// Insertion pages d'information internes paramétrées dans PMB
		// Ceci permet d'afficher une page d'info supplémentaire en incluant un code HTML lu en table.
		// Cette page s'appelle sous la forme ./index.php?lvl=internal&pagesid=#,#,#
		// tous les id des pages à afficher, séparés par une virgule, ils seront affichés dans l'ordre
		$idpages = array() ;
		$idpages = explode(",",$pagesid);
		require_once($base_path.'/includes/infopages.inc.php');
		break;
	case 'extend':
		if(file_exists($base_path.'/includes/extend.inc.php'))
			require_once($base_path.'/includes/extend.inc.php');
		break;
	case 'external_authorities':
		require_once($base_path.'/includes/external_authorities.inc.php');
		break;
	case 'perio_a2z_see':
		require_once($base_path.'/includes/perio_a2z.inc.php');
		break;
	case 'cmspage':
		// pageid
		require_once($base_path.'/includes/cms.inc.php');
		break;
	case 'bannette_see':
		require_once($base_path.'/includes/bannette_see.inc.php');
		break;
	case "faq" :
		if($faq_active){
			require_once($base_path.'/includes/faq.inc.php');
		}else{
			$lvl = "index";
		}
		
		break;
	case 'concept_see':
		require_once($base_path.'/includes/concept_see.inc.php');
		break;
	case 'contact_form':
		if($opac_contact_form) {
			require_once($base_path.'/includes/contact_form.inc.php');
		}
		break;
	case 'contribution_area':
		if($opac_contribution_area_activate && $allow_contribution) {
			require_once($base_path.'/includes/contribution_area.inc.php');
		} else {
			print $msg['empr_contribution_area_unauthorized'];
		}
		break;
	case 'collstate_bulletins_display':
		if($pmb_collstate_advanced) {
			require_once($base_path.'/includes/collstate_bulletins_display.inc.php');
		}
		break;
	case 'search_universe':
	case 'search_segment':
		if($opac_search_universes_activate) {
			require_once($class_path."/search_universes/search_universes_controller.class.php");
			$search_universes_controller = new search_universes_controller($id);
			$search_universes_controller->proceed();
		}
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed($module,$name,$sub,$admin_layout);
		if($file){
			include $file;
		}
		break;
	default:
		$lvl='index';
		require_once($base_path.'/includes/index.inc.php');
		break;
}

if($pmb_logs_activate){
	//Enregistrement du log
	global $log, $infos_notice, $infos_expl, $nb_results_tab;
	
	if($_SESSION['user_code']) {
		$res=pmb_mysql_query($log->get_empr_query());
		if($res){
			$empr_carac = pmb_mysql_fetch_array($res);
			$log->add_log('empr',$empr_carac);
		}
	}

	$log->add_log('num_session',session_id());
	$log->add_log('expl',$infos_expl);
	$log->add_log('docs',$infos_notice);

	//Enregistrement du nombre de résultats	
	$log->add_log('nb_results', $nb_results_tab);

	//Enregistrement multicritere
	global $search;
	if($search)	{
		require_once($base_path."/classes/search.class.php");
		if ($search_type=="external_search") {
			switch($_SESSION["ext_type"]) {
				case "multi":
					$search_file="search_fields_unimarc";
					break;
				default:
					$search_file="search_simple_fields_unimarc";
					break;
			}
		} else {
			if(isset($tab) && $tab == "affiliate"){
				switch($search_type) {
					case "simple_search":
						$search_file="search_fields_unimarc";
						break;
					default:
						$search_file="search_simple_fields_unimarc";
						break;
				}			
			}else $search_file = "";
		}
		$search_stat = new search($search_file);	
		$log->add_log('multi_search', $search_stat->serialize_search());
		$log->add_log('multi_human_query', $search_stat->make_human_query());
	}
	
	//Enregistrement vue
	if($opac_opac_view_activate){
		$log->add_log('opac_view', $_SESSION["opac_view"]);
	}
	
	$log->save();
}

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) $footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
	else $footer = str_replace("!!div_liens_bas!!","",$footer);

//affichage du bandeau_2 si $opac_show_bandeau_2 = 1
if ($opac_show_bandeau_2==0) {
	$bandeau_2_contains= "";
} else {
	$bandeau_2_contains= "<div id=\"bandeau_2\">!!contenu_bandeau_2!!</div>";	
}
if (!isset($facettes_tpl)) $facettes_tpl = '';
//affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
if ($opac_show_bandeaugauche==0) {
	$footer= str_replace("!!contenu_bandeau!!",$bandeau_2_contains,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer); 
} else {
	$footer = str_replace("!!contenu_bandeau!!","<div id=\"bandeau\">!!contenu_bandeau!!</div>".$bandeau_2_contains,$footer);
	$home_on_left=str_replace("!!welcome_page!!",$msg["welcome_page"],$home_on_left);
	$adresse=str_replace("!!common_tpl_address!!",$msg["common_tpl_address"],$adresse);
	$adresse=str_replace("!!common_tpl_contact!!",$msg["common_tpl_contact"],$adresse);
	
	if($lvl=="more_results"){
		$facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1=str_replace("!!lst_lvl1!!",$facettes_lvl1,$lvl1);
	}else if($lvl=="section_see" && !isset($id)){
		$facette="";
		$lvl1="";
	}else if(strpos($lvl,"_see")!==false){
		$facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1="";
	}else if ($lvl=="faq") {
		//au plus simple...
		if(!is_object($faq) || get_class($faq) != "faq"){
			$faq = new faq($faq_page,0,$faq_filters);
		}
		$facette=$faq->get_facettes_filter();
		$lvl1="";	
	}else if($lvl=="search_segment"){
	    $facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1=str_replace("!!lst_lvl1!!",$facettes_lvl1,$lvl1);
	}else{
		$facette="";
		$lvl1="";		
	}
	
	// loading the languages available in OPAC - martizva >> Eric
	require_once($base_path.'/includes/languages.inc.php');
	$home_on_left = str_replace("!!common_tpl_lang_select!!", show_select_languages("index.php"), $home_on_left);
	
	if (!$_SESSION["user_code"]) {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','<h3 class="login_invite">'.$msg['common_tpl_login_invite'].'</h3>',$loginform);
		$loginform__ = genere_form_connexion_empr();
	} else {
		$loginform=str_replace('<!-- common_tpl_login_invite -->','',$loginform);
		$loginform__ ="<b class='logged_user_name'>".$empr_prenom." ".$empr_nom."</b><br />\n";
		if($opac_quick_access) {
			$loginform__.= quick_access::get_selector();
			$loginform__.="<br />";
		} else {
			$loginform__.="<a href=\"empr.php\" id=\"empr_my_account\">".$msg["empr_my_account"]."</a><br />";
		}
		if(!$opac_quick_access_logout || !$opac_quick_access){	
			$loginform__.="<a href=\"index.php?logout=1\" id=\"empr_logout_lnk\">".$msg["empr_logout"]."</a>";
		}
		
	}	
	$loginform = str_replace("!!login_form!!",$loginform__,$loginform);
	$footer=str_replace("!!contenu_bandeau!!",($opac_accessibility ? $accessibility : "").$home_on_left.$loginform.$meteo.($opac_facette_in_bandeau_2?"":$lvl1.$facette).$adresse,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
} 
cms_build_info(array(
    'input' => 'index.php',
));

pmb_mysql_close($dbh);
?>