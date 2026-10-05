<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cms_cadres_ui.class.php,v 1.1.2.3 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_cms_cadres_ui extends list_ui {
	
    protected $exported_datas;
    
	protected function _get_query_base() {
		return 'SELECT * FROM cms_cadres';
	}
	
	protected function get_object_instance($row) {
	    $cadre_object = $row->cadre_object;
	    if (class_exists($cadre_object)) {
	        return new $cadre_object($row->id_cadre);
	    }
	    return null;
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function init_available_columns() {
		$this->available_columns = array (
				'main_fields' => array (
						'name' => 'Nom du cadre',
				        'datasource' => 'Source de donn&eacute;es',
    				    'filters' => 'Filtres',
    				    'view' => 'Vue',
    				    'conditions' => 'Conditions',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
		$this->add_column('datasource');
		$this->add_column('filters');
		$this->add_column('view');
		$this->add_column('conditions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function get_exported_parameters($object, $property) {
	    if(!isset($this->exported_datas[$object->id])) {
	        $this->exported_datas[$object->id] = $object->get_exported_datas();
	    }
	    $exported_parameters = $this->exported_datas[$object->id][$property]['parameters'] ?? [];
	    
	    return $exported_parameters;
	}
	protected function formatted_exported_parameters($exported_parameters) {
	    $content = '';
	    if (!empty($exported_parameters)) {
	        foreach ($exported_parameters as $key=>$value) {
	            if (is_array($value)) {
// 	                foreach ($value as $sub_key=>$sub_value) {
// 	                    $content .= $sub_key." : ".$sub_value."<br />";
// 	                }
	            } else {
	                if ($value != '') {
	                    $content .= "<b>".$key."</b>";
	                    $content .= "<pre>".$value."</pre>";
	                }
	            }
	        }
	    }
	    return $content;
	}
	
	protected function _get_object_property_datasource($object) {
	    $exported_parameters = $this->get_exported_parameters($object, 'datasource');
	    return $this->formatted_exported_parameters($exported_parameters);
	}
	
	protected function _get_object_property_filters($object) {
	    $exported_parameters = $this->get_exported_parameters($object, 'filters');
	    return $this->formatted_exported_parameters($exported_parameters);
	}
	
	protected function _get_object_property_view($object) {
	    $exported_parameters = $this->get_exported_parameters($object, 'view');
	    return $this->formatted_exported_parameters($exported_parameters);
	}
	
	protected function _get_object_property_conditions($object) {
	    $exported_parameters = $this->get_exported_parameters($object, 'conditions');
	    return $this->formatted_exported_parameters($exported_parameters);
	}

	protected function get_cell_content($object, $property) {
	    global $msg;
	    
	    $content = '';
	    switch($property) {
	        case 'datasource':
	        case 'filters':
	        case 'view':
	        case 'conditions':
	            $method_name = "_get_object_property_".$property;
	            $content .= $this->{$method_name}($object);
	            break;
	        case 'linked_bulletins':
	            $location = static::get_controller_url_base()."&sub=collstate_bulletins_list&id=".$object->id."&serial_id=".$this->filters['serial_id']."&bulletin_id=".$this->filters['bulletin_id'];
	            $content .= $this->get_interface_button($msg['collstate_linked_bulletins_list_link'], ['location' => $location]);
	            break;
	        default :
	            $content .= parent::get_cell_content($object, $property);
	            break;
	    }
	    return $content;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		return $base_path.'/cms.php?categ=cadres';
	}
}