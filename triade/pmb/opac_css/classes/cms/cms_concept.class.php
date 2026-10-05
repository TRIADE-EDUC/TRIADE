<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_concept.class.php,v 1.1 2016/04/07 15:35:24 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_concept {

	public static function get_format_data_structure(){
		global $msg;
		$format_datas = array();
		$format_datas[] = array(
			'var' => "id",
			'desc'=> $msg['cms_concept_format_data_id']
		);
		$format_datas[] = array(
			'var' => "uri",
			'desc'=> $msg['cms_concept_format_data_uri']
		);
		$format_datas[] = array(
			'var' => "display_label",
			'desc'=> $msg['cms_concept_format_data_display_label']
		);
		$format_datas[] = array(
			'var' => "schemes",
			'desc'=> $msg['cms_concept_format_data_schemes']
		);
		$format_datas[] = array(
			'var' => "narrowers.concepts",
			'desc'=> $msg['cms_document_format_data_narrowers'],
			'children' => array(
				array(
					'var' => "narrowers.concepts[i].id",
					'desc'=> $msg['cms_document_format_data_narrower_id']
				),
				array(
					'var' => "narrowers.concepts[i].uri",
					'desc'=> $msg['cms_document_format_data_narrower_uri']
				),
				array(
					'var' => "narrowers.concepts[i].display_label",
					'desc'=> $msg['cms_document_format_data_narrower_display_label']
				),
			)
		);
		$format_datas[] = array(
			'var' => "broaders.concepts",
			'desc'=> $msg['cms_document_format_data_broaders'],
			'children' => array(
				array(
					'var' => "broaders.concepts[i].id",
					'desc'=> $msg['cms_document_format_data_broader_id']
				),
				array(
					'var' => "broaders.concepts[i].uri",
					'desc'=> $msg['cms_document_format_data_broader_uri']
				),
				array(
					'var' => "broaders.concepts[i].display_label",
					'desc'=> $msg['cms_document_format_data_broader_display_label']
				),
			)
		);
		return $format_datas;
	}
}