<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_situation.class.php,v 1.21.4.1 2025/05/30 07:58:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class resa_situation{
	
	public $id_resa;
	
	public $resa;
	
	public $resa_idnotice;
	
	public $resa_idbulletin;
	
	public $resa_cb;
	
	public $idlocation;
	
	public $rank;
	
	public $no_aff;
	
	public $lien_deja_affiche;
	
	public $lien_transfert;
	
	public $display;
	
	protected $display_already_called;
	
	protected $my_home_location;
	
	public function __construct($id_resa= 0) {
		$this->id_resa = intval($id_resa);
	}

	public function initialize_no_aff() {
	    global $pmb_transferts_actif, $transferts_choix_lieu_opac;
	    
	    if ($this->rank == 1 || $this->is_first_availability()) {
	        // nombre d'exemplaires disponibles
	        $number_expl_available_inside = $this->get_number_expl_available_inside();
	        if($number_expl_available_inside>0) {
	            
	        } else {
	            if($this->resa_cb && $this->resa->formatted_date_fin) {
	                // On ne fait rien
	            } else {
	                // rien n'est disponible, on trouve la date du premier retour
	                if ( ($pmb_transferts_actif=="1") &&  $transferts_choix_lieu_opac!=3) {// && ($f_loc!=0) ?
	                    //regardons la localisation de retrait si différente de ma localisation
	                    $number_expl_lendable_retrait_inside = 0;
	                    $number_expl_available_retrait_inside = 0;
	                    $dest_loc = resa_loc_retrait($this->resa->id);
	                    if ($dest_loc!=0 && !empty($this->my_home_location) && $dest_loc != $this->my_home_location) {
	                        $number_expl_lendable_retrait_inside = $this->resa->get_number_expl_lendable($dest_loc);
	                        if ($number_expl_lendable_retrait_inside) {
	                            //Un exemplaire est-il dispo ?
	                            $number_expl_available_retrait_inside = $number_expl_lendable_retrait_inside - $this->resa->get_number_expl_out($dest_loc) - $this->resa->get_number_expl_in_circ($dest_loc);
	                        }
	                    }
	                    if ($number_expl_available_retrait_inside==0) {
	                        //on a pas d'exemplaires sur le site de retrait
	                        //on regarde si un des exemplaires n'est pas en transfert pour cette resa !
	                        $this->no_aff=0;
	                    }
	                }
	            }
	        }
	    }
	}
	
	protected function is_first_availability() {
		//on regarde si les rangs précédents sont validés
		$is_first_availability = true;
		$ranks = recupere_rangs($this->resa->id_notice, $this->resa->id_bulletin/*, $this->filters['removal_location']*/);
		if(!empty($ranks)) {
			$ranks = array_slice($ranks, 0, $this->rank, true);
			foreach ($ranks as $id_resa=>$rank) {
				if($rank < $this->rank) {
					if($is_first_availability && empty(reservation::get_cb_from_id($id_resa))) {
						$is_first_availability = false;
					}
				}
			}
		}
		return $is_first_availability;
	}
	
	protected function get_other_location_lendable($location=0, $outside=false) {
		$expl_locations = $this->resa->get_expl_locations_lendable($location, $outside);
		if(count($expl_locations) == 1) {
			return $expl_locations[0];
		}
		return 0;
	}
	
	protected function get_display_other_location_lendable($location=0, $outside=false) {
		$display = '';
		$location_lendable = $this->get_other_location_lendable($location, $outside);
		if($location_lendable) {
			$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$location_lendable."'";
			$display .= "<br />(".pmb_mysql_result(pmb_mysql_query($rqt),0).")";
		}
		return $display;
	}
	
	protected function get_number_expl_available_inside() {
		// on compte le nombre total d'exemplaires prêtables pour la notice
		$number_expl_lendable_inside = $this->resa->get_number_expl_lendable();
		if($this->resa->get_restrict_expl_location_query() && !$number_expl_lendable_inside) {
		    $this->no_aff=1;
		}
		// on compte le nombre d'exemplaires sortis
		$number_expl_out_inside = $this->resa->get_number_expl_out();
		
		// on compte le nombre d'exemplaires en circulation
		$number_expl_in_circ_inside = $this->resa->get_number_expl_in_circ();
		
		// on en déduit le nombre d'exemplaires disponibles
		return $number_expl_lendable_inside - $number_expl_out_inside - $number_expl_in_circ_inside;
	}
	
	protected function get_loan_return_date($indice=0, $location=0, $outside=false) {
		$loans = $this->resa->get_expl_lendable_in_loan($location, $outside);
		if(!empty($loans[$indice])) {
			return $loans[$indice]->aff_pret_retour;
		}
		return '';
	}
	
	protected function get_first_loan_return_date() {
		global $msg;
		
		$query = "SELECT date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour from pret p, exemplaires e ";
		if ($this->resa->id_notice) $query .= " WHERE e.expl_notice=".$this->resa->id_notice;
		elseif ($this->resa->id_bulletin) $query .= " WHERE e.expl_bulletin=".$this->resa->id_bulletin;
		$query .= " AND e.expl_id=p.pret_idexpl";
		$query .= " ORDER BY p.pret_retour LIMIT 1";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 0);
		}
		return '';
	}
	
	protected function get_display_availability_first_rank($info_gestion=NO_INFO_GESTION) {
		global $msg;
		global $pmb_transferts_actif, $f_loc, $transferts_choix_lieu_opac;
		global $has_resa_available; // utilisé au niveau de la fiche lecteur
		
		$this->lien_deja_affiche = false;
		// détermination de la date à afficher dans la case retour pour le rang 1
		// disponible, réservé ou date de retour du premier exemplaire
		
		// nombre d'exemplaires disponibles
		$number_expl_available_inside = $this->get_number_expl_available_inside();
		$this->lien_transfert = false;
		if($number_expl_available_inside>0) {
			if($this->resa->date_debut && $this->resa->date_debut != '0000-00-00') { //résa validée ?
				$has_resa_available = true;
			}
			if($this->resa_cb && $this->resa->formatted_date_fin) {
				$display = "<strong>".$msg['expl_reserve']."</strong>";
			} elseif($this->rank>$number_expl_available_inside)	{
				$display = "<strong>".$msg['expl_resa_already_reserved']."</strong>";
				
				if(!empty($this->my_home_location)) {
					// on trouve la date du premier retour chez soi
					$loan_return_date = $this->get_loan_return_date();
					if ($loan_return_date) {
						$display = $loan_return_date;
					}
				}
			} else {
				// un exemplaire est disponible pour le réservataire (affichage : disponible)
				$display = "<strong>".$msg['expl_resa_available']."</strong>";
				//est-il en rayon dans une autre localisation ?
				$expl_locations = $this->resa->get_expl_locations_lendable(0, true);
				if($this->idlocation && count($expl_locations) == 1 && !in_array($this->idlocation, $expl_locations)) {
					$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$expl_locations[0]."'";
					$display .= "<br />(".pmb_mysql_result(pmb_mysql_query($rqt),0).")";
				}
			}
			switch ($info_gestion) {
				case GESTION_INFO_GESTION:
				case NO_INFO_GESTION;
				case LECTEUR_INFO_GESTION:
					if ( ($pmb_transferts_actif=="1")) {
						$dest_loc = resa_loc_retrait($this->resa->id);
						if ($dest_loc!=0) {
							$number_expl_lendable_inside = $this->resa->get_number_expl_lendable($dest_loc);
							if ($number_expl_lendable_inside==0) {
								//on a pas d'exemplaires sur le site de retrait
								//on regarde si on en ailleurs
								$number_expl_lendable_outside = $this->resa->get_number_expl_lendable($dest_loc, true);
								if ($number_expl_lendable_outside!=0) {
									//on en a au moins un ailleurs!
									//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
									$query = "SELECT id_transfert, motif_refus, etat_demande FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
									$tresult = pmb_mysql_query($query);
									if (pmb_mysql_num_rows($tresult)) {
									    $trow = pmb_mysql_fetch_assoc($tresult);
										//on a un transfert en cours
									    if ($trow["etat_demande"] == 4) {
									        $display = "<strong>" . $msg["transferts_circ_menu_refuse"] . "</strong> : ".$trow['motif_refus'];
									    } else {
									        $display = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
									    }
									} elseif($number_expl_lendable_outside>=$this->rank)	{
										$this->lien_transfert = true;
										if($this->resa->transfert_resa_dispo($dest_loc)){
											$display = $msg["resa_expl_dispo_other_location"];
											$display .= $this->get_display_other_location_lendable($dest_loc, true);
										}
									}
								}
							} //if ($number_expl_lendable_inside==0)
						} //if ($dest_loc!=0)
					} //if ( ($pmb_transferts_actif=="1") )
					break;
			}
		} else {
			if($this->resa_cb && $this->resa->formatted_date_fin) {
				$display = "<strong>".$msg['expl_reserve']."</strong>";
			} else {
				// rien n'est disponible, on trouve la date du premier retour
				$loan_return_date = $this->get_first_loan_return_date();
				if ($loan_return_date) {
					$display = $loan_return_date;
				}else {
					if($this->resa->get_number_expl_in_circ()) {
						$display = $msg['transferts_circ_retour_filtre_circ'];
					} else {
						$display = $msg["resa_no_expl"];
					}
				}
				if ( ($pmb_transferts_actif=="1") &&  $transferts_choix_lieu_opac!=3) {// && ($f_loc!=0) ?
					//regardons la localisation de retrait si différente de ma localisation
					$number_expl_lendable_retrait_inside = 0;
					$number_expl_available_retrait_inside = 0;
					$dest_loc = resa_loc_retrait($this->resa->id);
					if ($dest_loc!=0 && !empty($this->my_home_location) && $dest_loc != $this->my_home_location) {
						$number_expl_lendable_retrait_inside = $this->resa->get_number_expl_lendable($dest_loc);
						if ($number_expl_lendable_retrait_inside) {
							//Un exemplaire est-il dispo ?
							$number_expl_available_retrait_inside = $number_expl_lendable_retrait_inside - $this->resa->get_number_expl_out($dest_loc) - $this->resa->get_number_expl_in_circ($dest_loc);
							if ($number_expl_available_retrait_inside) {
								$display = "<strong>".$msg['expl_resa_available']."</strong>";
								$display .= $this->get_display_other_location_lendable($dest_loc);
							} else {
								// rien n'est disponible, on trouve la date du premier retour sur la loc de retrait
								$loan_return_date = $this->get_loan_return_date(0, $dest_loc);
								if ($loan_return_date) {
									$display = $loan_return_date;
								}
							}
						}
					}
					if ($number_expl_available_retrait_inside==0) {
						//on a pas d'exemplaires sur le site de retrait
						//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
						$query = "SELECT id_transfert, motif_refus, etat_demande FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
						$this->no_aff=0;
						$tresult = pmb_mysql_query($query);
						if (pmb_mysql_num_rows($tresult)) {
						    $trow = pmb_mysql_fetch_assoc($tresult);
						    //on a un transfert en cours
						    if ($trow["etat_demande"] == 4) {
						        $display = "<strong>" . $msg["transferts_circ_menu_refuse"] . "</strong> : ".$trow['motif_refus'];
						    } else {
						        $display = "<strong>" . $msg["transferts_circ_resa_lib_en_transfert"] . "</strong>";
						    }
						} else {
							if($f_loc) {
								$number_expl_transferts_lendable_outside = $this->resa->get_number_expl_transferts_lendable($f_loc, true);
							} else {
								$dest_loc = resa_loc_retrait($this->resa->id);
								$number_expl_transferts_lendable_outside = $this->resa->get_number_expl_transferts_lendable($dest_loc, true);
							}
							if($number_expl_transferts_lendable_outside>=$this->rank)	{
								$this->lien_transfert = true;
								if($this->resa->transfert_resa_dispo($f_loc)){
									$display = $msg["resa_expl_dispo_other_location"];
									//on trouve la date du premier retour
									if($loan_return_date)$display = $msg["resa_condition"]." : ".$loan_return_date."<br>".$display;
									$display .= $this->get_display_other_location_lendable($f_loc, true);
								}
							}
						}
					}
				}
			}
		}
		return $display;
	}
	
	protected function get_display_availability_next_rank_not_reserved($total_available=0, $location=0, $outside=false) {
		global $msg;
		
		$display = '';
// 		$total_reserved = $this->resa->get_number_expl_reserved();
// 		if ($total_reserved == ($this->rank-1)) {
			$total_reserved_localized = $this->resa->get_number_expl_reserved($location, $outside);
			$total_available_not_reserved = $total_available - $total_reserved_localized;
			if($total_available_not_reserved>0) {
				if($outside) {
					if($this->get_other_location_lendable($location, $outside) == $this->my_home_location) {
						$display .= "<strong>".$msg["expl_resa_available"]."</strong>";
						$display .= $this->get_display_other_location_lendable($location, $outside);
					} else {
						$display .= $msg["resa_expl_dispo_other_location"];
						$display .= $this->get_display_other_location_lendable($location, $outside);
					}
				} else {
					// un exemplaire est disponible pour le réservataire (affichage : disponible)
					$display .= "<strong>".$msg['expl_resa_available']."</strong>";
					if(!empty($this->my_home_location) && $location != $this->my_home_location) {
						$display .= $this->get_display_other_location_lendable($location, $outside);
					}
				}
			}
// 		}
		return $display;
	}
	
	protected function get_number_subtracted_rank($number) {
		$number = intval($number);
		$number = $number - ($this->rank-1);
		if($number < 0) {
			return 0;
		}
		return $number;
	}
	
	protected function get_display_availability_next_rank($info_gestion=NO_INFO_GESTION) {
		global $msg;
		global $pmb_transferts_actif;
		
		$display = '';
		if($this->resa_cb && $this->resa->formatted_date_fin) $display = "<strong>".$msg['expl_reserve']."</strong>";
		if ($this->lien_deja_affiche) {
			$this->lien_transfert = false;
		}
		switch ($info_gestion) {
			case GESTION_INFO_GESTION:
			case NO_INFO_GESTION;
			case LECTEUR_INFO_GESTION:
				if (!$this->lien_transfert && !$this->lien_deja_affiche) {
					if ($pmb_transferts_actif=="1") {
						$dest_loc = resa_loc_retrait($this->resa->id);
						// nombre d'exemplaires disponibles à ce stade du rang
						$number_expl_available_inside = $this->get_number_subtracted_rank($this->resa->get_number_expl_available($dest_loc));
						
						//S'il n'y a aucun exemplaire dispo pour le rang en cours, on va regarder ailleurs...
						if ($number_expl_available_inside == 0) {
							if ($dest_loc!=0) {
								// nombre d'exemplaires disponibles à l'exterieur à ce stade du rang
								$number_expl_available_outside = $this->get_number_subtracted_rank($this->resa->get_number_expl_available($dest_loc, true));
								if ($number_expl_available_outside!=0) {
									//on en a au moins un ailleurs!
									//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
									$query = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert AND etat_transfert=0 AND origine=4 AND origine_comp=".$this->resa->id;
									$tresult = pmb_mysql_query($query);
									if (!pmb_mysql_num_rows($tresult)) {
										$this->lien_transfert = true;
										$this->lien_deja_affiche = true;
										
										//Si les rangs précédents ont été réservés, on regarde la disponibilité du suivant...
										$display .= $this->get_display_availability_next_rank_not_reserved($number_expl_available_outside, $dest_loc, true);
									}
								}
							}
						} else {
							//Si les rangs précédents ont été réservés, on regarde la disponibilité du suivant...
							$display .= $this->get_display_availability_next_rank_not_reserved($number_expl_available_inside, $dest_loc);
						}
					} else {
						//dans un contexte sans transferts
						//allons chercher les dates de retour
						if(empty($display)) {
							// nombre d'exemplaires disponibles
							$number_expl_available = $this->resa->get_number_expl_available();
							$indice = ($this->rank-1) - $number_expl_available;
							if($indice >= 0) {
								$loan_return_date = $this->get_loan_return_date($indice);
								if ($loan_return_date) {
									$display = $loan_return_date;
								}
							}
						}
					}
				}
				break;
		}
		return $display;
	}
	
	protected function get_number_expl_lendable() {
		$query = "SELECT * FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND statut_allow_resa=1 ";
		$query .= "AND ".$this->resa->get_restrict_expl_notice_query();
		$result = pmb_mysql_query($query);
		return pmb_mysql_num_rows($result);
	}
	
	/**
	 * Calcul de la colonne situation
	 * @param integer $info_gestion
	 */
	public function get_display($info_gestion=NO_INFO_GESTION) {
		//A-t-on déjà appelé cette méthode ? $display peut être égale à une chaîne vide
		if(!empty($this->display_already_called) && isset($this->display)) {
			return $this->display;
		}
		
		//Pour memo : la condition avant
		//if (($this->resa->id_notice != $this->precedenteresa_idnotice) || ($this->resa->id_bulletin != $this->precedenteresa_idbulletin)) {
		if ($this->rank == 1 || $this->is_first_availability()) {
			$this->display = $this->get_display_availability_first_rank($info_gestion);
		} else {
			if($this->rank <= $this->get_number_expl_lendable()) {
				$this->display = $this->get_display_availability_next_rank($info_gestion);
			} else {
				$this->display = '';
			}
		}
		$this->display_already_called = true;
		return $this->display;
	}
	
	public static function get_conditions() {
		global $msg;
		global $pmb_transferts_actif;
		
		$conditions = array(
				'expl_resa_available' => $msg['expl_resa_available'],
				'resa_expl_reserve' => $msg['resa_expl_reserve']
		);
		if($pmb_transferts_actif) {
			$conditions['transferts_circ_resa_lib_en_transfert'] = $msg['transferts_circ_resa_lib_en_transfert'];
		}
		return $conditions;
	}
	
	public function get_id_resa() {
		return $this->id_resa;
	}
	
	public function get_no_aff() {
		return $this->no_aff;
	}
	
	public function get_lien_deja_affiche() {
		return $this->lien_deja_affiche;
	}
	
	public function set_resa($resa) {
		$this->resa = $resa;
		return $this;
	}
	
	public function set_resa_cb($resa_cb) {
		$this->resa_cb = $resa_cb;
		return $this;
	}
	
	public function set_idlocation($idlocation) {
		$this->idlocation = $idlocation;
		return $this;
	}
	
	public function set_rank($rank) {
		$this->rank = $rank;
		return $this;
	}
	
	public function set_no_aff($no_aff) {
		$this->no_aff = $no_aff;
		return $this;
	}
	
	public function set_lien_deja_affiche($lien_deja_affiche) {
		$this->lien_deja_affiche = $lien_deja_affiche;
		return $this;
	}
	
	public function set_my_home_location($my_home_location) {
		$this->my_home_location = intval($my_home_location);
		return $this;
	}
}