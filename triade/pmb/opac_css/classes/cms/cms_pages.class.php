<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.class.php,v 1.2 2015-04-03 11:16:25 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_page {
	public $id;		// identifiant de l'objet
	public $hash;	// hash de l'objet
	public $name;	// nom
	public $description;	// description
	public $vars= array();	// Variables d'environnement
	
	public function __construct($id=""){
		$this->id= $id+0;		
		if($this->id){
			$this->fetch_data();
		}
	}
	
	protected function fetch_data(){
		$this->hash = "";
		$this->name = "";
		$this->description = "";
		$this->vars= array();
		
		if(!$this->id)	return false;					
		// les infos base...	
		$rqt = "select * from cms_pages where id_page ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$this->hash = $row->page_hash;
			$this->name = $row->page_name;
			$this->description = $row->page_description;
		}		
		// Variables d'environnement
		$rqt = "select * from cms_vars where var_num_page ='".$this->id."' order by var_name";
		$res = pmb_mysql_query($rqt);	
		$i=0;	
		if(pmb_mysql_num_rows($res)){					
			while($row = pmb_mysql_fetch_object($res)){
				$this->vars[$i]['id']=$row->id_var;
				$this->vars[$i]['name']=$row->var_name;
				$this->vars[$i]['comment']=$row->var_comment;
				$i++;
			}	
		}				
	}
	
	function get_env(){	
		return $this->vars;
	}

}// End of class
