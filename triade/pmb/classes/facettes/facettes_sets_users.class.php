<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_sets_users.class.php,v 1.1 2024/03/21 11:06:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class facettes_sets_users {
	
    protected $type;
    
    protected $data;
    
    public function __construct($type='') {
        $this->type = $type;
        $this->fetch_data();
    }
    
    protected function fetch_data() {
        $query = "SELECT id_set FROM facettes_sets";
        if(!empty($this->type)) {
            if($this->type == 'authorities') {
                $authorities_types = static::get_authorities_types();
                $query .= " WHERE (type IN ('".implode("','", $authorities_types)."') OR type LIKE 'authperso%')";
            } else {
                $query .= " WHERE type = '".$this->type."'";
            }
        }
        $query .= " ORDER BY type, name";
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->data[$row->id_set] = new facettes_set($row->id_set);
            }
        }
    }
    
    public static function get_visible($num_set) {
        global $PMBuserid;
        
        $num_set = intval($num_set);
        $query = "SELECT visible FROM facettes_sets_users WHERE num_set = ".$num_set." AND num_user = ".$PMBuserid;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 'visible');
        }
        return 0;
    }
    
    public static function get_ranking($num_set) {
        global $PMBuserid;
        
        $num_set = intval($num_set);
        $query = "SELECT ranking FROM facettes_sets_users WHERE num_set = ".$num_set." AND num_user = ".$PMBuserid;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            return pmb_mysql_result($result, 0, 'ranking');
        }
        return 0;
    }
}

