<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_section_ui.class.php,v 1.1 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/docs/list_configuration_docs_ui.class.php");

class list_configuration_docs_section_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idsection, section_libelle, sdoc_codage_import, sdoc_owner, lender_libelle, section_visible_opac FROM docs_section left join lenders on sdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'section_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'section_libelle' => '103',
				'section_visible_opac' => 'opac_object_visible_short',
				'section_visible_loc' => 'section_visible_loc',
				'lender_libelle' => 'proprio_codage_proprio',
				'sdoc_codage_import' => 'import_codage',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'section_libelle':
				if ($object->sdoc_owner) {
					$content .= "<i>".$object->section_libelle."</i>";
				} else {
					$content .= "<strong>".$object->section_libelle."</strong>";
				}
				break;
			case 'section_visible_opac':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			case 'section_visible_loc':
				$rqtloc = "select location_libelle from docsloc_section, docs_location where num_section='".$object->idsection."' and idlocation=num_location order by location_libelle " ;
				$resloc = pmb_mysql_query($rqtloc);
				$localisations=array();
				while ($loc=pmb_mysql_fetch_object($resloc)) $localisations[]=$loc->location_libelle ;
				$content .= implode("<br />",$localisations) ;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idsection;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['110'];
	}
}