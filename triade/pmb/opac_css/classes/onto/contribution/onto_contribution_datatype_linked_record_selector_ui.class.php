<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_record_selector_ui.class.php,v 1.2 2019-01-03 16:10:38 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_responsability_selector_ui
 * 
 */
class onto_contribution_datatype_linked_record_selector_ui extends onto_common_datatype_resource_selector_ui {

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
		global $msg,$charset,$ontology_tpl;
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->label, 'utf-8') ,ENT_QUOTES,$charset) , $form);
		
		/** traitement initial du range ?!*/
		$range_for_form = ""; 
		if(is_array($property->range)){
			foreach($property->range as $range){
				if($range_for_form) $range_for_form.="|||";
				$range_for_form.=$range;
			}
		}
		/** **/
		
		/** TODO: à revoir avec le chef ** / 
		/** On part du principe que l'on a qu'un range **/
// 		$selector_url = $this->get_resource_selector_url($property->range[0]);
		
		
		
		$content='';
		$content.=$ontology_tpl['form_row_content_input_sel'];
		
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		if(sizeof($datas)){
			$i = 1;
			$first = true;
			$new_element_order = max(array_keys($datas));
			
			$form = str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				$row = $ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order = $data->get_order();
				}else{
					$order = $key;
				}
				$formated_value = $data->get_formated_value();
				$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];
				$inside_row = str_replace("!!form_row_content_linked_record_selector_display_label!!",htmlentities((isset($formated_value['record']['display_label']) ? $formated_value['record']['display_label'] : ""), ENT_QUOTES, $charset) , $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_record_selector_value!!", (isset($formated_value['record']['value']) ? $formated_value['record']['value'] : ""), $inside_row);
				$inside_row = str_replace("!!form_row_content_linked_record_selector_range!!",$data->get_value_type() , $inside_row);
				
				$selector = notice_relations::get_selector('!!onto_row_id!![!!onto_row_order!!][value]',(isset($formated_value['relation_type']) ? $formated_value['relation_type']."-".$formated_value['direction'] : ""));
				$inside_row = str_replace('!!onto_row_content_linked_record_selector!!', $selector, $inside_row);
				$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
				
				
				$inside_row = str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row = str_replace("!!onto_current_range!!",'http://www.pmbservices.fr/ontology#record',$inside_row);
				
				$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input = '';
				if($first){
					$input.= $ontology_tpl['form_row_content_input_remove'];
				}else{
					$input.= $ontology_tpl['form_row_content_input_del'];
				}
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
				
				$row = str_replace("!!onto_row_inputs!!",$input , $row);
				$row = str_replace("!!onto_row_order!!",$order , $row);
				
				$content.= $row;
				$first = false;
				$i++;
			}
		}else{
			$form = str_replace("!!onto_new_order!!","0" , $form);
			
			$row = $ontology_tpl['form_row_content'];
			
			$inside_row = $ontology_tpl['form_row_content_linked_record_selector'];			
			$inside_row = str_replace("!!form_row_content_linked_record_selector_display_label!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_value!!","" , $inside_row);
			$inside_row = str_replace("!!form_row_content_linked_record_selector_range!!","" , $inside_row);
			
			$selector = notice_relations::get_selector('!!onto_row_id!![!!onto_row_order!!][value]');
			$inside_row = str_replace('!!onto_row_content_linked_record_selector!!', $selector, $inside_row);
			$inside_row = str_replace("!!onto_row_content_marclist_range!!",$property->range[0] , $inside_row);
			
			$inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#record', $inside_row);
			
			$row = str_replace("!!onto_inside_row!!",$inside_row , $row);
			
			$input = '';
			$input.= $ontology_tpl['form_row_content_input_remove'];
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row = str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row = str_replace("!!onto_row_order!!","0" , $row);
				
			$content.= $row;
		}
		
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!", 'notice', $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
		
		return $form;
	} // end of member function get_form
	
	/**
	 * 
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété

	 * @param property property la propriété à utiliser

	 * @param string instance_name nom de l'instance

	 * @return string
	 * @access public
	 */
	public function get_display($datas, $property, $instance_name) {
		
		$display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
		$display.='<p>';
		$display.=$property->label.' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
	}

	
	protected function get_resource_selector_url($resource_uri){
		/**
		 * TODO: 
		 * Deux solutions possibles ?
		 * Générer Les urls côté php et concatener avec les variables spéciales issues du formulaire dans les fonctions JS ? 
		 * 	Ex: transmetre './select.php?what=notice&caller='; et passer les params directement dans la fonction js appelée à l'appui sur ajouter
		 *   -> Si l'on a qu'une fonction JS, ça impose de ressortir un type depuis le php ?!
		 *   	  
		 * 
		 *  
		 */		
		switch($resource_uri){
			case 'http://www.pmbservices.fr/ontology#record':
				$selector_url = './select.php?what=notice&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#author':
			case 'http://www.pmbservices.fr/ontology#responsability':
				$selector_url = './select.php?what=auteur&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#category':
				$selector_url = './select.php?what=categorie&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#publisher':
				$selector_url = './select.php?what=editeur&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#collection':
				$selector_url = './select.php?what=collection&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#sub_collection':
				$selector_url = './select.php?what=subcollection&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#serie':
				$selector_url = './select.php?what=serie&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#work':
				$selector_url = './select.php?what=titre_uniforme&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#indexint':
				$selector_url = './select.php?what=indexint&caller=';
				break;
			case 'http://www.w3.org/2004/02/skos/core#Concept':
				$selector_url = './select.php?what=ontology&objs=&element=concept&caller=';
				break;
			case 'http://www.pmbservices.fr/ontology#bulletin':
				$selector_url = './select.php?what=bulletin&caller=';
				break;
			default: 
				$selector_url = './select.php?what=ontologies&caller=';
				break; 
		}
		return $selector_url;
	}
	

	protected static function get_completion_from_range($range) {
		$completion = '';
		//on récupère le type de range en enlevant le préfixe propre à l'ontologie
		switch ($range) {
			case 'http://www.pmbservices.fr/ontology#linked_record' :
			case 'http://www.pmbservices.fr/ontology#record' :
				$completion = 'notice';
				break;
			case 'http://www.pmbservices.fr/ontology#author' :
			case 'http://www.pmbservices.fr/ontology#responsability' :
				$completion = 'authors';
				break;
			case 'http://www.pmbservices.fr/ontology#category' :
				$completion = 'categories';
				break;
			case 'http://www.pmbservices.fr/ontology#publisher' :
				$completion = 'publishers';
				break;
			case 'http://www.pmbservices.fr/ontology#collection' :
				$completion = 'collections';
				break;
			case 'http://www.pmbservices.fr/ontology#sub_collection' :
				$completion = 'subcollections';
				break;
			case 'http://www.pmbservices.fr/ontology#serie' :
				$completion = 'serie';
				break;
			case 'http://www.pmbservices.fr/ontology#work' :
				$completion = 'titre_uniforme';
				break;
			case 'http://www.pmbservices.fr/ontology#indexint' :
				$completion = 'indexint';
				break;
			case 'http://www.w3.org/2004/02/skos/core#Concept' :
				$completion = 'onto';
				break;
			default:
				$completion ='onto';
				break;
		}
		return $completion;
	}

} // end of onto_common_datatype_responsability_selector_ui
