<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_indexation_entities_ui.class.php,v 1.1.2.3 2024/10/03 08:29:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_indexation_entities_ui extends list_indexation_ui {
	
    protected function _get_query_base() {
        return "SELECT DISTINCT ".$this->_get_query_primary_key()." as id, 
            count(DISTINCT code_champ) AS code_champ, group_concat(value SEPARATOR ' ** ') as value 
            FROM ".$this->_get_query_table_name();
    }
    
    /**
     * Tri SQL
     */
    protected function _get_query_order() {
        return " group by id ".parent::_get_query_order();
    }
    
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity_type');
		$this->add_selected_filter('field');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('id');
		$this->add_selected_filter('i_value');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'id' => 'indexation_id',
					'field' => 'indexation_field',
					'sub_field' => 'indexation_sub_field',
					'i_value' => 'indexation_i_value',
					'pond' => 'indexation_pond',
                    'actions' => 'indexation_actions'
			)
		);
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'field', 'sub_field'
		);
	}
	
	protected function init_default_columns() {
	    $this->add_column('id');
		$this->add_column('i_value');
		$this->add_column('actions');
	}
}