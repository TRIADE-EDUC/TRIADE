<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_small_text_card_ui.class.php,v 1.12 2021/09/03 08:16:22 gneveu Exp $

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
	    global $msg,$charset,$ontology_tpl, $msg;

		$max = $restrictions->get_max();
		
		$form=$ontology_tpl['form_row_card'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		$form=str_replace("!!onto_input_type!!",htmlentities(self::$default_type ,ENT_QUOTES,$charset) , $form);
		
		$tab_lang = array();
		if ($property->multilingue && $property->is_cp()) {
		    // Champ perso multilingue
		    $lang = new marc_list('lang');
		    if (!empty($lang->table)) {
		        $tab_lang = $lang->table;
		    }
		    // Sans langue
		    $tab_lang[''] = htmlentities($msg['onto_common_datatype_ui_no_lang'], ENT_QUOTES, $charset);
		}
		
		if ($property->multilingue && $property->use_lang_concept) {
		    $tab_lang =  onto_common_datatype_ui::get_concepts_liste_trad();
		}
		
		$content='';
		$multilingue = "";
		
		if(!empty($datas)){
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
				$inside_row=$ontology_tpl['form_row_content_small_text_card'];
				$inside_row .= $ontology_tpl['form_row_content_type'];
				
				$inside_row=str_replace("!!onto_row_content_small_text_value!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$inside_row);
				$multilingue = "";
				if ($property->multilingue) {
				    $multilingue = self::get_combobox_lang($instance_name.'_'.$property->pmb_name.'['.$order.'][lang]',$instance_name.'_'.$property->pmb_name.'_'.$order.'_lang',$data->get_lang(), 1, '', $tab_lang);
				}
				$inside_row=str_replace("!!onto_row_combobox_lang!!", $multilingue, $inside_row);
				$inside_row=str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input='';
				if($first){
					if($restrictions->get_max()<$i || $restrictions->get_max()===-1){
						$input=$ontology_tpl['form_row_content_input_add'];
					}
				}else{
					$input=$ontology_tpl['form_row_content_input_del'];
				}
				
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
			
			// Un champ sans langue par défaut
			$row=$ontology_tpl['form_row_content'];			
			$inside_row=$ontology_tpl['form_row_content_small_text_card'];
			$inside_row .= $ontology_tpl['form_row_content_type'];
			
			$inside_row=str_replace("!!onto_row_content_small_text_value!!", "", $inside_row);
			$multilingue = "";
			if ($property->multilingue) {
			    $multilingue = self::get_combobox_lang($instance_name.'_'.$property->pmb_name.'[0][lang]',$instance_name.'_'.$property->pmb_name.'_0_lang', "", 1, '', $tab_lang);
			}
			$inside_row=str_replace("!!onto_row_combobox_lang!!", $multilingue, $inside_row);
			$inside_row=str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);				
			
			$input='';						
			if($restrictions->get_max()!=1){
				$input=$ontology_tpl['form_row_content_input_add'];
			}
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
			
			$row=str_replace("!!onto_row_inputs!!",$input , $row);			
			$row=str_replace("!!onto_row_order!!","0" , $row);			
			$content.=$row;
		}
		$onto_rows = "";
		$onto_rows.= $content;
		$form = str_replace("!!input_add!!", $ontology_tpl['form_row_content_input_add_card'], $form);
		$form = str_replace("!!onto_row_max_card!!", $max, $form);
		$form = str_replace("!!onto_rows!!", $onto_rows, $form);
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = self::get_form_with_special_properties($property, $datas, $instance_name, $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);
		
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
     				for (var i=0; i<=order.value; i++){
    					var label = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_"+i+"_value");
                        if(label && label.value != "") {
    						if (label.value.length > 511) {
    							this.valid = false;
    							this.error = "too_long";
    						}
    						this.nb_values++;
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
					case "too_long" : 
						this.message = "'.addslashes($msg['onto_error_too_long_value_small_text']).'";
						break;
 				}
				this.message = this.message.replace("%s","'.addslashes($property->get_label()).'");
				return this.message;
			} 	
		}';	
	}
	
} // end of onto_common_datatype_ui