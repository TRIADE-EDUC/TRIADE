<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_acquisition_ui.class.php,v 1.1.10.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_acquisition_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'ach':
				$title .= $msg['acquisition_ach_ges'];
				break;
			case 'sug':
				$title .= $msg['acquisition_sug_ges'];
				break;
			case 'rent':
				$title .= $msg['acquisition_rent_ges'];
				break;
			default:
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'ach':
				switch (static::$sub) {
					case 'devi':
						$sub_title .= $msg['acquisition_ach_dev'];
						break;
					case 'cmde':
						$sub_title .= $msg['acquisition_ach_cde'];
						break;
					case 'recept':
						$sub_title .= $msg['acquisition_menu_ach_recept'];
						break;
					case 'livr':
						$sub_title .= $msg['acquisition_ach_liv'];
						break;
					case 'fact':
						$sub_title .= $msg['acquisition_ach_fac'];
						break;
					case 'fourn':
						$sub_title .= $msg['acquisition_ach_fou'];
						break;
					case 'bud':
						$sub_title .= $msg['acquisition_menu_ref_budget'];
						break;
					default:
						break;
				}
				break;
			case 'sug':
				break;
			case 'rent':
				$sub_title .= $msg['acquisition_rent_'.static::$sub];
				break;
			default:
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		
	}
}