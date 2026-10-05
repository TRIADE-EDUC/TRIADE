<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alerts_transferts.class.php,v 1.3 2024/02/21 08:24:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class alerts_transferts extends alerts {

	protected function get_module() {
		return 'circ';
	}

	protected function get_section() {
		return 'alerte_avis_transferts';
	}

	protected function fetch_data() {
		global $deflt_docs_location, $transferts_nb_jours_alerte, $transferts_regroupement_depart;

		$this->data = array();

		//pour les validations
		$cpt_validations = $this->cpt_transferts("etat_transfert=0 AND etat_demande=0 AND num_location_source =" . $deflt_docs_location );
		if ($cpt_validations != 0) {
			$this->add_data('trans', 'alerte_transferts_validation', 'valid', '', $cpt_validations);
		}
		//pour les envois
		$cpt_envois = $this->cpt_transferts("etat_transfert=0 AND etat_demande=1 AND num_location_source =" . $deflt_docs_location );
		if ($cpt_envois != 0) {
			$this->add_data('trans', 'alerte_transferts_envoi', 'envoi', '', $cpt_envois);
		}

		//pour les receptions
		$cpt_receptions = $this->cpt_transferts("etat_transfert=0 AND etat_demande=2 AND num_location_dest =" . $deflt_docs_location );
		if ($cpt_receptions != 0) {
			$this->add_data('trans', 'alerte_transferts_reception', 'recep', '', $cpt_receptions);
		}

		//pour les retours
		$cpt_retours = $this->cpt_transferts("etat_transfert=0 AND type_transfert=1 AND etat_demande=3 AND num_location_dest =" . $deflt_docs_location  . " AND DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY)<=CURDATE()");
		if ($cpt_retours != 0) {
			$this->add_data('trans', 'alerte_transferts_retours', 'retour', '', $cpt_retours);
		}

		//pour les départs ((Validation, envoi, retour)
		if($transferts_regroupement_depart){
			$cpt_departs = $cpt_validations + $cpt_envois + $cpt_retours;
			if ($cpt_departs != 0) {
				$this->add_data('trans', 'alerte_transferts_depart', 'departs', '', $cpt_departs);
			}
		}

		//pour les refus
		$cpt_refus = $this->cpt_transferts("etat_transfert=0 AND type_transfert=1 AND etat_demande=4 AND num_location_dest =" . $deflt_docs_location);
		if ($cpt_refus != 0) {
			$this->add_data('trans', 'alerte_transferts_refus', 'refus', '', $cpt_refus);
		}
	}

	//fonction pour compter les transferts
	protected function cpt_transferts($clause_where) {
		$query = 	"SELECT count(*) FROM transferts INNER JOIN transferts_demande ON id_transfert = num_transfert WHERE " . $clause_where ;
		$result = pmb_mysql_query($query);
		$number = pmb_mysql_result($result, 0, 0);
		return $number;
	}
}