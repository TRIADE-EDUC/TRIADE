<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: is_different_from_login.class.php,v 1.1 2020/11/20 15:31:56 dbellamy Exp $

require_once __DIR__."/../check_password_interface.class.php";

class is_different_from_login implements check_password_interface {
	
	public static function check(int $id, string $password, array $form_values) : bool {
		
		$login = '';
		if ( !empty($id) && empty($form_values['login']) ) {
			
			$login =  static::get_value($id);
			
		} elseif ( !empty($form_values['login']) )  {
			
			$login = $form_values['login'];
		}
		
		if($login == $password) {
			return false;
		}
		return true;
	}

	
	public static function get_value(int $id) {
		
		$login = '';
		if ( !empty($id) ) {
			
			$q = "select empr_login from empr where id_empr=".$id;
			$r = pmb_mysql_query($q);
			if(pmb_mysql_num_rows($r)) {
				$login = pmb_mysql_result($r, 0, 0);
			}
		}
		return $login;
	}
	
	
}