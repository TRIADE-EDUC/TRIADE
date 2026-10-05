<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_modelling.class.php,v 1.30.8.1 2025/04/29 09:51:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/modules/module.class.php');
require_once($class_path.'/contribution_area/contribution_area_status.class.php');
require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($class_path.'/contribution_area/contribution_area_forms_controller.class.php');
require_once($class_path.'/contribution_area/contribution_area_form.class.php');
require_once($class_path.'/contribution_area/contribution_area_equation.class.php');
require_once($class_path.'/contribution_area/contribution_area_param.class.php');
require_once($class_path.'/contribution_area/contribution_area_scenario.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($include_path.'/templates/contribution_area/contribution_area_forms.tpl.php');
require_once($include_path.'/templates/modules/module_modelling.tpl.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_schemes_controler.class.php');
require_once($class_path.'/contribution_area/computed_fields/computed_field.class.php');
require_once($class_path.'/contribution_area/contribution_area_clipboard.class.php');


class module_modelling extends module{
	
	public function proceed_ontologies(){
		global $sub, $act, $ontology_id;
				
		switch($sub){
		 	case 'general':
		 		$ontologies = new ontologies();
		  		$ontologies->admin_proceed($act, $ontology_id);
		 		break;
		 	default :	
		 		$ontology = new ontology($ontology_id);
		 		$ontology->exec_onto_framework();
		 		break;
		}
	}
	
	public function proceed_frbr(){
		global $sub;
		
		switch($sub){
		 	case 'cataloging_schemes':
		 	default :
		 		$frbr_cataloging_schemes_controler = new frbr_cataloging_schemes_controler();
		 		print $frbr_cataloging_schemes_controler->proceed();
		 		break;
		}
	}
	
	public function proceed_contribution_area(){
		global $sub;
		global $msg;
		global $database_window_title;
		global $include_path, $lang;
		
		switch($sub) {
			case 'area':
				$this->proceed_contribution_area_area();
				break;
			case 'form':
				$this->proceed_contribution_area_form();
				break;
			case 'scenario':
		
				break;
			case 'status':
				$this->proceed_contribution_area_status();
				break;
			case 'equation':
				$this->proceed_contribution_area_equation();
				break;
			case 'param':
				$this->proceed_contribution_area_param();
				break;
			default:				
				echo window_title($database_window_title. $msg['admin_contribution_area'].$msg[1003].$msg[1001]);
				include($include_path."/messages/help/".$lang."/admin_contribution_area.txt");
				break;
		}
	}
	
	public function proceed_contribution_area_area(){
		global $action, $base_path, $msg;
		
		switch($action) {
			case 'edit':
				$contribution_area= new contribution_area($this->object_id);
				print $contribution_area->get_form();
				break;
			case 'save':
				print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
				$contribution_area= new contribution_area($this->object_id);
				$contribution_area->save_from_form();
				$contribution_area->save();
				print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=area";
				</script>';
				break;
			case 'delete':
				print '<div class="row"><div class="msg-perio">'.$msg['catalog_notices_suppression'].'</div></div>';
				$contribution_area= new contribution_area($this->object_id);
				$contribution_area->delete();
				print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=area";
				</script>';
				break;
			case "define" :
				$contribution_area= new contribution_area($this->object_id);
				print $contribution_area->get_definition_form();
				break;
			case "computed" :
				$contribution_area = new contribution_area($this->object_id);
				print $contribution_area->get_computed_form();
				break;
			case "up";
			    print '<div class="row"><div class="msg-perio">'.$msg['maj_encours'].'</div></div>';
				$contribution_area = new contribution_area($this->object_id);
				$contribution_area->up_order();
				print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=area";
				</script>';
			    break;
			case "down";
			    print '<div class="row"><div class="msg-perio">'.$msg['maj_encours'].'</div></div>';
				$contribution_area = new contribution_area($this->object_id);
				$contribution_area->down_order();
				print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=area";
				</script>';
			    break;
			case 'default':
			    $contribution_area = new contribution_area($this->object_id);
			    $contribution_area->set_area_default();
			    print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=area";
				</script>';
			    break;
			default:
				print contribution_area::get_list();
				break;
		}
	}
	
	public function proceed_contribution_area_form(){
		global $form_id;
		global $action;
		global $type;
		global $msg;
		global $area;
		global $base_path;
		
		$form_id = intval($form_id);
		switch($action) {
			case 'grid':
	            $form =  new contribution_area_form('', $form_id);
	            print $form->render();
	            break;
		    case 'save' :
	    		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
	       		$form = new contribution_area_form($type, $form_id);
	       		$form->set_from_form();
	       		$result = $form->save();
	       		print '
				<script type="text/javascript">
					document.location = "'.$base_path.'/modelling.php?categ=contribution_area&sub=form&action=grid&form_id='.$form->get_id().'";
				</script>';
	       		break;
		    case 'delete':
	    		print '<div class="row"><div class="msg-perio">'.$msg['catalog_notices_suppression'].'</div></div>';
	       		$form = new contribution_area_form($type, $form_id);
	       		$form->delete();
	       		print $form->get_redirection();
	       		break;
		    case 'edit':
	    		if(!isset($area)){
	    			$area = 0;
	    		}
	       		$form = new contribution_area_form($type, $form_id);
	       		print $form->get_form(intval($area));
	       		break;
		    case 'duplicate':
		    	if(!isset($area)){
		    		$area = 0;
		    	}
		    	$form = new contribution_area_form($type, $form_id);
		    	print $form->get_duplication_form(intval($area));
		    	break;
			default:
				print contribution_area_forms_controller::display_forms_list();
	            break;
		}
	}
	
	
	public function proceed_contribution_area_status(){
		global $msg;
		global $action;
		
		switch($action) {
			case 'update':
				$statut = contribution_area_status::get_from_from();
				if(!contribution_area_status::save($statut)){
					error_message("",$msg['save_error'], 0);
				}
				contribution_area_status::show_list();
				break;
			case 'add':
				contribution_area_status::show_form(0);
				break;
			case 'edit':
				contribution_area_status::show_form($this->object_id);
				break;
			case 'del':
				if(!contribution_area_status::delete($this->object_id)){
					$used=contribution_area_status::check_used($this->object_id);
					$list = "";
					foreach($used as $auth){
						$list.=$auth['link'].'<br/>';
					}
					error_message("", $msg['contribution_area_status_used'].'<br/>'.$list);
				}
				contribution_area_status::show_list();
				break;
			default:
				contribution_area_status::show_list();
				break;
		}
	}
	
	public function proceed_contribution_area_equation(){
		global $action;
		global $msg;
		
		$contribution_area_equation = new contribution_area_equation($this->object_id);
		switch($action) {
			case 'save':
				$equation = $contribution_area_equation->get_from_from();
				if(!$contribution_area_equation->save($equation)){
					error_message("",$msg['save_error'], 0);
				}
				contribution_area_equation::show_list();
				break;
			case 'add':
				$contribution_area_equation->add();
				break;
			case 'edit':
				print $contribution_area_equation->do_form();
				break;
			case 'delete':
				if(!contribution_area_equation::delete($this->object_id)){
					$used=contribution_area_equation::check_used($this->object_id);
					$list = "";
					foreach($used as $auth){
						$list.=$auth['link'].'<br/>';
					}
					error_message("", $msg['contribution_area_equation_used'].'<br/>'.$list);
				}
				contribution_area_equation::show_list();
				break;
			case 'build':
				$contribution_area_equation->add();
				break;
			case 'form':
				print $contribution_area_equation->do_form();
				break;
			default:
				contribution_area_equation::show_list();
				break;
		}
	}
	
	public function proceed_contribution_area_param(){
		global $action, $msg;
		
		$contribution_area= new contribution_area_param();
		switch ($action){
			case "save":
    			print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
				$contribution_area->save_from_form();
				print "<script type='text/javascript'>window.location.href='./modelling.php?categ=contribution_area&sub=param'</script>";
				break;
			case "quick_param" :
				global $contribution_area_quick_param_user_id;
				if (!isset($contribution_area_quick_param_user_id) || !$contribution_area_quick_param_user_id) {
					print $contribution_area->get_quick_param_form();
					break;
				}
				print '<div class="row"><div class="msg-perio">'.$msg['admin_contribution_area_quick_param_in_progress'].'</div></div>';
				$contribution_area->set_quick_param($contribution_area_quick_param_user_id);
				print "<script type='text/javascript'>window.location.href='./modelling.php?categ=contribution_area&sub=param'</script>";
				break;
			case "empty_store":
			    print '<div class="row"><div class="msg-perio">'.$msg['admin_purge_contribution_store'].'</div></div>';
			    $area_store = new contribution_area_store();
			    $area_store->empty_store();
				print "<script type='text/javascript'>window.location.href='./modelling.php?categ=contribution_area&sub=param'</script>";
			    break;
			case "clear_drafts":
			    print '<div class="row"><div class="msg-perio">'.$msg['admin_clear_contribution_store_drafts_in_progress'].'</div></div>';
			    $area_store = new contribution_area_store();
			    $area_store->clear_drafts();
				print "<script type='text/javascript'>window.location.href='./modelling.php?categ=contribution_area&sub=param'</script>";
			    break;
			default:
				print $contribution_area->get_form();
				break;
		}
	}
	
	public function proceed_ajax_contribution_area(){
		global $sub;
		global $area_id;
		global $data;
		global $current_scenario;
		global $type;
		global $form_id;
		global $action;

		switch($sub) {
			case 'area':
				switch($action){
					case "save_graph":
						$area = new contribution_area($area_id);
						$area->save_graph($data, $current_scenario);
						break;
					case "list":
					    print encoding_normalize::json_encode(contribution_area::get_list_ajax());
						break;
					case "duplicate_scenario":
					    $area = new contribution_area($area_id);
					    $area->duplicate_scenario_to_area();
						break;
					case 'clipboard':
					    print encoding_normalize::json_encode(contribution_area_clipboard::push_clipboard());
					    break;
					case 'get_clipboard':
					    global $id_clipboard;
					    print encoding_normalize::json_encode(contribution_area_clipboard::get_clipboard(intval($id_clipboard)));
					    break;
					case 'clipboard_valid':
					    global $id_clipboard;
					    print encoding_normalize::json_encode(contribution_area_clipboard::is_valid(intval($id_clipboard)));
					    break;
					case 'delete_clipboard':
					    global $id_clipboard;
					    print encoding_normalize::json_encode(contribution_area_clipboard::delete_clipboard(intval($id_clipboard)));
					    break;
				}
				break;
			case 'form_grid':
			    global $datas, $class_path;
			    require_once($class_path."/grid.class.php");
			    grid::proceed($datas);
			    break;
			case 'form':
			    $form_id = intval($form_id);
				switch($action){
					case 'save' :
						$form = new contribution_area_form($type, $form_id);
						$form->set_from_form();
						$result = $form->save(true);
						print encoding_normalize::json_encode($result);
						break;
					case 'delete':
						$form = new contribution_area_form($type, $form_id);
						print encoding_normalize::json_encode($form->delete(true));
						break;
					case 'duplicate':
					    if(!isset($area)){
					        $area = 0;
					    }
					    $form = new contribution_area_form($type, $form_id);
					    print $form->generate_duplication_form(true);
					    break;
					case 'duplicate_computed_field':
					    global $form_identifier, $new_form_identifier, $new_area_id;
					    computed_field::duplicate_all_computed_field($area_id, $form_identifier, $new_form_identifier, $new_area_id);
					    break;
					case 'check_draft':
					    global $uri;
					    $response = contribution_area_form::has_draft_contribution_from_uri($uri);
					    print encoding_normalize::json_encode($response);
					    break;
					default :
						if($type){
							$form = new contribution_area_form($type, $form_id);
							print $form->get_form();
						}else{
							print 'todo helper';
						}
						break;
				}
				break;
			case 'scenario' :
				switch ($action) {
					case 'get_rights_form' :
						$scenario_uri_id = 0;
						if (!empty($current_scenario)) {
							$uri = 'http://www.pmbservices.fr/ca/Scenario#'.$current_scenario;
							$scenario_uri_id = onto_common_uri::set_new_uri($uri);
						}
						print contribution_area_scenario::get_rights_form($scenario_uri_id);
						break;
					case 'delete' :
						$scenario_uri_id = 0;
						if (!empty($current_scenario)) {
							$uri = 'http://www.pmbservices.fr/ca/Scenario#'.$current_scenario;
							$scenario_uri_id = onto_common_uri::set_new_uri($uri);
							contribution_area_scenario::delete($scenario_uri_id);
						}
						break;
				}
				break;
			case 'equation' :
			    switch ($action){
			        case 'get_list':
			            print encoding_normalize::json_encode(contribution_area_equation::get_list_by_type($type));
			            break;
			    }
			    break;
		}
	}
	
	public function proceed_ajax_computed_fields() {
		global $sub, $computed_field_id, $field_num, $entity_type;
		switch($sub){
			case 'save':
				$computed_field = new computed_field($computed_field_id);
				$computed_field->set_from_form();
				$computed_field->save();
				$return = [
				    "id" => $computed_field->get_id()
				];
				print encoding_normalize::json_encode($return);
				break;
			case 'get_data':
				$computed_field = computed_field::get_computed_field_from_field_num($field_num);
				if (!$computed_field->get_field_num()) {
					$computed_field->set_field_num($field_num);
				}
				print encoding_normalize::json_encode($computed_field->get_data());
				break;
			case 'get_entity_properties':
				$return = array();
				$onto = contribution_area::get_ontology();
				$classes = $onto->get_classes();
				foreach($classes as $class){
					if($class->pmb_name == $entity_type){
						$properties_uri = $onto->get_class_properties($class->uri);
						foreach ($properties_uri as $property_uri) {
							$property = $onto->get_property($class->uri, $property_uri);
							$return[] = array(
									'name' => $property->label,
									'id' => $property->pmb_name,
									'entity' => $class->pmb_name
							);
						}
						if (is_array($class->sub_class_of)) {
							foreach($class->sub_class_of as $parent_uri) {
								$properties_uri = $onto->get_class_properties($parent_uri);
								foreach ($properties_uri as $property_uri) {
									$property = $onto->get_property($parent_uri, $property_uri);
									$return[] = array(
											'name' => $property->label,
											'id' => $property->pmb_name,
											'entity' => $class->pmb_name
									);
								}
								
							}
						}
						break;
					}
				}
				usort($return, array($this, 'sort_entities_properties'));
				print encoding_normalize::json_encode($return);
				break;
				
			case 'delete':
			    $computed_field = new computed_field($computed_field_id);
			    $computed_field->delete();
			    break;
			default:
				break;
		}
	}
	
	protected function sort_entities_properties($a, $b) {
		if ($a['name'] < $b['name']) {
			return -1;
		}
		if ($a['name'] > $b['name']) {
			return 1;
		}
		return 0;
	}
	
	// AR - 13/09/22 j'aime pas trop cette idée de faire ça comme ça, mais pour le moment, l'objet n'est pas de refaire le framework !
	public function get_display_subtabs() {
	    global $ontology_id;
	    global $sub;
	    if(!isset($ontology_id) || $sub == "general"){
	        return parent::get_display_subtabs();
	    }
	}
} // end of concept