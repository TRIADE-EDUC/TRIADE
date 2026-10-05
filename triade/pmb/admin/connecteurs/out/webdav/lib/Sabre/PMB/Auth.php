<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Auth.php,v 1.8 2022/01/10 08:31:27 dgoron Exp $
namespace Sabre\PMB;

use Sabre\DAV;

//class Sabre_PMB_Auth extends Sabre_DAV_Auth_Backend_AbstractBasic {
//	protected $mode;
//	
//	public function __construct($mode){
//		$this->mode = $mode;
//	}
//	
//	public function validateUserPass($username,$password){
//		global $webdav_current_user_id,$webdav_current_user_name;
//		
//		switch($this->mode){
//			case "gestion" :
//				if($username && $password){
//					$query = "SELECT userid, username FROM users WHERE username='$username' AND pwd=password('$password') ";
//					$result = pmb_mysql_query($query);
//					if(pmb_mysql_num_rows($result) && pmb_mysql_result($result,0,0)>0){
//						$webdav_current_user_id= pmb_mysql_result($result,0,0);
//						$webdav_current_user_name = pmb_mysql_result($result,0,1);
//						return true;
//					}
//				}	
//			break;
//			case "opac" :
//				//TODO vérification abonnement...
//				if($username && $password){
//					$query ="select id_empr, concat(empr_nom,' ',empr_prenom) from empr where empr_login='".$username."' and empr_password='".$password."'";
//					$result = pmb_mysql_query($query);
//					if(pmb_mysql_num_rows($result) && pmb_mysql_result($result,0,0)>0){
//						$webdav_current_user_id= pmb_mysql_result($result,0,0);
//						$webdav_current_user_name = pmb_mysql_result($result,0,1);
//						return true;
//					}
//				}	
//				break;
//		}
//		return false;
//	}
//}

class Auth extends DAV\Auth\Backend\AbstractDigest {
	protected $mode;
	
	public function __construct($mode){
	    global $pmb_url_base;
		$this->mode = $mode;
		$this->realm = md5($pmb_url_base);
	}
	
    public function getDigestHash($realm,$username) {
		global $webdav_current_user_id,$webdav_current_user_name;
		global $base_path;
		
		switch($this->mode){
			
			case "gestion" :
				$query = "SELECT user_digest, userid, username FROM users WHERE username='$username'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$webdav_current_user_id= pmb_mysql_result($result,0,1);
					$webdav_current_user_name = pmb_mysql_result($result,0,2);
					return pmb_mysql_result($result,0,0);
				}
				break;
			case "opac" :
				$ext_auth=false;				
				if(file_exists($base_path.'/opac_css/includes/ext_auth.inc.php')) {
					$q ="select empr_digest, id_empr, concat(empr_nom,' ',empr_prenom) as empr_name from empr where empr_login='".$username."'";
					$r = pmb_mysql_query($q);
					if(pmb_mysql_num_rows($r)){
						$row = pmb_mysql_fetch_object($r);
						if ($row->empr_digest) {
							$ext_auth=true;
							$webdav_current_user_id= $row->id_empr;
							$webdav_current_user_name = $row->empr_name;
							return $row->empr_digest;
						}
					}								
				} 
				if ($ext_auth==false) {
					$q ="select empr_digest, id_empr, concat(empr_nom,' ',empr_prenom) as empr_name from empr where empr_login='".$username."'";
					$r = pmb_mysql_query($q);
					if(pmb_mysql_num_rows($r)){
						$row = pmb_mysql_fetch_object($r);
						if ($row->empr_digest) {
							$webdav_current_user_id= $row->id_empr;
							$webdav_current_user_name = $row->empr_name;
							return $row->empr_digest;
						}
					}
				}
				break;
		}
		return false;
    }   
    
}
