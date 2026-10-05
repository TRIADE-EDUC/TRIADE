<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_authority.class.php,v 1.4 2016/05/19 13:23:48 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_authority {

	public static function get_format_data_structure(){
		global $msg;
		$format_datas = array();
		$format_datas[] = array(
			'var' => "id",
			'desc'=> $msg['cms_authority_format_data_id']
		);
		$format_datas[] = array(
			'var' => "db_id",
			'desc'=> $msg['cms_authority_format_data_db_id']
		);
		$format_datas[] = array(
			'var' => "isbd",
			'desc'=> $msg['cms_authority_format_data_isbd']
		);
		$format_datas[] = array(
			'var' => "permalink",
			'desc'=> $msg['cms_authority_format_data_permalink']
		);
		$format_datas[] = array(
			'var' => "comment",
			'desc'=> $msg['cms_authority_format_data_comment']
		);
		$format_datas[] = array(
			'var' => "p_perso",
			'desc'=> $msg['cms_authority_format_data_p_perso']
		);
		$format_datas[] = array(
			'var' => "statut_class_html",
			'desc'=> $msg['cms_authority_format_data_statut_class_html']
		);
		$format_datas[] = array(
			'var' => "statut_label",
			'desc'=> $msg['cms_authority_format_data_statut_label']
		);
		$format_datas[] = array(
			'var' => "type_label",
			'desc'=> $msg['cms_authority_format_data_type_label']
		);
		
		$authority_types  = array();
		// Auteurs
		$authority_types[] =  array(
				'var' => $msg['133'],
				'desc' => $msg['133'],
				'children' => array(
						
				)
		);
		// Catégories
		$authority_types[] =  array(
				'var' => $msg['134'],
				'desc' => $msg['134'],
				'children' => array(
						
				)
		);
		// Editeurs
		$authority_types[] =  array(
				'var' => $msg['135'],
				'desc' => $msg['135'],
				'children' => array(
						
				)
		);
		// Collections
		$authority_types[] =  array(
				'var' => $msg['136'],
				'desc' => $msg['136'],
				'children' => array(
						
				)
		);
		// Sous-collections
		$authority_types[] =  array(
				'var' => $msg['137'],
				'desc' => $msg['137'],
				'children' => array(
						
				)
		);
		// Séries
		$authority_types[] =  array(
				'var' => $msg['333'],
				'desc' => $msg['333'],
				'children' => array(
						
				)
		);
		// Titres uniformes
		$authority_types[] =  array(
				'var' => $msg['aut_menu_titre_uniforme'],
				'desc' => $msg['aut_menu_titre_uniforme'],
				'children' => array(
						array(
								'var' => 'oeuvre_type_name',
								'desc' => $msg['search_extended_titre_uniforme_oeuvre_type']
						),
						array(
								'var' => 'oeuvre_nature_name',
								'desc' => $msg['search_extended_titre_uniforme_oeuvre_nature']
						),
						array(
								'var' => 'name',
								'desc' => $msg['search_extended_titre_uniforme_name']
						),
						array(
								'var' => 'oeuvre_expressions_datas',
								'desc' => $msg['cms_authority_format_data_authority_uniform_title_children_expressions']
						),
						array(
								'var' => 'oeuvre_parent_expressions_datas',
								'desc' => $msg['search_extended_titre_uniforme_expression']
						),
						array(
								'var' => 'other_links_datas',
								'desc' => $msg['search_extended_titre_uniforme_others_link']
						),
						array(
								'var' => 'sorted_responsabilities',
								'desc' => $msg['search_extended_titre_uniforme_author']
						),
						array(
								'var' => 'form',
								'desc' => $msg['search_extended_titre_uniforme_forme']
						),
						array(
								'var' => 'form_marclist',
								'desc' => $msg['search_extended_titre_uniforme_forme_marclist']
						),
						array(
								'var' => 'date_date',
								'desc' => $msg['search_extended_titre_uniforme_date']
						),
						array(
								'var' => 'place',
								'desc' => $msg['search_extended_titre_uniforme_lieu']
						),
						array(
								'var' => 'subject',
								'desc' => $msg['search_extended_titre_uniforme_sujet']
						),
						array(
								'var' => 'intended_termination',
								'desc' => $msg['search_extended_titre_uniforme_completude']
						),
						array(
								'var' => 'intended_audience',
								'desc' => $msg['search_extended_titre_uniforme_public']
						),
						array(
								'var' => 'history',
								'desc' => $msg['search_extended_titre_uniforme_histoire']
						),
						array(
								'var' => 'context',
								'desc' => $msg['search_extended_titre_uniforme_contexte']
						),
						array(
								'var' => 'tonalite',
								'desc' => $msg['search_extended_titre_uniforme_tonalite']
						),
						array(
								'var' => 'tonalite_marclist',
								'desc' => $msg['search_extended_titre_uniforme_tonalite_marclist']
						),
						array(
								'var' => 'coordinates',
								'desc' => $msg['search_extended_titre_uniforme_coords']
						),
						array(
								'var' => 'equinox',
								'desc' => $msg['search_extended_titre_uniforme_equinoxe']
						),
						array(
								'var' => 'characteristic',
								'desc' => $msg['search_extended_titre_uniforme_caracteristiques']
						)
				)
		);
		// Index. décimales
		$authority_types[] =  array(
				'var' => $msg['indexint_menu'],
				'desc' => $msg['indexint_menu'],
				'children' => array(
						
				)
		);
		// Concepts
		$authority_types[] =  array(
				'var' => $msg['ontology_skos_menu'],
				'desc' => $msg['ontology_skos_menu'],
				'children' => array(
						
				)
		);
		
		$format_datas[] = array(
			'var' => $msg['cms_authority_format_data_authority_types'],
			'desc'=> $msg['cms_authority_format_data_authority_types'],
			'children' => $authority_types
		);
		return $format_datas;
	}
}