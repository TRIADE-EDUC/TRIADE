<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_aut_link.class.php,v 1.2.2.1 2024/10/04 06:45:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class indexation_aut_link {
	
	protected $type = 'entity';
	
	protected $entity_type;
	
	protected $entity_link_type;
	
	public function __construct($entity_type=0) {
		$this->entity_type = intval($entity_type);
	}
	
	public static function get_tablefield($name, $id, $field, $pond=100, $marctype='') {
		$tablefield = array(
				'NAME' => $name,
				'ID' => $id,
				'value' => $field,
				'POND' => $pond
		);
		if($marctype) {
			$tablefield['MARCTYPE'] = $marctype;
		}
		return $tablefield;
	}
	
	public static function get_tablefield_isbd($name, $id, $class_name, $pond=0) {
		return array(
				'NAME' => $name,
				'ID' => $id,
				'POND' => $pond,
				'CLASS_NAME' => $class_name,
		);
	}
	
	public static function get_tablekey_entity($table) {
		switch ($table) {
			case 'authors':
				return 'author_id';
			case 'categories':
				return 'num_noeud';
			case 'publishers':
				return 'ed_id';
			case 'collections':
				return 'collection_id';
			case 'sub_collections':
				return 'sub_coll_id';
			case 'series':
				return 'serie_id';
			case 'titres_uniformes':
				return 'tu_id';
			case 'indexint':
				return 'indexint_id';
			case 'authperso_authorities':
			    return 'id_authperso_authority';
		}
	}
	
	public static function get_tablefields_entity($table) {
		switch ($table) {
			case 'authors':
				return array(
						static::get_tablefield('201', '01', 'author_name', '110'),
						static::get_tablefield('202', '02', 'author_rejete', '110'),
						static::get_tablefield('713', '03', 'author_date'),
						static::get_tablefield('147', '04', 'author_web'),
						static::get_tablefield('707', '05', 'author_comment'),
						static::get_tablefield('', '07', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'categories':
				return array(
						static::get_tablefield('lib_categ', '01', 'libelle_categorie', '110'),
						static::get_tablefield('', '03', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'publishers':
				return array(
						static::get_tablefield('editeur_nom', '01', 'ed_name'),
						static::get_tablefield('editeur_adr1', '02', 'ed_adr1'),
						static::get_tablefield('editeur_adr2', '03', 'ed_adr2'),
						static::get_tablefield('editeur_cp', '04', 'ed_cp'),
						static::get_tablefield('editeur_ville', '05', 'ed_ville'),
						static::get_tablefield('146', '06', 'ed_pays'),
						static::get_tablefield('editeur_web', '07', 'ed_web'),
						static::get_tablefield('ed_comment', '08', 'ed_comment'),
						static::get_tablefield('', '10', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'collections':
				return array(
						static::get_tablefield('lib_coll', '01', 'collection_name'),
						static::get_tablefield('issn_coll', '03', 'collection_issn'),
						static::get_tablefield('', '04', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'sub_collections':
				return array(
						static::get_tablefield('intit_sub_col', '01', 'sub_coll_name', '75'),
						static::get_tablefield('intit_sub_col_issn', '03', 'sub_coll_issn', '75'),
						static::get_tablefield('', '04', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'series':
				return array(
						static::get_tablefield('lib_serie', '01', 'serie_name'),
						static::get_tablefield('', '03', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'titres_uniformes':
				return array(
						static::get_tablefield('aut_titre_uniforme_form_nom', '01', 'tu_name'),
						static::get_tablefield('aut_titre_uniforme_form_tonalite', '02', 'tu_tonalite'),
						static::get_tablefield('aut_titre_uniforme_commentaire', '03', 'tu_comment'),
						static::get_tablefield('aut_oeuvre_form_forme', '09', 'tu_forme'),
						static::get_tablefield('aut_oeuvre_form_date', '10', 'tu_date'),
						static::get_tablefield('aut_oeuvre_form_sujet', '11', 'tu_sujet'),
						static::get_tablefield('aut_oeuvre_form_lieu', '12', 'tu_lieu'),
						static::get_tablefield('aut_oeuvre_form_histoire', '13', 'tu_histoire'),
						static::get_tablefield('aut_oeuvre_form_caracteristique', '14', 'tu_caracteristique'),
						static::get_tablefield('aut_oeuvre_form_public', '15', 'tu_public'),
						static::get_tablefield('aut_oeuvre_form_contexte', '16', 'tu_contexte'),
						static::get_tablefield('aut_oeuvre_form_coordonnees', '17', 'tu_coordonnees'),
						static::get_tablefield('aut_oeuvre_form_equinoxe', '18', 'tu_equinoxe'),
						static::get_tablefield('', '19', 'aut_link.aut_link_type', '100', 'aut_link')
				);
			case 'indexint':
			    return array(
			    		static::get_tablefield('indexint_nom', '01', 'indexint_name'),
			    		static::get_tablefield('indexint_comment', '02', 'indexint_comment'),
			    		static::get_tablefield('', '04', 'aut_link.aut_link_type', '100', 'aut_link')
			    );
			case 'authperso_authorities':
			    return array(
        			    static::get_tablefield('admin_menu_authperso', '01', 'authperso_index_infos_global', '110'),
			    );
		}
	}
	
	public static function get_tablefield_isbd_entity($table) {
		switch ($table) {
			case 'authors':
				return array(
						static::get_tablefield_isbd('isbd', '06', 'author')
				);
			case 'categories':
				return array(
						static::get_tablefield_isbd('isbd', '02', 'categories')
				);
			case 'publishers':
				return array(
						static::get_tablefield_isbd('isbd', '09', 'editeur')
				);
			case 'collections':
				return array(
						static::get_tablefield_isbd('isbd', '02', 'collection')
				);
			case 'sub_collections':
				return array(
						static::get_tablefield_isbd('isbd', '02', 'subcollection')
				);
			case 'series':
				return array(
						static::get_tablefield_isbd('isbd', '02', 'serie')
				);
			case 'titres_uniformes':
				return array(
						static::get_tablefield_isbd('isbd', '08', 'titre_uniforme')
				);
			case 'indexint':
				return array(
						static::get_tablefield_isbd('isbd', '03', 'indexint')
				);
			case 'authperso_authorities':
			    return array(
                        static::get_tablefield_isbd('isbd', '02', 'authperso')
			    );
		}
	}
	
	public static function get_language($alias, $field) {
	    $language = array(
	        'ALIAS' => $alias,
	        'value' => $field,
	    );
	    return $language;
	}
	
	public static function get_language_entity($table) {
	    switch ($table) {
	        case 'categories':
	            return array(
	                   static::get_language('lang', 'categories.langue')
	            );
	        default:
	            return array();
	    }
	}
	            
	protected function get_table($table, $referencefield, $externalfield) {
		$tablekey_entity = static::get_tablekey_entity($table);
		$linked_authority_type = authority::get_const_type_object($table);
		$structure = array(
				'NAME' => $table,
				'TABLEFIELD' => static::get_tablefields_entity($table),
                'LANGUAGE' => static::get_language_entity($table),
				'TABLEKEY' => array(
						array('value' => $tablekey_entity)
				),
				'IDKEY' => array(
						array('value' => $tablekey_entity)
				),
				'LINK' => array(
						array(
								'TYPE' => 'nn',
								'TABLE' => array(
										array('value' => 'aut_link')
								),
								'REFERENCEFIELD' => array(
										array('value' => $referencefield)
								),
								'EXTERNALFIELD' => array(
										array('value' => $externalfield)
								),
								'LINKRESTRICT' => array(
										array('value' => '')
								),
						)
				)
		);
		if ($this->entity_type == $linked_authority_type || ($this->entity_type > 1000 && $linked_authority_type == AUT_TABLE_AUTHPERSO)) {
		    // Aucun alias lors d'un lien categorie vs categorie
		    if ($table != 'categories') {
		        $structure['ALIAS'] = $table.'_link';
		        $filter_from = $table.'_link.'.$tablekey_entity;
		    } else {
		        $filter_from = 'noeuds.id_noeud';
		    }
		    if($this->type == 'entities') {
		        $structure['FILTER'] = array(
		            array('value' => $filter_from.' != '.$table.'.'.$tablekey_entity)
		            
		        );
		    } else {
		        $structure['FILTER'] = array(
		            array('value' => $filter_from.' != !!object_id!!')
		            
		        );
		    }
		}
		return $structure;
	}
	
	public function get_tables($table) {
		$linked_authority_type = authority::get_const_type_object($table);
		return array(
				$this->get_table($table, 'aut_link_from_num and aut_link_from = '.$this->entity_type.' and aut_link_to = '.$linked_authority_type, 'aut_link_to_num'),
				$this->get_table($table, 'aut_link_to_num and aut_link_to = '.$this->entity_type.' and aut_link_from = '.$linked_authority_type, 'aut_link_from_num')
		);
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
}