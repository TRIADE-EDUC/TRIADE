<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simili_search.inc.php,v 1.1 2011/12/30 16:09:14 ngantier Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/facette_search.class.php');

switch($sub){
	case 'search':
		if(!$id_notice)	return '0';	
		ajax_http_send_response(facettes::similitude($id_notice));
		
		break;
	break;
}

?>