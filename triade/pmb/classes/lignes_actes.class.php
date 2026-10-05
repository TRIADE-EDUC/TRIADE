<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lignes_actes.class.php,v 1.37 2021/12/08 13:54:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande
if(!defined('TYP_ACT_DEV')) define('TYP_ACT_DEV', 1);	//				1 = Demande de devis
if(!defined('TYP_ACT_LIV')) define('TYP_ACT_LIV', 2);	//				2 = Bon de Livraison
if(!defined('TYP_ACT_FAC')) define('TYP_ACT_FAC', 3);	//				3 = Facture
if(!defined('TYP_ACT_RENT_ACC')) define('TYP_ACT_RENT_ACC', 4);	//		4 = Demande/Décompte de location
if(!defined('TYP_ACT_RENT_INV')) define('TYP_ACT_RENT_INV', 5);	//		5 = Facture de Location

if(!defined('STA_ACT_ALL')) define('STA_ACT_ALL', -1);	//Statut acte	-1 = Tous
if(!defined('STA_ACT_AVA')) define('STA_ACT_AVA', 1);	//				1 = A valider
if(!defined('STA_ACT_ENC')) define('STA_ACT_ENC', 2);	//				2 = En cours
if(!defined('STA_ACT_REC')) define('STA_ACT_REC', 4);	//				4 = Reçu/Livré
if(!defined('STA_ACT_FAC')) define('STA_ACT_FAC', 8);	//				8 = Facturé
if(!defined('STA_ACT_PAY')) define('STA_ACT_PAY', 16);	//				16 = Payé
if(!defined('STA_ACT_ARC')) define('STA_ACT_ARC', 32);	//				32 = Archivé

class lignes_actes{


	public $id_ligne = 0;					//Identifiant de la ligne d'acte
	public $type_ligne = 0;				//type de ligne de commande (0=texte, 1=notice, 2=bulletin, 3=frais, 4=abt, 5=article)
	public $num_acte = 0;					//Identifiant de l'acte auquel est rattachée la ligne
	public $lig_ref = 0;					//Identifiant de la ligne de l'acte à laquelle est liée cette ligne (pour commande ->livraison)
	public $num_acquisition = 0;			//Identifiant de la suggestion ayant déclenché la commande (optionnel)
	public $num_rubrique = 0;				//Identifiant du numéro de rubrique budgétaire à laquelle est affectée la ligne d'acte
	public $num_produit = '';				//Identifiant de notice ou 0 si produit non géré
	public $num_type = '0';				//Identifiant du type de produit
	public $libelle = '';					//Libelle de la ligne de commande, reprend titre, editeur, auteur, collection, ...
	public $code = '';						//ISBN, ISSN, ...
	public $prix = '0.00';					//Prix de l'ouvrage
	public $tva = '0.00';					//Tva applicable sur l'ouvrage
	public $remise = '0.00';				//Remise sur ligne
	public $nb = 0;						//nb d'articles
	public $date_ech = '0000-00-00';		//Date d'échéance
	public $date_cre = '0000-00-00';		//Date de création de ligne
	public $statut = 1;					//Statut de reception
	public $index_ligne = '';				//Index de recherche
	public $debit_tva = 0;
	public $commentaires_gestion = '';
	public $commentaires_opac = '';
	public $applicants = array(); 			//Demandeurs (utilisé seulement pour les commandes) préchargement des demendeurs issus des suggestions)

	//Constructeur.
	public function __construct($id_ligne= 0) {
		$this->id_ligne = intval($id_ligne);
		if ($this->id_ligne) {
			$this->load();
		}
	}

	// charge une ligne d'acte à partir de la base.
	public function load(){
		global $acquisition_gestion_tva;

		$q = "select * from lignes_actes where id_ligne = '".$this->id_ligne."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->type_ligne = $obj->type_ligne;
		$this->num_acte = $obj->num_acte;
		$this->lig_ref = $obj->lig_ref;
		$this->num_acquisition = $obj->num_acquisition;
		$this->num_rubrique = $obj->num_rubrique;
		$this->num_produit = $obj->num_produit;
		$this->num_type = $obj->num_type;
		$this->libelle = $obj->libelle;
		$this->code = $obj->code;
		$this->prix = $obj->prix;
		$this->tva = $obj->tva;
		$this->remise = $obj->remise;
		$this->nb = $obj->nb;
		$this->date_ech = $obj->date_ech;
		$this->date_cre = $obj->date_cre;
		$this->statut = $obj->statut;
		$this->debit_tva = $obj->debit_tva;
		// Pour les anciennes commandes
		if(!$this->debit_tva)$this->debit_tva=$acquisition_gestion_tva;
		$this->commentaires_gestion = $obj->commentaires_gestion;
		$this->commentaires_opac = $obj->commentaires_opac;
	}

	// enregistre une ligne d'acte en base
	public function save(){
		global $acquisition_gestion_tva;

		if(!$this->debit_tva)$this->debit_tva=$acquisition_gestion_tva;

		if (!$this->num_acte) die("Erreur de création Lignes_Actes");

		if ($this->id_ligne) {

			$q = "update lignes_actes set type_ligne = '".$this->type_ligne."', num_acte = '".$this->num_acte."', lig_ref = '".$this->lig_ref."', num_acquisition = '".$this->num_acquisition."', ";
			$q.= "num_rubrique = '".$this->num_rubrique."', num_produit = '".$this->num_produit."', num_type = '".$this->num_type."', ";
			$q.= "libelle = '".$this->libelle."', code = '".$this->code."', prix = '".$this->prix."', tva = '".$this->tva."', nb = '".$this->nb."', debit_tva = '".$this->debit_tva."', ";
			$q.= "remise = '".$this->remise."', date_ech = '".$this->date_ech."', date_cre = '".$this->date_cre."', statut = '".$this->statut."', ";
			$q.= "commentaires_gestion = '".$this->commentaires_gestion."', commentaires_opac = '".$this->commentaires_opac."', ";
			$q.= "index_ligne = ' ".strip_empty_words($this->libelle)." '";
			$q.= "where id_ligne = '".$this->id_ligne."' ";
			pmb_mysql_query($q);

		} else {

			$q = "insert into lignes_actes set type_ligne = '".$this->type_ligne."', num_acte = '".$this->num_acte."', lig_ref = '".$this->lig_ref."', num_acquisition = '".$this->num_acquisition."', num_rubrique = '".$this->num_rubrique."', ";
			$q.= "num_produit = '".$this->num_produit."', num_type = '".$this->num_type."', libelle = '".$this->libelle."', code = '".$this->code."', prix = '".$this->prix."', tva = '".$this->tva."', nb = '".$this->nb."', debit_tva = '".$this->debit_tva."', ";
			$q.= "remise = '".$this->remise."', date_ech = '".$this->date_ech."', date_cre = '".today()."', statut = '".$this->statut."', ";
			$q.= "commentaires_gestion = '".$this->commentaires_gestion."', commentaires_opac = '".$this->commentaires_opac."', ";
			$q.= "index_ligne = ' ".strip_empty_words($this->libelle)." '";
			pmb_mysql_query($q);
			$this->id_ligne = pmb_mysql_insert_id();

		}
	}


	//supprime une ligne d'acte de la base
	public function delete($id_ligne= 0) {
		if(!$id_ligne) $id_ligne = $this->id_ligne;

		$q = "delete from lignes_actes where id_ligne = '".$id_ligne."' ";
		pmb_mysql_query($q);
	}


	//retourne les lignes de livraison pour une ligne de commande
	//Si num_acte est indiqué, recherche uniquement dans les enregistrements de l'acte correspondant
	public static function getLivraisons($id_lig, $num_acte=0) {
		if ($num_acte) {
			$q = "select * from lignes_actes where lig_ref = '".$id_lig."' and num_acte = '".$num_acte."' order by id_ligne ";
		} else {
			$q = "select lignes_actes.* from actes,lignes_actes where actes.type_acte = '".TYP_ACT_LIV."' and lignes_actes.lig_ref = '".$id_lig."' ";
			$q.= "and lignes_actes.num_acte = actes.id_acte order by id_ligne ";
		}
		$r = pmb_mysql_query($q);
		return $r;
	}


	//retourne les lignes de facture pour une ligne de commande
	//Si num_acte est indiqué, recherche uniquement dans les enregistrements de l'acte correspondant
	public static function getFactures($id_lig, $num_acte=0) {
		if ($num_acte) {
			$q = "select * from lignes_actes where lig_ref = '".$id_lig."' and num_acte = '".$num_acte."' order by id_ligne ";
		} else {
			$q = "select lignes_actes.* from actes,lignes_actes where actes.type_acte = '3' and lignes_actes.lig_ref = '".$id_lig."' ";
			$q.= "and lignes_actes.num_acte = actes.id_acte order by id_ligne ";
		}
		$r = pmb_mysql_query($q);
		return $r;
	}


	//optimization de la table lignes_actes
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE lignes_actes');
		return $opt;
	}

	//modification des lignes par lot
	public static function updateFields($t_id = array(), $t_fields = array()) {
		if (!empty($t_id) && !empty($t_fields)) {
			$t=array();
			foreach($t_fields as $f=>$v) {
				$t[]= $f."='".$v."' ";
			}
			$q="update lignes_actes set ".implode(',',$t)." where id_ligne in ('".implode("','",$t_id)."') ";
			pmb_mysql_query($q);
		}
	}

	//retourne une requete pour recuperation des lignes avec id d'acte et id fournisseur
	public static function getLines($tab_lig=array(), $relances=false) {
		$q='';
		if(count($tab_lig)) {
			$q = "select num_fournisseur, id_acte, id_ligne from lignes_actes join actes on actes.id_acte=lignes_actes.num_acte ";
			if($relances) $q.= "join lignes_actes_statuts on lignes_actes.statut=lignes_actes_statuts.id_statut and lignes_actes_statuts.relance='1' ";
			$q.= "where id_ligne in ('".implode("','",$tab_lig)."') ";
			$q.= "order by num_fournisseur, id_acte, id_ligne ";
		}
		return $q;
	}

	//retourne un tableau des dates de relances sur une ligne
	public static function getRelances ($id_lig=0) {
		global $msg;
		$tab = array();
		if ($id_lig) {
			$q = "select num_ligne, date_format(date_relance, '".$msg["format_date_heure"]."') as date_rel ";
			$q.= "from lignes_actes_relances where num_ligne ='".$id_lig."' ";
			$q.= "order by num_ligne, date_relance desc ";
			$r = pmb_mysql_query($q);

			if (pmb_mysql_num_rows($r)) {
				while ($row=pmb_mysql_fetch_object($r)) {
					$tab[]=$row->date_rel;
				}
			}
		}
		return $tab;
	}

	//retourne la date de la dernière relance sur une ligne
	public static function getLastDayRelance ($id_lig=0) {
	    $lastRelance = '';
	    if ($id_lig) {
	        $q = "select num_ligne, date_relance ";
	        $q.= "from lignes_actes_relances where num_ligne ='".$id_lig."' ";
	        $q.= "order by num_ligne, date_relance desc ";
	        $q.= "limit 1";
	        $r = pmb_mysql_query($q);
	        if (pmb_mysql_num_rows($r)) {
	            $row = pmb_mysql_fetch_object($r);
	            $lastRelance = substr($row->date_relance, 0, 10);
	        }
	    }
	    return $lastRelance;
	}
	
	//retourne un tableau des lignes de relances pour un fournisseur
	public static function getRelancesBySupplier ($id_fou=0) {
		global $msg;
		$tab = array();
		if ($id_fou) {
			$q = "select id_acte, type_acte, date_format(date_acte, '".$msg["format_date"]."') as date_acte, numero as numero, ";
			$q.= "num_ligne, date_format(date_relance, '".$msg["format_date_heure"]."') as date_rel , type_ligne, num_acquisition, num_rubrique, num_produit, num_type, ";
			$q.= "libelle, code, prix, tva, nb, lignes_actes_relances.statut as statut, remise, debit_tva, commentaires_gestion, commentaires_opac ";
			$q.= "from actes join lignes_actes_relances on num_acte=id_acte where num_fournisseur ='".$id_fou."' ";
			$q.= "order by date_relance desc, num_acte ";
			$r = pmb_mysql_query($q);

			if (pmb_mysql_num_rows($r)) {
				while ($row=pmb_mysql_fetch_array($r, PMB_MYSQL_ASSOC)) {
					$tab[]=$row;
				}
			}
		}
		return $tab;
	}

	//enregistre la relance d'un ensemble de lignes
	public static function setRelances ($tab_lig=array()) {
		if (count($tab_lig)) {
			$q1 = "select * from lignes_actes join lignes_actes_statuts on lignes_actes.statut=lignes_actes_statuts.id_statut and lignes_actes_statuts.relance='1' where id_ligne in ('".implode("','",$tab_lig)."') ";
			$r1 = pmb_mysql_query($q1);
			if (pmb_mysql_num_rows($r1)) {
				while ($row=pmb_mysql_fetch_object($r1)) {
					$q2 = "insert ignore into lignes_actes_relances set num_ligne = '".$row->id_ligne."' ,date_relance=now(), type_ligne = '".$row->type_ligne."', num_acte = '".$row->num_acte."', lig_ref = '".$row->lig_ref."', num_acquisition = '".$row->num_acquisition."', num_rubrique = '".$row->num_rubrique."', ";
					$q2.= "num_produit = '".$row->num_produit."', num_type = '".$row->num_type."', libelle = '".addslashes($row->libelle)."', code = '".addslashes($row->code)."', prix = '".$row->prix."', tva = '".$row->tva."', nb = '".$row->nb."', debit_tva = '".$row->debit_tva."', ";
					$q2.= "remise = '".$row->remise."', date_ech = '".$row->date_ech."', date_cre = '".today()."', statut = '".$row->statut."', ";
					$q2.= "commentaires_gestion = '".addslashes($row->commentaires_gestion)."', commentaires_opac = '".addslashes($row->commentaires_opac)."', ";
					$q2.= "index_ligne = ' ".addslashes($row->libelle)." '";
					pmb_mysql_query($q2);
				}
			}
		}
	}

	public static function deleteRelances($id_fou=0, $id_acte=0) {
		$q='';
		if ($id_fou) {
			$q = "delete from lignes_actes_relances where num_acte in (select id_acte from actes where num_fournisseur='".$id_fou."' ) ";
		} elseif($id_acte) {
			$q = "delete from lignes_actes_relances where num_acte='".$id_acte."' ";
		}
		if ($q) {
			pmb_mysql_query($q);
		}
	}

	public function getNbDelivered($id_lig=0) {
		if(!$id_lig) $id_lig=$this->id_ligne;
		$q = "select ifnull(sum(nb),0) from lignes_actes join actes on id_acte=num_acte where actes.type_acte = '".TYP_ACT_LIV."' and lig_ref = '".$id_lig."' ";
		$r = pmb_mysql_result(pmb_mysql_query($q),0,0);
		return $r;
	}

	public function fetchApplicants(){
		if($this->id_ligne && $this->num_acte){
			$acte = new actes($this->num_acte);
			if($acte->type_acte == 0){ //Ligne issue d'un acte de type commande
				$query = "select empr_num from lignes_actes_applicants where ligne_acte_num = ".$this->id_ligne;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->applicants = array();
					while($row = pmb_mysql_fetch_row($result)){
						$this->applicants[] = $row[0];
					}
				}
				return 0;
			}
			return 0;
		}
		return 0;
	}

	public function getApplicants(){
		if(!count($this->applicants)){
			$this->fetchApplicants();
		}
		return $this->applicants;
	}

	public function treatApplicants($applicants){
		$this->applicants = array();
		if(is_array($applicants) && count($applicants)){
			foreach($applicants as $applicant){
				$applicant=intval($applicant);
				if($applicant != 0){
					$this->applicants[] = $applicant;
				}
			}
		}
	}

	public function saveApplicants(){
		$query = "delete from lignes_actes_applicants where ligne_acte_num = ".$this->id_ligne;
		pmb_mysql_query($query);

		if(count($this->applicants)){
			foreach($this->applicants as $applicant){
				$query = "insert into lignes_actes_applicants set ligne_acte_num=".($this->id_ligne*1).", empr_num=".$applicant." ";
				pmb_mysql_query($query);
			}
		}
	}


	//Récupération sans instanciation. respect de ce qui est fait actuellement dans le fichier commmande.inc.php
	public static function getApplicantsFromId($id_ligne=0){
		$applicants = array();
		$query = 'select lignes_actes_applicants.empr_num from lignes_actes_applicants where lignes_actes_applicants.ligne_acte_num = '.$id_ligne;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$applicants[] = $row->empr_num;
			}
		}
		return $applicants;
	}


	protected static function check_before_duplication_or_transfer($id_cde,$ids_line) {

		$ret = array(	'valid'=>false,
								'ids_line'=>[],
								'transfer_budget'=>false);

		$id_cde = intval($id_cde);
		if(!$id_cde) {
			return $ret;
		}
		$tmp_ids_line = explode(',',$ids_line);
		if (!count($tmp_ids_line)) {
			return $ret;
		}
		$tab_ids_line = [];
		foreach($tmp_ids_line as $v) {
			$v1 = intval($v);
			if($v1) {
				$tab_ids_line[] = $v1;
			}
		}

		if(!count($tab_ids_line)) {
			return $ret;
		}
		$ids_line = implode(',', $tab_ids_line);

		$id_entite_org = 0;
		$id_entite_dest = 0;
		$id_exercice_org = 0;
		$id_exercice_dest = 0;

		$q_org = "select actes.num_entite, actes.num_exercice from actes join lignes_actes on lignes_actes.num_acte=actes.id_acte where lignes_actes.id_ligne=".$tab_ids_line[0];
		$r_org = pmb_mysql_query($q_org);
		if($r_org) {
			$id_entite_org = pmb_mysql_result($r_org,0,0);
			$id_exercice_org = pmb_mysql_result($r_org,0,1);
		}
		$q_dest = "select num_entite, num_exercice from actes where id_acte=".$id_cde;
		$r_dest = pmb_mysql_query($q_dest);
		if($r_dest) {
			$id_entite_dest = pmb_mysql_result($r_dest,0,0);
			$id_exercice_dest = pmb_mysql_result($r_dest,0,1);
		}
		if ( !($id_entite_org && $id_entite_dest  && $id_exercice_org && $id_exercice_dest) ) {
			return $ret;
		}

		$ret['valid'] = true;
		$ret['ids_line'] = $ids_line;

		if( ($id_entite_org==$id_entite_dest) &&  ($id_exercice_org==$id_exercice_dest) ) {
			$ret['transfer_budget'] = true;
		}

		return $ret;
	}


	public static function transfer_lines($id_cde,$ids_line) {

		$check = static::check_before_duplication_or_transfer($id_cde, $ids_line) ;
		if(!$check['valid']) {
			return '';
		}

		if($check['transfer_budget']) {
			$q = "update lignes_actes set num_acte=$id_cde where id_ligne in ({$check['ids_line']})";
		} else {
			$q =  "update lignes_actes set num_acte=$id_cde, lig_ref=0, num_acquisition=0, num_rubrique=0, remise='0.00' where id_ligne in ({$check['ids_line']})";
		}
		pmb_mysql_query($q);
	}


	public static function duplicate_lines($id_cde,$ids_line) {

		$check = static::check_before_duplication_or_transfer($id_cde, $ids_line) ;
		if(!$check['valid']) {
			return '';
		}

		if($check['transfer_budget']) {
			$q = "insert into lignes_actes ( type_ligne, num_acte, num_acquisition, num_rubrique, num_produit, num_type, libelle, code, prix, tva, nb, date_ech, date_cre, statut, remise, index_ligne, debit_tva, commentaires_gestion, commentaires_opac) ";
			$q.= "select  type_ligne, $id_cde, num_acquisition, num_rubrique, num_produit, num_type, libelle, code, prix, tva, nb, date_ech, date_cre, statut, remise, index_ligne, debit_tva, commentaires_gestion, commentaires_opac from lignes_actes where id_ligne in ({$check['ids_line']})";
		} else {
			$q = "insert into lignes_actes ( type_ligne, num_acte, num_produit, num_type, libelle, code, prix, tva, nb, date_ech, date_cre, statut, index_ligne, debit_tva, commentaires_gestion, commentaires_opac) ";
			$q.= "select  type_ligne, $id_cde, num_produit, num_type, libelle, code, prix, tva, nb, date_ech, date_cre, statut, index_ligne, debit_tva, commentaires_gestion, commentaires_opac from lignes_actes where id_ligne in ({$check['ids_line']})";
		}
		pmb_mysql_query($q);
	}
}













