<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_ui.class.php,v 1.1 2018-12-27 10:32:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/resa.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/expl.class.php");

class list_reservations_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = "SELECT resa_idempr, resa_idnotice, resa_idbulletin, resa_cb
			FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id )
			LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id)
			LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
			LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb), empr, docs_location ";
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = new reservation($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin, $row->resa_cb);
	}
	
	protected function fetch_data() {
		$this->objects = array();
		
		$query = $this->_get_query_base();
		$query .= $this->_get_query_filters();
		$query .= $this->_get_query_order();
		$query .= "  
			group by resa_idnotice, resa_idbulletin, resa_idempr";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->add_object($row);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises, $deflt_resas_location;
		
		$this->filters = array(
				'removal_location' => ($pmb_lecteurs_localises ? $deflt_resas_location : ''),
				'available_location' => '',
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
						'record' => '233',
						'cote' => '296',
						'empr' => 'empr_nom_prenom',
						'empr_location' => 'empr_location',
						'' => '366',
						'' => '374',
						'expl_location' => 'edit_resa_expl_location',
						'section' => '295',
						'statut' => '297',
						'support' => '294',
						'' => '232'
				)
		);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				case 'cote' :
					$order .= 'expl_cote';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
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
		global $removal_location;
		global $available_location;
		
		if(isset($removal_location)) {
			$this->filters['removal_location'] = $removal_location*1;
		}
		if(isset($available_location)) {
			$this->filters['available_location'] = $available_location*1;
		}
		parent::set_filters_from_form();
	}
		
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg, $current_module;
		global $list_reservations_ui_search_filters_form_tpl;
		global $pmb_transferts_actif, $pmb_location_reservation;
		global $deflt_resas_location;
		
		$search_filters_form = $list_reservations_ui_search_filters_form_tpl;
		if ($pmb_transferts_actif || $pmb_location_reservation) {
			if ($this->filters=="")	$f_loc = $deflt_resas_location;
			$query = "SELECT idlocation, location_libelle FROM docs_location order by location_libelle";
			$removal_location = gen_liste ($query, "idlocation", "location_libelle", "removal_location", "document.forms['form-$current_module-list'].dest.value='';document.forms['form-$current_module-list'].submit();", $this->filters['removal_location'], -1,"",0, $msg["all_location"]);
			$available_location = gen_liste ($query, "idlocation", "location_libelle", "available_location", "document.forms['form-$current_module-list'].dest.value='';document.forms['form-$current_module-list'].submit();", $this->filters['available_location'], -1,"",0, $msg["all_location"]);
			$search_filters_form = str_replace('!!removal_location!!', $removal_location, $search_filters_form);
			$search_filters_form = str_replace('!!available_location!!', $available_location, $search_filters_form);
		} else {
			$search_filters_form = str_replace('!!removal_location!!', '', $search_filters_form);
			$search_filters_form = str_replace('!!available_location!!', '', $search_filters_form);
		}
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
		
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $pmb_transferts_actif;
		global $transferts_choix_lieu_opac;
		global $transferts_site_fixe;
		global $pmb_location_reservation;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		
		if($this->filters['removal_location']) {
// 			$filters [] = 'empr_location = "'.$this->filters['empr_location_id'].'"';
		}
		if($this->filters['available_location']) {
// 			$filters [] = 'expl_location = "'.$this->filters['docs_location_id'].'"';
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
				case 'cote':
					return strcmp($a->get_exemplaire()->{$sort_by}, $b->get_exemplaire()->{$sort_by});
					break;
				case 'record' :
					return strcmp($a->get_exemplaire()->get_notice_title(), $b->get_exemplaire()->get_notice_title());
					break;
				case 'empr':
					return strcmp(emprunteur::get_name($a->id_empr), emprunteur::get_name($b->id_empr));
					break;
				case 'empr_location':
					return strcmp(emprunteur::get_location($a->id_empr)->libelle, emprunteur::get_location($b->id_empr)->libelle);
					break;
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
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'notices', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $base_path;
		global $empr_show_caddie;
		global $pmb_short_loan_management, $short_loan_flag;
		global $biblio_email;
		
		$content = '';
		switch($property) {
			case 'cb_expl':
				$content .= "<b>".exemplaire::get_cb_link($object->{$property})."</b>";
				break;
			case 'cote':
				$content .= $object->get_exemplaire()->cote;
				break;
			case 'record':
				$record_title = $object->get_exemplaire()->get_notice_title();
				if (SESSrights & CATALOGAGE_AUTH) {
					if ($object->id_notice) {
						$content .= "<a href='./catalog.php?categ=isbd&id=".$object->id_notice."'>".$record_title."</a>"; // notice de monographie
					} elseif ($object->id_bulletin) {
						$content .= "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$object->id_bulletin."'>".$record_title."</a>"; // notice de bulletin
					} else {
						$content .= $record_title;
					}
				} else {
					$content .= $record_title;
				}
				break;
			case 'empr':
				if (SESSrights & CIRCULATION_AUTH) {
					$content .= "<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode(emprunteur::get_cb_empr($object->id_empr))."'>".emprunteur::get_name($object->id_empr)."</a>";
				} else {
					$content .= emprunteur::get_name($object->id_empr);
				}
				break;
			case 'empr_location':
				$content .= emprunteur::get_location($object->id_empr)->libelle;
				break;
			case 'rank':
				$content .= recupere_rang($object->id_empr, $object->id_notice, $object->id_bulletin, 0) ;
				break;
			case 'date':
				$content .= $object->formatted_date;
				break;
			case 'date_debut':
				if($object->date_debut != '0000-00-00') {
					$content .= $object->formatted_date_debut;
				}
				break;
			case 'date_fin':
				if($object->date_fin != '0000-00-00') {
					$content .= $object->formatted_date_fin;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		if($this->filters['removal_location']) {
// 			$docs_location = new docs_location($this->filters['empr_location_id']);
// 			$humans[] = $this->_get_label_query_human($msg['editions_filter_empr_location'], $docs_location->libelle);
		}
		if($this->filters['available_location']) {
			$docs_location = new docs_location($this->filters['docs_location_id']);
			$humans[] = $this->_get_label_query_human($msg['editions_filter_docs_location'], $docs_location->libelle);
		}
		return $this->get_display_query_human($humans);
	}
}