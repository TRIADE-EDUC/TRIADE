<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.inc.php,v 1.7 2023/01/09 14:44:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $id;

require_once("$class_path/serialcirc/serialcirc_ask_controller.class.php");

switch($sub){		
	case 'circ_ask':
		serialcirc_ask_controller::proceed($id);
	break;		
	default :
		$list_serialcirc_ask_ui = new list_serialcirc_ask_ui();
		print $list_serialcirc_ask_ui->get_display_list();
	break;		
	
}



