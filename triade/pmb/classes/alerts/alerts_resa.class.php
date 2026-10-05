<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_resa.class.php,v 1.4 2024/02/21 10:26:28 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_resa extends alerts {

	protected function get_module() {
		return 'circ';
	}

	protected function get_section() {
		return 'resa_menu_alert';
	}

	protected function fetch_data() {
		$this->data = array();
		$this->resa_a_traiter();
		$this->resa_a_ranger();
		$this->resa_depassees_a_traiter();
		$this->resa_planning_a_traiter();
	}

	public function resa_a_traiter() {
		global $pmb_transferts_actif,$transferts_choix_lieu_opac,$deflt_docs_location, $pmb_location_reservation,$transferts_site_fixe,$pmb_lecteurs_localises;

		$query="SELECT count(*) FROM resa, exemplaires, docs_statut  WHERE (resa_cb is null OR resa_cb='')
		and resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin
		and expl_statut=idstatut AND pret_flag=1";

		if($pmb_lecteurs_localises && $deflt_docs_location){
			$query="SELECT count(*) FROM resa, exemplaires, docs_statut  WHERE (resa_cb is null OR resa_cb='')
			and resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin
			and expl_location='".$deflt_docs_location."'
			and expl_statut=idstatut AND pret_flag=1";
		}
		// respecter les droits de réservation du lecteur
		if($pmb_location_reservation) {
			$query="SELECT count(*) FROM resa, empr, resa_loc, exemplaires , docs_statut WHERE
			resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin
			and expl_location='".$deflt_docs_location."'
			and	expl_statut=idstatut AND pret_flag=1
			and	resa_idempr = id_empr AND (resa_cb is null OR resa_cb='')
			and empr_location=resa_emprloc and resa_loc='$deflt_docs_location'";
		}

		if ($pmb_transferts_actif=="1") {
			switch ($transferts_choix_lieu_opac) {
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$query="SELECT count(*) FROM resa, empr WHERE resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') AND resa_loc_retrait=".$deflt_docs_location;
					break;
				case "2":
					//retrait de la resa sur lieu fixé
				    if ($deflt_docs_location==$transferts_site_fixe) {
						$query="SELECT count(*) FROM resa WHERE (resa_cb is null OR resa_cb='')";
				    } else {
				        return "";
				    }

						break;
				case "3":
					//retrait de la resa sur lieu exemplaire
					// respecter les droits de réservation du lecteur
					if($pmb_location_reservation)
						$query = "select count(*) from resa, exemplaires,empr, resa_loc where resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') and empr_location=resa_emprloc and resa_loc='$deflt_docs_location' and
						resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin and expl_location=resa_loc";
						else
							$query = "select count(*) from resa, exemplaires,empr where resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') and
						resa_idnotice=expl_notice and resa_idbulletin=expl_bulletin and expl_location=".$deflt_docs_location;
							break;
				default:
					//retrait de la resa sur lieu lecteur
					$query="SELECT count(*) FROM resa, empr WHERE resa_idempr = id_empr AND (resa_cb is null OR resa_cb='') AND empr_location=".$deflt_docs_location;
					break;
			}
		}
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if($number) {
			$this->add_data('listeresa', 'resa_menu_a_traiter', 'encours', '', $number);
		}
	}

	public function resa_a_ranger() {
		global $deflt_docs_location;

		$query="SELECT count(*) from resa_ranger,exemplaires where resa_cb=expl_cb and expl_location='$deflt_docs_location'";
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
			$this->add_data('listeresa', 'resa_menu_a_ranger', 'docranger', '', $number);
		}
	}

	public function resa_depassees_a_traiter() {
		global $pmb_transferts_actif, $deflt_docs_location,$transferts_choix_lieu_opac;

		$query="SELECT count(*) FROM resa, empr WHERE resa_idempr = id_empr AND resa_date_fin < CURDATE() and resa_date_fin <>  '0000-00-00' ";
		if ($pmb_transferts_actif=="1") {
			switch ($transferts_choix_lieu_opac) {
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$query .= " AND resa_loc_retrait='".$deflt_docs_location."' ";
					break;
				case "2":
					//retrait de la resa sur lieu fixé
					break;
				case "3":
					//retrait de la resa sur lieu exemplaire
					break;
				default:
					//retrait de la resa sur lieu lecteur
					$query .= " AND empr_location='".$deflt_docs_location."' ";
					break;
			}

		}

		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		if ($number) {
			$this->add_data('listeresa', 'resa_menu_a_depassees', 'depassee', '', $number);
		}
	}

	public function resa_planning_a_traiter() {
		global $pmb_resa_planning, $pmb_resa_planning_toresa, $deflt_resas_location, $deflt_docs_location;

		if($pmb_resa_planning) {
			$pmb_resa_planning_toresa = intval($pmb_resa_planning_toresa);
			if ($deflt_resas_location) {
				$expl_loc = $deflt_resas_location;
			} else {
				$expl_loc = $deflt_docs_location;
			}
			$query = "SELECT count(*) ";
			$query.= "FROM resa_planning ";
			$query.= "WHERE resa_remaining_qty!=0 ";
			$query.= "and resa_validee=0 ";
			$query.= "and resa_loc_retrait = $expl_loc ";
			$query.= "and datediff(resa_date_debut, curdate()) <= ".$pmb_resa_planning_toresa;
			$result = pmb_mysql_query($query);
			$number = pmb_mysql_result($result, 0, 0);
			if ($number) {
				$this->add_data('resa_planning', 'resa_planning_to_validate', 'all', '&resa_planning_circ_ui_montrerquoi=invalidees', $number);
			}

			$query = "SELECT count(*) ";
			$query.= "FROM resa_planning ";
			$query.= "WHERE resa_remaining_qty!=0 ";
			$query.= "and resa_validee=1 ";
			$query.= "and resa_loc_retrait = $expl_loc ";
			$query.= "and datediff(resa_date_debut, curdate()) <= ".$pmb_resa_planning_toresa;
			$result = pmb_mysql_query($query);
			$number = pmb_mysql_result($result, 0, 0);
			if ($number) {
				$this->add_data('resa_planning', 'resa_planning_todo', 'all', '', $number);
			}
		}
	}
}