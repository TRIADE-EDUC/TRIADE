<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: acquisition.php,v 1.17 2021/04/28 06:52:35 dgoron Exp $

global $base_path, $class_path, $include_path, $base_auth, $base_title, $base_use_dojo, $base_noheader;

// définition du minimum nécéssaire 
$base_path = ".";                            
$base_auth = "ACQUISITION_AUTH";  
$base_title = "\$msg[acquisition_menu_title]";
$base_use_dojo = 1;

if (isset($_POST['dest']) && ($_POST['dest'] == "TABLEAU" || $_POST['dest'] == "TABLEAUHTML")) {
    $base_noheader = 1;
}

require_once ("$base_path/includes/init.inc.php");  

// modules propres à acquisition.php ou à ses sous-modules
require_once($class_path."/modules/module_acquisition.class.php");
require_once($class_path.'/interface/acquisition/interface_acquisition_form.class.php');
require_once("$include_path/templates/acquisition.tpl.php");

module_acquisition::get_instance()->proceed_header();

require_once("./acquisition/acquisition.inc.php");

module_acquisition::get_instance()->proceed_footer();

// deconnection MYSql
pmb_mysql_close();
?>