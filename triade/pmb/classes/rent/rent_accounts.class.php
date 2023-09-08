<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_accounts.class.php,v 1.30 2017-12-11 09:21:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rent/rent_root.class.php");
require_once($class_path."/rent/rent_account.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/editor.class.php");
require_once($include_path."/templates/rent/rent_accounts.tpl.php");

class rent_accounts extends rent_root {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_account from rent_accounts';
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new rent_account($row->id_account);
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
				'id_request_type' => '',
				'id_type' => '',
				'num_publisher' => '',
				'num_supplier' => '',
				'num_author' => '',
				'num_pricing_system' => '',
				'web' => '',
				'date_start' => '',
				'date_end' => '',
				'event_date_start' => '',
				'event_date_end' => '',
				'invoiced' => '',
				'request_status' => 0
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
		global $accounts_search_form_entities;
		global $accounts_search_form_exercices;
		global $accounts_search_form_request_types;
		global $accounts_search_form_types;
		global $accounts_search_form_num_publisher;
		global $accounts_search_form_num_supplier;
		global $accounts_search_form_num_author;
		global $accounts_search_form_date_start;
		global $accounts_search_form_date_end;
		global $accounts_search_form_event_date_start;
		global $accounts_search_form_event_date_end;
		global $accounts_search_form_num_pricing_system;
		global $accounts_search_form_web;
		global $accounts_search_form_invoiced_filter;
		global $accounts_search_form_request_status;
		
		if(isset($accounts_search_form_entities)) {
			$this->filters['id_entity'] = $accounts_search_form_entities*1;
		}
		if(isset($accounts_search_form_exercices)) {
			$this->filters['id_exercice'] = $accounts_search_form_exercices*1;
		}
		if(isset($accounts_search_form_request_types)) {
			$this->filters['id_request_type'] = $accounts_search_form_request_types;
		}
		if(isset($accounts_search_form_types)) {
			$this->filters['id_type'] = $accounts_search_form_types;
		}
		if(isset($accounts_search_form_num_publisher)) {
			$this->filters['num_publisher'] = $accounts_search_form_num_publisher*1;
		}
		if(isset($accounts_search_form_num_supplier)) {
			$this->filters['num_supplier'] = $accounts_search_form_num_supplier*1;
		}
		if(isset($accounts_search_form_num_author)) {
			$this->filters['num_author'] = $accounts_search_form_num_author*1;
		}
		if(isset($accounts_search_form_num_pricing_system)) {
			$this->filters['num_pricing_system'] = $accounts_search_form_num_pricing_system*1;
		}
		if(isset($accounts_search_form_web)) {
			$this->filters['web'] = stripslashes($accounts_search_form_web);
		}
		if(isset($accounts_search_form_date_start)) {
			$this->filters['date_start'] = stripslashes($accounts_search_form_date_start);
		}
		if(isset($accounts_search_form_date_end)) {
			$this->filters['date_end'] = stripslashes($accounts_search_form_date_end);
		}
		if(isset($accounts_search_form_event_date_start)) {
			$this->filters['event_date_start'] = stripslashes($accounts_search_form_event_date_start);
		}
		if(isset($accounts_search_form_event_date_end)) {
			$this->filters['event_date_end'] = stripslashes($accounts_search_form_event_date_end);
		}
		if(isset($accounts_search_form_invoiced_filter)) {
			$this->filters['invoiced'] = $accounts_search_form_invoiced_filter*1;
		}
		if(isset($accounts_search_form_request_status)) {
			$this->filters['request_status'] = $accounts_search_form_request_status*1;
		}
		//Sauvegarde des filtres en session
		$this->set_filter_in_session();
	}
		
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg, $charset;
		global $rent_accounts_search_form_tpl;
		
		$search_form = $rent_accounts_search_form_tpl;
		
		$search_form = str_replace('!!form_title!!', htmlentities($msg['search'], ENT_QUOTES, $charset).' : '.htmlentities($msg['acquisition_rent_accounts'], ENT_QUOTES, $charset), $search_form);
		$search_form = str_replace('!!selector_entities!!', entites::getBibliHtmlSelect(SESSuserid, $this->filters['id_entity'], false, array('id' => 'accounts_search_form_entities', 'name' => 'accounts_search_form_entities', 'onchange'=>'account_load_exercices(this.value);')), $search_form);
		$search_form = str_replace('!!selector_exercices!!', static::gen_selector_exercices($this->filters['id_entity'], 'accounts', $this->filters['id_exercice']), $search_form);
		$account_types = new marc_select('rent_account_type', 'accounts_search_form_types', $this->filters['id_type'], '', 0, $msg['acquisition_account_type_select_all']);
		$search_form = str_replace('!!selector_types!!', $account_types->display, $search_form);
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
		$search_form = str_replace('!!num_author!!', $this->filters['num_author'], $search_form);
		if($this->filters['num_author']) {
			$author = new auteur($this->filters['num_author']);
			$search_form = str_replace('!!author!!', $author->display, $search_form);
		} else {
			$search_form = str_replace('!!author!!', '', $search_form);
		}
		$selector_pricing_systems = gen_liste("select id_pricing_system, pricing_system_label from rent_pricing_systems","id_pricing_system","pricing_system_label","accounts_search_form_num_pricing_system","",$this->filters['num_pricing_system'], 0, "", 0, $msg['demandes_localisation_all']);
		$search_form = str_replace("!!pricing_systems!!",$selector_pricing_systems,$search_form);
		//$search_form = str_replace('!!web_checked!!', ($this->filters['web'] ? "checked='checked'" : ""), $search_form);
		
		$search_form = str_replace('!!event_date_start!!', $this->filters['event_date_start'], $search_form);
		$search_form = str_replace('!!event_date_end!!', $this->filters['event_date_end'], $search_form);
		$search_form = str_replace('!!selector_account_invoiced_filter!!', $this->get_selector_invoiced($this->filters['invoiced']), $search_form);
		$search_form = str_replace('!!selector_request_status!!', $this->get_selector_request_status(), $search_form);
		$search_form = str_replace('!!link_add_account!!', './acquisition.php?categ=rent&sub=accounts&action=edit&id=0', $search_form);
		$search_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_form);
		$search_form = str_replace('!!page!!', $this->pager['page'], $search_form);
		$search_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_form);
		$search_form = str_replace('!!pager!!', json_encode($this->pager), $search_form);
		$search_form = str_replace('!!messages!!', $this->get_messages(), $search_form);
				
		return $search_form;
	}

	protected function get_selector_invoiced($invoiced=0){
		global $msg;
	
		return '<select name="accounts_search_form_invoiced_filter">
			<option value="0" '.($invoiced == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($invoiced == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_not_invoiced'].'</option>
			<option value="2" '.($invoiced == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_invoiced'].'</option>
		</select>';
	}
	
	/**
	 * 
	 */
	protected function get_selector_request_status(){
		global $msg;
		
		return '<select name="accounts_search_form_request_status">
			<option value="0" '.($this->filters['request_status'] == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($this->filters['request_status'] == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_not_ordered'].'</option>
			<option value="2" '.($this->filters['request_status'] == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_ordered'].'</option>
			<option value="3" '.($this->filters['request_status'] == 3 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_account'].'</option>
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
		
		if($this->filters['id_request_type']) {
			$filters [] = 'account_request_type = "'.$this->filters['id_request_type'].'"';
		}
		if($this->filters['id_type']) {
			$filters [] = 'account_type = "'.$this->filters['id_type'].'"';
		}
		if($this->filters['num_publisher']) {
			$filters [] = 'account_num_publisher = "'.$this->filters['num_publisher'].'"';
		}
		if($this->filters['num_supplier']) {
			$filters [] = 'account_num_supplier = "'.$this->filters['num_supplier'].'"';
		}
		if($this->filters['num_author']) {
			$filters [] = 'account_num_author = "'.$this->filters['num_author'].'"';
		}
		if($this->filters['num_pricing_system']) {
			$filters [] = 'account_num_pricing_system = "'.$this->filters['num_pricing_system'].'"';
		}
		if($this->filters['web']) {
			$filters [] = 'account_web = "'.$this->filters['web'].'"';
		}
		if($this->filters['date_start']) {
			$filters [] = 'account_date >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'account_date <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		if($this->filters['event_date_start']) {
			$filters [] = 'account_event_date >= "'.$this->filters['event_date_start'].'"';
		}
		if($this->filters['event_date_end']) {
			$filters [] = 'account_event_date <= "'.$this->filters['event_date_end'].' 23:59:59"';
		}
		if($this->filters['invoiced']==1) {
			$filters [] = 'id_account not in(select account_invoice_num_account from rent_accounts_invoices)';
		}elseif($this->filters['invoiced']==2) {
			$filters [] = 'id_account in(select account_invoice_num_account from rent_accounts_invoices)';
		}
		if($this->filters['request_status']) {
			$filters [] = 'account_request_status = "'.$this->filters['request_status'].'"';
		}
		$filter_query .= ' where '.implode(' and ', $filters);		
		return $filter_query;
	}
		
	/**
	 * Fonction de callback
	 * @param account $a
	 * @param account $b
	 */
	protected function _compare_objects($a, $b) {
		if($this->applied_sort['by']) {
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'num_user' :
					return strcmp($a->get_user()->prenom.' '.$a->get_user()->nom, $b->get_user()->prenom.' '.$b->get_user()->nom);
					break;
				case 'num_publisher' :
					return strcmp($a->get_publisher()->display, $b->get_publisher()->display);
					break;
				case 'num_supplier' :
					return strcmp($a->get_supplier()->raison_sociale, $b->get_supplier()->raison_sociale);
					break;
				case 'num_author' :
					return strcmp($a->get_author()->display, $b->get_author()->display);
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
		foreach ($this->objects as $account) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$td_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$tr_css_style = "";
			if($account->is_editable()) {
				$td_javascript .= " onmousedown=\"document.location='./acquisition.php?categ=rent&sub=accounts&action=edit&id_bibli=".$id_bibli."&id=".$account->get_id()."';\" ";
				$tr_css_style = "style='cursor: pointer;'";
			}
			$publisher_diplay = '';
			if(isset($account->get_publisher()->display)) $publisher_diplay = $account->get_publisher()->display;
			$supplier_diplay = '';
			if(isset($account->get_supplier()->raison_sociale)) $supplier_diplay = $account->get_supplier()->raison_sociale;
			$author_diplay = '';
			if(isset($account->get_author()->display)) $author_diplay = $account->get_author()->display;
		
			$display .= "<tr class='$pair_impair' $tr_css_style>";
			$display .= '<td><input type="checkbox" id="account_'.$account->get_id().'" name="accounts[]" value="'.$account->get_id().'" /></td>';
			$display .= '<td '.$td_javascript.' class="center">'.$account->get_id().'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_user()->prenom.' '.$account->get_user()->nom.'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_request_type_name().'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_type_name().'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.formatdate($account->get_date()).'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_title().'</td>';
			$display .= '<td '.$td_javascript.'>'.$publisher_diplay.'</td>';
			$display .= '<td '.$td_javascript.'>'.$supplier_diplay.'</td>';
			$display .= '<td '.$td_javascript.'>'.$author_diplay.'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.$account->get_formatted_event_date().'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_request_status_label().'</td>';
			$display .= '<td class="center" id="icon_'.$account->get_id().'">'.$account->get_state_invoice().'</td>';
			$display .= '</tr>';
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
		<th onclick=\"".$this->objects_type."_sort_by('".$name."', this.getAttribute('data-sorted'));\" data-sorted='".($this->applied_sort['by'] == $name ? $data_sorted : '')."'>
				".htmlentities($msg['acquisition_account_'.$name],ENT_QUOTES,$charset)."
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
			<th style='width:5px'><input type='checkbox' id='accounts_select_all' value='1' onchange='accounts_select();' title=\"".$msg['acquisition_accounts_select_unselect']."\" /></th>
			".$this->_get_cell_header('id')."
			".$this->_get_cell_header('num_user')."
			".$this->_get_cell_header('request_type_name')."
			".$this->_get_cell_header('type_name')."
			".$this->_get_cell_header('date')."	
			".$this->_get_cell_header('title')."
			".$this->_get_cell_header('num_publisher')."
			".$this->_get_cell_header('num_supplier')."
			".$this->_get_cell_header('num_author')."
			".$this->_get_cell_header('event_date')."
			".$this->_get_cell_header('request_status')."
			".$this->_get_cell_header('state_icon')."
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste des décomptes
	 */
	public function get_display_list() {
		global $msg, $charset;
		
		// Affichage du formulaire de recherche
		$display = $this->get_search_form();
		
		// Affichage de la human_query
		$display .= $this->_get_query_human();
		
		//Affichage de la liste des décomptes
		$display .= "<table id='accounts_list'>";
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
				<input type='button' class='bouton' onclick=\"accounts_gen_invoices(); return false;\" value='".htmlentities($msg['acquisition_account_gen_invoices'], ENT_QUOTES, $charset)."' />
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
		if($this->filters['id_request_type']) {
			$account_request_types = new marc_list('rent_request_type');
			$humans[] = "<b>".htmlentities($msg['acquisition_account_request_type_name'], ENT_QUOTES, $charset)."</b> ".$account_request_types->table[$this->filters['id_request_type']];
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
		if($this->filters['num_author']) {
			$author = new auteur($this->filters['num_author']);
			$humans[] = "<b>".htmlentities($msg['acquisition_account_num_author'], ENT_QUOTES, $charset)."</b> ".$author->display;
		}
		if($this->filters['num_pricing_system']) {
			$rent_pricing_system = new rent_pricing_system($this->filters['num_pricing_system']);
			$humans[] = "<b>".htmlentities($msg['acquisition_account_num_pricing_system'], ENT_QUOTES, $charset)."</b> ".$rent_pricing_system->get_label();
		}
		if($this->filters['web']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_web'], ENT_QUOTES, $charset)."</b> ".$msg['acquisition_account_web_yes'];
		}
		if($this->filters['date_start']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_date_start'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['date_start']);
		}
		if($this->filters['date_end']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_date_end'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['date_end']);
		}
		if($this->filters['event_date_start']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_event_date_start'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['event_date_start']);
		}
		if($this->filters['event_date_end']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_event_date_end'], ENT_QUOTES, $charset)."</b> ".formatdate($this->filters['event_date_end']);
		}
		if($this->filters['invoiced']==2) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_invoiced_filter'], ENT_QUOTES, $charset)."</b> ".htmlentities($msg['acquisition_account_invoiced'], ENT_QUOTES, $charset);
		}elseif($this->filters['invoiced']==1) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_invoiced_filter'], ENT_QUOTES, $charset)."</b> ".htmlentities($msg['acquisition_account_not_invoiced'], ENT_QUOTES, $charset);
		}
		if($this->filters['request_status']) {
			$human_request_status = "<b>".htmlentities($msg['acquisition_account_request_status'], ENT_QUOTES, $charset)."</b> ";
			switch ($this->filters['request_status']) {
				case 1 :
					$human_request_status .= htmlentities($msg['acquisition_account_request_status_not_ordered'], ENT_QUOTES, $charset);
					break;
				case 2 :
					$human_request_status .= htmlentities($msg['acquisition_account_request_status_ordered'], ENT_QUOTES, $charset);
					break;
				case 3 :
					$human_request_status .= htmlentities($msg['acquisition_account_request_status_account'], ENT_QUOTES, $charset);
					break;
			}
			$humans[] = $human_request_status; 
		}
		$human_query = "<div class='align_left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";		
		return $human_query;
	}
}