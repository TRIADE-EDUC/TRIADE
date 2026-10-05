<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannettes_abo_controller.class.php,v 1.7 2023/09/04 14:53:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/dsi/bannettes_controller.class.php");

class bannettes_abo_controller extends bannettes_controller {
	
	protected static $list_ui_class_name = 'list_bannettes_abo_ui';
	
	protected static $id_empr;
	
	public static function proceed($id=0) {
		global $suite;
		global $pmb_javascript_office_editor, $base_path;
		global $form_actif, $id_equation, $requete;
		
		switch($suite) {
			case 'acces':
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'transform_equ':
				// mettre à jour l'équation
				$equation = new equation($id_equation) ;
				$equation->num_classement=      0;
				$s = new search() ;
				$equation->nom_equation=        $s->make_serialized_human_query(stripslashes($requete));
				$equation->requete=				stripslashes($requete);
				$equation->update_type=			"C";
				$equation->save();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'modif':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form("abo");
				if ($pmb_javascript_office_editor) {
					print $pmb_javascript_office_editor ;
					print "<script type='text/javascript'>
                        pmb_include('$base_path/javascript/tinyMCE_interface.js');
                    </script>";
				}
				break;
			case 'delete':
				$model_instance = static::get_model_instance($id);
				$model_instance->delete() ;
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'update':
				$model_instance = static::get_model_instance($id);
				if($form_actif) {
					$model_instance->set_properties_from_form();
					$model_instance->save();
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			default:
				$list_readers_bannettes_ui = new list_readers_bannettes_ui();
				if(count($list_readers_bannettes_ui->get_objects()) == 1) {
					print $list_readers_bannettes_ui->get_display_search_form();
					$objects = $list_readers_bannettes_ui->get_objects();
					static::set_id_empr($objects[0]->id);
					$list_ui_instance = static::get_list_ui_instance();
					print $list_ui_instance->get_display_list();
				} else {
					static::set_list_ui_class_name('list_readers_bannettes_ui');
					static::set_model_class_name('emprunteur');
					parent::proceed($id);
				}
				break;
		}
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name(array('proprio_bannette' => static::$id_empr));
	}
	
	public static function set_id_empr($id_empr) {
		static::$id_empr = intval($id_empr);
	}
}// end class
