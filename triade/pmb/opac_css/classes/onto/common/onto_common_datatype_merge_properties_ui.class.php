<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_merge_properties_ui.class.php,v 1.9.8.1 2025/01/30 09:08:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/onto/common/onto_common_class.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_common_datatype_merge_properties_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param Array() class_uris URI des classes de l'ontologie listées dans le sélecteur

	 * @return void
	 * @access public
	 */
	public function __construct( $class_uris ) {
	} // end of member function __construct

	/**
	 * 
	 *
	 * @param string class_uri URI de la classe d'instances à lister

	 * @param integer page Numéro de page à afficher

	 * @return Array()
	 * @access public
	 */
	public function get_list( $class_uri,  $page ) {
	} // end of member function get_list

	/**
	 * Recherche
	 *
	 * @param string user_query Chaine de recherche dans les labels

	 * @param string class_uri Rechercher iniquement les instances de la classe

	 * @param integer page Page du résultat de recherche à afficher

	 * @return Array()
	 * @access public
	 */
	public function search( $user_query,  $class_uri,  $page ) {
	} // end of member function search


	/**
	 * 
	 *
	 * @param onto_common_property $property la propriété concernée
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg, $charset, $ontology_tpl, $params, $area_id, $sub_form, $form_id, $sub;
		
		//$class = new onto_common_class($property->range[0], $property->get_ontology());
		
		$class = $property->get_ontology()->get_class($property->range[0]);
		$item = new onto_contribution_item($class, '');
		
		$item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($params->sub,$params->form_id,$params->area_id,$params->form_uri));
				
		//return $item->get_form("./index.php?lvl=".$params->lvl."&sub=".$params->sub."&area_id=".$params->area_id."&id=".$params->id.'&form_id='.$params->form_id.'&form_uri='.$params->form_uri);
		$sub_instance_name = onto_common_uri::get_name_from_uri($item->get_uri(), $class->pmb_name);
		
		$form = $ontology_tpl['form_row_merge_properties'];
				
		$form = str_replace("!!onto_row_label!!",htmlentities($property->get_label() ,ENT_QUOTES,$charset) , $form);
		
		$linked_forms = array();		
		$item_linked_forms = $item->get_contribution_area_form()->get_linked_forms(); 
		if (is_array($item_linked_forms)) {
			foreach($item_linked_forms as $item_linked_form) {
				if (isset($item_linked_form["propertyPmbName"]) && $item_linked_form["propertyPmbName"] == $property->pmb_name) {
					$linked_forms[] = $item_linked_form;
				}
			}
		}
				
		$content='';
// 		$valid_js = "";
		
		$properties =  array();
		foreach ($class->get_properties() as $uri_property) {
			$properties[] = $class->get_property($uri_property);
		}
		
		if(is_countable($datas) && sizeof($datas)){
			$new_element_order=max(array_keys($datas));
				
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				/**
				 * TODO : modif provisoire
				 * les valeurs par défaut ne devrait pas retourner un tableau
				 */
				if (!is_array($data->get_value())){
					$item->set_assertions($item::get_handler()->get_assertions($data->get_value()));
					$sub_instance_name = onto_common_uri::get_name_from_uri($data->get_value(), $item->onto_class->pmb_name);				
				} else {
					$assertions = array();
					foreach ($data->get_value() as $uri => $tab_properties) {
						foreach ($tab_properties as $sub_property) {
							$assertions[] = new onto_assertion($item->get_uri(), $uri, $sub_property->get_value(), $sub_property->get_value_type(), $sub_property->get_value_properties());
						}
					}
					$item->set_assertions($assertions);
				}
				
				$row=$ontology_tpl['form_row_content_hidden'];		
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}		
				$row=str_replace("!!onto_row_content_hidden_value!!",$data->get_value(),$row);
				$row=str_replace("!!onto_row_content_hidden_lang!!",$data->get_lang(),$row);
				$row=str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);	
				$row=str_replace("!!onto_row_order!!",$order , $row);		
				$content.= $row;
				$content.= self::get_sub_properties($properties, $property, $item, $sub_instance_name, $linked_forms);
				
			}
		} else {		
			$form=str_replace("!!onto_new_order!!","0" , $form);				
			$row = $ontology_tpl['form_row_content_hidden'];		
			$row = str_replace("!!onto_row_content_hidden_value!!", $item->get_uri(), $row);
			$row = str_replace("!!onto_row_content_hidden_lang!!","" , $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);		
			$row=str_replace("!!onto_row_order!!","0" , $row);		
			$content.=$row;
			$content.= self::get_sub_properties($properties, $property, $item, $sub_instance_name, $linked_forms);
		}		
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_row_id!!",htmlentities($instance_name.'_'.$property->pmb_name,ENT_QUOTES,$charset) , $form);
		
		return $form;
	} // end of member function get_form
	
	/**
	 *
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
	 * @param property property la propriété à utiliser
	 * @param string instance_name nom de l'instance
	 *
	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
	
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->get_label().' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
	
	} // end of member function get_display
	
	
	public static function get_validation_js($item_uri,$property, $restrictions,$datas, $instance_name,$flag){
		$valid_js = "";

		$class = new onto_common_class($property->range[0], $property->get_ontology());
		$properties =  array();
		foreach ($class->get_properties() as $uri_property) {
			$properties[] = $class->get_property($uri_property);
		}
		
		for ($i = 0; $i < count($properties); $i++) {
			if ((!$flag || (in_array($flag,$properties[$i]->flags)))) {
				if (!$valid_js) {
					$valid_js.= ",";
				}
				$valid_js.= parent::get_validation_js($item_uri, $properties[$i], $restrictions, $datas, $instance_name, $flag);
				
				if ($i < (count($properties)-1) && substr($valid_js, 0, 1) != ",") {
					$valid_js.= ",";
				}
			}			
		}	
		
	}
	
	protected static function get_sub_properties($properties, $property, $item, $sub_instance_name, $linked_forms) 
	{
		if(is_countable($properties) && sizeof($properties)){
			$content .= "<div style='border:1px solid black; border-color: #c5c5c5; border-radius:2px; padding:10px;'>";
			$index = 0;
			
			$temp_datatype_tab = $item->order_datatypes();
			
			foreach($properties as $prop){
		
				
				if((!$flag || (in_array($flag,$prop->flags)))){
						
					$datatype_class_name=$item->resolve_datatype_class_name($prop);
						
					$datatype_ui_class_name=$item->resolve_datatype_ui_class_name($datatype_class_name,$prop,$item->onto_class->get_restriction($prop->uri));
										
					//gestion des formulaires liés
					$prop->has_linked_form = false;
					$prop->linked_form = array();
					for($i = 0; $i < count($linked_forms); $i++) {
					
						//recherche du formulaire lié
						if ($linked_forms[$i]['propertyPmbName'] == $prop->pmb_name) {
					
							$prop->has_linked_form = true;
							$prop->linked_form['attachment_id'] = $linked_forms[$i]['id'];
					
							//id_du formulaire dans la base relationnelle
							$prop->linked_form['form_id'] = $linked_forms[$i]['formId'];
							//id du formulaire dans le store
							$prop->linked_form['form_id_store'] = $linked_forms[$i]['id'];
							//uri du formulaire dans le store
							$prop->linked_form['form_uri'] = $linked_forms[$i]['uri'];
							if ($area_id) {
								//id de l'espace
								$prop->linked_form['area_id'] = $area_id;
							} else {
								$prop->linked_form['area_id'] = "";
							}
							//type du formulaire
							$prop->linked_form['form_type'] = $linked_forms[$i]['entityType'];
							//titre du formulaire
							$prop->linked_form['form_title'] = $linked_forms[$i]['name'];
					
						}
					}
// 					for($i = 0; $i < count($linked_forms); $i++) {
// 						//recherche du formulaire lié
// 						$pmb_name_from_range = explode("#",$prop->range[0])[1];
						
// 						if (isset($pmb_name_from_range) && $linked_forms[$i]['entityType'] == $pmb_name_from_range) {
								
// 							$prop->has_linked_form = true;
// 							$property_linked_form = array();
// 							$property_linked_form['attachment_id'] = $linked_forms[$i]['id'];
								
// 							//id_du formulaire dans la base relationnelle
// 							$property_linked_form['form_id'] = $linked_forms[$i]['formId'];
// 							//id du formulaire dans le store
// 							$property_linked_form['form_id_store'] = $linked_forms[$i]['id'];
// 							//uri du formulaire dans le store
// 							$property_linked_form['form_uri'] = $linked_forms[$i]['uri'];
// 							if ($area_id) {
// 								//id de l'espace
// 								$property_linked_form['area_id'] = $area_id;
// 							} else {
// 								$property_linked_form['area_id'] = "";
// 							}
// 							//type du formulaire
// 							$property_linked_form['form_type'] = $linked_forms[$i]['entityType'];
// 							//titre du formulaire
// 							$property_linked_form['form_title'] = $linked_forms[$i]['name'];
							
// 							$prop->linked_form[] = $property_linked_form;
								
// 						}
// 					}
		
					//on modifie la propiété avec le paramétrage du formulaire
					if ($prop->pmb_extended['label']) {
						$prop->label = $prop->pmb_extended['label'];
					}
		
					if ($prop->pmb_extended['default_value']) {
						$prop->default_value = array();
						foreach ($prop->pmb_extended['default_value'] as $key => $value) {
							$prop->default_value[] = $value->value;
						}
					}
						
					//propriété obligatoire
					if ($prop->pmb_extended['mandatory']) {
						$item->onto_class->get_restriction($prop->uri)->set_min('1');
					}
					
					//propriété cachée
					if ($prop->pmb_extended['hidden']) {
						$datatype_form = $datatype_ui_class_name::get_hidden_fields($prop,$temp_datatype_tab[$prop->uri][$datatype_ui_class_name],$sub_instance_name);
					} else {						
						$datatype_form = $datatype_ui_class_name::get_form($item->get_uri(),$prop,$item->onto_class->get_restriction($prop->uri),$temp_datatype_tab[$prop->uri][$datatype_ui_class_name],$sub_instance_name,$flag);
					}
						
					$content .= $datatype_form;
		
					$index++;
				}
			}
			$content .= "</div>";
		}
		
		return $content;
	}
} // end of onto_common_datatype_resource_selector_ui
