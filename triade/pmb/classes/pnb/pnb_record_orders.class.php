<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_record_orders.class.php,v 1.4 2018-06-20 10:02:30 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/pnb/pnb_record_orders.tpl.php");
require_once($class_path.'/pnb/pnb_order.class.php');
require_once($class_path.'/pnb/dilicom.class.php');
require_once($class_path.'/encoding_normalize.class.php');

// Gestion des offres numériques d'une notice

class pnb_record_orders {
	
	/**
	 * 
	 * @var dilicom
	 */
	protected static $dilicom;
	protected $record_id;
	protected $pnb_orders;
	
	public function __construct($record_id = 0){
		$record_id += 0;
		if ($record_id) {
			$this->record_id = $record_id;
		}
		$this->fetch_data();
		$this->init_dilicom_instance();
	}
	
	protected function fetch_data() {
		if ($this->record_id) {
			$query = "SELECT id_pnb_order FROM pnb_orders WHERE pnb_order_num_notice = '".$this->record_id."' ORDER BY pnb_order_offer_date";			
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$this->pnb_orders[] = new pnb_order($row['id_pnb_order']);
			} 
		}
	}
	
	public function get_record_id() {
		return $this->record_id;
	}

	public function get_pnb_orders() {
		if (!isset($this->pnb_orders)) {
			$this->pnb_orders = array();
		}
		return $this->pnb_orders;
	}
	
	public function get_display_orders () {		
		global $pnb_record_orders_tpl;
		global $pnb_record_orders_tpl_line;
		
		$tpl = $pnb_record_orders_tpl;		
		$lines = '';		
		foreach ($this->get_pnb_orders() as $order) {
			$line = $pnb_record_orders_tpl_line;
			$line = str_replace('!!order_id!!', $order->get_order_id(), $line);
			$line = str_replace('!!line_id!!', $order->get_line_id(), $line);
			$line = str_replace('!!loan_max_duration!!', $order->get_loan_max_duration(), $line);
			$line = str_replace('!!nb_loans!!',  ' / ' .$order->get_nb_loans(), $line);
			$line = str_replace('!!nb_simultaneous_loans!!', $order->get_nb_simultaneous_loans(), $line);
			$line = str_replace('!!nb_consult_in_situ!!', $order->get_nb_consult_in_situ(), $line);
			$line = str_replace('!!nb_consult_ex_situ!!', $order->get_nb_consult_ex_situ(), $line);
			$line = str_replace('!!offer_date!!', $order->get_offer_formated_date(), $line);
			$line = str_replace('!!offer_date_end!!', $order->get_offer_formated_date_end(), $line);
			$lines .= $line;
		}		
		$tpl = str_replace('!!order_lines!!', $lines, $tpl);
		$tpl = str_replace('!!record_id!!', $this->record_id, $tpl);
		return $tpl;
	}
	
	public function get_orders_number() {
		return count($this->get_pnb_orders());
	}
	
	public function get_loans_completed_number($line_id) {
		$infos = static::$dilicom->get_loan_status(array($line_id));
		if (isset($infos['loanResponseLine'][0]['nta'])) {
		    return $infos['loanResponseLine'][0]['nta']; 
		}
		return '';
	}
	
	protected function get_loans_in_progress($line_id) {
		
	}
	
	protected function init_dilicom_instance() {
		if (!isset(static::$dilicom)) {
			static::$dilicom = new dilicom();
		}
		return static::$dilicom;
	}
}