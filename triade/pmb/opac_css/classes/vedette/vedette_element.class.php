<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_element.class.php,v 1.3 2018-05-16 14:18:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_cache.class.php");

/**
 * class vedette_element
 * un élément d'une vedette composee : une instance d'une autorité ou d'une notice
 */
abstract class vedette_element {

	/**
	 * Type de l'élément
	 * @var unknown_type
	 */
	protected $type;
	
	/**
	 * Identifiant de l'élément (URI dans le cas d'un concept)
	 * @var int or string
	 */
	protected $id;
	
	/**
	 * Libellé à afficher
	 * @var unknown_type
	 */
	protected $isbd;
	
	/**
	 * Instance de la classe PMB de l'autorité ou de la notice
	 * @access private
	 */
	protected $element;
	
	/**
	 * Identifiant de l'élément dans la base
	 * @var int
	 */
	protected $db_id;
	
	/**
	 * Construit un element de la vedette
	 *
	 * @param int type Type d'élément : auteur, titre uniforme, concept, notice, etc.
	 * @param int id Identifiant de l'élément (id d'autorité ou de notice)
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct($type, $id, $isbd = ""){
		$this->type = $type;
		$this->id=$id;
		$this->isbd = $isbd;
		$this->fetch_datas_cache();
	}
	
	/**
	 * Methode permettant de construire l'objet element dans la classe
	 * A ré-implémenter pour faire un new concept ou une new notice ...
	 */
	public abstract function set_vedette_element_from_database();
	
	/**
	 * Retourne l'identifiant de l'élément. Pas nécessairement un entier (une URI pour un concept).
	 * @return unknown_type
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Retourne l'identifiant en base de l'élément
	 * @return int
	 */
	public function get_db_id(){
		if (!$this->db_id) {
			$this->db_id = $this->id;
		}
		return $this->db_id;
	}
	
	public function get_type(){
		return $this->type;
	}
	
	public function get_isbd(){
		return $this->isbd;
	}
	
	public function get_element(){
		return $this->element;
	}
	
	public static function search_vedette_element_ui_class_name($vedette_element_class_name){
		if(class_exists($vedette_element_class_name.'_ui')){
			return $vedette_element_class_name.'_ui';
		}else{
			return 'vedette_element_ui';
		}
	}
	
	protected function fetch_datas_cache(){
		$tmp=vedette_cache::get_at_vedette_cache($this);
		if($tmp){
			$this->restore($tmp);
		}else{
			$this->set_vedette_element_from_database();
			vedette_cache::set_at_vedette_cache($this);
		}
	}
	
	protected function restore($vedette_object){
		foreach(get_object_vars($vedette_object) as $propertieName=>$propertieValue){
			$this->{$propertieName}=$propertieValue;
		}
	}
	
	public function get_lien_opac() {
		global $liens_opac;
		global $use_opac_url_base, $opac_url_base;
		
		if($use_opac_url_base) return $opac_url_base.str_replace('./', '', $liens_opac[$this->key_lien_opac]);
		return $liens_opac[$this->key_lien_opac];
	}
}
