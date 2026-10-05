<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_section_by_value_cp.class.php,v 1.1 2023/04/25 10:10:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_common_selector_section_by_value_cp extends cms_module_common_selector_type_editorial
{
    public function __construct($id=0)
    {
        parent::__construct($id);
        $this->cms_module_common_selector_type_editorial_type="section";
    }

    protected function get_sub_selectors()
    {
        $sub_selectors= parent::get_sub_selectors();
        $sub_selectors[]='cms_module_common_selector_global_var';
        return $sub_selectors;
    }

    public function execute_ajax()
    {
        global $id_type;
        $response = [];
        $fields = new cms_editorial_parametres_perso($id_type);

        $select ="
		<div class='row'>
			<div class='colonne3'>
				<label for=''>".$this->format_text($this->msg['cms_module_common_selector_type_editorial_fields_label'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='".$this->get_form_value_name("select_field")."' >";
        $select.= $fields->get_selector_options($this->parameters["type_editorial_field"]);
        $select.= "
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

			$query = "SELECT cms_editorial_custom_origine FROM cms_editorial_custom_values
				WHERE cms_editorial_custom_champ = {$fieldId}
                AND cms_editorial_custom_{$datatype} = '{$subSelector->get_value()}'";
			$result = pmb_mysql_query($query);

			if (pmb_mysql_num_rows($result)) {
				$idRubrique = pmb_mysql_result($result, 0, 0);
				$this->value = intval($idRubrique);
			}
		}

		return $this->value;
	}
}
