<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categories.class.php,v 1.59.2.1.2.1 2025/04/25 14:16:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/noeuds.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/indexation.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/indexation_authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once($class_path."/authority.class.php");
require_once($class_path."/indexation_stack.class.php");

class categories{


	/**
	 * @Description Id du noeud
	 */
    public $num_noeud;					//Identifiant du noeud de rattachement
	public $langue;
	public $libelle_categorie = '';
	public $note_application = '';
	public $comment_public = '';
	public $comment_voir = '';
	public $index_categorie = '';

	public static $labels = array();
	public static $list_ancestors_name = array();


	//Constructeur
	public function __construct($num_noeud, $langue, $for_indexation=false) {
		$this->num_noeud = intval($num_noeud);
		$this->langue = $langue;
		$q = "select count(1) from categories where num_noeud = '".$this->num_noeud."' and langue = '".$this->langue."' ";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_result($r, 0, 0) != 0) {
			$this->load();
		} else {
			if ($for_indexation) { //Ne pas créer de nouvelle categ pour la réindexation selon la langue
				$thesaurus = thesaurus::getByEltId($num_noeud);
				$this->langue = $thesaurus->langue_defaut;
				$this->load();
			} else {
				$defaultLibelle="-";
				$q = "SELECT libelle_categorie FROM categories JOIN thesaurus ON id_thesaurus = num_thesaurus AND langue_defaut=langue AND num_noeud='".$this->num_noeud."'";
				$r = pmb_mysql_query($q);
				if(pmb_mysql_num_rows($r)){
					$row=pmb_mysql_fetch_object($r);
					$defaultLibelle = $row->libelle_categorie;
				}
				$q = "insert into categories set num_noeud = '".$this->num_noeud."', langue = '".$langue."', ";
				$q.= "libelle_categorie = '".addslashes($defaultLibelle)."', note_application = '', comment_public = '', ";
				$q.= "comment_voir = '', index_categorie = '' ";
				$r = pmb_mysql_query($q);
				//pour les appels en enregistrement de notice, la méthode save n'est pas appelée, donc on force
				$this->libelle_categorie = $defaultLibelle;
				$this->save();
			}
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
		$this->update_index_path_word();

		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->num_noeud, TYPE_CATEGORY);

	}

	public function update_index_path_word(){
		global $thesaurus_auto_postage_search;
		global $thesaurus_auto_postage_search_nb_descendant,$thesaurus_auto_postage_search_nb_montant;

		/*	auto_postage_descendant:
		* 		Soit categ : Europe:France:Sarthe
		* 		et une notice sous la categ Sarthe.
		* 	la recherche tous champs de Europe va sortir la notice sous la categ Sarthe
		*/
		$no = new noeuds($this->num_noeud);
		$num_thesaurus = $no->num_thesaurus;
		$path=$no->path;
		// pour l'index coté gestion
		$lib_list=array();
		if($thesaurus_auto_postage_search){
			$limit=$thesaurus_auto_postage_search_nb_descendant;
			if($limit){
				$liste_num_noeud=explode('/',$path);
				if($limit != '*') array_splice($liste_num_noeud,0,count($liste_num_noeud) - intval($limit) - 1);
				$select_num_noeud=implode(',',$liste_num_noeud);
				if($select_num_noeud) {
					$q = "select libelle_categorie from categories where num_noeud in( $select_num_noeud ) and langue = '".$this->langue."' and num_thesaurus=$num_thesaurus";
					$r = pmb_mysql_query($q);
					while ($row = pmb_mysql_fetch_object($r))	{
						$lib_list[]= $row->libelle_categorie;
					}
				}
			}
		}

		/*	auto_postage_montant:
		 * 		Soit categ : Europe:France:Sarthe
		 * 		et une notice sous la categ Europe.
		 * 	la recherche tous champs de Sarthe va sortir la notice sous la categ Europe
		 */
		$liste_fils="";
		if($thesaurus_auto_postage_search){
			$limit=$thesaurus_auto_postage_search_nb_montant;
			if($limit){
				if( is_numeric($limit))
					$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$limit}$' ";
				elseif($limit == '*')
					$liste_fils=" (path like '$path/%' or  path = '$path') ";
				if($liste_fils)	{
					$q = "select libelle_categorie from categories,noeuds where id_noeud=num_noeud
					and $liste_fils and langue = '".$this->langue."' and categories.num_thesaurus=$num_thesaurus and noeuds.num_thesaurus=$num_thesaurus";
					$r = pmb_mysql_query($q);
					while ($row = pmb_mysql_fetch_object($r))	{
						$lib_list[]= $row->libelle_categorie;
					}
				}
			}
		}

		// Si rien, on ne met que le libelle de la categ
		if(!count($lib_list))$lib_list[]=$this->libelle_categorie;
		//$lib_list=array_unique  ($lib_list);
		$index=implode(" ",$lib_list);
		$clean_index=strip_empty_words($index);

		$q = "update categories set ";
		$q.= "path_word_categ = ' ".trim(addslashes($index))." ', ";
		$q.= "index_path_word_categ = ' ".trim(addslashes($clean_index))." ' ";
		$q.= "where num_noeud = '".$this->num_noeud."' and langue = '".$this->langue."' and num_thesaurus=$num_thesaurus";
		pmb_mysql_query($q);
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
		if(!isset(self::$list_ancestors_name[$num_noeud.'_'.$langue])){

			$thes = thesaurus::getByEltId($num_noeud);
			$id_list = noeuds::listAncestors($num_noeud);
			$id_list = array_reverse($id_list);
			self::$list_ancestors_name[$num_noeud.'_'.$langue] = '';

			foreach($id_list as $id) {
				if (categories::exists($id, $langue)) {
					$lg = $langue;
				} else {
					$lg = $thes ? $thes->langue_defaut : '';
				}
				$q = "select libelle_categorie from categories where num_noeud = '".$id."' ";
				$q.= "and langue = '".$lg."' limit 1";
				$r = pmb_mysql_query($q);
				if (pmb_mysql_num_rows($r))	{
					self::$list_ancestors_name[$num_noeud.'_'.$langue].= pmb_mysql_result($r, 0, 0);
					if ($id != $num_noeud) self::$list_ancestors_name[$num_noeud.'_'.$langue].= ':';
				}
			}
		}
		return self::$list_ancestors_name[$num_noeud.'_'.$langue];

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
	    $num_noeud = intval($num_noeud);
		$thes = thesaurus::getByEltId($num_noeud);

		$q = "select ";
		$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.not_use_in_indexation, noeuds.num_thesaurus, ";
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

	//optimization de la table categories
	public static function optimize() {
		$opt = pmb_mysql_query('OPTIMIZE TABLE categories');
		return $opt;

	}

	//recherche $libelle dans les libellés de la table categories et retourne 0 si non trouvé
	//sinon retourne identifiant de categorie
	public static function searchLibelle($libelle, $id_thesaurus=0, $lg=0, $num_parent=0) {
		global $lang;
		global $thesaurus_defaut;

		$libelle = trim($libelle);
		$id_thesaurus = intval($id_thesaurus);
		if (!$lg) $lg = $lang;
		$num_parent = intval($num_parent);
		if (!$id_thesaurus) $id_thesaurus = $thesaurus_defaut;

		$q = "select id_noeud from noeuds, categories where 1 ";
		if ($id_thesaurus != -1) $q.= "and noeuds.num_thesaurus = '".$id_thesaurus."' ";
		if ($num_parent) $q.= "and noeuds.num_parent = '".$num_parent."' ";
		if ($lg != -1) $q.= "and categories.langue = '".$lg."' ";
		$q.= "and categories.libelle_categorie = '".$libelle."' ";
		$q.= "and noeuds.id_noeud = categories.num_noeud ";
		$q.= "limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) return pmb_mysql_result($r, 0, 0);
			else return 0;

	}


	//---------------------------------------------------------------
	// update_index($id) : maj des index
	// en rapport avec cette catégorie
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		global $include_path;

		indexation_stack::push($id, TYPE_CATEGORY, $datatype);

		//ajout des mots des termes dans la table words pour l autoindexation
		$q = "select trim(index_categorie) as index_categorie, langue from categories where num_noeud=".$id;
		$r = pmb_mysql_query($q);
		$i=0;
		$t_words=array();
		if(pmb_mysql_num_rows($r)) {
			while ($row =pmb_mysql_fetch_object($r)) {
				$t_row = explode(' ',$row->index_categorie);
				if( is_array($t_row) && count($t_row) ) {
					$t_row = array_unique($t_row);
					foreach($t_row as $w) {
						if($w) {
							$t_words[$i]['word'] = $w;
							$t_words[$i]['lang'] = $row->langue;
							$i++;
						}
					}
				}
			}
		}
		if(count($t_words)) {
			//calcul de stem et double_metaphone
			foreach ($t_words as $i=>$w) {
				$q1 = "select id_word from words where word='".addslashes($w['word'])."' and lang='".addslashes($w['lang'])."' limit 1";
				$r1 = pmb_mysql_query($q1);
				if(pmb_mysql_num_rows($r1)) {
					//le mot existe
					$t_words[$i]['allready_exists']=1;
				} else {
					//le mot n'existe pas
					$dmeta = new DoubleMetaPhone($w['word']);
					if($dmeta->primary || $dmeta->secondary){
						$t_words[$i]['double_metaphone'] = $dmeta->primary." ".$dmeta->secondary;
					}
					if($w['lang']=='fr_FR') {
						$stemming = new stemming($w['word']);
						$t_words[$i]['stem']=$stemming->stem;
					} else {
						$t_words[$i]['stem']='';
					}
				}
			}
			foreach($t_words as $i=>$w) {
				if (!$w['allready_exists']) {
					$q2 = "insert ignore into words (word, lang, double_metaphone, stem) values ('".$w['word']."', '".$w['lang']."', '".$w['double_metaphone']."', '".$w['stem']."') ";
					pmb_mysql_query($q2);
				}
			}
		}

		// On cherche tous les n-uplet de la table notice correspondant à cette catégorie.
		$query = "select distinct notcateg_notice as notice_id from notices_categories where num_noeud='".$id ."' ";
		authority::update_records_index($query, 'subject');

	   	//on cherche les questions correspondantes...
	   	$query = "select num_faq_question from faq_questions_categories where num_categ = ".$id;
	   	$result = pmb_mysql_query($query);
	   	if(pmb_mysql_num_rows($result)){
	   	    $index = indexations_collection::get_indexation(AUT_TABLE_FAQ);
	   		while($row = pmb_mysql_fetch_object($result)){
	   			$index->maj($row->num_faq_question,"categories");
	   		}
	   	}
	}

	public static function getlibelle($num_noeud=0, $langue=""){
	    $num_noeud = intval($num_noeud);
		if(!isset(self::$labels[$num_noeud.'_'.$langue])){
			self::$labels[$num_noeud.'_'.$langue] = '';
			$thes = thesaurus::getByEltId($num_noeud);
			if (categories::exists($num_noeud, $langue)) $lg=$langue;
			else $lg=$thes->langue_defaut;
			$q = "select libelle_categorie from categories where num_noeud = '".$num_noeud."' ";
			$q.= "and langue = '".$lg."' limit 1";
			$r = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($r))	{
				self::$labels[$num_noeud.'_'.$langue]= pmb_mysql_result($r, 0, 0);
			}
		}
		return self::$labels[$num_noeud.'_'.$langue];
	}

	public static function process_categ_index($start=0, $limit=0) {
		$q = "select * from categories ";
		if($start || $limit) {
			$q .= " limit ".$start.",".$limit;
		}
		$r = pmb_mysql_query($q);
		while ($obj = pmb_mysql_fetch_object($r)) {
			$thes = new categories($obj->num_noeud,$obj->langue);
			$thes->update_index_path_word();
		}
	}
}
?>