<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.34.2.2.2.1 2025/03/06 08:09:03 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $action, $object_type, $class_path, $plugin, $sub, $PMBuserid, $num_roles;
global $opac_search_universes_activate, $search_xml_file, $search_xml_file_full_path;

//En fonction de $categ, il inclut les fichiers correspondants
switch($categ):
	case 'acces':
		include('./admin/acces/ajax/acces.inc.php');
		break;
	case 'req':
		include('./admin/proc/ajax/req.inc.php');
		break;
	case 'sync':
		include('./admin/connecteurs/in/dosync.php');
		break;
	case 'gestion':
	    include('./admin/gestion/ajax_main.inc.php');
	    break;
	case 'opac':
		include('./admin/opac/ajax_main.inc.php');
	   break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'nomenclature' :
		include("./admin/nomenclature/ajax_main.inc.php");
		break;
	case 'webdav' :
		include("./admin/connecteurs/out/webdav/ajax_main.inc.php");
		break;
	case 'connector' :
		include("./admin/connecteurs/ajax_main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("admin",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'cms':
		include('./admin/cms/ajax_main.inc.php');
		break;
	case 'planificateur':
		include("./admin/planificateur/ajax_main.inc.php");
		break;
	case 'search_universes':
		if ($opac_search_universes_activate) {
			include('./admin/search_universes/ajax_main.inc.php');
		}
		break;
	case 'param':
		include("./admin/param/ajax_main.inc.php");
		break;
	case 'extended_search' :
		require_once($class_path."/search.class.php");
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';

		$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	case 'misc':
		require_once($class_path.'/modules/module_admin.class.php');
		$module_admin = new module_admin();
		$module_admin->proceed_ajax_misc();
		break;
	case 'docnum':
		switch($sub) {
			case 'perso':
				switch($action) {
					case "list":
						require_once "$class_path/custom_fields/custom_fields_controller.class.php";
						custom_fields_controller::proceed_ajax($object_type);
						break;
				}
				break;
			default:
				switch($action) {
					case "list":
						lists_controller::proceed_ajax($object_type, 'configuration/explnum');
						break;
				}
		}
		break;
	case 'animations':
	    include("./admin/animations/ajax_main.inc.php");
	    break;
	case 'contact_forms':
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'contact_forms');
				break;
		}
		break;
	case 'ark':
	    include("./admin/ark/ajax_main.inc.php");
	    break;
	case 'digital_signature':
	    include("./admin/digital_signature/ajax_main.inc.php");
	    break;
	case 'users':
	    switch($action) {
	        case 'rights_content_form':
	            $user = new user($PMBuserid);
	            if (!empty($num_roles)) {
	                if (!is_array($num_roles)) {
	                   $num_roles = explode(',', $num_roles);
	                }
	                $user->adjustment_rights_from_roles($num_roles);
	            }
	            print $user->get_rights_content_form();
	            break;
	        case 'list':
                users_controller::proceed_ajax($object_type);
	        break;
	    }
	    break;
	default:
		switch($sub) {
			case 'perso':
			case 'parperso':
			case 'authperso':
			case 'type':
				switch($action) {
				    case "list":
					    if ($object_type == 'configuration_authorities_authperso_ui') {
					        lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
					    } else {
					        require_once "$class_path/custom_fields/custom_fields_controller.class.php";
					        custom_fields_controller::proceed_ajax($object_type);
					    }
						break;
				}
				break;
			default:
				switch($action) {
					case "list":
						lists_controller::proceed_ajax($object_type, 'configuration/'.$categ);
						break;
				}
				break;
		}
		break;
endswitch;
