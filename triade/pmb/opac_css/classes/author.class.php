<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author.class.php,v 1.48 2019-03-21 14:31:10 dgoron Exp $

// définition de la classe de gestion des 'auteurs'

if ( ! defined( 'AUTEUR_CLASS' ) ) {
  define( 'AUTEUR_CLASS', 1 );

require_once($class_path."/rdf/arc2/ARC2.php");
require_once($class_path.'/authority.class.php');
  
class auteur {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------

	public $id;            // MySQL id in table 'authors'
	public $type;          // author type (70 or 71)
	public $name;          // author name
	public $rejete;        // author name (rejected element)
	public $date;          // dates
	public $see;           // 'see' author MySQL id
	// public $see_libelle;  //// printable form of 'see' author (in fact 'display' of retained form)
	public $display;      //// usable form for displaying ( _name_, _rejete_ (_date_) )
	public $isbd_entry;   //// isbd like version ( _rejete_ _name_ (_date_))
	public $author_web;	// web de l'auteur
	public $author_isni;	// web de l'auteur
	public $author_comment; //
	public $author_web_link;	// lien web de l'auteur
	public $enrichment=null;
	public $authority;
	protected $p_perso;
	protected $see_data;
	/**
	 * Tableau des identifiants de l'auteur (comprenant les identifiants de renvoi)
	 * @var array
	 */
	protected $author_ids;
	
	/**
	 * Rendu HTML des renvois d'auteur
	 * @var string
	 */
	protected $author_see;
	
	/**
	 * Détail des renvois d'auteur
	 * @var string
	 */
	protected $author_see_details;
	
	/**
	 * Rendu HTML des documents numériques auxquels l'auteur est associé
	 * @var string $associated_explnums
	 */
	protected $associated_explnums;
	
	// ---------------------------------------------------------------
	//		auteur($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id) {
		$this->id = $id+0;
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	// getData() : récupération infos auteur
	// ---------------------------------------------------------------
	public function getData() {
		global $msg;
		$this->type        = '';
		$this->name        = '';
		$this->rejete      = '';
		$this->date       = '';
		$this->see         = '';
		$this->display     = '';
		$this->isbd_entry  = '';
		$this->author_web = '' ;
		$this->author_isni = '' ;
		$this->author_comment = '' ;
		$this->subdivision = '';
		$this->lieu	= '';
		$this->salle = '';
		$this->ville = '';
		$this->pays	= '';
		$this->numero = '';
		$this->info_bulle= '';
		if ($this->id) {
			$requete = "SELECT * FROM authors WHERE author_id='".addslashes($this->id)."' LIMIT 1 ";
			$result = @pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id       = $row->author_id;
				$this->type     = $row->author_type;
				$this->name     = $row->author_name;
				$this->rejete   = $row->author_rejete;
				$this->date     = $row->author_date;
				$this->see      = $row->author_see;
				$this->author_web = $row->author_web;
				$this->author_isni = $row->author_isni;
				$this->author_comment = $row->author_comment;
				//Ajout pour les congrès
				$this->subdivision	= $row->author_subdivision	;
				$this->lieu	= $row->author_lieu	;
				$this->ville = $row->author_ville	;
				$this->pays	= $row->author_pays	;
				$this->numero = $row->author_numero	;
				if($this->type==71 ) {
					// C'est une collectivité
					if($this->subdivision) {
						$this->isbd_entry = $this->name." ".$this->subdivision;
						$this->display = $this->name.", ".$this->subdivision;
					} else {
						$this->isbd_entry = $this->name;
						$this->display = $this->name;
					}
					if($this->rejete ) {
						$this->info_bulle=$this->rejete;
					}
					$liste_field=$liste_lieu=array();
					if($this->numero) {
						$liste_field[]=	$this->numero;
					}
					if($this->date) {
						$liste_field[]=	$this->date;
					}
					if($this->lieu) {
						$liste_lieu[]=	$this->lieu;
					}
					if($this->ville) {
						$liste_lieu[]=	$this->ville;
					}
					if($this->pays) {
						$liste_lieu[]=	$this->pays;
					}
					if(count($liste_lieu))	$liste_field[]=	implode(", ",$liste_lieu);
					if(count($liste_field))	{
						$liste_field=implode("; ",$liste_field);
						$this->isbd_entry .= ' ('.$liste_field.')';
						$this->display .= ' ('.$liste_field.')';
					}
				} elseif( $this->type==72) {
					// C'est un congrès
					$libelle=$msg["congres_libelle"].": ";
				
					if($this->rejete) {
						$this->isbd_entry = $libelle.$this->name." ".$this->rejete;
						$this->display = $libelle.$this->name." ".$this->rejete;
					} else {
						$this->isbd_entry = $this->name;
						$this->display = $this->name;
					}
					$liste_field=$liste_lieu=array();
					if($this->subdivision) {
						$liste_field[]=	$this->subdivision;
					}
					if($this->numero) {
						$liste_field[]=	$this->numero;
					}
					if($this->date) {
						$liste_field[]=	$this->date;
					}
					if($this->lieu) {
						$liste_lieu[]=	$this->lieu;
					}
					if($this->ville) {
						$liste_lieu[]=	$this->ville;
					}
					if($this->pays) {
						$liste_lieu[]=	$this->pays;
					}
					if(count($liste_lieu))	$liste_field[]=	implode(", ",$liste_lieu);
					if(count($liste_field))	{
						$liste_field=implode("; ",$liste_field);
						$this->isbd_entry .= ' ('.$liste_field.')';
						$this->display .= ' ('.$liste_field.')';
					}
				} else {
					// C'est un auteur physique
					if($this->rejete) {
						$this->isbd_entry = "$this->name, $this->rejete";
						$this->display = "$this->rejete $this->name";
					} else {
						$this->isbd_entry = "$this->name";
						$this->display = "$this->name";
					}
					if($this->date) $this->isbd_entry .= ' ('.$this->date.')';
				}
				if($this->author_web) $this->author_web_link = " <a href='$this->author_web' target='_blank' type='external_url_autor'><img src='".get_url_icon("globe.gif")."' style='border:0px' /></a>";
				else $this->author_web_link = "" ;
			}	
		}
	}
	
	public function get_similar_name($author_type='72',$from=0,$number=30) {
		global $dbh;
		if($author_type) $and_author_type = " and author_type='$author_type' ";
		$requete = "SELECT * FROM authors WHERE author_name='".$this->name."' and author_id != ".$this->id." $and_author_type order by author_date, author_lieu  LIMIT $from, $number";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			$i=0;
			while(($obj = pmb_mysql_fetch_object($result))) {
				$this->similar_name[$i]->id       = $obj->author_id;
				$this->similar_name[$i]->type     = $obj->author_type;
				$this->similar_name[$i]->name     = $obj->author_name;
				$this->similar_name[$i]->rejete   = $obj->author_rejete;
				$this->similar_name[$i]->date     = $obj->author_date;
				$this->similar_name[$i]->see      = $obj->author_see;
				$this->similar_name[$i]->author_web = $obj->author_web;
				$this->similar_name[$i]->author_isni = $obj->author_isni;
				$this->similar_name[$i]->author_comment = $obj->author_comment;
				$this->similar_name[$i]->subdivision	= $obj->author_subdivision	;
				$this->similar_name[$i]->lieu	= $obj->author_lieu	;
				$this->similar_name[$i]->ville = $obj->author_ville	;
				$this->similar_name[$i]->pays	= $obj->author_pays	;
				$this->similar_name[$i]->numero = $obj->author_numero;
				$requete = "SELECT count(distinct responsability_notice) FROM responsability WHERE responsability_author=".$this->similar_name[$i]->id;			
				$res_count = pmb_mysql_query($requete);			
				if ($res_count) $this->similar_name[$i]->nb_notice =  pmb_mysql_result($res_count,0,0); 
				else $this->similar_name[$i]->nb_notice=0;
				$i++;		
			}
		}	
	}
	public function print_similar_name($nb_by_line=3) {
		// Template
		global $base_path,
			$author_display_similar_congres, 
			$author_display_similar_congres_ligne, 
			$author_display_similar_congres_element;
		
		$nb=count($this->similar_name);	
		$congres="";
		for($i=0;$i<$nb;$i++) {
			$data=$this->similar_name[$i];
			
			$label= $data->numero." ".$data->date." ".$data->lieu;
			$detail= "";
			if($this->type!=71)	$detail.= $data->rejete." ";
			$detail.= $data->subdivision." "
			.$data->salle." "
			.$data->ville." "
			.$data->pays;
			if($data->nb_notice) {
				$detail.=" (".$data->nb_notice.")";
				$img_folder="<img src='".get_url_icon('folder_search.gif')."' style='border:0px' align='absmiddle'>";	
			}else {
				$img_folder="<img src='".get_url_icon('folder.gif')."' style='border:0px' align='absmiddle'>";
			}
			
			$congres_element = str_replace("!!congres_label!!",$label, $author_display_similar_congres_element);
			$congres_element = str_replace("!!img_folder!!",$img_folder, $congres_element);
			$congres_element = str_replace("!!congres_id!!",$data->id, $congres_element);
			$congres_element = str_replace("!!congres_detail!!",$detail, $congres_element);
			$congres_ligne.=$congres_element;  
			if(!(($i+1)%$nb_by_line) || (($i+1)==$nb)) {
				$congres.= str_replace("!!congres_ligne!!",$congres_ligne, $author_display_similar_congres_ligne);	
				$congres_ligne='';
			} 
		}
		if ($nb) $congres_contens= str_replace("!!congres_contens!!",$congres, $author_display_similar_congres);
		return 	$congres_contens;
		
	}
	public function print_congres_titre() {
		$print=$this->name;
		if($this->type==71 && $this->subdivision) {
			// Collectivité
			$print.= " ".$this->subdivision;  
		}
		elseif($this->rejete) {
			$print.= " ".$this->rejete;
		}		
		$liste_field=$liste_lieu=array();
		if($this->subdivision && !$this->type==71) {
			$liste_field[]=	$this->subdivision;
		}
		if($this->numero) {
			$liste_field[]=	$this->numero;
		}				
		if($this->date) {
			$liste_field[]=	$this->date;
		}
		if($this->lieu) {
			$liste_lieu[]=	$this->lieu;
		}
		if($this->ville) {
			$liste_lieu[]=	$this->ville;
		}	
		if($this->pays) {
			$liste_lieu[]=	$this->pays;
		}
		if(count($liste_lieu))	$liste_field[]=	implode(", ",$liste_lieu);
		if(count($liste_field))	{
			$liste_field=implode("; ",$liste_field);
			$print .= ' > '.$liste_field;
		}	
		return $print;
		
	}
	// ---------------------------------------------------------------
	public function print_resume($level = 2,$css='') {
		global $css;
		if(!$this->id)
			return;
	
		// adaptation par rapport au niveau de détail souhaité
		switch ($level) {
			// case x :
			case 1 :
				global $author_level1_display;
				global $author_level1_no_dates_info;
	
				$author_display = $author_level1_display;
				$author_no_dates_info = $author_level1_no_dates_info;
				break;
	
			case 2 :
			default :
				global $author_level2_display;
				global $author_level2_no_dates_info;
				global $author_level2_display_congres;
				
				if($this->type==72) {
					$author_display = $author_level2_display_congres;
				} else {
					$author_display = $author_level2_display;
				}
				
				$author_no_dates_info = $author_level2_no_dates_info;
			break;
		}
	
		$print = $author_display;
	
		// remplacement des champs statiques
		$print = str_replace("!!id!!", $this->id, $print);
		$print = str_replace("!!name!!", $this->name, $print);
		$print = str_replace("!!rejete!!", $this->rejete, $print);
		$print = str_replace("!!lieu!!", $this->lieu, $print);
		$print = str_replace("!!ville!!", $this->lieu, $print);
		$print = str_replace("!!pays!!", $this->pays, $print);
		$print = str_replace("!!numero!!", $this->numero, $print);
		$print = str_replace("!!subdivision!!", $this->subdivision, $print);
		if ($this->author_web) $print = str_replace("!!site_web!!", "<a href='$this->author_web' target='_blank' type='external_url_autor'><img src='".get_url_icon("globe.gif")."' style='border:0px' /></a>", $print);
		else $print = str_replace("!!site_web!!", "", $print);
		$print = str_replace("!!isni!!", $this->author_isni, $print);
		$print = str_replace("!!date!!", $this->date, $print);
		$print = str_replace("!!aut_comment!!", nl2br($this->author_comment), $print);
	
		// remplacement des champs dynamiques
		if ((preg_match("#!!allname!!#", $print)) || (preg_match("#!!allnamenc!!#", $print))) {
			if($this->type==71) {
				// Collectivité
				$remplacement = $this->name;
				if ($this->subdivision) $remplacement = $remplacement." ".$this->subdivision;
				if($this->rejete ) {
					 $this->info_bulle=$this->rejete; 
				}
				$liste_field=$liste_lieu=array();
				if($this->numero) {
					$liste_field[]=	$this->numero;
				}				
				if($this->date) {
					$liste_field[]=	$this->date;
				}
				if($this->lieu) {
					$liste_lieu[]=	$this->lieu;
				}
				if($this->ville) {
					$liste_lieu[]=	$this->ville;
				}	
				if($this->pays) {
					$liste_lieu[]=	$this->pays;
				}
				if(count($liste_lieu))	$liste_field[]=	implode(", ",$liste_lieu);
				if(count($liste_field))	{
					$liste_field=implode("; ",$liste_field);
					$remplacement .= ' ('.$liste_field.')';
				}	
			} elseif($this->type==72) {
				// Congrès
				$remplacement = $this->name;
				if ($this->rejete != "") $remplacement = $remplacement." ".$this->rejete;
				$liste_field=$liste_lieu=array();
				if($this->subdivision) {
					$liste_field[]=	$this->subdivision;
				}			
				if($this->numero) {
					$liste_field[]=	$this->numero;
				}				
				if($this->date) {
					$liste_field[]=	$this->date;
				}
				if($this->lieu) {
					$liste_lieu[]=	$this->lieu;
				}
				if($this->ville) {
					$liste_lieu[]=	$this->ville;
				}	
				if($this->pays) {
					$liste_lieu[]=	$this->pays;
				}
				if(count($liste_lieu))	$liste_field[]=	implode(", ",$liste_lieu);
				if(count($liste_field))	{
					$liste_field=implode("; ",$liste_field);
					$remplacement .= ' ('.$liste_field.')';
				}	
			} else {
				// auteur physique
				$remplacement = $this->name;
				if ($this->rejete != "") $remplacement = $this->rejete." ".$remplacement;			
			}	
			if (preg_match("#!!allname!!#", $print)) {
				$remplacement = "<a href='index.php?lvl=author_see&id=$this->id' title='".$this->info_bulle."'>$remplacement</a>";
				$print = str_replace("!!allname!!", $remplacement, $print);
			} else $print = str_replace("!!allnamenc!!", $remplacement, $print);
		}
	
		if (preg_match("#!!dates!!#", $print)) {
			if ($this->date != "") {
				$remplacement = " ($this->date)";
				} else $remplacement = $author_no_dates_info;
			$print = str_replace("!!dates!!", $remplacement, $print);
		}
	
		return $print;
	}
		
	public function get_enrichment() {
		global $dbh;
		global $charset;
		
		if($this->enrichment===null){
			/*$query="SELECT author_enrichment FROM authors WHERE author_id='".addslashes($this->id)."'";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows ( $result )) {
				$this->enrichment = unserialize(pmb_mysql_result ( $result, 0, 0 ));
			}*/
			
	// 		liste  des oeuvres qui ont au moins une notice dans la base
			if(isset($this->enrichment['biblio'])){
				$index=0;
				foreach ($this->enrichment['biblio'] as $work){
					if($work['tab_isbn']){
						$tab_isbn = implode(',',$work['tab_isbn']);
						$sql = "SELECT notice_id FROM notices WHERE code IN($tab_isbn)";
						$res = pmb_mysql_query($sql, $dbh);
						if ($res) {
							while($notice=pmb_mysql_fetch_object($res)){
								if($notice->notice_id){
									$this->enrichment['biblio'][$index]['notice_id']=$notice->notice_id;
									break;
								}
							}
						}
					}
					$index++;
				}
			}
			//tri des auteurs liés
			
			if (isset($this->enrichment['movement'])){
				$index=0;
				foreach ($this->enrichment['movement'] as $mvt){
					$index_author=0;
					foreach ($mvt['authors'] as $author){
						$query = "SELECT num_authority from authorities_sources WHERE authority_number='" . $author['id_bnf'] . " ' ";
						$result = @pmb_mysql_query ( $query, $dbh );
						if (pmb_mysql_num_rows ( $result )) {
							$this->enrichment['movement'][$index]['authors'][$index_author]['pmb_id']=pmb_mysql_result ( $result, 0, 0 );
						}
						$index_author++;
					} 
				$index++;
				}
			}
			
			if (isset($this->enrichment['genre'])){
				$index=0;
				foreach ($this->enrichment['genre'] as $gen){
					$index_author=0;
						foreach ($gen['authors'] as $author){
						$query = "SELECT num_authority from authorities_sources WHERE authority_number='" . $author['id_bnf'] . " ' ";
						$result = pmb_mysql_query ( $query, $dbh );
						if (pmb_mysql_num_rows ( $result )) {
							$this->enrichment['genre'][$index]['authors'][$index_author]['pmb_id']=pmb_mysql_result ( $result, 0, 0 );
						}
						$index_author++;
					}
					$index++;
				}
			}			
		}
		return $this->enrichment;
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_isbd() {
		return $this->isbd_entry;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_auteur']);
	}
	
	public function get_comment() {
		return $this->author_comment;
	}
	
	/**
	 * Renvoie le tableau des identifiants de l'auteur (comprenant les identifiants de renvoi)
	 */
	public function get_author_ids() {
		if (isset($this->author_ids)) {
			return $this->author_ids;
		}
		
		$this->author_ids = array($this->id);
		$query = 'select author_id as aut from authors where author_see = '.$this->id.' and author_id != 0'.
				' union select author_see as aut from authors where author_id = '.$this->id.' and author_see != 0';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$this->author_ids[] = $row->aut;
				$query = 'select author_id as aut from authors where author_see = '.$row->aut.' and author_id != 0';
				$result2 = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result2)) {
					while ($row2 = pmb_mysql_fetch_object($result2)) {
						$this->author_ids[] = $row2->aut;
					}
				}
			}
		}
		$this->author_ids = array_unique($this->author_ids);
		return $this->author_ids;
	}
	
	public function get_author_see() {
		global $dbh;
		
		if (isset($this->author_see)) {
			return $this->author_see;
		}
		
		$this->author_see = '';
		$this->get_author_ids();
		foreach ($this->author_ids as $author_id) {
			if ($author_id == $this->id) {
				continue;
			}
			//$authority = new authority(0, $author_id, AUT_TABLE_AUTHORS);
			$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $author_id, 'type_object' => AUT_TABLE_AUTHORS]);
			/* @var $author auteur */
			$author = $authority->get_object_instance();
			if (!$this->author_see) {
				$this->author_see = $author->get_isbd();
				continue;
			}
			$this->author_see.= ', ('.$author->get_isbd().')';
		}
		return $this->author_see;
	}
	
	public function get_author_see_details() {
		if (isset($this->author_see_details)) {
			return $this->author_see_details;
		}
	
		$this->author_see_details = array();
		$this->get_author_ids();
		foreach ($this->author_ids as $author_id) {
			if ($author_id == $this->id) {
				continue;
			}
			$authority = new authority(0, $author_id, AUT_TABLE_AUTHORS);
			/* @var $author auteur */
			$author = $authority->get_object_instance();
			$this->author_see_details[] = array(
					'id' => $author_id,
					'isbd' => $author->get_isbd()
			);
		}
		return $this->author_see_details;
	}
	
	/**
	 * Retourne le rendu HTML des documents numériques auxquels l'auteur est associé
	 */
	public function get_associated_explnums() {
		if (isset($this->associated_explnums)) {
			return $this->associated_explnums;
		}
		
		$this->associated_explnums = '';
		$query = "select distinct explnum_speaker_explnum_num from explnum_speakers where explnum_speaker_author in (".implode(',',$this->get_author_ids()).')';
		$result = pmb_mysql_query($query, $dbh);
		$docnum_associate = "";
		if (pmb_mysql_num_rows($result)) {
			$docnum_associate = pmb_bidi("<h3>".$msg['author_see_explnum_associate']."</h3>\n");
			while ($explnum = pmb_mysql_fetch_object($result)) {
				$docnum_associate.= "<div>".show_explnum_per_id($explnum->explnum_speaker_explnum_num)."</div>";
			}
		}
		return $this->associated_explnums;
	}
	
	public function get_header() {
		return $this->display;
	}
	
	public function format_datas($antiloop = false){
		$see_datas = array();
		if(!$antiloop) {
			if($this->see) {
				$see = new auteur($this->see);
				$see_datas = $see->format_datas(true);
			}
		}
		$formatted_data = array(
				'type' => $this->type,
				'name' => $this->name,
				'rejete' => $this->rejete,
				'date' => $this->date,
				'lieu' => $this->lieu,
				'ville' => $this->ville,
				'pays' => $this->pays,
				'subdivision' => $this->subdivision,
				'numero' => $this->numero,
				'see' => $see_datas,
		        'web' => $this->author_web,
		        'isni' => $this->author_isni,
				'comment' => $this->author_comment
		);
		$formatted_data = array_merge($this->get_authority()->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public function get_see_data(){
		if(!isset($this->see_data)){
			$this->see_data = '';
			if($this->see){
				//$this->see_data = new authority(0, $this->see, AUT_TABLE_AUTHORS);
				$this->see_data = authorities_collection::get_authority('authority', 0, ['num_object' => $this->see, 'type_object' => AUT_TABLE_AUTHORS]);
			}
		}
		return $this->see_data;
	}
	
	public function get_p_perso() {
		if(!isset($this->p_perso)) {
			$this->p_perso = $this->get_authority()->get_p_perso();
		}
		return $this->p_perso;
	}
	
	public function get_authority() {
		return authorities_collection::get_authority('authority', 0, ['num_object' => $this->id, 'type_object' => AUT_TABLE_AUTHORS]);
	}
} # fin de définition de la classe auteur

} # fin de délaration


