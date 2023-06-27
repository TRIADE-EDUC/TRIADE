<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesResas.class.php,v 1.8 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");
require_once($class_path."/bannette.class.php");

class pmbesResas extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
		
	public function list_empr_resas($empr_id) {
		global $dbh;
		global $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$empr_id += 0;
			if (!$empr_id)
				throw new Exception("Missing parameter: empr_id");
		
			$requete  = "SELECT id_resa FROM resa WHERE (resa_idempr='$empr_id')"; 
				
			$res = pmb_mysql_query($requete, $dbh);
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
		global $pmb_lecteurs_localises, $deflt_docs_location;
		global $dbh;
		global $msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$result = array();
	
			$empr_id += 0;
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
	
	public function infos_biblio($location_biblio=0) {
		global $dbh;
		global $pmb_lecteurs_localises;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;

		if ($pmb_lecteurs_localises) {
			if (!$location_biblio) {
				global $deflt2docs_location;
				$location_biblio = $deflt2docs_location;
			}
			$query = "select name, adr1,adr2,cp,town,state,country,phone,email,website,logo from docs_location where idlocation=".$location_biblio;
			$res = pmb_mysql_query($query,$dbh);
			if (pmb_mysql_num_rows($res) == 1) {
				$row = pmb_mysql_fetch_object($res);
				$biblio_name = $row->name;
				$biblio_adr1 = $row->adr1;
				$biblio_adr2 = $row->adr2;
				$biblio_cp = $row->cp;
				$biblio_town = $row->town;
				$biblio_state = $row->state;
				$biblio_country = $row->country;
				$biblio_phone = $row->phone;
				$biblio_email = $row->email;
				$biblio_website = $row->website;
				$biblio_logo = $row->logo;
			}	
		} else {
			/*** Informations provenant des paramètres généraux - on ne parle donc pas de multi-localisations **/
			// nom de la structure
			$var = "opac_biblio_name";
			global ${$var};
			eval ("\$biblio_name=\"".${$var}."\";");
		
			// logo de la structure
			$var = "opac_logo";
			global ${$var};
			eval ("\$biblio_logo=\"".${$var}."\";");
		
			// adresse principale
			$var = "opac_biblio_adr1";
			global ${$var};
			eval ("\$biblio_adr1=\"".${$var}."\";");
			
			// adresse secondaire
			$var = "opac_biblio_adr2";
			global ${$var};
			eval ("\$biblio_adr2=\"".${$var}."\";");
			
			// code postal
			$var = "opac_biblio_cp";
			global ${$var};
			eval ("\$biblio_cp=\"".${$var}."\";");
			
			// ville
			$var = "opac_biblio_town";
			global ${$var};
			eval ("\$biblio_town=\"".${$var}."\";");
			
			// Etat
			$var = "opac_biblio_state";
			global ${$var};
			eval ("\$biblio_state=\"".${$var}."\";");
			
			// pays
			$var = "opac_biblio_country";
			global ${$var};
			eval ("\$biblio_country=\"".${$var}."\";");
			
			// telephone
			$var = "opac_biblio_phone";
			global ${$var};
			eval ("\$biblio_phone=\"".${$var}."\";");
			
			// adresse mail
			$var = "opac_biblio_email";
			global ${$var};
			eval ("\$biblio_email=\"".${$var}."\";");
			
			//site web
			$var = "opac_biblio_website";
			global ${$var};
			eval ("\$biblio_website=\"".${$var}."\";");
		}
	}
}




?>