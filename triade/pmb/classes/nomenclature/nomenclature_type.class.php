<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_type.class.php,v 1.5 2017-06-29 13:08:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_type
 * Représente un type de formation
 */
class nomenclature_type{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	protected $id;
	
	/**
	 * Nom du type
	 * @access protected
	 */
	public $name;
	public $formation_num;
	public $order;	
	
	/**
	 * Formation auquel appartient le type
	 * @access protected
	 */
//	public $formation;
	
	
	/**
	 * Constructeur
	 *
	 * @param int id du type
	 
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
			$query = "select * from nomenclature_types where id_type = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_name($row->type_name);
					$this->set_formation_num($row->type_formation_num);
					$this->set_order($row->type_order);
				}
			}
		}else{
			$this->name = "";
			$this->ormation_num = 0;
			$this->order = 0;
		}
	}
	
	/**
	 * Setter
	 *
	 * @param nomenclature_formation formation à associer

	 * @return void
	 * @access public
	 */
	public function set_formation( $formation ) {
		$this->formation=$formation;
	} // end of member function set_formation
	
	
	
	public function get_data(){		
		return(
			array(
				"id" => $this->id,
				"name" => $this->name,
				"formation_num" => $this->formation_num,
				"order" => $this->order
			)
		);
	
	}
	
	/**
	 * Getter
	 *
	 * @return nomenclature_formation
	 * @access public
	 */
	public function get_formation( ) {
		return $this->formation;
	} // end of member function get_formation
	
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
	 * @param string name Nom du type

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
	public function get_order( ) {
		return $this->order;
	} // end of member function get_order
	
	/**
	 * Setter
	 *
	 * @param int name ordre du type
	
	 * @return void
	 * @access public
	 */
	public function set_order( $order ) {
		$this->order = $order;
	} // end of member function set_order
	
	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	
	public function get_formation_num( ) {
		return $this->formation_num;
	} // end of member function get_formation_num
	
	/**
	 * Setter
	 *
	 * @param int id de la formation
	
	 * @return void
	 * @access public
	 */
	public function set_formation_num( $formation_num ) {
		$this->formation_num = $formation_num;
	} // end of member function set_formation_num
	
		
	public function get_id(){
		return $this->id;
	}

} // end of nomenclature_type
