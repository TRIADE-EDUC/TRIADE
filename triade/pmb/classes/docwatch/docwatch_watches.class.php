<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_watches.class.php,v 1.9 2022/02/11 09:39:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/docwatch_root.class.php");
require_once($class_path."/docwatch/docwatch_watch.class.php");

/**
 * class docwatch_watches
 * 
 */
class docwatch_watches extends docwatch_root{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	public $id;
	public $type="category";
	public $title;
	public $num_parent;
	public $children = array();
	public $watches = array();
	
	/**
	 * analyse_query par veille
	 * @var array
	 */
	public static $aq_members = array();
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id) {
		$this->id = intval($id);
		$this->fetch_datas();
	} // end of member function __construct
	
	
	/**
	 * Fetch datas
	 * 
	 */
	public function fetch_datas(){
		global $PMBuserid;
		
		if ($this->id) {
			$query = "select category_title, category_num_parent from docwatch_categories where id_category=".$this->id;
			$result = pmb_mysql_query($query);
			if ($row = pmb_mysql_fetch_object($result)) {
				$this->title = $row->category_title;
				$this->num_parent = $row->category_num_parent;
			}
		} else {
			$this->title = "Racine";
			$this->num_parent = -1;
		}
		$query = "select id_watch from docwatch_watches where watch_num_category=".$this->id;
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$docwatch_watch = new docwatch_watch($row->id_watch);
			//Gestion des droits utilisateurs (on affiche uniquement les veilles paramétrées pour le current user)
			if(in_array(SESSuserid,$docwatch_watch->get_allowed_users()) || ($PMBuserid==1)){
				$this->watches[] = $docwatch_watch->get_informations();
			}
		}
		$query = "select id_category from docwatch_categories where category_num_parent=".$this->id;
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$this->children[] = new docwatch_watches($row->id_category);
		}
	}
	
	public static function contains_boolean_expression($item_id, $watch_id) {
		$contains = true;
		$item_id = intval($item_id);
		$watch_id = intval($watch_id);
		$query = "select watch_boolean_expression from docwatch_watches where id_watch =".$watch_id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		if($row->watch_boolean_expression != '') {
			if(!isset(static::$aq_members[$watch_id])) {
				$aq=new analyse_query($row->watch_boolean_expression);
				if (!$aq->error) {
					static::$aq_members[$watch_id]=$aq->get_query_members("docwatch_items","item_index_wew","item_index_sew","id_item");
				} else {
					static::$aq_members[$watch_id]=false;
				}
			}
			if(is_array(static::$aq_members[$watch_id])) {
				$query = "select id_item from docwatch_items where id_item=".$item_id." and ".static::$aq_members[$watch_id]["where"]." ";
				$result = pmb_mysql_query($query);
				if($result) {
					if(!pmb_mysql_num_rows($result)) {
						$contains = false;
					}
				}
			}
		}
		return $contains;
	}

} // end of docwatch_watches
