<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Musicstand.php,v 1.8 2019/07/05 13:25:14 btafforeau Exp $

namespace Sabre\PMB\Music;

class Musicstand extends Collection {
	protected $musicstand;
	
	public function __construct($name,$config) {
		parent::__construct($config);
		$this->musicstand = new \nomenclature_musicstand(substr($this->get_code_from_name($name),1));
		$this->type = "musicstand";
	}

	public function getName() {
		global $msg;
		if ($this->musicstand->get_id()) {
			return $this->format_name($this->musicstand->get_family()->get_order().$this->musicstand->get_order().' - '.$this->musicstand->get_name()." (P".$this->musicstand->get_id().")");
		}
		// Instruments non standards
		return $this->format_name($msg['nomenclature_js_exotic_instruments_label']." (P".$this->musicstand->get_id().")");
	}
	

	public function getChildren() {
		$children = array();
		if ($this->musicstand->get_used_by_workshops()) {
			// On va chercher les ateliers
			$query = 'select id_workshop from nomenclature_workshops where workshop_num_nomenclature = '.$this->get_parent_by_type('formation')->get_formation()->get_id().' order by workshop_order';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$children[] = $this->getChild("(A".$row->id_workshop.")");
				}
			}
		} else {
			$submanifestations_ids = $this->get_submanifestations();
			foreach($submanifestations_ids as $submanifestation_id){
				if($submanifestation_id != "'ensemble_vide'"){
					$children[] = $this->getChild("(I".$submanifestation_id.")");
				}
			}
		}
		return $children;
	}
	
	public function get_submanifestations(){
		if ($this->musicstand->get_used_by_workshops()) {
			// Pour les ateliers
			$query = 'select child_record_num_record as notice_id from nomenclature_children_records where child_record_num_workshop != 0';
		} else {
			// Pour pupitres et instruments non standards
			$query = 'select child_record_num_record as notice_id from nomenclature_children_records where child_record_num_musicstand = '.$this->musicstand->get_id().' and child_record_num_workshop = 0';
		}
		$this->filter_sub_manifestations($query);
		return $this->sub_manifestations;
	}
	
	public function get_musicstand(){
		return $this->musicstand;
	}
}