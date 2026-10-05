<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: circ.php,v 1.33 2022/04/22 11:31:41 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "CIRCULATION_AUTH";  
$base_title = "\$msg[5]";
$base_use_dojo = 1;

if (isset($_POST['dest']) && ($_POST['dest'] == "TABLEAU" || $_POST['dest'] == "TABLEAUHTML" || $_POST['dest'] == "TABLEAUCSV")) {
	$base_noheader = 1;
} elseif (isset($_GET['dest']) && ($_GET['dest'] == "TABLEAU" || $_GET['dest'] == "TABLEAUHTML" || $_GET['dest'] == "TABLEAUCSV")) {
	$base_noheader = 1;
}

require_once ("$base_path/includes/init.inc.php");  

global $class_path, $include_path, $categ;

if ((SESSrights & RESTRICTCIRC_AUTH) && ($categ!="pret") && ($categ!="pretrestrict") ) {
	$sub="";
	$categ="";
}
// modules propres à circ.php ou à ses sous-modules
require_once($class_path."/modules/module_circ.class.php");
require_once($class_path.'/interface/circ/interface_circ_form.class.php');
require_once("$include_path/templates/circ.tpl.php");
require_once("$include_path/templates/empr.tpl.php");
require_once("$include_path/templates/expl.tpl.php");

module_circ::get_instance()->proceed_header();

include("./circ/main.inc.php");
print alert_sound_script();
	
module_circ::get_instance()->proceed_footer();

pmb_mysql_close();
