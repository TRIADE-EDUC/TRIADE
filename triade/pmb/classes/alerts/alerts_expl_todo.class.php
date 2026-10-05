<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_expl_todo.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_expl_todo extends alerts {

	protected function get_module() {
		return 'circ';
	}

	protected function get_section() {
		return 'alert_circ_retour';
	}

	protected function fetch_data() {
		global $deflt_docs_location;

		$this->data = array();

		if(!$deflt_docs_location)	return"";

		$query = "SELECT count(*) FROM exemplaires where expl_retloc='$deflt_docs_location'";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('ret_todo', 'alert_circ_retour_todo', '', '', $number);
		}
	}
}