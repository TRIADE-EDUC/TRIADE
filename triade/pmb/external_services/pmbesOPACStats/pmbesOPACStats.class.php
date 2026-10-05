<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACStats.class.php,v 1.8 2023/09/22 07:34:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/consolidation.class.php");

class pmbesOPACStats extends external_services_api_class {
	
	public function listView($OPACUserId=-1) {
	    if(!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
		$list = array();
		
		$query = "SELECT id_vue, date_consolidation, nom_vue, comment FROM statopac_vues";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_assoc($result)) {
		    $list[] = array(
				"id_vue" => $row["id_vue"],
				"date_consolidation" => $row["date_consolidation"],
				"nom_vue" => $row["nom_vue"],
				"comment" => $row["comment"],
			);
		}
		return $list;
	}
	
	public function getView($id_view) {
	    if(!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
	    $id_view = intval($id_view);
	    if (!$id_view) {
	        throw new Exception("Missing parameter: id_view");
	    }
		$data = array();
		$query = "SELECT id_vue, date_consolidation, nom_vue, comment FROM statopac_vues where id_vue=".$id_view;
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_assoc($result)) {
		    $data[] = array(
				"id_vue" => $row["id_vue"],
				"date_consolidation" => $row["date_consolidation"],
				"nom_vue" => $row["nom_vue"],
				"comment" => $row["comment"],
			);
		}
		return $data;
	}
	
	public function getStatopacView($id_view) {
	    if(!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
		$id_view = intval($id_view);
		$data = array();
		$query = "select * from statopac_vue_".$id_view;
		$result = pmb_mysql_query($query);
		if ($result) {
		    while ($row = pmb_mysql_fetch_assoc($result)) {
			    $data[] = $row;
			}	
		}
		return $data;
	}
	
	public function makeConsolidation($conso,$date_deb,$date_fin,$date_ech, $list_ck) {
	    if(!$this->has_user_rights(ADMINISTRATION_AUTH)) {
	        return array();
	    }
	    $consolidation = new consolidation($conso,$date_deb,$date_fin,$date_ech, $list_ck);
	    return $consolidation->make_consolidation();
	}
}