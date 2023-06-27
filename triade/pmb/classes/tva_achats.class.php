<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tva_achats.class.php,v 1.12 2018-12-20 11:00:19 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class tva_achats{
	
	public $id_tva = 0;					//Identifiant de tva_achats 
	public $libelle = '';					//Libelle sur la tva
	public $taux_tva = '0.00';				//taux de tva en %					
	public $num_cp_compta = 0;
	 
	//Constructeur.	 
	public function __construct($id_tva= 0) {
		$this->id_tva = intval($id_tva);
		if ($this->id_tva) {
			$this->load();	
		}
	}
		
	// charge le taux de tva à partir de la base.
	public function load(){
		$q = "select * from tva_achats where id_tva = '".$this->id_tva."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle = $obj->libelle;
		$this->taux_tva = $obj->taux_tva;
		$this->num_cp_compta = $obj->num_cp_compta;
	}
	
	// enregistre le taux de tva en base.
	public function save(){
		if(!$this->libelle) die("Erreur de création tva_achats");
		if($this->id_tva) {
			$q = "update tva_achats set taux_tva ='".$this->taux_tva."', libelle = '".$this->libelle."', num_cp_compta = '".$this->num_cp_compta."' ";
			$q.= "where id_tva = '".$this->id_tva."' ";
			$r = pmb_mysql_query($q);
		} else {
			$q = "insert into tva_achats set libelle = '".$this->libelle."', taux_tva = '".$this->taux_tva."', num_cp_compta = '".$this->num_cp_compta."' ";
			$r = pmb_mysql_query($q);
			$this->id_tva = pmb_mysql_insert_id();
		}
	}

	//supprime un taux de tva de la base
	public static function delete($id_tva= 0) {
		$id_tva += 0;
		if(!$id_tva) return; 	
		$q = "delete from tva_achats where id_tva = '".$id_tva."' ";
		$r = pmb_mysql_query($q);
	}

	//Retourne une requete contenant la liste des taux de tva achats
	public static function listTva() {
		$q = "select * from tva_achats order by libelle ";
		return $q;
	}

	//Compte les taux de tva achats
	public static function countTva() {
		$q = "select count(1) from tva_achats  ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si un taux de tva achats existe			
	public static function exists($id_tva){
		$id_tva += 0;
		$q = "select count(1) from tva_achats where id_tva = '".$id_tva."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le libellé d'un taux de tva achats existe déjà			
	public static function existsLibelle($libelle, $id_tva=0){
		$id_tva += 0;
		$q = "select count(1) from tva_achats where libelle = '".$libelle."' ";
		if ($id_tva) $q.= "and id_tva != '".$id_tva."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le taux de tva achats est utilisé dans les types de produits			
	public static function hasTypesProduits($id_tva= 0){
		$id_tva += 0;
		if (!$id_tva) return 0;
		$q = "select count(1) from types_produits where num_tva_achat = '".$id_tva."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//Vérifie si le taux de tva achats est utilisé dans les frais		
	public static function hasFrais($id_tva= 0){
		$id_tva += 0;
		if (!$id_tva) return 0;
		$q = "select count(1) from frais where num_tva_achat = '".$id_tva."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
		
	}
	
	//optimization de la table taux de tva
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE tva_achats');
		return $opt;
	}
				
}
?>