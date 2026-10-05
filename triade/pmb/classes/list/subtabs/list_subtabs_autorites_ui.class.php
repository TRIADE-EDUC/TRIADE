<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_autorites_ui.class.php,v 1.4.8.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_autorites_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'auteurs':
			case 'categories':
			case 'editeurs':
			case 'collections':
			case 'souscollections':
			case 'series':
			case 'titres_uniformes':
			case 'indexint':
			case 'authperso':
				$title .= $msg['140'];
				break;
			case 'concepts':
				
				break;
			case 'caddie':
				$title .= $msg['caddie_menu'];
				break;
			case 'semantique':
				$title .= $msg['semantique'];
				break;
			case 'import':
				$title .= $msg['140'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'auteurs':
				$sub_title .= $msg['133'];
				break;
			case 'categories':
				$sub_title .= $msg['134'];
				break;
			case 'editeurs':
				$sub_title .= $msg['135'];
				break;
			case 'collections':
				$sub_title .= $msg['136'];
				break;
			case 'souscollections':
				$sub_title .= $msg['137'];
				break;
			case 'series':
				$sub_title .= $msg['333'];
				break;
			case 'titres_uniformes':
				$sub_title .= $msg['aut_menu_titre_uniforme'];
				break;
			case 'indexint':
				$sub_title .= $msg['indexint_menu_title'];
				break;
			case 'authperso':
				global $id_authperso, $id;
				$authperso = new authperso($id_authperso, $id);
				$sub_title .= $authperso->info['name'];
				break;
			case 'concepts':
				
				break;
			case 'caddie':
				if(empty(static::$sub)) static::$sub = 'gestion';
				$sub_title .= $msg["caddie_menu_".static::$sub];
				$selected_subtab = $this->get_selected_subtab();
				if(!empty($selected_subtab)) {
					$sub_title .= " > ".$selected_subtab->get_label();
				}
				break;
			case 'semantique':
				switch (static::$sub) {
					case 'synonyms':
						$sub_title .= $msg['dico_syn'];
						break;
					case 'empty_words':
						$sub_title .= $msg['dico_empty_words'];
						break;
				}
				break;
			case 'import':
				$sub_title .= $msg['authorities_import'];
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		switch (static::$categ) {
			case 'caddie':
				if(empty(static::$sub)) static::$sub = 'gestion';
				switch (static::$sub) {
					case 'gestion':
						$this->add_subtab(static::$sub, 'caddie_menu_gestion_panier', '', '&quoi=panier');
						$this->add_subtab(static::$sub, 'caddie_menu_gestion_procs', '', '&quoi=procs');
						$this->add_subtab(static::$sub, 'classementGen_list_libelle', '', '&quoi=classementGen');
						break;
					case 'collecte':
						$this->add_subtab(static::$sub, 'caddie_menu_collecte_selection', '', '&moyen=selection');
						break;
					case 'pointage':
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_selection', '', '&moyen=selection');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_panier', '', '&moyen=panier');
						$this->add_subtab(static::$sub, 'caddie_menu_pointage_raz', '', '&moyen=raz');
						break;
					case 'action':
						$this->add_subtab(static::$sub, 'caddie_menu_action_suppr_panier', '', '&quelle=supprpanier');
						$this->add_subtab(static::$sub, 'caddie_menu_action_edition', '', '&quelle=edition');
						$this->add_subtab(static::$sub, 'caddie_menu_action_selection', '', '&quelle=selection');
						$this->add_subtab(static::$sub, 'caddie_menu_action_suppr_base', '', '&quelle=supprbase');
						$this->add_subtab(static::$sub, 'caddie_menu_action_reindex', '', '&quelle=reindex');
						break;
				}
				break;
		}
	}
}