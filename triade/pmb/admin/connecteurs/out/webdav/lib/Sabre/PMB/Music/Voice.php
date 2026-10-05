<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Voice.php,v 1.2 2019/07/05 13:25:14 btafforeau Exp $

namespace Sabre\PMB\Music;

class Voice extends Collection {
	protected $voice;
	
	public function __construct($name,$config) {
		parent::__construct($config);
		$this->voice = new \nomenclature_voice(substr($this->get_code_from_name($name),1));
		$this->type = "voice";
	}

	public function getName() {
		return $this->format_name($this->voice->get_order().' - '.$this->voice->get_name()." (V".$this->voice->get_id().")");
	}
	

	public function getChildren() {
		
		$children = array();
		$submanifestations_ids = $this->get_submanifestations();
		foreach($submanifestations_ids as $submanifestation_id){
			if($submanifestation_id != "'ensemble_vide'"){
				$children[] = $this->getChild("(I".$submanifestation_id.")");
			}
		}
		return $children;
	}
	
	public function get_submanifestations(){
		$query = 'select child_record_num_record as notice_id from nomenclature_children_records where child_record_num_voice = '.$this->voice->get_id();
		$this->filter_sub_manifestations($query);
		return $this->sub_manifestations;
	}
	
	public function get_voice(){
		return $this->voice;
	}
}