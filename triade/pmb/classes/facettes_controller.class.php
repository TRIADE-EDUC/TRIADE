<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_controller.class.php,v 1.20.4.1 2025/02/24 15:36:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// Controleur de facettes
global $class_path;
require_once($class_path."/facette_search_opac.class.php");
require_once($class_path."/facette.class.php");

class facettes_controller extends lists_controller {
	
	protected static $type;
	
	protected static $is_external = 0;
	
	protected static $num_facettes_set = 0;
	
	protected static $num_user = 0;
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    $filters = array('type' => static::$type, 'num_facettes_set' => static::$num_facettes_set);
	    if (!empty(static::$num_user)) {
	        $filters['num_user'] = static::$num_user;
	        $grp_num = user::get_param(static::$num_user, 'grp_num');
	        if ($grp_num) {
	            $filters['users_groups'] = array($grp_num);
	        }
	    }
	    return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
	
	protected static function init_list_ui_class_name() {
	    
	}
	
	public static function has_rights($id=0) {
	    return true;
	}
	
	public static function proceed($id=0) {
		global $sub;
		global $action;
		
		$id = intval($id);
		static::init_list_ui_class_name();
		if($sub == 'facettes_authorities') {
			print static::get_authorities_tabs();
		}
		$list_ui_class_name = static::$list_ui_class_name;
		$facette_search = static::get_facette_search_opac_instance(static::$type, static::$is_external);
        $list_ui_class_name::set_facettes_model($facette_search);
		switch($action) {
		    case "add":
		    case "edit":
		        $facette = static::get_model_instance($id);
		        print $facette->get_form();
		        break;
		    case "update":
		    case "save":
		        $facette = static::get_model_instance($id);
		        $facette->set_properties_from_form();
		        $facette->save();
		        static::redirect_display_list();
		        break;
			case "delete":
			    $facette = static::get_model_instance($id);
				$facette->delete();
				static::redirect_display_list();
				break;
			case "up":
				facette_search_opac::facette_up($id, static::$type);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case "down":
				facette_search_opac::facette_down($id, static::$type);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case "order":
				facette_search_opac::facette_order_by_name(static::$type);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
			    parent::proceed($id);
				break;
		}
	}
	
	public static function get_url_base_authority_tab() {
	    global $base_path, $current_module, $categ;
	    
	    if($current_module == 'account') {
	        return $base_path.'/'.$current_module.'.php?categ=facettes&sub=facettes_authorities';
	    } else {
	        return $base_path.'/'.$current_module.'.php?categ='.$categ.'&sub=facettes_authorities';
	    }
	}
	
	public static function get_authority_tab($type, $label='') {
		global $msg;
		
		$url_base = static::get_url_base_authority_tab();
		return "<span".ongletSelect(substr($url_base, strpos($url_base, '?')+1)."&type=".$type).">
			<a title='".$msg[$type]."' href='".$url_base."&type=".$type."'>
				".$msg[$type]."
			</a>
		</span>";
	}
	
	public static function get_authorities_tabs() {
		$authorities_tabs = "<div class='hmenu'>";
		$authorities_tabs .= static::get_authority_tab('authors');
		$authorities_tabs .= static::get_authority_tab('categories');
		$authorities_tabs .= static::get_authority_tab('publishers');
		$authorities_tabs .= static::get_authority_tab('collections');
		$authorities_tabs .= static::get_authority_tab('subcollections');
		$authorities_tabs .= static::get_authority_tab('series');
		$authorities_tabs .= static::get_authority_tab('titres_uniformes');
		$authorities_tabs .= static::get_authority_tab('indexint');
		$authorities_tabs .= static::get_authority_tab('authperso');
		$authorities_tabs .= "</div>";
		return $authorities_tabs;
	}
	
	public static function proceed_ajax($object_type, $directory='') {
		global $sub, $object_type;
		global $action;
		global $type;
		global $list_crit,$sub_field;
		global $suffixe_id, $no_label;
		global $authperso_id, $facet_field;
		
		static::init_list_ui_class_name();
		switch($sub){
		    case "lst_fields_facet":
		    case "lst_fields_facettes_authorities":
		    case "lst_fields_facettes":
			    if( strpos($type, "authperso") !== false && !empty($authperso_id)) {
			        $type = "authperso_".$authperso_id;
			    }
			    $facettes = static::get_facette_search_opac_instance($type);
			    print $facettes->create_list_fields($facet_field);
			    break;
			case "lst_facet":
			case "lst_facettes_authorities":
			case "lst_facettes":
			    if( strpos($type, "authperso") !== false && !empty($authperso_id)) {
			        $type = "authperso_".$authperso_id;
			    }
				$facettes = static::get_facette_search_opac_instance($type);
				print $facettes->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
				break;
			case "lst_facettes_external":
			    $facettes_external = static::get_facette_search_opac_instance('notices_externes',1);
				print $facettes_external->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
				break;
			default:
			    $facette = static::get_model_instance(static::$object_id);
				switch($action) {
					case "add":
					case "edit":
						print $facette->get_form();
						break;
					case "save":
						$facette->set_properties_from_form();
						$facette->save();
						return $facette->get_id();
						break;
					case "list":
						$facette_search = static::get_facette_search_opac_instance(static::$type, static::$is_external);
						$list_ui_class_name = static::$list_ui_class_name;
						$list_ui_class_name::set_facettes_model($facette_search);
						parent::proceed_ajax($object_type, $directory);
						break;
				}
				break;
		}
	}
	
	protected static function get_model_instance($id) {
	    if (strpos(static::$type, "authperso") !== false) {
	        $facette_authperso = new facette_authperso($id, static::$is_external);
	        $facette_authperso->set_type(static::$type);
	        $facette_authperso->set_num_facettes_set(static::$num_facettes_set);
	        return $facette_authperso;
	    }
	    if (strpos(static::$type, "external") !== false) {
	    	static::$is_external = true;
	    }
	    $facette = new facette($id, static::$is_external);
	    $facette->set_type(static::$type);
	    $facette->set_num_facettes_set(static::$num_facettes_set);
	    return $facette;
	}
	
	public static function get_facette_search_opac_instance($type='notices', $is_external=false) {
	    if (empty($type)) {
	        $type = "notices";
	    }
	    if (strpos($type, "authperso") !== false) {
	        return new facette_authperso_search_opac($type, $is_external);
	    }
	    return new facette_search_opac($type, $is_external);
	}
	
	public static function set_type($type) {
		static::$type = $type;
	}
	
	public static function set_is_external($is_external) {
		static::$is_external = intval($is_external);
	}
	
	public static function set_num_facettes_set($num_facettes_set) {
	    static::$num_facettes_set = intval($num_facettes_set);
	}
	
	public static function set_num_user($num_user) {
	    static::$num_user = intval($num_user);
	}
	
	public static function get_url_base() {
	    $url_base = parent::get_url_base();
	    if(!empty(static::$type)) {
	        $url_base .= '&type='.static::$type;
	    }
	    if(!empty(static::$num_facettes_set)) {
	        $url_base .= '&num_facettes_set='.static::$num_facettes_set;
	    }
	    return $url_base;
	}
}

