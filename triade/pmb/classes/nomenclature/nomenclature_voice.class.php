<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice.class.php,v 1.2 2015-04-03 11:16:23 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_voice
 */

class nomenclature_voice{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Identifiant de la voix
	 * @access protected
	 */
	protected $id;
	
	/**
	 * Nom de la voix
	 * @access protected
	 */
	protected $name;
	protected $code;
	protected $order;

	/**
	 * Constructeur
	 *
	 * @param int id Identifiant de la voix
	 
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
		
		$this->name = "";
		$this->code = "";
		$this->order =0;
		if($this->id){
			$query = "select * from nomenclature_voices where id_voice = ".$this->id ." order by voice_order asc, voice_name";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->name = $row->voice_name;
					$this->code = $row->voice_code;
					$this->order= $row->voice_order;
				}
			}
		}
	}
	
	public function get_data(){
		
		return(
			array(
				"id" => $this->id,
				"code" => $this->code,
				"name" => $this->name,
				"order" => $this->order
			)
		);	
	}
	

	public function get_name( ) {
		return $this->name;
	}

	public function set_name( $name ) {
		$this->name = $name;
	} 
	
	public function get_code( ) {
		return $this->code;
	}
		
	public function set_code( $code ) {
		$this->code = $code;
	} 
	
	public function get_order( ) {
		return $this->order;
	}
	
	public function set_order( $order ) {
		$this->order = $order;
	}
		
	public function get_id(){
		return $this->id;
	}

	
} // end of nomenclature_voice
