<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_demandes.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_demandes extends alerts {

	protected function get_module() {
		return 'demandes';
	}

	protected function get_section() {
		return 'alerte_demandes';
	}

	protected function fetch_data() {
		$this->data = array();

		// comptage des demandes à valider
		$query = " SELECT count(*) FROM demandes where etat_demande=1";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('list', 'alerte_demandes_traiter', '', '&idetat=1', $number);
		}
	}

}