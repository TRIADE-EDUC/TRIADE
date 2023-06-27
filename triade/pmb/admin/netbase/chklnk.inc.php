<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.inc.php,v 1.27 2017-10-26 10:16:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';

require_once ("$include_path/misc.inc.php");
require_once ("$class_path/chklnk/chklnk.class.php");

session_write_close();

$admin_layout = str_replace('!!menu_sous_rub!!', $msg['chklnk_titre'], $admin_layout);
print $admin_layout;

if (!$suite) {
	chklnk::init_filtering_parameters();
	chklnk::init_parameters();
	$chklnk = new chklnk();
	print $chklnk->get_form(); 
} else {
	echo "<h1>".$msg['chklnk_verifencours']."</h1>" ;
	
	chklnk::init_queries();
	
	chklnk::init_progress_bar();
	
	chklnk::set_parameters($parameters);
	
	chklnk::proceed();

	chklnk::update_curl_timeout_parameter();

	echo "<div class='row'><hr /></div><h1>".$msg['chklnk_fin']."</h1>";
}
