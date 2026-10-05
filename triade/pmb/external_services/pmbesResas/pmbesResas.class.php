<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesResas.class.php,v 1.10 2023/03/16 11:01:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services.class.php");
require_once($class_path."/bannette.class.php");

class pmbesResas extends external_services_api_class {
	
	public function list_empr_resas($empr_id) {
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$empr_id = intval($empr_id);
			if (!$empr_id)
				throw new Exception("Missing parameter: empr_id");
		
			$requete  = "SELECT id_resa FROM resa WHERE (resa_idempr='$empr_id')"; 
				
			$res = pmb_mysql_query($requete);
			if ($res)
				while($row = pmb_mysql_fetch_assoc($res)) {
					$result[] = $row["id_resa"];
				}
		
			return $result;
		} else {
			return array();
		}
	}
	
	public function get_empr_information($idempr) {
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$idempr = intval($idempr);
			if (!$idempr)
				throw new Exception("Missing parameter: idempr");
				
			$sql = "SELECT id_empr, empr_cb, empr_nom, empr_prenom FROM empr WHERE id_empr = ".$idempr;
			$res = pmb_mysql_query($sql);
			if (!$res)
				throw new Exception("Not found: idempr = ".$idempr);
			$row = pmb_mysql_fetch_assoc($res);
	
			$result = $row;
			
			return $result;
		} else {
			return array();
		}			
	}
	
	public function get_empr_information_and_resas($empr_id) {
		$empr_id = intval($empr_id);
		return array(
			"information" => $this->get_empr_information($empr_id),
			"resas_ids" => $this->list_empr_resas($empr_id)
		);
	}

	public function generatePdfResasReaders($tresas, $location_biblio=0) {
		
	}
		
	public function confirmResaReader($id_resa=0, $id_empr_concerne=0, $f_loc=0) {
		
	}
	
	public function generatePdfResaReader($id_empr, $f_loc) {

	}
}