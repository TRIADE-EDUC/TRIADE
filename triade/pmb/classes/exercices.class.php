<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exercices.class.php,v 1.23 2019-03-06 11:48:22 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/actes.class.php");
require_once("$class_path/budgets.class.php");
require_once("$class_path/entites.class.php");

if(!defined('STA_EXE_CLO')) define('STA_EXE_CLO', 0);	//Statut		0 = Cloturé
if(!defined('STA_EXE_ACT')) define('STA_EXE_ACT', 1);	//Statut		1 = Actif
if(!defined('STA_EXE_DEF')) define('STA_EXE_DEF', 3);	//Statut		3 = Actif par défaut

class exercices{

	public $id_exercice = 0;					//Identifiant de l'exercice
	public $num_entite = 0;
	public $libelle = '';
	public $date_debut = '2006-01-01';
	public $date_fin = '2006-01-01';
	public $statut = STA_EXE_ACT;			//Statut de l'exercice

	//Constructeur.
	public function __construct($id_exercice= 0) {
		$this->id_exercice = $id_exercice+0;
		if ($this->id_exercice) {
			$this->load();
		}
	}

	// charge l'exercice à partir de la base.
	public function load(){
		$q = "select * from exercices where id_exercice = '".$this->id_exercice."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->id_exercice = $obj->id_exercice;
		$this->num_entite = $obj->num_entite;
		$this->libelle = $obj->libelle;
		$this->date_debut = $obj->date_debut;
		$this->date_fin = $obj->date_fin;
		$this->statut = $obj->statut;
	}

	// enregistre l'exercice en base.
	public function save(){
		if( (!$this->num_entite) || ($this->libelle == '') ) die("Erreur de création exercice");
		if($this->id_exercice) {
			$q = "update exercices set num_entite = '".$this->num_entite."', libelle ='".$this->libelle."', ";
			$q.= "date_debut = '".$this->date_debut."', date_fin = '".$this->date_fin."', statut = '".$this->statut."' ";
			$q.= "where id_exercice = '".$this->id_exercice."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into exercices set num_entite = '".$this->num_entite."', libelle = '".$this->libelle."', ";
			$q.= "date_debut =  '".$this->date_debut."', date_fin = '".$this->date_fin."', statut = '".$this->statut."' ";
			pmb_mysql_query($q);
			$this->id_exercice = pmb_mysql_insert_id();
			$this->load();
		}
	}

	//supprime un exercice de la base
	public static function delete($id_exercice= 0) {
		$id_exercice += 0;
		if(!$id_exercice) return;

		//Suppression des actes
//TODO Voir suppression du lien entre actes et exercices

 		$res_actes = actes::listByExercice($id_exercice);
		while (($row = pmb_mysql_fetch_object($res_actes))) {
			actes::delete($row->id_acte);
		}

		//Suppression des budgets
		$res_budgets = budgets::listByExercice($id_exercice);
		while (($row = pmb_mysql_fetch_object($res_budgets))) {
			budgets::delete($row->id_budget);
		}
		//Suppression de l'exercice
		$q = "delete from exercices where id_exercice = '".$id_exercice."' ";
		pmb_mysql_query($q);

	}

	//retourne une requete pour la liste des exercices de l'entité
	public static function listByEntite($id_entite, $mask='-1', $order='date_debut desc') {
		$q = "select * from exercices where num_entite = '".$id_entite."' ";
		if ($mask != '-1') $q.= "and (statut & '".$mask."') = '".$mask."' ";
		$q.= "order by ".$order." ";
		return $q;
	}

	//Vérifie si un exercice existe
	public static function exists($id_exercice){
		$q = "select count(1) from exercices where id_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le libellé d'un exercice existe déjà pour une entité
	public static function existsLibelle($id_entite, $libelle, $id_exercice=0){
		$id_entite += 0;
		$id_exercice += 0;
		$q = "select count(1) from exercices where libelle = '".$libelle."' and num_entite = '".$id_entite."' ";
		if ($id_exercice) $q.= "and id_exercice != '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb de budgets affectés à un exercice
	public static function hasBudgets($id_exercice=0){
		$id_exercice += 0;
		if (!$id_exercice) return 0;
		$q = "select count(1) from budgets where num_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb de budgets actifs affectés à un exercice
	public static function hasBudgetsActifs($id_exercice=0){
		$id_exercice += 0;
		if (!$id_exercice) return 0;
		$q = "select count(1) from budgets where num_exercice = '".$id_exercice."' and statut != '2' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb d'actes affectés à un exercice
	public static function hasActes($id_exercice=0){
		$id_exercice += 0;
		if (!$id_exercice) return 0;
		$q = "select count(1) from actes where num_exercice = '".$id_exercice."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//Compte le nb d'actes actifs affectés à un exercice
	//Actes actifs == commandes non soldées et non payées
	public static function hasActesActifs($id_exercice=0){
		$id_exercice += 0;
		if (!$id_exercice) return 0;
		$q = "select count(1) from actes where num_exercice = '".$id_exercice."' ";
		$q.= "and (type_acte = 0 and (statut & 32) != 32) ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);

	}

	//choix exercice par défaut pour une entité
	public function setDefault($id_exercice=0) {
		if (!$id_exercice) $id_exercice = $this->id_exercice;
		$q = "update exercices set statut = '".STA_EXE_ACT."' where statut = '".STA_EXE_DEF."' and num_entite = '".$this->num_entite."' limit 1 ";
		pmb_mysql_query($q);
		$q = "update exercices set statut = '".STA_EXE_DEF."' where id_exercice = '".$this->id_exercice."' limit 1 ";
		pmb_mysql_query($q);

	}

	//Recuperation de l'exercice session
	public static function getSessionExerciceId($id_bibli,$id_exer) {
		global $deflt3exercice;

		$q = "select id_exercice from exercices where num_entite = '".$id_bibli."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
		$q.= "order by statut desc ";
		$r = pmb_mysql_query($q);
		$res=array();
		while($row=pmb_mysql_fetch_object($r)) {
			$res[]=$row->id_exercice;
		}
		if (!$id_exer && isset($_SESSION['id_exercice']) && $_SESSION['id_exercice']) {
			$id_exer=$_SESSION['id_exercice'];
		}
		if (in_array($id_exer,$res)) {
			$_SESSION['id_exercice']=$id_exer;
		} elseif (in_array($deflt3exercice,$res)) {
			$_SESSION['id_exercice']=$deflt3exercice;
		} else {
			$_SESSION['id_exercice']=$res[0];
		}
		return $_SESSION['id_exercice'];
	}

	//Definition de l'exercice session
	public function setSessionExerciceId($deflt3exercice) {
		$_SESSION['id_exercice']=$deflt3exercice;
		return;
	}

	//optimization de la table exercices
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE exercices');
		return $opt;
	}

	//Retourne un selecteur html avec la liste des exercices actifs pour une ou plusieurs bibliotheque
	public static function getHtmlSelect($id_bibli=0, $selected=0, $sel_all=FALSE, $sel_attr=array()) {
		global $msg,$charset;

		$sel='';
		if ($id_bibli) {
			$q = "select id_exercice, libelle from exercices where num_entite = '".$id_bibli."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
			$q.= "order by statut desc, libelle asc ";
			$r = pmb_mysql_query($q);
			$res = array();
			if ($sel_all) {
				$res[0]=$msg['acquisition_exer_all'];
			}
			while ($row = pmb_mysql_fetch_object($r)){
				$res[$row->id_exercice] = $row->libelle;
			}

			if (count($res)) {
				$sel="<select ";
				if (count($sel_attr)) {
					foreach($sel_attr as $attr=>$val) {
						$sel.="$attr='".$val."' ";
					}
				}
				$sel.=">";
				foreach($res as $id=>$val){
					$sel.="<option value='".$id."'";
					if($id==$selected) $sel.=' selected=selected';
					$sel.=" >";
					$sel.=htmlentities($val,ENT_QUOTES,$charset);
					$sel.="</option>";
				}
				$sel.='</select>';
			}
		}
		return $sel;
	}

	public static function getActiveExercicesByEntite($id_bibli) {
		$id_bibli = intval($id_bibli);
		if(!$id_bibli) {
			 return [];
		}
		$q = "select id_exercice, libelle from exercices where num_entite = '".$id_bibli."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' ";
		$q.= "order by statut desc, libelle asc ";
		$r = pmb_mysql_query($q);
		if(!$r) {
			return [];
		}
		while($row = pmb_mysql_fetch_assoc($r)) {
			$ret[$row['id_exercice']] = $row['libelle'];
		}
		return $ret;
	}
}






