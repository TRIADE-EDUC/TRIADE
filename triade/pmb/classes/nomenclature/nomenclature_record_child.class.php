<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_child.class.php,v 1.36.4.2 2025/03/04 10:57:35 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/notice.class.php');
require_once($class_path.'/audit.class.php');
require_once($class_path."/notice_relations.class.php");

/**
 * class nomenclature_record_child
 */

class nomenclature_record_child {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	protected $nature;
	protected $num_formation;
	protected $formation;
	protected $num_type;
	protected $type;
	protected $num_musicstand;		
	protected $musicstand;		
	protected $num_instrument;
	protected $instrument;			
	protected $effective;		
	protected $order;			
	protected $other;		
	protected $num_voice;	
	protected $voice;		
	protected $num_workshop;	
	protected $workshop;
	protected $num_nomenclature;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id=intval($id);
		$this->fetch_datas();
	} // end of member function __construct
	
	protected function fetch_datas(){
		global $pmb_nomenclature_record_children_link;
		
		$this->nature=0;
		$this->set_num_formation(0);
		$this->set_num_type(0);
		$this->set_num_musicstand(0);
		$this->set_num_instrument(0);
		$this->set_effective(0);
		$this->set_order(0);
		$this->set_other("");
		$this->set_num_voice(0);
		$this->set_num_workshop(0);
		$this->set_num_nomenclature(0);
		if($this->id){
			$query = "select * from nomenclature_children_records where child_record_num_record = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->set_num_formation($row->child_record_num_formation);			
					$this->set_num_type($row->child_record_num_type);							
					$this->set_num_musicstand($row->child_record_num_musicstand);					
					$this->set_num_instrument($row->child_record_num_instrument);
					$this->set_effective($row->child_record_effective);
					$this->set_order($row->child_record_order);
					$this->set_other($row->child_record_other);
					$this->set_num_voice($row->child_record_num_voice);
					$this->set_num_workshop($row->child_record_num_workshop);
					$this->set_num_nomenclature($row->child_record_num_nomenclature);
				}
				$record_formation = new nomenclature_record_formation($this->num_nomenclature);
				$query="select notice_nomenclature_label, linked_notice from notices_relations, nomenclature_notices_nomenclatures where 
				linked_notice= notice_nomenclature_num_notice	and num_notice='".$this->id."' and notice_nomenclature_num_formation=".$row->child_record_num_formation." 				
				and relation_type='".$pmb_nomenclature_record_children_link."' and notice_nomenclature_order='".$record_formation->order."'";				
				$result_parent = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result_parent)){
					if($row_parent = pmb_mysql_fetch_object($result_parent)){
						$this->formation_label=$row_parent->notice_nomenclature_label;
						$this->id_parent=$row_parent->linked_notice;
					}
				}				
			}
		}
	}
	
	public function get_data($duplicate = false){
		return(
			array(
				"nature"=>$this->nature,
				"num_formation"=>$this->get_num_formation(),
				"formation_name"=>$this->formation->get_name(),
				"formation_label"=>$this->formation_label,	
				"id_parent"=> ($duplicate ? 0 : $this->id_parent),					
				"num_type"=>$this->get_num_type(),
				"type_name"=>$this->type->get_name(),
				"num_musicstand"=>$this->get_num_musicstand(),
				"musicstand_name"=>$this->musicstand->get_name(),
				"num_instrument"=>$this->get_num_instrument(),
				"instrument_name"=>$this->instrument->get_name(),
				"effective"=>$this->get_effective(),
				"order"=>$this->get_order(),
				"other"=>$this->get_other(),
				"num_voice"=>$this->get_num_voice(),
				"voice_name"=>$this->voice->get_name(),
				"num_workshop"=>$this->get_num_workshop(),
				"workshop_name"=>$this->workshop->get_name(),
				"num_nomenclature" => $this->get_num_nomenclature(),
			)
		);
	}

	public function get_num_formation(){
		return $this->num_formation;		
	}	
	
	public function get_num_type(){
		return $this->num_type;
	}
	
	public function get_num_musicstand(){
		return $this->num_musicstand;		
	}	
	
	public function get_num_instrument(){
		return $this->num_instrument;		
	}	
	
	public function get_effective(){
		return $this->effective;
	}
	
	public function get_order(){
		return $this->order;
	}
	
	public function get_other(){
		return $this->other;
	}
	
	public function get_num_voice(){
		return $this->num_voice;
	}
	
	public function get_num_workshop(){
		return $this->num_workshop;
	}
	
	public function get_id(){
		return $this->id;
	}	
	
	public function set_num_formation($num_formation){
		$this->num_formation = intval($num_formation);
		$this->formation = nomenclature_formation::get_instance($this->num_formation);
	}
	
	public function set_num_type($num_type){
		$this->num_type = intval($num_type);
		$this->type = nomenclature_type::get_instance($this->num_type);
	}
	
	public function set_num_musicstand($num_musicstand){
		$this->num_musicstand = intval($num_musicstand);
		$this->musicstand = nomenclature_musicstand::get_instance($this->num_musicstand);
	}
	
	public function set_num_instrument($num_instrument){
		$this->num_instrument = intval($num_instrument);
		$this->instrument = nomenclature_instrument::get_instance($this->num_instrument);
	}
	
	public function set_effective($effective){
		$this->effective=$effective;
	}
	
	public function set_order($order){
		$this->order=$order;
	}
	
	public function set_other($other){
		$this->other=$other;
	}
	
	public function set_num_voice($num_voice){
		$this->num_voice = intval($num_voice);
		if($this->num_voice) {
			$this->nature=1;
		} else {
			$this->nature=0;
		}
		$this->voice = nomenclature_voice::get_instance($this->num_voice);
	}
	
	public function set_num_workshop($num_workshop){
		$this->num_workshop = intval($num_workshop);
		$this->workshop = nomenclature_workshop::get_instance($this->num_workshop);
	}
	
	public function save_form(){
		if(!$this->id)return; 
		$this->delete();
		global $nomenclature_record_partial_formation;
		global $nomenclature_record_partial_musicstand;
		global $nomenclature_record_partial_workshop;
		global $nomenclature_record_partial_num_instrument;
		global $nomenclature_record_partial_other_instruments;
		global $nomenclature_record_partial_num_voice;
		global $nomenclature_record_partial_effective;
		global $nomenclature_record_partial_order;
		
		$data = array();
		$formation_detail = explode("_",$nomenclature_record_partial_formation);
		$data["num_formation"]	= intval($formation_detail[0]);
		$data["num_type"]		= intval($formation_detail[1]);
		$data["num_musicstand"]	= intval($nomenclature_record_partial_musicstand);
		$data["num_instrument"]	= intval($nomenclature_record_partial_num_instrument);
		$data["effective"]		= intval($nomenclature_record_partial_effective);
		$data["order"]			= intval($nomenclature_record_partial_order);
		$data["num_voice"]		= intval($nomenclature_record_partial_num_voice);
		$data["num_workshop"]	= intval($nomenclature_record_partial_workshop);		
		$data["other"]			= stripslashes($nomenclature_record_partial_other_instruments);
		return $this->save($data);
	}
	
	public function save($data){
		if(!$this->id)return;
		$fields="
		child_record_num_record='".$this->id."',
		child_record_num_formation='".$data["num_formation"]."',
		child_record_num_type='".$data["num_type"]."',
		child_record_num_musicstand='".$data["num_musicstand"]."',
		child_record_num_instrument='".$data["num_instrument"]."',
		child_record_effective='".$data["effective"]."',
		child_record_order='".$data["order"]."',
		child_record_other='".addslashes($data["other"])."',
		child_record_num_voice='".$data["num_voice"]."',
		child_record_num_workshop='".$data["num_workshop"]."',
		child_record_num_nomenclature='".$data["num_nomenclature"]."'
		";
		
		$req="REPLACE INTO nomenclature_children_records SET $fields ";
		pmb_mysql_query($req);
		$this->fetch_datas();
		return $this->id;
	}
	
	public function delete(){	
		if(!$this->id)return;
		$req="DELETE from nomenclature_children_records WHERE child_record_num_record = ".$this->id;
		pmb_mysql_query($req);
		
		$this->fetch_datas();
	}
	
	// retrouve la notice fille liée au données d'un instrument...
	public function get_child($id_parent,$data){
		global $pmb_nomenclature_record_children_link;
		
		$query='select notices_relations.num_notice from notices_relations join nomenclature_children_records as children on 
				notices_relations.num_notice = children.child_record_num_record
				where notices_relations.linked_notice = "'.$id_parent.'" 
				and notices_relations.relation_type = "'.$pmb_nomenclature_record_children_link.'"
				and children.child_record_num_formation = "'.($data['num_formation']*1).'"
				and children.child_record_num_musicstand = "'.($data['num_musicstand']*1).'"
				and children.child_record_num_instrument = "'.($data['num_instrument']*1).'"
				and children.child_record_num_voice = "'.($data['num_voice']*1).'"
				and children.child_record_other = "'.(isset($data['other']) ? $data['other'] : '').'"
				and children.child_record_effective = "'.($data['effective'] == "~" ? $data['effective'] : intval($data['effective'])).'"
				and children.child_record_order = "'.($data['order']*1).'"
				and children.child_record_num_nomenclature = "'.($data['num_nomenclature']*1).'"
				and children.child_record_num_workshop = "'.($data['num_workshop']*1).'"		
				';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			return $row->num_notice;
		}
		return 0;	
	}
	
	public function create_record_child($id_parent,$data){
		global $pmb_nomenclature_record_children_link;
		
		$tit1 = 'Notice fille temporaire de la nomenclature '.$id_parent;
		$fields = ' tit1 = "'.addslashes($tit1).'" ';
		if ($id_parent) {
			$query = 'select typdoc,statut from notices where notice_id = '.$id_parent;
			$result = pmb_mysql_query($query);
			if ($result){
				$row = pmb_mysql_fetch_object($result);
				$fields.= ', typdoc = "'.$row->typdoc.'" ';
				$fields.= ', statut = "'.$row->statut.'" ';
			}
		}

		$req="INSERT INTO notices SET $fields ";
		pmb_mysql_query($req);
		
		//traitement audit
	    $this->id = pmb_mysql_insert_id();
	    audit::insert_creation (AUDIT_NOTICE, $this->id) ;

		if(!$this->id) return 0;
		
		if(!$data["ranking"]) $data["ranking"]=1;
		$inserted = notice_relations::insert($this->id, $id_parent, $pmb_nomenclature_record_children_link, $data["ranking"]);
		
		$event = new event_record('record', 'after_create_child');
		$event->set_record_id($this->id);
		$event->set_parent_id($id_parent);
		$event_handler = events_handler::get_instance();
		$event_handler->send($event);
		
		notice::calc_access_rights($this->id);
		
		$this->save($data);
		
		$this->fetch_datas();
		$data=$this->get_data();
		
		$tit1 = $this->get_child_record_title($data);
		
		$fields="
		tit1='".addslashes($tit1)."'
		";
		
		$req="UPDATE notices SET $fields where notice_id= ".$this->id;
		pmb_mysql_query($req);
		
		// Mise à jour de tous les index de la notice
		notice::majNoticesTotal($this->id);
		
		return array('id'=>$this->id, 'title'=> $tit1, 'reverse_id_notices_relations' => $inserted['reverse_id_notices_relations'], 'reverse_num_reverse_link' => $inserted['reverse_num_reverse_link']);
	}
		
	public function get_index() {
		$mots = " ".$this->formation->get_name();
		$mots .= " ".$this->type->get_name();
		$mots .= " ".$this->musicstand->get_name();
		$mots .= " ".$this->instrument->get_name();
		$mots .= " ".$this->get_effective();
		$mots .= " ".$this->voice->get_name();
		$mots .= " ".$this->workshop->get_name();
		$mots .= " ";
		return $mots;
	}
	
	public function get_possible_values($num_parent){
		global $msg;
		$possible_values = array();
		$num_parent = intval($num_parent);
		
		//formations
		$query = "select nomenclature_notices_nomenclatures.id_notice_nomenclature,
				nomenclature_notices_nomenclatures.notice_nomenclature_num_formation,
				nomenclature_notices_nomenclatures.notice_nomenclature_num_type,
				nomenclature_notices_nomenclatures.notice_nomenclature_label,
				nomenclature_formations.formation_name,
				nomenclature_types.type_name,
				nomenclature_formations.formation_nature
		from nomenclature_notices_nomenclatures 
		join nomenclature_formations on nomenclature_formations.id_formation = nomenclature_notices_nomenclatures.notice_nomenclature_num_formation
		left join nomenclature_types on nomenclature_types.id_type = nomenclature_notices_nomenclatures.notice_nomenclature_num_type
		where notice_nomenclature_num_notice = ".$num_parent." order by notice_nomenclature_order, notice_nomenclature_label";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$possible_values['formations'][$row->id_notice_nomenclature] = $row->formation_name.($row->type_name ? " / ".$row->type_name : "").($row->notice_nomenclature_label ? " - ".$row->notice_nomenclature_label : "");
			}
		}
		//pupitres..
		$query = "select nomenclature_musicstands.id_musicstand, nomenclature_musicstands.musicstand_name from nomenclature_musicstands join nomenclature_families on musicstand_famille_num = id_family order by family_order,musicstand_order";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$possible_values['musicstands'][$row->id_musicstand] = $row->musicstand_name;
			}
		}
		//workshops
		$query = "select nomenclature_workshops.id_workshop, nomenclature_workshops.workshop_label, nomenclature_workshops.workshop_order from nomenclature_workshops join nomenclature_notices_nomenclatures on nomenclature_workshops.workshop_num_nomenclature= nomenclature_notices_nomenclatures.id_notice_nomenclature where nomenclature_notices_nomenclatures.notice_nomenclature_num_notice = ".$num_parent;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$possible_values['workshops'][$row->id_workshop] = ($row->workshop_label ? $row->workshop_label : $msg['nomenclature_isbd_child_workshop']." ".$row->workshop_order) ;
			}
		}
		return $possible_values;
	} 
	
	public function get_child_record_title($data){
		$tit1 = "";
		$tit1.= $data["instrument_name"].$data["voice_name"];
		if ($data["other"]){
			$other_instruments = explode('/', $data["other"]);
			$other_instruments_name = array();
			foreach ($other_instruments as $other_instrument) {
				$instrument_name = nomenclature_instrument::get_instrument_name_from_code($other_instrument);
				if($instrument_name) {
					$other_instruments_name[] = $instrument_name;
				}
			}
			if(count($other_instruments_name)) {
				if($tit1 != '')$tit1.=' / ';
				$tit1.=implode('/', $other_instruments_name);
			}
		}
		if ($data["order"] && !$data['num_workshop'] && $data['num_musicstand']){
			$tit1.=" ".$data["order"];
		}
		if ($data["musicstand_name"])$tit1.=" / ".$data["musicstand_name"];
		if ($data["formation_label"])$tit1.=" / ".$data["formation_label"];
		return $tit1;
	}
	
	public function set_num_nomenclature($num_nomenclature){
		$this->num_nomenclature = $num_nomenclature;
	}
	
	public function get_num_nomenclature(){
		return $this->num_nomenclature;
	}
	
	public function update_record_child($data){
		$this->delete();
		$this->save($data);
		$data = $this->get_data();
		$tit1 = $this->get_child_record_title($data);
		
		$query = "UPDATE notices SET tit1='".addslashes($tit1)."' where notice_id= ".$this->id;
		pmb_mysql_query($query);
		notice::manage_access_rights($this->id);
	    audit::insert_modif (AUDIT_NOTICE, $this->id) ;
	}
	
	public function delete_record_child(){
		notice::del_notice($this->id);
		$this->delete();
	}
} // end of nomenclature_record_child

