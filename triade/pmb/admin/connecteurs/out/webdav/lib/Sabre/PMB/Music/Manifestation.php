<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Manifestation.php,v 1.9 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\Music;

class Manifestation extends Collection {
	private $notice_id;
	public $type;

	public function __construct($name,$config) {
		$this->notice_id = substr($this->get_code_from_name($name),1);
		$this->type = "manifestation";
		$this->config = $config;
	}
	
	public function getChildren() {
		global $pmb_nomenclature_music_concept_before, $pmb_nomenclature_music_concept_after, $pmb_nomenclature_music_concept_blank;
		
		$children = array();
		switch($this->config['group_tree']){
			case 'music': //On est dans un webdav de musique classique
				$child_before = $this->getChild("(C".\onto_common_uri::get_id($pmb_nomenclature_music_concept_before).")");
				$child_after = $this->getChild("(C".\onto_common_uri::get_id($pmb_nomenclature_music_concept_after).")");
				$child_blank = $this->getChild("(C".\onto_common_uri::get_id($pmb_nomenclature_music_concept_blank).")");
				if ($this->check_write_permission()) {
					$children[] = $child_before;
					$children[] = $child_after;
					$children[] = $child_blank;
				} else {
					$child_before->set_parent($this);
					$child_after->set_parent($this);
					$child_blank->set_parent($this);
					if ($child_before->hasChildren()) {
						$children[] = $child_before;
					}
					if ($child_after->hasChildren()) {
						$children[] = $child_after;
					}
					if ($child_blank->hasChildren()) {
						$children[] = $child_blank;
					}
				}
				break;
			case 'scan_request': //On est dans un webdav de demande de numérisation
				$query = "select scan_request_explnum_num_explnum as explnum_id from scan_request_explnum join explnum on scan_request_explnum_num_explnum = explnum_id where explnum_mimetype!= 'URL' and scan_request_explnum_num_notice = ".$this->notice_id." and scan_request_explnum_num_bulletin = 0 and scan_request_explnum_num_request = ".$this->get_parent_by_type('scan_request')->get_scan_request()->get_id();
				$query = $this->filterExplnums($query);
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$children[] = $this->getChild("(E".$row->explnum_id.")");
					}
				}
				break;
		}
		$record_formations = new \nomenclature_record_formations($this->notice_id);
		
		foreach($record_formations->get_record_formations() as $formation){
			$child = $this->getChild("(F".$formation->get_id().")");
			if ($this->check_write_permission()) {
				$children[] = $child;
			} else {
				$child->set_parent($this);
				$submanifestations_ids = $child->get_submanifestations();
				if(count($submanifestations_ids) && ($submanifestations_ids[0] != "'ensemble_vide'")){
					$children[] = $child;
				}
			}
		}
		return $children;
	}

	public function getName() {
		$query = "select notices.tit1 as title from notices where notices.notice_id= ".$this->notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$name = $row->title." (M".$this->notice_id.")";
		}
		return $this->format_name($name);
	}
	
    public function get_submanifestations(){
    	global $pmb_nomenclature_record_children_link;
    
    	$query = 'select num_notice as notice_id from notices_relations where relation_type = "'.$pmb_nomenclature_record_children_link.'" and linked_notice = '.$this->notice_id;
		$this->filter_sub_manifestations($query);
    	return $this->sub_manifestations;
    }
    
    public function createFile($name, $data = null) {
    	if($this->config['group_tree'] == "scan_request"){
    		return $this->get_parent_by_type('scan_request')->create_scan_request_file($this->notice_id, 0, $name, $data);
    	}
    	parent::createFile($name, $data);
    }
    
    public function get_notice_id(){
    	return $this->notice_id;
    }
    
}