<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_unused_users_ui.class.php,v 1.1 2022/12/21 08:25:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_acces_profiles_unused_users_ui extends list_acces_profiles_unused_ui {
	
	protected $profile_type = 'user';
	
	protected function get_array_calc() {
		global $chk_prop;
		
		return $this->get_dom()->calcUserProfiles($chk_prop);
	}
	
	protected function _get_query_base() {
		return $this->get_dom()->loadUsedUserProfiles(static::$t_reused);
	}
}