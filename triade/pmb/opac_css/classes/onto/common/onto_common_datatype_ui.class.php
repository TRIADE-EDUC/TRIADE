<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.class.php,v 1.18 2023/07/26 12:12:59 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/onto_root_ui.class.php');
require_once($class_path.'/onto/common/onto_common_datatype.class.php');
require_once($include_path.'/templates/onto/common/onto_common_datatype_ui.tpl.php');
require_once ($class_path.'/encoding_normalize.class.php');


/**
 * class onto_common_datatype_ui
 *
 */
abstract class onto_common_datatype_ui extends onto_root_ui{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 *
	 *
	 * @param property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_uri URI de l'instance
	 * @param string flag Flag
	 *
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
	}

	/**
	 *
	 *
	 * @param onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
	 * @param property property la propriété à utiliser
	 * @param string instance_uri URI de l'instance (item)
	 *
	 * @return string
	 * @access public
	 */
	abstract public function get_display($datas, $property, $instance_name);


	/**
	 * Retourne un object JSON avec 2 méthodes check et get_error_message
	 *
	 * @param property property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
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
				var nodeOrder = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_new_order");
                if (nodeOrder) {
                    var order = nodeOrder.value;
    				for (var i=0; i<=order ; i++){
    					var label = document.getElementById("'.$instance_name.'_'.$property->pmb_name.'_"+i+"_value");
                        if(!label) continue;
    					var key = 0;
    					if((label.value != "") || (label.defaultValue != "")){
    						if(!this.values[key]){
    							this.values[key] = 0;
    						}
    						this.values[key]++;

    						if(this.nb_values < this.values[key]) {
    							this.nb_values = this.values[key];
    						}
    					}
    				}
                }
				if(this.nb_values < '.$restrictions->get_min().'){
					this.valid = false;
					this.error = "min";
				}
				if('.$restrictions->get_max().' != -1 && this.nb_values > '.$restrictions->get_max().'){
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

	public static function get_combobox_lang($name,$id,$current_lang='',$size=1,$onchange='', $tab_lang = array()) {
	    global $charset, $msg;

	    if (!count($tab_lang) && false == strpos($name,"preflabel_select_lang" )){
	        $tab_lang= self::get_concepts_liste_trad();
	    }

	    $combobox='';
	    $combobox.='<select onchange="'.$onchange.'" name="'.$name.'" id="'.$id.'" size="'.$size.'">';
	    foreach($tab_lang as $key=>$lang){
	        if($key==$current_lang){
	            $combobox.='<option selected value="'.$key.'">'.htmlentities($lang, ENT_QUOTES, $charset).'</option>';
	        }else{
	            $combobox.='<option value="'.$key.'">'.htmlentities($lang,ENT_QUOTES,$charset).'</option>';
	        }
	    }

	    $combobox.='</select>';

	    return $combobox;
	}

	public static function get_hidden_fields($property,$datas, $instance_name, $flag = false) {
		global $msg,$charset,$ontology_tpl;

		$form=$ontology_tpl['form_row_hidden'];

		$content = '';
		if ( !empty($datas) && is_array($datas) ) {
			$new_element_order = max(array_keys($datas));

			$form = str_replace("!!onto_new_order!!",$new_element_order , $form);

			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content_hidden'];

				if($data->get_order()){
					$order = $data->get_order();
				}else{
					$order = $key;
				}
				$value = $data->get_value();
				$row = str_replace("!!onto_row_content_hidden_value!!", htmlentities((is_array($value) ? implode(',', $value) : $value),ENT_QUOTES,$charset), $row);
				$row = str_replace("!!onto_row_content_hidden_lang!!", $data->get_lang(), $row);
				$row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
				$row = str_replace("!!onto_row_order!!", $order, $row);

				$content.= $row;
			}
		} else {
			$form = str_replace("!!onto_new_order!!","0" , $form);

			$row = $ontology_tpl['form_row_content_hidden'];
			$row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_lang!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
			$row=str_replace("!!onto_row_order!!", "0", $row);

			$content.=$row;
		}

		if ($flag) {
			$form = $content;
		} else {
			$form = str_replace("!!onto_rows!!", $content, $form);
		}
		$form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
		$form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);

		return $form;
	}

	public static function get_form_with_special_properties($property,$datas, $instance_name, $form) {
		$onto_disabled = '';
		$onto_input_props = '';

		if (!empty($property->pmb_extended['readonly'])) {
			$onto_input_props = 'readonly="readonly"';
			if (strpos($form,'!!onto_disabled!!')) {
				$onto_disabled = 'disabled="disabled"';
				/**
				 * on a besoin du champ new order
				 */
				$form = str_replace("!!onto_row_id!!_new_order",$instance_name.'_'.$property->pmb_name.'_new_order' , $form);
				/**
				 * les autres id doivent être 'disabled' pour ne pas entrer en conflit avec ceux des champs cachés ajoutés par la suite
				 */
				$form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name.'_disabled' , $form);
				/**
				 * on rajoute des champs cachés pour pouvoir poster un valeur quand même
				 */
				$form .= static::get_hidden_fields($property, $datas, $instance_name, true);
			}
		}

		if (!empty($property->pmb_extended['placeholder'])) {
		    $property->pmb_extended['placeholder'] = onto_common_ui::get_message($property->pmb_extended['placeholder']);
			if ($onto_input_props) {
				$onto_input_props.= ' ';
			}
			$onto_input_props.= 'placeholder="'.$property->pmb_extended['placeholder'].'"';
		}

		$form = str_replace("!!onto_disabled!!", $onto_disabled, $form);
		$form = str_replace("!!onto_input_props!!", $onto_input_props, $form);
		return $form;
	}

	public static function get_scripts() {
		$scripts = static::get_field_change_script();
		return $scripts;
	}

	protected static function get_field_change_script() {
		global $ontology_tpl;
		return $ontology_tpl['form_row_common_field_change_script'];
	}

	public static function get_concepts_liste_trad() {
	    global $thesaurus_concepts_liste_trad;
	    global $msg;
	    global $lang;

	    $return_tab = array();

	    if (!empty($lang)) {
	        $key = explode("_", $lang);
	        $message = "onto_common_datatype_ui_" . $key[0];
	        if (isset($msg[$message])) {
	            $return_tab[$key[0]] = $msg[$message];
	        } else {
	            $return_tab[$key[0]] = $lang;
	        }
	    }

	    if (empty($thesaurus_concepts_liste_trad)) {
	        return $return_tab;
	    }

	    $lang_tab = explode(",", $thesaurus_concepts_liste_trad);

	    $count = count($lang_tab);
	    for ($i = 0; $i < $count; $i++) {
	        $str = trim($lang_tab[$i]);
	        $key = explode("_", $str);
	        if (!array_key_exists($key[0], $return_tab)) {
	            if ('no' != $key[0]) {
	                $message = "onto_common_datatype_ui_" . $key[0];
	                // on traite le cas si le message n'existe pas
	                if (isset($msg[$message])) {
	                    $return_tab[$key[0]] = $msg[$message];
	                } else {
	                    $return_tab[$key[0]] = $str;
	                }
	            } else {
	                $return_tab[$key[0]] = $msg["onto_common_datatype_ui_no_lang"];
	            }
	        }
	    }
	    return $return_tab;
	}
} // end of onto_common_datatype_ui