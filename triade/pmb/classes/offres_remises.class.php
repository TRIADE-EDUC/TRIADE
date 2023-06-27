<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: offres_remises.class.php,v 1.10 2017-07-10 13:55:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class offres_remises{
	
	public $num_fournisseur = 0;				//Identifiant du fournisseur 
	public $num_produit = 0;					//Identifiant du type de produit
	public $remise = '0.00';					//Remise applicable en %
	public $condition_remise = '';
	 
	//Constructeur.	 
	public function __construct($num_fournisseur=0, $num_produit=0) {
		$this->num_fournisseur = $num_fournisseur+0;
		$this->num_produit = $num_produit+0;
		if ($this->num_fournisseur || $this->num_produit) {
			$this->load();			
		}
	}	

	// charge une offre de remise à partir de la base.
	public function load(){
		$q = "select * from offres_remises where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
		$r = pmb_mysql_query($q);
		$obj = pmb_mysql_fetch_object($r);
		$this->remise = $obj->remise;
		$this->condition_remise = $obj->condition_remise;

	}

	// enregistre une offre de remise en base.
	public function save(){
		if(!$this->num_fournisseur || !$this->num_produit) die("Erreur de création offres_remises");
		
		$q = "select count(1) from offres_remises where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {

			$q = "update offres_remises set remise = '".$this->remise."', condition_remise ='".$this->condition_remise."' ";
			$q.= "where num_fournisseur = '".$this->num_fournisseur."' and num_produit = '".$this->num_produit."' ";
			$r = pmb_mysql_query($q);
			
		} else {

			$q = "insert into offres_remises set num_fournisseur = '".$this->num_fournisseur."', num_produit = '".$this->num_produit."', ";
			$q.= "remise =  '".$this->remise."', condition_remise = '".$this->condition_remise."' ";
			$r = pmb_mysql_query($q);

		}
	}

	//supprime un exercice de la base
	public static function delete($num_fournisseur, $num_produit) {
		$num_fournisseur += 0;
		$num_produit += 0;
		$q = "delete from offres_remises where num_fournisseur = '".$num_fournisseur."' and num_produit = '".$num_produit."' ";
		$r = pmb_mysql_query($q);
				
	}
	
	//optimization de la table offres_remises
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE offres_remises');
		return $opt;
	}
}
?>