<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_payment_method_list.class.php,v 1.1 2018-12-19 13:59:19 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/transaction/transaction_payment_method.tpl.php");

class transaction_payment_method_list {
    
    protected $transaction_payment_method_list = array(); // liste des modes de paiement
	
	public function __construct() {
		$this->fetch_data();		
	}
	
	protected function fetch_data() {
		
		$this->transaction_payment_method_list = array();	
		$query = "select * from transaction_payment_methods order by transaction_payment_method_name";
		$res = pmb_mysql_query($query);
		$i=0;
		if (pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_object($res)) {
				$this->transaction_payment_method_list[$i]['id'] = $row->transaction_payment_method_id;
				$this->transaction_payment_method_list[$i]['name'] = $row->transaction_payment_method_name;
				$i++;
			}
		}
	}

	public function get_form() {
		global $transaction_payment_method_list_form, $charset;
		
		$form = '';
		$parity = 0;
		foreach ($this->transaction_payment_method_list as $transaction_payment_method) {
			if ($parity++ % 2)	$pair_impair = "even"; else $pair_impair = "odd";	
			$form.= "
				<tr class='" . $pair_impair . "' onmouseout=\"this.className='" . $pair_impair . "'\" onmouseover=\"this.className='surbrillance'\" style='cursor: pointer'>
					<td onmousedown=\"document.location='./admin.php?categ=finance&sub=transaction_payment_method&action=edit&id=" . $transaction_payment_method['id'] . "'\" >" . htmlentities($transaction_payment_method['name'], ENT_QUOTES, $charset) . "</td>
				</tr>
			";
		}		
		return str_replace('!!transaction_payment_method_list!!', $form, $transaction_payment_method_list_form);
	}
	
	public function proceed() {
		global $action;
		
		switch ($action) {
			case 'add':
				break;				
    		default:
				break;
		}
	}
	
	public function get_selector($f_name = 'f_payment_method', $id = 0) {
	    return do_selector('transaction_payment_methods', $f_name , $id);
	}
	
	public function get_data() {	    
		return $this->transaction_payment_method_list;	
	}
	
}