<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoices.class.php,v 1.30 2021/04/08 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/rent/rent_root.class.php");
require_once($class_path."/rent/rent_invoice.class.php");
require_once($class_path."/rent/rent_account.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");

class rent_invoices extends rent_root {
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select distinct id_invoice from rent_invoices 
			join rent_accounts_invoices on account_invoice_num_invoice = id_invoice
			join rent_accounts on id_account = account_invoice_num_account';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new rent_invoice($row->id_invoice);
			}
		}
	}
	
	public static function create_from_accounts($accounts = array()) {
		
		$invoices = array();
		$rent_accounts = array();
		if(is_array($accounts) && count($accounts)) {
			foreach ($accounts as $id_account) {
				$rent_account = new rent_account($id_account);
				if(!$rent_account->get_num_invoice()) {
					if($rent_account->get_request_status() != 3) {
						$rent_account->set_request_status(3);
						$rent_account->save();
					}
					$invoice_group = $rent_account->get_exercice()->id_exercice.'_'.$rent_account->get_type().'_'.$rent_account->get_pricing_system()->get_id().'_'.$rent_account->get_supplier()->id_entite;
					$invoices[$invoice_group]['accounts'][] = $id_account;
					$rent_accounts[$id_account] = $rent_account;
				}
			}
		}
		if(count($invoices)) {
			foreach ($invoices as $invoice) {
				$rent_invoice = new rent_invoice();
				foreach ($invoice['accounts'] as $id_account) {
					$rent_invoice->add_account($rent_accounts[$id_account]);	
				}
				$rent_invoice->save();
			}
			return true;
		}
		return false;
	}
	
	public static function validate($invoices = array()) {
		if(count($invoices)) {
			foreach ($invoices as $invoice) {
				$rent_invoice = new rent_invoice($invoice);
				$rent_invoice->validate();
				$rent_invoice->save();
			}
		}
	}
}