<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_responsability_selector_ui.class.php,v 1.4 2019-04-04 13:45:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path.'/authority.class.php';
require_once $class_path.'/notice.class.php';
/**
 * class onto_common_datatype_responsability_selector_ui
 * 
 */
class onto_contribution_datatype_responsability_selector_ui extends onto_common_datatype_resource_selector_ui {

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
		/** **/
		
		/** TODO: à revoir avec le chef ** / 
		/** On part du principe que l'on a qu'un range **/
// 		$selector_url = $this->get_resource_selector_url($property->range[0]);
		
		$marc_list = marc_list_collection::get_instance('function');
		
		$content='';
		//$content.=$ontology_tpl['form_row_content_input_sel'];
		
		$content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
		if(sizeof($datas)){
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$formated_value = $data->get_formated_value();
				
				$inside_row=$ontology_tpl['form_row_content_responsability_selector'];
				$inside_row=str_replace("!!form_row_content_responsability_selector_display_label!!",htmlentities((isset($formated_value['author']['display_label']) ? $formated_value['author']['display_label'] : ""), ENT_QUOTES, $charset) , $inside_row);
				$inside_row=str_replace("!!form_row_content_responsability_selector_value!!", (isset($formated_value['author']['value']) ? $formated_value['author']['value'] : ""), $inside_row);
				$inside_row=str_replace("!!form_row_content_responsability_selector_range!!",$data->get_value_type() , $inside_row);
				
				$options = '';
				foreach($marc_list->table as $value => $label){
				    $options.= '<option value="'.$value.'" '.(isset($formated_value['author_function']) && $formated_value['author_function'] == $value ? 'selected=selected>' : '>').htmlentities($label,ENT_QUOTES,$charset).'</option>';
				}
				/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
				$inside_row=str_replace('!!onto_row_content_marclist_options!!', $options, $inside_row);
				
				$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
				$inside_row=str_replace("!!onto_current_range!!",$data->get_value_type(),$inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input='';
				if($first){
					$input.=$ontology_tpl['form_row_content_input_remove'];
				}else{
					$input.=$ontology_tpl['form_row_content_input_del'];
				}
				
				if ($property->has_linked_form && $first) {
				    $access_granted = true;
				    if (onto_common_uri::is_temp_uri($item_uri)) {
				        //droit de creation
				        $acces_right = 4;
				    } else {
				        //droit de modification
				        $acces_right = 8;
				    }
				    if (isset($dom_5) && !$dom_5->getRights($_SESSION['id_empr_session'],onto_common_uri::get_id($property->linked_form['scenario_uri']), $acces_right)) {
				        $access_granted = false;
				    }
				    if ($access_granted) {
				        $input .= $ontology_tpl['form_row_content_linked_form'];
				        $url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form['form_type'].'&area_id='.$property->linked_form['area_id'].'&id='.onto_common_uri::get_id($data->get_raw_value()).'&sub_form=1&form_id='.$property->linked_form['form_id'].'&form_uri='.urlencode($property->linked_form['form_id_store']);
				        $input = str_replace("!!url_linked_form!!", $url, $input);
				        $input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
				    }
				}
				
				$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
				
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
			
			$row=$ontology_tpl['form_row_content'];
			
			$inside_row=$ontology_tpl['form_row_content_responsability_selector'];			
			$inside_row=str_replace("!!form_row_content_responsability_selector_display_label!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_responsability_selector_value!!","" , $inside_row);
			$inside_row=str_replace("!!form_row_content_responsability_selector_range!!","" , $inside_row);
			$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
			$inside_row=str_replace("!!onto_current_range!!",'',$inside_row);
			
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);			
			
			$input='';
			$input.=$ontology_tpl['form_row_content_input_remove'];
			$input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		$form = str_replace("!!onto_rows!!", $content, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_completion!!",'authors', $form);
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
