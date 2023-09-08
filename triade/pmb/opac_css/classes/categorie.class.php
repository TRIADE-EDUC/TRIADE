<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categorie.class.php,v 1.43 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// OPAC. Classe d'affichage des catégories

require_once ($base_path.'/classes/thesaurus.class.php');
require_once ($base_path.'/classes/noeuds.class.php');
require_once ($base_path.'/classes/categories.class.php');

class categorie {
	
	public $id				= 	0;		// id de la catégorie
	public $libelle		=	'';		// libellé de la catégorie
	public $parent			=	0;		// id parent
	public $voir			=	0;		// id renvoi
	public $has_child		= 	0;		// nombre d'enfants de la catégorie
	public $has_notices	=	0;		// nombre de notices utilisant la catégorie
	public $thes;						// thésaurus lié à la catégorie
	public $comment;	
	public $note;
	/**
	 * Rendu HTML du fil d'Arianne
	 * @var string
	 */
	protected $breadcrumb;
	
	/**
	 * Tableau des synonymes de la catégories
	 * @var array
	 */
	protected $synonyms;
	
	/**
	 * Instance du renvoi voir
	 * @var authority
	 */
	protected $categ_see;
	
	/**
	 * Tableau des renvois voir aussi
	 * @var array
	 */
	protected $see_also;

	
	// constructeur
	public function __construct($id) {
		$this->id = intval($id);
		if ($this->id) $this->get_data();
	}

	public function get_data() {
		
		global $dbh;
		global $categorie_separator;
		global $lang;
		
		// on récupère les infos de la catégorie
	
		$this->thes = thesaurus::getByEltId($this->id); 
		if (categories::exists($this->id, $lang)) $lg=$lang; else $lg=$this->thes->langue_defaut;
			
		$query = "select ";
		$query.= "categories.libelle_categorie,categories.note_application, categories.comment_public, ";
		$query.= "noeuds.num_parent, noeuds.num_renvoi_voir ";
		$query.= "from noeuds, categories ";
		$query.= "where categories.langue = '".$lg."' "; 
		$query.= "and noeuds.id_noeud = '".$this->id."' ";
		$query.= "and noeuds.id_noeud = categories.num_noeud ";
		$query.= "limit 1";
		$result = pmb_mysql_query($query, $dbh);
		
		if (pmb_mysql_num_rows($result)) {
    		$current = pmb_mysql_fetch_object($result);
    		$this->libelle 	= $current->libelle_categorie;
    		$this->parent	= $current->num_parent;
    		$this->voir		= $current->num_renvoi_voir;
    		$this->note		= $current->note_application;
    		$this->comment  = $current->comment_public;
		}
			
		// on regarde si la catégorie à des enfants
		$query = "select count(1) from noeuds where num_parent = '".$this->id."' ";
		$result = pmb_mysql_query($query, $dbh);
		$this->has_child = pmb_mysql_result($result, 0, 0);

		// on regarde si la catégorie à des associées
		$query = "select count(1) from voir_aussi where num_noeud_orig = '".$this->id."' or num_noeud_dest = '".$this->id."' ";
		$result = pmb_mysql_query($query, $dbh);
		$this->has_child = $this->has_child + pmb_mysql_result($result, 0, 0);

		// on regarde si la catégorie est utilisée dans des notices
		$query = "select count(1) from notices_categories where num_noeud = '".$this->id."' ";
		$result = pmb_mysql_query($query, $dbh);
		$this->has_notices = pmb_mysql_result($result, 0, 0);


	}


	public function categ_path($sep=' &gt; ',$css) {
	
		global $dbh;
		global $css;
		global $main;
		global $lang;
		
		if(!$this->id) return;
		
		$desc_categ = self::zoom_categ($this->id, $this->comment);
		$current = "$sep<a href='".static::format_url("index.php?lvl=categ_see&id=".$this->id.($main?"&main=".$main:""))."'".$desc_categ['java_com'].">".$this->libelle.'</a>'." ".$desc_categ['zoom'];
		// si pas de parent, le path se résume à la catégorie
		
		if(!$this->parent) return $current;
	
		// les parents sont mis en tableau
		$parent_id = $this->parent;
		$path_array = array();
		
		$path_array = categories::listAncestors($parent_id, $lang);
	
		$ret = '';
		foreach ($path_array as $cle => $valeur) {
			$ret .= $sep."<a href='".static::format_url("index.php?lvl=categ_see&id=${valeur['num_noeud']}".($main?"&main=".$main:""))."'>";
			$ret .= $valeur['libelle_categorie'].'</a>';
		}
		return $ret.$current;
	}
	
	public static function zoom_categ($id, $note) {
		global $charset;
		global $opac_show_infobulles_categ;
		
		if($opac_show_infobulles_categ) {
			if ($note) {
				$id.="_".md5(microtime(true));
				$zoom_com = "<div id='zoom".$id."' class='categmouseout' >";
				$zoom_com.= nl2br($note);
				$zoom_com.="</div>";
				$java_com = " onmouseover=\"y=document.getElementById('zoom".$id."'); y.className='categmouseover'; \" onmouseout=\"y=document.getElementById('zoom".$id."'); y.className='categmouseout'; \"" ;	
			} else {
				$zoom_com = "" ;
				$java_com = "" ;		
			}
			$result_zoom = array ('zoom' => $zoom_com, 'java_com' => $java_com);
		} else {
			$result_zoom = array ('zoom' => '', 'java_com' => '');
		}
		return $result_zoom;
	}

	public function child_list($image='./images/folder.gif') {
		global $dbh;
		global $opac_categories_nb_col_subcat, $opac_categories_sub_mode;
		global $main;
		global $lang;
		global $charset;
		global $base_path;
		$current_col = 0;	
			
		// récupération des enfants
		
		if ($this->id == $this->thes->num_noeud_racine) $result = categories::listChilds($this->id, $lang, 0, $opac_categories_sub_mode);
		else 
		$result = categories::listChilds($this->id, $lang, 1, $opac_categories_sub_mode);
		
		if(pmb_mysql_num_rows($result) < $opac_categories_nb_col_subcat) {

			// nombre de sous-catégories réduit
			while($child=pmb_mysql_fetch_object($result)) {
				$libelle = $child->libelle_categorie;
				$note = $child->comment_public;
				$id = $child->num_noeud;
				
					if($child->num_renvoi_voir) {
						$libelle = "<i>".$libelle."</i>@";
						$id = $child->num_renvoi_voir;
					} 
					 
					// Si il y a présence d'un commentaire affichage du layer					
					$result_com = self::zoom_categ($id, $note);				
					
					$l .= "<div><a href='".static::format_url("index.php?lvl=categ_see&id=$id".($main?"&main=".$main:""))."' class='folder small'>";
					
					if(category::has_notices($id))
						$l .= " <img src='".get_url_icon('folder_search.gif')."' alt='folder' style='border:0px' align='absmiddle' />";
					else
						$l .= "<img src='$image' alt='folder' style='border:0px' class='align_top' />";
				
					$l .="</a>".$result_com['zoom'];
					$l .= "<a href='".static::format_url("index.php?lvl=categ_see&id=$id".($main?"&main=".$main:""))."' class='small' ".$result_com['java_com'].">".$libelle."</a></div>";
				}
			$l = "<br /><div style='margin-left:48px'>$l</div>";
		} else {
				$l = "<table style='border:0px; margin-left:48px padding:3px'>";
				while($child=pmb_mysql_fetch_object($result)) {
					$libelle = $child->libelle_categorie;
					$note = $child->comment_public;
					$id = $child->num_noeud;
					
					if($child->num_renvoi_voir) {
						$libelle = "<i>".$libelle."</i>@";
						$id = $child->num_renvoi_voir;
					}
					// Si il y a présence d'un commentaire affichage du layer					
					$result_com = self::zoom_categ($id, $note);
					if ($current_col == 0) $l .= "\n<tr>";  
					$l .= "<td class='align_top'><a href='".static::format_url("index.php?lvl=categ_see&id=$id".($main?"&main=".$main:""))."' class='folder small'>";
					
					if(category::has_notices($id))
						$l .= " <img src='".get_url_icon('folder_search.gif')."' alt='folder' style='border:0px' align='absmiddle' />";		
					else		
						$l .= "<img src='$image' style='border:0px' alt='folder' class='align_top' />";

					$l .= "</a>".$result_com['zoom'];
					$l .= "<a href='".static::format_url("index.php?lvl=categ_see&id=$id".($main?"&main=".$main:""))."' class='small' ".$result_com['java_com'].">".$libelle."</a></td>";

					if ($current_col == $opac_categories_nb_col_subcat-1 ) {
						$l .= '</tr>';
						$current_col = 0;
					} else $current_col++;
				}
				$l .= '</table>';
			}
		return $l; 
	}

	public function get_db_id() {
		return $this->id;
	}
	
	public function get_isbd() {
		return $this->libelle;
	}
	
	public function get_permalink() {
		global $liens_opac;
		return str_replace('!!id!!', $this->id, $liens_opac['lien_rech_categ']);
	}
	
	public function get_comment() {
		return $this->comment;
	}
	
	/**
	 * Retourne le rendu HTML du fil d'Arianne dans le thésaurus
	 * @return string
	 */
	public function get_breadcrumb() {
		global $opac_thesaurus, $opac_categories_categ_path_sep, $css;
		if (isset($this->breadcrumb)) {
			return $this->breadcrumb;
		}
		$this->breadcrumb = '';
		if ($opac_thesaurus) {
			$this->breadcrumb = "<a href=\"".static::format_url("index.php?lvl=categ_see&id=".$this->thes->num_noeud_racine)."\">".$this->thes->libelle_thesaurus."</a>";
		}
		else {
			$this->breadcrumb = "<a href=\"".static::format_url("index.php?lvl=categ_see&id=".$this->thes->num_noeud_racine)."\"><img src='".get_url_icon("home.gif")."' style='border:0px'></a>";
		}
		$this->breadcrumb.= pmb_bidi($this->categ_path($opac_categories_categ_path_sep,$css));
		$this->breadcrumb = '<span class="fil_ariane">'.$this->breadcrumb.'</span>';
		return $this->breadcrumb;
	}
	
	/**
	 * Retourne le tableau des synonymes de la catégories
	 * @return array
	 */
	public function get_synonyms() {
		global $lang;
		
		if (isset($this->synonyms)) {
			return $this->synonyms;
		}
		$this->synonyms = array();
		$synonymes = categories::listSynonymes($this->id, $lang);
		while($row = pmb_mysql_fetch_object($synonymes)){
			$this->synonyms[] =$row->libelle_categorie;
		}
		return $this->synonyms;
	}
	
	/**
	 * Renvoie l'instance du renvoi voir
	 * @return authority
	 */
	public function get_categ_see() {
		if (isset($this->categ_see)) {
			return $this->categ_see;
		}
		$this->categ_see = null;
		if ($this->voir) {
			//$this->categ_see = new authority(0, $this->voir, AUT_TABLE_CATEG);
			$this->categ_see = authorities_collection::get_authority('authority', 0, ['num_object' => $this->voir, 'type_object' => AUT_TABLE_CATEG]);
		}
		return $this->categ_see;
	}
	
	/**
	 * Renvoie le tableau des renvois voir aussi
	 * @return string
	 */
	public function get_see_also() {
		global $lang, $opac_categories_max_display;
		
		if (isset($this->see_also)) {
			return $this->see_also;
		}
		$this->see_also = array();
		$query = "select ";
		$query.= "distinct catdef.num_noeud,catdef.note_application, catdef.comment_public,";
		$query.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as libelle_categorie ";
		$query.= "from voir_aussi left join noeuds on noeuds.id_noeud=voir_aussi.num_noeud_dest ";
		$query.= "left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
		$query.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$query.= "where ";
		$query.= "voir_aussi.num_noeud_orig = '".$this->id."' ";
		$query.= "order by libelle_categorie limit ".$opac_categories_max_display;
		
		$found_see_too = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($found_see_too)) {
			while (($mesCategories_see_too = pmb_mysql_fetch_object($found_see_too))) {
				$mesCategories_see_too->zoom  = categorie::zoom_categ($mesCategories_see_too->num_noeud, $mesCategories_see_too->comment_public);
				$mesCategories_see_too->has_notice = category::has_notices($mesCategories_see_too->num_noeud);
				$this->see_also[] = $mesCategories_see_too;
			}
		}
		return $this->see_also;
	}
	
	public static function format_url($url) {
		global $base_path;
		global $use_opac_url_base, $opac_url_base;
	
		if($use_opac_url_base) return $opac_url_base.$url;
		else return $base_path.'/'.$url;
	}
}
