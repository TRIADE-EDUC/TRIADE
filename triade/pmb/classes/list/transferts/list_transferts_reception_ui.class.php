<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_reception_ui.class.php,v 1.1 2018-12-27 10:05:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/transferts/list_transferts_ui.class.php');

class list_transferts_reception_ui extends list_transferts_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $msg;
		return "<h1>".$msg['transferts_circ_menu_titre']." > ".$msg['transferts_circ_menu_reception']."</h1>";
	}
	
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
		$this->add_column('record', '233');
		$this->add_column('cb', '232');
		$this->add_column('empr', 'transferts_circ_empr');
		$this->add_column('source', 'transferts_circ_source');
		$this->add_column('expl_owner', '651');
		$this->add_column('formatted_date_creation', 'transferts_circ_date_creation');
		$this->add_column('formatted_date_envoyee', 'transferts_circ_date_envoi');
		$this->add_column('motif', 'transferts_circ_motif');
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_send_user_num', 'transferts_edition_send_user');
		if(($action == '' || $action == 'list') && $transferts_reception_lot == '1') {
			$this->add_column_sel_button();
		}
	}
	
	protected function get_edition_link() {
		return '';
	}
	
	protected function get_search_filters() {
		global $msg;
		$search_filters = '';
		$search_filters .= "&nbsp;".$msg['transferts_circ_reception_filtre_source'];
		$search_filters .= "<select name='".$this->objects_type."_site_origine'>";
		$search_filters .= $this->get_search_options_locations($this->filters['site_origine']);
		$search_filters .= "</select>";
		return $search_filters;
	}
	
	protected function get_display_selection_actions() {
		global $msg;
		global $transferts_reception_lot;
		if ($transferts_reception_lot=="1") {
			return "<input type='button' class='bouton' name='".$msg["transferts_circ_btReception"]."' value='".$msg["transferts_circ_btReception"]."' onclick='verifChk(document.".$this->get_form_name().",\"aff_recep\")'>";
		} else {
			return "";
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
		global $msg, $charset;
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
		$display = str_replace('!!motif!!', $motif, $display);
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