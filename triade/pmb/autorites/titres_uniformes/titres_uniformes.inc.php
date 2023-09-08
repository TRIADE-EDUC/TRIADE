<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes.inc.php,v 1.29 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

// fonctions communes aux pages de gestion des autorités
require_once('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_titres_uniformes_controller.class.php");

// gestion des titres uniformes
print '<h1>'.$msg[140].'&nbsp;: '. $msg['aut_menu_titre_uniforme'].'</h1>';

$entities_titres_uniformes_controller = new entities_titres_uniformes_controller($id);
$entities_titres_uniformes_controller->set_url_base('autorites.php?categ=titres_uniformes');
$entities_titres_uniformes_controller->proceed();
