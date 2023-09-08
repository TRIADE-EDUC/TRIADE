<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_modelling.class.php,v 1.12 2019-01-17 08:15:10 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class concept
 * Un concept
 */

require_once($class_path.'/modules/module.class.php');
require_once($class_path.'/autoloader.class.php');
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


class module_modelling extends module{
	
	public function get_menu_ontologies() {
		global $msg;
		global $module_modelling_menu_ontologies;
	
		$menu = $module_modelling_menu_ontologies;
	
		$sub_tabs = $this->get_sub_tab('general', $msg["ontologies_general"]);
	
		$menu = str_replace('!!sub_tabs!!', $sub_tabs, $menu);
		return $menu;
	}
	
	public function proceed_ontologies(){
		global $sub, $msg, $act, $ontology_id;
		
		$autoloader = new autoloader();
		$autoloader->add_register("onto_class",true);
		
		$ontologies = new ontologies();
		
		$layout_template = $this->get_layout_template();
		$layout_template = str_replace("!!menu_contextuel!!", $this->get_menu_ontologies(), $layout_template);
		$layout_template = str_replace("!!ontologies_menu!!", $ontologies->get_modelling_menu(), $layout_template);
		switch($sub){
		 	case 'general':
		 		print str_replace("!!menu_sous_rub!!",$msg['ontologies_general'],$layout_template);
		  		$ontologies->admin_proceed($act, $ontology_id);
		 		break;
		 	default :	
		 		print str_replace("!!menu_sous_rub!!","",$layout_template);
		 		$ontology = new ontology($ontology_id);
		 		$ontology->exec_onto_framework();
		 		break;
		}
	}
	
	public function proceed_frbr(){
		global $sub, $msg;
		$layout_template = $this->get_layout_template();
		$layout_template = str_replace("!!menu_contextuel!!", $this->get_menu_frbr(), $layout_template);
		$layout_template = str_replace("!!ontologies_menu!!", "", $layout_template);
		switch($sub){
		 	case 'cataloging_schemes':
		 	default :
		 		print str_replace("!!menu_sous_rub!!", $msg['frbr_cataloging_schemes'], $layout_template);
		 		$frbr_cataloging_schemes_controler = new frbr_cataloging_schemes_controler();
		 		print $frbr_cataloging_schemes_controler->proceed();
		 		break;
		}
	}
	
	public function get_menu_frbr() {
		global $msg;
		global $module_modelling_menu_frbr;
	
		$menu = $module_modelling_menu_frbr;
	
		$sub_tabs = $this->get_sub_tab('cataloging_schemes', $msg["frbr_cataloging_schemes"]);
	
		$menu = str_replace('!!sub_tabs!!', $sub_tabs, $menu);
		return $menu;		
	}
	
	public function get_menu_contribution_area() {
		global $msg;
		global $module_modelling_menu_contribution_area;
		
		$menu = $module_modelling_menu_contribution_area;
		
		$sub_tabs = $this->get_sub_tab('area', $msg["admin_contribution_area"]);
		$sub_tabs .= $this->get_sub_tab('form', $msg["admin_contribution_area_form"]);
		$sub_tabs .= $this->get_sub_tab('status', $msg["admin_contribution_area_status"]);
		$sub_tabs .= $this->get_sub_tab('equation', $msg["admin_contribution_area_equation"]);
		$sub_tabs .= $this->get_sub_tab('param', $msg["admin_contribution_area_param"]);
		
		$menu = str_replace('!!sub_tabs!!', $sub_tabs, $menu);
		return $menu;
	}
	
	public function proceed_contribution_area(){
		global $sub;
		global $msg;
		global $database_window_title;
		global $include_path, $lang;
		
		$autoloader = new autoloader();
		$autoloader->add_register("onto_class",true);
		
		$layout_template = $this->get_layout_template();
		
		$layout_template = str_replace ( '!!menu_contextuel!!', $this->get_menu_contribution_area(), $layout_template);
		$message_key = 'admin_contribution_area';
		if($sub){
			if($sub != 'area'){
				$message_key.= "_".$sub;
			}
		}
		$layout_template = str_replace('!!menu_sous_rub!!', $msg[$message_key], $layout_template);
		print $layout_template;
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
		
		switch($action) {
			case 'grid':
	            $form_id+=0;
	            $form =  new contribution_area_form('', $form_id);
	            print $form->render();
	            break;
		    case 'save' :
	    		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
	       		$form_id+=0;
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
	       		$form_id+=0;
	       		$form = new contribution_area_form($type, $form_id);
	       		$form->delete();
	       		print $form->get_redirection();
	       		break;
		    case 'edit':
	    		if(!isset($area)){
	    			$area = 0;
	    		}
	       		$form_id+=0;
	       		$form = new contribution_area_form($type, $form_id);
	       		print $form->get_form($area*1);
	       		break;
		    case 'duplicate':
		    	if(!isset($area)){
		    		$area = 0;
		    	}
		    	$form_id+=0;
		    	$form = new contribution_area_form($type, $form_id);
		    	print $form->get_duplication_form($area*1);
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
		
		$autoloader = new autoloader();
		$autoloader->add_register("onto_class",true);
		
		switch($sub) {
			case 'area':
				switch($action){
					case "save_graph":
						$area = new contribution_area($area_id);
						$area->save_graph($data, $current_scenario);
						break;
				}
				break;
			case 'form':
				switch($action){
					case 'save' :
						$form_id+=0;
						$form = new contribution_area_form($type, $form_id);
						$form->set_from_form();
						$result = $form->save(true);
						print encoding_normalize::json_encode($result);
						break;
					case 'delete':
						$form_id+=0;
						$form = new contribution_area_form($type, $form_id);
						print encoding_normalize::json_encode($form->delete(true));
						break;
					default :
						if($type){
							$form_id+=0;
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
		}
	}
	
	public function get_left_menu() {
		global $module_modelling_left_menu;
		
		return $module_modelling_left_menu;
	}
	
	public function proceed_ajax_computed_fields() {
		global $sub, $computed_field_id, $field_num, $entity_type;
		switch($sub){
			case 'save':
				$computed_field = new computed_field($computed_field_id);
				$computed_field->set_from_form();
				$computed_field->save();
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
} // end of concept