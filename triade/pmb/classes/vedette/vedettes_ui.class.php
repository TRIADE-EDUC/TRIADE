<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedettes_ui.class.php,v 1.8 2019/05/28 10:45:34 ccraig Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/vedette/vedette_common.tpl.php');
require_once($class_path."/vedette/vedette_element.class.php");

class vedettes_ui {
	
	
	/*** Attributes: ***/
	
    /**
     * 
     * @var array
     */
	private $vedettes_composees = array();
	
	/**
	 *
	 *
	 * @param int id_vedette_composee id de la vedette composée à représenter
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct($vedettes_composees) {
		$this->vedettes_composees = $vedettes_composees;
	}
	
	/**
	 *
	 *
	 * @param vedette_element vedette_element Factory : renvoie la classe vedette_element_ui qui va bien avec l'instance de vedette_element
	 * 
	 * @return void
	 * @access public
	 */
	public function get_vedette_element_ui($vedette_element) {
		return vedette_element::search_vedette_element_ui_class_name(get_class($vedette_element));
	}
	
	/**
	 * Renvoie le formulaire de la vedette composée
	 * 
	 * @param $property onto_common_property
	 * @param $restrictions onto_restriction
	 * @param $datas
	 * @param $instance_name
	 *
	 * @return string
	 * @access public
	 */
	public function get_form($property_name, $order, $instance_name, $property_type = "",$no_add_script=1){
		global $dbh,$base_path,$charset,$lang;
		global $vedette_tpl;
		
		$form_html = '';
		$grammar_body = '';
		//TODO Retirer le style brut
		if(!$order && $no_add_script){
			//$form_html.=$vedette_tpl['css'].$vedette_tpl['form_body_script'];
		}
		$form_html.= $vedette_tpl['grammar_head'];
		$grammar_tabs = "";
		for ($i = 0 ; $i < count($this->vedettes_composees) ; $i++) {
		    $grammar_tab = $vedette_tpl['grammar_tab'];
		    $grammar_tab = str_replace("!!grammar_selected!!", ($i == 0 ? "grammar_selected" : ""), $grammar_tab);
		    $grammar_tab = str_replace("!!grammar_value!!", $this->vedettes_composees[$i]->get_config_filename(), $grammar_tab);
		    $grammar_tab = str_replace("!!grammar_label!!", $this->vedettes_composees[$i]->get_config_filename(), $grammar_tab);
		    $grammar_tab = str_replace("!!grammar_index!!", $i, $grammar_tab);
		    $grammar_tabs .= $grammar_tab;
		}
		$grammar_body = static::get_grammar_form($this->vedettes_composees[0], $property_name, $order, $instance_name, $property_type, $no_add_script);
		$form_html = str_replace("!!grammar_tabs!!", $grammar_tabs, $form_html);
		$form_html = str_replace("!!grammar_body!!", $grammar_body, $form_html);
		//$form_html = $grammar_body;
		$form_html=str_replace("!!caller!!", $instance_name, $form_html);
		$form_html=str_replace("!!vedette_composee_order!!", $order, $form_html);
		$form_html=str_replace("!!property_name!!", $property_name."_composed", $form_html);
		$form_html=str_replace("!!property!!", $property_name, $form_html);
		return $form_html;
	}
	
	public static function get_grammar_form($vedette, $property_name, $order, $instance_name, $property_type = "", $no_add_script=1){
	    global $dbh,$base_path,$charset,$lang;
	    global $vedette_tpl;
	    global $pmb_allow_authorities_first_page;
	    
	    $form_html = '';
	    //TODO Retirer le style brut
	    if(!$order){
	        $form_html.=$vedette_tpl['css'];
	        if ($no_add_script) {
	            $form_html .= $vedette_tpl['form_body_script'];
	        }
	    }
	    $form_html.=$vedette_tpl['grammar_body'];
	    
	    if (!count($vedette->get_elements())) {
	        $form_html = str_replace("!!vedette_composee_apercu!!", "", $form_html);
	    } else {
	        $form_html = str_replace("!!vedette_composee_apercu!!", htmlentities($vedette->get_label(), ENT_QUOTES, $charset), $form_html);
	    }
	    $form_html = str_replace("!!vedette_composee_id!!", $vedette->get_id(), $form_html);
	    $form_html = str_replace("!!vedette_composee_type!!", $property_type, $form_html);
	    $form_html = str_replace("!!vedette_composee_grammar!!", htmlentities($vedette->get_config_filename(), ENT_QUOTES, $charset), $form_html);
	    
	    //les champs disponibles
	    $available_fields_html='';
	    $available_fields_scripts = "";
	    $get_vedette_element_switchcases = "";
	    
	    foreach($vedette->get_available_fields() as $key=>$available_field){
	        $vedette_element_ui_class_name = vedette_element::search_vedette_element_ui_class_name($available_field["class_name"]);
	        $js_class_name = $available_field["class_name"];
	        $authid=0;
	        $field_params = "";
	        if(isset($available_field['params'])){
	            $field_params = encoding_normalize::json_encode($available_field['params']);
	            if (!empty($available_field['params']['id_authority'])) {
	                $authid = $available_field['params']['id_authority'];
	            }
	            if (method_exists($vedette_element_ui_class_name, 'get_js_class_name')) {
	                $js_class_name = $vedette_element_ui_class_name::get_js_class_name($available_field['params']);
	            }
	            $available_fields_scripts .= $vedette_element_ui_class_name::get_create_box_js($available_field['params']);
	        }else {
	            $available_fields_scripts .= $vedette_element_ui_class_name::get_create_box_js();
	        }
	        $get_vedette_element_switchcases .= str_replace("!!vedette_type!!", $js_class_name, $vedette_tpl["vedette_composee_get_vedette_element_switchcase"]);
	        
	        $tmp_html=$vedette_tpl['vedette_composee_available_field'];
	        $tmp_html = str_replace("!!available_field_id!!", $key, $tmp_html);
	        $tmp_html = str_replace("!!available_field_type!!", $js_class_name, $tmp_html);
	        $tmp_html = str_replace("!!available_field_num!!", $available_field['num'], $tmp_html);
	        $tmp_html = str_replace("!!authid!!", $authid, $tmp_html);
	        $tmp_html = str_replace("!!vedette_element_params!!", $field_params, $tmp_html);
	        
	        $tmp_html=str_replace("!!vedette_composee_available_field_label!!", get_msg_to_display($available_field['name']), $tmp_html);
	        $available_fields_html.=$tmp_html;
	    }
	    $form_html=str_replace("!!available_fields_scripts!!", $available_fields_scripts, $form_html);
	    $form_html=str_replace("!!vedette_composee_available_fields!!", $available_fields_html, $form_html);
	    $form_html=str_replace("!!get_vedette_element_switchcases!!", $get_vedette_element_switchcases, $form_html);
	    $form_html=str_replace("!!direct_search!!", $pmb_allow_authorities_first_page, $form_html);
	    
	    //les zones de subdivision
	    $subdivisions_html='';
	    
	    $tab_vedette_elements = array();
	    
	    //On parcourt les subdivisions
	    foreach($vedette->get_subdivisions() as $key=>$subdivision){
	        $tmp_html = $vedette_tpl['vedette_composee_subdivision'];
	        $tab_vedette_elements[$subdivision["order"]] = array();
	        
	        if (isset($subdivision["min"]) && $subdivision["min"]) $tmp_html = str_replace("!!vedette_composee_subdivision_cardmin!!", $subdivision["min"], $tmp_html);
	        else $tmp_html = str_replace("!!vedette_composee_subdivision_cardmin!!", "", $tmp_html);
	        if (isset($subdivision["max"]) && $subdivision["max"]) $tmp_html = str_replace("!!vedette_composee_subdivision_cardmax!!", $subdivision["max"], $tmp_html);
	        else $tmp_html = str_replace("!!vedette_composee_subdivision_cardmax!!", "", $tmp_html);
	        $tmp_html = str_replace("!!vedette_composee_subdivision_order!!", $subdivision["order"], $tmp_html);
	        $elements_html='';
	        if($elements=$vedette->get_at_elements_subdivision($subdivision['code'])){
	            
	            // tableau pour la gestion de l'ordre à l'intérieur d'une subdivision
	            $elements_order = array();
	            
	            // On parcourt les éléments de la subdivision
	            foreach($elements as $position=>$element){
	                $current_element_html = $vedette_tpl['vedette_composee_element'];
	                $elements_order[] = $position;
	                
	                $tab_vedette_elements[$subdivision["order"]][$position] = $element->get_isbd();
	                $element_ui_class_name = vedette_element::search_vedette_element_ui_class_name(get_class($element));
	                $element_params = $element->get_params();
	                $current_element_html = str_replace("!!vedette_composee_element_form!!", $element_ui_class_name::get_form($element_params), $current_element_html);
	                if(!empty($element_params['label'])){
	                    $autority_type = get_msg_to_display($element_params["label"]);
	                }else{
	                    $field_class_name = $vedette->get_at_available_field_class_name(get_class($element));
	                    $autority_type = get_msg_to_display($field_class_name["name"]);
	                }
	                $current_element_html = str_replace("!!vedette_composee_element_order!!", $position, $current_element_html);
	                $current_element_html = str_replace("!!vedette_composee_element_type!!", get_class($element), $current_element_html);
	                $current_element_html = str_replace("!!vedette_element_rawlabel!!", htmlentities($element->get_isbd(), ENT_QUOTES, $charset), $current_element_html);
	                $current_element_html = str_replace("!!vedette_element_label!!", htmlentities(($element->get_id() ? "[".$autority_type."] ".$element->get_isbd() : ''), ENT_QUOTES, $charset), $current_element_html);
	                $current_element_html = str_replace("!!vedette_element_id!!", $element->get_id(), $current_element_html);
	                $current_element_html = str_replace("!!vedette_element_available_field_num!!", $element->get_num_available_field(), $current_element_html);
	                
	                $elements_html.=$current_element_html;
	            }
	            $tmp_html = str_replace("!!vedette_composee_subdivision_elements!!", $elements_html, $tmp_html);
	            $tmp_html = str_replace("!!elements_order!!", implode(",", $elements_order), $tmp_html);
	        } else {
	            $tmp_html = str_replace("!!vedette_composee_subdivision_elements!!", "", $tmp_html);
	            $tmp_html = str_replace("!!elements_order!!", "", $tmp_html);
	        }
	        $tmp_html=str_replace("!!vedette_composee_subdivision_label!!", get_msg_to_display((isset($subdivision['name']) ? $subdivision['name'] : '')), $tmp_html);
	        $tmp_html=str_replace("!!vedette_composee_subdivision_id!!", $subdivision['code'], $tmp_html);
	        
	        $subdivisions_html.=$tmp_html;
	    }
	    $form_html=str_replace("!!vedette_composee_subdivisions!!", $subdivisions_html, $form_html);
	    $form_html=str_replace("!!tab_vedette_elements!!", encoding_normalize::json_encode($tab_vedette_elements), $form_html);
	    $form_html=str_replace("!!vedette_separator!!", htmlentities($vedette->get_separator(), ENT_QUOTES), $form_html);
	    $form_html=str_replace("!!caller!!", $instance_name, $form_html);
	    $form_html=str_replace("!!vedette_composee_order!!", $order, $form_html);
	    $form_html=str_replace("!!property_name!!", $property_name."_composed", $form_html);
	    
	    return $form_html;
	    
	}
	
	/**
	 * Récupère les éléments du formulaire
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_from_form(){
	
	}
	
	
	//inutile ?
	public function proceed() {
	
	}
}
