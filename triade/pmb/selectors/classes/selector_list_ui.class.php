<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_list_ui.class.php,v 1.2.4.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_list_ui extends selector {

	protected $objects_type;

	protected $filter_name;

	protected $list_ui_instance;

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		print $this->get_sel_header_template();
		print $this->get_search_form();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		print $this->get_display_list();
		print $this->get_sel_footer_template();
	}

	protected function get_filter_result($list) {
		foreach ($list as $key=>$value) {
			if(!preg_match('`'.str_replace('*', '', addslashes($this->user_input)).'`i', $value)) {
				unset($list[$key]);
			}
		}
		return $list;
	}

	protected function get_display_list() {
		$this->get_list_ui_instance();
		if(!empty($this->list_ui_instance)) {
			$query = $this->list_ui_instance->get_ajax_selection_query($this->filter_name);
			$result = pmb_mysql_query($query);
			if($result) {
				$list = array();
				while ($row = pmb_mysql_fetch_array($result)) {
					$list[$row[0]] = $row[1];
				}
				if($this->user_input && $this->user_input != '*') {
					$list = $this->get_filter_result($list);
				}
				$this->nbr_lignes = count($list);
				if($this->nbr_lignes) {
					$list = array_slice($list, $this->get_start_list(), $this->get_nb_per_page_list(), true);
					foreach ($list as $key=>$element) {
						$display_list .= $this->get_display_element($key, $element);
					}
					$display_list .= $this->get_pagination();
				} else {
					$display_list .= $this->get_message_not_found();
				}
			}
		}
		return $display_list;
	}

	protected function get_display_element($key='', $value='') {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$display .= pmb_bidi("<a href='#' onclick=\"set_parent('$caller', '".$key."', '".htmlentities(addslashes($value),ENT_QUOTES, $charset)."','$callback')\">".
				htmlentities($value,ENT_QUOTES, $charset)."</a><br />");
		return $display;
	}

	protected function get_message_not_found() {
		global $msg;
		return $msg["searcher_no_result"];
	}

	public function get_title() {
		global $msg;
		$title = "";
		$this->get_list_ui_instance();
		if(!empty($this->list_ui_instance)) {
			$label_code= $this->list_ui_instance->get_label_available_filter($this->filter_name);
			$label = (isset($msg[$label_code]) ? $msg[$label_code] : $label_code);
			$title = $label;
		}
		return $title;
	}

	public function get_list_ui_instance() {
		if(!isset($this->list_ui_instance)) {
			if(!empty($this->objects_type)) {
				$class_name = 'list_'.$this->objects_type;
				$class_name::set_without_data(true);
				$this->list_ui_instance = new $class_name();
				$class_name::set_without_data(false);
			}
		}
		return $this->list_ui_instance;
	}

	public static function get_params_url() {
		global $objects_type, $filter_name;

		$params_url = parent::get_params_url();
		$params_url .= ($objects_type ? "&objects_type=".$objects_type : "").($filter_name ? "&filter_name=".$filter_name : "");
		return $params_url;
	}

	public function set_objects_type($objects_type) {
		$this->objects_type = $objects_type;
	}

	public function set_filter_name($filter_name) {
		$this->filter_name = $filter_name;
	}
}
?>