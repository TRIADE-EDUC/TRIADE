<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: noeuds.class.php,v 1.18 2023/02/16 08:57:16 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/thesaurus.class.php");

class noeuds{
	
	
	public $id_noeud = 0;				//Identifiant du noeud 
	public $autorite = '';
	public $num_parent = 0;
	public $num_renvoi_voir = 0;
	public $visible = '1';
	public $num_thesaurus = 0;				//Identifiant du thesaurus de rattachement

	 
	//Constructeur.	 
	public function __construct($id=0) {
		$this->id_noeud = intval($id);
		if ($this->id_noeud) {
			$this->load();	
		}
	}

	// charge le noeud à partir de la base.
	public function load(){
		$q = "select * from noeuds where id_noeud = '".$this->id_noeud."' ";
		$r = pmb_mysql_query($q) ;
		$obj = pmb_mysql_fetch_object($r);
		$this->id_noeud = $obj->id_noeud;
		$this->autorite = $obj->autorite;
		$this->num_parent = $obj->num_parent;
		$this->num_renvoi_voir = $obj->num_renvoi_voir;
		$this->visible = $obj->visible;
		$this->num_thesaurus = $obj->num_thesaurus;
		$this->path = $obj->path;
	}

	
	// enregistre le noeud en base.
	public function save(){
		if (!$this->num_thesaurus) die ('Erreur de création noeud');
		
		if ($this->id_noeud) {	//Mise à jour noeud
			
			$q = 'update noeuds set autorite =\''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\' ';
			$q.= 'where id_noeud = \''.$this->id_noeud.'\' ';
			pmb_mysql_query($q);

		} else {
			
			$q = 'insert into noeuds set autorite = \''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\' ';
			pmb_mysql_query($q);
			$this->id_noeud = pmb_mysql_insert_id();
		}

		// Mis à jour du path de lui-meme, et de tous les fils
		$thes = thesaurus::getByEltId($this->id_noeud);

		$id_top = $thes->num_noeud_racine;
		$path='';		
		$id_tmp=$this->id_noeud;
		while (true) {
			$q = "select num_parent from noeuds where id_noeud = '".$id_tmp."' limit 1";
			$r = pmb_mysql_query($q);
			$id_tmp= $id_cur = pmb_mysql_result($r, 0, 0);
			print $id_tmp." ";
			if (!$id_cur || $id_cur == $id_top) break;
			if($path) $path='/'.$path;
			$path=$id_tmp.$path;			
		}
		noeuds::process_categ_path($this->id_noeud,$path);
	}
	
	public static function process_categ_path($id_noeud=0, $path='') {
		$id_noeud = intval($id_noeud);
		if(!$id_noeud) return;
		
		if($path) $path.='/';
		$path.=$id_noeud;
		
		$res = noeuds::listChilds($id_noeud, 0);
		while (($row = pmb_mysql_fetch_object($res))) {
			// la categorie a des filles qu'on va traiter
			noeuds::process_categ_path ($row->id_noeud,$path);
		}		
		$req="update noeuds set path='$path' where id_noeud='$id_noeud' ";
		pmb_mysql_query($req);		
	}
		

	//fonctions !!!

	//supprime un noeud et toutes ses références
	public function delete($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud; 	

		// Supprime les categories.
		$q = "delete from categories where num_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q);
		
		//Import d'autorité
		noeuds::delete_autority_sources($id_noeud);
		
		// Supprime les renvois voir_aussi vers ce noeud. 
		$q= "delete from voir_aussi where num_noeud_dest = '".$id_noeud."' ";
		pmb_mysql_query($q);
		
		// Supprime les renvois voir_aussi depuis ce noeud. 
		$q= "delete from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		pmb_mysql_query($q);
		
		// Supprime les associations avec des notices. 
		$q= "delete from notices_categories where num_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q);

		//Supprime les emprises du noeud
		$req = "select map_emprise_id from map_emprises where map_emprise_type=2 and map_emprise_obj_num=".$id_noeud;
		$result = pmb_mysql_query($req);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$q= "delete from map_emprises where map_emprise_obj_num ='".$id_noeud."' and map_emprise_type = 2";
			pmb_mysql_query($q);
			$req_areas="delete from map_hold_areas where type_obj=2 and id_obj=".$row->map_emprise_id;
			pmb_mysql_query($req_areas);
		}
		
		//suppression des renvois voir restants
		$q = "update noeuds set num_renvoi_voir = '0' where num_renvoi_voir = '".$id_noeud."' ";
		pmb_mysql_query($q);
		
			//suppression des associations avec le contenu éditorial
			$q = "delete from cms_articles_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);
			$q = "delete from cms_sections_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);
			
			//suppression des associations avec les items de veilles
			$q = "delete from docwatch_items_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);
			
			//suppression des associations avec les bannettes
			$q = "delete from bannettes_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);
			
			//suppression des associations avec les campagnes de mails
			$q = "delete from campaigns_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);
			
		// Supprime le noeud.
		$q = "delete from noeuds where id_noeud = '".$id_noeud."' ";
		pmb_mysql_query($q);
				
	}

	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	public static function delete_autority_sources($idnoeud=0){
		$tabl_id=array();
		$idnoeud = intval($idnoeud);
		if(!$idnoeud){
			$requete="SELECT DISTINCT num_authority FROM authorities_sources LEFT JOIN noeuds ON num_authority=id_noeud  WHERE authority_type = 'category' AND id_noeud IS NULL";
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)) {
					$tabl_id[]=$ligne->num_authority;
				}
			}
		}else{
			$tabl_id[]=$idnoeud;
		}
		foreach ( $tabl_id as $value ) {
			//suppression dans la table de stockage des numéros d'autorités...
			$query = "select id_authority_source from authorities_sources where num_authority = ".$value." and authority_type = 'category'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while ($ligne = pmb_mysql_fetch_object($result)) {
					$query = "delete from notices_authorities_sources where num_authority_source = ".$ligne->id_authority_source;
					pmb_mysql_query($query);
				}
			}
			$query = "delete from authorities_sources where num_authority = ".$value." and authority_type = 'category'";
			pmb_mysql_query($query);
		}
	}

	// recherche si une autorite existe deja dans un thesaurus, 
	// et retourne le noeud associe
	public function searchAutorite($num_thesaurus, $autorite) {
		$num_thesaurus = intval($num_thesaurus);
		$q = "select id_noeud from noeuds where num_thesaurus = '".$num_thesaurus."' ";
		$q.= "and autorite = '".addslashes($autorite)."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r) == 0) return FALSE;
		$noeud = new noeuds(pmb_mysql_result($r, 0, 0));
		return $noeud;
	}
	
	
	//recherche si un noeud a des fils
	public static function hasChild($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		if($id_noeud){
			$q = "select count(1) from noeuds where num_parent = '".$id_noeud."' ";
			$r = pmb_mysql_query($q);
			return pmb_mysql_result($r, 0, 0);
		}
		return 0;
	}

		
	//recherche si un noeud est le renvoi voir d'un autre noeud.
	public static function isTarget($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		if($id_noeud){
			$q = "select count(1) from noeuds where num_renvoi_voir = '".$id_noeud."' ";
			$r = pmb_mysql_query($q);
			return pmb_mysql_result($r, 0, 0);
		}
		return 0;
	}


	//Indique si un noeud est protégé (noeuds ORPHELINS et NONCLASSES).
	public static function isProtected($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		$q = "select autorite from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		$a = pmb_mysql_result($r, 0, 0);
		if( $a == 'ORPHELINS' || $a == 'NONCLASSES') return TRUE;
		else return FALSE;
	}		


	//Liste les ancetres d'un noeud et les retourne sous forme d'un tableau 
	public static function listAncestors($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		$q = "select path from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		if($r && pmb_mysql_num_rows($r)){
			$path=pmb_mysql_result($r, 0, 0);
		}
		if (!empty($path)){ 
			$id_list=explode('/',$path);
			krsort($id_list);
			return $id_list;		
		}		
		
		//si le chemin est vide, on le construit
		$thes = thesaurus::getByEltId($id_noeud);
		$id_top = !empty($thes) ? $thes->num_noeud_racine : null;

		$id_list[] = $id_noeud;
		$current_id = $id_noeud;
		while (true) {
		    $q = "select num_parent from noeuds where id_noeud = $current_id limit 1";
			$r = pmb_mysql_query($q);
		    $current_id = pmb_mysql_result($r, 0, 0);
		    if ( !$current_id || $current_id == $id_top || in_array($current_id, $id_list) ) {
		        break;
		    }
		    $id_list[] = $current_id;
		}
		return $id_list;		
	}
		
}
