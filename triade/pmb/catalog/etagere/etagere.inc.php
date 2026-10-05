<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.inc.php,v 1.24 2020/10/21 07:34:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $idetagere, $id;

require_once($class_path."/etageres/etageres_controller.class.php");

etageres_controller::proceed((!empty($idetagere) ? $idetagere : $id));
