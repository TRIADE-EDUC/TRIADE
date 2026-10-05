<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_param_social_network.inc.php,v 1.2 2014/03/25 08:44:41 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub){
	case 'get':
		if ($opac_param_social_network=='') {
			ajax_http_send_response("0");
			exit;
		}else{
			ajax_http_send_response($opac_param_social_network);
		}
		break;
}
?>