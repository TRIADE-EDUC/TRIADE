<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liens_actes.class.php,v 1.15 2022/02/10 10:46:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if(!defined('TYP_ACT_ALL')) define('TYP_ACT_ALL', -1);	//				-1 = Tous types
if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande
if(!defined('TYP_ACT_DEV')) define('TYP_ACT_DEV', 1);	//				1 = Demande de devis
if(!defined('TYP_ACT_LIV')) define('TYP_ACT_LIV', 2);	//				2 = Bon de Livraison
if(!defined('TYP_ACT_FAC')) define('TYP_ACT_FAC', 3);	//				3 = Facture
if(!defined('TYP_ACT_RENT_ACC')) define('TYP_ACT_RENT_ACC', 4);	//		4 = Demande/Décompte de location
if(!defined('TYP_ACT_RENT_INV')) define('TYP_ACT_RENT_INV', 5);	//		5 = Facture de Location

class liens_actes{
	
	public $num_acte = 0;				//Numéro d'acte
	public $num_acte_lie = 0;			//Numéro d'acte lié
	 
	//Constructeur.	 
	public function __construct($num_acte= 0, $num_acte_lie= 0 ) {
		$this->num_acte = intval($num_acte);
		$this->num_acte_lie = intval($num_acte_lie);
		if (!$this->num_acte || !$this->num_acte_lie) die ("Erreur de création liens_actes");

		$q = "select count(1) from liens_actes where num_acte = '".$this->num_acte."' and num_acte_lie = '".$this->num_acte_lie."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) == 0) {
			$q = "insert into liens_actes set num_acte = '".$this->num_acte."', num_acte_lie = '".$this->num_acte_lie."' ";
			$r = pmb_mysql_query($q);
		}
	}	

	//supprime un lien entre actes de la base
	public static function delete($num_acte) {
		$num_acte = intval($num_acte);
		$q = "delete from liens_actes where num_acte = '".$num_acte."' or num_acte_lie = '".$num_acte."' ";
		pmb_mysql_query($q);
	}

	//recherche l'acte pere de l'acte passé en paramètre
	public static function getParent($num_acte_lie) {
		$q = "select num_acte from liens_actes where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}
	
	//recherche du devis origine de l'acte passe en parametre
	public static function getDevis($num_acte_lie) {
		$q = "select num_acte from liens_actes join actes on num_acte=id_acte and type_acte = '".TYP_ACT_DEV."' where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}
	
	//recherche la commande du bl/facture passe  en parametre
	public static function getOrder($num_acte_lie) {
		$q = "select num_acte from liens_actes join actes on num_acte=id_acte and type_acte = '".TYP_ACT_CDE."' where num_acte_lie = '".$num_acte_lie."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0); else return '0';  
	}
	
	//recherche les enfants de l'acte passé en paramètre
	public static function getChilds($num_acte, $type_acte=TYP_ACT_ALL) {
		$q = "select num_acte_lie, numero, type_acte, statut from liens_actes, actes where num_acte = '".$num_acte."' and id_acte = num_acte_lie ";
		if ($type_acte != TYP_ACT_ALL) $q.= "and type_acte = '".$type_acte."' ";
		$q.= "order by type_acte, numero";
		$r = pmb_mysql_query($q);
		return $r;
	}	
	
	//recherche les livraisons pour une commande
	//retourne un tableau d'ids
	public static function getDeliveries ($num_cde, $with_date=false) {
		$t=array();
		
		$q = "select num_acte_lie from liens_actes join actes on num_acte_lie = id_acte and type_acte = '".TYP_ACT_LIV."' ";
		$q.= "where num_acte = '".$num_cde."' ";
		if($with_date) {
			"and date_acte = '".$with_date."' ";
		}
		$q.= "order by numero desc";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			while($row=pmb_mysql_fetch_object($r)) {
				$t[]=$row->num_acte_lie;
			}
		}
		return $t;
	}
	
	//optimization de la table liens_actes
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE liens_actes');
		return $opt;
	}
}
?>