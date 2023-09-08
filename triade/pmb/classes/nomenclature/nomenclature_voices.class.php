<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voices.class.php,v 1.3 2016-03-30 13:13:27 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/nomenclature/nomenclature_voice.class.php");

/**
 * class nomenclature_voices
 * Représente toutes les voices 
 */
class nomenclature_voices{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	public $voices;
			
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
		global $dbh;
		$this->voices =array();
		
		$query = "select id_voice from nomenclature_voices order by voice_order, voice_code, voice_name";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->add_voice( new nomenclature_voice($row->id_voice));				
			}
		}		
	}
	
	public function add_voice($voice ) {
		$this->voices[] = $voice;
	
	} // end of member function add_voice
	
	public function get_data() {
		$data=array();
		
		foreach($this->voices  as $voice){
			$data[]=$voice->get_data();
		}
		return($data);
	}
			
	public function get_json_informations(){
		$data = json_encode(encoding_normalize::utf8_normalize($this->get_data()));
		return $data;
	}	

} // end of nomenclature_voices
