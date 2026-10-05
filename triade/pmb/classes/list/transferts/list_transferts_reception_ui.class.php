<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_reception_ui.class.php,v 1.11 2023/12/15 14:56:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_transferts_reception_ui extends list_transferts_ui {
	
	protected function get_form_title() {
		global $msg;
		global $transferts_reception_lot;
		if ($transferts_reception_lot=="1") {
			return "<h3>".$msg["transferts_circ_reception_lot"]."</h3>";
		} else {
			return "<h3>".$msg["transferts_circ_lib_liste"]."</h3>";
		}
	}
	
	protected function init_default_columns() {
		global $action, $transferts_reception_lot;
		$this->add_column('record');
		$this->add_column('cb');
		$this->add_column('empr');
		$this->add_column('source');
		$this->add_column('expl_owner');
		$this->add_column('formatted_date_creation');
		$this->add_column('formatted_date_envoyee');
		$this->add_column('motif');
		$this->add_column('transfert_ask_user_num');
		$this->add_column('transfert_send_user_num');
		if(($action == '' || $action == 'list') && $transferts_reception_lot == '1') {
			$this->add_column_selection();
		}
	}
	
	protected function get_edition_link() {
		return '';
	}
	
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		
		$this->filters = array(
				'site_origine' => 0,
				'site_destination' => $deflt_docs_location,
		);
		//Surcharge si les filtres ne sont pas affiches dans ce contexte
		if(empty($this->selected_filters['site_origine'])) {
			$filters['site_origine'] = 0;
		}
		if(empty($this->selected_filters['site_destination'])) {
			$filters['site_destination'] = $deflt_docs_location;
		}
		parent::init_filters($filters);
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
				    'site_origine' => 'transferts_circ_reception_filtre_source',
				    'site_destination' => 'transferts_circ_validation_filtre_destination',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('site_origine');
	    $this->add_selected_filter('site_destination');
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $action, $transferts_reception_lot;
		
		parent::init_default_selection_actions();
		if (($action == '' || $action == 'list') && $transferts_reception_lot=="1") {
			$this->add_selection_action('recep', $msg['transferts_circ_btReception'], '');
		}
	}
	
	protected function get_display_no_results() {
		global $msg;
		global $list_transferts_ui_no_results;
		$display = $list_transferts_ui_no_results;
		$display = str_replace('!!message!!', $msg["transferts_reception_liste_vide"], $display);
		return $display;
	}
	
	protected function get_valid_form_title() {
		global $msg;
		return "<h3>".$msg["transferts_circ_reception_valide_liste"]."</h3>";
	}
	
	public function get_display_valid_list() {
		global $base_path, $sub, $action;
		global $list_transferts_ui_valid_list_tpl;
		global $PMBuserid;
		global $statut_reception, $section_reception;
		
		$display = $this->get_title();
		$display .= $list_transferts_ui_valid_list_tpl;
	
		$display = str_replace('!!submit_action!!', $base_path."/circ.php?categ=trans&sub=". $sub."&action=".str_replace('aff_', '', $action)."&statut_reception=".$statut_reception."&section_reception=".$section_reception , $display);
		$display = str_replace('!!valid_form_title!!', $this->get_valid_form_title(), $display);
		$display_valid_list = $this->get_display_header_list();
		if(count($this->objects)) {
			$display_valid_list .= $this->get_display_content_list();
		}
		$display = str_replace('!!valid_list!!', $display_valid_list, $display);
		$display = str_replace('!!motif!!', '', $display);
		$display = str_replace('!!valid_action!!', $base_path."/circ.php?categ=trans&sub=". $sub, $display);
		$display = str_replace('!!ids!!', $this->filters['ids'], $display);
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		
		//on récupere l'id du statut par défaut du site de l'utilisateur
		$rqt = "SELECT transfert_statut_defaut FROM docs_location " .
				"INNER JOIN users ON idlocation=deflt_docs_location " .
				"WHERE userid=".$PMBuserid;
		$res = pmb_mysql_query($rqt);
		$statut_defaut = pmb_mysql_result($res,0);
		
		//on remplit le select avec la liste des statuts
		$display = str_replace("!!liste_statuts!!", do_liste_statut($statut_defaut), $display);
		$display = str_replace("!!liste_sections!!", do_liste_section(0), $display);
		
		return $display;
	}
}