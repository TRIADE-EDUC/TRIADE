<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_task_calendar.class.php,v 1.5 2022/03/10 15:19:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class scheduler_task_calendar {
	public $new_date = array();					//nouvelle date
	public $nouvelle_date;						// valeur de la date en chaîne de caractères
	public $defaut_min;
	
	public function __construct($num_planificateur) {
		$num_planificateur = intval($num_planificateur);
		if ($num_planificateur) {
			//renseignements Jour J
			$date_du_jour = getdate();
			$this->new_date["ANNEE"] = $date_du_jour["year"];
			$this->new_date["MOIS"] = $date_du_jour["mon"];
			$this->new_date["JOUR"] = $date_du_jour["mday"];
			$this->calcul_next_exec($num_planificateur);
		}
	}
	
	public function calcul_next_exec($num_planificateur) {
		$sql = "SELECT id_planificateur, num_type_tache, libelle_tache, perio_heure, perio_minute, perio_jour_mois, perio_jour, perio_mois 
				FROM planificateur WHERE id_planificateur=".$num_planificateur;
		$res = pmb_mysql_query($sql);
		if ($res) {
			while ($obj_sql=pmb_mysql_fetch_object($res)) {
				//renseignements Jour J
				$date_du_jour = getdate();
				
				//utile pour le calcul de l'heure et de la minute
				$date_today = $date_du_jour["year"]."-".$date_du_jour["mon"]."-".$date_du_jour["mday"];					
				
				//renvoit 1 si année bissextile, 0 sinon
				$annee_bissextile = date('L');
				//Valeurs bdd
				$jour_mois_bdd = explode(',',$obj_sql->perio_jour_mois);
				$jour_semaine_bdd = explode(',',$obj_sql->perio_jour);
				$mois_bdd = explode(',',$obj_sql->perio_mois);
				$heure_bdd = $obj_sql->perio_heure;
				$minute_bdd = $obj_sql->perio_minute;
				
   				$this->calcul_date($jour_semaine_bdd, $jour_mois_bdd, $mois_bdd);

				//calcul de la minute
				$this->calcul_minute_exec($minute_bdd,$date_today,$date_du_jour,$jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
		
				//calcul de l'heure
				$this->calcul_heure_exec($heure_bdd,$date_today,$date_du_jour,$jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
				
				//la date calculée est identique à la date du jour
				if (($date_du_jour["year"] == $this->new_date["ANNEE"]) && ($date_du_jour["mon"] == $this->new_date["MOIS"]) && ($date_du_jour["mday"] == $this->new_date["JOUR"])) {
					//formatage de l'heure calculée
					$this->new_date["HEURE"] = (strlen($this->new_date["HEURE"]) == "1" ? "0".$this->new_date["HEURE"] : $this->new_date["HEURE"]);
					$this->new_date["MINUTE"] = (strlen($this->new_date["MINUTE"]) == "1" ? "0".$this->new_date["MINUTE"] : $this->new_date["MINUTE"]);
					if (($this->new_date["HEURE"] == $date_du_jour["hours"]) && ($this->new_date["MINUTE"] == $date_du_jour["minutes"])) {
						$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
					}
				}
				//formatage de l'heure calculée
				$this->new_date["HEURE"] = (strlen($this->new_date["HEURE"]) == "1" ? "0".$this->new_date["HEURE"] : $this->new_date["HEURE"]);
				$this->new_date["MINUTE"] = (strlen($this->new_date["MINUTE"]) == "1" ? "0".$this->new_date["MINUTE"] : $this->new_date["MINUTE"]);
			}
		}
	}

	//calcul de la date
	public function calcul_date($days_of_week, $days_of_month, $month_of_year) {
		$calendar = array(
			'1' => '31','2' => '28','3' => '31','4' => '30','5' => '31','6' => '30','7' => '31',
 			'8' => '31','9' => '30','10' => '31','11' => '30','12' => '31'
		);
		
		//Vérification de la périodicité pour éviter la boucle infinie
		$check_periodicite = false;
		if (($days_of_month[0] != '*') && ($month_of_year[0] != '*')) {
			foreach ($month_of_year as $month) {
				foreach ($days_of_month as $day) {
					if ($day <= $calendar[$month]) {
						$check_periodicite = true;
					}
				}
			}
		} else {
			$check_periodicite = true;
		}
		if (!$check_periodicite) {
			$this->new_date["ANNEE"] = "0000";
			$this->new_date["MOIS"] = "00";
			$this->new_date["JOUR"] = "00";
			$this->nouvelle_date = $this->new_date["ANNEE"]."-".$this->new_date["MOIS"]."-".$this->new_date["JOUR"];
			return;
		}
		
		/* Calcul du mois */
		if ($month_of_year[0] != '*') {
			$annee_courante = false;
			$nb_months_of_year = count($month_of_year);
			for ($i = 0; $i < $nb_months_of_year; $i++) {
				if (($month_of_year[$i] >= $this->new_date["MOIS"]) && (!$annee_courante)) {
					$annee_courante = true;
					$this->new_date["MOIS"] = $month_of_year[$i];
				}
			}
			if (!$annee_courante) {
				$this->new_date["ANNEE"]++;
				$this->new_date["MOIS"] = $month_of_year[0];
				if ($days_of_month[0] != '*') {
					$this->new_date["JOUR"] = $days_of_month[0];
				} else {
					$this->new_date["JOUR"] = '1';
				}
			}
		}
		if ($days_of_month[0] != '*') {
			$trouve = false;
			while (!$trouve) {
				foreach ($days_of_month as $i=>$day) {
					if ((date('Y') != $this->new_date["ANNEE"] || date('m') != str_pad($this->new_date["MOIS"], 2, 0, STR_PAD_LEFT)) || ($day >= $this->new_date["JOUR"]) && ($day <= $calendar[$this->new_date["MOIS"]])) {
						$this->new_date["JOUR"] = $day;
						$timestamp = mktime (0, 0, 0, $this->new_date["MOIS"]*1, $this->new_date["JOUR"]*1, $this->new_date["ANNEE"]*1);
						if ($days_of_week[0] != '*') {
							if (in_array(date("N", $timestamp),$days_of_week)) {
								$trouve = true;
							}
						} else {
							$trouve = true;
						}
					}
					if ($trouve) break;
				}
				if (!$trouve) {
					$this->new_date["JOUR"] = $days_of_month[0];
					if ($month_of_year[0] != '*') {
						$boolean_mois = false;
						foreach ($month_of_year as $i=>$month) {
							if (($month > $this->new_date["MOIS"]) && ($month <= 12)) {
								$this->new_date["MOIS"] = $month;
								$boolean_mois = true;
							}
							if ($boolean_mois) break;
						}
						if (!$boolean_mois) {
							$this->new_date["MOIS"] = $month_of_year[0];
							$this->new_date["ANNEE"]++;
						}
					} else {
						$this->new_date["MOIS"]++;
						if ($this->new_date["MOIS"] > '12') {
 							$this->new_date["MOIS"] = '1';
							$this->new_date["ANNEE"]++;
						}
					}
				}
			}	
		} elseif ($days_of_week[0] != '*') {
			$trouve = false;
			while (!$trouve) {
				$timestamp = mktime (0, 0, 0, $this->new_date["MOIS"]*1, $this->new_date["JOUR"]*1, $this->new_date["ANNEE"]*1);
				while ($this->new_date["JOUR"] <= $calendar[$this->new_date["MOIS"]]) {
					$timestamp = mktime (0, 0, 0, $this->new_date["MOIS"]*1, $this->new_date["JOUR"]*1, $this->new_date["ANNEE"]*1);
					if (in_array(date("N", $timestamp),$days_of_week)) {
						$trouve = true;
					} else {
						$this->new_date["JOUR"]++;
					}
					if ($trouve) break;
				}
				if (!$trouve) {
					$this->new_date["JOUR"] = '01';
					if ($month_of_year[0] != '*') {
						$boolean_mois = false;
						foreach ($month_of_year as $i=>$month) {
							if (($month > $this->new_date["MOIS"]) && ($month <= 12)) {
								$this->new_date["MOIS"] = $month;
								$boolean_mois = true;
							}
							if ($boolean_mois) break;
						}
						if (!$boolean_mois) {
							$this->new_date["MOIS"] = $month_of_year[0];
							$this->new_date["ANNEE"]++;
						}
					} else {
						$this->new_date["MOIS"]++;
						if ($this->new_date["MOIS"] > '12') {
							$this->new_date["MOIS"] = '1';
							$this->new_date["ANNEE"]++;
						}
					}
				}
			}
			
		} else {
			if ($this->new_date["JOUR"] > $calendar[$this->new_date["MOIS"]]) {
				$this->new_date["JOUR"] = '01';
				$this->new_date["MOIS"]++;
				if ($this->new_date["MOIS"] > '12') {
					$this->new_date["MOIS"] = '1';
					$this->new_date["ANNEE"]++;
				}
			} 
		}
		$this->nouvelle_date = $this->new_date["ANNEE"]."-".$this->new_date["MOIS"]."-".$this->new_date["JOUR"];
	}
		
	public function recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd) {
		$this->new_date["JOUR"]++;
 		$this->calcul_date($jour_semaine_bdd,$jour_mois_bdd,$mois_bdd);
	}
	
	
	/* Calcul de la minute de la prochaine exécution
	 * Paramètre $minute_bdd qui est une châine de caractères de la base qui doit être analysée
	 */
	public function calcul_minute_exec($minute_bdd,$date_today,$date_du_jour,$jour_semaine_bdd, $mois_bdd, $jour_mois_bdd) {
		//calcul de la minute, analyse de la chaîne
		if ($minute_bdd != '*') {
			// heure saisie au format statique, exemple: 3 ou 03 pour 3h
			if (preg_match("#^([0-9]{1})$#", $minute_bdd) || preg_match("#^([0-9]{2})$#", $minute_bdd)) {
				$this->new_date["MINUTE"] = $minute_bdd;
				$this->defaut_min = $minute_bdd;
			}
			//cela veut dire qu'il s'agit d'un intervalle
			if (strstr($minute_bdd,"-")) {
				$tab_m = explode("-", $minute_bdd);
				$value = "1";
				// action répétitive différente de l'heure, ex: toutes les 2 heures
				if (preg_match("#(\{[0-9]{1}\})$#", $minute_bdd) || preg_match("#(\{[0-9]{2}\})$#", $minute_bdd)) {
					//valeur de l'incrémentation
					$value = substr($minute_bdd, strpos($minute_bdd, "{")+1, -1);
					$tab_m = explode("-", substr($minute_bdd, 0, strpos($minute_bdd, "{")));
				}
				// même date, intervalle croissant ou décroissant ?
				if ($date_today == $this->nouvelle_date) {
					$this->defaut_min = $tab_m[0];
					$val = $tab_m[0];
 					if ($value != "") {
 						while (!($val > $date_du_jour["minutes"])) {
 							$val = $val + $value;
 						}
 					}
					if ($tab_m[0] < $tab_m[1]) {
						if (($date_du_jour["minutes"] > $tab_m[1]) || ($date_du_jour["minutes"] < $tab_m[0]) ) {
							$this->new_date["MINUTE"] = $tab_m[0];
						} else if ($val > $tab_m[1]) {
							$this->new_date["MINUTE"] = $tab_m[0];
						} else {
							$this->new_date["MINUTE"] = $val;
						}
					} else if ($tab_m[0] > $tab_m[1]) {
						$tab_m[1] = "59";
						// Pas très simple à gérer ??
						if (($val > $tab_m[0]) && ($val < $tab_m[1])) {
							$this->new_date["MINUTE"] = $val;
						} else {
							$this->new_date["MINUTE"] = $tab_m[0];
						}
					} else {
						$this->new_date["MINUTE"] = $tab_m[0];
					}	
				} else {
					$this->new_date["MINUTE"] = $tab_m[0];
				} 
			}
		} else {
			if ($date_today == $this->nouvelle_date) {
				$this->defaut_min = $minute_bdd;
				if ($date_du_jour["minutes"]+1 >= '60') {
					$this->new_date["MINUTE"] = "00";
				} else {
					$this->new_date["MINUTE"] = $date_du_jour["minutes"]+1;
				}
			} else {
				$this->new_date["MINUTE"] = "00";
			}
		}
	}
	
	public function calcul_heure_exec($heure_bdd,$date_today,$date_du_jour,$jour_semaine_bdd, $mois_bdd, $jour_mois_bdd) {
	//calcul de l'heure, analyse de la chaîne
		if ($heure_bdd != '*') {
			// heure saisie au format statique, exemple: 3 ou 03 pour 3h
			if (preg_match("#^([0-9]{1})$#", $heure_bdd) || preg_match("#^([0-9]{2})$#", $heure_bdd)) {
				// date identique, (heure courante > heure planifiée ? recalcule la date : heure planifiée)
				if ($date_today == $this->nouvelle_date) {
					if ($date_du_jour["hours"] > $heure_bdd) {
						//il faut recalculer la date...
						$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
					} else if (($date_du_jour["hours"] == $heure_bdd) && ($date_du_jour["minutes"] > $this->new_date["MINUTE"])) {
						//il faut recalculer la date...
						$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
					} else if (($date_du_jour["hours"] != $heure_bdd) && ($this->defaut_min == "*")) {
						$this->new_date["MINUTE"] = "00";
					}
				}
				//valeur de l'heure quoiqu'il arrive car elle est statique
				$this->new_date["HEURE"] = $heure_bdd;
			} 
			if (strstr($heure_bdd,"-")) {
				//cela veut dire qu'il s'agit d'un intervalle
				$tab_h = explode("-", $heure_bdd);
				$value = "1";
				// action répétitive différente de l'heure, ex: toutes les 2 heures
				if (preg_match("#(\{[0-9]{1}\})$#", $heure_bdd) || preg_match("#(\{[0-9]{2}\})$#", $heure_bdd)) {
					$value = substr($heure_bdd, strpos($heure_bdd, "{")+1, -1);
					$tab_h = explode("-", substr($heure_bdd, 0, strpos($heure_bdd, "{")));
				}
				//même date, intervalle croissant ou décroissant ?
 				if ($date_today == $this->nouvelle_date) {
 					$val = $tab_h[0];
 					if ($value != "") {
 						while (!($val >= $date_du_jour["hours"])) {
 							$val = $val + $value;
 						} 
 						if (($val == $date_du_jour["hours"]) && ($this->new_date["MINUTE"] <= $date_du_jour["minutes"])) {
 							$val = $val + $value;
 						}
 					}
 					if ($tab_h[0] < $tab_h[1]) {
 						if ($date_du_jour["hours"] > $tab_h[1]) {
 							// il faut donc recalculer la date...
							$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
							$this->new_date["HEURE"] = $tab_h[0];
							$this->new_date["MINUTE"] = ($this->defaut_min == "*" ? "00" : $this->defaut_min);
 						} else if (($date_du_jour["minutes"] >= $this->new_date["MINUTE"]) && (($val > $tab_h[1]))) {
							// il faut donc recalculer la date...
							$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
							$this->new_date["HEURE"] = $tab_h[0];
							$this->new_date["MINUTE"] = ($this->defaut_min == "*" ? "00" : $this->defaut_min);;
 						} else if (($date_du_jour["minutes"] >= $this->new_date["MINUTE"]) && (($val <= $tab_h[1]))) {
 							$this->new_date["HEURE"] = $val;
 						} else if (($date_du_jour["minutes"] < $this->new_date["MINUTE"]) && (($val <= $tab_h[1])) && ($val > $date_du_jour["hours"])) {
 							$this->new_date["HEURE"] = $val;
 							$this->new_date["MINUTE"] = ($this->defaut_min == "*" ? "00" : $this->defaut_min);
 						} else if ($date_du_jour["hours"] < $tab_h[0]){
 							//elle doit récupérer la valeur courante ...
 							$this->new_date["HEURE"] = $tab_h[0]; 							
						} else {
							$this->new_date["HEURE"] = $date_du_jour["hours"];
						}
 					} else if ($tab_h[0] > $tab_h[1]) {
 						$tab_h[1] = "23";
 						if ($date_du_jour["hours"] <= $val) {
 							$this->new_date["HEURE"] = $val;
 						} else {
 							// il faut recalculer la date...
							$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
							// on initialise l'heure à 00h
							$this->new_date["HEURE"] = "00";
 						}
						if (($date_du_jour["minutes"] >= $this->new_date["MINUTE"]) 
							&& ($this->new_date["HEURE"] == $date_du_jour["hours"])
							&& ($this->new_date["HEURE"]+$value <= "23")) {
							$this->new_date["HEURE"] = $this->new_date["HEURE"] + $value;
						}	
						if (($this->new_date["HEURE"] < $tab_h[0]) && ($date_du_jour["hours"] <= $tab_h[0])) {
							$this->new_date["HEURE"] = $date_du_jour["hours"];
						}
 					} else {
 						$this->new_date["HEURE"] = $tab_h[0];
 					}
				} else {
					$this->new_date["HEURE"] = $tab_h[0];
				}
			}
		} else {
			if ($date_today == $this->nouvelle_date) {
				if ($this->new_date["MINUTE"] < $date_du_jour["minutes"]) {
					if ($date_du_jour["hours"]+1 < 24) {
						$this->new_date["HEURE"] = $date_du_jour["hours"]+1;
					} else {
						// il faut recalculer la date...
						$this->recalcule_date($jour_semaine_bdd, $mois_bdd, $jour_mois_bdd);
						// on initialise l'heure à 00h
						$this->new_date["HEURE"] = "00";
					}
				} else {
					$this->new_date["HEURE"] = $date_du_jour["hours"];
				}
			} else {
				// on initialise l'heure à 00h
				$this->new_date["HEURE"] = "00";
			}
		}
	}
}