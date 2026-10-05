<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_frais_ui.class.php,v 1.2 2021/01/18 13:08:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_frais_ui extends list_configuration_acquisition_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM frais';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
		$this->set_setting_column('montant', 'text', array('italic' => true));
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'libelle' => '103',
				'montant' => 'acquisition_frais_montant',
				'add_to_new_order' => 'acquisition_frais_add_to_new_order',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		global $pmb_gestion_devise;
		
		$content = '';
		switch($property) {
			case 'montant':
				$content .= $object->montant." ".$pmb_gestion_devise;
				break;
			case 'add_to_new_order':
				if($object->add_to_new_order) {
					$flag ="enabled";
				} else {
					$flag ="disabled";
				}
				$content .= "<input type='checkbox' class='switch' disabled='disabled' ".($flag == 'enabled' ? "checked='checked'" : "")." />";
				$content .= "<label>".htmlentities($msg['acquisition_frais_add_to_new_order_'.$flag], ENT_QUOTES, $charset)."</label>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_frais;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_frais'];
	}
}