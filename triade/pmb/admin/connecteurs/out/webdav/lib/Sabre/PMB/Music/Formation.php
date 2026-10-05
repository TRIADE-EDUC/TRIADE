<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Formation.php,v 1.8 2019/07/05 13:25:14 btafforeau Exp $

namespace Sabre\PMB\Music;

class Formation extends Collection {
	protected $formation;
	
	public function __construct($name,$config) {
		parent::__construct($config);
		$this->formation = new \nomenclature_record_formation(substr($this->get_code_from_name($name),1));
		$this->type = "formation";
	}

	public function getName() {
		return $this->format_name($this->formation->get_label()." (F".$this->formation->get_id().")");
	}
	

	public function getChildren() {
		$children = array();
		if(!$this->formation->get_nature()){
			// Formation d'instruments
			$nomenclature_analyzer = new \nomenclature_nomenclature();
			$musicstands = array();
			foreach ($nomenclature_analyzer->get_families() as $family){
				foreach($family->get_musicstands() as $musicstand){
					$child = $this->getChild("(P".$musicstand->get_id().")");
					$child->get_musicstand()->set_family($family);
					// On décommentera quand on voudra afficher tous les instruments pour créer dynamiquement les sous-manifs à l'ajout de documents
// 					if ($this->check_write_permission()) {
// 						$children[] = $child;
// 					} else {
						$child->set_parent($this);
						$submanifestations_ids = $child->get_submanifestations();
						if(count($submanifestations_ids) && ($submanifestations_ids[0] != "'ensemble_vide'")){
							$children[] = $child;
						}
// 					}
				}
			}
			// Instruments non standards
			$child = $this->getChild("(P0)");
			$child->set_parent($this);
			$submanifestations_ids = $child->get_submanifestations();
			if(count($submanifestations_ids) && ($submanifestations_ids[0] != "'ensemble_vide'")){
				$children[] = $child;
			}
		} else {
			// Formation de voix
			$nomenclature_voices = new \nomenclature_voices();
			if (is_array($nomenclature_voices->voices)) {
				foreach ($nomenclature_voices->voices as $voice) {
					$child = $this->getChild("(V".$voice->get_id().")");
					$child->set_parent($this);
					$submanifestations_ids = $child->get_submanifestations();
					if(count($submanifestations_ids) && ($submanifestations_ids[0] != "'ensemble_vide'")){
						$children[] = $child;
					}
				}
			}
		}
		return $children;
	}
	
	public function get_submanifestations(){
		$query = 'select child_record_num_record as notice_id from nomenclature_children_records where child_record_num_formation = '.$this->formation->get_num_formation().' and child_record_num_nomenclature = '.$this->formation->get_id();
		$this->filter_sub_manifestations($query);
		return $this->sub_manifestations;
	}
	
	public function get_formation() {
		return $this->formation;
	}
}