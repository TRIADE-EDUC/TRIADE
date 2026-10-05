<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_controller.class.php,v 1.1 2021/08/06 14:14:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/demandes.class.php");

class demandes_controller extends lists_controller {
	
	protected static $model_class_name = 'demandes';
	protected static $list_ui_class_name = 'list_demandes_ui';
	
	public static function proceed($id=0) {
		global $act, $chk, $state;
		
		$model_instance = static::get_model_instance($id);
		switch ($act) {
			case 'new':
				$model_instance->show_modif_form();
				break;
			case 'save':
				$model_instance->set_properties_from_form();
				$model_instance->save();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'search':
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'suppr':
				if (!empty($id)) {
					demandes::delete($model_instance);
				} elseif(!empty($chk)) {
					$chk = explode(",", $chk);
					$nb_chk = count($chk);
					for ($i = 0; $i < $nb_chk; $i++) {
						$dmde = new demandes($chk[$i]);
						demandes::delete($dmde);
					}
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'suppr_noti':
				$requete = "SELECT num_notice FROM demandes WHERE id_demande IN (".implode(",", $chk).") AND num_notice!=0";
				$result = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result) > 0) {
					$model_instance->suppr_notice_form();
				} else {
					if (!empty($id)) {
						demandes::delete($model_instance);
					} elseif(!empty($chk)) {
						if (!is_array($chk)) {
							$chk = explode(",", $chk);
						}
						$nb_chk = count($chk);
						for ($i = 0; $i < $nb_chk; $i++) {
							$dmde = new demandes($chk[$i]);
							demandes::delete($dmde);
						}
					}
					$list_ui_instance = static::get_list_ui_instance();
					print $list_ui_instance->get_display_list();
				}
				break;
			case 'change_state':
				if (!empty($chk)) {
					$nb_chk = count($chk);
					for ($i = 0; $i < $nb_chk; $i++) {
						$dde = new demandes($chk[$i]);
						$dde->change_state($state);
					}
				} else {
					$model_instance->change_state($state);
					$model_instance->fetch_data($id);
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'affecter':
				$model_instance->attribuer();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
		}
	}
}
