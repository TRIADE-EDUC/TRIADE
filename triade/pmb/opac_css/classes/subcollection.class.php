<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollection.class.php,v 1.26 2022/02/10 08:18:42 dgoron Exp $

// définition de la classe de gestion des 'sous-collections'

if ( ! defined( 'SUB_COLLECTION_CLASS' ) ) {
  define( 'SUB_COLLECTION_CLASS', 1 );

global $class_path;
require_once($class_path."/authorities_collection.class.php");

class subcollection {

	// ---------------------------------------------------------------
	//  propriétés de la classe
	// ---------------------------------------------------------------

	// note : '//' signifie appartenant à la table concernée
	//        '////' signifie deviné avec des requêtes sur d'autres tables
	public $id;                  // MySQL id in table 'collections'
	public $name;                // collection name
	public $parent;              // MySQL id of parent collection
	public $parent_libelle;     //// name of parent collection
	public $parent_isbd;        //// name of parent collection, isbd form
	public $publisher;          //// MySQL id of publisher
	public $publisher_libelle;  //// name of parent publisher
	public $publisher_isbd;     //// isbd form of publisher
	public $display;            //// usable form for displaying	( _collection_. _name_ (_editeur_) )
	public $isbd_entry;         //// ISBD form ( _collection_. _name_ )
	public $issn;                // ISSN of sub collection
	public $comment;
	public $num_statut = 1;
	protected $p_perso;	

	// ---------------------------------------------------------------
	//  subcollection($id) : constructeur
	// ---------------------------------------------------------------

	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos sous collection
	// ---------------------------------------------------------------
	public function getData() {
		$this->name               = '';
		$this->parent             = '';
		$this->parent_libelle     = '';
		$this->parent_isbd        = '';
		$this->publisher          = '';
		$this->publisher_libelle  = '';
		$this->publisher_isbd     = '';
		$this->display            = '';
		$this->issn               = '';
		$this->isbd_entry         = '';
		$this->comment         	  = '';
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM sub_collections WHERE sub_coll_id='".$this->id."' ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->sub_coll_id;
				$this->name = $row->sub_coll_name;
				$this->parent = $row->sub_coll_parent;
				$this->issn = $row->sub_coll_issn;
				$this->comment = $row->subcollection_comment;
				$this->num_statut = $this->get_authority()->get_num_statut();
				if ($this->parent) {
					$parentcoll = authorities_collection::get_authority('collection', $this->parent);
					$this->parent_libelle = $parentcoll->name;
					$this->parent_isbd = $parentcoll->get_isbd();
					$this->publisher = $parentcoll->parent;
					$this->publisher_libelle = $parentcoll->publisher_libelle;
					$this->publisher_isbd = $parentcoll->publisher_isbd;
				}
				$this->display = $this->parent_libelle.'.&nbsp;'.$this->name.'&nbsp;('.$this->publisher_libelle.')';
				$this->isbd_entry = $this->issn ? $this->parent_libelle.'.&nbsp;'.$this->name.', ISSN '.$this->issn : $this->parent_libelle.'.&nbsp;'.$this->name ;
			}
		}
	}

	// ---------------------------------------------------------------
	//  print_resume($level) : affichage d'informations sur la sous-collection
	// ---------------------------------------------------------------

	public function print_resume($level = 2,$css=''){
		global $css;
		if(!$this->id)
			return;

		// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 2 :
			default :
				global $subcollection_level2_display;
				global $subcollection_level2_no_issn_info;

				$subcollection_display = $subcollection_level2_display;
				$subcollection_no_issn_info = $subcollection_level2_no_issn_info;
				break;
		}

		$print = $subcollection_display;
		
		// remplacement des champs statiques
		$print = str_replace("!!name!!", $this->name, $print);
		$print = str_replace("!!issn!!", $this->issn ? $this->issn : $subcollection_no_issn_info, $print);
		$print = str_replace("!!publ!!", $this->publisher_libelle, $print);
		$print = str_replace("!!publ_isbd!!", $this->publisher_isbd, $print);
		$print = str_replace("!!coll!!", $this->parent_libelle, $print);
		$print = str_replace("!!coll_isbd!!", $this->parent_isbd, $print);
		$print = str_replace("!!isbd!!", $this->isbd_entry, $print);
		$print = str_replace("!!comment!!", $this->comment, $print);

		// remplacement des champs dynamiques
		if (preg_match("#!!publisher!!#", $print))
		{
			$remplacement = "<a href='index.php?lvl=publisher_see&id=$this->publisher'>$this->publisher_libelle</a>";
			$print = str_replace("!!publisher!!", $remplacement, $print);
		}

		if (preg_match("#!!collection!!#", $print))
		{
			$remplacement = "<a href='index.php?lvl=coll_see&id=$this->parent'>$this->parent_libelle</a>";
			$print = str_replace("!!collection!!", $remplacement, $print);
		}

		return $print;
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_isbd() {
		return $this->isbd_entry;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_subcollection']);
	}
	
	public function get_comment() {
		return $this->comment;
	}

	public function get_header() {
		return $this->display;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		if(!$antiloop) {
			if($this->parent) {
				$parent = new collection($this->parent);
				$parent_datas = $parent->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->name,
				'issn' => $this->issn,
				'parent' => $parent_datas,
				'web' => $this->subcollection_web,
				'comment' => $this->comment
		);
		$formatted_data = array_merge($this->get_authority()->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_web(){
		return $this->subcollection_web;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_SUB_COLLECTIONS]);
	}
} # fin de définition de la classe subcollection

} # fin de délaration
