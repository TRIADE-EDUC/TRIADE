<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_record_formations.class.php,v 1.22 2018-03-21 15:42:14 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/nomenclature/nomenclature_record_formation.class.php");
require_once($class_path."/notice_relations.class.php");

/**
 * class nomenclature_record_formations
 * Représente les formations de la nomenclature d'une notice
 */
class nomenclature_record_formations{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom du type
	 * @access protected
	 */

	public $record_formations;
	
	protected $id;
		
	/**
	 * Constructeur
	 *
	 * @param int id de la notice
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = $id*1;
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		global $dbh;
		$this->record_formations = array();
		if($this->id){
			$query = "select id_notice_nomenclature from nomenclature_notices_nomenclatures where notice_nomenclature_num_notice = ".$this->id." order by notice_nomenclature_order, notice_nomenclature_label";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->add_record_formation( new nomenclature_record_formation($row->id_notice_nomenclature));	
				}
			}
		}
	}
	
	public function add_record_formation($record_formation ) {		
		$this->record_formations[] = $record_formation;	
	}
	
	public function get_data($duplicate = false) {	
		$data=array();
		foreach($this->record_formations  as $formation){
			$data[] = $formation->get_data($duplicate);
		}
		return($data);
	}
	
	public function save_form($formations_list) {		
		$this->delete_old_formations($formations_list);
		if(!is_array($formations_list)) return;
		foreach($formations_list as $record_formation){
			$formation=new nomenclature_record_formation($record_formation['nomenclature_id']);
			$formation->save_form($record_formation);
		}
		$this->reorder_children();
	}
	
	/**
	 * Supprime les formations qui ne sont plus présentes dans la notice
	 * @param array $formations_list Tableau des formations à conserver
	 */
	protected function delete_old_formations($formations_list) {
		$formations_ids = array();
		foreach ($formations_list as $formation) {
			$formations_ids[] = $formation['nomenclature_id'];
		}
		foreach ($this->record_formations as $formation) {
			if (!in_array($formation->get_id(), $formations_ids)) {
				$formation->delete();
			}
		}
	}

	public function delete(){
		// supression des formations de la notice
		foreach($this->record_formations  as $formation){
			$formation->delete();
		}
		$this->record_formations = array();
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public static function get_index($id) {
		global $dbh;
		
		$mots="";
		$req="
		select formation_name, notice_nomenclature_label, notice_nomenclature_notes,type_name 
			from nomenclature_notices_nomenclatures, nomenclature_formations, nomenclature_types
			where id_formation=notice_nomenclature_num_formation and id_type=notice_nomenclature_num_type and notice_nomenclature_num_notice='".$id."'
		union
		select formation_name, notice_nomenclature_label, notice_nomenclature_notes, '' as type_name
			from nomenclature_notices_nomenclatures, nomenclature_formations
			where id_formation=notice_nomenclature_num_formation and notice_nomenclature_num_type=0 and notice_nomenclature_num_notice='".$id."'
		";
		
		$result = pmb_mysql_query($req, $dbh);
		if($result){
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$mots.=$row->formation_name." ".$row->notice_nomenclature_label." ".$row->notice_nomenclature_notes." ".$row->type_name." ";
				}
			}	
		}
		return $mots;
	}	
	
	public function get_record_formations(){
		return $this->record_formations;
	}
	
	public function reorder_children() {
		global $pmb_nomenclature_record_children_link;
		global $dbh;
		
		$rank = 0;
		
		$query = 'select distinct child_record_num_record from nomenclature_children_records
						join nomenclature_notices_nomenclatures on nomenclature_children_records.child_record_num_nomenclature = nomenclature_notices_nomenclatures.id_notice_nomenclature
						left join nomenclature_musicstands on nomenclature_children_records.child_record_num_musicstand = nomenclature_musicstands.id_musicstand
						left join nomenclature_families on nomenclature_musicstands.musicstand_famille_num = nomenclature_families.id_family
						left join nomenclature_voices on nomenclature_children_records.child_record_num_voice = nomenclature_voices.id_voice
						left join nomenclature_workshops on nomenclature_children_records.child_record_num_nomenclature = nomenclature_workshops.workshop_num_nomenclature and nomenclature_children_records.child_record_num_workshop = nomenclature_workshops.id_workshop
						left join nomenclature_exotic_instruments on nomenclature_children_records.child_record_num_nomenclature = nomenclature_exotic_instruments.exotic_instrument_num_nomenclature and nomenclature_children_records.child_record_num_instrument = nomenclature_exotic_instruments.exotic_instrument_num_instrument';
		$query.= ' where nomenclature_notices_nomenclatures.notice_nomenclature_num_notice = '.$this->id;
		$query.= ' order by notice_nomenclature_order, notice_nomenclature_label, exotic_instrument_order, workshop_order, family_order, musicstand_order, child_record_order, voice_order';

		$result = pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				notice_relations::update_nomenclature_rank($row->child_record_num_record, $this->id, $pmb_nomenclature_record_children_link, $rank);
				$rank++;
			}
		}
	}
} // end of nomenclature_record_formations
