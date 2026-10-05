<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_item_creator_ui.class.php,v 1.19 2023/07/26 12:12:58 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path . '/onto/contribution/onto_contribution_datatype_resource_selector_ui.class.php';

/**
 * class onto_contribution_datatype_item_creator_ui
 */
class onto_contribution_datatype_item_creator_ui extends onto_contribution_datatype_resource_selector_ui
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
        global $charset, $ontology_tpl, $area_id;

        if (!$property->has_linked_form) {
            // Si il n'y a pas de formulaire lié, on ne retourne pas le template.
            return "";
        }

        // gestion des droits
        global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
        if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
            $ac = new acces();
            $dom_5 = $ac->setDomain(5);
        }

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
        } else {
            $range_for_form = $property->range;
        }

        $content = '';
        if ($restrictions->get_max() === -1) {
            $content .= $ontology_tpl['form_row_content_input_add_item_creator'];
        }

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

                //donnees utiles pour le bouton modifier
                $management_data = $data->get_management_data();

                $value_properties = $data->get_value_properties();
                $inside_row = $ontology_tpl['form_row_content_item_creator'];
                $inside_row .= $ontology_tpl['form_row_content_type'];
                $inside_row = str_replace(array(
                        "!!form_row_content_item_creator_display_label!!",
                        "!!form_row_content_item_creator_value!!",
                        "!!onto_row_content_range!!",
                        "!!form_row_content_item_creator_is_draft!!"
                    ), array(
                        htmlentities(addslashes($value_properties['display_label']), ENT_QUOTES, $charset),
                        $data->get_formated_value(),
                        $data->get_value_type(),
                        $management_data['is_draft']
                    ), $inside_row);

                $class = "";
                if (!empty($management_data['is_draft']) && $management_data['is_draft']) {
                    $class = "contribution_draft";
                }
                $row = str_replace("!!onto_row_is_draft!!", $class, $row);
                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input = "";
                $input .= $ontology_tpl['form_row_content_input_remove'];
                $input .= $ontology_tpl['form_row_content_search'];
                $input .= $ontology_tpl['form_row_content_update'];


                $params = [];
                $params['sub_form'] = 1;
                $params['is_draft'] = $property->is_draft ?? 0;
                $params['is_entity'] =  $property->is_entity ? true : false;
                $params['equation'] = static::get_equation_query($property);

                if ($property->has_linked_form) {

                    if ($property->is_entity && !empty($management_data['value'])) {
                        $linked_forms = true;
                        // On définis des valeurs par défaut
                        $management_data['form_uri'] = $property->linked_forms[0]['form_id_store'];
                        $management_data['form_id'] = $property->linked_forms[0]['form_id'];
                        $management_data['area_id'] = $property->linked_forms[0]['area_id'];
                    } else {
                        foreach ($property->linked_forms as $linked_form){
                            if (!empty($management_data['form_uri']) && $linked_form['form_id_store'] == $management_data['form_uri']) {
                                $linked_forms = true;
                            }
                        }
                    }

                    //Onglet modifier
                    if ($linked_forms && $management_data['value']) {
                        //Onglet modifier
                        $input .= $ontology_tpl['form_row_content_edit'];
                        $params['item_creator'] = true;
                        $params['origin'] = $domain;
                        $params['origin_uri'] = $item_uri;
                        $url = static::get_edit_url($management_data, $property->linked_forms[0]['scenario_id'], $property->linked_forms[0]['form_type'], $params) ;
                        $input = str_replace("!!url_edit_form!!", $url, $input);
                    } else {
                        $input .= $ontology_tpl['form_row_content_edit_hidden'];
                    }

                    $params['edit_contribution'] = 0;

                    $access_granted = true;

                    if (onto_common_uri::is_temp_uri($item_uri)) {
                        //droit de creation
                        $acces_right = 4;
                    } else {
                        //droit de modification
                        $acces_right = 8;
                    }

                    if (isset($dom_5)) {
                        $access_granted = false;
                        $length = count($property->linked_forms);
                        for ($i = 0; $i < $length; $i++) {
                            if ($dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($property->linked_forms[$i]['scenario_uri']), $acces_right)) {
                                // Si on a les droits pour un scenario on autorise les accès.
                                $access_granted = true;
                                break;
                            }
                        }
                    }

                    if ($access_granted) {

                        if ($linked_forms && !empty($management_data) && !empty($management_data['value'])) {
                            $input .= $ontology_tpl['form_row_content_hidden_linked_form'];
                        } else {
                            $input .= $ontology_tpl['form_row_content_linked_form'];
                        }


                        $params['area_id'] = $property->linked_forms[0]['area_id'] ?? 0;
                        $params['id'] = 0;
                        $params['form_id'] = $property->linked_forms[0]['form_id'] ?? 0;
                        $params['form_uri'] = isset($property->linked_forms[0]['form_id_store']) ? urlencode($property->linked_forms[0]['form_id_store']) : "";
                        $params['select_tab'] = 1;
                        $params['create'] = 1;
                        $params['scenario'] = $property->linked_forms[0]['scenario_id'] ?? 0;
                        $params['multiple_scenarios'] = $property->has_multiple_scenarios;
                        $params['attachment'] = $property->linked_forms[0]['attachment_id'] ?? 0;

                        $params['item_creator'] = true;
                        $params['origin'] = $domain;
                        $params['origin_uri'] = $item_uri;
                        $params['sub'] = $property->linked_forms[0]['form_type'];
                        $params['is_entity'] = $property->is_entity;

                        $json_data = encoding_normalize::json_encode($params);
                        $url = "./select.php?what=contribution&selector_data=".urlencode($json_data);
                        $input = str_replace("!!url_linked_form!!", $url, $input);
                    }
                    $input = str_replace("!!linked_scenario!!", $property->linked_forms[0]['scenario_id'] ?? 0, $input);
                }

                $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
                $input = str_replace("!!linked_tab_title!!", $property->label, $input);
                $input = str_replace("!!onto_new_order!!", $order, $input);

                $row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                $i ++;
            }
        } else {
            $form = str_replace("!!onto_new_order!!", "0", $form);
            $row = $ontology_tpl['form_row_content'];

            $inside_row = $ontology_tpl['form_row_content_item_creator'];
            $inside_row .= $ontology_tpl['form_row_content_type'];
            $inside_row = str_replace(
                array(
                    "!!form_row_content_item_creator_display_label!!",
                    "!!form_row_content_item_creator_value!!",
                    "!!onto_row_content_range!!",
                    "form_row_content_item_creator_is_draft"
                ), array(
                    "",
                    "",
                    $property->range[0],
                    "0"
                ), $inside_row);

            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = "";
            $input .= $ontology_tpl['form_row_content_input_remove'];

            if ($property->has_linked_form) {
                $input .= self::get_linked_form($property, $domain, $item_uri, $dom_5 , "0");
            }
            $row = str_replace("!!onto_row_inputs!!", $input, $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);
            $content .= $row;
        }

        $form = str_replace("!!onto_rows!!", $content, $form);
        $form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
        $form = str_replace("!!onto_completion!!", self::get_completion_from_range($range_for_form), $form);
        $form = str_replace("!!onto_equation_query!!", htmlentities(static::get_equation_query($property), ENT_QUOTES, $charset), $form);
        $form = str_replace("!!onto_area_id!!", ($area_id ? $area_id : ''), $form);
        $form = self::get_form_with_special_properties($property, $datas, $instance_name, $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);

        return $form;
    }

    /**
     * Retourne le template form_row_content_linked_form
     * @param onto_common_property $property
     * @param string $domain
     * @param string $item_uri
     * @param domain $dom_5
     * @param string|int $order
     * @return string
     */
    private static function get_linked_form($property, $domain, $item_uri, $dom_5 , $order = "0")
    {
        global $ontology_tpl;

        $input = "";

        $access_granted = true;
        if (onto_common_uri::is_temp_uri($item_uri)) {
            //droit de creation
            $acces_right = 4;
        } else {
            //droit de modification
            $acces_right = 8;
        }

        if (isset($dom_5)) {
            $access_granted = false;
            $length = count($property->linked_forms);
            for ($i = 0; $i < $length; $i++) {
                if ($dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($property->linked_forms[$i]['scenario_uri']), $acces_right)) {
                    // Si on a les droits pour un scenario on autorise les accès.
                    $access_granted = true;
                    break;
                }
            }
        }

        if ($access_granted) {

            $input .= $ontology_tpl['form_row_content_linked_form'];

            $url = './ajax.php?module=ajax&categ=contribution';
            $url .= '&sub=' . $property->linked_forms[0]['form_type'];
            $url .= '&area_id=' . $property->linked_forms[0]['area_id'] ?? 0;
            $url .= '&id=0&sub_form=1&form_id=' . $property->linked_forms[0]['form_id'] ?? 0;
            $url .= '&form_uri=' . (isset($property->linked_forms[0]['form_id_store']) ? urlencode($property->linked_forms[0]['form_id_store']) : "");
            $url .= '&origin=' . urlencode($domain);
            $url .= '&origin_uri=' . urlencode($item_uri);
            $url .= '&origin=' . urlencode($domain);

            $input = str_replace(
                array(
                    "!!onto_new_order!!",
                    "!!url_linked_form!!",
                    "!!linked_tab_title!!",
                    "!!linked_scenario!!"
                ),array(
                    $order,
                    $url,
                    $property->label,
                    $property->linked_forms[0]['scenario_id'] ?? 0
                ), $input);
        }

        return $input;
    }

    /**
     *
     * @param onto_common_property $property
     * @return string
     */
    protected static function get_equation_query($property)
    {
        if (empty($property->pmb_extended['equation'])) {
            return '';
        }
        $query = "SELECT contribution_area_equation_query FROM contribution_area_equations WHERE contribution_area_equation_id='" . $property->pmb_extended['equation'] . "'";

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            return $row->contribution_area_equation_query;
        }
        return '';
    }

    /**
     *
     * @param onto_common_property $property propriété concernée
     * @param array $datas le tableau des datatypes
     * @param string $instance_name nom de l'instance
     * @param boolean $flag
     * @return string
     */
    public static function get_hidden_fields($property, $datas, $instance_name, $flag = false)
    {
        global $charset, $ontology_tpl;

        $form = $ontology_tpl['form_row_hidden'];

        $content = '';

        if ( !empty($datas) && is_array($datas) ) {

            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach ($datas as $key => $data) {
                $row = $ontology_tpl['form_row_content_resource_selector_hidden'];

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }

                $management_data = $data->get_management_data();

                $is_draft = false;
                if (isset($management_data['is_draft'])) {
                    $is_draft = true;
                }

                $row = str_replace("!!onto_row_content_hidden_display_label!!", htmlentities($data->get_formated_value(), ENT_QUOTES, $charset), $row);
                $row = str_replace("!!onto_row_content_hidden_value!!", htmlentities($data->get_raw_value(), ENT_QUOTES, $charset), $row);
                $row = str_replace("!!onto_row_content_hidden_is_draft!!", ($is_draft ? $management_data['is_draft'] : "0"), $row);
                $row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
            }
        } else {

            $form = str_replace("!!onto_new_order!!", "0", $form);

            $row = $ontology_tpl['form_row_content_resource_selector_hidden'];
            $row = str_replace("!!onto_row_content_hidden_display_label!!", "", $row);
            $row = str_replace("!!onto_row_content_hidden_value!!", "", $row);
            $row = str_replace("!!onto_row_content_hidden_is_draft!!", "0", $row);
            $row = str_replace("!!onto_row_content_hidden_range!!", $property->range[0], $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);

            $content .= $row;
        }

        if ($flag) {
            $form = $content;
        } else {
            $form = str_replace("!!onto_rows!!", $content, $form);
        }

        $form = str_replace("!!onto_row_scripts!!", static::get_scripts(), $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);

        return $form;
    }
}
