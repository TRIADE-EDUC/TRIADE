<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage_unimarc.class.php,v 1.110.4.2 2025/03/26 13:29:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once($class_path."/publisher.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/category.class.php");
require_once($include_path."/isbn.inc.php");
require_once($include_path."/rss_func.inc.php") ;
require_once($class_path."/resa_planning.class.php") ;
include_once($include_path."/templates/expl_list.tpl.php");
require_once($include_path."/resa_func.inc.php");
require_once($class_path."/skos/skos_concepts_list.class.php");
require_once($class_path."/skos/skos_view_concepts.class.php");
require_once($base_path."/admin/connecteurs/in/cairn/cairn.class.php");
require_once($include_path."/notice_categories.inc.php");

function cmpexpl($a, $b) {
	$c1 = isset($a["priority"]) ? $a["priority"] : "";
	$c2 = isset($b["priority"]) ? $b["priority"] : "";
	if ($c1 == $c2) {
		$c1 = isset($a["content"]["v"]) ? $a["content"]["v"] : "";
		$c2 = isset($b["content"]["v"]) ? $b["content"]["v"] : "";
		return strcmp($c1, $c2);
	}
	return $c2-$c1;
}

global $tdoc;
if (empty($tdoc)) $tdoc = new marc_list('doctype');
global $fonction_auteur;
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// definition de la classe d'affichage des notices
class notice_affichage_unimarc {
	public $notice_id		= 0;		// id de la notice a afficher
	public $notice_header	= "" ;		// titre + auteur principaux
						// le terme affichage correspond au code HTML qui peut etre envoye avec un print
	public $notice_isbd	= "" ;		// Affichage ISBD de la notice
	public $notice_public	= "" ;		// Affichage public PMB de la notice
	public $notice_indexations	= "" ;		// Affichage des indexations categories et mots cles, peut etre ajoute a $notice_isbd ou a $notice_public afin d'avoir l'affichage complet PMB
	public $notice_exemplaires	= "" ;		// Affichage des exemplaires, peut etre ajoute a $notice_isbd ou a $notice_public afin d'avoir l'affichage complet PMB
	public $notice_explnum	= "" ;		// Affichage des exemplaires numeriques, peut etre ajoute a $notice_isbd ou a $notice_public afin d'avoir l'affichage complet PMB
	public $notice_notes	= "" ;		// Affichage des notes de contenu et resume, peut etre ajoute a $notice_isbd ou a $notice_public afin d'avoir l'affichage complet PMB
	public $notice;				// objet notice tel que fetche dans la table notices,
						//		augmente de $this->notice->serie_name si serie il y a
						//		augmente de n_gen, n_contenu, n_resume si on est alle les chercher car non ISBD standard
	public $responsabilites 	= array("responsabilites" => array(),"auteurs" => array());  // les auteurs avec tout ce qu'il faut
	public $categories 	= array();	// les id des categories
	public $auteurs_principaux	= "" ;		// ce qui apparait apres le titre pour le header
  	public $auteurs_tous	= "" ;		// Tous les auteurs avec leur fonction
  	public $categories_toutes	= "" ;		// Toutes les categories dans lesquelles est rangee la notice

	public $lien_rech_notice 		;
	public $lien_rech_auteur 		;
  	public $lien_rech_editeur 		;
  	public $lien_rech_serie 		;
  	public $lien_rech_collection 	;
  	public $lien_rech_subcollection 	;
  	public $lien_rech_indexint 	;
  	public $lien_rech_motcle 		;
  	public $lien_rech_categ 		;
  	public $lien_rech_perio 		;
  	public $lien_rech_bulletin 	;
 	public $liens = array();

 	public $langues = array();
	public $languesorg = array();

  	public $action		= '';	// URL à associer au header
	public $header		= '';	// chaine accueillant le chapeau de notice (peut-être cliquable)
	public $tit_serie		= '';	// titre de série si applicable
	public $tit1		= '';	// valeur du titre 1
	public $result		= '';	// affichage final
	public $isbd		= '';	// isbd de la notice en fonction du level défini
	public $expl		= 0;	// flag indiquant si on affiche les infos d'exemplaire
	public $link_expl		= '';	// lien associé à un exemplaire
	public $show_resa		= 0;	// flag indiquant si on affiche les infos de resa
	public $p_perso;
	public $cart_allowed = 0;
	public $avis_allowed = 0;
	public $tag_allowed = 0;
	public $to_print = 0;
	public $affichage_resa_expl = "" ; // lien réservation, exemplaires et exemplaires numériques, en tableau comme il faut
	public $affichage_expl = "" ;  // la même chose mais sans le lien réservation

	public $statut = 1 ;  			// Statut (id) de la notice
	public $statut_notice = "" ;  	// Statut (libellé) de la notice
	public $visu_notice = 1 ;  	// Visibilité de la notice à tout le monde
	public $visu_notice_abon = 0 ; // Visibilité de la notice aux abonnés uniquement
	public $visu_expl = 1 ;  		// Visibilité des exemplaires de la notice à tout le monde
	public $visu_expl_abon = 0 ;  	// Visibilité des exemplaires de la notice aux abonnés uniquement
	public $visu_explnum = 1 ;  	// Visibilité des exemplaires numériques de la notice à tout le monde
	public $visu_explnum_abon = 0 ;// Visibilité des exemplaires numériques de la notice aux abonnés uniquement

	public $childs = array() ; // filles de la notice
	public $notice_childs = "" ; // l'équivalent à afficher
	public $anti_loop="";
	public $seule = 0 ;
	public $premier = "PUBLIC" ;
	public $double_ou_simple = 2 ;
	public $avis_moyenne ; // Moyenne des  avis
	public $avis_qte; // Quantité d'un avis

	public $year;
	public $exemplaires;
	public $docnums;
	public $affichage_suite;
	public $affichage_bulletinnage;

	public $antiloop=array();

	public $unimarc=array();
	public $source_id;
	public $source_name;
	public $connector_id;
	public $entrepots_localisations=array();

	public $notice_expired = false;
	
	public $details = array();
	public $publishers = array();
	public $titres_uniformes = array();

	// constructeur------------------------------------------------------------
	public function __construct($id, $liens, $cart=0, $to_print=0, $entrepots_localisations=array()) {
	  	// $id = id de la notice à afficher
	  	// $liens	 = tableau de liens tel que ci-dessous
	  	// $cart : afficher ou pas le lien caddie
	  	// $to_print = affichage mode impression ou pas
	
		global $opac_avis_allow;
		global $opac_allow_add_tag;
	
	 	if (!$liens) $liens=array();
		$this->lien_rech_notice 		=       (isset($liens['lien_rech_notice']) ? $liens['lien_rech_notice'] : '');
		$this->lien_rech_auteur 		=       (isset($liens['lien_rech_auteur']) ? $liens['lien_rech_auteur'] : '');
		$this->lien_rech_editeur 		=       (isset($liens['lien_rech_editeur']) ? $liens['lien_rech_editeur'] : '');
		$this->lien_rech_serie 			=       (isset($liens['lien_rech_serie']) ? $liens['lien_rech_serie'] : '');
		$this->lien_rech_collection 	=       (isset($liens['lien_rech_collection']) ? $liens['lien_rech_collection'] : '');
		$this->lien_rech_subcollection 	=       (isset($liens['lien_rech_subcollection']) ? $liens['lien_rech_subcollection'] : '');
		$this->lien_rech_indexint 		=       (isset($liens['lien_rech_indexint']) ? $liens['lien_rech_indexint'] : '');
		$this->lien_rech_motcle 		=       (isset($liens['lien_rech_motcle']) ? $liens['lien_rech_motcle'] : '');
		$this->lien_rech_categ 			=       (isset($liens['lien_rech_categ']) ? $liens['lien_rech_categ'] : '');
		$this->lien_rech_perio 			=       (isset($liens['lien_rech_perio']) ? $liens['lien_rech_perio'] : '');
		$this->lien_rech_bulletin 		=       (isset($liens['lien_rech_bulletin']) ? $liens['lien_rech_bulletin'] : '');
		$this->liens = $liens;
		$this->cart_allowed = $cart;
		$this->entrepots_localisations = $entrepots_localisations;
	
		if ($to_print) {
			$this->avis_allowed = 0;
			$this->tag_allowed = 0;
		} else {
			$this->avis_allowed = $opac_avis_allow;
			$this->tag_allowed = $opac_allow_add_tag;
		}
	
		$this->to_print = $to_print;
	
	  	// $seule : si 1 la notice est affichée seule et dans ce cas les notices childs sont en mode dépliable
	  	global $seule ;
	  	$this->seule = $seule ;
	
	  	if(!$id)
	  		return;
		else {
		    $id = intval($id);
			$this->notice_id = $id;
			$this->fetch_data();
		}
	
		//$this->p_perso=new parametres_perso("notices");
	}
	
	// récupération des valeurs en table---------------------------------------
	public function fetch_data() {
		global $dbh;
	
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);
	
		$requete="select * from entrepot_source_".$source_id." where recid='".addslashes($this->notice_id)."' group by field_order,ufield,usubfield,subfield_order,value";
		$myQuery = pmb_mysql_query($requete);
	
		$notice= new stdClass();
		$lpfo="";
		$n_ed=-1;
	
		$exemplaires = array();
		$doc_nums = array();
		$cpt_notice_pperso=0;
		$notice->notice_pperso= array();
	
		if(pmb_mysql_num_rows($myQuery)) {
			$is_article = false;
			while ($l=pmb_mysql_fetch_object($myQuery)) {
				if (!$this->source_id) {
					$this->source_id=$l->source_id;
					$requete="select name, id_connector from connectors_sources where source_id=".$l->source_id;
					$result=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($result)) {
						$row = pmb_mysql_fetch_object($result);
						$this->source_name = $row->name;
						$this->connector_id = $row->id_connector;
					}
				}
				
				
				if (!isset($this->details[$l->ufield])) {
				    $this->details[$l->ufield] = array();
				}
				if ($l->usubfield === "") {
				    $this->details[$l->ufield][] = $l->value;
				} else {
				    $this->details[$l->ufield][] = array($l->usubfield => $l->value);
				}
				
				
// 				$this->unimarc[$l->ufield][$l->field_order][$l->usubfield][$l->subfield_order];
				switch ($l->ufield) {
					//dt
					case "dt":
						$notice->typdoc=$l->value;
						break;
					case "bl":
						if($l->value == 'a'){
							$notice->niveau_biblio=$l->value;
						} else $notice->niveau_biblio='m'; //On force le document au type monographie
						break;
					case "hl":
						if($l->value == '2'){
							$notice->niveau_hierar=$l->value;
						} else $notice->niveau_hierar='0'; //On force le niveau à zéro
						break;
					//ISBN
					case "010":
						if ($l->usubfield=="a") $notice->code=$l->value;
						break;
					//Titres
					case "200":
						switch ($l->usubfield) {
							case "a":
								if(!isset($notice->tit1)) $notice->tit1 = '';
								$notice->tit1.=($notice->tit1?" ":"").$l->value;
								break;
							case "c":
								if(!isset($notice->tit2)) $notice->tit2 = '';
								$notice->tit2.=($notice->tit2?" ":"").$l->value;
								break;
							case "d":
								if(!isset($notice->tit3)) $notice->tit3 = '';
								$notice->tit3.=($notice->tit3?" ":"").$l->value;
								break;
							case "e":
								if(!isset($notice->tit4)) $notice->tit4 = '';
								$notice->tit4.=($notice->tit4?" ":"").$l->value;
								break;
						}
						break;
					//Editeur
					case "210":
					case "214":
					case "219":
						if($l->field_order!=$lpfo) {
							$lpfo=$l->field_order;
							$n_ed++;
						}
						switch ($l->usubfield) {
							case "a":
								$this->publishers[$n_ed]["city"]=$l->value;
								break;
							case "c":
								$this->publishers[$n_ed]["name"]=$l->value;
								break;
							case "d":
								$this->publishers[$n_ed]["year"]=$l->value;
								$this->year=$l->value;
								break;
						}
						break;
					//Collation
					case "215":
						switch ($l->usubfield) {
							case "a":
								$notice->npages=$l->value;
								break;
							case "c":
								$notice->ill=$l->value;
								break;
							case "d":
								$notice->size=$l->value;
								break;
							case "e":
								$notice->accomp=$l->value;
								break;
						}
						break;
					//Collection
					case "225":
						switch ($l->usubfield) {
							case "a":
								if(!isset($notice->coll))$notice->coll = new stdClass();
								$notice->coll->titre=$l->value;
								break;
							case "i":
								if(!isset($notice->subcoll))$notice->subcoll = new stdClass();
								$notice->subcoll->titre=$l->value;
								break;
							case "v":
								if(!isset($notice->coll))$notice->coll = new stdClass();
								$notice->coll->num=$l->value;
								break;
						}
						break;
					//Note generale
					case "300":
						if(empty($notice->n_gen)) {
							$notice->n_gen = array();
						}
						$notice->n_gen[]=$l->value;
						break;
					//Note de contenu
					case "327":
						if(empty($notice->n_contenu)) {
							$notice->n_contenu = array();
						}
						$notice->n_contenu[]=$l->value;
						break;
					//Note de resume
					case "330":
						if(empty($notice->n_resume)) {
							$notice->n_resume = array();
						}
						$notice->n_resume[]=$l->value;
						break;
					//Serie ou Pério
					case "461":
						switch($l->usubfield){
							case 'x':
								$this->perio_issn = $l->value;
							break;
							case 't':
								$this->parent_title = $l->value;
								$notice->serie_name = $l->value;
							break;
							case '9':
								$is_article = true;
						    break;
						}
						if($is_article)
							$notice->serie_name = "";
						else {
							$this->parent_title = "";
							$this->perio_issn = "";
						}
						break;
					//Bulletins
					case "463" :
						switch($l->usubfield){
							case 't':
								$notice->bulletin_titre = $l->value;
							break;
							case 'v':
								$this->parent_numero = $l->value;
							break;
							case 'd':
								$this->parent_aff_date_date = $l->value;
							break;
							case 'e':
								$this->parent_date = $l->value;
							break;
						}
						break;
					//Titres Uniformes
					case "500":
						switch ($l->usubfield) {
							case "a":
								$this->titres_uniformes[]=$l->value;
								break;
						}
						break;
					//Mots cles
					case "610":
						switch ($l->usubfield) {
							case "a":
							    if (!isset($notice->index_l)) {
							        $notice->index_l = "";
							    }
								$notice->index_l.=($notice->index_l?" / ":"").$l->value;
								break;
						}
						break;
					//Indexations décimales..;
					case "676":
					case "686":
						switch ($l->usubfield) {
							case "a":
								$notice->indexint[] = $l->value;
								break;
						}
						break;
	
					//URL
					case "856":
						switch ($l->usubfield) {
							case "u":
								$notice->lien=$l->value;
								break;
							case "q":
								$notice->eformat=$l->value;
								break;
							case "t":
								$notice->lien_texte=$l->value;
								break;
						}
						break;
						// champs perso notice
					case "900":
						switch ($l->usubfield) {
							case "a":
								if(isset($notice->notice_pperso[$cpt_notice_pperso]['value'])){
									$cpt_notice_pperso++;
								}
								$notice->notice_pperso[$cpt_notice_pperso]['value']=$l->value;
								break;
							case "l":
								$notice->notice_pperso[$cpt_notice_pperso]['libelle']=$l->value;
								break;
							case "n":
								$notice->notice_pperso[$cpt_notice_pperso]['name']=$l->value;
								break;
							case "t":
								$notice->notice_pperso[$cpt_notice_pperso]['type']=$l->value;
								break;
						}
						break;
					case "996":
						$exemplaires[$l->field_order][$l->usubfield] = $l->value;
						break;
					//Thumbnail
					case "896":
						switch ($l->usubfield) {
							case "a":
								$notice->thumbnail_url=$l->value;
						}
						break;
					//Documents numériques
					case "897":
						$doc_nums[$l->field_order][$l->usubfield] = $l->value;
						break;
				}
			}
		}
		$this->exemplaires = $exemplaires;
		$this->docnums = $doc_nums;
	
		$this->notice=$notice;
		if (!$this->notice->typdoc) $this->notice->typdoc='a';
	
		// serials : si article
		//if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2)
		//$this->get_bul_info();
	
		$this->fetch_categories() ;
	
		$this->fetch_auteurs() ;
	
		//$this->fetch_visibilite() ;
		$this->fetch_langues(0) ;
		$this->fetch_langues(1) ;
		//$this->fetch_avis();
	
		//$this->childs=array();
		
		return pmb_mysql_num_rows($myQuery);
		} // fin fetch_data
	
	//public function fetch_visibilite() {
	//	global $dbh;
	//	global $hide_explnum;
	//	$requete = "SELECT opac_libelle, notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
	//	$myQuery = pmb_mysql_query($requete);
	//	if(pmb_mysql_num_rows($myQuery)) {
	//		$statut_temp = pmb_mysql_fetch_object($myQuery);
	//		$this->statut_notice =        $statut_temp->opac_libelle  ;
	//		$this->visu_notice =          $statut_temp->notice_visible_opac  ;
	//		$this->visu_notice_abon =     $statut_temp->notice_visible_opac_abon  ;
	//		$this->visu_expl =            $statut_temp->expl_visible_opac  ;
	//		$this->visu_expl_abon =       $statut_temp->expl_visible_opac_abon  ;
	//		$this->visu_explnum =         $statut_temp->explnum_visible_opac  ;
	//		$this->visu_explnum_abon =    $statut_temp->explnum_visible_opac_abon  ;
	//
	//		if ($hide_explnum) {
	//			$this->visu_explnum=0;
	//			$this->visu_explnum_abon=0;
	//		}
	//	}
	//
	//}
	
	// recuperation des auteurs ---------------------------------------------------------------------
	// retourne $this->auteurs_principaux = ce qu'on va afficher en titre du resultat
	// retourne $this->auteurs_tous = ce qu'on va afficher dans l'isbd
	// NOTE: now we have two functions:
	// 		fetch_auteurs()  	the pmb-standard one
	
	public function fetch_auteurs() {
		global $fonction_auteur;
	
		$this->responsabilites  = array() ;
		$auteurs = array() ;
	
		$res = array();
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;
	
		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}
	
		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->notice_id)."' and ufield like '7%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);
			
			$id_aut="";
			$n_aut=-1;
			$responsabilites = array();
			while ($l=pmb_mysql_fetch_object($res_sql)) {
				if ($l->field_order!=$id_aut) {
					$n_aut++;
					switch ($l->ufield) {
						case "700":
						case "710":
							$responsabilites[]=0;
							break;
						case "701":
						case "711":
							$responsabilites[]=1;
							break;
						case "702":
						case "712":
							$responsabilites[]=2;
							break;
					}
					switch (substr($l->ufield,0,2)) {
						case "70":
							$auteurs[$n_aut]["type"]=1;
							break;
						case "71":
							$auteurs[$n_aut]["type"]=2;
							break;
					}
					$auteurs[$n_aut]["id"]=(isset($l->recid) ? $l->recid : '').$l->field_order;
					$id_aut=$l->field_order;
				}
				switch ($l->usubfield) {
					case '4':
						$auteurs[$n_aut]['fonction']=$l->value;
						$auteurs[$n_aut]['fonction_aff']=$fonction_auteur[$l->value];
						break;
					case 'a':
						$auteurs[$n_aut]['name']=$l->value;
						break;
					case 'b':
						if ($auteurs[$n_aut]['type']==2) {
							$auteurs[$n_aut]['subdivision']=$l->value;
						} else {
							$auteurs[$n_aut]['rejete']=$l->value;
						}
						break;
					case 'd':
						if ($auteurs[$n_aut]['type']==2) {
							$auteurs[$n_aut]['numero']=$l->value;
						}
						break;
					case 'e':
						if ($auteurs[$n_aut]['type']==2) {
							if (!isset($auteurs[$n_aut]['lieu'])) {
								$auteurs[$n_aut]['lieu'] = "";
							}
							$auteurs[$n_aut]['lieu'].=(($auteurs[$n_aut]['lieu'])?'; ':'').$l->value;
						}
						break;
					case 'f':
						$auteurs[$n_aut]['date']=$l->value;
						break;
					case 'g':
						if ($auteurs[$n_aut]['type']==2) {
							$auteurs[$n_aut]['rejete']=$l->value;
						}
						break;
				}
			}
		}
	
		foreach($auteurs as $n_aut=>$auteur) {
			$auteurs[$n_aut]['auteur_titre'] = (!empty($auteurs[$n_aut]['rejete']) ? $auteurs[$n_aut]['rejete'].' ' : '').$auteurs[$n_aut]['name'];
			if ($auteur['type']==2 && (!empty($auteurs[$n_aut]['subdivision']) || !empty($auteurs[$n_aut]['numero']) || !empty($auteurs[$n_aut]['date']) || !empty($auteurs[$n_aut]['lieu']))) {
				$c='';
				
				$c.=(!empty($auteurs[$n_aut]['subdivision']) ? $auteurs[$n_aut]['subdivision'] : "");
				
				$c.=($c && !empty($auteurs[$n_aut]['numero'])) ? ', ' : "";
				$c.=(!empty($auteurs[$n_aut]['numero'])) ? $auteurs[$n_aut]['numero'] : "";
				
				$c.=($c && !empty($auteurs[$n_aut]['date'])) ? ', ' : "";
				$c.=(!empty($auteurs[$n_aut]['date'])) ? $auteurs[$n_aut]['date'] : "";
				
				$c.=($c && !empty($auteurs[$n_aut]['lieu'])) ? ', ' : "";
				$c.=(!empty($auteurs[$n_aut]['lieu'])) ? $auteurs[$n_aut]['lieu'] : "";
				
				$auteurs[$n_aut]['auteur_titre'].=' ('.$c.')';
			}
			$auteurs[$n_aut]['auteur_isbd']=$auteurs[$n_aut]['auteur_titre'].(!empty($auteurs[$n_aut]['fonction_aff'])?' ,':'').(isset($auteurs[$n_aut]['fonction_aff']) ? $auteurs[$n_aut]['fonction_aff'] : '');
		}
	
		if (!isset($responsabilites)) $responsabilites = array();
		if (!$auteurs) $auteurs = array();
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;
	
		// $this->auteurs_principaux
		// on ne prend que le auteur_titre = "Prenom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
			} else {
				$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
				$aut1_libelle = array();
				for ($i = 0 ; $i < count($as) ; $i++) {
					$indice = $as[$i] ;
					$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
					$aut1_libelle[]= $auteur_1["auteur_titre"];
					}
				$auteurs_liste = implode ("; ",$aut1_libelle) ;
				if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
				}
	
		// $this->auteurs_tous
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}
	
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}
	
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_2["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}
	
		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
			else $this->auteurs_tous ="" ;
	} // fin fetch_auteurs
	
	
	// recuperation des categories ------------------------------------------------------------------
	public function fetch_categories() {
		$this->categories_toutes="";
		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}
	
		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->notice_id)."' and ufield like '60%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);
			
			$id_categ="";
			$n_categ=-1;
			$categ_l=array();
			while ($l=pmb_mysql_fetch_object($res_sql)) {
				if ($l->field_order!=$id_categ) {
					if ($n_categ!=-1) {
						$categ_libelle=(!empty($categ_l["a"][0]) ? $categ_l["a"][0] : "").(!empty($categ_l["x"])?" - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"]) ?" - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"]) ?" - ".implode(" - ",$categ_l["z"]):"");
						$this->categories_toutes.=($this->categories_toutes?"<br />":"").$categ_libelle;
					}
					$categ_l=array();
					$n_categ++;
					$id_categ=$l->field_order;
				}
				$categ_l[$l->usubfield][]=$l->value;
			}
			if ($n_categ>=0) {
				$categ_libelle=(!empty($categ_l["a"][0]) ? $categ_l["a"][0] : "").(!empty($categ_l["x"])?" - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"]) ?" - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"]) ?" - ".implode(" - ",$categ_l["z"]):"");
				$this->categories_toutes.=($this->categories_toutes?"<br />":"").$categ_libelle;
			}
		}
	}
	
	public function fetch_langues($quelle_langues=0) {
		global $marc_liste_langues ;
		if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');
	
		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}
	
		$langues = array() ;
		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->notice_id)."' and ufield like '101' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);
			
			$subfield=array("0"=>"a","1"=>"c");
			
			while ($l=pmb_mysql_fetch_object($res_sql)) {
				if ($l->usubfield==$subfield[$quelle_langues]) {
					if (isset($marc_liste_langues->table[$l->value])) {
						$langues[] = array(
								'lang_code' => $l->value,
								'langue' => $marc_liste_langues->table[$l->value]
						) ;
					}
				}
			}
		}
	
		if (!$quelle_langues) $this->langues = $langues;
			else $this->languesorg = $langues;
	}
	
	public function fetch_avis() {
		$sql="select avg(note) as m from avis where valide=1 and type_object=1 and num_notice='$this->notice_id' group by num_notice";
		$r = pmb_mysql_query($sql);
	
		$sql_nb = "select * from avis where valide=1 and type_object=1 and num_notice='$this->notice_id'";
		$r_nb = pmb_mysql_query($sql_nb);
	
		$qte_avis = pmb_mysql_num_rows($r_nb);
		$loc = pmb_mysql_fetch_object($r);
		if($loc->m > 0) $moyenne=number_format($loc->m,1, ',', '');
	
		$this->avis_moyenne = $moyenne;
		$this->avis_qte = $qte_avis;
	}
	
	//public function affichage_etat_collections() {
	//	global $msg;
	//	global $pmb_etat_collections_localise;
	//
	//	//etat des collections
	//	$affichage="";
	//	if ($pmb_etat_collections_localise) {
	//		$restrict_location=" and idlocation=location_id";
	//		$table_location=",docs_location";
	//		$select_location=",location_libelle";
	//	} else $restrict_location=" group by id_serial";
	//	$rqt="select state_collections$select_location from collections_state$table_location where id_serial=".$this->notice_id.$restrict_location;
	//	$execute_query=pmb_mysql_query($rqt);
	//	if ($execute_query) {
	//		if (pmb_mysql_num_rows($execute_query)) {
	//			$affichage = "<br /><strong>".$msg["perio_etat_coll"]."</strong><br />";
	//			$bool=false;
	//			while ($r=pmb_mysql_fetch_object($execute_query)) {
	//				if ($r->state_collections) {
	//					if ($r->location_libelle) $affichage .= "<strong>".$r->location_libelle."</strong> : ";
	//					$affichage .= $r->state_collections."<br />\n";
	//					$bool=true;
	//				}
	//			}
	//			if ($bool==false) $affichage="";
	//		}
	//	}
	//	return $affichage;
	//}
	
	
	public function construit_liste_langues($tableau) {
		$langues = "";
		if (is_countable($tableau)) {
			for ($i = 0 ; $i < sizeof($tableau) ; $i++) {
				if ($langues) $langues.=" ";
				$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
			}
		}
		return $langues;
	}
	
	// Fonction d'affichage des avis
	public function affichage_avis($notice_id) {
		global $msg, $charset;
		/**
		 * //Affichage des Etoiles et nombre d'avis
		 */
		$nombre_avis = "";
	
		//Affichage des Etoiles et nombre d'avis
		if ($this->avis_qte > 0) {
			$nombre_avis = "<a href='#' class='donner_avis' title=\"".htmlentities($msg['notice_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&noticeid=$notice_id','avis'); return false;\">".$this->avis_qte."&nbsp;".htmlentities($msg['notice_bt_avis'],ENT_QUOTES,$charset)."</a>";
			$etoiles_moyenne = $this->stars($this->avis_moyenne);
		} else {
			$nombre_avis = "<a href='#' class='donner_avis' title=\"".htmlentities($msg['notice_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&noticeid=$notice_id','avis'); return false;\">".htmlentities($msg['avis_aucun'],ENT_QUOTES,$charset)."</a>";
			$cpt_star = -1;
		}

		// Affichage du nombre d'avis ainsi que la note moyenne et les etoiles associees
		$img_tag .= $nombre_avis."<a href='#' class='consult_avis' title=\"".htmlentities($msg['notice_title_avis'],ENT_QUOTES,$charset)."\" onclick=\"openPopUp('avis.php?todo=liste&noticeid=$notice_id','avis'); return false;\">".$etoiles_moyenne."</a>";
		return $img_tag;
	}
	
	// Gestion des etoiles pour les avis
	public function stars() {
		$etoiles_moyenne="";
		$cpt_star = 4;
	
		for ($i = 1; $i <= $this->avis_moyenne; $i++) {
			$etoiles_moyenne.="<img src='".get_url_icon('star.png')."' >";
		}
	
		if(substr($this->avis_moyenne,2) > 1) {
			$etoiles_moyenne .= "<img src='".get_url_icon('star-semibright.png')."' >";
			$cpt_star = 3;
		}
	
		for ( $j = round($this->avis_moyenne);$j <= $cpt_star ; $j++) {
			$etoiles_moyenne .= "<img src='".get_url_icon('star_unlight.png')."' >";
		}
		return $etoiles_moyenne;
	}
	
	// generation du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	public function genere_double($depliable=1, $premier='ISBD') {
		global $msg;
		global $css;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $tdoc;
		global $allow_tag, $charset ; // l'utilisateur a-t-il le droit d'ajouter un tag
	
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;
		$this->notice_childs = $this->genere_notice_childs();
		if ($this->cart_allowed) {
			if(isset($_SESSION["cart"]) && in_array("es".$this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".get_url_icon('basket_exist.png', 1)."\" alt=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\" /></a>";
			} else {
				$basket="<a href=\"cart_info.php?id=es".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."\" target=\"cart_info\" title=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"><img src=\"".get_url_icon("basket_small_20x20.png", 1)."\" alt=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"></a>";
			}
		} else {
			$basket="";
		}
		
		//add tags
		$img_tag = "";
		//if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) )
		//	$img_tag.="<a href='#' onclick=\"openPopUp('addtags.php?noticeid=$this->notice_id','ajouter_un_tag'); return false;\"><img src='".$opac_url_base."images/tag.png'  title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\" ></a>";
	
		 //Avis
		 //if ($this->avis_allowed) {
		//	$img_tag .= $this->affichage_avis($this->notice_id);
		 //}
	
		// preparation de la case a cocher pour traitement panier
		if ($cart_aff_case_traitement) {
		    $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]' title='".htmlentities($msg['rgaa_checkbox_check'],ENT_QUOTES,$charset)."' />&nbsp;";
		} else {
		    $case_a_cocher = "" ;
		}
	
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = (isset($icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc]) ? $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc] : "");
	
		$biblio_doc = marc_list_collection::get_instance('nivbiblio');
		if($depliable == 1){
			$template="
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher";
			if(!$this->notice_expired)
				$template.="
	    			<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" onClick=\"expandBase('el!!id!!', true); return false;\" />";
			if ($icon) $template.="
						<img src=\"".get_url_icon($icon, 1)."\" alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."' title='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
			$template.="
				<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-left:-6px;margin-bottom:6px;display:none;\">";
		}elseif($depliable == 2){
			$template="
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); return false;\">";
			if(!$this->notice_expired)
				$template.="
	    			<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" />";
			if ($icon) $template.="
						<img src=\"".get_url_icon($icon, 1)."\" alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."' title='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
			$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-left:-6px;margin-bottom:6px;display:none;\">";
		}else{
			$template="<div class='parent'>$case_a_cocher";
			if ($icon) $template.="<img src=\"".get_url_icon($icon, 1)."\"  alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
			$template.="<span class=\"notice-heada\" draggable=\"yes\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>";
		}
	 	$template.="!!CONTENU!!
					!!SUITE!!</div>";
	
	 	$template_in = "";
		//$template_in=$basket;
		$template_in.="<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
	    if ($premier=='ISBD') $template_in.="
	    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>
	    	<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    	<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
	    	<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>
			</ul>
			<div class='row'></div>
			<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
	  		else $template_in.="
		    	<li id='baskets!!id!!' class='onglet_basket'>$basket</li>
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
		    	<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>
				</ul>
				<div class='row'></div>
				<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
	  			<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
	
		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$lien_bull = "";//(count($this->get_bulletins()) ? "&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>" : "");
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>$lien_bull&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>$lien_bull&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
	
	
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', "es". $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);
	
		$this->result = str_replace('!!id!!', "es". $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);
		if ($this->affichage_resa_expl || $this->notice_childs) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl, $this->result);
		$this->result = str_replace('!!SUITE!!', "", $this->result);
		}
	
	// generation de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	public function genere_simple($depliable=1, $what='ISBD') {
		global $msg;
		global $opac_cart_allow;
		global $css;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $tdoc;
		global $allow_tag ; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $charset;
		
		$cpt_star = 4;
	
		$this->double_ou_simple = 1 ;
		$this->notice_childs = $this->genere_notice_childs();
		// preparation de la case a cocher pour traitement panier
		if ($cart_aff_case_traitement) {
			$case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]' title='".htmlentities($msg['rgaa_checkbox_check'],ENT_QUOTES,$charset)."' />&nbsp;";

		} else {
			$case_a_cocher = "" ;
		}
	
		if ($this->cart_allowed) {
			if(isset($_SESSION["cart"]) && in_array("es".$this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\"><img src=\"".get_url_icon('basket_exist.png', 1)."\"  alt=\"".$msg['notice_title_basket_exist']."\" /></a>";
			} else {
				$basket="<a href=\"cart_info.php?id=es".$this->notice_id."&header=".rawurlencode(strip_tags($this->notice_header))."\" target=\"cart_info\" title=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"><img src='".get_url_icon("basket_small_20x20.png", 1)."'  alt=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"></a>";
			}
		} else {
			$basket="";
		}
		
		//add tags
		$img_tag = "";
		/*
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag.="&nbsp;&nbsp;<a href='#' onclick=\"openPopUp('addtags.php?noticeid=$this->notice_id','ajouter_un_tag'); return false;\"><img src='".get_url_icon('tag.png', 1)."'  title=\"".$msg['notice_title_tag']."\" alt=\"".$msg['notice_title_tag']."\"></a>&nbsp;&nbsp;";
		*/
		 //Avis pas en notice externes
	/*	 if ($this->avis_allowed) {
			$img_tag .= $this->affichage_avis($this->notice_id);
		 }
	*/
		if ($basket) $basket="<div>".$basket.$img_tag."</div>";
	
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc];
	
		$biblio_doc = marc_list_collection::get_instance('nivbiblio');
		if($depliable == 1){
			$template="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg["expandable_notice"],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg["expandable_notice"],ENT_QUOTES,$charset)."\" onClick=\"expandBase('el!!id!!', true); return false;\" >";
			if ($icon) $template.="
					<img src=\"".get_url_icon($icon, 1)."\" alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."' title='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
			$template.="
	    		<span class=\"notice-heada\">!!heada!!</span><br />
	    		</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">".$basket."!!ISBD!!\n
				!!SUITE!!
				</div>";
		}elseif($depliable == 2){
			$template="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); return false;\">
	    		<img class='img_plus' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg["expandable_notice"],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg["expandable_notice"],ENT_QUOTES,$charset)."\" >";
			if ($icon) $template.="
					<img src=\"".get_url_icon($icon, 1)."\" alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."' title='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
			$template.="
	    		<span class=\"notice-heada\">!!heada!!</span></span><br />
	    		</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">".$basket."!!ISBD!!\n
				!!SUITE!!
				</div>";
		}else{
				$template="
				\n<div id=\"el!!id!!Parent\" class=\"parent\">
	    				$case_a_cocher";
				if ($icon) $template.="
					<img src=\"".get_url_icon($icon, 1)."\" alt='".$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc]."'/>";
				$template.="
	    				<span class=\"heada\">!!heada!!</span><br />
		    			</div>
				\n<div id='el!!id!!Child' class='child' >".$basket."
				!!ISBD!!
				!!SUITE!!
				</div>";
		}
	
	
		// Serials : difference avec les monographies on affiche [periodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$lien_bull = "";//(count($this->get_bulletins())  ? "&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>" : "");
			$template = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>$lien_bull&nbsp;!!ISBD!!", $template);
		} elseif ($this->notice->niveau_biblio =='a') {
			$template = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template);
			}
	
		$this->result = str_replace('!!id!!', "es". $this->notice_id, $template);
		$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
	
		if ($what=='ISBD') {
			$this->do_image($this->notice_isbd,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_isbd, $this->result);
		} else {
			$this->do_image($this->notice_public,$depliable);
			$this->result = str_replace('!!ISBD!!', $this->notice_public, $this->result);
		}
	
		$this->affichage_bulletinnage=$this->genere_bulletinage();
	
		if ($this->affichage_resa_expl || $this->notice_childs || $this->affichage_bulletinnage) $this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_bulletinnage, $this->result);
			else $this->result = str_replace('!!SUITE!!', '', $this->result);
	
		}
	
	// generation de l'isbd----------------------------------------------------
	public function do_isbd($short=0,$ex=1) {
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
	
		$this->notice_isbd="";
	
		if($this->notice_expired ){
			return $this->notice_isbd;
		}
	
		// constitution de la mention de titre
		$serie_temp = '';
		if(!empty($this->notice->serie_name)) {
			$serie_temp .= inslink($this->notice->serie_name,  str_replace("!!id!!","es". (isset($this->notice->tparent_id) ? $this->notice->tparent_id : ''), $this->lien_rech_serie));
			if(!empty($this->notice->tnvol))
				$serie_temp .= ',&nbsp;'.$this->notice->tnvol;
		}
		if ($serie_temp) $this->notice_isbd .= $serie_temp.".&nbsp;".$this->notice->tit1 ;
		else $this->notice_isbd .= $this->notice->tit1;
	
		$this->notice_isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		if (!empty($this->notice->tit3)) $this->notice_isbd .= "&nbsp;= ".$this->notice->tit3 ;
		if (!empty($this->notice->tit4)) $this->notice_isbd .= "&nbsp;: ".$this->notice->tit4 ;
		if (!empty($this->notice->tit2)) $this->notice_isbd .= "&nbsp;; ".$this->notice->tit2 ;
	
		if ($this->auteurs_tous) $this->notice_isbd .= " / ".$this->auteurs_tous;
	
		// mention d'edition
		if(!empty($this->notice->mention_edition)) $this->notice_isbd .= " &nbsp;. -&nbsp; ".$this->notice->mention_edition;
	
		if(!empty($this->notice->coll)){
			$collections = $this->notice->coll->titre.(isset($this->notice->coll->num) ? ", ".$this->notice->coll->num :"");
		} else {
			$collections = '';
		}
	
		if (is_array($this->publishers)) {
			for ($i=0; $i<count($this->publishers) ;$i++) {
			    $editeur[$i] = (!empty($this->publishers[$i]["name"]) ? $this->publishers[$i]["name"] : "").(!empty($this->publishers[$i]["city"])?" (".$this->publishers[$i]["city"].")":"");
			}
			$editeurs=implode("&nbsp;: ",$editeur);
		}
	
		if(!empty($this->notice->year)) {
		    if ($editeurs) {
		        $editeurs .= ', '.$this->notice->year;
		    } else {
		        $editeurs = $this->notice->year;
		    }
		} else if ($this->notice->niveau_biblio == 'm' && $this->notice->niveau_hierar == 0) {
		    if ($editeurs) {
		        $editeurs .= ', [s.d.]';
		    } else {
		        $editeurs = "[s.d.]";
		    }
		}
		if($editeurs) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$editeurs";
	
	
		// zone de la collation
		$collation = '';
		if(!empty($this->notice->npages))
			$collation .= $this->notice->npages;
		if(!empty($this->notice->ill))
			$collation .= '&nbsp;: '.$this->notice->ill;
		if(!empty($this->notice->size))
			$collation .= '&nbsp;; '.$this->notice->size;
		if(!empty($this->notice->accomp))
			$collation .= '&nbsp;+ '.$this->notice->accomp;
	
		if($collation)
			$this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$collation";
	
		if($collections) $this->notice_isbd .= ".&nbsp;-&nbsp;($collections)".' ';
	
		$this->notice_isbd .= '.';
	
		// ISBN ou NO. commercial
		$zoneISBN = '';
		if(!empty($this->notice->code)) {
			if(isISBN($this->notice->code)) $zoneISBN = '<b>ISBN</b>&nbsp;: ';
				else $zoneISBN .= '<b>'.$msg["issn"].'</b>&nbsp;: ';
			$zoneISBN .= $this->notice->code;
			}
		if(!empty($this->notice->prix)) {
			if($this->notice->code) $zoneISBN .= '&nbsp;: '.$this->notice->prix;
				else {
					if ($zoneISBN) $zoneISBN .= '&nbsp; '.$this->notice->prix;
						else $zoneISBN = $this->notice->prix;
					}
			}
		if($zoneISBN) $this->notice_isbd .= "<br />".$zoneISBN;
	
		// note generale
		if(!empty($this->notice->n_gen)) {
			$zoneNote = nl2br(htmlentities(implode("\n",$this->notice->n_gen),ENT_QUOTES, $charset));
			$this->notice_isbd .= "<br />".$zoneNote;
		}
	
		// langues
		if(count($this->langues)) {
			$langues = "<span class='etiq_champ'>{$msg[537]}</span>&nbsp;: ".$this->construit_liste_langues($this->langues);
			}
		if(count($this->languesorg)) {
			$langues .= " <span class='etiq_champ'>{$msg[711]}</span>&nbsp;: ".$this->construit_liste_langues($this->languesorg);
		}
		if (isset($langues)) $this->notice_isbd .= "<br />".$langues ;
	
		$html_pprerso = "";
		if(count($this->notice->notice_pperso)){
			foreach($this->notice->notice_pperso as $pperso){
				// est-ce bien de type pmb
				if($pperso['value'] && $pperso['libelle'] && $pperso['name'] ){
				    if(!empty($pperso['type']) && $pperso['type'] == 'url') {
				        $html_pprerso .= "<span class='etiq_champ'>".$pperso['libelle']."</span>&nbsp;: <a href='".$pperso['value']."' >".$pperso['value']."</a>";
				    } else {
				        $html_pprerso .= "<span class='etiq_champ'>".$pperso['libelle']."</span>&nbsp;: ".$pperso['value'];				        
				    }
				}
			}
		}
		if($html_pprerso)$this->notice_isbd .= "<br />".$html_pprerso ;
	
		if (!$short) {
			$this->notice_isbd .="<table>";
			$this->notice_isbd .= $this->aff_suite() ;
			$this->notice_isbd .="</table>";
		} else {
			$this->notice_isbd.=$this->genere_in_perio();
		}
	
	}
	
	// generation de l'affichage public----------------------------------------
	public function do_public($short=0,$ex=1) {
		global $msg;
		global $tdoc;
		global $charset;
	
		$this->notice_public="";
		if($this->notice_expired){
			return $this->notice_public;
		}
		$this->fetch_categories() ;
	
		$this->notice_public .= "<table>";
		// constitution de la mention de titre
		if (!empty($this->notice->serie_name)) {
			$this->notice_public.= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!","es". (isset($this->notice->tparent_id) ? $this->notice->tparent_id : ''), $this->lien_rech_serie));;
			if (!empty($this->notice->tnvol))
				$this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}
	
		$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td>".$this->notice->tit1 ;
	
		if (!empty($this->notice->tit4)) $this->notice_public .= ": ".$this->notice->tit4 ;
		$this->notice_public.="</td></tr>";
	
		if (!empty($this->notice->tit2)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		if (!empty($this->notice->tit3)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;
	
		if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";
	
		if (!empty($this->auteurs_tous)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
	
		// mention d'edition
		if (!empty($this->notice->mention_edition)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";
	
		// zone de l'editeur
		$annee = "";
		if (!empty($this->year)) {
		    $annee = "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['year_start']."</span></td><td>".$this->year."</td></tr>" ;
		}
	
		if (is_array($this->publishers) && (!empty($this->publishers[0]["name"]) || !empty($this->publishers[1]["name"])) ) {
			for ($i=0; $i<count($this->publishers) ;$i++) {
				$this->notice_public.= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>";
				$this->notice_public.= htmlentities($this->publishers[$i]["name"].(!empty($this->publishers[$i]["city"])?" (".$this->publishers[$i]["city"].")":""),ENT_QUOTES,$charset);
				$this->notice_public.= "</td></tr>";
			}
		}
		
		if ($annee) {
			$this->notice_public .= $annee ;
		}
		
		//titres uniformes
		if(isset($this->titres_uniformes) && count($this->titres_uniformes)){
			$this->notice_public.= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td><td>";
			for ($i=0; $i<count($this->titres_uniformes) ;$i++) {
				$this->notice_public.= htmlentities($this->titres_uniformes[$i],ENT_QUOTES,$charset)."<br />";
			}
			$this->notice_public."</td></tr>";
		}
		if(!empty($this->notice->coll)){
			$collection = $this->notice->coll->titre.(empty($this->notice->subcoll) && !empty($this->notice->coll->num) ? ". ".$this->notice->coll->num :"");
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>$collection</td></tr>" ;
		}
		if(!empty($this->notice->subcoll)){
			$subcollection = $this->notice->subcoll->titre.($this->notice->coll->num ? ". ".$this->notice->coll->num :"");
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>$subcollection</td></tr>" ;
		}
	
		// zone de la collation
		if(!empty($this->notice->npages))
			if ($this->notice->niveau_biblio<>"a") $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			else $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
	
		if (!empty($this->notice->ill))
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		if (!empty($this->notice->size))
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		if (!empty($this->notice->accomp))
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";
	
		// ISBN ou NO. commercial
		if (!empty($this->notice->code))
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".$this->notice->code."</td></tr>";
	
		if (!empty($this->notice->prix))
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";
	
		// note generale
		if (isset($this->notice->n_gen) && is_array($this->notice->n_gen) && count($this->notice->n_gen)) $zoneNote = nl2br(htmlentities(strip_tags(implode("\n",$this->notice->n_gen)),ENT_QUOTES, $charset));
		if (!empty($zoneNote)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
	
		// langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} else
			if (count($this->languesorg)) $this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
	
		$html_pprerso = "";
		if(count($this->notice->notice_pperso)){
			foreach($this->notice->notice_pperso as $pperso){
				// est-ce bien de type pmb
				if($pperso['value'] && $pperso['libelle'] && $pperso['name'] ){				    
				    if(!empty($pperso['type']) && $pperso['type'] == 'url') {
				        $html_pprerso .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$pperso['libelle']." :</span></td><td><a href='".$pperso['value']."' >".$pperso['value']."</a></td></tr>";
				    } else {
				        $html_pprerso .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$pperso['libelle']." :</span></td><td>".$pperso['value']."</td></tr>";
				    }
				}
			}
		}
		if($html_pprerso)$this->notice_public .= $html_pprerso ;
	
		//Documents numériques
		if ($this->docnums) {
			$this->notice_public .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['entrepot_notice_docnum']."</span></td><td>";
			$this->notice_public .= "<ul>";
			foreach($this->docnums as $docnum) {
				if (!$docnum["a"])
					continue;
				$this->notice_public .= "<li>";
				if ($docnum["b"])
					$this->notice_public .= $docnum["b"].": ";
				$this->notice_public .= "<i><a href=\"".htmlentities($docnum["a"],ENT_QUOTES,$charset)."\">".$docnum["a"]."</a></i>";
				$this->notice_public .= "</li>";
			}
			$this->notice_public .= "</ul>";
			$this->notice_public .= "</td></tr>";
		}
	
		if (!$short) $this->notice_public .= $this->aff_suite() ; else $this->notice_public.=$this->genere_in_perio();
		$this->notice_public.="</table>\n";
	
		$this->notice_public .= $this->expl_list();
		//if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;
	
		return;
		}
	
	// generation du header----------------------------------------------------
	public function do_header() {
		global $opac_notice_reduit_format ;
	
		$type_reduit = substr($opac_notice_reduit_format,0,1);
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-etre veut-on des personnalises ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="")
				$perso_voulus = explode(",",$perso_voulus_temp);
			}
	
		if ($type_reduit=="E") {
			// zone de l'editeur
			if (is_array($this->publishers[0])) {
				$editeur_reduit = $this->publishers[0]["name"].($this->publishers[0]["city"]?" (".$this->publishers[0]["city"].")":"") ;
				if ($this->publishers[0]["year"]) {
					$editeur_reduit .= " - ".$this->publishers[0]["city"]." ";
					}
				} elseif ($this->notice->year) { // annee mais pas d'editeur
					$editeur_reduit = $this->notice->year." ";
					}
			} else $editeur_reduit = "" ;
	
		//Si c'est un periodique, ajout du titre et bulletin
		$aff_perio_title = "";
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2)  {
			 $aff_perio_title .= "<i>in ".$this->parent_title;
			 if($this->parent_numero && (!empty($this->parent_date) || !empty($this->parent_aff_date_date))){
			 	$aff_perio_title .= " (".$this->parent_numero.", ".(!empty($this->parent_date)?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
			 } elseif(!$this->parent_numero && (!empty($this->parent_date) || !empty($this->parent_aff_date_date))){
			 	$aff_perio_title .= " (".(!empty($this->parent_date)?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
			 } elseif($this->parent_numero && !(!empty($this->parent_date) || !empty($this->parent_aff_date_date))){
			 	$aff_perio_title .= " (".$this->parent_numero.")</i>";
			 }
		}
		//Source
		if ($this->source_name) {
			$this->notice_header=$this->source_name." : ";
		}
		// recuperation du titre de serie
			// constitution de la mention de titre
		if(!empty($this->notice->serie_name)) {
			$this->notice_header .= $this->notice->serie_name;
			if(!empty($this->notice->tnvol))
				$this->notice_header .= ', '.$this->notice->tnvol;
			}
		if (!empty($this->notice->serie_name)) $this->notice_header .= ". ".$this->notice->tit1 ;
			else $this->notice_header.= $this->notice->tit1;
		if ($type_reduit=="T" && $this->notice->tit4) $this->notice_header = $this->notice_header." : ".$this->notice->tit4;
		if ($this->auteurs_principaux) $this->notice_header .= " / ".$this->auteurs_principaux;
		if ($editeur_reduit) $this->notice_header .= " / ".$editeur_reduit ;
		if ($aff_perio_title) $this->notice_header .= " ".$aff_perio_title;
	}
	
	
	// Construction des mots cle----------------------------------------------------
	public function do_mots_cle() {
		global $pmb_keyword_sep ;
		if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";
	
		if (!isset($this->notice->index_l) || !trim($this->notice->index_l)) return "";
	
		$tableau_mots = explode ($pmb_keyword_sep,trim($this->notice->index_l)) ;
	
		if (empty($tableau_mots)) {
			return "";
		}
		for ($i=0; $i<sizeof($tableau_mots); $i++) {
			$mots=trim($tableau_mots[$i]) ;
			$tableau_mots[$i] = inslink($mots, str_replace("!!mot!!", urlencode($mots), $this->lien_rech_motcle)) ;
		}
		$mots_cles = implode("&nbsp; ", $tableau_mots);
		return $mots_cles ;
	}
	
	//// recuperation des info de bulletinage (si applicable)
	//public function get_bul_info() {
	//	global $dbh;
	//	global $msg;
	//	// recuperation des donnees du bulletin et de la notice apparentee
	//	$requete = "SELECT b.tit1,b.notice_id,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
	//	$requete .= "from analysis a, notices b, bulletins c";
	//	$requete .= " WHERE a.analysis_notice=".$this->notice_id;
	//	$requete .= " AND c.bulletin_id=a.analysis_bulletin";
	//	$requete .= " AND c.bulletin_notice=b.notice_id";
	//	$requete .= " LIMIT 1";
	//	$myQuery = pmb_mysql_query($requete);
	//	if (pmb_mysql_num_rows($myQuery)) {
	//		$parent = pmb_mysql_fetch_object($myQuery);
	//		$this->parent_title = $parent->tit1;
	//		$this->parent_id = $parent->notice_id;
	//		$this->bul_id = $parent->bulletin_id;
	//		$this->parent_numero = $parent->bulletin_numero;
	//		$this->parent_date = $parent->mention_date;
	//		$this->parent_date_date = $parent->date_date;
	//		$this->parent_aff_date_date = $parent->aff_date_date;
	//		}
	//	}
	
	// fonction de generation de ,la mention in titre du perio + numero
	public function genere_in_perio () {
		global $charset ;
		
		$retour = "";
		// serials : si article
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			$bulletin = $this->parent_title;
			$notice_mere = inslink($this->parent_title, str_replace("!!id!!","es". (isset($this->parent_id) ? $this->parent_id : ''), $this->lien_rech_perio));
			if($this->parent_numero) {
				$numero = $this->parent_numero." " ;
			} else {
				$numero = '';
			}
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			if (!empty($this->parent_date))
				$date_affichee = " (".$this->parent_date.")";
			elseif (!empty($this->parent_date_date))
				$date_affichee .= " [".formatdate($this->parent_date_date)."]";
			else $date_affichee="" ;
			$bulletin = inslink($numero.$date_affichee, str_replace("!!id!!","es". intval($this->bul_id), $this->lien_rech_bulletin));
			$mention_parent = "<b>in</b> $notice_mere > $bulletin ";
			$retour .= "<br />$mention_parent";
			$pagination = "";
			if (isset($this->notice->npages)) {
			    $pagination = htmlentities($this->notice->npages,ENT_QUOTES, $charset);
			}
			if ($pagination) $retour .= ".&nbsp;-&nbsp;$pagination";
		}
		return $retour ;
	}
	
	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour eviter la redondance de calcul
	public function aff_suite() {
		global $msg;
		global $charset;
		global $mode;
		global $opac_allow_tags_search;
	
		// afin d'eviter de recalculer un truc deja calcule...
		if (!empty($this->affichage_suite)) return $this->affichage_suite ;
	
		$ret = '';
		
		// serials : si article
		$ret .= $this->genere_in_perio () ;
	
		//Espace
		$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
	
		// resume
		if(!empty($this->notice->n_resume))
	 		$ret .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td>".nl2br(htmlentities(strip_tags(implode("\n",$this->notice->n_resume)),ENT_QUOTES, $charset))."</td></tr>";
	
		// note de contenu
		if(!empty($this->notice->n_contenu))
	 		$ret .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities(strip_tags(implode("\n",$this->notice->n_contenu)),ENT_QUOTES, $charset))."</td></tr>";
	
		// Categories
		if(!empty($this->categories_toutes))
			$ret .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['categories_start']."</span></td><td>".$this->categories_toutes."</td></tr>";
	
		// Concepts
// 		$concepts_list = new skos_concepts_list();
// 		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
// 			$ret .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['concepts_start']."</span></td><td>".skos_view_concepts::get_list_in_notice($concepts_list)."</td></tr>";
// 		}
	
	
		// Affectation du libelle mots cles ou tags en fonction de la recherche precedente
	
		if($opac_allow_tags_search == 1)
			$libelle_key = $msg['tags'];
		else
			$libelle_key = 	$msg['motscle_start'];
	
		// indexation libre
		$mots_cles = $this->do_mots_cle() ;
		if($mots_cles)
			$ret .= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".$mots_cles."</td></tr>";
	
	
		if(isset($this->notice->indexint) && is_array($this->notice->indexint) && count($this->notice->indexint)) {
			$indexint = implode("<br>",$this->notice->indexint);
			$ret.= "<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>$indexint</td></tr>" ;
		}
	
		if (!empty($this->notice->lien)) {
			$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
			$ret.="<tr><td class='align_right bg-grey'><span class='etiq_champ'>".$msg["lien_start"]."</span></td><td>" ;
			if (isset($this->notice->eformat) && substr($this->notice->eformat,0,3)=='RSS') {
				$ret .= affiche_rss($this->notice->notice_id) ;
			} else {
				$lien = $this->notice->lien;
				if (($this->connector_id == 'cairn') || (strpos($lien, "cairn.info") !== false)) {
					$cairn_connector = new cairn();
					$cairn_sso_params = $cairn_connector->get_sso_params();
					if ($cairn_sso_params && (strpos($lien, '?') === false)) {
						$lien.= '?';
						$cairn_sso_params = substr($cairn_sso_params, 1);
					}
					$lien.= $cairn_sso_params;
				}
				$ret.="<a href=\"".$lien."\" target=\"top\">".htmlentities(!empty($this->notice->lien_texte)?$this->notice->lien_texte:$lien,ENT_QUOTES,$charset)."</a>";
			}
			$ret.="</td></tr>";
			if (!empty($this->notice->eformat) && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td class='align_right bg-grey'><b>".$msg["eformat_start"]."</b></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}
	
		$this->affichage_suite = $ret;
		return $ret;
	}
	
	
	// fonction de generation du tableau des exemplaires
	public function expl_list() {
		global $msg;
		global $expl_list_header, $expl_list_footer;
	
		if (!$this->exemplaires)
			return;
	
		$expl_output = $expl_list_header;
		$count = 1;
	
		$expl996 = array(
			"f" => $msg["extexpl_codebar"],
			"k" => $msg["extexpl_cote"],
			"v" => $msg["extexpl_location"],
			"x" => $msg["extexpl_section"],
			"1" => $msg["extexpl_statut"],
			"a" => $msg["extexpl_emprunteur"],
			"e" => $msg["extexpl_doctype"],
			"u" => $msg["extexpl_note"]
		);
	
		$final_location = array();
		foreach ($this->exemplaires as $expl) {
			$alocation = array();
			//Si on trouve une localisation, on la convertie en libelle et on l'oublie si spécifié
			if (isset($expl["v"]) && preg_match("/\d{9}/", $expl["v"]) && $this->entrepots_localisations) {
				if (isset($this->entrepots_localisations[$expl["v"]])) {
					if (!$this->entrepots_localisations[$expl["v"]]["visible"]) {
						continue;
					}
					$alocation["priority"] = $this->entrepots_localisations[$expl["v"]]["visible"];
	
					$expl["v"] = $this->entrepots_localisations[$expl["v"]]["libelle"];
				}
			}
			if (!isset($alocation["priority"]))
				$alocation["priority"] = 1;
			$alocation["content"] = $expl;
			$final_location[] = $alocation;
		}
	
		if (!$final_location)
			return;
	
		//trions
		usort($final_location, "cmpexpl");
	
		$expl_output .= "<tr>";
		foreach ($expl996 as $caption996) {
			$expl_output .= "<th>".$caption996."</th>";
		}
		$expl_output .= "</tr>";
	
		foreach ($final_location as $expl) {
			$axepl_output = "<tr>";
			foreach ($expl996 as $key996 => $caption996) {
				if (isset($expl["content"][$key996])) {
					$axepl_output .= "<td>".$expl["content"][$key996]."</td>";
				} else {
					$axepl_output .= "<td></td>";
				}
			}
			$axepl_output .= "</tr>";
			$expl_output .= $axepl_output;
			$count++;
		}
		$expl_output .= $expl_list_footer;
	
		return $expl_output;	
	} // fin function expl_list
	
	public function do_image(&$entree,$depliable) {
		global $charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_url ;
		global $opac_book_pics_msg;
		global $opac_url_base ;
		global $msg;
		$image = "";
		if (isset($this->notice->code) && $this->notice->code<>"") {
			if ($opac_show_book_pics=='1' && $opac_book_pics_url) {
				$url_image_ok = getimage_url($this->notice->code, "");
				$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
				if(!trim($title_image_ok)){
					$title_image_ok = htmlentities($this->notice->tit1, ENT_QUOTES, $charset);
				}

				if ($depliable) {
					$image = "<img class='vignetteimg align_right' src='".$opac_url_base."images/vide.png' title=\"".$title_image_ok."\" vigurl=\"".$url_image_ok."\" alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."'>";
				} else {
					$image = "<img class='vignetteimg align_right' src='".$url_image_ok."' title=\"".$title_image_ok."\" alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."'>";
				}
			}
		}
		if ($image) {
			$entree = "<table style='width:100%' role='presentation'><tr><td>$entree</td><td style='vertical-align:top' class='align_right'>$image</td></tr></table>" ;
		} else {
			$entree = "<table style='width:100%' role='presentation'><tr><td>$entree</td></tr></table>" ;
		}
	}
	
	public function genere_notice_childs(){
		global $msg, $opac_notice_affichage_class ;
	
		$this->antiloop[$this->notice_id]=true;
		//Notices liees
		if ($this->notice_childs) return $this->notice_childs;
		if ((count($this->childs))&&(!$this->to_print)) {
			if ($this->seule) $affichage="";
				else $affichage = "<a href='".str_replace("!!id!!","es".$this->notice_id,$this->lien_rech_notice)."&seule=1'>".$msg['voir_contenu_detail']."</a>";
			global $relation_typedown;
			if (!$relation_typedown) $relation_typedown=new marc_list("relationtypedown");
			reset($this->childs);
			$affichage.="<br />";
			foreach ($this->childs as $rel_type => $child_notices) {
				$affichage="<b>".$relation_typedown->table[$rel_type]."</b>";
				if ($this->seule) {
						} else $affichage.="<ul>";
				$bool=false;
				for ($i=0; (($i<count($child_notices))&&(($i<20)||($this->seule))); $i++) {
					if (!$this->antiloop[$child_notices[$i]]) {
						if ($opac_notice_affichage_class) $child_notice=new $opac_notice_affichage_class($child_notices[$i],$this->liens,$this->cart_allowed,$this->to_print);
							else $child_notice=new notice_affichage($child_notices[$i],$this->liens,$this->cart_allowed,$this->to_print);
							if ($child_notice->notice->niveau_biblio!='b' || ($child_notice->notice->niveau_biblio=='b' && $this->notice->niveau_biblio != "s")) {
							$child_notice->antiloop=$this->antiloop;
							$child_notice->do_header();
							if ($this->seule) {
								$child_notice->do_isbd();
								$child_notice->do_public();
								if ($this->double_ou_simple == 2 ) $child_notice->genere_double(1, $this->premier) ;
								$child_notice->genere_simple(1, $this->premier) ;
								$affichage .= $child_notice->result ;
							} else {
								$child_notice->visu_expl = 0 ;
								$child_notice->visu_explnum = 0 ;
								$affichage.="<li><a href='".str_replace("!!id!!","es".$child_notices[$i],$this->lien_rech_notice)."'>".$child_notice->notice_header."</a></li>";
							}
							$bool=true;
						}
					}
				}
				if ($bool==true) $aff_childs.=$affichage;
				if ((count($child_notices)>20)&&(!$this->seule)) {
					$aff_childs.="<br />";
					if ($this->lien_rech_notice) $aff_childs.="<a href='".str_replace("!!id!!","es".$this->notice_id,$this->lien_rech_notice)."&seule=1'>";
					$aff_childs.=sprintf($msg["see_all_childs"],20,count($child_notices),count($child_notices)-20);
					if ($this->lien_rech_notice) $aff_childs.="</a>";
				}
				if ($this->seule) {
				} else $aff_childs.="</ul>";
			}
			$this->notice_childs=$aff_childs."<br />";
		} else $this->notice_childs = "" ;
		return $this->notice_childs ;
	}

	public function genere_bulletinage(){
		global $msg;

		$html="";
		if($this->notice->niveau_biblio == "s" && $this->notice->niveau_hierar == "1"){
			$query ="select
				serial.recid as bulletin_id,
				bulletin_num.value as bulletin_num,
				bulletin_date.value as bulletin_date,
				bulletin_date_date.value as bulletin_date_date,
				group_concat(distinct analysis.recid) as analysis
			from entrepot_source_".$this->source_id." as serial
			join entrepot_source_".$this->source_id." as bulletin_num on serial.recid = bulletin_num.recid and bulletin_num.ufield='463' and bulletin_num.usubfield = 'v'
			left join entrepot_source_".$this->source_id." as bulletin_title on serial.recid = bulletin_title.recid and bulletin_title.ufield='463' and bulletin_title.usubfield = 't'
			left join entrepot_source_".$this->source_id." as bulletin_date on serial.recid = bulletin_date.recid and bulletin_date.ufield='463' and bulletin_date.usubfield = 'd'
			left join entrepot_source_".$this->source_id." as bulletin_date_date on serial.recid = bulletin_date_date.recid and bulletin_date_date.ufield='463' and bulletin_date_date.usubfield = 'e'
			left join entrepot_source_".$this->source_id." as analysis on serial.recid = analysis.recid
			where serial.ufield='461' and serial.usubfield='t' and serial.value = '".addslashes($this->notice->tit1)."'
			group by bulletin_num.value,bulletin_date.value,bulletin_date_date.value order by bulletin_date.value desc,bulletin_num.value desc";

			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$html="
				<h3><span class='titre_exemplaires'>".$msg['see_bull']."</span></h3>";
				while($row = pmb_mysql_fetch_object($result)){
// 					highlight_string(print_r($row,true));
					$html.=$this->genere_bulletin($row);
				}
			}
		}
		return $html;
	}

	public function genere_bulletin($bulletin_infos){
		global $opac_notices_depliable;
		$html.="<div id='es_bull_".$bulletin_infos->bulletin_id."' class='notice-parent'></div>";
		$titre = $bulletin_infos->bulletin_num;
		if($bulletin_infos->bulletin_date){
			$titre.= " (".format_date($bulletin_infos->bulletin_date).")";
		}
		if($bulletin_infos->bulletin_titre){
			$titre.=": ".$bulletin_infos->bulletin_titre;
		}
		$contenu = "";
		$analysis = explode(",",$bulletin_infos->analysis);

		$opac_notices_depliable=1;
		foreach($analysis as $article){
			$contenu.=aff_notice_unimarc($article);
		}
		$opac_notices_depliable=0;
		return gen_plus("es_bull_".$bulletin_infos->bulletin_id,$titre, $contenu);
	}

//	public public function get_bulletins(){
//		global $dbh;
//
//		if($this->notice->opac_visible_bulletinage){
//			$requete = "SELECT * FROM bulletins where bulletin_id in(
//				SELECT bulletin_id FROM bulletins WHERE bulletin_notice='".$this->notice_id."' and num_notice=0
//				) or bulletin_id in(
//				SELECT bulletin_id FROM bulletins,notice_statut, notices WHERE bulletin_notice='".$this->notice_id."'
//				and notice_id=num_notice
//				and statut=id_notice_statut
//				and((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")) ";
//			$res = pmb_mysql_query($requete);print $requete; exit();
//			if(pmb_mysql_num_rows($res)){
//				return pmb_mysql_fetch_array($res);
//			}
//		} else return 0;
//	}

	public function __get($name) {
	    if (isset($this->notice->{$name})) {
	        return $this->notice->{$name};
	    }
	    if (isset($this->{$name})) {
	        return $this->{$name};
	    }	    
	    return null;
	}
	
	public function get_print_css_style() {
	    $css_style = "
			<style type='text/css'>
				td.bg-grey {
					width:156;
				}
				td.public_line_value {
					width:551;
				}
				.vignetteimg {
				    max-width: 140px;
				    max-height: 200px;
				    -moz-box-shadow: 1px 1px 5px #666666;
				    -webkit-box-shadow: 1px 1px 5px #666666;
				    box-shadow: 1px 1px 5px #666666;
				}
			</style>
				";
	    return $css_style;
	}
}
