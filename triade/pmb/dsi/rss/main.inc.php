<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2021/12/01 13:09:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id_rss_flux;
$id_rss_flux = intval($id_rss_flux);

require_once($class_path."/dsi/rss_controller.class.php");

rss_controller::proceed($id_rss_flux);