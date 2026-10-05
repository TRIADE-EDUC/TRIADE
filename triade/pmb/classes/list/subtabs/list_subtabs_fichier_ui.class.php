<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_fichier_ui.class.php,v 1.2 2021/02/12 22:36:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_fichier_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg, $mode;
		
		$title = "";
		switch (static::$categ) {
			case 'gerer':
				if ($mode == 'display') {
					$title .= $msg['fichier_gestion_affichage'];
				}
				break;
			case 'panier':
				$title .= $msg['fichier_menu_paniers'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg, $mode;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'panier':
				switch ($mode) {
					case 'collect':
						$sub_title .= $msg['fichier_menu_panier_collecter']." > ";
						break;
					case 'pointer':
						$sub_title .= $msg['fichier_menu_panier_pointer']." > ";
						break;
					case 'action':
						$sub_title .= $msg['fichier_menu_panier_action']." > ";
						break;
					default:
						break;
				}
				$sub_title .= parent::get_sub_title();
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		global $mode;
		
		switch (static::$categ) {
			case 'gerer':
				switch($mode){
					case 'display':
						$this->add_subtab('position', 'fichier_display_position', '', '&mode='.$mode);
						$this->add_subtab('list', 'fichier_display_result_list', '', '&mode='.$mode);
						break;
				}
				break;
			case 'panier':
				switch($mode){
					case 'collect':
						$this->add_subtab('proc', 'fichier_pointer_procedures', '', '&mode='.$mode);
						break;
					case 'pointer':
						$this->add_subtab('proc', 'fichier_pointer_procedures', '', '&mode='.$mode);
						break;
					case 'action':
						$this->add_subtab('proc', 'fichier_pointer_procedures', '', '&mode='.$mode);
						$this->add_subtab('mail', 'fichier_action_mail', '', '&mode='.$mode);
						$this->add_subtab('sms', 'fichier_action_sms', '', '&mode='.$mode);
						$this->add_subtab('edit', 'fichier_action_edit', '', '&mode='.$mode);
						break;
				}
				break;
		}
	}
}