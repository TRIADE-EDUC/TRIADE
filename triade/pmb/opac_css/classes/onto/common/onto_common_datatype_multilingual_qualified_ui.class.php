<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_multilingual_qualified_ui.class.php,v 1.2 2020/07/02 13:30:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_multilingual_qualified_ui
 * 
 */
class onto_common_datatype_multilingual_qualified_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	/*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propriété concernée
	 * @param restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag) {
		global $charset,$ontology_tpl;
		
		$form = $ontology_tpl['form_row'];
		$form = str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		$content='';
		
		$pattern = "/^([a-z]+_)/i";
		$matches = array();
		preg_match($pattern, $property->pmb_name, $matches);
		$name = preg_replace($pattern, "", $property->pmb_name); // nom du champ perso
		$prefix = str_replace("_", "", $matches[0]); // prefix de la table
		
		if (!empty($datas)) {
		    
		    $i=1;
			$new_element_order=max(array_keys($datas));
			$form = str_replace("!!onto_new_order!!", $new_element_order, $form);
			
			foreach ($datas as $key => $data ) {
			    $row=$ontology_tpl['form_row_content'];
			    
			    if($data->get_order()){
			        $order=$data->get_order();
			    }else{
			        $order=$key;
			    }
			    
			    $field = custom_parametres_perso::get_values_from_name($prefix, $name);
			    $maxsize = $field['OPTIONS']['MAXSIZE'][0]['value'];
			    
			    $formated_value = $data->get_formated_value();
			    $lang_selected = (!empty($formated_value[1]) ? $formated_value[1] : "");
			    $qualifications_selected = (!empty($formated_value[2]) ? $formated_value[2] : "");

			    $type = $field['OPTIONS']['TYPE'][0]['value'] ?? "text";
			    $tpl = "";
			    if ($type == "textarea") {
			        if ($field['DATATYPE'] == "small_text") {
			            /**
			             * si le datatype choisie est small_text,
			             * on force le maxlength à 200 pour pouvoir enregistrer
			             * la qualification et la langue.
			             */
			            $maxsize = 200;
			        }
			        $tpl = $ontology_tpl['onto_row_content_multilingual_qualified_textarea'];
			    } else {
			        $tpl = $ontology_tpl['onto_row_content_multilingual_qualified_text'];
			    }
			    
			    $inside_row = $ontology_tpl['form_row_content_multilingual_qualified'];
			    $inside_row = str_replace("!!onto_row_content_value_type!!", $tpl, $inside_row);
			    $inside_row = str_replace("!!multilingual_qualified_maxlength!!", $maxsize, $inside_row);
			    $inside_row = str_replace("!!onto_row_content_values!!", htmlentities($formated_value[0], ENT_QUOTES, $charset), $inside_row);
			    $inside_row = str_replace("!!onto_row_content_lang_options!!", self::get_options_lang($property, $lang_selected), $inside_row);
			    $inside_row = str_replace("!!onto_row_content_qualification_options!!", self::get_options_qualifications($field, $qualifications_selected), $inside_row);
			    $inside_row = str_replace("!!onto_row_content_multilingual_qualified_range!!", $property->range[0], $inside_row);
			    
			    $row=str_replace("!!onto_inside_row!!", $inside_row, $row);
			    
			    $input='';
			    if($i == 1 && ($restrictions->get_max() < $i || $restrictions->get_max()===-1)){
			        $input=$ontology_tpl['form_row_content_input_add_multilingual_qualified'];
			    } else {
			        $input=$ontology_tpl['form_row_content_input_del'];
			    }
			    
			    $row=str_replace("!!onto_row_inputs!!", $input, $row);
			    $row=str_replace("!!onto_row_order!!", $order, $row);
			    
			    $content.=$row;
			    $i++;
			}
		} else {
		    
		    $order = 0;
			$form = str_replace("!!onto_new_order!!", "0", $form);
			$row = $ontology_tpl['form_row_content'];
			
			$field = custom_parametres_perso::get_values_from_name($prefix, $name);
			$maxsize = $field['OPTIONS']['MAXSIZE'][0]['value'];
			
			$type = $field['OPTIONS']['TYPE'][0]['value'] ?? "text";
			$tpl = "";
			if ($type == "textarea") {
			    if ($field['DATATYPE'] == "small_text") {
			        /**
			         * si le datatype choisie est small_text,
			         * on force le maxlength à 200 pour pouvoir enregistrer
			         * la qualification et la langue.
			         */ 
			        $maxsize = 200;
			    }
			    $tpl = $ontology_tpl['onto_row_content_multilingual_qualified_textarea'];
			} else {
			    $tpl = $ontology_tpl['onto_row_content_multilingual_qualified_text'];
			}
			
			$inside_row = $ontology_tpl['form_row_content_multilingual_qualified'];
			$inside_row = str_replace("!!onto_row_content_value_type!!", $tpl, $inside_row);
			$inside_row = str_replace("!!multilingual_qualified_maxlength!!", $maxsize, $inside_row);
			$inside_row = str_replace("!!onto_row_content_values!!", "", $inside_row);
			$inside_row = str_replace("!!onto_row_content_lang_options!!", self::get_options_lang($property), $inside_row);
			$inside_row = str_replace("!!onto_row_content_qualification_options!!", self::get_options_qualifications($field), $inside_row);
			$inside_row = str_replace("!!onto_row_content_multilingual_qualified_range!!", $property->range[0], $inside_row);
			
			$row=str_replace("!!onto_inside_row!!", $inside_row, $row);
			$row=str_replace("!!onto_row_inputs!!", $input, $row);
			$row=str_replace("!!onto_row_order!!", $order, $row);
			
			$content = $row;
		}
		
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		$editor_class = 'editor_'.(explode('_', $instance_name)[0].'_').$property->pmb_name;
		$form=str_replace("!!editor_class!!", $editor_class, $form);
		
		return $form;
	} // end of member function get_form

	public static function get_options_qualifications($field, $selected_qualifications = "") {
	    global $charset;
	    
	    $field_options = $field['OPTIONS'];
	    $field_options['ITEMS'] = self::get_items($field);
	    
	    // Option par defaut
	    $options = "<option value='' ".(empty($selected_qualifications) ? "selected" : "").">";
	    $options .= htmlentities($field_options['UNSELECT_ITEM'][0]['value'], ENT_QUOTES, $charset);
	    $options .= "</option>";
	    
	    // On ajoute les options
        foreach ($field_options['ITEMS'] as $item) {
            $options.="<option value='".htmlentities($item['value'], ENT_QUOTES, $charset)."' ".($item['value'] == $selected_qualifications ? "selected" : "").">";
            $options.= htmlentities($item['label'], ENT_QUOTES, $charset);
            $options.= "</option>";
	    }
	    
	    return $options;
	}
	
	public static function get_items($field) {
	    
	    $query = "SELECT ".$field["PREFIX"]."_custom_list_value, ".$field["PREFIX"]."_custom_list_lib ";
	    $query .= "FROM ".$field["PREFIX"]."_custom_lists WHERE ".$field["PREFIX"]."_custom_champ=".$field['ID']." ORDER BY ordre";
	    
	    $results = pmb_mysql_query($query);
	    $items = array();
	    
	    if (!empty($results)) {
	        $i = 0;
	        while ($r = pmb_mysql_fetch_array($results)) {
	            $items[$i]['value'] = $r[$field["PREFIX"]."_custom_list_value"];
	            $items[$i]['label'] = $r[$field["PREFIX"]."_custom_list_lib"];
	            $i++;
	        }
	    }
	    return $items;
	}
	
	public static function get_options_lang($property, $selected_value = "") {
	    global $charset, $msg;
	    
	    $marc_list = new marc_list("lang");
	    $list_values_to_display = static::get_list_values_to_display($property);
	    
	    // Option par defaut
	    $options = "<option value='' ".(empty($selected_value) ? "selected" : "").">";
	    $options .= htmlentities($msg['onto_common_datatype_ui_no_lang'], ENT_QUOTES, $charset);
	    $options .= "</option>";
	    
	    foreach($marc_list->table as $value => $label){
	        if (count($list_values_to_display) && !in_array($value, $list_values_to_display)) {
	            continue;
	        }
	        $selected = ($selected_value == $value ? "selected=''" : '');
	        $options.= '<option value="'.htmlentities($value, ENT_QUOTES, $charset).'" '.$selected.' >'.
	   	        htmlentities($label, ENT_QUOTES, $charset)
	   	        .'</option>';
	    }
	    return $options;
	}
	
	
	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
	    return array();
	}
	
	
	/**
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

} // end of onto_common_datatype_ui