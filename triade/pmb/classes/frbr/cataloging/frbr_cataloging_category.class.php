<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_category.class.php,v 1.4 2022/02/14 13:41:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class frbr_cataloging_category
 * 
 */
class frbr_cataloging_category{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Identifiant du classement
	 * @access public
	 */
	protected $id;

	/**
	 * Nom du classement
	 * @access public
	 */
	protected $title;

	/**
	 * Classement parent
	 * @access public
	 */
	protected $parent;

	/**
	 * 
	 * @access public
	 */
	protected $children;
	
	/**
	 * 
	 * @access protected
	 */
	protected $error;

	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
	    $this->id = (int) $id;
		$this->fetch_data();
	
	}

	protected function fetch_data(){
		$this->title = "";
		$this->parent = 0;
		$this->children = array();
		if($this->id){
			$query = "select * from frbr_cataloging_categories where id_cataloging_category = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->cataloging_category_title;
				$this->parent = $row->cataloging_category_num_parent;
			}else{
				$this->id=0;
			}
		}
	}
	
	public function set_properties_from_form() {
		global $category_parent;
		global $category_title;
		
		$this->parent = (int) $category_parent;
		$this->title = stripslashes($category_title);
	}
	
	public function save(){
		if($this->id){
			$query = "update frbr_cataloging_categories set ";
			$clause = " where id_cataloging_category = '".$this->id."'";
		}else{
			$query = "insert into frbr_cataloging_categories set ";
			$clause = "";
		}
		$query.="cataloging_category_title='".addslashes($this->title)."',";
		$query.="cataloging_category_num_parent='".addslashes($this->parent)."'";
		$result = pmb_mysql_query($query.$clause);
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			return  true;
		}
		return false;
	}
	
	public function delete(){
		global $msg;
		if($this->id){
			//pas de jeu de données sur cette catég?
			$query = "select cataloging_datanode_num_category from frbr_cataloging_datanodes where cataloging_datanode_num_category = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->error = $msg['frbr_cataloging_category_error_datanode_associated'];
				return false;
			}else{
				//des sous-catég?
				$query = "select id_cataloging_category from frbr_cataloging_categories where cataloging_category_num_parent = '".$this->id."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->error = $msg['frbr_cataloging_category_error_sub_categ'];
					return false;
				}else{
					$query = "delete from frbr_cataloging_categories where id_cataloging_category = '".$this->id."'";
					$result = pmb_mysql_query($query);
					if($result){
						return true;
					}else{
						$this->error = $msg['frbr_cataloging_category_error_database'];
						return false;
					}
				}
			}
		}else{
			$this->error = $msg['frbr_cataloging_category_error_dont_exist'];
		}
	}
	
	public function get_children() {
		if(!count($this->children)){
			$query = "select id_cataloging_category from frbr_cataloging_categories where category_num_parent = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row=pmb_mysql_fetch_object($result)){
					$this->children[] = $row->id_cataloging_category;
				}
			}
		}
		return $this->children;
	}
	public function set_children($children) {
		$this->children = $children;
	}
	
	public function get_title() {
	  return $this->title;
	}
	
	public function set_title($title) {
	  $this->title = $title;
	}
	    
	public function get_id() {
	  return $this->id;
	}
	
	public function set_id($id) {
	  $this->id = intval($id);
	}
	    
	public function get_parent() {
	  return $this->parent;
	}
	
	public function set_parent($parent) {
	  $this->parent = intval($parent);
	}
	
	public function get_error(){
		return $this->error;
	}
} // end of frbr_cataloging_category
