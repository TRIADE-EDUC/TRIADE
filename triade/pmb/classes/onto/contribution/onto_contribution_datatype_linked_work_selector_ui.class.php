<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_work_selector_ui.class.php,v 1.10 2023/11/16 15:00:31 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once $class_path . '/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path . '/authority.class.php';

/**
 * class onto_common_datatype_responsability_selector_ui
 */
class onto_contribution_datatype_linked_work_selector_ui extends onto_common_datatype_resource_selector_ui
{

    /**
     * Aggregations:
     */

    /**
     * Compositions:
     */

    /**
     * * Attributes: **
     */

    /**
     *
     * @param
     *            Array() class_uris URI des classes de l'ontologie listées dans le sélecteur
     *            
     * @return void
     * @access public
     */
    public function __construct($class_uris)
    {}

    // end of member function __construct

    /**
     *
     * @param
     *            string class_uri URI de la classe d'instances à lister
     *            
     * @param
     *            integer page Numéro de page à afficher
     *            
     * @return Array()
     * @access public
     */
    public function get_list($class_uri, $page)
    {}

    // end of member function get_list

    /**
     * Recherche
     *
     * @param
     *            string user_query Chaine de recherche dans les labels
     *            
     * @param
     *            string class_uri Rechercher iniquement les instances de la classe
     *            
     * @param
     *            integer page Page du résultat de recherche à afficher
     *            
     * @return Array()
     * @access public
     */
    public function search($user_query, $class_uri, $page)
    {}

    // end of member function search

    /**
     *
     * @param onto_common_property $property
     *            la propriété concernée
     * @param onto_restriction $restrictions
     *            le tableau des restrictions associées à la propriété
     * @param
     *            array datas le tableau des datatypes
     * @param
     *            string instance_name nom de l'instance
     * @param
     *            string flag Flag
     *            
     * @return string
     * @static
     * @access public
     */
    public static function get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag)
    {
        global $area_id, $charset, $ontology_tpl;
        // gestion des droits
        global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
        if (($gestion_acces_active == 1) && ($gestion_acces_empr_contribution_scenario == 1)) {
            $ac = new acces();
            $dom_5 = $ac->setDomain(5);
        }

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
         * TODO: à revoir avec le chef ** /
         * /** On part du principe que l'on a qu'un range *
         */

        // Récupération du nom du formulaire
        $exploded_uri = explode("/", $item_uri);
        $i = (count($exploded_uri) - 1);

        $uri_suffix = str_replace('#', '_', $exploded_uri[$i]);

        $content = '';
        $content .= $ontology_tpl['form_row_content_input_sel'];
        $content = str_replace("!!property_name!!", './select.php?what=titre_uniforme&caller=', $content);
        $content = str_replace("!!onto_current_range!!", $uri_suffix, $content);

        $id = '!!onto_row_id!!_!!onto_row_order!!_relation_type_work';
        $name = '!!onto_row_id!![!!onto_row_order!!][relation_type_work]';

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

                $formated_value = $data->get_formated_value();
                $inside_row = $ontology_tpl['form_row_content_linked_record_selector'];

                $inside_row = str_replace(array(
                    "!!form_row_content_linked_record_selector_display_label!!",
                    "!!form_row_content_linked_record_selector_value!!",
                    "!!form_row_content_linked_record_selector_range!!"
                ), array(
                    htmlentities((isset($formated_value['work']['display_label']) ? $formated_value['work']['display_label'] : ""), ENT_QUOTES, $charset),
                    (isset($formated_value['work']['value']) ? $formated_value['work']['value'] : ""),
                    $data->get_value_type()
                ), $inside_row);

                // $selector = notice_relations::get_selector('!!onto_row_id!![!!onto_row_order!!][value]',(isset($formated_value['relation_type_work']) ? $formated_value['relation_type_work']."-".$formated_value['direction'] : ""));
                $selector = static::get_selector($property, $id, $name, (isset($formated_value['relation_type_work']) ? $formated_value['relation_type_work'] : ""));

                $inside_row = str_replace(array(
                    "!!onto_row_content_linked_record_selector!!",
                    "!!onto_row_content_marclist_range!!",
                    "!!onto_current_element!!",
                    "!!onto_current_range!!"
                ), array(
                    $selector,
                    $property->range[0],
                    onto_common_uri::get_id($item_uri),
                    "http://www.pmbservices.fr/ontology#linked_work"
                ), $inside_row);

                $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

                $input = '';
                if ($first) {
                    $input .= $ontology_tpl['form_row_content_input_remove'];
                } else {
                    $input .= $ontology_tpl['form_row_content_input_del'];
                }

                if ($property->has_linked_form && $first) {
                    $access_granted = true;
                    if (onto_common_uri::is_temp_uri($item_uri)) {
                        // droit de creation
                        $acces_right = 4;
                    } else {
                        // droit de modification
                        $acces_right = 8;
                    }
                    if (isset($dom_5) && ! $dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($property->linked_form['scenario_uri']), $acces_right)) {
                        $access_granted = false;
                    }
                    if ($access_granted) {
                        $input .= $ontology_tpl['form_row_content_linked_form'];

                        $url = './ajax.php?module=ajax&categ=contribution';
                        $url .= '&sub=' . $property->linked_form['form_type'];
                        $url .= '&area_id=' . $property->linked_form['area_id'];
                        $url .= '&id=' . onto_common_uri::get_id($data->get_value());
                        $url .= '&sub_form=1&form_id=' . $property->linked_form['form_id'];
                        $url .= '&form_uri=' . urlencode($property->linked_form['form_id_store']);

                        $input = str_replace(array(
                            "!!url_linked_form!!",
                            "!!linked_form_title!!",
                            "!!onto_new_order!!"
                        ), array(
                            $url,
                            $property->linked_form['form_title'],
                            $order
                        ), $input);
                    }
                }

                $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);

                $row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                $first = false;
                $i ++;
            }
        } else {

            $form = str_replace("!!onto_new_order!!", "0", $form);
            $row = $ontology_tpl['form_row_content'];

            $inside_row = $ontology_tpl['form_row_content_linked_record_selector'];
            $inside_row = str_replace(array(
                "!!form_row_content_linked_record_selector_display_label!!",
                "!!form_row_content_linked_record_selector_value!!",
                "!!form_row_content_linked_record_selector_range!!"
            ), "", $inside_row);

            $selector = static::get_selector($property, $id, $name, "");
            $inside_row = str_replace(array(
                '!!onto_row_content_linked_record_selector!!',
                "!!onto_row_content_marclist_range!!",
                "!!onto_current_element!!",
                "!!onto_current_range!!"
            ), array(
                $selector,
                $property->range[0],
                onto_common_uri::get_id($item_uri),
                'http://www.pmbservices.fr/ontology#linked_work'
            ), $inside_row);

            $row = str_replace("!!onto_inside_row!!", $inside_row, $row);

            $input = '';
            $input .= $ontology_tpl['form_row_content_input_remove'];
            $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);

            if ($property->has_linked_form) {
                $access_granted = true;
                if (isset($dom_5) && ! $dom_5->getRights($_SESSION['id_empr_session'], onto_common_uri::get_id($property->linked_form['scenario_uri']), 4)) {
                    $access_granted = false;
                }
                if ($access_granted) {
                    $input .= $ontology_tpl['form_row_content_linked_form'];

                    $url = './ajax.php?module=ajax&categ=contribution';
                    $url .= '&sub=' . $property->linked_form['form_type'];
                    $url .= '&area_id=' . $property->linked_form['area_id'];
                    $url .= '&id=0&sub_form=1&form_id=' . $property->linked_form['form_id'];
                    $url .= '&form_uri=' . urlencode($property->linked_form['form_id_store']);

                    $input = str_replace(array(
                        "!!url_linked_form!!",
                        "!!linked_form_title!!",
                        "!!onto_new_order!!"
                    ), array(
                        $url,
                        $property->linked_form['form_title'],
                        "0"
                    ), $input);
                }
            }

            $row = str_replace("!!onto_row_inputs!!", $input, $row);
            $row = str_replace("!!onto_row_order!!", "0", $row);
            $content .= $row;
        }

        $form = str_replace("!!onto_rows!!", $content, $form);
        $form = str_replace("!!onto_completion!!", 'titre_uniforme', $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);
        $form = str_replace("!!onto_area_id!!", ($area_id ? $area_id : ''), $form);

        return $form;
    }

    // end of member function get_form

    /**
     *
     * @param
     *            onto_common_datatype datas Tableau des valeurs à afficher associées à la propriété
     *            
     * @param
     *            property property la propriété à utiliser
     *            
     * @param
     *            string instance_name nom de l'instance
     *            
     * @return string
     * @access public
     */
    public function get_display($datas, $property, $instance_name)
    {
        $display = '<div id="' . $instance_name . '_' . $property->pmb_name . '">';
        $display .= '<p>';
        $display .= $property->get_label() . ' : ';
        foreach ($datas as $data) {
            $display .= $data->get_formated_value();
        }
        $display .= '</p>';
        $display .= '</div>';
        return $display;
    }

    public static function get_list_values_to_display($property)
    {
        return (! empty($property->pmb_extended['list_entities']) ? explode(',', $property->pmb_extended['list_entities']) : array());
    }

    protected static function get_selector($property, $id, $name, $selected)
    {
        global $charset, $msg;

        $type = "";
        switch ($property->pmb_name) {
            case 'has_other_link':
                $type = "other_link";
                break;
            case 'expression_of':
                $type = "expression_of";
                break;
            case 'has_expression':
            default:
                $type = "have_expression";
                break;
        }

        $optgroup_list = array();

        $list_entities = static::get_list_values_to_display($property);
        $list_entities = array_map('strval', $list_entities);

        $selector = '<select id="' . $id . '" name="' . $name . '" data-form-name=' . substr($name, 0, - 1) . '>';
        $oeuvre_link = marc_list_collection::get_instance('oeuvre_link');
        foreach ($oeuvre_link->table as $group => $types) {
            $options = '';
            foreach ($types as $code => $libelle) {
                if ($oeuvre_link->attributes[$code]['GROUP'] == $type) {
                    $display_none = "";
                    if (count($list_entities) && ! in_array(strval($code), $list_entities, true)) {
                        $display_none = 'style="display:none;"';
                    }
                    if (! ($code == $selected) || ! empty($display_none)) {
                        $options .= "<option value='" . $code . "' " . $display_none . ">" . $libelle . "</option>";
                    } else {
                        $options .= "<option value='" . $code . "' selected='selected'>" . $libelle . "</option>";
                    }
                }
            }
            if ($options)
                $optgroup_list[$group] = $options;
        }
        if (count($optgroup_list) > 1) {
            foreach ($optgroup_list as $group => $options) {
                $selector .= '<optgroup label="' . htmlentities($group, ENT_QUOTES, $charset) . '">' . $options . '</optgroup>';
            }
        } elseif (count($optgroup_list)) {
            foreach ($optgroup_list as $group => $options) {
                $selector .= $optgroup_list[$group];
            }
        } else {
            $selector .= "<option value=''>" . $msg['authority_marc_list_empty_filter'] . "</option>";
        }
        $selector .= '</select>';

        return $selector;
    }
} // end of onto_common_datatype_responsability_selector_ui
