<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_sugg.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_sugg extends alerts {

	protected function get_module() {
		return 'acquisition';
	}

	protected function get_section() {
		return 'alerte_suggestion';
	}

	protected function fetch_data() {
		global $opac_show_suggest;
		global $acquisition_sugg_localises, $deflt_docs_location;

		$this->data = array();

		if ($opac_show_suggest) {
			// comptage des tags à valider
			$query = " SELECT count(*) FROM suggestions where statut=1 ".($acquisition_sugg_localises?" AND sugg_location=".$deflt_docs_location:"");
			$result = pmb_mysql_query($query);
			$number = pmb_mysql_result($result, 0, 0);
			if ($number) {
				$this->add_data('sug', 'alerte_suggestion_traiter', '', '&action=list&statut=1', $number);
			}
		}
	}
}