<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.class.php,v 1.13.4.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/resa.class.php');

class resa_planning{
	public $id_resa = 0;							//Identifiant de prévision
	public $resa_idempr = 0;						//Identifiant du lecteur ayant fait la prévision
	public $resa_idnotice = 0;						//Identifiant de la notice sur laquelle est posée la prévision
	public $resa_idbulletin	= 0	;					//Identifiant de bulletin si applicable
	public $resa_date = NULL;						//Date et heure de la demande
	public $aff_resa_date = '';
	public $resa_date_debut = '0000-00-00';			//Date de début de la prévision
	public $aff_resa_date_debut = '';
	public $resa_date_fin = '0000-00-00';			//Date de fin de la prévision
	public $aff_resa_date_fin = '';
	public $resa_validee = 0;						//Prévision validée si 1
	public $resa_confirmee = 0;						//Prévision confirmée si 1
	public $resa_loc_retrait = 0;					//Lieu de retrait de la prévision
	public $resa_qty = 1;							//Quantité à réserver
	public $resa_remaining_qty = 1;					//Quantité restante

	public function __construct($id_resa= 0) {
		$this->id_resa = intval($id_resa);
		if ($this->id_resa) {
			$this->load();
		}
	}

	// charge une prévision à partir de la base.
	public function load(){
		global $msg;

		$q = "select resa_idempr, resa_idnotice, resa_idbulletin, resa_date, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date, resa_date_debut, date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, resa_date_fin, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, resa_validee, resa_confirmee, resa_loc_retrait, resa_qty, resa_remaining_qty from resa_planning where id_resa = '".$this->id_resa."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->resa_idempr = $obj->resa_idempr;
		$this->resa_idnotice = $obj->resa_idnotice;
		$this->resa_idbulletin = $obj->resa_idbulletin;
		$this->resa_date = $obj->resa_date;
		$this->aff_resa_date = $obj->aff_resa_date;
		$this->resa_date_debut = $obj->resa_date_debut;
		$this->aff_resa_date_debut = $obj->aff_resa_date_debut;
		$this->resa_date_fin = $obj->resa_date_fin;
		$this->aff_resa_date_fin = $obj->aff_resa_date_fin;
		$this->resa_validee = $obj->resa_validee;
		$this->resa_confirmee = $obj->resa_confirmee;
		$this->resa_loc_retrait = $obj->resa_loc_retrait;
		$this->resa_qty = $obj->resa_qty;
		$this->resa_remaining_qty  = $obj->resa_remaining_qty ;
	}

	// enregistre une prévision en base.
	public function save(){
		if ($this->id_resa) {
			if ($this->resa_date_debut && $this->resa_date_fin) {
				$this->resa_validee = ($this->resa_validee==1)?1:0;
				$this->resa_confirmee = ($this->resa_confirmee==1)?1:0;
				$q = "update resa_planning set resa_date_debut = '".$this->resa_date_debut."', resa_date_fin = '".$this->resa_date_fin."', ";
				$q.= "resa_validee = '".$this->resa_validee."', resa_confirmee = '".$this->resa_confirmee."', ";
				$q.= "resa_loc_retrait = ".$this->resa_loc_retrait.", resa_qty=".$this->resa_qty.", resa_remaining_qty=".$this->resa_remaining_qty;
				$q.= " where id_resa = '".$this->id_resa."' ";
				pmb_mysql_query($q);
			}
		} else {
			if ($this->resa_idempr && ((!$this->resa_idnotice && $this->resa_idbulletin) || ($this->resa_idnotice && !$this->resa_idbulletin)) && $this->resa_date_debut && $this->resa_date_fin) {
				$q = "insert into resa_planning set resa_idempr = '".$this->resa_idempr."', resa_idnotice = '".$this->resa_idnotice."', resa_idbulletin = '".$this->resa_idbulletin."', resa_date = SYSDATE(), ";
				$q.= "resa_date_debut = '".$this->resa_date_debut."', resa_date_fin = '".$this->resa_date_fin."', resa_validee = '0', resa_confirmee = '0', ";
				$q.= "resa_loc_retrait = ".$this->resa_loc_retrait.", resa_qty=".$this->resa_qty.", resa_remaining_qty=".$this->resa_remaining_qty;
				pmb_mysql_query($q);
				$this->id_resa = pmb_mysql_insert_id();
			}
		}
	}

	//supprime une prévision de la base
	public static function delete($id_resa=0) {
		$id_resa = intval($id_resa);
		if($id_resa) {
			$q = "delete from resa_planning where id_resa=$id_resa ";
			pmb_mysql_query($q);
		}
	}

	//Compte le nb de prévisions sur une notice
	public static function count_resa($id_notice=0,$id_bulletin=0) {
		$id_notice = intval($id_notice);
		$id_bulletin = intval($id_bulletin);
		if (!$id_notice && !$id_bulletin) {
			return 0;
		}
		$q = "SELECT count(1) FROM resa_planning WHERE resa_idnotice=$id_notice and resa_idbulletin=$id_bulletin ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//optimisation de la table resa_planning
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE resa_planning');
		return $opt;
	}

	//retourne la liste des localisations de retrait possibles pour un emprunteur selon le paramétrage ainsi que la qté d'exemplaires disponibles
	public static function get_available_locations($id_empr=0,$id_notice=0,$id_bulletin=0) {
		global $pmb_location_reservation,$pmb_location_resa_planning;

		$id_empr = intval($id_empr);
		$id_notice = intval($id_notice);
		$id_bulletin = intval($id_bulletin);

		$loc = array();
		if($id_empr && ($id_notice || $id_bulletin)) {
			$q = "select expl_location, location_libelle, count(expl_id) as nb from exemplaires join docs_location on expl_location=idlocation join docs_statut on expl_statut=idstatut ";
			$q.= "where expl_notice=$id_notice and expl_bulletin=$id_bulletin ";
			$q.= "and statut_allow_resa=1";
			if($pmb_location_resa_planning) {
				if ($pmb_location_reservation) {
					$q.=" and expl_location in (select resa_loc from empr,resa_loc where id_empr=$id_empr and empr_location=resa_emprloc)";
				} else {
					$q.=" and expl_location = (select empr_location from empr where id_empr=$id_empr)";
				}
			}
			$q.= ' group by expl_location order by location_libelle';
			$r = pmb_mysql_query($q);
			if(pmb_mysql_num_rows($r)) {
				$i=0;
				while ($o=pmb_mysql_fetch_object($r)) {
					$loc[$i]['location_id']=$o->expl_location;
					$loc[$i]['location_libelle']=$o->location_libelle;
					$loc[$i]['location_nb']=$o->nb;
					$i++;
				}
			}
		}
		return $loc;
	}


	//Transformation prevision en reservation(s)
	public function to_resa() {
		//Il faut insérer la prévision avant les résas déjà créées
		$q = 'select date_sub(resa_date,INTERVAL 1 DAY) from resa where resa_idnotice='.$this->resa_idnotice.
				' and resa_idbulletin='.$this->resa_idbulletin.
				' and resa_cb="" order by resa_date limit 1';
		$r = pmb_mysql_query($q);

		if(pmb_mysql_num_rows($r)) {
			//Au moins une resa
			$d=pmb_mysql_result($r,0,0);
		} else {
			$d=date('Y-m-d H:i:s');
		}

		while($this->resa_remaining_qty) {
			$q = 'insert into resa (resa_idempr,resa_idnotice,resa_idbulletin,resa_date,resa_date_debut,resa_date_fin,resa_loc_retrait,resa_planning_id_resa) '.
					'values ('.$this->resa_idempr.','.$this->resa_idnotice.','.$this->resa_idbulletin.',"'.$d.'","'.$this->resa_date_debut.'","'.$this->resa_date_fin.'",'.$this->resa_loc_retrait.','.$this->id_resa.')';
			$r = pmb_mysql_query($q);
			$id_resa = pmb_mysql_insert_id();

			// Archivage de la résa: info lecteur et notice et nombre d'exemplaire
			$q = "SELECT * FROM empr WHERE id_empr=".$this->resa_idempr;
			$r = pmb_mysql_query($q);
			$empr = pmb_mysql_fetch_object($r);

			$q = "SELECT count(*) FROM exemplaires where expl_notice=".$this->resa_idnotice." and expl_bulletin=".$this->resa_idbulletin;
			$r = pmb_mysql_query($q);
			$nb_expl = pmb_mysql_result($r,0,0);

			$q = "INSERT INTO resa_archive SET
					resarc_id_empr = ".$this->resa_idempr.",
					resarc_idnotice = ".$this->resa_idnotice.",
					resarc_idbulletin = ".$this->resa_idbulletin.",
					resarc_date = '".$d."',
					resarc_debut = '".$this->resa_date_debut."',
					resarc_fin = '".$this->resa_date_fin."',
					resarc_loc_retrait = ".$this->resa_loc_retrait.",
					resarc_from_opac= 0,
					resarc_empr_cp ='".addslashes($empr->empr_cp)."',
					resarc_empr_ville = '".addslashes($empr->empr_ville)."',
					resarc_empr_prof = '".addslashes($empr->empr_prof)."',
					resarc_empr_year = '".$empr->empr_year."',
					resarc_empr_categ = ".$empr->empr_categ.",
					resarc_empr_codestat = ".$empr->empr_codestat.",
					resarc_empr_sexe = '".$empr->empr_sexe."',
					resarc_empr_location = ".$empr->empr_location.",
					resarc_expl_nb = $nb_expl,
					resarc_resa_planning_id_resa = ".$this->id_resa;
			pmb_mysql_query($q);
			$id_resarc = pmb_mysql_insert_id();
			// Lier archive et résa pour suivre l'évolution de la résa
			$query = "update resa SET resa_arc=$id_resarc where id_resa=".$id_resa;
			pmb_mysql_query($query);

			$this->resa_remaining_qty--;
		}
		$this->save();
	}

	public function get_id() {
	    return $this->id_resa;
	}
}