<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.3 2019/06/18 12:42:56 ngantier Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path.'/explnum_licence/explnum_licence.class.php');
session_write_close();
$id = intval($id);
switch ($sub) {
	case 'get_licence_tooltip':
		print explnum_licence::get_explnum_licence_tooltip($id);
		break;
	case 'get_licence_as_pdf':
		print explnum_licence::get_explnum_licence_as_pdf($id);
		break;
	case 'get_licence_quotation':
		print explnum_licence::get_explnum_licence_quotation($id);
		break;
}
