<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_vedette.class.php,v 1.3.4.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_vedette.tpl.php");

class selector_vedette extends selector {

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

	protected function get_display_list() {
		global $grammars;

		$display_list = '';
		if(!$grammars) {
			$grammars_in = "'notice_authors'";
		} else {
			$grammars_in = "'".implode("','", explode(',', $grammars))."'";
		}
		$user_input = str_replace('*','',$this->user_input);
		// on récupére le nombre de lignes
		if($user_input=="") {
			$query = "SELECT COUNT(1) FROM vedette where grammar in (".$grammars_in.")";
		} else {
			$query = "SELECT count(id_vedette) FROM vedette where label like '%".$user_input."%' and  grammar in (".$grammars_in.") ";
		}
		$result = pmb_mysql_query($query);
		$this->nbr_lignes = pmb_mysql_result($result, 0, 0);
		if($this->nbr_lignes) {
			// on lance la vraie requête
			if($user_input=="") {
				$query = "SELECT id_vedette, label FROM vedette where grammar in (".$grammars_in.") ";
			} else {
				$query = "SELECT id_vedette, label FROM vedette where label like '%".$user_input."%' and  grammar in (".$grammars_in.") ";
			}
			$query .= " ORDER BY label LIMIT ".$this->get_start_list().",".$this->get_nb_per_page_list()." ";
			$result = pmb_mysql_query($query);
			$display_list .= "<table><tr>";
			while($vedette=pmb_mysql_fetch_object($result)) {
				$display_list .= $this->get_display_element($vedette->id_vedette, $vedette->label);
			}
			$display_list .= "</table>";
			$display_list .= $this->get_pagination();
		}
		return $display_list;
	}

	protected function get_display_element($index='', $value='') {
		global $charset;
		global $caller;
		global $callback;

		$display = "
		<tr>
		<td>
		<a href='#' onclick=\"set_parent('".$caller."', '".$index."', '".htmlentities(addslashes($value),ENT_QUOTES,$charset)."','$callback')\">".htmlentities($value,ENT_QUOTES,$charset)."</a>
			</td>
		</tr>";
		return $display;
	}

	public function get_title() {
		global $msg;
		return $msg["notice_vedette_composee_author"];
	}

	public static function get_params_url() {
		global $grammars, $mode;

		$params_url = parent::get_params_url();
		$params_url .= ($grammars ? "&grammars=".$grammars : "").($mode ? "&mode=".$mode : "");
		return $params_url;
	}
}
?>