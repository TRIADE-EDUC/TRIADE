<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_ontologies.class.php,v 1.2.2.1.2.1 2025/03/04 16:43:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector.class.php");
require($base_path.'/selectors/templates/sel_ontology.tpl.php');
require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path.'/searcher_tabs.class.php');
require_once($class_path.'/concept.class.php');
require_once($class_path.'/elements_list/elements_authorities_selectors_list_ui.class.php');
require_once($class_path.'/skos/skos_datastore.class.php');
require_once($class_path.'/skos/skos_onto.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');
require_once($class_path.'/vedette/vedette_schemes.class.php');

class selector_ontologies extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'onto';
	}

	public function proceed() {
		global $msg;
		global $class_path;
		global $action;
		global $element_name;
		global $range;
		global $item_uri;
		global $bt_ajouter;
		global $nb_per_page_gestion;
		global $pmb_base_url;
		global $mode;
		global $element_id;
		global $att_id_filter;
		global $deb_rech;
		global $dyn;
		global $page;

        $entity_form = '';
		global $base_url;
		$base_url = $this->get_base_url();
		global $ontology_id,$source,$caller,$objs,$element,$order,$infield,$callback,$dyn,$deb_rech,$param1,$param2,$module_from;

		$params_array = array(
				'base_url' => $base_url,
				'categ'=>'ontologies',
				'sub'=> 'concept',
				'objs'=>$objs,
				'action' => $action,
				'nb_per_page'=> $nb_per_page_gestion,
				'id'=>'',
				'parent_id'=>'',
				'param1'=> $param1,
				'param2'=> $param2,
				'range'=>$range,
				'page' => '1',
				'user_input'=>'',
				'item_uri' => $item_uri,
				'base_resource'=> "autorites.php",
				'element' => $element,
				'caller' => $caller,
				'deb_rech' => $deb_rech,
				/* Pour le replace */
				'by' => '',
				'dyn' => $dyn,
				'selector_context' => 1,
				'type' => '',
				'callback' => '',
				'mode' => $mode
		);

		if(!isset($element)){
			if(empty($action)){
				$action = "list_selector";
			}
			$ontology = new ontology($ontology_id);
			$params = new onto_param(array(
			    'categ'=>'ontologies',
			    'sub'=> '',
			    'objs'=>$objs,
			    'action'=>'list_selector',
			    'page'=>'1',
			    'nb_per_page'=>'20',
			    'caller'=>$caller,
			    'element'=>$element,
			    'order'=>$order,
			    'callback'=>$callback,
			    'base_url'=>$base_url,
			    'deb_rech'=>$deb_rech,
			    'parent_id'=>'',
			    'param1' => $param1,
			    'param2' => $param2,
			    'item_uri' => $item_uri,
			    'range' => $range,
			    'ontology_id' => $ontology_id,
			    'base_resource'=> "modelling.php"
			));
			$ontology->exec_data_selector_framework($params);
		}else {
			switch($action){
				case 'simple_search':
                    $entity_form = $this->get_simple_search_form();
					break;
				case 'advanced_search':
                    $entity_form = $this->get_advanced_search_form();
					break;
				case 'add':
				case 'list':
				case 'last':
				case 'search':
				    $ontology = new ontology($ontology_id);
				    $params = new onto_param(array(
				        'categ'=>'ontologies',
				        'sub'=> '',
				        'objs'=>$objs,
				        'action'=>'list_selector',
				        'page'=>'1',
				        'nb_per_page'=>'20',
				        'caller'=>$caller,
				        'element'=>$element,
				        'order'=>$order,
				        'callback'=>$callback,
				        'base_url'=>$base_url,
				        'deb_rech'=>$deb_rech,
				        'parent_id'=>'',
				        'param1' => $param1,
				        'param2' => $param2,
				        'item_uri' => $item_uri,
				        'range' => $range,
				        'ontology_id' => $ontology_id,
				        'base_resource'=> "modelling.php"
				    ));
				    ob_start();
				    $ontology->exec_data_selector_framework($params);
					$display_contents = ob_get_contents();
					ob_end_clean();
					print encoding_normalize::utf8_normalize($display_contents);
					break;
				case 'results_search':
					ob_start();
					print $this->results_search();
                    $entity_form = ob_get_contents();
					ob_end_clean();
					break;
				case 'element_display':
					global $id_authority, $caller, $element;
					$id_authority = intval($id_authority);
					if($id_authority) {
						$elements_authorities_selectors_list_ui = new elements_authorities_selectors_list_ui(array($id_authority), 1, 1);
						$elements = $elements_authorities_selectors_list_ui->get_elements_list();
						search_authorities::get_caddie_link();
                        $entity_form = $elements;
					}
					break;
				case 'update':
				    $ontology = new ontology($ontology_id);
				    $params = new onto_param(array(
				        'categ'=>'ontologies',
				        'sub'=> '',
				        'objs'=>$objs,
				        'action'=>'list_selector',
				        'page'=>'1',
				        'nb_per_page'=>'20',
				        'caller'=>$caller,
				        'element'=>$element,
				        'order'=>$order,
				        'callback'=>$callback,
				        'base_url'=>$base_url,
				        'deb_rech'=>$deb_rech,
				        'parent_id'=>'',
				        'param1' => $param1,
				        'param2' => $param2,
				        'item_uri' => $item_uri,
				        'range' => $range,
				        'ontology_id' => $ontology_id,
				        'base_resource'=> "modelling.php"
				    ));
				    ob_start();
				    $ontology->exec_data_selector_framework($params);
					break;
				default:
					print $this->get_js_script();
					print $this->get_sub_tabs();
					break;
			}
            if ($entity_form) {
                header("Content-Type: text/html; charset=UTF-8");
                print encoding_normalize::utf8_normalize($entity_form);
            }
			if($action=='selector_save'){
				print '<script>document.forms["search_form"].submit();</script>';

			}
		}
	}

	public static function get_params_url() {
		global $objs, $element, $unique_scheme, $return_concept_id, $concept_scheme;
		global $order, $grammar, $perso_id, $custom_prefixe, $perso_name;
		global $att_id_filter;

        $params_url = parent::get_params_url();
		$params_url .= ($objs ? "&objs=".$objs : "");
		$params_url .= ($element ? "&element=".$element : "");
		$params_url .= ($unique_scheme ? "&unique_scheme=".$unique_scheme : "");
		$params_url .= ($return_concept_id ? "&return_concept_id=".$return_concept_id : "");
		$params_url .= ($concept_scheme ? "&concept_scheme=".implode(",",$concept_scheme) : "");
		$params_url .= ($order ? "&order=".$order : "");
		$params_url .= ($grammar ? "&grammar=".$grammar : "");
		$params_url .= ($perso_id ? "&perso_id=".$perso_id : "");
		$params_url .= ($custom_prefixe ? "&custom_prefixe=".$custom_prefixe : "");
		$params_url .= ($perso_name ? "&perso_name=".$perso_name : "");
		$params_url .= ($att_id_filter ? "&att_id_filter=".$att_id_filter : "");
		return $params_url;
	}

	protected function get_change_link($display_mode) {
		$link = static::get_base_url();
		if($display_mode == 2) {
			$link .= "&action=edit";
		} else {
			$link .= "&action=selector_add";
		}
		return $link;
	}

	protected function get_html_button($location='', $label='') {
		global $charset;

		return "<input type='button' class='bouton_small' onclick=\"document.location='".$location."'\" value='".htmlentities($label, ENT_QUOTES, $charset)."' />";
	}

	protected function get_search_fields_filtered_objects_types() {
		return array($this->get_objects_type(), "authorities");
	}

	protected function get_search_instance() {
		return new search_ontology(true, 'search_fields_ontologies');
	}

	protected function get_current_mode(){
		global $mode;
		if(!$mode){
		    global $concept_scheme;
			$searcher_tab = $this->get_searcher_tabs_instance();
			$mode = $searcher_tab->get_default_selector_concept_mode($concept_scheme);
		}
		return $mode;
	}

	protected function get_selector_url(){
		global $base_path;
		global $entity_type;
		global $mode;
		global $caller;
		global $no_display, $bt_ajouter, $dyn, $callback, $infield;
		global $max_field, $field_id, $field_name_id, $add_field;
		global $entity_id,$ontology_id;

		$selector_url = $base_path."/select.php?what=".$this->get_what_from_type($entity_type)."&caller=".$caller."&mode=".$this->get_current_mode();
		$selector_url .= static::get_params_url();
		if($no_display) 	$selector_url .= "&no_display=".$no_display;
		if($bt_ajouter) 	$selector_url .= "&bt_ajouter=".$bt_ajouter;
		if($dyn) 			$selector_url .= "&dyn=".$dyn;
		if($callback) 		$selector_url .= "&callback=".$callback;
		if($infield) 		$selector_url .= "&infield=".$infield;
		if($max_field) 		$selector_url .= "&max_field=".$max_field;
		if($field_id) 		$selector_url .= "&field_id=".$field_id;
		if($field_name_id) 	$selector_url .= "&field_name_id=".$field_name_id;
		if($add_field) 		$selector_url .= "&add_field=".$add_field;
		if($entity_id)   $selector_url .= "&entity_id=".$entity_id;
		if($ontology_id)   $selector_url .= "&ontology_id=".$ontology_id;

		foreach($_GET as $name => $value){
			if(strpos($selector_url, $name) === false){
				$selector_url .= "&".$name."=".$value;
			}
		}
		return $selector_url;
	}
}