<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_aut_link_collections.class.php,v 1.1 2017/05/05 07:43:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_aut_link_collections extends frbr_entity_common_datasource_aut_link_authorities {
	
	protected $key_name = 'collection_id';
	protected $table_name = 'collections';
	protected $authority_type = AUT_TABLE_COLLECTIONS;
	
	public function __construct($id=0){
		$this->entity_type = "collections";
		parent::__construct($id);
	}
	
// 	public function get_query() {
// 		$query = "select distinct collections.collection_id FROM collections 
// 			WHERE (collection_id IN (select aut_link_from_num FROM aut_link WHERE aut_link_from = 4 AND aut_link_type = '!!p!!' and aut_link_to_num = '!!id!!')
// 			OR collection_id IN (select aut_link_to_num FROM aut_link WHERE aut_link_to = 4 and aut_link_reciproc = 1 and aut_link_type = '!!p!!' and aut_link_from_num = '!!id!!')
// 			";
// 		return $query;
// 	}
}