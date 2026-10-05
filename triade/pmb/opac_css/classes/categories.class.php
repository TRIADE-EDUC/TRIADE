<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categories.class.php,v 1.27 2023/10/25 08:07:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/noeuds.class.php");
require_once($class_path."/thesaurus.class.php");

class categories{


	public $num_noeud;					//Identifiant du noeud de rattachement
	public $langue;
	public $libelle_categorie = '';
	public $note_application = '';
	public $comment_public = '';
	public $comment_voir = '';
	public $index_categorie = '';

	//Constructeur
	public function __construct($num_noeud, $langue) {
		$this->num_noeud = intval($num_noeud);
		$this->langue = $langue;
		$q = "select count(1) from categories where num_noeud = '".$this->num_noeud."' and langue = '".$this->langue."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {
			$this->load();
		}
	}

	// charge la catégorie à partir de la base si elle existe.
	public function load(){
		$q = "select * from categories where num_noeud = '".$this->num_noeud."' and langue = '".$this->langue."' limit 1";
		$r = pmb_mysql_query($q);
		$obj = pmb_mysql_fetch_object($r);
		$this->libelle_categorie = $obj->libelle_categorie;
		$this->note_application = $obj->note_application;
		$this->comment_public = $obj->comment_public;
		$this->comment_voir = $obj->comment_voir;
		$this->index_categorie = $obj->index_categorie;
	}

	// enregistre la catégorie en base.
	public function save(){
		$no = new noeuds($this->num_noeud);
		$num_thesaurus = $no->num_thesaurus;

		$q = "update categories set ";
		$q.= "num_thesaurus = '".$num_thesaurus."', ";
		$q.= "libelle_categorie = '".addslashes($this->libelle_categorie)."', ";
		$q.= "note_application = '".addslashes($this->note_application)."', ";
		$q.= "comment_public = '".addslashes($this->comment_public)."', ";
		$q.= "comment_voir = '".addslashes($this->comment_voir)."', ";
		$q.= "index_categorie = ' ".addslashes(strip_empty_words($this->libelle_categorie,$this->langue))." ' ";
		$q.= "where num_noeud = '".$this->num_noeud."' and langue = '".$this->langue."' ";
		pmb_mysql_query($q);
		categories::update_index($this->num_noeud);

	}

	//verifie si une categorie existe dans la langue concernée
	public static function exists($num_noeud, $langue) {
		$num_noeud = intval($num_noeud);
		$q = "select count(1) from categories where num_noeud = '".$num_noeud."' and langue = '".$langue."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) == 0) return FALSE;
			else return TRUE;
	}

	//supprime une categorie en base.
	public function delete($num_noeud, $langue) {
		$num_noeud = intval($num_noeud);
		$q = "delete from categories where num_noeud = '".$num_noeud."' and langue = '".$langue."' ";
		pmb_mysql_query($q);
	}

	//Liste les libelles des ancetres d'une categorie dans la langue concernée
	//a partir de la racine du thesaurus
	public static function listAncestorNames($num_noeud=0, $langue='') {
		$num_noeud = intval($num_noeud);
		$thes = thesaurus::getByEltId($num_noeud);
		$id_list = noeuds::listAncestors($num_noeud);
		$id_list = array_reverse($id_list);
		$lib_list = '';

		foreach($id_list as $dummykey=>$id) {
			if (categories::exists($id, $langue)) $lg=$langue;
			else $lg=$thes->langue_defaut;
			$q = "select libelle_categorie from categories where num_noeud = '".$id."' ";
			$q.= "and langue = '".$lg."' limit 1";
			$r = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($r))	{
				$lib_list.= pmb_mysql_result($r, 0, 0);
				if ($id != $num_noeud) $lib_list.= ':';
			}
		}
		return $lib_list;
	}

	//Retourne un tableau des ancetres d'une categorie dans la langue concernée
	//a partir de la racine du thesaurus
	public static function listAncestors($num_noeud=0, $langue='') {
		$num_noeud = intval($num_noeud);
		$thes = thesaurus::getByEltId($num_noeud);
		$id_list = noeuds::listAncestors($num_noeud);
		$id_list = array_reverse($id_list);
		$anc_list = array();

		foreach($id_list as $id) {
			if (categories::exists($id, $langue)) $lg=$langue;
			else $lg=$thes->langue_defaut;
			$q = "select * from noeuds, categories ";
			$q.= "where categories.num_noeud = '".$id."' ";
			$q.= "and categories.langue = '".$lg."' ";
			$q.= "and categories.num_noeud = noeuds.id_noeud ";
			$q.= "limit 1";
			$r = pmb_mysql_query($q);

			while ($row = pmb_mysql_fetch_object($r))	{
				$anc_list[$id]['num_noeud'] = $row->num_noeud;
				$anc_list[$id]['num_parent'] = $row->num_parent;
				$anc_list[$id]['num_renvoi_voir'] = $row->num_renvoi_voir;
				$anc_list[$id]['visible'] = $row->visible;
				$anc_list[$id]['num_thesaurus'] = $row->num_thesaurus;
				$anc_list[$id]['langue'] = $row->langue;
				$anc_list[$id]['libelle_categorie'] = $row->libelle_categorie;
				$anc_list[$id]['note_application'] = $row->note_application;
				$anc_list[$id]['comment_public'] = $row->comment_public;
				$anc_list[$id]['comment_voir'] = $row->comment_voir;
				$anc_list[$id]['index_categorie'] = $row->index_categorie;
				$anc_list[$id]['autorite'] = $row->autorite;
			}
		}
		return $anc_list;
	}

	//Retourne un resultset des enfants d'une categorie dans la langue concernée
	public static function listChilds($num_noeud=0, $langue='', $keep_tilde=1, $ordered=0) {
		global $opac_categories_nav_max_display;

		$num_noeud = intval($num_noeud);
		$thes = thesaurus::getByEltId($num_noeud);
		if($opac_categories_nav_max_display > 0) $limit= " limit $opac_categories_nav_max_display ";
		else $limit='';
		$q = "select ";
		$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
		$q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
		$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
		$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
		$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
		$q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
		$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$langue."' ";
		$q.= "where ";
		$q.= "noeuds.num_parent = '".$num_noeud."' ";
		if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
		if ($ordered !== 0) $q.= "order by ".$ordered." ";
		$q.=$limit;
		$r = pmb_mysql_query($q);
		return $r;
	}


	public static function hasChildren($num_noeud=0) {
	    $num_noeud = intval($num_noeud);

	    $q = "SELECT 1 FROM noeuds LEFT JOIN categories AS catdef ON noeuds.id_noeud=catdef.num_noeud ";
	    $q.= "WHERE noeuds.num_parent = '".$num_noeud."' LIMIT 1";
	    $r = pmb_mysql_query($q);
	    return pmb_mysql_result($r, 0) == "1";
	}

	//Retourne un resultset des Orphelins dont le renvoi voir pointe sur ce noeud
	public static function listSynonymes($num_noeud=0, $langue='', $keep_tilde=1, $ordered=0) {
		global $opac_categories_nav_max_display;

		$num_noeud = intval($num_noeud);
		$thes = thesaurus::getByEltId($num_noeud);
		$q = "select id_noeud from noeuds where num_thesaurus = '".$thes->id_thesaurus."' and autorite = 'ORPHELINS' ";
		$r = pmb_mysql_query($q);
		if($r && pmb_mysql_num_rows($r)){
			$num_noeud_orphelins = pmb_mysql_result($r, 0, 0);
		}else{
			$num_noeud_orphelins=0;
		}
		if($opac_categories_nav_max_display > 0) $limit= " limit $opac_categories_nav_max_display ";
		else $limit='';
		$q = "select ";
		$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
		$q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
		$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
		$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
		$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
		$q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
		$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$langue."' ";
		$q.= "where ";
		$q.= "noeuds.num_parent = '$num_noeud_orphelins' and noeuds.num_renvoi_voir='".$num_noeud."' ";
		if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
		if ($ordered !== 0) $q.= "order by ".$ordered." ";
		$q.=$limit;
		$r = pmb_mysql_query($q);
		return $r;
	}

	public static function getlibelle($num_noeud=0, $langue=""){
		$num_noeud = intval($num_noeud);
		$lib="";
		$thes = thesaurus::getByEltId($num_noeud);
		if (categories::exists($num_noeud, $langue)) $lg=$langue;
		else $lg=$thes->langue_defaut;
		$q = "select libelle_categorie from categories where num_noeud = '".$num_noeud."' ";
		$q.= "and langue = '".$lg."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r))	{
			$lib= pmb_mysql_result($r, 0, 0);
		}
		return $lib;
	}
}

?>