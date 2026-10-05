<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_small_text_card_ui.class.php,v 1.17 2021/06/09 13:28:40 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_small_text_card_ui
 * 
 */
class onto_common_datatype_small_text_card_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	private static $default_type="http://www.w3.org/2000/01/rdf-schema#Literal";

	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl,$lang, $combobox_lang;
		
		if($lang) {
			$current_lang = substr($lang, 0, 2);
		} else {
			$current_lang = "";
		}
		
		//$tab_lang=array(0=>$msg["onto_common_datatype_ui_no_lang"],'fr'=>$msg["onto_common_datatype_ui_fr"],'en'=>$msg["onto_common_datatype_ui_en"]);
		$tab_lang = self::get_concepts_liste_trad();
		$max = $restrictions->get_max();
		foreach ($tab_lang as $key => $label) {
			$available_lang[$key] = $max;
		}
		
		$form=$ontology_tpl['form_row_card'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		$form=str_replace("!!onto_input_type!!",htmlentities(self::$default_type ,ENT_QUOTES,$charset) , $form);
		
		$content='';
		
		if (!empty($datas)) {
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));
			
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			
			foreach($datas as $key=>$data){
				// On décrémente le tableau des langues disponibles
				if ($data->get_lang() && isset($available_lang[$data->get_lang()])) {
					$available_lang[$data->get_lang()]--;
				} else {
					$available_lang[0]--;
				}
				
				$row=$ontology_tpl['form_row_content'];
				
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				$inside_row=$ontology_tpl['form_row_content_small_text_card'];
				
				$inside_row=str_replace("!!onto_row_content_small_text_value!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$inside_row);
				$inside_row=str_replace("!!label_lang!!",($tab_lang[$data->get_lang()] ? "(".$tab_lang[$data->get_lang()].")" : ""), $inside_row);
				$inside_row=str_replace("!!onto_row_content_small_text_lang!!",($data->get_lang() ? $data->get_lang() : ""), $inside_row);
				$inside_row=str_replace("!!onto_row_content_small_text_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input=$ontology_tpl['form_row_content_input_del_card'];
				
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","1" , $form);
			
			// Et un champ de la langue courante
			if($current_lang){
			    
			    $row=$ontology_tpl['form_row_content'];
			    
			    $inside_row=$ontology_tpl['form_row_content_small_text_card'];
			    $inside_row=str_replace("!!onto_row_content_small_text_value!!", "", $inside_row);
                
			    $message = "onto_common_datatype_ui_" . $current_lang;
			    $inside_row=str_replace("!!label_lang!!",($tab_lang[$current_lang] ? "(".$tab_lang[$current_lang].")" : "(".$msg[$message].")"), $inside_row);
			    
			    $inside_row=str_replace("!!onto_row_content_small_text_lang!!",($current_lang ? $current_lang : "0"), $inside_row);
			    $inside_row=str_replace("!!onto_row_content_small_text_range!!",$property->range[0] , $inside_row);
			    
			    $available_lang[$current_lang]--;
			    
			    $row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			    
			    $input=$ontology_tpl['form_row_content_input_del_card'];
			    
			    $row=str_replace("!!onto_row_inputs!!",$input , $row);
			    
			    $row=str_replace("!!onto_row_order!!","0" , $row);
			    
			    $content.=$row;
			}
			
			// Un champ sans langue par défaut si défini dans le paramétre concepts_liste_trad
			if (isset($tab_lang['no'])) {
    			$row=$ontology_tpl['form_row_content'];
    			
    			$inside_row=$ontology_tpl['form_row_content_small_text_card'];
    			
    			$inside_row=str_replace("!!onto_row_content_small_text_value!!", "", $inside_row);
    			$inside_row=str_replace("!!label_lang!!", "(".$msg["onto_common_datatype_ui_no_lang"].")", $inside_row);
    			$inside_row=str_replace("!!onto_row_content_small_text_lang!!", "no", $inside_row);
    			$inside_row=str_replace("!!onto_row_content_small_text_range!!",$property->range[0] , $inside_row);
    			
    			$available_lang['no']--;
    			
    			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
    				
    			$input=$ontology_tpl['form_row_content_input_del_card'];
    			
    			$row=str_replace("!!onto_row_inputs!!",$input , $row);
    			
    			$row=str_replace("!!onto_row_order!!","1" , $row);
    			
    			$content.=$row;
			}
			
		}
		$onto_rows = "";
		
		// On regarde quelles langues sont ajoutables
		$possible_lang = array();
		foreach ($available_lang as $key => $nb) {
			if ($available_lang[$key] > 0) {
				$possible_lang[$key] = $tab_lang[$key];
			}
		}
		
		$onto_rows.= $combobox_lang;
		$onto_rows.= $content;
		
		// Gestion du combobox de langues
		$form = str_replace("!!onto_row_combobox_lang!!", self::get_combobox_lang($instance_name.'_'.$property->pmb_name.'_select_lang',$instance_name.'_'.$property->pmb_name.'_select_lang',$current_lang, 1, "", $possible_lang), $form);
		$form = str_replace("!!input_add!!", $ontology_tpl['form_row_content_input_add_card'], $form);
		$form = str_replace("!!onto_row_max_card!!", $max, $form);
		$form = str_replace("!!tab_available_lang!!", json_encode($available_lang), $form);
		$form = str_replace("!!tab_lang_label!!", json_encode(encoding_normalize::utf8_normalize($tab_lang)), $form);
		
		$form=str_replace("!!onto_rows!!",$onto_rows ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
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
	
	/**
	 *
	 *
	 * @param property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag
	
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_validation_js($item_uri,$property, $restrictions,$datas, $instance_name,$flag){
		global $msg;
		return '{
			"message": "'.addslashes($property->get_label()).'",
			"valid" : true,
			"nb_values": 0,
			"error": "",
			"values": new Array(),
			"check": function(){
				this.values = new Array();
				this.nb_values = 0;
				this.valid = true;
				var order = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_new_order");
                if (order) {
    				for (var i=0; i<=order.value ; i++){
    					var label = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_"+i+"_value");
    					var lang = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_"+i+"_lang");
    					if(lang && label.value != ""){
    						if(!this.values[lang.value]){
    							this.values[lang.value] = 0;
    						}
    						this.values[lang.value]++;
    						if(this.nb_values < this.values[lang.value]) {
    							this.nb_values = this.values[lang.value];
    						}
    					}
                    }
				}
							
				if(this.nb_values < '.$restrictions->get_min().'){
					this.valid = false;
					this.error = "min";
				}
				if(this.nb_values > '.$restrictions->get_max().'){
					this.valid = false;
					this.error = "max";
				}
				return this.valid;
			},
			"get_error_message": function(){
 				switch(this.error){
 					case "min" :
						this.message = "'.addslashes($msg['onto_error_no_minima']).'";
						break;
					case "max" : 
						this.message = "'.addslashes($msg['onto_error_too_much_values']).'";
						break;
 				}
				this.message = this.message.replace("%s","'.addslashes($property->get_label()).'");			
				return this.message;	
			} 	
		}';	
	}
	
} // end of onto_common_datatype_ui