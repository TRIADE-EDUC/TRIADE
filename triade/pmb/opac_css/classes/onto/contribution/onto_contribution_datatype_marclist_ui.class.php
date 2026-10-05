<?php
// +-------------------------------------------------+
// ï¿½ 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_marclist_ui.class.php,v 1.1 2018/10/23 14:48:35 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/common/onto_common_datatype_list_ui.class.php');

class onto_contribution_datatype_marclist_ui extends onto_common_datatype_marclist_ui {
	
	/**
	 * A dériver pour filtrer la liste des valeurs à afficher dans le sélecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return ($property->pmb_extended['list_values'] ? explode(',', $property->pmb_extended['list_values']) : array());
	}
}