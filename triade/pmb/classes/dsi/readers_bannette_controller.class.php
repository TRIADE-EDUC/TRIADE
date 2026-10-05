<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: readers_bannette_controller.class.php,v 1.2 2020/11/05 09:39:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class readers_bannette_controller extends lists_controller {
	
	protected static $model_class_name = '';
	protected static $list_ui_class_name = 'list_readers_bannette_ui';
	
	protected static $id_bannette;
	
	public static function proceed($id=0) {
		global $faire;
		
		if ($faire=="enregistrer") {
			$list_ui_instance = static::get_list_ui_instance();
			$list_ui_instance->run_action_affect_lecteurs();
		}
		parent::proceed($id);
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		$instance = new static::$list_ui_class_name($filters, $pager, $applied_sort);
		$instance->set_id_bannette(static::$id_bannette);
		return $instance;
	}

	public static function set_id_bannette($id_bannette) {
		static::$id_bannette = intval($id_bannette);
	}
}// end class
