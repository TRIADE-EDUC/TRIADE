<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: material.inc.php,v 1.1 2016/03/30 13:13:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/nomenclature_material_admin.class.php");

$nomenclature_material = new nomenclature_material_admin();
switch($action) {
	case 'save':
		$nomenclature_material->get_values_from_form();
		print $nomenclature_material->save();
	default:
		print $nomenclature_material->get_form();
		break;
}
