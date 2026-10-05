<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_frbr_ui.class.php,v 1.1 2020/11/23 09:02:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_frbr_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		$this->add_tab('admin_menu_modules', 'cataloging', '93', 'general');
		$this->add_tab('admin_menu_modules', 'cataloging', 'frbr_cataloging_schemes', 'schemes', '&action=select');
	}
}