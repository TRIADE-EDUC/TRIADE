<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_marc_list.class.php,v 1.6.4.1.2.1 2025/01/30 09:08:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_marc_list extends selector {

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

	protected function get_marc_list_instance() {
		if($this->search_field_id) {
			if(!isset($this->search)) {
				$this->search = new search($this->search_xml_file);
			}
			$p=explode('_', $this->search_field_id);
			if($p[0] == 'f') {
				return new marc_list($this->search->fixedfields[$p[1]]["INPUT_OPTIONS"]["NAME"][0]["value"]);
			}
		}
	}

	protected function filter_from_search($marc_list_instance) {
		$p=explode('_', $this->search_field_id);
		if($p[0] == 'f') {
			$array_selector = $this->search->get_options_list_field($this->search->fixedfields[$p[1]]);
			$marc_list_instance->table = $array_selector;
		}
		return $marc_list_instance;
	}

	protected function get_display_list() {
		global $letter;
		global $msg;

		$display_list = '';
		$marc_list_instance = $this->get_marc_list_instance();
		if(isset($this->search)) {
			$marc_list_instance = $this->filter_from_search($marc_list_instance);
		}
		$amarc_list=$marc_list_instance->table;

		$special = false;
		$favorite = false;
		$alphabet = array();
		asort($amarc_list);
		foreach($amarc_list as $key => $val) {
			if ($key>=900) {
				$special=true;
			} else {
				$alphabet[] = strtoupper(convert_diacrit(pmb_substr($val,0,1)));
			}
			if (isset($marc_list_instance->tablefav[$key])) $favorite=true;
		}
		$alphabet = array_unique($alphabet);

		if(!$letter) {
			if ($favorite) {
				$letter = "Fav";
			} elseif ($special) {
				$letter="My";
			} else {
				$letter = "a";
			}
		}

		// affichage d'un sommaire par lettres
		$display_list .= "<div class='row'>";
		if ($favorite) {
			if ($letter!='Fav') {
				$display_list .= "<a href='".static::get_base_url()."&letter=Fav'>".$msg['favoris']."</a> ";
			} else {
				$display_list .= "<strong><u>".$msg['favoris']."</u></strong> ";
			}
		}
		if ($special) {
			if ($letter!='My')
				$display_list .= "<a href='".static::get_base_url()."&letter=My'>#</a> ";
			else
				$display_list .= "<strong><u>#</u></strong> ";
		}
		foreach($alphabet as $char) {
			$present = pmb_preg_grep("/^$char/i", $marc_list_instance->table);
			if(is_countable($present) && count($present) && strcasecmp($letter, $char)) {
				$display_list .= "<a href='".static::get_base_url()."&letter=$char'>$char</a> ";
			} else if(!strcasecmp($letter, $char)) {
				$display_list .= "<strong><u>$char</u></strong> ";
			}
		}
		$display_list .= "</div><hr />";
		$display = array();
		foreach($marc_list_instance->table as $index=>$value ) {
			if((preg_match("/^$letter/i", convert_diacrit($value))) ||(($letter=='My')&&($index>=900)) ||(($letter=='Fav')&&($marc_list_instance->tablefav[$index]))) {
				$display[] = $this->get_display_element($index, $value);
			}
		}

		$display_list .= "<div class='row'>";
		foreach($display as $link) {
			$display_list .= $link;
		}
		$display_list .= "</div>";
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
				<div class='colonne2'  style='width: 20%;'>
					$index
				</div>
			</div>";
		return $display;
	}

	public static function get_params_url() {
		global $search_xml_file, $search_field_id;

		$params_url = parent::get_params_url();
		$params_url .= ($search_xml_file ? "&search_xml_file=".$search_xml_file : "").($search_field_id ? "&search_field_id=".$search_field_id : "");
		return $params_url;
	}

	public function get_title() {
		$title = "";
		if($this->search_field_id) {
			$this->search = new search($this->search_xml_file);
			$p=explode('_', $this->search_field_id);
			if($p[0] == 'f') {
				$title = $this->search->fixedfields[$p[1]]['TITLE'];
			}
		}
		return $title;
	}

	public function set_search_xml_file($search_xml_file) {
		$this->search_xml_file = $search_xml_file;
	}

	public function set_search_field_id($search_field_id) {
		$this->search_field_id = $search_field_id;
	}
}
?>