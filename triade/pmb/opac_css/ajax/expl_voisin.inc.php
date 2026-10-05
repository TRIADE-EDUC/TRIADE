<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_voisin.inc.php,v 1.1 2012/10/25 13:15:19 ngantier Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/facette_search.class.php');

switch($sub){
	case 'search':
		if(!$id_notice)	return '0';	
		ajax_http_send_response(facettes::expl_voisin($id_notice));
		
		break;
	break;
}

?>