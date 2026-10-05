<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: autorites.php,v 1.23 2021/04/29 12:22:22 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "AUTORITES_AUTH";  
$base_title = "\$msg[132]";    
$base_use_dojo = 1;
require_once ("$base_path/includes/init.inc.php");

require_once($class_path."/authperso.class.php");

// modules propres à autorites.php ou à ses sous-modules
require_once($class_path."/modules/module_autorites.class.php");
require_once($class_path.'/interface/autorites/interface_autorites_form.class.php');
require_once($class_path.'/interface/autorites/interface_autorites_replace_form.class.php');
require("$include_path/templates/autorites.tpl.php");

if($categ == 'caddie') {
	if(empty($sub)) $sub = 'gestion';
}
module_autorites::get_instance()->proceed_header();

include("./autorites/autorites.inc.php");

module_autorites::get_instance()->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
