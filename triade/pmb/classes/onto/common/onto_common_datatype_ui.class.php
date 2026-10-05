<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_datatype_ui.class.php,v 1.27 2021/06/16 09:15:19 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/onto_root_ui.class.php');
require_once($class_path.'/onto/common/onto_common_datatype.class.php');
require_once($include_path.'/templates/onto/common/onto_common_datatype_ui.tpl.php');


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
	 * @param restriction $restrictions le tableau des restrictions associées à la propriété 
	 * @param array datas le tableau des datatypes
	 * @param string instance_uri URI de l'instance
	 * @param string flag Flag
	 * 
	 * @return string
	 * @static
	 * @access public
	 */
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag){}

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
		return '{
			"check": function(){
				return true;
			},
			"get_error_message": function(){
				return "";	
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
	
	public static function get_hidden_fields($item_uri,$property, $restrictions,$datas, $instance_name,$flag = false) {
		global $msg,$charset,$ontology_tpl;
	
		$form=$ontology_tpl['form_row_hidden'];
	
		$content='';
	
		if (!empty($datas)) {
			$new_element_order=max(array_keys($datas));
				
			$form=str_replace("!!onto_new_order!!",$new_element_order , $form);
	
			foreach($datas as $key=>$data){
				$row=$ontology_tpl['form_row_content_hidden'];
	
				if($data->get_order()){
					$order=$data->get_order();
				}else{
					$order=$key;
				}
				/**
				 * TODO  : voir $data->get_formated_value() pour les sélecteurs multiples 
				 */
				$formated_value = $data->get_formated_value();
				$row=str_replace("!!onto_row_content_hidden_value!!",htmlentities((is_array($formated_value) ? reset($formated_value) : $formated_value) ,ENT_QUOTES,$charset) ,$row);
				$row=str_replace("!!onto_row_content_hidden_lang!!",$data->get_lang(),$row);
				$row=str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
	
				$row=str_replace("!!onto_row_order!!",$order , $row);
	
				$content.=$row;
			}
		} else {
	
			$form=str_replace("!!onto_new_order!!","0" , $form);
				
			$row = $ontology_tpl['form_row_content_hidden'];
	
			$row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
			$row = str_replace("!!onto_row_content_hidden_lang!!","" , $row);
			$row = str_replace("!!onto_row_content_hidden_range!!",$property->range[0] , $row);
	
			$row=str_replace("!!onto_row_order!!","0" , $row);
	
			$content.=$row;
		}
	
		$form=str_replace("!!onto_rows!!",$content ,$form);
		$form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
	
		return $form;
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