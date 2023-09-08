<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoices.class.php,v 1.27 2017-12-11 09:21:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rent/rent_root.class.php");
require_once($class_path."/rent/rent_invoice.class.php");
require_once($class_path."/rent/rent_account.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/marc_table.class.php");
require_once($include_path."/templates/rent/rent_invoices.tpl.php");

class rent_invoices extends rent_root {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select distinct id_invoice from rent_invoices 
			join rent_accounts_invoices on account_invoice_num_invoice = id_invoice
			join rent_accounts on id_account = account_invoice_num_account';
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new rent_invoice($row->id_invoice);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$id_entity = entites::getSessionBibliId();
		$query = exercices::listByEntite($id_entity);
		$result = pmb_mysql_query($query);
		$id_exercice = 0;
		if($result && pmb_mysql_num_rows($result)) {
			$id_exercice = pmb_mysql_result($result, 0, 'id_exercice');
		}
		$this->filters = array(
				'id_entity' => $id_entity,
				'id_exercice' => $id_exercice,
				'id_type' => '',
				'num_publisher' => '',
				'num_supplier' => '',
				'num_pricing_system' => '',
				'status' => 0,
				'date_start' => '',
				'date_end' => ''
		);
		foreach ($this->filters as $key => $val){
			if(isset($_SESSION['rent_'.$this->objects_type.'_filter'][$key])) {
				$this->filters[$key] = $_SESSION['rent_'.$this->objects_type.'_filter'][$key];
			}
		}
		if(count($filters)){
			foreach ($filters as $key => $val){
				$this->filters[$key]=$val;
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $invoices_search_form_entities;
		global $invoices_search_form_exercices;
		global $invoices_search_form_types;
		global $invoices_search_form_num_publisher;
		global $invoices_search_form_num_supplier;
		global $invoices_search_form_status;
		global $invoices_search_form_date_start;
		global $invoices_search_form_date_end;
		
		if(isset($invoices_search_form_entities)) {
			$this->filters['id_entity'] = $invoices_search_form_entities*1;
		}
		if(isset($invoices_search_form_exercices)) {
			$this->filters['id_exercice'] = $invoices_search_form_exercices*1;
		}
		if(isset($invoices_search_form_types)) {
			$this->filters['id_type'] = $invoices_search_form_types;
		}
		if(isset($invoices_search_form_num_publisher)) {
			$this->filters['num_publisher'] = $invoices_search_form_num_publisher*1;
		}
		if(isset($invoices_search_form_num_supplier)) {
			$this->filters['num_supplier'] = $invoices_search_form_num_supplier*1;
		}
		if(isset($invoices_search_form_status)) {
			$this->filters['status'] = $invoices_search_form_status*1;
		}
		if(isset($invoices_search_form_date_start)) {
			$this->filters['date_start'] = stripslashes($invoices_search_form_date_start);
		}
		if(isset($invoices_search_form_date_end)) {
			$this->filters['date_end'] = stripslashes($invoices_search_form_date_end);
		}
		//Sauvegarde des filtres en session
		$this->set_filter_in_session();
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg, $charset;
		global $rent_invoices_search_form_tpl;
		
		$search_form = $rent_invoices_search_form_tpl;
		
		$search_form = str_replace('!!form_title!!', htmlentities($msg['search'], ENT_QUOTES, $charset).' : '.htmlentities($msg['acquisition_rent_invoices'], ENT_QUOTES, $charset), $search_form);
		$search_form = str_replace('!!selector_entities!!', entites::getBibliHtmlSelect(SESSuserid, $this->filters['id_entity'], false, array('id' => 'invoices_search_form_entities', 'name' => 'invoices_search_form_entities', 'onchange'=>'account_load_exercices(this.value);')), $search_form);
		$search_form = str_replace('!!selector_exercices!!', static::gen_selector_exercices($this->filters['id_entity'], 'invoices', $this->filters['id_exercice']), $search_form);
		$invoice_types = new marc_select('rent_account_type', 'invoices_search_form_types', $this->filters['id_type'], '', 0, $msg['acquisition_account_type_select_all']);
		$search_form = str_replace('!!selector_types!!', $invoice_types->display, $search_form);
		$search_form = str_replace('!!num_publisher!!', $this->filters['num_publisher'], $search_form);
		if($this->filters['num_publisher']) {
			$publisher = new editeur($this->filters['num_publisher']);
			$search_form = str_replace('!!publisher!!', $publisher->display, $search_form);
		} else {
			$search_form = str_replace('!!publisher!!', '', $search_form);
		}
		$search_form = str_replace('!!num_supplier!!', $this->filters['num_supplier'], $search_form);
		if($this->filters['num_supplier']) {
			$supplier = new entites($this->filters['num_supplier']);
			$search_form = str_replace('!!supplier!!', $supplier->raison_sociale, $search_form);
		} else {
			$search_form = str_replace('!!supplier!!', '', $search_form);
		}
		$search_form = str_replace('!!selector_status!!', $this->get_selector_status($this->filters['status']), $search_form);
		$search_form = str_replace('!!date_start!!', $this->filters['date_start'], $search_form);
		$search_form = str_replace('!!date_end!!', $this->filters['date_end'], $search_form);
		$search_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_form);
		$search_form = str_replace('!!page!!', $this->pager['page'], $search_form);
		$search_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_form);
		$search_form = str_replace('!!pager!!', json_encode($this->pager), $search_form);
		$search_form = str_replace('!!messages!!', $this->get_messages(), $search_form);
		
		return $search_form;
	}
	
	protected function get_selector_status($status=0){
		global $msg;
		
		return '<select name="invoices_search_form_status"> 
			<option value= "0" '.($status == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($status == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_new'].'</option>
			<option value="2" '.($status == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_invoice_status_validated'].'</option>
		</select>';
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'account_num_exercice = "'.$this->filters['id_exercice'].'"';
		
		if($this->filters['id_type']) {
			$filters [] = 'account_type = "'.$this->filters['id_type'].'"';
		}
		if($this->filters['num_publisher']) {
			$filters [] = 'account_num_publisher = "'.$this->filters['num_publisher'].'"';
		}
		if($this->filters['num_supplier']) {
			$filters [] = 'account_num_supplier = "'.$this->filters['num_supplier'].'"';
		}
		if($this->filters['num_pricing_system']) {
			$filters [] = 'account_num_pricing_system = "'.$this->filters['num_pricing_system'].'"';
		}
		if($this->filters['status']) {
			$filters [] = 'invoice_status = "'.$this->filters['status'].'"';
		}
		if($this->filters['date_start']) {
			$filters [] = 'invoice_date >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'invoice_date <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		$filter_query .= ' where '.implode(' and ', $filters);		
		return $filter_query;
	}
	
	/**
	 * Fonction de callback
	 * @param invoice $a
	 * @param invoice $b
	 */
	protected function _compare_objects($a, $b) {
		global $sort_by;
		
		if($sort_by) {
			switch($sort_by) {
				case 'num_user' :
					return strcmp($a->get_user()->prenom.' '.$a->get_user()->nom, $b->get_user()->prenom.' '.$b->get_user()->nom);
					break;
				case 'num_publisher' :
					return strcmp((count($a->get_accounts()) ? $a->get_accounts()[0]->get_publisher()->display : ''), (count($b->get_accounts()) ? $b->get_accounts()[0]->get_publisher()->display : ''));
					break;
				case 'num_supplier' :
					return strcmp((count($a->get_accounts()) ? $a->get_accounts()[0]->get_supplier()->raison_sociale : ''), (count($b->get_accounts()) ? $b->get_accounts()[0]->get_supplier()->raison_sociale : ''));
					break;
				case 'id' :
					return $this->intcmp($a->get_id(), $b->get_id());
					break;
				default :
					return strcmp($a->{'get_'.$sort_by}(), $b->{'get_'.$sort_by}());
					break;
			}
		}
		
	}
	
	/**
	 * Liste des décomptes
	 */
	public function get_display_content_list() {
		global $id_bibli;
		
		$display = '';
		$parity=1;
		if(count($this->objects)) {
			$marclist = new marc_list('rent_destination');
			foreach ($this->objects as $invoice) {
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity++;
				
				$publisher_display = '';
				$td_javascript= " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./acquisition.php?categ=rent&sub=invoices&action=edit&id_bibli=".$id_bibli."&id=".$invoice->get_id()."';\" ";
				$display .= "<tr class='$pair_impair' style='cursor: pointer'>";
				$display .= '<td><input type="checkbox" id="invoice_'.$invoice->get_id().'" name="invoices[]" value="'.$invoice->get_id().'" /></td>';
				$display .= '<td '.$td_javascript.' class="center">'.$invoice->get_id().'</td>';
				$display .= '<td '.$td_javascript.'>'.$invoice->get_user()->prenom.' '.$invoice->get_user()->nom.'</td>';
				$display .= '<td '.$td_javascript.' class="center">'.formatdate($invoice->get_date()).'</td>';
				$accounts = $invoice->get_accounts();
				if(count($accounts)) {
					if(isset($accounts[0]->get_publisher()->display)) $publisher_display = $accounts[0]->get_publisher()->display;
					$supplier_display = '';
					if(isset($accounts[0]->get_supplier()->raison_sociale)) $supplier_display = $accounts[0]->get_supplier()->raison_sociale;
					$display .= '<td '.$td_javascript.'>'.$publisher_display.'</td>';
					$display .= '<td '.$td_javascript.'>'.$supplier_display.'</td>';
				} else {
					$display .= '<td '.$td_javascript.'></td>';
					$display .= '<td '.$td_javascript.'></td>';
				}
				$display .= '<td '.$td_javascript.' class="center">'.$invoice->get_status_label().'</td>';
				$display .= '<td '.$td_javascript.' class="center">'.formatdate($invoice->get_valid_date()).'</td>';
				$display .= '<td '.$td_javascript.'>'.$marclist->table[$invoice->get_destination()].'</td>';
				$display .= '</tr>';
			}
		}
		return $display;
	}
	
	/**
	 * Construction dynamique des cellules du header 
	 * @param unknown $name
	 */
	protected function _get_cell_header($name) {
		global $msg, $charset;
		
		$data_sorted = ($this->applied_sort['asc_desc'] ? $this->applied_sort['asc_desc'] : 'asc');
		$icon_sorted = ($data_sorted == 'asc' ? '<i class="fa fa-sort-desc"></i>' : '<i class="fa fa-sort-asc"></i>');
		return "
		<th onclick=\"invoices_sort_by('".$name."', this.getAttribute('data-sorted'));\" data-sorted='".($this->applied_sort['by'] == $name ? $data_sorted : '')."'>
				".htmlentities($msg['acquisition_invoice_'.$name],ENT_QUOTES,$charset)."
				".($this->applied_sort['by'] == $name ? $icon_sorted : '<i class="fa fa-sort"></i>')."
		</th>";
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		global $msg;
		
		$display = "
		<tr>
			<th style='width:5px'><input type='checkbox' id='invoices_select_all' value='1' onchange='invoices_select();' title=\"".$msg['acquisition_invoices_select_unselect']."\" /></th>
			".$this->_get_cell_header('id')."
			".$this->_get_cell_header('num_user')."
			".$this->_get_cell_header('date')."	
			".$this->_get_cell_header('num_publisher')."
			".$this->_get_cell_header('num_supplier')."
			".$this->_get_cell_header('status')."
			".$this->_get_cell_header('valid_date')."
			".$this->_get_cell_header('destination_name')."
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste des factures
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $id_bibli;
		
		// Affichage du formulaire de recherche
		$display = $this->get_search_form();
		
		// Affichage de la human_query
		$display .= $this->_get_query_human();
		
		//Affichage de la liste des décomptes
		$display .= "<table id='invoices_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		$display .= $this->pager();
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
			</div>
			<div class='right'>
				<input type='button' class='bouton' onclick=\"invoices_gen_invoices(); return false;\" value='".htmlentities($msg['acquisition_invoice_generate'], ENT_QUOTES, $charset)."' />
				<input type='button' class='bouton' onclick=\"invoices_validate_invoices(); return false;\" value='".htmlentities($msg['acquisition_invoice_validate'], ENT_QUOTES, $charset)."' />
			</div>
		</div>";
		return $display;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if($this->filters['id_entity']) {
			$entity = new entites($this->filters['id_entity']);
			$humans[] = "<b>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</b> ".$entity->raison_sociale;
		}
		if($this->filters['id_exercice']) {
			$exercice = new exercices($this->filters['id_exercice']);
			$humans[] = "<b>".htmlentities($msg['acquisition_budg_exer'], ENT_QUOTES, $charset)."</b> ".$exercice->libelle;
		}
	
		if($this->filters['id_type']) {
			$account_types = new marc_list('rent_account_type');
			$humans[] = "<b>".htmlentities($msg['acquisition_account_type_name'], ENT_QUOTES, $charset)."</b> ".$account_types->table[$this->filters['id_type']];
		}
		if($this->filters['num_publisher']) {
			$publisher = new editeur($this->filters['num_publisher']);
			$humans[] = "<b>".htmlentities($msg['acquisition_account_num_publisher'], ENT_QUOTES, $charset)."</b> ".$publisher->display;
		}
		if($this->filters['num_supplier']) {
			$supplier = new entites($this->filters['num_supplier']);
			$humans[] = "<b>".htmlentities($msg['acquisition_account_num_supplier'], ENT_QUOTES, $charset)."</b> ".$supplier->raison_sociale;
		}
		if($this->filters['num_pricing_system']) {
			$rent_pricing_system = new rent_pricing_system($this->filters['num_pricing_system']);
			$humans[] = "<b>".htmlentities($msg['acquisition_account_num_pricing_system'], ENT_QUOTES, $charset)."</b> ".$rent_pricing_system->get_label();
		}
		if($this->filters['status'] == 1) {
			$humans[] = "<b>".htmlentities($msg['acquisition_invoice_status'], ENT_QUOTES, $charset)."</b> ".$msg['acquisition_invoice_status_new'];
		} elseif($this->filters['status'] == 2){
			$humans[] = "<b>".htmlentities($msg['acquisition_invoice_status'], ENT_QUOTES, $charset)."</b> ".$msg['acquisition_invoice_status_validated'];
		}
		if($this->filters['date_start']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_date_start'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['date_start']);
		}
		if($this->filters['date_end']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_date_end'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['date_end']);
		}
		$human_query = "<div class='align_left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";
		return $human_query;
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
	
	/**
	 * Header de la liste du sélecteur de facture
	 */
	public function get_display_header_selector_list() {
	
		$display = "
		<tr>
			".$this->_get_cell_header('id')."
			".$this->_get_cell_header('num_user')."
			".$this->_get_cell_header('date')."
			".$this->_get_cell_header('num_publisher')."
			".$this->_get_cell_header('num_supplier')."
			".$this->_get_cell_header('destination_name')."
		</tr>
		";
		return $display;
	}
	
	/**
	 * Liste des factures du sélecteur de facture
	 */
	public function get_display_content_selector_list($id_account) {
		$display = '';
		$parity=1;
		$marclist = new marc_list('rent_destination');
		foreach ($this->objects as $invoice) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$tr_javascript= " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"
			onmousedown=\"account_add_account_in_invoice(".$id_account.", ".$invoice->get_id().");\" ";
			$display .= "<tr class='$pair_impair' style='cursor: pointer' '.$tr_javascript.'>";
			$display .= '<td class="center">'.$invoice->get_id().'</td>';
			$display .= '<td>'.$invoice->get_user()->prenom.' '.$invoice->get_user()->nom.'</td>';
			$display .= '<td class="center">'.$invoice->get_formatted_date().'</td>';
			$accounts = $invoice->get_accounts();
			if(count($accounts)) {
				$publisher_display = '';
				if(isset($accounts[0]->get_publisher()->display)) $publisher_display = $accounts[0]->get_publisher()->display;
				$supplier_display = '';
				if(isset($accounts[0]->get_supplier()->raison_sociale)) $supplier_display = $accounts[0]->get_supplier()->raison_sociale;			
				$display .= '<td>'.$publisher_display.'</td>';
				$display .= '<td>'.$supplier_display.'</td>';
			} else {
				$display .= '<td></td>';
				$display .= '<td></td>';
			}
			$display .= '<td>'.$marclist->table[$invoice->get_destination()].'</td>';
			$display .= '</tr>';
		}
		return $display;
	}
	
	/**
	 * Liste des factures dans un dialog DOJO 
	 */
	public function get_display_selector_list($id_account) {
		global $msg, $charset;
		
		// Affichage de la human_query
		$display = $this->_get_query_human();
		
		if(count($this->objects)) {
			//Affichage de la liste des factures
			$display .= '<table id="invoices_selector">';
			$display .= $this->get_display_header_selector_list();
			$display .= $this->get_display_content_selector_list($id_account);
			$display .= '</table>';
		} else {
			$display .= htmlentities($msg['acquisition_account_invoices_not_found'], ENT_QUOTES, $charset);
		}
		return $display;		
	}
}