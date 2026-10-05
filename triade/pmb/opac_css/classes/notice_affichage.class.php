<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage.class.php,v 1.571.4.3 2025/03/21 10:30:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $base_path, $class_path, $include_path;
require_once($base_path."/classes/author.class.php");
require_once($base_path."/classes/collection.class.php");
require_once($base_path."/classes/subcollection.class.php");
require_once($base_path."/classes/categorie.class.php");
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
require_once($class_path."/tu_notice.class.php");
require_once($class_path."/collstate.class.php");
require_once($class_path."/acces.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/notice_affichage.ext.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($class_path."/enrichment.class.php");
require_once($include_path."/interpreter/bbcode.inc.php");
require_once($class_path.'/facette_search.class.php');
require_once($base_path."/includes/explnum.inc.php");
require_once($class_path."/notice_onglet.class.php");
require_once($class_path."/explnum.class.php");
require_once($class_path."/authperso_notice.class.php");
require_once($class_path."/map/map_objects_controler.class.php");
require_once($class_path."/map_info.class.php");
require_once($class_path."/skos/skos_concepts_list.class.php");
require_once($class_path."/skos/skos_view_concepts.class.php");
require_once($class_path."/record_display.class.php");
require_once($class_path."/avis.class.php");
require_once($class_path."/liste_lecture.class.php");
require_once($class_path."/map/map_locations_controler.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/exemplaires.class.php");
require_once($class_path."/record_display.class.php");
require_once($base_path."/admin/connecteurs/in/cairn/cairn.class.php");
require_once $class_path."/pnb/dilicom.class.php";

global $tdoc;
if (empty($tdoc)) $tdoc = new marc_list('doctype');
global $fonction_auteur;
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// définition de la classe d'affichage des notices
class notice_affichage {
	public $notice_id		= 0;					// id de la notice à afficher
	public $notice_header	= "" ;					// titre + auteur principaux
	public $notice_header_without_html	= "" ;		// titre + auteur principaux sans <span>
	public $notice_header_with_link="" ;			// titre + auteur principaux avec un lien sur la notice
	public $notice_header_globe_link	= "" ;		// le globe du lien
			// le terme affichage correspond au code HTML qui peut être envoyé avec un print
	public $notice_isbd	= "" ;			// Affichage ISBD de la notice
	public $notice_isbd_simple = ""; //Affichage SIMPLIFIE de l'ISBD de la notice
	public $notice_public	= "" ;			// Affichage public PMB de la notice
	public $notice_indexations	= "" ;		// Affichage des indexations catégories et mots clés, peut être ajouté à $notice_isbd ou à $notice_public afin d'avoir l'affichage complet PMB
	public $notice_exemplaires	= "" ;		// Affichage des exemplaires, peut être ajouté à $notice_isbd ou à $notice_public afin d'avoir l'affichage complet PMB
	public $notice_explnum	= "" ;			// Affichage des exemplaires numériques, peut être ajouté à $notice_isbd ou à $notice_public afin d'avoir l'affichage complet PMB
	public $notice_notes	= "" ;			// Affichage des notes de contenu et résumé, peut être ajouté à $notice_isbd ou à $notice_public afin d'avoir l'affichage complet PMB
	public $notice;				// objet notice tel que fetché dans la table notices,
						//		augmenté de $this->notice->serie_name si série il y a
						//		augmenté de n_gen, n_contenu, n_resume si on est allé les chercher car non ISBD standard
	public $responsabilites 	= array("responsabilites" => array(),"auteurs" => array());  // les auteurs avec tout ce qu'il faut
	public $categories 	= array();	// les id des categories
	public $auteurs_principaux	= "" ;		// ce qui apparait après le titre pour le header
  	public $auteurs_tous	= "" ;		// Tous les auteurs avec leur fonction
  	public $categories_toutes	= "" ;		// Toutes les catégories dans lesquelles est rangée la notice

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
	public $sugg_allowed = 0;
	public $liste_lecture_allowed = 0;
	public $to_print = 0;
	public $affichage_resa_expl = "" ; // lien réservation, exemplaires et exemplaires numériques, en tableau comme il faut
	public $affichage_expl = "" ;  // la même chose mais sans le lien réservation
	public $affichage_avis_detail=""; // affichage des avis de lecteurs

	public $statut = 1 ;  			// Statut (id) de la notice
	public $statut_notice = "" ;  	// Statut (libellé) de la notice
	public $visu_notice = 1 ;  	// Visibilité de la notice à tout le monde
	public $visu_notice_abon = 0 ; // Visibilité de la notice aux abonnés uniquement
	public $visu_expl = 1 ;  		// Visibilité des exemplaires de la notice à tout le monde
	public $visu_expl_abon = 0 ;  	// Visibilité des exemplaires de la notice aux abonnés uniquement
	public $visu_explnum = 1 ;  	// Visibilité des exemplaires numériques de la notice à tout le monde
	public $visu_explnum_abon = 0 ;// Visibilité des exemplaires numériques de la notice aux abonnés uniquement
	public $visu_scan_request = 1; // Visibilité du lien de demande de numérisation
	public $visu_scan_request_abon = 0; // Visibilité du lien de demande de numérisation aux abonnés uniquement

	public $childs = array() ; // filles de la notice
	public $notice_childs = "" ; // l'équivalent à afficher
	public $anti_loop="";
	public $seule = 0 ;
	public $premier = "PUBLIC" ;
	public $double_ou_simple = 2 ;
	public $avis = null;

	public $antiloop=array();
	public $bulletin_id=0;		// id du bulletin s'il s'agit d'une notice de bulletin

	public $dom_2 = null;			// objet domain
	public $dom_3 = null;			// objet domain
	public $rights = 0;			// droits d'acces emprunteur/notice

	public $header_only = 0;		// pour ne prendre que le nécessaire pour composer le titre
	public $parents = "";			// la chaine des parents, utilisée pour do_parents en isbd et en public
	public $no_header = 0 ;		// ne pas afficher de header, permet de masquer l'icône
	public $notice_header_without_doclink=""; // notice_header sans les icones de lien url et d'indication de documents numériques
	public $notice_header_doclink=""; // les icones de lien url et d'indication de documents numériques
	public $notice_affichage_cmd;
	public $notice_affichage_enrichment="";
	public $authperso_info=array();
	public $datetime = "";
	public $hash = "";
	public $show_map = 1;
	public $docnum_allowed;
	public $parent_id = 0;
	public $parent_title = "";
	public $parent_numero = "";
	public $parent_date = "";
	public $parent_date_date = "";
	public $parent_aff_date_date = "";
	public $congres_tous = "";
	public $map;
	public $map_info;
	public $affichage_suite_flag;
	public $affichage_suite;
	public $memo_perso_;
	public $bulletin_numero;
	public $bulletin_date;
	public $permalink;
	public $affichage_simili_search_head;
	public $affichage_resa_expl_flag;
	public $publishers = [];
	public $collections = [];

	protected $affichage_demand = ""; // Bouton de création de demande à partir d'une notice
	protected $affichage_scan_requests = ""; // Bouton de création de demande de numérisation à partir d'une notice

	private $parents_header_without_html = false;
	protected $notice_relations;

	protected $record_datas;

	protected $display_childs = true; //affichage ou non des notices filles (dérivable en classe d'aff perso)

	protected $notice_reduit_format;

	protected $onglet_perso;

	// constructeur------------------------------------------------------------
	public function __construct($id, $liens="", $cart=0, $to_print=0,$header_only=0,$no_header=0, $show_map=1, $parents_header_without_html = false) {
	  	// $id = id de la notice à afficher
	  	// $liens	 = tableau de liens tel que ci-dessous
	  	// $cart : afficher ou pas le lien caddie
	  	// $to_print = affichage mode impression ou pas

		global $opac_avis_allow;
		global $opac_allow_add_tag;
		global $opac_show_suggest_notice;
		global $opac_shared_lists;
		global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
		global $memo_expl;

		$this->show_map = $show_map;
		$memo_expl = array();
		if (!$id) return;
		$id = intval($id);
		//droits d'acces emprunteur/notice
		$this->dom_2=null;
		$this->dom_3=null;
		if ($gestion_acces_active==1 && ($gestion_acces_empr_notice || $gestion_acces_empr_docnum)) {
			$ac= new acces();
			if ($gestion_acces_empr_notice == 1) {
				$this->dom_2= $ac->setDomain(2);
				$this->rights= $this->dom_2->getRights($_SESSION['id_empr_session'], $id);
			}
			if ($gestion_acces_empr_docnum == 1) {
				$this->dom_3= $ac->setDomain(3);
			}
		}
	 	if (!$liens) $liens=array();
	 	$this->set_liens_rech($liens);
		$this->liens = $liens;
		$this->cart_allowed = $cart;
		$this->no_header = $no_header ;
		if ($to_print) {
			$this->avis_allowed = 0;
			$this->tag_allowed = 0;
			$this->sugg_allowed = 0;
			$this->liste_lecture_allowed = 0;
		} else {
			$this->avis_allowed = $opac_avis_allow;
			$this->tag_allowed = $opac_allow_add_tag;
			$this->sugg_allowed = $opac_show_suggest_notice;
			$this->liste_lecture_allowed = $opac_shared_lists;
		}

		$this->to_print = $to_print;
		$this->header_only = $header_only;
	  	// $seule : si 1 la notice est affichée seule et dans ce cas les notices childs sont en mode dépliable
	  	global $seule ;
	  	$this->seule = $seule ;
	  	$this->docnum_allowed = 1;

	  	$this->parents_header_without_html = $parents_header_without_html;

	  	if(!$id) return;
		else {
			$this->notice_id = $id;
			$this->record_datas = record_display::get_record_datas($this->notice_id);
			if(!$this->fetch_data()) return;
		}
		global $memo_p_perso_notices;
		if(!$memo_p_perso_notices)
			$memo_p_perso_notices=$this->p_perso=new parametres_perso("notices");
		else $this->p_perso=$memo_p_perso_notices;

		$date = new DateTime();
		$this->datetime = $date->getTimestamp();
		$this->hash = $this->generate_hash();
	}

	// récupération des valeurs en table---------------------------------------
	public function fetch_data() {
		global $opac_map_activate;

		if(is_null($this->dom_2)) {
			$requete = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, statut, thumbnail_url, (opac_visible_bulletinage&0x1) as opac_visible_bulletinage, opac_serialcirc_demande, notice_is_new ";
			$requete.= "FROM notices WHERE notice_id='".$this->notice_id."' ";
		} else {
			$requete = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, thumbnail_url, (opac_visible_bulletinage&0x1) as opac_visible_bulletinage, opac_serialcirc_demande, notice_is_new ";
			$requete.= "FROM notices ";
			$requete.= "WHERE notice_id='".$this->notice_id."'";
		}
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)) {
			$this->notice = pmb_mysql_fetch_object($myQuery);
		} else {
			$this->statut_notice =			"" ;
			$this->statut =				  	0 ;
			$this->visu_notice =          	0 ;
			$this->visu_notice_abon =     	0 ;
			$this->visu_expl =            	0 ;
			$this->visu_expl_abon =       	0 ;
			$this->visu_explnum =         	0 ;
			$this->visu_explnum_abon =    	0 ;
			$this->visu_scan_request =		0 ;
			$this->visu_scan_request_abon =	0 ;
			$this->notice_id=0;
			$this->opac_visible_bulletinage=0;
			return 0 ;
		}

		if (!$this->notice->typdoc) $this->notice->typdoc='a';
		$this->notice->serie_name = '';
		if ($this->notice->tparent_id) {
			$requete_serie = "SELECT serie_name FROM series WHERE serie_id='".$this->notice->tparent_id."' ";
			$myQuery_serie = pmb_mysql_query($requete_serie);
			if (pmb_mysql_num_rows($myQuery_serie)) {
				$serie = pmb_mysql_fetch_object($myQuery_serie);
				$this->notice->serie_name = $serie->serie_name ;
			}
		}
		// serials : si article
		if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) $this->get_bul_info();
		if ($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2) $this->get_bul_info();

		if(!$this->header_only)$this->fetch_categories();
		$this->fetch_auteurs();
		$this->fetch_titres_uniformes();
		$this->fetch_visibilite();
		if(!$this->header_only) $this->fetch_langues(0);
		if(!$this->header_only) $this->fetch_langues(1);
		if(!$this->header_only) $this->avis = new avis($this->notice_id);
		if(!$this->header_only){
			$this->authperso_info=array();
			$authperso = new authperso_notice($this->notice_id);
			$this->authperso_info =$authperso->get_info();
		}

		$this->map=new stdClass();
		$this->map_info=new stdClass();
		if($opac_map_activate==1 || $opac_map_activate==2){
			$ids[]=$this->notice_id;
			$this->map=new map_objects_controler(TYPE_RECORD,$ids);
			$this->map_info=new map_info($this->notice_id);
		}

// 		$this->childs=array();
		if(!$this->header_only) {
			$this->notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
// 			if ($this->notice->niveau_biblio =='b') {
// 				if (is_null($this->dom_2)) {
// 					$acces_j='';
// 					$statut_j=',notice_statut';
// 					$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
// 				} else {
// 					$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
// 					$statut_j = "";
// 					$statut_r = "";
// 				}
// 				// notice de bulletins, les relations sont dans la table analysis
// 				$requete = "select analysis_notice as notice_id, 'd' as relation_type from analysis JOIN bulletins ON bulletin_id = analysis_bulletin, notices $acces_j  $statut_j ";
// 				$requete.= "where num_notice=$this->notice_id AND notice_id = analysis_notice $statut_r ";
// 				$requete.= "order by analysis_notice ASC";
// 				$resultat=pmb_mysql_query($requete); // il y a des enfants ?
// 				if (pmb_mysql_num_rows($resultat)) {
// 					while (($r=pmb_mysql_fetch_object($resultat))) $this->childs[$r->relation_type][]=$r->notice_id;
// 				}
// 			} else {
// 				// autres notices
// 				$this->childs = $this->notice_relations->get_childs();
// 			}
			$this->do_parents();
		}
		// On traite les connecteurs pour les notices externes
		$this->do_connectors();

		return pmb_mysql_num_rows($myQuery);
	} // fin fetch_data


	public function fetch_visibilite() {
		global $hide_explnum;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac = new acces();
			$this->dom_2= $ac->setDomain(2);
			if ($hide_explnum) {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->notice_id,4);
			} else {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->notice_id);
			}
		} else {
			$requete = "SELECT opac_libelle, notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon, notice_scan_request_opac, notice_scan_request_opac_abon FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$statut_temp = pmb_mysql_fetch_object($myQuery);

				$this->statut_notice =			$statut_temp->opac_libelle;
				$this->visu_notice =          	$statut_temp->notice_visible_opac;
				$this->visu_notice_abon =     	$statut_temp->notice_visible_opac_abon;
				$this->visu_expl =            	$statut_temp->expl_visible_opac;
				$this->visu_expl_abon =       	$statut_temp->expl_visible_opac_abon;
				$this->visu_explnum =         	$statut_temp->explnum_visible_opac;
				$this->visu_explnum_abon =		$statut_temp->explnum_visible_opac_abon;
				$this->visu_scan_request =		$statut_temp->notice_scan_request_opac;
				$this->visu_scan_request_abon =	$statut_temp->notice_scan_request_opac_abon;

				if ($hide_explnum) {
					$this->visu_explnum=0;
					$this->visu_explnum_abon=0;
				}
			}
		}
	} // fin fetch_visibilite()

	public function get_display_author_name($author_name='', $author_rejete='') {
		if ($author_rejete) $display = $author_rejete." ".$author_name;
		else  $display = $author_name;
		return $display;
	}

	protected function displayed_responsability_fonction() {
		return true;
	}

	public function get_responsabilites() {
		global $fonction_auteur;
		global $pmb_authors_qualification;
		global $msg, $charset;

		$this->responsabilites = array(
				'responsabilites' => array(),
				'auteurs' => array()
		);

		$rqt = "SELECT author_id, responsability_fonction, responsability_type, id_responsability, author_type,author_name, author_rejete, author_type, author_date, author_see, author_web, author_isni ";
		$rqt.= "FROM responsability, authors ";
		$rqt.= "WHERE responsability_notice='".$this->notice_id."' AND responsability_author=author_id ";
		$rqt.= "ORDER BY responsability_type, responsability_ordre " ;
		$res_sql = pmb_mysql_query($rqt);
		while (($notice=pmb_mysql_fetch_object($res_sql))) {
			$this->responsabilites['responsabilites'][] = $notice->responsability_type ;
			$info_bulle="";
			if($notice->author_type==72 || $notice->author_type==71) {
				$congres=new auteur($notice->author_id);
				$auteur_isbd=$congres->get_isbd();
				$auteur_titre=$congres->display;
				$info_bulle=" title='".$congres->info_bulle."' ";
			} else {
				$auteur_isbd = $this->get_display_author_name($notice->author_name, $notice->author_rejete);
				// on s'arrête là pour auteur_titre = "Prénom NOM" uniquement
				$auteur_titre = $auteur_isbd ;
				// on complète auteur_isbd pour l'affichage complet
				if ($notice->author_date) $auteur_isbd .= " (".$notice->author_date.")" ;
			}
			$qualification = '';
			if ($pmb_authors_qualification) {
			    if ($notice->responsability_type == 0) {
			        $vedette_type = TYPE_NOTICE_RESPONSABILITY_PRINCIPAL;
			    } elseif ($notice->responsability_type == 1) {
			        $vedette_type = TYPE_NOTICE_RESPONSABILITY_AUTRE;
			    } else {
			        $vedette_type = TYPE_NOTICE_RESPONSABILITY_SECONDAIRE;
			    }
			    $qualif_id = vedette_composee::get_vedette_id_from_object($notice->id_responsability, $vedette_type);
			    if($qualif_id){
			        $qualif = new vedette_composee($qualif_id);
			        $qualification = $qualif->get_label();
			    }
			}
			// URL de l'auteur
			if ($notice->author_web) $auteur_web_link = " <a href='$notice->author_web' target='_blank' type='external_url_autor' title='".htmlentities($msg['rgaa_author_field_link'], ENT_QUOTES, $charset)."'><img src='".get_url_icon("globe.gif", 1)."' style='border:0px'/></a>";
			else $auteur_web_link = "" ;
			if (!$this->to_print) $auteur_isbd .= $auteur_web_link ;
			$auteur_isbd = inslink($auteur_isbd, str_replace("!!id!!", $notice->author_id, $this->lien_rech_auteur),$info_bulle) ;
			if ($notice->responsability_fonction && $this->displayed_responsability_fonction()) $auteur_isbd .= ", ".$fonction_auteur[$notice->responsability_fonction] ;
			$this->responsabilites['auteurs'][] = array(
					'id' => $notice->author_id,
					'fonction' => $notice->responsability_fonction,
					'responsability' => $notice->responsability_type,
					'name' => $notice->author_name,
					'rejete' => $notice->author_rejete,
					'date' => $notice->author_date,
					'type' => $notice->author_type,
			        'fonction_aff' => ($notice->responsability_fonction ? $fonction_auteur[$notice->responsability_fonction] : ''),
			        'qualification' => $qualification,
					'auteur_isbd' => $auteur_isbd,
			        'auteur_titre' => $auteur_titre,
			        'isni' => $notice->author_isni
			) ;
		}
		return $this->responsabilites;
	}
	// récupération des auteurs ---------------------------------------------------------------------
	// retourne $this->auteurs_principaux = ce qu'on va afficher en titre du résultat
	// retourne $this->auteurs_tous = ce qu'on va afficher dans l'isbd
	// NOTE: now we have two functions:
	// 		fetch_auteurs()  	the pmb-standard one

	public function fetch_auteurs() {
		global $fonction_auteur;

		$this->get_responsabilites();

		// $this->auteurs_principaux
		// on ne prend que le auteur_titre = "Prénom NOM"
		$this->auteurs_principaux = $this->record_datas->get_auteurs_principaux();

		// $this->auteurs_tous
		$mention_resp = array() ;
		$congres_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			if($this->responsabilites["auteurs"][$as]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_2["auteur_isbd"];
			if($this->responsabilites["auteurs"][$indice]["type"]==72) {
				$congres_resp[] = $mention_resp_lib ;
			} else {
				$mention_resp[] = $mention_resp_lib ;
			}
		}

		$libelle_mention_resp = implode (" ; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
		else $this->auteurs_tous ="" ;

		$libelle_congres_resp = implode (" ; ",$congres_resp) ;
		if ($libelle_congres_resp) $this->congres_tous = $libelle_congres_resp ;
		else $this->congres_tous ="" ;

	} // fin fetch_auteurs

	// requête de récupération des categories ------------------------------------------------------------------
	public function get_query_categories() {
		global $lang;
		global $opac_thesaurus, $opac_thesaurus_defaut;
		global $opac_categories_affichage_ordre;

		$query = "select * from (
			select libelle_thesaurus, if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, noeuds.id_noeud , noeuds.num_parent, langue_defaut,id_thesaurus, if(catdef.langue = '".$lang."',2, if(catdef.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM ((noeuds
			join thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus
			left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = thesaurus.langue_defaut
			left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."'))
			,notices_categories
			where ";
		if(!$opac_thesaurus && $opac_thesaurus_defaut)$query .=" thesaurus.id_thesaurus='".$opac_thesaurus_defaut."' AND ";
		$query .=" notices_categories.num_noeud=noeuds.id_noeud and
			notices_categories.notcateg_notice=".$this->notice_id."	order by id_thesaurus, noeuds.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($opac_categories_affichage_ordre==1) $query .= " order by ordre_vedette, ordre_categorie";
		return $query;
	}

	protected function get_display_categories($categ_repetables) {
		global $opac_categories_affichage_ordre;
		global $opac_thesaurus, $opac_categories_categ_in_line;
		global $pmb_keyword_sep;

		$tmpcateg_aff = '';
		foreach ($categ_repetables as $nom_thesaurus => $val_lib) {
			//c'est un tri par libellé qui est demandé
			if ($opac_categories_affichage_ordre==0){
				$tmp=array();
				foreach ( $val_lib as $key => $value ) {
					$tmp[$key]=strip_tags($value);
				}
				$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$val_lib[$key];//On reprend les bons couples clé / libellé
				}
				$val_lib=$tmp;
			}
			if ($opac_thesaurus) {
				if (!$opac_categories_categ_in_line) {
					$categ_repetables_aff = "[".$nom_thesaurus."] ".implode("<br />[".$nom_thesaurus."] ",$val_lib) ;
				}else {
					$categ_repetables_aff = "<b>".$nom_thesaurus."</b><br />".implode(" $pmb_keyword_sep ",$val_lib) ;
				}
			} elseif (!$opac_categories_categ_in_line) {
				$categ_repetables_aff = implode("<br />",$val_lib) ;
			} else {
				$categ_repetables_aff = implode(" $pmb_keyword_sep ",$val_lib) ;
			}
			if($categ_repetables_aff) $tmpcateg_aff .= "$categ_repetables_aff<br />";
		}
		return $tmpcateg_aff;
	}

	// récupération des categories ------------------------------------------------------------------
	public function fetch_categories() {
		global $opac_thesaurus, $opac_categories_categ_in_line, $opac_categories_affichage_ordre;
		global $lang,$opac_categories_show_only_last;
		global $categories_memo,$libelle_thesaurus_memo;
		global $categories_top;

		$categ_repetables = array() ;
		if(!isset($categories_top) || !is_array($categories_top) || !count($categories_top)) {
			$q = "select id_noeud from noeuds where autorite='TOP' ";
			$r = pmb_mysql_query($q);
			while(($res = pmb_mysql_fetch_object($r))) {
				$categories_top[]=$res->id_noeud;
			}
		}

		$requete = $this->get_query_categories();
		$result_categ=@pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result_categ)) {
			while(($res_categ = pmb_mysql_fetch_object($result_categ))) {
				$libelle_thesaurus=$res_categ->libelle_thesaurus;
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$comment_public=$res_categ->comment_public ;
				$num_parent=$res_categ->num_parent ;
				$langue_defaut=$res_categ->langue_defaut ;
				$categ_head=0;
				if(in_array($num_parent,$categories_top))$categ_head=1;

				if ($opac_categories_show_only_last || $categ_head) {
					if ($opac_thesaurus) $catalog_form="[".$libelle_thesaurus."] ".$libelle_categ;
					// Si il y a présence d'un commentaire affichage du layer
					$result_com = categorie::zoom_categ($categ_id, $comment_public);
					$libelle_aff_complet = inslink($libelle_categ,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
					$libelle_aff_complet .= $result_com['zoom'];

					if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus][] =$libelle_aff_complet;
					else $categ_repetables['MONOTHESAURUS'][] =$libelle_aff_complet ;

				} else {
					if(!isset($categories_memo[$categ_id]) || !$categories_memo[$categ_id]) {
						$anti_recurse[$categ_id]=1;
						$path_table = array();
						$requete = "
						select id_noeud as categ_id,
						num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,
						num_renvoi_voir as categ_see,
						note_application as categ_comment,
						if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
						FROM noeuds, categories where id_noeud ='".$num_parent."'
						AND noeuds.id_noeud = categories.num_noeud
						order by p desc limit 1";

						$result=@pmb_mysql_query($requete);
						if (pmb_mysql_num_rows($result)) {
							$parent = pmb_mysql_fetch_object($result);
							$anti_recurse[$parent->categ_id]=1;
							$path_table[] = array(
										'id' => $parent->categ_id,
										'libelle' => $parent->categ_libelle);

							// on remonte les ascendants
							if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
							while (($parent->categ_parent)&&(!$anti_recurse[$parent->categ_parent])) {
								$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
									FROM noeuds, categories where id_noeud ='".$parent->categ_parent."'
									AND noeuds.id_noeud = categories.num_noeud
									order by p desc limit 1";
								$result=@pmb_mysql_query($requete);
								if (pmb_mysql_num_rows($result)) {
									$parent = pmb_mysql_fetch_object($result);
									$anti_recurse[$parent->categ_id]=1;
									$path_table[] = array(
												'id' => $parent->categ_id,
												'libelle' => $parent->categ_libelle);
									if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
								} else {
									break;
								}
							}
							$anti_recurse=array();
						}
						// ceci remet le tableau dans l'ordre général->particulier
						$path_table = array_reverse($path_table);
						if(!empty($path_table)) {
							$temp_table = array();
							foreach ($path_table as $l) {
								$temp_table[] = $l['libelle'];
							}
							$parent_libelle = join(':', $temp_table);
							$catalog_form = $parent_libelle.':'.$libelle_categ;
						} else {
							$catalog_form = $libelle_categ;
						}
						// pour libellé complet mais sans le nom du thésaurus
						$libelle_aff_complet = $catalog_form ;

						if ($opac_thesaurus) $catalog_form="[".$libelle_thesaurus."] ".$catalog_form;

						//$categ = new category($categ_id);
						// Si il y a présence d'un commentaire affichage du layer
						$result_com = categorie::zoom_categ($categ_id, $comment_public);
						$libelle_aff_complet = inslink($libelle_aff_complet,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
						$libelle_aff_complet .= $result_com['zoom'];
						if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus][] =$libelle_aff_complet;
						else $categ_repetables['MONOTHESAURUS'][] =$libelle_aff_complet ;

						$categories_memo[$categ_id]=$libelle_aff_complet;
						$libelle_thesaurus_memo[$categ_id]=$libelle_thesaurus;

					} else {
						if ($opac_thesaurus) $categ_repetables[$libelle_thesaurus_memo[$categ_id]][] =$categories_memo[$categ_id];
						else $categ_repetables['MONOTHESAURUS'][] =$categories_memo[$categ_id] ;
					}
				}
			}
		}
		$this->categories_toutes = $this->get_display_categories($categ_repetables);
	} // fin fetch_categories()

	//Titres uniformes
	public function fetch_titres_uniformes() {
		global $opac_url_base;
		$this->notice->tu= new tu_notice($this->notice_id);
		$this->notice->tu_print_type_2=$this->notice->tu->get_print_type($opac_url_base."index.php?lvl=titre_uniforme_see&id=" );
	} // fin fetch_titres_uniformes()

	public function fetch_langues($quelle_langues=0) {
		$langues = $this->record_datas->get_langues();
		if (!$quelle_langues) $this->langues = $langues['langues'];
		else $this->languesorg = $langues['languesorg'];

	} // fin fetch_langues($quelle_langues=0)

	public function affichage_etat_collections() {
		$affichage = record_display::get_display_collstate($this->notice_id);
		return $affichage;
	} // fin affichage_etat_collections()

	public function get_display_collstates_bulletin_notice() {
		global $msg;
		global $pmb_etat_collections_localise;

		$display = '';
		$query = "select bulletin_id from bulletins where num_notice=".$this->notice->notice_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$collstate = new collstate(0,0,$row->bulletin_id);
			if($pmb_etat_collections_localise) {
				$collstate->get_display_list("",0,0,0,1);
			} else {
				$collstate->get_display_list("",0,0,0,0);
			}
			if($collstate->nbr) {
				$display .= "<h3><span class='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
				$display .= $collstate->liste;
			}
		}
		return $display;
	} // fin get_display_collstates_bulletin_notice()

	public function get_display_analysis_bulletin_notice() {
		$affichage = record_display::get_display_analysis_list($this->notice_id);
		return $affichage;
	} // fin get_display_analysis_bulletin_notice()

	public function construit_liste_langues($tableau) {
		return record_display::get_lang_list($tableau);
	} // fin construit_liste_langues($tableau)

	public function get_avis() {
		if(!is_object($this->avis)) {
			$this->avis = new avis($this->notice_id);
		}
		return $this->avis;
	}

	// Fonction d'affichage des avis
	public function affichage_avis() {
		return $this->get_avis()->get_display();
	} // fin affichage_avis


	public function avis_detail() {
		return $this->get_avis()->get_display_detail();
	}


	//Fonction d'affichage des suggestions
	public function affichage_suggestion(){
		return record_display::get_display_suggestion($this->notice_id);
	} // fin affichage_suggestion()

	//Affichage de l'icône permettant d'ajouter la notice à une liste de lecture
	public function affichage_liste_lecture() {
		global $msg, $charset;
		return "<script src='./includes/javascript/liste_lecture.js'></script>
			<script>
				msg_notice_title_liste_lecture_added = '".$msg["notice_title_liste_lecture_added"]."';
				msg_notice_title_liste_lecture_failed = '".$msg["notice_title_liste_lecture_failed"]."';
			</script>
			<a id='liste_lecture_display_tooltip_notice_".$this->notice_id."'><img src='".get_url_icon('liste_lecture.png')."'  style='border:0px' title=\"".htmlentities($msg['notice_title_liste_lecture'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['notice_title_liste_lecture'],ENT_QUOTES,$charset)."\" /></a>
			<div data-dojo-type='dijit/Tooltip' data-dojo-props=\"connectId:'liste_lecture_display_tooltip_notice_".$this->notice_id."', position:['below']\">
				<div class='row'>
					".$msg['notice_title_liste_lecture']."
				</div>
				<div class='row'>
					".liste_lecture::gen_selector_my_list($this->notice_id)."
				</div>
			</div>";
	}

	public function get_img_plus_css_class() {
		return 'img_plus';
	}

	// génération du de l'affichage double avec onglets ---------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	public function genere_double($depliable=1, $premier='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_avis;// l'utilisateur a-t-il le droit d'ajouter un avis
		global $allow_sugg;// l'utilisateur a-t-il le droit de faire une suggestion
		global $allow_liste_lecture;// l'utilisateur a-t-il le droit de faire une liste de lecture
		global $lvl;	   // pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		global $opac_visionneuse_allow, $opac_photo_filtre_mimetype;

		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}

		$this->result ="";
		if(!$this->notice_id) return;
		$this->premier = $premier ;
		$this->double_ou_simple = 2 ;

		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\"><img src=\"".get_url_icon('basket_exist.png', 1)."\"  style='border:0px' alt=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"><img src='".get_url_icon("basket_small_20x20.png", 1)."'  style='border:0px' alt=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\" /></a>";
			}
		}else $basket="";

		//add tags
		if ( ($this->tag_allowed==1) || ( ($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag) ) ) {
			$img_tag="<a href='#' onclick=\"openPopUp('addtags.php?noticeid=$this->notice_id','ajouter_un_tag'); return false;\"><img src='".get_url_icon('tag.png', 1)."'  style='border:0px' title=\"".htmlentities($msg['notice_title_tag'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['notice_title_tag'],ENT_QUOTES,$charset)."\" /></a>";
        } else {
			    $img_tag="";
			}

		//Avis
		if (($opac_avis_display_mode==0) && (($this->avis_allowed && $this->avis_allowed !=2) || ($_SESSION["user_code"] && $this->avis_allowed ==2 && $allow_avis))) {
			$img_avis= $this->affichage_avis();
		} else {
		    $img_avis="";
		}

		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg)) {
			$img_sugg= $this->affichage_suggestion();
		} else {
		    $img_sugg="";
		}

		//Listes de lecture
		if ($this->liste_lecture_allowed == 1 && $_SESSION["user_code"] && $allow_liste_lecture) {
			$img_liste_lecture = $this->affichage_liste_lecture();
		} else {
		    $img_liste_lecture = "";
		}

		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) {
		    $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]' title='".htmlentities($msg['rgaa_checkbox_check'],ENT_QUOTES,$charset)."' />&nbsp;";
		} else {
		    $case_a_cocher = "" ;
		}

		$source_enrichment = '';
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if(!isset($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc])) {
				$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc] = '';
			}
			if(!isset($enrichment->active[$this->notice->niveau_biblio])) {
				$enrichment->active[$this->notice->niveau_biblio] = '';
			}
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);
			}
		}
		if($opac_allow_simili_search){
			$simili_search_script_all="
				<script>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";
		} else {
			$simili_search_script_all="";
		}

		$script_simili_search = $this->get_simili_script();

		if ($depliable == 1) {
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			$case_a_cocher
			<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="
			<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}elseif ($depliable == 2) {
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\">
			<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}else{
			$template="
			<script>
				if(param_social_network){
					creeAddthis('el".$this->notice_id."');
				}else{
					waitingAddthisLoaded('el".$this->notice_id."');
				}
			</script>
			<div id='el!!id!!Parent' class='parent'>$case_a_cocher";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";

		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}

		$template_in = '';
		if($opac_show_social_network){
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}
			$template_in.="
			<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style '
			addthis:url='".$opac_url_base."fb.php?id=" . intval($this->notice_id) . "&type=" . intval(TYPE_NOTICE) . "'>
			</div>";
		}
		$li_tags="";
		if($img_tag) $li_tags.="<li id='tags!!id!!' class='onglet_tags'>$img_tag</li>";
		if($img_avis) $li_tags.="<li id='avis!!id!!' class='onglet_avis'>$img_avis</li>";
		if($img_sugg) $li_tags.="<li id='sugg!!id!!' class='onglet_sugg'>$img_sugg</li>";
		if($img_liste_lecture) $li_tags.="<li id='liste_lecture!!id!!' class='onglet_liste_lecture'>$img_liste_lecture</li>";
		$template_in.="
		<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>";
		if ($premier=='ISBD'){
			if ($basket) $template_in.="<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			$template_in.="
	    		<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
	    		<li id='onglet_public!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
		    		<!-- onglets_perso_list -->
		    		$li_tags
		    		</ul>
		    		<div class='row'></div>
		    		<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
		    		<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
			$template_in.="<!-- onglets_perso_content -->";
		} elseif($premier=="autre") {
			if ($basket) $template_in.="
			<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			if (!isset($this->onglet_perso)) {
				$this->onglet_perso=new notice_onglets();
			}
			$template_in.=$this->onglet_perso->build_onglets($this->notice_id,$li_tags);

		}else{
			if ($basket) $template_in.="
			<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
			$template_in.="
	  			<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>
				<li id='onglet_isbd!!id!!' class='isbd_public_inactive'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>
					<!-- onglets_perso_list -->
					$li_tags
					</ul>
					<div class='row'></div>
					<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
					<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
			$template_in.="<!-- onglets_perso_content -->";
		}

		//Onglets
		if (!isset($this->onglet_perso)) {
			$this->onglet_perso=new notice_onglets();
		}
		$template_in=$this->onglet_perso->insert_onglets($this->notice_id,$template_in);

		if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2) || ($_SESSION["user_code"] && $this->avis_allowed ==2 && $allow_avis))) $this->affichage_avis_detail=$this->avis_detail();

		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$voir_bulletins = "";
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			$voir_docnum_bulletins="";
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
			    $nb_bulletins_docnums = 0;
			    $allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			    $bulletins_docnums = $this->get_bulletins_docnums();
			    foreach($bulletins_docnums as $bulletin_docnum) {
			        if ($allowed_mimetype && in_array($bulletin_docnum->explnum_mimetype,$allowed_mimetype)){
			            $nb_bulletins_docnums++;
			        }
			    }
			    if ($nb_bulletins_docnums) {
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script>
						function sendToVisionneusePerio".$this->notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."&bull_only=1';
						}
					</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			} else {
				$search_in_serial ="";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}
		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);

		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}else {
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		}
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);

		$this->affichage_simili_search_head=$this->get_simili_search($depliable);

		if($this->display_childs) {
			$this->notice_childs = $this->genere_notice_childs();
		} else {
			$this->notice_childs = "";
		}
		$this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_demand.$this->affichage_scan_requests.$this->affichage_simili_search_head, $this->result);
	} // fin genere_double($depliable=1, $premier='ISBD')

	protected function get_display_tab_basket() {
		global $msg, $charset;

		if ($this->cart_allowed){
			if(isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
				$basket="<a href='#' class=\"img_basket_exist\" title=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\"><img src=\"".get_url_icon('basket_exist.png', 1)."\"  alt=\"".htmlentities($msg['notice_title_basket_exist'],ENT_QUOTES,$charset)."\" /></a>";
			} else {
				$title=$this->notice_header;
				if(!$title)$title=$this->notice->tit1;
				$basket="<a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\"><img src='".get_url_icon("basket_small_20x20.png", 1)."'  alt=\"".htmlentities($msg['notice_title_basket'],ENT_QUOTES,$charset)."\" /></a>";
			}
			return "<li id='baskets!!id!!' class='onglet_basket'>$basket</li>";
		}
		return "";
	}

	protected function get_display_tab($label, $css) {
		if($label) {
			return "<li id='".$css."!!id!!' class='onglet_".$css."'>".$label."</li>";
		}
		return "";
	}

	protected function get_display_tabs($mode="simple", $what='ISBD') {
		global $msg;
		global $allow_tag; // l'utilisateur a-t-il le droit d'ajouter un tag
		global $allow_avis;// l'utilisateur a-t-il le droit d'ajouter un avis
		global $allow_sugg;// l'utilisateur a-t-il le droit de faire une suggestion
		global $allow_liste_lecture;// l'utilisateur a-t-il le droit de faire une liste de lecture
		global $opac_avis_display_mode;
		global $opac_notice_enrichment, $charset;

		$display = '';

		//add tags
		if (($this->tag_allowed==1)||(($this->tag_allowed==2)&&($_SESSION["user_code"])&&($allow_tag)))
			$img_tag="<a href='#' onclick=\"openPopUp('addtags.php?noticeid=$this->notice_id','ajouter_un_tag'); return false;\"><img src='".get_url_icon('tag.png', 1)."'  title=\"".htmlentities($msg['notice_title_tag'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['notice_title_tag'],ENT_QUOTES,$charset)."\" /></a>";
		else $img_tag="";

		//Avis
		if (($opac_avis_display_mode==0)&&(($this->avis_allowed && $this->avis_allowed !=2)|| ($_SESSION["user_code"] && $this->avis_allowed ==2 && $allow_avis)))
			$img_avis= $this->affichage_avis();
		else $img_avis="";

		//Suggestions
		if (($this->sugg_allowed ==2)|| ($_SESSION["user_code"] && ($this->sugg_allowed ==1) && $allow_sugg))
			$img_sugg= $this->affichage_suggestion();
		else $img_sugg="";

		//Listes de lecture
		if ($this->liste_lecture_allowed == 1 && $_SESSION["user_code"] && $allow_liste_lecture) {
			$img_liste_lecture = $this->affichage_liste_lecture();
		} else $img_liste_lecture="";

		$display .= $this->get_display_tab_basket();
		if($opac_notice_enrichment){
			if($what =='ISBD') $display.="<li id='onglet_isbd!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['ISBD_info']."\" onclick=\"show_what('ISBD', '!!id!!'); return false;\">".$msg['ISBD']."</a></li>";
			else $display.="<li id='onglet_public!!id!!' class='isbd_public_active'><a href='#' title=\"".$msg['Public_info']."\" onclick=\"show_what('PUBLIC', '!!id!!'); return false;\">".$msg['Public']."</a></li>";
		}
		$display .= $this->get_display_tab($img_tag, 'tags');
		$display .= $this->get_display_tab($img_avis, 'avis');
		$display .= $this->get_display_tab($img_sugg, 'sugg');
		$display .= $this->get_display_tab($img_liste_lecture, 'liste_lecture');

		if($display) {
			$display = "
			<ul id='onglets_isbd_public!!id!!' class='onglets_isbd_public'>
				".$display."
				<!-- onglets_perso_list -->
			</ul>
			<div class='row'></div>";
		}
		return $display;
	}

	// génération du de l'affichage simple sans onglet ----------------------------------------------
	//	si $depliable=1 alors inclusion du parent / child
	public function genere_simple($depliable=1, $what='ISBD') {
		global $msg,$charset;
		global $cart_aff_case_traitement;
		global $opac_url_base ;
		global $opac_notice_enrichment;
		global $opac_show_social_network;
		global $allow_avis;// l'utilisateur a-t-il le droit d'ajouter un avis
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_avis_display_mode;
		global $flag_no_get_bulletin;
		global $opac_allow_simili_search;
		global $opac_draggable;
		global $opac_visionneuse_allow, $opac_photo_filtre_mimetype;

		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}

		$this->result ="";
		if(!$this->notice_id) return;

		$this->double_ou_simple = 1 ;

		// préparation de la case à cocher pour traitement panier
		if ($cart_aff_case_traitement) {
		    $case_a_cocher = "<input type='checkbox' value='!!id!!' name='notice[]' title='".htmlentities($msg['rgaa_checkbox_check'],ENT_QUOTES,$charset)."' />&nbsp;";
		} else {
		    $case_a_cocher = "" ;
		}

		$source_enrichment = '';
		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if(!isset($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc])) {
				$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc] = '';
			}
			if(!isset($enrichment->active[$this->notice->niveau_biblio])) {
				$enrichment->active[$this->notice->niveau_biblio] = '';
			}
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$source_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);
			}
		}
		if($opac_allow_simili_search){
			$simili_search_script_all="
				<script>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";
		} else {
			$simili_search_script_all="";
		}

		$script_simili_search = $this->get_simili_script();

		if ($depliable == 1) {
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			$case_a_cocher
			<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' onClick=\"expandBase('el!!id!!', true); $script_simili_search return false;\" />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="
			<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
    			<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}elseif($depliable == 2){
			$template="$simili_search_script_all
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
			$case_a_cocher<span class=\"notices_depliables\" onClick=\"expandBase('el!!id!!', true);  $script_simili_search return false;\">
			<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
	    		<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".($source_enrichment ? "enrichment='".$source_enrichment."'" : "")." ".($opac_allow_simili_search ? "simili_search='1'" : "")." token='".$this->hash."' datetime='".$this->datetime."'>";
		}else{
			$template="
			<script>
				if(param_social_network){
					creeAddthis('el".$this->notice_id."');
				}else{
					waitingAddthisLoaded('el".$this->notice_id."');
				}
			</script>
			<div id='el!!id!!Parent' class='parent'>$case_a_cocher";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
			$template.="<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink;
		}
		$template.="!!CONTENU!!
					!!SUITE!!</div>";

		if($this->notice->niveau_biblio != "b"){
			$this->permalink = "index.php?lvl=notice_display&id=".$this->notice_id;
		}else {
			$this->permalink = "index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}

		$template_in = '';
		if($opac_show_social_network){
			if($this->notice_header_without_html == ""){
				$this->do_header_without_html();
			}
			$template_in.="
		<div id='el!!id!!addthis' class='addthis_toolbox addthis_default_style '
			addthis:url='".$opac_url_base."fb.php?id=" . intval($this->notice_id) . "&type=" . intval(TYPE_NOTICE) . "'>
		</div>";
		}
		$template_in.=$this->get_display_tabs("simple", $what);

		if($what =='ISBD') {
			$template_in.="
				<div id='div_isbd!!id!!' style='display:block;'>!!ISBD!!</div>
	  			<div id='div_public!!id!!' style='display:none;'>!!PUBLIC!!</div>";
		} else {
			$template_in.="
		    	<div id='div_public!!id!!' style='display:block;'>!!PUBLIC!!</div>
				<div id='div_isbd!!id!!' style='display:none;'>!!ISBD!!</div>";
		}
		$template_in.="
			<!-- onglets_perso_content -->";

		//Onglets
		if (!isset($this->onglet_perso)) {
			$this->onglet_perso=new notice_onglets();
		}
		$template_in=$this->onglet_perso->insert_onglets($this->notice_id,$template_in);

		if (($opac_avis_display_mode==1) && (($this->avis_allowed && $this->avis_allowed !=2) || ($_SESSION["user_code"] && $this->avis_allowed ==2 && $allow_avis))) $this->affichage_avis_detail=$this->avis_detail();


		// Serials : différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$voir_bulletins = "";
			if(!$flag_no_get_bulletin){
				if($this->get_bulletins()){
					if ($lvl == "notice_display")$voir_bulletins="&nbsp;&nbsp;<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
					else $voir_bulletins="&nbsp;&nbsp;<a href='index.php?lvl=notice_display&id=".$this->notice_id."'><i>".$msg["see_bull"]."</i></a>";
				}
			}
			//si visionneuse active...
			$voir_docnum_bulletins="";
			if ($opac_visionneuse_allow && $this->notice->opac_visible_bulletinage)	{
			    $nb_bulletins_docnums = 0;
			    $allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			    $bulletins_docnums = $this->get_bulletins_docnums();
			    foreach($bulletins_docnums as $bulletin_docnum) {
			        if ($allowed_mimetype && in_array($bulletin_docnum->explnum_mimetype,$allowed_mimetype)){
			            $nb_bulletins_docnums++;
			        }
			    }
			    if ($nb_bulletins_docnums) {
					$voir_docnum_bulletins="
			<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$this->notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
			<script>
				function sendToVisionneusePerio".$this->notice_id."(){
					document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$this->notice_id."&bull_only=1';
				}
			</script>";
				}
			}
			if($this->open_to_search()) {
				$search_in_serial ="&nbsp;<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=$this->notice_id'><i>".$msg["rechercher_in_serial"]."</i></a>";
			} else {
				$search_in_serial ="";
			}
			$template_in = str_replace('!!ISBD!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>".$voir_bulletins.$voir_docnum_bulletins.$search_in_serial."&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='a') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		} elseif ($this->notice->niveau_biblio =='b') {
			$template_in = str_replace('!!ISBD!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!ISBD!!", $template_in);
			$template_in = str_replace('!!PUBLIC!!', "<span class='fond-article'>[".$msg['isbd_type_bul']."]</span>&nbsp;!!PUBLIC!!", $template_in);
		}

		$template_in.=$this->get_serialcirc_form_actions();
		$template_in = str_replace('!!ISBD!!', $this->notice_isbd, $template_in);
		$template_in = str_replace('!!PUBLIC!!', $this->notice_public, $template_in);
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$depliable);


		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}else {
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		}
		$this->result = str_replace('!!CONTENU!!', $template_in, $this->result);

		$this->affichage_simili_search_head=$this->get_simili_search($depliable);

		if($this->display_childs) {
			$this->notice_childs = $this->genere_notice_childs();
		} else {
			$this->notice_childs = "";
		}
		$this->result = str_replace('!!SUITE!!', $this->notice_childs.$this->affichage_resa_expl.$this->affichage_avis_detail.$this->affichage_demand.$this->affichage_scan_requests.$this->affichage_simili_search_head, $this->result);

	} // fin genere_simple($depliable=1, $what='ISBD')

	public function genere_ajax_param($aj_type_aff,$header_only_origine=0){
		global $opac_notice_enrichment;

		$param=array();
		$param['id']=$this->notice_id;
		$param['function_to_call']="aff_notice";
		$param['aj_liens']=$this->liens;
		$param['aj_cart']=$this->cart_allowed;
		$param['aj_to_print']=$this->to_print;
		$param['aj_header_only']=$header_only_origine;
		$param['aj_no_header']=$this->no_header;
		$param['aj_nodocnum']=($this->docnum_allowed ? 0:1);
		$param['aj_type_aff']=$aj_type_aff;
		$param['token']=$this->hash;
		$param['datetime']=$this->datetime;
		$this->notice_affichage_cmd=encoding_normalize::json_encode($param);

		if($opac_notice_enrichment){
			$enrichment = new enrichment();
			if(!isset($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc])) {
				$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc] = '';
			}
			if(!isset($enrichment->active[$this->notice->niveau_biblio])) {
				$enrichment->active[$this->notice->niveau_biblio] = '';
			}
			if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
				$this->notice_affichage_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]);
			}else if ($enrichment->active[$this->notice->niveau_biblio]){
				$this->notice_affichage_enrichment = implode(",",$enrichment->active[$this->notice->niveau_biblio]);
			}
		}

		if($this->notice_affichage_enrichment){
			$this->notice_affichage_enrichment="enrichment='".$this->notice_affichage_enrichment."'";
		}
	}

	public function genere_ajax($aj_type_aff,$header_only_origine=0){
		global $msg,$charset;
		global $opac_url_base;
		global $tdoc;
		global $lvl;		// pour savoir qui demande l'affichage
		global $opac_notices_depliable;
		global $opac_allow_simili_search;
		global $opac_draggable;

		if($opac_draggable){
			$draggable='yes';
		}else{
			$draggable='no';
		}

		$this->genere_ajax_param($aj_type_aff,$header_only_origine);

		if($this->notice->niveau_biblio != "b"){
			$this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
		}else{
			$this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
		}

		if($opac_allow_simili_search){
			$simili_search_script_all="
				<script>
					tab_notices_simili_search_all[tab_notices_simili_search_all.length]=".$this->notice_id.";
				</script>
			";
		} else {
			$simili_search_script_all="";
		}
		$script_simili_search = $this->get_simili_script();

		$case_a_cocher = "" ;
		$template_in = "";
		if($opac_notices_depliable == 2){
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher<span class=\"notices_depliables\" param='".rawurlencode($this->notice_affichage_cmd)."'  onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param'));  $script_simili_search return false;\">
		    	<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus&optionnel=1\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
	    	$template.="
				<span class=\"notice-heada\" draggable=\"no\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span></span>".$this->notice_header_doclink."
		    	<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";
		}else{
			$template="$simili_search_script_all
				<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				$case_a_cocher
		    	<img class='".$this->get_img_plus_css_class()."' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" alt=\"".htmlentities($msg['expandable_notice'],ENT_QUOTES,$charset)."\" style='border:0px' param='".rawurlencode($this->notice_affichage_cmd)."' onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); $script_simili_search return false;\" />";
			if (!$this->no_header) {
				$template.=$this->get_icon_html($this->notice->niveau_biblio, $this->notice->typdoc);
			}
	    	$template.="
				<span class=\"notice-heada\" draggable=\"$draggable\" dragtype=\"notice\" id=\"drag_noti_!!id!!\">!!heada!!</span>".$this->notice_header_doclink."
		    	<br />
				</div>
				<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->notice_affichage_enrichment." ".($opac_allow_simili_search ? "simili_search='1'" : "").">
		    	</div>";
		}


		$template.="<a href=\"".$this->permalink."\" style=\"display:none;\">Permalink</a>
			$simili_search_script_all
		";
		$template_in = str_replace('!!id!!', $this->notice_id, $template_in);
		$this->do_image($template_in,$opac_notices_depliable);

		$this->result = str_replace('!!id!!', $this->notice_id, $template);
		if($this->notice_header_doclink){
			$this->result = str_replace('!!heada!!', $this->notice_header_without_doclink, $this->result);
		}elseif($this->notice_header)
			$this->result = str_replace('!!heada!!', $this->notice_header, $this->result);
		else $this->result = str_replace('!!heada!!', '', $this->result);

	} // fin genere_ajax()

	// génération de l'isbd----------------------------------------------------
	public function do_isbd($short=0,$ex=1) {
		global $msg;
		global $tdoc;
		global $charset;
		global $opac_notice_affichage_class;
		global $memo_notice;
		global $opac_map_activate;
		global $opac_demandes_allow_from_record;
		global $opac_scan_request_activate;
		global $memo_expl;

		$this->notice_isbd="";
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_isbd.=$this->parents;

		// constitution de la mention de titre
		$serie_temp = '';
		if($this->notice->serie_name) {
			$serie_temp .= inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));
			if($this->notice->tnvol) $serie_temp .= ',&nbsp;'.$this->notice->tnvol;
		}
		if ($serie_temp) $this->notice_isbd .= $serie_temp.".&nbsp;".$this->notice->tit1 ;
		else $this->notice_isbd .= $this->notice->tit1;

		if ($this->notice->tit3) $this->notice_isbd .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->tit4) $this->notice_isbd .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit2) $this->notice_isbd .= "&nbsp;; ".$this->notice->tit2 ;

		$this->notice_isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';

		if ($this->auteurs_tous) $this->notice_isbd .= " / ".$this->auteurs_tous;
		if ($this->congres_tous) $this->notice_isbd .= " / ".$this->congres_tous;

		// mention d'édition
		if($this->notice->mention_edition) $this->notice_isbd .= " &nbsp;. -&nbsp; ".$this->notice->mention_edition;

		// zone de collection et éditeur
		$editeurs = '';
		$collections = '';
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->publisher, $this->lien_rech_editeur));
			$collections = inslink($collection->get_isbd(),  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection));
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$editeurs .= inslink($collection->publisher_isbd, str_replace("!!id!!", $collection->parent, $this->lien_rech_editeur));
			$collections = inslink($collection->get_isbd(),  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection));
		} elseif ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->publishers[]=$editeur;
			$editeurs .= inslink($editeur->get_isbd(),  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur));
		}

		if($this->notice->ed2_id) {
			$editeur = new publisher($this->notice->ed2_id);
			$this->publishers[]=$editeur;
			if ($editeurs) {
			    $editeurs .= '&nbsp;: '.inslink($editeur->get_isbd(),  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur));
			} else {
			    $editeurs = inslink($editeur->get_isbd(),  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur));
			}
		}

		if($this->notice->year) {
		    if ($editeurs) {
		        $editeurs .= ', '.$this->notice->year;
		    } else {
		        $editeurs = $this->notice->year;
		    }
		} elseif ($this->notice->niveau_biblio == 'm' && $this->notice->niveau_hierar == 0) {
		    if ($editeurs) {
		        $editeurs .= ', [s.d.]';
		    } else {
		        $editeurs = "[s.d.]";
		    }
		}
		if($editeurs) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$editeurs";
		// zone de la collation
		$collation = '';
		if($this->notice->npages) $collation = $this->notice->npages;
		if($this->notice->ill) $collation .= '&nbsp;: '.$this->notice->ill;
		if($this->notice->size) $collation .= '&nbsp;; '.$this->notice->size;
		if($this->notice->accomp) $collation .= '&nbsp;+ '.$this->notice->accomp;
		if($collation) $this->notice_isbd .= "&nbsp;.&nbsp;-&nbsp;$collation";
		if($collections) {
			if($this->notice->nocoll) $collections .= '; '.$this->notice->nocoll;
			$this->notice_isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}

		if(substr(trim($this->notice_isbd), -1) != "."){
			$this->notice_isbd .= '.';
		}

		if($opac_map_activate==1 || $opac_map_activate==2){
			if($mapisbd=$this->map_info->get_isbd())	$this->notice_isbd .=$mapisbd;
		}

		// ISBN ou NO. commercial
		$zoneISBN = '';
		if($this->notice->code) {
			if(isISBN($this->notice->code)) $zoneISBN = '<b>ISBN</b>&nbsp;: ';
			else $zoneISBN .= '<b>'.$msg["issn"].'</b>&nbsp;: ';
			$zoneISBN .= $this->notice->code;
		}
		if($this->notice->prix) {
			if($this->notice->code) $zoneISBN .= '&nbsp;: '.$this->notice->prix;
			else {
				if ($zoneISBN) $zoneISBN .= '&nbsp; '.$this->notice->prix;
				else $zoneISBN = $this->notice->prix;
			}
		}
		if($zoneISBN) $this->notice_isbd .= "<br />".$zoneISBN;

		// oeuvre / titre uniforme
		if($this->notice->tu_print_type_2) {
			$oeuvre = '<b>'.$msg['isbd_oeuvre'].'</b>&nbsp;: '.$this->notice->tu_print_type_2;
			$this->notice_isbd.= '<br />'.$oeuvre;
		}

		$zoneNote = '';
		// note générale
		if($this->notice->n_gen) {
			$zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		}
		if($zoneNote) {
			$this->notice_isbd .= "<br />".$zoneNote;
		}

		// langues
		$langues = "";
		if(count($this->langues)) {
			$langues .= "<span class='etiq_champ'>{$msg["537"]}</span>&nbsp;: ".$this->construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <span class='etiq_champ'>{$msg["711"]}</span>&nbsp;: ".$this->construit_liste_langues($this->languesorg);
		}
		if ($langues) $this->notice_isbd .= "<br />".$langues ;

		$this->notice_isbd.=$this->genere_in_perio();
		if (!$short) {
			$this->notice_isbd .="<table>";
			$this->notice_isbd .= $this->aff_suite() ;
			$this->notice_isbd .="</table>";
		}

		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_isbd.=$this->affichage_etat_collections();

		//notice de bulletin : etat des collections
		if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) $this->notice_isbd.=$this->get_display_collstates_bulletin_notice();

		//notice de bulletin : dépouillements
		//if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) $this->notice_isbd.=$this->get_display_analysis_bulletin_notice();

		//Notices liées
		// ajoutées en dehors de l'onglet PUBLIC ailleurs

		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;

		// demandes
		if ($opac_demandes_allow_from_record) $this->aff_demand();

		// demandes de numérisation
		if ($opac_scan_request_activate) $this->aff_scan_requests();
	} // fin do_isbd($short=0,$ex=1)

	public function do_public_line($label, $value, $css='') {
		if($value) {
			if(substr(trim($label), strlen(trim($label))-1) != ':') $label .= ' :';
			$this->notice_public .=
				"<tr class='tr_".$css."'>
					<td class='align_right bg-grey'><span class='etiq_champ'>".$label."</span></td>
					<td class='public_line_value'><span class='public_".$css."'>".$value."</span></td>
				</tr>";
		}
	}

	public function do_public_line_perso($name='') {
		for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p['NAME'] == $name) {
				if ($p['OPAC_SHOW'] && $p["AFF"] !== '') {
					$this->do_public_line(strip_tags($p["TITRE"]), $p["AFF"], $p['NAME']);
				}
			}
		}
	}

	public function get_line_aff_suite($label, $value, $css='') {
		$line_aff_suite = '';
		if($value) {
			if(substr(trim($label), strlen(trim($label))-1) != ':') $label .= ' :';
			$line_aff_suite	.= "<tr class='tr_".$css."'>
					<td class='align_right bg-grey'><span class='etiq_champ'>".$label."</span></td>
					<td class='public_line_value'><span class='public_".$css."'>".$value."</span></td>
				</tr>";
		}
		return $line_aff_suite;
	}

	public function get_line_aff_suite_perso($name='') {
		$line_aff_suite_perso = '';
		for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p['NAME'] == $name) {
				if ($p['OPAC_SHOW'] && $p["AFF"] !== '') {
					$line_aff_suite_perso .= $this->get_line_aff_suite(strip_tags($p["TITRE"]), $p["AFF"], $p['NAME']);
				}
			}
		}
		return $line_aff_suite_perso;
	}

	// génération de l'affichage public----------------------------------------
	public function do_public($short=0,$ex=1) {
		global $msg;
		global $tdoc;
		global $charset;
		global $memo_notice;
		global $opac_notice_affichage_class;
		global $opac_map_activate;
		global $opac_demandes_allow_from_record;
		global $opac_scan_request_activate;
		global $memo_expl;

		$this->notice_public= "";
		if(!$this->notice_id) return;

		// Notices parentes
		$this->notice_public.=$this->parents;
		$this->notice_public .= "<table role='presentation'>";
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->do_public_line($msg['tparent_start'], inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie)).($this->notice->tnvol ? ",&nbsp;".$this->notice->tnvol : ''), 'serie');
		}

		//titre 1 - titre 4
		$this->do_public_line($msg['title'], $this->notice->tit1.($this->notice->tit4 ? "&nbsp;: ".$this->notice->tit4 : ''), 'title');

		//titre 2
		$this->do_public_line($msg['other_title_t2'], $this->notice->tit2, 'tit2');
		//titre 3
		$this->do_public_line($msg['other_title_t3'], $this->notice->tit3, 'tit3');
		//type de document
		$this->do_public_line($msg['typdocdisplay_start'], $tdoc->table[$this->notice->typdoc], 'typdoc');
		//auteurs
		$this->do_public_line($msg['auteur_start'], $this->auteurs_tous, 'auteurs');
		//congrès
		$this->do_public_line($msg['congres_aff_public_libelle'], $this->congres_tous, 'congres');
		// mention d'édition
		$this->do_public_line($msg['mention_edition_start'], $this->notice->mention_edition, 'mention');

		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->publishers[]=$editeur;
			$this->do_public_line($msg['editeur_start'], inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur)), 'ed1');
			//année d'édition
			$this->do_public_line($msg['year_start'], $this->notice->year, 'year');
		}
		// Autre editeur
		if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->publishers[]=$editeur;
			$this->do_public_line($msg['other_editor'], inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur)), 'ed2');
		}

		// collection
		if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->do_public_line($msg['coll_start'], inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link, 'coll');
			$this->do_public_line($msg['subcoll_start'], inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)).$affnocoll, 'subcoll');
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->do_public_line($msg['coll_start'], inslink($collection->get_isbd(),  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)).$affnocoll." ".$collection->collection_web_link, 'coll');
		}

		// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		//année d'édition
		if (!$this->notice->ed1_id) {
			$this->do_public_line($msg['year_start'], $this->notice->year, 'year');
		}

		// Titres uniformes
		if($this->notice->tu_print_type_2) {
			$this->do_public_line($msg['titre_uniforme_aff_public'], $this->notice->tu_print_type_2, 'tu');
		}

		if($this->authperso_info)$this->notice_public .= $this->get_authperso_display();

		// zone de la collation
		if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->do_public_line($msg['npages_start'], $this->notice->npages, 'npages');
			} else {
				$this->do_public_line($msg['npages_start_perio'], $this->notice->npages, 'npages');
			}
		}
		$this->do_public_line($msg['ill_start'], $this->notice->ill, 'ill');
		$this->do_public_line($msg['size_start'], $this->notice->size, 'size');
		$this->do_public_line($msg['accomp_start'], $this->notice->accomp, 'accomp');

		if($opac_map_activate==1 || $opac_map_activate==2){
			if($mapisbd=$this->map_info->get_public())	$this->notice_public .=$mapisbd;
		}
		// map
		if(($opac_map_activate==1 || $opac_map_activate==2) && $this->show_map){
			$map = $this->map->get_map();
			if($map){
				$this->do_public_line($msg['map_notice_map'], $this->map->get_map(), 'map');
			}
		}
		// ISBN ou NO. commercial
		$this->do_public_line($msg['code_start'], $this->notice->code, 'code');

		$this->do_public_line($msg['price_start'], $this->notice->prix, 'prix');

		// note générale
		$this->do_public_line($msg['n_gen_start'], nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset)), 'ngen');

		// langues
		if (count($this->langues)) {
			$langues_value = $this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $langues_value .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->do_public_line($msg['537'], $langues_value, 'langues');
		} elseif (count($this->languesorg)) {
			$this->do_public_line($msg['711'], $this->construit_liste_langues($this->languesorg), 'langues');
		}

		if (!$short){
			$this->notice_public .= $this->aff_suite() ;
		}
		$this->notice_public.=$this->genere_in_perio();

		$this->notice_public.="</table>\n";

		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();

		//notice de bulletin : etat des collections
		if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) $this->notice_public.=$this->get_display_collstates_bulletin_notice();

		//notice de bulletin : dépouillements
		//if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) $this->notice_public.=$this->get_display_analysis_bulletin_notice();

		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;

		//carte des localisations
		if(($opac_map_activate==1 || $opac_map_activate==3) && $ex && $this->affichage_resa_expl){
			$this->affichage_resa_expl = '<div id="expl_area_' . $this->notice_id . '">' . $this->affichage_resa_expl . map_locations_controler::get_map_location($memo_expl, $this->notice_id.'_0') . '</div>';
		}

		// demandes
		if ($opac_demandes_allow_from_record) $this->aff_demand();

		// demandes de numérisation
		if ($opac_scan_request_activate) $this->aff_scan_requests();

		return;
	} // fin do_public($short=0,$ex=1)

	public function get_authperso_display(){
		$aff="";
		$authperso_name="";
		foreach($this->authperso_info as $auth){
			if($authperso_name!=$auth['authperso_name']){
			    if($aff) {
			        $aff."</td></tr>";
			    }
				$authperso_name=$auth['authperso_name'];
				$aff.="<tr><td class='bg-grey align_right'><span class='etiq_champ'>".$authperso_name." : </span><td>";
				$new=1;
			}
			if(!$new) {
			    $aff.=", ";
			}
			$aff.=$auth['auth_see'];
			$new=0;
		}
		if($aff) {
		    $aff.="</td></tr>";
		}
		return $aff;
	}

	protected function get_notice_header($id_tpl=0) {
		global $opac_notice_reduit_format;
		global $msg, $charset;
		global $memo_notice;

		$notice_header="";

		if(!isset($this->notice_reduit_format)) {
			$this->notice_reduit_format = $opac_notice_reduit_format;
		}
		$type_reduit = substr($this->notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($this->notice_reduit_format,2);
			if($id_tpl){
				$tpl = notice_tpl_gen::get_instance($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$notice_header=$notice_tpl_header;
					//coins pour Zotero
					$coins_span=$this->gen_coins_span();
					$notice_header.=$coins_span;
					$memo_notice[$this->notice_id]["header_without_doclink"]=$notice_header;
					$memo_notice[$this->notice_id]["header_doclink"]="";
					$memo_notice[$this->notice_id]["header"]=$notice_header;
					$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
					return $notice_header;
				}
			}
		}
		$perso_voulus = array();
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($this->notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}

		if ($type_reduit=="E") {
			// zone de l'éditeur
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			} elseif ($this->notice->year) {
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;

		//Champs personalisés à ajouter au réduit
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
				$perso_voulu_aff=trim($perso_voulu_aff);
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;

		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			$aff_perio_title="<span class='header_perio'><i>".$msg['in_serial']." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i></span>";
		} else {
			$aff_perio_title="";
		}

		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";

		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $notice_header .= $this->notice->tnvol;

		if ($notice_header) $notice_header .= ". ".$this->notice->tit1 ;
		else $notice_header = $this->notice->tit1;

		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;
		}

		$notice_header .= $aff_bullperio_title;

		$notice_header = "<span !!zoteroNotice!! class='header_title'>".$notice_header."</span>";
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$notice_header);
		}else $notice_header =str_replace("!!zoteroNotice!!","",$notice_header);

		$notice_header = '<span class="statutnot'.($this->notice->statut ?? '').'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$notice_header;

		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= "<span class='header_authors'> / ".$this->auteurs_principaux."</span>";
		if ($editeur_reduit) $notice_header_suite .= " / ".$editeur_reduit ;
		if ($perso_voulu_aff) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if ($aff_perio_title) $notice_header_suite .= " ".$aff_perio_title;
		$notice_header .= $notice_header_suite;

		if ($this->notice->niveau_biblio =='m' || $this->notice->niveau_biblio =='s') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		return $notice_header;
	}

	protected function get_resource_link_notice_header() {
		global $msg;

		if(!$this->notice->eformat) $info_bulle=$msg["open_link_url_notice"];
		else $info_bulle=$this->notice->eformat;
		// ajout du lien pour les ressources électroniques
		$resource_link = "
			&nbsp;<span class='notice_link'>
			<a href=\"".$this->notice->lien."\" target=\"_blank\" type='external_url_notice'>
				<img src=\"".get_url_icon("globe.gif", 1)."\" style='border:0px' class='align_middle' alt=\"".$info_bulle."\" title=\"".$info_bulle."\" />
			</a></span>";
		return $resource_link;
	}

	protected function get_query_explnum_header() {
		global $opac_show_links_invisible_docnums;

		$join_acces_explnum = "";
		if (!$opac_show_links_invisible_docnums) {
			if (!is_null($this->dom_3)) {
				$join_acces_explnum = $this->dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
			} else {
				$join_acces_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)":"").")";
			}
		}
		$query = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype FROM explnum ".$join_acces_explnum;
		if ($this->notice->niveau_biblio == 'b') {
			$query .= " JOIN bulletins ON bulletins.bulletin_id = explnum.explnum_bulletin WHERE bulletins.num_notice = ".$this->notice_id;
		} else {
			$query .= " WHERE explnum_notice = ".$this->notice_id;
		}
		$query .= " order by explnum_id";
		return $query;
	}

	// génération du header----------------------------------------------------
	public function do_header($id_tpl=0) {
		global $opac_url_base, $msg, $charset;
		global $memo_notice;
		global $opac_visionneuse_allow;
		global $opac_photo_filtre_mimetype;
		global $opac_show_links_invisible_docnums;

		$this->notice_header="";
		if(!$this->notice_id) return;

		$this->notice_header = $this->get_notice_header($id_tpl);
		$type_reduit = substr($this->notice_reduit_format,0,1);
		if ($type_reduit=="H" || $id_tpl){
		    $this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;
			return;
		}

		//$this->notice_header.="&nbsp;<span id=\"drag_symbol_drag_noti_".$this->notice->notice_id."\" style=\"visibility:hidden\"><img src=\"images/drag_symbol.png\"\></span>";
		$this->notice_header_doclink="";
		if ($this->notice->lien) {
			$this->notice_header_doclink .= $this->get_resource_link_notice_header();
		}
		$sql_explnum = $this->get_query_explnum_header();
		$explnums = pmb_mysql_query($sql_explnum);
		$explnumscount = pmb_mysql_num_rows($explnums);

		if ($opac_show_links_invisible_docnums || (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);

				if ($explnumrow->explnum_nomfichier){
					if($explnumrow->explnum_nom == $explnumrow->explnum_nomfichier)	$info_bulle=$msg["open_doc_num_notice"].$explnumrow->explnum_nomfichier;
					else $info_bulle=$explnumrow->explnum_nom;
				}elseif ($explnumrow->explnum_url){
					if($explnumrow->explnum_nom == $explnumrow->explnum_url)	$info_bulle=$msg["open_link_url_notice"].$explnumrow->explnum_url;
					else $info_bulle=$explnumrow->explnum_nom;
				}
				$this->notice_header_doclink .= "&nbsp;<span>";
				if ($opac_visionneuse_allow)
					$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
				if ($opac_visionneuse_allow && $this->docnum_allowed && ($allowed_mimetype && in_array($explnumrow->explnum_mimetype,$allowed_mimetype))){
					$this->notice_header_doclink .="
					<script>
						if(typeof(sendToVisionneuse) == 'undefined'){
							var sendToVisionneuse = function (explnum_id){
								document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
							}
						}
						function sendToVisionneuse_".$explnumrow->explnum_id."(){
								open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");
						}
					</script>";
					if($this->check_accessibility_explnum($explnumrow->explnum_id)){
						$this->notice_header_doclink .="
					<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$explnumrow->explnum_id."');\">";
					}else{
						$this->notice_header_doclink .="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$explnumrow->explnum_id.");return false;\">";
					}
				}else{
					if($this->check_accessibility_explnum($explnumrow->explnum_id)){
						$this->notice_header_doclink .= "
					<a href='#' onclick=\"auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id)."'); return false;\">";
					}else{
						$this->notice_header_doclink .= "
					<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
					}
				}
				$this->notice_header_doclink .= "<img src=\"".get_url_icon("globe_orange.png")."\" style='border:0px' class='align_middle' ";
				$this->notice_header_doclink .= " alt=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\" title=\"";
				$this->notice_header_doclink .= htmlentities($info_bulle,ENT_QUOTES,$charset);
				$this->notice_header_doclink .= "\">";
				$this->notice_header_doclink .= "</a></span>";
			} elseif ($explnumscount > 1) {
				$info_bulle=$msg["info_docs_num_notice"];
				$this->notice_header_doclink .= "<img src=\"".get_url_icon("globe_rouge.png")."\" alt=\"$info_bulle\" title=\"$info_bulle\" style='border:0px' class='align_middle' >";
			}
		}
		$this->notice_header_doclink.=$this->get_icon_is_new();

		//coins pour Zotero
		$coins_span=$this->gen_coins_span();
		$this->notice_header.=$coins_span;


		$this->notice_header_without_doclink=$this->notice_header;
		$this->notice_header.=$this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header_without_doclink"]=$this->notice_header_without_doclink;
		$memo_notice[$this->notice_id]["header_doclink"]= $this->notice_header_doclink;

		$memo_notice[$this->notice_id]["header"]=$this->notice_header;
		$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;

		$this->notice_header_with_link=inslink($this->notice_header, str_replace("!!id!!", $this->notice_id, $this->lien_rech_notice)) ;

	} // fin do_header()

	// génération du header_without_html----------------------------------------------------
	public function do_header_without_html($id_tpl=0) {
		global $opac_notice_reduit_format,$charset ;
		global $msg ;
		global $memo_notice;

		$this->notice_header_without_html="";
		if(!$this->notice_id) return;

		$type_reduit = substr($opac_notice_reduit_format,0,1);

		$notice_tpl_header="";

		$perso_voulus = array();
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($opac_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}

		if ($type_reduit=="E") {
			// zone de l'éditeur
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " - ".$this->notice->year." ";
			} elseif ($this->notice->year) {
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;

		//Champs personalisés à ajouter au réduit
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;

		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && !empty($this->parent_title))  {
			 $aff_perio_title="<i>".$msg['in_serial']." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i>";
		}

		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2 && !empty($this->parent_title))  {
			$aff_bullperio_title = " ".(!empty($this->parent_date) ? sprintf($msg["bul_titre_perio"] , $this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"));
		} else $aff_bullperio_title="";

		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$this->notice_header_without_html = $this->notice->serie_name;
			if($this->notice->tnvol) $this->notice_header_without_html .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $this->notice_header_without_html .= $this->notice->tnvol;

		if ($this->notice_header_without_html) $this->notice_header_without_html .= ". ".$this->notice->tit1 ;
		else $this->notice_header_without_html = $this->notice->tit1;

		$this->notice_header_without_html .= $aff_bullperio_title;

		if ($this->notice->niveau_biblio =='m') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $this->notice_header_without_html.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $this->notice_header_without_html.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $this->notice_header_without_html.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		$memo_notice[$this->notice_id]["header_without_html"]=$this->notice_header_without_html;

	} // fin do_header_without_html()


	// génération du header similaire (pour le notices similaires uniquement) ----------------------------------------------------
	public function do_header_similaire($id_tpl=0) {
		global $opac_notice_reduit_format, $opac_notice_reduit_format_similaire;
		if(isset($opac_notice_reduit_format_similaire)) {
			$this->notice_reduit_format = $opac_notice_reduit_format_similaire;
		} else {
			$this->notice_reduit_format = $opac_notice_reduit_format;
		}
		$this->do_header($id_tpl);
		$this->notice_reduit_format = $opac_notice_reduit_format;
	} // fin do_header_similaire ()

	// Construction des parents-----------------------------------------------------
	public function do_parents() {
		$this->parents = "";
		$r_type=array();
		$ul_opened=false;

		if($this->notice_relations->get_nb_parents()) {
			$this->parents .= "<div class='notice_parents'>";
			$display_links = $this->notice_relations->get_display_links('parents', $this);
			foreach ($display_links as $relation_type=>$relations_links) {
				if ($this->notice_relations->get_nb_parents()==1) {
					$this->parents.="<br /><b>".$relation_type."</b> ";
					$this->parents.= $relations_links[0]['display'];
					$this->parents.="<br /><br />";
					// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this->bulletin_id
					$rqbull="select bulletin_id from bulletins where num_notice=".$this->notice_id;
					$rqbullr=pmb_mysql_query($rqbull);
					if(pmb_mysql_num_rows($rqbullr)) {
						$rqbulld=@pmb_mysql_fetch_object($rqbullr);
						$this->bulletin_id=$rqbulld->bulletin_id;
					} else {
						$this->bulletin_id=0;
					}
				} else {
					if (!isset($r_type[$relation_type])) {
						$r_type[$relation_type]=1;
						if ($ul_opened) {
							$this->parents.="</ul>";
							$ul_opened=false;
						}
						else {
							$this->parents.="<br />";
						}
						$this->parents.="<b>".$relation_type."</b>";
						$this->parents.="<ul class='notice_rel'>\n";
						$ul_opened=true;
					}
					foreach ($relations_links as $parent) {
						$this->parents.="<li>".$parent['display']."</li>";
					}
					if($ul_opened==true) {
						$this->parents.="</ul>\n";
						$ul_opened=false;
					}
				}
			}
			$this->parents .= "</div>\n";
		}
		return;
	} // fin do_parents()

	protected function get_separator_keywords() {
		return "&nbsp; ";
	}

	// Construction des mots clé----------------------------------------------------
	public function do_mots_cle() {
		global $pmb_keyword_sep ;
		if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";

		if (!trim($this->notice->index_l)) return "";

		$tableau_mots = explode ($pmb_keyword_sep,trim($this->notice->index_l)) ;

		if (empty($tableau_mots)) return "";
		for ($i=0; $i<sizeof($tableau_mots); $i++) {
			$mots=trim($tableau_mots[$i]) ;
			$tableau_mots[$i] = inslink($mots, str_replace("!!mot!!", urlencode($mots), $this->lien_rech_motcle)) ;
		}
		if(ord($pmb_keyword_sep)==0xa || ord($pmb_keyword_sep)==0xd) {
			$mots_cles = implode("<br />", $tableau_mots);
		} else {
			$mots_cles = implode($this->get_separator_keywords(), $tableau_mots);
		}
		return $mots_cles ;
	}

	// récupération des info de bulletinage (si applicable)
	public function get_bul_info() {
		global $msg;
		if ($this->notice->niveau_biblio == 'a') {
			// récupération des données du bulletin et de la notice apparentée
			$requete = "SELECT b.tit1,b.notice_id,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
			$requete .= "from analysis a, notices b, bulletins c";
			$requete .= " WHERE a.analysis_notice=".$this->notice_id;
			$requete .= " AND c.bulletin_id=a.analysis_bulletin";
			$requete .= " AND c.bulletin_notice=b.notice_id";
			$requete .= " LIMIT 1";
		} elseif ($this->notice->niveau_biblio == 'b') {
			// récupération des données du bulletin et de la notice apparentée
			$requete = "SELECT tit1,notice_id,b.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
			$requete .= "from bulletins b, notices";
			$requete .= " WHERE num_notice=$this->notice_id ";
			$requete .= " AND  bulletin_notice=notice_id ";
			$requete .= " LIMIT 1";
		}
		$myQuery = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($myQuery)) {
			$parent = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $parent->tit1;
			$this->parent_id = $parent->notice_id;
			$this->bulletin_id = $parent->bulletin_id;
			$this->parent_numero = $parent->bulletin_numero;
			$this->parent_date = $parent->mention_date;
			$this->parent_date_date = $parent->date_date;
			$this->parent_aff_date_date = $parent->aff_date_date;
		}
	} // fin get_bul_info()

	protected function genere_pagination_in_perio () {
		global $charset ;

		$retour = '';
		$pagination = htmlentities($this->notice->npages,ENT_QUOTES, $charset);
		if ($pagination) $retour .= ".&nbsp;-&nbsp;$pagination";
		return $retour;
	}

	// fonction de génération de ,la mention in titre du pério + numéro
	public function genere_in_perio () {
		// serials : si article
		$retour = '';
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			$bulletin = $this->parent_title;
			$notice_mere = inslink("<span class='perio_title'>".$this->parent_title."</span>", str_replace("!!id!!", $this->parent_id, $this->lien_rech_perio));
			if($this->parent_numero) $numero = $this->parent_numero." " ;
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			if ($this->parent_date) $date_affichee = " (".$this->parent_date.")";
			elseif ($this->parent_date_date) $date_affichee = " [".formatdate($this->parent_date_date)."]";
			else $date_affichee="" ;
			$bulletin = inslink("<span class='bull_title'>".$numero.$date_affichee."</span>", str_replace("!!id!!", $this->bulletin_id, $this->lien_rech_bulletin));
			$this->bulletin_numero=$numero;
			$this->bulletin_date=$date_affichee;
			$mention_parent = "<b>in</b> $notice_mere > $bulletin ";
			$retour .= "<br />$mention_parent";
			$retour .= $this->genere_pagination_in_perio();
		}
		return $retour ;
	} // fin genere_in_perio ()

	// fonction d'affichage des exemplaires, résa et expl_num
	public function aff_resa_expl() {
		global $opac_resa ;
		global $opac_max_resa ;
		global $opac_show_exemplaires ;
		global $msg;
		global $popup_resa ;
		global $opac_resa_planning; // la résa est elle planifiée
		global $allow_book;
		global $opac_show_exemplaires_analysis;

		// afin d'éviter de recalculer un truc déjà calculé...
		if (isset($this->affichage_resa_expl_flag)) {
		    return $this->affichage_resa_expl ;
		}

		$ret='';

		if($this->notice->niveau_biblio != 's') {

			if ( (is_null($this->dom_2) && $opac_show_exemplaires && $this->visu_expl && (!$this->visu_expl_abon || ($this->visu_expl_abon && $_SESSION["user_code"]))) || ($this->rights & 8) ) {

				//Si la resa porte sur une monographie, c'est l'id de notice qui est pris en compte, sinon c'est l'id de bulletin
				$resa_id_notice=0;
				$resa_id_bulletin=0;
				if($this->notice->niveau_biblio=="m") {
					$resa_id_notice=$this->notice_id;
				} else {
					$resa_id_bulletin=$this->bulletin_id;
				}

				//Des exemplaires réservables ?
				if($resa_id_bulletin) {
					$resa_check=check_statut(0,$resa_id_bulletin) ;
				} else {
					$resa_check=check_statut($resa_id_notice,0) ;
				}

				if ($resa_check) {

					if (!$opac_resa_planning) {

						//des réservations en cours?
						if ($resa_id_bulletin) {
							$requete_resa = "SELECT count(1) FROM resa WHERE resa_idbulletin='$resa_id_bulletin' ";
						} else {
							$requete_resa = "SELECT count(1) FROM resa WHERE resa_idnotice='$resa_id_notice' ";
						}
						$nb_resa_encours = pmb_mysql_result(pmb_mysql_query($requete_resa), 0, 0) ;
						if ($nb_resa_encours) {
							$message_nbresa = str_replace("!!nbresa!!", $nb_resa_encours, $msg["resa_nb_deja_resa"]) ;
						} else {
							$message_nbresa = '';
						}

						if(($this->notice->niveau_biblio=="m" || $this->notice->niveau_biblio=="b" || ($this->notice->niveau_biblio=="a" && $opac_show_exemplaires_analysis)) && $opac_resa && !$popup_resa) {
							if ( $_SESSION["user_code"] && $allow_book ) {
								$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
								if ($opac_max_resa==0 || $opac_max_resa>$nb_resa_encours) {
								    $ret .= record_display::get_display_action_resa_expl($resa_id_notice, $resa_id_bulletin);
									$ret .= $message_nbresa ;
								} else {
								    $ret .= str_replace("!!nb_max_resa!!", $opac_max_resa, $msg["resa_nb_max_resa"]) ;
								}
								$ret.= "<br />";
							} elseif (!$_SESSION["user_code"]) {
								// utilisateur pas connecté
								// préparation lien réservation sans être connecté
								$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
								$ret .= record_display::get_display_action_resa_expl($resa_id_notice, $resa_id_bulletin);
								$ret .= $message_nbresa ;
								$ret .= "<br />";
							}
						}

					} else {

						//des prévisions en cours?
						if($resa_id_bulletin) {
							$nb_resa_encours = resa_planning::count_resa(0,$resa_id_bulletin);
						}else {
							$nb_resa_encours = resa_planning::count_resa($resa_id_notice,0);
						}
						if ($nb_resa_encours) {
							$message_nbresa = str_replace("!!nbresa!!", $nb_resa_encours, $msg["resa_nb_deja_resa"]) ;
						} else {
							$message_nbresa = '';
						}

						if(($this->notice->niveau_biblio=="m" || $this->notice->niveau_biblio=="b" || ($this->notice->niveau_biblio=="a" && $opac_show_exemplaires_analysis)) && $opac_resa && !$popup_resa) {


							if ($_SESSION["user_code"] && $allow_book) {
								$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
								if ($opac_max_resa==0 || $opac_max_resa>$nb_resa_encours) {
								    $ret .= record_display::get_display_action_resa_planning_expl($resa_id_notice, $resa_id_bulletin);
									$ret .= $message_nbresa ;
								} else {
									$ret .= str_replace("!!nb_max_resa!!", $opac_max_resa, $msg["resa_nb_max_resa"]) ;
								}
								$ret.= "<br />";
							} elseif (!$_SESSION["user_code"]) {
								// utilisateur pas connecté
								// préparation lien réservation sans être connecté
								$ret .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
								$ret .= record_display::get_display_action_resa_planning_expl($resa_id_notice, $resa_id_bulletin);
								$ret .= $message_nbresa ;
								$ret .= "<br />";
							}
						}
					}
				}
				if (!$this->bulletin_id) {
					$record_datas = record_display::get_record_datas($this->notice_id);
					if ($record_datas->is_numeric()) {
						if ($record_datas->get_availability() && $_SESSION["user_code"]) {
							$this->affichage_expl = $this->get_display_pnb_loan_button();
							$ret.= $this->affichage_expl;
						}
						//affichage exemplaires numeriques
						if ($this->docnum_allowed) {
							$ret.= $this->aff_explnum();
						}
						if (($autres_lectures = static::autres_lectures($this->notice_id,$this->bulletin_id))) {
							$ret.= $autres_lectures;
						}
						$this->affichage_resa_expl = $ret ;
						$this->affichage_resa_expl_flag = 1 ;
						return $ret;
					}
				}
				$this->affichage_expl = static::expl_list($this->notice->niveau_biblio,$this->notice_id, $this->bulletin_id);
				$ret.= $this->affichage_expl;
			}
		}

		//affichage exemplaires numeriques
		if($this->docnum_allowed) $ret.= $this->aff_explnum();

		if (($autres_lectures = static::autres_lectures($this->notice_id,$this->bulletin_id))) {
			$ret .= $autres_lectures;
		}
		$this->affichage_resa_expl = $ret ;
		$this->affichage_resa_expl_flag = 1 ;
		return $ret ;
	}

	/**
	 * function pour affichager le bouton d'emprunt d'un prêt pnb
	 *
	 * @return string code html du boutton
	 */
	public function get_display_pnb_loan_button() {
	    global $msg;
	    global $charset, $allow_pnb, $opac_pnb_loan_display_mode;

	    // $allow_pnb = Droit à l'emprunt de document numérique
	    if ($allow_pnb && dilicom::is_pnb_active()) {
	        return '
            <div id="pnb_notice-' . $this->notice_id . '">
				<h3>'.htmlentities($msg['pnb_digital_expl'], ENT_QUOTES, $charset).'</h3>
				<a id="bt_pnb_NotCourte-' . $this->notice_id . '" href="#" onclick="pnb_post_loan_info(' . $this->notice_id . ','.$opac_pnb_loan_display_mode.');return false;">' . htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset).'</a>
                <div id="response_pnb_pret_' . $this->notice_id . '">
                </div>
			</div>';
	    }
	    return "";
	}


	protected function aff_title_explnum() {
	    global $msg;
	    global $opac_rgaa_active;

	    return "<a name='docnum'><h3><span ".((1 == $opac_rgaa_active) ? 'class' : 'id')."='titre_explnum'>".$msg['explnum']."</span></h3></a>";

	}

	// fonction d'affichage des exemplaires numeriques
	public function aff_explnum () {
		global $opac_show_links_invisible_docnums;

		$ret='';
		if ($opac_show_links_invisible_docnums || (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"]))) || ($this->rights & 16)){
			if ($this->notice->niveau_biblio=="b" && ($explnum = show_explnum_per_notice(0, $this->bulletin_id, ''))) {
				$ret .= $this->aff_title_explnum().$explnum;
				$this->affichage_expl .= $this->aff_title_explnum().$explnum;
			} elseif (($explnum = show_explnum_per_notice($this->notice_id,0, ''))) {
			    $ret .= $this->aff_title_explnum().$explnum;
			    $this->affichage_expl .= $this->aff_title_explnum().$explnum;
			}
		}
		return $ret;
	} // fin aff_explnum ()


	public function get_aff_fields_perso() {
		$aff_fields_perso = "" ;
		if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!isset($this->memo_perso_)) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"] !== '') {
					$aff_fields_perso .= $this->get_line_aff_suite(strip_tags($p["TITRE"]), ($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"])), 'persofield');
				}
			}
		}
		return $aff_fields_perso;
	}

	// fonction d'affichage de la suite ISBD ou PUBLIC : partie commune, pour éviter la redondance de calcul
	public function aff_suite() {
		global $msg;
		global $charset;
		global $opac_allow_tags_search, $opac_permalink, $opac_url_base;

		// afin d'éviter de recalculer un truc déjà calculé...
		if(!isset($this->affichage_suite_flag)) $this->affichage_suite_flag = 0;
		if ($this->affichage_suite_flag) return $this->affichage_suite ;

		// toutes indexations
		$ret_index = "";
		// Catégories
		// On demande à afficher le header seulement et on arrive ici..appelons donc le fetch_categories
		if($this->header_only) {
			$this->fetch_categories();
		}
		$ret_index .= $this->get_line_aff_suite($msg['categories_start'], $this->categories_toutes, 'categ');

		// Concepts
		$concepts_list = new skos_concepts_list();
		if ($concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->notice_id)) {
			$ret_index .= $this->get_line_aff_suite($msg['concepts_start'], skos_view_concepts::get_list_in_notice($concepts_list), 'concept');
		}

		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente
		if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		else $libelle_key = 	$msg['motscle_start'];

		// indexation libre
		$ret_index .= $this->get_line_aff_suite($libelle_key, nl2br($this->do_mots_cle()), 'keywords');

		// indexation interne
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index .= $this->get_line_aff_suite($msg['indexint_start'], inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." <span>".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</span>", 'indexint');
		}
		$ret = $ret_index;

		// résumé
		$ret .= $this->get_line_aff_suite($msg['n_resume_start'], nl2br($this->notice->n_resume), 'nresume');

		// note de contenu
		$ret .= $this->get_line_aff_suite($msg['n_contenu_start'], nl2br($this->notice->n_contenu), 'contenu');

		//Champs personalisés
		$ret .= $this->get_aff_fields_perso();

		if ($this->notice->lien) {
			$ret .= $this->get_line_aff_suite($msg['lien_start'], $this->get_constructed_external_url(), 'lien');
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') {
				$ret .= $this->get_line_aff_suite($msg["eformat_start"], $this->notice->eformat, 'eformat');
			}
		}
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
			}else {
				$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
			}
			$ret .= $this->get_line_aff_suite($msg['notice_permalink'], "<a href='".$permalink."'>".substr($permalink,0,80)."</a>", 'permalink');
		}
		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()

	public function gen_coins_span(){
		$coins_span = record_display::get_display_coins_span($this->notice_id);
		return $coins_span;
	}

	// fonction de génération d'une colonne du tableau des exemplaires
	public static function get_display_column($label='', $expl=array()) {
		global $msg, $charset;
		global $opac_url_base;
		global $memo_p_perso_expl;

		$column = '';
		if (strstr($label, "#")) {
		    if (!$memo_p_perso_expl->no_special_fields) {
		        $id=substr($label,1);
		        $column .="<td class='".htmlentities($memo_p_perso_expl->t_fields[$id]['NAME'],ENT_QUOTES, $charset)."'>".htmlentities($expl[$label], ENT_QUOTES, $charset)."</td>";
		    }
		} elseif (($label == "location_libelle") && $expl['num_infopage']) {
			if ($expl['surloc_id'] != "0") $param_surloc="&surloc=".$expl['surloc_id'];
			else $param_surloc="";
			$column .="<td class='".$label."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$label], ENT_QUOTES, $charset)."</a></td>";
		} else if ($label=="expl_comment") {
			$column.="<td class='".$label."'>".nl2br(htmlentities($expl[$label],ENT_QUOTES, $charset))."</td>";
		} elseif ($label=="expl_cb") {
			$column.="<td class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		} elseif ($label=="section_libelle") {
			$column.="<td class='".$label."'>".htmlentities((!empty($expl['section_libelle_opac']) ? $expl['section_libelle_opac'] : $expl[$label]),ENT_QUOTES, $charset)."</td>";
		} else {
			$column .="<td class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		}
		return $column;
	}

	public static function get_display_situation($expl) {
		global $msg, $charset;
		global $opac_show_empr ;
		global $pmb_transferts_actif, $transferts_statut_transferts;

		$situation = "";
		if ($expl['statut_libelle_opac'] != "") $situation .= $expl['statut_libelle_opac']."<br />";
		if ($expl['flag_resa']) {
			$situation .= "<strong>".$msg['expl_reserve']."</strong>";
		} else {
			if ($expl['pret_flag']) {
				if($expl['pret_retour']) { // exemplaire sorti
					if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
						$situation .= $msg['entete_show_empr'].htmlentities(" ".$expl['empr_prenom']." ".$expl['empr_nom'],ENT_QUOTES, $charset)."<br />";
					}
							$situation .= "<strong>".str_replace('!!date!!', formatdate($expl['pret_retour']), $msg['out_until'] )."</strong>";
					// ****** Affichage de l'emprunteur
				} else { // pas sorti
					$situation .= "<strong>".$msg['available']."</strong>";
				}
			} else { // pas prêtable
				// exemplaire pas prêtable, on affiche juste "exclu du pret"
				if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
					$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>";
				} else {
					$situation .= "<strong>".$msg['exclu']."</strong>";
				}
			}
		} // fin if else $flag_resa
		return $situation;
	}

	// fonction de génération du tableau des exemplaires
	public static function expl_list($type, $id, $bull_id = 0, $build_ifempty = 1) {
		global $msg, $charset;
		global $pmb_transferts_actif, $transferts_statut_transferts;
		global $memo_p_perso_expl, $memo_expl;
		global $opac_show_empty_items_block, $opac_show_exemplaires_analysis, $opac_aff_expl_localises;

		// Les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "";

		$nb_perso_aff = 0;
		$found_p_perso = 0;

		if($bull_id) {
			$exemplaires = new exemplaires(0, $bull_id);
			$expls_datas = $exemplaires->get_data();
		} else {
			$record_datas = record_display::get_record_datas($id);
			$expls_datas = $record_datas->get_expls_datas();
		}
		$template_path_expl_list = "./includes/templates/expl_list.tpl.html";
		$template_path_expl_list_subst = "./includes/templates/expl_list_subst.tpl.html";

		if(file_exists($template_path_expl_list_subst)) {
			$template_path_expl_list = $template_path_expl_list_subst;
		}

		if(!$memo_p_perso_expl) {
			$memo_p_perso_expl = new parametres_perso("expl");
		}

		//formatting data before sending to view
		$expls_datas_formatted = array(
			"expls" => [],
			"expls_loc" => [],
			"columns" => [],
			"nb_expl_other_loc" => 0,
			"nb_p_perso" => 0,
			"nb_colspan" => 0,
		);
		foreach ($expls_datas["expls"] as $key => $expl) {
			$expls_datas_formatted["expls"][$key] = $expl;

			// parse all html entities for the data to display
			foreach ($expls_datas_formatted["expls"][$key] as $index => $value) {
				if (is_string($value)) {
					$expls_datas_formatted[$index] = htmlentities($value, ENT_QUOTES, $charset);
				}
			}

			$expls_datas_formatted["expls"][$key]["parity"] = ($key % 2 == 0) ? "even" : "odd";
			$expls_datas_formatted["expls"][$key]["expl_situation"] = static::get_display_situation($expl);
			$expls_datas_formatted["expls"][$key]["expl_p_perso"] = $memo_p_perso_expl->show_fields($expl['expl_id']);

			foreach ($expls_datas_formatted["expls"][$key]["expl_p_perso"]["FIELDS"] as $index => $p) {
				$expls_datas_formatted["expls"][$key]["expl_p_perso"]["FIELDS"][$index]['NOT_IN_COLUMNS'] = var_export(!in_array('#'.$p['ID'], $expls_datas['colonnesarray']), true);
			}

			// Mémorisation des exemplaires et de leur localisation
			$memo_expl['expl'][] = array(
				'expl_id' => $expl['expl_id'],
				'expl_location'	=> array($expl['expl_location']),
				'id_notice' => $id,
				'id_bulletin' => $expl['id_bulletin']
			);

			if ($expl['flag_resa']) {
				$expls_datas_formatted["expls"][$key]["class_statut"] = "expl_reserve";
			} else {
				if ($expl['pret_flag']) {
					if($expl['pret_retour']) { // exemplaire sorti
						$expls_datas_formatted["expls"][$key]["class_statut"] = "expl_out";
					} else { // pas sorti
						$expls_datas_formatted["expls"][$key]["class_statut"] = "expl_available";
					}
				} else { // pas prêtable
					// exemplaire pas prêtable, on affiche juste "exclu du pret"
					if ($pmb_transferts_actif == "1" && $expl['expl_statut'] == $transferts_statut_transferts) {
						$expls_datas_formatted["expls"][$key]["class_statut"] = "expl_transfert";
					} else {
						$expls_datas_formatted["expls"][$key]["class_statut"] = "expl_unavailable";
					}
				}
			}

			foreach ($expls_datas['colonnesarray'] as $k => $column) {
				$expls_datas_formatted['columns'][$k] = ["column" => $column];
				if (strstr($column, "#")) {
					if (!$memo_p_perso_expl->no_special_fields) {
						$id=substr($column,1);

						$expls_datas_formatted['columns'][$k]["name"] = $memo_p_perso_expl->t_fields[$id]['NAME'];
						$expls_datas_formatted['columns'][$k]["title"] = htmlentities($memo_p_perso_expl->t_fields[$id]['TITRE'], ENT_QUOTES, $charset);
						$expls_datas_formatted['columns'][$k]["entry"] = htmlentities($expls_datas_formatted["expls"][$key][$expls_datas_formatted['columns'][$key]["column"]], ENT_QUOTES, $charset);
					}
				} else {
					$expls_datas_formatted['columns'][$k]["msg"] = htmlentities($msg['expl_header_' . $column],ENT_QUOTES, $charset);
					if($expls_datas_formatted['columns'][$k]["column"] == "location_libelle" && $expl['num_infopage']) {
						$expl['param_surloc'] = $expl['surloc_id'] != "0" ? "&surloc=".$expl['surloc_id'] : "";
					}
				}
			}
			if($opac_aff_expl_localises && isset($_SESSION["empr_location"]) && $_SESSION["empr_location"]) {
				if($expl['expl_location'] != $_SESSION["empr_location"]) {
					if(!isset($expls_datas_formatted["nb_expl_other_loc"])) {
						$expls_datas_formatted["nb_expl_other_loc"] = 0;
					}
					$expls_datas_formatted["nb_expl_other_loc"]++;
				} else {
					if(!isset($expls_datas_formatted["expls_loc"])) {
						$expls_datas_formatted["expls_loc"] = [];
					}
					$expls_datas_formatted["expls_loc"][] = $expls_datas_formatted["expls"][$key];
				}
			}

			//Champs personalisés
			if (!$memo_p_perso_expl->no_special_fields) {
				$perso_ = $expls_datas_formatted["expls"][$key]["expl_p_perso"];
				for ($i = 0; $i < count($perso_["FIELDS"]); $i++) {
					$p=$perso_["FIELDS"][$i];
					if ($p['OPAC_SHOW'] && !in_array('#' . $p['ID'], $expls_datas['colonnesarray'])) {
						if(!$found_p_perso) {
							$nb_perso_aff++;
						}
					}
				}
			}
			$found_p_perso = 1;
		}

		$expls_datas_formatted["nb_p_perso"] = count($expls_datas['colonnesarray']) + 1 + $nb_perso_aff;
		$expls_datas_formatted["nb_colspan"] = count($expls_datas['colonnesarray']) + 1;

		if(count($expls_datas_formatted["expls"]) == 0 && !$opac_show_empty_items_block) {
			return "";
		}

		$data = array(
			'id' => $id + $bull_id,
			'empr_location_libelle' => $_SESSION["empr_location_libelle"] ?? "",
			'expls_datas' => $expls_datas_formatted,
			'memo_p_perso_expl' => $memo_p_perso_expl
		);

		$h2o_expl_list = H2o_collection::get_instance($template_path_expl_list);
		return $h2o_expl_list->render($data);
	}

	// fontion qui génère le bloc H3 + table des autres lectures
	public static function autres_lectures ($notice_id=0,$bulletin_id=0) {
		global $msg;
		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;

		$notice_id = intval($notice_id);
		$bulletin_id = intval($bulletin_id);
		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return "";

		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		else $restrict_date="";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		else $pas_notice = "";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		else $pas_bulletin = "";
		// Ajout ici de la liste des notices lues par les lecteurs de cette notice
		$rqt_autres_lectures = "SELECT oal.arc_expl_notice, oal.arc_expl_bulletin, count(*) AS total_prets,
					trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '%d/%m/%Y'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id
				FROM ((((pret_archive AS oal JOIN
					(SELECT distinct arc_id_empr FROM pret_archive nbec where (nbec.arc_expl_notice='".$notice_id."' AND nbec.arc_expl_bulletin='".$bulletin_id."') AND nbec.arc_id_empr !=0) as nbec
					ON (oal.arc_id_empr=nbec.arc_id_empr and oal.arc_id_empr!=0 and nbec.arc_id_empr!=0))
					LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id )
					LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id)
					LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				WHERE $restrict_date $pas_notice $pas_bulletin oal.arc_id_empr !=0
				GROUP BY oal.arc_expl_notice, oal.arc_expl_bulletin
				HAVING total_prets>=$opac_autres_lectures_nb_mini_emprunts
				ORDER BY $opac_autres_lectures_tri
				";

		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures) or die ("<br />".pmb_mysql_error()."<br />".$rqt_autres_lectures."<br />");
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			$odd_even=1;
			$inotvisible=0;
			$ret="";

			//droits d'acces emprunteur/notice
			$acces_j='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}

			if($acces_j) {
				$statut_j='';
				$statut_r='';
			} else {
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}

			while (($data=pmb_mysql_fetch_array($res_autres_lectures))) { // $inotvisible<=$opac_autres_lectures_nb_maxi
				$requete = "SELECT  1  ";
				$requete .= " FROM notices $acces_j $statut_j  WHERE notice_id='".$data['not_id']."' $statut_r ";
				$myQuery = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($myQuery) && $inotvisible<=$opac_autres_lectures_nb_maxi) { // pmb_mysql_num_rows($myQuery)
					$inotvisible++;
					$titre = $data['tit'];
					// **********
					$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
					$responsab = get_notice_authors($data['not_id']) ;
					$as = array_search ("0", $responsab["responsabilites"]) ;
					if ($as!== FALSE && $as!== NULL) {
						$auteur_0 = $responsab["auteurs"][$as] ;
						$auteur = new auteur($auteur_0["id"]);
						$mention_resp = $auteur->get_isbd();
					} else {
						$aut1_libelle = array();
						$as = array_keys ($responsab["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsab["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->get_isbd();
						}
						$mention_resp = implode (", ",$aut1_libelle) ;
					}
					$mention_resp ? $auteur = $mention_resp : $auteur="";

					// on affiche les résultats
					if ($odd_even==0) {
						$pair_impair="odd";
						$odd_even=1;
					} else if ($odd_even==1) {
						$pair_impair="even";
						$odd_even=0;
					}
					if ($data['arc_expl_notice']) $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=notice_display&id=".$data['not_id']."&seule=1';\" style='cursor: pointer' ";
						else $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=bulletin_display&id=".$data['arc_expl_bulletin']."';\" style='cursor: pointer' ";
					$ret .= "<tr $tr_javascript>";
					$ret .= "<td>".$titre."</td>";
					$ret .= "<td>".$auteur."</td>";
					$ret .= "</tr>\n";
				}
			}
			if ($ret) $ret = "<h3 class='autres_lectures'>".$msg['autres_lectures']."</h3><table style='width:100%;'>".$ret."</table>";
		} else $ret="";

	return $ret;
	} // fin autres_lectures ($notice_id=0,$bulletin_id=0)

	public function get_html_image($depliable, $url_image_ok, $title_image_ok) {
		global $msg, $opac_url_base, $charset;

		if ($depliable) {
			return "<img class='vignetteimg align_right' src='".$opac_url_base."images/vide.png' title=\"".$title_image_ok."\" vigurl=\"".$url_image_ok."\" alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."' />";
		} else {
			return "<img class='vignetteimg align_right' src='".$url_image_ok."' title=\"".$title_image_ok."\" alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."' />";
		}
	}

	public function get_image($depliable) {
		global $charset;
		global $opac_show_book_pics ;
		global $opac_book_pics_msg;
		global $use_opac_url_base ;

		if ($opac_show_book_pics) {
			$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			if($use_opac_url_base) {
			    $url_image_ok = $thumbnailSourcesHandler->generateSrcBase64(TYPE_NOTICE, $this->notice_id);
			} else {
			    $url_image_ok = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $this->notice_id);
			}

			$title_image_ok = "";
			if(!$url_image_ok) {
				$title_image_ok = htmlentities($opac_book_pics_msg, ENT_QUOTES, $charset);
			}
			if(!trim($title_image_ok)){
				$title_image_ok = htmlentities($this->notice->tit1, ENT_QUOTES, $charset);
			}
			return $this->get_html_image($depliable, $url_image_ok, $title_image_ok);
		} else {
			return "";
		}
	}

	public function do_image(&$entree,$depliable) {
		global $opac_notice_is_pdf;
		$image = $this->get_image($depliable);
		if (isset($opac_notice_is_pdf) && $opac_notice_is_pdf == true && $image) {
		    $entree = "<table style='width:100%' role='presentation'><tr><td style='vertical-align:top;width:80%'>$entree</td><td class='align_right;width:20%' style='vertical-align:top'>$image</td></tr></table>" ;
		} elseif ($image) {
			$entree = "<table style='width:100%' role='presentation'><tr><td style='vertical-align:top'>$entree</td><td class='align_right' style='vertical-align:top'>$image</td></tr></table>" ;
		} else {
			$entree = "<table style='width:100%' role='presentation'><tr><td>$entree</td></tr></table>" ;
		}
	} // fin do_image(&$entree,$depliable)

	public function get_icon_html($niveau_biblio, $typdoc) {
		global $msg, $charset, $opac_url_base;
		global $tdoc;

		$html_icon = '';
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = $icon_doc->table[$niveau_biblio.$typdoc];
		if ($icon) {
			$biblio_doc = marc_list_collection::get_instance('nivbiblio');
			$info_bulle_icon=str_replace("!!niveau_biblio!!",$biblio_doc->table[$niveau_biblio],$msg["info_bulle_icon"]);
			$info_bulle_icon=str_replace("!!typdoc!!",$tdoc->table[$typdoc],$info_bulle_icon);
			$html_icon ="<img src=\"".$opac_url_base."images/$icon\" alt='".htmlentities($info_bulle_icon, ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon, ENT_QUOTES, $charset)."'/>";
		} else {
			$html_icon = "";
		}
		return $html_icon;
	}

	public function get_icon_is_new() {
		global $msg, $charset;

		$icon_is_new="";
		if (!$this->no_header && $this->notice->notice_is_new){
				$info_bulle_icon_new=$msg["notice_is_new_gestion"];
				$icon_is_new.="&nbsp;<img src=\"".get_url_icon("icone_nouveautes.png")."\" alt='".htmlentities($info_bulle_icon_new,ENT_QUOTES, $charset)."' title='".htmlentities($info_bulle_icon_new,ENT_QUOTES, $charset)."'/>";
		}
		return $icon_is_new;
	}

	protected function link_see_more($total=0) {
		global $msg;

		$link_see_more = "<br />";
		if ($this->lien_rech_notice) {
			$link_see_more.="<a href='".str_replace("!!id!!",$this->notice_id,$this->lien_rech_notice)."&seule=1'>";
		}
		$link_see_more .= sprintf($msg["see_all_childs"],20,$total,$total-20);
		if ($this->lien_rech_notice) $link_see_more .= "</a>";
		return $link_see_more;
	}

	protected function get_display_title_childs_relation($relation_type) {
		return "<b>".$relation_type."</b>";
	}

	protected function genere_childs_relation($relation_type, $child_notices) {
		$notice_childs = $this->get_display_title_childs_relation($relation_type);
		if (!$this->seule) {
			$notice_childs .= "<ul>";
		}
		foreach ($child_notices as $i=>$child_data) {
			if(($i<20) || $this->seule) {
				if (!$this->seule) {
					$notice_childs .= "<li>".$child_data['display']."</li>";
				} elseif($child_data['header_only']) {
					$notice_childs .= "<ul><li>".$child_data['display']."</li></ul>";
				} else {
					$notice_childs .= $child_data['display'];
				}
			} else {
				break;
			}
		}
		if ((count($child_notices)>20) && (!$this->seule)) {
			$notice_childs .= $this->link_see_more(count($child_notices));
		}
		if (!$this->seule) {
			$notice_childs.="</ul>";
		}
		return $notice_childs;
	}

	public function genere_notice_childs() {
		$this->antiloop[$this->notice_id]=true;
		//Notices liées
		$this->notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
		$display_links_pairs = $this->notice_relations->get_display_links('pairs', $this);
		$display_links_childs = $this->notice_relations->get_display_links('childs', $this);
		if(!$this->notice_childs && !$this->to_print && ($display_links_pairs || $display_links_childs)) {
			$this->notice_childs = '';
			$notice_pairs = '';
			foreach ($display_links_pairs as $relation_type=>$child_notices) {
				$notice_pairs .= $this->genere_childs_relation($relation_type, $child_notices);
			}
			$this->notice_childs .= "<div class='notice_pairs'>".$notice_pairs."</div>";

			$notice_childs = '';
			foreach ($display_links_childs as $relation_type=>$child_notices) {
				$notice_childs .= $this->genere_childs_relation($relation_type, $child_notices);
			}
			$this->notice_childs .= "<div class='notice_childs'>".$notice_childs."</div>";
		} else {
			$this->notice_childs="";
		}
		return $this->notice_childs ;
	}

	public function get_bulletins(){
		$nb_bul=0;
		if($this->notice->opac_visible_bulletinage){
			//Droits d'accès
			if (is_null($this->dom_2)) {
				$acces_j='';
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			} else {
				$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
				$statut_j = "";
				$statut_r = "";
			}

			//Bulletins sans notice
			$req="SELECT bulletin_id FROM bulletins WHERE bulletin_notice='".$this->notice_id."' and num_notice=0";
			$res = pmb_mysql_query($req);
			if($res){
				$nb_bul+=pmb_mysql_num_rows($res);
			}

			//Bulletins avec notice
			$req="SELECT bulletin_id FROM bulletins
				JOIN notices ON notice_id=num_notice AND num_notice!=0
				".$acces_j." ".$statut_j."
				WHERE bulletin_notice='".$this->notice_id."'
				".$statut_r."";
			$res = pmb_mysql_query($req);
			if($res){
				$nb_bul+=pmb_mysql_num_rows($res);
			}
		}
		return $nb_bul;
	}

	public function get_bulletins_info(){
		if($this->notice->opac_visible_bulletinage){
			$i=0;
			//Droits d'accès
			if (is_null($this->dom_2)) {
				$acces_j='';
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			} else {
				$acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
				$statut_j = "";
				$statut_r = "";
			}

			//Bulletins sans notice
			$req="SELECT * FROM bulletins WHERE bulletin_notice='".$this->notice_id."' and num_notice=0";
			$res = pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				while($r=pmb_mysql_fetch_object($res)){
					$this->bulletins_info[$i]["bulletin_id"]=$r->bulletin_id;
					$this->bulletins_info[$i]["bulletin_numero"]=$r->bulletin_numero;
					$this->bulletins_info[$i]["mention_date"]=$r->mention_date;
					$this->bulletins_info[$i]["date_date"]=$r->date_date;
					$this->bulletins_info[$i]["bulletin_titre"]=$r->bulletin_titre;
					$this->bulletins_info[$i]["num_notice"]=$r->num_notice;
					$i++;
				}
			}

			//Bulletins avec notice
			$req="SELECT bulletins.* FROM bulletins
				JOIN notices ON notice_id=num_notice AND num_notice!=0
				".$acces_j." ".$statut_j."
				WHERE bulletin_notice='".$this->notice_id."'
				".$statut_r."";
			$res = pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				while($r=pmb_mysql_fetch_object($res)){
					$this->bulletins_info[$i]["bulletin_id"]=$r->bulletin_id;
					$this->bulletins_info[$i]["bulletin_numero"]=$r->bulletin_numero;
					$this->bulletins_info[$i]["mention_date"]=$r->mention_date;
					$this->bulletins_info[$i]["date_date"]=$r->date_date;
					$this->bulletins_info[$i]["bulletin_titre"]=$r->bulletin_titre;
					$this->bulletins_info[$i]["num_notice"]=$r->num_notice;
					$i++;
				}
			}
		}
		return 0;
	}
	public function get_bulletins_docnums() {
		return $this->record_datas->get_bulletins_docnums();
	}

	/*
	 * Un pério est ouvert à la recherche si il possède au moins un article ou une notice de bulletin
	 */
	public function open_to_search(){
		return $this->record_datas->is_open_to_search();
	}

	public function get_serialcirc_form_actions(){
		global $opac_serialcirc_active;
		global $allow_serialcirc;
		$display ="";
		//si on n'est pas connecté, il n'y a pas de boutons à afficher
		if($_SESSION['id_empr_session'] && $opac_serialcirc_active && $this->notice->opac_serialcirc_demande && $allow_serialcirc){
			$display .= record_display::get_display_serialcirc_form_actions($this->notice_id);
		}
		return $display;
	}

	public function get_simili_script(){
		return record_display::get_display_simili_script($this->notice_id);
	}

	public function get_simili_search($depliable=1) {
		global $opac_allow_simili_search;

		$simili_search = "";
		if($opac_allow_simili_search) {
			switch($opac_allow_simili_search){
				case "1" :
					$simili_search .="
						<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>
						<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
					break;
				case "2" :
					$simili_search .="
						<div id='expl_voisin_search_".$this->notice_id."' class='expl_voisin_search'></div>";
					break;
				case "3" :
					$simili_search .="
						<div id='simili_search_".$this->notice_id."' class='simili_search'></div>";
					break;
			}
			if(!$depliable){
				$simili_search .="
					<script>
						".$this->get_simili_script()."
					</script>";
			}
		}
		return $simili_search;
	}

	public function generate_hash() {
		$hash = "";
		$query = "select notice_id, create_date from notices where notice_id=".$this->notice_id;
		$result = pmb_mysql_query($query);
		if ($result) {
			if (pmb_mysql_num_rows($result) == 1) {
				$row = pmb_mysql_fetch_object($result);
				$short_request_uri = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "/")+1);
				$hash = md5($row->notice_id."_".$this->datetime."_".$row->create_date."_".$short_request_uri);
			}
		}
		return $hash;
	}

	/**
	 * Retourne les informations sur la notice
	 */
	static public function get_infos_notice($id_notice){
		$id_notice = intval($id_notice);
		$infos_notice = array();
		$query ="select notice_id, typdoc, niveau_biblio, index_l, libelle_categorie, name_pclass, indexint_name
			from notices n
			left join notices_categories nc on nc.notcateg_notice=n.notice_id
			left join categories c on nc.num_noeud=c.num_noeud
			left join indexint i on n.indexint=i.indexint_id
			left join pclassement pc on i.num_pclass=pc.id_pclass
			where notice_id='".$id_notice."'";
		$result = pmb_mysql_query($query);
		if ($result) {
			$infos_notice = pmb_mysql_fetch_array($result);
		}
		return $infos_notice;
	}

	/**
	 * Retourne les informations d'exemplaires rattachés à la notice
	 */
	static public function get_infos_expl($id_notice){
		$id_notice = intval($id_notice);
		$infos_expl = array();
		$query = " select section_libelle, location_libelle, statut_libelle, codestat_libelle, expl_date_depot, expl_date_retour, tdoc_libelle
				from exemplaires e
				left join docs_codestat co on e.expl_codestat = co.idcode
				left join docs_location dl on e.expl_location=dl.idlocation
				left join docs_section ds on ds.idsection=e.expl_section
				left join docs_statut dst on e.expl_statut=dst.idstatut
				left join docs_type dt on dt.idtyp_doc=e.expl_typdoc
				where expl_notice='".$id_notice."'";
		$result = pmb_mysql_query($query);
		while(($row = pmb_mysql_fetch_array($result))){
			$infos_expl[]=$row;
		}
		return $infos_expl;
	}

	/**
	 * Vérifie que la requête Ajax soit bien envoyée par une action utilisateur
	 */
	static public function check_token($id_notice, $datetime, $token) {
		$id_notice = intval($id_notice);
		$query = "select notice_id, create_date from notices where notice_id=".$id_notice;
		$result = pmb_mysql_query($query);
		if ($result) {
			if (pmb_mysql_num_rows($result) == 1) {
				$row = pmb_mysql_fetch_object($result);
				if(isset($_SERVER["HTTP_REFERER"])) {
				    $short_referer = substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);
				} else {
				    $short_referer = '';
				}
				$hash = md5($row->notice_id."_".$datetime."_".$row->create_date."_".$short_referer);
				if ($token == $hash) {
					return true;
				}
			}
		}
		return false;
	}

	public function aff_demand() {
		global $msg, $opac_demandes_allow_from_record;
		if ($opac_demandes_allow_from_record && $_SESSION['id_empr_session'] && !$this->affichage_demand) {
			$this->affichage_demand = "<h3>".$msg['demandes_demande']."</h3>".record_display::get_display_demand($this->notice_id);
		}
		return $this->affichage_demand;
	}

	public function aff_scan_requests() {
		global $msg, $opac_scan_request_activate, $allow_scan_request;
		if ($opac_scan_request_activate && $_SESSION['id_empr_session'] && $allow_scan_request
			&& (is_null($this->dom_2) && $this->visu_scan_request && (!$this->visu_scan_request_abon || ($this->visu_scan_request_abon && $_SESSION["user_code"])) || ($this->rights & 32))
			&& !$this->affichage_scan_requests) {
			$this->affichage_scan_requests = "<h3>".$msg['scan_request_scan']."</h3>".record_display::get_display_scan_request($this->notice_id);
		}
		return $this->affichage_scan_requests;
	}

	protected function do_connectors() {
		// On gère un flag pour les cas particuliers des notices cairn qui ne seraient pas issue du connecteur
		$from_cairn_connector = false;
		$query = "SELECT recid FROM notices_externes WHERE num_notice = " . $this->notice_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$recid = pmb_mysql_result($result, 0,0);
			$data = explode(" ", $recid);
			$external_rec_id = array(
					'recid' => $recid,
					'connector' => $data[0],
					'source_id' => $data[1],
					'ref' => $data[2]
			);
			if ($external_rec_id['connector'] == 'cairn') {
				$from_cairn_connector = true;
			}
		}
		if ($from_cairn_connector || (strpos($this->notice->lien, "cairn.info") !== false)) {
			$cairn_connector = new cairn();
			$cairn_sso_params = $cairn_connector->get_sso_params();
			if ($cairn_sso_params && (strpos($this->notice->lien, "?") === false)) {
				$this->notice->lien.= "?";
				$cairn_sso_params = substr($cairn_sso_params, 1);
			}
			$this->notice->lien.= $cairn_sso_params;
		}
	}

	protected function get_constructed_external_url() {
		global $charset;
		$external_url = '';
		if (substr($this->notice->eformat,0,3)=='RSS') {
			$external_url .= affiche_rss($this->notice->notice_id) ;
		} else {
			$external_url .= "<a href=\"".$this->notice->lien."\" target=\"top\" class='lien856' type=\"external_url_notice\">";
			if (strlen($this->notice->lien)>80) {
				$external_url .= htmlentities(substr($this->notice->lien, 0, 80),ENT_QUOTES,$charset)."</a>&nbsp;[...]";
			} else {
				$external_url .= htmlentities($this->notice->lien,ENT_QUOTES,$charset)."</a>";
			}
		}
		return $external_url;
	}

	protected function check_accessibility_explnum($explnum_id=0) {
		return $this->record_datas->check_accessibility_explnum($explnum_id);
	}

	public function get_parents_header_without_html() {
		return $this->parents_header_without_html;
	}

	protected function set_liens_rech($liens) {
		if(isset($liens['lien_rech_notice'])) {
			$this->lien_rech_notice = $liens['lien_rech_notice'];
		} else {
			$this->lien_rech_notice = '';
		}
		if(isset($liens['lien_rech_auteur'])) {
			$this->lien_rech_auteur = $liens['lien_rech_auteur'];
		} else {
			$this->lien_rech_auteur = '';
		}
		if(isset($liens['lien_rech_editeur'])) {
			$this->lien_rech_editeur = $liens['lien_rech_editeur'];
		} else {
			$this->lien_rech_editeur = '';
		}
		if(isset($liens['lien_rech_serie'])) {
			$this->lien_rech_serie = $liens['lien_rech_serie'];
		} else {
			$this->lien_rech_serie = '';
		}
		if(isset($liens['lien_rech_collection'])) {
			$this->lien_rech_collection = $liens['lien_rech_collection'];
		} else {
			$this->lien_rech_collection = '';
		}
		if(isset($liens['lien_rech_subcollection'])) {
			$this->lien_rech_subcollection = $liens['lien_rech_subcollection'];
		} else {
			$this->lien_rech_subcollection = '';
		}
		if(isset($liens['lien_rech_indexint'])) {
			$this->lien_rech_indexint = $liens['lien_rech_indexint'];
		} else {
			$this->lien_rech_indexint = '';
		}
		if(isset($liens['lien_rech_motcle'])) {
			$this->lien_rech_motcle = $liens['lien_rech_motcle'];
		} else {
			$this->lien_rech_motcle = '';
		}
		if(isset($liens['lien_rech_categ'])) {
			$this->lien_rech_categ = $liens['lien_rech_categ'];
		} else {
			$this->lien_rech_categ = '';
		}
		if(isset($liens['lien_rech_perio'])) {
			$this->lien_rech_perio = $liens['lien_rech_perio'];
		} else {
			$this->lien_rech_perio = '';
		}
		if(isset($liens['lien_rech_bulletin'])) {
			$this->lien_rech_bulletin = $liens['lien_rech_bulletin'];
		} else {
			$this->lien_rech_bulletin = '';
		}
	}

	public function get_print_css_style() {
		$css_style = "
			<style type='text/css'>
				td.bg-grey {
					width:100px;
				}
				td.public_line_value {
					width:500px;
				}
				.vignetteimg {
				    max-width: 140px;
				    max-height: 200px;
				    -moz-box-shadow: 1px 1px 5px #666666;
				    -webkit-box-shadow: 1px 1px 5px #666666;
				    box-shadow: 1px 1px 5px #666666;
				}
				td.align_right {
					text-align: right;
				}
			</style>
				";
		return $css_style;
	}

	/**
	 * Génération d'un ISBD dans un format simplifié
	 */
	public function do_isbd_simple() {

	    $this->notice_isbd_simple = $this->notice->tit1;

	    if (!empty($this->responsabilites['auteurs'][0]['auteur_titre'])) {
	        $this->notice_isbd_simple .= " / " . $this->responsabilites['auteurs'][0]['auteur_titre'];
	    }
	    if (!empty($this->notice->year)) {
	        $this->notice_isbd_simple .= " (".$this->notice->year.")";
	    }
	}
}