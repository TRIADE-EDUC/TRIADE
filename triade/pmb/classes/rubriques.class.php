<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rubriques.class.php,v 1.27 2017-07-10 12:17:03 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/actes.class.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");

class rubriques{
	
	public $id_rubrique = 0;						//Identifiant de rubrique	
	public $num_budget = 0;						//Identifiant du budget auquel appartient la rubrique
	public $num_parent = 0;						//Identifiant de la rubrique parent (0 si rubrique de tête)
	public $libelle = '';							//Libellé de rubrique
	public $commentaires = '';						//Commentaires sur la rubrique
	public $montant = '000000.00';					//Montant affecté à la rubrique
	public $num_cp_compta = '';					//Numéro de compte comptable pour affectation
	public $autorisations = '';					//Autorisations d'accès à la rubrique

	 
	//Constructeur.	 
	public function __construct($id_rubrique= 0) {
		$this->id_rubrique = $id_rubrique+0;
		if ($this->id_rubrique) {
			$this->load();	
		}
	}
	
	// charge une rubrique à partir de la base.
	public function load(){
		$q = "select * from rubriques where id_rubrique = '".$this->id_rubrique."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->num_budget = $obj->num_budget;
		$this->num_parent = $obj->num_parent;
		$this->libelle = $obj->libelle;
		$this->commentaires = $obj->commentaires;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->autorisations = $obj->autorisations;
	}
	
	// enregistre une rubrique en base.
	public function save(){
		if ($this->libelle == '' || !$this->num_budget) die("Erreur de création rubriques");
	
		if ($this->id_rubrique) {
			
			$q = "update rubriques set num_budget = '".$this->num_budget."', num_parent = '".$this->num_parent."', libelle = '".$this->libelle."', ";
			$q.= "commentaires = '".$this->commentaires."', montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', autorisations = '".$this->autorisations."' ";
			$q.= "where id_rubrique = '".$this->id_rubrique."' ";
			$r = pmb_mysql_query($q);
			
		} else {
			
			$q = "insert into rubriques set num_budget = '".$this->num_budget."', num_parent = '".$this->num_parent."', libelle = '".$this->libelle."', ";
			$q.= "commentaires = '".$this->commentaires."', montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', autorisations = '".$this->autorisations."' ";
			$r = pmb_mysql_query($q);
			$this->id_rubrique = pmb_mysql_insert_id();
			
		}

	}

	//supprime un rubrique de la base
	public static function delete($id_rubrique= 0) {
		if(!$id_rubrique) return; 	

		$q = "delete from rubriques where id_rubrique = '".$id_rubrique."' ";
		$r = pmb_mysql_query($q);
	}
	
	//calcule le montant engagé pour une rubrique budgétaire
	public static function calcEngagement($id_rubrique= 0) {
		$id_rubrique += 0;
		//	Montant Total engagé pour une rubrique =
		//	Somme des Montants engagés non facturés pour une rubrique par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour une rubrique par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)

		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q1.= "from actes, lignes_actes ";
		$q1.= "where ";
		$q1.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q1.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while($row1 = pmb_mysql_fetch_object($r1)) {
			
			$tab_cde[$row1->id_ligne]['nb']=$row1->nb;
			$tab_cde[$row1->id_ligne]['prix']=$row1->prix;				
			$tab_cde[$row1->id_ligne]['rem']=$row1->rem;				
			$tab_cde[$row1->id_ligne]['debit_tva']=$row1->debit_tva;
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb, lignes_actes.debit_tva ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q2.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."') ";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while($row2 = pmb_mysql_fetch_object($r2)) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['nb'] = $tab_cde[$row2->lig_ref]['nb'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q3.= "from actes, lignes_actes ";
		$q3.= "where ";
		$q3.= "lignes_actes.num_rubrique = '".$id_rubrique."' ";
		$q3.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."') ";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$r3 = pmb_mysql_query($q3);

		$tab_fac = array();
		while($row3 = pmb_mysql_fetch_object($r3)) {
			
			$tab_fac[$row3->id_ligne]['nb']=$row3->nb;
			$tab_fac[$row3->id_ligne]['prix']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['rem']=$row3->rem;
		
		}			

		$tot_rub = 0;
		$tab = array_merge($tab_cde, $tab_fac);
		
		foreach($tab as $key=>$value) {
			$tot_lig = $tab[$key]['nb']*$tab[$key]['prix'];
			if($tab[$key]['rem'] != 0) $tot_lig = $tot_lig * (1- ($tab[$key]['rem']/100));
			$tot_rub = $tot_rub + $tot_lig;
		}
		return $tot_rub;
	}

	
	//calcule le montant engagé pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcEngage($id_rubrique= 0, $wc=TRUE) {
		$id_rubrique += 0;
		//	Montant Total engagé pour une rubrique =
		//	Somme des Montants engagés non facturés pour une rubrique par ligne de commande		(nb_commandé-nb_facturé)*prix_commande*(1-remise_commande)
		//+ Somme des Montants engagés pour une rubrique par ligne de facture					(nb_facturé)*prix_facture*(1-remise_facture)
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		
		$q1 = "select ";
		$q1.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q1.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q1.= "from actes, lignes_actes ";
		$q1.= "where ";
		$q1.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q1.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q1.= "and actes.statut > '".STA_ACT_AVA."' and ( (actes.statut & ".STA_ACT_FAC.") != ".STA_ACT_FAC.") ";
		$q1.= "and actes.id_acte = lignes_actes.num_acte ";
		$r1 = pmb_mysql_query($q1);

		$tab_cde = array();
		while($row1 = pmb_mysql_fetch_object($r1)) {
			
			$tab_cde[$row1->id_ligne]['q']=$row1->nb;
			$tab_cde[$row1->id_ligne]['p']=$row1->prix;
			$tab_cde[$row1->id_ligne]['t']=$row1->tva;				
			$tab_cde[$row1->id_ligne]['r']=$row1->rem;
			$tab_cde[$row1->id_ligne]['debit_tva']=$row1->debit_tva;	
		
		}			
		
		$q2 = "select ";
		$q2.= "lignes_actes.lig_ref, sum(nb) as nb ";
		$q2.= "from actes, lignes_actes ";
		$q2.= "where ";
		$q2.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q2.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q2.= "and actes.id_acte = lignes_actes.num_acte ";
		$q2.= "group by lignes_actes.lig_ref ";
		$r2 = pmb_mysql_query($q2);	

		while($row2 = pmb_mysql_fetch_object($r2)) {
			if(array_key_exists($row2->lig_ref,$tab_cde)) {
				$tab_cde[$row2->lig_ref]['q'] = $tab_cde[$row2->lig_ref]['q'] - $row2->nb; 
			}
		}

		$q3 = "select ";
		$q3.= "lignes_actes.id_ligne, lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q3.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva ";
		$q3.= "from actes, lignes_actes ";
		$q3.= "where ";
		$q3.= "lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q3.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q3.= "and actes.id_acte = lignes_actes.num_acte ";
		$r3 = pmb_mysql_query($q3);

		$tab_fac = array();
		while($row3 = pmb_mysql_fetch_object($r3)) {
			
			$tab_fac[$row3->id_ligne]['q']=$row3->nb;
			$tab_fac[$row3->id_ligne]['p']=$row3->prix;				
			$tab_fac[$row3->id_ligne]['t']=$row3->tva;				
			$tab_fac[$row3->id_ligne]['r']=$row3->rem;
			$tab_fac[$row3->id_ligne]['debit_tva']=$row3->debit_tva;		
		}			

		$lg = array_merge($tab_cde, $tab_fac);
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
		
	}
	
	//calcule le montant a valider pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcAValider($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique += 0;
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les commandes non encore validees 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";
		$q.= "and (actes.type_acte = '".TYP_ACT_CDE."' or actes.type_acte = '".TYP_ACT_RENT_ACC."') ";
		$q.= "and ((actes.statut & '".STA_ACT_AVA."')= '".STA_ACT_AVA."') ";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	
	
	//calcule le montant facture pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcFacture($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique += 0;
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les factures 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";		
		$q.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	
	

	//calcule le montant facture/paye pour une rubrique budgétaire
	//$ws=avec rubriques filles
	//et retourne un tableau
	//['ht']=montant ht
	//['ttc']=montant ttc
	//['tva']=montant tva
	public static function calcPaye($id_rubrique= 0,$wc=TRUE) {
		$id_rubrique += 0;
		//	Montant A valider pour une rubrique =
		//	Somme des Montants pour les factures 
		
		if($wc) {
			$tab_r[$id_rubrique]='1';
			$tab_r=$tab_r + rubriques::getChilds($id_rubrique);
			$id_rubrique=implode("','", array_keys($tab_r));
		}
		if (!$id_rubrique) {
			return array('ht'=>0,'tva'=>0,'ttc'=>0);
		}
		$q = "select ";
		$q.= "lignes_actes.nb as nb, lignes_actes.prix as prix, ";
		$q.= "lignes_actes.tva as tva, lignes_actes.remise as rem, lignes_actes.debit_tva  ";
		$q.= "from actes, lignes_actes ";
		$q.= "where 1 ";
		$q.= "and (actes.type_acte = '".TYP_ACT_FAC."' or actes.type_acte = '".TYP_ACT_RENT_INV."' )";
		$q.= "and ((actes.statut & '".STA_ACT_PAY."') = '".STA_ACT_PAY."') ";
		$q.= "and lignes_actes.num_rubrique in('".$id_rubrique."') ";
		$q.= "and actes.id_acte = lignes_actes.num_acte ";
		$r = pmb_mysql_query($q);
		$i=0;
		$lg=array();
		while($row = pmb_mysql_fetch_object($r)) {
			$lg[$i]['q']=$row->nb;
			$lg[$i]['p']=$row->prix;				
			$lg[$i]['t']=$row->tva;
			$lg[$i]['r']=$row->rem;
			$lg[$i]['debit_tva']=$row->debit_tva;
			$i++;			
		}
		$tot_rub = calc($lg,2);
		return $tot_rub;
	}	

	//compte le nb d'enfants directs d'une rubrique
	public static function countChilds($id_rubrique=0) {
		$id_rubrique += 0;
		$q = "select count(1) from rubriques where num_parent ='".$id_rubrique."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}		

	
	//retourne la liste des descendants d'une rubrique sous forme de tableau
	//[id_rubrique]=1
	public static function getChilds($id_rubrique=0) {
		$id_rubrique += 0;
		$tab_childs=array();
		
		$q="select id_rubrique from rubriques where num_parent='".$id_rubrique."' ";
		$r=pmb_mysql_query($q);
		while($row=pmb_mysql_fetch_object($r)){
			if (!array_key_exists($row->id_rubrique, $tab_childs)) {
				$tab_childs=$tab_childs + rubriques::getChilds($row->id_rubrique);
			}
			$tab_childs[$row->id_rubrique]=1;
		}
		return $tab_childs;
	}
	
	//Liste les ancetres d'une rubrique et les retourne sous forme d'un tableau 
	//[index][niveau][0]=id_rubrique 
	//[index][niveau][1]=libelle
	//[index][niveau][2]=num_parent
	public static function listAncetres($id_rub=0, $inclus=FALSE) {
		$id_rub += 0;
		$q = "select id_rubrique, libelle, num_parent from rubriques where id_rubrique = '".$id_rub."' limit 1";
		$r = pmb_mysql_query($q);
		$row = pmb_mysql_fetch_object($r);
		$rub_list = array();

		$i=0;
		if ($inclus) {
			$rub_list[$i][0] = $row->id_rubrique;
			$rub_list[$i][1] = $row->libelle;
			$rub_list[$i][2] = $row->num_parent;
			$i++;
		}
		while ($row->num_parent){
			$q = "select id_rubrique, libelle, num_parent from rubriques where id_rubrique = '".$row->num_parent."' limit 1";
			$r = pmb_mysql_query($q);
			$row = pmb_mysql_fetch_object($r);
			$rub_list[$i][0] = $row->id_rubrique;
			$rub_list[$i][1] = $row->libelle;
			$rub_list[$i][2] = $row->num_parent;
			$i++;
		}
		$rub_list = array_reverse($rub_list);
		return $rub_list;		
	}	

	//Compte le nb de lignes d'actes affectées à une rubrique budgetaire			
	public static function hasLignes($id_rubrique=0){
		$id_rubrique += 0;
		if (!$id_rubrique) return 0;

		$q = "select count(1) from lignes_actes where num_rubrique = '".$id_rubrique."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}	

	//Recalcul des montants des rubriques parent et raz des numéros de compte comptable et autorisations
	public static function maj($num_parent=0, $calcul=TRUE ) {
		if ($calcul) {
			if($num_parent) {
				$q = "select sum(montant) from rubriques where num_parent = '".$num_parent."' ";
				$r = pmb_mysql_query($q);
				$total = pmb_mysql_result($r,0,0);
			
				$parent = new rubriques($num_parent);	
				$parent->montant = $total;
				$parent->num_cp_compta = '';
				$parent->autorisations = '';
				$parent->save();
			
				rubriques::maj($parent->num_parent);
			}
		} else {
			if($num_parent) {
				$parent = new rubriques($num_parent);	
				$parent->num_cp_compta = '';
				$parent->autorisations = '';
				$parent->save();				
				rubriques::maj($parent->num_parent, FALSE);
			}
			
		}
	}
	
	//
	public static function getAutorisations($id_rubrique, $id_user) {
		$id_rubrique += 0;
		$q = "select count(1) from rubriques where id_rubrique = '".$id_rubrique."' and autorisations like('% ".$id_user." %') ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}
	
	//optimization de la table rubriques
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE rubriques');
		return $opt;
	}
	
	//Retourne un tableau (id_rubrique=>libelle) a partir d'un tableau d'id 
	//si id_bibli et id_exer, userid sont précisés, limite les resultats aux rubriques par bibliotheque, exercice, utilisateur
	public static function getLibelle($tab=array(),$id_bibli=0,$id_exer=0,$userid=0) {
		$res=array();
		if(is_array($tab) && count($tab)) {
			$q = "select id_rubrique, rubriques.libelle from rubriques ";
			if ($id_exer || $id_bibli) {
				$q.= "join budgets on num_budget=id_budget " ;
			}
			$q.= "where 1 ";
			if($id_bibli) $q.= " and num_entite='".$id_bibli."' ";
			if($id_exer) $q.= " and num_exercice='".$id_exer."' ";
			if($userid) $q.= " and autorisations like '% ".$userid." %' ";
			$q.= "and id_rubrique in ('".implode("','", $tab)."') ";
			$r = pmb_mysql_query($q);
			while($row=pmb_mysql_fetch_object($r)) {
				$res[$row->id_rubrique]=$row->libelle;
			}
		}
		return $res;
	}
}
?>