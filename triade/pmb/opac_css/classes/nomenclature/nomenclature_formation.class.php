<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formation.class.php,v 1.5 2023/08/17 09:47:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/nomenclature/nomenclature_formation.tpl.php");

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
	 * Tableau d'instances
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * Constructeur
	 *
	 * @param int id de la formation
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct

	protected function fetch_datas(){
		$this->name = "";
		$this->nature = 0;
		$this->order = 0;
		$this->types=array();
		if($this->id){
			$query = "select * from nomenclature_formations where id_formation = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				
				$this->set_name($row->formation_name);
				$this->set_nature($row->formation_nature);
				$this->set_order($row->formation_order);
			
				//récupération des types
				$query = "select id_type from nomenclature_types where type_formation_num = ".$this->id." order by type_order asc";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->add_type(nomenclature_type::get_instance($row->id_type));
					}
					pmb_mysql_free_result($result);
				}				
			}
		}
	}
	
	public function get_content_form() {
		global $msg;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('name', 'admin_nomenclature_formation_form_name')
		->add_input_node('text', $this->name);
		$element = $interface_content_form->add_element('nature', 'admin_nomenclature_formation_form_nature');
		$element->add_input_node('radio', '0')->set_checked(($this->nature ? false : true))->set_label($msg['admin_nomenclature_formation_form_nature_instrument']);
		$element->add_input_node('radio', '1')->set_checked(($this->nature ? true : false))->set_label($msg['admin_nomenclature_formation_form_nature_voice']);
		
		$tpl_types="
		<script src='./javascript/sorttable.js'></script>
		<table class='sortable'>
			<tr>
				<th>".$msg["admin_nomenclature_formation_type_form_name"]."
				</th>
			</tr>
		";
		foreach($this->types as $type){
			$tpl_type="
			<tr>
				<td><a href='./admin.php?categ=formation&sub=formation_type&action=form&id=".$type->get_id()."'>".$type->get_name()."</a>
				</td>
			</tr>
			";
			$tpl_types.=$tpl_type;
		}
		$tpl_types.="
		</table>";
		$interface_content_form->add_element('types', 'admin_nomenclature_formation_types')
		->add_html_node($tpl_types);
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_nomenclature_form('nomenclature_formation_form');
		if(!$this->id){
			$interface_form->set_label($msg['admin_nomenclature_formation_form_add']);
		}else{
			$interface_form->set_label($msg['admin_nomenclature_formation_form_edit']);
		}
		
		$interface_form->set_object_id($this->id)
		->set_object_type('formation')
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('nomenclature_formations')
		->set_field_focus('name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $name, $nature;
		
		$this->name = stripslashes($name);
		$this->nature = stripslashes($nature);
	}
	
	public function save() {
		global $msg;
		$fields="
			formation_name='".addslashes($this->name)."',
			formation_nature='".addslashes($this->nature)."'
		";
		if(!$this->id){ // Ajout
			$requete="select max(formation_order) as ordre from nomenclature_formations";
			$resultat=pmb_mysql_query($requete);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_formations SET $fields, formation_order=".($ordre_max+1);
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE nomenclature_formations SET $fields where id_formation=".$this->id;
			pmb_mysql_query($req);
		}
		print display_notification($msg['account_types_success_saved']);
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from nomenclature_types WHERE type_formation_num=".$id;
			pmb_mysql_query($req);
			$req="DELETE from nomenclature_formations WHERE id_formation=".$id;
			pmb_mysql_query($req);
		}
		return true;
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

	public static function get_instance($id) {
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new nomenclature_formation($id);
		}
		return static::$instances[$id];
	}

} // end of nomenclature_formation
