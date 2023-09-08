<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_authorities_caddie_ui.class.php,v 1.4 2018-08-06 12:20:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/caddie/list_caddie_root_ui.class.php");

class list_authorities_caddie_ui extends list_caddie_root_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_caddie_content() {
		$query = "SELECT authorities_caddie_content.object_id FROM authorities_caddie_content";
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function _get_query_base() {
		$query = "";
		switch (static::$object_type) {
			case 'AUTHORS':
				$query = "select authorities.id_authority as id, authors.* from authors JOIN authorities ON authorities.num_object = authors.author_id AND authorities.type_object = ".AUT_TABLE_AUTHORS;
				break;
			case 'CATEGORIES':
				$query = "select authorities.id_authority as id, categories.* from categories JOIN authorities ON authorities.num_object = categories.num_noeud AND authorities.type_object = ".AUT_TABLE_CATEG;
				break;
			case 'PUBLISHERS':
				$query = "select authorities.id_authority as id, publishers.* from publishers JOIN authorities ON authorities.num_object = publishers.ed_id AND authorities.type_object = ".AUT_TABLE_PUBLISHERS;
				break;
			case 'COLLECTIONS':
				$query = "select authorities.id_authority as id, collections.* from collections JOIN authorities ON authorities.num_object = collections.collection_id AND authorities.type_object = ".AUT_TABLE_COLLECTIONS;
				break;
			case 'SUBCOLLECTIONS':
				$query = "select authorities.id_authority as id, sub_collections.* from sub_collections JOIN authorities ON authorities.num_object = sub_collections.sub_coll_id AND authorities.type_object = ".AUT_TABLE_SUB_COLLECTIONS;
				break;
			case 'SERIES':
				$query = "select authorities.id_authority as id, series.* from series JOIN authorities ON authorities.num_object = series.serie_id AND authorities.type_object = ".AUT_TABLE_SERIES;
				break;
			case 'TITRES_UNIFORMES':
				$query = "select authorities.id_authority as id, titres_uniformes.* from titres_uniformes JOIN authorities ON authorities.num_object = titres_uniformes.tu_id AND authorities.type_object = ".AUT_TABLE_TITRES_UNIFORMES;
				break;
		}
		if($query) {
			$query .= " where authorities.id_authority IN (".$this->_get_query_caddie_content().")";
		}
		return $query;
	}
	
	protected function get_main_fields() {
// 		switch (static::$object_type) {
// 			case 'AUTHORS':
// 				return array_merge(
// 				$this->get_describe_fields('authors', 'authors', 'authors')
// 				);
// 				break;
// 			case 'CATEGORIES':
// 				return array_merge(
// 				$this->get_describe_fields('categories', 'categories', 'categories')
// 				);
// 				break;
// 		}
		return array_merge(
			$this->get_describe_fields(strtolower(static::$object_type), strtolower(static::$object_type), strtolower(static::$object_type))
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		switch (static::$object_type) {
			case 'AUTHORS':
				$this->add_custom_fields_available_columns('author', 'author_id');
				break;
			case 'CATEGORIES':
				$this->add_custom_fields_available_columns('categ', 'num_noeud');
				break;
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		switch (static::$object_type) {
			case 'AUTHORS':
				$sort_by = 'author_name';
				break;
			case 'CATEGORIES':
				$sort_by = 'libelle_categorie';
				break;
			case 'PUBLISHERS':
				$sort_by = 'ed_name';
				break;
			case 'COLLECTIONS':
				$sort_by = 'collection_name';
				break;
			case 'SUBCOLLECTIONS':
				$sort_by = 'sub_coll_name';
				break;
			case 'SERIES':
				$sort_by = 'serie_name';
				break;
			case 'TITRES_UNIFORMES':
				$sort_by = 'tu_name';
		}
		$this->applied_sort = array(
				'by' => $sort_by,
				'asc_desc' => 'asc'
		);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/autorites.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type='.static::$object_type.'&idcaddie='.static::$id_caddie.'&item=0';
	}
}