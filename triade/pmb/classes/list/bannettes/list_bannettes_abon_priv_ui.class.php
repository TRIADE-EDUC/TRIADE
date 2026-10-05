<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_abon_priv_ui.class.php,v 1.1 2021/11/19 14:03:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_bannettes_abon_priv_ui extends list_bannettes_abon_ui {
	
	protected function get_title() {
		global $msg;
		
		return "<h3><span>".$msg['dsi_bannette_gerer_priv']."</span></h3>\n";
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$delete_link = array(
				'onClick' => "delete_bannette_abon"
		);
		$this->add_selection_action('delete', $msg['63'], 'cross.png', $delete_link);
	}
}