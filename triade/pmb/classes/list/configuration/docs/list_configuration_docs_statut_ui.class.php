<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_statut_ui.class.php,v 1.1 2018-10-12 14:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_statut_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM docs_statut left join lenders on statusdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'statut_libelle',
				'asc_desc' => 'asc'
		);
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

	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'statut_libelle':
				if ($object->statusdoc_owner) {
					$content .= "<i>".$object->statut_libelle."</i>";
				} else {
					$content .= "<strong>".$object->statut_libelle."</strong>";
				}
				break;
			case 'pret_flag':
				if($object->pret_flag) {
					$content .= $msg[113];
				} else {
					$content .= $msg[114];
				}
				break;
			case 'statut_allow_resa':
				if($object->statut_allow_resa) {
					$content .= $msg['statut_allow_resa_yes'];
				} else {
					$content .= $msg['statut_allow_resa_no'];
				}
				break;
			case 'transfert_flag':
				if($object->transfert_flag) {
					$content .= $msg['statut_allow_transfert_yes'];
				} else {
					$content .= $msg['statut_allow_transfert_no'];
				}
				break;
			case 'statut_visible_opac':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
	
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_display_header_list() {
		global $msg;
		global $pmb_transferts_actif;
	
		$display = "
		<tr>
			<th colspan=".($pmb_transferts_actif ? "6" : "5").">".$msg["docs_statut_gestion"]."</th>
			<th colspan=2>".$msg["docs_statut_opac"]."</th>
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