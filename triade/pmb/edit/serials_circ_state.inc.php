<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_circ_state.inc.php,v 1.3 2022/03/25 07:38:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path.'/serialcirc/serialcirc_controller.class.php');

serialcirc_controller::set_list_ui_class_name('list_serialcirc_state_ui');
serialcirc_controller::proceed($id);