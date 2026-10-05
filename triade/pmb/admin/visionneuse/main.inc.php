<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2021/02/09 07:25:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_param, $opac_visionneuse_params, $mimetypeConfByDefault, $sub;
global $include_path, $lang;

$visionneuse_path = $base_path."/opac_css/visionneuse";

require_once "$visionneuse_path/classes/defaultConf.class.php";
require_once "$visionneuse_path/classes/mimetypeClass.class.php";

$class_param = new mimetypeClass("$visionneuse_path/classes/mimetypes/");

//on récup les paramétrages actuels...
$mimetypeConf = unserialize(htmlspecialchars_decode($opac_visionneuse_params));
if (empty($mimetypeConf)) {
	$defaultConf = new defaultConf();
	$mimetypeConfByDefault = $defaultConf->defaultMimetype;
}

switch ($sub) {
	case 'class':
		include('./admin/visionneuse/class_dispo.inc.php');
	break;
	case 'mimetype':
		include('./admin/visionneuse/mimetype.inc.php');
	break;
	default:
		include "$include_path/messages/help/$lang/admin_visionneuse.txt";
	break;
}
?>
