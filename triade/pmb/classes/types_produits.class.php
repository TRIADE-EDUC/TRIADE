<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: types_produits.class.php,v 1.16 2019-05-31 08:07:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class types_produits{
	
	
	public $id_produit = 0;					//Identifiant du type_produit 
	public $libelle = '';
	public $num_cp_compta = 0;
	public $num_tva_achat = 0;

	 
	//Constructeur.	 
	public function __construct($id_produit= 0) {
		$this->id_produit = $id_produit+0;
		if ($this->id_produit) {
			$this->load();	
		}
	}
	
		
	// charge le type de produit à partir de la base.
	public function load(){
		$q = "select * from types_produits where id_produit = '".$this->id_produit."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
	}
	
	// enregistre le type de produit en base.
	public function save(){
		if($this->libelle == '') die("Erreur de création type produit");

		if($this->id_produit) {
			$q = "update types_produits set libelle ='".$this->libelle."', num_cp_compta = '".$this->num_cp_compta."', ";
			$q.= "num_tva_achat = '".$this->num_tva_achat."' ";
			$q.= "where id_produit = '".$this->id_produit."' ";
			$r = pmb_mysql_query($q);		
		} else {
			$q = "insert into types_produits set libelle = '".$this->libelle."', num_cp_compta = '".$this->num_cp_compta."', ";
			$q.= " num_tva_achat = '".$this->num_tva_achat."' ";
			$r = pmb_mysql_query($q);
			$this->id_produit = pmb_mysql_insert_id();
		}
	}

	//supprime un type de produit de la base
	public static function delete($id_produit= 0) {
		$id_produit += 0;
		if(!$id_produit) return; 	

		$q = "delete from types_produits where id_produit = '".$id_produit."' ";
		$r = pmb_mysql_query($q);
	}

	//Retourne une requete pour liste des types de produits
	public static function listTypes($debut=0, $nb_per_page=0) {
		$q = "select * from types_produits order by libelle ";
		if ($debut) {
			$q.="limit ".$debut ;
			if($nb_per_page) $q.= ",".$nb_per_page;
		} else {
			if($nb_per_page) $q.= "limit ".$nb_per_page;
		}
		return $q;
	}

	//Retourne le nb de types de produits
	public static function countTypes() {
		$q = "select count(1) from types_produits  ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si un type de produit existe			
	public static function exists($id_produit){
		$id_produit += 0;
		$q = "select count(1) from types_produits where id_produit = '".$id_produit."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
	
	//Vérifie si le libellé d'un type de produit existe déjà			
	public static function existsLibelle($libelle, $id_produit=0){
		$id_produit += 0;
		$q = "select count(1) from types_produits where libelle = '".$libelle."' ";
		if ($id_produit) $q.= "and id_produit != '".$id_produit."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le type de produit est utilisé dans les offres de remises	
	public static function hasOffres_remises($id_produit){
		$id_produit += 0;
		if (!$id_produit) return 0;
		$q = "select count(1) from offres_remises where num_produit = '".$id_produit."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le type de produit est utilisé dans les suggestions	
	public static function hasSuggestions($id_produit){
		$id_produit += 0;
		if (!$id_produit) return 0;
		$q = "select count(1) from suggestions where num_produit = '".$id_produit."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table types_produits
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE types_produits');
		return $opt;
	}
}
?>