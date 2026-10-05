<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SubManifestation.php,v 1.9 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\Music;

class SubManifestation extends Collection {
	private $notice_id;
	public $type;

	public function __construct($name,$config) {
		$this->notice_id = substr($this->get_code_from_name($name),1);
		$this->type = "submanifestation";
		$this->config = $config;
	}
	

	public function getChildren() {
		$children = array();
		global $pmb_nomenclature_record_children_link;
		global $pmb_nomenclature_music_concept_before, $pmb_nomenclature_music_concept_after, $pmb_nomenclature_music_concept_blank;
		
		switch($this->config['group_tree']){
			case 'music':
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
			case 'scan_request':
				// la requete se complexifie un peu... mais dans le cas de la sous-manif de nomenclature
				// on doit récupérer les documents associé à la sous-manif mais dont le dépot est lui assumé par la manif au niveau de la demande de numérisation
				$query = "select scan_request_explnum_num_explnum as explnum_id 
						from scan_request_explnum 
						join explnum on scan_request_explnum_num_explnum = explnum_id 
						where 
							explnum_mimetype!= 'URL' 
							and (
								scan_request_explnum_num_notice = ".$this->notice_id."
								or
								(
									scan_request_explnum_num_notice in (select linked_notice from notices_relations where relation_type = '".$pmb_nomenclature_record_children_link."' and num_notice = ".$this->notice_id.")
									and explnum.explnum_notice = ".$this->notice_id." and explnum.explnum_bulletin = 0
								)
							)
							and scan_request_explnum_num_bulletin = 0
							and scan_request_explnum_num_request = ".$this->get_parent_by_type('scan_request')->get_scan_request()->get_id();
				$query = $this->filterExplnums($query);
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$children[] = $this->getChild("(E".$row->explnum_id.")");
					}
				}
				break;
		}		
		return $children;
	}

	public function createFile($name, $data = null) {
		if($this->config['group_tree'] == "scan_request"){
			return $this->get_parent_by_type('scan_request')->create_scan_request_file($this->notice_id, 0, $name, $data,"submanif");
		}
		parent::createFile($name, $data);
	}
	
	public function getName() {
		$query = "select notices.tit1 as title, submanifs.child_record_order, musicstands.musicstand_order, families.family_order from notices
				left join nomenclature_children_records as submanifs on notices.notice_id = submanifs.child_record_num_record
				left join nomenclature_musicstands as musicstands on submanifs.child_record_num_musicstand = musicstands.id_musicstand
				left join nomenclature_families as families on musicstands.musicstand_famille_num = families.id_family
				where notices.notice_id= ".$this->notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$prefix = '';
			if (($row->family_order !== NULL) && ($row->musicstand_order !== NULL)) {
				$prefix.= $row->family_order.$row->musicstand_order;
			}
			if ($row->child_record_order !== NULL) {
				$prefix.= $row->child_record_order.' ';
			}
			$name = $prefix.$row->title." (I".$this->notice_id.")";
		}
		return $this->format_name($name);
	}
	
	public function get_notice_id() {
		return $this->notice_id;
	}
	
}