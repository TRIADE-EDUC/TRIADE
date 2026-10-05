<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_ui.class.php,v 1.3 2021/01/21 09:42:08 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/vedette/vedette_common.tpl.php');
require_once($class_path."/vedette/vedette_element.class.php");

class vedette_ui {
    
    
    /*** Attributes: ***/
    
    private $vedette_composee;
    
    /**
     *
     *
     * @param int id_vedette_composee id de la vedette composée à représenter
     *
     * @return void
     * @access public
     */
    public function __construct(vedette_composee $vedette_composee) {
        $this->vedette_composee = $vedette_composee;
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
    public function get_form($property_name, $order, $instance_name, $property_type = "",$no_add_script=1, $contribution = false){
        global $charset, $pmb_allow_authorities_first_page, $vedette_tpl;
        
        $form_html = '';
        //TODO Retirer le style brut
        if(!$order && $no_add_script){
            $form_html.=$vedette_tpl['css'].$vedette_tpl['form_body_script'];
        }
        if ($contribution){
            $form_html.=$vedette_tpl['form_body_contribution'];
        } else {
            $form_html.=$vedette_tpl['form_body'];
        }
        
        if (!count($this->vedette_composee->get_elements())) {
            $form_html = str_replace("!!vedette_composee_apercu!!", "", $form_html);
        } else {
            $form_html = str_replace("!!vedette_composee_apercu!!", htmlentities($this->vedette_composee->get_label(), ENT_QUOTES, $charset), $form_html);
        }
        $form_html = str_replace("!!vedette_composee_id!!", $this->vedette_composee->get_id(), $form_html);
        $form_html = str_replace("!!vedette_composee_type!!", $property_type, $form_html);
        $form_html = str_replace("!!vedette_composee_grammar!!", htmlentities($this->vedette_composee->get_config_filename(), ENT_QUOTES, $charset), $form_html);
        
        //les champs disponibles
        $available_fields_html='';
        $available_fields_scripts = "";
        $get_vedette_element_switchcases = "";
        
        foreach($this->vedette_composee->get_available_fields() as $key=>$available_field){
            if (empty($available_field['params'])) {
                $available_field['params'] = array();
            }
            $vedette_element_ui_class_name = vedette_element::search_vedette_element_ui_class_name($available_field["class_name"]);
            $js_class_name = $available_field["class_name"];
            $authid=0;
            if(!empty($available_field['params']['id_authority'])){
                $authid=$available_field['params']['id_authority'];
                if (method_exists($vedette_element_ui_class_name, 'get_js_class_name')) {
                    $js_class_name = $vedette_element_ui_class_name::get_js_class_name($available_field['params']);
                }
            }
            if ($contribution){
                $available_fields_scripts.= $vedette_element_ui_class_name::get_create_box_js($available_field['params'], "_contribution");
            } else {
                $available_fields_scripts.= $vedette_element_ui_class_name::get_create_box_js($available_field['params']);
            }
            $get_vedette_element_switchcases.= str_replace("!!vedette_type!!", ($available_field['class_name'] == "vedette_authpersos" && !empty($available_field['params']['id_authority'])) ? 'vedette_authperso_'.$available_field['params']['id_authority'] : $js_class_name, $vedette_tpl["vedette_composee_get_vedette_element_switchcase"]);
            
            $tmp_html=$vedette_tpl['vedette_composee_available_field'];
            $tmp_html = str_replace("!!available_field_id!!", $key, $tmp_html);
            $tmp_html = str_replace("!!available_field_type!!", ($available_field['class_name'] == "vedette_authpersos" && !empty($available_field['params']['id_authority'])) ? 'vedette_authperso_'.$available_field['params']['id_authority'] : $js_class_name, $tmp_html);
            $tmp_html = str_replace("!!available_field_num!!", $available_field['num'], $tmp_html);
            $tmp_html = str_replace("!!authid!!", $authid, $tmp_html);
            $tmp_html = str_replace("!!vedette_element_params!!", encoding_normalize::json_encode($available_field['params']), $tmp_html);
            
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
        foreach($this->vedette_composee->get_subdivisions() as $key=>$subdivision){
            $tmp_html = $vedette_tpl['vedette_composee_subdivision'];
            $tab_vedette_elements[$subdivision["order"]] = array();
            
            if (isset($subdivision["min"]) && $subdivision["min"]) $tmp_html = str_replace("!!vedette_composee_subdivision_cardmin!!", $subdivision["min"], $tmp_html);
            else $tmp_html = str_replace("!!vedette_composee_subdivision_cardmin!!", "", $tmp_html);
            if (isset($subdivision["max"]) && $subdivision["max"]) $tmp_html = str_replace("!!vedette_composee_subdivision_cardmax!!", $subdivision["max"], $tmp_html);
            else $tmp_html = str_replace("!!vedette_composee_subdivision_cardmax!!", "", $tmp_html);
            $tmp_html = str_replace("!!vedette_composee_subdivision_order!!", $subdivision["order"], $tmp_html);
            $elements_html='';
            if($elements=$this->vedette_composee->get_at_elements_subdivision($subdivision['code'])){
                // tableau pour la gestion de l'ordre à l'intérieur d'une subdivision
                $elements_order = array();
                // On parcourt les éléments de la subdivision
                foreach($elements as $position=>$element){
                    $current_element_html = $vedette_tpl['vedette_composee_element'];
                    $elements_order[] = $position;
                    
                    $tab_vedette_elements[$subdivision["order"]][$position] = $element->get_isbd();
                    $element_ui_class_name = vedette_element::search_vedette_element_ui_class_name(get_class($element));
                    $params = $element->get_params();
                    
                    $suffix = '';
                    if ($contribution) {
                        $suffix = '_contribution';
                    }
                    $current_element_html = str_replace("!!vedette_composee_element_form!!", $element_ui_class_name::get_form($params, $suffix), $current_element_html);
                    if(!empty($params['label'])){
                        $autority_type = get_msg_to_display($params["label"]);
                    }else{
                        $field_class_name = $this->vedette_composee->get_at_available_field_class_name(get_class($element));
                        $autority_type = get_msg_to_display($field_class_name["name"]);
                    }
                    $current_element_html = str_replace("!!vedette_composee_element_order!!", $position, $current_element_html);
                    $current_element_html = str_replace("!!vedette_composee_element_type!!", get_class($element), $current_element_html);
                    $current_element_html = str_replace("!!vedette_element_rawlabel!!", htmlentities($element->get_isbd(), ENT_QUOTES, $charset), $current_element_html);
                    $current_element_html = str_replace("!!vedette_element_label!!", htmlentities(($element->get_id() ? "[".$autority_type."] ".$element->get_isbd() : ''), ENT_QUOTES, $charset), $current_element_html);
                    $current_element_html = str_replace("!!vedette_element_id!!", $element->get_id(), $current_element_html);
                    $current_element_html = str_replace("!!vedette_element_available_field_num!!", $element->get_num_available_field(), $current_element_html);
                    
                    $selector_data = array();
                    $selector_data['type'] = ($element->get_type() !== null ? entities::get_string_from_const_type($element->get_type()) : '');
                    $json_data = encoding_normalize::json_encode($selector_data);
                    $current_element_html = str_replace("!!selector_data!!", urlencode($json_data), $current_element_html);
                                
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
        
        $form_html=str_replace("!!caller!!", $instance_name, $form_html);
        $form_html=str_replace("!!vedette_composee_order!!", $order, $form_html);
        $form_html=str_replace("!!property_name!!", $property_name."_composed", $form_html);
        $form_html=str_replace("!!caller_contribution!!", $instance_name, $form_html);
        $form_html=str_replace("!!property_name_contribution!!", $property_name, $form_html);
        $form_html=str_replace("!!tab_vedette_elements!!", encoding_normalize::json_encode($tab_vedette_elements), $form_html);
        $form_html=str_replace("!!vedette_separator!!", htmlentities($this->vedette_composee->get_separator(), ENT_QUOTES, $charset), $form_html);

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
    
    public function get_vedette_type_from_pmb_name($pmb_name) {
        switch ($pmb_name) {
            case 'has_secondary_author':
                return TYPE_NOTICE_RESPONSABILITY_SECONDAIRE;
            case 'has_other_author':
                return TYPE_NOTICE_RESPONSABILITY_AUTRE;
            case 'has_responsability_authperso':
                return TYPE_AUTHPERSO_RESPONSABILITY;
            case 'has_responsability_author':
                return TYPE_TU_RESPONSABILITY;
            case 'has_responsability_performer':
                return TYPE_TU_RESPONSABILITY_INTERPRETER;
            case 'has_main_author':
                return TYPE_NOTICE_RESPONSABILITY_PRINCIPAL;
        }
        return 0;
    }
}
