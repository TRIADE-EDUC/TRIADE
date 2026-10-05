<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_docnum_licence_ui.class.php,v 1.5 2021/08/13 08:35:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');
require_once($class_path.'/encoding_normalize.class.php');

class onto_contribution_datatype_docnum_licence_ui extends onto_common_datatype_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
    static protected $licence_data;

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
	static public function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
		global $msg,$charset,$ontology_tpl, $ontology_contribution_tpl;		
		
		$form=$ontology_tpl['form_row'];
		$form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
		
		static::get_licence_data();
		
		$content='';
		if (!empty($datas)) {
			$i=1;
			$first=true;
			$new_element_order=max(array_keys($datas));

			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
			foreach ($datas as $data) {
                $row=$ontology_tpl['form_row_content_without_flex'];
				$inside_row = $ontology_contribution_tpl['form_row_content_licence'];
				
				$inside_row=str_replace("!!form_row_content_licence_value!!",$data->get_value(), $inside_row);
				$inside_row=str_replace('!!onto_row_licence_data!!', encoding_normalize::json_encode(static::$licence_data), $inside_row);
				$inside_row=str_replace("!!onto_row_content_list_range!!",$property->range[0] , $inside_row);
				
				$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
				$row=str_replace("!!onto_row_inputs!!",'' , $row);
		
				$row=str_replace("!!onto_row_order!!",0 , $row);
		
				$content.=$row;
				$first=false;
				$i++;
			}
		}else{
			$form=str_replace("!!onto_new_order!!", "0", $form);
				
            $row=$ontology_tpl['form_row_content_without_flex'];
				
			$inside_row = $ontology_contribution_tpl['form_row_content_licence'];
			$inside_row=str_replace("!!form_row_content_licence_value!!","", $inside_row);
			$inside_row=str_replace('!!onto_row_licence_data!!', encoding_normalize::json_encode(static::$licence_data), $inside_row);
			$inside_row=str_replace("!!onto_row_content_list_range!!",$property->range[0] , $inside_row);
				
			$row=str_replace("!!onto_inside_row!!",$inside_row , $row);
			$row=str_replace("!!onto_row_inputs!!",'' , $row);
			$row=str_replace("!!onto_row_order!!","0" , $row);
				
			$content.=$row;
		}
		
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
		
		return $form;
		
	} // end of member function get_form

	public static function get_licence_data() {
		if (isset(static::$licence_data)) {
		    return static::$licence_data;
		}
		static::$licence_data = [];
		$query = "SELECT * FROM explnum_licence_profiles P 
            JOIN explnum_licence L ON P.explnum_licence_profile_explnum_licence_num = L.id_explnum_licence 
            ORDER BY P.explnum_licence_profile_label";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_assoc($result)) {
		        if (!isset(static::$licence_data[$row["id_explnum_licence"]])) {
		            static::$licence_data[$row["id_explnum_licence"]] = [
		                "id" => $row["id_explnum_licence"],
		                "label" => $row["explnum_licence_label"],
		                "profiles" => [],
		            ];
		        }
		        static::$licence_data[$row["id_explnum_licence"]]["profiles"][$row["id_explnum_licence_profile"]] = [
		            "id" => $row["id_explnum_licence_profile"],
		            "label" => $row["explnum_licence_profile_label"],
		            "logo" => $row["explnum_licence_profile_logo_url"],
		            "explanation" => $row["explnum_licence_profile_explanation"],
		        ];
		    }
		}
		return static::$licence_data;
	}
	
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
     * Retourne un object JSON avec 2 méthodes check et get_error_message
     *
     * @param property property la propriété concernée
     * @param restriction $restrictions le tableau des restrictions associées à la propriété
     * @param array datas le tableau des datatypes
     * @param string instance_uri URI de l'instance
     * @param string flag Flag
     *
     * @return string
     * @static
     * @access public
     */
    public static function get_validation_js($item_uri,$property, $restrictions,$datas, $instance_name,$flag){
        global $msg;
        
        $mandatory = isset($property->pmb_extended['mandatory']) ? 'true' : 'false';
        return '{
			"message": "'.addslashes($property->get_label()).'",
			"valid" : true,
			"nb_values": 0,
			"error": "",
			"is_required": '.$mandatory.',   
			"values": new Array(),
			"check": function() {
                var elementName = "'.$instance_name.'_'.$property->pmb_name.'_0_licence";
                var nodeOrder = document.getElementById(elementName);

                //On ne peut pas récupérer les input qui nous interessent (id aléatoires, pas de classe), on doit contourner
                var div = document.querySelector("#'.$instance_name.'_'.$property->pmb_name.'_0_profil_selector");
                var spans = div.querySelectorAll("span");

                if (spans.length > 0) {
                    for (let i=0; i < spans.length; i++) {
                        let input = spans[i].querySelector("input");
                        //on vérifie qu une valeur à été choisie dans les propositions
                        if (input.checked) {
                            return true;
                        }
                    }
                }
                
                if (this.is_required == true) {
                    this.error = "min";
                    return false;
                }
                return true;
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
}