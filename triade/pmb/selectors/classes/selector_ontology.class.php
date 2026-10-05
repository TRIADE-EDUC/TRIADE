<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_ontology.class.php,v 1.42.2.1 2025/01/16 10:24:11 qvarin Exp $

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

class selector_ontology extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'concepts';
	}

	public function proceed() {
		global $msg;
		global $class_path;
		global $action;
		global $objs, $caller, $element, $element_name, $order, $callback;
		global $range;
		global $param1, $param2;
		global $item_uri;
		global $deflt_concept_scheme;
		global $bt_ajouter;
		global $nb_per_page_gestion;
		global $authority_statut;
		global $pmb_base_url;
		global $mode;
		global $element_id;
		global $att_id_filter;
		global $concept_scheme;
		global $deb_rech;
		global $dyn;
		global $page;

        $entity_form = '';
		if(!is_array($concept_scheme)){
		    $concept_scheme = explode(",",$concept_scheme);
		}
		global $base_url;
		$base_url = $this->get_base_url();

		$params_array = array(
				'base_url' => $base_url,
				'categ'=>'concepts',
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
				'concept_scheme'=>((isset($concept_scheme) && $concept_scheme != '') ? $concept_scheme : ((isset($_SESSION['onto_skos_concept_last_concept_scheme']) && ($_SESSION['onto_skos_concept_last_concept_scheme'] !== "")) ? $_SESSION['onto_skos_concept_last_concept_scheme'] : $deflt_concept_scheme)),
				'only_top_concepts' => ((empty($skos_concept_search_form_submitted) && isset($_SESSION['onto_skos_concept_only_top_concepts'])) ? $_SESSION['onto_skos_concept_only_top_concepts'] : 0),
				'base_resource'=> "autorites.php",
				'element' => $element,
				'caller' => $caller,
				'deb_rech' => $deb_rech,
				/* Pour le replace */
				'by' => '',
				'dyn' => $dyn,
				'link_save' => '',
				'authority_statut' => $authority_statut,
				'selector_context' => 1,
				'type' => '',
				'callback' => '',
				'return_concept_id' => false,
				'mode' => $mode
		);

		if(!isset($element) || $element != 'concept'){
			if(empty($action)){
				$action = "list_selector";
			}
			$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", skos_onto::get_store(), "", skos_datastore::get_store(), "", array(), 'http://www.w3.org/2004/02/skos/core#prefLabel', new onto_param($params_array));
			$onto_ui->proceed();
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
					$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", skos_onto::get_store(), "", skos_datastore::get_store(), "", array(), 'http://www.w3.org/2004/02/skos/core#prefLabel', new onto_param($params_array));
					ob_start();
					$onto_ui->proceed();
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
					$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", skos_onto::get_store(), "", skos_datastore::get_store(), "", array(),'http://www.w3.org/2004/02/skos/core#prefLabel', new onto_param($params_array));
					$id = $onto_ui->proceed();

					$auth_instance = new authority(0, $id, AUT_TABLE_CONCEPT);
					$concept_instance = $auth_instance->get_object_instance();
                    $entity_form =
					'<textarea>'.encoding_normalize::json_encode(array(
							'id' => $id,
							'id_authority' => $auth_instance->get_id(),
							'isbd' => $concept_instance->get_isbd(),
							'uri' => $concept_instance->get_uri(),
							'type' => 'authorities'
					)).'</textarea>';
					break;
				case 'authority_searcher':
                  //  $entity_form = "<div id='att'></div>";
					//onglets de recherche autorites
					$searcher_tabs = new searcher_tabs('authorities');
					ob_start();
					$searcher_tabs->proceed($this->get_current_mode(), '','selector');
                    $entity_form.= ob_get_contents();
					ob_end_clean();
					$entity_form.= $this->get_search_tabs();
					break;
				case 'get_composed_concept':
					if(isset($element_id) && $element_id){ //Identifiant d'autorité passé à l'aiguilleur
						$concept_id = $this->get_composed_concept($element_id);
						$concept_to_print = $this->compute_concept_list($concept_id);

						$elements_authorities_selectors_list_ui = new elements_authorities_selectors_list_ui($concept_to_print, count($concept_id), 1);
						//Pour l'instant les différents éléments composant l'url sont passés en dur
						//A voir pour essayer de les passer de façon automatique (modification de la méthode get_base_url)

						$elements_authorities_selectors_list_ui->set_current_url(static::get_base_url()."&action=get_composed_concept&module=selectors&element_id=".$element_id);
						$elements = $elements_authorities_selectors_list_ui->get_elements_list();
						search_authorities::get_caddie_link();
						print $elements;
						print $elements_authorities_selectors_list_ui->get_elements_list_nav();

					}
					break;
				case "navigate" :
				    $navigator = new selector_concept_navigator();
				    print $navigator->proceed();
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

	protected function get_searcher_tabs_instance() {
		if(!isset($this->searcher_tabs_instance)) {
			$this->searcher_tabs_instance = new searcher_selectors_tabs('authorities');
		}
		return $this->searcher_tabs_instance;
	}

 	protected function get_search_perso_instance($id=0) {
 		return new search_perso($id, 'AUTHORITIES');
 	}

	protected function get_search_instance() {
		return new search_authorities(true, 'search_fields_authorities');
	}

	protected function get_sub_tabs(){
		$current_url = static::get_base_url();
		$current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);
		$searcher_tab = $this->get_searcher_tabs_instance();
		global $concept_scheme;
		$schemes = '';
		if(isset($concept_scheme)){
		    if(!is_array($concept_scheme) && $concept_scheme != -1){
		        $schemes = 'conceptSchemes:['.$concept_scheme.'],';
		    }else {
		        for($i=0 ; $i<count($concept_scheme) ; $i++){
		            if($concept_scheme[$i] != "-1" && $concept_scheme[$i] != "-1"){
		                if($schemes!= 0)$schemes.= ',';
		                $schemes.= $concept_scheme[$i];
		            }
		        }
		        $schemes = 'conceptSchemes:['.$schemes.'],';
		    }
		}
		$url_concept = static::get_base_url();
		$url_concept = str_replace('select.php?', 'ajax.php?module=selectors&', $url_concept);
		return "
				<div id='widget-container'></div>
				<script type='text/javascript'>
							require(['apps/pmb/form/form_concept/FormConceptContainer', 'dojo/dom'], function(FormConceptContainer, dom){
								new FormConceptContainer({doLayout: false, ".$schemes." currentURL: '".$url_concept."', selectorURL:'".$current_url."', multicriteriaMode: '".$searcher_tab->get_mode_multi_search_criteria()."', parametersTabs: '".encoding_normalize::json_encode($this->get_parameters_tabs())."'}, 'widget-container');
							});
					   </script>
				";
	}

	protected function get_search_tabs(){
		global $mode;
		$current_url = static::get_selector_url();
		$current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);
		$searcher_tab = $this->get_searcher_tabs_instance();
		global $concept_scheme;
		$schemes = '';
		if(isset($concept_scheme)){
		    if(!is_array($concept_scheme) && $concept_scheme != "-1"){
		        $schemes = 'conceptSchemes:['.$concept_scheme.'],';
		    }else {
		        for($i=0 ; $i<count($concept_scheme) ; $i++){
		            if($concept_scheme[$i] != "-1" && $concept_scheme[$i] != "-1"){
		                if($schemes!= 0)$schemes.= ',';
    		            $schemes.= $concept_scheme[$i];
		            }
		        }
		        $schemes = 'conceptSchemes:['.$schemes.'],';
		    }
		}
		$url_concept = static::get_base_url();
		$url_concept = str_replace('select.php?', 'ajax.php?module=selectors&', $url_concept);

		$script = "
				<div id='sub-container'></div>
				<script type='text/javascript'>
							require(['apps/pmb/form/form_concept/FormConceptSelector', 'dojo/dom', 'dojo/ready'], function(FormConceptSelector, dom, ready){
								ready(function(){
									new FormConceptSelector({doLayout: false, ".$schemes." selectorURL:'".$current_url."', currentURL: '".$url_concept."', multicriteriaMode: '".$searcher_tab->get_mode_multi_search_criteria()."', parametersTabs: '".encoding_normalize::json_encode($this->get_parameters_tabs())."'}, 'sub-container');
								});
							});
					   </script>
				";

		return $script ;
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
		global $authperso_id;

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
		if($authperso_id)   $selector_url .= "&authperso_id=".$authperso_id;

		foreach($_GET as $name => $value){
			if(strpos($selector_url, $name) === false){
				$selector_url .= "&".$name."=".$value;
			}
		}
		return $selector_url;
	}

	protected function get_composed_concept($element_id){
		global $entity_type, $mode;
		$authority = new authority($element_id);
		$vedette = new vedette_composee(0, 'rameau');
		$composed_concept = array();

		$vedette_class_name = $authority->get_vedette_class();
		$vedette_field = $vedette->get_at_available_field_class_name($vedette_class_name);
		$num_type = $vedette_field['num'];
		$composed_concept = concept::get_concepts_composed_with_entity($authority->get_num_object(), $authority->get_type_const(), 'rameau');
		if(!count($composed_concept)){
			if(class_exists($vedette_class_name)){
				$vedette_elt = new $vedette_class_name($num_type, $authority->get_num_object());
				$vedette->add_element($vedette_elt, 'subdivision_tete', 0);
				$vedette->update_label();
				$vedette->save();
				if ($entity_type == 'authperso') {
					$composed_concept = array($this->create_concept_from_vedette($vedette, vedette_schemes::get_scheme_by_entity($mode)));
				} else {
					$composed_concept = array($this->create_concept_from_vedette($vedette, vedette_schemes::get_scheme_by_entity($authority->get_type_const())));
				}
			}
		}
		/**
		 * TODO: Ajouter le cas de retour d'une liste de
		 * concepts utilisant cette autorité dans sa composition
		 *
		 */
		return $composed_concept;
	}

	protected function create_concept_from_vedette($vedette, $scheme_id = 0){
		global $base_path;
		global $opac_url_base;
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);

		$tab_namespaces=array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		$store = ARC2::getStore($data_store_config);

		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$handler = new onto_handler($base_path."/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config, $tab_namespaces, 'http://www.w3.org/2004/02/skos/core#prefLabel');
		$uri = onto_common_uri::get_new_uri("",$opac_url_base."concept#");
		$num_concept = onto_common_uri::get_id($uri);

		$clause = "";
		if ($scheme_id) {
			$clause = "<".$uri."> skos:inScheme <".onto_common_uri::get_uri($scheme_id)."> .";
		}

		$query = "insert into <pmb> {
				 		<".$uri."> rdf:type skos:Concept .
				 		<".$uri."> pmb:showInTop owl:Nothing .
		 				<".$uri."> skos:prefLabel '".addslashes($vedette->get_label())."' .
						".$clause."
					}";
		$handler->data_query($query);
		$query = "insert into vedette_link set
						num_object = ".$num_concept.",
						num_vedette = ".$vedette->get_id().",
						type_object = 1";
		$result = pmb_mysql_query($query);

		$onto_index = onto_index::get_instance($handler->get_onto_name());
		$onto_index->set_handler($handler);
		$onto_index->init();

		$onto_index->maj(0, $uri);


		$authority = new authority(0, $num_concept, AUT_TABLE_CONCEPT);
		return $authority->get_id();
	}

	protected function compute_concept_list($concept_id){
		global $tab_page;
		global $pmb_nb_elems_per_tab;
		global $tab_nb_per_page;
		global $msg,$charset, $base_path;
		global $tab_nb_results;

		if(!$tab_page){
			$tab_page = 1;
		}
		if(!$tab_nb_per_page){
			$tab_nb_per_page = $pmb_nb_elems_per_tab;
		}
		return array_slice($concept_id, (($tab_page-1) * ($tab_nb_per_page*1)), $tab_nb_per_page);
	}


	/**
	 * Fonction permettant de retourner une variable what en
	 * fonction de l'attribut objects_type défini dans le fichier xml authorities.xml
	 */
	private function get_what_from_type($type){
		switch($type){
			case 'authors':
				return 'auteur';
			case 'categories':
				return 'categorie';
			case 'publishers':
				return 'editeur';
			case 'collections':
				return 'collection';
			case 'subcollections':
				return 'subcollection';
			case 'series':
				return 'serie';
			case 'titres_uniformes':
				return 'titre_uniforme';
			case 'indexint':
				return 'indexint';
			case 'concepts':
				return 'ontology';
			case 'authpersos':
			case 'authperso':
				return 'authperso';
			default:
				return 'ontology';
		}
	}
}

?>