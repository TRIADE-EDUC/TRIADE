<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_url_ui.class.php,v 1.3 2017-05-31 14:43:29 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_common_datatype_small_text_ui
 * 
 */
class onto_common_datatype_url_ui extends onto_common_datatype_ui {
	
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
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities($property->label ,ENT_QUOTES,$charset) , $form);
		//A voir pour rendre le champs répetable.. 
// 		if($restrictions->get_max()<$i || $restrictions->get_max()===-1){
// 			$form.=$ontology_tpl['form_row_content_input_add'];
// 		}
		$content='';
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
				$inside_row=$ontology_tpl['form_row_content_url'];
				
				if($restrictions->get_max() > 1 || $restrictions->get_max() == -1){
					if($i == 1){
						$inside_row=str_replace("!!onto_url_add_button!!",$ontology_tpl['form_row_content_input_add_url'],$inside_row);
					}else{
						$inside_row=str_replace("!!onto_max_value!!",'',$inside_row);
						$inside_row=str_replace("!!onto_url_add_button!!",$ontology_tpl['form_row_content_input_del_url'],$inside_row);
					}
				}else{
					$inside_row=str_replace("!!onto_url_add_button!!",'',$inside_row);
				}
				$inside_row=str_replace("!!onto_max_value!!",$ontology_tpl['form_row_content_url_max_value'],$inside_row);
				$inside_row=str_replace("!!onto_restrict_max_value!!",$restrictions->get_max(),$inside_row);
				$inside_row=str_replace("!!onto_row_content_url_value!!",htmlentities($data->get_formated_value() ,ENT_QUOTES,$charset) ,$inside_row);
				$inside_row=str_replace("!!onto_row_content_url_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				
				$input='';
// 				if($first){
// 					//Todo; replace by del sur first elem (voir resource_selector_ui)
// 				}else{
// 					$input=$ontology_tpl['form_row_content_input_del'];
// 				}
				
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
				$row=str_replace("!!onto_row_order!!",$order , $row);
				
				/**
				 * TODO: ajout de champs, vérifs avec new order + ajouter le nombre max (récupérer la restriction ou la vérifier avec le vbalidation js)
				 * ajouter la purge du champs également
				 */
				
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!","0" , $form);
			
			$row=$ontology_tpl['form_row_content'];
			
			$inside_row=$ontology_tpl['form_row_content_url'];
			if($restrictions->get_max() > 1 || $restrictions->get_max() == -1){
				$inside_row=str_replace("!!onto_url_add_button!!",$ontology_tpl['form_row_content_input_add_url'],$inside_row);
			}else{
				$inside_row=str_replace("!!onto_url_add_button!!",'',$inside_row);
			}
			$inside_row=str_replace("!!onto_max_value!!",$ontology_tpl['form_row_content_url_max_value'],$inside_row);
			$inside_row=str_replace("!!onto_restrict_max_value!!",$restrictions->get_max(),$inside_row);
			
			$inside_row=str_replace("!!onto_row_content_url_value!!","" , $inside_row);
			$inside_row=str_replace("!!onto_row_content_url_range!!",$property->range[0] , $inside_row);
			
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$input='';
// 			if($restrictions->get_max()!=1){
				//$input=$ontology_tpl['form_row_content_input_add'];
				//Replace par del sur first element (voir resource_selector_ui)
// 			}
			$row=str_replace("!!onto_row_inputs!!",$input , $row);
			
			$row=str_replace("!!onto_row_order!!","0" , $row);
			
			$content.=$row;
		}
		
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

	/**
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
			"message": "'.addslashes($property->label).'",
			"valid" : true,
			"nb_values": 0,
			"error": "",
			"values": new Array(),
			"check": function(){
				this.values = new Array();
				this.nb_values = 0;
				this.valid = true;
				var order = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_new_order").value;
				for (var i=0; i<=order ; i++){
					var label = document.getElementsByName("'.$instance_name.'_'.$property->pmb_name.'["+i+"][value]")[0];
					if(label && label.value != ""){
						this.nb_values++;
					}
				}
				
				if(this.nb_values < '.$restrictions->get_min().'){
					this.valid = false;
					this.error = "min";
				}
				if('.$restrictions->get_max().' > -1 && this.nb_values > '.$restrictions->get_max().'){
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
				this.message = this.message.replace("%s","'.addslashes($property->label).'");
				return this.message;
			}
		}';
	}
} // end of onto_common_datatype_url_ui