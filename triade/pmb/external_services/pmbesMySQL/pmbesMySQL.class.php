<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesMySQL.class.php,v 1.5 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesMySQL extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	/*
	 * @param CHECK ANALYZE REPAIR OPTIMIZE
	 */
	public function mysqlTable($action) {
		global $pmb_set_time_limit, $dbh;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$result=array();
			
			if($action) {	
				@set_time_limit($pmb_set_time_limit);
				$db = DATA_BASE;
				$tables = pmb_mysql_list_tables($db);
				$num_tables = @pmb_mysql_num_rows($tables);
			
				$i = 0;
				while($i < $num_tables) {
					$table[$i] = pmb_mysql_tablename($tables, $i);
					$i++;
				}
	
				foreach ($table as $cle => $valeur) {
					$requete = $action." TABLE ".$valeur." ";
					$res = @pmb_mysql_query($requete, $dbh);
					$nbr_lignes = @pmb_mysql_num_rows($res);
	
					if($nbr_lignes) {			
						for($i=0; $i < $nbr_lignes; $i++) {
							$row = pmb_mysql_fetch_row($res);
							$tab = array();
							foreach($row as $dummykey=>$col) {
								if(!$col) $col="&nbsp;";
									$tab[$dummykey] = $col;	
							}
							$result[] = $tab;
						}
					}	
				}
			}	
			return $result;
		} else {
			return array();
		}
	}
}




?>