<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_composed_concepts.class.php,v 1.5 2021/11/18 09:16:37 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_composed_concepts extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->entity_type = 'concepts';
	}
	
	public function get_datas($datas=array()){	    
	    $skos_concepts = new skos_concepts_list();
	    $return_datas = array();
	    $return_datas[0] = array();
	    foreach ($datas as $data) {
	        $skos_concepts->set_composed_concepts_built_with_element($data, $this->get_type_from_entity_type($this->get_parent_type()));
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
	
	protected function get_element_type_from_entity_type($type) {
	    switch($type) {
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
	    		return $type;
// 	    	"ontologie"
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