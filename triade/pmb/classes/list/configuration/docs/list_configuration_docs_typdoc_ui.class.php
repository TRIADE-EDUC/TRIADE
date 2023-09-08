<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_typdoc_ui.class.php,v 1.2 2019-06-10 15:14:33 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_typdoc_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idtyp_doc, tdoc_libelle, duree_pret, duree_resa, tdoc_owner, tdoc_codage_import, lender_libelle, tarif_pret, short_loan_duration FROM docs_type left join lenders on tdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'tdoc_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		global $pmb_short_loan_management;
		global $pmb_gestion_financiere, $pmb_gestion_tarif_prets;
		
		$main_fields = array();
		$main_fields['tdoc_libelle'] = '103';
		$main_fields['duree_pret'] = '120';
		if ($pmb_short_loan_management) {
			$main_fields['short_loan_duration'] = 'short_loan_duration';
		}
		$main_fields['duree_resa'] = 'duree_resa';
		if ($pmb_gestion_financiere && $pmb_gestion_tarif_prets) {
			$main_fields['tarif_pret'] = 'typ_doc_tarif';
		}
		$main_fields['lender_libelle'] = 'proprio_codage_proprio';
		$main_fields['tdoc_codage_import'] = 'import_codage';
		return $main_fields;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		global $pmb_quotas_avances;
		global $pmb_gestion_tarif_prets;
		
		$content = '';
		switch($property) {
			case 'tdoc_libelle':
				if ($object->tdoc_owner) {
					$content .= "<i>".$object->tdoc_libelle."</i>";
				} else {
					$content .= "<strong>".$object->tdoc_libelle."</strong>";
				}
				break;
			case 'duree_pret':
			case 'duree_resa':
			case 'short_loan_duration':
				$content .= ((!$pmb_quotas_avances)?(htmlentities($object->{$property},ENT_QUOTES,$charset).' '.$msg[121]):($msg['quotas_see_quotas']));
				break;
			case 'tarif_pret':
				$content .= htmlentities((($pmb_gestion_tarif_prets==1)?($object->tarif_pret):($msg['finance_see_finance'])),ENT_QUOTES,$charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idtyp_doc;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['122'];
	}
}