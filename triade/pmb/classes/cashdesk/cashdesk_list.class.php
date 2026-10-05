<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk_list.class.php,v 1.18.2.1 2024/09/11 12:23:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/cashdesk/cashdesk.tpl.php");
require_once($class_path."/cashdesk/cashdesk.class.php");

class cashdesk_list {	
	public $cashdesk_list=array(); // liste des caisses
	
	public function __construct(){
		$this->fetch_data();		
	}
	
	protected function fetch_data(){
		// les data...	
		$this->cashdesk_list=array();	
		$rqt = "select * from cashdesk order by cashdesk_name";
		$res = pmb_mysql_query($rqt);
		$i=0;
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->cashdesk_list[$i]['id'] = $row->cashdesk_id;
				$this->cashdesk_list[$i]['name'] = $row->cashdesk_name;
				$i++;
			}
		}
	}
	
	public function get_display_payment_method($encaissement_payment_method, $for_excel=0) {
	    
	    $display = '';
	    $data = array();
	    foreach ($encaissement_payment_method as $transaction) {
	        if ($transaction['transaction_payment_method_name'] && $transaction['cash']) {
	            if(!isset($data[$transaction['transaction_payment_method_name']])) {
	                $data[$transaction['transaction_payment_method_name']] = 0;
	            }
	            $data[$transaction['transaction_payment_method_name']]+= $transaction['cash'];
	        }	        
	    }
	    foreach ($data as $mode => $montant) {
	        if($for_excel) {
	            if ($display) {
	                $display.= '; ';
	            }
	            $display.= $mode . ': ' . $this->format_price($montant);
	        } else {
	            if (!$display) {
	                $display.= '<br />';
	            } else {	                
	                $display.= '; ';
	            }
	            $display.= $mode . ': ' . $this->format_price($montant);
	        }
	    }
	    return $display;
	}
	
	public function format_price($price) {
		global $pmb_fine_precision;
		
		if (!$pmb_fine_precision) $pmb_fine_precision=2;
		return 	number_format(floatval($price), $pmb_fine_precision, '.', ' ');
	}
}