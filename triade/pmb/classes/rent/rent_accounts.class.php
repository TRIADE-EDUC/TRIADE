<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_accounts.class.php,v 1.32 2021/04/08 07:01:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/rent/rent_root.class.php");
require_once($class_path."/rent/rent_account.class.php");

class rent_accounts extends rent_root {
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_account from rent_accounts';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new rent_account($row->id_account);
			}
		}
	}
}