<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_list_ui.class.php,v 1.7 2019-04-19 14:33:32 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_list_ui
 * 
 */
class onto_common_datatype_list_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * 
	 *
	 * @param property property la propriété concernée
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
		
		
		$form = $ontology_tpl['form_row'];
		$form = str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->label, 'utf-8') ,ENT_QUOTES,$charset) , $form);	
		
		$options_values = array();
		if (isset($property->pmb_list_item)) {
			foreach ($property->pmb_list_item as $list_item) {
				$options_values[$list_item['id']] = $list_item['value'];
			}
			
		}
		if (isset($property->pmb_list_query)) {
			$result = pmb_mysql_query($property->pmb_list_query);
			if (pmb_mysql_num_rows($result)) {
    			while ($row = pmb_mysql_fetch_array($result)) {
    				$options_values[$row[0]] = $row[1];
    			}
			}
		}
		
		$content='';	
		$form=str_replace("!!onto_new_order!!",0 , $form);
	
		$row=$ontology_tpl['form_row_content'];
		
		$cp_options = array();
		if (!empty($property->cp_options)) {
			$cp_options = encoding_normalize::json_decode($property->cp_options, true);
		}
		if (!empty($cp_options) && !empty($cp_options["CHECKBOX"]) && $cp_options["CHECKBOX"][0]["value"] == "yes") {
			$inside_row = static::get_checkbox_form($item_uri, $property, $restrictions, $datas, $options_values);
		} else {
			$inside_row = static::get_selector_form($item_uri, $property, $restrictions, $datas, $options_values);
		}

		$row=str_replace("!!onto_inside_row!!", $inside_row , $row);

		$input='';

		$row=str_replace("!!onto_row_inputs!!",$input , $row);
		$row=str_replace("!!onto_row_order!!",0 , $row);

		$content.=$row;
				
		$form=str_replace("!!onto_rows!!",$content ,$form);
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
		$display.=$property->label.' : ';
		foreach($datas as $data){
			$display.=$data->get_formated_value();
		}
		$display.='</p>';
		$display.='</div>';
		return $display;
	} // end of member function get_display
	
	public static function get_selector_form($item_uri, $property, $restrictions, $datas, $options_values) {
		global $ontology_tpl, $charset;
		
		$inside_row = ($restrictions->get_max() != 1 ? $ontology_tpl['form_row_content_list_multi'] : $ontology_tpl['form_row_content_list']);
		$inside_row.= $ontology_tpl['form_row_content_type'];
		
		$list_values_to_display = static::get_list_values_to_display($property);
		
		$options = '';
		$values = array();
		if (empty($datas)) {
			$datas = array();
		}
		foreach($datas as $key=>$data){
			$formated_value = $data->get_formated_value();
			if (is_array($formated_value)) {
				$values = array_merge($values, $formated_value);
			} else {
				$values[] = $formated_value;
			}
		}
		foreach($options_values as $id => $value){
			if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
				continue;
			}
			$options.= '<option value="'.htmlentities($id, ENT_QUOTES, $charset).'" '.(in_array($id, $values) ? 'selected="selected"' : '').'>'.htmlentities($value, ENT_QUOTES, $charset).'</option>';
		}
		/*generate rows *///htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset)
		$inside_row = str_replace("!!onto_row_content_list_options!!", $options, $inside_row);
		$inside_row = str_replace("!!onto_row_content_range!!",$property->range[0] , $inside_row);
		
		return $inside_row;
	}
	
	public static function get_checkbox_form($item_uri, $property, $restrictions, $datas, $options_values) {
		global $ontology_tpl, $charset;
		
		$radio_or_checkbox = ($restrictions->get_max() != 1 ? "checkbox" : "radio");
		$list_values_to_display = static::get_list_values_to_display($property);
		
		$cp_options = array();
		if (!empty($property->cp_options)) {
			$cp_options = encoding_normalize::json_decode($property->cp_options, true);
		}
		$nb_per_line = 3;
		if (!empty($cp_options) && !empty($cp_options["CHECKBOX_NB_ON_LINE"])) {
			$nb_per_line = $cp_options["CHECKBOX_NB_ON_LINE"][0]["value"];
		}
		
		$values = array();
		if (empty($datas)) {
			$datas = array();
		}
		foreach($datas as $key=>$data){
			$formated_value = $data->get_formated_value();
			if (is_array($formated_value)) {
				$values = array_merge($values, $formated_value);
			} else {
				$values[] = $formated_value;
			}
		}
		$search = array(
				"!!radio_or_checkbox!!",
				"!!onto_row_content_value!!",
				"!!onto_checked!!",
				"!!onto_row_content_label!!",
				"!!onto_row_content_value_index!!"
		);
		$i = 0;
		$inside_row = "<table><tr>";
		foreach($options_values as $id => $value){
			if (count($list_values_to_display) && !in_array($id, $list_values_to_display)) {
				continue;
			}
			if ($i && !($i%$nb_per_line)) {
				$inside_row.= "</tr><tr>";
			}
			$replace = array(
					$radio_or_checkbox,
					htmlentities($id, ENT_QUOTES, $charset),
					(in_array($id, $values) ? 'checked="checked"' : ''),
					htmlentities($value, ENT_QUOTES, $charset),
					$i
			);
			$inside_row.= "<td>".str_replace($search, $replace, $ontology_tpl["form_row_content_list_checkbox_option"])."</td>";
			$i++;
		}
		$inside_row.= "<tr></table>";
		
		$inside_row.= $ontology_tpl['form_row_content_type'];
		$inside_row.= $ontology_tpl['form_row_content_list_checkbox'];
		$inside_row = str_replace("!!onto_row_content_values!!", implode(",", $values), $inside_row);
		
		$inside_row = str_replace("!!onto_row_content_range!!", $property->range[0], $inside_row);
		
		return $inside_row;
	}
	
	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return array();
	}

} // end of onto_common_datatype_ui