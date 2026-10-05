<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_demandes_ui.class.php,v 1.2 2021/08/06 11:50:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_demandes_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $faq_active;
		
		//Listes
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_all', '', '&idetat=0');
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_a_valide', '', '&idetat=1');
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_en_cours', '', '&idetat=2&iduser='.SESSuserid);
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_refuse', '', '&idetat=3&iduser='.SESSuserid);
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_fini', '', '&idetat=4&iduser='.SESSuserid);
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_abandon', '', '&idetat=5&iduser='.SESSuserid);
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_archive', '', '&idetat=6&iduser='.SESSuserid);
		$this->add_tab('demandes_menu_liste', 'list', 'demandes_menu_not_assigned', '', '&idetat=0&iduser=-1');
		
		//Actions
		$this->add_tab('demandes_menu_action', 'action', 'demandes_menu_comm', 'com');
		$this->add_tab('demandes_menu_action', 'action', 'demandes_menu_rdv_planning', 'rdv_plan');
		$this->add_tab('demandes_menu_action', 'action', 'demandes_menu_rdv_a_valide', 'rdv_val');
		
		//FAQ
		if($faq_active) {
			$this->add_tab('demandes_menu_faq', 'faq', 'demandes_menu_faq', 'question');
		}
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		
		$active = false;
		switch ($label_code) {
			case 'demandes_menu_all':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '0'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_a_valide':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '1'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_en_cours':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '2'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_refuse':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '3'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_fini':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '4'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_abandon':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '5'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_archive':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('idetat', '6'))) {
					$active = true;
				}
				break;
			case 'demandes_menu_not_assigned':
				if(($this->is_equal_var_get('categ', 'list') && $this->is_equal_var_get('iduser', '-1'))) {
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