<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_keyword.class.php,v 1.3.2.1 2025/01/16 11:24:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");

class selector_keyword extends selector {

	public function proceed() {
		global $page;

		print $this->get_sel_header_template();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		print $this->get_display_list();
		print $this->get_sel_footer_template();
	}

	protected function get_values_list() {
	    global $pmb_keyword_sep, $start;

		$values_list = array();
		$query = "select index_l from notices where index_l is not null and index_l!=''";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)){
			$start = trim(str_replace('%', '', $start ?? ""));
			$start_length = strlen($start);
			while ($row = pmb_mysql_fetch_object($result)) {
				$liste = explode($pmb_keyword_sep,$row->index_l);
				for ($i=0;$i<count($liste);$i++){
					$value = trim($liste[$i]);
					if(($start == substr($value, 0, $start_length)) && !in_array($value, $values_list)) {
						$values_list[$value] = $value;
					}
				}
			}
		}
		ksort($values_list);
		return $values_list;
	}

	protected function get_js_script() {
		global $jscript;
		global $jscript_common_selector_simple;
		global $param1, $param2, $p1, $p2;
		global $infield;

		if(!isset($jscript)) $jscript = $jscript_common_selector_simple;
		$jscript = str_replace('!!param1!!', ($param1 ? $param1 : $p1), $jscript);
		$jscript = str_replace('!!param2!!', ($param2 ? $param2 : $p2), $jscript);
		$jscript = str_replace('!!infield!!', $infield, $jscript);
		return $jscript;
	}

	protected function get_display_list() {
		global $nb_per_page;
		global $page;
		global $msg;

		$page = intval($page);
		$nb_per_page = intval($nb_per_page);

		$display_list = '';
		if(!$page) {
			$debut = 0;
		} else {
			$debut = ($page-1)*$nb_per_page;
		}
// 		$lettre="";
		$values_list = $this->get_values_list();
		foreach($values_list as $index=>$value ) {
// 			if ($index{0}!=$lettre){
// 				$lettre=$index{0};
// 				$display_list.="<div class='row'><span class='erreur'>".$lettre."</span></div>";
// 			}
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
}
?>