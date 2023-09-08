<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_filters.class.php,v 1.5 2017-01-31 16:25:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class opac_filters {
	
	public $catalog=array();			//Liste des filtres declares
	
	//Constructeur
	public function __construct($id_vue) {
		$this->id_vue=$id_vue;
    	$this->fetch_data();
	}
	  
    public function fetch_data() {
 		global $dbh;
    			
		$this->params=array();
		$req="SELECT * FROM opac_filters where opac_filter_view_num=".$this->id_vue;
		$myQuery = pmb_mysql_query($req, $dbh);
		if(pmb_mysql_num_rows($myQuery)){		
			while(($r=pmb_mysql_fetch_object($myQuery))) {		
				$param=unserialize($r->opac_filter_param);
				$this->params[$r->opac_filter_path]=$param["selected"];
			}
		}
    }
    
    public function is_selected($path, $id_ask) {
 		return in_array($id_ask, $this->params[$path]);
    }	
}
?>