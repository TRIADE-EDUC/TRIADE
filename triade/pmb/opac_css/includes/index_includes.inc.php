<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_includes.inc.php,v 1.240.2.4.2.4 2025/05/20 14:00:07 qvarin Exp $

use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\DSI\Models\Channel\Portal\PortalChannel;
use Pmb\Animations\Opac\Controller\AnimationsController;
use Pmb\Animations\Opac\Controller\RegistrationController;
use Pmb\Common\Library\CSRF\ParserCSRF;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Opac\Controller\DiffusionsController;
use Pmb\DSI\Orm\DiffusionOrm;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $msg, $charset, $lvl, $sub;
global $opac_autolevel2, $opac_cart_allow, $opac_cart_only_for_subscriber, $opac_congres_affichage_mode;
global $opac_opac_view_activate, $opac_view, $current_opac_view, $opac_search_other_function;
global $opac_show_login_form_next, $opac_notice_enrichment, $opac_parse_html, $cms_active;
global $opac_contact_form, $faq_active, $opac_search_universes_activate, $pmb_collstate_advanced;
global $opac_contribution_area_activate, $allow_contribution, $opac_duration_session_auth, $animations_active;
global $opac_quick_access, $opac_quick_access_logout;
global $opac_show_liensbas, $opac_show_bandeau_2, $opac_show_bandeaugauche, $opac_facette_in_bandeau_2, $opac_accessibility, $opac_show_homeontop, $opac_biblio_main_header;
global $opac_rgaa_active, $nb_per_page_custom, $opac_items_pagination_custom;

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
    $opac_view = intval($opac_view);
	if ($opac_view) {
		if ($current_opac_view!=$opac_view) {
			//on change de vue donc :
			//on stocke le tri en cours pour la vue en cours
		    $_SESSION["last_sortnotices_view_".$current_opac_view]=(isset($_SESSION["last_sortnotices"]) ? $_SESSION["last_sortnotices"] : '');
			if (isset($_SESSION["last_sortnotices_view_".($opac_view)])) {
				//on a déjà un tri pour la nouvelle vue, on l'applique
				$_SESSION["last_sortnotices"] = $_SESSION["last_sortnotices_view_".($opac_view)];
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
    $id = intval($id);
	$requete="select idetagere,name,comment,id_tri from etagere where idetagere=$id";
	$resultat=pmb_mysql_query($requete);
	$r=pmb_mysql_fetch_object($resultat);
	require_once($base_path.'/classes/sort.class.php');
	$dSort = new dataSort('notices', 'session');
	$dSort->applyTri($r->id_tri);
}

// L'usager a demandé à voir plus de résultats dans sa liste paginée, on fait un controle
if (isset($nb_per_page_custom) && intval($nb_per_page_custom)) {
	$nb_per_page_custom = intval($nb_per_page_custom);

	if (!empty($opac_items_pagination_custom)) {
		$items_pagination_custom = explode(",", $opac_items_pagination_custom);
		$items_pagination_custom = array_map("intval", $items_pagination_custom);
		$max_nb_per_page = max($items_pagination_custom);
	} else {
		$max_nb_per_page = 200;
	}

	if ($nb_per_page_custom > $max_nb_per_page) {
		// On bloque au nombre max definie dans opac_items_pagination_custom
		$nb_per_page_custom = $max_nb_per_page;
	}
	if ($nb_per_page_custom < 0) {
		// On bloque les chiffres negatifs
		$nb_per_page_custom = 0;
	}
}

// L'usager a demandé à voir plus de résultats dans sa liste paginée
if(isset($nb_per_page_custom)) {
	$nb_per_page_custom=intval($nb_per_page_custom);
	if($nb_per_page_custom) {
    	$opac_nb_aut_rec_per_page = $nb_per_page_custom;
    	$opac_search_results_per_page = $nb_per_page_custom;
    	$opac_bull_results_per_page = $nb_per_page_custom;
     	$opac_categories_categ_rec_per_page = $nb_per_page_custom;
     	$opac_term_search_n_per_page = $nb_per_page_custom;
     	$nb_per_page = $nb_per_page_custom;
	}
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
if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
    require_once($base_path.'/includes/ext_auth.inc.php');
    //Ack - On a obtenu un user_code via l'authentification externe et les globales lecteurs sont vides
    global $empr_nom, $empr_location;
    if (!empty($_SESSION["user_code"]) && empty($empr_nom) && empty($empr_location)) {
        require_once($base_path.'/includes/empr_func.inc.php');
        recupere_pref_droits($_SESSION["user_code"]);
    }
}

// autenticazione LDAP - by MaxMan
require_once($base_path."/includes/ldap_auth.inc.php");

// pour visualiser une notice issue de DSI avec une connexion auto
if(isset($code)) {
	// pour fonction de vérification de connexion
	require_once($base_path.'/includes/empr_func.inc.php');
	$log_ok=connexion_empr();
	if($log_ok) $_SESSION["connexion_empr_auto"]=1;
} elseif(empty($_SESSION["user_code"]) && !empty($_POST['login'])) {
    $log_ok=connexion_empr();
}
// connexion en cours et paramètre de rebond vers le compte emprunteur
if (!empty($login) && $first_log && empty($direct_access)) {
	if($opac_show_login_form_next) {
		die("<script>document.location='$opac_show_login_form_next';</script>");
	} elseif($opac_opac_view_activate && !empty($auth_ok_need_refresh_page)) {
		if(!empty($_SERVER['REQUEST_URI'])) {
			$parsed_uri = parse_url(substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/')+1));
			$parsed_query = array();
			parse_str($parsed_uri['query'] ?? "", $parsed_query);

			unset($parsed_query['opac_view']); // Retirons la variable opac_view sauvegardée en session
			$builded_uri_query = http_build_query($parsed_query);
			$action = $parsed_uri['path'];
			if(!empty($builded_uri_query)) {
				$action .= "?".$builded_uri_query;
			}
			unset($_POST['login']);
			unset($_POST['password']);
			die("
			<form action='".$base_path."/".$action."' method='post' name='myrefreshpageform'>
				".get_hidden_global_var('POST')."
			</form>
			<script>document.forms['myrefreshpageform'].submit();</script>
			");
		} else {
			die("<script>document.location='".$base_path."/empr.php';</script>");
		}
	}
}

// tentative de connexion echouée : redirection vers le formulaire de connexion
// erreur_connexion CONNEXION_ERROR_WAITING_FOR_OTP_CODE (4) correspond a la double authentification, on ne redirige pas
if(isset($_POST['login']) && isset($_POST['password']) && !$log_ok && $erreur_connexion != CONNEXION_ERROR_WAITING_FOR_OTP_CODE ) {
	$parseCSRF = new ParserCSRF();
	print "
	<form action='".$base_path."/empr.php' method='post' name='myredirectform'>
		<input type='text' name='login' value='".htmlentities($_POST['login'], ENT_QUOTES, $charset)."'><br />
		<input type='password' name='password' value='".htmlentities($_POST['password'], ENT_QUOTES, $charset)."'/>
		".(!empty($_SESSION['opac_view']) ? "<input type='hidden' name='opac_view' value='".$_SESSION['opac_view']."'/>" : "")."
		{$parseCSRF->generateHiddenField()}
		" . get_hidden_global_var('POST') . "
	</form>
	<script>document.forms['myredirectform'].submit();</script>";
	exit;
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
						switch($_SESSION["search_type".(string)$key]) {
							case "search_universes":
								$_SESSION["search_universes".(string)$value]=$_SESSION["search_universes".(string)$key];
								break;
							case "ai_search":
								$_SESSION["ai_search_history_" . (string)$value] = $_SESSION["ai_search_history_" . (string)$key];
								break;
							default:
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
								break;
						}
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
if ($opac_notice_enrichment) {
	require_once($base_path."/classes/enrichment.class.php");
	$enrichment = new enrichment();
	$std_header = str_replace("!!enrichment_headers!!", $enrichment->getHeaders() ?? "", $std_header);
} else {
    $std_header = str_replace("!!enrichment_headers!!", "", $std_header);
}

// si $opac_show_homeontop est à 1 alors on affiche le lien retour à l'accueil sous le nom de la bibliothèque
if ($opac_show_homeontop == 1) {
    $std_header = str_replace("!!home_on_top!!", $home_on_top ?? "", $std_header);
} else {
    $std_header = str_replace("!!home_on_top!!", "", $std_header);
}

// mise à jour du contenu opac_biblio_main_header
$std_header = str_replace("!!main_header!!", $opac_biblio_main_header ?? "", $std_header);

// RSS
$std_header = str_replace("!!liens_rss!!", genere_link_rss() ?? "", $std_header);
// l'image $logo_rss_si_rss est calculée par genere_link_rss() en global
$liens_bas = str_replace("<!-- rss -->", $logo_rss_si_rss ?? "", $liens_bas);

if ($opac_parse_html || $cms_active || $opac_rgaa_active){
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

require_once($base_path.'/includes/nav_history.inc.php');
require_once($base_path.'/includes/navigator.inc.php');

$segment_parameter = "";
if($lvl == "search_segment"){
	//On a besoin de l'id du segment pour recuperer en session les resultats a imprimer
	$segment_parameter = "&id_segment=" . intval($id);
}

if ($opac_rgaa_active) {
    $link_to_print_search_result = "<span class=\"printSearchResult\">
    <button type='button' onclick=\"openPopUp('".$base_path."/print.php?lvl=search&current_search=".(intval($_SESSION['last_query'])).$segment_parameter."','print'); w.focus(); return false;\" title=\"".$msg["histo_print_current_page"]."\">
    	<img src='".get_url_icon('print.gif')."' style='border:0px' class='align_bottom' alt=\"".$msg["histo_print_current_page"]."\"/>
    </button>
    </span>";

	$link_to_print_search_result_spe = "<span class=\"printSearchResult\">
	<button type='button' onClick=\"openPopUp('".$base_path."/print.php?lvl=search&current_search=".(intval($_SESSION['last_query'])).$segment_parameter."!!spe!!','print'); w.focus(); return false;\" title=\"".$msg["histo_print_current_page"]."\">
		<img src='".get_url_icon('print.gif')."' style='border:0px' class='align_bottom' alt=\"".$msg["histo_print_current_page"]."\"/>
	</button>
	</span>";
} else {
    $link_to_print_search_result = "<span class=\"printSearchResult\">
    <a href='#' onClick=\"openPopUp('".$base_path."/print.php?lvl=search&current_search=".(intval($_SESSION['last_query'])).$segment_parameter."','print'); w.focus(); return false;\" title=\"".$msg["histo_print_current_page"]."\">
    	<img src='".get_url_icon('print.gif')."' style='border:0px' class='align_bottom' alt=\"".$msg["histo_print_current_page"]."\"/>
    </a>
    </span>";

	$link_to_print_search_result_spe = "<span class=\"printSearchResult\">
	<a href='#' onClick=\"openPopUp('".$base_path."/print.php?lvl=search&current_search=".(intval($_SESSION['last_query'])).$segment_parameter."!!spe!!','print'); w.focus(); return false;\" title=\"".$msg["histo_print_current_page"]."\">
		<img src='".get_url_icon('print.gif')."' style='border:0px' class='align_bottom' alt=\"".$msg["histo_print_current_page"]."\"/>
	</a>
	</span>";
}


if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
    $id = intval($id);
	if(!isset($id)) $id =0;
	$add_cart_link="<span class=\"addCart\"><a href='cart_info.php?lvl=$lvl&id=$id' target='cart_info' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a></span>";
	$add_cart_link_spe="<span class=\"addCart\"><a href='cart_info.php?lvl=$lvl&id=$id!!spe!!' target='cart_info' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a></span>";
}

if ($opac_rgaa_active) {
    $link_to_visionneuse = "
    <script>var oldAction;</script>
    <span class='open_visionneuse'>
        <button type='button' onclick='open_visionneuse(sendToVisionneuse);return false;'
            title='".htmlentities($msg["result_to_phototeque"], ENT_QUOTES, $charset)."'>".$msg["result_to_phototeque"]."</button>
    </span>";

    $link_to_visionneuse_authority = "
    <script>var oldAction;</script>
    <span class='open_visionneuse'>
        <button type='button' onclick='open_visionneuse(sendToVisionneuseAuthority);return false;'
            title='".htmlentities($msg["result_to_phototeque"], ENT_QUOTES, $charset)."'>".$msg["result_to_phototeque"]."</button>
    </span>";
} else {
    $link_to_visionneuse = "
    <script>var oldAction;</script>
    <span class=\"open_visionneuse\"><a href='#' onclick=\"open_visionneuse(sendToVisionneuse);return false;\" title=\"".htmlentities($msg["result_to_phototeque"], ENT_QUOTES, $charset)."\">".$msg["result_to_phototeque"]."</a></span>";

    $link_to_visionneuse_authority = "
    <script>var oldAction;</script>
    <span class=\"open_visionneuse\"><a href='#' onclick=\"open_visionneuse(sendToVisionneuseAuthority);return false;\" title=\"".htmlentities($msg["result_to_phototeque"], ENT_QUOTES, $charset)."\">".$msg["result_to_phototeque"]."</a></span>";
}

//cas général
$sendToVisionneuseByPost ="
<script>
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

//cas de notice display
$sendToVisionneuseNoticeDisplay ="
<script>
	function sendToVisionneuse(explnum_id){
		document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
	}
</script>";

//cas des autorités
$sendToVisionneuseAuthorityDisplay ="
<script>
	function sendToVisionneuseAuthority(explnum_id){
		document.getElementById('visionneuseIframe').src = \"visionneuse.php?mode=!!mode!!&idautorite=!!idautorite!!\"+(typeof(explnum_id) != 'undefined' ? '&explnum_id='+explnum_id : \"\");
	}
</script>";

//cas segment de recherche
$sendToVisionneuseSegmentSearch ="
<script>
	function sendToVisionneuse(explnum_id){
		oldAction = document.form_values.action;

        let formValues = document.querySelector(\"form[name='form_values']\");
		formValues.action = \"visionneuse.php?mode=segment\"+(typeof(explnum_id) != 'undefined' ? '&explnum_id='+explnum_id : \"\");
		formValues.target = 'visionneuse';
		formValues.submit();
	}
</script>";

//cas recherche semantique ai_search
$sendToVisionneuseAiSearch ="
<script>
	function sendToVisionneuse(explnum_id) {
		let explnum_id_param = (typeof(explnum_id) != 'undefined' ? '&explnum_id='+explnum_id : '');
		document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=ai_search&id=!!index_ai_search_result!!' + explnum_id_param;
	}
</script>";

switch($lvl) {
	case 'author_see':
		$author_type_aff=0;
		$id = intval($id);
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
	    if ($opac_sur_location_activate==1 && empty($location)) {
	        require_once($base_path.'/includes/show_sur_location.inc.php');
	    } else {
	        require_once($base_path.'/includes/show_localisation.inc.php');
	    }
		break;
	case 'rss_see':
		require_once($base_path.'/includes/rss_see.inc.php');
		break;
	case 'doc_command':
	    if ($opac_photo_show_form) {
    		require_once($base_path.'/includes/doc_command.inc.php');
	    }
		break;
	case 'sort':
		require_once($base_path.'/includes/sort.inc.php');
		break;
	case 'lastrecords':
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
	    if(file_exists($base_path.'/includes/extend.inc.php')) {
			require_once($base_path.'/includes/extend.inc.php');
	    }
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
		    print common::format_title($msg['contribution_area_new']);
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
	case 'animations_see':
	    if ($animations_active) {
    	    $opac = new AnimationsController(new stdClass());
    	    $opac->proceed('list');
	    }
	    break;
	case 'animation_see':
	    if ($animations_active) {
    	    $obj = new stdClass();
    	    $obj->id = (int) $id;
    	    $obj->empr_id = (int) $_SESSION['id_empr_session'] ?? 0;
    	    $opac = new AnimationsController($obj);
    	    $opac->proceed('see');
	    }
	    break;
	case 'registration':
	    if ($animations_active) {
	        if ($opac_animations_only_empr && empty($id_empr)) {
	            print $msg['animation_registration_unauthorized'];
	            break;
	        }

            $obj = new stdClass();
            // Identifiant de l'emprunteur
            $obj->id_empr = !empty($id_empr) ? intval($id_empr) : 0;
            // Identifiant de l'animation
            $obj->id_animation = !empty($id_animation) ? intval($id_animation) : 0;
            // Identifiant de l'inscription
            $obj->id_registration = !empty($id_registration) ? intval($id_registration) : 0;
            // Identifiant de l'inscription
            $obj->id_person = !empty($id_person) ? intval($id_person) : 0;
            // hash pour la désinscription
            $obj->hash = !empty($hash) ? $hash : "";
            // Inscription Multiple
            if (!empty($numDaughtersAnimation) && is_array($numDaughtersAnimation)) {
                $numDaughtersAnimation = implode(',', $numDaughtersAnimation);
            }
            $obj->numDaughtersAnimation = !empty($numDaughtersAnimation) ? $numDaughtersAnimation : "";

    	    $registration_controller = new RegistrationController($obj);
    	    $registration_controller->proceed($action);
	    }
	    break;
	case 'onto_see':
	    if($ontology_id){
	       $ontology_id = intval($ontology_id);
	        $ontology = new ontology($ontology_id);
	        $ontology->exec_data_framework();
	    }
	    break;
	case 'dsi':
		global $action, $hist, $diff;
		$hist = intval($hist);
		$diff = intval($diff);
		if($action == "unsubscribe") {
			require_once($include_path . "/empr_func.inc.php");
			$check = connexion_auto();
			if(! $check) {
				die ("Acc&egrave;s interdit");
			}
			global $id_diffusion, $emprlogin, $empr_type;
			$id_diffusion = intval($id_diffusion);
			if (! $id_diffusion) die ("Acc&egrave;s interdit");
			$controller_data = new stdClass();
			$controller_data->idEmpr = intval($emprlogin);
			$controller_data->idDiffusion = intval($id_diffusion);
			$controller_data->emprType = $empr_type;
			$controller = new DiffusionsController($controller_data);
			$controller->proceed($action);
			return;
		}
		if ($dsi_active == 2 && DiffusionOrm::exist($diff)) {
		    $diffusion = new Diffusion($diff);
			if($hist != 0) {
				$diffusion->fetchDiffusionHistory();
				for($i = 0; $i < $diffusion->diffusionHistory; $i++) {
					if($diffusion->diffusionHistory[$i]->id == $hist) {
						$history = $diffusion->diffusionHistory[$i];
						break;
					}
				}
			} else {
				$history = $diffusion->getLastHistorySent(PortalChannel::class);
			}
		    if (!empty($history)) {
		        $history->send();
		    } else {
				die ("Acc&egrave;s interdit");
			}
		}
		break;
	default:
		$lvl='index';
		require_once($base_path.'/includes/index.inc.php');
		break;
}

// Enregistrement du log
$record_log = generate_log('index');
if ($record_log) {
    print $record_log->validation_script();
}

//Alerte avant la fin de session pour la prolongation
$instance_session = new session();
print $instance_session->monitoring_endsession();

//insertions des liens du bas dans le $footer si $opac_show_liensbas
if ($opac_show_liensbas==1) {
	$footer = str_replace("!!div_liens_bas!!",$liens_bas,$footer);
} else {
    $footer = str_replace("!!div_liens_bas!!", $liens_bas_disabled, $footer);
}

//affichage du bandeau_2 si $opac_show_bandeau_2 = 1
if ($opac_show_bandeau_2==0) {
	$bandeau_2_contains= "";
} else {
	$bandeau_2_contains= "<div id=\"bandeau_2\">!!contenu_bandeau_2!!</div>";
}

if (!isset($facettes_tpl)) {
    $facettes_tpl = '';
}

//affichage du bandeau de gauche si $opac_show_bandeaugauche = 1
if ($opac_show_bandeaugauche == 0) {
	$footer= str_replace("!!contenu_bandeau!!",$bandeau_2_contains,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
} else {
	$footer = str_replace("!!contenu_bandeau!!","<div id=\"bandeau\">!!contenu_bandeau!!</div>".$bandeau_2_contains,$footer);
	$home_on_left=str_replace("!!welcome_page!!",$msg["welcome_page"],$home_on_left);
	$adresse=str_replace("!!common_tpl_address!!",$msg["common_tpl_address"],$adresse);
	$adresse=str_replace("!!common_tpl_contact!!",$msg["common_tpl_contact"],$adresse);

	if ($lvl=="more_results") {
		$facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1=str_replace("!!lst_lvl1!!",$facettes_lvl1,$lvl1);
	} elseif ($lvl=="section_see" && !isset($id)) {
		$facette="";
		$lvl1="";
	} elseif (strpos($lvl,"_see")!==false) {
		$facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1="";
	} elseif ($lvl=="faq") {
		//au plus simple...
		if(!is_object($faq) || get_class($faq) != "faq"){
			$faq = new faq($faq_page,0,$faq_filters);
		}
		$facette=$faq->get_facettes_filter();
		$lvl1="";
	} elseif ($lvl=="search_segment") {
	    $facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1=str_replace("!!lst_lvl1!!",$facettes_lvl1,$lvl1);
	} elseif ($lvl == "search_result" && $search_type_asked == "ai_search") {
	    $facette=str_replace("!!title_block_facette!!",$msg["label_title_facette"],$facette);
		$facette=str_replace("!!lst_facette!!",($facettes_tpl == '' ? facettes::destroy_dom_node() : $facettes_tpl),$facette);
		$lvl1 = "";
	} else {
		$facette="";
		$lvl1="";
	}

	// loading the languages available in OPAC - martizva >> Eric
	require_once($base_path.'/includes/languages.inc.php');
	$home_on_left = str_replace("!!common_tpl_lang_select!!", show_select_languages("index.php"), $home_on_left);

	$external_authentication_form = '';
	if (!$_SESSION["user_code"]) {
		$common_tpl_login_invite = $opac_rgaa_active ? '<h2 class="login_invite">%s</h2>' : '<h3 class="login_invite">%s</h3>';
		$loginform = str_replace(
			'<!-- common_tpl_login_invite -->',
			sprintf($common_tpl_login_invite, $msg['common_tpl_login_invite']),
			$loginform
		);
		$loginform__ = genere_form_connexion_empr();
		$external_authentication_form = generate_external_authentication_form();
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
	$loginform = str_replace('<!-- external_authentication -->', $external_authentication_form ,$loginform);
	$footer=str_replace("!!contenu_bandeau!!",($opac_accessibility ? $accessibility : "").$home_on_left.$loginform.$meteo.($opac_facette_in_bandeau_2?"":$lvl1.$facette).$adresse,$footer);
	$footer= str_replace("!!contenu_bandeau_2!!",$opac_facette_in_bandeau_2?$lvl1.$facette:"",$footer);
}

printPasswordNoCompliant();
cms_build_info(['input' => 'index.php']);
pmb_mysql_close();
