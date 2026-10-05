<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_tpl_ui.class.php,v 1.2 2022/03/01 08:01:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_tpl_ui extends list_configuration_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'edit';
		static::$categ = 'tpl';
		static::$sub = str_replace(array('list_configuration_tpl_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('id', 'datatype', 'integer');
		$this->set_setting_column('id', 'align', 'right');
		$this->set_setting_column('id', 'text', array('bold' => true));
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'id' => 'template_id',
				'name' => 'template_name',
				'comment' => 'template_description'
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->get_id();
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['template_ajouter'];
	}
}