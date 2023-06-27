<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frais.class.php,v 1.13 2017-04-20 16:25:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frais{
	
	public $id_frais = 0;					//Identifiant du frais 
	public $libelle = '';
	public $condition_frais = '';
	public $montant = '000000.00';
	public $num_cp_compta = 0;
	public $num_tva_achat = 0;
	
	//Constructeur.	 
	public function __construct($id_frais= 0) {
		$this->id_frais = $id_frais+0;
		if ($this->id_frais) {
			$this->load();	
		}
	}
	
		
	// charge le frais à partir de la base.
	public function load(){
		$q = "select * from frais where id_frais = '".$this->id_frais."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->condition_frais = $obj->condition_frais;
		$this->montant = $obj->montant;
		$this->num_cp_compta = $obj->num_cp_compta;
		$this->num_tva_achat = $obj->num_tva_achat;
	}
	
	// enregistre le frais de tva en base.
	public function save(){
		if($this->libelle == '') die("Erreur de création frais"); 
		if($this->id_frais) {
			
			$q = "update frais set libelle ='".$this->libelle."', condition_frais = '".$this->condition_frais."', ";
			$q.= "montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', ";
			$q.= "num_tva_achat = '".$this->num_tva_achat."', index_libelle = ' ".strip_empty_words($this->libelle)." ' ";
			$q.= "where id_frais = '".$this->id_frais."' ";
			$r = pmb_mysql_query($q);
	
		} else {
		
			$q = "insert into frais set libelle = '".$this->libelle."', condition_frais =  '".$this->condition_frais."', ";
			$q.= "montant = '".$this->montant."', num_cp_compta = '".$this->num_cp_compta."', num_tva_achat = '".$this->num_tva_achat."', index_libelle = ' ".strip_empty_words($this->libelle)." ' ";
			$r = pmb_mysql_query($q);
			$this->id_frais = pmb_mysql_insert_id();
		
		}
	
	}


	//supprime un taux de tva de la base
	public static function delete($id_frais=0) {
		$id_frais += 0;
		if(!$id_frais) return; 	
		$q = "delete from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q);
	}

	//Retourne un Resultset contenant la liste des frais
	public static function listFrais() {
		$q = "select * from frais order by libelle ";
		$r = pmb_mysql_query($q);
		return $r;
	}
	
	//Vérifie si un frais existe			
	public static function exists($id_frais){
		$id_frais += 0;
		$q = "select count(1) from frais where id_frais = '".$id_frais."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libellé d'un frais annexe existe déjà			
	public static function existsLibelle($libelle, $id_frais=0){
		$id_frais += 0;
		$q = "select count(1) from frais where libelle = '".$libelle."' ";
		if ($id_frais) $q.= "and id_frais != '".$id_frais."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}

	//Vérifie si le frais est utilisé dans les fournisseurs	
	public static function hasFournisseurs($id_frais){
		$id_frais += 0;
		if (!$id_frais) return 0;
		$q = "select count(1) from entites where num_frais = '".$id_frais."' and type_entite = '0'";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table taux de tva
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE frais');
		return $opt;
	}
}
?>