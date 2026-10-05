<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_lists_datasets_ui.class.php,v 1.5 2024/01/26 09:14:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_lists_datasets_ui extends list_lists_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT id_list as id, lists.* FROM lists';
		return $query;
	}
	
	protected function get_object_instance($row) {
		global $charset;
		
		if (is_object($row->instance) && $row->instance->get_objects_type()) {
		    $objects_type = $row->instance->get_objects_type();
		} else {
		    $objects_type = $row->type;
		}
		
		if(strpos($objects_type, 'authorities_caddie_content_ui_') === 0) {
			$object_type = str_replace('authorities_caddie_content_ui_', '', $objects_type);
			$list_ui_class_name = 'list_authorities_caddie_content_ui';
			$list_ui_class_name::set_object_type($object_type);
		} elseif(strpos($objects_type, 'empr_caddie_content_ui_') === 0) {
			$object_type = str_replace('empr_caddie_content_ui_', '', $objects_type);
			$list_ui_class_name = 'list_empr_caddie_content_ui';
			$list_ui_class_name::set_object_type($object_type);
		} elseif(strpos($objects_type, 'caddie_content_ui_') === 0) {
			$object_type = str_replace('caddie_content_ui_', '', $objects_type);
			$list_ui_class_name = 'list_caddie_content_ui';
			$list_ui_class_name::set_object_type($object_type);
		} else {
			$list_ui_class_name = 'list_'.$objects_type;
		}
		
		$object = $row;
		$object->class_name = $list_ui_class_name;
		$object->instance = $this->get_list_ui_instance($object->class_name);
		$object->num_dataset = $row->id_list;
		$object->label = $row->list_label;
		if(empty($object->label)) {
			$object->label = html_entity_decode($object->instance->get_dataset_title(), ENT_QUOTES, $charset);
		}
		return $object;
	}
	
	protected function get_xmlfile_lists() {
	    if (file_exists($this->get_list_directory()."datasources_subst.xml")) {
	        return $this->get_list_directory()."datasources_subst.xml";
	    } else {
	        return $this->get_list_directory()."datasources.xml";
	    }
	}
	
	protected function get_list_info_from_directories($row) {
	    $directories_lists=$this->get_directories_lists();
	    if(!empty($directories_lists)) {
	        foreach ($directories_lists as $directory_lists) {
	            foreach ($directory_lists as $lists) {
	                foreach ($lists['LIST'] as $list) {
	                    if ($list['NAME'].'_ui' == $row->list_objects_type) {
	                        return array(
	                            'directory_path' => $lists['PATH'][0]['value'],
	                            'directory_label' => $lists['LABEL'][0]['value'],
	                            'details' => $list
	                        );
	                    }
	                }
	            }
	        }
	    }
	}
	
	protected function add_object($row) {
	    $list_info = $this->get_list_info_from_directories($row);
	    if(!empty($list_info)) {
	        $builded_object = $this->get_builded_object($list_info['details'], $list_info['directory_path'], $list_info['directory_label']);
	        
	        $merged_object = (object) array_merge(
	            (array) $builded_object, (array) $row);
	        parent::add_object($merged_object);
	    }
	}
	
	protected function fetch_data() {
	    list_ui::set_without_data(true);
	    list_ui::fetch_data();
	    list_ui::set_without_data(false);
	}
	
	protected function _add_query_filters() {
		$this->query_filters [] = 'list_num_user <> 0';
		
		global $PMBuserid;
		$this->filters['autorisations'] = array($PMBuserid);
		
		if(!empty($this->filters['autorisations'])) {
			$filters_autorisations = array();
			foreach ($this->filters['autorisations'] as $autorisation) {
				$filters_autorisations [] = "(list_autorisations='".$autorisation."' or list_autorisations like '".$autorisation." %' or list_autorisations like '% ".$autorisation." %' or list_autorisations like '% ".$autorisation."')";
			}
			$this->query_filters [] = implode(' or ', $filters_autorisations);
		}
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'ranking');
	}
	
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['ranking'] = 'Classement';
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('name');
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column_execute();
		$this->add_column('label');
		$this->add_column('default_selected_filters');
		$this->add_column('default_selected_columns');
		$this->add_column('default_applied_sort');
		$this->add_column('default_pager');
		$this->add_column('default_applied_group');
	}

	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'visible', true);
	}
	
	protected function add_column_execute() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['708'],
				'link' => static::get_controller_url_base().'&action=play&id=!!id!!'
		);
		$this->add_column_simple_action('play', '', $html_properties);
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_button('edit', $msg['lists_dataset_add']);
	}
	
	protected function get_display_left_actions() {
		return $this->get_button_add();
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		list_ui::init_default_selection_actions();
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['list_dataset_delete_confirm']
		);
		$this->add_selection_action('delete', $msg['63'], '', $delete_link);
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id=".$object->num_dataset."\"";
		return $attributes;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!class_name!!', $object->class_name, $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function _get_object_property_ranking($object) {
		global $msg;
		
		if(!empty($object->list_num_ranking)) {
			$procs_classement = new procs_classement($object->list_num_ranking);
			return $procs_classement->libelle;
		}
		return $msg['proc_clas_aucun'];
	}
}