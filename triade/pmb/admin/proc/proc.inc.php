<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: proc.inc.php,v 1.29 2019/12/09 10:29:38 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$include_path/parser.inc.php");
require_once("./classes/notice_tpl_gen.class.php");
require_once($class_path."/procs.class.php");

function show_procs() {
	global $msg;

	//Procédures Internes
	print procs::get_display_list();
	
	//Procédures Externes
	procs::get_display_remote_lists();

	print "<br>
		<input class='bouton' type='button' value=' $msg[704] ' onClick=\"document.location='./admin.php?categ=proc&sub=proc&action=add'\" />
		<input class='bouton' type='button' value=' $msg[procs_bt_import] ' onClick=\"document.location='./admin.php?categ=proc&sub=proc&action=import'\" />
		<input class='bouton' type='button' value=' $msg[admin_menu_req] ' onClick=\"document.location='./admin.php?categ=proc&sub=req&action=add'\" />";
}

if(strpos($action, '_remote') !== false) {
	procs::proceed_remote();
} else {
	procs::proceed();
}
print "<script type='text/javascript' src='./javascript/ajax.js'></script>
       <script type='text/javascript'>ajax_parse_dom();</script>";
