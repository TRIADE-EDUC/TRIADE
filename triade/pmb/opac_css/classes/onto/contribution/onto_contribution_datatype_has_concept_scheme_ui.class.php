<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_has_concept_scheme_ui.class.php,v 1.2 2022/01/18 09:44:34 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
global $class_path;
require_once($class_path.'/onto/contribution/onto_contribution_datatype_list_ui.class.php');

class onto_contribution_datatype_has_concept_scheme_ui extends onto_contribution_datatype_list_ui {
	
	protected static function get_options_values($property) {
	    global $lang;
	    $options_values = array();

	    $query = "select distinct id_item, value from skos_fields_global_index where code_champ = 100 and code_ss_champ = 1 and lang IN ( '$lang',  '' ) order by value";	
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_array($result)) {
                $options_values[$row[0]] = $row[1];
            }
        }
	    return $options_values;
	}
	
	public static function get_selector_form($item_uri, $property, $restrictions, $datas, $options_values) {
	    $property->range[0] = 'http://www.w3.org/2000/01/rdf-schema#Literal';
	    return parent::get_selector_form($item_uri, $property, $restrictions, $datas, $options_values);
	}
}