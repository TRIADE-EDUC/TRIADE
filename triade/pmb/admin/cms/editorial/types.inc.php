<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: types.inc.php,v 1.6 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $quoi, $id, $elem;
require_once($class_path."/cms/cms_editorial_types.class.php");
require_once($class_path."/cms/cms_editorial_type.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");
require_once($class_path."/list/configuration/cms_editorial/list_configuration_cms_editorial_type_ui.class.php");
require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");

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
		switch($action){
			case "add":
				$cms_editorial_type = new cms_editorial_type();
				$cms_editorial_type->set_element($elem);
				print $cms_editorial_type->get_form();
				break;
			case "edit":
				$cms_editorial_type = new cms_editorial_type($id);
				print $cms_editorial_type->get_form();
				break;
			case "save":
				$cms_editorial_type = new cms_editorial_type($id);
				if(!$id) {
					$cms_editorial_type->set_element($elem);
				}
				$cms_editorial_type->set_properties_from_form();
				$cms_editorial_type->save();
				print list_configuration_cms_editorial_type_ui::get_instance(array('element' => $elem))->get_display_list();
				break;
			case "delete":
				$cms_editorial_type = new cms_editorial_type($id);
				$deleted = $cms_editorial_type->delete();
				if($deleted) {
					print list_configuration_cms_editorial_type_ui::get_instance(array('element' => $elem))->get_display_list();
				} else {
					pmb_error::get_instance('cms_editorial_type')->display(1, "./admin.php?categ=cms_editorial&sub=type&elem=".$elem);
				}
				break;		
			case "list" :
			default :
				print list_configuration_cms_editorial_type_ui::get_instance(array('element' => $elem))->get_display_list();
				break;
		}
		break;
}


