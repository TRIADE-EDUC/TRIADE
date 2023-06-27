<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesDocwatches.class.php,v 1.3 2017-06-22 08:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/docwatch/docwatch_watch.class.php");

class pmbesDocwatches extends external_services_api_class {

	public function restore_general_config() {

	}

	public function form_general_config() {
		return false;
	}

	public function save_general_config() {

	}
	
	public function update(){		
		global $dbh;
		$docwatchesUpdated = array();
		$query = "select id_watch from docwatch_watches";
		$result = pmb_mysql_query($query, $dbh);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)){				
				$docwatch_watch = new docwatch_watch($row->id_watch);				
				$docwatch_watch->sync();				
				$docwatchesUpdated[$docwatch_watch->get_id()] = $docwatch_watch->get_synced_datasources();
								
			}
		}
		return $docwatchesUpdated;		
	}
	
}