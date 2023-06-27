<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: password.class.php,v 1.1 2015-06-02 13:24:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class password
 * 
 */
class password {

  	public function __construct() {  		
  		 
  	}

  	public static function gen_salt_base() {
  		global $opac_empr_password_salt;
  		$salt=md5(str_replace(array(" ","0."),"",microtime()));
  		$query = "update parametres set valeur_param='".$salt."'
  				where type_param='opac' and sstype_param='empr_password_salt'";
  		$result = pmb_mysql_query($query);
  		if ($result) {
  			$opac_empr_password_salt = $salt;
  			return true;
  		} else {
  			return false;
  		}
  	}
  	
	public static function gen_hash($password,$salt) {
  		global $opac_empr_password_salt;

  		return crypt($password.$opac_empr_password_salt.$salt,substr($opac_empr_password_salt, 0, 2));
  	}

} // end of class password
