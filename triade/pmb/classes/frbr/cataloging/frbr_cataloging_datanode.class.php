<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_datanode.class.php,v 1.4 2019/06/13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_cataloging_datanode {
	
	protected $id;
	
	protected $title;
	
	protected $comment;
	
	protected $owner;
	
	protected $allowed_users;
	
	protected $num_category;
	
	protected $error;
	
	/**
	 * Constructeur
	 */
	public function __construct($id) {
	    $this->id = (int) $id;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $PMBuserid;
		$this->title = '';
		$this->comment = '';
		$this->owner = $PMBuserid;
		$this->allowed_users = array();
		$this->num_category = 0;
		$query = "select cataloging_datanode_title, cataloging_datanode_comment, cataloging_datanode_owner, cataloging_datanode_allowed_users, cataloging_datanode_num_category from frbr_cataloging_datanodes where id_cataloging_datanode =".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->title = $row->cataloging_datanode_title;
			$this->comment = $row->cataloging_datanode_comment;
			$this->owner = $row->cataloging_datanode_owner;
			$this->allowed_users = explode(',', $row->cataloging_datanode_allowed_users);
			$this->num_category = $row->cataloging_datanode_num_category;
		}
	}
	
	public function set_properties_from_form() {
		global $datanode_title;
		global $datanode_comment;
		global $datanode_allowed_users;
		global $datanode_num_category;
		
		$this->title = stripslashes($datanode_title);
		$this->comment = stripslashes($datanode_comment);
		$this->allowed_users = $datanode_allowed_users;
		$this->num_category = (int) $datanode_num_category;
	}
	
	public function save() {
		$query = 'insert into frbr_cataloging_datanodes set 
			id_cataloging_datanode = "'.$this->id.'",
			cataloging_datanode_title = "'.addslashes($this->title).'",
			cataloging_datanode_comment = "'.addslashes($this->comment).'",
			cataloging_datanode_owner = "'.$this->owner.'",
			cataloging_datanode_allowed_users = "'.implode(',', $this->allowed_users).'",
			cataloging_datanode_num_category = "'.$this->num_category.'"';
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function delete() {
		$query = "delete from frbr_cataloging_datanodes where id_cataloging_datanode =".$this->id;
		$result = pmb_mysql_query($query);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function get_informations(){
		$datas = new stdClass();
		$datas->id = $this->id;
		$datas->type = "datanode";
		$datas->title = $this->title;
		$datas->comment = $this->comment;
		$datas->num_category = $this->num_category;
		$datas->owner = $this->owner;
		$datas->allowed_users = $this->allowed_users;
		return $datas;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_allowed_users() {
		return $this->allowed_users;
	}
	
	public function set_num_category($num_category) {
	    $this->num_category = (int) $num_category;
	}
	
	public function get_error(){
		return $this->error;
	}
}