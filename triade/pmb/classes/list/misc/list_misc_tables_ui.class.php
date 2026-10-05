<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_misc_tables_ui.class.php,v 1.7 2022/09/30 11:41:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_misc_tables_ui extends list_ui {
	
	protected function _init_tables() {
		$result_table = pmb_mysql_query("SHOW TABLES FROM `".DATA_BASE."`");
		while ($table = pmb_mysql_fetch_array($result_table)) {
			$query = "DESCRIBE ".$table[0];
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				$row->Table = $table[0];
				$this->add_object($row);
			}
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_tables();
		$this->messages = "";
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	public function init_filters($filters=array()) {
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'Table');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'Field', 'Type', 'Null', 'Key', 'Default', 'Extra'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 250;
		$this->pager['nb_per_page_on_group'] = true;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'Field' => 'Field',
						'Type' => 'Type',
						'Null' => 'Null',
						'Key' => 'Key',
						'Default' => 'Default',
						'Extra' => 'Extra',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('Field');
		$this->add_column('Type');
		$this->add_column('Null');
		$this->add_column('Key');
		$this->add_column('Default');
		$this->add_column('Extra');
	}
}