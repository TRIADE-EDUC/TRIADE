<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_pnb_ui.class.php,v 1.10 2018-11-09 14:45:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($include_path.'/templates/list/list_pnb_ui.tpl.php');

class list_pnb_ui extends list_ui {

	protected function _get_query_base() {
		$query = 'select id_pnb_order from pnb_orders';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new pnb_order($row->id_pnb_order);
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		
		$this->filters = array(
		    'alert_end_offers' => '',
		    'alert_staturation_offers' => '',
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'order_id' => 'edit_pnb_order_id',
					'line_id' => 'edit_pnb_order_line_id',
					'notice' => 'edit_pnb_order_notice',
					'loan_max_duration' => 'edit_pnb_order_loan_max_duration',
					'nb_loans' => 'edit_pnb_order_nb_loans',
					'nb_simultaneous_loans' => 'edit_pnb_order_nb_simultaneous_loans',
					'nb_consult_in_situ' => 'edit_pnb_order_nb_consult_in_situ',
					'nb_consult_ex_situ' => 'edit_pnb_order_nb_consult_ex_situ',
					'offer_date' => 'edit_pnb_order_offer_date',
					'offer_date_end' => 'edit_pnb_order_offer_date_end',
					'offer_duration' => 'edit_pnb_order_offer_duration',
			)
		);
		
	}
	
	protected function init_default_columns() {
		global $action;
		$this->add_column('order_id', 'edit_pnb_order_id');
		$this->add_column('line_id', 'edit_pnb_order_line_id');
		$this->add_column('notice', 'edit_pnb_order_notice');
		$this->add_column('loan_max_duration','edit_pnb_order_loan_max_duration');
		$this->add_column('nb_loans','edit_pnb_order_nb_loans');
		$this->add_column('nb_simultaneous_loans','edit_pnb_order_nb_simultaneous_loans');
		$this->add_column('nb_consult_in_situ','edit_pnb_order_nb_consult_in_situ');
		$this->add_column('nb_consult_ex_situ','edit_pnb_order_nb_consult_ex_situ');
		$this->add_column('offer_formated_date','edit_pnb_order_offer_date');
		$this->add_column('offer_formated_date_end','edit_pnb_order_offer_date_end');
		$this->add_column('offer_duration','edit_pnb_order_offer_duration');
		$this->add_column_sel_button();
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => 10,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'offer_date',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {	
		if ($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'offer_date':
					$order .= 'pnb_order_offer_date';
					break;
				case 'offer_date_end':
					$order .= 'pnb_order_offer_date_end';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if ($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc']; 
			} else {
				return "";
			}
		}	
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	    global $alert_end_offers, $alert_staturation_offers;
	
		if (isset($alert_end_offers)) {
			$this->filters['alert_end_offers'] = $alert_end_offers;
		} else {			
			$this->filters['alert_end_offers'] = '';
		}
		if (isset($alert_staturation_offers)) {
		    $this->filters['alert_staturation_offers'] = $alert_staturation_offers;
		} else {
		    $this->filters['alert_staturation_offers'] = '';
		}
		parent::set_filters_from_form();
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $pmb_pnb_alert_end_offers;
		
		$filter_query = '';		
		$this->set_filters_from_form();
		
		$filters = array();
		if ($this->filters['alert_end_offers']) {
			$filters [] = " DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
		}
		if ($this->filters['alert_staturation_offers']) {
		    $filters [] = " DATE_ADD(pnb_order_offer_date_end, INTERVAL - " . $pmb_pnb_alert_end_offers . " DAY) < NOW() ";
		}

		if (count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}	
		return $filter_query;
	}
	
	protected function fetch_data() {
	    $this->objects = array();
	    $query = $this->_get_query_base();
	    $query .= $this->_get_query_filters();
	    
	    if ($this->filters['alert_staturation_offers']) {
	        global $pmb_pnb_alert_staturation_offers;
	        $query = "select id_pnb_order from (select * from pnb_orders
	        join pnb_loans on pnb_loan_order_line_id = pnb_order_line_id
	        group by pnb_order_line_id having count(id_pnb_loan) >= pnb_order_nb_simultaneous_loans - " . $pmb_pnb_alert_staturation_offers . " ) as t";	        
	    }
	    
	    $query .= $this->_get_query_order();
	    if ($this->applied_sort_type == "SQL"){
	        $this->pager['nb_results'] = pmb_mysql_num_rows(pmb_mysql_query($query));
	        $query .= $this->_get_query_pager();
	    }
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while($row = pmb_mysql_fetch_object($result)) {
	            $this->add_object($row);
	        }
	        if ($this->applied_sort_type != "SQL"){
	            $this->pager['nb_results'] = pmb_mysql_num_rows($result);
	        }
	    }
	    $this->messages = "";
	}
	
	/**
	 * Fonction de callback
	 * @param account $a
	 * @param account $b
	 */
	protected function _compare_objects($a, $b) {
		if ($this->applied_sort['by']) {
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;		
		global $list_pnb_ui_script_case_a_cocher;
		
		$display = parent::get_js_sort_script_sort();
		$display.= $list_pnb_ui_script_case_a_cocher;
		$display = str_replace('!!categ!!', 'pnb', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $base_path;
		
		$content = '';
		switch($property) {
			case 'notice' :			    
				if ($object->get_num_notice()) {
				    $disp = new mono_display($object->get_num_notice(), 0, './catalog.php?categ=isbd&id=!!id!!');					
				    $content.= $disp->header;
				} elseif ($object->get_num_bulletin()) {
				    $disp = new bulletinage_display($object->get_num_bulletin(), 0, './catalog.php?categ=serials&sub=view&serial_id=!!id!!');
				    $content.= $disp->header;
				}
				break;
			case 'nb_loans':
				$content.=  "
					<script type=\"text/javascript\">
						addLoadEvent(function() {
						pnb_get_loans_completed_number_by_line_id('" . $object->get_line_id() . "');
						});
					</script>
					<span id='nb_loans_" . $object->get_line_id() . "'></span> / 
				";
				$content.=  parent::get_cell_content($object, $property);
				break;
			case 'order_id':
			case 'order_line_id':
			case 'order_notice':
			case 'order_loan_max_duration':
			case 'order_nb_simultaneous_loans':
			case 'order_nb_consult_in_situ':
			case 'order_nb_consult_ex_situ':
			case 'order_offer_date':
			case 'order_offer_date_end':
			case 'order_offer_duration':
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}		
		return $content;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->get_id(), $value);
		$display = "<td>".$value."</td>";
		return $display;
	}
	
	protected function get_display_cell($object, $property) {
		$display = "<td>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	protected function get_edition_link() {
		global $msg;
		global $sub;
		
		$edition_link = '';
		
		return $edition_link;
	}
		
	protected function add_column_sel_button() {
		$this->columns[] = array(
				'property' => '',
		    'label' => "<div class='center'><input type='button' id='check_all_command_lines' class='bouton' name='+' value='+'></div>",
		    'html' => "<div class='center'><input type='checkbox' data-pnb name='sel_!!id!!' value='!!id!!'></div>"
		);
	}
	
	public function get_display_list() {
		global $msg, $charset;
		global $current_module;
		
		return parent::get_display_list();
	}
		
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if ($this->filters['alert_end_offers']) {
			$humans[] = "<b>".htmlentities($msg['pnb_edit_end_offers_filter'], ENT_QUOTES, $charset)."</b> ";
		}
		if ($this->filters['alert_staturation_offers']) {
		    $humans[] = "<b>".htmlentities($msg['pnb_edit_staturation_offers_filter'], ENT_QUOTES, $charset)."</b> ";
		}
		$human_query = "<div class='align_left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";
		return $human_query;
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
	
		return $base_path.'/edit.php?categ=pnb&sub=' . $sub;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $pnb_ui_search_filters_form_tpl;
		
		$search_filters_form = $pnb_ui_search_filters_form_tpl;			
		$search_filters_form = str_replace('!!alert_end_offers_checked!!', ($this->filters['alert_end_offers'] ? 'checked=checked' : '' ), $search_filters_form);
		$search_filters_form = str_replace('!!alert_staturation_offers_checked!!', ($this->filters['alert_staturation_offers'] ? 'checked=checked' : '' ), $search_filters_form);
		
		return $search_filters_form;
	}

	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	public function get_offer_formated_date_end() {
		
	}
}