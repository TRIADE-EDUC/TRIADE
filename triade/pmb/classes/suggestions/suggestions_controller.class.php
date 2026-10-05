<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_controller.class.php,v 1.2 2023/09/28 09:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class suggestions_controller extends lists_controller {
	
	protected static $model_class_name = 'suggestions';
	
	protected static $list_ui_class_name = 'list_suggestions_ui';
	
	protected static $id_bibli;
	
	public static function set_id_bibli($id_bibli) {
		static::$id_bibli = intval($id_bibli);
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		
		$filters = array();
		$filters['entite'] = static::$id_bibli;
		
		return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
}