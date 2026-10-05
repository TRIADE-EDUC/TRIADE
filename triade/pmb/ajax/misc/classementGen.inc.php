<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $object_type, $object_id, $action, $classement_libelle;

require_once($class_path."/classementGen.class.php");

$classementGen = new classementGen($object_type,$object_id);

switch($action){
	case "update" :
		print $classementGen->saveLibelle($classement_libelle);
		break;
}