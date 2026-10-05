<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_fichier_ui.class.php,v 1.1 2020/11/23 09:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_fichier_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		//Consulter
		$this->add_tab('fichier_menu_consulter', 'consult', 'fichier_menu_search', '', '&mode=search');
		$this->add_tab('fichier_menu_consulter', 'consult', 'fichier_menu_search_multi', '', '&mode=search_multi');
		
		//Saisie
		$this->add_tab('fichier_menu_saisie', 'saisie', 'fichier_menu_new_fiche');
		
		//Gérer
		$this->add_tab('fichier_menu_gerer', 'gerer', 'fichier_gestion_champs', '', '&mode=champs');
		$this->add_tab('fichier_menu_gerer', 'gerer', 'fichier_gestion_reindex', '', '&mode=reindex');
	}
}