<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_orinot_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/notices/list_configuration_notices_ui.class.php");

class list_configuration_notices_orinot_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		return 'SELECT orinot_id, orinot_nom, orinot_pays, orinot_diffusion FROM origine_notice';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'orinot_nom',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'orinot_nom' => 'orinot_nom',
				'orinot_pays' => 'orinot_pays',
				'orinot_diffusion' => 'orinot_diffusable',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'orinot_diffusion':
				if ($object->orinot_diffusion) {
					$content .= $msg['orinot_diffusable_oui'];
				} else {
					$content .= $msg['orinot_diffusable_non'];
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->orinot_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['orinot_ajout'];
	}
}