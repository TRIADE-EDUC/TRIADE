<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_frbr_ui.class.php,v 1.1.10.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_frbr_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'cataloging':
				switch (static::$sub) {
					case 'schemes':
						$title .= $msg['frbr_cataloging_schemes'];
						break;
					case 'general':
					default:
						$title .= $msg['frbr_cataloging_title'];
						break;
				}
				break;
			default:
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		switch (static::$categ) {
			default:
				return '';
		}
	}
	
	protected function _init_subtabs() {
		switch (static::$categ) {
			default:
				return '';
		}
	}
}