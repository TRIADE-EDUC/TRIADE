<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_resource_selector_ui.class.php,v 1.25 2023/07/26 12:12:58 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');
/**
 * class onto_common_datatype_resource_selector_ui
 *
 */
class onto_contribution_datatype_resource_selector_ui extends onto_common_datatype_resource_selector_ui {

	/**
	 *
	 *
	 * @param onto_common_property $property la propriété concernée
	 * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
    public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
        global $charset, $ontology_tpl, $ontology_contribution_tpl, $area_id;

        if (empty($datas)) {
            $datas = array();
        }

        $form = $ontology_tpl['form_row'];
        $form = str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);

        /** traitement initial du range ?!*/
        $range_for_form = "";
        if(is_array($property->range)){
            foreach($property->range as $range){
                if($range_for_form) $range_for_form.="|||";
                $range_for_form .= $range;
            }
        } else {
            $range_for_form = $property->range;
        }
        $exploded_uri = explode("/", $item_uri);
        $i = (count($exploded_uri) - 1);

        $uri_suffix = str_replace('#', '_', $exploded_uri[$i]);
        /** **/
        /** TODO: à revoir avec le chef ** /
         /** On part du principe que l'on a qu'un range **/
        // 		$selector_url = $this->get_resource_selector_url($property->range[0]);

        $content='';
        if (!$property->is_no_search()) {
            $content .= $ontology_contribution_tpl['form_row_content_input_sel'];
            $content = str_replace("!!onto_current_range!!", $uri_suffix, $content);
        }

        if (($restrictions->get_max() < count($datas) || $restrictions->get_max() === -1) && !$property->is_no_search()) {
            $content .= $ontology_contribution_tpl['form_row_content_input_add_ressource_selector'];
        }

        if (!empty($datas)) {
            $i=1;
            $first=true;
            $new_element_order=max(array_keys($datas));

            $form=str_replace("!!onto_new_order!!",$new_element_order , $form);

            foreach($datas as $key=>$data){
                $row=$ontology_contribution_tpl['form_row_content'];

                if($data->get_order()){
                    $order=$data->get_order();
                }else{
                    $order=$key;
                }

                $inside_row = $ontology_contribution_tpl['form_row_content_resource_selector'];
                $inside_row .= $ontology_contribution_tpl['form_row_content_type'];

                $formated_value = $data->get_formated_value();
                $display_label = htmlentities(stripslashes((is_array($formated_value) ? reset($formated_value) : $formated_value)), ENT_QUOTES, $charset);

                $inside_row = str_replace("!!form_row_content_resource_selector_display_label!!", $display_label, $inside_row);
				$inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $inside_row);
				$inside_row = str_replace("!!onto_current_range!!", $uri_suffix, $inside_row);
                $inside_row = str_replace("!!onto_row_content_range!!", $range_for_form, $inside_row);

                $value = "";
                if (!$property->is_no_search()) {
                    $value = $data->get_value();
                    $value = (is_array($value) ? reset($value) : $value);
                }
                $inside_row = str_replace("!!form_row_content_resource_selector_value!!", $value, $inside_row);
                $inside_row = str_replace("!!form_row_content_resource_selector_range!!", $range_for_form, $inside_row);
                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input='';
                if (!$property->is_no_search()) {
                    if($first){
                        $input.=$ontology_contribution_tpl['form_row_content_input_remove'];
                    }else{
                        $input.=$ontology_contribution_tpl['form_row_content_input_del'];
                    }
                }

                if (!empty($property->has_linked_form) && $first) {
                    $input .= $ontology_contribution_tpl['form_row_content_linked_form'];
                    $url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form['form_type'].'&area_id='.$property->linked_form['area_id'].'&id='.onto_common_uri::get_id($data->get_raw_value()).'&sub_form=1&form_id='.$property->linked_form['form_id'].'&form_uri='.urlencode($property->linked_form['form_id_store']);
                    $input = str_replace("!!url_linked_form!!", $url, $input);
                    $input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
                }
                if (strpos($input, "!!property_name!!")) {
                    $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
                }

				$row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                $first = false;
                $i++;
            }
        }else{
            $form = str_replace("!!onto_new_order!!", "0", $form);
            $row = $ontology_contribution_tpl['form_row_content'];

            $inside_row = $ontology_contribution_tpl['form_row_content_resource_selector'];
            $inside_row = str_replace("!!form_row_content_resource_selector_display_label!!", "", $inside_row);
            $inside_row = str_replace("!!form_row_content_resource_selector_value!!", "", $inside_row);
            $inside_row = str_replace("!!form_row_content_resource_selector_range!!", $range_for_form, $inside_row);
            $inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $inside_row);
            $inside_row = str_replace("!!onto_current_range!!", $uri_suffix, $inside_row);
            $inside_row = str_replace("!!onto_row_content_range!!", $range_for_form, $inside_row);

            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = '';
            if (!$property->is_no_search()) {
                $input .= $ontology_contribution_tpl['form_row_content_input_remove'];
            }
            $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);

            $row = str_replace("!!onto_row_inputs!!", $input, $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);

            $content.=$row;

        }

        //sans content on a pas les champs caché donc toujours besoin
        $form = str_replace("!!onto_rows!!", $content,$form);
        //remplace les id des input donc nécessaire
        $form = str_replace("!!onto_completion!!", self::get_completion_from_range($range_for_form), $form);
        $form = str_replace("!!onto_equation_query!!", htmlentities(static::get_equation_query($property),ENT_QUOTES,$charset), $form);
        $form = str_replace("!!onto_area_id!!", ($area_id ? $area_id : ''), $form);
        $form = str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);

        //le selecteur doit il etre masqué ?

        $form = str_replace("!!onto_selector_url!!", self::get_resource_selector_url($property->range[0]), $form);

        return $form;
    } // end of member function get_form

    /**
     *
     * @param onto_common_property $property
     * @return string
     */
    protected static function get_equation_query($property) {
        if(empty($property->pmb_extended['equation'])) {
            return '';
        }
        $query = "SELECT contribution_area_equation_query FROM contribution_area_equations WHERE contribution_area_equation_id='".$property->pmb_extended['equation']."'";


        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $row = pmb_mysql_fetch_object($result);
            return $row->contribution_area_equation_query;
        }
        return '';
    }


    public static function get_hidden_fields($item_uri,$property, $restrictions,$datas, $instance_name,$flag = false) {
        global $charset, $ontology_tpl;

        $form = $ontology_tpl['form_row_hidden'];

        $content = '';

        if ( !empty($datas) && is_array($datas) ) {

            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach($datas as $key => $data) {

                $row = $ontology_tpl['form_row_content_resource_selector_hidden'];

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }

                $row = str_replace("!!onto_row_content_hidden_display_label!!", htmlentities($data->get_formated_value(), ENT_QUOTES, $charset), $row);
                $row = str_replace("!!onto_row_content_hidden_value!!", htmlentities($data->get_value(), ENT_QUOTES, $charset),$row);
                $row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
            }
        } else {

            $form = str_replace("!!onto_new_order!!", "0", $form);

            $row = $ontology_tpl['form_row_content_resource_selector_hidden'];

            $row = str_replace("!!onto_row_content_hidden_display_label!!", "", $row);
            $row = str_replace("!!onto_row_content_hidden_value!!", "",$row);
            $row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);

            $content .= $row;
        }

        if ($flag) {
            $form = $content;
        } else {
            $form = str_replace("!!onto_rows!!", $content, $form);
        }
        $form = str_replace("!!onto_row_id!!", $instance_name.'_'.$property->pmb_name, $form);

        return $form;
    }

} // end of onto_contribution_datatype_resource_selector_ui
