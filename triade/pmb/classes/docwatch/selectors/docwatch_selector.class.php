<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector.class.php,v 1.12 2023/09/20 13:54:01 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/docwatch_root.class.php");

/**
 * class docwatch_selector
 * 
 */
class docwatch_selector extends docwatch_root{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/


	/**
	 * Identifiant du selecteur dans la base
	 * @access protected
	 */
	
	protected $id;
	
	/**
	 * Identifiant de la datasource
	 */
	protected $num_datasource;
	
	/**
	 * Paramètres du selecteur
	 */
	protected $parameters;
	
	/**
	 * Valeur
	 */
	protected $value;
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
	    $this->id = (int) $id;
		$this->fetch_datas();
		parent::__construct($id);
	} // end of member function __construct
	
	/**
	 *
	 *
	 * @return void
	 * @access public
	 */
	protected function fetch_datas(){
		$this->parameters = array();
		$this->num_datasource= 0;
		if($this->id){
			$query = "select * from docwatch_selectors where id_selector = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->num_datasource = $row->selector_num_datasource;
				$this->unserialize($row->selector_parameters);
			}
		}
	} // end of member function fetch_datas
	
	/**
	 * Formulaire du sélecteur
	 *
	 * @return string
	 * @access public
	 */
	public function get_form(){
		$form ="";

		return $form;
	} // end of member function get_form

	/**
	 *
	 *
	 * @return void
	 * @access public
	 */
	public function set_from_form( ) {
	
	} // end of member function set_from_form
		
	/**
	 * Sauvegarde des propriétés
	 *
	 * @return void
	 * @access public
	 */
	public function save() {
		if($this->id){
			$query = "update docwatch_selectors set";
			$clause = " where id_selector=".$this->id;
		}else{
			$query = "insert into docwatch_selectors set";
			$clause = "";
		}
		$query.= "
			selector_type = '".addslashes(get_class($this))."',
			selector_num_datasource = '".addslashes($this->num_datasource)."',
			selector_parameters = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		}else{
			return false;
		}
	} // end of member function save
	
	public function delete(){
		if($this->id){
			$query = "delete from docwatch_selectors where id_selector = ".$this->id;
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function get_num_datasource(){
	  return $this->num_datasource;
	}
	
	public function set_num_datasource($num_datasource){
	  $this->num_datasource = $num_datasource;
	}
	    
	public function get_id(){
	  return $this->id;
	}
	
	public function set_id($id){
	  $this->id = $id;
	}

	public function get_value(){
		return $this->value;
	}

	public function get_parameters()
	{
		return $this->parameters;
	}
	

} // end of docwatch_selector
