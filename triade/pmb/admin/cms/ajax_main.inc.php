<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2019/06/18 12:29:08 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_editorial_types.class.php");

switch($sub):
	case "editorial" :
		switch($action) {
			case "get_env_var":
			    $page_id = intval($page_id);
				print cms_editorial_types::get_env_var_options($page_id);
				break;
		}
		break;
	default:
		ajax_http_send_error('400',$msg["ajax_commande_inconnue"]);
		break;		
endswitch;	
