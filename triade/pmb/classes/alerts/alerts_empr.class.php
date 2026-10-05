<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_empr.class.php,v 1.2 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_empr extends alerts {

	protected function get_module() {
		return 'edit';
	}

	protected function get_section() {
		return 'fins_abonnements';
	}

	protected function fetch_data() {
		global $pmb_relance_adhesion, $deflt2docs_location,$pmb_lecteurs_localises;
		global $empr_statut_adhes_depassee;

		$this->data = array();
		if($pmb_lecteurs_localises){
			$condition_loc=" AND empr_location='".$deflt2docs_location."' ";
		}else{
		    $condition_loc="";
		}
		// comptage des emprunteurs proche d'expiration d'abonnement
		$query = " SELECT count(*) FROM empr where ((to_days(empr_date_expiration) - to_days(now()) ) <=  $pmb_relance_adhesion ) and empr_date_expiration >= now()  ".$condition_loc;
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('empr', 'empr_expir_pro', 'limite', '', $number);
		}
		if (!$empr_statut_adhes_depassee) $empr_statut_adhes_depassee=2;

		// comptage des emprunteurs expiration d'abonnement
		$query = "SELECT count(*) FROM empr where empr_statut!=$empr_statut_adhes_depassee and empr_date_expiration < now() ".$condition_loc;
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('empr', 'empr_expir_att', 'depasse', '', $number);
		}
	}

}