<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-02-20 19:04:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($act)) $act = '';

require_once("$class_path/stat_view.class.php");

$admin_layout = str_replace('!!menu_sous_rub!!', $msg["stat_opac_menu"], $admin_layout);
print $admin_layout;

$stat_view = new stat_view($section,$act);
$stat_view->proceed();

?>