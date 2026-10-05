<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_controller.class.php,v 1.7 2021/03/11 09:11:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_controller extends lists_controller {
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
			case 'add':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'edit':
			case 'modif':
				if ($id) {
					$model_instance = static::get_model_instance($id);
					if(pmb_error::get_instance(static::$model_class_name)->has_error()) {
						pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
					} else {
						print $model_instance->get_form();
					}
				} else {
					static::redirect_display_list();
				}
				break;
			case 'duplicate':
				if($id){
					$model_instance = static::get_model_instance($id);
					if(pmb_error::get_instance(static::$model_class_name)->has_error()) {
						pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
					} else {
						if(method_exists($model_instance, 'set_id')) {
							$model_instance->set_id(0);
						} else {
							$model_instance->id = 0;
						}
						if(property_exists($model_instance, 'duplicate_from_id')) {
							$model_instance->duplicate_from_id = $id;
						}
						print $model_instance->get_form();
					}
				} else {
					static::redirect_display_list();
				}
				break;
			case 'save':
			case 'update':
				$model_class_name = static::$model_class_name;
				$checked = true;
				if(method_exists($model_class_name, 'check_data_from_form')) {
					$checked = $model_class_name::check_data_from_form();
				}
				if($checked) {
					$model_instance = static::get_model_instance($id);
					$model_instance->set_properties_from_form();
					$has_doublon = false;
					if(method_exists($model_instance, 'get_query_if_exists')) {
						$query = $model_instance->get_query_if_exists();
						$result = pmb_mysql_query($query);
						$has_doublon = pmb_mysql_result($result, 0, 0);
						if ($has_doublon > 0) {
							error_form_message($model_instance->libelle.static::get_label_already_used());
						}
					}
					if(!$has_doublon) {
						$model_instance->save();
					}
				}
				if(pmb_error::get_instance(static::$model_class_name)->has_error()) {
					pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
				} else {
					static::redirect_display_list();
				}
				break;
			case 'delete':
			case 'del':
				$model_class_name = static::$model_class_name;
				$deleted = $model_class_name::delete($id);
				if($deleted) {
					static::redirect_display_list();
				} else {
					pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
				}
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	public static function get_label_already_used() {
		global $msg, $categ, $sub;
		
		if(isset($msg[$categ."_".$sub."_label_already_used"])) {
			return $msg[$categ."_".$sub."_label_already_used"];
		} elseif(isset($msg[$sub."_label_already_used"])) {
			return $msg[$sub."_label_already_used"];
		} elseif(isset($msg[$categ."_label_already_used"])) {
			return $msg[$categ."_label_already_used"];
		} else {
			return $msg["docs_label_already_used"];
		}
	}
	
	public static function get_url_base() {
		if(empty(static::$url_base)) {
			global $current_module, $categ, $sub;
			static::$url_base = $current_module.".php?categ=".$categ."&sub=".$sub;
		}
		return parent::get_url_base();
	}
}