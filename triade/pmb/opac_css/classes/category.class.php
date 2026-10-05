<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.class.php,v 1.35.4.1 2025/01/30 09:08:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'
if ( ! defined( 'CATEGORY_CLASS' ) ) {
  define( 'CATEGORY_CLASS', 1 );
  
global $class_path;
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/acces.class.php");

class category {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $id=0;
	public $libelle='';
	public $commentaire='';
	public $catalog_form=''; // forme pour affichage complet
	public $parent_id=0;
	public $parent_libelle = '';
	public $voir_id=0;
	public $has_child=FALSE;
	public $has_parent=FALSE;
	public $path_table=array();	// tableau contenant le path éclaté (ids et libellés)
	public $associated_terms=array(); // tableau des termes associés
	public $is_under_tilde=0; // Savoir si c'est sous une catégorie qui commence par un ~
	public $thes;		//le thesaurus d'appartenance
	public $libelle_aff_complet = "";
	public $commentaire_public = "";
	public $not_use_in_indexation=0; //Savoir si l'on peut utiliser le terme en indexation
	public $list_see = array();
	protected $listchilds;
	
	// ---------------------------------------------------------------
	//		category($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->is_under_tilde=0;
		if($this->id) {
			$this->thes = thesaurus::getByEltId($this->id);
		}
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération des propriétés
	// ---------------------------------------------------------------
	public function getData() {
		global $lang;
		global $opac_categories_show_only_last ; // le paramètre pour afficher le chemin complet ou pas
		
		$anti_recurse=array();	
		if(!$this->id) return;
		$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, not_use_in_indexation, 	note_application as categ_comment, comment_public,	if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
			FROM noeuds, categories where id_noeud ='".$this->id."' 
			AND noeuds.id_noeud = categories.num_noeud 
			order by p desc limit 1";
	
		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) return;
		
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->categ_id;
		$this->libelle = $data->categ_libelle;
		if(preg_match("#^~#",$this->libelle)){
			$this->is_under_tilde=1;
		}
		$this->commentaire = $data->categ_comment;
		$this->parent_id = $data->categ_parent;
		$this->voir_id = $data->categ_see;
		$this->not_use_in_indexation = $data->not_use_in_indexation;
		$this->commentaire_public = $data->comment_public;
		//$anti_recurse[$this->voir_id]=1;
		if($this->parent_id ) $this->has_parent = TRUE;
	
		$requete = "SELECT id_noeud as categ_id FROM noeuds WHERE num_parent='".$this->id."' ";
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) $this->has_child = TRUE;
	
		// constitution du chemin
		$anti_recurse[$this->id]=1;
		$this->path_table=array();
		if ($this->has_parent) {
			$id_parent=$this->parent_id;
			do {
				$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$id_parent."' 
				AND noeuds.id_noeud = categories.num_noeud 
				order by p desc limit 1";
				$result=@pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$parent = pmb_mysql_fetch_object($result);
					if(preg_match("#^~#",$parent->categ_libelle)){
						$this->is_under_tilde=1;
					}
					$anti_recurse[$parent->categ_id]=1;
					$this->path_table[] = array(
								'id' => $parent->categ_id,
								'libelle' => $parent->categ_libelle,
								'commentaire' => $parent->categ_comment);
					$id_parent=$parent->categ_parent;
				} else {
					break;
				}
				if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
			} while (($parent->categ_parent) &&(!$anti_recurse[$parent->categ_parent]));
		}
		
		// ceci remet le tableau dans l'ordre général->particulier	
		$this->path_table = array_reverse($this->path_table);
	
		if ($opac_categories_show_only_last) {
			$this->catalog_form = $this->libelle;
			// si notre catégorie a un parent, on initie la boucle en le récupérant
			if(!empty($parent->parent_id)) {
				$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
					FROM noeuds, categories where id_noeud ='".$parent->parent_id."' 
					AND noeuds.id_noeud = categories.num_noeud 
					order by p desc limit 1";
				
				$result_temp=@pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result_temp)) {
					$parent = pmb_mysql_fetch_object($result_temp);
					$this->parent_libelle = $parent->categ_libelle ;
				}
			}
	
		} else {
			if(is_countable($this->path_table) && sizeof($this->path_table)) {
				$temp_table = array();
			    foreach ($this->path_table as $i => $l) {
						$temp_table[] = $l['libelle'];
				}
				$this->parent_libelle = join(':', $temp_table);
				$this->catalog_form = $this->parent_libelle.':'.$this->libelle;
			} else {
				$this->catalog_form = $this->libelle;
			}
		}
		// pour libellé complet mais sans le nom du thésaurus 
		$this->libelle_aff_complet = $this->catalog_form ;
	
		global $opac_thesaurus;
		if ($opac_thesaurus) $this->catalog_form="[".$this->thes->libelle_thesaurus."] ".$this->catalog_form;
		/* Ne sert plus??
		//Recherche des termes associés
		$requete = "select distinct voir_aussi.num_noeud_dest as categ_assoc_categassoc, id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,num_renvoi_voir as categ_see, note_application as categ_comment, if(categories.langue = '".$lang."',2, if(categories.langue= '".$this->thes->langue_defaut."' ,1,0)) as p
			FROM noeuds, categories, voir_aussi where id_noeud ='".$this->id."' 
			AND noeuds.id_noeud = categories.num_noeud 
			AND categories.num_noeud=voir_aussi.num_noeud_dest 
			AND voir_aussi.num_noeud_orig=id_noeud
			order by p desc limit 1";	
	
		$result=pmb_mysql_query($requete);
		while ($ta=pmb_mysql_fetch_object($result)) {
			print $requete;
			$this->associated_terms[] = array(
							'id' => $ta->categ_assoc_categassoc,
							'libelle' => $ta->categ_libelle,
							'commentaire' => $ta->categ_comment);
		}
		*/
	}
	
	public static function has_notices($id=0) {
		global $gestion_acces_active, $gestion_acces_empr_notice;
		
		//droits d'acces emprunteur/notice
		$acces_j='';
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
		}
			
		if($acces_j) {
			$statut_j='';
			$statut_r='';
		} else {
			$statut_j=',notice_statut';
			$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
		}
		
		$query = "select count(1) from notices_categories,notices $acces_j $statut_j ";
		$query.= "where (notices_categories.num_noeud='".$id."' and notices_categories.notcateg_notice=notice_id) $statut_r ";
		$result = pmb_mysql_query($query);
		return (pmb_mysql_result($result, 0, 0));
	
	}
	
	public function listChilds() {
		global $lang;
		if(!isset($this->listchilds)){
			
			if ($this->id == $this->thes->num_noeud_racine){
				$keep_tilde = 0;
			}else{
				$keep_tilde = 1;
			}
			
			$q = "select ";
			$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
			$q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
			$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
			$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
			$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
			$q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
			$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
			$q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$q.= "where ";
			$q.= "noeuds.num_parent = '".$this->id."' ";
			if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
			$q.= "order by libelle_categorie ";
			// Possibilité d'ajouter une limitation ici (voir nouveau paramètre gestion)
			$q.="";
			
			$r = pmb_mysql_query($q);
			while($child=pmb_mysql_fetch_object($r)) {
				$authority = new authority(0, $child->num_noeud, AUT_TABLE_CATEG);
				$this->listchilds[]= array(
						'id' => $child->num_noeud,
						'name' => $child->comment_public,
						'libelle' => $child->libelle_categorie,
						'num_authority' => $authority->get_id()
				);
			}
			
		}
		return $this->listchilds;
	}
	
	/**
	 * Permet de récupérer les catégories dont le num_renvoi correspond à l'id du noeud courant
	 */
	public function listSynonyms(){
	    if (isset($this->list_see)) {
	        return $this->list_see;
	    }
	    global $lang;
	    
	    $this->list_see = array();
	    $thes = thesaurus::getByEltId($this->id);
	    $q = "select id_noeud from noeuds where num_thesaurus = '".$thes->id_thesaurus."' and autorite = 'ORPHELINS' ";
	    
	    $r = pmb_mysql_query($q);
	    if($r && pmb_mysql_num_rows($r)){
	        $num_noeud_orphelins = pmb_mysql_result($r, 0, 0);
	    }else{
	        $num_noeud_orphelins=0;
	    }
	    $q = "select ";
	    $q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
	    $q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
	    $q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
	    $q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
	    $q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
	    $q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
	    $q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
	    $q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
	    $q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
	    $q.= "where ";
	    $q.= "noeuds.num_parent = '$num_noeud_orphelins' and noeuds.num_renvoi_voir='".$this->id."' ";
	    //if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
	    //if ($ordered !== 0) $q.= "order by ".$ordered." ";
	    $q.=""; // A voir pour ajouter un parametre gestion maxddisplay
	    $r = pmb_mysql_query($q);
	    
	    while($cat_see=pmb_mysql_fetch_object($r)) {
	        $this->list_see[]= array(
	            'id' => $cat_see->num_noeud,
	            'name' => $cat_see->comment_public,
	            'parend_id' => $cat_see ->num_parent,
	            'libelle' => $cat_see->libelle_categorie
	        );
	    }
	    return $this->list_see;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		$renvoi_datas = array();
		if(!$antiloop) {
			if($this->parent_id) {
				$parent = new category($this->parent_id);
				$parent_datas = $parent->format_datas(true);
			}
			if($this->voir_id) {
				$renvoi = new category($this->voir_id);
				$renvoi_datas = $renvoi->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->libelle,
				'comment' => $this->commentaire,
				'parent' => $parent_datas,
				'renvoi' => $renvoi_datas,
// 				'renvoi_voir_aussi' =>
		);
		//$authority = new authority(0, $this->id, AUT_TABLE_CATEG);
		$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_CATEG]);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_isbd() {
	    return $this->libelle;
	}
} # fin de définition de la classe category

} # fin de déclaration
