<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_gestion_facettes_root_sets_ui.class.php,v 1.5.4.2 2025/03/20 13:53:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_gestion_facettes_root_sets_ui extends list_configuration_gestion_ui {
	
	public function init_filters($filters=array()) {
		$this->filters = array(
				'type' => '',
                'types' => [],
                'num_user' => 0,
                'users_groups' => []
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('ranking');
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('ranking', 'datatype', 'integer');
	}
	
	protected function get_main_fields_from_sub() {
	    global $current_module;
	    
	    $main_fields = array();
	    if ($current_module == 'account') {
	        $main_fields['ranking'] = 'facettes_set_order';
	    }
	    $main_fields['name'] = 'facettes_set_name';
	    $main_fields['user'] = 'facettes_set_user';
	    $main_fields['users_groups'] = 'facettes_set_users_groups';
	    $main_fields['facettes'] = 'facettes_set_facets';
	    $main_fields['actions'] = 'facettes_set_actions';
	    if ($current_module == 'account') {
	        $main_fields['visible'] = 'facettes_set_visible';
	    }
		return $main_fields;
	}
	
	protected function _add_query_filters() {
	    if(!empty($this->filters['types'])) {
	        $this->_add_query_filter_multiple_restriction('types', 'type');
	    } elseif(!empty($this->filters['type']) && $this->filters['type'] != 'authorities') {
            $this->query_filters [] = 'type LIKE "'.$this->filters['type'].'%"';
	    }
		if (!empty($this->filters['num_user']) && !empty($this->filters['users_groups'])) {
		    $filter_num_user = $this->_get_query_filter_simple_restriction('num_user', 'num_user', 'integer');
		    $filter_users_groups = $this->_get_query_filter_multiple_restriction('users_groups', 'users_groups', 'integer');
		    $this->query_filters [] = '('.$filter_num_user.' OR '.$filter_users_groups.')';
		} else {
		    $this->_add_query_filter_simple_restriction('num_user', 'num_user', 'integer');
		    $this->_add_query_filter_multiple_restriction('users_groups', 'users_groups', 'integer');
		}
	}
	
	protected function get_title() {
	    global $msg, $charset;
	    
	    return '<h3>'.htmlentities($msg['list_configuration_facettes_sets'], ENT_QUOTES, $charset).'</h3>';
	}
	
	public function get_dataset_title() {
	    global $msg, $charset;
	    
	    $dataset_title = parent::get_dataset_title();
	    if (empty($dataset_title)) {
	        $dataset_title = htmlentities($msg['list_configuration_facettes_sets'], ENT_QUOTES, $charset);
	    }
	    return $dataset_title;
	}
	    
	protected function _get_object_property_user($object) {
	    return user::get_name($object->num_user);
	}
	
	protected function _get_object_property_visible($object) {
	    return facettes_sets_users::get_visible($object->id);
	}
	
	protected function _get_object_property_ranking($object) {
	    return facettes_sets_users::get_ranking($object->id);
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg, $charset, $PMBuserid;
		
		$content = '';
		switch($property) {
			case 'ranking':
				$content .= "
					<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=down&id=".$object->id."'\" style='cursor:pointer;'/>
					<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=up&id=".$object->id."'\" style='cursor:pointer;'/>
				";
				break;
			case 'users_groups':
			    $users_groups = encoding_normalize::json_decode($object->users_groups, true);
			    if (!empty($users_groups)) {
			        $groups = [];
			        foreach ($users_groups as $id_group) {
			            $users_group = new users_group($id_group);
			            $groups[] = $users_group->name;
			        }
			        $content .= '<ul><li>';
			        $content .= implode('</li><li>', $groups);
			        $content .= '</li></ul>';
			    }
			    break;
			case 'actions':
			    $users_groups = encoding_normalize::json_decode($object->users_groups, true);
			    if (!is_array($users_groups)) {
			        $users_groups = [];
			    }
			    $user_group = user::get_param($PMBuserid, 'grp_num');
			    if ($PMBuserid == 1 || in_array($user_group, $users_groups) || $object->num_user == $PMBuserid) {
			        $content .= $this->get_interface_button($msg['facettes_set_action_edit'], ['location' => static::get_controller_url_base()."&action=edit&id=".$object->id], ['id' => '".$this->objects_type."_button_edit_".$object->id."', 'name' => '".$this->objects_type."_button_edit']);
			        $content .= $this->get_interface_button($msg['facettes_set_action_view'], ['location' => static::get_controller_url_base()."&action=view&num_facettes_set=".$object->id], ['id' => '".$this->objects_type."_button_view_".$object->id."', 'name' => '".$this->objects_type."_button_view']);
			    }
			    $content .= $this->get_interface_button($msg['duplicate'], ['location' => static::get_controller_url_base()."&action=duplicate&id=".$object->id], ['id' => '".$this->objects_type."_button_duplicate_".$object->id."', 'name' => '".$this->objects_type."_button_duplicate']);
			    break;
			case 'visible':
			    $visible = $this->_get_object_property_visible($object);
			    $content .= "
                    <input type='checkbox' class='switch' id='".$this->objects_type."_visible_".$object->id."' name='".$this->objects_type."_visible_".$object->id."' value='1' ".($visible ? "checked='checked'" : "")." style='display:none'/>
    			    <label for='".$this->objects_type."_visible_".$object->id."'>
                        <span style='color:green;" . (! $visible ? " display:none;" : "") . "'>" . $msg['activated'] . "</span>
                        <span style='color:red;" . ($visible ? " display:none;" : "") . "'>" . $msg['disabled'] . "</span>
                    </label>";
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->id;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'ranking':
			case 'actions':
				return array();
			default :
				return array(
// 						'onclick' => "document.location=\"".$this->get_edition_link($object)."\""
				);
		}
	}
	
	public function get_error_message_empty_list() {
	    global $msg;
	    return $msg['facettes_sets_empty'];
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['facettes_sets_add'];
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	public static function get_controller_url_base() {
	    global $base_path;
	    
	    switch (static::$sub) {
	        case 'facettes_authorities_sets':
	            return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub=facettes_authorities';
	        case 'facettes_sets':
	            return $base_path.'/'.static::$module.'.php?categ='.static::$categ.'&sub=facettes';
	        default:
	            return parent::get_controller_url_base();
	    }
	}
}