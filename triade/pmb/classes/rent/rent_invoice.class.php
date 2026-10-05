<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoice.class.php,v 1.31.4.2 2025/03/19 11:04:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

global $class_path, $include_path;

require_once($class_path."/rent/rent_account.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/marc_table.class.php");
require_once($include_path.'/templates/rent/rent_invoice.tpl.php');
require_once($class_path."/actes.class.php");
require_once($class_path."/lignes_actes.class.php");

class rent_invoice {
	
	/**
	 * Identifiant de la facture
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Utilisateur associé
	 * @var integer
	 */
	protected $num_user;
	
	/**
	 * Date
	 * @var datetime
	 */
	protected $date;
	
	/**
	 * Date formatée
	 * @var string
	 */
	protected $formatted_date;
	
	/**
	 * Statut (0 = encours, 1 = validé)
	 * @var integer
	 */
	protected $status;
	
	/**
	 * Date de validation
	 * @var datetime
	 */
	protected $valid_date;
	
	/**
	 * Date de validation formatée
	 * @var string
	 */
	protected $formatted_valid_date;
	
	/**
	 * Marclist rent_destination
	 */
	protected $destination;

	/**
	 * Marclist rent_destination label
	 */
	protected $destination_name;
			
	/**
	 * Identifiant de l'acte budgétaire associé
	 */
	protected $num_acte;
	
	/**
	 * Décomptes associés
	 * @var rent_account
	 */
	protected $accounts;
	
	protected $in_edit;

	public function __construct($id = 0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$this->num_user = 0;
		$this->date = date('Y-m-d H:i:s');
		$this->formatted_date = formatdate($this->date);
		$this->status = 1;
		$this->valid_date = '';
		$this->formatted_valid_date = '';
		$this->destination = '';
		$this->destination_name = '';
		$this->num_acte = 0;
		$this->accounts = array();
		$this->in_edit = false;
		if ($this->id) {
			$query = 'select * from rent_invoices where id_invoice = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->num_user = $row->invoice_num_user;
				$this->date = $row->invoice_date;
				$this->formatted_date = format_date($row->invoice_date);
				$this->status = $row->invoice_status;
				if($row->invoice_valid_date != '0000-00-00 00:00:00'){
					$this->valid_date = $row->invoice_valid_date;
					$this->formatted_valid_date = format_date($row->invoice_valid_date);
				}
				$this->destination = $row->invoice_destination;
				$destination = new marc_list('rent_destination');
				$this->destination_name=$destination->table[$this->destination] ?? '';
				$this->num_acte = $row->invoice_num_acte;				
				$query = 'select account_invoice_num_account from rent_accounts_invoices where account_invoice_num_invoice = '.$this->id;
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while($row = pmb_mysql_fetch_object($result)) {
						$this->accounts[] = new rent_account($row->account_invoice_num_account);
					}
				}
			}
		}
	}
	
	public function get_content_form() {
	    global $msg,$charset;
	    global $include_path;
	    global $rent_invoice_content_form_tpl;
	    
	    $content_form = $rent_invoice_content_form_tpl;
	    $content_form = str_replace("!!entity_id!!",$this->get_entity()->id_entite,$content_form);
	    $content_form = str_replace("!!entity_label!!",$this->get_entity()->raison_sociale,$content_form);
	    
	    $content_form = str_replace("!!status!!",$this->gen_selector_status(),$content_form);
	    $rent_destinations = new marc_select('rent_destination', 'invoice_destinations', $this->destination, '', '0', htmlentities($msg['acquisition_invoice_no_destination'], ENT_QUOTES, $charset));
	    $content_form = str_replace("!!destinations!!",$rent_destinations->display,$content_form);
	    
	    $tpl = $include_path.'/templates/rent/rent_account_invoice.tpl.html';
	    if (file_exists($include_path.'/templates/rent/rent_account_invoice_subst.tpl.html')) {
	        $tpl = $include_path.'/templates/rent/rent_account_invoice_subst.tpl.html';
	    }
	    $h2o = H2o_collection::get_instance($tpl);
	    if($this->status == 1) {
	        $this->in_edit = true;
	    }
	    $content_form = str_replace("!!content!!", $h2o->render(array('invoice' => $this)), $content_form);
	    return $content_form;
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg;
		global $include_path;
		
		$interface_form = new interface_acquisition_rent_form('invoice_form');
	    $interface_form->set_label($msg['acquisition_invoice_form_edit']);
		$interface_form->set_object_id($this->id)
		->set_num_entity($this->get_entity()->id_entite)
		->set_confirm_delete_msg($msg['acquisition_invoice_confirm_delete'])
		->set_content_form($this->get_content_form())
		->set_table_name('rent_invoices');
		if($this->status != 1) {
		    $interface_form->set_no_deletable(true);
		}
		$form = $interface_form->get_display();
		return $form;
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $invoice_status;
		global $invoice_destinations;
		
		$this->status = $invoice_status;
		$this->destination = stripslashes($invoice_destinations);
	}
	
	/**
	 * Sauvegarde de l'acte associé
	 */
	protected function save_acte() {
		$acte=new actes($this->num_acte);
		$acte->type_acte=TYP_ACT_RENT_INV;
		switch($this->status){
			case 1 :
				$acte->statut=STA_ACT_AVA;
				break;
			case 2 :
				$acte->statut=STA_ACT_PAY;
				break;
		}
		$acte->num_entite=$this->get_entity()->id_entite;
		$acte->num_fournisseur=$this->accounts[0]->get_supplier()->id_entite;
		$acte->num_exercice=$this->accounts[0]->get_exercice()->id_exercice;
		$acte->save();
		$this->num_acte=$acte->id_acte;
		if($this->num_acte){
			$id_ligne=0;
			$res_lignes_acte=actes::getLignes($this->num_acte);
			if (pmb_mysql_num_rows($res_lignes_acte)) {
				$row = pmb_mysql_fetch_object($res_lignes_acte);
				$id_ligne=$row->id_ligne;
			}
			$ligne_acte=new lignes_actes($id_ligne);
			$ligne_acte->type_ligne=TYP_ACT_RENT_INV;
			$ligne_acte->statut=$acte->statut;
			$ligne_acte->num_acte=$acte->id_acte;
			$ligne_acte->num_rubrique=$this->get_num_section();
			$ligne_acte->prix=$this->get_total_price();
			$ligne_acte->nb=1;
			$ligne_acte->save();
		}
	}
	
	/**
	 * Sauvegarde
	 */
	public function save(){
		$this->save_acte();
		if($this->id) {
			$query = 'update rent_invoices set ';
			$fields_in_create = '';
			$where = 'where id_invoice= '.$this->id;
		} else {
			$this->num_user = SESSuserid;
			$this->date = date('Y-m-d H:i:s');
			$this->formatted_date = format_date($this->date);
			$query = 'insert into rent_invoices set ';
			$fields_in_create = '
					invoice_num_user = "'.$this->num_user.'",
					invoice_date = "'.$this->date.'",
			';
			$where = '';
		}
		$query .= $fields_in_create;
		$query .= '
				invoice_status = "'.$this->status.'",
				invoice_valid_date = "'.$this->valid_date.'",
				invoice_destination = "'.$this->destination.'",	
				invoice_num_acte = "'.$this->num_acte.'"		
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			$query = 'delete from rent_accounts_invoices where
					account_invoice_num_invoice = "'.$this->id.'"';
			pmb_mysql_query($query);
			foreach ($this->accounts as $account) {
				$query = 'insert into rent_accounts_invoices set 
					account_invoice_num_account = "'.$account->get_id().'",
					account_invoice_num_invoice = "'.$this->id.'"';
				$result = pmb_mysql_query($query);
				if($result) {
					$account->set_num_invoice($this->id);
					$account->save();
				} 
			}
			return true;
		} else {
			return false;
		}
	}

	public function get_num_section(){
		$query = 'select account_type_num_section from rent_account_types_sections where account_type_num_exercice='.$this->accounts[0]->get_exercice()->id_exercice.' and account_type_marclist="'.$this->accounts[0]->get_type().'"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'account_type_num_section');
		} else {
			return 0;
		}
	}
	
	/**
	 * Suppression de l'acte associé
	 */
	protected function delete_acte() {
		actes::delete($this->num_acte);
	}
	
	/**
	 * Suppression
	 */
	public function delete(){
		if($this->id && ($this->status == 1)) {
			$accounts = $this->accounts;
			foreach ($accounts as $account) {
				$this->delete_account($account->get_id());
			}
			$this->delete_acte();
			$query = "delete from rent_invoices where id_invoice = ".$this->id;
			pmb_mysql_query($query);
			return true;
		}
	}

	/**
	 * Ajout d'un décompte
	 */
	public function add_account($account) {
		if(/*$this->id &&*/ $account->get_id()) {
// 			$query = 'insert into rent_accounts_invoices set
// 						account_invoice_num_account = "'.$account->get_id().'",
// 						account_invoice_num_invoice = "'.$this->id.'"';
// 			pmb_mysql_query($query);
			$this->accounts[] = $account;
		}
	}
	
	/**
	 * Suppression d'un décompte associé
	 */
	public function delete_account($id){
		if($this->id && $id) {
			$query = "delete from rent_accounts_invoices 
				where account_invoice_num_account = ".$id." 	
				and account_invoice_num_invoice  = ".$this->id;
			pmb_mysql_query($query);
			foreach ($this->accounts as $indice=>$account) {
				if($account->get_id() == $id) {
					array_splice($this->accounts, $indice);
					$account->set_num_invoice(0);
					$account->save();
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Retourne le nombre de décomptes associés
	 */
	public function get_nb_accounts() {
		return count($this->accounts);
	}
	
	public function get_entity(){
		return new entites(entites::getSessionBibliId());
	}
	
	public function get_address_entity(){
		$query_result = entites::get_coordonnees(entites::getSessionBibliId(), '1');
		return pmb_mysql_fetch_object($query_result);
	}
	
	public function get_user() {
		$query ='select * from users where userid='.$this->num_user;
		$result = pmb_mysql_query($query);
		return pmb_mysql_fetch_object($result);
	}

	public function get_total_price() {
		$total_price=0;
		foreach($this->accounts as $account){
			$total_price+=$account->get_total_price();
		}
		return number_format($total_price, 2, '.', '');
	}
	
	public function get_num_account_type(){
		$query = 'select account_type_num_section from rent_account_types_sections where account_type_num_exercice='.$this->accounts[0]->get_exercice()->id_exercice.' and account_type_marclist="'.$this->accounts[0]->get_type().'"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'account_type_num_section');
		} else {
			return 0;
		}
	}
	
	public function validate() {
		
		if($this->status == 1) {
			$this->status = 2;
			$this->valid_date = date('Y-m-d H:i:s');
						
			$acte=new actes($this->num_acte);
			$acte->statut=STA_ACT_PAY; //payé
			$acte->save();
			$this->num_acte=$acte->id_acte;
			if($this->num_acte){
				$id_ligne=0;
				$res_lignes_acte=actes::getLignes($this->num_acte);
				if (pmb_mysql_num_rows($res_lignes_acte)) {
					$row = pmb_mysql_fetch_object($res_lignes_acte);
					$id_ligne=$row->id_ligne;
				}	
				$ligne_acte=new lignes_actes($id_ligne);
				$ligne_acte->statut=$acte->statut;
				$ligne_acte->save();
			}
		}
	}
	
	protected function gen_selector_status(){
		global $msg;
	
		return '<select name="invoice_status" '.($this->status == 2 ?  "disabled='disabled'" : "").'>
			<option value="1" '.($this->status == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_new'].'</option>
			<option value="2" '.($this->status == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_validated'].'</option>
		</select>';
	}
	
	public function get_id() {
		return $this->id;
	}

	public function get_num_user() {
		return $this->num_user;
	}
	
	public function get_date() {
		return $this->date;
	}
	
	public function get_short_year_date() {
		return substr($this->date, 2, 2);
	}

	public function get_quarter() {
		if(is_object($this->accounts[0]) && (substr($this->accounts[0]->get_event_date(), 0, 10) != '0000-00-00')) {
			$result=pmb_mysql_query("SELECT QUARTER('".substr($this->accounts[0]->get_event_date(), 0, 10)."')");
		} else {
			$result=pmb_mysql_query('SELECT QUARTER(CURDATE())');
		}
		return pmb_mysql_result($result, 0, 0);
	}
	
	public function get_status_label() {
		global $msg;
		switch ($this->get_status()) {
			case 2 :
				return $msg['acquisition_invoice_status_validated'];
				break;
			case 1 :
			default :
				return $msg['acquisition_invoice_status_new'];
				break;
		}
	}
	
	public function get_formatted_date() {
		return $this->formatted_date;
	}
	
	public function get_status() {
		return $this->status;
	}
	
	public function get_valid_date() {
		return $this->valid_date;
	}
	
	public function get_formatted_valid_date() {
		return $this->formatted_valid_date;
	}
	
	public function get_destination() {
		return $this->destination;
	}

	public function get_destination_name() {
		return $this->destination_name;
	}

	public function get_num_acte() {
		return $this->num_acte;
	}
	
	public function get_accounts() {
		return $this->accounts;
	}
	
	public function is_in_edit() {
		return $this->in_edit;
	}
	
	public function set_id($id) {
		$this->id = intval($id);
	}
	
	public function set_num_user($num_user) {
		$this->num_user = intval($num_user);
	}
			
	public function set_date($date) {
		$this->date = $date;
	}
	
	public function set_status($status) {
		$this->status = $status;
	}
	
	public function set_valid_date($valid_date) {
		$this->valid_date = $valid_date;
	}
	
	public function set_destination($destination) {
		$this->destination = $destination;
	}
	
	public function set_destination_name($destination_name) {
		$this->destination_name = $destination_name;
	}

	public function set_num_acte($num_acte) {
		$this->num_acte = $num_acte;
	}
	
	public function set_accounts($accounts) {
		$this->accounts = $accounts;
	}

	public function gen_invoice() {		
		global $msg, $include_path, $charset;
		
		$tpl = $include_path.'/templates/rent/rent_account_invoice.tpl.html';
		if (file_exists($include_path.'/templates/rent/rent_account_invoice_subst.tpl.html')) {
			$tpl = $include_path.'/templates/rent/rent_account_invoice_subst.tpl.html';
		}
		$h2o = H2o_collection::get_instance($tpl);
		
		$invoice_tpl = $h2o->render(array('invoice' => $this));
		if($charset!="utf-8"){
			$invoice_tpl=encoding_normalize::utf8_normalize($invoice_tpl);
		}
		
		$html2pdf = new Html2Pdf('PL','A4','fr');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($invoice_tpl);
		$html2pdf->output(sprintf($msg['acquisition_invoice_pdf_filename'], $this->get_accounts()[0]->get_supplier()->raison_sociale, $this->get_id()).'.pdf','D');
	}
}