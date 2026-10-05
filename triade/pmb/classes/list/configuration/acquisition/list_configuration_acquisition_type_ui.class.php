<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_acquisition_type_ui.class.php,v 1.2 2021/04/19 07:10:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_acquisition_type_ui extends list_configuration_acquisition_ui {
	
	protected $tab_tva;
	
	protected function _get_query_base() {
		return 'SELECT * FROM types_produits';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('libelle');
	}
	
	protected function init_default_settings() {
		global $acquisition_gestion_tva;
		
		parent::init_default_settings();
		$this->set_setting_column('libelle', 'text', array('italic' => true));
		$this->set_setting_column('taux_tva', 'text', array('italic' => true));
		if($acquisition_gestion_tva) {
			$this->set_setting_column('num_tva_achat', 'text', array('italic' => true));
		}
	}
	
	protected function get_main_fields_from_sub() {
		global $acquisition_gestion_tva;
		
		$main_fields = array(
				'libelle' => '103',
				'num_cp_compta' => 'acquisition_num_cp_compta',
		);
		if($acquisition_gestion_tva) {
			$main_fields['num_tva_achat'] = 'acquisition_num_tva_achat';
		}
		return $main_fields;
	}
	
	protected function get_tab_tva() {
		global $acquisition_gestion_tva;
		if ($acquisition_gestion_tva) {
			if(!isset($this->tab_tva)) {
				$q2 = tva_achats::listTva();
				$r2 = pmb_mysql_query($q2);
				while($row=pmb_mysql_fetch_object($r2)) {
					$this->tab_tva[$row->id_tva]=$row->libelle;
				}
			}
			return $this->tab_tva;
		}
	}
	
	protected function _get_object_property_num_tva_achat($object) {
		return $this->get_tab_tva()[$object->num_tva_achat];
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_produit;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['acquisition_ajout_type'];
	}
}