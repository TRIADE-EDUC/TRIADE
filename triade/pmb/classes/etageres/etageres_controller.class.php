<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etageres_controller.class.php,v 1.4 2020/11/05 09:39:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/etagere.class.php");

class etageres_controller extends lists_controller {
	
	protected static $model_class_name = 'etagere';
	protected static $list_ui_class_name = 'list_etageres_ui';
	
	public static function proceed($id=0) {
		global $action;
		
		switch ($action) {
			case 'new_etagere':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'edit_etagere':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'duplicate_etagere':
				$model_instance = static::get_model_instance($id);
				$model_instance->idetagere = 0;
				print $model_instance->get_form();
				break;
			case 'del_etagere':
				$model_instance = static::get_model_instance($id);
				$model_instance->delete();
				print static::get_display_hmenu();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'save_etagere':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->save_etagere();
				print static::get_display_hmenu();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'valid_new_etagere':
				$model_instance = static::get_model_instance($id);
				$model_instance->create_etagere();
				$model_instance->set_properties_from_form();
				$model_instance->save_etagere();
				print static::get_display_hmenu();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				print static::get_display_hmenu();
				parent::proceed($id);
				break;
		}
	}
	
	public static function get_display_hmenu() {
		global $msg;
		return "
			<div class='hmenu'>
				<span><a href='catalog.php?categ=etagere&sub=classementGen'>".$msg["classementGen_list_libelle"]."</a></span>
			</div><hr>";
	}
}
