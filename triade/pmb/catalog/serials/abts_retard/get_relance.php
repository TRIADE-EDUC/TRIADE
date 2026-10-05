<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Yves PRATTER                                                   |
// +-------------------------------------------------+
// $Id: get_relance.php,v 1.3 2023/02/08 07:38:34 dgoron Exp $

$base_path="./../../..";                            
$base_auth = "";  
$base_title = "\$msg[demandes_menu_title]";
$base_noheader = 1;
$base_nobody   = 1;   
require_once ("$base_path/includes/init.inc.php"); 

global $class_path, $print_mode;

require_once("$class_path/abts_pointage.class.php");

$abts= new abts_pointage();
$abts->print_mode=$print_mode;
$abts->relance_retard();


?>