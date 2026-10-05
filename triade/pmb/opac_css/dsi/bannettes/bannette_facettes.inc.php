<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_facettes.inc.php,v 1.1 2021/04/12 10:38:43 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $suite, $id_bannette, $i_field, $crit_id, $ss_crit_id;

require_once "$class_path/bannette_facettes.class.php";

switch ($suite) {	
	case "add_facette":
		$facette = new bannette_facettes($id_bannette);
		ajax_http_send_response($facette->add_facette($i_field));
		break;
	case "ss_crit":
		$facette = new bannette_facettes($id_bannette);
		ajax_http_send_response($facette->add_ss_crit($i_field, $crit_id, $ss_crit_id));
		break;
	break;
	default:
	   break;
}