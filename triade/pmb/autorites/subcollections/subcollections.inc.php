<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollections.inc.php,v 1.16 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_subcollections_controller.class.php");

// gestion des sous-collections
print '<h1>'.$msg[140].'&nbsp;: '. $msg[137].'</h1>';

$entities_subcollections_controller = new entities_subcollections_controller($id);
$entities_subcollections_controller->set_url_base('autorites.php?categ=souscollections');
$entities_subcollections_controller->proceed();
