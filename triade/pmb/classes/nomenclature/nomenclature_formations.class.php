<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formations.class.php,v 1.7 2015-04-03 11:16:23 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_formations
 * Représente toutes les formations 
 */
class nomenclature_formations{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	public $formations;
			
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
		$this->formations =array();
		
		$query = "select id_formation from nomenclature_formations order by formation_order";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->add_formation( new nomenclature_formation($row->id_formation));				
			}
		}		
	}
	
	public function add_formation($formation ) {
	//	$formation->set_formation($this);
		$this->formations[] = $formation;
	
	} // end of member function add_formation
	
	public function get_data() {
		$data=array();
		
		foreach($this->formations  as $formation){
			$data[]=$formation->get_data();
		}
		return($data);
	}
			
	public function get_json_informations(){
		$data = json_encode($this->get_data());
		return $data;
	}	

} // end of nomenclature_formations
