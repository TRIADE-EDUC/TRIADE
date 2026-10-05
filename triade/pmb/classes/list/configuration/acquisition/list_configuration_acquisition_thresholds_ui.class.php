<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_thresholds_ui.class.php,v 1.3.8.1 2025/02/21 13:24:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/threshold.class.php");

class list_configuration_acquisition_thresholds_ui extends list_configuration_acquisition_ui {
	
	protected function get_title() {
		$entity = new entites($this->filters['num_entity']);
		return "<div class='row'><label>".$entity->raison_sociale."</label></div>";
	}
	
	protected function _get_query_base() {
		return 'SELECT * FROM thresholds';
	}
	
	protected function get_object_instance($row) {
		return new threshold($row->id_threshold);
	}
	
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'num_entity' => 0,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('threshold_amount');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('amount', 'align', 'center');
		$this->set_setting_column('amount_tax_included', 'align', 'center');
		$this->set_setting_column('amount_tax_included', 'datatype', 'boolean');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('num_entity', 'threshold_num_entity', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => 'threshold_label',
				'amount' => 'threshold_amount',
				'amount_tax_included' => 'threshold_amount_tax_included',
				'footer' => 'threshold_footer',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $pmb_gestion_devise;
		
		$content = '';
		switch($property) {
			case 'amount':
				$content .= number_format($object->get_amount(),'2','.',' ')." ".$pmb_gestion_devise;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->get_id();
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['ajouter'];
	}
	
	protected function get_button_add() {
		return $this->get_button('add', $this->get_label_button_add(), "&id_entity=".$this->filters['num_entity']);
	}
}