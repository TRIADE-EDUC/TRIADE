<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_list.class.php,v 1.4.4.1 2025/01/16 11:24:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_list extends selector {

	protected $search;

	protected $search_xml_file;

	protected $search_field_id;

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		print $this->get_sel_header_template();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		print $this->get_display_list();
		print $this->get_sel_footer_template();
	}

	protected function get_values_list() {
		$values_list = array();
		if($this->search_field_id) {
			if(!isset($this->search)) {
				$this->search = new search($this->search_xml_file);
			}
			$p=explode('_', $this->search_field_id);
			if($p[0] == 'f') {
				$values_list = $this->search->get_options_list_field($this->search->fixedfields[$p[1]]);
			}
		}
		return $values_list;
	}

	protected function get_display_list() {
		$display_list = '';
		$values_list = $this->get_values_list();
		foreach($values_list as $index=>$value ) {
			$display_list .= $this->get_display_element($index, $value);
		}
		return $display_list;
	}

	protected function get_display_element($index='', $value='') {
		global $charset;
		global $caller;
		global $callback;

		$display = "
			<div class='row'>
				<div class='colonne2' style='width: 80%;'>
					<a href='#' onclick=\"set_parent('$caller', '$index', '".htmlentities(addslashes($value),ENT_QUOTES,$charset)."', '".$callback."')\">$value</a>
				</div>
			</div>";
		return $display;
	}

	public function get_title() {
		$title = "";
		if($this->search_field_id) {
			if(!isset($this->search)) {
				$this->search = new search($this->search_xml_file);
			}
			$p=explode('_', $this->search_field_id);
			if($p[0] == 'f') {
				$title = $this->search->fixedfields[$p[1]]['TITLE'];
			}
		}
		return $title;
	}

	public static function get_params_url() {
		global $search_xml_file, $search_field_id;

		$params_url = parent::get_params_url();
		$params_url .= ($search_xml_file ? "&search_xml_file=".$search_xml_file : "").($search_field_id ? "&search_field_id=".$search_field_id : "");
		return $params_url;
	}

	public function set_search_xml_file($search_xml_file) {
		$this->search_xml_file = $search_xml_file;
	}

	public function set_search_field_id($search_field_id) {
		$this->search_field_id = $search_field_id;
	}
}
?>