<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collection.class.php,v 1.35 2021/12/28 13:30:46 dgoron Exp $

// définition de la classe de gestion des collections
// inclure :
// classes/publisher.class.php

if ( ! defined( 'COLLECTION_CLASS' ) ) {
  define( 'COLLECTION_CLASS', 1 );

global $class_path;
require_once($class_path."/authorities_collection.class.php");

class collection {

	// ---------------------------------------------------------------
	//  propriétés de la classe
	// ---------------------------------------------------------------

	// note : '//' signifie appartenant à la table concernée
	//        '////' signifie deviné avec des requêtes sur d'autres tables
	public $id;                 // MySQL id in table 'collections'
	public $name;               // collection name
	public $parent;             // MySQL id of parent publisher
	public $publisher_libelle; //// name of parent publisher
	public $publisher_isbd;    //// isbd form of publisher
	public $display;           //// usable form for displaying	( _name_ (_publisher_) )
	public $isbd_entry;        //// isbd form
	public $issn;               // ISSN of collection
	public $collection_web;		// web de collection
	public $collection_web_link;	// lien web de collection
	public $num_statut = 1; //Statut de la collection
	public $comment;
	protected $p_perso;	
	
	/**
	 * Tableau des sous-collections
	 * @var authority
	 */
	protected $subcollections;
	
	// ---------------------------------------------------------------
	//  collection($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos collection
	// ---------------------------------------------------------------
	public function getData() {
		global $charset;
		$this->name              = '';
		$this->parent            = '';
		$this->publisher_libelle = '';
		$this->publisher_isbd    = '';
		$this->display           = '';
		$this->issn              = '';
		$this->isbd_entry        = '';
		$this->collection_web	 = '';
		$this->collection_web_link = "" ;
		$this->comment = "" ;
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM collections WHERE collection_id='".$this->id."'";
			$result = @pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->collection_id;
				$this->name = $row->collection_name;
				$this->parent = $row->collection_parent;
				$this->issn = $row->collection_issn;
				$this->collection_web= $row->collection_web;
				$this->comment= $row->collection_comment;
				$this->num_statut = $this->get_authority()->get_num_statut();
				if($row->collection_web) 
					$this->collection_web_link = " <a href='$row->collection_web' target=_blank title='".htmlentities($row->collection_web,ENT_QUOTES,$charset)."' type='external_url_autor' ><img src='".get_url_icon("globe.gif")."' style='border:0px' /></a>";
				$publisher = authorities_collection::get_authority('publisher', $this->parent);
				$this->publisher_isbd = $publisher->get_isbd();
				$this->publisher_libelle = $publisher->name;
				$this->isbd_entry = $this->issn ? $this->name.', ISSN '.$this->issn : $this->name;
				$this->display = $this->name.' ('.$this->publisher_libelle.')';
			}
		}
	}
	
	// ---------------------------------------------------------------
	//  print_resume($level) : affichage d'informations sur la collection
	// ---------------------------------------------------------------
	public function print_resume($level = 2,$css='') {
		global $css;
		global $msg;
		
		if(!$this->id)
			return;
	
		// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 2 :
			default :
				global $collection_level2_display;
				global $collection_level2_no_issn_info;
	
				$collection_display = $collection_level2_display;
				$collection_no_issn_info = $collection_level2_no_issn_info;
				break;
		}
	
		$print = $collection_display;
		// remplacement des champs statiques
		$print = str_replace("!!name!!", $this->name." ".$this->collection_web_link, $print);
		$print = str_replace("!!issn!!", $this->issn ? $this->issn : $collection_no_issn_info, $print);
		$print = str_replace("!!publ!!", $this->publisher_libelle, $print);
		$print = str_replace("!!publ_isbd!!", $this->publisher_isbd, $print);
		$print = str_replace("!!isbd!!", $this->isbd_entry, $print);
		$print = str_replace("!!comment!!", nl2br($this->comment), $print);
		// remplacement des champs dynamiques
		if (preg_match("#!!publisher!!#", $print)) {
			$remplacement = "<a href='index.php?lvl=publisher_see&id=$this->parent'>$this->publisher_libelle</a>";
			$print = str_replace("!!publisher!!", $remplacement, $print);
		}
	
		if (preg_match("#!!subcolls!!#", $print)) {
			$query = "select sub_coll_id, sub_coll_name from sub_collections where sub_coll_parent=".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$remplacement = $msg["subcollection_attached"]."\n<ul>\n";
				while ($obj = pmb_mysql_fetch_object($result)) 
					$remplacement .= "<li><a href='index.php?lvl=subcoll_see&id=".$obj->sub_coll_id."'>".$obj->sub_coll_name."</a></li>\n";
				pmb_mysql_free_result($result);
				$remplacement .= "</ul><div class='row'></div>\n";
			} else $remplacement = "";
			$print = str_replace("!!subcolls!!", $remplacement, $print);
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
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_collection']);
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	public function get_subcollections() {
		if (isset($this->subcollections)) {
			return $this->subcollections;
		}
		$this->subcollections = array();
		$query = "select sub_coll_id from sub_collections where sub_coll_parent = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				//$this->subcollections[] = new authority(0, $row->sub_coll_id, AUT_TABLE_SUB_COLLECTIONS);
				$this->subcollections[] = authorities_collection::get_authority('authority', 0, ['num_object' => $row->sub_coll_id, 'type_object' => AUT_TABLE_SUB_COLLECTIONS]);
			}
		}
		return $this->subcollections;
	}

	public function get_header() {
		return $this->display;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		if(!$antiloop) {
			if($this->parent) {
				$parent = new publisher($this->parent);
				$parent_datas = $parent->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->name,
				'issn' => $this->issn,
				'publisher' => $parent_datas,
				'web' => $this->collection_web,
				'comment' => $this->comment
		);
		//$authority = new authority(0, $this->id, AUT_TABLE_COLLECTIONS);
		$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_COLLECTIONS]);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_web(){
		return $this->collection_web;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_COLLECTIONS]);
	}
} # fin de définition de la classe collection

} # fin de délaration
