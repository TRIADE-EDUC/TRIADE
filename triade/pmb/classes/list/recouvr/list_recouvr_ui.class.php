<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_recouvr_ui.class.php,v 1.5.8.1 2025/05/30 08:11:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/comptes.class.php");

class list_recouvr_ui extends list_ui {
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'id_empr' => 'editions_datasource_id_empr',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'id_empr' => 0,
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields'] = array(
				'date_rec' => 'relance_recouvrement_date',
				'type' => 'relance_recouvrement_type',
				'titre' => 'relance_recouvrement_titre',
				'expl_cb' => 'relance_recouvrement_cb',
				'expl_cote' => 'relance_recouvrement_cote',
				'expl_codestat_libelle' => 'relance_recouvrement_expl_codestat',
				'expl_location_libelle' => 'relance_recouvrement_expl_location',
				'expl_section_libelle' => 'relance_recouvrement_expl_section',
				'expl_statut_libelle' => 'relance_recouvrement_expl_statut',
				'expl_tdoc_libelle' => 'relance_recouvrement_expl_type',
				'expl_lender_libelle' => 'relance_recouvrement_expl_lender',
				'date_pret' => 'relance_recouvrement_pret_date',
				'date_relance1' => 'relance_recouvrement_relance_date1',
				'date_relance2' => 'relance_recouvrement_relance_date2',
				'date_relance3' => 'relance_recouvrement_relance_date3',
				'prix_calcul' => 'relance_recouvrement_prix_calcul',
				'montant' => 'relance_recouvrement_montant',
		);
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date_rec');
		$this->add_applied_sort('id');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('date_pret', 'align', 'center');
		$this->set_setting_column('date_relance1', 'align', 'center');
		$this->set_setting_column('date_relance2', 'align', 'center');
		$this->set_setting_column('date_relance3', 'align', 'center');
		$this->set_setting_column('prix_calcul', 'align', 'center');
		$this->set_setting_column('montant', 'align', 'right');
		$this->set_setting_column('date_rec', 'datatype', 'date');
		$this->set_setting_column('date_pret', 'datatype', 'date');
		$this->set_setting_column('date_relance1', 'datatype', 'date');
		$this->set_setting_column('date_relance2', 'datatype', 'date');
		$this->set_setting_column('date_relance3', 'datatype', 'date');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('id_empr', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function _get_object_property_type($object) {
		global $msg;
		if(!$object->recouvr_type) {
			return $msg["relance_recouvrement_amende"];
		} elseif ($object->id_expl) {
			return $msg["relance_recouvrement_prix"];
		}
	}
	
	protected function _get_object_property_prix_calcul($object) {
		if(!$object->recouvr_type) {
			return '';
		} elseif ($object->id_expl) {
			$comment_prix='';
			$query = "select expl_prix, prix from exemplaires, notices where (notice_id=expl_notice or notice_id=expl_bulletin) and expl_id =".$object->id_expl;
			$result = pmb_mysql_query($query);
			if($row = pmb_mysql_fetch_object($result)) {
				if(!$comment_prix=$row->expl_prix) {
					$comment_prix=$row->prix;
					return $comment_prix;
				}
			}
		}
		return '';
	}
	
	protected function _get_object_property_expl_codestat_libelle($object) {
		$docs_codestat = new docs_codestat($object->expl_codestat);
		return $docs_codestat->libelle;
	}
	
	protected function _get_object_property_expl_location_libelle($object) {
		$docs_location = new docs_location($object->expl_location);
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_expl_section_libelle($object) {
		$docs_section = new docs_section($object->expl_section);
		return $docs_section->libelle;
	}
	
	protected function _get_object_property_expl_statut_libelle($object) {
		$docs_statut = new docs_statut($object->expl_statut);
		return $docs_statut->libelle;
	}
	
	protected function _get_object_property_expl_tdoc_libelle($object) {
		$docs_type = new docs_type($object->expl_typdoc);
		return $docs_type->libelle;
	}
	
	protected function _get_object_property_expl_lender_libelle($object) {
		$lender = new lender($object->expl_owner);
		return $lender->lender_libelle;
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		$content = '';
		switch($property) {
		    case 'titre':
			    if ($object->id_expl) {
			        $notice = null;
				    if ($object->expl_notice) {
				        $notice = new mono_display($object->expl_notice);
				    } elseif ($object->expl_bulletin) {
						$req="select bulletin_notice from bulletins where bulletin_id=$object->expl_bulletin";
						$res=pmb_mysql_query($req);
						$id_bull_notice=pmb_mysql_result($res,0,0);
						$notice = new serial_display($id_bull_notice);
					}
					if ($notice !== null) {
					   $content .= strip_tags(html_entity_decode($notice->header,ENT_QUOTES,$charset));
					}
				} else {
					$content .= $object->libelle;
				}
				break;
			case 'expl_cb':
				$content .= "<a href='./circ.php?categ=visu_ex&form_cb_expl=".$object->expl_cb."'>".$object->expl_cb."</a>";
				break;
			case 'montant':
				$content .= "<span dynamics='circ,recouvr_prix' dynamics_params='text' id='prix_".$object->recouvr_id."'>".comptes::format_simple($object->montant)."</span>";
				break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('id_empr', 'id_empr', 'integer');
	}
}