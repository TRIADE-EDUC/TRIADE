<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_groups.class.php,v 1.4.4.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");

class selector_groups extends selector {

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

	protected function get_display_query() {
		if(!$this->user_input) {
			return "SELECT id_groupe, libelle_groupe FROM groupe ORDER BY libelle_groupe";
		} else {
			return "SELECT id_groupe, libelle_groupe FROM groupe WHERE libelle_groupe like '".str_replace("*", "%", $this->user_input)."%' ORDER BY libelle_groupe";
		}
	}

	protected function get_display_element($index='', $value='') {
		global $charset;
		global $caller;
		global $callback;

		$display = "
			<div class='row'>
				<a href='#' onclick=\"set_parent('$caller', '".$index."', '".htmlentities(addslashes($value),ENT_QUOTES, $charset)."','$callback')\">".htmlentities($value,ENT_QUOTES, $charset)."</a>
			</div>";
		return $display;
	}

	protected function get_display_list() {
		$display_list = '';
		$query = $this->get_display_query();
		$result = pmb_mysql_query($query);
		if($result) {
			$list = array();
			while ($row = pmb_mysql_fetch_array($result)) {
				$list[$row[0]] = $row[1];
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
}
?>