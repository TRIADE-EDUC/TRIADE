<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1 2016/10/18 07:54:03 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($base_path."/admin/connecteurs/in/cairn/cairn.class.php");

switch($act){
	case 'cairn_generate_id_pmb':
		global $id_cairn;
		$cairn = new cairn();
		$id_pmb = $cairn->set_pmb_id($id_cairn)->get_pmb_id();
		ajax_http_send_response($id_pmb);
		break;
	default:
		break;
}