<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice.class.php,v 1.6 2023/06/23 07:21:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/nomenclature/nomenclature_voice.tpl.php");

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
	 * Tableau d'instances
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * Constructeur
	 *
	 * @param int id Identifiant de la voix
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct

	public function fetch_datas(){
		$this->name = "";
		$this->code = "";
		$this->order =0;
		if($this->id){
			$query = "select * from nomenclature_voices where id_voice = ".$this->id ." order by voice_order asc, voice_name";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				if($row = pmb_mysql_fetch_object($result)){
					$this->name = $row->voice_name;
					$this->code = $row->voice_code;
					$this->order= $row->voice_order;
				}
			}
		}
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('code', 'admin_nomenclature_voice_form_code')
		->add_input_node('text', $this->code);
		$interface_content_form->add_element('name', 'admin_nomenclature_voice_form_name')
		->add_input_node('text', $this->name);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_nomenclature_form('nomenclature_voice_form');
		if(!$this->id){
			$interface_form->set_label($msg['admin_nomenclature_voice_form_add']);
		}else{
			$interface_form->set_label($msg['admin_nomenclature_voice_form_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_object_type('voice')
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('nomenclature_voices')
		->set_field_focus('code');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $name, $code;
		
		$this->name = stripslashes($name);
		$this->code = stripslashes($code);
	}
	
	public function save() {
		global $msg;
		
		$fields="
			voice_name='".addslashes($this->name)."', voice_code='".addslashes($this->code)."'
		";
		if(!$this->id){ // Ajout
			$requete="select max(voice_order) as ordre from nomenclature_voices";
			$resultat=pmb_mysql_query($requete);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_voices SET $fields, voice_order=".($ordre_max+1);
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE nomenclature_voices SET $fields where id_voice=".$this->id;
			pmb_mysql_query($req);
		}
		print display_notification($msg['account_types_success_saved']);
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from nomenclature_voices WHERE id_voice=".$id;
			pmb_mysql_query($req);
		}
		return true;
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

	public static function get_voice_name_from_id($id) {
	    $voice_name = '';
	    $id = intval($id);
	    $query = "select voice_name from nomenclature_voices where id_voice=".$id;
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        $row = pmb_mysql_fetch_object($result);
	        $voice_name = $row->voice_name;
	    }
	    return $voice_name;
	}
	
	public static function get_instance($id) {
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new nomenclature_voice($id);
		}
		return static::$instances[$id];
	}
	
} // end of nomenclature_voice
