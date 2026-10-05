<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_item_creator_ui.class.php,v 1.5 2023/07/26 12:12:58 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path . '/onto/common/onto_common_datatype_ui.class.php';
require_once ($include_path . '/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');

/**
 * class onto_contribution_datatype_item_creator_ui
 */
class onto_contribution_datatype_item_creator_ui extends onto_common_datatype_resource_selector_ui
{

    /**
     * Retourne le template
     *
     * @param string $item_uri
     * @param onto_common_property $property la propriété concernée
     * @param onto_restriction $restrictions le tableau des restrictions associées à la propriété
     * @param array $datas le tableau des datatypes
     * @param string $instance_name
     * @param string|boolean $flag
     * @return string
     */
    public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $charset, $ontology_tpl, $ontology_contribution_tpl;
        $domain = array_values($property->domain)[0];
        $form = $ontology_tpl['form_row'];
        $form = str_replace("!!onto_row_label!!", htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8'), ENT_QUOTES, $charset), $form);

        /**
         * traitement initial du range ?!
         */
        $range_for_form = "";
        if (is_array($property->range)) {
            foreach ($property->range as $range) {
                if ($range_for_form)
                    $range_for_form .= "|||";
                $range_for_form .= $range;
            }
        }
        /**
         * TODO: à revoir avec le chef
         * On part du principe que l'on a qu'un range
         */
        // $selector_url = $this->get_resource_selector_url($property->range[0]);

        $content = '';
        $content .= $ontology_contribution_tpl['form_row_content_input_sel'];
        $content = str_replace("!!onto_current_range!!", $range_for_form, $content);


        if ($restrictions->get_max() < count($datas) || $restrictions->get_max() === - 1) {
            $content .= $ontology_tpl['form_row_content_input_add_ressource_selector'];
        }
        $content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);
        if ( !empty($datas) && is_array($datas) ) {
            $i = 1;
            $first = true;
            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach ($datas as $key => $data) {
                $row = $ontology_tpl['form_row_content'];

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }

                $inside_row = $ontology_tpl['form_row_content_item_creator'];
                $inside_row .= $ontology_tpl['form_row_content_type'];
                $formated_value = $data->get_formated_value();
                $value = $data->get_value();
                $inside_row = str_replace(
                    array(
                        "!!form_row_content_item_creator_display_label!!",
                        "!!form_row_content_item_creator_value!!",
                        "!!form_row_content_item_creator_range!!",
                        "!!onto_current_element!!",
                        "!!onto_current_range!!",
                        "!!onto_row_content_range!!"
                    ),
                    array(
                        htmlentities(stripslashes((is_array($formated_value) ? reset($formated_value) : $formated_value)), ENT_QUOTES, $charset),
                        (is_array($value) ? reset($value) : $value),
                        $data->get_value_type(),
                        onto_common_uri::get_id($item_uri),
                        $range_for_form,
                        $range_for_form
                    ),
                    $inside_row);

                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input = '';
                if ($first) {
                    $input .= $ontology_tpl['form_row_content_input_remove'];
                } else {
                    $input .= $ontology_tpl['form_row_content_input_del'];
                }

                if (! empty($property->has_linked_form) && $first) {
                    $input .= $ontology_tpl['form_row_content_linked_form'];
                    $url = './ajax.php?module=ajax&categ=contribution&sub=' . $property->linked_form['form_type'] . '&area_id=' . $property->linked_form['area_id'] . '&id=' . onto_common_uri::get_id($data->get_raw_value()) . '&sub_form=1&form_id=' . $property->linked_form['form_id'] . '&form_uri=' . urlencode($property->linked_form['form_id_store']);
                    $url .= '&origin_uri=' . urlencode($item_uri);
                    $url .= '&origin=' . urlencode($domain);
                    $input = str_replace("!!url_linked_form!!", $url, $input);
                    $input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
                }

                $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
                $row = str_replace(["!!onto_row_inputs!!", "!!onto_row_order!!"], [$input, $order], $row);

                $content .= $row;
                $first = false;
                $i ++;
            }
        } else {
            $form = str_replace("!!onto_new_order!!", "0", $form);

            $row = $ontology_tpl['form_row_content'];

            $inside_row = $ontology_tpl['form_row_content_item_creator'];
            $inside_row = str_replace(
                array(
                    "!!form_row_content_item_creator_display_label!!",
                    "!!form_row_content_item_creator_value!!",
                    "!!form_row_content_item_creator_range!!",
                    "!!onto_current_element!!",
                    "!!onto_current_range!!",
                    "!!onto_row_content_range!!"
                ),
                array(
                    "",
                    "",
                    "",
                    onto_common_uri::get_id($item_uri),
                    $range_for_form,
                    $range_for_form
                ),
                $inside_row);

            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = '';
            $input .= $ontology_tpl['form_row_content_input_remove'];
            $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
            $row = str_replace(["!!onto_row_inputs!!", "!!onto_row_order!!"], [$input, "0"], $row);

            $content .= $row;
        }

        $form = str_replace(
            array(
                "!!onto_rows!!",
                "!!onto_row_id!!",
                "!!onto_selector_url!!",
                "!!onto_completion!!"
            ),
            array(
                $content,
                $instance_name . '_' . $property->pmb_name,
                self::get_resource_selector_url($property->range[0]),
                self::get_completion_from_range($range_for_form),
            ), $form);

        return $form;
    }
}
