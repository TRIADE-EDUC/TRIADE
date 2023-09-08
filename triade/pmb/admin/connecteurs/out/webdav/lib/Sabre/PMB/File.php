<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: File.php,v 1.6 2015-04-03 11:16:24 jpermanne Exp $
namespace Sabre\PMB;

use Sabre\DAV;

class File extends DAV\File {
	
	function get_code_from_name($name){
		return substr($name,strrpos($name,"(")+1,(strrpos($name,")")-strrpos($name,"("))-1);
	}
	
	function set_parent($parent){
		$this->parentNode = $parent;
	}

	function getName() {
		return "";
	}

	function get() {
		return "";
	}

	function getSize() {
		return 0;
	}

	function getETag() {
		if(file_exists(time())){
			return '"' . md5_file(time()) . '"';
		}else{
			return '"' . md5(time()) . '"';
		}
		
	}
	
    function check_write_permission(){
    	global $webdav_current_user_id;
    	if($this->config['write_permission']){
    		$tab = array();
    		$query = "";
    		switch($this->config['authentication']){
    			case "gestion" :
    				$tab = $this->config['restrcited_user_write_permission'];
    				$query = "select grp_num from users where userid = ".$webdav_current_user_id;
    				break;
    			case "opac" :
    				$query = "select empr_categ from empr where id_empr = ".$webdav_current_user_id;
    			case "anonymous" : 
    			default :
    				$tab = $this->config['restrcited_empr_write_permission'];
    				break;
    		}
    		//pas de restriction, on est bon
    		if(!count($tab)){
    			return true;
    		}elseif($query != ""){
    			//on doit s'assurer que la personne connectée est dispose des droits...
    			$result = pmb_mysql_query($query);
    			if(pmb_mysql_num_rows($result)){
    				if(in_array(pmb_mysql_result($result,0,0),$tab)){
    					return true;
    				}
    			}
    		} 
    	}
    	//si on est encore dans la fonction, c'est qu'on correspond à aucun critère !
    	return false;
    }	
}