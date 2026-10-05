<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_retour_class.php,v 1.25.8.1 2025/03/14 08:07:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/emprunteur.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/comptes.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/emprunteur.class.php");
require_once("$class_path/amende.class.php");
require_once("$class_path/calendar.class.php");
require_once("$class_path/pret.class.php");
require_once("$base_path/circ/pret_func.inc.php");
require_once("$include_path/resa_func.inc.php");

/*
 Pour effectuer un retour de pret:
 // Appel de la class retour:
 $retour = new retour();
 // Fonction qu effectue le retour d'un document
 $status_xml = $retour->do_retour($cb_doc);


 Fonction do_retour
 		Effectue le retour d'un document emprunté
 input:
 		$cb_doc	Cb du document

 output:
 		status
 				0 : pas d'erreur, le retour est effectué
 				-1 Erreur. Voir message d'erreur (error_message)
 		error_message
 				Message de l'erreur
 		retour_message
 		libelle:
 				Titre du document
 		type_doc
 		location
 		section
 		empr_nom
 		empr_prenom
 		empr_cb
 		...
 */

class retour {
	public $expl_cb;
	public $msg_293;
	public $msg_652;
	public $msg_294;
	public $msg_rfid_retour_emprunteur_titre;
	public $expl_id;
	public $error_message;
	public $status ;
	public $expl_section;
	public $expl_location;
	public $expl_typdoc;
	public $expl_cote;
	public $expl_statut;
	public $expl_codestat;
	public $expl_owner;
	public $libelle;
	public $expl_note;
	public $expl_comment;
	public $expl_bulletin;
	public $expl_notice;
	public $lastempr_cb;
	public $lastempr_nom;
	public $lastempr_prenom;
	public $pret_date;
	public $empr_cp;
	public $empr_cb;
	public $empr_nom;
	public $empr_prenom;
	public $empr_pays;
	public $empr_codestat;
	public $empr_msg;
	public $empr_date_adhesion;
	public $empr_date_expiration;
	public $groupes;
	public $empr_ville;
	public $empr_prof;
	public $empr_year;
	public $empr_categ;
	public $empr_sexe;
	public $empr_statut;
	public $empr_location;
	public $type_abt;
	public $pret_arc_id;
	public $codestat;
	public $pret_idempr;
	public $pret_idexpl;
	public $pret_retour;
	public $aff_pret_date;
	public $aff_pret_retour;
	public $id_resa;
	public $resa_idempr;
	public $resa_idnotice;
	public $resa_idbulletin;
	public $resa_date;
	public $resa_date_fin;
	public $aff_resa_date;
	public $aff_resa_date_fin;
	public $resa_cb;
	public $cb_reservataire;
	public $nom_reservataire;
	public $prenom_reservataire;
	public $id_reservataire;
	public $url_reservation;
	public $retour_message;
	public $nbparts;
	public $id_empr;
	public $niveau_relance;
	public $date_relance;
	public $printed;
	public $cpt_prolongation;
	public $type_doc;
	public $location;
	public $section;
	public $cb_expl;

	// constructeur
	public function __construct() {
		global $msg;
		$this->expl_cb = '';
		// Messages utiles au traitement javascript
		$this->msg_293=$msg[293];
		$this->msg_652=$msg[652];
		$this->msg_294=$msg[294];
		$this->msg_rfid_retour_emprunteur_titre=$msg['rfid_retour_emprunteur_titre'];
	}


	public function check_barcode($cb) {
		global $msg;

		$query = "select * from exemplaires where expl_cb='$cb' ";
		$result = pmb_mysql_query($query);
		$expl = pmb_mysql_fetch_object($result);
		if(!$expl->expl_id) {
			// exemplaire inconnu
			$this->error_message=$msg[367];
			$this->status=-1;
			return -1;
		} else {
			$this->expl_id = $expl->expl_id;
			$this->expl_cb = $expl->expl_cb;
			$this->expl_section = $expl->expl_section;
			$this->expl_location = $expl->expl_location;
			$this->expl_typdoc = $expl->expl_typdoc;
			$this->nbparts	 = $expl->expl_nbparts;
			$this->expl_cote = $expl->expl_cote;
			$this->expl_comment=$expl->expl_comment;
			// récupération des infos exemplaires
			if ($expl->expl_notice) {
				$notice = new mono_display($expl->expl_notice, 0);
				$this->libelle = $notice->header_texte;
				$this->expl_notice=$expl->expl_notice;
			} else {
				$bulletin = new bulletinage_display($expl->expl_bulletin);
				$this->libelle = $bulletin->display ;
				$this->expl_bulletin=$expl->expl_bulletin;
			}
			$pos=strpos($this->libelle,'<a');
			if($pos) $this->libelle = substr($this->libelle,0,strpos($this->libelle,'<a'));
			if ($expl->expl_lastempr) {
				// récupération des infos emprunteur
				$query_last_empr = "select empr_cb, empr_nom, empr_prenom from empr where id_empr='".$expl->expl_lastempr."' ";
				$result_last_empr = pmb_mysql_query($query_last_empr);
				if(pmb_mysql_num_rows($result_last_empr)) {
					$last_empr = pmb_mysql_fetch_object($result_last_empr);
					$this->lastempr_cb = $last_empr->empr_cb;
					$this->lastempr_nom = $last_empr->empr_nom;
					$this->lastempr_prenom = $last_empr->empr_prenom;
				}
			}
		}
		return 0;
	}


	// mise à jour des stat des infos du prêt
	public function maj_stat_pret () {
		global $empr_archivage_prets, $empr_archivage_prets_purge;

		$query = "update pret_archive set ";
		$query .= "arc_debut='".$this->pret_date."', ";
		$query .= "arc_fin=now(), ";
		if ($empr_archivage_prets) $query .= "arc_id_empr='".addslashes($this->id_empr)."', ";
		$query .= "arc_empr_cp='".			addslashes($this->empr_cp)		."', ";
		$query .= "arc_empr_ville='".		addslashes($this->empr_ville)	."', ";
		$query .= "arc_empr_prof='".		addslashes($this->empr_prof)	."', ";
		$query .= "arc_empr_year='".		addslashes($this->empr_year)	."', ";
		$query .= "arc_empr_categ='".		$this->empr_categ    			."', ";
		$query .= "arc_empr_codestat='".	$this->empr_codestat 			."', ";
		$query .= "arc_empr_sexe='".		$this->empr_sexe     			."', ";
		$query .= "arc_empr_statut='".		$this->empr_statut    	 		."', ";
		$query .= "arc_empr_location='".	$this->empr_location    	 	."', ";
		$query .= "arc_type_abt='".			$this->type_abt    	 			."', ";
		$query .= "arc_expl_typdoc='".		$this->expl_typdoc   			."', ";
		$query .= "arc_expl_id='".			$this->expl_id   				."', ";
		$query .= "arc_expl_notice='".		$this->expl_notice   			."', ";
		$query .= "arc_expl_bulletin='".	$this->expl_bulletin  			."', ";
		$query .= "arc_expl_cote='".		addslashes($this->expl_cote)	."', ";
		$query .= "arc_expl_statut='".		$this->expl_statut   			."', ";
		$query .= "arc_expl_location='".	$this->expl_location 			."', ";
		$query .= "arc_expl_section='".		$this->expl_section 			."', ";
		$query .= "arc_expl_codestat='".	$this->expl_codestat 			."', ";
		$query .= "arc_expl_owner='".		$this->expl_owner    			."', ";
		$query .= "arc_niveau_relance='".	$this->niveau_relance  			."', ";
		$query .= "arc_date_relance='".		$this->date_relance    			."', ";
		$query .= "arc_printed='".			$this->printed    				."', ";
		$query .= "arc_cpt_prolongation='".	$this->cpt_prolongation 		."' ";

		$query .= " where arc_id='".$this->pret_arc_id."' ";

		$res = pmb_mysql_query($query);
		// purge des vieux trucs
		if ($empr_archivage_prets_purge) {
			//on ne purge qu'une fois par session et par jour
			if (!isset($_SESSION["last_empr_archivage_prets_purge_day"]) || ($_SESSION["last_empr_archivage_prets_purge_day"] != date("m.d.y"))) {
				pmb_mysql_query("update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()") or die(pmb_mysql_error()."<br />"."update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()");
				$_SESSION["last_empr_archivage_prets_purge_day"] = date("m.d.y");
			}
		}

		return $res ;
	}

	public function check_pret() {
		global $msg;

		// récupération des infos du prêt
		$query = "select *, date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, IF(pret_retour>sysdate(),0,1) as retard from pret where pret_idexpl=".$this->expl_id." limit 1";
		$result = pmb_mysql_query($query);

		if(pmb_mysql_num_rows($result))
		{
			$pret = pmb_mysql_fetch_object($result);
			// le document était bien en prêt -> récupération des infos du prêt
			$this->pret_idempr = $pret->pret_idempr;
			$this->pret_idexpl = $pret->pret_idexpl;
			$this->pret_date = $pret->pret_date;
			$this->pret_retour = $pret->pret_retour;
			$this->aff_pret_date = $pret->aff_pret_date;
			$this->aff_pret_retour = $pret->aff_pret_retour;
			$this->pret_arc_id = $pret->pret_arc_id;
			$this->niveau_relance = $pret->niveau_relance;
			$this->date_relance = $pret->date_relance;
			$this->printed = $pret->printed;
			$this->cpt_prolongation  = $pret->cpt_prolongation;

			// récupération des infos emprunteur
			$query = "select * , date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion,
			date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration from empr where id_empr=".$pret->pret_idempr." limit 1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {

				// stockage des infos sur l'emprunteur
				$empr = pmb_mysql_fetch_object($result);
				$this->empr_cb = $empr->empr_cb;
				$this->id_empr = $empr->id_empr;
				$this->empr_nom = $empr->empr_nom;
				$this->empr_prenom = $empr->empr_prenom;
				$this->empr_ville = $empr->empr_ville;
				$this->empr_cp = $empr->empr_cp;
				$this->empr_pays = $empr->empr_pays;
				$this->empr_prof = $empr->empr_prof;
				$this->empr_year = $empr->empr_year;
				$this->empr_categ = $empr->empr_categ;
				$this->empr_codestat = $empr->empr_codestat;
				$this->empr_sexe = $empr->empr_sexe;
				$this->empr_statut = $empr->empr_statut;
				$this->empr_location = $empr->empr_location;
				$this->type_abt = $empr->type_abt;
				$this->empr_msg = $empr->empr_msg;
				$this->empr_date_adhesion = $empr->aff_empr_date_adhesion;
				$this->empr_date_expiration = $empr->aff_empr_date_expiration;

				$query_groupe = "select libelle_groupe from groupe, empr_groupe where empr_id='".$pret->pret_idempr."' and groupe_id=id_groupe";
				$result_g = pmb_mysql_query($query_groupe);
				$groupesarray = array();
				while ($groupes=pmb_mysql_fetch_object($result_g)) $groupesarray[]=$groupes->libelle_groupe ;
				$this->groupes = @implode("/",$groupesarray);
			}
		}

		return 0;
	}

	public function check_resa() {
		global $msg;

		if (!$this->expl_notice) $this->expl_notice=0;
		if (!$this->expl_bulletin) $this->expl_bulletin=0 ;
		$rqt = "select *, IF(resa_date_fin>sysdate(),0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date from resa where resa_idnotice='".$this->expl_notice."' and resa_idbulletin='".$this->expl_bulletin."' order by resa_date limit 1 ";

		$result = pmb_mysql_query($rqt) or die (pmb_mysql_error()) ;
		if(pmb_mysql_num_rows($result)) {
			// des réservations ont été trouvées -> récupération des infos résa
			$resa = pmb_mysql_fetch_object($result);
			$this->id_resa = $resa->id_resa;
			$this->resa_idempr = $resa->resa_idempr;
			$this->resa_idnotice = $resa->resa_idnotice;
			$this->resa_idbulletin = $resa->resa_idbulletin;
			$this->resa_date = $resa->resa_date;
			$this->resa_date_fin = $resa->resa_date_fin;
			$this->aff_resa_date = $resa->aff_resa_date;
			$this->aff_resa_date_fin = $resa->aff_resa_date_fin;
			$this->resa_cb = $resa->resa_cb;

			// récupération des infos sur le réservataire
			$query = "select empr_nom, empr_prenom, empr_cb, id_empr from empr where id_empr=".$resa->resa_idempr." limit 1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				// stockage des infos sur le réservataire
				$empr = pmb_mysql_fetch_object($result);
				$this->cb_reservataire = $empr->empr_cb;
				$this->nom_reservataire = $empr->empr_nom;
				$this->prenom_reservataire = $empr->empr_prenom;
				$this->id_reservataire = $empr->id_empr;
			}
			$this->error_message=$msg["rfid_retour_document_reserve_message"];
		}
		return 0;
	}


	public function do_retour_doc() {
		global $msg;
		global $pmb_gestion_amende,$pmb_gestion_financiere,$pmb_blocage_retard, $pmb_blocage_max, $pmb_blocage_delai, $pmb_blocage_coef;

		// récupération localisation exemplaire
		$query = "select t.tdoc_libelle as type_doc";
		$query .= ", l.location_libelle as location";
		$query .= ", s.section_libelle as section";
		$query .= " from docs_type t";
		$query .= ", docs_location l";
		$query .= ", docs_section s";
		$query .= " where t.idtyp_doc=".$this->expl_typdoc;
		$query .= " and l.idlocation=".$this->expl_location;
		$query .= " and s.idsection=".$this->expl_section;
		$query .= " limit 1";

		$result = pmb_mysql_query($query);
		$info_doc = pmb_mysql_fetch_object($result);

		$this->type_doc=$info_doc->type_doc;
		if($this->nbparts>1)
					$this->type_doc.=" (".$this->nbparts.")";
		$this->location=$info_doc->location;
		$this->section=$info_doc->section;

		if ($this->expl_note) {
			$this->error_message=$msg[377];
		}
		if ($this->pret_idempr) {
			//choix du mode de calcul
			$loc_calendar = 0;
			global $pmb_utiliser_calendrier, $pmb_utiliser_calendrier_location;
			if (($pmb_utiliser_calendrier==1) && $pmb_utiliser_calendrier_location) {
				$loc_calendar = $this->expl_location;
			}

			// calcul du retard éventuel
			$rqt_date = "select ((TO_DAYS(CURDATE()) - TO_DAYS('$this->pret_retour'))) as retard ";
			$resultatdate=pmb_mysql_query($rqt_date);
			$resdate=pmb_mysql_fetch_object($resultatdate);
			$retard = $resdate->retard;
			if($retard > 0) {
				//Calcul du vrai nombre de jours
				$date_debut=explode("-",$this->pret_retour);
				$ndays=calendar::get_open_days($date_debut[2],$date_debut[1],$date_debut[0],date("d"),date("m"),date("Y"),$loc_calendar);
				if ($ndays>0) {
					$retard = (int)$ndays;
					$this->error_message=$msg[369]." : ".$retard." ".$msg[370];
				} else
					$this->error_message = $msg["calendrier_active_aucun_retard"];
			}
			//Calcul du blocage
			if ($pmb_blocage_retard) {
				$date_debut=explode("-",$this->pret_retour);
				$ndays=calendar::get_open_days($date_debut[2],$date_debut[1],$date_debut[0],date("d"),date("m"),date("Y"),$loc_calendar);
				if ($ndays>$pmb_blocage_delai) {
					$ndays=$ndays*$pmb_blocage_coef;
					if (($ndays>$pmb_blocage_max)&&($pmb_blocage_max!=0)) {
						if ($pmb_blocage_max!=-1) {
							$ndays=$pmb_blocage_max;
						}
					}
				} else $ndays=0;
				if ($ndays>0) {
					$informations = pret::update_blocage($this->pret_idempr, $this->pret_idexpl, $ndays, $loc_calendar);
					$this->error_message=$informations['message'];
				}
			}
			//Vérification des amendes
			if (($pmb_gestion_financiere) && ($pmb_gestion_amende)) {
				$amende=new amende($this->pret_idempr);
				$amende_t=$amende->get_amende($this->pret_idexpl);
				//Si il y a une amende, je la débite
				if ($amende_t["valeur"]) {
					$this->error_message=$msg["finance_retour_amende"]."&nbsp;: ".comptes::format($amende_t["valeur"]);
					$compte_id=comptes::get_compte_id_from_empr($this->pret_idempr,2);
					if ($compte_id) {
						$cpte=new comptes($compte_id);
						if ($cpte->id_compte) {
							$cpte->record_transaction("",$amende_t["valeur"],-1,sprintf($msg["finance_retour_amende_expl"],$this->expl_cb),0);
							$this->error_message.=" ".$msg["finance_retour_amende_recorded"];
						}
					}
				}
			}
			// Suppression prêt et la mise en table de stat
			$query = "delete from pret where pret_idexpl = '" . $this->pret_idexpl . "' ";
			$result = pmb_mysql_query($query);

			if($result) {
				$this->retour_message=$msg["retour_ok"];
				if (!$this->maj_stat_pret()) {
					// impossible de maj en table stat
					$this->error_message=$msg[371];
				}
			} else {
				// impossible de supprimer en table pret
				$this->error_message=$msg[372];
			}
			// traitement de l'éventuelle réservation
			if ($this->resa_idempr) {
				// le doc en retour peut servir à valider une résa suivante
				if (!verif_cb_utilise ($this->cb_expl)) {
					$affect = affecte_cb ($this->cb_expl) ;
					// affichage message de réservation
					if ($affect) {
						$this->error_message=$msg["rfid_retour_document_reserve_message"];
						$this->url_reservation="./circ.php?categ=pret&form_cb=".rawurlencode($this->cb_reservataire);
					} // fin if affect
				} // fin if !verif_cb_utilise
			} // fin if resa
		} else {
			$this->error_message=$msg[605];
		}
	}


	public function do_retour( $cb_expl) {
		$this->cb_expl=$cb_expl;
		$this->error_message='';
		$this->status=0;
		if ($this->check_barcode($cb_expl)==0) {
			$this->check_pret();
			$this->check_resa();
			$this->do_retour_doc();
		}
		$array=array();
		$array[0]=$this;
		$buf_xml = array2xml($array);
		return $buf_xml;
	}

// Fin class
}


?>