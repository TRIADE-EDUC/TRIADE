<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Workshop.php,v 1.1 2016-03-30 13:13:27 apetithomme Exp $

namespace Sabre\PMB\Music;

class Workshop extends Collection {
	protected $workshop;
	
	function __construct($name,$config) {
		parent::__construct($config);
		$this->workshop = new \nomenclature_workshop(substr($this->get_code_from_name($name),1));
		$this->type = "workshop";
	}

	function getName() {
		return $this->format_name($this->workshop->get_order().' - '.$this->workshop->get_label()." (A".$this->workshop->get_id().")");
	}
	

	function getChildren() {
		$children = array();
		$submanifestations_ids = $this->get_submanifestations();
		foreach($submanifestations_ids as $submanifestation_id){
			if($submanifestation_id != "'ensemble_vide'"){
				$children[] = $this->getChild("(I".$submanifestation_id.")");
			}
		}
		return $children;
	}
	
	function get_submanifestations(){
		$query = 'select child_record_num_record as notice_id from nomenclature_children_records where child_record_num_workshop = '.$this->workshop->get_id();
		$this->filter_sub_manifestations($query);
		return $this->sub_manifestations;
	}
	
	function get_workshop(){
		return $this->workshop;
	}
}