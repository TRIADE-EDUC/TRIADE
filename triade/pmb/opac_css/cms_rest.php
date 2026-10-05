<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_rest.php,v 1.4 2023/08/28 14:04:12 tsamson Exp $

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

if($charset != "utf-8"){
	$_POST = encoding_normalize::utf8_decode($_POST);
}

require_once($base_path."/includes/global_vars.inc.php");

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");


require_once($base_path.'/includes/start.inc.php');

require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');
require_once($base_path."/includes/rec_history.inc.php");

require_once($base_path.'/includes/divers.inc.php');
require_once($base_path."/includes/misc.inc.php");
require_once($base_path."/includes/templates/common.tpl.php");
// inclusion des fonctions utiles pour renvoyer la réponse à la requette recu 
require_once ($base_path . "/includes/ajax.inc.php");
require_once($base_path."/includes/marc_tables/".$pmb_indexation_lang."/empty_words");

//si les vues sont activées (à laisser après le calcul des mots vides)
if($opac_opac_view_activate){
	if($opac_view)	{
		$_SESSION["opac_view"]=$opac_view;
	}
	$_SESSION['opac_view_query']=0;
	if(!$pmb_opac_view_class) $pmb_opac_view_class= "opac_view";
	require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");
	if($_SESSION["opac_view"]){
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

//TOUT EST CHARGE, on va au plus simple...

$elems = explode("/",$_SERVER['PATH_INFO']);

if(file_exists('./temp/'.$elems[1])) {
	$content = unserialize(file_get_contents('./temp/'.$elems[1]));
 	$jsonsource = new $content['classname']($content['id']);
 	$jsonsource->set_datas($content['datas']);
 	$jsonsource->set_nb_row($content['nb_row']);
//   	header('Content-Type: application/json');
 	print encoding_normalize::json_encode($jsonsource->store_proceed($content));
}