<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_misc_table_index_ui.class.php,v 1.1.4.3 2025/05/21 12:27:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_misc_table_index_ui extends list_ui {
	
    protected function _init_tables() {
        global $tabindexref;
        require_once ("./tables/dataref.inc.php");
        foreach ($tabindexref as $table=>$key_names) {
            $query = "SHOW INDEX FROM $table";
            $result = pmb_mysql_query($query);
            $cles_reelles = array();
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $cles_reelles[$row->Key_name][] = $row->Column_name;
                }
            }
            foreach ($key_names as $key_name=>$columns_name) {
                if (!empty($cles_reelles[$key_name])) {
                    foreach ($columns_name as $column_name) {
                        if (array_search($column_name,$cles_reelles[$key_name])===false) {
                            $object = new stdClass();
                            $object->id = $table."||".$key_name;
                            $object->table = $table;
                            $object->key_name = $key_name;
                            $object->column_name = $column_name;
                            $this->add_object($object);
                            $object->information = 'missing';
                        }
                    }
                } else {
                    $object = new stdClass();
                    $object->id = $table."||".$key_name;
                    $object->table = $table;
                    if ($key_name == 'PRIMARY') {
                        $object->key_name = $key_name;
                    } else {
                        $object->key_name = 'INDEX '.$key_name;
                    }
                    $object->column_name = implode(',', $columns_name);
                    $object->information = 'missing';
//                     $pb .= "<br />-- $table $key_name missing";
                    
                    $this->add_object($object);
                }
            }
        }
    }
    
    protected function fetch_data() {
        $this->objects = array();
        $this->_init_tables();
        $this->pager['nb_results'] = count($this->objects);
        $this->messages = "";
    }
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'table' => 'Table',
						'key_name' => 'Key_name',
				        'column_name' => 'Column_name',
				        'information' => 'Information',
				        'query_sql' => 'SQL query'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('table');
		$this->add_column('key_name');
		$this->add_column('column_name');
		$this->add_column('information');
		//query_sql permet d'afficher la requête SQL de MAJ - juste a titre d'information car pas suffisante a 100%
		//$this->add_column('query_sql'); 
	}
	
	public function get_display_header_list() {
	    return '';
	}

	protected function _get_object_property_query_sql($object) {
	    if ($object->key_name == 'PRIMARY') {
	        $query = "SELECT column_type, is_nullable, COLUMN_DEFAULT, COLUMN_KEY, EXTRA
	        FROM information_schema.columns
	        WHERE table_name = '".$object->table."' AND column_name = '".$object->column_name."'";
	        $result = pmb_mysql_query($query);
	        $data = pmb_mysql_fetch_object($result);
	        return "ALTER TABLE ".$object->table." CHANGE ".$object->column_name." ".$object->column_name." ".$data->column_type." ".($data->is_nullable == 'NO' ? "NOT NULL" : "")." PRIMARY KEY";
	    }
	    return "ALTER TABLE ".$object->table." ADD ".$object->key_name." (".$object->column_name.")";
	}
	
	public function get_error_message_empty_list() {
	    global $msg, $charset;
	    return htmlentities($msg['admin_info_table_index_ok'], ENT_QUOTES, $charset);
	}
}