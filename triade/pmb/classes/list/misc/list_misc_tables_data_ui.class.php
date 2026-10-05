<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_misc_tables_data_ui.class.php,v 1.3 2023/09/29 06:46:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_misc_tables_data_ui extends list_ui {
	
	protected static $tables;
	
	protected static $table;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$table;
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected static function _init_tables() {
		if(!isset(static::$tables)) {
			static::$tables = array();
			$db = DATA_BASE;
			$tables = pmb_mysql_list_tables($db);
			$num_tables = @pmb_mysql_num_rows($tables);
			
			$i = 0;
			while($i < $num_tables) {
				static::$tables[$i] = pmb_mysql_tablename($tables, $i);
				$i++;
			}
		}
	}
	
	public static function get_selector_tables() {
		global $charset;
		
		static::_init_tables();
		$selector = "<select id='misc_tables_data_ui_table' name='table' class='misc_tables_data_ui_simple_selector' onchange=\"document.location='".static::get_controller_url_base()."&table='+this.value\">";
		foreach (static::$tables as $tablename) {
			$selector .= "<option value='".htmlentities($tablename, ENT_QUOTES, $charset)."' ".($tablename == static::$table ? "selected='selected'" : "").">";
			$selector .= htmlentities($tablename, ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function _get_query_base() {
		return 'select * from '.static::$table;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				)
		);
		$this->available_filters['custom_fields'] = array();
	}/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	
	
	protected function init_default_selected_filters() {
		$this->selected_filters = array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
				array('main_fields' => array()
		);
		$query = "DESC ".static::$table;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->available_columns['main_fields'][$row->Field] = $row->Field;
				if(strpos($row->Type, 'int') !== false) {
					$this->set_setting_column($row->Field, 'datatype', 'integer');
				} elseif(strpos($row->Type, 'date') !== false) {
					$this->set_setting_column($row->Field, 'datatype', 'date');
				}
			}
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort(array_key_first($this->available_columns['main_fields']), 'desc');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    return $sort_by;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_display('objects_list', 'fast_filters', true);
		$this->set_setting_column('default', 'fast_filter', 1);
	}
	
	public static function set_table($table='') {
		if(empty($table)) {
			static::_init_tables();
			$table = static::$tables[0];
		}
		static::$table = $table;
	}
}