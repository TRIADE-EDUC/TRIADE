<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family.class.php,v 1.8 2016-02-15 14:09:40 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class nomenclature_family
 * Représente une famille dans une nomenclature
 */
class nomenclature_family{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

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
	 * Constructeur
	 *
	 * @param string name Nom de la famille
	 
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
		if($this->id){
			//le nom de la famille
			$query = "select family_name, family_order from nomenclature_families where id_family = ".$this->id;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->set_name($row->family_name);
					$this->order = $row->family_order;
				}
				//récupération des pupitres
				$query = "select id_musicstand from nomenclature_musicstands where musicstand_famille_num = ".$this->id." order by musicstand_order asc";
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->add_musicstand(new nomenclature_musicstand($row->id_musicstand));
					}
				}
			}
		}else{
			$this->musicstands =array();
			$this->name = "";
			$this->order = "";
		}
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
				$nomenclature_musicstand = new nomenclature_musicstand($musicstand->get_id());
				$nomenclature_musicstand->calc_abbreviation();
				$tmusicstands[] = $nomenclature_musicstand->get_abbreviation();
			}
		}
		$this->set_abbreviation(implode(".", $tmusicstands));
	} // end of member function calc_abbreviation
	
} // end of nomenclature_family
