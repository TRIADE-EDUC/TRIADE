<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.class.php,v 1.90.2.2 2024/11/20 08:07:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'
if ( ! defined( 'CATEGORY_CLASS' ) ) {
  define( 'CATEGORY_CLASS', 1 );

  global $base_path, $class_path;

require_once($class_path."/thesaurus.class.php");
require_once($base_path."/javascript/misc.inc.php");

require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/aut_pperso.class.php");
require_once("$class_path/XMLlist.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map/map_edition_controler.class.php");
require_once("$class_path/aut_link.class.php");

//Renvoi récursivement la liste des notices référançant un noeuds et ses enfants
function get_category_notice_count($node_id, &$listcontent) {
	//On ajoute les notices du noeuds
	$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		$listcontent[] = $arow[0];
	}

	//Et on recurse
	$asql = "SELECT id_noeud FROM noeuds WHERE num_parent = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		get_category_notice_count($arow[0], $listcontent);
	}
}

class category {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $id = 0;
	public $autorite = '';
	public $libelle = '';
	public $commentaire = '';
	public $catalog_form = ''; // forme pour affichage complet
	public $isbd_entry_lien_gestion = ''; // pour affichage avec lien vers la gestion
	public $parent_id = 0;
	public $parent_libelle = '';
	public $voir_id = 0;
	public $has_link = FALSE;
	public $has_child = FALSE;
	public $has_parent = FALSE;
	public $path_table = array();	// tableau contenant le path éclaté (ids et libellés)
	public $associated_terms = array(); // tableau des termes associés
	public $is_under_tilde = 0; // Savoir si c'est sous une catégorie qui commence par un ~
	public $thes;		//le thesaurus d'appartenance
	public $import_denied = 0;
	public $not_use_in_indexation = 0; //Savoir si l'on peut utiliser le terme en indexation
	public $list_see = array();
	protected $listchilds;
	protected static $controller;
	protected $parent;

	/**
	 * Carte associée
	 * @var map_objects_controler
	 */
	private $map = null;

	/**
	 * Info de la carte associée
	 * @var map_info
	 */
	private $map_info = null;


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
		global $opac_url_base, $use_opac_url_base;
		global $thesaurus_categories_show_only_last ; // le paramètre pour afficher le chemin complet ou pas
		$anti_recurse=array();

		if(!$this->id) return;

		$requete = "SELECT noeuds.id_noeud as categ_id, autorite, ";
		$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
		$requete.= "noeuds.num_parent as categ_parent, ";
		$requete.= "noeuds.num_renvoi_voir as categ_see, ";
		$requete.= "noeuds.not_use_in_indexation as not_use_in_indexation, ";
		$requete.= "noeuds.authority_import_denied as authority_import_denied, ";
		$requete.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as categ_comment ";
		$requete.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
		$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$requete.= "where noeuds.id_noeud = '".$this->id."' limit 1 ";

		$result = pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) return;

		$data = pmb_mysql_fetch_object($result);
		pmb_mysql_free_result($result);

		$this->id = $data->categ_id;
		$this->autorite = $data->autorite;
		$id_top = $this->thes->num_noeud_racine;
		$this->libelle = $data->categ_libelle;
		if(preg_match("#^~#",$this->libelle)){
			$this->is_under_tilde=1;
		}
		$this->commentaire = $data->categ_comment;
		$this->parent_id = $data->categ_parent;
		$this->voir_id = $data->categ_see;
		$this->import_denied = $data->authority_import_denied;
		$this->not_use_in_indexation = $data->not_use_in_indexation;
		//$anti_recurse[$this->voir_id]=1;
		if($this->parent_id != $id_top) $this->has_parent = TRUE;

		// on regarde si la catégorie à des enfants
		$query = "SELECT 1 FROM noeuds WHERE num_parent='".$this->id."' limit 1";
		$result = pmb_mysql_query($query);
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
					pmb_mysql_free_result($result);

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
			} while (($parent->categ_parent != $id_top) &&(!$anti_recurse[$parent->categ_parent]));
		}

		// ceci remet le tableau dans l'ordre général->particulier
		$this->path_table = array_reverse($this->path_table);

		if ($thesaurus_categories_show_only_last) {
			$this->catalog_form = $this->libelle;

			// si notre catégorie a un parent, on initie la boucle en le récupérant
			/*
			$requete_temp = "SELECT noeuds.id_noeud as categ_id, ";
			$requete_temp.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle ";
			$requete_temp.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$requete_temp.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$requete_temp.= "where noeuds.id_noeud = '".$this->parent_id."' limit 1 ";

			ER 12/08/2008 NOUVELLE VERSION OPTIMISEE DESSOUS : */
			$requete_temp = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$this->parent_id."'
				AND noeuds.id_noeud = categories.num_noeud
				order by p desc limit 1";

			$result_temp=pmb_mysql_query($requete_temp);
			if (pmb_mysql_num_rows($result_temp)) {
				$parent = pmb_mysql_fetch_object($result_temp);
				$this->parent_libelle = $parent->categ_libelle ;
			} else $this->parent_libelle ;

		} elseif (!empty($this->path_table)) {
			$temp_table = array();
		    foreach ($this->path_table as $l) {
				$temp_table[] = $l['libelle'];
			}
			$this->parent_libelle = implode(':', $temp_table);
			$this->catalog_form = $this->parent_libelle.':'.$this->libelle;
		} else {
			$this->catalog_form = $this->libelle;
		}

		// Ajoute un lien sur la fiche catégorie si l'utilisateur à accès aux autorités, ou bien en envoi en OPAC.
		if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
		else $url_base_lien_aut = static::format_url("&sub=categ_form&id=");
		if ((defined("SESSrights") && (SESSrights & AUTORITES_AUTH)) || $use_opac_url_base) $this->isbd_entry_lien_gestion = "<a href='".$url_base_lien_aut.$this->id."' class='lien_gestion'>".$this->catalog_form."</a>";
		else $this->isbd_entry_lien_gestion = $this->catalog_form;

		//Recherche des termes associés
		$requete = "select count(1) from categories where num_noeud = '".$this->id."' and langue = '".$lang."' ";
		$result = pmb_mysql_query($requete);
		if (pmb_mysql_result($result, 0,0) == 0) $lg = $this->thes->langue_defaut ;
		else $lg = $lang;

		$requete = "SELECT distinct voir_aussi.num_noeud_dest as categ_assoc_categassoc, ";
		$requete.= "categories.libelle_categorie as categ_libelle, categories.note_application as categ_comment ";
		$requete.= "FROM voir_aussi, categories ";
		$requete.= "WHERE voir_aussi.num_noeud_orig='".$this->id."' ";
		$requete.= "AND categories.num_noeud=voir_aussi.num_noeud_dest ";
		$requete.= "AND categories.langue = '".$lg."' ";

		$result=pmb_mysql_query($requete);
		while ($ta=pmb_mysql_fetch_object($result)) {

			//Recherche des renvois réciproques
			$requete1 = "select count(1) from voir_aussi where num_noeud_orig = '".$ta->categ_assoc_categassoc."' and num_noeud_dest = '".$this->id."' ";
			if (pmb_mysql_result(pmb_mysql_query($requete1), 0, 0)) $rec=1;
			else $rec=0;

			$this->associated_terms[] = array(
				'id' => $ta->categ_assoc_categassoc,
				'libelle' => $ta->categ_libelle,
				'commentaire' => $ta->categ_comment,
				'rec' => $rec);
		}
	}

	public function build_header_to_export() {
	    global $msg;

	    $data = array(
	        $msg[67],
	        $msg[707],
	        $msg['categ_parent'],
	        $msg['aut_categs_children'],
	        $msg['categ_renvoi'],
	    );
	    return $data;
	}

	public function build_data_to_export() {

	    $listchilds_display = '';
	    $this->listChilds();
	    foreach ($this->listchilds as $child) {
	        if ($listchilds_display) $listchilds_display.= '; ';
	        $listchilds_display.= $child['libelle'];
	    }
	    $renvoivoir = $this->get_renvoivoir();
	    $data = array(
	        $this->libelle,
	        $this->commentaire,
	        $this->parent_libelle,
	        $listchilds_display,
	        $renvoivoir['libelle'],
	    );
	    return $data;
	}

	/**
	 * catégorie parente
	 */
	public function get_form_categ_parent() {
		global $charset;
		global $form_categ_parent;
		global $parent;

		if($this->id) {
			$p_value = $this->parent_id;
			$p_libelle = $this->parent_libelle;
		} else {
			if($parent) {
				$pr = new category($parent);
				$p_value = $pr->id;
				$p_libelle = $pr->catalog_form;
			} else {
				$p_value = 0;
				$p_libelle = '';
			}
		}
		$form = $form_categ_parent;
		$form = str_replace('!!parent_value!!', $p_value, $form);
		$form = str_replace('!!id_thes!!', $this->thes->id_thesaurus, $form);
		$form = str_replace('!!id_noeud!!', $this->id, $form);
		$form = str_replace('!!parent_libelle!!', htmlentities($p_libelle,ENT_QUOTES, $charset), $form);
		return $form;
	}

	/**
	 * renvoi voir
	 */
	public function get_renvoivoir() {
	    if ($this->id) {
	        $v_value = $this->voir_id;
	        if ($v_value) {
	            $voir = new category($v_value);
	            $v_libelle = $voir->catalog_form;
	        } else {
	            $v_libelle = "";
	        }
	    } else {
	        $v_value = 0;
	        $v_libelle = '';
	    }
	    return array(
	        'id'  => $v_value,
	        'libelle'  => $v_libelle
	    );
	}

	public function get_form_renvoivoir() {
		global $charset;
		global $form_renvoivoir;

		$form = $form_renvoivoir;
		$data = $this->get_renvoivoir();
		$form = str_replace('!!voir_value!!', $data['id'], $form);
		$form = str_replace('!!voir_libelle!!', htmlentities($data['libelle'],ENT_QUOTES, $charset), $form);
		return $form;
	}

	/**
	 * renvois voir aussi
	 */
	public function get_form_renvoivoiraussi() {
		global $add_see_also;
		global $form_renvoivoiraussi;
		global $categ0, $categ_add_button;
		global $parent;

		$see_also = array();
		if ($this->id) {
			$see_also = $this->associated_terms;
		}

		$categ0_rec = '';
		if (empty($see_also)) {
			$max_categ = 1;
			$categ0_id = 0;
			$categ0_lib = '';
		} else {
			$max_categ = count($see_also);
			$csa = new category($see_also[0]['id']);
			$categ0_id = $see_also[0]['id'];
			$categ0_lib = $csa->catalog_form;
			if (!empty($see_also[0]['rec'])) {
			    $categ0_rec = 'checked';
			}
		}

		$see_also_form = $add_see_also;
		$see_also_form .= "<input type='hidden' name='max_categ' value='$max_categ'/>\n";
		$categ_ = $categ0;
		$categ_ = str_replace("!!categ_libelle!!", $categ0_lib, $categ_);
		$categ_ = str_replace("!!categ_id!!", $categ0_id, $categ_);
		$categ_ = str_replace("!!icateg!!", "0", $categ_);
		$categ_ = str_replace("!!parent!!", $parent, $categ_);
		$categ_ = str_replace("!!chk!!", $categ0_rec, $categ_);

		$categ_sort = [];
		$categ_sort[$categ0_lib] = $categ_;
		if (is_array($see_also) && count($see_also)) {
			for ($i = 1; $i < count($see_also); $i++) {
				$csa = new category($see_also[$i]['id']);
				$categ_ = $categ0;
				$categ_ = str_replace("!!categ_libelle!!", $csa->catalog_form, $categ_);
				$categ_ = str_replace("!!categ_id!!", $see_also[$i]['id'], $categ_);
				$categ_ = str_replace("!!icateg!!", $i, $categ_);
				$categ_ = str_replace("!!parent!!", $parent, $categ_);
			    $categ_rec = "unchecked='unchecked'";
				if (!empty($see_also[$i]['rec'])) {
				    $categ_rec = "checked='checked'";
				}
				$categ_ = str_replace("!!chk!!", $categ_rec, $categ_);
				$categ_sort[$csa->catalog_form] = $categ_;
			}
			ksort($categ_sort);
		}
		$categ_last_key = array_key_last($categ_sort);
		$categ_sort[$categ_last_key] = str_replace("!!categ_add_button!!", $categ_add_button, $categ_sort[$categ_last_key]);
		$first = true;
		foreach ($categ_sort as $categ_aff) {
		    $categ_aff = str_replace("!!categ_add_button!!", "", $categ_aff);
		    $see_also_form .= $categ_aff."\n";
		    if ($first) {
		        $see_also_form .= "<div id='addcateg'>\n";
		        $first = false;
		    }
		}
		$see_also_form .= "</div>";

		return str_replace("!!renvoi_voir_aussi!!", $see_also_form, $form_renvoivoiraussi);
	}

	protected function get_content_link($tcateg, $odd_even=0) {
		global $charset;
		global $parent;

		$content = '';
		if ($odd_even==0) {
			$content .= "	<tr class='odd'>";
			$odd_even=1;
		} else if ($odd_even==1) {
			$content .= "	<tr class='even'>";
			$odd_even=0;
		}
		$notice_count = $tcateg->notice_count(false);

		$content .= "<td class='colonne80'>";
		if($tcateg->has_child) {
			$content .= "<a href='".static::format_url("&sub=&id=0&parent=".$tcateg->id)."'>";
			$content .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px; margin:3px 3px'></a>";
		} else {
			$content .= "<img src='".get_url_icon('doc.gif')."' style='border:0px; margin:3px 3px'>";
		}
		if ($tcateg->commentaire) {
			$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
			$zoom_comment.= htmlentities($tcateg->commentaire,ENT_QUOTES, $charset);
			$zoom_comment.="</div>";
			$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
		} else {
			$zoom_comment = "" ;
			$java_comment = "" ;
		}
		$content .= "<a href='".static::format_url("&sub=categ_form&parent=".$parent."&id=".$tcateg->id)."' $java_comment >";
		$content .= $tcateg->libelle;
		$content .= '</a>';
		$content .= $zoom_comment.'</td>';
		if($notice_count && $notice_count!=0)
			$content .= "<td style='cursor: pointer; width:20%; text-align:center;' onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=$tcateg->id'\">".$notice_count."</td>";
		else $content .= "<td>&nbsp;</td>";
		$content .='</tr>';
		return $content;
	}

	public function get_form_links() {
		global $msg;
		global $categories_liaison_tpl;

		$form_links = "";

		$categ_child_content="";
		if (noeuds::hasChild($this->id)) {
			$this->has_link = true;
			$odd_even=1;
			if ($res = noeuds::listChilds($this->id, 0)) {
				$categ_child_content .= "
				<div class='row'>
				<label for='' class='etiquette'>".$msg['categ_childs']."</label>
				</div>
				<div class='row'>
				<table>";

				$categs = array();
				while ($row = pmb_mysql_fetch_object($res)) {
					$categs[] = authorities_collection::get_authority(AUT_TABLE_CATEG,$row->id_noeud);
				}
				usort($categs, "usort_categs_array_by_libelle");

				foreach ($categs as $tcateg) {
					$categ_child_content .= $this->get_content_link($tcateg, $odd_even);
				}
				$categ_child_content .= "</table>
				</div>";
			}
		}
		$categ_renvoivoir_content="";
		if (noeuds::isTarget($this->id)){
			$this->has_link = true;
			$odd_even=1;
			if ($res = noeuds::listTargets($this->id)) {
				$categ_renvoivoir_content .= "
				<div class='row'>
				<label for='' class='etiquette'>".$msg['categ_renvoivoir']."</label>
				</div>
				<div class='row'>
				<table>";

				$categs = array();
				while ($row = pmb_mysql_fetch_object($res)) {
					$categs[] = authorities_collection::get_authority(AUT_TABLE_CATEG,$row->id_noeud);
				}
				usort($categs, "usort_categs_array_by_libelle");

				foreach ($categs as $tcateg) {
					$categ_renvoivoir_content .= $this->get_content_link($tcateg, $odd_even);
				}
				$categ_renvoivoir_content .= "</table>
				</div>";
			}
		}
		$categ_renvoivoiraussi_content="";
		//Voir aussi
		$requete="SELECT distinct num_noeud_orig AS id_noeud FROM voir_aussi WHERE num_noeud_dest='".$this->id."'";
		$res=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$this->has_link = true;
			$odd_even=1;
			$categ_renvoivoiraussi_content .= "
			<div class='row'>
			<label for='' class='etiquette'>".$msg['categ_renvoivoiraussi']."</label>
			</div>
			<div class='row'>
			<table>";

			$categs = array();
			while ($row = pmb_mysql_fetch_object($res)) {
				$categs[] = authorities_collection::get_authority(AUT_TABLE_CATEG,$row->id_noeud);
			}
			usort($categs, "usort_categs_array_by_libelle");

			foreach ($categs as $tcateg) {
			    $categ_renvoivoiraussi_content .= $this->get_content_link($tcateg, $odd_even);
			}
			$categ_renvoivoiraussi_content .= "</table>
			</div>";
		}

		if ($this->has_link) {
			$form_links=$categories_liaison_tpl;
			$form_links=str_replace("<!-- categ_child -->",$categ_child_content, $form_links);
			$form_links=str_replace("<!-- categ_renvoivoir -->",$categ_renvoivoir_content,$form_links);
			$form_links=str_replace("<!-- categ_renvoivoiraussi -->",$categ_renvoivoiraussi_content,$form_links);
		}
		return $form_links;
	}

	// ---------------------------------------------------------------
	// show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form() {
		global $msg, $charset;
		global $id_thes;
		global $include_path;
		global $parent;
		global $thesaurus_mode_pmb;
		global $category_form;
		global $lang;
		global $thesaurus_concepts_active;
		global $form_num_aut;
		global $pmb_type_audit;
		global $traduction_na_tpl, $traduction_cm_tpl;
		global $user_input, $nbr_lignes, $page, $nb_per_page_gestion;
		global $thesaurus_defaut;

		if(!$this->id) {
			//recuperation du thesaurus session
			if(!$id_thes) {
				$id_thes = thesaurus::getSessionThesaurusId();
			}
			if($id_thes == '-1') $id_thes = $thesaurus_defaut;
			thesaurus::setSessionThesaurusId($id_thes);

			$this->thes = new thesaurus($id_thes);
		}

		//Récuperation de la liste des langues définies pour l'interface
		$langages = new XMLlist("$include_path/messages/languages.xml", 1);
		$langages->analyser();
		$lg = $langages->table;

		//Récuperation de la liste des langues définies pour les thésaurus
		//autre que la langue par defaut du thesaurus
		$thes_liste_trad = thesaurus::getTranslationsList();
		$lg1 = array();
		foreach($thes_liste_trad as $item) {
			if ( ($item != $this->thes->langue_defaut) && ($lg[$item]!= '') )
				$lg1[$item] = $lg[$item];
		}

		if($this->id) {
			$title = $msg[318];
			$action = static::format_url("&sub=update&id=".$this->id."&parent=".$parent);
			$delete_button = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";

			$button_voir = "<input type='button' class='bouton' value='".$msg['voir_notices_assoc']."' ";
			$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=".$this->id."\"'>";

			// on récupère les données de la catégorie
			$c_form = '<p>'.$this->catalog_form.'</p>';

			//Non utilisisable en indexation
			$not_use_in_indexation=$this->not_use_in_indexation;

			//numero autorite
			$n=new noeuds($this->id);
			$num_aut=$n->autorite;
			$authority_statut=$n->num_statut;
			//import bloqué
			$import_denied = $n->authority_import_denied;
			if (noeuds::isProtected($this->id)) {
				$aff_node_info=false;
			} else {
				$aff_node_info=true;
			}
		} else {
			$action = static::format_url("&sub=update&id=".$this->id."&parent=".$parent);
			$delete_button = '';
			$button_voir = '';
			$title = $msg[319];
			$libelle = '';
			$c_form = '';
			$not_use_in_indexation = 0;
			$num_aut=0;
			$authority_statut=0;
			$aff_node_info=true;
			$import_denied = 0;
		}
		if ($thesaurus_mode_pmb != 0) {
		    $title.= ' ('.htmlentities($this->thes->libelle_thesaurus, ENT_QUOTES, $charset).')';
		    if(!$this->id) {
		        $thesaurus_action = static::format_url("&sub=categ_form&id=".$this->id."&parent=0");
		        if(strpos($thesaurus_action, '&action=update&') !== false) {
		            $thesaurus_action = str_replace('&action=update&', '&', $thesaurus_action);
                }
		        $category_form = str_replace('<!-- sel_thesaurus -->', thesaurus::getSelectorCategoryForm($this->thes->id_thesaurus, $thesaurus_action, true), $category_form);
		    } else {
		        $category_form = str_replace('<!-- sel_thesaurus -->', thesaurus::getSelectorCategoryForm($this->thes->id_thesaurus, '', false), $category_form);
		    }
		}

		//Traductions
		$tab_traductions = array();

		//Affichage des boutons de traduction
		$bt_lib_trad = '';
		$bt_cm_trad = '';
		$bt_na_trad = '';
		if ( count($lg1) > 0 ) {
			$bt_lib_trad = 	"<input type='button' class='bouton_small' value='".htmlentities(addslashes($msg['thes_traductions']), ENT_QUOTES, $charset)."' onclick=\"bascule_trad('lib_trad')\" />";
			$bt_cm_trad =  	"<input type='button' class='bouton_small' value='".htmlentities(addslashes($msg['thes_traductions']), ENT_QUOTES, $charset)."' onclick=\"bascule_trad('cm_trad')\" />";
			$bt_na_trad =  	"<input type='button' class='bouton_small' value='".htmlentities(addslashes($msg['thes_traductions']), ENT_QUOTES, $charset)."' onclick=\"bascule_trad('na_trad')\" />";
		}
		$category_form = str_replace('<!-- bt_lib_trad -->', $bt_lib_trad, $category_form);
		$category_form = str_replace('<!-- bt_cm_trad -->', $bt_cm_trad, $category_form);
		$category_form = str_replace('<!-- bt_na_trad -->', $bt_na_trad, $category_form);

		//On lit d'abord dans la langue par défaut du thesaurus
		if (categories::exists($this->id, $this->thes->langue_defaut)) {
			$c = new categories($this->id, $this->thes->langue_defaut);
			$libelle_categorie = $c->libelle_categorie;
			$note_application = $c->note_application;
			$commentaire = $c->comment_public;
		} else {
			$libelle_categorie = '';
			$note_application = '';
			$commentaire = '';
		}
		$tab_traductions [$this->thes->langue_defaut][0] = $lg[$this->thes->langue_defaut];
		$tab_traductions [$this->thes->langue_defaut][1] = $libelle_categorie;
		$tab_traductions [$this->thes->langue_defaut][2] = $note_application;
		$tab_traductions [$this->thes->langue_defaut][3] = $commentaire;

		//Ensuite, on regarde si les categories existent pour les langues de traduction	des thesaurus
		foreach($lg1 as $key=>$value){
			if (categories::exists($this->id, $key)) {
				$c = new categories($this->id, $key);
				$libelle_categorie = $c->libelle_categorie;
				$note_application = $c->note_application;
				$commentaire = $c->comment_public;
			} else {
				$libelle_categorie = '';
				$note_application = '';
				$commentaire = '';
			}
			$tab_traductions[$key][0] = $value;
			$tab_traductions[$key][1] = $libelle_categorie;
			$tab_traductions[$key][2] = $note_application;
			$tab_traductions[$key][3] = $commentaire;
		}


		//categories langue par defaut thesaurus
		$category_form = str_replace('!!lang_def_cle!!', htmlentities('['.$this->thes->langue_defaut.']', ENT_QUOTES, $charset), $category_form);
		$category_form = str_replace('!!lang_def!!', htmlentities(' ('.$tab_traductions[$this->thes->langue_defaut][0].') ', ENT_QUOTES, $charset), $category_form);
		$category_form = str_replace('!!lang_def_js!!', ' ('.$tab_traductions[$this->thes->langue_defaut][0].') ', $category_form);
		$category_form = str_replace('!!lang_def_libelle!!', htmlentities($tab_traductions[$this->thes->langue_defaut][1], ENT_QUOTES, $charset), $category_form);

		$label1 = "\t<div class='row'><label class='etiquette'>(";
		$label2 = ") </label></div>\n";
		$input1 = "\t<div class='row'><input type='text' class='saisie-80em' name='category_libelle[";
		$input2 = "]' value=\"";
		$input3 = "\" /></div>\n";


		//categories langue interface (si dans la liste des langues pour les thesaurus)
		if ( ($lang != $this->thes->langue_defaut) && ($lg1[$lang] != '') ) {
			$c_libelle_trad = $label1.htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset).$label2;
			$c_libelle_trad.= $input1.$lang.$input2.htmlentities($tab_traductions[$lang][1], ENT_QUOTES, $charset).$input3;
		} else {
			$c_libelle_trad = '';
		}

		//categories autres langues
		foreach($tab_traductions as $key=>$value) {
			if ($key != $this->thes->langue_defaut && $key != $lang) {
				$c_libelle_trad.= $label1.htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset).$label2;
				$c_libelle_trad.= $input1.$key.$input2.htmlentities($tab_traductions[$key][1], ENT_QUOTES, $charset).$input3;
			}
		}
		$category_form = str_replace('!!c_libelle_trad!!', $c_libelle_trad, $category_form);

		//Non utilisisable en indexation
		if($not_use_in_indexation == 1){
			$not_use_checked = "checked='checked'";
		}else{
			$not_use_checked = "";
		}
		$category_form = str_replace('!!not_use_in_indexation!!',$not_use_checked,$category_form);


		//note d'application langue par defaut thesaurus
		$category_form = str_replace('!!lang_def_na!!', htmlentities($tab_traductions[$this->thes->langue_defaut][2], ENT_QUOTES, $charset), $category_form);

		//commentaire langue par defaut thesaurus
		$category_form = str_replace('!!lang_def_cm!!', htmlentities($tab_traductions[$this->thes->langue_defaut][3], ENT_QUOTES, $charset), $category_form);

		$na_trad = "";
		$cm_trad = "";
		//note d'application et commentaire en langue de l'interface
		if ($lang != $this->thes->langue_defaut) {
			$na_trad = $traduction_na_tpl;
			$na_trad = str_replace('!!lang_value!!', htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset), $na_trad);
			$na_trad = str_replace('!!lang!!', $lang, $na_trad);
			$na_trad = str_replace('!!note_application!!', htmlentities($tab_traductions[$lang][2], ENT_QUOTES, $charset), $na_trad);
			$cm_trad = $traduction_cm_tpl;
			$cm_trad = str_replace('!!lang_value!!', htmlentities($tab_traductions[$lang][0], ENT_QUOTES, $charset), $cm_trad);
			$cm_trad = str_replace('!!lang!!', $lang, $cm_trad);
			$cm_trad = str_replace('!!commentaire!!', htmlentities($tab_traductions[$lang][3], ENT_QUOTES, $charset), $cm_trad);
		}

		//note d'application et commentaire autres langues
		foreach($tab_traductions as $key=>$value) {
			if ($key != $this->thes->langue_defaut && $key != $lang) {
				$temp_na_trad = $traduction_na_tpl;
				$temp_na_trad = str_replace('!!lang_value!!', htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset), $temp_na_trad);
				$temp_na_trad = str_replace('!!lang!!', $key, $temp_na_trad);
				$temp_na_trad = str_replace('!!note_application!!', htmlentities($tab_traductions[$key][2], ENT_QUOTES, $charset), $temp_na_trad);
				$na_trad .= $temp_na_trad;
				$temp_cm_trad = $traduction_cm_tpl;
				$temp_cm_trad = str_replace('!!lang_value!!', htmlentities($tab_traductions[$key][0], ENT_QUOTES, $charset), $temp_cm_trad);
				$temp_cm_trad = str_replace('!!lang!!', $key, $temp_cm_trad);
				$temp_cm_trad = str_replace('!!commentaire!!', htmlentities($tab_traductions[$key][3], ENT_QUOTES, $charset), $temp_cm_trad);
				$cm_trad .= $temp_cm_trad;
			}
		}
		$category_form = str_replace('!!na_trad!!', $na_trad, $category_form);
		$category_form = str_replace('!!cm_trad!!', $cm_trad, $category_form);

		$category_form = str_replace('!!action!!', $action, $category_form);
		$category_form = str_replace('!!cancel_action!!', static::format_back_url(), $category_form);
		$category_form = str_replace('!!id_parent!!', $parent, $category_form);
		$category_form = str_replace('!!form_title!!', $title, $category_form);
		$category_form = str_replace('!!category_comment!!', htmlentities($commentaire,ENT_QUOTES, $charset), $category_form);
		/**
		 * Gestion du selecteur de statut d'autorité
		 */
		$category_form = str_replace('!!auth_statut_selector!!', authorities_statuts::get_form_for(AUT_TABLE_CATEG, $authority_statut), $category_form);

		if ($tab_traductions[$this->thes->langue_defaut][1]) {
		    $category_form = str_replace('!!document_title!!', addslashes($tab_traductions[$this->thes->langue_defaut][1].' - '.$title), $category_form);
		} else {
		    $category_form = str_replace('!!document_title!!', addslashes($title), $category_form);
		}

		if ($aff_node_info) {
			$category_form = str_replace('<!--categ_parent -->', $this->get_form_categ_parent(), $category_form);
			$category_form = str_replace('<!-- renvoivoir -->', $this->get_form_renvoivoir() , $category_form);

			$category_form=str_replace("<!-- renvoivoiraussi -->",$this->get_form_renvoivoiraussi(),$category_form);

			//liaisons
			$category_form=str_replace("<!-- liaison -->", $this->get_form_links(),$category_form);

			//Numéro d'autorité
			$form_num_aut=str_replace("!!num_aut!!",$num_aut,$form_num_aut);
			$category_form=str_replace("<!-- numero_autorite -->",$form_num_aut,$category_form);

			// Indexation concepts
			if($thesaurus_concepts_active == 1 ){
				$index_concept = new index_concept($this->id, TYPE_CATEGORY);
				$category_form = str_replace('!!concept_form!!', $index_concept->get_form('categ_form'), $category_form);
			}else{
				$category_form = str_replace('!!concept_form!!', "", $category_form);
			}

			//Remplacement
			$button_remplace = "<input type='button' class='bouton' value='$msg[158]' ";
			$button_remplace .= "onclick='unload_off();document.location=\"".static::format_url("&sub=categ_replace&id=".$this->id."&parent=".$parent)."\"'/>";
			$category_form = str_replace("<!-- remplace_categ -->", $button_remplace, $category_form);

			//Suppression
			$category_form = str_replace('<!-- delete_button -->', $delete_button, $category_form);

		} else {
			$category_form=str_replace("<!-- numero_autorite -->",$num_aut,$category_form);
			$category_form = str_replace('!!concept_form!!', "", $category_form);
		}
		$authority = new authority(0, $this->id, AUT_TABLE_CATEG);
		$category_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $category_form);
		if($import_denied == 1 || !$this->id){
			$import_denied_checked = "checked='checked'";
		}else{
			$import_denied_checked = "";
		}
		$category_form = str_replace('!!authority_import_denied!!',$import_denied_checked,$category_form);

		$aut_link= new aut_link(AUT_TABLE_CATEG,$this->id);
		$category_form = str_replace('<!-- aut_link -->', $aut_link->get_form('categ_form') , $category_form);

		global $pmb_map_activate;
		if($pmb_map_activate){
			$map_edition=new map_edition_controler(AUT_TABLE_CATEG,$this->id);
			$map_form=$map_edition->get_form();
			$category_form = str_replace('<!-- map -->', $map_form , $category_form);
		}

		$aut_pperso= new aut_pperso("categ",$this->id);
		$category_form = str_replace('!!aut_pperso!!', $aut_pperso->get_form(), $category_form);

		$category_form = str_replace('!!voir_notices!!', $button_voir, $category_form);

		if($pmb_type_audit && $this->id) {
			$bouton_audit= audit::get_dialog_button($this->id, AUDIT_CATEG);
		} else {
			$bouton_audit= "";
		}
		$category_form = str_replace('!!audit_bt!!', $bouton_audit, $category_form);

		if ($aff_node_info && $this->id) {
			// Impression de la branche du thésaurus
			$button_impression_thesaurus="<input class='bouton' type='button' onClick=\"openPopUp('./print_thesaurus.php?current_print=2&action=print_prepare&aff_num_thesaurus=".$id_thes."&id_noeud_origine=".$this->id."','print'); return false;\" title=\"".$msg['print_branche']."\" value=\"".$msg['print_branche']."\" />";
		} else {
			$button_impression_thesaurus="";
		}
		$category_form=str_replace("<!-- imprimer_thesaurus -->",$button_impression_thesaurus,$category_form);

		$category_form = str_replace('!!user_input!!', htmlentities($user_input ?? "", ENT_QUOTES, $charset), $category_form);
		$category_form = str_replace('!!nbr_lignes!!', $nbr_lignes ?? "", $category_form);
		$category_form = str_replace('!!page!!', $page ?? "", $category_form);
		$category_form = str_replace('!!nb_per_page!!', $nb_per_page_gestion ?? "", $category_form);
		$category_form = str_replace('!!id!!', $this->id ?? "", $category_form);
		$category_form = str_replace('!!parent!!', $parent ?? "", $category_form);
		$category_form = str_replace('!!controller_url_base!!', static::format_url() ?? "", $category_form);
		$category_form = str_replace('!!delete_action!!', static::format_delete_url("&parent=".$parent."&id=".$this->id) ?? "", $category_form);

		print $category_form;
	}

	public function has_notices() {
		global $thesaurus_auto_postage_montant,$thesaurus_auto_postage_descendant,$thesaurus_auto_postage_nb_montant,$thesaurus_auto_postage_nb_descendant;
		global $thesaurus_auto_postage_etendre_recherche,$nb_level_enfants,$nb_level_parents;
		$thesaurus_auto_postage_descendant = $thesaurus_auto_postage_montant=0;
		// Autopostage actif
		if ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant ) {
			if(!isset($nb_level_enfants)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_enfants"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
				else $nb_level_descendant=$thesaurus_auto_postage_nb_descendant;
			} else {
				$nb_level_descendant=$nb_level_enfants;
			}

			// lien Etendre auto_postage
			if(!isset($nb_level_parents)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_parents"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
				else $nb_level_montant=$thesaurus_auto_postage_nb_montant;
			} else {
				$nb_level_montant=$nb_level_parents;
			}
			$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
			$_SESSION["nb_level_parents"]=	$nb_level_montant;

			$q = "select path from noeuds where id_noeud = '".$this->id."' ";
			$r = pmb_mysql_query($q);
			$path=pmb_mysql_result($r, 0, 0);
			$nb_pere=substr_count($path,'/');
			// Si un path est renseigné et le paramètrage activé
			if ($path && ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){

				//Recherche des fils
				if(($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_etendre_recherche)&& $nb_level_descendant) {
					if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
						$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
					else
						$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
				} else {
					$liste_fils=" id_noeud='".$this->id."' ";
				}

				// recherche des pères
				$liste_pere = "";
				if(($thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && $nb_level_montant) {

					$id_list_pere=explode('/',$path);
					$stop_pere=0;
					if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere = $nb_pere - intval($nb_level_montant);
					for($i=$nb_pere;$i>=$stop_pere; $i--) {
						$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
					}
				}
				// requete permettant de remonter les notices associées à la liste des catégories trouvées;
				$suite_req = " FROM noeuds inner join notices_categories on id_noeud=num_noeud inner join notices on notcateg_notice=notice_id
					WHERE ($liste_fils $liste_pere) and notices_categories.notcateg_notice = notices.notice_id ";
			} else {
				// cas normal d'avant
				$suite_req=" FROM notices_categories, notices WHERE notices_categories.num_noeud = '".$this->id."' and notices_categories.notcateg_notice = notices.notice_id ";
			}

			$query ="SELECT COUNT(1) ".$suite_req;
		} else {
			// Autopostage désactivé
			$query ="SELECT COUNT(1) FROM notices_categories WHERE notices_categories.num_noeud='".$this->id."' ";

		}
		$result = pmb_mysql_query($query);
		return (pmb_mysql_result($result, 0, 0));
	}

	public function notice_count($include_subcategories=true) {
		/*
		 * $include_subcategories : Inclue également les notices dans les catégories filles
		 */
		$listcontent = array();
		if (!$include_subcategories) {
			$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$this->id;
			$ares = pmb_mysql_query($asql);
			while ($arow=pmb_mysql_fetch_row($ares)) {
				$listcontent[] = $arow[0];
			}
			$notice_count = count($listcontent);
			return $notice_count;
		}
		else {
			get_category_notice_count($this->id, $listcontent);
			$listcontent = array_unique($listcontent); //S'agirait pas d'avoir deux fois la même notice comptée.
			$notice_count = count($listcontent);
			return $notice_count;
		}
	}

	public static function get_informations_from_unimarc($fields,$link = false,$code_field="250"){
		$data = array();
		if(!$link){
			$data['label'] = $fields[$code_field][0]['a'][0];
			if($fields[$code_field][0]['j']){
				for($i=0 ; $i<count($fields[$code_field][0]['j']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['j'][$i];
				}
			}
			if($fields[$code_field][0]['x']){
				for($i=0 ; $i<count($fields[$code_field][0]['x']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['x'][$i];
				}
			}
			if($fields[$code_field][0]['y']){
				for($i=0 ; $i<count($fields[$code_field][0]['y']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['y'][$i];
				}
			}
			if($fields[$code_field][0]['z']){
				for($i=0 ; $i<count($fields[$code_field][0]['z']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['z'][$i];
				}
			}

			for ($i=0 ; $i<count($fields['300']) ; $i++){
				for($j=0 ; $j<count($fields['300'][$i]['a']) ; $j++){
					if($data['comment'] != "") $data['comment'].="\n";
					$data['comment'] .= $fields['300'][$i]['a'][$j];
				}
			}
			for ($i=0 ; $i<count($fields['330']) ; $i++){
				for($j=0 ; $j<count($fields['330'][$i]['a']) ; $j++){
					if($data['note'] != "") $data['note'].="\n";
					$data['note'] .= $fields['330'][$i]['a'][$j];
				}
			}
		}else{
			$data['label'] = $fields['a'][0];
			if($fields['j']){
				for($i=0 ; $i<count($fields['j']) ; $i++){
					$data['label'] .=  " -- ".$fields['j'][$i];
				}
			}
			if($fields['x']){
				for($i=0 ; $i<count($fields['x']) ; $i++){
					$data['label'] .=  " -- ".$fields['x'][$i];
				}
			}
			if($fields['y']){
				for($i=0 ; $i<count($fields['y']) ; $i++){
					$data['label'] .=  " -- ".$fields['y'][$i];
				}
			}
			if($fields['z']){
				for($i=0 ; $i<count($fields['z']) ; $i++){
					$data['label'] .=  " -- ".$fields['z'][$i];
				}
			}
			$data['authority_number'] = $fields['3'][0];
		}
		$data['type_authority'] = "category";
		return $data;
	}

	public static function import($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}

		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}

		$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
			if(!$id){
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->num_statut = ($data['statut'] ? intval($data['statut']) : 1);
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie = trim($data['label']);
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}else{
				$c=new categories($id, $lang);
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}
		}else{
			//pas de thésausus, on peut rien faire...
			return 0;
		}
		return $id;
	}

	public static function check_if_exists($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}

		if($data['label'] == ""){
			return 0;
		}
		$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
		return $id;
	}

	/*
	 * Pour import autorité
	 */
	public function update($data,$id_thesaurus,$num_parent,$lang){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}

		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}
		if($this->id == 0){
			$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie = trim($data['label']);
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
				$this->id = $c->num_noeud;
				return 1;
			}
		}else{
			$c=new categories($this->id, $lang);
			$c->libelle_categorie = trim($data['label']);
			$c->note_application = $data['note'];
			$c->comment_public = $data['comment'];
			$c->save();
			return 1;
		}
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

	public function get_header() {
		return $this->catalog_form;
	}

	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=categ&id='.$this->id;
	}

	public function get_isbd() {
		return $this->libelle;
	}

	public static function get_format_data_structure($antiloop = false) {
		global $msg;

		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['103']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['categ_commentaire']
		);
		if(!$antiloop) {
			$main_fields[] = array(
					'var' => "parent",
					'desc' => $msg['categ_parent'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"parent")
			);
			$main_fields[] = array(
					'var' => "renvoi",
					'desc' => $msg['categ_renvoi'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"renvoi")
			);
			/*$main_fields[] = array(
					'var' => "renvoi_voir_aussi",
					'desc' => $msg['renvoi_voir_aussi'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"renvoi_voir_aussi[i]")
			);*/
		}

		$authority = new authority(0, 0, AUT_TABLE_CATEG);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
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
		$authority = new authority(0, $this->id, AUT_TABLE_CATEG);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}

	/**
	 * Suppression d'une catégorie
	 */
	public function delete() {
		global $msg, $charset, $parent, $force_delete_target, $forcage, $ret_url;
		global $current_module;

		if (noeuds::hasChild($this->id)) {
			//cette autorité a des sous catégories
			return return_error_message($msg[321], $msg[322], 1, static::format_url("&id=".$this->id."&sub=categ_form&parent=".$parent));
		} elseif (count(noeuds::listTargetsExceptOrphans($this->id))){
			//d'autres catégories renvoient vers elle
			return return_error_message($msg[321], $msg["thes_suppr_impossible_renvoi_voir"], 1, static::format_url("&id=".$this->id."&sub=categ_form&parent=".$parent));
		} elseif (noeuds::isProtected($this->id)) {
			//catégorie protégée
			return return_error_message($msg[321], $msg["thes_suppr_impossible_protege"], 1, static::format_url("&id=".$this->id."&sub=categ_form&parent=".$parent));
		} elseif (count(vedette_composee::get_vedettes_built_with_element($this->id, TYPE_CATEGORY))) {
			// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
			return return_error_message($msg[321], $msg["vedette_dont_del_autority"], 1).'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
		}elseif(($usage=aut_pperso::delete_pperso(AUT_TABLE_CATEG, $this->id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return return_error_message($msg[321], $msg["autority_delete_error"].'<br /><br />'.$usage['display'], 1);
		} elseif (noeuds::isUsedInNotices($this->id)) {
			if ($forcage == 1) {
				$tab= unserialize( urldecode($ret_url) );
				foreach($tab->GET as $key => $val){
					$GLOBALS[$key] = $val;
				}
				foreach($tab->POST as $key => $val){
					$GLOBALS[$key] = $val;
				}

				$this->maj_graph_rdf($this->id);
				$this->delete_node_and_index($this->id);

				$requete="DELETE FROM notices_categories WHERE num_noeud=".$this->id;
				pmb_mysql_query($requete);

			} else {
				$tab = new stdClass();
				$requete="SELECT notcateg_notice FROM notices_categories WHERE num_noeud=".$this->id." ORDER BY ordre_categorie";
				$result_cat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result_cat)) {
					//affichage de l'erreur, en passant tous les param postés (serialise) pour l'éventuel forcage
					$tab->POST = $_POST;
					$tab->GET = $_GET;
					$ret_url= urlencode(serialize($tab));

					$html = "
					<br /><div class='erreur'>".$msg[540]."</div>
					<script type='text/javascript' src='./javascript/tablist.js'></script>
					<script>
						function confirm_delete() {
							phrase = \"".$msg["autorite_confirm_suppr_categ"]."\";
							result = confirm(phrase);
							if(result) form.submit();
						}
					</script>
					<div class='row'>
						<div class='colonne10'>
							<img src='".get_url_icon('error.gif')."' class='align_left'>
						</div>
						<div class='colonne80'>
							<strong>".$msg["autorite_suppr_categ_titre"]."</strong>
						</div>
					</div>
					<div class='row'>
						<form class='form-".$current_module."' name='dummy'  method='post' action='".static::format_url("&sub=delete&parent=".$parent."&id=".$this->id)."'>
							<input type='hidden' name='forcage' value='1'>
							<input type='hidden' name='ret_url' value='".$ret_url."'>
							<input type='button' name='ok' class='bouton' value='".$msg[89]."' onClick='history.go(-1);'>
							<input type='submit' class='bouton' name='bt_forcage' value='".htmlentities($msg["autorite_suppr_categ_forcage_button"], ENT_QUOTES,$charset)."'  onClick=\"confirm_delete();return false;\">
						</form>
					</div>";
					while (($r_cat=pmb_mysql_fetch_object($result_cat))) {
						$requete="select signature, niveau_biblio ,notice_id from notices where notice_id=".$r_cat->notcateg_notice." limit 20";
						$result=pmb_mysql_query($requete);
						if (($r=pmb_mysql_fetch_object($result))) {
							if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
								// notice de monographie
								$nt = new mono_display($r->notice_id);
							} else {
								// on a affaire à un périodique
								$nt = new serial_display($r->notice_id,1);
							}
							$html .= "
								<div class='row'>
									".$nt->result."
								</div>";
						}
						$html .= "<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";
					}
					return $html;
				}
			}
		} elseif (count(noeuds::listTargetsOrphansOnly($this->id)) && !isset($force_delete_target)) {
			return return_box_confirm_message($msg[321], $msg["confirm_suppr_categ_rejete"], static::format_url("&sub=delete&parent=".$parent."&id=".$this->id."&force_delete_target=1"), static::format_url("&id=".$this->id."&sub=categ_form&parent=".$parent), $msg[40], $msg[39]);
		} else {
			$array_to_delete = array();
			$id_list_orphans = noeuds::listTargetsOrphansOnly($this->id);

			if (count($id_list_orphans)) {
				foreach ($id_list_orphans as $id_orphan) {
					// on n'efface pas les termes orphelins avec terme spécifique
					// on n'efface pas les termes orphelins utilisées en indexation
					if (!noeuds::hasChild($id_orphan) && !noeuds::isUsedInNotices($id_orphan)) {
						$array_to_delete[] = $id_orphan;
					}
				}
			}
			$array_to_delete[] = $this->id;

			foreach($array_to_delete as $id_to_delete){
				$this->maj_graph_rdf($id_to_delete);
				$this->delete_node_and_index($id_to_delete);
			}
		}
		return false;
	}

	protected function maj_graph_rdf($id) {
		global $pmb_synchro_rdf;
		//On met à jour le graphe rdf avant de supprimer
		if ($pmb_synchro_rdf) {
			$arrayIdImpactes=array();
			$synchro_rdf=new synchro_rdf();
			$noeud=new noeuds($id);
			$thes=new thesaurus($noeud->num_thesaurus);
			//parent
			if($noeud->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$noeud->num_parent;
			}
			//renvoi_voir
			if($noeud->num_renvoi_voir){
				$arrayIdImpactes[]=$noeud->num_renvoi_voir;
			}
			//on supprime le rdf
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
				}
			}
			$synchro_rdf->delConcept($id);

			//On remet à jour les noeuds impactes
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			//On met à jour le thésaurus pour les topConcepts
			$synchro_rdf->updateAuthority($noeud->num_thesaurus,'thesaurus');
		}
	}

	protected function delete_node_and_index($id) {
		// nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_CATEGORY);
		$index_concept->delete();

		noeuds::delete($id);
	}

	public function get_parent() {
		$authority = new authority(0, $this->parent_id, AUT_TABLE_CATEG);
		return array(
				'id' => $this->parent_id,
				'libelle' => $this->parent_libelle,
				'num_authority' => $authority->get_id()
		);
	}

	public function get_right() {
		return SESSrights & THESAURUS_AUTH;
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
		global $base_path;

		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}/**
	* Charge les données de carthographie
	*/
	private function fetch_map() {
	    global $pmb_map_activate;

	    if ($pmb_map_activate) {
	        $this->map = new map_objects_controler(AUT_TABLE_CATEG, array($this->id));
	        $this->map_info = new map_info($this->id);
	    }
	}
	/**
	 * Retourne la carte associée
	 * @return map_objects_controler
	 */
	public function get_map() {
	    if (!$this->map) {
	        $this->fetch_map();
	    }
	    return $this->map;
	}

	/**
	 * Retourne les infos de la carte associée
	 * @return map_info
	 */
	public function get_map_info() {
	    if (!$this->map_info) {
	        $this->fetch_map();
	    }
	    return $this->map_info;
	}
} # fin de définition de la classe category

} # fin de déclaration
