<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_indexation_fields_ui.class.php,v 1.1.2.4 2024/11/21 07:19:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_indexation_fields_ui extends list_indexation_ui {
    
    protected $origin_table_fields = array();
    
    protected function _init_fields() {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            $this->add_object((object) $field);
        }
    }
    
    protected function add_field($id, $label, $field=[]) {
        parent::add_field($id, $label, $field);
        if (!empty($field['type']) && $field['type'] == 'custom') {
        	$this->origin_fields_number[$id] = $this->get_count_field_from_query("SELECT count(*) FROM ".$field['table']."_custom_values WHERE ".$field['table']."_custom_champ = ".($id- $field['id']));
        } elseif (!empty($field['TABLE'][0]['NAME'])) {
//             $this->origin_fields_number[$id] = $this->get_count_entity_from_query();
        } elseif (!empty($field['TABLE'][0]['TABLEFIELD'][0]['value'])) {
            $this->origin_fields_number[$id] = $this->get_count_field_from_query("SELECT count(*) FROM ".$this->reference_table." WHERE ".$field['TABLE'][0]['TABLEFIELD'][0]['value']." != ''");
        }
    }
    
    protected function fetch_data() {
        $this->set_filters_from_form();
        $this->objects = array();
        $this->_init_fields();
        $this->pager['nb_results'] = count($this->objects);
        $this->messages = "";
    }
    
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('entity_type');
		$this->add_selected_filter('field');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
    			    'id' => 'indexation_code',
    			    'field' => 'indexation_label',
    			    'pond' => 'indexation_pond',
                    'state' => 'indexation_state',
                    'actions' => 'indexation_actions'
			)
		);
	}
	
	protected function init_default_columns() {
	    $this->add_column('id');
		$this->add_column('field');
		$this->add_column('pond');
		$this->add_column('state');
		$this->add_column('actions');
	}	
	
	protected function _get_object_property_state($object) {
	    if(!isset($object->state)) {
	        $object->state = 0;
	        $this->_init_table_fields();
	        if (!empty($object->field['type']) && $object->field['type'] == 'custom') {
	            $code_champ = $object->field['id'];
	            $code_ss_champ = ($object->id-$object->field['id']);
	        } else {
	            $code_champ = $object->id;
	            $code_ss_champ = 0;
	        }
	        $object->state = (!empty($this->table_fields[$code_champ][$code_ss_champ]) ? $this->table_fields[$code_champ][$code_ss_champ] : 0);
	    }
	    return $object->state;
	}
	
	protected function get_count_field_from_query($query) {
	    $count = 0;
	    if ($query) {
	        $result = pmb_mysql_query($query);
	        if (pmb_mysql_num_rows($result)) {
	            $count = pmb_mysql_result($result, 0);
	        }
	    }
	    return $count;
	}
	
	public function get_origin_fields_number($object) {
	    if (isset($this->origin_fields_number[$object->id])) {
	        return $this->origin_fields_number[$object->id];
	    }
	    return false;
	}
}