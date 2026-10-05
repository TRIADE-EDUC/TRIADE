<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_mailtpl_files_ui.class.php,v 1.5 2023/03/07 15:30:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/files_gestion.tpl.php");

class list_configuration_mailtpl_files_ui extends list_configuration_mailtpl_ui {
	
	protected $files_type = '';
	protected $files_path="";
	protected $files_url="";
	protected $files_error="";
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		global $msg;
		
		$this->_init_files_path();
		$this->_init_files_url();
		// path exist?
		if(!is_dir($this->files_path)){
			if(!@mkdir($this->files_path)){
				$this->files_error=$msg["admin_files_gestion_error_create_folder"].$this->files_path."<br />".$msg["admin_files_gestion_error_param_".$this->files_type."_folder"];
				$this->files_path="";
			} else {
				chmod($this->files_path, 0777);
			}
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _init_files_path() {
		if(empty($this->files_path)) {
			$parameter = "pmb_".$this->files_type."_folder";
			global ${$parameter};
			$this->files_path = ${$parameter};
		}
		return $this->files_path;
	}
	
	protected function _init_files_url() {
		if(empty($this->files_url)) {
			$parameter = "pmb_".$this->files_type."_url";
			global ${$parameter};
			$this->files_url = ${$parameter};
		}
		return $this->files_url;
	}
	
	protected function get_title() {
		global $msg, $charset;
		return "<h1>".htmlentities($msg["admin_files_gestion_title"], ENT_QUOTES, $charset)."</h1>";
	}
	
	protected function fetch_data() {
		global $msg;
		$this->objects = array();
		
		if(empty($this->files_path)) {
			$this->files_error=$msg["admin_files_gestion_error_no_path"];
			return;
		}
		if(!is_dir($this->files_path)){
			$this->files_error=$msg["admin_files_gestion_error_is_no_path"].$this->files_path;
			$this->files_path="";
			return;
		}
		if(($entries = @scandir($this->files_path)) !== false) {
			$i=0;
			foreach ($entries as $entry) {
				if($entry != '.' && $entry != '..') {
					if (filetype($this->files_path."/".$entry) != "dir") {
						$object = new stdClass();
						$object->name = $entry;
						$this->add_object($object);
						$i++;
					}
				}
			}
		}
		$this->pager['nb_results'] = count($this->objects);
		$this->messages = "";
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name' => 'admin_files_gestion_name',
				'thumbnail' => 'admin_files_gestion_thumbnail',
				'weight' => 'admin_files_gestion_weight'
		);
	}
	
	protected function init_default_columns() {
		parent::init_default_columns();
		$this->add_column_delete();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('thumbnail', 'align', 'center');
		$this->set_setting_column('weight', 'align', 'right');
		$this->set_setting_column('weight', 'datatype', 'integer');
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'thumbnail', 'delete',
		);
	}
	
	protected function add_column_delete() {
		$html_properties = array(
				'value' => 'X',
				'link' => static::get_controller_url_base().'&action=delete&filename=!!urlencode_name!!',
		);
		$this->add_column_simple_action('delete', '', $html_properties);
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!urlencode_name!!', urlencode($object->name), $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function _get_object_property_thumbnail($object) {
		$parameter = "pmb_".$this->files_type."_url";
		global ${$parameter};
		$url = ${$parameter};
		return $url.urlencode($object->name);
	}
	
	protected function _get_object_property_weight($object) {
		return round(filesize($this->files_path.$object->name) / 1024, 2);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'thumbnail':
				$thumbnail = $this->_get_object_property_thumbnail($object);
				$content .= "<img height='15' width='15' src=\"".$thumbnail."\" alt=\"\" onmouseover=\"show_div_img(event,'".$thumbnail."')\" onmouseout=\"hide_div_img()\" />";
				break;
			case 'weight':
				$content .= $this->_get_object_property_weight($object)." KB";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_files_gestion_add'];
	}
	
	protected function get_js_sort_script_sort() {
		global $files_gestion_js_script_list;
		
		$display = parent::get_js_sort_script_sort();
		$display.= $files_gestion_js_script_list;
		return $display;
	}
	
	protected function get_display_top_actions() {
		if(count($this->objects) > 20) {
			$left_actions = static::get_button_add_file_from($this->get_label_button_add(), 'top');
			if($left_actions) {
				return $this->get_display_block_actions($left_actions);
			}
		}
		return '';
	}
	
	protected function get_button_add() {
		return static::get_button_add_file_from($this->get_label_button_add());
	}
	
	public function get_files_error() {
		return $this->files_error;
	}
	
	public static function get_button_add_file_from($label_button, $from='bottom') {
		global $charset, $current_module;
		
		return "
			<form class='form-".$current_module."' name='files_gestion_form_".$from."'  method='post' action=\"".static::get_controller_url_base()."\"  enctype='multipart/form-data'>
				<input type='hidden' name='action' id='action' />
				<input type='hidden' name='from' id='from' />
				<input class='saisie-80em' type='file' name='select_file_".$from."' />
				<input class='bouton' type='button' value='".htmlentities($label_button, ENT_QUOTES, $charset)."' onclick=\" this.form.from.value='".$from."'; this.form.action.value='upload'; this.form.submit();\" />
			</form>";
	}
}