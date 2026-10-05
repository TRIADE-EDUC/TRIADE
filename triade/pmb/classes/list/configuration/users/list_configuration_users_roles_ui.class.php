<?php
use Pmb\Users\Models\RoleModel;
use Pmb\Users\Models\RoleRightModel;

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_users_roles_ui.class.php,v 1.1.2.5 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_users_roles_ui extends list_configuration_users_ui {
	
    protected $members;
    
    protected $modules;
    
    protected $list_modules_data;
    
	protected function _get_query_base() {
		return 'SELECT * FROM users_roles';
	}
	
	protected function init_default_columns() {
	    $this->add_column_selection();
	    parent::init_default_columns();
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_column('members', 'edition_type', 'checkbox');
	    $this->set_setting_column('modules', 'edition_type', 'checkbox');
	}
	
	protected function init_available_editable_columns() {
	    $this->available_editable_columns = array(
	        'members',
	        'modules'
	    );
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'role_name',
    		    'members' => 'role_members',
    		    'modules' => 'role_modules',
				'comment' => 'role_comment',
		);
	}
	
	protected function _compare_members($a, $b) {
	    if ($a['label'] == $b['label']) {
	        return 0;
	    }
	    return (strtolower(convert_diacrit($a['label'])) < strtolower(convert_diacrit($b['label']))) ? -1 : 1;
	}
	
	protected function _get_members($object) {
	    global $base_path, $msg;
	    
	    if (!isset($this->members[$object->id])) {
	        $this->members[$object->id] = array();
	        $query = "SELECT type_member, num_member FROM users_roles_members WHERE num_role =".$object->id;
	        $result = pmb_mysql_query($query);
	        while ($row = pmb_mysql_fetch_object($result)) {
	            if($row->type_member == 'group') {
	                if ($row->num_member) {
    	                $users_group = new users_group($row->num_member);
    	                $label = $users_group->name;
                        $permalink = $base_path."/admin.php?categ=users&sub=groups&action=modif&id=".$row->num_member;
	                } else {
	                    $label = $msg['admin_usr_grp_non_aff'];
	                    $permalink = "";
	                }
	            } else {
	                $label = user::get_name($row->num_member);
	                $permalink = $base_path."/admin.php?categ=users&sub=users&action=modif&id=".$row->num_member;
	            }
	            $this->members[$object->id][] = array(
	                'type' => $row->type_member,
	                'id' => $row->num_member,
	                'label' => $label,
	                'permalink' => $permalink
	            );
	        }
	        //Tri des libellés
	        uasort($this->members[$object->id], array($this, '_compare_members'));
	    }
	    return $this->members[$object->id];
	}
	
	protected function _get_object_property_members($object) {
	    $labels = array();
	    $members = $this->_get_members($object);
	    foreach ($members as $member) {
	        $labels[] = $member['label'];
	    }
	    return implode(' ', $labels);
	}
	
	protected function _get_list_modules_data() {
	    if (empty($this->list_modules_data)) {
	        list_modules_ui::set_no_check_rights(true);
	        $this->list_modules_data = array();
	        $objects_data = list_modules_ui::get_instance([], [], ['by' => 'label'])->get_objects_data();
	        foreach ($objects_data as $object_data) {
	            $this->list_modules_data[$object_data['name']] = $object_data;
	        }
	    }
	    return $this->list_modules_data;
	}
	protected function _get_modules($object) {
	    if (!isset($this->modules[$object->id])) {
	        $this->_get_list_modules_data();
	        $this->modules[$object->id] = array();
	        $query = "SELECT module FROM users_roles_rights WHERE num_role =".$object->id." AND component='module' AND visible=1";
	        $result = pmb_mysql_query($query);
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $this->modules[$object->id][] = $this->list_modules_data[$row->module]['label'];
	        }
	    }
	    return $this->modules[$object->id];
	}
	
	protected function _get_object_property_modules($object) {
	    $modules = $this->_get_modules($object);
	    return implode(' ', $modules);
	}
	
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'members':
			    $labels = array();
			    $members = $this->_get_members($object);
			    foreach ($members as $member) {
			        $labels[] = "<a href='".$member['permalink']."' target='_blank'>".$member['label']."</a>";
			    }
			    $content .= implode('<br />', $labels);
			    break;
			case 'modules':
			    $modules = $this->_get_modules($object);
			    $content .= implode('<br />', $modules);
			    break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
	    return array(
	        'onclick' => "document.location=\"".static::get_controller_url_base().'&action=edit&id='.$object->id."\"",
	    );
	}
	
	protected function init_default_selection_actions() {
	    global $msg;
	    
	    parent::init_default_selection_actions();
	    //Bouton modifier
// 	    $edit_link = array(
// 	        'showConfiguration' => static::get_controller_url_base()."&action=list_save"
// 	    );
// 	    $this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
	    
	    //Bouton supprimer
	    // 		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'delete'));
	}
	
	protected function get_options_editable_column($object, $property) {
	    switch ($property) {
	        case 'members':
	            $options = array();
	            $objects_data = list_users_ui::get_instance()->get_objects_data();
	            foreach ($objects_data as $object_data) {
	                $options[] = array('value' => $object_data['userid'], 'label' => $object_data['prenom'].' '.$object_data['nom'].' ('.$object_data['username'].')');
	            }
	            $objects_data = list_configuration_users_groups_ui::get_instance()->get_objects_data();
	            foreach ($objects_data as $object_data) {
	                $options[] = array('value' => $object_data['grp_id'], 'label' => $object_data['grp_name']);
	            }
	            return $options;
	        case 'modules':
	            $options = array();
	            $this->_get_list_modules_data();
	            foreach ($this->list_modules_data as $object_name=>$object_data) {
	                $options[] = array('value' => $object_name, 'label' => $object_data['label']);
	            }
	            return $options;
	        default:
	            return parent::get_options_editable_column($object, $property);
	    }
	}
	
	protected function save_object($object, $property, $value) {
	    switch ($property) {
	        case 'members':
	            
	            break;
	        case 'modules':
                $roleModel = new RoleModel($object->id);
                $roleModel->modules = array();
                foreach ($value as $module_name) {
                    $roleRightModel = new RoleRightModel();
                    $roleRightModel->component = 'module';
                    $roleRightModel->module = $module_name;
//                     $roleRightModel->setPropertiesFromForm($module);
                    $this->modules[] = $roleRightModel;
                }
	            break;
	        default:
	            parent::save_object($object, $property, $value);
	            break;
	    }
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['role_add'];
	}
}