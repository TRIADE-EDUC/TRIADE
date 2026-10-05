<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_statut_ui.class.php,v 1.6 2023/12/22 13:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_docs_statut_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM docs_statut left join lenders on statusdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('statut_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		global $pmb_transferts_actif;
		
		$main_fields = array(
				'statut_libelle' => '103',
				'pret_flag' => '',
				'statut_allow_resa' => '',
		); 
		if($pmb_transferts_actif) {
			$main_fields['transfert_flag']= '';
		}
		$main_fields['lender_libelle']= 'proprio_codage_proprio';
		$main_fields['statusdoc_codage_import']= 'import_codage';
		$main_fields['statut_libelle_opac']= '103';
		$main_fields['statut_visible_opac']= 'docs_statut_visu_opac';
		return $main_fields;
	}

	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('statut_visible_opac', 'align', 'center');
		$this->set_setting_column('statut_visible_opac', 'datatype', 'boolean');
	}
	
	protected function _get_object_property_pret_flag($object) {
		global $msg;
		
		if($object->pret_flag) {
			return $msg[113];
		} else {
			return $msg[114];
		}
	}
	
	protected function _get_object_property_statut_allow_resa($object) {
		global $msg;
		
		if($object->statut_allow_resa) {
			return $msg['statut_allow_resa_yes'];
		} else {
			return $msg['statut_allow_resa_no'];
		}
	}
	
	protected function _get_object_property_transfert_flag($object) {
		global $msg;
		
		if($object->transfert_flag) {
			return $msg['statut_allow_transfert_yes'];
		} else {
			return $msg['statut_allow_transfert_no'];
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'statut_libelle':
				if ($object->statusdoc_owner) {
					return array(
							'style' => 'font-style:italic;'
					);
				} else {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return parent::get_default_attributes_format_cell($object, $property);
	}
	
	public function get_display_header_list() {
		global $msg;
		global $pmb_transferts_actif;
	
		$display = "
		<tr>
			<th colspan='".($pmb_transferts_actif ? "6" : "5")."' scope='colgroup'>".$msg["docs_statut_gestion"]."</th>
			<th colspan='2' scope='colgroup'>".$msg["docs_statut_opac"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idstatut;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['115'];
	}
}