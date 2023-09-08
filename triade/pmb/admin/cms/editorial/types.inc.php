<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: types.inc.php,v 1.4 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($quoi)) $quoi = '';

require_once($class_path."/cms/cms_editorial_types.class.php");
require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("cms_modules",true);

switch($quoi){
	case "fields":
		switch($elem){
			case "article_generic" :
			case "section_generic" :
				$query = "select id_editorial_type from cms_editorial_types where editorial_type_element = '".$elem."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$row = pmb_mysql_fetch_object($result);
					$type_id = $row->id_editorial_type;
				}
				break;
		}
		$fields = new cms_editorial_parametres_perso($type_id,"./admin.php?categ=cms_editorial&sub=type&elem=".$elem."&quoi=fields&type_id=".$type_id);
		$fields->proceed();
		break;
	default :
		$types = new cms_editorial_types($elem);
		switch($action){
			case "edit":
				print $types->get_form($id);
				break;
			case "save":
				$types->save();
				print $types->get_table();
				break;
			case "delete":
				$types->delete($id);
				print $types->get_table();
				break;		
			case "list" :
			default :
				print $types->get_table();
				break;
		}
		break;
}


