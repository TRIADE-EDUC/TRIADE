<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_expl_pointage_ui.class.php,v 1.1 2022/01/17 08:19:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_serialcirc_expl_pointage_ui extends list_serialcirc_expl_ui {
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->settings['objects']['default']['display_mode'] = 'table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'table';
	}
}