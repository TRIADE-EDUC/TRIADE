<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_entities_indexation_ui.class.php,v 1.1.2.8.2.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_entities_indexation_ui extends list_entities_ui {
	
    protected $table_fields = [];
    
    protected $table_words = [];
    
    protected $sphinxDBHandler = null;
    
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    parent::init_available_columns();
	    $this->available_columns['main_fields']['table_words'] = 'indexation_table_words';
	    $this->available_columns['main_fields']['table_words'] = 'indexation_table_words';
	    $this->available_columns['main_fields']['table_fields'] = 'indexation_table_fields';
	    $this->available_columns['main_fields']['table_sphinx'] = 'indexation_table_sphinx';
	    $this->available_columns['main_fields']['state'] = 'indexation_state';
	    $this->available_columns['main_fields']['actions'] = 'indexation_actions';
	}
	
	
	protected function init_default_columns() {
		$this->add_column('entity_label');
		$this->add_column('table_base');
		$this->add_column('table_authorities');
		$this->add_column('table_words');
		$this->add_column('table_fields');
		$this->add_column('table_sphinx');
		$this->add_column('state');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('state', 'datatype', 'boolean');
	}
	
	protected function _get_query_table_words($object) {
	    switch($object->name){
	        case 'notices' :
	            return 'select count(distinct(id_notice)) from notices_mots_global_index';
	        case 'authors' :
	        case 'categories' :
	        case 'collections' :
	        case 'subcollections' :
	        case 'series' :
	        case 'titres_uniformes' :
	        case 'indexint' :
	            $type_table = authority::$type_table[$object->type];
	            return 'select count(distinct(id_authority)) as nb from authorities_words_global_index where type = '.$type_table;
	        case 'authperso':
	            return 'select count(distinct(authorities_words_global_index.id_authority)) as nb from authorities_words_global_index 
                join authorities on authorities_words_global_index.id_authority = authorities.id_authority and type_object = 9
                join authperso_authorities on id_authperso_authority = num_object  
                where authperso_authority_authperso_num = '.$object->id_authperso;
	        case 'concepts' :
	            return 'select count(distinct(id_item)) from skos_words_global_index';
	    }
	}
	
	protected function _init_table_words() {
	    if (empty($this->table_words)) {
	        $query = 'select count(distinct(id_authority)) as nb, type from authorities_words_global_index where type != 9 group by type';
	        $this->table_words = $this->get_numbers_from_query($query);
	    }
	}
	
	protected function _get_object_property_table_words($object) {
	    if(!isset($object->table_words)) {
	        $this->_init_table_words();
	        $type_table = authority::$type_table[$object->type] ?? '';
	        if(isset($this->table_words[$type_table])) {
	            $object->table_words = $this->table_words[$type_table];
	        } else {
	            $object->table_words = $this->get_number_from_query($this->_get_query_table_words($object));
	        }
	    }
	    return $object->table_words;
	}
	
	protected function _get_query_table_fields($object) {
	    switch($object->name){
	        case 'notices' :
	            return 'select count(distinct(id_notice)) from notices_fields_global_index';
	        case 'authors' :
	        case 'categories' :
	        case 'collections' :
	        case 'subcollections' :
	        case 'series' :
	        case 'titres_uniformes' :
	        case 'indexint' :
	            $type_table = authority::$type_table[$object->type];
	            return 'select count(distinct(id_authority)) as nb from authorities_fields_global_index where type = '.$type_table;
            case 'authperso':
	            return 'select count(distinct(authorities_fields_global_index.id_authority)) as nb from authorities_fields_global_index 
                    join authorities on authorities_fields_global_index.id_authority = authorities.id_authority and type_object = 9
                    join authperso_authorities on id_authperso_authority = num_object  
                    where authperso_authority_authperso_num = '.$object->id_authperso;
	        case 'concepts' :
	            return 'select count(distinct(id_item)) from skos_fields_global_index';
	    }
	}
	
	protected function _init_table_fields() {
	    if (empty($this->table_fields)) {
	        $query = 'select count(distinct(id_authority)) as nb, type from authorities_fields_global_index where type != 9 group by type';
	        $this->table_fields = $this->get_numbers_from_query($query);
	    }
	}
	
	protected function _get_object_property_table_fields($object) {
	    if(!isset($object->table_fields)) {
	        $this->_init_table_fields();
	        $type_table = authority::$type_table[$object->type] ?? '';
	        if(isset($this->table_fields[$type_table])) {
	            $object->table_fields = $this->table_fields[$type_table];
	        } else {
	            $object->table_fields = $this->get_number_from_query($this->_get_query_table_fields($object));
	        }
	    }
	    return $object->table_fields;
	}
	
	protected function _get_query_table_sphinx($object) {
	    global $sphinx_indexes_prefix;
	    
	    switch($object->name){
	        case 'notices' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'records';
	        case 'authors' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'authors';
	        case 'categories' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'categories';
	        case 'publishers' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'publishers';
	        case 'collections' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'collections';
	        case 'subcollections' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'subcollections';
	        case 'series' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'series';
	        case 'titres_uniformes' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'titres_uniformes';
	        case 'indexint' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'indexint';
	        case 'authperso' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'authperso_'.$object->id_authperso;
	        case 'concepts' :
	            return 'select count(*) from '.$sphinx_indexes_prefix.'concepts';
	    }
	}
	
	protected function _get_object_property_table_sphinx($object) {
	    global $sphinx_active;
	    
	    if(!$sphinx_active){
	        return 'disable';
	    }
	    if (empty($this->sphinxDBHandler)) {
	        $sb = new sphinx_base();
	        $this->sphinxDBHandler = $sb->getDBHandler();
	    }
	    if(!isset($object->table_sphinx)) {
	        $query = $this->_get_query_table_sphinx($object);
	        $result = pmb_mysql_query($query, $this->sphinxDBHandler);
	        if (pmb_mysql_num_rows($result)) {
	            $object->table_sphinx = pmb_mysql_result($result,0,0);
	        }
	    }
	    return $object->table_sphinx;
	}
	
	protected function _get_object_property_state($object) {
	    global $sphinx_active;
	    
	    switch($object->name){
	        case 'notices' :
	            if($object->table_base == $object->table_fields && $object->table_fields == $object->table_words) {
	                if($sphinx_active == 0 || ($object->table_words == $object->table_sphinx)){
	                    return 1;
	                }
	            }
	            break;
	        default:
	            if($object->table_base == $object->table_fields && $object->table_fields == $object->table_words &&  $object->table_words == $object->table_authorities) {
	                if($sphinx_active == 0 || ($object->table_words == $object->table_sphinx)){
	                    return 1;
	                }
	            }
	            break;
	    }
	    return 0;
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg;
	    global $sphinx_active;
	    
	    $content = '';
	    switch($property) {
	        case 'state':
	            $state = $this->_get_object_property_state($object);
	            if ($state) {
	                $content .= "<img src='images/tick.gif' style='height:16px;'/>";
	            } else {
	                $content .= "<img src='images/error.gif' style='height:16px;' />";
	            }
	            break;
	        case 'actions':
                $location = static::get_controller_url_base()."&name=".$object->name;
                if (!empty($object->id_authperso)) {
                    $location .= "&id_authperso=".$object->id_authperso;
                }
	            //Voir le détails
                if ($object->name != 'concepts') {
                    $content .= $this->get_interface_button_small($msg['see'], ['location' => $location]);
                }
	            //Indexer
                $content .= $this->get_interface_button_small($msg['index'], ['location' => $location."&action=reindex"]);
	            //Indexer dans Sphinx
	            if ($sphinx_active) {
	                $content .= $this->get_interface_button_small('[Sphinx] '.$msg["index"], ['location' => $location."&action=reindex_sphinx"]);
	            }
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
}