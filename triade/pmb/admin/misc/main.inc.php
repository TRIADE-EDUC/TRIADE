<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13 2021/11/25 14:30:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $categ, $sub;

$module_admin = module_admin::get_instance();
$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
$module_admin->proceed_misc();