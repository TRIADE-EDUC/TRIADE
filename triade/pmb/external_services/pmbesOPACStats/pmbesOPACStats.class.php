<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACStats.class.php,v 1.3 2017-06-22 08:49:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/consolidation.class.php");

class pmbesOPACStats extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	public function listView($OPACUserId=-1) {
		global $dbh;

		if (SESSrights & ADMINISTRATION_AUTH) {
			$result = array();
			
			$requete = "SELECT id_vue, date_consolidation, nom_vue, comment FROM statopac_vues";
			$res = pmb_mysql_query($requete, $dbh);
		
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = array(
					"id_vue" => $row["id_vue"],
					"date_consolidation" => $row["date_consolidation"],
					"nom_vue" => $row["nom_vue"],
					"comment" => $row["comment"],
				);
	
			}
			return $result;
		} else {
			return array();
		}
	}
	
	public function getView($id_view) {
		global $dbh;
		global $msg;
		$result = array();

		$id_view += 0;
		if (!$id_view)
			throw new Exception("Missing parameter: id_view");
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$requete = "SELECT id_vue, date_consolidation, nom_vue, comment FROM statopac_vues where id_vue=".$id_view;
			$res = pmb_mysql_query($requete, $dbh);
		
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = array(
					"id_vue" => $row["id_vue"],
					"date_consolidation" => $row["date_consolidation"],
					"nom_vue" => $row["nom_vue"],
					"comment" => $row["comment"],
				);
			}
			return $result;
		} else {
			return array();
		}
	}
	
	public function getStatopacView($id_view) {
		global $dbh;

		if (SESSrights & ADMINISTRATION_AUTH) {
			$result = array();
			
			$query = "select * from statopac_vue_".$id_view;
			$res = pmb_mysql_query($query, $dbh);
			if ($res) {
				while ($row = pmb_mysql_fetch_assoc($res)) {
					$result[] = $row;
				}	
			}
			return $result;
		} else {
			return array();
		}			
	}
	
	public function makeConsolidation($conso,$date_deb,$date_fin,$date_ech, $list_ck) {
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$consolidation = new consolidation($conso,$date_deb,$date_fin,$date_ech, $list_ck);
			$consolidation->make_consolidation();
		}
		return "";
	}
	
	
}




?>