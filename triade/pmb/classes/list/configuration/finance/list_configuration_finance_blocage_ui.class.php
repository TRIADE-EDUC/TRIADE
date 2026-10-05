<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_finance_blocage_ui.class.php,v 1.3 2021/03/05 07:33:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_finance_blocage_ui extends list_configuration_finance_ui {
	
	protected function fetch_data() {
		global $pmb_gestion_abonnement, $pmb_gestion_tarif_prets, $pmb_gestion_amende;
		
		$this->objects = array();
		if ($pmb_gestion_abonnement) {
			$this->add_parameter('finance', 'blocage_abt', 'finance_blocage_abt');
		}
		if ($pmb_gestion_tarif_prets) {
			$this->add_parameter('finance', 'blocage_pret', 'finance_blocage_pret');
		}
		if ($pmb_gestion_amende) {
			$this->add_parameter('finance', 'blocage_amende', 'finance_blocage_amende');
		}
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg["finance_blocage_parameters"], ENT_QUOTES, $charset);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'label' => '',
				'blocage_no' => 'finance_blocage_no',
				'blocage_force' => 'finance_blocage_force',
				'blocage_yes' => 'finance_blocage_yes'
		);
	}
	
	protected function init_default_settings() {
		global $action;
		
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'center');
		$this->set_setting_column('label', 'align', 'left');
		//Par sécurité on cite + d'actions
		if(in_array($action, array('modif', 'edit', 'update', 'save'))) {
			$this->set_setting_column('blocage_no', 'display_mode', 'edition');
			$this->set_setting_column('blocage_force', 'display_mode', 'edition');
			$this->set_setting_column('blocage_yes', 'display_mode', 'edition');
			$this->settings['objects']['default']['display_mode'] = 'form_table';
		}
	}
	
	protected function get_display_content_object_list($object, $indice) {
		$this->is_editable_object_list = false;
		return parent::get_display_content_object_list($object, $indice);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'blocage_no':
				if(!$this->get_parameter_value($object->name)) {
					$content .= "X";
				}
				break;
			case 'blocage_force':
				if($this->get_parameter_value($object->name) == 1) {
					$content .= "X";
				}
				break;
			case 'blocage_yes':
				if($this->get_parameter_value($object->name) == 2) {
					$content .= "X";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_cell_edition_content($object, $property) {
		$content = '';
		switch($property) {
			case 'blocage_no':
				$content .= "<input type='radio' name='".$this->objects_type."_".$object->name."' value='0' ".(empty($object->valeur_param) ? "checked='checked'" : "")." />";
				break;
			case 'blocage_force':
				$content .= "<input type='radio' name='".$this->objects_type."_".$object->name."' value='1' ".($object->valeur_param == 1 ? "checked='checked'" : "")." />";
				break;
			case 'blocage_yes':
				$content .= "<input type='radio' name='".$this->objects_type."_".$object->name."' value='2' ".($object->valeur_param == 2 ? "checked='checked'" : "")." />";
				break;
			default :
				break;
		}
		return $content;
	}
	
	protected function save_object_property($object, $property) {
		switch ($property) {
			case 'blocage_no':
			case 'blocage_force':
			case 'blocage_yes':
				$this->save_parameter($object, 'valeur_param');
				break;
		}
	}
	
	protected function get_button_add() {
		return '';
	}
}