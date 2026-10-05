<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tab.class.php,v 1.9 2023/08/29 14:18:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/tabs/tab.tpl.php");

/**
 * class tab
 * Un menu
 */
class tab {
	
	protected $id;
	
	protected $module;
	
	protected $section;
	
	protected $label_code;
	
	protected $categ;
	
	protected $label;
	
	protected $sub;
	
	protected $url_extra;
	
	protected $number;
	
	protected $destination_link;
	
	protected $visible;
		
	protected $autorisations;
	
	protected $autorisations_all;
	
	protected $shortcut;
	
	protected $order;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->visible = 1;
		$this->autorisations = '';
		$this->autorisations_all = 1;
		$this->get_shortcut();
		if(!$this->id) return;
		
		$query = 'SELECT * FROM tabs WHERE id_tab = '.$this->id;
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->module = $data->tab_module;
		$this->categ = $data->tab_categ;
		$this->sub = $data->tab_sub;
		$this->visible = $data->tab_visible;
		$this->autorisations = $data->tab_autorisations;
		$this->autorisations_all = $data->tab_autorisations_all;
		$this->shortcut = $data->tab_shortcut;
		$this->order = $data->tab_order;
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('tab_visible', 'tab_visible', 'flat')
		->add_input_node('boolean', $this->visible);
		$interface_content_form->add_element('tab_autorisations_all', 'tab_autorisations_all', 'flat')
		->add_input_node('boolean', $this->autorisations_all);
		
		$interface_content_form->add_inherited_element('permissions_users', 'tab_autorisations', 'tab_autorisations')
		->set_autorisations($this->autorisations);
		
		$interface_content_form->add_element('tab_shortcut', 'tab_shortcut')
		->add_input_node('char', $this->get_shortcut());
		$interface_content_form->add_element('tab_module')
		->add_input_node('hidden', $this->module);
		$interface_content_form->add_element('tab_categ')
		->add_input_node('hidden', $this->categ);
		$interface_content_form->add_element('tab_sub')
		->add_input_node('hidden', $this->sub);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_form('tab_form');
		$interface_form->set_label($msg['tab_form_edit']." : ".$this->label);
		$interface_form->set_object_id($this->id)
		->set_content_form($this->get_content_form())
		->set_table_name('tabs');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $tab_module, $tab_categ, $tab_sub;
		global $tab_visible, $autorisations, $tab_autorisations_all, $tab_shortcut, $tab_order;
		
		$this->module = stripslashes($tab_module);
		$this->categ = stripslashes($tab_categ);
		$this->sub = stripslashes($tab_sub);
		$this->visible = intval($tab_visible);
		if (is_array($autorisations)) {
			$this->autorisations=implode(" ",$autorisations);
		} else {
			$this->autorisations="";
		}
		$this->autorisations_all = intval($tab_autorisations_all);
		$this->shortcut = stripslashes($tab_shortcut);
		$this->order = intval($tab_order);
	}
	
	public function save() {
		if($this->id) {
			$query = 'update tabs set ';
			$where = 'where id_tab= '.$this->id;
		} else {
			$query = 'insert into tabs set ';
			$where = '';
		}
		$query .= '
				tab_module = "'.addslashes($this->module).'",
                tab_categ = "'.addslashes($this->categ).'",
				tab_sub = "'.addslashes($this->sub).'",
				tab_visible = "'.$this->visible.'",
				tab_autorisations = "'.addslashes($this->autorisations).'",
				tab_autorisations_all = "'.$this->autorisations_all.'",
				tab_shortcut = "'.addslashes($this->shortcut).'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		$query = 'DELETE FROM tabs WHERE id_tab = '.$id;
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_id() {
	    if(!empty($this->id)) {
	        return $this->id;
	    }
	    return $this->module."_".$this->categ.(!empty($this->sub) ? "_".$this->sub : "");
	}
	
	public function get_module() {
		return $this->module;
	}
	
	public function set_module($module) {
		$this->module = $module;
		return $this;
	}
	
	public function get_section() {
		return $this->section;
	}
	
	public function set_section($section) {
		$this->section = $section;
		return $this;
	}
	
	public function get_label_code() {
		return $this->label_code;
	}
	
	public function set_label_code($label_code) {
		$this->label_code = $label_code;
		return $this;
	}
	
	public function get_categ() {
		return $this->categ;
	}
	
	public function set_categ($categ) {
		$this->categ = $categ;
		return $this;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
		return $this;
	}
	
	public function get_sub() {
		return $this->sub;
	}
	
	public function set_sub($sub) {
		$this->sub = $sub;
		return $this;
	}
	
	public function get_url_extra() {
		return $this->url_extra;
	}
	
	public function set_url_extra($url_extra) {
		$this->url_extra = $url_extra;
		return $this;
	}
	
	public function get_number() {
		return $this->number;
	}
	
	public function set_number($number) {
		$this->number = $number;
		return $this;
	}
	
	public function get_destination_link() {
		return $this->destination_link;
	}
	
	public function set_destination_link($destination_link) {
		$this->destination_link = $destination_link;
		return $this;
	}
	
	public function get_visible() {
		return $this->visible;
	}
	
	public function set_visible($visible) {
		$this->visible = $visible;
		return $this;
	}
	
	public function get_autorisations() {
		return $this->autorisations;
	}
	
	public function set_autorisations($autorisations) {
		$this->autorisations = $autorisations;
		return $this;
	}
	
	public function get_autorisations_all() {
		return $this->autorisations_all;
	}
	
	public function set_autorisations_all($autorisations_all) {
		$this->autorisations_all = $autorisations_all;
		return $this;
	}
	
	public function get_shortcut() {
		global $raclavier;
		
		if(!isset($this->shortcut)) {
			if(!empty($raclavier)) {
				foreach ($raclavier as $rac) {
					if($rac[1] == $this->destination_link) {
						$this->shortcut = $rac[0];
					}
				}
			}
		}
		return $this->shortcut;
	}
	
	public function set_shortcut($shortcut) {
		$this->shortcut = $shortcut;
		return $this;
	}
	
	public function get_order() {
		return $this->order;
	}
	
	public function set_order($order) {
		$this->order = $order;
		return $this;
	}
	
	public function is_in_database() {
		if($this->id) {
			return true;
		}
		$query = 'SELECT * FROM tabs
			WHERE tab_module = "'.addslashes($this->module).'"
			AND tab_categ = "'.addslashes($this->categ).'"
			AND tab_sub="'.addslashes($this->sub).'"';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$data = pmb_mysql_fetch_object($result);
			pmb_mysql_free_result($result);
			$this->id = $data->id_tab;
			$this->fetch_data();
			return true;
		}
		return false;
	}
	
	public function is_substituted() {
		if($this->id) {
			return true;
		}
		return false;
	}
} // end of tab