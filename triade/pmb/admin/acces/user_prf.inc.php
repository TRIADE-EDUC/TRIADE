<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_prf.inc.php,v 1.11 2023/08/02 07:36:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $id, $ac, $dom;

require_once($class_path."/acces/acces_profiles_users_controller.class.php");
require_once("$class_path/acces.class.php");
require_once("$include_path/templates/acces.tpl.php");

//recuperation domaine
$id = intval($id);
if (!$id) {
    return;
}
if ( empty($ac) ) {
    $ac= new acces();
}
if ( empty($dom) ) {
    $dom=$ac->setDomain($id);
}

acces_profiles_users_controller::set_dom($dom);
acces_profiles_users_controller::proceed($id);