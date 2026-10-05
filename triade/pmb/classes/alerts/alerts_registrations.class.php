<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_registrations.class.php,v 1.3 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_registrations extends alerts {

	protected function get_module() {
		return 'animations';
	}

	protected function get_section() {
		return 'alerte_registrations';
	}

	protected function fetch_data() {
		$this->data = [];

		// Inscriptions à valider
		$query = " SELECT count(*) FROM anim_registrations WHERE num_registration_status = 1 ";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
		    $this->add_data('registration', 'alerte_registration_waiting', '', '&action=list&num_status=1', '', $number);
		}
	}
}