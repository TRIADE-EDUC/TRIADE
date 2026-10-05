<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_external_services_general_ui.class.php,v 1.4 2023/12/22 13:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_ui.class.php");

class list_configuration_external_services_general_ui extends list_configuration_external_services_ui {
	
	protected $es_rights;
	
	protected $es_groups;
	
	protected $es_rights_groups;
	
	protected function fetch_data() {
		$this->objects = array();
		
		$es = new external_services();
		$this->es_rights = new external_services_rights($es);
		$this->es_groups = array();
		$group_list=$es->get_group_list();
		for ($i=0; $i<count($group_list); $i++) {
			$group=$group_list[$i];
			$this->es_groups[$group["name"]]=$group;
			//Pour chaque méthode
			if(isset($group["methods"]) && is_array($group["methods"])) {
				for ($j=0; $j<count($group["methods"]); $j++) {
					$method=$group["methods"][$j];
					$method['group_name'] = $group["name"];
					$this->add_object((object) $method);
				}
			}
		}
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('name', 'text', array('bold' => true));
		$this->set_setting_column('description', 'text', array('italic' => true));
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'group_name');
	}
	
	public function get_display_search_form() {
		//Ne pas retourner le formulaire car non compatible avec la sauvegarde des droits
		return '';
	}
	
	public function get_display_header_list() {
		global $msg, $charset;
		
		$display = '<thead>';
		$display .= '<tr>';
		$display .= '<th colspan="3" scope="colgroup">Groupe</th>';
		$display .= '<th colspan="3" scope="colgroup">'.htmlentities($msg['external_services_general_utilisateurs_autorises'],ENT_QUOTES,$charset).'</th>';
		$display .= '</tr>';
		$display .= '</thead>';
		return $display;
	}
	
	protected function users_list($group, $method, $users, $parent_users) {
		global $msg, $charset;
		
		$list_users=$this->es_rights->possible_users($group,$method);
		$count = 0;
		
		$result="<ul>\n";
		for ($j=0; $j<count($list_users); $j++) {
			if (array_search($list_users[$j],$users)!==false) {
				//Si l'utilisateur a les droits pour le groupe entier, on ne l'affiche pas dans le détail
				$group_authorized = in_array($this->es_rights->users[$list_users[$j]]->userid, $parent_users);
				if (!$group_authorized) {
					$page_link_href = 'admin.php?categ=external_services&sub=peruser&iduser='.$this->es_rights->users[$list_users[$j]]->userid.'#'.urlencode($group).($method ? '_'.urlencode($method) : "");
					$user_name_display = htmlentities($this->es_rights->users[$list_users[$j]]->username,ENT_QUOTES,$charset);
					$result.="<li><a href=".$page_link_href.">".$user_name_display."</a></li>\n";
					++$count;
				}
			}
		}
		$result.="</ul>";
		
		//A-t-on trouvé des utilisateur? Si non, on affiche 'Aucun'
		if (!$count) {
			return "<ul><li><i>".htmlentities($msg["es_user_auth_none"],ENT_QUOTES,$charset)."</i></li></ul>";
		}
		return $result;
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		global $msg, $charset;
		
		$group = $this->es_groups[$group_label];
		$display = "
		<tr id='".$uid."_group_header'>
			<td>
				<b>".htmlentities($group["name"],ENT_QUOTES,$charset)."</b>
			</td>
			<td colspan='2'>
				<i>".htmlentities($group["description"],ENT_QUOTES,$charset)."</i>
			</td>
			<td>
				<input type='hidden' name='group[".$group["name"]."]' value='1'/>
			</td>
			<td colspan='3'>
				".$this->users_list($group["name"],'',$this->get_es_rights_group($group["name"])->users,array())."
			</td>
		</tr>
		<thead>
			<tr>
				<th scope='colgroup' style='all : unset;'></th>
				<th colspan='2' scope='colgroup'>".htmlentities($msg["external_services_general_methode"],ENT_QUOTES,$charset)."</th>
				<th colspan='3' scope='colgroup'>".htmlentities($msg["external_services_general_utilisateurs_autorises"],ENT_QUOTES,$charset)."</th>
			<tr>
		</thead>";
		return $display;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'empty1' => 'empty1',
				'name' => 'Groupe',
				'description' => 'Description',
				'empty2' => 'empty2',
				'empty3' => 'empty3',
				'rights_users' => 'external_services_general_utilisateurs_autorises',
		);
	}
	
	protected function get_es_rights_group($name) {
		if(!isset($this->es_rights_groups[$name])) {
			$this->es_rights_groups[$name] = $this->es_rights->get_rights($name,"");
		}
		return $this->es_rights_groups[$name];
	}
	
	protected function _get_object_property_rights_users($object) {
		$rights=$this->es_rights->get_rights($object->group_name,$object->name);
		$rights_group=$this->get_es_rights_group($object->group_name);
		return $this->users_list($object->group_name,$object->name,$rights->users,$rights_group->users);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'rights_users':
				$content .= $this->_get_object_property_rights_users($object);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_button_add() {
		return '';
	}
}