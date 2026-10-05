<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_mails_configuration.class.php,v 1.3 2024/02/21 10:26:27 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_mails_configuration extends alerts {

	protected function get_module() {
		return 'admin';
	}

	protected function get_section() {
		return 'mails_configuration';
	}

	protected function fetch_data() {
		$this->data = array();

		//pour les mails non configurés
		$query = "SELECT count(*) FROM mails_configuration WHERE mail_configuration_validated = 0 AND mail_configuration_type = 'domain'";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
			$this->add_data('mails', 'mail_configuration_unvalidated', 'configuration', '', $number);
		}
	}
}