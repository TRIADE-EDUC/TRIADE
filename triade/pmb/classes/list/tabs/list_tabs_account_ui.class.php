<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_account_ui.class.php,v 1.5 2024/01/31 13:06:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_account_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $PMBuseremail, $PMBuserid;
		
		$this->add_tab('33', '', '933');
		if($PMBuseremail) {
			$this->add_tab('33', 'mails', 'mail_configuration', 'configuration', '&action=edit&name='.$PMBuseremail);
		}
		$this->add_tab('33', 'authentication', 'authentication', '', '&action=edit&id=' . $PMBuserid);
		$this->add_tab('33', 'facettes', 'gestion_facette');
	}
}