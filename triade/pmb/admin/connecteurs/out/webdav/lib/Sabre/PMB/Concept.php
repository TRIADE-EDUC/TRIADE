<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Concept.php,v 1.2 2020/11/25 11:07:50 arenou Exp $
namespace Sabre\PMB;

class Concept extends Collection {
    protected $concept;
	public $config;
	public $type;
	
	public function __construct($name,$config) {
		$this->config = $config;
		$this->type = "concept";
		$code = $this->get_code_from_name($name);
		$id = substr($code,1);
		if($id){
		    $this->concept = new \skos_concept($id);
		}
	}
	
	public function getChildren() {
		//les enfants attendus par le paramétrage du connecteur
		//sauf pour le noeud racine d'un thésaurus...
		$current_children=array();
 	
		$children = parent::getChildren();
		$store = \skos_datastore::get_store();
		
		$sparql = "select ?uri where {
                ?uri rdf:type skos:Concept .
                ?uri skos:broader <".$this->concept->uri.">.
        }";
        if($store->query($sparql)){
            $results = $store->get_result();
            for($i=0 ; $i<count($results) ; $i++){
                $children[] = new Concept("(D".\onto_common_uri::get_id($results[$i]->uri).")" ,$this->config);
            }
		}
		usort($current_children,"sortChildren");
		return array_merge($children,$current_children);
	}

	public function getName() {
	    return $this->format_name($this->concept->display_label." (D".$this->concept->get_id().")");
	}
	
	public function need_to_display($categ_id){
		
				return true;
		
	}
	
	public function getNotices(){
		$this->notices = array();	
		$query = "select num_object as notice_id from index_concept where num_concept = ".$this->concept->id." and type_object = ".TYPE_NOTICE;
		$this->filterNotices($query);		
		return $this->notices;
	}
    
	public function update_notice_infos($notice_id){
// 		if($notice_id*1 >0){
// 			$query = "select * from notices_categories where notcateg_notice = ".$notice_id." and num_noeud = ".$this->categ->id;
// 			$result = pmb_mysql_query($query);
// 			if(pmb_mysql_num_rows($result) == 0){
// 				$query = "insert into notices_categories set notcateg_notice = ".$notice_id.",num_noeud = ".$this->categ->id;
// 				pmb_mysql_query($query);				
// 			} 
// 		}
	}
}