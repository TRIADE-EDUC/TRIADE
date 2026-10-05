<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instruments.class.php,v 1.3 2023/05/05 13:50:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/encoding_normalize.class.php");

/**
 * class nomenclature_instruments
 * Représente tous les instruments 
 */
class nomenclature_instruments{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	public $instruments;
			
	/**
	 * Constructeur
	 *
	 * @param
	 
	 * @return void
	 * @access public
	 */
	public function __construct() {
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		$this->instruments =array();
		
		$query = "select id_instrument from nomenclature_instruments order by instrument_name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->add_instrument(nomenclature_instrument::get_instance($row->id_instrument));				
			}
		}		
	}
	
	public function add_instrument($instrument ) {
		$this->instruments[] = $instrument;
	} // end of member function add_instrument
	
	public function get_data($duplicate = false) {
		$data=array();
		foreach($this->instruments  as $instrument){
		    $data[]=$instrument->get_data($duplicate);
		}
		return($data);
	}
			
	public function get_json_informations(){
		$data = json_encode(encoding_normalize::utf8_normalize($this->get_data()));
		return $data;
	}	

} // end of nomenclature_instruments
