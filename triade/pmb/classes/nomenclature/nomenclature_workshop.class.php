<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_workshop.class.php,v 1.15 2016-03-30 13:13:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_workshop
 * Représente un atelier dans une nomenclature
 */
class nomenclature_workshop{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Identifiant de l'atelier
	 * @access protected
	 */
	protected $id;
	
	/**
	 * Nom de l'atelier
	 * @access protected
	 */
	protected $label;
	protected $num_nomenclature;
	protected $order;
	protected $defined;
	protected $instruments =array();
	protected $instruments_data =array();


	/**
	 * Constructeur
	 *
	 * @param int id Identifiant de l'atelier
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		if($id){
			$this->id = $id*1;
		}
		$this->fetch_datas();
	} // end of member function __construct

	public function fetch_datas(){
		global $dbh;
		
		$this->instruments =array();
		$this->instruments_data=array();
		$this->label = "";
		$this->num_nomenclature =0;
		$this->order =0;
		$this->defined = 0;
		if($this->id){
			//le nom de l'atelier
			$query = "select * from nomenclature_workshops where id_workshop = ".$this->id ." order by workshop_order asc, workshop_label";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->label = $row->workshop_label;
					$this->num_nomenclature = $row->workshop_num_nomenclature;
					$this->order= $row->workshop_order;
					$this->defined= $row->workshop_defined;
					//récupération des instruments
					$query = "select id_workshop_instrument, workshop_instrument_num_instrument, workshop_instrument_number,workshop_instrument_order from nomenclature_workshops_instruments where workshop_instrument_num_workshop = ".$this->id." order by workshop_instrument_order asc";
					$result = pmb_mysql_query($query,$dbh);
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							$this->add_instrument($row->id_workshop_instrument, new nomenclature_instrument($row->workshop_instrument_num_instrument));							
							$this->instruments_data[$row->id_workshop_instrument]['effective']=$row->workshop_instrument_number;
							$this->instruments_data[$row->id_workshop_instrument]['order']=$row->workshop_instrument_order;
							$this->instruments_data[$row->id_workshop_instrument]['id_workshop_instrument'] = $row->id_workshop_instrument;
						}
					}
				}
			}
		}
	}
	
	public function add_instrument($id_workshop_instrument,$instrument) {
		$this->instruments[$id_workshop_instrument] = $instrument;
	}
	
	public function get_data(){
		$data_intruments=array();
		foreach ($this->instruments as $key => $instrument)	{			
			$data=$instrument->get_data();
			$data['effective'] = $this->instruments_data[$key]['effective'];
			$data['order'] = $this->instruments_data[$key]['order'];
			$data['id_workshop_instrument'] = $key;
			$data_intruments[] = $data;
		}
		return(
			array(
				"id" => $this->id,
				"label" => $this->label,
				"num_nomenclature" => $this->num_nomenclature,
				"instruments" => $data_intruments,
				"order" => $this->order,
				"defined" => $this->defined
			)
		);
	
	}
	
	public function get_name(){	
		return $this->label;
	}
	
	public function save_form($data){
		
		$this->label=stripslashes($data["label"]);
		$this->num_nomenclature=$data["num_nomenclature"]*1;		
		$this->order=$data["order"]*1;
		$this->defined=$data["defined"]*1;
		
		$this->delete_old_instruments($data);
		
		$this->instruments_data=array();
		if(is_array($data["instruments"])){
			foreach ($data["instruments"] as $form_id => $instrument){
				$this->instruments_data[$form_id]['id']=$instrument['id']*1;
				$this->instruments_data[$form_id]['effective']=$instrument['effective']*1;
				$this->instruments_data[$form_id]['order']=$instrument['order']*1;
				$this->instruments_data[$form_id]['id_workshop_instrument']=$instrument['id_workshop_instrument']*1;
			}	
		}
		$this->save();
	}		
	
	public function save(){	
		global $dbh;	
		
		$fields="
			workshop_label='". addslashes($this->label) ."',
			workshop_num_nomenclature='".$this->num_nomenclature."',
			workshop_order='".$this->order."',
			workshop_defined='".$this->defined."'
			";
		
		if($this->id){
			$query = "UPDATE nomenclature_workshops SET ".$fields." where id_workshop=".$this->id;
			pmb_mysql_query($query, $dbh);
		}else{
			$query = "INSERT INTO nomenclature_workshops SET ".$fields;
			pmb_mysql_query($query, $dbh);
			$this->id = pmb_mysql_insert_id();
		}
		
		
		foreach ($this->instruments_data as $instrument){			
			
			$fields = "workshop_instrument_num_workshop='".$this->id."',
			workshop_instrument_num_instrument='".$instrument['id']."',
			workshop_instrument_number='".$instrument['effective']."',
			workshop_instrument_order='".$instrument['order']."'
			";
			
			if($instrument['id_workshop_instrument']){
				$query = "UPDATE nomenclature_workshops_instruments SET ".$fields." WHERE id_workshop_instrument = ".$instrument['id_workshop_instrument'];
			}else{
				$query = "INSERT INTO nomenclature_workshops_instruments SET ".$fields;
			}
				
			pmb_mysql_query($query, $dbh);
		}
		$this->fetch_datas();
	}
	
	public function delete(){
		global $dbh;	
		
		$req = "DELETE FROM nomenclature_workshops_instruments WHERE workshop_instrument_num_workshop='$this->id' ";
		pmb_mysql_query($req, $dbh);	
		
		$req = "DELETE FROM nomenclature_workshops WHERE id_workshop='$this->id' ";
		pmb_mysql_query($req, $dbh);
		
		$this->id=0;
		$this->fetch_datas();
	}	
	
// 	/**
// 	 * Méthode qui indique si l'atelier est complet et cohérent
// 	 *
// 	 * @return bool
// 	 * @access public
// 	 */
// 	public function check( ) {
// 		return $this->valid;
// 	} // end of member function check
	
	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_label( ) {
		return $this->label;
	} // end of member function get_label

	/**
	 * Setter
	 *
	 * @param string label Nom de l'atelier

	 * @return void
	 * @access public
	 */
	public function set_label( $label ) {
		$this->label = $label;
	} // end of member function set_label

	/**
	 * Getter
	 *
	 * @return nomenclature_instrument
	 * @access public
	 */
	public function get_instruments( ) {
		return $this->instruments;
	} // end of member function get_instruments

	/**
	 * Setter
	 *
	 * @param nomenclature_instrument instruments Tableau des instruments

	 * @return void
	 * @access public
	 */
	public function set_instruments( $instruments ) {
		$this->instruments = $instruments;
	} // end of member function set_instruments
	
// 	public function get_musicstand($indice){
// 		return $this->musicstands[$indice];
// 	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_order() {
		return $this->order;
	}

	/**
	 * Setter
	 *
	 * @param string abbreviation Nomenclature abrégée
	
	 * @return void
	 * @access public
	 */
	public function set_abbreviation( $abbreviation ) {
		$this->abbreviation = pmb_preg_replace('/\s+/', '', $abbreviation);
	} // end of member function set_abbreviation
	
	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_abbreviation( ) {
		return  pmb_preg_replace('/\s+/', '', $this->abbreviation);
	} // end of member function get_abbreviation
	
	/**
	 * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
	 *
	 * @return void
	 * @access public
	 */
	public function calc_abbreviation( ) {
		$tinstruments = array();
		if(is_array($this->instruments)) {
// 			foreach ($this->musicstands as $musicstand) {
// 				$nomenclature_musicstand = new nomenclature_musicstand($musicstand->get_id());
// 				$nomenclature_musicstand->calc_abbreviation();
// 				$tmusicstands[] = $nomenclature_musicstand->get_abbreviation();
// 			}
		}
		$this->set_abbreviation(implode(".", $tmusicstands));
	} // end of member function calc_abbreviation
	
	/**
	 * Fonction de suppression des instruments des workshops non repostés à l'enregistrement d'une notice
	 * @param array $data (données de workshops reçues depuis un formulaire)
	 */
	public function delete_old_instruments($data){
		global $dbh;
		$ids_workshop_instruments = array();
		if(is_array($data['instruments'])){
			foreach($data['instruments'] as $instrument){
				$ids_workshop_instruments[] = $instrument['id_workshop_instrument'];
			}	
		}
		if(is_array($this->instruments_data)){
			foreach($this->instruments_data as $instrument){
				if(!in_array($instrument['id_workshop_instrument'], $ids_workshop_instruments)){
					$query = 'DELETE FROM nomenclature_workshops_instruments WHERE id_workshop_instrument='.$instrument['id_workshop_instrument'];
					pmb_mysql_query($query, $dbh);
				}
			}
		}
	}
} // end of nomenclature_workshop
