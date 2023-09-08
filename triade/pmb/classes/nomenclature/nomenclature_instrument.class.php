<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument.class.php,v 1.13 2016-06-01 08:19:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/encoding_normalize.class.php");
require_once($include_path."/templates/nomenclature_instrument.tpl.php");

/**
 * class nomenclature_instrument
 * Représente un instrument
 * 
 */
class nomenclature_instrument{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Nom de l'instrument
	 * @access protected
	 */
	protected $name;

	/**
	 * Abréviation de l'instrument
	 * @access protected
	 */
	protected $code;

	/**
	 * Effectif de l'instrument
	 * @access protected
	 */
	protected $effective = 1;

	/**
	 * Booléen pour savoir si l'instrument est standard
	 * @access protected
	 */
	protected $standard = true;

	/**
	 * Tableau des instruments annexes à l'instrument.
	 * ex : Un flutiste qui joue aussi du piccolo et du basson 1/Pic/Bn
	 * @access protected
	 */
	protected $others_instruments = array();

	/**
	 * Ordre de l'instrument sur le pupitre
	 * @access protected
	 */
	protected $order = 1;

	/**
	 * Pupitre auquel est rattaché l'instrument
	 * @access protected
	 */
	protected $musicstand;

	/**
	 * Booléen qui indique si l'instrument est valide
	 * @access protected
	 */
	protected $valid = false;

	/**
	 * Numéro de partie
	 * @access protected
	 */
	protected $part;

	/**
	 * Nomenclature de l'instrument abrégée
	 * @access protected
	 */
	protected $abbreviation;
	
	/**
	 * Constructeur
	 *
	 * @param string code Abréviation de l'instrument
	
	 * @param string name Nom de l'instrument
	
	 * @return void
	 * @access public
	 */
	public function __construct($id,$code="", $name="") {
		if($id){
			$this->id = $id*1;
			$this->fetch_datas();
		}else{
			$this->set_code($code);
			$this->set_name($name);
		}
	} // end of member function __construct
	
	
	protected function fetch_datas(){
	global $dbh;
		$this->code = "";
		$this->name = "";
		$this->musicstand_num = "";
		$this->standard = 0;
		$this->order = 0;
		if($this->id){
			$query = "select * from nomenclature_instruments where id_instrument = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_code($row->instrument_code);
					$this->set_name($row->instrument_name);
					$this->set_musicstand_num($row->instrument_musicstand_num);
					$this->set_standard($row->instrument_standard);
				}
			}
		}
	}
	
	public function get_data(){
		return(
			array(
				"id" => $this->id,
				"code" => $this->get_code(),
				"name" => $this->get_name(),
				"musicstand_num" => $this->get_musicstand_num(),
				"standard" => $this->get_standard()
			)
		);	
	}
		
	/**
	 * Méthode d'ajout d'un instrument annexe
	 *
	 * @param nomenclature_instrument other_instrument Instrument annexe  Ajouter à la liste des instruments annexes
	
	 * @return void
	 * @access public
	 */
	public function add_other_instrument( $other_instrument ) {
		$other_instrument->set_order(count($this->get_others_instruments())+1);
		$this->others_instruments[] = $other_instrument;
	} // end of member function add_other_instrument
	
	/**
	 *
	 *
	 * @param integer order Ordre de l'instrument à supprimer de la liste des instruments annexes
	
	 * @return void
	 * @access public
	 */
	public function delete_other_instrument( $order ) {
		array_splice($this->others_instruments, $order,1);
	} // end of member function delete_other_instrument
	
	/**
	 * Retourne la propriété "standard" qui nous indique s'il s'agit d'un instrument
	 * standard du pupitre courant
	 *
	 * @return bool
	 * @access public
	 */
	public function is_standard( ) {
		return $this->standard;
	} // end of member function is_standard
	
	/**
	 * Méthode qui indique si l'instrument est valide
	 *
	 * @return bool
	 * @access public
	 */
	public function check( ) {
		if (!$this->effective) $this->valid = false;
		return $this->valid;
	} // end of member function check
	
	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_name( ) {
		return $this->name;
	} // end of member function get_name
	
	public function get_standard( ) {
		return $this->standard;
	} 
	
	public function set_musicstand_num( $musicstand_num) {
		$this->musicstand_num=$musicstand_num;
	} // end of member function set_musicstand_num
	
	public function get_musicstand_num( ) {
		return $this->musicstand_num;
	}
	/**
	 * Setter
	 *
	 * @param integer name Nom de l'instrument

	 * @return void
	 * @access public
	 */
	public function set_name( $name ) {
		$this->name = $name;
	} // end of member function set_name

	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_code( ) {
		return $this->code;
	} // end of member function get_code

	/**
	 * Setter
	 *
	 * @param integer code Abréviation de l'instrument

	 * @return void
	 * @access public
	 */
	public function set_code( $code ) {
		$this->code = $code;
	} // end of member function set_code

	

	/**
	 * Getter
	 *
	 * @return integer
	 * @access public
	 */
	public function get_effective( ) {
		return $this->effective;
	} // end of member function get_effective

	/**
	 * 
	 *
	 * @param integer effective Effectif de l'instrument

	 * @return void
	 * @access public
	 */
	public function set_effective( $effective ) {
		$this->effective = $effective;
	} // end of member function set_effective

	/**
	 * Getter
	 *
	 * @return nomenclature_instrument
	 * @access public
	 */
	public function get_others_instruments( ) {
		return $this->others_instruments;
	} // end of member function get_others_instruments

	/**
	 * Setter
	 *
	 * @param nomenclature_instrument others_instruments Tableau des instruments annexes

	 * @return void
	 * @access public
	 */
	public function set_others_instruments( $others_instruments ) {
		$this->others_instruments = $others_instruments;
	} // end of member function set_others_instruments

	/**
	 * Getter
	 *
	 * @return integer
	 * @access public
	 */
	public function get_order( ) {
		return $this->order;
	} // end of member function get_order

	/**
	 * Setter
	 *
	 * @param integer order Ordre de l'instrument sur le pupitre

	 * @return void
	 * @access public
	 */
	public function set_order( $order ) {
		$this->order = $order;
	} // end of member function set_order

	/**
	 * Getter
	 *
	 * @return nomenclature_musicstand
	 * @access public
	 */
	public function get_musicstand( ) {
		return $this->musicstand;
	} // end of member function get_musicstand

	/**
	 * Setter
	 *
	 * @param nomenclature_musicstand musicstand Pupitre à  associer à l'instrument

	 * @return void
	 * @access public
	 */
	public function set_musicstand( $musicstand ) {
		$this->musicstand = $musicstand;
	} // end of member function set_musicstand

	/**
	 * Getter
	 *
	 * @return integer
	 * @access public
	 */
	public function get_part( ) {
		return $this->part;
	} // end of member function get_part

	/**
	 * Setter
	 *
	 * @param integer part Numéro de partie

	 * @return void
	 * @access public
	 */
	public function set_part( $part = 0 ) {
		$this->part = $part;
	} // end of member function set_part
	
	/**
	 * Setter
	 *
	 * @param boolean standard Instrument standard Oui/Non
	
	 * @return void
	 * @access public
	 */
	public function set_standard( $standard = false ) {
		$this->standard = $standard;
	} // end of member function set_standard

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
	
	/**
	 * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
	 *
	 * @return void
	 * @access public
	 */
	public function calc_abbreviation( ) {
		$others_instruments = "";
		$tothers = array();
		if (count($this->others_instruments)) {
			foreach ($this->others_instruments as $other_instrument) {
				$tothers[$other_instrument->get_order()] = $other_instrument->get_code();
			}
			ksort($tothers);
			$others_instruments = implode("/", $tothers);
		}
		if ($this->is_standard) {
			if ($this->part) {
				$this->abbreviation = $this->effective.($others_instruments != "" ? "/".$others_instruments : "");
			} else {
				$this->abbreviation = $this->order.($others_instruments != "" ? "/".$others_instruments : "");
			}
		} else {
			$this->abbreviation = $this->code.($others_instruments != "" ? "/".$others_instruments : "");
		}
	} // end of member function calc_abbreviation
	
	public function get_tree_informations(){
		$tree = array(
				'id' => $this->get_id(),
				'code' => $this->get_code(),
				'name' => $this->get_name()
		);
		return $tree;
	}
	
	public static function get_dialog_form(){
		global $nomenclature_instrument_dialog_tpl, $msg;
		
		$form = $nomenclature_instrument_dialog_tpl;
		
		$query = "select id_musicstand, concat(musicstand_name,' ( ',family_name,' )')as label from nomenclature_musicstands,nomenclature_families where musicstand_famille_num=id_family order by musicstand_name";
		$musicstand = gen_liste($query, "id_musicstand", "label", "id_musicstand", "", "", 0,encoding_normalize::utf8_normalize($msg["admin_nomenclature_instrument_form_musicstand_no"]), 0, encoding_normalize::utf8_normalize($msg["admin_nomenclature_instrument_form_musicstand_no_sel"]));
		$form = str_replace('!!musicstand!!', $musicstand, $form);
		
		return $form;
	}
	
	public static function create(){
		global $msg;
		global $code, $name, $id_musicstand, $standard;
		
		$return = array();
		$id_musicstand += 0;
		$standard +=0;
		if($code && $name) {
			$query = "select * from nomenclature_instruments where instrument_code='".$code."'";
			$result = pmb_mysql_query($query);
			if (!pmb_mysql_num_rows($result)) {
				$query = "INSERT INTO nomenclature_instruments 
					SET instrument_code='".$code."',
					instrument_name='".$name."',
					instrument_musicstand_num='".$id_musicstand."',
					instrument_standard='".$standard."' ";
				pmb_mysql_query($query);
				$return = array(
					'code' => $code,
					'id' => pmb_mysql_insert_id(),
					'musicstand_num' => $id_musicstand,
					'name' => $name,
					'standard' => $standard,
					'state' => true
				);
			} else {
				$return['error_message'] = $msg['admin_nomenclature_instrument_already_exists'];
				$return['state'] = false;
			}
		} else {
			if(!$code) {
				$return['error_message'] = $msg['admin_nomenclature_instrument_form_code_error'];
			} elseif(!$name) {
				$return['error_message'] = $msg['admin_nomenclature_instrument_form_name_error'];
			}
			$return['state'] = false;
		}
		return $return;
	}
	
	public static function get_instrument_name_from_code($code) {
		$instrument_name = '';
		$query = "select instrument_name from nomenclature_instruments where instrument_code='".$code."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$instrument_name = $row->instrument_name;
		}
		return $instrument_name;
	}
	
} // end of nomenclature_instrument
