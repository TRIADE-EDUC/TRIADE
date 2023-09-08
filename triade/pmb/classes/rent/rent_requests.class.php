<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_requests.class.php,v 1.8 2017-11-07 16:06:19 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/rent/rent_accounts.class.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/editor.class.php");
require_once($include_path."/templates/rent/rent_requests.tpl.php");

class rent_requests extends rent_accounts {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg, $charset;
		global $rent_requests_search_form_tpl;
		
		$search_form = $rent_requests_search_form_tpl;
		
		$search_form = str_replace('!!form_title!!', htmlentities($msg['search'], ENT_QUOTES, $charset).' : '.htmlentities($msg['acquisition_rent_requests'], ENT_QUOTES, $charset), $search_form);
		$search_form = str_replace('!!selector_entities!!', entites::getBibliHtmlSelect(SESSuserid, $this->filters['id_entity'], false, array('id' => 'accounts_search_form_entities', 'name' => 'accounts_search_form_entities', 'onchange'=>'account_load_exercices(this.value);')), $search_form);
		$search_form = str_replace('!!selector_exercices!!', static::gen_selector_exercices($this->filters['id_entity'], 'accounts', $this->filters['id_exercice']), $search_form);
		$request_types = new marc_select('rent_request_type', 'accounts_search_form_request_types', $this->filters['id_request_type'], '', 0, $msg['acquisition_account_type_select_all']);
		$search_form = str_replace('!!selector_types!!', $request_types->display, $search_form);
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
		$search_form = str_replace('!!event_date_start!!', $this->filters['event_date_start'], $search_form);
		$search_form = str_replace('!!event_date_end!!', $this->filters['event_date_end'], $search_form);
		$search_form = str_replace('!!date_start!!', $this->filters['date_start'], $search_form);
		$search_form = str_replace('!!date_end!!', $this->filters['date_end'], $search_form);
		$search_form = str_replace('!!selector_request_status!!', $this->get_selector_request_status(), $search_form);
		$search_form = str_replace('!!link_add_request!!', './acquisition.php?categ=rent&sub=requests&action=edit&id=0', $search_form);
		$search_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_form);
		$search_form = str_replace('!!page!!', $this->pager['page'], $search_form);
		$search_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_form);
		$search_form = str_replace('!!pager!!', json_encode($this->pager), $search_form);
		$search_form = str_replace('!!messages!!', $this->get_messages(), $search_form);
		return $search_form;
	}
				
	/**
	 * Liste des demandes
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
				$td_javascript .= " onmousedown=\"document.location='./acquisition.php?categ=rent&sub=requests&action=edit&id_bibli=".$id_bibli."&id=".$account->get_id()."';\" ";
				$tr_css_style = "style='cursor: pointer;'";
			}
			$publisher_diplay = '';
			if(isset($account->get_publisher()->display)) $publisher_diplay = $account->get_publisher()->display;
			$supplier_diplay = '';
			if(isset($account->get_supplier()->raison_sociale)) $supplier_diplay = $account->get_supplier()->raison_sociale;
			$author_diplay = '';
			if(isset($account->get_author()->display)) $author_diplay = $account->get_author()->display;
			
			$display .= "<tr class='$pair_impair' $tr_css_style>";
			$display .= '<td><input type="checkbox" id="account_'.$account->get_id().'" name="requests[]" value="'.$account->get_id().'" /></td>';
			$display .= '<td '.$td_javascript.' class="center">'.$account->get_id().'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_user()->prenom.' '.$account->get_user()->nom.'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_request_type_name().'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.formatdate($account->get_date()).'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_title().'</td>';			
			$display .= '<td '.$td_javascript.'>'.$publisher_diplay.'</td>';
			$display .= '<td '.$td_javascript.'>'.$supplier_diplay.'</td>';
			$display .= '<td '.$td_javascript.'>'.$author_diplay.'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.$account->get_formatted_event_date().'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.formatdate($account->get_receipt_limit_date()).'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.formatdate($account->get_receipt_effective_date()).'</td>';
			$display .= '<td '.$td_javascript.' class="center">'.formatdate($account->get_return_date()).'</td>';
			$display .= '<td '.$td_javascript.'>'.$account->get_request_status_label().'</td>';
			$display .= '</tr>';
		}
		return $display;
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		global $msg;
		
		$display = "
		<tr>
			<th style='width:5px'><input type='checkbox' id='requests_select_all' value='1' onchange='requests_select();' title=\"".$msg['acquisition_accounts_select_unselect']."\" /></th>
			".$this->_get_cell_header('id')."
			".$this->_get_cell_header('num_user')."
			".$this->_get_cell_header('request_type_name')."
			".$this->_get_cell_header('date')."	
			".$this->_get_cell_header('title')."
			".$this->_get_cell_header('num_publisher')."
			".$this->_get_cell_header('num_supplier')."
			".$this->_get_cell_header('num_author')."
			".$this->_get_cell_header('event_date')."
			".$this->_get_cell_header('receipt_limit_date')."
			".$this->_get_cell_header('receipt_effective_date')."		
			".$this->_get_cell_header('return_date')."
			".$this->_get_cell_header('request_status')."
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste des demandes
	 */
	public function get_display_list() {
		global $msg, $charset;
		
		// Affichage du formulaire de recherche
		$display = $this->get_search_form();
		
		// Affichage de la human_query
		$display .= $this->_get_query_human();
		
		//Affichage de la liste des décomptes
		$display .= "<table id='requests_list'>";
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
				<input type='button' class='bouton' onclick=\"requests_gen_commands(); return false;\" value='".htmlentities($msg['acquisition_account_gen_commands'], ENT_QUOTES, $charset)."' />
			</div>
		</div>";
		return $display;
	}
}