<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: actes.class.php,v 1.45 2018-04-23 13:25:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path.'/acquisition/achats/func_achats.inc.php');
require_once($include_path.'/misc.inc.php');
global $pmb_indexation_lang;
if($pmb_indexation_lang) {
	require_once($include_path.'/marc_tables/'.$pmb_indexation_lang.'/empty_words');
}

require_once("$class_path/liens_actes.class.php");
require_once("$class_path/audit.class.php");

if(!defined('TYP_ACT_ALL')) define('TYP_ACT_ALL', -1);	//				-1 = Tous types
if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande
if(!defined('TYP_ACT_DEV')) define('TYP_ACT_DEV', 1);	//				1 = Demande de devis
if(!defined('TYP_ACT_LIV')) define('TYP_ACT_LIV', 2);	//				2 = Bon de Livraison
if(!defined('TYP_ACT_FAC')) define('TYP_ACT_FAC', 3);	//				3 = Facture
if(!defined('TYP_ACT_LOC_CDE')) define('TYP_ACT_LOC_CDE', 4);	//		4 = Demande de location
if(!defined('TYP_ACT_LOC_FAC')) define('TYP_ACT_LOC_FAC', 5);	//		5 = Facture de Location

if(!defined('STA_ACT_ALL')) define('STA_ACT_ALL', -1);	//Statut acte	-1 = Tous
if(!defined('STA_ACT_AVA')) define('STA_ACT_AVA', 1);	//				1 = A valider
if(!defined('STA_ACT_ENC')) define('STA_ACT_ENC', 2);	//				2 = En cours
if(!defined('STA_ACT_REC')) define('STA_ACT_REC', 4);	//				4 = Reçu/Livré
if(!defined('STA_ACT_FAC')) define('STA_ACT_FAC', 8);	//				8 = Facturé
if(!defined('STA_ACT_PAY')) define('STA_ACT_PAY', 16);	//				16 = Payé
if(!defined('STA_ACT_ARC')) define('STA_ACT_ARC', 32);	//				32 = Archivé


class actes{
	
	public $id_acte = 0;							//Identifiant de l'acte	
	public $date_acte = '0000-00-00';				//date de création de l'acte
	public $numero = '';							//Numero de l'acte
	public $nom_acte = '';							//Nom de l'acte
	public $type_acte = 0;							//Type d'acte (0 = Commande, 1 = Demande de devis, 2 = Bon de Livraison, 3 = Facture, ...)
	public $statut = 0;							//Statut de l'acte (
												//Commande			1=A valider, 2=En cours, 4=Livrée, 8=Facturée, 16=Payée, 32=Archivée
												//Demande Devis		2=En cours, 4=Reçu, 32=Archivé
												//Bon de Livraison	4=Recu, 32=Archivé
												//Facture			4=Reçue, 16=Payée, 32=Archivée
	public $date_paiement = '0000-00-00';			//Date du paiement (pré-paiement)
	public $num_paiement = 0;						//Numéro de virement, chèque, ...
	public $num_entite = 0;						//Identifiant de l'entité sur laquelle est affectée la acte
	public $num_fournisseur = 0;					//Identifiant du fournisseur associé
	public $num_contact_livr = 0;					//Identifiant du contact pour l'adresse de livraison
	public $num_contact_fact = 0;					//Identifiant du contact pour l'adresse de facturation
//TODO 	Voir suppression num_exercice
	public $num_exercice = 0;						//Identifiant de l'exercice auquel est affecté l'acte
	public $commentaires = '';						//Lignes de commentaires de gestion
	public $reference = '';						//Référence fournisseur
	public $index_acte = '';						//Champ de recherche fulltext
	public $commentaires_i = '';					//Lignes de commentaires imprimés sur la commande
	public $devise = '';							//Devise de la commande
	public $date_ech = '0000-00-00';				//Echeance acte
	public $date_valid = '0000-00-00';				//Date de validation
	
	//Constructeur.	 
	public function __construct($id_acte=0) {
		$this->id_acte = $id_acte+0;
		if ($this->id_acte) {
			$this->load();	
		} else {
			$this->date_acte = today();
		}
	}	
	
	// charge une acte à partir de la base.
	public function load(){
		$q = "select * from actes where id_acte = '".$this->id_acte."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->date_acte = $obj->date_acte;
		$this->numero = $obj->numero;
		$this->nom_acte = $obj->nom_acte;
		$this->type_acte = $obj->type_acte;
		$this->statut = $obj->statut;
		$this->date_paiement = $obj->date_paiement;
		$this->num_paiement = $obj->num_paiement;
		$this->num_entite = $obj->num_entite;
		$this->num_fournisseur = $obj->num_fournisseur;
		$this->num_contact_livr = $obj->num_contact_livr;
		$this->num_contact_fact = $obj->num_contact_fact;
		//TODO Voir suppression num_exercice
		$this->num_exercice = $obj->num_exercice;
		$this->commentaires = $obj->commentaires;
		$this->reference = $obj->reference;
		$this->commentaires_i = $obj->commentaires_i;
		$this->devise = $obj->devise;
		$this->date_ech = $obj->date_ech;
		$this->date_valid = $obj->date_valid;
	}
	
	// enregistre un acte en base.
	public function save(){
		global $num_cde,$num_dev;
		
		if ( !$this->num_entite  || !$this->num_fournisseur ) die("Erreur de création actes");
		
		//récupération du libelle fournisseur
		$q = "select raison_sociale from entites where id_entite = '".$this->num_fournisseur."' ";
		$r = pmb_mysql_query($q);

		$fou = pmb_mysql_result($r, 0, 0);		
		$num = '';
		if($this->type_acte == TYP_ACT_CDE)
			$num = trim($num_cde);
		else if($this->type_acte == TYP_ACT_DEV)
			$num = trim($num_dev);
		if ($this->id_acte) {
			if ($num!='') {
				$this->numero=$num;
			} else {
				$this->numero=addslashes($this->numero);
			}
			$q = "update actes set ";
			$q.= "numero = '".$this->numero."', ";
			$q.= "nom_acte = '".$this->nom_acte."', ";
			$q.= "statut = '".$this->statut."', ";
			$q.= "date_paiement = '".$this->date_paiement."', ";
			$q.= "num_paiement = '".$this->num_paiement."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', ";
			$q.= "num_contact_livr = '".$this->num_contact_livr."', ";
			$q.= "num_contact_fact = '".$this->num_contact_fact."', "; 
//TODO Voir suppression num_exercice
			$q.= "num_exercice = '".$this->num_exercice."', ";
			$q.= "commentaires = '".$this->commentaires."', ";
			$q.= "reference = '".$this->reference."', ";
			$q.= "commentaires_i = '".$this->commentaires_i."', ";
			$q.= "devise = '".$this->devise."', ";
			$q.= "date_ech = '".$this->date_ech."', ";
			$q.= "date_valid = '".$this->date_valid."', ";
			$q.= "index_acte = ' ".$this->numero." ".strip_empty_words($fou)." ".strip_empty_words($this->commentaires)." ".strip_empty_words($this->reference)." ' "; 
			$q.= "where id_acte = '".$this->id_acte."' ";
			$r = pmb_mysql_query($q);
			audit::insert_modif(AUDIT_ACQUIS, $this->id_acte);		
		} else {
			if ($num!='') {
				$this->numero=$num;
			} else {
				$this->calc();
			}
			$q = "insert into actes set type_acte = '".$this->type_acte."', ";
			$q.= "date_acte = '".today()."', ";
			$q.= "numero = '".$this->numero."', ";
			$q.= "nom_acte = '".$this->nom_acte."', ";
			$q.= "statut = '".$this->statut."', ";
			$q.= "date_paiement = '".$this->date_paiement."', ";
			$q.= "num_paiement = '".$this->num_paiement."', ";
			$q.= "num_entite = '".$this->num_entite."', ";
			$q.= "num_fournisseur = '".$this->num_fournisseur."', ";
			$q.= "num_contact_livr = '".$this->num_contact_livr."', ";
			$q.= "num_contact_fact = '".$this->num_contact_fact."', ";
//TODO Voir suppression num_exercice			
			$q.= "num_exercice = '".$this->num_exercice."', ";
			$q.= "commentaires = '".$this->commentaires."' , ";
			$q.= "reference = '".$this->reference."', "; 
			$q.= "commentaires_i = '".$this->commentaires_i."', ";
			$q.= "devise = '".$this->devise."', ";
			$q.= "date_ech = '".$this->date_ech."', ";
			$q.= "date_valid = '".$this->date_valid."', ";
			$q.= "index_acte = ' ".strip_empty_words($this->numero)." ".strip_empty_words($fou)." ".strip_empty_words($this->commentaires)." ".strip_empty_words($this->reference)." ' "; 
			$r = pmb_mysql_query($q);
			$this->id_acte = pmb_mysql_insert_id();
			audit::insert_creation(AUDIT_ACQUIS, $this->id_acte);	
		}
	}

	//supprime un acte de la base
	public function delete($id_acte= 0) {
		if(!$id_acte) $id_acte = $this->id_acte; 	

		actes::deleteLignes($id_acte);
		liens_actes::delete($id_acte);
		$q = "delete from actes where id_acte = '".$id_acte."' ";
		pmb_mysql_query($q);
		audit::delete_audit(AUDIT_ACQUIS, $id_acte);

	}

	//supprime les lignes d'un acte
	public static function deleteLignes($id_acte) {
		$query = "delete from lignes_actes_applicants where ligne_acte_num in (select id_ligne from lignes_actes where num_acte=".$id_acte.") ";
		pmb_mysql_query($query);
		
		$q = "delete from lignes_actes where num_acte = '".$id_acte."' ";
		pmb_mysql_query($q);
	}

	//supprime les lignes de l'acte non comprises dans le tableau de lignes
	public function cleanLignes($id_acte = 0, $tab_lig=array()) {
		if(!$id_acte) $id_acte=$this->id_acte;
		if(count($tab_lig)==0) return;

		$list_lig=implode("','", $tab_lig);
		$q = "delete from lignes_actes where num_acte='".$id_acte."' and id_ligne not in ('".$list_lig."')";
		pmb_mysql_query($q);
	}
	
	//Recherche la prochaine echeance d'une commande en cours 
	public static function getNextLivr($id_acte) {
		$q = "select min((date_format(date_ech, '%Y%m%d'))) from lignes_actes where num_acte = '".$id_acte."' and (('2' & statut) = '0') ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$res = pmb_mysql_result($r,0,0);
			$res = substr($res,0,4).'-'.substr($res,4,2).'-'.substr($res,6,2);
		} else $res = '0';
		return $res;
	}

	// calcule le numéro d'un acte en base.
	// Il faut d'abord avoir renseigné le numéro d'entité et le type d'acte
	public function calc(){
		$this->numero = calcNumero($this->num_entite, $this->type_acte);
	}

	// Retourne les lignes d'un acte
	public static function getLignes($id_acte=0, $param=0){
		//if(!$id_acte) $id_acte = $this->id_acte;
		$q = "select * from lignes_actes where num_acte = '".$id_acte."' ";
		if($param) $q.="and ".$param." ";
		$r = pmb_mysql_query($q);
		return $r; 
	}

//TODO Voir suppression num_exercice 
	//Retourne la liste des actes appartenant à l'exercice passé en paramètres
	public static function listByExercice($num_exercice){
		$q = "select id_acte from actes where num_exercice = '".$num_exercice."' ";
		$r = pmb_mysql_query($q);
		return $r; 
	}

	//Retourne un tableau de la liste des etats possibles pour un acte en fonction de son type (valeur, libelle)
	public static function getStatelist($type_acte, $all=TRUE) {
		global $msg;
		$t=array();
		switch($type_acte) {
			case TYP_ACT_DEV :
				if ($all) {
					$t[-1]=$msg['acquisition_dev_tous'];
				}
				$t[2]=$msg['acquisition_dev_enc'];
				$t[4]=$msg['acquisition_dev_rec'];
				$t[32]=$msg['acquisition_dev_arc'];
				break;
			case TYP_ACT_CDE :
				if ($all) {
					$t[-1]=$msg['acquisition_cde_tous'];
				}
				$t[1]=$msg['acquisition_cde_aval'];
				$t[2]=$msg['acquisition_cde_enc'];
				$t[4]=$msg['acquisition_cde_liv'];
				$t[32]=$msg['acquisition_cde_arc'];
				break;
			case TYP_ACT_LIV :
				if ($all) {
					$t[-1]=$msg['acquisition_liv_tous'];
				}
				$t[4]=$msg['acquisition_liv_rec'];
				$t[32]=$msg['acquisition_liv_arc'];
				break;
			case TYP_ACT_FAC :
				if ($all) {
					$t[-1]=$msg['acquisition_fac_tous'];
				}
				$t[4]=$msg['acquisition_fac_rec'];
				$t[16]=$msg['acquisition_fac_pay'];
				$t[32]=$msg['acquisition_fac_arc'];
				break;
		}
		return $t;
	}
	
	public function update_statut($id_acte=0) {
		if(!$id_acte) $id_acte = $this->id_acte;
		$q = "update actes set statut='".$this->statut."' where id_acte='".$id_acte."' ";
		pmb_mysql_query($q);
	}

	//optimization de la table actes
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE actes');
		return $opt;
	}
}
?>