<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa.class.php,v 1.40.2.2 2024/08/30 15:09:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe emprunteur
// classe remaniée le 7.12.2003. : prise en compte de résas sur le bulletinage

if (!defined('RESA_CLASS')) {
	define('RESA_CLASS', 1);

	global $class_path, $include_path;
	require_once("$class_path/quotas.class.php");
	require_once("$class_path/serials.class.php");
	require_once("$include_path/resa_func.inc.php");
	
	class reservation {

		//---------------------------------------------------------
		//			Propriétés
		//---------------------------------------------------------
		public $id=0; // id MySQL de la réservation
		public $id_empr=0; // id MySQL emprunteur
		public $id_notice=0; // id notice
		public $id_bulletin=0; // id bulletin si applicable
		public $date			= '';
		public $formatted_date 	= '';
		public $date_debut		= '';
		public $formatted_date_debut 	= '';
		public $date_fin		= '';
		public $formatted_date_fin 		= '';
		public $confirmee		= 0;
		public $loc_retrait		= 0;
		public $tstamp=''; // time stamp de la réservation
		public $message=''; // message d'erreur éventuel
		public $statut=0; // statuts possibles
		public $notice=''; // notice abrégée (pour affichage)
		public $force=0; // Forcage de la notice en cas de dépassement de quota de réservation
		public $expl_cb='';
		public $expl_id=0;
		protected $exemplaire;
		protected $on_empr_fiche = false;
		/* note les statuts possibles :
		0	->	aucun problème pour réserver
		1	->	aucun exemplaire ne peut être reservé
		2	->	un ou des exemplaires peuvent être reservés et un au moins des exemplaires est disponible
		 */
		
		//---------------------------------------------------------
		//			Méthodes
		//---------------------------------------------------------

		// <----------------- constructeur ------------------>
		public function __construct($id_empr=0, $id_notice=0, $bulletinage=0, $cb='') {
			$this->id_empr = intval($id_empr);
			$this->id_notice = intval($id_notice);
			$this->service = new stdClass();
			if($bulletinage) {
				$this->id_bulletin = intval($bulletinage);
				$this->id_notice = 0;
			}
			$this->fetch_data();
			if($cb) {
				$query = "select expl_id,expl_notice,expl_bulletin from exemplaires where expl_cb='$cb' limit 1";
				$result = pmb_mysql_query($query);
				if ($expl = pmb_mysql_fetch_object($result)) {
					$this->id_notice = $expl->expl_notice;
					$this->id_bulletin = $expl->expl_bulletin;
					$this->expl_id = $expl->expl_id;
					$this->expl_cb = $cb;
				} else {
					$this->id_bulletin = 0;
					$this->id_notice = 0;
				}
			}
			
			if ($this->id_notice) {
				$notice=new notice_affichage($this->id_notice, 0, 0, 0);
				$notice->do_header();
				$this->notice=$notice->notice_header;
			} elseif ($this->id_bulletin) {
			
				$query='SELECT num_notice FROM bulletins WHERE bulletin_id='.$this->id_bulletin;
				$result=pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					while ($bulletin=pmb_mysql_fetch_object($result)) {
						$notice=new notice_affichage($bulletin->num_notice, 0, 0, 0);
						$notice->bulletin_id=$this->id_bulletin;
						$notice->do_header();
						$this->notice=$notice->notice_header;
					}
				}
			}
		}

		protected function fetch_data() {
			if($this->id_empr && ($this->id_notice || $this->id_bulletin)) {
				$query = "select * from resa where resa_idempr = ".$this->id_empr." AND resa_idnotice = ".$this->id_notice." AND resa_idbulletin = ".$this->id_bulletin;
				$result = pmb_mysql_query($query);
				if($query && pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					$this->id = $row->id_resa;
					$this->date = $row->resa_date;
					$this->formatted_date = format_date($this->date);
					$this->date_debut = $row->resa_date_debut;
					$this->formatted_date_debut = format_date($this->date_debut);
					$this->date_fin = $row->resa_date_fin;
					$this->formatted_date_fin = format_date($this->date_fin);
					$this->confirmee = $row->resa_confirmee;
					$this->loc_retrait = $row->resa_loc_retrait;
				}
			}
		}
		
		public function get_empr_info() {
			$query = "select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb
					  from resa left join empr on resa_idempr=id_empr where resa_idnotice='".$this->id_notice . "' and resa_idbulletin='".$this->id_bulletin . "' 
					  order by resa_date limit 1";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				if(($row=pmb_mysql_fetch_object($result))) {
					$link="<a href=\"./circ.php?categ=pret&form_cb=".$row->empr_cb."\">".$row->nom_prenom."</a>";
					return $link;
				}
			}
			return '';
		}
		public function get_empr_info_cb() {
			$query="select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb
		              from resa, empr where resa_cb='".$this->expl_cb."' and resa_idempr=id_empr limit 1";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				if(($row=pmb_mysql_fetch_object($result))) {
					$link="<a href=\"./circ.php?categ=pret&form_cb=".$row->empr_cb."\">".$row->nom_prenom."</a>";
					return $link;
				}
			}
			return '';
		}
		public static function check_expl_reservable($id_expl) {
			$id_expl = intval($id_expl);
			$query = "select e.expl_cb as cb, e.expl_id as id, s.statut_allow_resa as reservable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
			$query .= " from exemplaires e, docs_statut s, docs_location l, docs_section se";
			$query .= " where e.expl_id=$id_expl";
			$query .= " and s.idstatut=e.expl_statut";
			$query .= " and l.idlocation=e.expl_location";
			$query .= " and se.idsection=e.expl_section";
			$query .= " and s.statut_visible_opac=1";
			$query .= " and l.location_visible_opac=1";
			$query .= " and se.section_visible_opac=1";
			$query .= " limit 1";

			$result=pmb_mysql_query($query);
			if (($expl=pmb_mysql_fetch_array($result))) {
				if (!$expl['reservable']) {
					// l'exemplaire est en consultation sur place ou pas réservable
					return 0;
				}
			} else {
				// exemplaire inconnu
				return 0;
			}
			// on check si l'exemplaire a une réservation
			$query="select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb from resa left join empr on resa_idempr=id_empr where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' order by resa_date limit 1";
			$result=pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				// l'exemplaire a une réservation
				return 0;

			}
			// l'exemplaire est disponible pour valider une réservation
			return 1;
		}
		
		public function get_query_expl_lendable() {
			global $pmb_location_reservation;
			
			$query = "select expl_id ,expl_cb from exemplaires e, docs_statut s
				where s.idstatut=e.expl_statut and s.statut_allow_resa=1";
			$query .= " and ".$this->get_restrict_expl_notice_query();
			if($pmb_location_reservation) {
				$query_loc = "select empr_location from empr where id_empr=".$this->id_empr;
				$res = pmb_mysql_query($query_loc);
				$empr = pmb_mysql_fetch_object($res);
				$query.=" and e.expl_location in (select resa_loc from resa_loc where resa_emprloc=".$empr->empr_location.") ";
			}
			return $query;
		}
		
		public function check_localisation_expl() {
			global $msg, $pmb_transferts_actif, $transferts_choix_lieu_opac;
			// recup de la localisation de l'emprunteur
			$query = "select empr_location from empr where id_empr=".$this->id_empr;
			$res = pmb_mysql_query($query);
			$empr = pmb_mysql_fetch_object($res);
			$empr_location=$empr->empr_location;

			if($this->id_notice) $field_expl=" expl_notice=$this->id_notice ";
			else $field_expl=" expl_bulletin=$this->id_bulletin ";
			// vérifier si un exemplaire est disponible dans les localisation autorisées

			if ($pmb_transferts_actif && $transferts_choix_lieu_opac != 3) {
				$transf_possible=" and (s.transfert_flag=1 or expl_location=$empr_location )";
				$requete="select expl_id from exemplaires e ,docs_statut s
			                where  $field_expl and  expl_location in (select resa_loc  from resa_loc where resa_emprloc=$empr_location )
			                and s.idstatut=e.expl_statut and s.statut_allow_resa=1 $transf_possible limit 1";

				$res = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($res)) {
					return TRUE;
				}
				$this->message="<strong>".$msg["resa_no_expl_in_location_transferable"]."</strong>";
			} else {
				$requete="select expl_id from exemplaires e ,docs_statut s
			                where  $field_expl and  expl_location in (select resa_loc  from resa_loc where resa_emprloc=$empr_location )
		                    and s.idstatut=e.expl_statut and s.statut_allow_resa=1 limit 1";

				$res=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($res)) {
					return TRUE;
				}
				// recup de la liste des localisations ou l'emprunteur peut réserver un exemplaire
				$requete="select idlocation, location_libelle from resa_loc, docs_location  where resa_emprloc=$empr_location and idlocation=resa_loc";
				$res=pmb_mysql_query($requete);
				$locations="";
				if (pmb_mysql_num_rows($res)) {
					while (($row=pmb_mysql_fetch_object($res))) {
						if($locations) $locations .=", ";
						$locations .= translation::get_translated_text($row->idlocation, "docs_location", "location_libelle", $row->location_libelle);
					}
				}
				$this->message="<strong>".str_replace("!!loc_liste!!", $locations,$msg["resa_no_expl_in_location"]) . "</strong>";
			}
			return FALSE;
		}

		public function can_reserve() {
			global $quota_resa;
			global $pmb_location_reservation;

			$this->service->error="";
			if (!$this->empr_exists()) {
				$this->service->error="check_empr_exists";
				return FALSE;
			}
			if (!$this->notice_exists()) {
				$this->service->error="check_notice_exists";
				return FALSE;
			}
			if($this->waiting_already_loaned()){
				$this->service->error="check_waiting_already_loaned";
				return FALSE;
			}
			//les quotas
			if (!$quota_resa) {
				//Si un quota atteint (check_quota inclu le message + la mise à 0 ou 1 de $this->force)
				$ret=$this->check_quota();
				if ($ret["ERROR"]) {
					$this->service->message=$ret["MESSAGE"];
					$this->service->error="check_quota";
					return FALSE;
				}
			}
			if ($this->resa_exists()) {
				$this->service->error="check_resa_exists";
				return FALSE;
			}
			if ($this->allready_loaned()) {
				$this->service->error="check_allready_loaned";
				return FALSE;
			}
			// check_statut inclus la possibilité de réserver ou pas les docs dispo
			if ($this->check_statut()) {
				// $this->service->error est affecté dans check_statut()
				return FALSE;
			}
			if ($pmb_location_reservation) {
				if (!$this->check_localisation_expl()) {
					$this->service->error="check_localisation_expl";
					return FALSE;
				}
			}
			return TRUE;
		}

		// <----------------- add() : ajout d'une réservation ------------------>
		public function add($idloc_retrait=0) {
			global $msg;
			global $quota_resa;
			global $pmb_location_reservation;

			$this->service->error="";
			if (!$this->empr_exists()) {
				$this->service->error="check_empr_exists";
				return FALSE;
			}
			if (!$this->notice_exists()) {
				$this->service->error="check_notice_exists";
				return FALSE;
			}
			if($this->waiting_already_loaned()){
				$this->service->error="check_waiting_already_loaned";
				return FALSE;
			}
			//les quotas
			if (!$quota_resa) {
				//Si un quota atteint (check_quota inclu le message + la mise à 0 ou 1 de $this->force)
				$ret=$this->check_quota();
				if ($ret["ERROR"]) {
					$this->service->message=$ret["MESSAGE"];
					$this->service->error="check_quota";
					return FALSE;
				}
			}

			if ($this->resa_exists()) {
				$this->service->error="check_resa_exists";
				return FALSE;
			}
			if ($this->allready_loaned()) {
				$this->service->error="check_allready_loaned";
				return FALSE;
			}
			// check_statut inclus la possibilité de réserver ou pas les docs dispo
			if ($this->check_statut()) {
				// $this->service->error est affecté dans check_statut()
				return FALSE;
			}
			if ($pmb_location_reservation) {
				if (!$this->check_localisation_expl()) {
					$this->service->error="check_localisation_expl";
					return FALSE;
				}
			}
			// tout est OK, écriture de la réservation en table
			$query="INSERT INTO resa (id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_date, resa_loc_retrait) ";
			$query .= "VALUES ('', '" . $this->id_empr . "', ";
			if ($this->id_notice) $query .= "'".$this->id_notice."',0 ,";
			elseif ($this->id_bulletin)
				$query .= "0, '" . $this->id_bulletin . "',";
			$query .= " SYSDATE(),";

			$query .= "'$idloc_retrait' )";
			$result=pmb_mysql_query($query);
			if (!$result) {
				$this->message="$query -> $msg[resa_no_create]";
				$this->service->error="resa_no_create";
				return FALSE;
			} else {
				$this->id=pmb_mysql_insert_id();
				$this->message=$msg["resa_ajoutee"];

				// Archivage de la résa: info lecteur et notice et nombre d'exemplaire
				$rqt="SELECT * FROM empr WHERE id_empr=" . $this->id_empr;
				$empr=pmb_mysql_fetch_object(pmb_mysql_query($rqt));

				$id_notice=$id_bulletin=0;
				if ($this->id_notice) {
					$id_notice=$this->id_notice;
					$query="SELECT count(*) FROM exemplaires where expl_notice='$id_notice'";
				} elseif ($this->id_bulletin) {
					$id_bulletin=$this->id_bulletin;
					$query="SELECT count(*) FROM exemplaires where expl_bulletin='$id_bulletin'";
				}
				$nb_expl=pmb_mysql_result(pmb_mysql_query($query), 0);

				$query="INSERT INTO resa_archive SET
						resarc_id_empr='" . $this->id_empr . "',
						resarc_idnotice='" . $id_notice . "',
						resarc_idbulletin='" . $id_bulletin."',
						resarc_date=SYSDATE(),
						resarc_loc_retrait='$idloc_retrait',
						resarc_from_opac= 1,
						resarc_empr_cp ='" . addslashes($empr->empr_cp)."',
						resarc_empr_ville='" . addslashes($empr->empr_ville)."',
						resarc_empr_prof='" . addslashes($empr->empr_prof)."',
						resarc_empr_year='" . $empr->empr_year . "',
						resarc_empr_categ='" . $empr->empr_categ . "',
						resarc_empr_codestat='" . $empr->empr_codestat."',
						resarc_empr_sexe='" . $empr->empr_sexe . "',
						resarc_empr_location='" . $empr->empr_location."',
						resarc_expl_nb='$nb_expl'
						";
				pmb_mysql_query($query);
				$stat_id=pmb_mysql_insert_id();
				// Lier achive et résa pour suivre l'évolution de la résa
				$query="update resa SET resa_arc='$stat_id' where id_resa='".$this->id . "'";
				pmb_mysql_query($query);
			}
			return TRUE;
		}

		// <----------------- delete() : suppression d'une réservation ------------------>
		public function delete() {
			global $msg;
			//  suppression  de la réservation de la table des réservations
			$id_notice=$id_bulletin=0;
			if ($this->id_notice) {
				$id_notice=$this->id_notice;
				$query="delete from resa where resa_idempr=" . $this->id_empr." and resa_idnotice=" . $this->id_notice;
			} elseif ($this->id_bulletin) {
				$id_bulletin=$this->id_bulletin;
				$query="delete from resa where resa_idempr=" . $this->id_empr." and resa_idbulletin=" . $this->id_bulletin;
			}
			$result=pmb_mysql_query($query);
			// archivage
			$rqt_arch="UPDATE resa_archive SET resarc_anulee=1 WHERE resarc_id_empr='".$this->id_empr."' and resarc_idnotice='".$id_notice."' and	resarc_idbulletin='".$id_bulletin."' ";
			pmb_mysql_query($rqt_arch);

			if (!$result) {
				$this->message=$msg["resa_no_suppr"];
				return FALSE;
			} else {
				// on checke l'existence d'autres réservataires
				$query="select e.empr_nom, e.empr_prenom, e.empr_cb from resa r, empr e";
				$query .= " where r.resa_idempr=e.id_empr";
				if ($this->id_notice)
					$query .= " and r.resa_idnotice=" . $this->id_notice;
				elseif ($this->id_bulletin)
					$query .= " and r.resa_idbulletin=" . $this->id_bulletin;
				$query .= " order by r.resa_date limit 1";
				$result=pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {

					// d'autres réservataires existent
					$next_empr=pmb_mysql_fetch_object($result);

					$this->message=$msg['resa_supprimee'];

					// on regarde la disponibilité du document
					// on compte le nombre total d'exemplaires pour la notice
					if ($this->id_notice)
						$query="select count(1) from exemplaires where expl_notice=".$this->id_notice;
					elseif ($this->id_bulletin)
						$query="select count(1) from exemplaires where expl_bulletin=".$this->id_bulletin;
					$result=pmb_mysql_query($query);
					$total_ex=pmb_mysql_result($result, 0, 0);

					// on compte le nombre d'exemplaires sortis
					$query="select count(1) from exemplaires e, pret p";
					if ($this->id_notice)
						$query .= " where e.expl_notice=" . $this->id_notice;
					elseif ($this->id_bulletin)
						$query .= " where e.expl_bulletin=".$this->id_bulletin;
					$query .= " and p.pret_idexpl=e.expl_id";
					$result=pmb_mysql_query($query);
					$total_sortis=pmb_mysql_result($result, 0, 0);

					// on en déduit le nombre d'exemplaires disponibles
					$total_dispo=$total_ex - $total_sortis;

					if ($total_dispo) {
						$this->message .= " <strong> $msg[resa_dispo_suivant] ";
						$this->message .= $next_empr->empr_nom." ".$next_empr->empr_prenom."</strong>. (".$next_empr->empr_cb.")";
					}
					return TRUE;
				} else {
					$this->message=$msg["resa_supprimee"];
					return TRUE;
				}
			}
		}

		// <----------------- check_quota() : vérification du dépassement de quota -------------------------->
		public function check_quota() {
			global $msg;
			global $pmb_quotas_avances;
			global $pmb_resa_quota_pret_depasse;
			
			//Initialisation résultat
			$error=array();
			$error["ERROR"]=false;
			
			//Si les quotas avancés sont autorisés
			if ($pmb_quotas_avances) {
				$struct=array();
				//Quota de notice ou bulletin ?
				if ($this->id_notice) {
					$quota_type="BOOK_NMBR_QUOTA";
					$struct["NOTI"]=$this->id_notice;
					$elt_name="NOTICETYPE";
				} else {
					$quota_type="BOOK_NMBR_SERIAL_QUOTA";
					$struct["NOTI"] = bulletinage::get_notice_id_from_id($this->id_bulletin);
					$struct["BULL"]=$this->id_bulletin;
					$elt_name="BULLETINTYPE";
				}
				//Initialisation du quota
				$qt=new quota($quota_type);
				$struct["READER"]=$this->id_empr;
			
				//Si résa bloquée en cas de dépassement de quota de prêt
				if (!$pmb_resa_quota_pret_depasse) {
					//Le quota de prêt est-il atteint pour cette notice ou bulletin
					//Récupération de l'élément indirect à tester
					$elt=$qt->get_element_by_name($elt_name);
					//Récupération de l'exemplaire le plus défavorable associé à la réservation
					$object_id=$qt->get_object_for_indirect_element(quota::$_quotas_[$qt->descriptor]['_elements_'][$elt],$struct);
					//Initialisation du quota de prêt
					$qt_pret=new quota("LEND_NMBR_QUOTA");
					$struct_pret=array();
					$struct_pret["READER"]=$this->id_empr;
					$struct_pret["EXPL"]=$object_id;
					$struct_pret["NOTI"] = exemplaire::get_expl_notice_from_id($object_id);
					$struct_pret["BULL"] = exemplaire::get_expl_bulletin_from_id($object_id);
					$r=$qt_pret->check_quota($struct_pret);
				} else $r=false;
			
				//Si quota de prêt non violé alors on regarde les quotas de réservation
				if (!$r) {
					//Vérification
					$r=$qt->check_quota($struct);
					//Si quota violé
					if ($r) {
						$error["ERROR"]=true;
						//Erreur
						$error["MESSAGE"]=$qt->error_message;
						//Peut-on forcer ou pas la résa
						$error["FORCE"] = $qt->force;
					}
				} else {
					$error["ERROR"]=true;
					//Erreur
					$error["MESSAGE"]=$qt_pret->error_message."<br />".$msg["resa_quota_pret_error"];
					//Peut-on forcer ou pas la résa
					$error["FORCE"] = 0;
				}
			}	
			if ($error["ERROR"]) {
				$this->message=$error["MESSAGE"];
				$this->force=$error["FORCE"];
			}
			return $error;
		}

		// <----------------- empr_exists() : vérification de l'existence de l'utilisateur ------------------>
		public function empr_exists() {
			global $msg;
			$query="select count(1) from empr where id_empr=".$this->id_empr;
			$result=@pmb_mysql_query($query);
			if (!@pmb_mysql_result($result, 0, 0)) {
				$this->message="<strong>$msg[resa_no_empr]</strong>";
				return FALSE;
			}
			return TRUE;
		}

		// <----------------- notice_exists() : vérification de l'existence de la notice  ou du bulletinage ------------------>
		public function notice_exists() {
			global $msg;
			if($this->id_notice) $query="select count(1) from notices where notice_id=".$this->id_notice;
			elseif ($this->id_bulletin)
				$query="select count(1) from bulletins where bulletin_id=".$this->id_bulletin;
			$result=@pmb_mysql_query($query);
			if (!@pmb_mysql_result($result, 0, 0)) {
				$this->message="<strong>$msg[resa_no_doc]</strong>";
				return FALSE;
			}
			return TRUE;
		}

		// <----------------- resa_exists() : vérification de l'existence de la réservation ------------------>
		public function resa_exists() {
			global $msg;
			$query="select count(1) from resa where resa_idempr=".$this->id_empr;
			if ($this->id_notice) $query .= " and resa_idnotice=".$this->id_notice;
			elseif ($this->id_bulletin)
				$query .= " and resa_idbulletin=" . $this->id_bulletin;
			$result=@pmb_mysql_query($query);
			if (@pmb_mysql_result($result, 0, 0)) {
				$this->message="<strong>$msg[resa_deja_resa]</strong>";
				return TRUE;
			}
			return FALSE;
		}

		// <----------------- allready_loaned() : on regarde si l'emprunteur n'a pas déjà ce document ------------------>
		public function allready_loaned() {
			global $msg;
			$query="select count(1) from pret p, exemplaires e";
			$query .= " where p.pret_idempr=" . $this->id_empr;
			$query .= " and p.pret_idexpl=e.expl_id";
			if ($this->id_notice)
				$query .= " and e.expl_notice=" . $this->id_notice;
			elseif ($this->id_bulletin)
				$query .= " and e.expl_bulletin=" . $this->id_bulletin;
			$result=@pmb_mysql_query($query);
			if (@pmb_mysql_result($result, 0, 0)) {
				$this->message="<strong>$msg[resa_deja_doc]</strong>";
				return TRUE;
			}
			return FALSE;
		}

		// <----------------- waiting_already_loaned() : on regarde si l'emprunteur a récemment emprunté ce document ------------------>
		public function waiting_already_loaned() {
			global $msg;
			global $pmb_resa_waiting_already_loaned;
			
			$pmb_resa_waiting_already_loaned = intval($pmb_resa_waiting_already_loaned);
			if(!$pmb_resa_waiting_already_loaned) {
				return FALSE;
			}
			$dateTime = new DateTime(date('Y-m-d'));
			$dateTime->modify('-'.$pmb_resa_waiting_already_loaned.' days');
			
			$query = "SELECT arc_id FROM pret_archive WHERE arc_id_empr = '".$this->id_empr."'";
			if($this->id_notice) {
				$query .= " AND arc_expl_notice = ".$this->id_notice;
			} elseif ($this->id_bulletin) {
				$query .= " AND arc_expl_bulletin = ".$this->id_bulletin;
			}
			$query .= " AND arc_fin > '".$dateTime->format('Y-m-d')."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$this->message = "<strong>".str_replace('!!nb_days!!', $pmb_resa_waiting_already_loaned, $msg['resa_waiting_already_loaned'])."</strong>";
				return TRUE;
			}
			return FALSE;
		}
		
		// <----------------- check_statut() : le genre de choses qu'on peut attendre en retour ------------------>
		/* fonction complexe à rediscuter : cas possibles :
		- doc en consultation sur place uniquement
		- doc mixed : exemplaire(s) en consultation sur place et exemplaire(s) en circulation
		- doc en circulation ET disponible
		La solution retenue : fetcher tous les exemplaires attachés à la notice et définir des flags de situation
		 */
		public function check_statut() {
			global $opac_resa_dispo; // les résa de disponibles sont-elles autorisées ?
			global $msg;

			// on checke s'il y a des exemplaires prêtables
			$query = $this->get_query_expl_lendable();
			$result=pmb_mysql_query($query);
			if (!pmb_mysql_num_rows($result)) {
				// aucun exemplaire n'est disponible pour le prêt
				$this->message .= "$msg[resa]&nbsp;:&nbsp;" . $this->notice."<br /><strong>$msg[resa_no_expl]</strong>";
				$this->service->error="check_statut";
				return 1;
			}

			// Réservations possibles de documents sortis ?
			if($opac_resa_dispo=='2') {
				$nb_expl_available = count(expl_dispo($this->id_notice, $this->id_bulletin));
				if(!$nb_expl_available) return 1;
			}
			
			// on regarde si les résa de disponibles sont autorisées
			if ($opac_resa_dispo) return 0;

			// on checke si un exemplaire est disponible
			// aka. si un des exemplaires en circulation n'est pas mentionné dans la table des prêts,
			// c'est qu'il est disponible à la bibliothèque
			$list_dispo='';

			while($reservable = pmb_mysql_fetch_object($result)) {
				$req2 = "select count(1) from pret where pret_idexpl=".$reservable->expl_id;
				$req2_result=pmb_mysql_query($req2);
				if (!pmb_mysql_result($req2_result, 0, 0)) {
					// l'exemplaire ne figure pas dans la table pret -> dispo
					// on récupère les données exemplaires pour constituer le message
					$req3="select p.expl_cote, s.section_libelle, s.idsection, l.location_libelle, l.idlocation";
					$req3 .= " from exemplaires p, docs_section s, docs_location l";
					$req3 .= " where p.expl_id=".$reservable->expl_id;
					$req3 .= " and s.idsection=p.expl_section";
					$req3 .= " and l.idlocation=p.expl_location limit 1";

					$req3_result=pmb_mysql_query($req3);
					$req3_obj=pmb_mysql_fetch_object($req3_result);
					if ($req3_obj->expl_cote) {
						// Si résa validé il n'est pas disponible en prêt
						$req4 = "select count(1) from resa where resa_cb='".$reservable->expl_cb."' and resa_confirmee='1'";
						$req4_result = pmb_mysql_query($req4);
						if (!pmb_mysql_result($req4_result, 0, 0)) {
						    $list_dispo .= '<br />'.translation::get_translated_text($req3_obj->idlocation, "docs_location", "location_libelle",$req3_obj->location_libelle) . '.';
						    $list_dispo .= translation::get_translated_text($req3_obj->idsection, "docs_section", "section_libelle",$req3_obj->section_libelle).' cote&nbsp;: ' . $req3_obj->expl_cote;
						}
					}
				}
			}

			if ($list_dispo) {

				$this->message = $msg['resa_doc_dispo']."<br />";
				$this->message .= $this->notice . $list_dispo;
				$this->service->error="check_doc_dispo";
				return 2;
			}

			// rien de spécial
			return 0;
		}

		public function get_resa_cb() {
			global $msg, $deflt_docs_location, $pmb_transferts_actif, $transferts_choix_lieu_opac;

			$this->expl_affectable=array();
			$this->expl_reservable=array();
			$this->expl_transferable=array();

			// on check s'il y a des exemplaires prêtables
			$query = $this->get_query_expl_lendable();
			$result=pmb_mysql_query($query);
			if (!@pmb_mysql_num_rows($result)) {
				// aucun exemplaire n'est disponible pour le prêt
				$this->message .= "$msg[resa]&nbsp;:&nbsp;" . $this->notice."<br /><strong>$msg[resa_no_expl]</strong>";
				return 1;
			}

			while (($pretable=pmb_mysql_fetch_object($result))) {
				$req2="select count(1) from pret where pret_idexpl=".$pretable->expl_id;
				$req2_result=pmb_mysql_query($req2);
				if (!pmb_mysql_result($req2_result, 0, 0)) {
					// l'exemplaire ne figure pas dans la table pret -> dispo
					// on récupère les données exemplaires pour constituer le message
					$req3="select p.expl_cote, s.section_libelle, l.location_libelle, expl_location";
					$req3 .= " from exemplaires p, docs_section s, docs_location l";
					$req3 .= " where p.expl_id=" . $pretable->expl_id;
					$req3 .= " and s.idsection=p.expl_section";
					$req3 .= " and l.idlocation=p.expl_location limit 1";
					$req3_result=pmb_mysql_query($req3);
					$req3_obj=pmb_mysql_fetch_object($req3_result);
					if ($req3_obj->expl_cote) {
						// Si résa validé il n'est pas disponible en prêt
						$req4="select count(1) from resa where resa_cb='".addslashes($pretable->expl_cb)."' and resa_confirmee='1'";
						$req4_result=pmb_mysql_query($req4);
						if (!pmb_mysql_result($req4_result, 0, 0)) {
							$this->expl_affectable[]=$pretable->expl_cb;
						}
						if ($req3_obj->expl_location != $deflt_docs_location) {
							$this->expl_transferable[]=$pretable->expl_cb;
						}
					}
				}
				$this->expl_reservable[]=$pretable->expl_cb;
			}
			// Calcul du rang de réservation du lecteur
			$rank=1;

			$from= "";
			$where = "";
			if ($pmb_transferts_actif == "1") {
				switch ($transferts_choix_lieu_opac) {
				case "1":
				//retrait de la resa sur lieu choisi par le lecteur
					$where=" AND resa_loc_retrait=" . $deflt_docs_location;
					break;
				case "2":
				//retrait de la resa sur lieu fixé
					$where=" AND resa_loc_retrait=" . $deflt_docs_location;
					break;
				case "3":
				//retrait de la resa sur lieu exemplaire
					$from=" ,exemplaires ";
					$where=" AND expl_cb='" . $pretable->expl_cb."' and expl_location=" . $deflt_docs_location;
					break;
				default:
				//retrait de la resa sur lieu lecteur
					$from=" ,empr ";
					$where=" AND resa_idempr=id_empr and empr_location=".$deflt_docs_location;
					break;
				} //switch $transferts_choix_lieu_opac
			}
			// chercher le premier (par ordre de rang, donc de date de début de résa, non validé
			$rqt="SELECT id_resa, resa_idempr,resa_loc_retrait
					FROM resa $from
					WHERE resa_idnotice='" . $this->id_notice."'
					AND resa_idbulletin='" . $this->id_bulletin. "'
					AND resa_cb=''
					AND resa_date_fin='0000-00-00'
					$where
					ORDER BY resa_date ";

			/*
			if($pmb_location_reservation) {
			$rqt="SELECT resa_idempr FROM resa ,empr WHERE resa_idnotice='".$this->id_notice."' AND resa_idbulletin='".$this->id_bulletin."' and resa_idempr=id_empr
			and empr_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location) ORDER BY resa_date";
			} else {
			$rqt="SELECT resa_idempr FROM resa WHERE resa_idnotice='".$this->id_notice."' AND resa_idbulletin='".$this->id_bulletin."' ORDER BY resa_date";
			}
			 */
			$result=pmb_mysql_query($rqt);
			while (($resa=pmb_mysql_fetch_object($result))) {
				if($resa->resa_idempr == $this->id_empr) break;
				$rank++;
			}
			$this->resa_rank=$rank;
			//print $query ."<br />".$rank."<br />";

			// rien de spécial
			return 0;
		}

		public function set_on_empr_fiche($on_empr_fiche=false) {
			$this->on_empr_fiche = $on_empr_fiche;
		}
		
		public function get_restrict_expl_location_query() {
			global $pmb_lecteurs_localises;
			global $deflt_resas_location;
			global $pmb_transferts_actif, $transferts_choix_lieu_opac;
			global $f_loc, $f_dispo_loc;
				
			$sql_expl_loc = '';
			if ($pmb_lecteurs_localises && !$this->on_empr_fiche){
				if ($f_loc=="")	$f_loc = $deflt_resas_location;
				if ($f_loc && $f_dispo_loc)	$sql_expl_loc= " and (expl_location='".$f_loc."' or expl_location='".$f_dispo_loc."') ";
				elseif ($f_loc) $sql_expl_loc .= " and expl_location='".$f_loc."' ";
				elseif ($f_dispo_loc) $sql_expl_loc= " and expl_location='".$f_dispo_loc."' ";
			}
			if ($pmb_transferts_actif=="1" && $f_loc && !$this->on_empr_fiche) {
				switch ($transferts_choix_lieu_opac) {
					case "1":
						//retrait de la resa sur lieu choisi par le lecteur
						break;
					case "2":
						//retrait de la resa sur lieu fixé
						break;
					case "3":
						//retrait de la resa sur lieu exemplaire
						if (!$this->on_empr_fiche) {
							if ($f_loc && $f_dispo_loc)	$sql_expl_loc .= " and (expl_location='".$f_loc."' or expl_location='".$f_dispo_loc."') ";
							elseif ($f_loc) $sql_expl_loc .= " and expl_location='".$f_loc."' ";
							elseif ($f_dispo_loc) $sql_expl_loc .= " and expl_location='".$f_dispo_loc."' ";
						}
						break;
					default:
						//retrait de la resa sur lieu lecteur
						if (!$this->on_empr_fiche) {
							if ($f_loc && $f_dispo_loc)	$sql_expl_loc .= " and (expl_location='".$f_loc."' or expl_location='".$f_dispo_loc."') ";
							elseif ($f_loc) $sql_expl_loc .= " and expl_location='".$f_loc."' ";
							elseif ($f_dispo_loc) $sql_expl_loc .= " and expl_location='".$f_dispo_loc."' ";
						}
						break;
				}
			}
			return $sql_expl_loc;
		}
		
		public function get_restrict_expl_notice_query() {
			if ($this->id_notice) {
				return " expl_notice=".$this->id_notice;
			} elseif ($this->id_bulletin) {
				return " expl_bulletin=".$this->id_bulletin;
			}
		}
		
		/**
		 * On retourne la requête concernant les exemplaires prêtables pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return string
		 */
		public function get_expl_lendable_query($location=0, $outside=false, $filter_loc_retrait=false) {
			$query = "SELECT * FROM exemplaires, docs_statut WHERE expl_statut=idstatut AND statut_allow_resa=1 ";
			if($location) {
				if($outside) {
					$query .= " AND expl_location <> ".$location;
				} else {
					$query .= " AND expl_location = ".$location;
				}
			} else {
				$query .= $this->get_restrict_expl_location_query($filter_loc_retrait);
			}
			$query .= " AND ".$this->get_restrict_expl_notice_query();
			return $query;
		}
		
		/**
		 * On retourne les exemplaires prêtables pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return array
		 */
		public function get_expl_lendable($location=0, $outside=false) {
			$expl_lendable = array();
			$query = $this->get_expl_lendable_query($location, $outside);
			$result = pmb_mysql_query($query);
			while($row = pmb_mysql_fetch_object($result)) {
				$expl_lendable[] = $row;
			}
			return $expl_lendable;
		}
		
		/**
		 * On compte le nombre total d'exemplaires prêtables pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return string
		 */
		public function get_number_expl_lendable($location=0, $outside=false) {
			$query = $this->get_expl_lendable_query($location, $outside);
			$tresult = pmb_mysql_query($query);
			return pmb_mysql_num_rows($tresult);
		}
		
		public function get_number_expl_transferts_lendable($location=0, $outside=false) {
		    $query = $this->get_expl_lendable_query($location, $outside, true);
		    $tresult = pmb_mysql_query($query);
		    return pmb_mysql_num_rows($tresult);
		}
		
		/**
		 * On retourne la requête concernant les exemplaires sortis pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return string
		 */
		public function get_expl_out_query($location=0, $outside=false) {
			$query = "SELECT * FROM exemplaires , pret WHERE pret_idexpl=expl_id ";
			if($location) {
				if($outside) {
					$query .= " AND expl_location <> ".$location;
				} else {
					$query .= " AND expl_location = ".$location;
				}
			} else {
				$query .= $this->get_restrict_expl_location_query();
			}
			$query .= " AND ".$this->get_restrict_expl_notice_query();
			return $query;
		}
		
		/**
		 * On retourne les exemplaires sortis pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return array
		 */
		public function get_expl_out($location=0, $outside=false) {
			$expl_out = array();
			$query = $this->get_expl_out_query($location, $outside);
			$result = pmb_mysql_query($query);
			while($row = pmb_mysql_fetch_object($result)) {
				$expl_out[] = $row;
			}
			return $expl_out;
		}
		
		/**
		 * On compte le nombre d'exemplaires sortis
		 */
		public function get_number_expl_out() {
			$query = $this->get_expl_out_query();
			$tresult = pmb_mysql_query($query);
			return pmb_mysql_num_rows($tresult);
		}
		
		/**
		 * On compte le nombre d'exemplaires en circulation
		 */
		public function get_number_expl_in_circ() {
			$query = "SELECT count(1) FROM exemplaires, serialcirc_expl WHERE num_serialcirc_expl_id=expl_id ".$this->get_restrict_expl_location_query();
			$query .= " AND ".$this->get_restrict_expl_notice_query();
			$tresult = pmb_mysql_query($query);
			return pmb_mysql_result($tresult, 0, 0);
		}
		
		/**
		 * On retourne la requête concernant les exemplaires en transfert pour la notice
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return string
		 */
		public function get_expl_in_transfert_query($location=0, $outside=false) {
			$query = "SELECT * FROM exemplaires
				JOIN transferts_demande ON transferts_demande.num_expl=exemplaires.expl_id
				JOIN transferts ON num_transfert=id_transfert
				WHERE etat_transfert=0 AND origine=4 ";
			if($location) {
				if($outside) {
					$query .= " AND expl_location <> ".$location;
				} else {
					$query .= " AND expl_location = ".$location;
				}
			} else {
				$query .= $this->get_restrict_expl_location_query();
			}
			$query .= " AND ".$this->get_restrict_expl_notice_query();
			return $query;
		}
		
		/**
		 * On compte le nombre d'exemplaires en transfert non sortis
		 */
		public function get_number_expl_in_transfert($location=0, $outside=false) {
			$expl_out_ids = array();
			$expl_out = $this->get_expl_out($location, $outside);
			foreach ($expl_out as $expl) {
				$expl_out_ids[] = $expl->expl_id;
			}
			$query = $this->get_expl_in_transfert_query($location, $outside);
			if(!empty($expl_out_ids)) {
				$query .= " AND expl_id NOT IN (".implode(',', $expl_out_ids).")";
			}
			$tresult = pmb_mysql_query($query);
			return pmb_mysql_num_rows($tresult);
		}
		
		/**
		 * On compte le nombre d'exemplaires disponibles
		 */
		public function get_number_expl_available() {
			// on compte le nombre total d'exemplaires prêtables pour la notice
			// on compte le nombre d'exemplaires sortis
			// on compte le nombre d'exemplaires en circulation
			// on en déduit le nombre d'exemplaires disponibles
			$number = $this->get_number_expl_lendable() - $this->get_number_expl_out() - $this->get_number_expl_in_circ();
			return $number;
		}
		
		/**
		 * On retourne la requête concernant les exemplaires réservés
		 * @param number $location Localisation
		 * @param string $outside Pour la localisation ou en dehors
		 * @return string
		 */
		public function get_expl_reserved_query($location=0, $outside=false) {
			$query = "SELECT count(1) as qte FROM exemplaires JOIN resa ON resa_cb=expl_cb WHERE resa_cb <> '' ";
			if($location) {
				if($outside) {
					$query .= " AND expl_location <> ".$location;
				} else {
					$query .= " AND expl_location = ".$location;
				}
			} else {
				$query .= $this->get_restrict_expl_location_query();
			}
			$query .= " AND ".$this->get_restrict_expl_notice_query();
			return $query;
		}
		
		public function get_number_expl_reserved($location=0, $outside=false) {
			$query = $this->get_expl_reserved_query($location, $outside);
			$tresult = pmb_mysql_query($query);
			return pmb_mysql_result($tresult, 0, 0);
		}
		
		public function get_expl_lendable_in_loan_query($location=0, $outside=false) {
			global $msg;
			
			$query = "SELECT e.*, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour from pret p, exemplaires e ";
			if ($this->id_notice) $query .= " WHERE e.expl_notice=".$this->id_notice;
			elseif ($this->id_bulletin) $query .= " WHERE e.expl_bulletin=".$this->id_bulletin;
			else $query .= " WHERE 0"; // ni bulletin ni notice
			$query .= " AND e.expl_id=p.pret_idexpl";
			if($location) {
				if($outside) {
					$query .= " AND expl_location <> ".$location;
				} else {
					$query .= " AND expl_location = ".$location;
				}
			} else {
				$query .= $this->get_restrict_expl_location_query();
			}
			$query .= " ORDER BY p.pret_retour";
			return $query;
		}
		
		public function get_expl_lendable_in_loan($location=0, $outside=false) {
			$expl_in_loan = array();
			$query = $this->get_expl_lendable_in_loan_query($location, $outside);
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					$expl_in_loan[] = $row;
				}
			}
			return $expl_in_loan;
		}
		
		public function get_exemplaire() {
			if(!isset($this->exemplaire)) {
				$this->exemplaire = new exemplaire($this->expl_cb, $this->expl_id);
			}
			return $this->exemplaire;
		}
		
		public function set_exemplaire($exemplaire) {
			$this->exemplaire = $exemplaire;
		}
		
		public function get_id() {
			return $this->id;	
		}
		
		public static function alert_mail_users_pmb($id_notice=0, $id_bulletin=0, $id_empr=0, $annul=0, $resa_planning=0, $id_resa=0) {
			global $msg;
			global $pmb_location_reservation,$pmb_resa_alert_localized;
			global $use_opac_url_base;
			
			$id_notice = intval($id_notice);
			$id_bulletin = intval($id_bulletin);
			$id_empr = intval($id_empr);
			
			//Pas très propre mais pas mieux pour le moment / Réaffectation à la fin de la méthode
			$temp_use_opac_url_base = $use_opac_url_base;
			$use_opac_url_base=1;
			
			// paramétrage OPAC: choix du nom de la bibliothèque comme expéditeur
			$requete = "select location_libelle, email, empr_location from empr, docs_location where empr_location=idlocation and id_empr='$id_empr' ";
			$res = pmb_mysql_query($requete);
			$loc=pmb_mysql_fetch_object($res) ;
			if ($loc->email) {
				$query = "select distinct empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_tel2, empr_cp, empr_ville, location_libelle, nom, prenom, userid, user_email, date_format(sysdate(), '".$msg["format_date_heure"]."') as aff_quand, deflt2docs_location, deflt_docs_location  from empr, docs_location, users where id_empr='$id_empr' and empr_location=idlocation and user_email like('%@%') and user_alert_resamail=1";
				$result = pmb_mysql_query($query);
				if ($resa_planning) {
					$mail_opac_user_resa = new mail_opac_user_resa_planning();
				} else {
					$mail_opac_user_resa = new mail_opac_user_resa();
				}
				$mail_opac_user_resa->set_id_notice($id_notice)
				->set_id_bulletin($id_bulletin)
				->set_annul($annul);
				
				while ($empr=pmb_mysql_fetch_object($result)) {
					if ($pmb_location_reservation && $pmb_resa_alert_localized) {
						if ($pmb_resa_alert_localized==1 && $loc->empr_location!=$empr->deflt2docs_location) {
							continue;
						}
						if ($pmb_resa_alert_localized==2 && $loc->empr_location!=$empr->deflt_docs_location) {
							continue;
						}
						if ($pmb_resa_alert_localized==3 && $loc->empr_location!=$empr->deflt2docs_location && $loc->empr_location!=$empr->deflt_docs_location) {
							continue;
						}
						if ($pmb_resa_alert_localized==4) {
						    $id_resa = intval($id_resa);
						    if (empty($id_resa)) {
						        continue;
						    }
						    $loc_retrait = pmb_mysql_fetch_assoc(pmb_mysql_query("SELECT resa_loc_retrait FROM resa WHERE id_resa = $id_resa"));
						    if ($loc_retrait["resa_loc_retrait"] != $empr->deflt2docs_location && $loc_retrait["resa_loc_retrait"] != $empr->deflt_docs_location) {
						        continue;
						    }
						}
					}
					$mail_opac_user_resa->set_mail_to_id($empr->userid)
					->set_mail_from_id($loc->empr_location)
					->set_empr($empr);
					$mail_opac_user_resa->send_mail();
				}
			}
			$use_opac_url_base = $temp_use_opac_url_base;
		}
		
		//Récupération de la durée de réservation pour une notice ou un bulletin et un emprunteur
		public static function get_time($id_empr,$id_notice,$id_bulletin) {
			global $pmb_quotas_avances;
			
			//Si les quotas avancés sont actifs
			if ($pmb_quotas_avances) {
				$struct=array();
				if ($id_notice) {
					$struct["NOTI"]=$id_notice;
					$quota_type="BOOK_TIME_QUOTA";
				} else {
				    $struct["NOTI"] = bulletinage::get_notice_id_from_id($id_bulletin);
					$struct["BULL"]=$id_bulletin;
					$quota_type="BOOK_TIME_SERIAL_QUOTA";
				}
				$struct["READER"]=$id_empr;
				$qt=new quota($quota_type);
				$t=$qt->get_quota_value($struct);
				if ($t==-1) $t=0;
			} else {
				//Sinon je regarde la durée de réservation la plus défavorable par type de document
				if ($id_notice)
					$requete="select min(duree_resa) from docs_type, exemplaires where expl_notice='$id_notice' and expl_typdoc=idtyp_doc";
					else
						$requete="select min(duree_resa) from docs_type, exemplaires where expl_bulletin='$id_bulletin' and expl_typdoc=idtyp_doc";
						$resultat=pmb_mysql_query($requete);
						if (pmb_mysql_num_rows($resultat)) $t=pmb_mysql_result($resultat,0,0); else $t=0;
			}
			return $t;
		}
		
		public static function get_cb_from_id($id) {
			$rqt = "select resa_cb from resa where id_resa='".$id."' ";
			$res = pmb_mysql_query($rqt) ;
			$nb=pmb_mysql_num_rows($res) ;
			if (!$nb) return "" ;
			$obj=pmb_mysql_fetch_object($res) ;
			return $obj->resa_cb ;
		}
		
		public static function verif_cb_resa_flag($cb){
			$query = " select statut_allow_resa from exemplaires , docs_statut where expl_cb='".addslashes($cb)."' and idstatut=expl_statut";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$expl = pmb_mysql_fetch_object($result);
				return $expl->statut_allow_resa;
			}
		}
		
		public static function get_instance_from_empr_and_notice($id_empr, $notice_id) {
		    $bulletin_id=0;
		    //On vérifie que notre notice n'est pas une notice de bulletin.
		    $query='SELECT bulletin_id FROM bulletins WHERE num_notice='.$notice_id;
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)){
		        while($line=pmb_mysql_fetch_array($result,PMB_MYSQL_ASSOC)){
		            $bulletin_id=$line['bulletin_id'];
		        }
		    }
		    return new reservation($id_empr, $notice_id, $bulletin_id);
		}
	} # fin de déclaration classe reservation

} # fin de définition
