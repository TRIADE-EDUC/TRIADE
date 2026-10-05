<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_external_services_peruser_ui.class.php,v 1.4 2023/12/22 13:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/list/configuration/external_services/list_configuration_external_services_general_ui.class.php");

class list_configuration_external_services_peruser_ui extends list_configuration_external_services_general_ui {
	
	protected static $num_user;
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('rights_users', 'datatype', 'boolean');
	}
	
	public function get_display_header_list() {
		$display = '<thead>';
		$display .= '<tr>';
		$display .= '<th colspan="3" scope="colgroup">Groupe</th>';
		$display .= "<th colspan='3' scope='colgroup'>Droits pour l'utilisateur</th>";
		$display .= '</tr>';
		$display .= '</thead>';
		return $display;
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		global $msg, $charset;
		
		$group = $this->es_groups[$group_label];
		$full_group_allowed = array_search(static::$num_user,$this->get_es_rights_group($group["name"])->users)!==false;
		
		$display = "
		<tr id='".$uid."_group_header'>
			<td><br /><b>".htmlentities($group["name"],ENT_QUOTES,$charset)."</b><br /><br /></td><td colspan='2'><i>".htmlentities($group["description"],ENT_QUOTES,$charset)."</i></td>
			<td colspan=\"3\">
				<a name=\"".htmlentities($group["name"], ENT_QUOTES, $charset)."\"/><input id=\"nonavailable_".$group["name"]."\" name=\"grp_right[".$group["name"]."]\" ".($full_group_allowed ? "checked" : "")." value=\"1\" onclick=\"enable_or_disable_group_checboxes('".$group["name"]."')\" type=\"checkbox\">&nbsp;<label class='label' for='nonavailable_".htmlentities($group["name"],ENT_QUOTES,$charset)."'>Autoriser tout</label>
			</td>
		</tr>
		<thead>
			<tr>
				<th scope='colgroup' style='all : unset;'></th>
				<th colspan='2' scope='colgroup'>".htmlentities($msg["external_services_peruser_methode"],ENT_QUOTES,$charset)."</th>
				<th colspan='3' scope='colgroup'>".htmlentities($msg["external_services_peruser_methode_autorisees"],ENT_QUOTES,$charset)."</th>
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
				'rights_users' => 'external_services_general_utilisateurs_autorises',
				'empty3' => 'empty3',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		switch($property) {
			case 'rights_users':
				$full_rights=$this->es_rights->get_rights($object->group_name,"");
				$full_group_allowed = array_search(static::$num_user,$full_rights->users)!==false;
				
				$rights=$this->es_rights->get_rights($object->group_name,$object->name);
				$has_basics=(!$this->es_rights->has_basic_rights(static::$num_user,$object->group_name,$object->name)?"disabled='disabled'":"");
				
				$method_checked = !$full_group_allowed && array_search(static::$num_user,$rights->users)!==false;
				$method_enabled = !$full_group_allowed;
				return "
					<a name=\"".htmlentities($object->group_name, ENT_QUOTES, $charset).'_'.htmlentities($object->name, ENT_QUOTES, $charset)."\"/>
					<input type='checkbox' es_group='".$object->group_name."' $has_basics value='1' ".(!$method_enabled ? "disabled" : "")." ".($method_checked ? "checked" : "")." name='mth_right[".htmlentities($object->group_name."][".$object->name."]",ENT_QUOTES,$charset)."]' id='available_".htmlentities($object->group_name."_".$object->name,ENT_QUOTES,$charset)."'>
				";
			default :
				return parent::get_cell_content($object, $property);
		}
	}
	
	protected function _get_object_property_rights_users($object) {
		$full_rights=$this->es_rights->get_rights($object->group_name,"");
		$full_group_allowed = array_search(static::$num_user,$full_rights->users)!==false;
		
		$rights=$this->es_rights->get_rights($object->group_name,$object->name);
		return !$full_group_allowed && array_search(static::$num_user,$rights->users)!==false;
	}
	
	public static function set_num_user($num_user) {
		static::$num_user = intval($num_user);
	}
	
	public function get_users_selector($selected=0) {
		global $charset;
		
		$users_selector = "<select name='iduser' onChange=\"document.location='".static::get_controller_url_base()."&iduser='+this.value;\">\n";
		foreach ($this->es_rights->users as $userid=>$user) {
			$users_selector .= "	<option value='".$userid."' ".($userid==$selected?"selected":"").">".htmlentities($user->username,ENT_QUOTES,$charset)."</option>\n";
		}
		$users_selector .= "</select>";
		return $users_selector;
	}
}