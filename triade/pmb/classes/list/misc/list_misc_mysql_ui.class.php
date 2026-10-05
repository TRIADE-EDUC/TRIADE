<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_misc_mysql_ui.class.php,v 1.1 2023/02/28 13:51:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_misc_mysql_ui extends list_ui {
	
	protected static $table_action = '';
	
	protected $tables_actions = array();
	
	protected function _init_tables() {
		global $pmb_set_time_limit;
		
		@set_time_limit($pmb_set_time_limit);
		$db = DATA_BASE;
		$list_tables = pmb_mysql_list_tables($db);
		$num_tables = pmb_mysql_num_rows($list_tables);
		
		$tables = array();
		$i = 0;
		while($i < $num_tables) {
			$tables[$i] = pmb_mysql_tablename($list_tables, $i);
			$i++;
		}
		foreach ($tables as $valeur) {
			$object = new stdClass();
			$object->Table = $valeur;
			$object->FirstCharacter = pmb_strtolower(substr($valeur, 0, 1));
			
			if(empty($this->filters['alphabet']) 
					|| (!empty($this->filters['alphabet']) 
							&& $this->strcmp(substr($this->filters['alphabet'], 0, 1), $object->FirstCharacter) <= 0
					&& $this->strcmp(substr($this->filters['alphabet'], 2, 1), $object->FirstCharacter) >= 0)
					) {
				
				$this->add_object($object);
			}
		}
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->_init_tables();
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'alphabet' => 'Alphabet'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	public function init_filters($filters=array()) {
		$this->filters = array(
				'alphabet' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('alphabet');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'Table', 'Op', 'Msg_type', 'Msg_text'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('alphabet');
	}
	
	protected function get_search_filter_alphabet() {
		$options = array(
				'A-G' => 'A-G',
				'H-N' => 'H-N',
				'O-U' => 'O-U',
				'U-Z' => 'U-Z'
		);
		return $this->get_search_filter_simple_selection('', 'alphabet', '', $options);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'Table' => 'Table',
						'Op' => 'Op',
						'Msg_type' => 'Msg_type',
						'Msg_text' => 'Msg_text',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('Table');
		$this->add_column('Op');
		$this->add_column('Msg_type');
		$this->add_column('Msg_text');
	}
	
	protected function _get_object_property_Op($object) {
		return $this->get_table_action($object->Table)->Op;
	}
	
	protected function _get_object_property_Msg_type($object) {
		return $this->get_table_action($object->Table)->Msg_type;
	}
	
	protected function _get_object_property_Msg_text($object) {
		return $this->get_table_action($object->Table)->Msg_text;
	}
	
	public static function is_authorized_action($table_action) {
		if(in_array(strtoupper($table_action), array('CHECK', 'ANALYZE', 'REPAIR', 'OPTIMIZE'))) {
			return true;
		}
		return false;
	}
	public static function set_table_action($table_action) {
		if(static::is_authorized_action($table_action)) {
			static::$table_action = $table_action;
		}
	}
	
	public function get_table_action($table) {
		if(!isset($this->tables_actions[$table][static::$table_action])) {
			$query = static::$table_action." TABLE ".$table." ";
			$result = pmb_mysql_query($query);
			$this->tables_actions[$table][static::$table_action] = pmb_mysql_fetch_object($result);
		}
		return $this->tables_actions[$table][static::$table_action];
	}
}