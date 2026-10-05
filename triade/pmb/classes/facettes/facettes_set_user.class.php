<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_set_user.class.php,v 1.2 2024/03/21 15:24:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_set_user {
	protected $id;
	protected $type = 'notices';
	protected $num_user = 0;
	protected $visible = 0;
	protected $ranking = 0;
	
	
	public function __construct($id=0){
	    global $PMBuserid;
	    
		$this->id = intval($id);
		$this->num_user = $PMBuserid;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		if($this->id) {
		    $query = "SELECT * FROM facettes_sets WHERE id_set=".$this->id;
		    $result = pmb_mysql_query($query);
		    $row = pmb_mysql_fetch_object($result);
		    $this->type = $row->type;
		    
			$query = "SELECT * FROM facettes_sets_users WHERE num_set=".$this->id." AND num_user =".$this->num_user;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
    			$row = pmb_mysql_fetch_object($result);
    			$this->visible = $row->visible;
    			$this->ranking = $row->ranking;
			}
		}
	}
	
	protected function init(){
	    $facettes_sets = new facettes_sets($this->type);
	    $filtered_list = $facettes_sets->get_filtered_list($this->num_user);
	    foreach($filtered_list as $ranking=>$facette_set) {
	        $query = "SELECT ranking FROM facettes_sets_users WHERE num_set = ".$facette_set->get_id()." AND num_user = ".$facette_set->get_num_user();
	        $result = pmb_mysql_query($query);
	        if(pmb_mysql_num_rows($result)) {
	            $query = "UPDATE facettes_sets_users SET ranking='".($ranking+1)."' WHERE num_set = ".$facette_set->get_id()." AND num_user = ".$facette_set->get_num_user();
	            pmb_mysql_query($query);
	        } else {
	            $query = "INSERT INTO facettes_sets_users SET num_set = ".$facette_set->get_id().", num_user = ".$facette_set->get_num_user().", ranking='".($ranking+1)."'";
	            pmb_mysql_query($query);
	        }
	    }
	    $this->fetch_data();
	}
	
	public function up(){
	    $this->init();
	    $query = "select max(ranking) as ranking from facettes_sets_users join facettes_sets on num_set=id_set and facettes_sets_users.num_user=".$this->num_user." where type LIKE '".$this->type."%' and ranking < ".$this->ranking;
	    $resultat = pmb_mysql_query($query);
	    $ordre_max = pmb_mysql_result($resultat,0,0);
	    if ($ordre_max) {
	        $query = "select id_set from facettes_sets_users join facettes_sets on num_set=id_set and facettes_sets_users.num_user=".$this->num_user." where type LIKE '".$this->type."%' and ranking=$ordre_max limit 1";
	        $resultat = pmb_mysql_query($query);
	        $id_set_max = pmb_mysql_result($resultat,0,0);
	        $query = "update facettes_sets_users set ranking='".$ordre_max."' where num_set=".$this->id." and num_user=".$this->num_user;
	        pmb_mysql_query($query);
	        $query = "update facettes_sets_users set ranking='".$this->ranking."' where num_set=".$id_set_max." and num_user=".$this->num_user;
	        pmb_mysql_query($query);
	    }
	}
	
	public function down(){
	    $this->init();
	    $query = "select min(ranking) as ranking from facettes_sets_users join facettes_sets on num_set=id_set and facettes_sets_users.num_user=".$this->num_user." where type LIKE '".$this->type."%' and ranking > ".$this->ranking;
	    $resultat = pmb_mysql_query($query);
	    $ordre_min = pmb_mysql_result($resultat,0,0);
	    if ($ordre_min) {
	        $query = "select id_set from facettes_sets_users join facettes_sets on num_set=id_set and facettes_sets_users.num_user=".$this->num_user." where type LIKE '".$this->type."%' and ranking=$ordre_min limit 1";
	        $resultat = pmb_mysql_query($query);
	        $id_set_min = pmb_mysql_result($resultat,0,0);
	        $query = "update facettes_sets_users set ranking='".$ordre_min."' where num_set=".$this->id." and num_user=".$this->num_user;
	        pmb_mysql_query($query);
	        $query = "update facettes_sets_users set ranking='".$this->ranking."' where num_set=".$id_set_min." and num_user=".$this->num_user;
	        pmb_mysql_query($query);
	    }
	}
	
	public static function delete($id=0) {
	    $id = intval($id);
		if($id) {
			$query = "DELETE FROM facettes_sets_users WHERE num_set=".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_type(){
	    return $this->type;
	}
	
	public function get_num_user(){
	    return $this->num_user;
	}
	
	public function get_visible(){
	    return $this->visible;
	}
	
	public function set_id($id) {
	    $this->id = intval($id);
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
}

