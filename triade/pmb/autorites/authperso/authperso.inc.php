<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.15 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id_authperso, $id;

require_once('./autorites/auth_common.inc.php');

require_once($class_path."/entities/entities_authperso_controller.class.php");

require_once($class_path.'/authperso.class.php');

// gestion des authperso
$entities_authperso_controller = new entities_authperso_controller($id);
$entities_authperso_controller->set_id_authperso($id_authperso);
$entities_authperso_controller->set_url_base('autorites.php?categ=authperso&id_authperso='.$id_authperso);
$entities_authperso_controller->proceed();
