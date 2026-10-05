<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_ui.class.php,v 1.3 2023/09/29 11:54:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_authorities_ui extends list_ui {
	
	protected function _get_query_base_select() {
		return "SELECT authorities.id_authority AS id, authorities_statuts.authorities_statut_label, authorities.*";
	}
	
	protected function _get_query_base_from() {
		return "FROM authorities JOIN authorities_statuts ON authorities_statuts.id_authorities_statut = authorities.num_statut";
	}
	
	protected function _get_query_base() {
		$query = $this->_get_query_base_select();
		$query .= " ".$this->_get_query_base_from();
		return $query;
	}
	
	protected function get_exclude_fields() {
		return array();
	}
	
	protected function get_describe_field($fieldname, $datasource_name, $prefix) {
		global $msg;
		
		if(isset($msg['search_extended_'.$fieldname])) {
			return $msg['search_extended_'.$fieldname];
		} elseif(substr($fieldname, strlen($fieldname)-2) == 'id') {
			return $msg['1601'];
		}else {
			return $fieldname;
		}
	}
	
	protected function get_describe_fields($table_name, $datasource_name, $prefix) {
		$describe_fields = array();
		$query = "DESCRIBE ".$table_name;
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_assoc($result)) {
			$fieldname = $row['Field'];
			if(!in_array($fieldname, $this->get_exclude_fields())) {
				$describe_fields[$fieldname] = $this->get_describe_field($fieldname, $datasource_name, $prefix);
			}
		}
		return $describe_fields;
	}
	
	protected function get_main_fields() {
		return array();
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$main_fields = $this->get_main_fields();
		$this->available_columns = array(
				'main_fields' => $main_fields,
		);
		$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_mixed_available_columns());
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns as $columns) {
			foreach ($columns as $property=>$label) {
				$this->add_column($property, $label);
			}
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('id_authority');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'isbd_authority':
	        case 'tu_authors':
	        case 'tu_performers':
	        case 'tu_concepts':
	        case 'author_concepts':
	            return '';
	        default :
	            if (isset($this->available_columns['custom_fields']) && array_key_exists($sort_by, $this->available_columns['custom_fields'])) {
	                $sort_by = 'custom_fields';
	            }
	            if ($sort_by == 'custom_fields') {
	                return '';
	            }
	            return $sort_by;
	    }
	}
	
	protected function add_authperso_available_columns() {
	    return array(
	        'authperso_name' => 'search_by_authperso_title'
	    );
	}
	
	protected function add_mixed_available_columns() {
	    return array(
	        'id_authority' => 'cms_authority_format_data_id',
	        'num_object' => 'cms_authority_format_data_db_id',
	        'type_object' => 'include_option_type_donnees',
	        'isbd_authority' => 'cms_authority_format_data_isbd',
	        'authorities_statut_label' => 'search_extended_common_statut',
	        'thumbnail_url' => 'explnum_vignette',
    		'aut_link' => 'aut_link'
	    );
	}
	
	protected function _get_object_property_type_object($object) {
		return authority::get_type_label_from_type_id($object->type_object);
	}
	
	protected function _get_object_property_isbd_authority($object) {
		$authority = new authority($object->id_authority);
		return $authority->get_isbd();
	}
	
	protected function _get_object_property_aut_link($object) {
		$authority = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $object->id);
		$aut_link = $authority->get_aut_link();
		if(!empty($aut_link) && is_object($aut_link)) {
			return $authority->get_aut_link()->get_display();
		}
		return '';
	}
}