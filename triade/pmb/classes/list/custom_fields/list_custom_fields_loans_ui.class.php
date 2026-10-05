<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_custom_fields_loans_ui.class.php,v 1.3 2020/11/05 12:26:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_custom_fields_loans_ui extends list_custom_fields_ui {
	
	protected function _get_query_base() {
		$query = "select idchamp as id, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export, filters, exclusion_obligatoire, opac_sort, comment, custom_classement from
				".static::$prefix."_custom";
		return $query;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['filters'] = 'parperso_filters';
	}
}