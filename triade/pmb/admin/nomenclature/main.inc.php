<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.7 2021/02/08 10:30:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub;
require_once($class_path.'/interface/admin/interface_admin_nomenclature_form.class.php');

switch($sub) {
	case 'family':
		include("./admin/nomenclature/family.inc.php");		
		break;
	case 'family_musicstand':
		include("./admin/nomenclature/family_musicstand.inc.php");
		break;
	case 'formation':
		include("./admin/nomenclature/formation.inc.php");		
		break;
	case 'formation_type':
		include("./admin/nomenclature/formation_type.inc.php");
		break;
	case 'voice':
		include("./admin/nomenclature/voice.inc.php");		
		break;
	case 'instrument':
		include("./admin/nomenclature/instrument.inc.php");
		break;
	case 'material':
		include("./admin/nomenclature/material.inc.php");
		break;
	default:
		break;
}
?>