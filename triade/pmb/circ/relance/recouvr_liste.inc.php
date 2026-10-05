<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr_liste.inc.php,v 1.9 2020/05/04 12:02:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/readers/readers_recouvr_controller.class.php");

//Gestion des recouvrements
readers_recouvr_controller::proceed();

?>