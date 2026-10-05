<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_merge_properties_ui.class.php,v 1.7 2019/08/14 08:02:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/onto/common/onto_common_class.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_contribution_datatype_merge_properties_ui extends onto_common_datatype_ui {

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
		global $msg, $charset, $ontology_tpl, $area_id, $sub_form, $form_id, $sub, $form_uri;
		
		$class = $property->get_ontology()->get_class($property->range[0]);
		$item = new onto_contribution_item($class, '');
		$item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($sub,$form_id,$area_id,$form_uri));
		/**
		 * contruction du nom de l'instance utilisé pour le sous champs
		 */
		$sub_instance_name = $property->pmb_name.'_'.$class->pmb_name;
		$form = $ontology_tpl['form_row_merge_properties'];				
		$form = str_replace("!!onto_row_label!!",htmlentities($property->get_label() ,ENT_QUOTES,$charset) , $form);
					
		$content='';
		
		$properties =  array();
		foreach ($class->get_properties() as $uri_property) {
			$properties[] = $class->get_property($uri_property);
		}
		
		if (!empty($datas)) {
			$new_element_order=max(array_keys($datas));
				
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content_hidden'];
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$row=str_replace("!!onto_row_content_hidden_value!!",$property->pmb_name.'_'.$class->pmb_name,$row);
				$row=str_replace("!!onto_row_content_hidden_lang!!",'',$row);
				//$row=str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);	
				$row=str_replace("!!onto_row_content_hidden_range!!",'merge_properties', $row);	
				$row=str_replace("!!onto_row_order!!",$order , $row);		
				$content.= $row;
				$content.= self::get_sub_properties($properties, $property, $item, $class, $data);
				
			}
		} else {		
			$form=str_replace("!!onto_new_order!!","0" , $form);				
			$row = $ontology_tpl['form_row_content_hidden'];		
			$row = str_replace("!!onto_row_content_hidden_value!!", '', $row);
			$row = str_replace("!!onto_row_content_hidden_lang!!",'', $row);
			//$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);		
			$row = str_replace("!!onto_row_content_hidden_range!!",'merge_properties', $row);		
			$row=str_replace("!!onto_row_order!!","0" , $row);		
			$content.=$row;
			$content.= self::get_sub_properties($properties, $property, $item, $class, null);
		}		
		$form=str_replace("!!onto_rows!!",$content ,$form);
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
				if (!empty($valid_js)) {
					$valid_js.= ",";
				}
				$valid_js.= parent::get_validation_js($item_uri, $properties[$i], $restrictions, $datas, $instance_name, $flag);
				
				if ($i < (count($properties) - 1) && substr($valid_js, 0, 1) != ",") {
					$valid_js.= ",";
				}
			}			
		}	
		
	}
	
	protected static function get_sub_properties($properties, $property, $item, $class, $data){
		$content = "";
		if (!empty($properties)) {
			$content .= "<div style='border:1px solid black; border-color: #c5c5c5; border-radius:2px; padding:10px;'>";
			$index = 0;
			
			$temp_datatype_tab = '';
			
			foreach($properties as $prop){
				$datatype_class_name=$item->resolve_datatype_class_name($prop);
					
				$datatype_ui_class_name=$item->resolve_datatype_ui_class_name($datatype_class_name,$prop,$item->onto_class->get_restriction($prop->uri));
				
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
				$sub_data = null;
				if ($data && isset($data->get_value()->{$prop->pmb_name})) {
					$sub_data = $data->get_value()->{$prop->pmb_name};	
				}
				$datatype_form = $datatype_ui_class_name::get_form(null,$prop,$item->onto_class->get_restriction($prop->uri),$sub_data,$property->pmb_name.'_'.$class->pmb_name,'')
				.'<input type="hidden" name="inputs_name[]" value="http://www.pmbservices.fr/ontology#'.$property->pmb_name.'_'.$class->pmb_name.'_'.$prop->pmb_name.'_default_value"/>
				<input type="hidden" name="'.$property->pmb_name.'_'.$class->pmb_name.'_'.$prop->pmb_name.'_default_value" value="'.$property->pmb_name.'_'.$class->pmb_name.'_'.$prop->pmb_name.'"/>
				<input type="hidden" name="'.$property->pmb_name.'_'.$class->pmb_name.'['.$prop->pmb_name.']" value="'.$property->pmb_name.'_'.$class->pmb_name.'_'.$prop->pmb_name.'"/>';
					
				$content .= $datatype_form;
	
				$index++;
			}
			$content .= "</div>";
		}		
		return $content;
	}
} // end of onto_common_datatype_resource_selector_ui
