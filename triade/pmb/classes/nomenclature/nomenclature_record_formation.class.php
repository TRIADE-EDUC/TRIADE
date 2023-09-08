<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formation.class.php,v 1.28 2018-07-05 15:32:20 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/nomenclature/nomenclature_workshop.class.php");
require_once($class_path."/nomenclature/nomenclature_formation.class.php");

/**
 * class nomenclature_record_formation
 * Représente une formation de la nomenclature d'une notice
 */
class nomenclature_record_formation{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	public $num_record=0;
	public $num_formation=0;
	public $num_type=0;
	public $label="";
	public $abbreviation="";
	public $notes="";
	public $families_notes=array();
	public $exotic_instruments_note="";
	public $order=0;
	public $nature=0;
	public $workshops=array();
	public $instruments =array();
	public $instruments_other =array();
	public $instruments_data =array();
	public $id = 0;
	
	/**
	 * Constructeur
	 *
	 * @param int id de nomenclature_notices_nomenclatures: id_notice_nomenclature
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {

		$this->id = $id*1;			

		$this->fetch_datas();
	} // end of member function __construct

	public function fetch_datas(){
		global $dbh;
		
		$this->num_record=0;		
		$this->num_formation=0;		
		$this->num_type=0;	
		$this->label="";
		$this->abbreviation="";
		$this->notes="";
		$this->families_notes=array();
		$this->exotic_instruments_note="";
		$this->order=0;										
		$this->workshops=array();			
		$this->instruments =array();// non standard
		$this->instruments_other =array();// non standard
		$this->instruments_data=array();
		$this->nature=0;
		if($this->id){
			$query = "select * from nomenclature_notices_nomenclatures where id_notice_nomenclature = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->num_record=$row->notice_nomenclature_num_notice;		
					$this->num_formation=$row->notice_nomenclature_num_formation;		
					$this->num_type=$row->notice_nomenclature_num_type;	
					$this->label=$row->notice_nomenclature_label;
					$this->abbreviation=$row->notice_nomenclature_abbreviation;
					$this->notes=$row->notice_nomenclature_notes;
					$this->families_notes=unserialize($row->notice_nomenclature_families_notes);
					$this->exotic_instruments_note=$row->notice_nomenclature_exotic_instruments_note;
					$this->order=$row->notice_nomenclature_order;	
					
					$formation=new nomenclature_formation($row->notice_nomenclature_num_formation);		
					$this->nature=$formation->get_nature();
					if(!$this->nature){
						// formation instruments
						// Ateliers de la nomenclature de la notice
						$query = "select id_workshop from nomenclature_workshops where workshop_num_nomenclature = ".$this->id." order by workshop_order, workshop_label";
						$result = pmb_mysql_query($query,$dbh);
						if($result){
							if(pmb_mysql_num_rows($result)){
								while($row = pmb_mysql_fetch_object($result)){	
									$this->add_workshop( new nomenclature_workshop($row->id_workshop));				
								}	
							}
						}
						// Instruments non standard de la nomenclature de la notice
						$query = "select * from nomenclature_exotic_instruments where exotic_instrument_num_nomenclature = ".$this->id." order by exotic_instrument_order";
						$result = pmb_mysql_query($query,$dbh);
						if($result){
							if(pmb_mysql_num_rows($result)){
								while($row = pmb_mysql_fetch_object($result)){	
									$id_exotic_instrument=$row->id_exotic_instrument;	
									$this->add_instrument($id_exotic_instrument,new nomenclature_instrument($row->exotic_instrument_num_instrument));
									$this->instruments_data[$id_exotic_instrument]['effective']=$row->exotic_instrument_number;
									$this->instruments_data[$id_exotic_instrument]['order']=$row->exotic_instrument_order;	
									$this->instruments_data[$id_exotic_instrument]['id']=$row->exotic_instrument_num_instrument;
									$this->instruments_data[$id_exotic_instrument]['id_exotic_instrument']=$id_exotic_instrument;
									$this->instruments_data[$id_exotic_instrument]['other']=array();
									$query = "select * from nomenclature_exotic_other_instruments where exotic_other_instrument_num_exotic_instrument = ".$id_exotic_instrument." order by exotic_other_instrument_order";
									$result_other = pmb_mysql_query($query,$dbh);
									if($result_other){
										if(pmb_mysql_num_rows($result_other)){
											while($row = pmb_mysql_fetch_object($result_other)){
												$id_exotic_other_instrument = $row->id_exotic_other_instrument;
												$this->add_other_instrument($id_exotic_instrument, $id_exotic_other_instrument, new nomenclature_instrument($row->exotic_other_instrument_num_instrument));
												$this->instruments_data[$id_exotic_instrument]['other'][$id_exotic_other_instrument]['id']=$row->exotic_other_instrument_num_instrument;
												$this->instruments_data[$id_exotic_instrument]['other'][$id_exotic_other_instrument]['order']=$row->exotic_other_instrument_order;
												$this->instruments_data[$id_exotic_instrument]['other'][$id_exotic_other_instrument]['id_exotic_instrument'] = $id_exotic_other_instrument;
											}
										}
									}
								}		
							}
						}
					// fin formation instrument
					}else{
						// formation voix
						
					}// fin formation voix
				}		
			}
		}
	}
	
	public function get_formation_nature($formation) {
		return($formation->nature);
	}	
	
	public function add_workshop( $workshop ) {
		$this->workshops[] = $workshop;
	}
	
	public function add_instrument($id_exotic_instrument, $instrument) {
		$this->instruments[$id_exotic_instrument]= $instrument;
	}
	
	public function add_other_instrument($id_exotic_instrument, $id_other_exotic_instrument, $instrument) {
		$this->instruments_other[$id_exotic_instrument][$id_other_exotic_instrument] = $instrument;
	}
	
	public function get_data($duplicate = false){
		
		// Ateliers de la nomenclature de la notice
		$data_workshop=array();
		foreach($this->workshops as $workshop){
			$data_workshop[]=$workshop->get_data();
		}
		// Instruments non standards de la nomenclature de la notice
		$data_intruments=array();
		foreach ($this->instruments as $key => $instrument)	{
			$data=$instrument->get_data();
			$data['effective']=$this->instruments_data[$key]['effective'];
			$data['order']=$this->instruments_data[$key]['order'];
			$data['id_exotic_instrument'] = $key;
			if(isset($this->instruments_other[$key])){
				foreach ($this->instruments_other[$key] as $other_key => $instrument_other)	{
					$data_other=$instrument_other->get_data();
					$data_other['order']=$this->instruments_data[$key]['other'][$other_key]['order'];
					$data_other['id_exotic_instrument'] = $other_key;
					$data['other'][]=$data_other;
				}
			}			
			$data_intruments[]=$data;
		}
		
		// data de la nomenclature de la notice
		return (
			array(
				"id" => ($duplicate ? 0 : $this->id),
				"num_record" => ($duplicate ? 0 : $this->num_record),
				"num_formation" => $this->num_formation,
				"num_type" => $this->num_type,
				"nature" => $this->nature,
				"label" => $this->label,
				"abbreviation" => $this->abbreviation,
				"notes" => $this->notes,
				"families_notes" => $this->families_notes,
				"exotic_instruments_note" => $this->exotic_instruments_note,
				"workshops" => $data_workshop,
				"instruments" => $data_intruments,
				"order" => $this->order
			)
		);
	}
	
	public function save_form($data) {
		$this->num_record=$data["num_record"]*1;		
		$this->num_formation=$data["num_formation"]*1;		
		$this->num_type=$data["num_type"]*1;	
		$this->label=stripslashes($data["label"]);
		$this->abbreviation=stripslashes($data["abbr"]);
		$this->notes=stripslashes($data["notes"]);
		$this->families_notes=stripslashes_array($data["families_notes"]);
		$this->exotic_instruments_note=(isset($data["exotic_instruments_note"]) ? stripslashes($data["exotic_instruments_note"]) : '');
		$this->order=$data["order"]*1;	

		$this->delete_old_instruments($data);
		
		$this->workshops = array();			
		$this->instruments = array();// non standard
		$this->instruments_data = array();
	
		// instruments non standarts de la nomenclature de la notice
		if(isset($data["instruments"]) && is_array($data["instruments"])){
			foreach($data["instruments"] as $form_id => $formation_instrument){
				$this->instruments_data[$form_id]["id"]=$formation_instrument["id"]*1;
				$this->instruments_data[$form_id]["effective"]=$formation_instrument["effective"]*1;
				$this->instruments_data[$form_id]["order"]=$formation_instrument["order"]*1;
				$this->instruments_data[$form_id]["id_exotic_instrument"] = $formation_instrument["id_exotic_instrument"]*1;
				$this->instruments_data[$form_id]["other"]=array();
				$other_order=1;
				if(isset($formation_instrument["other"]) && is_array($formation_instrument["other"])) {
					foreach($formation_instrument["other"] as $second_form_id => $instrument_other){
						$this->instruments_data[$form_id]["other"][$second_form_id]["id"]=$instrument_other["id"]*1;
						$this->instruments_data[$form_id]["other"][$second_form_id]["order"]=$instrument_other["order"]*1;
						$this->instruments_data[$form_id]["other"][$second_form_id]["id_exotic_instrument"]=$instrument_other["id_exotic_instrument"]*1;
					}
				}
			}
		}

		$this->save();
		
		$workshops = array();
		// Ateliers de la nomenclature de la notice
		if(isset($data["workshops"]) && is_array($data["workshops"])){
			foreach($data["workshops"] as $formation_workshop){
				$workshop = new nomenclature_workshop($formation_workshop["id"]);
				$formation_workshop["num_nomenclature"]=$this->id;
				$workshop->save_form($formation_workshop);
				$workshops[] = $workshop->get_data();
			}		
		}
		
		return array(
				'nomenclature_id' => $this->id,
				'exotic_instruments' => $this->instruments_data,
				'workshops' => $workshops
		);
	}
	
	public function save(){
		global $dbh;
				
		$fields="
			notice_nomenclature_num_notice='".$this->num_record."',
			notice_nomenclature_num_formation='".$this->num_formation."',
			notice_nomenclature_num_type='".$this->num_type."',
			notice_nomenclature_label='". addslashes($this->label) ."',
			notice_nomenclature_abbreviation='". addslashes($this->abbreviation) ."',
			notice_nomenclature_notes='". addslashes($this->notes) ."',
			notice_nomenclature_families_notes='". addslashes(serialize($this->families_notes)) ."',
			notice_nomenclature_exotic_instruments_note='". addslashes($this->exotic_instruments_note) ."',
			notice_nomenclature_order='".$this->order."'
		";	
		
		
		if(!$this->id){
			$req= 'INSERT INTO nomenclature_notices_nomenclatures SET '.$fields;
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id();
			$audit_type="creation";
		}else{
			$req = 'UPDATE nomenclature_notices_nomenclatures SET '.$fields.' where id_notice_nomenclature='.$this->id;
			pmb_mysql_query($req, $dbh);
			$audit_type="update";
		}
		foreach($this->instruments_data as $form_id => $formation_instrument){
			$req ="exotic_instrument_num_instrument=".$formation_instrument["id"].",
			exotic_instrument_number=".$formation_instrument["effective"].",
			exotic_instrument_order=".$formation_instrument["order"].",
			exotic_instrument_num_nomenclature=".$this->id;
			
			if($formation_instrument["id_exotic_instrument"]){
				$req = "UPDATE nomenclature_exotic_instruments SET ".$req." where id_exotic_instrument = ".$formation_instrument["id_exotic_instrument"]; //add where clause
				pmb_mysql_query($req, $dbh);
				$id_exotic_instrument = $formation_instrument["id_exotic_instrument"];
			}else{
				$req = "INSERT INTO nomenclature_exotic_instruments SET ".$req;
				pmb_mysql_query($req, $dbh);
				$id_exotic_instrument = pmb_mysql_insert_id();
			}
			
			$temp_other_instruments_data = array();
			if(is_array($formation_instrument["other"]) && $id_exotic_instrument){
				foreach($formation_instrument["other"] as $instrument_other){
					
					$req = "exotic_other_instrument_num_instrument=".$instrument_other["id"].",
						    exotic_other_instrument_order=".$instrument_other["order"].",
						    exotic_other_instrument_num_exotic_instrument=".$id_exotic_instrument;
					if($instrument_other["id_exotic_instrument"]){
						$req = "UPDATE nomenclature_exotic_other_instruments SET ".$req." where id_exotic_other_instrument =".$instrument_other["id_exotic_instrument"];
					}else{
						$req = "INSERT INTO nomenclature_exotic_other_instruments SET ".$req;
					}
					pmb_mysql_query($req, $dbh);
				}	
			}
			
		}
 		if($audit_type == 'creation'){
 		    audit::insert_creation(AUDIT_NOTICE,$this->num_record,'Nomenclature: '.$this->label.'('.$this->get_abbreviation().')');
 		}else{
 		    audit::insert_modif(AUDIT_NOTICE,$this->num_record,'Nomenclature: '.$this->label.'('.$this->get_abbreviation().')');
 		}
		$this->fetch_datas();
	}
	
	public function delete(){
		global $dbh;
		
		foreach($this->workshops as $workshop){
			$workshop->delete();
		}
		
		foreach($this->instruments_data as $id_exotic_instrument => $formation_instrument){
			foreach($formation_instrument["other"]  as $instrument_other){
				$req = "DELETE FROM nomenclature_exotic_other_instruments WHERE exotic_other_instrument_num_exotic_instrument=".$id_exotic_instrument;
			}
			pmb_mysql_query($req, $dbh);
		}
		
		$req = "DELETE FROM nomenclature_exotic_instruments WHERE exotic_instrument_num_nomenclature=".$this->id;
		pmb_mysql_query($req, $dbh);
		
		$req="DELETE from nomenclature_notices_nomenclatures WHERE id_notice_nomenclature = ".$this->id;
		pmb_mysql_query($req, $dbh);
		
		$this->id=0;
		$this->fetch_datas();
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_label(){
		return $this->label;
	}
	
	public function get_nature(){
		return $this->nature;
	}
	
	public function get_abbreviation(){
		return $this->abbreviation;
	}
	
	public function get_num_formation(){
		return $this->num_formation;
	}
	
	protected function delete_old_instruments($data){
		global $dbh;
		$ids_exotics_instruments = array();
		$ids_others_exotics_instruments = array();

		if(isset($data["instruments"]) && is_array($data["instruments"])){
			foreach($data["instruments"] as $instruments){
				$ids_others_exotics_instruments[$instruments["id_exotic_instrument"]] = array();
				if(is_array($instruments["other"])){
					foreach($instruments["other"] as $others_instruments){
						$ids_others_exotics_instruments[$instruments["id_exotic_instrument"]][] = $others_instruments["id_exotic_instrument"];  
					}
				}
				$ids_exotics_instruments[] = $instruments["id_exotic_instrument"];
			}
		}
		foreach($this->instruments_data as $instrument_data){
			if(!in_array($instrument_data["id_exotic_instrument"], $ids_exotics_instruments)){
				$req = "DELETE FROM nomenclature_exotic_other_instruments WHERE exotic_other_instrument_num_exotic_instrument=".$instrument_data["id_exotic_instrument"];
				pmb_mysql_query($req, $dbh);
				
				$req = "DELETE FROM nomenclature_exotic_instruments WHERE id_exotic_instrument=".$instrument_data["id_exotic_instrument"];
				pmb_mysql_query($req, $dbh);
			}else{
				foreach($instrument_data["other"] as $other_exotic){
					if(!in_array($other_exotic["id_exotic_instrument"], $ids_others_exotics_instruments[$instrument_data["id_exotic_instrument"]])){
						$req = "DELETE FROM nomenclature_exotic_other_instruments WHERE id_exotic_other_instrument=".$other_exotic["id_exotic_instrument"];
						pmb_mysql_query($req, $dbh);
					}
				}
			}
		}
		
		$ids_workshops = array();
		if(isset($data["workshops"]) && is_array($data["workshops"])){
			foreach($data["workshops"] as $workshop){
				$ids_workshops[] = $workshop['id'];
			}
		}
		
		foreach($this->workshops as $workshop){
			if(!in_array($workshop->get_id(), $ids_workshops)){
				$workshop->delete();	
			}
		}
		
		
		/**
		 * TODO: Détruire également les Workshops
		 */
		
	}

} // end of nomenclature_record_formation
