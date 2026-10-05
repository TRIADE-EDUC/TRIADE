<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family.class.php,v 1.12 2024/04/25 12:30:32 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/nomenclature/nomenclature_family.tpl.php");

/**
 * class nomenclature_family
 * Représente une famille dans une nomenclature
 */
class nomenclature_family{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	protected $id;
	
	/**
	 * Nom de la famille
	 * @access protected
	 */
	protected $name;

	/**
	 * 
	 * @access protected
	 */
	protected $musicstands;

	/**
	 * Booléen qui indique si la famille est valide
	 * @access protected
	 */
	protected $valid = false;

	/**
	 * Nomenclature de la famille abrégée
	 * @access protected
	 */
	protected $abbreviation;

	/**
	 * Ordre de la famille en base
	 * @access protected
	 */
	protected $order;
	
	/**
	 * Tableau d'instances
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * Constructeur
	 *
	 * @param string name Nom de la famille
	 
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct

	public function fetch_datas(){
		if($this->id){
			//le nom de la famille
			$query = "select family_name, family_order from nomenclature_families where id_family = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_name($row->family_name);
					$this->order = $row->family_order;
				}
				//récupération des pupitres
				$query = "select id_musicstand from nomenclature_musicstands where musicstand_famille_num = ".$this->id." order by musicstand_order asc";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->add_musicstand(nomenclature_musicstand::get_instance($row->id_musicstand, true));
					}
				}
			}
		}else{
			$this->musicstands =array();
			$this->name = "";
			$this->order = "";
		}
	}
	
	public function get_content_form() {
		global $msg;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('name', 'admin_nomenclature_family_form_name')
		->add_input_node('text', $this->name);
		
		$tpl_musicstands="
		<script type='text/javascript' src='./javascript/sorttable.js'></script>
		<table class='sortable'>
			<tr>
				<th>".$msg["admin_nomenclature_family_musicstand_form_name"]."
				</th>
				<th>".$msg["admin_nomenclature_family_musicstand_form_instruments"]."
				</th>
			</tr>
		";
		foreach($this->musicstands as $musicstand){
			$tpl_musicstand="
			<tr>
				<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=family&sub=family_musicstand&action=form&id=".$musicstand->get_id()."';\">
					<a href='./admin.php?categ=family&sub=family_musicstand&action=form&id=".$musicstand->get_id()."'>".$musicstand->get_name()."</a>
				</td>
				<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=family&sub=family_musicstand&action=form&id=".$musicstand->get_id()."';\">
					".$musicstand->get_instruments_display()."
				</td>
			</tr>
			";
			$tpl_musicstands.=$tpl_musicstand;
		}
		$tpl_musicstands.="
		</table>";
		$interface_content_form->add_element('musicstands', 'admin_nomenclature_family_pupitres')
		->add_html_node($tpl_musicstands);
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_nomenclature_form('nomenclature_family_form');
		if(!$this->id){
			$interface_form->set_label($msg['admin_nomenclature_family_form_add']);
		}else{
			$interface_form->set_label($msg['admin_nomenclature_family_form_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_object_type('family')
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->name." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('nomenclature_families')
		->set_field_focus('name');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
		global $name;
		
		$this->name = stripslashes($name);
	}
	
	public function save() {
		global $msg;
		
		$fields="
			family_name='".addslashes($this->name)."'
		";
		if(!$this->id){ // Ajout
			$requete="select max(family_order) as ordre from nomenclature_families";
			$resultat=pmb_mysql_query($requete);
			$ordre_max=@pmb_mysql_result($resultat,0,0);
			$req="INSERT INTO nomenclature_families SET $fields, family_order=".($ordre_max+1);
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE nomenclature_families SET $fields where id_family=".$this->id;
			pmb_mysql_query($req);
		}
		print display_notification($msg['account_types_success_saved']);
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from nomenclature_musicstands WHERE musicstand_famille_num=".$id;
			pmb_mysql_query($req);
			$req="DELETE from nomenclature_families WHERE id_family=".$id;
			pmb_mysql_query($req);
		}
		return true;
	}
	
	/**
	 * Méthode d'ajout d'un pupitre de la liste
	 *
	 * @param nomenclature_musicstand musicstand Pupitre à  ajouter à  la liste des pupitres
	
	 * @return void
	 * @access public
	 */
	public function add_musicstand( $musicstand ) {
 		$musicstand->set_family($this);
		$this->musicstands[] = $musicstand;
		
	} // end of member function add_musicstand
	
	/**
	 * Méthode qui indique si la famille est complète et cohérente
	 *
	 * @return bool
	 * @access public
	 */
	public function check( ) {
		return $this->valid;
	} // end of member function check
	
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
	 * @param string name Nom de la famille

	 * @return void
	 * @access public
	 */
	public function set_name( $name ) {
		$this->name = $name;
	} // end of member function set_name

	/**
	 * Getter
	 *
	 * @return nomenclature_musicstand
	 * @access public
	 */
	public function get_musicstands( ) {
		return $this->musicstands;
	} // end of member function get_musicstands

	/**
	 * Setter
	 *
	 * @param nomenclature_musicstand musicstands Tableau des pupitre

	 * @return void
	 * @access public
	 */
	public function set_musicstands( $musicstands ) {
		$this->musicstands = $musicstands;
	} // end of member function set_musicstands
	
	public function get_musicstand($indice){
		return $this->musicstands[$indice];
	}
	
	public function get_id(){
		return $this->id;
	}

	/**
	 * Setter
	 *
	 * @param string abbreviation Nomenclature abrégée
	
	 * @return void
	 * @access public
	 */
	public function set_abbreviation( $abbreviation ) {
		$this->abbreviation = pmb_preg_replace('/\s+/', '', $abbreviation);
	} // end of member function set_abbreviation
	
	/**
	 * Getter
	 *
	 * @return string
	 * @access public
	 */
	public function get_abbreviation( ) {
		return  pmb_preg_replace('/\s+/', '', $this->abbreviation);
	} // end of member function get_abbreviation
	
	/**
	 *  Récupération de l'ordre
	 *
	 * @return int
	 * @access public
	 */
	public function get_order() {
		return $this->order;
	} // end of member function get_abbreviation
	
	
	/**
	 * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
	 *
	 * @return void
	 * @access public
	 */
	public function calc_abbreviation( ) {
		$tmusicstands = array();
		if(is_array($this->musicstands)) {
			foreach ($this->musicstands as $musicstand) {
				$nomenclature_musicstand = nomenclature_musicstand::get_instance($musicstand->get_id());
				$nomenclature_musicstand->calc_abbreviation();
				$tmusicstands[] = $nomenclature_musicstand->get_abbreviation();
			}
		}
		$this->set_abbreviation(implode(".", $tmusicstands));
	} // end of member function calc_abbreviation
	
	public static function get_instance($id, $force_new_instance = false) {
		if ($force_new_instance && isset(static::$instances[$id])) {
			unset(static::$instances[$id]);
        }
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new nomenclature_family($id);
		}
		return static::$instances[$id];
	}
	
} // end of nomenclature_family
