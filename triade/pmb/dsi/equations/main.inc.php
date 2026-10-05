<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8 2021/04/21 18:40:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id_equation;
require_once($class_path."/dsi/equations_controller.class.php") ;

$id_equation = intval($id_equation);
equations_controller::proceed($id_equation);
