<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameters_controller.class.php,v 1.4 2021/10/26 09:44:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path.'/translation.class.php');

class parameters_controller extends lists_controller {
	
	protected static $model_class_name = '';
	protected static $list_ui_class_name = 'list_parameters_ui';
	
	public static function proceed($id=0) {
		global $action;
		global $form_valeur_param, $comment_param, $form_id_param;
		
		switch($action) {
			case 'modif':
				include("./admin/param/param_modif.inc.php");
				break;
			case 'update':
				$requete = "update parametres set ";
				$requete .= "valeur_param='$form_valeur_param', ";
				$requete .= "comment_param='$comment_param' ";
				$requete .= "where id_param='$form_id_param' ";
				pmb_mysql_query($requete);
				
				$translation = new translation($form_id_param, "parametres");
				$translation->update("valeur_param", "form_valeur_param", "text");
				
// 				show_param();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'add':
				param_form();
				break;
			default:
// 				show_param();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
		}
	}
}
