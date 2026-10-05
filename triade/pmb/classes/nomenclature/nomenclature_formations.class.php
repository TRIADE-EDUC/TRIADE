<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formations.class.php,v 1.9 2023/05/05 13:38:10 dgoron Exp $

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
		$this->formations =array();
		$query = "select id_formation from nomenclature_formations order by formation_order";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$this->add_formation(nomenclature_formation::get_instance($row->id_formation));				
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
