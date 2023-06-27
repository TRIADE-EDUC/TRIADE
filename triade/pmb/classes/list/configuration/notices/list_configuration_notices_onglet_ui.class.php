<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_onglet_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/notices/list_configuration_notices_ui.class.php");

class list_configuration_notices_onglet_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT id_onglet, onglet_name FROM notice_onglet';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'onglet_name',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'onglet_name' => 'admin_noti_onglet_name',
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_onglet;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['admin_noti_onglet_ajout'];
	}
}