<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external_authorities.inc.php,v 1.7 2024/01/03 14:38:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $include_path, $charset;
global $opac_allow_affiliate_search, $opac_search_results_per_page;
global $user_query;
global $search_type, $mode, $count, $clause, $clause_bull, $clause_bull_num_notice;
global $join, $tri, $pert, $l_typdoc, $id_thes;
global $opac_cart_allow, $opac_cart_only_for_subscriber, $opac_cart_allow;

// récupération configuration
	require_once($base_path."/includes/opac_config.inc.php");

	// récupération paramètres MySQL et connection à la base
	require_once($base_path."/includes/opac_db_param.inc.php");
	require_once($base_path."/includes/opac_mysql_connect.inc.php");
	if(!$dbh){
	   $dbh = connection_mysql();
	}
	
	require_once($base_path."/includes/start.inc.php");

	// récupération localisation
	require_once($base_path."/includes/localisation.inc.php");
	// les mots vides sont importants pour la requête à appliquer
	require_once($base_path."/includes/marc_tables/$pmb_indexation_lang/empty_words");
	
	// version actuelle de l'opac
	require_once($base_path."/includes/opac_version.inc.php");

	// fonctions de formattage requêtes
	require_once($base_path."/includes/misc.inc.php");


	// fonctions de gestion de formulaire
	require_once($base_path."/includes/javascript/form.inc.php");
	require_once($base_path."/includes/templates/common.tpl.php");
	
	require_once($base_path."/includes/rec_history.inc.php");
	
	require_once($include_path.'/surlignage.inc.php');
	
	if ($get_last_query) {
		get_last_history();
	} else {
		if ($_SESSION["new_last_query"]) {
			$_SESSION["last_query"]=$_SESSION["new_last_query"];
			$_SESSION["new_last_query"]="";
		}
		rec_last_history();
	}
	//Surlignage
	require_once("$include_path/javascript/surligner.inc.php");
	print $inclure_recherche;

	require_once($class_path."/affiliate_search.class.php");

	if(!$page){
		$page=1;
		if($opac_allow_affiliate_search) $affiliate_page = $catalog_page = 1;
	} 
	else{
		if($opac_allow_affiliate_search){
			if($tab == "affiliate"){
				$page = $affiliate_page;
			}else{
				$page = $catalog_page;
			}
		}
	}
	$debut =($page-1)*$opac_search_results_per_page;
	$limiter = "LIMIT $debut,$opac_search_results_per_page";

switch($type){
	case "author" :
		$as=new affiliate_search_author($user_query,"notices_authority");
		//un peu crade, mais dans l'immédiat ca fait ce qu'on lui demande...
		$as->filter = $filter;
		break;
	case "collection" :
		$as=new affiliate_search_collection($user_query,"notices_authority");
		break;
	case "subcollection" :
		$as=new affiliate_search_subcollection($user_query,"notices_authority");
		break;	
	case "category" :
		$as=new affiliate_search_category($user_query,"notices_authority");
		break;	
	case "indexint" :
		$as=new affiliate_search_indexint($user_query,"notices_authority");
		break;	
	case "publisher" :
		$as=new affiliate_search_publisher($user_query,"notices_authority");
		break;
	case "titre_uniforme" :
		$as=new affiliate_search_titre_uniforme($user_query,"notices_authority");
		break;
}
print $as->getResults();

switch ($search_type) {
	case 'simple_search':
	case 'tags_search':
		// constitution du form pour la suite
		$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"mode\" value=\"$mode\">\n";
		$form .= "<input type=\"hidden\" name=\"count\" value=\"$count\">\n";
		$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause_bull\" value=\"".htmlentities($clause_bull,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"clause_bull_num_notice\" value=\"".htmlentities($clause_bull_num_notice,ENT_QUOTES,$charset)."\">\n";
		if($opac_indexation_docnum_allfields) 
			$form .= "<input type=\"hidden\" name=\"join\" value=\"".htmlentities($join,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">\n";
		$form .= "<input type=\"hidden\" name=\"author_type\" value=\"$author_type\">\n";		
		$form .= "<input type=\"hidden\" name=\"id_thes\" value=\"".$id_thes."\">\n";
		$form .= "<input type=\"hidden\" name=\"surligne\" value=\"".(isset($surligne) ? $surligne : '')."\">\n";
		$form .= "<input type=\"hidden\" name=\"tags\" value=\"".(isset($tags) ? $tags : '')."\">\n";
		$f_values=$form;
		$form = "<form name=\"form_values\" action=\"./index.php?lvl=more_results\" method=\"post\">\n";
		$form .= $f_values;
		$form .= "<input type=\"hidden\" name=\"page\" value=\"$page\">\n";
		if($opac_allow_affiliate_search){
			$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
			$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
		}
		$form .= "<input type=\"hidden\" name=\"nbexplnum_to_photo\" value=\"".$nbexplnum_to_photo."\">\n";
		$form .= "</form>";
		if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
			$form .= "<form name='cart_values' action='./cart_info.php?lvl=more_results' method='post' target='cart_info'>\n";
			$form .= $f_values;
			$form .= "</form>";
		}
		break;
	case 'extended_search':
		$form=$es->make_hidden_search_form("./index.php?lvl=more_results&mode=extended");
		if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) 
			$form.=$es->make_hidden_search_form("./cart_info.php?lvl=more_results&mode=extended","cart_values","cart_info");
		break;
	case 'external_search':
		$form=$es->make_hidden_search_form("./index.php?lvl=more_results&mode=external","form_values","",false);
		if ($_SESSION["ext_type"]!="multi") {
			$form.="<input type='hidden' name='external_env' value='".htmlentities(stripslashes($external_env),ENT_QUOTES,$charset)."'/>";
			$form.="</form>";
		} else $form.="</form>";
		if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) 
			$form.=$es->make_hidden_search_form("./cart_info.php?lvl=more_results&mode=external","cart_values","cart_info");
		break;
}
print pmb_bidi($form);
