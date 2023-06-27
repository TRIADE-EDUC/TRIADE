<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesReaders.class.php,v 1.5 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesReaders extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	/**
	 * $search => limit - exceed - running
	 * $empr_location_id => ex : (0 pour Toutes les localisations,..)
	 * $empr_statut_edit => ex : (1 pour actif,..)
	 * $sortby => nom des champs
	 */
	public function listReadersSubscription($search='',$empr_location_id='',$empr_statut_edit='', $sortby=''){
		global $dbh, $msg, $pmb_relance_adhesion,$pmb_lecteurs_localises;
		global $deflt2docs_location;
							
		if ($search =='limit') {
			$restrict = " ((to_days(empr_date_expiration) - to_days(now()) ) <=  $pmb_relance_adhesion ) and empr_date_expiration >= now() ";
		} else if ($search =='exceed') {
			$restrict = " empr_date_expiration < now() ";
		} else if ($search =='running') {
			$restrict = " empr_date_expiration >= now() ";
		}
		
		// restriction localisation le cas échéant
		if ($pmb_lecteurs_localises) {
			if ($empr_location_id=="") 
				$empr_location_id = $deflt2docs_location ;
			if ($empr_location_id!=0) 
				$restrict_localisation = " AND empr_location='$empr_location_id' ";
			else 
				$restrict_localisation = "";
		}

		// filtré par un statut sélectionné
		if ($empr_statut_edit) {
			if ($empr_statut_edit!=0) 
				$restrict_statut = " AND empr_statut='$empr_statut_edit' ";
			else 
				$restrict_statut="";
		} 
		
		// on récupére le nombre de lignes 
		if(!$nbr_lignes) {
			$requete = "SELECT COUNT(1) FROM empr, empr_statut where 1 ";
			$requete = $requete.$restrict_localisation.$restrict_statut." and ".$restrict;
			$requete .= " and empr_statut=idstatut";
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = @pmb_mysql_result($res, 0, 0);
		}

		if($nbr_lignes) {
//			if ($statut_action=="modify") {
//				$requete="UPDATE empr set empr_statut='$empr_chang_statut_edit' where 1 ".$restrict_localisation.$restrict_statut." and ".$restrict;
//				$restrict_statut = " AND empr_statut='$empr_chang_statut_edit' ";
//				@pmb_mysql_query($requete);
//			} 
			// on lance la vraie requête
			$requete = "SELECT id_empr,empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_ville, empr_mail,
				empr_year, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration, statut_libelle  FROM empr, empr_statut ";
			$restrict_empr = " WHERE 1 ";
			$restrict_requete = $restrict_empr.$restrict_localisation.$restrict_statut." and ".$restrict;
			$requete .= $restrict_requete;
			$requete .= " and empr_statut=idstatut ";
			if (!isset($sortby))
				$sortby = 'empr_nom';
		
			$requete .= " ORDER BY $sortby ";

			$res = @pmb_mysql_query($requete, $dbh);
			
			while ($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = array (
					"id_empr" => $row["id_empr"],
					"empr_cb" => $row["empr_cb"],
					"empr_nom" => $row["empr_nom"],
					"empr_prenom" => $row["empr_prenom"],
					"empr_adr1" => $row["empr_adr1"],
					"empr_adr2" => $row["empr_adr2"],
					"empr_ville" => $row["empr_ville"],
					"empr_mail" => $row["empr_mail"],
					"empr_year" => $row["empr_year"],
					"aff_empr_date_expiration" => $row["aff_empr_date_expiration"],
					"statut_libelle" => $row["statut_libelle"],
				);
			}
		}
		return $result;
	}

	public function listGroupReaders() {
		global $dbh;
		
		$result=array();
		
		$requete = "SELECT id_groupe, libelle_groupe, resp_groupe, concat(IFNULL(empr_prenom,'') ,' ',IFNULL(empr_nom,'')) as resp_name, count( empr_id ) as nb_empr FROM groupe LEFT  JOIN empr_groupe ON groupe_id = id_groupe left join empr on resp_groupe = id_empr
		$clause group by id_groupe, libelle_groupe, resp_groupe, resp_name ORDER BY libelle_groupe LIMIT $debut,$nb_per_page ";
		$res = pmb_mysql_query($requete, $dbh);
		
		while($rgroup=pmb_mysql_fetch_assoc($res)) {
			$requete = "SELECT count( pret_idempr ) as nb_pret FROM empr_groupe,pret where groupe_id=$rgroup->id_groupe and empr_id = pret_idempr";
			$res_pret = pmb_mysql_query($requete, $dbh);
			if (pmb_mysql_num_rows($res_pret)) {
				$rpret=pmb_mysql_fetch_object($res_pret);
				$nb_pret=$rpret->nb_pret;	
			}
			
			$result[] = array (
				"libelle_groupe" => $rgroup->libelle_groupe,
				"resp_name" => $rgroup->resp_name,
				"nb_empr" => $rgroup->nb_empr,
				"nb_pret" => $nb_pret,
			);
		}
	}
	
	/**
	 * $search => limit - exceed - running
	 * $empr_location_id => ex : (0 pour Toutes les localisations,..)
	 * $empr_statut_edit => ex : (1 pour actif,..)
	 * $sortby => nom des champs
	 */
	public function relanceReadersSubscription($tresults, $empr_location_id) {
		
	}
	
	public function generatePdfReaderSubscription($id_empr,$empr_location_id) {
		
	}
	
	public function generateMailReadersSubscription($id_empr,$empr_location_id) {
		
	}
	
	public function infos_biblio($empr_location_id) {
		global $dbh,$pmb_lecteurs_localises;
		global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email,$biblio_website;
		global $biblio_logo;
		
		if ($pmb_lecteurs_localises) {
			if ($empr_location_id == '0') {
				global $deflt_docs_location;
				$empr_location_id = $deflt_docs_location;
			}
			$query = "select name, adr1,adr2,cp,town,state,country,phone,email,website,logo from docs_location where idlocation=".$empr_location_id;
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