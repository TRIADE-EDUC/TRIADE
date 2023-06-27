<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transaction_payment_method.class.php,v 1.2 2019-06-07 12:23:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
	
	public function get_form(){
		global $msg, $charset;
		global $transaction_payment_method_form;
		
		$form = $transaction_payment_method_form;
		
		if ($this->id) {
			$titre = $msg["transaction_payment_method_form_titre_edit"];
			$form = str_replace('!!supprimer!!', "<input type='button' class='bouton' value=' ".$msg["transaction_payment_method_form_delete"]." ' onClick=\"if(confirm('".$msg["transaction_payment_method_form_delete_question"]."'))
					document.location = './admin.php?categ=finance&sub=transaction_payment_method&action=delete&id=!!id!!'\" />", $form);
		} else {
			$titre = $msg["transaction_payment_method_form_titre_add"];
			$form = str_replace('!!supprimer!!', "", $form);
		}		
		$form = str_replace('!!titre!!', $titre, $form);
		$form = str_replace('!!name!!', htmlentities($this->name, ENT_QUOTES, $charset), $form);
				
		$form = str_replace('!!action!!', "./admin.php?categ=finance&sub=transaction_payment_method&action=save&id=!!id!!", $form);
		$form = str_replace('!!id!!', $this->id, $form);
		return $form; 
	}
	
	public function get_from_form() {		
		global $f_name;
		global $id;
		
		$this->id = $id+0;
		$this->name = stripslashes($f_name);
	}
	
	public function check_delete() {
		
		return 1;
	}
	
	public function get_name() {
	    
	    return $this->name;
	}
	
	public function delete() {
		
		$rqt = "DELETE FROM transaction_payment_methods WHERE transaction_payment_method_id = " . $this->id;
		pmb_mysql_query($rqt);
		
		$this->id = 0;
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
		$this->fetch_data();
	}
	
	public function proceed(){
		global $action, $msg;
		
		switch ($action) {
			case 'edit':
				print $this->get_form();
				break;
			case 'save':
				$this->get_from_form();
				$this->save();
				print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=transaction_payment_method'</script>";
				break;
			case 'delete':
				if ($this->check_delete()) {
					$this->delete();
					print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=transaction_payment_method'</script>";
				} else {
					print "<script type='text/javascript'>alert(".$msg["transaction_payment_method_form_delete_no"].");window.location='./admin.php?categ=finance&sub=transaction_payment_method'</script>";
				}
				break;	
			default:
				break;
		}
	}
}