<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_serialcirc_ask_reader_ui.class.php,v 1.1 2023/12/21 10:34:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_serialcirc_ask_reader_ui extends list_opac_serialcirc_ask_ui {
	
	protected function init_default_columns() {
	    $this->add_column('type');
	    $this->add_column('serial');
	    $this->add_column('date');
	    $this->add_column('status');
	    $this->add_column('comment');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('query', 'human', false);
	    $this->set_setting_display('pager', 'visible', false);
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
}