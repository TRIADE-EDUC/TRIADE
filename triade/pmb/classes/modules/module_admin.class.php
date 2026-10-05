<?php
use Pmb\MFA\Controller\MFAServicesController;
use Pmb\MFA\Controller\MFAMailController;
use Pmb\MFA\Controller\MFASmsController;
use Pmb\MFA\Controller\MFAOtpController;

// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_admin.class.php,v 1.28.2.6 2024/12/18 08:27:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/modules/module.class.php");
require_once($class_path."/parameters/parameter.class.php");
require_once($include_path."/templates/modules/module_admin.tpl.php");

class module_admin extends module{
	
	public function proceed_misc() {
		global $sub;
		global $module_admin_misc_files_content;
		global $include_path, $lang;
		
		switch($sub) {
			case 'tables':
				$this->load_class("/misc/misc_tables_controller.class.php");
				misc_tables_controller::proceed($this->object_id);
				break;
			case 'tables_data':
				$this->load_class("/misc/misc_tables_data_controller.class.php");
				misc_tables_data_controller::proceed($this->object_id);
				break;
			case 'mysql':
				$this->load_class("/misc/misc_mysql_controller.class.php");
				misc_mysql_controller::proceed_info();
				misc_mysql_controller::proceed($this->object_id);
				break;
			case 'files':
				$this->load_class("/misc/files/misc_files.class.php");
				print $module_admin_misc_files_content;
				break;
			default:
				include("$include_path/messages/help/$lang/admin_misc.txt");
				break;
		}
	}
	
	public function proceed_opac_facets() {
		global $sub, $type;
		
		switch($sub){
			case "facettes":
				facettes_opac_controller::set_type('notices');
				facettes_opac_controller::proceed($this->object_id);
				break;
			case "facettes_authorities":
				facettes_opac_controller::set_type($type);
				facettes_opac_controller::proceed($this->object_id);
				break;
			case "facettes_external":
				facettes_opac_controller::set_type('notices_externes');
				facettes_opac_controller::set_is_external(1);
				facettes_opac_controller::proceed($this->object_id);
				break;
			case "facettes_comparateur":
			    facettes_compare_controller::proceed($this->object_id);
				break;
		}
	}
	
	public function proceed_mails_waiting() {
		$this->load_class("/mails_waiting.class.php");
		
		mails_waiting::proceed();
	}
	
	public function proceed_search_universes() {	    
		global $sub, $msg, $database_window_title, $include_path;
		global $lang;
		
		$this->load_class("/search_universes/search_universes_controller.class.php");
	  
        switch($sub) {
        	case 'universe':
        		$search_universes_controller = new search_universes_controller($this->object_id);
        	    $search_universes_controller->proceed_universe();
        		break;
        	case 'segment':
        		$search_universes_controller = new search_universes_controller($this->object_id);
        		$search_universes_controller->proceed_segment();
        		break;
        	default:
        		echo window_title($database_window_title. $msg['admin_menu_search_universes'].$msg[1003].$msg[1001]);
         		include($include_path."/messages/help/".$lang."/admin_search_universes.txt");
        		break;
        }    
	}
	
	public function proceed(){
		global $categ;
		global $module_layout_end;
	
		if($categ && method_exists($this, "proceed_".$categ)) {
			$method_name = "proceed_".$categ;
			$this->{$method_name}();
		} else {
			$layout_template = $this->get_layout_template();
			$layout_template = str_replace("!!menu_contextuel!!", "", $layout_template);
			print str_replace("!!menu_sous_rub!!","",$layout_template);
		}
		print $module_layout_end;
	}
	
	public function proceed_ajax_misc() {
		global $class_path;
		global $sub;
		global $action;
		global $path, $filename;
		global $object_type;
		
		$this->load_class("/misc/files/misc_files.class.php");
		switch($sub){
			case 'tables':
				switch($action){
					case 'list':
						$this->load_class("/misc/misc_tables_controller.class.php");
						misc_tables_controller::proceed_ajax($object_type, 'misc');
						break;
				}
				break;
			case 'tables_data':
				switch($action){
					case 'list':
						$this->load_class("/misc/misc_tables_data_controller.class.php");
						list_misc_tables_data_ui::set_table(substr($object_type,strpos($object_type, '_ui_')+4));
						misc_tables_data_controller::proceed_ajax(substr($object_type,0,strpos($object_type, '_ui_')+3), 'misc');
						break;
				}
				break;
			case 'files':
				switch($action){
					case 'get_datas':
						$misc_files = new misc_files();
						print encoding_normalize::json_encode(encoding_normalize::utf8_normalize($misc_files->get_tree_data()));
						break;
				}
				break;
			case 'file':
				switch($action){
					case 'get_form':
						header('Content-type: text/html;charset=utf-8');
						$misc_file = misc_files::get_model_instance($path, $filename);
						print encoding_normalize::utf8_normalize($misc_file->get_form());
						break;
					case 'get_contents':
						//On ne permet pas de voir le contenu des fichiers autres que xml
						if(substr(strtolower($filename),-4,4) != ".xml") {
							header('Content-type: application/json;charset=utf-8');
							print encoding_normalize::json_encode(array('contents' => "no access", 'is_writable_dir' => 0));
						} else {
							$misc_file = misc_files::get_model_instance($path, $filename);
							$is_writable_dir = 0;
							if(is_writable($path)) {
								$is_writable_dir = 1;
							}
							header('Content-type: application/json;charset=utf-8');
							print encoding_normalize::json_encode(array('contents' => $misc_file->get_contents(), 'is_writable_dir' => $is_writable_dir));
						}
						break;
					case 'save_contents':
						$misc_file = misc_files::get_model_instance($path, $filename);
						$saved = $misc_file->save_contents();
						print encoding_normalize::json_encode(array('status' => $saved, 'elementId' => $misc_file->get_full_path()));
						break;
					case 'save':
						$misc_file = misc_files::get_model_instance($path, $filename);
						$misc_file->set_properties_from_form();
						$saved = $misc_file->save();
						print encoding_normalize::json_encode(array('status' => $saved, 'elementId' => $misc_file->get_full_path()));
						break;
					case 'initialization':
						$misc_file = misc_files::get_model_instance($path, $filename);
						$misc_file->set_data();
						$saved = $misc_file->save();
						print encoding_normalize::json_encode(array('status' => $saved, 'elementId' => $misc_file->get_full_path()));
						break;
					case 'delete':
						$misc_file = misc_files::get_model_instance($path, $filename);
						$deleted = $misc_file->delete();
						print encoding_normalize::json_encode(array('status' => $deleted, 'elementId' => $misc_file->get_full_path()));
						break;
					case 'add_substitute':
						break;
					case 'delete_substitute':
						break;
				}
				break;
			
		}
	}
	
	public function proceed_ajax_search_universes(){
		global $class_path;
		global $sub;
		
		$this->load_class("/search_universes/search_universes_controller.class.php");
		
		switch($sub) {
			case 'universe':
			case 'segment':
				$search_universes_controller = new search_universes_controller();
				$search_universes_controller->proceed_ajax();
				break;
			default:
				print encoding_normalize::json_encode(array());
				break;
		}
	}
	
	public function proceed_mails() {
		global $sub;
		
		switch($sub) {
			case 'configuration':
				$this->load_class("/mails/mails_configuration_controller.class.php");
				mails_configuration_controller::proceed($this->object_id);
				break;
			case 'settings':
				$this->load_class("/mails/mails_settings_controller.class.php");
				mails_settings_controller::proceed($this->object_id);
				break;
		}
	}
	
	public function proceed_interface() {
		global $sub, $action;
		global $name;
		
		switch($sub) {
			case 'lists':
				$this->load_class("/list/lists_ui_controller.class.php");
				lists_ui_controller::proceed($this->object_id);
				break;
			case 'modules':
				$this->load_class("/modules/module_model.class.php");
				switch($action){
					case 'edit':
						if(isset($name) && $name) {
							$model_instance = new module_model($name);
							print $model_instance->get_form();
						}
						break;
					case 'save':
						$model_instance = new module_model($name);
						$model_instance->set_properties_from_form();
						$model_instance->save();
						
						$list_modules_ui = new list_modules_ui();
						print $list_modules_ui->get_display_list();
						break;
					case 'delete':
						module_model::delete($name);
						
						$list_modules_ui = new list_modules_ui();
						print $list_modules_ui->get_display_list();
						break;
					default :
						$list_modules_ui = new list_modules_ui();
						print $list_modules_ui->get_display_list();
						break;
				}
				break;
			case 'tabs':
				$this->load_class("/tabs/tab_controller.class.php");
				tabs_controller::proceed($this->object_id);
				break;
			case 'selectors':
				$this->load_class("/selectors/selectors_controller.class.php");
				selectors_controller::proceed($this->object_id);
				break;
			case 'forms':
				$this->load_class("/forms/forms_controller.class.php");
				forms_controller::proceed($this->object_id);
				break;
			case 'translations':
			    lists_controller::set_model_class_name('translation');
			    lists_controller::set_list_ui_class_name('list_translations_ui');
			    lists_controller::set_list_import_ui_class_name('list_import_translations_ui');
			    lists_controller::proceed();
			    break;
		}
	}
	
	public function proceed_supervision() {
	    global $sub, $base_noheader;
		global $supervision_mails_active;
		global $supervision_logs_active;
		global $pmb_clean_mode;
		global $name, $field;
		
		switch($sub) {
			case 'mails':
			    if (empty($base_noheader)) {
    				print "
    				<div class='row'>
    					".parameter::get_input_activation('supervision', 'mails_active', $supervision_mails_active)."
    				</div>
    				";
			    }
				$this->load_class("/mails/mails_controller.class.php");
				mails_controller::proceed($this->object_id);
				break;
			case 'mails_waiting':
				$this->load_class("/mails/mails_waiting_controller.class.php");
				mails_waiting_controller::proceed($this->object_id);
				break;
			case 'logs':
			    if (empty($base_noheader)) {
    				print "
    				<div class='row'>
    					".parameter::get_input_activation('supervision', 'logs_active', $supervision_logs_active)."
    				</div>
    				";
			    }
				$this->load_class("/logs/logs_controller.class.php");
				logs_controller::proceed($this->object_id);
				break;
			case 'indexation':
			    //Visibilite sur les elements encore dans la pile
			    $this->load_class("/indexation/indexation_stack_controller.class.php");
			    indexation_stack_controller::proceed($this->object_id);
			    
			    if (empty($base_noheader)) {
			        print "
    				<div class='row'>
                        <div class='row'>
                            <b>".parameter::get_comment_param('pmb', 'clean_mode')."</b>
                        </div>
    					".parameter::get_input_activation('pmb', 'clean_mode', $pmb_clean_mode)."
    				</div>
    				";
			    }
			    if (empty($name)) {
			        indexation_controller::set_list_ui_class_name('list_entities_indexation_ui');
			    } else {
			        if (empty($field)) {
			            indexation_controller::set_list_ui_class_name('list_indexation_fields_ui');
			        } else {
			            indexation_controller::set_list_ui_class_name('list_indexation_entities_ui');
			        }
			    }
				$this->load_class("/indexation/indexation_controller.class.php");
				indexation_controller::proceed($this->object_id);
				break;
		}
	}
	
	public function proceed_mfa() {
	    global $sub;
	    
	    switch($sub) {
	        case 'services':
	        default:
	            $controller = new MFAServicesController();
	            $controller->proceed();
	            break;
	        case 'mail':
	            $controller = new MFAMailController();
	            $controller->proceed();
	            break;
	        case 'sms':
	            $controller = new MFASmsController();
	            $controller->proceed();
	            break;
	        case 'otp':
	            $controller = new MFAOtpController();
	            $controller->proceed();
	            break;
	    }
	}
	
	public function proceed_audit() {
		print list_audit_ui::get_instance()->get_display_list();
	}
	
	public function proceed_gestion() {
	    global $sub, $type, $num_facettes_set;
	    
	    $num_facettes_set = intval($num_facettes_set);
	    if(empty($num_facettes_set)) {
	        switch($sub){
	            case "facettes":
	                configuration_facettes_sets_controller::set_list_ui_class_name('list_configuration_gestion_facettes_sets_ui');
	                configuration_facettes_sets_controller::set_type('notices');
	                configuration_facettes_sets_controller::proceed($this->object_id);
	                break;
	            case "facettes_authorities":
	                configuration_facettes_sets_controller::set_list_ui_class_name('list_configuration_gestion_facettes_authorities_sets_ui');
	                configuration_facettes_sets_controller::set_type($type);
	                configuration_facettes_sets_controller::proceed($this->object_id);
	                break;
	            case "facettes_external":
	                configuration_facettes_sets_controller::set_list_ui_class_name('list_configuration_gestion_facettes_external_sets_ui');
	                configuration_facettes_sets_controller::set_is_external(1);
	                configuration_facettes_sets_controller::proceed($this->object_id);
	                break;
	        }
	    } else {
	        switch($sub){
	            case "facettes":
	                facettes_gestion_controller::set_num_facettes_set($num_facettes_set);
	                facettes_gestion_controller::set_type('notices');
	                facettes_gestion_controller::proceed($this->object_id);
	                break;
	            case "facettes_authorities":
	                facettes_gestion_controller::set_num_facettes_set($num_facettes_set);
	                facettes_gestion_controller::set_type($type);
	                facettes_gestion_controller::proceed($this->object_id);
	                break;
	            case "facettes_external":
	                facettes_gestion_controller::set_type('notices_externes');
	                facettes_gestion_controller::set_is_external(1);
	                facettes_gestion_controller::proceed($this->object_id);
	                break;
	        }
	    }
	}
}