<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.inc.php,v 1.2 2019-01-09 09:17:03 apetithomme Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path.'/explnum_licence/explnum_licence.class.php');
session_write_close();

switch ($sub) {
	case 'get_licence_tooltip':
		$id+=0;
		print explnum_licence::get_explnum_licence_tooltip($id);
		break;
	case 'get_licence_as_pdf':
		$id+=0;
		print explnum_licence::get_explnum_licence_as_pdf($id);
		break;
	case 'get_licence_quotation':
		$id+=0;
		print explnum_licence::get_explnum_licence_quotation($id);
		break;
}

?>