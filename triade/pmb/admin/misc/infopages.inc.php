<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: infopages.inc.php,v 1.17 2021/02/09 07:33:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $action, $sub2, $msg;
global $id;

require_once("$class_path/classementGen.class.php");
require_once("$class_path/infopage.class.php");
// gestion des pages d'information

if ($sub2) {
	switch($sub2){
		case 'classementGen' :
			$baseLink="./admin.php?categ=infopages&sub2=classementGen";
			$classementGen = new classementGen($categ,0);
			$classementGen->proceed($action);
			break;
	}
} else {
	if(empty($action)) {
		print "<div class='hmenu'>
						<span><a href='admin.php?categ=infopages&sub2=classementGen'>".$msg["classementGen_list_libelle"]."</a></span>
					</div><hr>";
	}
	
	require_once($class_path."/configuration/configuration_controller.class.php");
	
	configuration_controller::set_model_class_name('infopage');
	configuration_controller::set_list_ui_class_name('list_infopages_ui');
	configuration_controller::proceed($id);
}
