<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_categ.class.php,v 1.6 2022/02/11 11:31:01 dgoron Exp $


class suggestions_categ{
	
	public $id_categ = 0;							//Identifiant de categorie de suggestions	
	public $libelle_categ  = '';					//Libelle  de categorie de suggestions
	
	//Constructeur.	 
	public function __construct($id_categ= 0) {
		$this->id_categ = intval($id_categ);
		if ($this->id_categ) {
			$this->load();	
		}
	}	
	
	// charge une categorie de suggestions à partir de la base.
	public function load(){
		$q = "select * from suggestions_categ where id_categ = '".$this->id_categ."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle_categ = $obj->libelle_categ;
	}
	
	// enregistre une categorie de suggestions en base.
	public function save(){
		if( $this->libelle_categ == '' ) die("Erreur de création catégorie de suggestions");
		if ($this->id_categ) {
			$q = "update suggestions_categ set libelle_categ = '".addslashes($this->libelle_categ)."' ";
			$q.= "where id_categ = '".$this->id_categ."' ";
			pmb_mysql_query($q);
		} else {
			$q = "insert into suggestions_categ set libelle_categ = '".addslashes($this->libelle_categ)."' ";
			pmb_mysql_query($q);
			$this->id_categ = pmb_mysql_insert_id();
		}
	}

	//Retourne une liste des categories de suggestions (tableau id->libelle)
	public static function getCategList() {
		$list_categ = array();

		$q = "select * from suggestions_categ order by libelle_categ ";
		$r = pmb_mysql_query($q);
		while ($row = pmb_mysql_fetch_object($r)){
			$list_categ[$row->id_categ] = $row->libelle_categ;
		}
		return $list_categ;
	}

	//Vérifie si une categorie de suggestions existe			
	public static function exists($id_categ) {
		$id_categ = intval($id_categ);
		$q = "select count(1) from suggestions_categ where id_categ = '".$id_categ."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}
		
	//Vérifie si le libelle d'une categorie de suggestions existe déjà en base
	public static function existsLibelle($libelle, $id_categ=0) {
		$id_categ = intval($id_categ);
		$q = "select count(1) from suggestions_categ where libelle_categ = '".$libelle."' ";
		if($id_categ) $q.= "and id_categ != '".$id_categ."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//supprime une categorie de suggestions de la base
	public function delete($id_categ= 0) {
		$id_categ = intval($id_categ);
		if(!$id_categ) $id_categ = $this->id_categ; 	

		$q = "delete from suggestions_categ where id_categ = '".$id_categ."' ";
		pmb_mysql_query($q);
	}

	//Vérifie si la categorie de suggestions est utilisee dans les suggestions	
	public function hasSuggestions($id_categ){
		$id_categ = intval($id_categ);
		if (!$id_categ) $id_categ = $this->id_categ;
		$q = "select count(1) from suggestions where num_categ = '".$id_categ."' ";
		$r = pmb_mysql_query($q); 
		return pmb_mysql_result($r, 0, 0);
	}

	//optimization de la table suggestions_categ
	public function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE suggestions_categ');
		return $opt;
	}
				
}?>