<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reporting.inc.php,v 1.6 2018-11-08 16:20:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/scheduler/scheduler_dashboard_controller.class.php");

scheduler_dashboard_controller::proceed($id);




