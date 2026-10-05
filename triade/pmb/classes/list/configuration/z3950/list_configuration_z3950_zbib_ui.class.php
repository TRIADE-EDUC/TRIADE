<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_z3950_zbib_ui.class.php,v 1.3 2021/04/19 07:10:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_z3950_zbib_ui extends list_configuration_z3950_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM z_bib';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('bib_nom');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'bib_nom' => 'zbib_nom',
				'base' => 'zbib_base',
				'search_type' => 'zbib_utilisation',
				'nb_attr' => 'zbib_nb_attr',
		);
	}
	
	protected function _get_object_property_nb_attr($object) {
		$query = "select count(*) as nb_attr from z_attr where attr_bib_id = ".$object->bib_id;
		return pmb_mysql_result(pmb_mysql_query($query), 0);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->bib_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['ajouter'];
	}
}