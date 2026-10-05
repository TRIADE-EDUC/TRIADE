<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publisher.class.php,v 1.51 2024/03/21 09:15:00 pmallambic Exp $

// définition de la classe de gestion des 'editeurs'

if ( ! defined( 'PUBLISHER_CLASS' ) ) {
  define( 'PUBLISHER_CLASS', 1 );

class publisher {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------

	// note : '//' signifie appartenant à la table concernée
	//        '////' signifie deviné avec des requêtes sur d'autres tables
	public $id;          // MySQL id in table 'publishers'
	public $name;        // publisher name
	public $adr1;        // adress line 1
	public $adr2;        // adress line 2
	public $cp;          // zip code
	public $ville;       // city
	public $pays;        // country
	public $web;         // url of web site
	public $link;       //// url of web site (clickable)
	public $display;    //// usable form for displaying ( _name_ (_ville_) or just _name_ )
	public $isbd_entry; //// isbd like version ( _ville_ (_country ?_) : _name_ )
	public $isbd_tpl; 
	public $ed_comment;
	public $num_statut = 1;    //Identifiant du statut affecté à l'éditeur
	public $authority;	// Instance de authority
	protected $p_perso;	
	
	/**
	 * Tableau des collections
	 * @var authority
	 */
	protected $collections;


	// ---------------------------------------------------------------
	//  publisher($id) : constructeur
	// ---------------------------------------------------------------

	public function __construct($id) {
		$this->id = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : recuperation infos editeurs
	// ---------------------------------------------------------------
	public function getData() {
		$this->name        = '';
		$this->adr1        = '';
		$this->adr2        = '';
		$this->cp          = '';
		$this->ville       = '';
		$this->pays        = '';
		$this->web         = '';
		$this->link        = '';
		$this->display     = '';
		$this->isbd_entry  = '';
		$this->ed_comment  = '';
		$this->num_statut   =   1;
		if($this->id) {
			$requete = "SELECT * FROM publishers WHERE ed_id='".$this->id."'";
			$result = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id 	= $row->ed_id;
				$this->name = $row->ed_name;
				$this->adr1 = $row->ed_adr1;
				$this->adr2 = $row->ed_adr2;
				$this->cp 	= $row->ed_cp;
				$this->ville = $row->ed_ville;
				$this->pays = $row->ed_pays;
				$this->web = $row->ed_web;
				$this->ed_comment = $row->ed_comment;
				$this->num_statut = $this->get_authority()->get_num_statut();
				if ($this->web) {
					$this->link = "<a href='".$this->web."' target='_new'>$this->web</a>";
				}
				// Détermine le lieu de publication
				$l = '';
				if ($this->adr1)  $l = $this->adr1;
				if ($this->adr2)  $l = ($l=='') ? $this->adr2 : $l.', '.$this->adr2;
				if ($this->cp)    $l = ($l=='') ? $this->cp   : $l.', '.$this->cp;
				if ($this->pays)  $l = ($l=='') ? $this->pays : $l.', '.$this->pays;
				if ($this->ville) $l = ($l=='') ? $this->ville : $this->ville.' ('.$l.')';
				if ($l=='')       $l = '[S.l.]';
				
				// Détermine le nom de l'éditeur
				if ($this->name) $n = $this->name; else $n = '[S.n.]';
				
				// Constitue l'ISBD pour le coupe lieu/éditeur
				if ($l == '[S.l.]' AND $n == '[S.n.]') $this->isbd_entry = '[S.l.&nbsp;: s.n.]';
				else $this->isbd_entry = $l.'&nbsp;: '.$n;
				//On fait en sorte que le &nbsp; ne nous embête pas à l'affichage
				global $charset;
				$this->isbd_entry = html_entity_decode($this->isbd_entry,ENT_QUOTES, $charset);
				
				if ($this->ville) {
					if ($this->pays) $this->display = "$this->ville [$this->pays] : $this->name";
					else $this->display = "$this->ville : $this->name";
				} else {
					$this->display = $this->name;
				}
			}
		}
	}

	// ---------------------------------------------------------------
	//  print_resume($level) : affichage d'informations sur la collection
	// ---------------------------------------------------------------

	public function print_resume($level = 2,$css='') {
		global $css, $msg, $charset;
		if(!$this->id)
			return;

		// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 2 :
			default :
				global $publisher_level2_display;

				$publisher_display = $publisher_level2_display;
				break;
		}

		$print = $publisher_display;

		// remplacement des champs statiques
		$print = str_replace("!!id!!", $this->id, $print);
		$print = str_replace("!!name!!", $this->name, $print);
		$print = str_replace("!!adr1!!", $this->adr1, $print);
		$print = str_replace("!!adr2!!", $this->adr2, $print);
		$print = str_replace("!!cp!!", $this->cp, $print);
		$print = str_replace("!!ville!!", $this->ville, $print);
		$print = str_replace("!!pays!!", $this->pays, $print);
		if ($this->web) $print = str_replace("!!site_web!!", "<a href='$this->web' target='_blank' type='external_url_autor' title='".htmlentities($msg['rgaa_author_field_link'], ENT_QUOTES, $charset)."'><img src='".get_url_icon("globe.gif")."' style='border:0px' /></a>", $print);
		else $print = str_replace("!!site_web!!", "", $print);
		$print = str_replace("!!isbd!!", $this->isbd_entry, $print);
		$print = str_replace("!!aut_comment!!", $this->ed_comment, $print);


		if (preg_match("#!!colls!!#", $print)) {
			$query = "select collection_id, collection_name from collections where collection_parent='".$this->id."' order by index_coll";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$remplacement = $msg['publishers_collections']."\n<ul>\n";
				while ($obj = pmb_mysql_fetch_object($result)) {
					$remplacement .= "<li><a href='index.php?lvl=coll_see&id=".$obj->collection_id."'>".$obj->collection_name."</a></li>\n";
				}
				pmb_mysql_free_result($result);
				$remplacement .= "</ul><div class='row'></div>\n";
			} else {
				$remplacement = "";
			}
			$print = str_replace("!!colls!!", $remplacement, $print);
		}

		if (preg_match("#!!address!!#", $print)) {
			if (($this->adr1 != "") && ($this->cp != "") && ($this->ville != "")) {
				$remplacement = $this->adr1;
				if ($this->adr2 != "") $remplacement .= "<br />\n".$this->adr2;
				$remplacement .= "<br />\n".$this->cp." ".$this->ville;
				if ($this->pays != "") $remplacement .= "<br />\n".$this->pays;
			} else {
				$remplacement = "";
			}
			$print = str_replace("!!address!!", $remplacement, $print);
		}

		return $print;
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_isbd() {
		if(!$this->isbd_tpl ){
			$authority = $this->get_authority();
			$template_path =  $authority->find_template("isbd");				
			if(file_exists($template_path)){
				$h2o = H2o_collection::get_instance($template_path);
				$this->isbd_tpl = str_replace(array("\n", "\t", "\r"), '', strip_tags($h2o->render(array('publisher' => $this->get_authority()))));
				return $this->isbd_tpl;
			}
		}else if($this->isbd_tpl){
			return $this->isbd_tpl;
		}
		return $this->display;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_editeur']);
	}
	
	public function get_comment() {
		return $this->ed_comment;
	}
	
	public function get_collections() {
		if (isset($this->collections)) {
			return $this->collections;
		}
		$this->collections = array();
	
		$query = "select collection_id from collections where collection_parent = '".$this->id."' order by index_coll";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				//$this->collections[] = new authority(0, $row->collection_id, AUT_TABLE_COLLECTIONS);
				$this->collections[] = authorities_collection::get_authority('authority', 0, ['num_object' => $row->collection_id, 'type_object' => AUT_TABLE_COLLECTIONS]);
			}
		}
		return $this->collections;
	}
	
	public function get_header() {
		return $this->display;
	}

	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name,
				'adr1' => $this->adr1,
				'adr2' => $this->adr2,
				'cp' => $this->cp,
				'ville' => $this->ville,
				'pays' => $this->pays,
				'web' => $this->web,
				'comment' => $this->ed_comment
		);
		$formatted_data = array_merge($this->get_authority()->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		$this->authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_PUBLISHERS]);
		return $this->authority;
	}
} # fin de définition de la classe éditeur

} # fin de délaration

