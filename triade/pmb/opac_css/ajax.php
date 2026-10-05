<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.php,v 1.33 2023/08/30 14:56:19 dgoron Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;
$base_is_http_request=1;

//Il me faut le charset pour la suite
require_once($base_path."/includes/init.inc.php");

global $include_path, $charset, $module, $plugin, $sub;
global $pmb_indexation_lang;
global $opac_opac_view_activate, $opac_view, $pmb_opac_view_class;

require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path.'/includes/opac_config.inc.php');
// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
	
if (strtoupper($charset) != "UTF-8" && !(isset($_GET['is_iframe']) && $_GET['is_iframe'])) {
	$_POST = encoding_normalize::utf8_decode($_POST);
}

require_once($base_path."/includes/global_vars.inc.php");

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');

require_once($base_path."/includes/check_session_time.inc.php");

require_once($base_path."/includes/misc.inc.php");
require_once($base_path.'/includes/divers.inc.php');

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');
require_once($base_path."/includes/rec_history.inc.php");

// inclusion des fonctions utiles pour renvoyer la réponse à la requette recu 
require_once ($base_path . "/includes/ajax.inc.php");
require_once($base_path."/includes/marc_tables/".$pmb_indexation_lang."/empty_words");

require_once($include_path.'/plugins.inc.php');

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");
	if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"]){
		$opac_view_class= new $pmb_opac_view_class($_SESSION["opac_view"],$_SESSION["id_empr_session"]);
	 	if($opac_view_class->id){
	 		$opac_view_class->set_parameters();
	 		$opac_view_filter_class=$opac_view_class->opac_filters;
	 		$_SESSION["opac_view"]=$opac_view_class->id;
	 		if(!$opac_view_class->opac_view_wo_query) {
	 			$_SESSION['opac_view_query']=1;
	 		}
	 	}else {
	 		$_SESSION["opac_view"]=0;
	 	}
		$css=$_SESSION["css"]=$opac_default_style;
	}
}

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

require_once($base_path."/includes/templates/common.tpl.php");

$main_file="./$module/ajax_main.inc.php";
switch($module) {
	case 'ajax':
	case 'expand_notice':
	case 'cms':
	case 'dsi':
	case 'animations':
	case 'digital_signature':
	case 'empr':
		include($main_file);
	break;
	case 'empr_extended':
		include("./includes/empr_extended.inc.php");
	break;
	case "selectors":
	    // classes pour la gestion des sélecteurs
	    
	    require_once($base_path.'/selectors/classes/selector_controller.class.php');
	    if(!isset($user_input)) $user_input = '';
	    $selector_controller = new selector_controller(stripslashes($user_input));
	    $selector_controller->proceed();
	    break;
	default:
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax($module, $plugin, $sub);
		if($file){
			include $file;
		}
	break;	
}