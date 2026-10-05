<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id_notice_tpl, $action;

require_once($class_path."/notice_tpl.class.php");

if (!$id_notice_tpl) {
	$id_notice_tpl = 0;
}
$notice_tpl = new notice_tpl($id_notice_tpl);

switch($action){
	case "get_locations" :
		print encoding_normalize::utf8_normalize($notice_tpl->get_form_typenotice_all_loc());
		break;
}