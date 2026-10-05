<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_list_ui.class.php,v 1.2 2021/08/19 12:08:15 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/common/onto_common_datatype_list_ui.class.php');

class onto_contribution_datatype_list_ui extends onto_common_datatype_list_ui {
	
	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return ($property->pmb_extended['list_values'] ? explode(',', $property->pmb_extended['list_values']) : array());
	}
	
	public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
	    $template = "";
	    if (!empty($property->pmb_extended['template']) && $property->pmb_extended['template']) {
    	    global $ontology_contribution_tpl;
    	    $template = str_replace('!!prefix_id!!', $instance_name.'_'.$property->pmb_name, $ontology_contribution_tpl['list_script_template_tag']);
	    }
	    $template .= parent::get_form($item_uri, $property, $restrictions, $datas, $instance_name, $flag);
	    return $template;
	}
}