<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_small_text_link_ui.class.php,v 1.5.2.1.2.1 2025/02/06 10:53:24 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Library\CSRF\CollectionCSRF;

/**
 * onto_common_datatype_small_text_link_ui
 */
class onto_contribution_datatype_small_text_link_ui  extends onto_common_datatype_small_text_link_ui
{

    private static $default_type = "http://www.w3.org/2000/01/rdf-schema#Literal";

    /**
     *
     * @param string $item_uri
     * @param onto_property $property
     * @param onto_restriction $restrictions
     * @param [onto_common_datatype_small_text] $datas
     * @param string $instance_name
     * @param string $flag
     * @return mixed
     */
    public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $charset, $ontology_tpl, $pmb_curl_timeout;

        $max = $restrictions->get_max();
        $form = $ontology_tpl['form_row_link'];
        $form = str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8'), ENT_QUOTES, $charset), $form);
        $form = str_replace("!!onto_input_type!!", htmlentities(self::$default_type, ENT_QUOTES, $charset), $form);

        $content = '';

        if ( !empty($datas) && is_array($datas) ) {
            $i = 1;
            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach ($datas as $key => $data) {

                $row = $ontology_tpl['form_row_content'];

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }

                $inside_row = $ontology_tpl['form_row_content_small_text_link'];
                $inside_row = str_replace("!!onto_row_content_small_text_value!!", htmlentities($data->get_formated_value(), ENT_QUOTES, $charset), $inside_row);
                $inside_row = str_replace("!!onto_row_content_small_text_range!!", $property->range[0], $inside_row);
                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input = $ontology_tpl['form_row_content_input_open_link'];
                if ($new_element_order == $order) {
                    if ($restrictions->get_max() < $i || $restrictions->get_max() === - 1) {
                        $input .= $ontology_tpl['form_row_content_input_add_text_link'];
                    }
                }

                $row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                $i ++;
            }
        } else {
            $form = str_replace("!!onto_new_order!!", "1", $form);

            $row = $ontology_tpl['form_row_content'];

            $inside_row = $ontology_tpl['form_row_content_small_text_link'];
            $inside_row = str_replace("!!onto_row_content_small_text_value!!", "", $inside_row);
            $inside_row = str_replace("!!onto_row_content_small_text_range!!", $property->range[0], $inside_row);
            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = $ontology_tpl['form_row_content_input_open_link'];
            if ($restrictions->get_max() != 1) {
                $input = $ontology_tpl['form_row_content_input_add_text_link'];
            }
            $row = str_replace("!!onto_row_inputs!!", $input, $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);

            $content .= $row;
        }

        $onto_rows = "";
        $onto_rows .= $content;
        $onto_rows .= $ontology_tpl['onto_script_small_text_link'];
        $onto_rows = str_replace("!!pmb_curl_timeout!!", $pmb_curl_timeout, $onto_rows);
        $onto_rows = str_replace("!!csrf_token_id!!", $instance_name . '_' . $property->pmb_name, $onto_rows);
        
        $collectionCSRF = new CollectionCSRF();
        $tokens = json_encode($collectionCSRF->getArrayTokens());
        
        $onto_rows = str_replace('!!tokens_csrf!!', $tokens, $onto_rows);

        $form = str_replace("!!input_add!!", '', $form);
        $form = str_replace("!!onto_row_max_card!!", $max, $form);

        $form = str_replace("!!onto_rows!!", $onto_rows, $form);
        $form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);

        return $form;
    }
}