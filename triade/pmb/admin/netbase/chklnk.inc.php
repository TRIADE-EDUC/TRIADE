<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.inc.php,v 1.31 2021/12/15 08:47:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $suite;
global $filtering_parameters, $parameters;

if(empty($suite)) $suite = '';

require_once ("$include_path/misc.inc.php");
require_once ("$class_path/chklnk/chklnk.class.php");

session_write_close();

if (!$suite) {
	chklnk::init_filtering_parameters();
	chklnk::init_parameters();
	$chklnk = new chklnk();
	print $chklnk->get_form(); 
} else {
	echo "<h1>".$msg['chklnk_verifencours']."</h1>" ;
	
	if(empty($filtering_parameters['chkrestrict'])) {
		$filtering_parameters['chkrestrict'] = 0;
	}
	chklnk::set_filtering_parameters($filtering_parameters);
	
	chklnk::init_queries();
	
	chklnk::init_progress_bar();
	
	chklnk::set_parameters($parameters);
	
	chklnk::proceed();

	chklnk::update_curl_timeout_parameter();

	echo "<div class='row'><hr /></div><h1>".$msg['chklnk_fin']."</h1>";
}
