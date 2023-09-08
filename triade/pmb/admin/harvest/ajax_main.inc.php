<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2012-01-25 15:20:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/harvest.class.php");

switch($sub) {
	case 'add_field':
		$harvest=new harvest($id);
		ajax_http_send_response($harvest->add_field($id_field,$nb));
	break;

	default:
	break;
}
?>