<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning_edition_controller.class.php,v 1.1 2021/10/21 12:03:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/resa_planning/resa_planning_controller.class.php");

class resa_planning_edition_controller extends resa_planning_controller {
	
	protected static $list_ui_class_name = 'list_resa_planning_edition_ui';
	
}