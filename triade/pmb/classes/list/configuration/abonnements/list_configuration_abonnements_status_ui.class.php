<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_abonnements_status_ui.class.php,v 1.3 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_abonnements_status_ui extends list_configuration_abonnements_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM abts_status';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('abts_status_gestion_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => 'noti_statut_libelle',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
	
		$content = '';
		switch($property) {
			case 'libelle':
			    $content .= $this->get_cell_img_class_html($object, 'abts_status_class_html')
					.htmlentities($object->abts_status_gestion_libelle, ENT_QUOTES, $charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id='.$object->abts_status_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['115'];
	}
}