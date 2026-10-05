<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.php,v 1.5 2023/08/17 09:47:55 dbellamy Exp $
//
// Affichage de la zone de recherche et choix du mode de navigation dans les catégories

$base_path="..";                            
$base_auth = ""; 
$base_title="";

require_once($base_path."/includes/init.inc.php");  
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
	
// récupération paramètres MySQL et connection á la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

// fonctions de gestion de formulaire
require_once($base_path.'/includes/javascript/form.inc.php');
require_once($base_path.'/includes/templates/common.tpl.php');

require_once($base_path.'/includes/divers.inc.php');

if(!isset($user_input)) $user_input = '';

print $popup_header;
// modules propres à select.php ou à ses sous-modules
print "<script src='".$javascript_path."/misc.js'></script>";

// classe pour la gestion des catégories dans le sélecteur
require_once($base_path."/selectors/classes/selector_category.class.php");

$selector_category = new selector_category(stripslashes($user_input));
$selector_category->proceed();

print $popup_footer;