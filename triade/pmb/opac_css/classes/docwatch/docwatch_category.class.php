<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_category.class.php,v 1.3 2021/12/22 14:25:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class docwatch_category
 * 
 */
class docwatch_category{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * Identifiant de la catégorie de classement des veilles
	 * @access public
	 */
	protected $id;

	/**
	 * Nom de la catégorie
	 * @access public
	 */
	protected $title;

	/**
	 * Catgéorie parente
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
	 * @access proptected
	 */
	protected $error;

	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	
	} // end of member function __construct

	public function get_children() {
		if(!count($this->children)){
			$query = "select id_category from docwatch_categories where category_num_parent = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row=pmb_mysql_fetch_object($result)){
					$this->children[] = $row->id_category;
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
	
	public function fetch_data(){
		$this->title = "";
		$this->parent = 0;
		$this->children = array();
		if($this->id){
			$query = "select * from docwatch_categories where id_category = '".$this->id."'";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->category_title;
				$this->parent = $row->category_num_parent;
			}else{
				$this->id=0;
			}
		}
	}
	
	public function save(){
		if($this->id){
			$query = "update docwatch_categories set ";
			$clause = " where id_category = '".$this->id."'";
		}else{
			$query = "insert into docwatch_categories set ";
			$clause = "";
		}
		$query.="category_title='".addslashes($this->title)."',";
		$query.="category_num_parent='".addslashes($this->parent)."'";
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
			//pas de veille sur cette catég?
			$query = "select watch_num_category from docwatch_watches where watch_num_category = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->error = $msg['dsi_docwatch_category_error_watch_associated'];
				return false;
			}else{
				//des sous-catég?
				$query = "select id_category from docwatch_categories where category_num_parent = '".$this->id."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->error = $msg['dsi_docwatch_category_error_sub_categ'];
					return false;
				}else{
					$query = "delete from docwatch_categories where id_category = '".$this->id."'";
					$result = pmb_mysql_query($query);
					if($result){
						return true;
					}else{
						$this->error = $msg['dsi_docwatch_category_error_database'];
						return false;
					}
				}
			}
		}else{
			$this->error = $msg['dsi_docwatch_category_error_dont_exist'];
		}
	}
	
	public function get_error(){
		return $this->error;
	}

	    


} // end of docwatch_category
