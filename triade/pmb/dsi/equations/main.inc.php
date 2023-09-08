<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.6 2017-11-13 10:24:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';
if(!isset($id_equation)) $id_equation = 0;

require_once($class_path."/dsi/equations_controller.class.php") ;

echo window_title($database_window_title.$msg['dsi_menu_title']);
print "<h1>".$msg['dsi_equ_gestion']."</h1>" ;
$equations_controller = new equations_controller($id_equation);
$equations_controller->proceed($suite);

