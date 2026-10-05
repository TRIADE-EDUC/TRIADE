<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask_ajax.inc.php,v 1.1 2011/11/22 14:48:59 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/serialcirc_diff.class.php");

switch($sub){		
	case '':
		$serialcirc_diff=new serialcirc_diff($id_serialcirc,$num_abt);
		ajax_http_send_response($serialcirc_diff->option_form()); 
	break;		

}



