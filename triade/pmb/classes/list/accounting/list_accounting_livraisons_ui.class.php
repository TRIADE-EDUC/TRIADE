<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_livraisons_ui.class.php,v 1.4 2021/04/19 07:10:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_accounting_livraisons_ui extends list_accounting_ui {
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'numero' => '38',
						'num_acte_parent' => 'acquisition_act_num_cde',
						'num_fournisseur' => 'acquisition_ach_fou2',
						'date_acte' => 'acquisition_fac_date_rec',
						'statut' => 'acquisition_statut'
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('numero');
		$this->add_column('num_acte_parent');
		$this->add_column('num_fournisseur');
		$this->add_column('date_acte');
		$this->add_column('statut');
		$this->add_column_print('livr');
	}
	
	protected function _get_object_property_num_acte_parent($object) {
		$id_cde = liens_actes::getParent($object->id_acte);
		$cde = new actes($id_cde);
		return $cde->numero;
	}
	
	public function get_type_acte() {
		return TYP_ACT_LIV;
	}
	
	public function get_initial_name() {
		return 'liv';
	}
}