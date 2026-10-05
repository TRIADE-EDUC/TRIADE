<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_type.class.php,v 1.4 2023/06/23 07:21:23 dgoron Exp $

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
	public $formation;
	
	/**
	 * Tableau d'instances
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * Constructeur
	 *
	 * @param int id du type
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		if($this->id){
			$query = "select * from nomenclature_types where id_type = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_name($row->type_name);
					$this->set_formation_num($row->type_formation_num);
					$this->set_order($row->type_order);
				}
				pmb_mysql_free_result($result);
			}
		}else{
			$this->name = "";
			$this->formation_num = 0;
			$this->order = 0;
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('name', 'admin_nomenclature_formation_type_form_name')
		->add_input_node('text', $this->name);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_nomenclature_form('nomenclature_formation_type_form');
		$formation_name = "<a href='./admin.php?categ=formation&sub=formation&action=form&id=".$this->formation_num."'>".$this->get_formation()->get_name()."</a>";
		if(!$this->id){
			$interface_form->set_label(str_replace('!!formation_name!!',$formation_name,$msg['admin_nomenclature_formation_type_form_add']));
		}else{
			$interface_form->set_label(str_replace('!!formation_name!!',$formation_name,$msg['admin_nomenclature_formation_type_form_edit']));
		}
		$interface_form->set_object_id($this->id)
		->set_object_type('formation_type')
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('nomenclature_types')
		->set_field_focus('name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $name;
		
		$this->name = stripslashes($name);
	}
	
	public function save() {
		if(!$this->formation_num) {
			return false;
		}
		$fields="
		type_formation_num='".$this->formation_num."',
		type_name='".addslashes($this->name)."'
		";
		if(!$this->id){ // Ajout
			
			$requete="select max(type_order) as ordre from nomenclature_types where type_formation_num=".$this->formation_num;
			$resultat=pmb_mysql_query($requete);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_types SET $fields, type_order=".($ordre_max+1);
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE nomenclature_types SET $fields where id_type=".$this->id;
			pmb_mysql_query($req);
		}
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from nomenclature_types WHERE id_type=".$id;
			pmb_mysql_query($req);
		}
		return true;
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
		if(!isset($this->formation) && $this->formation_num) {
			$this->formation = nomenclature_formation::get_instance($this->formation_num);
		}
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

	public static function get_instance($id) {
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new nomenclature_type($id);
		}
		return static::$instances[$id];
	}
} // end of nomenclature_type
