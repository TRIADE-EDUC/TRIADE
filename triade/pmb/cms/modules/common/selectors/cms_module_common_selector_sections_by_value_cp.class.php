<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_sections_by_value_cp.class.php,v 1.2.2.2 2024/11/22 14:35:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_sections_by_value_cp extends cms_module_common_selector_type_editorial
{
    /**
     * Pour récupérer les rubriques donc l'identifiant passé par le sous-sélecteur est présent en valeur de champ perso
     */
    protected const TYPE_TO = 1;

    /**
     * Pour récupérer les rubriques utilisées dans le champ perso de la rubrique dont l'identifiant est récupéré par le sous-sélecteur
     */
    protected const TYPE_FROM = 2;

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->cms_module_common_selector_type_editorial_type = "section";
    }

    protected function get_sub_selectors()
    {
        $sub_selectors = parent::get_sub_selectors();
        $sub_selectors[] = 'cms_module_common_selector_global_var';
        return $sub_selectors;
    }

    public function execute_ajax()
    {
        global $id_type;
        $response = [];
        $fields = new cms_editorial_parametres_perso($id_type);

        $select = "
        <div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->format_text($this->msg['cms_module_common_selector_type_editorial_fields_label']) . "</label>
            </div>
            <div class='colonne-suite'>
                <select name='" . $this->get_form_value_name("select_field") . "' >";
        $select .= $fields->get_selector_options($this->parameters["type_editorial_field"]);
        $select .= "
                </select>
            </div>
        </div>";
        $response['content'] = $select;
        $response['content-type'] = 'text/html';
        return $response;
    }

    public function get_value()
    {
        if (isset($this->value)) {
            return $this->value;
        }

        $this->value = 0;

        $classSubSelector = $this->parameters['sub_selector'] ?? "";
        if (empty($classSubSelector) || !class_exists($classSubSelector)) {
            return $this->value;
        }

        $subSelector = new $classSubSelector($this->get_sub_selector_id($this->parameters['sub_selector']));
        $genericType = 0;

        $query = "SELECT id_editorial_type FROM cms_editorial_types
            WHERE editorial_type_element = '{$this->cms_module_common_selector_type_editorial_type}_generic'";

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $genericType = pmb_mysql_result($result, 0, 0);
            $genericType = intval($genericType);
        }

        $type = intval($this->parameters["type_editorial"]);
        $fieldId = intval($this->parameters['type_editorial_field']);
        $query = "SELECT datatype FROM cms_editorial_custom
            WHERE idchamp={$fieldId} AND num_type in ({$genericType}, {$type})";

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $datatype = pmb_mysql_result($result, 0, 0);
            switch ($this->parameters["type_select"]) {
                case static::TYPE_FROM:
                    $fieldNameSelect = "cms_editorial_custom_{$datatype}";
                    $fieldNameConstraint = "cms_editorial_custom_origine";
                    break;
                case static::TYPE_TO:
                    $fieldNameSelect = "cms_editorial_custom_origine";
                    $fieldNameConstraint = "cms_editorial_custom_{$datatype}";
                    break;
                default:
                    return $this->value;
            }
            $query = "SELECT $fieldNameSelect FROM cms_editorial_custom_values
                WHERE cms_editorial_custom_champ = {$fieldId}
                AND $fieldNameConstraint = '{$subSelector->get_value()}'
                ORDER BY cms_editorial_custom_order ";

            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $this->value = array();
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $this->value[] = $row[$fieldNameSelect];
                }
            }
        }
        return $this->value;
    }

    public function get_form()
    {
        $select_name = $this->get_form_value_name("select_section_cp_type");
        
        if(!isset($this->parameters["type_select"])) {
            $this->parameters["type_select"] = static::TYPE_TO;
        }
        $form = "<div class='row'>
        <div class='colonne3'>
            <label for='$select_name'>" . $this->format_text($this->msg['cms_module_common_selector_section_cp_type_select']) . "</label>
        </div>
        <div class='colonne-suite'>
            <select name='" . $select_name . "' >
                <option value='" . static::TYPE_TO . "' " . (static::TYPE_TO == $this->parameters["type_select"] ? "selected='selected'" : "") . ">" . $this->format_text($this->msg['cms_module_common_selector_section_cp_type_select_to_cp']) . "</option>
                <option value='" . static::TYPE_FROM . "' " . (static::TYPE_FROM == $this->parameters["type_select"] ? "selected='selected'" : "") . ">" . $this->format_text($this->msg['cms_module_common_selector_section_cp_type_select_from_cp']) . "</option>
            </select>
            </div>
        </div>";
        $form .= parent::get_form();

        return $form;
    }

    public function save_form()
    {
        $this->parameters["type_select"] = $this->get_value_from_form("select_section_cp_type");
        return parent::save_form();
    }
}
