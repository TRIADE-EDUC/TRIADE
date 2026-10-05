<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: noeuds.class.php,v 1.75.4.1 2025/03/04 15:50:01 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkEntityPmb;

global $class_path, $include_path;
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/category.class.php");
require_once($include_path."/templates/category.tpl.php");
require_once("$include_path/user_error.inc.php");
require_once("$include_path/misc.inc.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/authority.class.php");

class noeuds{


	public $id_noeud = 0;				//Identifiant du noeud
	public $autorite = '';
	public $num_parent = 0;
	public $num_renvoi_voir = 0;
	public $visible = '1';
	public $num_thesaurus = 0;			//Identifiant du thesaurus de rattachement
	public $authority_import_denied = 0;			//Interdit l'import de l'autorité
	public $not_use_in_indexation = 0; // Interdir l'utilisation de la catégorie en indexation de notice
	public $num_statut = 1;
	public $thumbnail_url = '';
	public $path = '';
	protected static $controller;

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
		$this->not_use_in_indexation = $obj->not_use_in_indexation;
		$this->authority_import_denied = $obj->authority_import_denied;
		$authority = new authority(0, $this->id_noeud, AUT_TABLE_CATEG);
		$this->num_statut = $authority->get_num_statut();
		$this->thumbnail_url = $authority->get_thumbnail_url();
	}


	// enregistre le noeud en base.
	public function save(){
		if (!$this->num_thesaurus) die ('Erreur de création noeud');

		if ($this->id_noeud) {	//Mise à jour noeud

			$q = 'update noeuds set autorite =\''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\', ';
			$q.= 'authority_import_denied = \''.$this->authority_import_denied.'\', not_use_in_indexation = \''.$this->not_use_in_indexation.'\' ';
			$q.= 'where id_noeud = \''.$this->id_noeud.'\' ';
			pmb_mysql_query($q);
			audit::insert_modif (AUDIT_CATEG, $this->id_noeud) ;

		} else {

			$q = 'insert into noeuds set autorite = \''.addslashes($this->autorite).'\', ';
			$q.= 'num_parent = \''.$this->num_parent.'\', num_renvoi_voir = \''.$this->num_renvoi_voir.'\', ';
			$q.= 'visible = \''.$this->visible.'\', num_thesaurus = \''.$this->num_thesaurus.'\', ';
			$q.= 'authority_import_denied = \''.$this->authority_import_denied.'\', not_use_in_indexation = \''.$this->not_use_in_indexation.'\' ';
			pmb_mysql_query($q);
			$this->id_noeud = pmb_mysql_insert_id();
			audit::insert_creation (AUDIT_CATEG, $this->id_noeud) ;
		}

		//update authority informations
		if($this->autorite != "TOP"){//Le neoud racine du thésaurus n'a pas de libellé, il n'est donc pas considéré comme une auritée
			$authority = new authority(0, $this->id_noeud, AUT_TABLE_CATEG);
			$authority->set_num_statut($this->num_statut);
			$authority->set_thumbnail_url($this->thumbnail_url);
			$authority->update();
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
		$req="update noeuds set path='$path' where id_noeud=$id_noeud";
		pmb_mysql_query($req);
	}

	public static function process_categ($id_noeud) {
		global $deleted;

		$res = noeuds::listChilds($id_noeud, 0);
		$total = pmb_mysql_num_rows($res);
		if ($total) {
			while ($row = pmb_mysql_fetch_object($res)) {
				// la categorie a des filles qu'on va traiter
				noeuds::process_categ ($row->id_noeud);
			}

			// après ménage de ses filles, reste-t-il des filles ?
			$total_filles = noeuds::hasChild($id_noeud);

			// categ utilisée en renvoi voir ?
			$total_see = noeuds::isTarget($id_noeud);

			// est-elle utilisée ?
			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);

			// est-elle est utilisée dans des champs perso ?
			$use_pperso = aut_pperso::delete_pperso(AUT_TABLE_CATEG, $id_noeud,0);

			if(!$iuse && !$total_filles && !$total_see && !$use_pperso) {
				$deleted++ ;
				noeuds::delete($id_noeud);
			}

		} else { // la catégorie n'a pas de fille on va la supprimer si possible
			// regarder si categ utilisée
			$iuse = noeuds::isUsedInNotices($id_noeud) + noeuds::isUsedinSeeALso($id_noeud);
			// est-elle est utilisée dans des champs perso ?
			$use_pperso = aut_pperso::delete_pperso(AUT_TABLE_CATEG, $id_noeud,0);
			if(!$iuse && !$use_pperso) {
				$deleted++ ;
				noeuds::delete($id_noeud);
			}
		}

	}

	//fonctions !!!

	//supprime un noeud et toutes ses références
	public static function delete($id_noeud=0, $indexation_active = true) {
		$id_noeud = intval($id_noeud);
		if($id_noeud) {
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
			$articles_to_index = array();
			if ($indexation_active) {
                $query = "SELECT num_article FROM cms_articles_descriptors WHERE num_noeud = '".$id_noeud."' ";
    			$result = pmb_mysql_query($query);
    			if (pmb_mysql_num_rows($result)) {
    			    while ($row = pmb_mysql_fetch_object($result)) {
    			        $articles_to_index[] = intval($row->num_article);
    			    }
    			}
			}
			$q = "delete from cms_articles_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);

			$sections_to_index = array();
			if ($indexation_active) {
    			$query = "SELECT num_section FROM cms_sections_descriptors WHERE num_noeud = '".$id_noeud."' ";
    			$result = pmb_mysql_query($query);
    			if (pmb_mysql_num_rows($result)) {
    			    while ($row = pmb_mysql_fetch_object($result)) {
    			        $sections_to_index[] = intval($row->num_section);
    			    }
    			}
			}
			$q = "delete from cms_sections_descriptors where num_noeud = '".$id_noeud."' ";
			pmb_mysql_query($q);

			if ($indexation_active) {
    			$index = count($articles_to_index);
    			for ($i = 0; $i < $index; $i++) {
    			    $cms_article = new cms_article($articles_to_index[$i]);
    			    $cms_article->maj_indexation("categories");
    			}

    			$index = count($sections_to_index);
    			for ($i = 0; $i < $index; $i++) {
    			    $cms_section = new cms_section($sections_to_index[$i]);
    			    $cms_section->maj_indexation("categories");
    			}
			}

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

			audit::delete_audit(AUDIT_CATEG,$id_noeud);

			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_CATEG,$id_noeud);
			$aut_link->delete($indexation_active);

			$aut_pperso= new aut_pperso("categ",$id_noeud);
			$aut_pperso->delete();

			// nettoyage indexation
			indexation_authority::delete_all_index($id_noeud, "authorities", "id_authority", AUT_TABLE_CATEG);

			// effacement de l'identifiant unique d'autorité
			$authority = new authority(0, $id_noeud, AUT_TABLE_CATEG);
			$authority->delete();
		}
	}

	// ---------------------------------------------------------------
	//		delete_autority_sources($idcol=0) : Suppression des informations d'import d'autorité
	// ---------------------------------------------------------------
	public static function delete_autority_sources($idnoeud=0){
		$tabl_id=array();
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


	//Indique si un noeud est protégé (TOP, ORPHELINS et NONCLASSES).
	public static function isProtected($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		$q = "select autorite from noeuds where id_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		$a = pmb_mysql_result($r, 0, 0);
		if( $a=='TOP' || $a=='ORPHELINS' || $a=='NONCLASSES') return TRUE;
			else return FALSE;
	}


	//Indique si un noeud est racine (non modifiable).
	public static function isRacine($id_noeud=0) {
		if (!$id_noeud) return FALSE;
		$q = "select * from thesaurus where num_noeud_racine = '".$id_noeud."' limit 1 ";
		$r = pmb_mysql_query($q);
		if( pmb_mysql_num_rows($r)) return TRUE;
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


	//Liste les enfants d'un noeud sous forme de resultset (si $renvoi=0, ne retourne pas les noeuds renvoyés)
	public static function listChilds($id_noeud=0, $renvoi=0) {
		$id_noeud = intval($id_noeud);
		$q = "select id_noeud from noeuds where num_parent = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		if (!$renvoi) $q.= "and num_renvoi_voir = '0' ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Liste les noeuds qui ont un renvoi voir d'un autre noeud sous forme de resultset
	public static function listTargets($id_noeud=0) {
		$id_noeud = intval($id_noeud);
		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//Liste les noeuds termes orphelins qui ont un renvoi voir d'un autre noeud sous forme de tableau
	public static function listTargetsOrphansOnly($id_noeud=0) {
		$id_list = array();
		$id_noeud = intval($id_noeud);

		$thes = thesaurus::getByEltId($id_noeud);

		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$id_list_ancestors = noeuds::listAncestors($row->id_noeud);
				if (count($id_list_ancestors)) {
					if (in_array($thes->num_noeud_orphelins,$id_list_ancestors)) {
						$id_list[] = $row->id_noeud;
					}
				}
			}
		}
		return $id_list;
	}

	//Liste les noeuds sauf termes orphelins qui ont un renvoi voir d'un autre noeud sous forme de tableau
	public static function listTargetsExceptOrphans($id_noeud=0) {
		$id_list = array();
		$id_noeud = intval($id_noeud);

		$thes = thesaurus::getByEltId($id_noeud);

		$q = "select id_noeud from noeuds where num_renvoi_voir = '".$id_noeud."' ";
		$q.= "and autorite not in ('ORPHELINS', 'NONCLASSES') ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			while ($row = pmb_mysql_fetch_object($r)) {
				$id_list_ancestors = noeuds::listAncestors($row->id_noeud);
				if (count($id_list_ancestors)) {
					if (!in_array($thes->num_noeud_orphelins,$id_list_ancestors)) {
						$id_list[] = $row->id_noeud;
					}
				} else {
					$id_list[] = $row->id_noeud;
				}
			}
		}
		return $id_list;
	}

	//recherche si un noeud est utilisé dans une notice.
	public static function isUsedInNotices($id_noeud=0) {
		if(!$id_noeud) return 0;
		$q = "select count(1) from notices_categories where num_noeud = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}


	//recherche si un noeud est utilisé dans la table voir_aussi.
	public static function isUsedInSeeAlso($id_noeud=0) {
		if(!$id_noeud) return 0;
		$q = "select count(1) from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		$q.= "or num_noeud_dest = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		return pmb_mysql_result($r, 0, 0);
	}

	//Liste les noeuds de la table voir_aussi sous forme de resultset
	public function listUsedInSeeAlso($id_noeud=0) {
		if(!$id_noeud && (is_object($this))) $id_noeud = $this->id_noeud;
		$q = "select distinct if(num_noeud_orig!= ".$id_noeud.",num_noeud_orig,num_noeud_dest)as id_noeud from voir_aussi where num_noeud_orig = '".$id_noeud."' ";
		$q.= "or num_noeud_dest = '".$id_noeud."' ";
		$r = pmb_mysql_query($q);
		return $r;
	}

	//optimization de la table noeuds
	public static function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE noeuds');
		return $opt;
	}

	//vérification de l'unicité du numéro d'autorité dans le thésaurus
	public static function isUnique($num_thesaurus, $num_aut='', $id_noeud=0) {
		if ($num_aut=='') return true;
		$q = 'select count(1) from noeuds where num_thesaurus=\''.$num_thesaurus.'\' ';
		$q.= 'and autorite=\''.addslashes($num_aut).'\' ';
		if ($id_noeud) $q.= 'and id_noeud != \''.$id_noeud.'\' ';
		$r = pmb_mysql_query($q);
		if(pmb_mysql_result($r, 0, 0)==0) return true;
			else return false;
	}

	// ---------------------------------------------------------------
	//		replace_categ_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_categ_form($parent=0) {
		global $categ_replace_content_form;
		global $thesaurus_mode_pmb;
		global $msg;

		if(!$this->id_noeud) {
			error_message($msg[161], $msg[162], 1, static::format_url("&sub=&parent=".$parent."&id=0"));//Voir éventuelement pour mettre un message valable quand le cas se présentera
			return false;
		}

		$categ = new category($this->id_noeud);
		if ($thesaurus_mode_pmb) $nom_thesaurus='['.$categ->thes->getLibelle().'] ' ;
		else $nom_thesaurus='' ;

		$content_form = $categ_replace_content_form;
		$content_form = str_replace('!!id!!', $this->id_noeud, $content_form);

		$interface_form = new interface_autorites_replace_form('categ_replace');
		$interface_form->set_object_id($this->id_noeud)
		->set_num_parent($this->num_parent)
		->set_label($msg["159"]." ".$nom_thesaurus.$categ->catalog_form)
		->set_content_form($content_form)
		->set_table_name('noeuds')
		->set_field_focus('by_libelle')
		->set_url_base(static::format_url());
		print $interface_form->get_display();
		return true;
	}

	// ---------------------------------------------------------------
	//		replace : Remplacement d'un noeud du thésaurus par un autre
	// ---------------------------------------------------------------
	public function replace($by=0, $link_save=0, $indexation_active = true) {
		global $msg;
		global $pmb_synchro_rdf;
		global $faq_active;
		global $pmb_ark_activate;

		if (($this->id_noeud == $by) || (!$this->id_noeud) || (!$by))  {
			return $msg["categ_imposible_remplace_elle_meme"];
		}

		$aut_link= new aut_link(AUT_TABLE_CATEG,$this->id_noeud);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_CATEG,$by);
		}

		$authorities_to_index = array();
		$aut_list = $aut_link->get_aut_list();
		if (!empty($aut_list)) {
    		$index = count($aut_list);
    		for ($i = 0; $i < $index; $i++) {
    		    if (!empty($aut_list[$i]["to"]) && !empty($aut_list[$i]["to_num"])) {
        		    $authorities_to_index[] = array(
        		        "type" => $aut_list[$i]["to"],
        		        "num" => intval($aut_list[$i]["to_num"])
        		    );
    		    }
    		}
		}
		$aut_link->delete();

		vedette_composee::replace(TYPE_CATEGORY, $this->id_noeud, $by);

		//synchro_rdf : on empile les noeuds impactés pour les traiter plus loin
		if($pmb_synchro_rdf){
			$arrayIdImpactes=array();
			$arrayThesImpactes=array();
			$thes = thesaurus::getByEltId($this->id_noeud);
			$arrayThesImpactes[]=$thes->id_thesaurus;
			//parent
			if($this->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$this->num_parent;
			}
			//enfants
			$res=noeuds::listChilds($this->id_noeud,1);
			if(pmb_mysql_num_rows($res)){
				while($row=pmb_mysql_fetch_array($res)){
					$arrayIdImpactes[]=$row[0];
				}
			}
			//renvoi_voir
			if($this->num_renvoi_voir){
				$arrayIdImpactes[]=$this->num_renvoi_voir;
			}
		}

		$noeuds_a_garder = new noeuds($by);

		//Si les noeuds sont du même thésaurus
		if($noeuds_a_garder->num_thesaurus == $this->num_thesaurus){
			//On déplace les catégories qui renvoi vers l'ancien noeuds pour qu'elle renvoie vers le nouveau
			if(noeuds::isTarget($this->id_noeud)){
				$requete="UPDATE noeuds SET num_renvoi_voir='".$by."' WHERE num_renvoi_voir='".$this->id_noeud."' and id_noeud!='".$by."' ";
				@pmb_mysql_query($requete);
			}
			//On garde les liens voir_aussi
			$requete="UPDATE ignore voir_aussi SET num_noeud_orig='".$by."' WHERE num_noeud_orig='".$this->id_noeud."' and num_noeud_dest!='".$by."' ";
			@pmb_mysql_query($requete);
			$requete="UPDATE ignore voir_aussi SET num_noeud_dest='".$by."' WHERE num_noeud_dest='".$this->id_noeud."' and num_noeud_orig!='".$by."'";
			@pmb_mysql_query($requete);
		}

		if(noeuds::isTarget($this->id_noeud)){//Si le noeuds à supprimé est utilisé pour des renvois et qu'il reste des liens on les supprime
			//On supprime les renvoies
			$requete="UPDATE noeuds SET num_renvoi_voir='0' WHERE num_renvoi_voir='".$this->id_noeud."'";
			@pmb_mysql_query($requete);
		}

		$notices_to_index = array();
		$query = "select notcateg_notice from notices_categories where num_noeud = '".$this->id_noeud."' ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$notices_to_index[] = intval($row->notcateg_notice);
			}
		}
		//On déplace les notices liées
		$requete= "UPDATE ignore notices_categories SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		@pmb_mysql_query($requete);

		//nettoyage d'autorities_sources
		$query = "select * from authorities_sources where num_authority = ".$this->id_noeud." and authority_type = 'category'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->authority_favorite == 1){
					//on suprime les références si l'autorité a été importée...
					$query = "delete from notices_authorities_sources where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
					$query = "delete from authorities_sources where id_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}else{
					//on fait suivre le reste
					$query = "update authorities_sources set num_authority = ".$by." where num_authority_source = ".$row->id_authority_source;
					pmb_mysql_query($query);
				}
			}
		}

		// nettoyage indexation concepts
		$index_concept = new index_concept($this->id_noeud, TYPE_CATEGORY);
		$index_concept->delete();

		//remplacement des associations avec le contenu éditorial
		$articles_to_index = array();
		$query = "SELECT num_article FROM cms_articles_descriptors WHERE num_noeud = '".$this->id_noeud."' ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $articles_to_index[] = intval($row->num_article);
		    }
		}
		$q = "UPDATE ignore cms_articles_descriptors SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		pmb_mysql_query($q);

		$sections_to_index = array();
		$query = "SELECT num_section FROM cms_sections_descriptors WHERE num_noeud = '".$this->id_noeud."' ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $sections_to_index[] = intval($row->num_section);
		    }
		}
		$q = "UPDATE ignore cms_sections_descriptors SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		pmb_mysql_query($q);

		//remplacement des associations avec les items de veilles
		$q = "UPDATE ignore docwatch_items_descriptors SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		pmb_mysql_query($q);

		//remplacement des associations avec les bannettes
		$q = "UPDATE ignore bannettes_descriptors SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		pmb_mysql_query($q);

		//remplacement des associations avec les campagnes de mails
		$q = "UPDATE ignore campaigns_descriptors SET num_noeud='".$by."' where num_noeud = '".$this->id_noeud."' ";
		pmb_mysql_query($q);

		if($faq_active) {
		    $faq_to_index = array();
		    $query = "SELECT num_faq_question FROM faq_questions_categories WHERE num_categ = '".$this->id_noeud."' ";
		    $result = pmb_mysql_query($query);
		    if (pmb_mysql_num_rows($result)) {
		        while ($row = pmb_mysql_fetch_object($result)) {
		            $faq_to_index[] = intval($row->num_faq_question);
		        }
		    }

		    //remplacement des associations avec les faq
		    $q = "UPDATE ignore faq_question_categories SET num_categ='".$by."' where num_categ = '".$this->id_noeud."' ";
		    pmb_mysql_query($q);
		}


		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_CATEG, $this->id_noeud, $by);

		// On supprime le noeud tout en effacant l'identifiant unique d'autorité
		static::delete($this->id_noeud);

		if ($indexation_active) {

		    // Notices
		    $index = count($notices_to_index);
		    for ($i = 0; $i < $index; $i++) {
		        notice::majNoticesGlobalIndex($notices_to_index[$i]);
		        notice::majNoticesMotsGlobalIndex($notices_to_index[$i], 'subject');
		    }

		    // Autorités liés (Auteurs, editeur...)
		    $index = count($authorities_to_index);
		    for ($i = 0; $i < $index; $i++) {
		        $entity_type = authority::aut_const_to_type_const($authorities_to_index[$i]["type"]);
		        indexation_stack::push($authorities_to_index[$i]["num"], $entity_type, "aut_link");
		    }

		    // Contenu éditorial Article
		    $index = count($articles_to_index);
		    for ($i = 0; $i < $index; $i++) {
		        $cms_article = new cms_article($articles_to_index[$i]);
    		    $cms_article->maj_indexation("categories");
		    }

		    // Contenu éditorial Rubrique
		    $index = count($sections_to_index);
		    for ($i = 0; $i < $index; $i++) {
		        $cms_section = new cms_section($sections_to_index[$i]);
    		    $cms_section->maj_indexation("categories");
		    }

		    if($faq_active){
		        // FAQ
		        $index = count($faq_to_index);
		        for ($i = 0; $i < $index; $i++) {
		            faq_question::maj_indexation($faq_to_index[$i]);
		        }
		    }
		}

		//synchro_rdf
		if($pmb_synchro_rdf){
			//on ajoute les noeuds impactés par le $by
			$thesBy = thesaurus::getByEltId($by);
			if(!in_array($thesBy->id_thesaurus,$arrayThesImpactes)){
				$arrayThesImpactes[]=$thesBy->id_thesaurus;
			}
			$arrayIdImpactes[]=$by;
			//parent
			if($noeuds_a_garder->num_parent!=$thesBy->num_noeud_racine){
				$arrayIdImpactes[]=$noeuds_a_garder->num_parent;
			}
			//enfants
			$res=noeuds::listChilds($noeuds_a_garder->id_noeud,1);
			if(pmb_mysql_num_rows($res)){
				while($row=pmb_mysql_fetch_array($res)){
					$arrayIdImpactes[]=$row[0];
				}
			}
			//renvoi_voir
			if($noeuds_a_garder->num_renvoi_voir){
				$arrayIdImpactes[]=$noeuds_a_garder->num_renvoi_voir;
			}
			//On met le tout à jour
			$synchro_rdf=new synchro_rdf();
			$synchro_rdf->delConcept($this->id_noeud);
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			if(count($arrayThesImpactes)){
				foreach($arrayThesImpactes as $idThes){
					$synchro_rdf->updateAuthority($idThes, 'thesaurus');
				}
			}
		}
		if ($pmb_ark_activate) {
		    $idReplaced = authority::get_authority_id_from_entity($this->id_noeud, AUT_TABLE_CATEGORIES);
		    $idReplacing = authority::get_authority_id_from_entity($by, AUT_TABLE_CATEGORIES);
		    if ($idReplaced && $idReplacing) {
		        $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplaced);
		        $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_AUTHORITY, $idReplacing);
		        $arkEntityReplaced->markAsReplaced($arkEntityReplacing);
		    }
		}
		return "";
	}

	public static function set_controller($controller) {
		static::$controller = $controller;
	}

	protected static function format_url($url='') {
		global $base_path;

		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=categories'.$url;
		}
	}

	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}

	protected static function format_delete_url($url='') {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}
}
?>