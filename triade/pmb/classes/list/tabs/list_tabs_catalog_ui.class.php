<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_catalog_ui.class.php,v 1.3 2023/05/05 12:34:46 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_catalog_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $opac_avis_allow, $opac_allow_add_tag;
		global $pmb_collstate_advanced, $pmb_serialcirc_active;
		global $acquisition_active, $z3950_accessible, $z3950_accessible;
		global $pmb_contribution_area_activate, $deflt_bypass_isbn_page;
		
		//Recherche
		$this->add_tab('recherche', '', 'recherche_catalogue');
		$this->add_tab('recherche', 'serials', 'recherche_periodique');
		$this->add_tab('recherche', 'last_records', '938');
		$this->add_tab('recherche', 'search_perso', 'search_perso_menu');
		$this->add_tab('recherche', 'search_perso', 'search_perso_expl_menu', '', '&type=EXPL');
		
		//Documents
		if ($deflt_bypass_isbn_page) {
    		$this->add_tab('4057', 'create_form&id=0', '270');
		} else {
    		$this->add_tab('4057', 'create', '270');
		}
		if($opac_avis_allow) {
			$this->add_tab('4057', 'avis', 'menu_gestion_avis', 'records');
		}
		if($opac_allow_add_tag) {
			$this->add_tab('4057', 'tags', 'menu_gestion_tags');
		}
		
		//Périodiques
		$this->add_tab('771', 'serials', 'new_serial', 'serial_form', '&id=0');
		$this->add_tab('771', 'serials', 'pointage_menu_pointage', 'pointage', '&id=0');
		if($pmb_serialcirc_active) {
			$this->add_tab('771', 'serials', 'serialcirc_ask_menu', 'circ_ask');
		}
		if($pmb_collstate_advanced) {
			$this->add_tab('771', 'serials', 'collstate_advanced_add_collstate', 'collstate_form');
		}
		
		//Paniers
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_gestion');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_collecte', 'collecte');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_pointage', 'pointage');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_action', 'action');
		
		//Etageres
		$this->add_tab('etagere_menu', 'etagere', 'etagere_menu_gestion');
		$this->add_tab('etagere_menu', 'etagere', 'etagere_menu_constitution', 'constitution');
		
		//Externe
		if($z3950_accessible) {
			$this->add_tab('externe_menu', 'z3950', 'externe_z3950');
		}
		$this->add_tab('externe_menu', 'search', 'externe_connecteurs', '', '&mode=7&external_type=simple');
		
		//Suggestions
		if($acquisition_active) {
			$this->add_tab('acquisition_menu_sug', 'sug', 'acquisition_sug_do', '', '&action=modif&id_bibli=0');
		}
		
		//Contributions
		if($pmb_contribution_area_activate) {
			$this->add_tab('catalog_menu_contribution', 'contribution_area', 'contribution_area_moderation', '', '&action=list');
		}
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		global $mode;
		
		$active = false;
		switch ($label_code) {
			case 'recherche_catalogue':
				if($this->is_equal_var_get('categ', 'search') && in_array($mode, array(0, 1, 2, 3, 5, 6, 7, 8, 9, 11))) {
					$active = true;
				}
				break;
			case 'recherche_periodique':
				if($this->is_equal_var_get('categ', 'search') && $this->is_equal_var_get('sub')) {
					$active = true;
				}
				break;
			case 'search_perso':
				if($this->is_equal_var_get('categ', 'search_perso') && $this->is_equal_var_get('type')) {
					$active = true;
				}
				break;
			case 'search_perso_expl_menu':
				if($this->is_equal_var_get('categ', 'search_perso') && $this->is_equal_var_get('type', 'EXPL')) {
					$active = true;
				}
				break;
			case '270':
				if($this->is_equal_var_get('categ', 'create') || $this->is_equal_var_get('categ', 'create_form')) {
					$active = true;
				}
				break;
			case 'menu_gestion_avis':
				if($this->is_equal_var_get('categ', 'avis') && $this->is_equal_var_get('sub', array("records", "articles", "sections"))) {
					$active = true;
				}
				break;
			case 'etagere_menu_gestion':
				if ($this->is_equal_var_get('categ', 'etagere') && in_array($_GET['sub'] ?? "", array("", "classementGen"))) {
					$active = true;
				}
				break;
			case 'externe_connecteurs':
				if($this->is_equal_var_get('categ', 'search') && $this->is_equal_var_get('external_type', 'simple') && $mode == 7) {
					$active = true;
				}
				break;
			default:
				$active = parent::is_active_tab($label_code, $categ, $sub);
				break;
		}
		return $active;
	}
}