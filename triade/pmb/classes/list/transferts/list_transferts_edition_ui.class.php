<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.class.php,v 1.1 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/transferts/list_transferts_ui.class.php');
require_once($include_path.'/templates/list/transferts/list_transferts_edition_ui.tpl.php');

class list_transferts_edition_ui extends list_transferts_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'record' => 'transferts_edition_tableau_titre',
						'cb' => 'transferts_edition_tableau_expl',
						'empr' => 'transferts_edition_tableau_empr',
						'source' => 'transferts_edition_tableau_source',
						'destination' => 'transferts_edition_tableau_destination',
						'expl_owner' => 'transferts_edition_tableau_expl_owner',
						'motif' => 'transferts_edition_tableau_motif',
						'transfert_ask_user_num' => 'transferts_edition_ask_user',
						'transfert_send_user_num' => 'transferts_edition_send_user',
						'section' => 'transferts_edition_tableau_section',
						'cote' => 'transferts_edition_tableau_cote',
						'transfert_ask_formatted_date' => 'transferts_popup_ask_date'
				)
		);
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('notices', 'num_notice');
		$this->add_custom_fields_available_columns('expl', 'num_exemplaire');
	}
	
	protected function get_title() {
		global $msg, $sub;
		return "<h1>".$msg["transferts_edition_titre"]."&nbsp;&gt;&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
	}
	
	protected function get_form_title() {
		return '';
	}
	
	protected function get_search_retour_filtre_etat_selector() {
		global $msg, $charset;
	
		$selector = "<div class='row'><label class='etiquette'>".$msg["transferts_circ_retour_filtre_etat"]."</label></div>";
		$selector .= "<div class='row'>";
		$selector .= parent::get_search_retour_filtre_etat_selector();
		$selector .= "</div>";
		return $selector;
	}
	
	protected function get_display_cell($object, $property) {
		$display = "<td class='center'>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $sub;
		global $list_transferts_edition_ui_search_filters_form_tpl;
	
		$search_filters_form = $list_transferts_edition_ui_search_filters_form_tpl;
		$search_filters_form = str_replace('!!liste_sites_origine!!', $this->get_search_options_locations($this->filters['site_origine']), $search_filters_form);
		$search_filters_form = str_replace('!!liste_sites_destination!!', $this->get_search_options_locations($this->filters['site_destination']), $search_filters_form);
		if ($sub=="retours") {
			//le filtre de l'etat de la date
			$search_filters_form = str_replace('!!retour_filtre_etat!!', $this->get_search_retour_filtre_etat_selector(), $search_filters_form);
		} else {
			$search_filters_form = str_replace('!!retour_filtre_etat!!', '', $search_filters_form);
		}
		$search_filters_form = str_replace('!!objects_type!!', $this->objects_type, $search_filters_form);
		return $search_filters_form;
	}
	
	/**
	 * Affichage du tri du formulaire de recherche
	 */
	public function get_search_order_form() {
		global $list_transferts_edition_ui_search_order_form_tpl;
		
		$search_order_form = $list_transferts_edition_ui_search_order_form_tpl;
		$search_order_form = str_replace('!!list_order!!', $this->get_list_order(), $search_order_form);
		$search_order_form = str_replace('!!objects_type!!', $this->objects_type, $search_order_form);
		return $search_order_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$this->is_displayed_options_block = true;
		$this->is_displayed_datasets_block = true;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	public function init_applied_sort($applied_sort=array()) {
		global $transferts_edition_ui_select_order;
		
		if($transferts_edition_ui_select_order) {
			$this->applied_sort = array('by' => '', 'asc_desc' => '');
			unset($_SESSION['list_'.$this->objects_type.'_applied_sort']['by']);
			unset($_SESSION['list_'.$this->objects_type.'_applied_sort']['asc_desc']);
		} else {
			parent::init_applied_sort($applied_sort);
		}
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		$order = $this->objects_type.'_select_order';
		global ${$order};
		if(isset(${$order}) && ${$order} != '') {
			return ' order by '.${$order};
		} else {
			return parent::_get_query_order();
		}
	}
	
	public function set_filters_from_form() {
		$select_order = $this->objects_type.'_select_order';
		global ${$select_order};
		$this->filters['select_order'] = '';
		if(isset(${$select_order})) {
			$this->filters['select_order'] = ${$select_order};
		}
		parent::set_filters_from_form();
	}
	
	protected function init_default_columns() {
		global $pmb_expl_data;
		global $transferts_edition_show_all_colls;
		
		$this->add_column('record', 'transferts_edition_tableau_titre');
		$this->add_column('section', 'transferts_edition_tableau_section');
		$this->add_column('cote', 'transferts_edition_tableau_cote');
		$this->add_column('cb', 'transferts_edition_tableau_expl');
		
		// paramtres perso demandé dans $pmb_expl_data
		$colonnesarray=explode(",",$pmb_expl_data);
		$this->displayed_cp = array();
		if (strstr($pmb_expl_data, "#")) {
			$this->cp=new parametres_perso("expl");
			for ($i=0; $i<count($colonnesarray); $i++) {
				if (substr($colonnesarray[$i],0,1)=="#") {
					//champ personnalisé
					if (!$this->cp->no_special_fields) {
						$id=substr($colonnesarray[$i],1);
						$this->add_column($this->cp->t_fields[$id]['NAME'], $this->cp->t_fields[$id]['TITRE']);
						$this->displayed_cp[$id] = $this->cp->t_fields[$id]['NAME'];
					}
				}
			}
		}
		
		$this->add_column('empr', 'transferts_edition_tableau_empr');
		$this->add_column('expl_owner', 'transferts_edition_tableau_expl_owner');
		$this->add_column('transfert_ask_formatted_date', 'transferts_popup_ask_date');
		if($this->filters['site_origine'] == 0 || $transferts_edition_show_all_colls) {
			$this->add_column('source', 'transferts_edition_tableau_source');
		}
		if($this->filters['site_destination'] == 0 || $transferts_edition_show_all_colls) {
			$this->add_column('destination', 'transferts_edition_tableau_destination');
		}
		$this->add_column('motif', 'transferts_edition_tableau_motif');
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_send_user_num', 'transferts_edition_send_user');

	}
	
	protected function get_display_spreadsheet_title() {
		global $msg, $sub;
		$this->spreadsheet->write_string(0,0,$msg["transferts_edition_titre"]." : ".$msg["transferts_edition_".$sub]);
	}
		
	protected function get_html_title() {
		global $msg, $sub;
		return "<h1>".$msg["transferts_edition_titre"]."&nbsp;:&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
	}
	
	protected function get_list_order() {
		global $msg;
	
		$order_list=array(
				array(
						'msg' => $msg['transferts_edition_order_cote'],
						'value' => 'expl_cote',
				),
				array(
						'msg' => $msg['transferts_edition_order_user'],
						'value' => 'transfert_ask_user_num, transfert_ask_user_num, expl_cote',
				),
				array(
						'msg' => $msg['transferts_edition_order_send_user'],
						'value' => 'transfert_send_user_num, transfert_ask_user_num, expl_cote',
				),
				array(
						'msg' => $msg['transferts_edition_order_empr'],
						'value' => 'empr_cb, expl_cote, transfert_ask_user_num',
				),
				array(
						'msg' => $msg['transferts_edition_order_ask_date'],
						'value' => 'transfert_ask_date, empr_cb ',
				),
		);
		$tmpListe='';
// 		if(count($this->applied_sort)) {
// 			foreach($this->columns as $column) {
// 				if($column['property'] == $this->applied_sort['by']) {
// 					$tmpListe.='<option value="'.$this->applied_sort['by'].'">'.$this->_get_label_cell_header($column['label']).'</option>';
// 				}
// 			}
// 		}
		
		foreach ($order_list as $elt_order){
			if($elt_order['value']==$this->filters['select_order']) $selected=' selected '; else $selected='';
			$tmpListe.= "<option value='".$elt_order['value']."' ".$selected.">".$elt_order['msg']."</option>";
		}
		return $tmpListe;
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
	
		return $base_path.'/edit.php?categ=transferts&sub='.$sub;
	}
}