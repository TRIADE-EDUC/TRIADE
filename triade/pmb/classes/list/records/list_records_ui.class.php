<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_records_ui.class.php,v 1.1 2018-12-28 13:15:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/analyse_query.class.php");

class list_records_ui extends list_ui {
		
	protected $aq_members;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$aq_members = $this->get_aq_members();
		$query = 'SELECT *,'.$aq_members["select"].' as pert FROM notices ';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'pert',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $sub;
		
		$this->filters = array(
				'user_query' => '*',
				'niveau_biblio' => '',
				'niveau_hierar' => ''
		);
		parent::init_filters($filters);
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$user_query = $this->objects_type.'_user_query';
		global ${$user_query};
		if(isset(${$user_query}) && ${$user_query} != '') {
			$this->filters['user_query'] = ${$user_query};
		}
		parent::set_filters_from_form();
	}
		
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $base_path, $categ, $sub;
		
		$this->is_displayed_options_block = true;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	protected function get_aq_members() {
		global $msg;
	
		if(!isset($this->aq_members)) {
			$aq=new analyse_query($this->filters['user_query']);
			if ($aq->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				exit();
			}
			$this->aq_members=$aq->get_query_members("notices","index_wew","index_sew","notice_id");
		}
		return $this->aq_members;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['user_query']) {
			$aq_members = $this->get_aq_members();
			$filters[] = $aq_members["where"];
		}
		if($this->filters['niveau_biblio']) {
			$filters [] = 'niveau_biblio = "'.$this->filters['niveau_biblio'].'"';
		}
		if($this->filters['niveau_hierar']) {
			$filters [] = 'niveau_hierar = "'.$this->filters['niveau_hierar'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
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
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
			case 'record_isbd':
				$cart_click_noti = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=".$object->notice_id."', 'cart')\"";
				$url = "./catalog.php?categ=serials&sub=view&serial_id=".$object->notice_id;
				
				$content .= "<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".$msg[400]."' ".$cart_click_noti.">";
				$content .= "<a href='".$url."'>".$object->get_record_isbd()."</a>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}