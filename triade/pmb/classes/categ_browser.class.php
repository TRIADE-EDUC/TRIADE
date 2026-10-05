<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_browser.class.php,v 1.31 2023/07/26 15:07:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion le l'explorateur de catégories
if ( ! defined( 'CATEG_BROWSER_CLASS' ) ) {
  define( 'CATEG_BROWSER_CLASS', 1 );

global $class_path;
require_once($class_path."/thesaurus.class.php");

class categ_browser {

	// properties
	// browser images
	public $up_folder = "<img src='./images/folderup.gif' />";
	public $closed_folder = "<img src='./images/folderclosed.gif' />";
	public $open_folder = "<img src='./images/folderopen.gif' />";
	public $document = "<img src='./images/doc.gif' />";
	public $see_img = "<img src='./images/see.gif' />";
	
	public $parent = 0;			// 	current parent
	public $level = 0;				//	current level in browser tree
	public $parents_tab;			//	array parents values
	public $children_tab;			//	array for children values
	public $display = '';			//	string to display
	public $offset = 18;			//	offset for margin
	public $current_margin = 0;	// 	actual margin
	public $folder_link = '';		//	link to use if a folder is clicked
	public $document_link = '';	//	link to use if a document or name is clicked
	public $id_thes = 0;			//  identifiant de thesaurus
	public $thes;					//  objet thesaurus 
	
	// constructor
	public function __construct($parent=0, $folder_link='', $document_link='', $id_thes=0) {
		global $PHP_SELF;

		$this->parent = $parent;
		$this->folder_link = $folder_link;
		$this->document_link = $document_link;

		//recuperation du thesaurus session 
		if(!$id_thes) {
			$id_thes = thesaurus::getSessionThesaurusId();
		} else {
			thesaurus::setSessionThesaurusId($id_thes);
		}
		if ($id_thes != -1) {
			$this->thes = new thesaurus($id_thes);
		}
		$this->id_thes = $id_thes; 
		if ($this->id_thes != -1) { // 1 seul thesaurus
			if (!$this->parent) {
				$this->parent = $this->thes->num_noeud_racine;
			}
			$this->parents_tab = array();
			$this->children_tab = array();
			$caller = preg_replace('/\/.*\//', './', $PHP_SELF);
			if(!$this->folder_link)
				$this->folder_link = "<a href='".$caller."?parent=!!id!!'>";
			if(!$this->document_link)
				$this->document_link = "<a href='".$caller."?id=!!id!!'>";
			$this->get_children();
			$this->get_parents();
		}

		return TRUE;
	}

	// getting images location if required
	public function set_images($up_folder='', $closed_folder='', $open_folder='', $document='', $see='') {
		if($up_folder)
			$this->up_folder = $up_folder;
		if($closed_folder)
			$this->closed_folder = $closed_folder;
		if($open_folder)
			$this->open_folder = $open_folder;
		if($document)
			$this->document = $document;
		if($see)
			$this->see_img = $see;
	}

	// do_browser() : drawing final browser
	public function do_browser() {
		// display up link if applying
		$up_link = str_replace('!!id!!', '0', $this->folder_link);
		if($this->parent != $this->thes->num_noeud_racine)
			$this->display = "<div style='margin:$this->current_margin'>".$up_link.$this->up_folder."<...</a></div>\n";
		
		// adding path
		foreach ($this->parents_tab as $valeur) {
			$link = str_replace('!!id!!', $valeur['id'], $this->folder_link);
			$doc_l = str_replace('!!id!!', $valeur['id'], $this->document_link);

			$this->display .=  "\n<div style='margin-left:".$this->current_margin."px'>";
			$this->display .= $link.$this->open_folder.'</a>';
			if($valeur['has_records'])
				$this->display .= $doc_l.$valeur['name']."</a></div>";
			else
				$this->display .= $valeur['name'].'</div>';
				//$this->display.="</div>";
			$this->current_margin = $this->current_margin + $this->offset;
		}

		// adding children
		foreach ($this->children_tab as $valeur) {
			if($valeur['has_children']) {
				$link = str_replace('!!id!!', $valeur['id'], $this->folder_link);
				$doc_l = str_replace('!!id!!', $valeur['id'], $this->document_link);
				$icon = $this->closed_folder;
				$this->display .= "\n<div style='margin-left:".$this->current_margin."px'>";
				$this->display .= $link;
				$this->display .= $icon.'</a>';
				if($valeur['has_records']) {
					$this->display .= $doc_l;
					$this->display .= $valeur['name'];
					$this->display .= '</a>';
					$this->display .= '</div>';
				} else {
					$this->display .= $valeur['name'].'</div>';
				}
			}
			else {
				$icon = $this->document;
				if($valeur['see']) {
					// il y a renvoi vers une autre catégorie
					$icon = $this->see_img;
					$valeur['id'] = $valeur['see'];
					$valeur['name'] = '<i>'.$valeur['name'].'@</i>';
					// on regarde si la catégorie cible a des enfants
					$see_requete = "SELECT count(1) FROM noeuds WHERE num_parent={$valeur['id']} LIMIT 1";

					$count_result = pmb_mysql_query($see_requete);
					if(@pmb_mysql_result($count_result, 0, 0)) {
						// la catégorie cible à des enfants -> tous les liens pointent vers l'affichage catégorie
						$link = str_replace('!!id!!', $valeur['id'], $this->folder_link);
						$doc_l = $link;
					} else {
						// la catégorie cible n'a pas d'enfants -> tous les liens pointent vers la recherche notice
						$link = str_replace('!!id!!', $valeur['id'], $this->document_link);
						$doc_l = $link;
					}
					$this->display .=  "\n<div style='margin-left:".$this->current_margin."px'>";
					$this->display .= $doc_l;
					$this->display .= $icon.$valeur['name'];
					$this->display .= "</a></div>";
				} else {
					if($valeur['has_records']) {
						$link = str_replace('!!id!!', $valeur['id'], $this->folder_link);
						$doc_l = str_replace('!!id!!', $valeur['id'], $this->document_link);
						$this->display .=  "\n<div style='margin-left:".$this->current_margin."px'>";
						$this->display .= $doc_l;
						$this->display .= $icon.$valeur['name'];
						$this->display .= "</a></div>";
					} else {
						$this->display .=  "\n<div style='margin-left:".$this->current_margin."px'>";
						$this->display .= $icon.$valeur['name'].'</div>';
					}
				}
			}
		}
	}


	// get_parents() method : retrieves parents infos
	public function get_parents() {
		global $lang;

		if(!$this->parent) {
			$this->parents_tab = array();
			return 0;
		}
		
		$temp = $this->parent;

		while($temp != $this->thes->num_noeud_racine) {
			// fetching category information
			$requete = "select catdef.num_noeud as categ_id, ";
			$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
			$requete.= "noeuds.num_parent as categ_parent, ";
			$requete.= "noeuds.num_renvoi_voir as categ_see, ";
			$requete.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as categ_comment, ";
			$requete.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie) as index_categorie "; 
			$requete.= "from noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$requete.= "where catdef.num_noeud = '".$temp."' ";
			if($this->id_thes) {
				$requete.= "and noeuds.num_thesaurus = '".$this->id_thes."' ";
			}
			$requete.= "limit 1 ";

			$result = pmb_mysql_query($requete);

			$upper = pmb_mysql_fetch_object($result);

			// getting number of associated records
			$requete = "select count(1) from notices_categories where num_noeud ='".$upper->categ_id."' ";

			$count_result = pmb_mysql_query($requete);

			$has_records = pmb_mysql_result($count_result, 0, 0);
			$this->parents_tab[] = array(	
							'id' => $upper->categ_id,
							'name' => $upper->categ_libelle,
							'has_records' => $has_records);
			$temp = $upper->categ_parent;
		}
		if (!empty($this->parents_tab)) {
		    $this->parents_tab = array_reverse($this->parents_tab);
		}
	}

	public function get_children() {
		global $thesaurus_categories_show_empty_categ;
		global $lang;
		
		// getting infos for children categories
		$requete = "select catdef.num_noeud as categ_id, ";
		$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
		$requete.= "noeuds.num_parent as categ_parent, ";
		$requete.= "noeuds.num_renvoi_voir as categ_see, ";
		$requete.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as categ_comment, ";
		$requete.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie) as index_categorie "; 
		$requete.= "from noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
		$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$requete.= "where noeuds.num_parent = '".$this->parent."' ";
		if($this->id_thes) {
			$requete.= "and noeuds.num_thesaurus = '".$this->id_thes."' ";
		}
		$requete.= "order by categ_libelle limit 200 ";
		$result = pmb_mysql_query($requete);
		while($current=pmb_mysql_fetch_object($result)) {
			$count_child = "select count(1) from noeuds where num_parent = '".$current->categ_id."' limit 1";
			$count_result = pmb_mysql_query($count_child);

			// getting number of associated records
			$query = "select count(1) from notices_categories where num_noeud = '".$current->categ_id."' ";
			$count_records = pmb_mysql_query($query);

			if (((pmb_mysql_result($count_records, 0, 0)||$thesaurus_categories_show_empty_categ)||(pmb_mysql_result($count_result, 0, 0)))&&($current->categ_libelle[0]!="~")) {
				$this->children_tab[] = array(	
								'id' => $current->categ_id,
								'name' => $current->categ_libelle,
								'see' => $current->categ_see,
								'has_children' => pmb_mysql_num_rows($count_result),
								'has_records' => pmb_mysql_num_rows($count_records));
			}
		}
	}


	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form($categ_id=0) {
		global $user_query;
		global $msg;
		global $user_input,$id_thes,$charset;
		
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[134] , $user_query);
		$user_query = str_replace ('!!action!!', './autorites.php?categ=categories&sub=search', $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[317] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', "./autorites.php?categ=categories&sub=categ_form&parent=$categ_id&id=0", $user_query);
		if ($id_thes>=1)
			$lien_derniers = "<a href='./autorites.php?categ=categories&sub=categorie_last&id_thes=$id_thes'>".$msg["autorites_categ_last"]."</a>&nbsp;";
		else
			$lien_derniers = "" ;
		$user_query = str_replace("<!-- lien_derniers -->",$lien_derniers,$user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		
		print pmb_bidi($user_query) ;
	}

} # fin de définition de la classe 'categ_browser'

} # fin de délaration

