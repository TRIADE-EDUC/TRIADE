<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_edition_treat_ui.class.php,v 1.13 2021/09/15 09:37:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_reservations_edition_treat_ui extends list_reservations_edition_ui {
	
	public static function set_globals_from_selected_filters() {
		global $f_loc, $f_dispo_loc;
		global $pmb_transferts_actif, $pmb_location_reservation, $deflt_resas_location;
		
		$objects_type = str_replace('list_', '', static::class);
		$initialization = $objects_type.'_initialization';
		global ${$initialization};
		if(empty(${$initialization}) || ${$initialization} != 'reset') {
		    $applied_action = $objects_type.'_applied_action';
		    global ${$applied_action};
		    if(!empty(${$applied_action}) || ${$applied_action} == 'apply') {
		        if(empty($f_loc)) {
		            global $reservations_edition_treat_ui_resa_loc_retrait;
		            $f_loc = $reservations_edition_treat_ui_resa_loc_retrait;
		        }
		        if(empty($f_dispo_loc)) {
		            global $reservations_edition_treat_ui_available_location;
		            $f_dispo_loc = $reservations_edition_treat_ui_available_location;
		        }
		    } else {
		        if(isset($_SESSION['list_'.$objects_type.'_filter']['f_loc'])) {
		            $f_loc = $_SESSION['list_'.$objects_type.'_filter']['f_loc'];
		        }
		    }
		}
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
		    if ($f_loc=="")	$f_loc = $deflt_resas_location;
		}
	}
	
	public static function set_globals_from_json_filters($json_filters) {
		global $f_loc, $f_dispo_loc;
		global $pmb_transferts_actif, $pmb_location_reservation, $deflt_resas_location;
		
		$filters = (!empty($json_filters) ? encoding_normalize::json_decode($json_filters, true) : array());
		if(empty($f_loc) && isset($filters['f_loc']) && $filters['f_loc'] !== '') {
			$f_loc = $filters['f_loc'];
		}
		if(empty($f_dispo_loc) && isset($filters['available_location']) && $filters['available_location'] !== '') {
			$f_dispo_loc = $filters['available_location'];
		}
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			if ($f_loc=="")	$f_loc = $deflt_resas_location;
		}
	}
	
	protected function add_object($row) {
		global $pmb_transferts_actif;
		
		$empr_location = emprunteur::get_location($row->resa_idempr)->id;
		if($this->is_visible_object($empr_location, $row->resa_loc_retrait)) {
			$resa = new reservation($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin);
			
			// on compte le nombre total d'exemplaires prêtables pour la notice
			$total_ex = $resa->get_number_expl_lendable();
			
			// on compte le nombre d'exemplaires sortis
			$total_sortis = $resa->get_number_expl_out();
			
			// on en déduit le nombre d'exemplaires disponibles
			$total_dispo = $total_ex - $total_sortis ;
			
			// on a au moins UN dispo :
			if ($total_dispo > 0) {
				$available = true;
				$rank = recupere_rang($row->resa_idempr, $row->resa_idnotice, $row->resa_idbulletin) ;
				if($rank>$total_dispo)	$available = false;
				
				if ($pmb_transferts_actif == "1") {
					$dest_loc = resa_loc_retrait($resa->id);
					
					if ($dest_loc!=0) {
						$total_ex = $resa->get_number_expl_lendable($dest_loc);
						if ($total_ex==0) {
							//on a pas d'exemplaires sur le site de retrait
							//on regarde si on en ailleurs
							$total_ex = $resa->get_number_expl_lendable($dest_loc, true);
							if ($total_ex!=0) {
								//on en a au moins un ailleurs!
								//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
								$query = "SELECT id_transfert FROM transferts WHERE etat_transfert=0 AND origine=4 AND origine_comp=".$resa->id." limit 1";
								$tresult = pmb_mysql_query($query);
								if (pmb_mysql_num_rows($tresult)) {
									//on a un transfert en cours
									$available = false;
								} elseif($total_ex>=$rank)	{
									if(!$resa->transfert_resa_dispo($dest_loc)){
										//non disponible dans une autre localisation
										$available = false;
									}
								}
							}
						}
					}
				}
				// un exemplaire est disponible pour cette resa
				if ($available) {
					$tableau_expl_dispo = expl_dispo ($row->resa_idnotice, $row->resa_idbulletin) ;
					if (count($tableau_expl_dispo)) {
						for ($i=0;$i<count($tableau_expl_dispo);$i++) {
							if (!$this->filters['available_location'] || ($tableau_expl_dispo[$i]['idlocation'] == $this->filters['available_location'])) {
								$resa->expl_cb = $tableau_expl_dispo[$i]['expl_cb'];
								$resa->expl_id = $tableau_expl_dispo[$i]['expl_id'];
								$exemplaire = new exemplaire($tableau_expl_dispo[$i]['expl_cb'], $tableau_expl_dispo[$i]['expl_id']);
								$resa->set_exemplaire($exemplaire);
								if($this->is_visible_exemplaire($exemplaire)) {
    								$this->objects[] = clone($resa);
    								$this->location_reservations[$row->id_resa] = $empr_location;
								}
							}
						}
					}
				}
			}
		}
	}
	
	protected function get_form_title() {
		global $msg;
		
		return $msg['edit_resa_menu_a_traiter'];
	}
	
	protected function init_available_filters() {
		parent::init_available_filters();
		unset($this->available_filters['main_fields']['montrerquoi']);
		unset($this->available_filters['main_fields']['removal_location']);
	}
	
	protected function init_default_selected_filters() {
		global $pmb_transferts_actif, $pmb_location_reservation;
		
		if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
			$this->add_selected_filter('resa_loc_retrait');
			$this->add_selected_filter('available_location');
		} else {
			parent::init_default_selected_filters();
		}
	}
	
	protected function init_default_columns() {
		global $pmb_transferts_actif;
		
		$this->add_column('rank');
		$this->add_column('empr');
		$this->add_column('empr_location', 'edit_resa_empr_location');
		$this->add_column('record');
		$this->add_column('expl_location');
		$this->add_column('section');
		$this->add_column('expl_cote');
		$this->add_column('statut');
		$this->add_column('support');
		$this->add_column('expl_cb');
		if ($pmb_transferts_actif=="1") {
			$this->add_column('resa_loc_retrait');
		}
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg[350].": ".$msg['edit_resa_menu_a_traiter']);
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu_a_traiter']."</h1>";
	}
}