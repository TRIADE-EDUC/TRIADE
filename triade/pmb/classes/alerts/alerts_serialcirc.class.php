<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_serialcirc.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_serialcirc extends alerts {

	protected function get_module() {
		return 'catalog';
	}

	protected function get_section() {
		return 'menu_alert_demande_abo';
	}

	protected function fetch_data() {
		$this->data = array();

		$query="SELECT count(*) FROM serialcirc_ask WHERE serialcirc_ask_statut=0";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('serials', 'alert_demande_abo', 'circ_ask', '', $number);
		}
	}
}