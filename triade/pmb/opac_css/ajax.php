<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.php,v 1.23 2018-12-04 09:54:54 ngantier Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;

//Il me faut le charset pour la suite
require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path.'/includes/opac_config.inc.php');
// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
	
if (strtoupper($charset) != "UTF-8" && !(isset($_GET['is_iframe']) && $_GET['is_iframe'])) {
	$_POST = array_uft8_decode($_POST);
}
//$_GET = array_uft8_decode($_GET);

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
		include($main_file);
	break;
	case 'expand_notice':
		include($main_file);
	break;
	case 'cms':
		include($main_file);
	break;
	case 'empr_extended':
		include("./includes/empr_extended.inc.php");
	break;
	case "selectors":
	    // classes pour la gestion des sélecteurs
	    if(!isset($autoloader) || !is_object($autoloader)){
	        require_once($class_path."/autoloader.class.php");
	        $autoloader = new autoloader();
	    }
	    $autoloader->add_register("selectors_class",true);
	    
	    require_once($base_path.'/selectors/classes/selector_controller.class.php');
	    $selector_controller = new selector_controller();
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

function array_uft8_decode($tab){
	foreach($tab as $key => $val) {
		if(is_array($val)){
			$tab[$key] = array_uft8_decode($val);
		}else{
			$tab[$key] = utf8_decode($val);
		}
	}
	return $tab;
}
?>