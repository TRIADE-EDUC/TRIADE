<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_pnb.class.php,v 1.3 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_pnb extends alerts {

	protected function get_module() {
		return 'edit';
	}

	protected function get_section() {
		return 'alert_pnb';
	}

	protected function fetch_data() {
		global $pmb_pnb_alert_end_offers, $pmb_pnb_alert_staturation_offers;
		global $pmb_pnb_alert_threshold_tokens;

		$this->data = array();

		$pmb_pnb_alert_end_offers = intval($pmb_pnb_alert_end_offers);
		$pmb_pnb_alert_staturation_offers = intval($pmb_pnb_alert_staturation_offers);

		$query = "SELECT count(*) FROM pnb_orders WHERE DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number){
			$this->add_data('pnb', 'alert_pnb_end', 'orders', '&alert_end_offers=1', $number);
		}

		$query = "select pnb_order_id_order, pnb_order_line_id,count(id_pnb_loan), pnb_order_nb_simultaneous_loans from pnb_orders
        join pnb_loans on pnb_loan_order_line_id = pnb_order_line_id
        group by pnb_order_line_id having count(id_pnb_loan) >= pnb_order_nb_simultaneous_loans - " . $pmb_pnb_alert_staturation_offers;
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_num_rows($result);
		if($number){
			$this->add_data('pnb', 'alert_pnb_saturation', 'orders', '&alert_staturation_offers=1', $number);
		}

		// Seuil d'alerte sur nombre de jetons restants
		$query = "SELECT count(*) FROM pnb_orders WHERE pnb_current_nta < ".$pmb_pnb_alert_threshold_tokens;
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number){
			$this->add_data('pnb', 'pnb_alert_threshold_tokens', 'orders', '&alert_threshold_tokens=1', $number);
		}
	}
}