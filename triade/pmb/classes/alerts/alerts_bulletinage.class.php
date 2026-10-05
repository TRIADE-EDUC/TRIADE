<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_bulletinage.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_bulletinage extends alerts {

	protected function get_module() {
		return 'catalog';
	}

	protected function get_section() {
		return 'pointage_menu_pointage';
	}

	protected function fetch_data() {
		global $deflt_bulletinage_location;

		$this->data = array();

		// comptage des abonnements à renouveler
		$query = "SELECT count(*) as total FROM abts_abts WHERE date_fin BETWEEN CURDATE() AND  DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
		if($deflt_bulletinage_location) {
			$query .= " AND location_id='".$deflt_bulletinage_location."'";
		}
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
			$this->add_data('serials', 'abonnements_to_renew', 'pointage', '', $number);
		}
		// comptage des abonnements dépassés
		$query = "SELECT count(*) as total FROM abts_abts WHERE date_fin < CURDATE()";
		if($deflt_bulletinage_location) {
			$query .= " AND location_id='".$deflt_bulletinage_location."'";
		}
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
			$this->add_data('serials', 'abonnements_outdated', 'pointage', '', $number);
		}
	}
}