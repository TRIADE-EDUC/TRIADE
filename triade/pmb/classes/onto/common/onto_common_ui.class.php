<?php
// +-------------------------------------------------+
// ï¿½ 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_ui.class.php,v 1.38 2022/11/22 11:07:00 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/common/onto_common_ui.tpl.php');

class onto_common_ui extends onto_root_ui{
	

	/**
	 * Renvoie le formulaire de recherche 
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return string $form
	 */
	public static function get_search_form($controler,$params){
		
		if(!$controler->class_is_indexed($params->sub)){
			return "";
		}
		global $msg,$charset,$base_path,$ontology_tpl;
		
		$title = '';
		$classes = $controler->get_classes();
		foreach($classes as $class){
			if($class->pmb_name == $params->sub){
				$title.= $controler->get_label($class->pmb_name);
			}
		}
		
		$form = $ontology_tpl['search_form'];
		$form = str_replace('!!search_form_action!!', $base_path.'/'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=search', $form);
		$form = str_replace('!!search_form_last_link!!', $base_path.'/'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=last', $form);
		$form = str_replace('!!search_form_title!!', $title, $form);
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub];
		}else{
			$add_msg = sprintf($msg['onto_common_add'], $controler->get_label($params->sub));
		}
		$form = str_replace('!!search_form_user_input!!',stripslashes(htmlentities($params->user_input,ENT_QUOTES,$charset)),$form);
		$form = str_replace('!!search_form_add_value_onclick!!','document.location=\'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit\'', $form);
		$form = str_replace('!!search_form_add_value!!',htmlentities($add_msg,ENT_QUOTES,$charset), $form);
		
		return $form;
	}
	
	/**
	 * 
	 * Renvoie le formulaire de recherche pour le selecteur d'autorité
	 * 
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_search_form_selector($controler,$params){
		global $sel_search_form,$jscript,$range_link_form;
		global $list_range_links_form;
		global $sel_no_available_search_form;
		global $msg;
		
		if($params->objs){
		    $property=$controler->get_onto_property_from_pmb_name($params->objs);
		    $element=$controler->get_class_uri($controler->get_item_type_to_list($params,true));
		    $type =$controler->get_class_pmb_name($element);
		}else {
			$type = $params->element;
			$element = $controler->get_class_uri($params->element);
		}
		$form = "";
		
		
		if($controler->get_searcher_class_name($element)){
			$search = $sel_search_form;
			$search = str_replace("!!base_url!!", $params->base_url."&range=".$params->range, $search);
			$search = str_replace("!!deb_rech!!", stripslashes($params->deb_rech), $search);
			$form.= $search;
		}else{
			$form = $sel_no_available_search_form;
		}
		if (is_object($property) && count($property->range) > 1) {
			$range_links_form = "";
			foreach ($property->range as $uri_class) {
				$current_range_link_form = $range_link_form;
				$range_name = $controler->get_class_pmb_name($uri_class);
				$current_range_link_form = str_replace("!!class!!", ($type == $range_name ? "class='selected'" : ""), $current_range_link_form);
				$current_range_link_form = str_replace("!!href!!", $params->base_url."&range=".$range_name, $current_range_link_form);
				$current_range_link_form = str_replace("!!libelle!!", $controler->get_class_label($uri_class), $current_range_link_form);
		
				$range_links_form.= $current_range_link_form;
			}
 			$range_links = str_replace("!!range_links_form!!", $range_links_form, $list_range_links_form);
			$form.= $range_links;
		}
	
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_'.$type])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_'.$type];
		}else{
			$add_msg = sprintf($msg['onto_common_add'],$controler->get_label($type));
		}
		$form = str_replace("!!add_button_label!!", $add_msg, $form);		
		
		$onclick = "document.location=\"".$params->base_url."&range=".$params->range."&action=selector_add\"";
		$form = str_replace("!!add_button_onclick!!", $onclick, $form);
		
		$form.= $jscript;
		
		return $form;
	}
	
	/**
	 * Renvoie l'affichage html de la liste pour le selecteur d'autorité
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 *
	 * @return string $form
	 */
	public static function get_list_selector($controler,$params){
		global $msg,$charset,$element_form,$list_form,$lang;
		
		$multiple_range = false;
		if($params->objs){
			$property=$controler->get_onto_property_from_pmb_name($params->objs);
			if(count($property->range)>1){
				$multiple_range = true;
			}
		}
		$type = $controler->get_item_type_to_list($params,true);
		$element = $controler->get_class_uri($type);
		$elements = $controler->get_list_elements($params);
		$elements_form = "";
		$list = $list_form;
		if($elements["nb_total_elements"]){
			foreach($elements['elements'] as $uri => $item){
				$current_element_form = $element_form;
				$current_element_form = str_replace("!!caller!!", $params->caller, $current_element_form);
				$current_element_form = str_replace("!!element!!", $params->element, $current_element_form);
				$current_element_form = str_replace("!!order!!", (!empty($params->order) ? $params->order : 0), $current_element_form);
				$current_element_form = str_replace("!!uri!!", ($params->return_concept_id ? onto_common_uri::get_id($uri) : $uri), $current_element_form);
				$current_element_form = str_replace("!!range!!", $element, $current_element_form);
				$current_element_form = str_replace("!!callback!!", $params->callback, $current_element_form);
				$item_label = (isset($item[$lang]) ? $item[$lang] : $item['default']);
				$current_element_form = str_replace("!!item_libelle!!", htmlentities($item_label,ENT_QUOTES,$charset), $current_element_form);
				if($multiple_range){
				    $item_label = "[".$controler->get_class_label($element)."] ".$item_label;
				}
				$current_element_form = str_replace("!!item!!", addslashes($item_label), $current_element_form);
				$elements_form.= $current_element_form;
			}
			$list = str_replace("!!elements_form!!", $elements_form, $list);
			$list = str_replace("!!aff_pagination!!", aff_pagination($params->base_url.'&range='.$type.(isset($params->deb_rech) ? '&deb_rech='.$params->deb_rech : ''),$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ), $list);
		}else{
			$list = $msg["1915"];
		}
		return $list;
	}
	
	/**
	 * Renvoie l'affichage html de la liste
	 *
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 */
	public static function get_list($controler,$params){
		global $msg,$charset,$ontology_tpl,$lang;
		
		$elements = $controler->get_list_elements($params);
		
		$list="<h3>".$elements['nb_total_elements']." ".$msg['onto_nb_results']."</h3>".$ontology_tpl['list'];
		$list=str_replace("!!list_header!!", htmlentities($msg['103'],ENT_QUOTES,$charset), $list);
		$list_content='';
		foreach($elements['elements'] as $uri => $item){
		    $id = onto_common_uri::get_id($uri);
			$line=$ontology_tpl['list_line'];
			$line=str_replace("!!list_line_link_see!!","./".$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&action=see&id='.$id, $line);
			$line=str_replace("!!list_line_href!!",'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&action=edit&id='.$id , $line);
			$line=str_replace("!!list_line_libelle!!",htmlentities((isset($item[$lang]) ? $item[$lang] : $item['default']),ENT_QUOTES,$charset) , $line);
			$list_content.= $line;
		}
		
		$list=str_replace("!!list_content!!",$list_content , $list);
		
		if(isset($msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub])){
			$add_msg = $msg['onto_'.$controler->get_onto_name().'_add_'.$params->sub];
		}else{
			$add_msg = sprintf($msg['onto_common_add'],$controler->get_label($params->sub));
		}
		
		$list=str_replace("!!list_onclick!!",'document.location=\'./'.$controler->get_base_resource().'categ='.$params->categ.'&sub='.$params->sub.'&id=&action=edit\'' , $list);
		$list=str_replace("!!list_value!!",htmlentities($add_msg,ENT_QUOTES,$charset) , $list);
		$list=str_replace("!!list_pagination!!",aff_pagination("./".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&action=".$params->action."&user_input=".$params->user_input,$elements['nb_total_elements'],$elements['nb_onto_element_per_page'], $params->page, 10, true, true ) , $list);
		
		return $list;
	}
		
	/**
	 * Renvoie l'affichage html des erreur
	 *
	 * @param onto_common_controler $controler
	 * @param array $errors
	 */
	public static function display_errors($controler,$errors, $return_messages = false){
		global $msg;
		
		$messages = array();
		if (!empty($errors)) {
    		foreach ($errors as $property => $error){
    			if(isset($error['type'])){
    				switch($error['type']){
    					case "card" :
    						if($error['error'] == "no minima"){
    							$messages[] = sprintf($msg['onto_error_no_minima'],$controler->get_label($property));
    						}else if( $error['error'] == "too much values"){
    							$messages[] = sprintf($msg['onto_error_too_much_values'],$controler->get_label($property));
    						}
    						break;
    					case "must be distinct" :
    						$messages[] = sprintf($msg['onto_error_must_be_distinct'],$controler->get_label($property),$controler->get_label($error['error']));
    						break;
    					case "unvalid datas" :
    						$messages[] = sprintf($msg['onto_error_unvalid_datas'],$controler->get_label($property));
    						break;
    					case "sparql" :
    					    $messages[] = $error['message'] ?? $msg['540'];
    					    break;
    					default :
    					    $messages[] = !empty($error['message']) ? $error['message'] :$controler->get_label($property)." : ".$msg['540'];
    						break;
    				}
    			}
    		}
		}
		if ($return_messages) {
		    return $messages;
		}
		error_message($msg['540'], implode("<br/>",$messages), 1);
	}
	
	/**
	 * Retourne la liste des assertions contenant l'item susceptible d'être supprimé
	 * @param onto_common_controler $controler
	 * @param onto_param $params
	 * @param onto_assertion $assertions
	 * @return string
	 */
	public static function get_list_assertions($controler, $params, $assertions){
		global $ontology_tpl;
		
		$list = $ontology_tpl["list_assertions"];
		
		$list_content = "";
		foreach ($assertions as $assertion) {
			/* @var $assertion onto_assertion */
			$current_assertion = $ontology_tpl["list_assertions_line"];
			$current_assertion = str_replace("!!assertion_subject!!", $controler->get_data_label($assertion->get_subject()), $current_assertion);
			$current_assertion = str_replace("!!assertion_predicate!!", $controler->get_label($assertion->get_predicate()), $current_assertion);
			$current_assertion = str_replace("!!assertion_object!!", $controler->get_data_label($assertion->get_object()), $current_assertion);
			$list_content .= $current_assertion;
		}
		$list = str_replace("!!list_content!!", $list_content, $list);
		$list = str_replace("!!href_cancel!!", "./".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&id=".$params->id."&action=edit", $list);
		$list = str_replace("!!href_continue!!", "./".$controler->get_base_resource()."categ=".$params->categ."&sub=".$params->sub."&id=".$params->id."&action=delete", $list);
		
		return $list;
	}
}