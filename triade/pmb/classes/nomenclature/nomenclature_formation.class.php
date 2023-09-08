<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formation.class.php,v 1.7 2017-06-29 13:08:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_formation
 * Représente une formation 
 */
class nomenclature_formation{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	protected $id;
	
	/**
	 * Nom de la formation
	 * @access protected
	 */
	public $name;
	public $order;	
	public $nature;
	public $types=array();	
	
	/**
	 * Notice à laquelle appartient cette formation
	 * @access protected
	 */	
//	public $record_formation;
	
	
	/**
	 * Constructeur
	 *
	 * @param int id de la formation
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = $id*1;
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		global $dbh;
		$this->name = "";
		$this->nature = 0;
		$this->order = 0;
		$this->types=array();
		if($this->id){
			$query = "select * from nomenclature_formations where id_formation = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->set_name($row->formation_name);
				$this->set_nature($row->formation_nature);
				$this->set_order($row->formation_order);
			
				//récupération des types
				$query = "select id_type from nomenclature_types where type_formation_num = ".$this->id." order by type_order asc";
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->add_type(new nomenclature_type($row->id_type));
					}
				}				
			}
		}
	}
	
	public function set_formation( $formation ) {
		$this->formation=$formation;
	}

	public function add_type( $type ) {
		$this->types[] = $type;
	
	} // end of member function add_type
	
		
	public function get_data(){
		$data_types=array();
		for($i=0; $i<count($this->types);$i++) {
			$type=$this->types[$i];
			$data_types[]=$type->get_data();
		}
		return(
			array(		
				'id'=>	$this->id,
				'name'=>	$this->name,
				'nature'=>	$this->nature,
				'order'=>	$this->order,				
				'types'=>$data_types	
			)
		);		
	}
	
	
	/**
	 * Setter
	 *
	 * @param nomenclature_record_formations notice à associer
	
	 * @return void
	 * @access public
	 */
	public function set_record( $record_formation ) {
//		$this->record_formation=$record_formation;
	} // end of member function set_record
	
	/**
	 * Getter
	 *
	 * @return nomenclature_record_formations
	 * @access public
	 */
	public function get_record( ) {
		return $this->record_formation;
	} // end of member function get_record
		
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
	 * @param string name Nom de la formation

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
	 * @param int name ordre de la formation
	
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
	
	public function get_nature( ) {
		return $this->nature;
	} // end of member function get_nature
	
	/**
	 * Setter
	 *
	 * @param int name ordre de la formation
	
	 * @return void
	 * @access public
	 */
	public function set_nature( $nature ) {
		$this->nature = $nature;
	} // end of member function set_nature
	
	/**
	 * Getter
	 *
	 * @return nomenclature_type
	 * @access public
	 */
	public function get_types( ) {
		return $this->types;
	} // end of member function get_types

	/**
	 * Setter
	 *
	 * @param nomenclature_type types Tableau des types

	 * @return void
	 * @access public
	 */
	public function set_types( $types ) {
		$this->types = $types;
	} // end of member function set_types
	
	public function get_type($indice){
		return $this->types[$indice];
	}
	
	public function get_id(){
		return $this->id;
	}


} // end of nomenclature_formation
