<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.class.php,v 1.15 2023/12/15 14:56:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path.'/templates/list/transferts/list_transferts_edition_ui.tpl.php');

class list_transferts_edition_ui extends list_transferts_ui {
	
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
						'motif_refus' => 'transferts_circ_motif_refus',
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
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'site_origine' => 'transferts_edition_filtre_origine',
						'site_destination' => 'transferts_edition_filtre_destination',
						'f_etat_date' => 'transferts_circ_retour_filtre_etat',
						'cb' => '232'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('site_origine');
		$this->add_selected_filter('site_destination');
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
	public function get_display_search_form() {
		$this->is_displayed_add_filters_block = true;
		$display_search_form = parent::get_display_search_form();
		return $display_search_form;
	}
	
	public function init_applied_sort($applied_sort=array()) {
		global $transferts_edition_ui_select_order;
		
		if($transferts_edition_ui_select_order) {
			$this->applied_sort = array(array('by' => '', 'asc_desc' => ''));
			if (isset($_SESSION['list_'.$this->objects_type.'_applied_sort'][0]['by'])) {
			    unset($_SESSION['list_'.$this->objects_type.'_applied_sort'][0]['by']);
			}
			if (isset($_SESSION['list_'.$this->objects_type.'_applied_sort'][0]['asc_desc'])) {
			    unset($_SESSION['list_'.$this->objects_type.'_applied_sort'][0]['asc_desc']);
			}
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
		if($transferts_edition_show_all_colls) {
			$this->add_column('source', 'transferts_edition_tableau_source');
		}
		if($transferts_edition_show_all_colls) {
			$this->add_column('destination', 'transferts_edition_tableau_destination');
		}
		if (isset($this->filters["etat_demande"]) && $this->filters["etat_demande"] == 4) {
            $this->add_column('motif_refus', 'transferts_circ_motif_refus');
		} else {
		    $this->add_column('motif', 'transferts_edition_tableau_motif');
		}
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_send_user_num', 'transferts_edition_send_user');

	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_display('search_form', 'datasets', true);
		$this->set_setting_column('default', 'align', 'center');
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
// 				if($column['property'] == $this->applied_sort[0]['by']) {
// 					$tmpListe.='<option value="'.$this->applied_sort[0]['by'].'">'.$this->_get_label_cell_header($column['label']).'</option>';
// 				}
// 			}
// 		}
		
		foreach ($order_list as $elt_order){
			if($elt_order['value']==$this->filters['select_order']) $selected=' selected '; else $selected='';
			$tmpListe.= "<option value='".$elt_order['value']."' ".$selected.">".$elt_order['msg']."</option>";
		}
		return $tmpListe;
	}
}