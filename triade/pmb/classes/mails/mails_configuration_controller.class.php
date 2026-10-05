<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_configuration_controller.class.php,v 1.12 2023/12/26 13:45:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/mails/mail_configuration.class.php");

class mails_configuration_controller extends lists_controller {
	
	protected static $model_class_name = 'mail_configuration';
	
	protected static $list_ui_class_name = 'list_mails_configuration_ui';
	
	public static function proceed($id=0) {
		global $current_module;
		global $action;
		global $name;
		global $dest;
		
		$id = intval($id);
		switch ($action) {
			case 'edit':
				if(!empty($name)) {
					$model_instance = static::get_model_instance($name);
					if(pmb_error::get_instance(static::$model_class_name)->has_error()) {
						pmb_error::get_instance(static::$model_class_name)->display(1, static::get_url_base());
					} else {
						print $model_instance->get_form();
					}
				} else {
					static::redirect_display_list();
				}
				break;
			case 'save':
				$model_instance = static::get_model_instance($name);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				if(!$model_instance->is_validated() && (
						($model_instance->get_type() == 'domain' && !$model_instance->get_authentification())
						|| ($model_instance->get_type() == 'domain' && $model_instance->get_authentification() && !$model_instance->is_allowed_authentification_override())
						|| ($model_instance->get_type() == 'address' && $model_instance->get_domain()->is_allowed_authentification_override())
				)) {
					print "<div class='erreur'>".$model_instance->get_information('smtpConnect_error')."</div>";
					if(!empty($name)) {
						$model_instance = static::get_model_instance($name);
						print $model_instance->get_form();
					} else {
						$list_ui_instance = static::get_list_ui_instance();
						print $list_ui_instance->get_display_list();
					}
				} else {
					static::redirect_display_list();
				}
				break;
			case 'delete':
				$model_instance = static::get_model_instance($name);
				if($model_instance->is_used()) {
				    $model_instance->initialization();
				} else {
				    $model_class_name = static::$model_class_name;
				    $model_class_name::delete($name);
				}
				static::redirect_display_list();
				break;
			case 'list_check_configuration':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::run_action_list('check_configuration');
				static::redirect_display_list();
				break;
			case 'list_initialization':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::run_action_list('initialization');
				static::redirect_display_list();
				break;
			default:
				if ($current_module == 'admin' && (SESSrights & ADMINISTRATION_AUTH)) {
					if(empty($action) && empty($dest)) {
						$list_mails_configuration_domains_ui = list_mails_configuration_domains_ui::get_instance();
						$list_mails_configuration_domains_ui->initialization();
					}
					parent::proceed($id);
				}
				break;
		}
	}
	
	public static function redirect_display_list() {
		global $current_module;
		global $name;
		
		if ($current_module == 'admin' && SESSrights & ADMINISTRATION_AUTH) {
			$location_url = static::get_url_base();
		} else {
			$location_url = static::get_url_base()."&action=edit&name=".$name;
		}
		if(headers_sent()) {
			print "
				<script type='text/javascript'>
					window.location.href='".$location_url."';
				</script>";
		} else {
			header('Location: '.$location_url);
		}
	}
}