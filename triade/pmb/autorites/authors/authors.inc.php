<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authors.inc.php,v 1.25 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_authors_controller.class.php");

// gestion des auteurs
$entities_authors_controller = new entities_authors_controller($id);
$entities_authors_controller->set_url_base('autorites.php?categ=auteurs');
$entities_authors_controller->proceed();
