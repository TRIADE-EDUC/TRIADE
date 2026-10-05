<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Work.php,v 1.7 2019/07/05 13:25:14 btafforeau Exp $

namespace Sabre\PMB\Music;

class Work extends Collection {
	protected $titre_uniforme;
	
	public function __construct($name,$config) {
		parent::__construct($config);
		$id = substr($this->get_code_from_name($name),1);
		$this->titre_uniforme =  new \titre_uniforme($id);
		$this->type = "work";
	}

	public function getName() {
		global $charset;
		return $this->format_name($this->titre_uniforme->get_isbd()." (W".$this->titre_uniforme->id.")");
	}
	
	public function getNotices(){
		$this->notices = array();
		$vedette_composee_found = \vedette_composee::get_vedettes_built_with_element($this->titre_uniforme->id, TYPE_TITRE_UNIFORME);
		$concepts_ids = array();
		foreach($vedette_composee_found as $vedette_id){
			$concepts_ids[] = \vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
		}
		if (count(count($concepts_ids))) {
			$query = 'select num_object as notice_id from index_concept where type_object = '.TYPE_NOTICE.' and num_concept in ('.implode(',', $concepts_ids).')';
			$this->filterNotices($query);
		}
		return $this->notices;
	}
    
	public function getChildren(){
		$children = array();
		$vedette_composee_found = \vedette_composee::get_vedettes_built_with_element($this->titre_uniforme->id, TYPE_TITRE_UNIFORME);
		$concepts_ids = array();
		foreach($vedette_composee_found as $vedette_id){
			$concepts_ids[] = \vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
		}
		if(count($concepts_ids)){
			$query = 'select num_object from index_concept where type_object = '.TYPE_NOTICE.' and num_concept in ('.implode(',', $concepts_ids).')';
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$children[] = $this->getChild("(M".$row->num_object.")");
				}
			}
		}
		return $children;
	}
	
	public function get_titre_uniforme() {
		return $this->titre_uniforme;
	}
}