<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_item.class.php,v 1.6 2019/06/13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_cataloging_item {
	
	protected $id;
	
	protected $type;
	
	protected $num_user;
	
	protected $added_date;
	
	protected $num_datanode;
	
	/**
	 * Constructeur
	 */
	public function __construct($id, $type, $num_datanode=0) {
	    $this->id = (int) $id;
		$this->type = $type;
		$this->num_datanode = (int) $num_datanode;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $PMBuserid;
		$this->num_user = $PMBuserid;
		$this->added_date = today();
		$query = "select cataloging_item_num_user, cataloging_item_added_date from frbr_cataloging_items where num_cataloging_item =".$this->id." and type_cataloging_item ='".$this->type."' and cataloging_item_num_datanode ='".$this->num_datanode."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->num_user = $row->cataloging_item_num_user;
			$this->added_date = $row->cataloging_item_added_date;
		}
	}
	
	public function save() {
		$query = 'insert into frbr_cataloging_items set 
			num_cataloging_item = "'.$this->id.'",
			type_cataloging_item = "'.$this->type.'",
			cataloging_item_num_user = "'.$this->num_user.'",
			cataloging_item_added_date = "'.$this->added_date.'",
			cataloging_item_num_datanode = "'.$this->num_datanode.'"';
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public function delete() {
		$query = "delete from frbr_cataloging_items where num_cataloging_item =".$this->id." and type_cataloging_item ='".$this->type."'";
		pmb_mysql_query($query);
	}
	
	public function get_display() {
		//Retourne l'affichage de l'élément en fonction de son type
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function set_num_datanode($num_datanode) {
	    $this->num_datanode = (int) $num_datanode;
	}
}