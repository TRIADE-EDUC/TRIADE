<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_composed_concepts.class.php,v 1.4 2018/12/04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_composed_concepts extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
	
// 	public function get_sub_datasources(){
// 		if(get_called_class() != 'frbr_entity_common_datasource_composed_concepts' ) {
// 			return array();
// 		} else {
// 			return array(
// 					"frbr_entity_records_datasource_custom_fields",
// 					"frbr_entity_authors_datasource_custom_fields",
// 					"frbr_entity_categories_datasource_custom_fields",
// 					"frbr_entity_concepts_datasource_custom_fields",
// 					"frbr_entity_publishers_datasource_custom_fields",
// 					"frbr_entity_collections_datasource_custom_fields",
// 					"frbr_entity_subcollections_datasource_custom_fields",
// 					"frbr_entity_series_datasource_custom_fields",
// 					"frbr_entity_works_datasource_custom_fields",
// 					"frbr_entity_indexint_datasource_custom_fields",
// 					"frbr_entity_authperso_datasource_custom_fields"
// 			);
// 		}
// 	}
	
	
	public function get_datas($datas=array()){	    
	    $skos_concepts = new skos_concepts_list();
	    $return_datas = array();
	    $return_datas[0] = array();
	    foreach ($datas as $data) {
	        $skos_concepts->set_composed_concepts_built_with_element($data, $this->get_type_const_from_entity_type($this->get_parent_type()));
	        $concepts = $skos_concepts->get_concepts();
	        $concepts_ids = array();
	        foreach($concepts as $concept){
	        	$concepts_ids[] = $concept->get_id();
	        }
	        $skos_concepts->set_concepts(array());
	        if(count($concepts_ids)){
	        	$return_datas[$data] = $concepts_ids;
	        	$return_datas[0] = array_merge($return_datas[0], $concepts_ids);
	        }
	    }
	    return parent::get_datas($return_datas);
	}
	
	protected function get_element_type_from_entity_type($entity_type = '') {
		if (!$entity_type) {
			$entity_type = $this->get_entity_type();
		}
	    switch($entity_type) {
	    	case 'authors':
	    		return "author";
	    	case 'categories':
	    		return "category";
	    	case 'publishers':
	    		return "publisher";
	    	case 'collections':
	    		return "collection"; 
	    	case 'subcollections':
	    		return "subcollection";
	    	case 'series':
	    		return "serie";
	    	case 'works':
	    		return "titre_uniforme";
	    	case 'concepts': 
	    		return "concept";
	    	case 'records':
	    	case 'indexint':
	    	case 'authperso':
	    		return $entity_type;
	    }
	}
	
	protected function get_type_const_from_entity_type($entity_type = '') {
		if (!$entity_type) {
			$entity_type = $this->get_entity_type();
		}
	    switch($entity_type) {
	    	case 'authors':
	    		return TYPE_AUTHOR;
	    	case 'categories':
	    		return TYPE_CATEGORY;
	    	case 'publishers':
	    		return TYPE_PUBLISHER;
	    	case 'collections':
	    		return TYPE_COLLECTION; 
	    	case 'subcollections':
	    		return TYPE_SUBCOLLECTION;
	    	case 'series':
	    		return TYPE_SERIE;
	    	case 'works':
	    		return TYPE_TITRE_UNIFORME;
	    	case 'concepts': 
	    		return TYPE_CONCEPT;
	    	case 'records':
	    		return TYPE_NOTICE;
	    	case 'indexint':
	    		return TYPE_INDEXINT;
	    	case 'authperso':
	    		return TYPE_AUTHPERSO;
	    }
	}
	
	protected function get_parent_type(){
		$query = "select datanode_num_parent, page_entity from frbr_datanodes join frbr_pages on 
				frbr_pages.id_page=frbr_datanodes.datanode_num_page where id_datanode = ".$this->num_datanode;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$result = pmb_mysql_fetch_assoc($result);
			if($result['datanode_num_parent'] != 0){
				return frbr_entity_common_entity_datanode::get_entity_type_from_id($result['datanode_num_parent']);
			}
			return $result['page_entity'];
		}
		return '';
	}
}