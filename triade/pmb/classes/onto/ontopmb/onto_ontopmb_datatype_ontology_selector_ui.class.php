<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_ontology_selector_ui.class.php,v 1.2 2019/08/14 08:02:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once $include_path.'/templates/onto/ontopmb/onto_ontopmb_datatype_range_selector_ui.tpl.php';


/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_ontopmb_datatype_ontology_selector_ui extends onto_common_datatype_ui {

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


	
	public static function get_available_ontologies($property){
		$ontos = new ontologies();
		$ontologies = $ontos->get_other_ontologies();
		return $ontologies;
	}
	
	
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
		$form=str_replace("!!onto_row_label!!",htmlentities($property->get_label() ,ENT_QUOTES,$charset) , $form);
		$form=str_replace("!!onto_new_order!!","0" , $form);
		$options = "";

  		$ontologies = self::get_available_ontologies($property);
 		foreach($ontologies as $value => $label){
 			$selected = false;
 			if(count($datas)){
	 			foreach($datas as $data){
	 				if($value == $data->get_value()){
	 					$selected = true;
	 				}
	 			}
 			}
  			if($selected){
  				$options.="<option value='".$value."' selected='selected'>".htmlentities($label,ENT_QUOTES,$charset)."</option>";
  			}else{
  				$options.="<option value='".$value."'>".htmlentities($label,ENT_QUOTES,$charset)."</option>";
  			}
 		}
		$form=str_replace("!!onto_rows!!",$ontology_tpl['onto_ontopmb_datatype_range_selector_ui'], $form);
		$form=str_replace("!!options!!",$options, $form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
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
		$display.=$property->get_label().' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
		
	}
	
	public static function get_validation_js($item_uri,$property, $restrictions,$datas, $instance_name,$flag){
		global $msg;
		return '{
			"message": "'.addslashes($property->get_label()).'",
			"valid" : true,
			"error": "",
			"values": new Array(),
			"check": function(){
				this.valid = true;	
				var div_values = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_values");	
				while(div_values.childNodes.length>0){
					div_values.removeChild(div_values.firstChild);
				}
				var select = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_select");
				var literal_selected = false;	
				j=0;		
				for(var i=0 ; i< select.options.length ; i++){
					if(	select.options[i].selected){
						j++;
						if(select.options[i].value == "http://www.w3.org/2000/01/rdf-schema#Literal"){
							literal_selected = true;
						}
						var input = document.createElement("input");
						input.setAttribute("type","hidden");
						input.setAttribute("name","'.$instance_name.'_'.$property->pmb_name.'["+j+"][value]");
						input.value = select.options[i].value;
						div_values.appendChild(input);
					}
				}
				if(j>1 && literal_selected){
					this.valid = false;
					this.error = "only_literal";			
				}
				return this.valid;
			},
			"get_error_message": function(){
 				switch(this.error){
 					case "only_literal" :
						this.message = "'.addslashes($msg['onto_error_only_literal']).'";
						break;
 				}
				this.message = this.message.replace("%s","'.addslashes($property->get_label()).'");
				return this.message;
			}
		}';
	}

} // end of onto_common_datatype_resource_selector_ui
