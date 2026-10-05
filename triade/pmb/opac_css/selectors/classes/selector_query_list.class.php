<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_query_list.class.php,v 1.8.2.1 2025/01/16 11:24:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_query_list extends selector {

	protected $search;

	protected $search_xml_file;

	protected $search_field_id;

	protected $search_terms;

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->search = new search($this->search_xml_file);
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

	protected function is_search_terms_value($value) {
		global $opac_default_operator;

		$founded = false;
		foreach ($this->search_terms as $term) {
			if(preg_match('`'.str_replace('*', '', addslashes($term)).'`i', $value)) {
				$founded = true;
			} elseif($opac_default_operator == 1) { //Operator AND
				$founded = false;
				break;
			}
		}
		return $founded;
	}

	protected function get_filter_result($list) {
		$this->search_terms = explode(' ',strip_empty_chars(clean_string($this->user_input)));
		if(!empty($this->search_terms) && count($this->search_terms)) {
			foreach ($list as $key=>$value) {
				if(!$this->is_search_terms_value($value)) {
					unset($list[$key]);
				}
			}
		}
		return $list;
	}

	protected function get_display_list() {
		$display_list = '';
		$p=explode('_', $this->search_field_id);
		if($p[0] == 'f') {
			$query=$this->search->fixedfields[$p[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
			if ($this->search->fixedfields[$p[1]]["INPUT_FILTERING"] == "yes") {
				$this->search->access_rights();
				$query = str_replace("!!acces_j!!", $this->search->tableau_access_rights["acces_j"], $query);
				$query = str_replace("!!statut_j!!", $this->search->tableau_access_rights["statut_j"], $query);
				$query = str_replace("!!statut_r!!", $this->search->tableau_access_rights["statut_r"], $query);
			}
		}
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

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				<input type='hidden' id='search_xml_file' name='search_xml_file' value='".$this->search_xml_file."' />
				<input type='hidden' id='search_field_id' name='search_field_id' value='".$this->search_field_id."' />
			</form>
			<script>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
		";
		return $sel_search_form;
	}

	protected function get_message_not_found() {
		global $msg;
		return $msg["searcher_no_result"];
	}

	protected function get_link_pagination() {
		$link = static::get_base_url()."&user_input=".rawurlencode($this->user_input)."&search_xml_file=".$this->search_xml_file."&search_field_id=".$this->search_field_id."&page=!!page!!";
		return $link;
	}

	public function get_title() {
		$p=explode('_', $this->search_field_id);
		return $this->search->fixedfields[$p[1]]['TITLE'];
	}

	public function set_search_xml_file($search_xml_file) {
		$this->search_xml_file = $search_xml_file;
	}

	public function set_search_field_id($search_field_id) {
		$this->search_field_id = $search_field_id;
	}
}
?>