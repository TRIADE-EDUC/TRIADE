<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_payment_method.class.php,v 1.6 2023/06/28 07:53:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/transaction/transaction_payment_method.tpl.php");

class transaction_payment_method {
    protected $id = 0;				// identifiant du mode de paiement
    protected $name = "";			// Libellé du mode de paiement
	
	public function __construct($id = 0) {
	    $this->id = intval($id);
	    $this->fetch_data();		
	}
	
	protected function fetch_data() {
		$this->name = '';
		if (!$this->id)	return false;
		$rqt = "SELECT * FROM transaction_payment_methods WHERE transaction_payment_method_id = " . $this->id;
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_object($res);
			$this->id = $row->transaction_payment_method_id;
			$this->name = $row->transaction_payment_method_name;	
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('f_name', 'transaction_payment_method_form_name')
		->add_input_node('text', $this->name);
		return $interface_content_form->get_display();
	}
	
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_admin_form('transaction_payment_method');
		if(!$this->id){
			$interface_form->set_label($msg['transaction_payment_method_form_titre_add']);
		}else{
			$interface_form->set_label($msg['transaction_payment_method_form_titre_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg["transaction_payment_method_form_delete_question"])
		->set_content_form($this->get_content_form())
		->set_table_name('transaction_payment_methods')
		->set_field_focus('f_name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {		
		global $f_name;
		
		$this->name = stripslashes($f_name);
	}
	
	public function get_name() {
	    return $this->name;
	}
	
	public function save() {
		if ($this->id) {			
			$save = "UPDATE ";
			$clause = "WHERE transaction_payment_method_id = " . $this->id;
		} else {
			$save = "INSERT INTO ";
			$clause = "";
		}
		$save.= " transaction_payment_methods SET transaction_payment_method_name ='" . addslashes($this->name) . "' " . $clause;
		pmb_mysql_query($save);		
		if (!$this->id) {
			$this->id=pmb_mysql_insert_id();
		}			
	}
	
	public static function delete($id) {
		$id = intval($id);
		$rqt = "DELETE FROM transaction_payment_methods WHERE transaction_payment_method_id = ".$id;
		pmb_mysql_query($rqt);
		return true;
	}
}