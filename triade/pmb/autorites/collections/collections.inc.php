<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.inc.php,v 1.19 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $tri_param, $entities_collections_controller, $id;

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_collections_controller.class.php");

// gestion des collections
$tri_param = ' order by index_coll ';

$entities_collections_controller = new entities_collections_controller($id);
$entities_collections_controller->set_url_base('autorites.php?categ=collections');
$entities_collections_controller->proceed();