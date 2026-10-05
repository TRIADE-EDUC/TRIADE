<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_facettes_sets_controller.class.php,v 1.3 2024/03/21 11:06:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_facettes_sets_controller extends configuration_controller {
	
    protected static $model_class_name = 'facettes_set';
    
	protected static $type;
	
	protected static $is_external = 0;
	
	protected static $num_user = 0;
	
	protected static function get_model_instance($id) {
	    $model_instance = new static::$model_class_name($id);
	    $model_instance->set_type(static::$type);
	    return $model_instance;
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    $filters = array('types' => [static::$type]);
	    if (!empty(static::$num_user)) {
	        $filters['num_user'] = static::$num_user;
	        $grp_num = user::get_param(static::$num_user, 'grp_num');
	        if ($grp_num) {
	            $filters['users_groups'] = array($grp_num);
	        }
	    }
	    return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
	
	public static function has_rights($id=0) {
	    return true;
	}
	
	public static function proceed($id=0) {
		global $sub;
		global $action;
		
		$id = intval($id);
		if($sub == 'facettes_authorities') {
			print static::get_authorities_tabs();
		}
		switch($action) {
			case "up":
			    $facettes_set_user = new facettes_set_user($id);
			    $facettes_set_user->up();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case "down":
			    $facettes_set_user = new facettes_set_user($id);
			    $facettes_set_user->down();
			    $list_ui_instance = static::get_list_ui_instance();
			    print $list_ui_instance->get_display_list();
				break;
			default:
			    parent::proceed($id);
				break;
		}
	}
	
	public static function get_url_base_authority_tab() {
	    return facettes_gestion_controller::get_url_base_authority_tab();
	}
	
	public static function get_authority_tab($type, $label='') {
		return facettes_controller::get_authority_tab($type, $label);
	}
	
	public static function get_authorities_tabs() {
	    return facettes_controller::get_authorities_tabs();
	}
	
	public static function set_type($type) {
		static::$type = $type;
	}
	
	public static function set_is_external($is_external) {
		static::$is_external = intval($is_external);
	}
	
	public static function set_num_user($num_user) {
	    static::$num_user = intval($num_user);
	}
	
	public static function get_url_base() {
	    $url_base = parent::get_url_base();
	    if(!empty(static::$type)) {
	        $url_base .= '&type='.static::$type;
	    }
	    return $url_base;
	}
}

