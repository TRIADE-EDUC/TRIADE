<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie.class.php,v 1.19 2022/02/10 08:18:42 dgoron Exp $

// définition de la classe de gestion des 'titres de séries'

if ( ! defined( 'SERIE_CLASS' ) ) {
  define( 'SERIE_CLASS', 1 );

global $base_path;
require_once($base_path.'/includes/templates/serie.tpl.php');
  
class serie {

	// ---------------------------------------------------------------
	//  propriétés de la classe
	// ---------------------------------------------------------------

	public $id       = 0;        // MySQL serie_id in table 'series'
	public $name     = '';       // serie name
	public $index    = '';       // serie form for index
	public $num_statut = 1; //Statut

	// ---------------------------------------------------------------
	//  série($id) : constructeur
	// ---------------------------------------------------------------

	public function __construct($id) {
		$this->id = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos du titre
	// ---------------------------------------------------------------

	public function getData() {
		$this->name			=	'';
		$this->index			=	'';
		$this->num_statut = 1;
		if($this->id) {
			$requete = "SELECT * FROM series WHERE serie_id='".addslashes($this->id)."' ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->serie_id;
				$this->name = $row->serie_name;
				$this->index = $row->serie_index;
				//$authority = new authority(0, $this->id, AUT_TABLE_SERIES);
				$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_SERIES]);
				$this->num_statut = $authority->get_num_statut();
			}
		}
	}

	// ---------------------------------------------------------------
	//  print_resume($level) : affichage d'informations sur la série
	// ---------------------------------------------------------------

	public function print_resume($level = 2,$css='') {
		global $css;
		if(!$this->id) return;

		// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 2 :
			default :
				global $serie_level2_display;
				$publisher_display = $serie_level2_display;
				break;
			}

		$print = $publisher_display;

		// remplacement des champs statiques
		$print = str_replace("!!id!!", $this->id, $print);
		$print = str_replace("!!name!!", $this->name, $print);

		return $print;
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_isbd() {
		return $this->name;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_serie']);
	}
	
	public function get_comment() {
		return '';
	}

	public function get_header() {
		return $this->name;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name
		);
		//$authority = new authority(0, $this->id, AUT_TABLE_SERIES);
		$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_SERIES]);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
} # fin de définition de la classe serie

} # fin de délaration

