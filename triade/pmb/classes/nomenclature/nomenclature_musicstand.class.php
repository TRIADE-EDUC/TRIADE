<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_musicstand.class.php,v 1.14 2016-02-15 14:09:40 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/nomenclature/nomenclature_instrument.class.php");


/**
 * class nomenclature_musicstand
 * Représente un pupitre
 */
class nomenclature_musicstand {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Famille auquel appartient le pupitre
	 * @access protected
	 */
	protected $family;

	/**
	 * Nom du pupitre
	 * @access protected
	 */
	protected $name;

	/**
	 * Effectif pour le pupitre
	 * @access protected
	 */
	protected $effective;

	/**
	 * Instrument standard du pupitre
	 * @access protected
	 */
	protected $standard_instrument;

	/**
	 * Liste des instruments composants le pupitre
	 * @access protected
	 */
	protected $instruments;

	/**
	 * Le pupitre est il divisable en partie
	 * @access protected
	 */
	protected $divisable;
	/**
	 * Booléen qui indique si le pupitre est valide
	 * @access protected
	 */
	protected $valid = false;

	/**
	 * Nomenclature du pupitre abrégée
	 * @access protected
	 */
	protected $abbreviation;
	
	/**
	 * Flag pour savoir si le pupitre est lié aux ateliers
	 * @access protected
	 */
	protected $used_by_workshops;
	
	/**
	 * Ordre du musicstand
	 * @access protected
	 */
	protected $order;
	
	
	/**
	 * Constructueur
	 *
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = $id*1;
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		global $dbh;
		if($this->id){
			$query = "select nomenclature_musicstands.musicstand_order, nomenclature_musicstands.musicstand_name, nomenclature_musicstands.musicstand_division, nomenclature_musicstands.musicstand_workshop, nomenclature_instruments.id_instrument, nomenclature_instruments.instrument_code, nomenclature_instruments.instrument_name from nomenclature_musicstands left join nomenclature_instruments on nomenclature_musicstands.id_musicstand = nomenclature_instruments.instrument_musicstand_num and instrument_standard = 1 where nomenclature_musicstands.id_musicstand = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_name($row->musicstand_name);
					if($row->id_instrument){
						$this->set_standard_instrument(new nomenclature_instrument($row->id_instrument,$row->instrument_code,$row->instrument_name));
					}
					$this->order = $row->musicstand_order;
					$this->set_divisable($row->musicstand_division);
					$this->set_used_by_workshops($row->musicstand_workshop);
				}
			}
		}
	}
	
	public function set_used_by_workshops($used_by_workshop){
		if($used_by_workshop){
			$this->used_by_workshops = true;
		}else{
			$this->used_by_workshops = false;
		}
	}
	
	public function get_used_by_workshops(){
		return $this->used_by_workshops;
	}
	
	public function set_divisable($divisable) {
		if($divisable){
			$this->divisable = true;
		}else{
			$this->divisable = false;
		}
	}
	
	public function get_divisable(){
		return $this->divisable;
	}
	
	/**
	 * Setter
	 *
	 * @param nomenclature_family family Famille à associer

	 * @return void
	 * @access public
	 */
	public function set_family( $family ) {
		$this->family=$family;
	} // end of member function set_family

	/**
	 * Getter
	 *
	 * @return nomenclature_family
	 * @access public
	 */
	public function get_family( ) {
		return $this->family;
	} // end of member function get_family

	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_name( ) {
		return $this->name;
	} // end of member function get_name

	/**
	 * Setter
	 *
	 * @param string name Nom du pupitre

	 * @return void
	 * @access public
	 */
	public function set_name( $name ) {
		$this->name=$name;
	} // end of member function set_name

	/**
	 * Getter
	 *
	 * @return integer
	 * @access public
	 */
	public function get_effective( ) {
		$this->calc_effective();
		return $this->effective;
	} // end of member function get_effective

	/**
	 * Setter
	 *
	 * @param integer effective Effectif du pupitre

	 * @return void
	 * @access public
	 */
	public function set_effective( $effective ) {
		$this->effective=$effective;
	} // end of member function set_effective

	/**
	 * Getter
	 *
	 * @return nomenclature_instrument
	 * @access public
	 */
	public function get_standard_instrument( ) {
		return $this->standard_instrument;
	} // end of member function get_standard_instrument

	/**
	 * Setter
	 *
	 * @param nomenclature_instrument standard_instrument Instrument standard du pupitre

	 * @return void
	 * @access public
	 */
	public function set_standard_instrument( $standard_instrument ) {
		$this->standard_instrument=$standard_instrument;
	} // end of member function set_standard_instrument

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
	 * @param nomenclature_instrument instruments Tableau des instruments du pupitre

	 * @return void
	 * @access public
	 */
	public function set_instruments( $instruments ) {
		$this->instruments = $instruments;
	} // end of member function set_instruments

	/**
	 * Methode d'ajout d'instrument à la liste des instruments du pupitre
	 *
	 * @param nomenclature_instrument instrument Instrument du pupitre

	 * @param bool reorder Forcer l'insertion à l'ordre de l'instrument (décale les autres)

	 * @return void
	 * @access public
	 */
	public function add_instrument( $instrument,  $reorder = false ) {
		$instrument->set_musicstand($this);
		$this->instruments[]=$instrument;
		if($reorder){
			$this->reorder();					
		}
	} // end of member function add_instrument

	/**
	 * 
	 *
	 * @param integer order Numéro d'ordre de l'instrument à supprimer de la liste

	 * @param bool reorder Booléen pour forcer le recalcul de l'ordre de chaque instrument

	 * @return void
	 * @access public
	 */
	public function delete_instrument( $order,  $reorder = false ) {
		for ($i=0;$i<count($this->instruments); $i++){			
			$instrument=$this->instruments[$i];			
			if($instrument->get_order()==$order){	
				array_splice($this->instruments,$i,1);
				break;			
			}
			
		}		
		if($reorder){
			$this->reorder();			
		}		
	} // end of member function delete_instrument
	
	/**
	 * Méthode qui ré-ordonne la liste d'instrument
	 * 
	 *
	 * @return void
	 * @access protected
	 */
	protected function reorder( ) {
		$orders=array();
		for ($i=0;$i<count($this->instruments); $i++){
			$instrument=$this->instruments[$i];
			$orders[$instrument->get_order()]=$i;			
		}
		foreach($orders as $i){
			$instrument=$this->instruments[$i];
			$instrument->set_order($i+1);
		}
	} // end of member function reorder
	
	
	/**
	 * Méthode qui indique si un pupitre est bon
	 *
	 * @return bool
	 * @access public
	 */
	public function check( ) {
	
		
	} // end of member function check


	/**
	 * Méthode qui calcule l'effectif du pupitre en fonction des effectifs de chaque
	 * intrument
	 *
	 * @return void
	 * @access protected
	 */
	protected function calc_effective( ) {
		$this->effective=0;
		foreach ($this->instruments as $instrument) {
			$this->effective+=$instrument->get_effective();
		}
	} // end of member function calc_effective

	public function get_tree_informations(){
		$tree = array(
			'id' => $this->get_id(),
			'name' => $this->get_name(),
			'divisable' => $this->get_divisable(),
			'used_by_workshops' => $this->get_used_by_workshops()
		);
		if(is_object($this->get_standard_instrument())){
			$tree['std_instrument'] = array(
				'id' => $this->get_standard_instrument()->get_id(),
				'code' => $this->get_standard_instrument()->get_code(),
				'name' => $this->get_standard_instrument()->get_name()
			);
		}
		return $tree;
	}
	
	public function get_id(){
		return $this->id;
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
	
	
	public function get_order(){
		return $this->order;
	}
	
	/**
	 * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
	 *
	 * @return void
	 * @access public
	 */
	public function calc_abbreviation( ) {
		$tinstruments = array();
		$musicstand_standard_all = true;
		if(is_array($this->instruments)) {
			foreach ($this->instruments as $instrument) {
				if (count($instrument->get_others_instruments())) {
					$musicstand_standard_all = false;
				}
				$nomenclature_instrument = new nomenclature_instrument($instrument->get_id(),$instrument->get_code(),$instrument->get_name());
				$nomenclature_instrument->calc_abbreviation();
				if ($instrument->is_standard()) {
					if ($instrument->get_part()) {
						$tinstruments[$instrument->get_part()] = $nomenclature_instrument->get_abbreviation();
					} else {
						$tinstruments[$instrument->get_order()] = $nomenclature_instrument->get_abbreviation();
					}
				} else {
					$tinstruments[$instrument->get_order()] = $nomenclature_instrument->get_abbreviation();
					$musicstand_standard_all = false;
				}
			}
			if ($musicstand_standard_all) {
				$this->set_abbreviation($this->effective);
			} else {
				ksort($tinstruments);
				$this->set_abbreviation($this->effective."[".implode(".", $tinstruments)."]");
			}
		} else {
			$this->set_abbreviation("0");
		}
	} // end of member function calc_abbreviation

} // end of nomenclature_musicstand

