<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rss.inc.php,v 1.12 2019-02-12 08:28:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($id_rss_flux)) $id_rss_flux = 0;

require_once($class_path."/dsi/rss_controller.class.php");

print "<h1>".$msg['dsi_rss_titre']."</h1>" ;

rss_controller::proceed($id_rss_flux);