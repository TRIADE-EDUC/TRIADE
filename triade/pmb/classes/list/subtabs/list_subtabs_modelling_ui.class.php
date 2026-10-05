<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_modelling_ui.class.php,v 1.4 2022/09/13 12:30:01 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_modelling_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'ontologies':
				$title .= $msg["admin_ontologies"];
				break;
			case 'frbr':
				$title .= $msg["frbr"];
				break;
			case 'contribution_area':
				$title .= $msg["admin_menu_contribution_area"];
				break;
			default:
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		$sub_title = '';
		switch (static::$categ) {
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		switch (static::$categ) {
			case 'frbr':
				//FRBR
				$this->add_subtab('cataloging_schemes', 'frbr_cataloging_schemes');
				break;
			case 'contribution_area':
				//Espace de contribution
				$this->add_subtab('area', 'admin_contribution_area');
				$this->add_subtab('form', 'admin_contribution_area_form');
				$this->add_subtab('status', 'admin_contribution_area_status');
				$this->add_subtab('equation', 'admin_contribution_area_equation');
				$this->add_subtab('param', 'admin_contribution_area_param');
				break;
			default:
				break;
		}
	}
}