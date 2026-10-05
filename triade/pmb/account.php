<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.php,v 1.87 2022/04/15 12:16:06 dbellamy Exp $

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $class_path;
global $categ, $id;

// Définition du minimum nécéssaire 
$base_path = ".";
$base_auth = "PREF_AUTH|ADMINISTRATION_AUTH";
$base_title = "\$msg[933]";
$base_use_dojo = 1;

require_once "$base_path/includes/init.inc.php";
require_once "$class_path/modules/module_account.class.php";
require_once "$class_path/interface/account/interface_account_form.class.php";
require_once "$include_path/user_error.inc.php";
require_once "$base_path/admin/users/users_func.inc.php";
require_once "$class_path/user.class.php";

include "$include_path/account.inc.php";
include "$include_path/templates/account.tpl.php";

if(empty($categ)) {
	$categ = 'favorites';
}

module_account::get_instance()->proceed_header();

$module_account = module_account::get_instance();
$module_account->set_url_base($base_path.'/account.php?categ='.$categ);
$module_account->set_object_id($id);
$module_account->proceed();

module_account::get_instance()->proceed_footer();

pmb_mysql_close();