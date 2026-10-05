<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Event.php,v 1.6 2019/07/05 13:25:14 btafforeau Exp $
namespace Sabre\PMB\Music;

class Event extends Collection {
	protected $authperso;

	public function __construct($name,$config) {
		$this->config = $config;
		$this->type = "event";
		$code = $this->get_code_from_name($name);
		$id = substr($code,1);
		if($id){
			$this->authperso = new \authperso_authority($id);
		}
	}
	
	public function getName() {
		return $this->format_name($this->authperso->info['isbd']." (K".$this->authperso->id.")");
	}

	public function getNotices(){
		$this->notices = array();
		if($this->authperso->id){
			$concepts_ids = array();
			$query = 'select titres_uniformes.tu_id from titres_uniformes join tu_oeuvres_events on titres_uniformes.tu_id = tu_oeuvres_events.oeuvre_event_tu_num where ';
			$query.= 'tu_oeuvres_events.oeuvre_event_authperso_authority_num='.$this->authperso->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$vedette_composee_found = \vedette_composee::get_vedettes_built_with_element($row->tu_id, TYPE_TITRE_UNIFORME);
					foreach($vedette_composee_found as $vedette_id){
						$concepts_ids[] = \vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
					}
				}
			}
			if (count($concepts_ids)) {
				$query = 'select num_object as notice_id from index_concept where type_object = '.TYPE_NOTICE.' and num_concept in ('.implode(',', $concepts_ids).')';
				$this->filterNotices($query);
			}
		}
		return $this->notices;
	}
	
	public function getChildren(){
		$children = array();
		$query = 'select titres_uniformes.tu_id from titres_uniformes join tu_oeuvres_events on titres_uniformes.tu_id = tu_oeuvres_events.oeuvre_event_tu_num where ';
		$query.= 'tu_oeuvres_events.oeuvre_event_authperso_authority_num='.$this->authperso->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$child = $this->getChild("(W".$row->tu_id.")");
				if ($this->check_write_permission()) {
					$children[] = $child;
				} else {
					$child->set_parent($this);
					$notices = $child->getNotices();
					if (count($notices) && ($notices[0] != "'ensemble_vide'")) {
						$children[] = $child;
					}
				}
			}
		}
		return $children;
	}
}