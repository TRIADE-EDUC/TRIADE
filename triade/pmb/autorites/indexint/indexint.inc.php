<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.22 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_pclass, $class_path, $id, $id_pclass, $deflt_pclassement;

if(!isset($id_pclass) && empty($id) && !empty($deflt_pclassement)) {
	$id_pclass = intval($deflt_pclassement);
}

require_once($class_path."/entities/entities_indexint_controller.class.php");

$entities_indexint_controller = new entities_indexint_controller($id);
$entities_indexint_controller->set_id_pclass($id_pclass);
$entities_indexint_controller->set_url_base('autorites.php?categ=indexint');
$entities_indexint_controller->proceed();
