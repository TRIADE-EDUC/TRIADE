<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: drm_parameters.inc.php,v 1.1 2018-06-25 15:30:44 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/pnb/drm_parameters.class.php");

$pnb_param_devices = new drm_parameters();
$pnb_param_devices->proceed();
