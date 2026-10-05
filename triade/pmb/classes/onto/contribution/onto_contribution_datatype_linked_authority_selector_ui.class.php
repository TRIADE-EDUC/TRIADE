<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_linked_authority_selector_ui.class.php,v 1.11 2023/11/16 15:00:31 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once $class_path . '/onto/common/onto_common_datatype_ui.class.php';
require_once $class_path . '/authority.class.php';
require_once $class_path . '/aut_link.class.php';

class onto_contribution_datatype_linked_authority_selector_ui extends onto_common_datatype_resource_selector_ui
{

    private static $aut_link_xml;

    private static $relation_type_authority_options;

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
     * @param restriction $restrictions
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
        global $msg, $charset, $ontology_tpl;

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
        $content = '';
        $content = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $content);

        if ($restrictions->get_max() < count($datas) || $restrictions->get_max() === - 1) {
            $add_button = $ontology_tpl['form_row_content_input_add_linked_authority'];
            $add_button = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $add_button);
        }

        $list_entitites = array();
        $first_entity = "1";
        if (! empty($property->pmb_extended['list_entities'])) {
            $list_entitites = explode(',', $property->pmb_extended['list_entities']);
            if (! empty($list_entitites)) {
                $first_entity = $list_entitites[0] ?? "1"; // "1" == author
            }
        }

        if (! empty($datas) && is_array($datas)) {
            $i = 1;
            // $first = true;
            $new_element_order = max(array_keys($datas));

            $form = str_replace("!!onto_new_order!!", $new_element_order, $form);

            foreach ($datas as $key => $data) {
                $row = "";

                if ($data->get_order()) {
                    $order = $data->get_order();
                } else {
                    $order = $key;
                }
                $formated_value = $data->get_formated_value();

                $inside_row = $ontology_tpl['form_row_content_linked_authority_selector'];
                $inside_row = str_replace("!!form_row_content_linked_authority_selector_display_label!!", htmlentities((isset($formated_value['authority']['display_label']) ? $formated_value['authority']['display_label'] : ""), ENT_QUOTES, $charset), $inside_row);
                $inside_row = str_replace("!!form_row_content_linked_authority_selector_value!!", (isset($formated_value['authority']['value']) && is_string($formated_value['authority']['value']) ? $formated_value['authority']['value'] : ""), $inside_row);
                $inside_row = str_replace("!!form_row_content_linked_authority_selector_range!!", $data->get_value_type(), $inside_row);
                $inside_row = str_replace("!!form_row_content_linked_authority_selector_is_draft!!", $formated_value['authority']['is_draft'] ?? "", $inside_row);

                $selected = $formated_value['relation_type_authority'] ?? null;
                $inside_row = str_replace('!!onto_row_content_marclist_options!!', static::generate_relation_type_authority($property, $selected), $inside_row);

                $inside_row = str_replace('!!form_row_content_linked_authority_selector_comment!!', htmlentities($formated_value['comment'] ?? ""), $inside_row);
                $inside_row = str_replace('!!form_row_content_linked_authority_selector_start_date!!', ($formated_value['start_date'] ?? ""), $inside_row);
                $inside_row = str_replace('!!form_row_content_linked_authority_selector_end_date!!', ($formated_value['end_date'] ?? ""), $inside_row);

                $inside_row = str_replace("!!onto_row_content_marclist_range!!", $property->range[0], $inside_row);

                $inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $inside_row);
                $inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#authority', $inside_row);

                $inside_row = str_replace("!!onto_row_content_authority_type!!", self::generate_aut_type_selector($property, $instance_name, ($formated_value["authority_type"] ?? 0)), $inside_row);

                $row .= $inside_row;

                $class = "";
                if (! empty($formated_value['authority']['is_draft'])) {
                    $class = "contribution_draft";
                }
                $row = str_replace("!!onto_row_is_draft!!", $class, $row);

                $input = '';
                // if($first){
                $input .= $ontology_tpl['form_row_content_input_remove'];
                // }else{
                // $input.= $ontology_tpl['form_row_content_input_del'];
                // }
                if ($i == count($datas)) {
                    $input .= $add_button;
                }
                $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);

                $type = $first_entity;
                if (! empty($formated_value['authority_type'])) {
                    $type = $formated_value['authority_type'];
                }

                $row = str_replace("!!onto_completion!!", self::get_completion_from_type($type), $row);
                $row = str_replace("!!onto_row_inputs!!", $input, $row);
                $row = str_replace("!!onto_row_order!!", $order, $row);

                $content .= $row;
                // $first = false;
                $i ++;
            }
        } else {
            $form = str_replace("!!onto_new_order!!", "0", $form);

            $row = "";

            $inside_row = $ontology_tpl['form_row_content_linked_authority_selector'];
            $inside_row = str_replace("!!form_row_content_linked_authority_selector_display_label!!", "", $inside_row);
            $inside_row = str_replace("!!form_row_content_linked_authority_selector_value!!", "", $inside_row);
            $inside_row = str_replace("!!form_row_content_linked_authority_selector_range!!", "", $inside_row);

            $inside_row = str_replace('!!onto_row_content_marclist_options!!', static::generate_relation_type_authority($property, null), $inside_row);

            $inside_row = str_replace('!!form_row_content_linked_authority_selector_comment!!', "", $inside_row);
            $inside_row = str_replace('!!form_row_content_linked_authority_selector_start_date!!', "", $inside_row);
            $inside_row = str_replace('!!form_row_content_linked_authority_selector_end_date!!', "", $inside_row);

            $inside_row = str_replace("!!onto_row_content_marclist_range!!", $property->range[0], $inside_row);

            $inside_row = str_replace("!!onto_current_element!!", onto_common_uri::get_id($item_uri), $inside_row);
            $inside_row = str_replace("!!onto_current_range!!", 'http://www.pmbservices.fr/ontology#authority', $inside_row);

            $inside_row = str_replace("!!onto_row_content_authority_type!!", self::generate_aut_type_selector($property, $instance_name), $inside_row);

            $row .= $inside_row;

            $input = '';
            $input .= $ontology_tpl['form_row_content_input_remove'];
            $input .= $add_button;
            $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
            $row = str_replace("!!onto_row_inputs!!", $input, $row);

            $row = str_replace("!!onto_row_order!!", "0", $row);
            $row = str_replace("!!onto_completion!!", self::get_completion_from_type($first_entity), $row);

            $content .= $row;
        }

        $form = str_replace("!!onto_rows!!", $content, $form);
        $form = str_replace("!!onto_completion!!", 'authors', $form);
        $form = str_replace("!!onto_row_id!!", $instance_name . '_' . $property->pmb_name, $form);

        return $form;
    }

    private static function parse_file()
    {
        global $base_path, $include_path, $charset;
        global $msg, $KEY_CACHE_FILE_XML;

        $filepath = $include_path . "/authorities/aut_links_subst.xml";
        if (! file_exists($filepath)) {
            $filepath = $include_path . "/authorities/aut_links.xml";
        }

        $fileInfo = pathinfo($filepath);
        $fileName = preg_replace("/[^a-z0-9]/i", "", $fileInfo['dirname'] . $fileInfo['filename'] . $charset);
        $tempFile = $base_path . "/temp/XML" . $fileName . ".tmp";
        $dejaParse = false;

        $cache_php = cache_factory::getCache();
        $key_file = "";
        if ($cache_php) {
            $key_file = getcwd() . $fileName . filemtime($filepath);
            $key_file = $KEY_CACHE_FILE_XML . md5($key_file);
            if ($tmp_key = $cache_php->getFromCache($key_file)) {
                if ($cache = $cache_php->getFromCache($tmp_key)) {
                    if (count($cache) == 1) {
                        self::$aut_link_xml = $cache[0];
                        $dejaParse = true;
                    }
                }
            }
        } else {
            if (file_exists($tempFile)) {
                // Le fichier XML original a-t-il été modifié ultérieurement ?
                if (filemtime($filepath) > filemtime($tempFile)) {
                    // on va re-générer le pseudo-cache
                    if ($tempFile && file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                } else {
                    $dejaParse = true;
                }
            }
            if ($dejaParse) {
                $tmp = fopen($tempFile, "r");
                $cache = unserialize(fread($tmp, filesize($tempFile)));
                fclose($tmp);
                if (count($cache) == 1) {
                    self::$aut_link_xml = $cache[0];
                } else {
                    // SOUCIS de cache...
                    if ($tempFile && file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                    $dejaParse = false;
                }
            }
        }

        if (! $dejaParse) {
            $fp = fopen($filepath, "r") or die("Can't find XML file");
            $size = filesize($filepath);

            $xml = fread($fp, $size);
            fclose($fp);
            $aut_links = _parser_text_no_function_($xml, "AUT_LINKS", $filepath);

            self::$aut_link_xml = array();
            $aut_definition = array();
            foreach ($aut_links['DEFINITION'][0]['ENTRY'] as $xml_aut_definition) {
                $aut_def[$xml_aut_definition['CODE']] = $xml_aut_definition['value'];
            }

            /**
             * Le résultat du parse du fichier xml est stocké en temps que tableau sérialisé dans le fichier tempo
             */
            // Lecture des liens
            foreach ($aut_links['LINKS'][0]['AUTHORITY'] as $main_authority) {
                $aut_allowed = array();
                if ($main_authority['AUTHORITY_ALLOWED']) {
                    foreach ($main_authority['AUTHORITY_ALLOWED'] as $sub_aut_allowed) {
                        if (isset($aut_def[$sub_aut_allowed['value']])) {
                            $aut_allowed[] = $aut_def[$sub_aut_allowed['value']];
                        }
                    }
                }
                if (isset($aut_def[$main_authority['CODE']])) {
                    self::$aut_link_xml[$aut_def[$main_authority['CODE']]]['aut_to_display'] = $aut_allowed;
                }
            }

            if ($key_file) {
                $key_file_content = $KEY_CACHE_FILE_XML . md5(serialize(array(
                    self::$aut_link_xml
                )));
                $cache_php->setInCache($key_file_content, array(
                    self::$aut_link_xml
                ));
                $cache_php->setInCache($key_file, $key_file_content);
            } else {
                $tmp = fopen($tempFile, "wb");
                fwrite($tmp, serialize(array(
                    self::$aut_link_xml
                )));
                fclose($tmp);
            }
        }
    }

    private static function generate_aut_type_selector($property, $instance_name, $aut_sel = 0, $index = 0)
    {
        global $msg;
        global $thesaurus_concepts_active;
        global $form_aut_link_buttons;

        self::parse_file();
        $js_aut_link_table_list = "";

        $aut_table_list = "<select name='!!onto_row_id!![!!onto_row_order!!][authority_type]' id='!!onto_row_id!!_!!onto_row_order!!_authority_type' data-prefix='!!onto_row_id!!_!!onto_row_order!!' onchange='onchange_aut_link_contrib_selector(this.dataset.prefix, this.value)'>";
        $options = '';

        $auth_type = authority::get_const_type_object($instance_name);
        if ($auth_type === 0) {
            $auth_type = authority::get_const_type_object(strtolower(explode('_', $instance_name)[0]));
        }
        $list_entities = (isset($property->pmb_extended['list_entities']) ? explode(',', $property->pmb_extended['list_entities']) : array());
        $first = 0;
        if (self::$aut_link_xml[$auth_type]['aut_to_display']) {
            foreach (self::$aut_link_xml[$auth_type]['aut_to_display'] as $aut_to_display) {
                $display_none = "";
                if (count($list_entities) && ! in_array($aut_to_display, $list_entities)) {
                    $display_none = 'style="display:none;"';
                } else {
                    if (! $aut_sel) {
                        $aut_sel = $aut_to_display;
                    }
                }
                $selected = '';
                if ((! $aut_sel && ! $first && ! $display_none) || ($aut_to_display == $aut_sel)) {
                    $selected = ' selected="selected" ';
                }
                $first = 1;
                $value = "";
                $label = "";
                switch ($aut_to_display) {
                    case '1':
                        $value = AUT_TABLE_AUTHORS;
                        $label = $msg["133"];
                        break;
                    case '2':
                        $value = AUT_TABLE_CATEG;
                        $label = $msg["134"];
                        break;
                    case '3':
                        $value = AUT_TABLE_PUBLISHERS;
                        $label = $msg["135"];
                        break;
                    case '4':
                        $value = AUT_TABLE_COLLECTIONS;
                        $label = $msg["136"];
                        break;
                    case '5':
                        $value = AUT_TABLE_SUB_COLLECTIONS;
                        $label = $msg["137"];
                        break;
                    case '6':
                        $value = AUT_TABLE_SERIES;
                        $label = $msg["333"];
                        break;
                    case '7':
                        $value = AUT_TABLE_TITRES_UNIFORMES;
                        $label = $msg["aut_menu_titre_uniforme"];
                        break;
                    case '8':
                        $value = AUT_TABLE_INDEXINT;
                        $label = $msg["indexint_menu"];
                        break;
                    case '9':
                        $authpersos = authpersos::get_instance();
                        $info = $authpersos->get_data();
                        foreach ($info as $elt) {
                            $selected = '';
                            if (($elt['id'] + 1000) == $aut_sel) {
                                $selected = ' selected="selected" ';
                            }
                            $display_none = "";
                            if (count($list_entities) && ! in_array(($elt['id'] + 1000), $list_entities)) {
                                $display_none = 'style="display:none;"';
                            }
                            $tpl_elt = "<option value='" . ($elt['id'] + 1000) . "' " . $selected . " $display_none >" . $elt['name'] . "</option>";

                            // $js_aut_link_table_list.="aut_link_table_select[".($elt['id'] + 1000)."]='./select.php?what=authperso&authperso_id=".$elt['id']."&caller=$caller&dyn=2&param1=';";

                            $options .= $tpl_elt;
                        }
                        break;
                    case '10':
                        if ($thesaurus_concepts_active) {
                            $value = AUT_TABLE_CONCEPT;
                            $label = $msg["onto_common_concept"];
                        }
                        break;
                }
                if ($value && $label && $aut_to_display != "9") {
                    $options .= "<option value='$value' $selected $display_none>$label</option>";
                }
            }
        }
        if ($options) {
            return $aut_table_list . $options . '</select>';
            // ce qui suit n'est pas bon du tout. Ça ne peut pas fonctionner
            $add_button = $form_aut_link_buttons;
            $add_button = str_replace("!!index!!", $index, $add_button);
            return $aut_table_list . $options . '</select>' . $add_button;
        }
        return '';
    }

    protected static function get_field_change_script()
    {
        global $ontology_tpl;
        return $ontology_tpl['form_row_content_linked_authority_selector_script'] . $ontology_tpl['form_row_common_field_change_script'];
    }

    private static function get_completion_from_type($type)
    {
        switch ($type) {
            case "1":
                return "authors";
            case "2":
                return "categories";
            case "3":
                return "publishers";
            case "4":
                return "collections";
            case "5":
                return "subcollections";
            case "6":
                return "serie";
            case "7":
                return "titre_uniforme";
            case "8":
                return "indexint";
            case "10":
                return "onto";
            default:
                if ($type > 1000) {
                    return "authperso_" . ($type - 1000);
                }
        }
        return "authors";
    }

    public static function get_list_values_to_display($property)
    {
        return (! empty($property->pmb_extended['list_values']) ? explode(',', $property->pmb_extended['list_values']) : array());
    }

    private static function generate_relation_type_authority($property, $selected)
    {
        if (! empty(static::$relation_type_authority_options)) {
            return static::$relation_type_authority_options;
        }
        global $charset;
        $marc_list = marc_list_collection::get_instance('aut_link');
        $tmp = array();
        if (count($marc_list->inverse_of)) {
            // sous tableau genre ascendant descendant...
            foreach ($marc_list->table as $table) {
                $tmp = array_merge($tmp, $table);
            }
            $marc_list->table = $tmp;
        }

        $list_values = static::get_list_values_to_display($property);
        $list_values = array_map('strval', $list_values);

        static::$relation_type_authority_options = '';
        foreach ($marc_list->table as $value => $label) {
            $display_none = "";
            if (count($list_values) && ! in_array(strval($value), $list_values, true)) {
                $display_none = 'style="display:none;"';
            } else {
                if (is_null($selected)) {
                    $selected = $value;
                }
            }
            static::$relation_type_authority_options .= '<option value="' . $value . '" ' . (isset($selected) && ($selected == $value) ? 'selected="selected"' : '') . ' ' . $display_none . ' >' . htmlentities($label, ENT_QUOTES, $charset) . '</option>';
        }
        return static::$relation_type_authority_options;
    }
} // end of onto_common_datatype_responsability_selector_ui
