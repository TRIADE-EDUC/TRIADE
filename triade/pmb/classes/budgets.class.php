<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: budgets.class.php,v 1.30 2017-04-19 12:37:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/actes.class.php");

if(!defined('TYP_BUD_RUB')) define('TYP_BUD_RUB', 0);	//Type de budget	0 = Affectation par rubrique
if(!defined('TYP_BUD_GLO')) define('TYP_BUD_GLO', 1);	//					1 = Affectation globale

if(!defined('STA_BUD_PRE')) define('STA_BUD_PRE', 0);	//Statut		0 = En préparation
if(!defined('STA_BUD_VAL')) define('STA_BUD_VAL', 1);	//				1 = Valide
if(!defined('STA_BUD_CLO')) define('STA_BUD_CLO', 2);	//				2 = Cloturé

class budgets{
	
	
	public $id_budget = 0;							//Identifiant de budget	
	public $num_entite = 0;						//Identifiant de l'entité propriétaire du budget
	public $num_exercice = 0;						//Numéro de l'exercice sur lequel le budget est affecté
	public $libelle = '';							//Libellé du budget
	public $commentaires = '';						//Commentaires sur le budget
	public $montant_global = '000000.00';			//Montant global du budget
	public $seuil_alerte = '000';					//Niveau d'alerte en % du montant global
	public $statut = '0';							//Statut du budget (0=En préparation, 1=valide, 2=Cloturé)
	public $type_budget = '0';						//Type de budget 0=Affectation par rubriques, 1=Affectation globale
	 
	//Constructeur.	 
	public function __construct($id_budget= 0){ 
		$this->id_budget = $id_budget+0;
		if ($this->id_budget) {
			$this->load();	
		}
	}	
	
	// charge un budget à partir de la base.
	public function load(){
		$q = "select * from budgets where id_budget = '".$this->id_budget."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->num_entite = $obj->num_entite;
		$this->num_exercice = $obj->num_exercice;
		$this->libelle = $obj->libelle;
		$this->commentaires = $obj->commentaires;
		$this->montant_global = $obj->montant_global;
		$this->seuil_alerte = $obj->seuil_alerte;
		$this->statut = $obj->statut;
		$this->type_budget = $obj->type_budget;
	}

	
	// enregistre un budget en base.
	public function save(){
		if( $this->libelle == '' || !$this->num_entite || !$this->num_exercice ) die("Erreur de création budgets");
		if($this->id_budget) {
				$q = "update budgets set num_entite = '".$this->num_entite."', num_exercice = '".$this->num_exercice."', libelle = '".addslashes($this->libelle)."', ";
				$q.= "commentaires = '".addslashes($this->commentaires)."', montant_global = '".$this->montant_global."', seuil_alerte = '".$this->seuil_alerte."', ";
				$q.= "statut = '".$this->statut."', type_budget = '".$this->type_budget."' "; 
				$q.= "where id_budget = '".$this->id_budget."' ";
				pmb_mysql_query($q);
		} else {
			$q = "insert into budgets set num_entite = '".$this->num_entite."', num_exercice = '".$this->num_exercice."', libelle = '".addslashes($this->libelle)."', ";
			$q.= "commentaires = '".addslashes($this->commentaires)."', montant_global = '".$this->montant_global."', seuil_alerte = '".$this->seuil_alerte."', ";
			$q.= "statut = '".$this->statut."', type_budget = '".$this->type_budget."' "; 
			pmb_mysql_query($q);
			$this->id_budget = pmb_mysql_insert_id();
		}
	}

	// duplique un budget et l'enregistre en base.
	public static function duplicate($id_budget=0){
		$id_budget += 0;
		$new_bud = new budgets($id_budget);
		$new_bud->id_budget = 0;

		$lib = $new_bud->libelle.'_';
		$l_lib = strlen($lib);
		$q = "select if(max(substring(libelle, ".$l_lib."+1)) is null, 1, max(substring(libelle, ".$l_lib."+1))+1)  from budgets ";
		$q.= "where substring(libelle, 1, ".$l_lib.") = '".addslashes($lib)."' ";
		$q.= "and substring(libelle, ".$l_lib."+1) regexp '^[0-9]+\$' ";
		$r = pmb_mysql_query($q);
		$n=pmb_mysql_result($r, 0, 0);
		$new_bud->libelle = $lib.$n;
		
		$new_bud->statut = STA_BUD_PRE;
		$new_bud->save();
		$id_new_bud = $new_bud->id_budget;
		
		$q = budgets::listAllRubriques($id_budget);
		$r = pmb_mysql_query($q);
		$tab_p = array();
		while (($obj=pmb_mysql_fetch_object($r))) {
			
			$new_rub = new rubriques($obj->id_rubrique);
			$new_rub->num_budget = $id_new_bud;
			$new_rub->id_rubrique = 0;
			if ($obj->num_parent) $new_rub->num_parent = $tab_p[$obj->num_parent];
			$new_rub->save();
			$id_new_rub = $new_rub->id_rubrique;
			$tab_p[$obj->id_rubrique]= $id_new_rub;
			
		}
		return $id_new_bud;
	}

	//supprime un budget de la base
	public function delete($id_budget= 0) {
		$id_budget += 0;
		if(!$id_budget) $id_budget = $this->id_budget; 	

		$q = "delete from budgets where id_budget = '".$id_budget."' ";
		pmb_mysql_query($q);
		
		//supprime les rubriques associées
		$q = "delete from rubriques where num_budget = '".$id_budget."' ";
		pmb_mysql_query($q);
	}

	//retourne une requete pour liste des budgets de l'entité
	public static function listByEntite($id_entite) {
		$id_entite += 0;
		$q = "select * from budgets where num_entite = '".$id_entite."' order by statut, libelle  ";
		return $q;
	}

	//retourne la liste des budgets d'un exercice
	public static function listByExercice($num_exercice) {
		$num_exercice += 0;
		$q = "select id_budget, libelle from budgets where num_exercice = '".$num_exercice."' ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Vérifie si un budget existe			
	public static function exists($id_budget){
		$id_budget += 0;
		$q = "select count(1) from budgets where id_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libellé d'un budget existe déjà pour une entité	et un même exercice		
	public static function existsLibelle($id_entite, $libelle, $id_exer, $id_budget=0){
		$id_entite += 0;
		$id_exer += 0;
		$id_budget += 0;
		$q = "select count(1) from budgets where libelle = '".$libelle."' and num_entite = '".$id_entite."' ";
		$q.= "and num_exercice = '".$id_exer."' ";
		if ($id_budget) $q.= "and id_budget != '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//compte le nb de budgets activés pour une entité			
	public static function countActifs($id_entite, $id_budget=0){
		$id_entite += 0;
		$id_budget += 0;
		$q = "select count(1) from budgets where num_entite = '".$id_entite."' and statut = '1' ";
		if ($id_budget) $q.= "and id_budget != '".$id_budget."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//Compte le nb de lignes d'actes affectées à un budget			
	public static function hasLignes($id_budget=0){
		$id_budget += 0;
		$q = "select id_rubrique from rubriques where num_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q);
		$nb = pmb_mysql_num_rows($r);
		
		if ($nb != '0') {			
			$liste= '';
			for ($i=0; $i<$nb; $i++) { 
				$row =pmb_mysql_fetch_row($r);
				$liste.= $row[0];
				if ($i<$nb-1) $liste.= ', ';
			}
			
			$q = "select count(1) from lignes_actes where num_rubrique in (".$liste.") ";
			$r = pmb_mysql_query($q); 
			return pmb_mysql_result($r, 0, 0);
		} else return '0';
	}	

	//Retourne une requete pour les rubriques d'un budget ayant pour parent la rubrique mentionnée
	public static function listRubriques($id_budget=0, $num_parent=0){
		$id_budget += 0;
		$num_parent += 0;
		$q = "select * from rubriques where num_budget = '".$id_budget."' ";
		$q.= "and num_parent = '".$num_parent."' ";
		$q.= "order by libelle ";
		return $q;
	}


	//Retourne une requete pour l'ensemble des rubriques d'un budget 	
	public static function listAllRubriques($id_budget=0){
		$id_budget += 0;
		$q = "select * from rubriques where num_budget = '".$id_budget."' order by num_parent asc ";
		return $q;
	}


	//Retourne le nombre de rubriques d'un budget	
	public static function countRubriques($id_budget=0){
		$id_budget += 0;
		$q = "select count(1) from rubriques where num_budget = '".$id_budget."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0); 
	}
	
	
	//calcule le montant engagé pour un budget 
	public static function calcEngagement($id_budget=0) {
		//	Montant Total engagé pour un budget =
		//	Somme des Montants engagés non facturés pour les rubriques du budget par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour les rubriques du budget par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)
		$id_budget += 0;
		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem ";
		$q1.= "from actes, lignes_actes, rubriques ";
		$q1.= "where ";
		$q1.= "actes.type_acte = '".TYP_ACT_CDE."' ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and rubriques.num_budget = '".$id_budget."' ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$q1.= "and lignes_actes.num_rubrique = rubriques.id_rubrique ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while (($row1 = pmb_mysql_fetch_object($r1))) {
			
			$tab_cde[$row1->id_ligne]['nb']=$row1->nb;
			$tab_cde[$row1->id_ligne]['prix']=$row1->prix;				
			$tab_cde[$row1->id_ligne]['rem']=$row1->rem;
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "actes.type_acte = '".TYP_ACT_FAC."' ";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while(($row2 = pmb_mysql_fetch_object($r2))) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['nb'] = $tab_cde[$row2->lig_ref]['nb'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem ";
		$q3.= "from actes, lignes_actes, rubriques ";
		$q3.= "where ";
		$q3.= "actes.type_acte = '".TYP_ACT_FAC."' ";
		$q3.= "and rubriques.num_budget = '".$id_budget."' ";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$q3.= "and lignes_actes.num_rubrique = rubriques.id_rubrique ";
		$r3 = pmb_mysql_query($q3);
		$tab_fac = array();
		while (($row3 = pmb_mysql_fetch_object($r3))) {
			
			$tab_fac[$row3->id_ligne]['nb']=$row3->nb;
			$tab_fac[$row3->id_ligne]['prix']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['rem']=$row3->rem;
		
		}			

		$tot_bud = 0;
		$tab = array_merge($tab_cde, $tab_fac);
		
		foreach($tab as $key=>$value) {
			$tot_lig = $tab[$key]['nb']*$tab[$key]['prix'];
			if($tab[$key]['rem'] != 0) $tot_lig = $tot_lig * (1- ($tab[$key]['rem']/100));
			$tot_bud = $tot_bud + $tot_lig;
		}
		return $tot_bud;
	}
	

	//Recalcul du montant global du budget
	public static function calcMontant($id_budget=0) {
		$id_budget += 0;
		if($id_budget) {
			$q = "select sum(montant) from rubriques where num_budget = '".$id_budget."' and num_parent = '0' ";
			$r = pmb_mysql_query($q);
			$total = pmb_mysql_result($r,0,0);
			$budget = new budgets($id_budget);
			$budget->montant_global = $total;
			$budget->save();
		}	
	}	


	//optimization de la table budgets
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE budgets');
		return $opt;
	}
}
?>