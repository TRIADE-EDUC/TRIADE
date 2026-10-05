<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_questions_controller.class.php,v 1.4 2023/11/17 14:27:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/faq_question.class.php");
require_once ($class_path."/list/demandes/list_faq_questions_ui.class.php");

class faq_questions_controller extends lists_controller {
	
	protected static $model_class_name = 'faq_question';
	protected static $list_ui_class_name = 'list_faq_questions_ui';
	
	public static function proceed($id=0) {
		global $action, $msg;
		global $num_demande;
		
		switch($action){
			case "new":
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form($num_demande);
				break;
			case "edit" :
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case "save" :
				$model_instance = static::get_model_instance($id);
				$result = $model_instance->get_value_from_form();
				if($result){
					$result =$model_instance->save();
				}
				if(!$result){
					error_form_message($msg['faq_question_save_error']);
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case "delete" :
				$model_class_name = static::$model_class_name;
				$result = $model_class_name::delete($id);
				if(!$result){
					error_message("", $msg['faq_question_delete_error']);
					print "<div class='row'>&nbsp;</div>";
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case "list" :
			default :
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
		}
	}
}
