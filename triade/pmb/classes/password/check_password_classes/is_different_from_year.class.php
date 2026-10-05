<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: is_different_from_year.class.php,v 1.1 2020/11/20 15:31:56 dbellamy Exp $

require_once __DIR__."/../check_password_interface.class.php";

class is_different_from_year implements check_password_interface {
	
	public static function check(int $id, string $password, array $form_values) : bool {

		$year = '';
		if ( !empty($id) && empty($form_values['year']) ) {
			
			$year = static::get_value($id);
			
		} elseif ( !empty($form_values['year']) ) {
			
			$year = $form_values['year'];
		}
		
		if($year == $password) {
			return false;
		}
		return true;
	}

	
	public static function get_value(int $id) {
		
		$year = '';
		if ( !empty($id) ) {
			
			$q = "select empr_year from empr where id_empr=".$id;
			$r = pmb_mysql_query($q);
			if(pmb_mysql_num_rows($r)) {
				$year = pmb_mysql_result($r, 0, 0);
			}
		}
		return $year;
	}
	
}