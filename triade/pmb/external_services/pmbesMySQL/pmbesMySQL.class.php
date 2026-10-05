<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesMySQL.class.php,v 1.8 2023/09/22 07:34:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");

class pmbesMySQL extends external_services_api_class {
	
	/*
	 * @param CHECK ANALYZE REPAIR OPTIMIZE
	 */
	public function mysqlTable($action) {
		global $pmb_set_time_limit;
		
		if(!$this->has_user_rights(ADMINISTRATION_AUTH)) {
		    return array();
		}
		$data=array();
		if(!empty($action) && in_array(strtoupper($action), array('CHECK', 'ANALYZE', 'REPAIR', 'OPTIMIZE'))) {	
			@set_time_limit($pmb_set_time_limit);
			$db = DATA_BASE;
			$tables = pmb_mysql_list_tables($db);
			$num_tables = pmb_mysql_num_rows($tables);
		
			$table = array();
			$i = 0;
			while($i < $num_tables) {
				$table[$i] = pmb_mysql_tablename($tables, $i);
				$i++;
			}

			foreach ($table as $valeur) {
				$query = $action." TABLE ".$valeur." ";
				$result = pmb_mysql_query($query);
				$nbr_lignes = pmb_mysql_num_rows($result);
				if($nbr_lignes) {			
					for($i=0; $i < $nbr_lignes; $i++) {
					    $row = pmb_mysql_fetch_row($result);
						$tab = array();
						foreach($row as $dummykey=>$col) {
							if(!$col) $col="&nbsp;";
								$tab[$dummykey] = $col;	
						}
						$data[] = $tab;
					}
				}	
			}
		}	
		return $data;
	}
}