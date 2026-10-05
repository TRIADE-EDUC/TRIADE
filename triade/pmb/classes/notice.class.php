<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice.class.php,v 1.356.2.1.2.3 2025/04/25 09:37:43 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;

use Pmb\Ark\Models\ArkModel;
use Pmb\Ark\Entities\ArkEntityPmb;
use Pmb\Common\Library\CSRF\CollectionCSRF;

require_once($class_path.'/event/events/event_record.class.php');

// classe de gestion des notices
// if ( ! defined( 'NOTICE_CLASS' ) ) {
//   define( 'NOTICE_CLASS', 1 );

	require_once("$class_path/author.class.php");
	require_once("$class_path/marc_table.class.php");
	require_once("$class_path/category.class.php");
	require_once("$class_path/serie.class.php");
	require_once("$class_path/indexint.class.php");
//	require_once("$class_path/tu_notice.class.php");
	require_once($class_path."/parametres_perso.class.php");
	require_once($class_path."/audit.class.php");
	require_once($class_path."/avis_records.class.php");
	include_once($include_path."/notice_authors.inc.php");
	include_once($include_path."/notice_categories.inc.php");
	require_once($class_path."/thesaurus.class.php");
	require_once($class_path."/noeuds.class.php");
	require_once($include_path."/parser.inc.php");
	require_once($include_path."/rss_func.inc.php");
	require_once("$class_path/acces.class.php");
	require_once($class_path."/marc_table.class.php");
	require_once($include_path."/misc.inc.php");

	require_once($class_path."/double_metaphone.class.php");
	require_once($class_path."/stemming.class.php");
	require_once($class_path."/aut_pperso.class.php");
	require_once($class_path."/synchro_rdf.class.php");
	require_once($class_path."/index_concept.class.php");
	require_once($class_path."/authperso_notice.class.php");
	require_once($class_path."/map/map_edition_controler.class.php");
	require_once($class_path."/map_info.class.php");
	require_once($class_path."/nomenclature/nomenclature_record_ui.class.php");
	require_once($class_path."/nomenclature/nomenclature_record_formations.class.php");

	require_once($class_path."/tu_notice.class.php");
	require_once($class_path."/titre_uniforme.class.php");
	require_once($class_path.'/vedette/vedette_composee.class.php');
	require_once($class_path.'/vedette/vedette_link.class.php');

	require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
	require_once($class_path.'/elements_list/elements_authorities_list_ui.class.php');
	require_once($class_path.'/elements_list/elements_docnums_list_ui.class.php');
	require_once($class_path.'/elements_list/elements_graph_ui.class.php');
	require_once($class_path.'/form_mapper/form_mapper.class.php');
	require_once($class_path.'/scan_request/scan_requests.class.php');

	require_once($class_path.'/sphinx/sphinx_records_indexer.class.php');
	require_once($class_path."/notice_relations.class.php");
	require_once($class_path."/notice_relations_collection.class.php");
	require_once($class_path."/thumbnail.class.php");
	require_once($base_path.'/admin/convert/export.class.php');
	require_once($class_path.'/indexation_record.class.php');
	require_once($class_path."/pnb/dilicom.class.php");
	require_once($include_path."/templates/catal_form.tpl.php");
	require_once($class_path."/mono_display.class.php");
	require_once($class_path."/indexation_stack.class.php");
	require_once($class_path."/interface/entity/interface_entity_record_form.class.php");
	require_once($class_path."/selectors/selector_model.class.php");

	class notice {

		// proprietes
		public $libelle_form = '';
		public $id = 0;
		public $duplicate_from_id = 0;
		public $tit1 = '';			// titre propre
		public $tit2 = '';			// titre propre 2
		public $tit3 = '';			// titre parallele
		public $tit4 = '';			// complement du titre
		public $tparent_id = 0;		// id du titre parent
		public $tparent = '';		// libelle du titre parent
		public $tnvol = '';		// numero de partie
		public $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  // les auteurs
		public $ed1_id = '';		// id editeur 1
		public $ed1 ='';			// libelle editeur 1
		public $coll_id = '';		// id collection
		public $coll = '';			// libelle collection
		public $subcoll_id = '';		// id sous collection
		public $subcoll = '';		// libelle sous collection
		public $year = '';			// annee de publication
		public $nocoll = '';		// no. dans la collection
		public $mention_edition = '';	// mention d'edition (1ere, deuxieme...)
		public $ed2_id = '';		// id editeur 2
		public $ed2 ='';			// libelle editeur 2
		public $code = '';			// ISBN, code barre commercial ou no. commercial
		public $npages = '';		// importance materielle (nombre de pages, d'elements...)
		public $ill = '';			// mention d'illustration
		public $size = '';			// format
		public $prix = '';			// prix du document
		public $accomp = '';		// materiel d'accompagnement
		public $n_gen = '';		// note generale
		public $n_contenu = '';		// note de contenu
		public $n_resume = '';		// resume/extrait
		public $categories =	array();// les categories
		public $indexint = 0;		// indexation interne
		public $indexint_lib    = '';        // libelle indexation interne
		public $index_l = '';		// indexation libre
		public $langues = array();
		public $languesorg = array();
		public $lien = '';			// URL de la ressource electronique associee
		public $eformat = '';		// format de la ressource electronique associee
		public $ok = 1;
		public $type_doc = '';
		public $biblio_level = 'm';	// niveau bibliographique
		public $hierar_level = '0';	// niveau hierarchique
		public $action = './catalog.php?categ=update&id=';
		public $link_annul = './catalog.php';
		public $statut = 0 ; // statut
		public $commentaire_gestion = '' ; // commentaire de gestion
		public $thumbnail_url = '' ;
		public $notice_link=array();
		public $date_parution;
		public $is_new=0; // nouveauté
		public $date_is_new="0000-00-00 00:00:00"; // date nouveauté
		public $create_date="0000-00-00 00:00:00"; // date création
		public $update_date="0000-00-00 00:00:00"; // date modification
		public $num_notice_usage = 0; // droit d'usage
		public $concepts_ids;
		public $indexation_lang;
		public $signature;
		public $opac_visible_bulletinage = 0;
		public $opac_serialcirc_demande = 0;
		public $titres_uniformes;
		public $target_link_on_error = "./catalog.php";
		public $is_numeric = 0;

		public $id_bibli = 0;
		public $id_sug = 0;
		public $id_demande = 0;
		public $notice_header = '';

		/**
		 * Affichage des éléments contenu dans les onglets
		 * @var elements_list_ui
		 */
		private $records_list_ui = null;
		/**
		 * Onglets à afficher
		 * @var records_tabs
		 */
		private $record_tabs = null;
		/**
		 * Nomenclatures associées
		 * @var nomenclature_record_formations
		 */
		private $nomenclature_record_formations = null;
		// methodes

		private static $sphinx_indexer = null;

		protected static $notice = array();

		public static $indexation_record;
		protected static $deleted_index = false;
		protected static $aut_pperso_instance;
		protected static $parametres_perso=array();

		protected static $vedette_composee_config_filename ='notice_authors';
		protected static $controller;

		protected $detail;

		protected $reset_thumbnail = false;

		public static function get_notice($id, $cb = '') {
			if (!$id || !isset(self::$notice[$id])) {
				$notice = new notice($id, $cb);
				if ($id) {
					self::$notice[$id] = $notice;
					return self::$notice[$id];
				}
				return $notice;
			}
			return self::$notice[$id];
		}

		// constructeur
		public function __construct($id=0, $cb='') {
			global $msg;
			global $include_path, $class_path ;
			global $deflt_notice_is_new;

			$this->id = intval($id);
			if($this->id) {
				$this->fetch_data();
			} else {
		    	// initialisation des valeurs (vides)
				$this->libelle_form = $msg[270];  // libelle du form : creation d'une notice
				$this->code = $cb;
				// initialisation avec les parametres du user :
				global $value_deflt_lang, $value_deflt_relation ;
				if ($value_deflt_lang) {
					$lang = new marc_list('lang');
					$this->langues[] = array(
						'lang_code' => $value_deflt_lang,
						'langue' => $lang->table[$value_deflt_lang]
						) ;
				}
				global $deflt_notice_statut ;
				if ($deflt_notice_statut) $this->statut = $deflt_notice_statut;
					else $this->statut = 1;

				global $xmlta_doctype ;
				$this->type_doc = $xmlta_doctype ;

				global $notice_parent;
				//relation montante ou descendante
				if ($notice_parent) {
					$this->notice_link['down'][0] = new notice_relation();
					$this->notice_link['down'][0]->set_linked_notice($notice_parent);
				}
				$this->is_new = $deflt_notice_is_new;
			}
		}

		public function fetch_data() {
			global $msg;
			global $include_path, $class_path ;

			$this->libelle_form = $msg[278];  // libelle du form : modification d'une notice

			$requete = "SELECT *, date_format(create_date, '".$msg["format_date_heure"]."') as aff_create, date_format(update_date, '".$msg["format_date_heure"]."') as aff_update FROM notices WHERE notice_id='".$this->id."' LIMIT 1 ";
			$result = @pmb_mysql_query($requete);

			if($result) {
				$notice = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);

				$this->type_doc = $notice->typdoc;				// type du document
				$this->tit1		= $notice->tit1;				// titre propre
				$this->tit2		= $notice->tit2;				// titre propre 2
				$this->tit3		= $notice->tit3;				// titre parallele
				$this->tit4		= $notice->tit4;				// complement du titre
				$this->tparent_id	= $notice->tparent_id;				// id du titre parent

				// libelle du titre parent
				if($this->tparent_id) {
					$serie = new serie($this->tparent_id);
					$this->tparent = $serie->get_isbd();
				} else {
					$this->tparent 		= '';
				}

				$this->tnvol		= $notice->tnvol;				// numero de partie

				$this->responsabilites = get_notice_authors($this->id) ;
				$this->subcoll_id 	= $notice->subcoll_id;				// id sous collection
				$this->coll_id 		= $notice->coll_id;				// id collection
				$this->ed1_id		= $notice->ed1_id	;			// id editeur 1

				require_once("$class_path/editor.class.php");

				if($this->subcoll_id) {
					require_once("$class_path/subcollection.class.php");
					require_once("$class_path/collection.class.php");
					$collection = new subcollection($this->subcoll_id);
					$this->subcoll = $collection->get_isbd();
				}

				if($this->coll_id) {
					require_once("$class_path/collection.class.php");
					$collection = new collection($this->coll_id);
					$this->coll = $collection->get_isbd();
				}

				if($this->ed1_id) {
					$editeur = new editeur($this->ed1_id);
					$this->ed1 = $editeur->get_isbd();
				}

				$this->year 		= $notice->year;				// annee de publication
				$this->nocoll		= $notice->nocoll;				// no. dans la collection
				$this->mention_edition		= $notice->mention_edition;	// mention d'edition (1ere, deuxieme...)
				$this->ed2_id		= $notice->ed2_id;				// id editeur 2

				if($this->ed2_id) {		// libelle editeur 2
					$editeur = new editeur($this->ed2_id);
					$this->ed2 = $editeur->get_isbd();
				}

				$this->code		= $notice->code;				// ISBN, code barre commercial ou no. commercial

				$this->npages		= $notice->npages;				// importance materielle (nombre de pages, d'elements...)
				$this->ill		= $notice->ill;					// mention d'illustration
				$this->size		= $notice->size;				// format
				$this->prix		= $notice->prix;				// Prix du document
				$this->accomp		= $notice->accomp;				// materiel d'accompagnement

				$this->n_gen		= $notice->n_gen;				// note generale
				$this->n_contenu	= $notice->n_contenu;				// note de contenu
				$this->n_resume		= $notice->n_resume;				// resume/extrait

				$this->categories = get_notice_categories($this->id) ;

				$this->indexint		= $notice->indexint;				// indexation interne
				if($this->indexint) {
					$indexint = new indexint($this->indexint);
					$this->indexint_lib = $indexint->get_isbd();
				}

				$this->index_l		= $notice->index_l;				// indexation libre

				$this->langues	= get_notice_langues($this->id, 0) ;	// langues de la publication
				$this->languesorg	= get_notice_langues($this->id, 1) ; // langues originales

				$this->lien	= $notice->lien;				// URL de la ressource electronique associee
				$this->eformat	= $notice->eformat;				// format de la ressource electronique associee
				$this->biblio_level = $notice->niveau_biblio;   	    	// niveau bibliographique
				$this->hierar_level = $notice->niveau_hierar;       		// niveau hierarchique
				$this->statut = $notice->statut;
				if ((trim($notice->date_parution)) && ($notice->date_parution!='0000-00-00')){
					$this->date_parution = $notice->date_parution;
				} else {
					$this->date_parution = static::get_date_parution($notice->year);
				}
				$this->indexation_lang = $notice->indexation_lang;

				$this->is_new = $notice->notice_is_new;
				$this->date_is_new = $notice->notice_date_is_new;
				$this->num_notice_usage = $notice->num_notice_usage;

				//La notice est une notice numérique ?
				$this->is_numeric = $notice->is_numeric;

				//liens vers autres notices
				$this->notice_link = notice_relations::get_notice_links($this->id, $this->biblio_level);

				$this->commentaire_gestion = $notice->commentaire_gestion;
				$this->thumbnail_url = $notice->thumbnail_url;

				$this->create_date = $notice->aff_create;
				$this->update_date = $notice->aff_update;

				$this->signature = $notice->signature;

				// Montrer ou pas le bulletinage en opac
				$this->opac_visible_bulletinage = $notice->opac_visible_bulletinage;

				// Autoriser la demande d'abonnement à l'OPAC
				$this->opac_serialcirc_demande = $notice->opac_serialcirc_demande;
			} else {
				require_once("$include_path/user_error.inc.php");
				error_message("", $msg[280], 1, $this->target_link_on_error);
				$this->ok = 0;
			}
		}

		// Donne l'id de la notice par son isbn
		public static function get_notice_id_from_cb($code) {

			if(!$code) return 0;
			$isbn = traite_code_isbn($code);

			if(isISBN10($isbn)) {
				$isbn13 = formatISBN($isbn,13);
				$isbn10 = $isbn;
			} elseif (isISBN13($isbn)) {
				$isbn10 = formatISBN($isbn,10);
				$isbn13 = $isbn;
			} else {
				// ce n'est pas un code au format isbn
				$isbn10=$code;
			}

			$requete = "SELECT notice_id FROM notices WHERE ( code='$isbn10' or code='$isbn13') and code !='' LIMIT 1 ";
			if(($result = pmb_mysql_query($requete))) {
				if (pmb_mysql_num_rows($result)) {
					$notice = pmb_mysql_fetch_object($result);
					return($notice->notice_id);
				}
			}
			return 0;
		}

		//Récupération d'un titre de notice
		public static function get_notice_title($notice_id) {
// 			$requete="select serie_name, tnvol, tit1, code from notices left join series on serie_id=tparent_id where notice_id=".$notice_id;
// 			$resultat=pmb_mysql_query($requete);
// 			if (pmb_mysql_num_rows($resultat)) {
// 				$r=pmb_mysql_fetch_object($resultat);
// 				return ($r->serie_name?$r->serie_name." ":"").($r->tnvol?$r->tnvol." ":"").$r->tit1.($r->code?" (".$r->code.")":"");
// 			}
// 			return '';
			$mono_display = new mono_display($notice_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
			return strip_tags($mono_display->header_texte);
		}

		public static function init_globals_patterns_links() {
			global $link, $link_expl, $link_explnum;
			global $link_serial, $link_analysis, $link_bulletin;
			global $link_explnum_serial, $link_explnum_analysis, $link_explnum_bulletin;

			$link = static::get_pattern_link();
			$link_expl = exemplaire::get_pattern_link();
			$link_explnum = explnum::get_pattern_link();

			$link_serial = serial::get_pattern_link();
			$link_analysis = analysis::get_pattern_link();
			$link_bulletin = bulletinage::get_pattern_link();

			$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
			$link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
			$link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";

		}

		public static function get_pattern_link() {
			global $base_path;
			return $base_path.'/catalog.php?categ=isbd&id=!!id!!';
		}

		public static function get_permalink($notice_id, $parent_id=0) {
			global $base_path;
			return $base_path.'/catalog.php?categ=isbd&id='.$notice_id;
		}

		public static function get_notice_view_link($notice_id) {

			$requete="select niveau_biblio, serie_name, tnvol, tit1, code from notices left join series on serie_id=tparent_id where notice_id=".$notice_id;
			$fetch = pmb_mysql_query($requete);
			$notice_id = intval($notice_id);
			if (pmb_mysql_num_rows($fetch)) {
				$header_perio='';
				$r = pmb_mysql_fetch_object($fetch);
				if($r->niveau_biblio == 's'){
					// périodique
					$link = './catalog.php?categ=serials&sub=view&serial_id='.$notice_id;
				}elseif($r->niveau_biblio == 'b') {
					// notice de bulletin
					$query = 'select bulletin_id, bulletin_notice from bulletins where num_notice = '.$notice_id;
					$result = pmb_mysql_query($query);
					if($result && pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$link = './catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=' . intval($row->bulletin_id);
						$requete_perio="select tit1, code from notices where notice_id=" . intval($row->bulletin_notice);
						$fetch_perio = pmb_mysql_query($requete_perio);
						if (pmb_mysql_num_rows($fetch_perio)) {
							$r_perio = pmb_mysql_fetch_object($fetch_perio);
							$header_perio= $r_perio->tit1.($r_perio->code?" (".$r_perio->code.") ":" ");
						}
					}
				}else{
					// notice de monographie
					$link = self::get_permalink($notice_id);
				}
				$header= ($r->serie_name?$r->serie_name." ":"").($r->tnvol?$r->tnvol." ":"").$r->tit1.($r->code?" (".$r->code.")":"");
				return "<a href='".$link."' class='lien_gestion'>".$header_perio.$header."</a>";
			}
			return '';
		}

		//Récupérer une date au format AAAA-MM-JJ
		public static function get_date_parution($annee) {
		    $date_parution = detectFormatDate($annee);
		    return (!empty($date_parution) ? $date_parution : "0000-00-00");
		}

		public static function get_niveau_biblio($notice_id) {
		    $query = "SELECT niveau_biblio FROM notices WHERE notice_id = ".$notice_id;
		    $result = pmb_mysql_query($query);
		    return pmb_mysql_result($result, 0, 'niveau_biblio');
		}

		public static function get_typdoc($notice_id) {
		    $query = "SELECT typdoc FROM notices WHERE notice_id = ".$notice_id;
		    $result = pmb_mysql_query($query);
		    return pmb_mysql_result($result, 0, 'typdoc');
		}

		public static function get_field($id, $field) {
		    $id = intval($id);
		    $param = '';
		    if($id) {
		        $query = "SELECT ".addslashes($field)." FROM notices WHERE notice_id = '".$id."' ";
		        $result = pmb_mysql_query($query);
		        $param = pmb_mysql_result($result, 0, 0);
		    }
		    return $param;
		}

		protected function get_tab_responsabilities_form() {
			global $charset;
			global $value_deflt_fonction;
			global $pmb_authors_qualification;
			global $notice_tab_responsabilities_form_tpl;
			global $notice_responsabilities_others_form_tpl;
			global $notice_responsabilities_secondary_form_tpl;

			$tab_responsabilities_form = $notice_tab_responsabilities_form_tpl;
			$fonction = new marc_list('function');

			$as = array_search ("0", $this->responsabilites["responsabilites"]);
			if ($as !== false && $as !== null) {
				$auteur_0 = $this->responsabilites["auteurs"][$as];
			} else {
				$auteur_0 = array(
						'id' => 0,
						'fonction' => (!empty($value_deflt_fonction) ? $value_deflt_fonction : ''),
						'responsability' => '',
						'id_responsability' => 0
				);
			}
			$authority_isbd = "";
			if ($auteur_0["id"] != 0) {
    			$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_0["id"], 'type_object' => AUT_TABLE_AUTHORS]);
    			$authority_isbd = $authority_instance->get_isbd();
			}

			if (!empty($pmb_authors_qualification)) {
				$vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($auteur_0["id_responsability"], TYPE_NOTICE_RESPONSABILITY_PRINCIPAL), static::$vedette_composee_config_filename));
				$tab_responsabilities_form = str_replace('!!vedette_author!!', $vedette_ui->get_form('role', 0, 'notice'), $tab_responsabilities_form);
			} else {
				$tab_responsabilities_form = str_replace('!!vedette_author!!', "", $tab_responsabilities_form);
			}

			$tab_responsabilities_form = str_replace('!!iaut!!', 0, $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!aut0_id!!',	$auteur_0["id"], $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!aut0!!', htmlentities($authority_isbd, ENT_QUOTES, $charset), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!f0_code!!', $auteur_0["fonction"], $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!f0!!', ($auteur_0["fonction"] ? $fonction->table[$auteur_0["fonction"]] : ''), $tab_responsabilities_form);

			$autres_auteurs = '';
			$as = array_keys($this->responsabilites["responsabilites"], "1");
			$max_aut1 = count($as);
			if (empty($max_aut1)) {
			    $max_aut1 = 1;
			}
			for ($i = 0; $i < $max_aut1; $i++) {
				if (isset($as[$i]) && $as[$i]!== false && $as[$i]!== null) {
					$indice = $as[$i];
					$auteur_1 = $this->responsabilites["auteurs"][$indice];
				} else {
					$auteur_1 = array(
							'id' => 0,
							'fonction' => (!empty($value_deflt_fonction) ? $value_deflt_fonction : ''),
							'responsability' => '',
							'id_responsability' => 0
					);
				}
				$authority_isbd = "";
				if ($auteur_1["id"] != 0) {
    				$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_1["id"], 'type_object' => AUT_TABLE_AUTHORS]);
    				$authority_isbd = trim($authority_instance->get_isbd());
				}

				$ptab_aut_autres = $notice_responsabilities_others_form_tpl;
				if ($i == 0) {
					$ptab_aut_autres = str_replace('!!bouton_add_display!!', '', $ptab_aut_autres);
				} else {
					$ptab_aut_autres = str_replace('!!bouton_add_display!!', 'display:none', $ptab_aut_autres);
				}
				$button_add = '';

				if ($i == ($max_aut1 -1)) {
					$button_add = "<input type='button' id='button_add_f_aut1' class='bouton' value='+' onClick=\"add_aut(1);\"/>";
				}

				$ptab_aut_autres = str_replace('!!button_add_aut1!!', $button_add, $ptab_aut_autres);

				if (!empty($pmb_authors_qualification)) {
					$vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($auteur_1["id_responsability"], TYPE_NOTICE_RESPONSABILITY_AUTRE), static::$vedette_composee_config_filename));
					$ptab_aut_autres = str_replace('!!vedette_author!!', $vedette_ui->get_form('role_autre', $i, 'notice', '', 0), $ptab_aut_autres);
				} else {
					$ptab_aut_autres = str_replace('!!vedette_author!!', "", $ptab_aut_autres);
				}
				$ptab_aut_autres = str_replace('!!iaut!!', $i, $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut1_id!!', $auteur_1["id"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut1!!', htmlentities($authority_isbd, ENT_QUOTES, $charset), $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f1_code!!', $auteur_1["fonction"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f1!!', ($auteur_1["fonction"] ? $fonction->table[$auteur_1["fonction"]] : ''), $ptab_aut_autres);
				$autres_auteurs .= $ptab_aut_autres;
			}
			$tab_responsabilities_form = str_replace('!!max_aut1!!', $max_aut1, $tab_responsabilities_form);

			$auteurs_secondaires = '';
			$as = array_keys($this->responsabilites["responsabilites"], "2");
			$max_aut2 = count($as);
			if (empty($max_aut2)) {
			    $max_aut2 = 1;
			}
			for ($i = 0; $i < $max_aut2; $i++) {
				if (isset($as[$i]) && $as[$i] !== false && $as[$i] !== null) {
					$indice = $as[$i];
					$auteur_2 = $this->responsabilites["auteurs"][$indice];
				} else {
					$auteur_2 = array(
							'id' => 0,
							'fonction' => (!empty($value_deflt_fonction) ? $value_deflt_fonction : ''),
							'responsability' => '',
							'id_responsability' => 0
					);
				}
				$authority_isbd = "";
				if ($auteur_2["id"] != 0) {
    				$authority_instance = authorities_collection::get_authority(AUT_TABLE_AUTHORITY, 0, [ 'num_object' => $auteur_2["id"], 'type_object' => AUT_TABLE_AUTHORS]);
    				$authority_isbd = $authority_instance->get_isbd();
				}

				$ptab_aut_autres = $notice_responsabilities_secondary_form_tpl;
 				if ($i == 0) {
 					$ptab_aut_autres = str_replace('!!bouton_add_display!!', '', $ptab_aut_autres);
 				} else {
 					$ptab_aut_autres = str_replace('!!bouton_add_display!!', 'display:none', $ptab_aut_autres);
 				}
 				$button_add = '';
				if ($i == ($max_aut2 - 1)) {
					$button_add = "<input type='button' id='button_add_f_aut2' style='!!bouton_add_display!!' class='bouton' value='+' onClick=\"add_aut(2);\"/>";
				}
				$ptab_aut_autres = str_replace('!!button_add_aut2!!', $button_add, $ptab_aut_autres);

				if (!empty($pmb_authors_qualification)) {
					$vedette_ui = new vedette_ui(new vedette_composee(vedette_composee::get_vedette_id_from_object($auteur_2["id_responsability"], TYPE_NOTICE_RESPONSABILITY_SECONDAIRE), static::$vedette_composee_config_filename));
					$ptab_aut_autres = str_replace('!!vedette_author!!', $vedette_ui->get_form('role_secondaire', $i, 'notice', '', 0), $ptab_aut_autres);
				} else {
					$ptab_aut_autres = str_replace('!!vedette_author!!', "", $ptab_aut_autres);
				}
				$ptab_aut_autres = str_replace('!!iaut!!', $i, $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut2_id!!', $auteur_2["id"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!aut2!!', htmlentities($authority_isbd, ENT_QUOTES, $charset), $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f2_code!!', $auteur_2["fonction"], $ptab_aut_autres);
				$ptab_aut_autres = str_replace('!!f2!!', ($auteur_2["fonction"] ? $fonction->table[$auteur_2["fonction"]] : ''), $ptab_aut_autres);
				$auteurs_secondaires .= $ptab_aut_autres;
			}
			$tab_responsabilities_form = str_replace('!!max_aut2!!', $max_aut2, $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!autres_auteurs!!', $autres_auteurs, $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!auteurs_secondaires!!', $auteurs_secondaires, $tab_responsabilities_form);

			$tab_responsabilities_form = str_replace('!!force_dialog_author!!', $this->is_force_dialog('author'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_dialog_category!!', $this->is_force_dialog('category'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_dialog_func!!', $this->is_force_dialog('func'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_dialog_lang!!', $this->is_force_dialog('lang'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_popup_author!!', $this->is_force_popup('author'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_popup_category!!', $this->is_force_popup('category'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_popup_func!!', $this->is_force_popup('func'), $tab_responsabilities_form);
			$tab_responsabilities_form = str_replace('!!force_popup_lang!!', $this->is_force_popup('lang'), $tab_responsabilities_form);
			return $tab_responsabilities_form;
		}

		protected function get_tab_uniform_title_form() {
			global $charset;
			global $notice_tab_uniform_title_form_tpl;

			$tab_uniform_title_form = $notice_tab_uniform_title_form_tpl;
			if($this->duplicate_from_id) $tu=new tu_notice($this->duplicate_from_id);
			else $tu=new tu_notice($this->id);
			$tab_uniform_title_form = str_replace("!!titres_uniformes!!", $tu->get_form("notice"), $tab_uniform_title_form);
			return $tab_uniform_title_form;
		}

		protected function get_tab_isbn_form() {
			global $notice_tab_isbn_form_tpl;

			$tab_isbn_form_tpl = str_replace('!!cb!!', $this->code ?? "", $notice_tab_isbn_form_tpl);
			$tab_isbn_form_tpl = str_replace('!!notice_id!!', $this->id ?? "", $tab_isbn_form_tpl);
			return $tab_isbn_form_tpl;
		}

		protected function get_tab_notes_form() {
			global $charset;
			global $notice_tab_notes_form_tpl;

			$tab_notes_form = $notice_tab_notes_form_tpl;

			// Note generale
			$element_n_gen = $this->get_textarea_content_form('el5Child_0', 'n_gen', '265');
			$tab_notes_form = str_replace('!!element_n_gen!!', $element_n_gen, $tab_notes_form);

			//Note de contenu
			$element_n_contenu = $this->get_textarea_content_form('el5Child_1', 'n_contenu', '266');
			$tab_notes_form = str_replace('!!element_n_contenu!!', $element_n_contenu, $tab_notes_form);

			//Résumé/Extrait
			$element_n_resume = $this->get_textarea_content_form('el5Child_2', 'n_resume', '267');
			$tab_notes_form = str_replace('!!element_n_resume!!', $element_n_resume, $tab_notes_form);

			return $tab_notes_form;
		}

		protected function get_tab_lang_form() {
			global $charset;
			global $notice_tab_lang_form_tpl;
			global $notice_lang_first_form_tpl;
			global $notice_lang_next_form_tpl;
			global $notice_langorg_first_form_tpl;
			global $notice_langorg_next_form_tpl;

			$tab_lang_form = $notice_tab_lang_form_tpl;
			// langues repetables
			$lang_repetables = '';
			if (empty($this->langues)) {
			    $max_lang = 1;
			} else {
			    $max_lang = count($this->langues);
			}
			for ($i = 0; $i < $max_lang; $i++) {
			    if ($i == 0) {
			        $ptab_lang = str_replace('!!ilang!!', $i, $notice_lang_first_form_tpl);
			    } else {
			        $ptab_lang = str_replace('!!ilang!!', $i, $notice_lang_next_form_tpl);
			    }
			    if ($i == $max_lang - 1) {
			        $ptab_lang = str_replace('!!button_add_lang!!', "<input id='button_add_f_lang_code' type='button' class='bouton' value='+' onClick=\"add_lang();\"/>", $ptab_lang);
			    } else {
			        $ptab_lang = str_replace('!!button_add_lang!!', '', $ptab_lang);
			    }
				if (empty($this->langues)) {
					$ptab_lang = str_replace('!!lang_code!!', '', $ptab_lang);
					$ptab_lang = str_replace('!!lang!!', '', $ptab_lang);
				} else {
					$ptab_lang = str_replace('!!lang_code!!', $this->langues[$i]["lang_code"], $ptab_lang);
					$ptab_lang = str_replace('!!lang!!', htmlentities($this->langues[$i]["langue"], ENT_QUOTES, $charset), $ptab_lang);
				}
				$lang_repetables .= $ptab_lang;
			}
			$tab_lang_form = str_replace('!!max_lang!!', $max_lang, $tab_lang_form);
			$tab_lang_form = str_replace('!!langues_repetables!!', $lang_repetables, $tab_lang_form);

			// langues originales repetables
			$langorg_repetables = '';
			if (empty($this->languesorg)) {
			    $max_langorg = 1;
			} else {
			    $max_langorg = count($this->languesorg);
			}
			for ($i = 0; $i < $max_langorg; $i++) {
			    if ($i == 0) {
			        $ptab_lang = str_replace('!!ilangorg!!', $i, $notice_langorg_first_form_tpl);
			    } else {
			        $ptab_lang = str_replace('!!ilangorg!!', $i, $notice_langorg_next_form_tpl);
			    }
				if (empty($this->languesorg)) {
					$ptab_lang = str_replace('!!langorg_code!!', '', $ptab_lang);
					$ptab_lang = str_replace('!!langorg!!', '', $ptab_lang);
				} else {
					$ptab_lang = str_replace('!!langorg_code!!', $this->languesorg[$i]["lang_code"], $ptab_lang);
					$ptab_lang = str_replace('!!langorg!!', htmlentities($this->languesorg[$i]["langue"], ENT_QUOTES, $charset), $ptab_lang);
				}
				$langorg_repetables .= $ptab_lang ;
			}
			$tab_lang_form = str_replace('!!max_langorg!!', $max_langorg, $tab_lang_form);
			$tab_lang_form = str_replace('!!languesorg_repetables!!', $langorg_repetables, $tab_lang_form);

			$tab_lang_form = str_replace('!!force_dialog_lang!!', $this->is_force_dialog('lang'), $tab_lang_form);
			$tab_lang_form = str_replace('!!force_popup_lang!!', $this->is_force_popup('lang'), $tab_lang_form);
			return $tab_lang_form;
		}

		protected function get_tab_indexation_form() {
		    global $charset, $msg;
			global $notice_tab_indexation_form_tpl, $notice_indexation_first_form_tpl, $notice_indexation_next_form_tpl;
			global $thesaurus_concepts_active;
			global $thesaurus_categories_affichage_ordre;
			global $thesaurus_mode_pmb, $thesaurus_classement_mode_pmb, $pmb_keyword_sep;

			$tab_indexation_form = $notice_tab_indexation_form_tpl;

			// categories
			$categ_repetables = '';
			//tri ?
			if ($thesaurus_categories_affichage_ordre == 0 && !empty($this->categories)) {
				$tmp = array();
				foreach ($this->categories as $key => $value) {
					$tmp[$key] = strip_tags($value['categ_libelle']);
				}
				$tmp = array_map("convert_diacrit", $tmp);//On enlève les accents
				$tmp = array_map("strtoupper", $tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ($tmp as $key => $value) {
					$tmp[$key] = $this->categories[$key];//On reprend les bons couples
				}
				$this->categories = array_values($tmp);
			}
			if (empty($this->categories)) {
			    $max_categ = 1;
			} else {
			    $max_categ = count($this->categories);
			}
			$tab_categ_order = "";
			for ($i = 0; $i < $max_categ; $i++) {
				if (!empty($this->categories[$i]["categ_id"])) {
					$categ_id = $this->categories[$i]["categ_id"];
				} else {
					$categ_id = 0;
				}
				$categ = new category($categ_id);

				if ($i == 0) {
				    $ptab_categ = str_replace('!!icateg!!', $i, $notice_indexation_first_form_tpl);
				} else {
				    $ptab_categ = str_replace('!!icateg!!', $i, $notice_indexation_next_form_tpl);
				}
				if ($i == $max_categ - 1) {
				    $ptab_categ = str_replace("!!add_categ_btn!!", "<input id='add_categ_btn' type='button' class='bouton' value='+' onClick=\"add_categ();\"/>", $ptab_categ);
				} else {
				    $ptab_categ = str_replace('!!add_categ_btn!!', '', $ptab_categ);
				}
				$ptab_categ = str_replace('!!categ_id!!', $categ_id, $ptab_categ);

				if (empty($this->categories)) {
					$ptab_categ = str_replace('!!categ_libelle!!', '', $ptab_categ);
				} else {
				    $nom_thesaurus = '';
				    if (!empty($thesaurus_mode_pmb)) {
				        $nom_thesaurus = '['.$categ->thes->getLibelle().'] ';
				    }
			        $ptab_categ = str_replace('!!categ_libelle!!',	htmlentities($nom_thesaurus.$categ->catalog_form, ENT_QUOTES, $charset), $ptab_categ);
			        if ($tab_categ_order != "") {
			            $tab_categ_order .= ",";
			        }
					$tab_categ_order .= $i;
				}
				$categ_repetables .= $ptab_categ ;
			}
			$tab_indexation_form = str_replace('!!max_categ!!', $max_categ, $tab_indexation_form);
			$tab_indexation_form = str_replace('!!categories_repetables!!', $categ_repetables, $tab_indexation_form);
			$tab_indexation_form = str_replace('!!tab_categ_order!!', $tab_categ_order, $tab_indexation_form);

			// indexation interne
			$tab_indexation_form = str_replace('!!indexint_id!!', $this->indexint, $tab_indexation_form);
			if (!empty($this->indexint)) {
				$indexint = new indexint($this->indexint);
				$tab_indexation_form = str_replace('!!indexint!!', htmlentities($indexint->get_isbd(), ENT_QUOTES, $charset), $tab_indexation_form);
				$tab_indexation_form = str_replace('!!num_pclass!!', $indexint->id_pclass, $tab_indexation_form);
			} else {
				$tab_indexation_form = str_replace('!!indexint!!', '', $tab_indexation_form);
				$tab_indexation_form = str_replace('!!num_pclass!!', '', $tab_indexation_form);
			}

			// indexation libre
			$tab_indexation_form = str_replace('!!f_indexation!!', htmlentities($this->index_l, ENT_QUOTES, $charset), $tab_indexation_form);
			$sep = "'$pmb_keyword_sep'";
			if (empty($pmb_keyword_sep)) {
			    $sep = "' '";
			}
			if (ord($pmb_keyword_sep) == 0xa || ord($pmb_keyword_sep) == 0xd) {
			    $sep = $msg['catalogue_saut_de_ligne'];
			}
			$tab_indexation_form = str_replace("!!sep!!",htmlentities($sep, ENT_QUOTES, $charset), $tab_indexation_form);

			// Indexation concept
			if ($thesaurus_concepts_active == 1) {
				if (!empty($this->duplicate_from_id)) {
					$index_concept = new index_concept($this->duplicate_from_id, TYPE_NOTICE);
				} else {
					$index_concept = new index_concept($this->id, TYPE_NOTICE);
				}
				$tab_indexation_form = str_replace('!!index_concept_form!!', $index_concept->get_form("notice"), $tab_indexation_form);
			} else {
				$tab_indexation_form = str_replace('!!index_concept_form!!', "", $tab_indexation_form);
			}
			$tab_indexation_form = str_replace('!!force_dialog_indexint!!', $this->is_force_dialog('indexint'), $tab_indexation_form);
			$tab_indexation_form = str_replace('!!force_dialog_category!!', $this->is_force_dialog('category'), $tab_indexation_form);
			$tab_indexation_form = str_replace('!!force_popup_indexint!!', $this->is_force_popup('indexint'), $tab_indexation_form);
			$tab_indexation_form = str_replace('!!force_popup_category!!', $this->is_force_popup('category'), $tab_indexation_form);
			return $tab_indexation_form;
		}

		protected function get_tab_links_form() {
			global $charset;
			global $notice_tab_links_form_tpl;
			global $pmb_curl_timeout;

			$tab_links_form = $notice_tab_links_form_tpl;
			$tab_links_form = str_replace('!!lien!!',			htmlentities($this->lien	,ENT_QUOTES, $charset)	, $tab_links_form);

			// Format électronique de la ressource
			$element_eformat = $this->get_input_text_content_form('el8Child_1', 'eformat', '276');
			$tab_links_form = str_replace('!!element_eformat!!',		$element_eformat	, $tab_links_form);


			$tab_links_form = str_replace('!!pmb_curl_timeout!!',		$pmb_curl_timeout	, $tab_links_form);

			$collectionCSRF = new CollectionCSRF();
			$tokens = json_encode($collectionCSRF->getArrayTokens());

			$tab_links_form = str_replace('!!tokens_csrf!!', $tokens, $tab_links_form);

			return thumbnail::get_js_function_chklnk_tpl().$tab_links_form;
		}

		protected function get_tab_customs_perso_form() {
			global $charset;
			global $notice_tab_customs_perso_form_tpl;
			global $pmb_form_empr_editables;

			$tab_customs_perso_form = $notice_tab_customs_perso_form_tpl;
			$p_perso=new parametres_perso("notices");
			if (!$p_perso->no_special_fields) {
				// si on duplique, construire le formulaire avec les donnees de la notice d'origine
				if ($this->duplicate_from_id) $perso_=$p_perso->show_editable_fields($this->duplicate_from_id);
				else $perso_=$p_perso->show_editable_fields($this->id);
				$perso="";
				for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
					$p=$perso_["FIELDS"][$i];
					if($pmb_form_empr_editables == 2) {
						$perso.="<div id='el9Child_".$p["ID"]."' movable='yes' title=\"".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."\">";
					} else {
						$perso.="<div id='move_".$p["NAME"]."' movable='yes' title=\"".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."\">";
					}
					$perso.="
								<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".htmlentities($p["TITRE"],ENT_QUOTES, $charset)."</label></div>
                                <div class='row'>".$p["COMMENT_DISPLAY"]."</div>
								<div class='row'>".$p["AFF"]."</div>
							 </div>";
				}
				$perso.=$perso_["CHECK_SCRIPTS"];
				$tab_customs_perso_form = str_replace("!!champs_perso!!",$perso,$tab_customs_perso_form);
			} else {
				$tab_customs_perso_form = "\n<script>function check_form() { return true; }</script>\n";
			}
			return $tab_customs_perso_form;
		}

		protected function get_selector_indexation_lang() {
			global $xmlta_indexation_lang;
			global $include_path;
			global $charset;

			if(!$this->get_id() && !$this->indexation_lang)$this->indexation_lang=$xmlta_indexation_lang;
			//	if(!$this->indexation_lang) $this->indexation_lang="fr_FR";
			$langues = new XMLlist("$include_path/messages/languages.xml");
			$langues->analyser();
			$clang = $langues->table;

			$combo = "<select name='indexation_lang' id='indexation_lang' class='saisie-20em' >";
			if(!$this->indexation_lang) $combo .= "<option value='' selected>--</option>";
			else $combo .= "<option value='' >--</option>";
			foreach ($clang as $cle => $value) {
				// arabe seulement si on est en utf-8
				if (($charset != 'utf-8' and $this->indexation_lang != 'ar') or ($charset == 'utf-8')) {
					if(strcmp($cle, $this->indexation_lang) != 0) $combo .= "<option value='$cle'>$value ($cle)</option>";
					else $combo .= "<option value='$cle' selected>$value ($cle)</option>";
				}
			}
			$combo .= "</select>";
			return $combo;
		}

		protected function get_tab_gestion_fields() {
			global $msg, $charset;
			global $pmb_notices_show_dates;
			global $notice_tab_gestion_fields_form_tpl;

			$tab_gestion_fields_form = $notice_tab_gestion_fields_form_tpl;

			$element_notice_statut = interface_entity_record_element::get_instance('el10Child_0', 'f_notice_statut', 'notice_statut_gestion');
			$select_statut = gen_liste_multiple ("select id_notice_statut, gestion_libelle from notice_statut order by 2", "id_notice_statut", "gestion_libelle", "id_notice_statut", "form_notice_statut", "", $this->statut, "", "","","",0) ;
			$element_notice_statut->add_html_node($select_statut);
			$tab_gestion_fields_form = str_replace('!!element_notice_statut!!', $element_notice_statut->get_display(), $tab_gestion_fields_form);


			if($this->is_new){
				$tab_gestion_fields_form = str_replace('!!checked_yes!!', "checked", $tab_gestion_fields_form);
				$tab_gestion_fields_form = str_replace('!!checked_no!!', "", $tab_gestion_fields_form);
			}else{
				$tab_gestion_fields_form = str_replace('!!checked_no!!', "checked", $tab_gestion_fields_form);
				$tab_gestion_fields_form = str_replace('!!checked_yes!!', "", $tab_gestion_fields_form);
			}

			// Notice numérique ?
			$element_is_numeric = interface_entity_record_element::get_instance('el10Child_11', 'is_numeric', 'numeric_record');
			$element_is_numeric->add_input_node('radio', '0')->set_label_code('notice_is_new_gestion_no')
			->set_checked(!$this->is_numeric ? true : false);
			$element_is_numeric->add_input_node('radio', '1')->set_label_code('notice_is_new_gestion_yes')
			->set_checked($this->is_numeric ? true : false);
			$tab_gestion_fields_form = str_replace('!!element_is_numeric!!', $element_is_numeric->get_display(), $tab_gestion_fields_form);

			// commentaire de gestion
			$element_commentaire_gestion = $this->get_textarea_content_form('el10Child_1', 'commentaire_gestion', 'notice_commentaire_gestion');
			$tab_gestion_fields_form = str_replace('!!element_commentaire_gestion!!', $element_commentaire_gestion, $tab_gestion_fields_form);

			$tab_gestion_fields_form = str_replace('!!thumbnail_url!!',htmlentities($this->thumbnail_url,ENT_QUOTES, $charset), $tab_gestion_fields_form);

			$tab_gestion_fields_form = str_replace('!!message_folder!!',thumbnail::get_message_folder(), $tab_gestion_fields_form);

		    $element_notice_usage = interface_entity_record_element::get_instance('el10Child_10', 'f_notice_usage', 'notice_usage_libelle');
			$select_num_notice_usage = gen_liste_multiple ("select id_usage, usage_libelle from notice_usage order by 2", "id_usage", "usage_libelle", "id_usage", "form_num_notice_usage", "", $this->num_notice_usage, "", "", 0, $msg['notice_usage_none'],0) ;
			$element_notice_usage->add_html_node($select_num_notice_usage);
			$tab_gestion_fields_form = str_replace('!!element_notice_usage!!', $element_notice_usage->get_display(), $tab_gestion_fields_form);

			if ($this->id && $pmb_notices_show_dates) {
				$dates_notices = "<br>
					<label for='notice_date_crea' class='etiquette'>".$msg["noti_crea_date"]."</label>&nbsp;".$this->create_date."
			    	<br>
			    	 <label for='notice_date_mod' class='etiquette'>".$msg["noti_mod_date"]."</label>&nbsp;".$this->update_date;
				$tab_gestion_fields_form = str_replace('!!dates_notice!!',$dates_notices, $tab_gestion_fields_form);
			} else {
				$tab_gestion_fields_form = str_replace('!!dates_notice!!',"", $tab_gestion_fields_form);
			}
			$img_loaded_src = "";
			if (empty($this->thumbnail_url) && $this->id) {
			    $img_loaded_src = thumbnail::get_thumbnail_url($this->id, 'record');
			}
			$tab_gestion_fields_form = str_replace('!!f_img_loaded_src!!',$img_loaded_src, $tab_gestion_fields_form);

			//affichage des formulaires des droits d'acces
			$tab_gestion_fields_form = str_replace('<!-- rights_form -->', $this->get_rights_form(), $tab_gestion_fields_form);

			// langue de la notice
			$element_indexation_lang = interface_entity_record_element::get_instance('el10Child_4', 'f_notice_lang', 'indexation_lang_select');
			$element_indexation_lang->add_html_node($this->get_selector_indexation_lang());
			$tab_gestion_fields_form = str_replace('!!element_indexation_lang!!', $element_indexation_lang->get_display(), $tab_gestion_fields_form);

			return thumbnail::get_js_function_chklnk_tpl().$tab_gestion_fields_form;
		}

		protected function get_tab_map_form() {
			global $notice_tab_map_form_tpl;

			if($this->duplicate_from_id) $map_edition=new map_edition_controler(TYPE_RECORD,$this->duplicate_from_id);
			else $map_edition=new map_edition_controler(TYPE_RECORD,$this->id);
			$map_form=$map_edition->get_form();
			if($this->duplicate_from_id) $map_info=new map_info($this->duplicate_from_id);
			else $map_info=new map_info($this->id);
			$map_form_info=$map_info->get_form();
			$map_notice_form = $notice_tab_map_form_tpl;
			$element = interface_entity_record_element::get_instance('el14Child_0', 'f_map', 'notice_map');
			$element->add_html_node($map_form.$map_form_info);
			$map_notice_form = str_replace('!!element_notice_map!!', $element->get_display(), $map_notice_form);
			return $map_notice_form;
		}

		protected function get_input_text_content_form($uid, $name, $label_code) {
		    $element = interface_entity_record_element::get_instance($uid, "f_".$name, $label_code);
		    $element->add_input_node('text', $this->{$name}, ['data-form-name' => "f_".$name]);
		    return $element->get_display();
		}

		protected function get_textarea_content_form($uid, $name, $label_code) {
		    $element = interface_entity_record_element::get_instance($uid, "f_".$name, $label_code);
		    $element->add_textarea_node($this->{$name}, 0, 5)
		    ->set_class('saisie-80em')
		    ->set_attributes(['wrap' => 'virtual', 'data-form-name' => "f_".$name]);
		    return $element->get_display();
		}

		protected function get_authority_content_form($uid, $name, $label_code, $what, $completion, $hidden = []) {
		    if(empty($hidden['name'])) {
		        $hidden['name'] = "f_".$name."_id";
		    }
		    if(empty($hidden['value'])) {
		        $property = $name."_id";
    		    $hidden['value'] = $this->{$property};
		    }
		    $element = interface_entity_record_element::get_instance($uid, "f_".$name, $label_code);
		    $element->add_authority_node($this->{$name}, $completion)
		    ->set_class('saisie-30emr')
		    ->set_hidden_name($hidden['name'])
		    ->set_hidden_value($hidden['value'])
		    ->set_openPopUpUrl("./select.php?what=".$what."&caller=notice&param1=".$hidden['name']."&param2=f_".$name."&deb_rech=".pmb_escape()."(this.form.f_".$name.".value)")
            ->set_forceSelectorDialog($this->is_force_dialog($what))
            ->set_forceSelectorPopUp($this->is_force_popup($what));

		    return $element->get_display();
		}

		protected function get_content_form() {
			global $charset;
			global $lang;
			global $include_path;
			global $xmlta_indexation_lang;
			global $pmb_map_activate, $pmb_nomenclature_activate;

			include($include_path."/templates/catal_form.tpl.php");
			$fonction = new marc_list('function');

			$content_form = $notice_content_form;

			// mise a jour de l'onglet 0
			$ptab[0] = str_replace('!!tit1!!',				htmlentities($this->tit1,ENT_QUOTES, $charset)			, $ptab[0]);

			// Titre propre d'un auteur différent
			$element_tit2 = $this->get_input_text_content_form('el0Child_1', 'tit2', '238');
			$ptab[0] = str_replace('!!element_tit2!!', $element_tit2, $ptab[0]);

			// Titre parallèle
			$element_tit3 = $this->get_input_text_content_form('el0Child_2', 'tit3', '239');
			$ptab[0] = str_replace('!!element_tit3!!', $element_tit3, $ptab[0]);

			// Complément du titre
			$element_tit4 = $this->get_input_text_content_form('el0Child_3', 'tit4', '240');
			$ptab[0] = str_replace('!!element_tit4!!', $element_tit4, $ptab[0]);

			$ptab[0] = str_replace('!!tparent_id!!',		$this->tparent_id										, $ptab[0]);
			$ptab[0] = str_replace('!!tparent!!',			htmlentities($this->tparent,ENT_QUOTES, $charset)		, $ptab[0]);
			$ptab[0] = str_replace('!!tnvol!!',				htmlentities($this->tnvol,ENT_QUOTES, $charset)			, $ptab[0]);
			$ptab[0] = str_replace('!!force_dialog_serie!!',$this->is_force_dialog('serie')				, $ptab[0]);
			$ptab[0] = str_replace('!!force_popup_serie!!',$this->is_force_popup('serie')				, $ptab[0]);

			$content_form = str_replace('!!tab0!!', $ptab[0], $content_form);

			// mise a jour de l'onglet 1
			// constitution de la mention de responsabilite
			$content_form = str_replace('!!tab1!!', $this->get_tab_responsabilities_form(), $content_form);

			// mise a jour de l'onglet 2
			$ptab[2] = str_replace('!!ed1_id!!',			$this->ed1_id			, $ptab[2]);
			$ptab[2] = str_replace('!!ed1!!',				htmlentities($this->ed1,ENT_QUOTES, $charset)				, $ptab[2]);
			$ptab[2] = str_replace('!!coll_id!!',			$this->coll_id			, $ptab[2]);
			$ptab[2] = str_replace('!!coll!!',				htmlentities($this->coll,ENT_QUOTES, $charset)				, $ptab[2]);
			$ptab[2] = str_replace('!!subcoll_id!!',			$this->subcoll_id		, $ptab[2]);
			$ptab[2] = str_replace('!!subcoll!!',			htmlentities($this->subcoll,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!year!!',				$this->year				, $ptab[2]);
			$ptab[2] = str_replace('!!nocoll!!',			htmlentities($this->nocoll,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!mention_edition!!',			htmlentities($this->mention_edition,ENT_QUOTES, $charset)			, $ptab[2]);
			$ptab[2] = str_replace('!!ed2_id!!',			$this->ed2_id			, $ptab[2]);
			$ptab[2] = str_replace('!!ed2!!',				htmlentities($this->ed2,ENT_QUOTES, $charset)				, $ptab[2]);
			$ptab[2] = str_replace('!!force_dialog_publisher!!', $this->is_force_dialog('publisher')			, $ptab[2]);
			$ptab[2] = str_replace('!!force_dialog_collection!!', $this->is_force_dialog('collection')		, $ptab[2]);
			$ptab[2] = str_replace('!!force_dialog_subcollection!!', $this->is_force_dialog('subcollection')	, $ptab[2]);
			$ptab[2] = str_replace('!!force_popup_publisher!!', $this->is_force_popup('publisher')			, $ptab[2]);
			$ptab[2] = str_replace('!!force_popup_collection!!', $this->is_force_popup('collection')		, $ptab[2]);
			$ptab[2] = str_replace('!!force_popup_subcollection!!', $this->is_force_popup('subcollection')	, $ptab[2]);

			$content_form = str_replace('!!tab2!!', $ptab[2], $content_form);

			// mise a jour de l'onglet 3
			$content_form = str_replace('!!tab3!!', $this->get_tab_isbn_form(), $content_form);

			// Gestion des titres uniformes
			global $pmb_use_uniform_title;
			if ($pmb_use_uniform_title) {
				$content_form = str_replace('!!tab230!!', $this->get_tab_uniform_title_form(), $content_form);
			}

			// mise a jour de l'onglet 4

			// Importance matérielle (nombre de pages, d'éléments...)
			$element_npages = $this->get_input_text_content_form('el4Child_0', 'npages', '259');
			$ptab[4] = str_replace('!!element_npages!!', $element_npages, $ptab[4]);

			// Autres caractèristiques matérielle (ill., ...)
			$element_ill = $this->get_input_text_content_form('el4Child_1', 'ill', '260');
			$ptab[4] = str_replace('!!element_ill!!', $element_ill, $ptab[4]);

			// Format
			$element_size = $this->get_input_text_content_form('el4Child_2', 'size', '261');
			$ptab[4] = str_replace('!!element_size!!', $element_size, $ptab[4]);

			// Prix
			$element_prix = $this->get_input_text_content_form('el4Child_3', 'prix', '4050');
			$ptab[4] = str_replace('!!element_prix!!', $element_prix, $ptab[4]);

			// Matériel d'accompagnement
			$element_accomp = $this->get_input_text_content_form('el4Child_4', 'accomp', '262');
			$ptab[4] = str_replace('!!element_accomp!!', $element_accomp, $ptab[4]);

			$content_form = str_replace('!!tab4!!', $ptab[4], $content_form);

			// mise a jour de l'onglet 5
			$content_form = str_replace('!!tab5!!', $this->get_tab_notes_form(), $content_form);

			// mise a jour de l'onglet 6
			$content_form = str_replace('!!tab6!!', $this->get_tab_indexation_form(), $content_form);

			// mise a jour de l'onglet 7 : langues
			$content_form = str_replace('!!tab7!!', $this->get_tab_lang_form(), $content_form);

			// mise a jour de l'onglet 8
			$content_form = str_replace('!!tab8!!', $this->get_tab_links_form(), $content_form);

			//Mise a jour de l'onglet 9
			$content_form = str_replace('!!tab9!!', $this->get_tab_customs_perso_form(), $content_form);

			// Nomenclature
			if($pmb_nomenclature_activate){
				$nomenclature_duplicate = false;
				if($this->duplicate_from_id) {
					$nomenclature= new nomenclature_record_ui($this->duplicate_from_id);
					$nomenclature_duplicate = true;

					// On va chercher les relations vers les sous-manifs pour les supprimer
					$sub_manifs = array();
					$query = "SELECT child_record_num_record FROM nomenclature_notices_nomenclatures JOIN nomenclature_children_records ON id_notice_nomenclature = child_record_num_nomenclature WHERE notice_nomenclature_num_notice = ".$this->duplicate_from_id;
					$result = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result)) {
						while ($row = pmb_mysql_fetch_assoc($result)) {
							$sub_manifs[] = $row['child_record_num_record'];
						}
					}

					if (count($sub_manifs)) {
						foreach ($this->notice_link['down'] as $i => $notice_link_down) {
							if (in_array($notice_link_down->get_linked_notice(), $sub_manifs)) {
								unset($this->notice_link['down'][$i]);
							}
						}
					}
				} else {
					$nomenclature= new nomenclature_record_ui($this->id);
				}
				$nomenclature_notice_form=$ptab[15];
				$nomenclature_notice_form = str_replace('!!nomenclature_form!!', $nomenclature->get_form($nomenclature_duplicate), $nomenclature_notice_form);
				$content_form = str_replace('!!tab15!!', $nomenclature_notice_form, $content_form);
			}else{
				$content_form = str_replace('!!tab15!!', "", $content_form);
			}

			//Liens vers d'autres notices
			if($this->duplicate_from_id) {
				$notice_relations = notice_relations_collection::get_object_instance($this->duplicate_from_id);
			} else {
				$notice_relations = notice_relations_collection::get_object_instance($this->id);
			}
			$content_form = str_replace('!!tab11!!', $notice_relations->get_form($this->notice_link, 'm', ($this->duplicate_from_id ? true : false)),$content_form);

			// champs de gestion
			$content_form = str_replace('!!tab10!!', $this->get_tab_gestion_fields(), $content_form);

			$content_form = str_replace('!!indexation_lang_sel!!', ($this->indexation_lang ? $this->indexation_lang : $xmlta_indexation_lang), $content_form);

			// autorité personnalisées
			if($this->duplicate_from_id) {
				$authperso = new authperso_notice($this->duplicate_from_id);
			} else {
				$authperso = new authperso_notice($this->id);
			}
			$authperso_tpl=$authperso->get_form();
			$content_form = str_replace('!!authperso!!', $authperso_tpl, $content_form);

			// map
			if($pmb_map_activate){
				$content_form = str_replace('!!tab14!!', $this->get_tab_map_form(), $content_form);
			}else{
				$content_form = str_replace('!!tab14!!', "", $content_form);
			}

			return $content_form;
		}

		public function get_form() {
			global $msg, $categ;

			$interface_form = new interface_entity_record_form('notice');
			$interface_form->set_enctype('multipart/form-data');
			if (isset($this->notice_mere[0]) && $this->notice_mere[0]) {
				$this->libelle_form.=" ".$msg["catalog_notice_fille_lib"]." ".substr($this->notice_mere[0],0,100).(count($this->notice_mere)>1?", ...":"");
			}
			if($this->id) {
				$interface_form->set_label($this->libelle_form);
				$interface_form->set_document_title(($this->tit1 ? $this->tit1.' - ' : '').$this->libelle_form);
			} else {
				$interface_form->set_label($this->libelle_form);
				$interface_form->set_document_title(($this->tit1 ? $this->tit1.' - ' : '').$this->libelle_form);
			}

			$interface_form->set_object_id($this->id)
			->set_hierar_level($this->hierar_level)
			->set_code($this->code)
			->set_type_doc($this->type_doc)
			->set_content_form($this->get_content_form())
			->set_table_name('notices')
			->set_field_focus('f_tit1')
			->set_url_base(static::format_url());

			if($this->id_bibli) $interface_form->set_id_bibli($this->id_bibli);
			if($this->id_sug) $interface_form->set_id_sug($this->id_sug);
			if($this->id_demande) $interface_form->set_id_demande($this->id_demande);
			//Gestion de la duplication
			if($categ == "duplicate") {
				$interface_form->set_is_duplication("1");
			}
			return $interface_form->get_display();
		}

		// affichage du form associe
		public function show_form() {
			global $msg;

			$form_notice = $this->get_form();
			$event = new event_record('record', 'after_show_form');
			$event->set_record_id($this->id);
			$event_handler = events_handler::get_instance();
			$event_handler->send($event);
			$plugins_form = '';
			if ($event->get_result()) {
				$plugins_form = $event->get_result();
			}
			$form_notice .= $plugins_form;
			return $form_notice;
		}

		//creation formulaire droits d'acces pour notices
		public function get_rights_form() {

			global $msg,$charset;
			global $gestion_acces_active,$gestion_acces_user_notice, $gestion_acces_empr_notice;
			global $gestion_acces_user_notice_def, $gestion_acces_empr_notice_def;
			global $PMBuserid;

			if ($gestion_acces_active!=1) return '';
			$ac = new acces();

			$form = '';
			$c_form = "<label class='etiquette'><!-- domain_name --></label>
						<div class='row'>
				    	<div class='colonne3'>".htmlentities($msg['dom_cur_prf'],ENT_QUOTES,$charset)."</div>
				    	<div class='colonne_suite'><!-- prf_rad --></div>
				    	</div>
				    	<div class='row'>
				    	<div class='colonne3'>".htmlentities($msg['dom_cur_rights'],ENT_QUOTES,$charset)."</div>
					    <div class='colonne_suite'><!-- r_rad --></div>
					    <div class='row'><!-- rights_tab --></div>
					    </div>";

			if($gestion_acces_user_notice==1) {

				$r_form=$c_form;
				$dom_1 = $ac->setDomain(1);
				$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_1->getComment('long_name'), ENT_QUOTES, $charset) ,$r_form);
				if($this->id) {

					//profil ressource
					$def_prf=$dom_1->getComment('res_prf_def_lib');
					$res_prf=$dom_1->getResourceProfile($this->id);
					$q=$dom_1->loadUsedResourceProfiles();

					//recuperation droits utilisateur
					$user_rights = $dom_1->getRights($PMBuserid,$this->id,3);

					if($user_rights & 2) {
						$p_sel = gen_liste($q,'prf_id','prf_name', 'res_prf[1]', '', $res_prf, '0', $def_prf , '0', $def_prf);
						$p_rad = "<input type='radio' id='prf_rad_1_R' name='prf_rad[1]' value='R' ";
						if ($gestion_acces_user_notice_def!='1') {
						    $p_rad.= "checked='checked' ";
						}
						$p_rad.= "><label for='prf_rad_1_R' >".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</label></input><input type='radio' id='prf_rad_1_C' name='prf_rad[1]' value='C' ";
						if ($gestion_acces_user_notice_def=='1') {
						    $p_rad.= "checked='checked' ";
						}
						$p_rad.= "><label for='prf_rad_1_C' >".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)." $p_sel</label></input>";
						$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
					} else {
						$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_1->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
					}


					//droits/profils utilisateurs
					if($user_rights & 1) {
						$r_rad = "<input type='radio' id='rad_1_R' name='r_rad[1]' value='R' ";
						if ($gestion_acces_user_notice_def!='1') {
						    $r_rad.= "checked='checked' ";
						}
						$r_rad.= "><label for='rad_1_R' >".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</label></input><input type='radio' id='rad_1_C' name='r_rad[1]' value='C' ";
						if ($gestion_acces_user_notice_def=='1') {
						    $r_rad.= "checked='checked' ";
						}
						$r_rad.= "><label for='rad_1_C' >".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)."</label></input>";
						$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
					}


					//recuperation profils utilisateurs
					$t_u=array();
					$t_u[0]= $dom_1->getComment('user_prf_def_lib');	//niveau par defaut
					$qu=$dom_1->loadUsedUserProfiles();
					$ru=pmb_mysql_query($qu);
					if (pmb_mysql_num_rows($ru)) {
						while(($row=pmb_mysql_fetch_object($ru))) {
					        $t_u[$row->prf_id]= $row->prf_name;
						}
					}

					//recuperation des controles dependants de l'utilisateur
					$t_ctl=$dom_1->getControls(0);

					//recuperation des droits
					$t_rights = $dom_1->getResourceRights($this->id);

					if (count($t_u)) {

						$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
						foreach($t_u as $k=>$v) {
							$h_tab.= "<th class='dom_col'>".htmlentities($v, ENT_QUOTES, $charset)."</th>";
						}
						$h_tab.="</tr><!-- rights_tab --></table></div>";

						$c_tab = '<tr>';
						foreach($t_u as $k=>$v) {

							$c_tab.= "<td><table style='border:1px solid;' ><!-- rows --></table></td>";
							$t_rows = "";

							foreach($t_ctl as $k2=>$v2) {

								$t_rows.="
									<tr>
										<td style='width:25px;' ><input type='checkbox' id='chk_rights_1_".$k."_".$k2."' name='chk_rights[1][".$k."][".$k2."]' value='1' ";
								if ($t_rights[$k][$res_prf] & (pow(2,$k2-1))) {
									$t_rows.= "checked='checked' ";
								}
								if(($user_rights & 1)==0) {
								    $t_rows.="disabled='disabled' /></td>
										<td>".htmlentities($v2, ENT_QUOTES, $charset)."</td>
									</tr>";
								} else {
								    $t_rows.= "/></td>
										<td><label for='chk_rights_1_".$k."_".$k2."' >".htmlentities($v2, ENT_QUOTES, $charset)."</label></td>
									</tr>";
								}
							}
							$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
						}
						$c_tab.= "</tr>";

					}
					$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);
					$r_form=str_replace('<!-- rights_tab -->', $h_tab, $r_form);

				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
					$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
				}
				$form.= $r_form;

			}

			if($gestion_acces_empr_notice==1) {

				$r_form=$c_form;
				$dom_2 = $ac->setDomain(2);
				$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_2->getComment('long_name'), ENT_QUOTES, $charset) ,$r_form);
				if($this->id) {

					//profil ressource
					$def_prf=$dom_2->getComment('res_prf_def_lib');
					$res_prf=$dom_2->getResourceProfile($this->id);
					$q=$dom_2->loadUsedResourceProfiles();

					//Recuperation droits generiques utilisateur
					$user_rights = $dom_2->getDomainRights(0,$res_prf);

					if($user_rights & 2) {
						$p_sel = gen_liste($q,'prf_id','prf_name', 'res_prf[2]', '', $res_prf, '0', $def_prf , '0', $def_prf);
						$p_rad = "<input type='radio' id='prf_rad_2_R' name='prf_rad[2]' value='R' ";
						if ($gestion_acces_empr_notice_def!='1') {
						    $p_rad.= "checked='checked' ";
						}
						$p_rad.= "><label for='prf_rad_2_R' >".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</label></input><input type='radio' id='prf_rad_2_C' name='prf_rad[2]' value='C' ";
						if ($gestion_acces_empr_notice_def=='1') {
						    $p_rad.= "checked='checked' ";
						}
						$p_rad.= "><label for='prf_rad_2_C' >".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)." $p_sel</label></input>";
						$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
					} else {
						$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_2->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
					}

					//droits/profils utilisateurs
					if($user_rights & 1) {
						$r_rad = "<input type='radio' name='r_rad[2]' value='R' ";
						if ($gestion_acces_empr_notice_def!='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_calc'],ENT_QUOTES,$charset)."</input><input type='radio' name='r_rad[2]' value='C' ";
						if ($gestion_acces_empr_notice_def=='1') $r_rad.= "checked='checked' ";
						$r_rad.= ">".htmlentities($msg['dom_rad_def'],ENT_QUOTES,$charset)."</input>";
						$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
					}

					//recuperation profils utilisateurs
					$t_u=array();
					$t_u[0]= $dom_2->getComment('user_prf_def_lib');	//niveau par defaut
					$qu=$dom_2->loadUsedUserProfiles();
					$ru=pmb_mysql_query($qu);
					if (pmb_mysql_num_rows($ru)) {
						while(($row=pmb_mysql_fetch_object($ru))) {
					        $t_u[$row->prf_id]= $row->prf_name;
						}
					}

					//recuperation des controles dependants de l'utilisateur
					$t_ctl=$dom_2->getControls(0);

					//recuperation des droits
					$t_rights = $dom_2->getResourceRights($this->id);

					if (count($t_u)) {

						$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
						foreach($t_u as $k=>$v) {
							$h_tab.= "<th class='dom_col'>".htmlentities($v, ENT_QUOTES, $charset)."</th>";
						}
						$h_tab.="</tr><!-- rights_tab --></table></div>";

						$c_tab = '<tr>';
						foreach($t_u as $k=>$v) {

							$c_tab.= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
							$t_rows = "";

							foreach($t_ctl as $k2=>$v2) {

								$t_rows.="
									<tr>
										<td style='width:25px;' ><input type='checkbox' id='chk_rights_2_".$k."_".$k2."' name='chk_rights[2][".$k."][".$k2."]' value='1' ";
								if ($t_rights[$k][$res_prf] & (pow(2,$k2-1))) {
									$t_rows.= "checked='checked' ";
								}
								if(($user_rights & 1)==0) {
								    $t_rows.="disabled='disabled' /></td>
										<td>".htmlentities($v2, ENT_QUOTES, $charset)."</label></td>
									</tr>";
								} else {
								    $t_rows.="/></td>
										<td><label for='chk_rights_2_".$k."_".$k2."' >".htmlentities($v2, ENT_QUOTES, $charset)."</label></td>
									</tr>";
								}
							}
							$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
						}
						$c_tab.= "</tr>";

					}
					$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);;
					$r_form=str_replace('<!-- rights_tab -->', $h_tab, $r_form);

				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
					$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
				}
				$form.= $r_form;

			}
			return $form;
		}


		// ---------------------------------------------------------------
		//		replace_form : affichage du formulaire de remplacement
		// ---------------------------------------------------------------
		public function replace_form() {
			global $notice_replace;
			global $msg;
			global $include_path;
			global $deflt_notice_replace_keep_categories;
			global $notice_replace_categories, $notice_replace_category;
			global $thesaurus_mode_pmb;
			global $charset;

			// a completer
			if(!$this->id) {
				require_once("$include_path/user_error.inc.php");
				error_message($msg[161], $msg[162], 1, $this->target_link_on_error);
				return false;
			}

			$notice_replace=str_replace('!!old_notice_libelle!!', $this->tit1." - ".$this->code, $notice_replace);
			$notice_replace=str_replace('!!id!!', $this->id, $notice_replace);
			if (!empty($deflt_notice_replace_keep_categories) && !empty($this->categories)) {
				// categories
				$categories_to_replace = "";
				$nb_categories = count($this->categories);
				for ($i = 0; $i < $nb_categories; $i++) {
					$categ_id = $this->categories[$i]["categ_id"] ;
					$categ = new category($categ_id);
					$ptab_categ = str_replace('!!icateg!!', $i, $notice_replace_category) ;
					$ptab_categ = str_replace('!!categ_id!!', $categ_id, $ptab_categ);
					if ($thesaurus_mode_pmb) $nom_thesaurus='['.$categ->thes->getLibelle().'] ' ;
					else $nom_thesaurus='' ;
					$ptab_categ = str_replace('!!categ_libelle!!',	htmlentities($nom_thesaurus.$categ->catalog_form,ENT_QUOTES, $charset), $ptab_categ);
					$categories_to_replace .= $ptab_categ ;
				}
				$notice_replace_categories=str_replace('!!notice_replace_category!!', $categories_to_replace, $notice_replace_categories);
				$notice_replace_categories=str_replace('!!nb_categ!!', count($this->categories), $notice_replace_categories);

				$notice_replace=str_replace('!!notice_replace_categories!!', $notice_replace_categories, $notice_replace);
			} else {
				$notice_replace=str_replace('!!notice_replace_categories!!', "", $notice_replace);
			}
			print $notice_replace;
			return true;
		}

		public function set_properties_from_form() {
			global $typdoc, $form_notice_statut;
			global $indexation_lang, $f_notice_is_new, $f_is_numeric;
			global $f_commentaire_gestion, $f_thumbnail_url, $form_num_notice_usage;
			global $f_tit1, $f_tit2, $f_tit3, $f_tit4;
			global $f_tparent, $f_tparent_id, $f_tnvol;
			global $pmb_use_uniform_title, $max_titre_uniforme;
			global $f_aut0_id, $f_f0_code;
			global $max_aut1, $max_aut2;
			global $f_ed1, $f_ed1_id, $f_ed2, $f_ed2_id, $f_coll, $f_coll_id, $f_subcoll, $f_subcoll_id;
			global $f_year, $f_nocoll, $f_mention_edition, $f_cb;
			global $f_npages, $f_ill, $f_size, $f_prix, $f_accomp;
			global $f_n_gen, $f_n_contenu, $f_n_resume;
			global $tab_categ_order, $max_categ;
			global $f_indexint, $f_indexint_id, $f_indexation;
			global $f_lien, $f_eformat;
			global $b_level, $h_level;
			global $max_lang, $max_langorg;
			global $f_reset_thumbnail;

			$this->type_doc = $typdoc;
			$this->statut = intval($form_notice_statut);
			$this->indexation_lang = $indexation_lang;
			$this->is_new = intval($f_notice_is_new);
			$this->is_numeric = intval($f_is_numeric);
			$this->commentaire_gestion = stripslashes($f_commentaire_gestion);
			$this->thumbnail_url = stripslashes($f_thumbnail_url);
			$this->num_notice_usage = (!empty($form_num_notice_usage) ? intval($form_num_notice_usage) : 0);

			$this->tit1 =	clean_string(stripslashes($f_tit1 ?? ''));
			$this->tit2		=	clean_string(stripslashes($f_tit2 ?? ''));
			$this->tit3		=	clean_string(stripslashes($f_tit3 ?? ''));
			$this->tit4		=	clean_string(stripslashes($f_tit4 ?? ''));
			$this->tparent	=	clean_string(stripslashes($f_tparent ?? ''));
			$this->tparent_id = intval($f_tparent_id ?? 0);
			$this->tnvol	=	clean_string(stripslashes($f_tnvol ?? ''));

			// Titres uniformes
			$this->titres_uniformes = array();
			if ($pmb_use_uniform_title) {
				for ($i=0; $i<$max_titre_uniforme ; $i++) {
					$var_tu_id = "f_titre_uniforme_code$i" ;
					$var_ntu_titre = "ntu_titre$i" ;
					$var_ntu_date = "ntu_date$i" ;
					$var_ntu_sous_vedette = "ntu_sous_vedette$i" ;
					$var_ntu_langue = "ntu_langue$i" ;
					$var_ntu_version = "ntu_version$i" ;
					$var_ntu_mention = "ntu_mention$i" ;

					global ${$var_tu_id}, ${$var_ntu_titre}, ${$var_ntu_date};
					global ${$var_ntu_sous_vedette}, ${$var_ntu_langue}, ${$var_ntu_version}, ${$var_ntu_mention};
					$this->titres_uniformes[] = array (
							'num_tu' => ${$var_tu_id},
							'ntu_titre' => ${$var_ntu_titre},
							'ntu_date' => ${$var_ntu_date},
							'ntu_sous_vedette' => ${$var_ntu_sous_vedette},
							'ntu_langue' => ${$var_ntu_langue},
							'ntu_version' => ${$var_ntu_version},
							'ntu_mention' => ${$var_ntu_mention} )
							;
				}
			}

			$this->responsabilites = array();
			$this->responsabilites['responsabilites'] = array();
			$this->responsabilites['auteurs'] = array();

			//Ajout d'un test sur la précense d'un auteur
			if(isset($f_aut0_id) && ($f_aut0_id != 0)){
				// auteur principal
				$this->responsabilites['responsabilites'][] = '0';
				$this->responsabilites['auteurs'][] = array(
						'id' => $f_aut0_id,
						'fonction' => $f_f0_code,
						'responsability' => '0',
						'order' => '0'
				);
			}
			// autres auteurs
			for ($i=0; $i<$max_aut1; $i++) {
				$var_autid = "f_aut1_id$i" ;
				$var_autfonc = "f_f1_code$i" ;
				global ${$var_autid}, ${$var_autfonc};
				if(isset(${$var_autid}) && (${$var_autid} != 0)){
					$this->responsabilites['responsabilites'][] = '1';
					$this->responsabilites['auteurs'][] = array(
							'id' => ${$var_autid},
							'fonction' => ${$var_autfonc},
							'responsability' => '1',
							'order' => $i
					);
				}
			}

			// auteurs secondaires
			for ($i=0; $i<$max_aut2 ; $i++) {
				$var_autid = "f_aut2_id$i" ;
				$var_autfonc = "f_f2_code$i" ;
				global ${$var_autid}, ${$var_autfonc};
				if(isset(${$var_autid}) && (${$var_autid} != 0)){
					$this->responsabilites['responsabilites'][] = '2';
					$this->responsabilites['auteurs'][] = array(
							'id' => ${$var_autid},
							'fonction' => ${$var_autfonc},
							'responsability' => '2',
							'order' => $i
					);
				}
			}

			$this->ed1		=	clean_string(stripslashes($f_ed1 ?? ''));
			if($this->ed1) {
			    $this->ed1_id	=	intval($f_ed1_id);
			} else {
				$this->ed1_id	=	0;
			}
			$this->ed2		=	clean_string(stripslashes($f_ed2 ?? ''));
			if($this->ed2) {
			    $this->ed2_id	=	intval($f_ed2_id);
			} else {
				$this->ed2_id	=	0;
			}
			$this->coll		=	clean_string(stripslashes($f_coll ?? ''));
			if($this->coll && $this->ed1_id) {
			    $this->coll_id	= 	intval($f_coll_id);
			} else {
				$this->coll_id	= 	0;
			}
			$this->subcoll	=	clean_string(stripslashes($f_subcoll ?? ''));
			if($this->subcoll && $this->coll_id) {
			    $this->subcoll_id = intval($f_subcoll_id);
			} else {
				$this->subcoll_id = 0;
			}
			$this->year		=	trim(clean_string(stripslashes($f_year ?? '')));
			$this->nocoll	=	trim(clean_string(stripslashes($f_nocoll ?? '')));
			if(!$this->coll_id) {
				$this->nocoll	= '';
			}
			$this->mention_edition	=	trim(clean_string(stripslashes($f_mention_edition ?? '')));

			$this->code = '';
			$f_cb = clean_string(stripslashes($f_cb ?? ''));
			if ($f_cb) {
				// ce controle redondant est la pour le cas ou l'utilisateur aurait change le code
				if(isEAN($f_cb)) {
					// la saisie est un EAN -> on tente de le formater en ISBN
					$code = EANtoISBN($f_cb);
					// si echec, on prend l'EAN comme il vient
					if(!$code) $code = $f_cb;
				} else {
					if(isISBN($f_cb)) {
						// si la saisie est un ISBN
						$code = formatISBN($f_cb,13);
						// si echec, ISBN errone on le prend sous cette forme
						if(!$code) $code = $f_cb;
					} else {
						// ce n'est rien de tout ca, on prend la saisie telle quelle
						$code = $f_cb;
					}
				}
				$this->code = $code;
			}
			$this->npages	=	clean_string(stripslashes($f_npages ?? ''));
			$this->ill		=	clean_string(stripslashes($f_ill ?? ''));
			$this->size		=	clean_string(stripslashes($f_size ?? ''));
			$this->prix		=	clean_string(stripslashes($f_prix ?? ''));
			$this->accomp	=	clean_string(stripslashes($f_accomp ?? ''));


			$this->n_gen 	= 	stripslashes($f_n_gen);
			$this->n_contenu = 	stripslashes($f_n_contenu);
			$this->n_resume = 	stripslashes($f_n_resume);

			// categories
			$this->categories = array();
			if($tab_categ_order){
				$categ_order=explode(",",$tab_categ_order);
				foreach($categ_order as $old_order){
					$var_categid = "f_categ_id$old_order" ;
					global ${$var_categid};
					if(${$var_categid}){
						$this->categories[] = array(
								'categ_id' => ${$var_categid}
						);
					}
				}
			}else{
				for ($i=0; $i< $max_categ ; $i++) {
					$var_categid = "f_categ_id$i" ;
					global ${$var_categid};
					if(isset(${$var_categid}) && (${$var_categid} != 0)){
						$this->categories[] = array(
								'categ_id' => ${$var_categid}
						);
					}
				}
			}

			if($f_indexint) {
			    $this->indexint	=	intval($f_indexint_id);
			} else {
				$this->indexint	=	0;
			}
			$this->index_l 	=	clean_tags(stripslashes($f_indexation));

			$this->lien		=	clean_string(stripslashes($f_lien));
			if($this->lien) {
				$this->eformat	=	clean_string(stripslashes($f_eformat));
			}

			if($b_level) {
				$this->biblio_level = $b_level;
			} else {
				$this->biblio_level = 'm';
			}
			if($h_level) {
				$this->hierar_level = $h_level;
			} else {
				$this->hierar_level = '0';
			}


			$marc_liste_langues = marc_list_collection::get_instance('lang');
			$this->langues = array();
			// langues
			for ($i=0; $i< $max_lang ; $i++) {
				$var_langcode = "f_lang_code$i";
				global ${$var_langcode};
				if (${$var_langcode}) {
					$this->langues[] = array(
							'lang_code' => ${$var_langcode},
							'langue' => $marc_liste_langues->table[${$var_langcode}]
					);
				}
			}
			$this->languesorg = array();
			// langues originales
			for ($i=0; $i< $max_langorg ; $i++) {
				$var_langorgcode = "f_langorg_code$i";
				global ${$var_langorgcode};
				if (${$var_langorgcode}) {
					$this->languesorg[] = array(
							'lang_code' => ${$var_langorgcode},
							'langue' => $marc_liste_langues->table[${$var_langorgcode}]
					);
				}
			}

			$this->reset_thumbnail = !empty($f_reset_thumbnail);

			global $opac_url_base, $pmb_url_base;
			$queryParameters = [];
			parse_str(
				parse_url($this->thumbnail_url, PHP_URL_QUERY) ?? '',
				$queryParameters
			);

			// Il ne faut pas supprime la vignette uploade si l'URL de la vignette est l'ancien mecanisme
			// Exemple : ./getimage.php?noticecode=&vigurl=&notice_id=170
			// Pour la refonte des vignettes, il faut juste vide l'url de la vignette
			if (
				(
					strpos($this->thumbnail_url, $opac_url_base) === 0 ||
					strpos($this->thumbnail_url, $pmb_url_base) === 0
				) &&
				strpos($this->thumbnail_url, "getimage.php") !== false &&
				intval($queryParameters['notice_id']) === intval($this->id)
			) {
				$this->thumbnail_url = "";
				$this->reset_thumbnail = false;
			}
		}

		public function save() {
			global $msg;
			global $pmb_synchro_rdf;
			global $pmb_authors_qualification;
			global $signature;
			global $id_sug;
			global $pmb_ark_activate, $is_duplication;

			//synchro_rdf
			if($pmb_synchro_rdf) {
				$synchro_rdf = new synchro_rdf();
				if($this->id) {
					$synchro_rdf->delRdf($this->id,0);
				}
			}

			$postrequete = "";
			if($this->id) {
				$requete = "UPDATE notices SET update_date=sysdate(), ";
				$postrequete = " WHERE notice_id=".$this->id;
			} else {
				$requete = "INSERT INTO notices SET create_date=sysdate(), update_date=sysdate(), ";
			}

			$req_notice_date_is_new="";
			if($this->id) {
				$req_new="select notice_is_new, notice_date_is_new from notices where notice_id=".$this->id;
				$res_new=pmb_mysql_query($req_new);
				if (pmb_mysql_num_rows($res_new)) {
					if($r=pmb_mysql_fetch_object($res_new)){
						if($r->notice_is_new==$this->is_new){ // pas de changement du flag
							$req_notice_date_is_new= "";
						}elseif($this->is_new){ // Changement du flag et affecté comme new
							$req_notice_date_is_new= ", notice_date_is_new =now() ";
						}else{// raz date
							$req_notice_date_is_new= ", notice_date_is_new ='' ";
						}
					}
				}
			}else{
				if($this->is_new){ // flag affecté comme new en création
					$req_notice_date_is_new= ", notice_date_is_new =now() ";
				}
			}

			// clean des vieilles nouveautés
			static::cleaning_is_new();

			//spécification pour les articles de périodiques et les notices de bulletin
			if ((get_called_class() == 'analysis' || get_called_class() == 'bulletinage') && $this->date_parution!='0000-00-00'){
				$date_parution_notice = $this->date_parution;
			} else {
				$date_parution_notice = static::get_date_parution($this->year);
			}

			$requete .= " typdoc='".$this->type_doc."'";
			$requete .= ", tit1='".addslashes($this->tit1)."'";
			$requete .= ", tit2='".addslashes($this->tit2)."'";
			$requete .= ", tit3='".addslashes($this->tit3)."'";
			$requete .= ", tit4='".addslashes($this->tit4)."'";
			$requete .= ", tparent_id=".$this->tparent_id;
			$requete .= ", tnvol='".addslashes($this->tnvol)."'";
			$requete .= ", ed1_id='".$this->ed1_id."'";
			$requete .= ", ed2_id='".$this->ed2_id."'";
			$requete .= ", coll_id='".$this->coll_id."'";
			$requete .= ", subcoll_id='".$this->subcoll_id."'";
			$requete .= ", year='".addslashes($this->year)."'";
			$requete .= ", nocoll='".addslashes($this->nocoll)."'";
			$requete .= ", mention_edition='".addslashes($this->mention_edition)."'";
			$requete .= ", code='".addslashes($this->code)."'";
			$requete .= ", npages='".addslashes($this->npages)."'";
			$requete .= ", ill='".addslashes($this->ill)."'";
			$requete .= ", size='".addslashes($this->size)."'";
			$requete .= ", prix='".addslashes($this->prix)."'";
			$requete .= ", accomp='".addslashes($this->accomp)."'";
			$requete .= ", n_gen='".addslashes($this->n_gen)."'";
			$requete .= ", n_contenu='".addslashes($this->n_contenu)."'";
			$requete .= ", n_resume='".addslashes($this->n_resume)."'";
			$requete .= ", indexint='".$this->indexint."'";
			$requete .= ", index_l='".addslashes($this->index_l)."'";
			$requete .= ", lien='".addslashes($this->lien)."'";
			$requete .= ", eformat='".addslashes($this->eformat)."'";
			$requete .= ", niveau_biblio='".$this->biblio_level."'";
			$requete .= ", niveau_hierar='".$this->hierar_level."'";
			$requete .= ", statut='".$this->statut."'";
			$requete .= ", commentaire_gestion='".addslashes($this->commentaire_gestion)."'";
			$requete .= ", thumbnail_url='".addslashes($this->thumbnail_url)."'";
			$requete .= ", signature='".$this->signature."'";
			$requete .= ", date_parution='".$date_parution_notice."'";
			$requete .= ", opac_visible_bulletinage='".$this->opac_visible_bulletinage."'";
			$requete .= ", indexation_lang='".$this->indexation_lang."'";
			$requete .= ", opac_serialcirc_demande='".$this->opac_serialcirc_demande."'";
			$requete .= ", notice_is_new='".$this->is_new."'";
			$requete .= ", num_notice_usage='".$this->num_notice_usage."'";
			$requete .= ", is_numeric='".$this->is_numeric."'";
			$requete .= $req_notice_date_is_new;
			$requete .= $postrequete;

			$result = pmb_mysql_query($requete);

			//traitement audit
			if (!$this->id) {
				$sav_id=0;
				$this->id=pmb_mysql_insert_id();
				audit::insert_creation (AUDIT_NOTICE, $this->id) ;
			} else {
				$sav_id=$this->id;
				audit::insert_modif (AUDIT_NOTICE, $this->id) ;
			}
			// autorité personnalisées
			$authperso = new authperso_notice($this->id);
			$authperso->save_form();

			// map
			global $pmb_map_activate;
			if($pmb_map_activate){
				$map = new map_edition_controler(TYPE_RECORD, $this->id);
				$map->save_form();
				$map_info = new map_info($this->id);
				$map_info->save_form();
			}
			//VIGNETTES
			thumbnail::clearCache($this->id, TYPE_NOTICE);
			//raz vignette uploadee
			if ($this->reset_thumbnail) {
			    thumbnail::delete($this->id);
			}
			// vignette de la notice uploadé dans un répertoire
			if(thumbnail::create($this->id)) {
				$query = "update notices set thumbnail_url='' where notice_id ='".$this->id."'";
				pmb_mysql_query($query);
			} elseif ($this->thumbnail_url) {
			    thumbnail::delete($this->id);
			}

			// Traitement des titres uniformes
			global $pmb_use_uniform_title;
			if ($pmb_use_uniform_title) {
				$ntu=new tu_notice($this->id);
				$ntu->update($this->titres_uniformes);
			}

			//traitement des droits acces user_notice
			global $gestion_acces_active;
			if ($gestion_acces_active==1) {
				$ac = new acces();
				global $res_prf, $chk_rights, $prf_rad, $r_rad;
				global $gestion_acces_user_notice;
				if ($gestion_acces_user_notice==1) {
					$dom_1= $ac->setDomain(1);
					if ($sav_id) {
						$dom_1->storeUserRights(1, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					} else {
						$dom_1->storeUserRights(0, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					}
				}

				//traitement des droits acces empr_notice
				global $gestion_acces_empr_notice;
				if ($gestion_acces_empr_notice==1) {
					$dom_2= $ac->setDomain(2);
					if ($sav_id) {
						$dom_2->storeUserRights(1, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					} else {
						$dom_2->storeUserRights(0, $this->id, $res_prf, $chk_rights, $prf_rad, $r_rad);
					}
				}
			}

			//Traitement des liens
			$notice_relations = notice_relations_collection::get_object_instance($this->id);
			$notice_relations->set_properties_from_form();
			$notice_relations->save();

			// nomenclature
			global $pmb_nomenclature_activate;
			if($pmb_nomenclature_activate){
				$nomenclature= new nomenclature_record_ui($this->id);
				$nomenclature->save_form();
			}

			// Clean des vedettes
			$id_vedettes_links_deleted=static::delete_vedette_links($this->id);

			// traitement des auteurs
			$rqt_del = "delete from responsability where responsability_notice='".$this->id."' ";
			$res_del = pmb_mysql_query($rqt_del);
			$rqt_ins = "INSERT INTO responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre) VALUES ";

			$i=0;
			$var_name='notice_role_composed';
			global ${$var_name};
			$role_composed=${$var_name};
			$var_name='notice_role_autre_composed';
			global ${$var_name};
			$role_composed_autre=${$var_name};
			$var_name='notice_role_secondaire_composed';
			global ${$var_name};
			$role_composed_secondaire=${$var_name};
			$id_vedettes_used=array();
			foreach($this->responsabilites['auteurs'] as $auteur) {
				$rqt = $rqt_ins . " ('".$auteur['id']."','".$this->id."','".$auteur['fonction']."','".$auteur['responsability']."', ".$auteur['order'].") " ;
				$res_ins = pmb_mysql_query($rqt);
				$id_responsability=pmb_mysql_insert_id();
				if($pmb_authors_qualification){
					$id_vedette=0;
					switch($auteur['responsability']){
						case 0:
							//On duplique la vedette en mode duplication
							if(! empty($is_duplication) && $is_duplication != 0) {
								$role_composed[$auteur['order']]["id"] = 0;
							}
							$id_vedette=static::update_vedette(stripslashes_array($role_composed[$auteur['order']]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_PRINCIPAL);
							break;
						case 1:
							if(! empty($is_duplication) && $is_duplication != 0) {
								$role_composed_autre[$auteur['order']]["id"] = 0;
							}
							$id_vedette=static::update_vedette(stripslashes_array($role_composed_autre[$auteur['order']]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_AUTRE);
							break;
						case 2:
							if(! empty($is_duplication) && $is_duplication != 0) {
								$role_composed_secondaire[$auteur['order']]["id"] = 0;
							}
							$id_vedette=static::update_vedette(stripslashes_array($role_composed_secondaire[$auteur['order']]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_SECONDAIRE);
							break;
					}
					if($id_vedette)$id_vedettes_used[]=$id_vedette;
				}
			}
			foreach ($id_vedettes_links_deleted as $id_vedette){
				if(!in_array($id_vedette,$id_vedettes_used)){
					$vedette_composee = new vedette_composee($id_vedette);
					$vedette_composee->delete();
				}
			}

			// traitement des categories
			$rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='".$this->id."' ";
			pmb_mysql_query($rqt_del);
			$rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
			foreach ($this->categories as $ordre_categ=>$categorie) {
				$rqt = $rqt_ins . " ('".$this->id."','".$categorie['categ_id']."',$ordre_categ) " ;
				pmb_mysql_query($rqt);
			}

			// traitement des concepts
			global $thesaurus_concepts_active;
			if($thesaurus_concepts_active == 1){
				$index_concept = new index_concept($this->id, TYPE_NOTICE);
				$index_concept->save();
			}

			// traitement des langues
			$rqt_del = "delete from notices_langues where num_notice='".$this->id."' ";
			pmb_mysql_query($rqt_del);

			// langues
			$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
			foreach ($this->langues as $order=>$langue) {
				$rqt = $rqt_ins . " ('".$this->id."',0, '".$langue['lang_code']."',$order) " ;
				pmb_mysql_query($rqt);
			}

			// langues originales
			$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
			foreach ($this->languesorg as $order=>$langue) {
				$rqt = $rqt_ins . " ('".$this->id."',1, '".$langue['lang_code']."',$order) " ;
				pmb_mysql_query($rqt);
			}

			//Traitement des champs personnalises
			$p_perso=new parametres_perso("notices");
			$p_perso->rec_fields_perso($this->id);

			if(!$result) {
				return false;
			}

			//Recherche du titre uniforme automatique
			global $opac_enrichment_bnf_sparql;
			//$opac_enrichment_bnf_sparql=1;

			$titre_uniforme = static::getAutomaticTu($this->id);//ATTENTION si on récupère le titre uniforme ici alors il est bien ajouté à la notice mais pas affiché

			// Mise à jour de tous les index de la notice
			static::majNoticesTotal($this->id);

			//synchro_rdf
			if($pmb_synchro_rdf){
				$synchro_rdf->addRdf($this->id,0);
			}

			//Soumission dans le module acquisition
			if(intval($id_sug)) {
				//Mise a jour de la suggestion
				$sug = new suggestions($id_sug);
				$sug->titre = stripslashes($this->tit1);
				global $f_aut0_id, $f_f0_code;
				$f_aut[] = array (
						'id' => $f_aut0_id,
						'fonction' => $f_f0_code,
						'type' => '0' );
				if ($f_aut0_id) {
					$auteur = new auteur($f_aut0_id);
					$sug->auteur = $auteur->display;
				}
				global $f_ed1_id, $f_ed1;
				if ($f_ed1_id)	$sug->editeur = stripslashes($f_ed1);
				$sug->code = $this->code;
				$sug->prix = str_replace(',','.',$this->prix); //float(8,2)
				$sug->num_notice = $this->id;
				$sug->save();
			}

			if ($pmb_ark_activate) {
				ArkModel::saveArkFromEntity($this);
			}
			return true;
		}

		public static function cleaning_is_new() {
			global $pmb_newrecord_timeshift;

			if($pmb_newrecord_timeshift){
				$notices = array();
				$query = "SELECT notice_id FROM notices WHERE notice_date_is_new !='0000-00-00 00:00:00' and (notice_date_is_new < now() - interval ".$pmb_newrecord_timeshift." day )";
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)) {
					while($row = pmb_mysql_fetch_object($result)) {
						$notices[] = $row->notice_id;
					}
				}
				$req_old="UPDATE notices SET notice_date_is_new ='', notice_is_new=0, update_date=update_date where notice_date_is_new !='0000-00-00 00:00:00' and (notice_date_is_new < now() - interval ".$pmb_newrecord_timeshift." day )";
				pmb_mysql_query($req_old);
				if(count($notices)) {
					foreach ($notices as $notice_id) {
						static::majNoticesMotsGlobalIndex($notice_id,'new');
					}
				}
			}
		}

		public static function update_vedette($data,$id,$type){
			if ($data["elements"]) {
				$vedette_composee = new vedette_composee($data["id"], static::$vedette_composee_config_filename);
				if ($data["value"]) {
					$vedette_composee->set_label($data["value"]);
				}
				// On commence par réinitialiser le tableau des éléments de la vedette composée
				$vedette_composee->reset_elements();
				// On remplit le tableau des éléments de la vedette composée
				$vedette_composee_id=0;
				$tosave=false;
				foreach ($data["elements"] as $subdivision => $elements) {
					if ($elements["elements_order"] !== "") {
						$elements_order = explode(",", $elements["elements_order"]);
						foreach ($elements_order as $position => $num_element) {
							if ($elements[$num_element]["id"] && $elements[$num_element]["label"]) {
								$tosave=true;
								$velement = $elements[$num_element]["type"];
								if(strpos($velement,"vedette_ontologies") === 0){
									$velement = "vedette_ontologies";
								}
								$available_field_class_name = $vedette_composee->get_at_available_field_num($elements[$num_element]['available_field_num']);
								if(empty($available_field_class_name['params'])) {
									$available_field_class_name['params'] = array();
								}
								$vedette_element = new $velement($elements[$num_element]['available_field_num'],$elements[$num_element]["id"], $elements[$num_element]["label"], $available_field_class_name['params']);
								$vedette_composee->add_element($vedette_element, $subdivision, $position);
							}
						}
					}
				}
				if($tosave)$vedette_composee_id = $vedette_composee->save();
			}
			if ($vedette_composee_id) {
				vedette_link::save_vedette_link($vedette_composee, $id, $type);
			}
			return $vedette_composee_id;
		}

		// ---------------------------------------------------------------
		//		replace($by) : remplacement de la notice
		// ---------------------------------------------------------------
		public function replace($by,$supp_notice=true) {
			global $msg;
			global $keep_categories;
			global $notice_replace_links;
			global $pmb_ark_activate;

			if($this->id == $by) {
				return $msg[223];
			}
			if (($this->id == $by) || (!$this->id)) {
				return $msg[223];
			}

			$by_notice= new notice($by);
			if ($this->biblio_level != $by_notice->biblio_level || $this->hierar_level != $by_notice->hierar_level) {
				return $msg['catal_rep_not_err1'];
			}

			// traitement des catégories (si conservation cochée)
			if ($keep_categories) {
				update_notice_categories_from_form($by);
			}

			//gestion des liens
			notice_relations::replace_links($this->id, $by, $notice_replace_links);

			vedette_composee::replace(TYPE_NOTICE, $this->id, $by);
			// Mise à jour des vedettes composées contenant cette notice
			vedette_composee::update_vedettes_built_with_element($by, TYPE_NOTICE);

			// remplacement dans les exemplaires numériques
			$requete = "UPDATE explnum SET explnum_notice='$by' WHERE explnum_notice='$this->id' ";
			pmb_mysql_query($requete);

			// remplacement dans les exemplaires
			$requete = "UPDATE exemplaires SET expl_notice='$by' WHERE expl_notice='$this->id' ";
			pmb_mysql_query($requete);

			// remplacement dans les depouillements
			$requete = "UPDATE analysis SET analysis_notice='$by' WHERE analysis_notice='$this->id' ";
			pmb_mysql_query($requete);

			// remplacement dans les bulletins
			$requete = "UPDATE bulletins SET bulletin_notice='$by' WHERE bulletin_notice='$this->id' ";
			pmb_mysql_query($requete);

			// remplacement dans les resas
			$requete = "UPDATE resa SET resa_idnotice='$by' WHERE resa_idnotice='$this->id' ";
			pmb_mysql_query($requete);

			$req="UPDATE notices_authperso SET notice_authperso_notice_num='$by' where notice_authperso_notice_num='$this->id' ";
			pmb_mysql_query($req);

			if ($pmb_ark_activate) {
			    $arkEntityReplaced = ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $this->id);
			    $arkEntityReplacing = ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $by);
    			$arkEntityReplaced->markAsReplaced($arkEntityReplacing);
			}
			//Suppression de la notice
			if($supp_notice){
				static::del_notice($this->id);
			}
			return FALSE;
		}

		public static function del_notice ($id) {

			global $class_path,$pmb_synchro_rdf;
			global $sphinx_active;
			global $pmb_ark_activate;

			//Suppression de la vignette de la notice si il y en a une d'uploadée
			thumbnail::clearCache($id, TYPE_NOTICE);
			thumbnail::delete($id);

			//synchro_rdf (à laisser en premier : a besoin des éléments de la notice pour retirer du graphe rdf)
			if($pmb_synchro_rdf){
				$synchro_rdf = new synchro_rdf();
				$synchro_rdf->delRdf($id,0);
			}

			$p_perso=new parametres_perso("notices");
			$p_perso->delete_values($id);

			$requete = "DELETE FROM notices_categories WHERE notcateg_notice='$id'" ;
			@pmb_mysql_query($requete);

			$requete = "DELETE FROM notices_langues WHERE num_notice='$id'" ;
			@pmb_mysql_query($requete);

			$requete = "DELETE FROM notices WHERE notice_id='$id'" ;
			@pmb_mysql_query($requete);
			audit::delete_audit (AUDIT_NOTICE, $id) ;

			// Effacement de l'occurence de la notice ds la table notices_global_index :
			$requete = "DELETE FROM notices_global_index WHERE num_notice=".$id;
			@pmb_mysql_query($requete);

			// Effacement des occurences de la notice ds la table notices_mots_global_index :
			$requete = "DELETE FROM notices_mots_global_index WHERE id_notice=".$id;
			@pmb_mysql_query($requete);

			// Effacement des occurences de la notice ds la table notices_fields_global_index :
			$requete = "DELETE FROM notices_fields_global_index WHERE id_notice=".$id;
			@pmb_mysql_query($requete);

			//Suppression des nomenclatures avant la suppression des relations entre notices (manif / sous manif)
			$nomenclature_record = new nomenclature_record_ui($id);
			$nomenclature_record->delete();

			notice_relations::delete($id);

			// elimination des docs numeriques
			$req_explNum = "select explnum_id from explnum where explnum_notice=".$id." ";
			$result_explNum = @pmb_mysql_query($req_explNum);
			while(($explNum = pmb_mysql_fetch_object($result_explNum))) {
				$myExplNum = new explnum($explNum->explnum_id);
				$myExplNum->delete();
			}

			// Clean des vedettes
			$id_vedettes_links_deleted=static::delete_vedette_links($id);
			foreach ($id_vedettes_links_deleted as $id_vedette){
				$vedette_composee = new vedette_composee($id_vedette);
				$vedette_composee->delete();
			}

			$requete = "DELETE FROM responsability WHERE responsability_notice='$id'" ;
			@pmb_mysql_query($requete);

			$requete = "DELETE FROM bannette_contenu WHERE num_notice='$id'" ;
			@pmb_mysql_query($requete);

			$requete = "delete from caddie_content using caddie, caddie_content where caddie_id=idcaddie and type='NOTI' and object_id='".$id."' ";
			@pmb_mysql_query($requete);

			$requete = "delete from analysis where analysis_notice='".$id."' ";
			@pmb_mysql_query($requete);

			$requete = "update bulletins set num_notice=0 where num_notice='".$id."' ";
			@pmb_mysql_query($requete);

			//Suppression de la reference a la notice dans la table suggestions
			$requete = "UPDATE suggestions set num_notice = 0 where num_notice=".$id;
			@pmb_mysql_query($requete);

			//Suppression de la reference a la notice dans la table lignes_actes
			$requete = "UPDATE lignes_actes set num_produit=0, type_ligne=0 where num_produit='".$id."' and type_ligne in ('1','5') ";
			@pmb_mysql_query($requete);

			//suppression des droits d'acces user_notice
			$query_acces = "show tables like 'acces_res_1'";
			$result_acces = pmb_mysql_query($query_acces);
			if($result_acces && pmb_mysql_num_rows($result_acces)) {
				$requete = "delete from acces_res_1 where res_num=".$id;
				@pmb_mysql_query($requete);
			}

			// suppression des tags
			$rqt_del = "delete from tags where num_notice=".$id;
			@pmb_mysql_query($rqt_del);

			//suppression des avis
			avis_records::delete_from_object($id);

			//suppression des droits d'acces empr_notice
			$query_acces = "show tables like 'acces_res_2'";
			$result_acces = pmb_mysql_query($query_acces);
			if($result_acces && pmb_mysql_num_rows($result_acces)) {
				$requete = "delete from acces_res_2 where res_num=".$id;
				@pmb_mysql_query($requete);
			}

			// Supression des liens avec les titres uniformes
			$requete = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice='$id'" ;
			@pmb_mysql_query($requete);

			//Suppression dans les listes de lecture partagées
			$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_notice_num=" . $id;
			pmb_mysql_query($query);

			// Suppression des résas
			$requete = "DELETE FROM resa WHERE resa_idnotice=".$id;
			pmb_mysql_query($requete);

			// Suppression des résas planifiées
			$requete = "DELETE FROM resa_planning WHERE resa_idnotice=".$id;
			pmb_mysql_query($requete);

			// Suppression des transferts_demande
			$requete = "DELETE FROM transferts_demande using transferts_demande, transferts WHERE num_transfert=id_transfert and num_notice=".$id;
			pmb_mysql_query($requete);
			// Suppression des transferts
			$requete = "DELETE FROM transferts WHERE num_notice=".$id;
			pmb_mysql_query($requete);

			//si intégré depuis une source externe, on supprime aussi la référence
			$query="delete from notices_externes where num_notice=".$id;
			@pmb_mysql_query($query);

			$req="delete from notices_authperso where notice_authperso_notice_num=".$id;
			pmb_mysql_query($req);

			//Suppression des emprises liées à la notice
			$req = "select map_emprise_id from map_emprises where map_emprise_type=11 and map_emprise_obj_num=".$id;
			$result = pmb_mysql_query($req);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$query="delete from map_emprises where map_emprise_obj_num=".$id." and map_emprise_type=11";
				pmb_mysql_query($query);
				$req_areas="delete from map_hold_areas where type_obj=11 and id_obj=".$row->map_emprise_id;
				pmb_mysql_query($req_areas);
			}
			$query = "update docwatch_items set item_num_notice=0 where item_num_notice = ".$id;
			pmb_mysql_query($query);

			//Suppression de la reference a la notice dans les veilles
			$requete = "UPDATE docwatch_items set item_num_notice = 0 where item_num_notice=".$id;
			@pmb_mysql_query($requete);

			// Nettoyage indexation concepts
			$index_concept = new index_concept($id, TYPE_NOTICE);
			$index_concept->delete();

			scan_requests::clean_scan_requests_on_delete_record($id);

			if($sphinx_active){
				$si = self::get_sphinx_indexer();
				$si->deleteIndex($id);
			}
			if ($pmb_ark_activate) {
			    $arkEntity = ArkEntityPmb::getEntityClassFromType(TYPE_NOTICE, $id);
    			$arkEntity->markAsDeleted();
			}
		}

		// Clean des vedettes
		public static function delete_vedette_links($id) {
			$id_vedettes=array();
			$rqt_responsability = 'select id_responsability, responsability_type from responsability where responsability_notice="'.$id.'" ';
			$res_responsability=pmb_mysql_query($rqt_responsability);
			if (pmb_mysql_num_rows($res_responsability)) {
				while($r=pmb_mysql_fetch_object($res_responsability)){
					$object_id=$r->id_responsability;
					$type_aut=$r->responsability_type;
					$id_vedette=0;
					switch($type_aut){
						case 0:
							$id_vedette=vedette_link::delete_vedette_link_from_object(new vedette_composee(0,static::$vedette_composee_config_filename), $object_id, TYPE_NOTICE_RESPONSABILITY_PRINCIPAL);
							break;
						case 1:
							$id_vedette=vedette_link::delete_vedette_link_from_object(new vedette_composee(0,static::$vedette_composee_config_filename), $object_id,TYPE_NOTICE_RESPONSABILITY_AUTRE);
							break;
						case 2:
							$id_vedette=vedette_link::delete_vedette_link_from_object(new vedette_composee(0,static::$vedette_composee_config_filename), $object_id,TYPE_NOTICE_RESPONSABILITY_SECONDAIRE);
							break;
					}
					if($id_vedette)$id_vedettes[]=$id_vedette;
				}
			}
			return $id_vedettes;
		}

		//sauvegarde un ensemble de notices dans un entrepot agnostique a partir d'un tableau d'ids de notices
		public static function save_to_agnostic_warehouse($notice_ids=array(),$source_id=0,$keep_expl=0, $copy_expl = false) {
			global $base_path,$class_path,$include_path;

			if (is_array($notice_ids) && count($notice_ids) && intval($source_id)) {

				$export_params=array(
					'genere_lien'	=>1,
					'notice_mere'	=>1,
					'notice_fille'	=>1,
					'mere'			=>0,
					'fille'			=>0,
					'bull_link'		=>1,
					'perio_link'	=>1,
					'art_link'		=>0,
					'bulletinage'	=>0,
					'notice_perio'	=>0,
					'notice_art'	=>0
				);

				require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
				$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
				$source_params = $conn->get_source_params($source_id);
				$export_params['docnum']=1;
				$export_params['docnum_rep']=$source_params['REP_UPLOAD'];
				$notice_ids=array_unique($notice_ids);
				$e=new export($notice_ids);
				$records=array();
				do{
				    $nn = $e->get_next_notice('',array(),array(),$keep_expl,$export_params, $copy_expl);
					if ($e->notice) $records[] = $e->xml_array;
				} while($nn);
				$conn->rec_records_from_xml_array($records,$source_id);
			}
		}


		// Donne les id des notices liés a une notice
		public static function get_list_child($notice_id,$liste=array()){
			$tab=array();
			$liste[]=$notice_id;
			$notice_relations = notice_relations_collection::get_object_instance($notice_id);
			$childs = $notice_relations->get_childs();
			foreach ($childs as $childs_relations) {
				foreach ($childs_relations as $child) {
					if(!in_array($child->get_linked_notice(),$liste)) {
						$liste[]=$child->get_linked_notice();
						$tab_tmp=static::get_list_child($child->get_linked_notice(),$liste);
						$tab=array_merge($tab,$tab_tmp);
					}else {
						// cas de rebouclage d'une fille sur une mère: donc on sort.
						$tab[]=$notice_id;
						return	$tab;
					}
				}
			}
			$tab[]=$notice_id;
			return	$tab;
		}

		// Donne les id des notices de bulletins associées à la notice de pério
		public static function get_list_bulletin_notice($notice_id){
		    $tab=array();
		    $query = "SELECT num_notice FROM bulletins WHERE bulletin_notice = ".$notice_id;
		    $result = pmb_mysql_query($query);
		    if($result && pmb_mysql_num_rows($result)) {
		        while ($row = pmb_mysql_fetch_object($result)) {
		            $tab[]=$row->num_notice;
		        }
		    }
		    return	$tab;
		}

		// Donne les id des notices d'articles associées à la notice de pério
		public static function get_list_analysis($id){
			$id = intval($id);
		    $tab=array();
		    $query = "SELECT analysis_notice FROM analysis JOIN bulletins ON bulletins.bulletin_id = analysis.analysis_bulletin WHERE bulletin_notice = ".$id;
		    $result = pmb_mysql_query($query);
		    if($result && pmb_mysql_num_rows($result)) {
		        while ($row = pmb_mysql_fetch_object($result)) {
		            $tab[]=$row->analysis_notice;
		        }
		    }
		    return	$tab;
		}

		public static function majNotices_clean_tags($notice=0,$with_reindex=true) {
			$requete = "select index_l ,notice_id from notices where index_l is not null and index_l!='' ";
			if($notice) {
				$requete.= " and notice_id = $notice ";
			}
			$res = pmb_mysql_query($requete);
			if($res && pmb_mysql_num_rows($res)){
				while (($r = pmb_mysql_fetch_object($res))) {
					$val=clean_tags($r->index_l);
					$requete = "update notices set index_l='".addslashes($val)."', update_date=update_date where notice_id=".$r->notice_id;
					pmb_mysql_query($requete);
					if($with_reindex && ($val != $r->index_l)){//On réindexe la notice si le nettoyage à réalisé des changements
						static::majNoticesTotal($r->notice_id);
					}
				}
			}
		}

		// Fonction statique pour la creation / maj d'un n-uplet dans la table "notices_global_index" lors de la creation ou mise a jour d'une notice.
		public static function majNoticesGlobalIndex($notice, $NoIndex = 1) {
			if(!static::$deleted_index) {
				pmb_mysql_query("delete from notices_global_index where num_notice = ".$notice." AND no_index = ".$NoIndex);
			}

			$titres = pmb_mysql_query("select index_serie, tnvol, index_wew, index_sew, index_l, index_matieres, n_gen, n_contenu, n_resume, index_n_gen, index_n_contenu, index_n_resume, eformat, niveau_biblio from notices where notice_id = ".$notice);
			$mesNotices = pmb_mysql_fetch_assoc($titres);
			pmb_mysql_free_result($titres);

			$tit = $mesNotices['index_wew'];
			$indTit = $mesNotices['index_sew'];
			$indMat = $mesNotices['index_matieres'];
			$indL = $mesNotices['index_l'];
			$indResume = $mesNotices['index_n_resume'];
			$indGen = $mesNotices['index_n_gen'];
			$indContenu = $mesNotices['index_n_contenu'];
			$resume = $mesNotices['n_resume'];
			$gen = $mesNotices['n_gen'];
			$contenu = $mesNotices['n_contenu'];
			$indSerie = $mesNotices['index_serie'];
			$tvol = $mesNotices['tnvol'];
			$eformatlien = $mesNotices['eformat'];
		   	$infos_notice_global=' '.$tvol.' '.$tit.' '.$resume.' '.$gen.' '.$contenu.' '.$indL.' ';
		   	$infos_notice_global_index=' '.$indSerie.' '.$indTit.' '.$indResume.' '.$indGen.' '.$indContenu.' '.$indMat.' ';

		   	$infos_global='';

		   	// Authors :
		   	$auteurs = pmb_mysql_query("select author_id, author_type, author_name, author_rejete, author_date, author_lieu,author_ville,author_pays,author_numero,author_subdivision, index_author from authors, responsability WHERE responsability_author = author_id AND responsability_notice = $notice");
		   	$numA = pmb_mysql_num_rows($auteurs);
		   	if($numA) {
			   	if(!isset(static::$aut_pperso_instance['author'])) {
			   		static::$aut_pperso_instance['author'] = new aut_pperso("author");
			   	}

			   	for($j=0;$j < $numA; $j++) {
			   		$mesAuteurs = pmb_mysql_fetch_assoc($auteurs);
			   		$infos_global.=
			   			$mesAuteurs['author_name'].' '.
				   		$mesAuteurs['author_rejete'].' '.
				   		$mesAuteurs['author_lieu'].' '.
				   		$mesAuteurs['author_ville'].' '.
				   		$mesAuteurs['author_pays'].' '.
				   		$mesAuteurs['author_numero'].' '.
				   		$mesAuteurs['author_subdivision'].' ';

			   		if ($mesAuteurs['author_type'] == "72") {
			   			$infos_global.= ' '.$mesAuteurs['author_date'].' ';
			   		}

				   	$mots_perso = static::$aut_pperso_instance['author']->get_fields_recherche($mesAuteurs['author_id']);
				   	if($mots_perso) {
				   		$infos_global.= $mots_perso.' ';
				   	}
			   	}

			   	$mesAuteurs = null;
			   	pmb_mysql_free_result($auteurs);
		   	}

		   	// Nom du periodique
			//cas d'un article
		   	if($mesNotices['niveau_biblio'] == 'a'){
			   	$temp = pmb_mysql_query("select bulletin_notice, bulletin_titre, index_titre, index_wew, index_sew from analysis, bulletins, notices  WHERE analysis_notice=".$notice." and analysis_bulletin = bulletin_id and bulletin_notice=notice_id");
			   	$numP = pmb_mysql_num_rows($temp);
			   	if ($numP) {
					// La notice appartient a un periodique, on selectionne le titre de periodique :
			   		$mesTemp = pmb_mysql_fetch_assoc($temp);
				  	$infos_global.= $mesTemp['index_wew'].' '.$mesTemp['bulletin_titre'].' '.$mesTemp['index_titre'].' ';

				  	$mesTemp = null;
				  	pmb_mysql_free_result($temp);
			   	}
			   //cas d'un bulletin
		   	}else if ($mesNotices['niveau_biblio'] == 'b'){
		   		$temp = pmb_mysql_query("select serial.index_wew from notices join bulletins on bulletins.num_notice = notices.notice_id join notices as serial on serial.notice_id = bulletins.bulletin_notice where notices.notice_id = ".$notice);
		   		$numP = pmb_mysql_num_rows($temp);
			   	if ($numP) {
					// La notice appartient a un periodique, on selectionne le titre de periodique :
			   		$mesTemp = pmb_mysql_fetch_assoc($temp);
				  	$infos_global.= $mesTemp['index_wew'].' ';

				  	$mesTemp = null;
				   	pmb_mysql_free_result($temp);
			   	}
		   	}


		   	// Categories :
		   	$noeud = pmb_mysql_query("select notices_categories.num_noeud as categ_id, libelle_categorie from notices_categories,categories where notcateg_notice = ".$notice." and notices_categories.num_noeud=categories.num_noeud order by ordre_categorie");
		   	$numNoeuds = pmb_mysql_num_rows($noeud);
		   	if($numNoeuds) {
		   		if(!isset(static::$aut_pperso_instance['categ'])) {
		   			static::$aut_pperso_instance['categ'] = new aut_pperso("categ");
		   		}
			   	// Pour chaque noeud trouve on cherche les noeuds parents et les noeuds fils :
			   	for($j=0;$j < $numNoeuds; $j++) {
			   		// On met a jour la table notices_global_index avec le noeud trouve:
				 	$mesNoeuds = pmb_mysql_fetch_assoc($noeud);
				   	$infos_global.= $mesNoeuds['libelle_categorie'].' ';

				 	$mots_perso = static::$aut_pperso_instance['categ']->get_fields_recherche($mesNoeuds['categ_id']);
				 	if($mots_perso) {
				 		$infos_global.= $mots_perso.' ';
				 	}
			   	}

			   	$mesNoeuds = null;
			   	pmb_mysql_free_result($noeud);
		   	}

		   	// Sous-collection :
		   	$subColls = pmb_mysql_query("select subcoll_id, sub_coll_name, index_sub_coll from notices, sub_collections WHERE subcoll_id = sub_coll_id AND notice_id = ".$notice);
		   	$numSC = pmb_mysql_num_rows($subColls);
		   	if($numSC) {
		   		if(!isset(static::$aut_pperso_instance['subcollection'])) {
		   			static::$aut_pperso_instance['subcollection'] = new aut_pperso("subcollection");
		   		}
			   	for($j=0;$j < $numSC; $j++) {
			   		$mesSubColl = pmb_mysql_fetch_assoc($subColls);
			   		$infos_global.=$mesSubColl['index_sub_coll'].' '.$mesSubColl['sub_coll_name'].' ';

			   		$mots_perso = static::$aut_pperso_instance['subcollection']->get_fields_recherche($mesSubColl['subcoll_id']);
				 	if($mots_perso) {
				 		$infos_global.= $mots_perso.' ';
				 		$mots_perso = null;
				 	}
			   	}

			   	$mesSubColl = null;
			   	pmb_mysql_free_result($subColls);
		   	}

		   	// Indexation numerique :
		   	$indexNums = pmb_mysql_query("select indexint_id, indexint_name, indexint_comment from notices, indexint WHERE indexint = indexint_id AND notice_id = ".$notice);
		   	$numIN = pmb_mysql_num_rows($indexNums);
		   	if($numIN) {
		   		if(!isset(static::$aut_pperso_instance['indexint'])) {
		   			static::$aut_pperso_instance['indexint'] = new aut_pperso("indexint");
		   		}
			   	for($j=0;$j < $numIN; $j++) {
			   		$mesindexNums = pmb_mysql_fetch_assoc($indexNums);
			   		$infos_global.=$mesindexNums['indexint_name'].' '.$mesindexNums['indexint_comment'].' ';

			   		$mots_perso = static::$aut_pperso_instance['indexint']->get_fields_recherche($mesindexNums['indexint_id']);
				 	if($mots_perso) {
				 		$infos_global.= $mots_perso.' ';
				 	}
			   	}

			   	$indexNums = null;
			   	pmb_mysql_free_result($indexNums);
		   	}

		   	// Collection :
		   	$Colls = pmb_mysql_query("select coll_id, collection_name ,collection_issn from notices, collections WHERE coll_id = collection_id AND notice_id = ".$notice);
		   	$numCo = pmb_mysql_num_rows($Colls);
		   	if($numCo) {
		   		if(!isset(static::$aut_pperso_instance['collection'])) {
		   			static::$aut_pperso_instance['collection'] = new aut_pperso("collection");
		   		}
			   	for($j=0;$j < $numCo; $j++) {
			   		$mesColl = pmb_mysql_fetch_assoc($Colls);
			   		$infos_global.= $mesColl['collection_name'].' '.$mesColl['collection_issn'].' ';

			   		$mots_perso = static::$aut_pperso_instance['collection']->get_fields_recherche($mesColl['coll_id']);
				 	if($mots_perso) {
				 		$infos_global.= $mots_perso.' ';
				 		$mots_perso = null;
				 	}
			   	}

			   	$mesColl = null;
			   	pmb_mysql_free_result($Colls);
		   	}

		   	// Editeurs :
		   	$editeurs = pmb_mysql_query("select ed_id, ed_name from notices, publishers WHERE (ed1_id = ed_id OR ed2_id = ed_id) AND notice_id = ".$notice);
		   	$numE = pmb_mysql_num_rows($editeurs);
		   	if($numE) {
		   		if(!isset(static::$aut_pperso_instance['publisher'])) {
		   			static::$aut_pperso_instance['publisher'] = new aut_pperso("publisher");
		   		}
			   	for($j=0;$j < $numE; $j++) {
			   		$mesEditeurs = pmb_mysql_fetch_assoc($editeurs);
			   		$infos_global.= $mesEditeurs['ed_name'].' ';

			   		$mots_perso = static::$aut_pperso_instance['publisher']->get_fields_recherche($mesEditeurs['ed_id']);
				 	if($mots_perso) {
				 		$infos_global.= $mots_perso.' ';
				 	}
			   	}

			   	$mesEditeurs = null;
			   	pmb_mysql_free_result($editeurs);
		   	}


			// Titres Uniformes :
		   	$tu = pmb_mysql_query("select tu_id, ntu_titre, tu_name, tu_tonalite, tu_sujet, tu_lieu, tu_contexte from notices_titres_uniformes,titres_uniformes WHERE tu_id=ntu_num_tu and ntu_num_notice=".$notice);
		   	$numtu = pmb_mysql_num_rows($tu);
		   	if($numtu){
		   		if(!isset(static::$aut_pperso_instance['tu'])) {
		   			static::$aut_pperso_instance['tu'] = new aut_pperso("tu");
		   		}
		   		for($j=0;$j < $numtu; $j++) {
		   			$mesTu = pmb_mysql_fetch_assoc($tu);
		   			$infos_global.=$mesTu['ntu_titre'].' '.$mesTu['tu_name'].' '.$mesTu['tu_tonalite'].' '.$mesTu['tu_sujet'].' '.$mesTu['tu_lieu'].' '.$mesTu['tu_contexte'].' ';
		   			$mots_perso = static::$aut_pperso_instance['tu']->get_fields_recherche($mesTu['tu_id']);
		   			if($mots_perso) {
		   				$infos_global.= $mots_perso.' ';
		   			}
		   		}

		   		$mesTu = null;
			   	pmb_mysql_free_result($tu);
		   	}

			// indexer les cotes des etat des collections :
			$p_perso = static::get_parametres_perso_class("collstate");
			$coll = pmb_mysql_query("select collstate_id, collstate_cote from collections_state WHERE id_serial=".$notice);
		   	$numcoll = pmb_mysql_num_rows($coll);
		   	if($numcoll){
			   	for($j=0;$j < $numcoll; $j++) {
			   		$mescoll = pmb_mysql_fetch_assoc($coll);
			   		$infos_global.=$mescoll['collstate_cote'].' ';
			   		// champ perso cherchable
					$mots_perso=$p_perso->get_fields_recherche($mescoll['collstate_id']);
					if($mots_perso) {
						$infos_global.= $mots_perso.' ';
					}
			   	}

			   	$mescoll = null;
			   	pmb_mysql_free_result($coll);
		   	}

		   	// Nomenclature
		   	global $pmb_nomenclature_activate;
		   	if($pmb_nomenclature_activate){
		   		$mots=nomenclature_record_ui::get_index($notice);
		   		$infos_global.= $mots.' ';
		   	}

		    // champ perso cherchable
		   	$p_perso = static::get_parametres_perso_class("notices");
			$mots_perso=$p_perso->get_fields_recherche($notice);
			if($mots_perso) {
				$infos_global.= $mots_perso.' ';
			}

			// champs des authperso
			$auth_perso=new authperso_notice($notice);
			$mots_authperso=$auth_perso->get_fields_search();
			if($mots_authperso) {
				$infos_global.= $mots_authperso.' ';
			}

			$infos_global_index = $infos_notice_global_index.strip_empty_words($infos_global).' ';
			$infos_global = $infos_notice_global.$infos_global;

			// flux RSS éventuellement
			$eformat=array();
			$eformat = explode(' ', $eformatlien) ;
			if ($eformat[0]=='RSS' && $eformat[3]=='1') {
				$flux=strip_tags(affiche_rss($notice)) ;
				$infos_global_index.= strip_empty_words($flux).' ';
			}
			pmb_mysql_query("insert into notices_global_index SET num_notice=".$notice.",no_index =".$NoIndex.", infos_global='".addslashes($infos_global)."', index_infos_global='".addslashes($infos_global_index)."'" );
		}


		// Fonction statique pour la creation / maj d'un n-uplet dans la table "notices_mots_global_index" lors de la creation ou mise a jour d'une notice.
		public static function majNoticesMotsGlobalIndex($notice, $datatype='all') {
			global $include_path;
			global $lang;
			global $indexation_lang;
			global $sphinx_active;

			if(!isset(static::$indexation_record)) {
				static::$indexation_record = new indexation_record($include_path."/indexation/notices/champs_base.xml", 'notices');
			}
			static::$indexation_record->set_deleted_index(static::$deleted_index);
			static::$indexation_record->maj($notice, $datatype);
		}

		public static function get_sphinx_indexer(){
			if(!self::$sphinx_indexer){
				self::$sphinx_indexer = new sphinx_records_indexer();
			}
			return self::$sphinx_indexer;
		}

		//Fonction statique pour la maj des champs index de la notice
		public static function majNotices($notice){
			global $pmb_keyword_sep;
			if($notice){
				$query = pmb_mysql_query("SELECT notice_id,tparent_id,tit1,tit2,tit3,tit4,index_l, n_gen, n_contenu, n_resume, tnvol, indexation_lang FROM notices WHERE notice_id='".$notice."'");
				if(pmb_mysql_num_rows($query)) {
					//Nettoyage des mots clès
					static::majNotices_clean_tags($notice,false);
					$row = pmb_mysql_fetch_object($query);
					// titre de série
					if ($row->tparent_id) {
						$tserie = new serie($row->tparent_id);
						$ind_serie = ' '.strip_empty_words($tserie->name).' ';
					} else {
						$ind_serie = '';
					}
					$ind_wew = $ind_serie." ".$row->tnvol." ".$row->tit1." ".$row->tit2." ".$row->tit3." ".$row->tit4 ;
					$ind_sew = strip_empty_words($ind_wew) ;
					$row->index_l ? $ind_matieres = ' '.strip_empty_words(str_replace($pmb_keyword_sep," ",$row->index_l)).' ' : $ind_matieres = '';
					$row->n_gen ? $ind_n_gen = ' '.strip_empty_words($row->n_gen).' ' : $ind_n_gen = '';
					$row->n_contenu ? $ind_n_contenu = ' '.strip_empty_words($row->n_contenu).' ' : $ind_n_contenu = '';
					$row->n_resume ? $ind_n_resume = ' '.strip_empty_words($row->n_resume).' ' : $ind_n_resume = '';


					$req_update = "UPDATE notices";
					$req_update .= " SET index_wew='".addslashes($ind_wew)."'";
					$req_update .= ", index_sew=' ".addslashes($ind_sew)." '";
					$req_update .= ", index_serie='".addslashes($ind_serie)."'";
					$req_update .= ", index_n_gen='".addslashes($ind_n_gen)."'";
					$req_update .= ", index_n_contenu='".addslashes($ind_n_contenu)."'";
					$req_update .= ", index_n_resume='".addslashes($ind_n_resume)."'";
					$req_update .= ", index_matieres='".addslashes($ind_matieres)."'";
					$req_update .= ", update_date=update_date";
					$req_update .= " WHERE notice_id=$row->notice_id ";
					pmb_mysql_query($req_update);

					pmb_mysql_free_result($query);
					// Mise à jour des vedettes composées contenant cette notice
					vedette_composee::update_vedettes_built_with_element($notice, TYPE_NOTICE);
				}
			}
		}

		public static function indexation_prepare($notice){
			global $lang,$include_path;
			global $pmb_indexation_lang;
			global $empty_word;
			global $indexation_lang;

			$info = array();
			$info['flag_lang_change']=0;

			$notice = intval($notice);
			if(!$notice)  {
				return;
			}

			$query = pmb_mysql_query("SELECT indexation_lang FROM notices WHERE notice_id='".$notice."'");
			if(pmb_mysql_num_rows($query)) {
				$row = pmb_mysql_fetch_object($query);
				pmb_mysql_free_result($query);

				$indexation_lang = $row->indexation_lang;
				if($indexation_lang && $indexation_lang!= $lang){
					$info['save_pmb_indexation_lang']=$pmb_indexation_lang;
					$info['save_lang']=$lang;
					$info['flag_lang_change']=1;

					$pmb_indexation_lang=$indexation_lang;
					$lang=$indexation_lang;
					$empty_word=array();
					include("$include_path/marc_tables/".$lang."/empty_words");
				}else{
					//$indexation_lang=$lang;
				}
			}
			return $info;
		}

		public static function indexation_restaure($info){
			global $lang,$include_path;
			global $pmb_indexation_lang;
			global $empty_word;

			if($info['flag_lang_change']){
				// restauration de l'environemment
				$pmb_indexation_lang=$info['save_pmb_indexation_lang'];
				$lang=$info['save_lang'];
				$empty_word=array();
				include("$include_path/marc_tables/$lang/empty_words");
			}
			//$pmb_indexation_lang="";
			//$flag_lang_change=0;
		}

		//Met à jour toutes les informations liées une notice
		public static function majNoticesTotal($notice){
			$info=static::indexation_prepare($notice);
			indexation_stack::push($notice, TYPE_NOTICE);
			static::indexation_restaure($info);
		}

		public static function getAutomaticTu($notice) {
			global $charset,$opac_enrichment_bnf_sparql;

			if (!$opac_enrichment_bnf_sparql) return 0;

			$requete="select code, responsability_author from notices left join responsability on (responsability_notice=$notice and responsability_type=0)
			left join notices_titres_uniformes on notice_id=ntu_num_notice where notice_id=$notice and ntu_num_notice is null";
			$resultat=pmb_mysql_query($requete);
			if ($resultat && pmb_mysql_num_rows($resultat)) {
				$code=pmb_mysql_result($resultat,0,0);
				$id_author=pmb_mysql_result($resultat,0,1);
			} else $code="";
			$id_tu=0;
			if (isISBN($code)) {
				$uri=titre_uniforme::get_data_bnf_uri($code);
				if ($uri) {
					//Recherche du titre uniforme déjà existant ?
					$requete="select tu_id from titres_uniformes where tu_databnf_uri='".addslashes($uri)."'";
					$resultat=pmb_mysql_query($requete);
					if ($resultat && pmb_mysql_num_rows($resultat))
						$id_tu=pmb_mysql_result($resultat,0,0);
					else {
						//Interrogation de data.bnf pour obtenir les infos !
						$configbnf = array(
								'remote_store_endpoint' => 'http://data.bnf.fr/sparql'
						);
						$storebnf = ARC2::getRemoteStore($configbnf);

						$sparql = "
						PREFIX dc: <http://purl.org/dc/terms/>

						SELECT ?title ?date ?description WHERE {
						  <".$uri."> dc:title ?title.
						  OPTIONAL { <".$uri."> dc:date ?date. }
						  OPTIONAL { <".$uri."> dc:description ?description. }
						}";
						$rows = $storebnf->query($sparql, 'rows');
						// On vérifie qu'il n'y a pas d'erreur sinon on stoppe le programme et on renvoi une chaine vide
						$err = $storebnf->getErrors();
						if (!$err) {
							$value=array(
									"name"=>encoding_normalize::charset_normalize($rows[0]['title'],"utf-8"),
									"num_author"=>$id_author,
									"date"=>encoding_normalize::charset_normalize($rows[0]['date'],"utf-8"),
									"comment"=>encoding_normalize::charset_normalize($rows[0]['description'],"utf-8"),
									"databnf_uri"=>$uri
							);
							$id_tu=titre_uniforme::import($value);
						}
					}
				}
			}
			if ($id_tu) {
				$titres_uniformes=array(array("num_tu"=>$id_tu));
				$ntu=new tu_notice($notice);
				$ntu->update($titres_uniformes);
			}
			return $id_tu;
		}

		public function get_records_list_ui(){
			global $quoi;
			if(!$this->records_list_ui){
				$tab = null;
				foreach($this->record_tabs->get_tabs() as $current_tab){
				    if (!$tab && $current_tab->can_display_tab()) {
						$tab = $current_tab;
					}
					if(($current_tab->get_name() == $quoi) && $current_tab->can_display_tab()){
						$tab = $current_tab;
						break;
					}
				}
				if ($tab) {
					$quoi = $tab->get_name();
					switch($tab->get_content_type()){
						case 'records':
							$this->records_list_ui = new elements_records_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
							break;
						case 'authorities':
							$this->records_list_ui = new elements_authorities_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
							break;
						case 'docnums':
							$this->records_list_ui = new elements_docnums_list_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
							break;
						case 'graph':
							$this->records_list_ui = new elements_graph_ui($tab->get_contents(), $tab->get_nb_results(), $tab->is_mixed(), $tab->get_groups(), $tab->get_nb_filtered_results());
							break;
					}
				}
			}
			return $this->records_list_ui;
		}

		public function set_record_tabs($record_tabs){
			$this->record_tabs = $record_tabs;
		}

		public function get_nomenclature_record_formations() {
			global $pmb_nomenclature_activate;

			if ($pmb_nomenclature_activate && !$this->nomenclature_record_formations) {
				$this->nomenclature_record_formations = new nomenclature_record_formations($this->id);
			}
			return $this->nomenclature_record_formations;
		}

		public static function manage_access_rights($id, $create=false){
		    global $gestion_acces_active;
		    global $gestion_acces_empr_notice;
		    global $gestion_acces_user_notice;
		    global $res_prf;
		    global $prf_rad;
		    global $r_rad;
		    global $chk_rights;
		    global $class_path;

    		if ($gestion_acces_active==1 && $id) {
            	$ac = new acces();
                //droits d'acces utilisateur/notice (modification)
                if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
                	$dom_1= $ac->setDomain(1);
                }
    		    //traitement des droits acces user_notice
    		    if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
    		        if (!$create) {
    		            $dom_1->storeUserRights(1, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
    		        } else {
    		            $dom_1->storeUserRights(0, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
    		        }
    		    }
    		    //traitement des droits acces empr_notice
    		    if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
    		        $dom_2= $ac->setDomain(2);
    		        if (!$create) {
    		            $dom_2->storeUserRights(1, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
    		        } else {
    		            $dom_2->storeUserRights(0, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
    		        }
    		    }
    		}
		}

		public static function calc_access_rights($id){
		    global $gestion_acces_active;
		    global $gestion_acces_empr_notice;
		    global $gestion_acces_user_notice;

    		if ($gestion_acces_active==1 && $id) {
            	$ac = new acces();
                //droits d'acces utilisateur/notice (modification)
                if ($gestion_acces_user_notice==1) {
                	$dom_1= $ac->setDomain(1);
					$dom_1->applyRessourceRights($id);
    		    }
    		    //traitement des droits acces empr_notice
    		    if ($gestion_acces_empr_notice==1) {
    		        $dom_2= $ac->setDomain(2);
    		        $dom_2->applyRessourceRights($id);
    		    }
    		}
		}



		public static function get_icon($id) {
			global $icon_list_instance;
			if(!isset($icon_list_instance)) {
				$icon_list_instance=new marc_list("icondoc");
			}
			$requete="select concat(niveau_biblio,typdoc) as i from notices where notice_id=".$id;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$icon="./images/".$icon_list_instance->table[pmb_mysql_result($resultat,0,0)];
			} else $icon='./images/icon_a_16x16.gif';
			return $icon;
		}

		public static function get_gestion_link($notice_id) {
		    $notice_id = intval($notice_id);
			$requete="select niveau_biblio, serie_name, tnvol, tit1, code from notices left join series on serie_id=tparent_id where notice_id=".$notice_id;
			$fetch = pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($fetch)) {
				$r = pmb_mysql_fetch_object($fetch);
				if($r->niveau_biblio == 's'){
					// périodique
					$link = './catalog.php?categ=serials&sub=view&serial_id='.$notice_id;
				}elseif($r->niveau_biblio == 'b') {
					// notice de bulletin
					$query = 'select bulletin_id, bulletin_notice from bulletins where num_notice = '.$notice_id;
					$result = pmb_mysql_query($query);
					if($result && pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$link = './catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=' . intval($row->bulletin_id);
					}
				}elseif($r->niveau_biblio == 'a') {
				    // notice de bulletin
				    $query = 'select analysis_bulletin from analysis where analysis_notice = '.$notice_id;
				    $result = pmb_mysql_query($query);
				    if($result && pmb_mysql_num_rows($result)){
				        $analysis = pmb_mysql_result($result, '0');
				    }
				    $link = './catalog.php?categ=serials&sub=view&sub=bulletinage&action=view&bul_id=' . intval($analysis) . '&art_to_show='.$notice_id;
				}else{
					// notice de monographie
					$link = self::get_permalink($notice_id);
				}
				return $link;
			}
			return '';
		}

		public function get_id(){
			return $this->id;
		}

		/**
		 * Retourne les identifiants de concepts associés à la notice
		 */
		public function get_concepts_ids(){
			if (!isset($this->concepts_ids)) {
				$this->concepts_ids = array();
				$vedette_composee_found = vedette_composee::get_vedettes_built_with_element($this->id, TYPE_NOTICE);
				foreach($vedette_composee_found as $vedette_id){
					$this->concepts_ids[] = vedette_composee::get_object_id_from_vedette_id($vedette_id, TYPE_CONCEPT_PREFLABEL);
				}
			}
			return $this->concepts_ids;
		}

		public function get_entity_type(){
			return 'record';
		}

		public static function set_deleted_index($deleted_index) {
			static::$deleted_index = $deleted_index;
		}

		protected static function get_parametres_perso_class($type){
			if(!isset(self::$parametres_perso[$type])){
				self::$parametres_perso[$type] = new parametres_perso($type);
			}
			return self::$parametres_perso[$type];
		}

		public static function set_controller($controller) {
			static::$controller = $controller;
		}

		protected static function format_url($url='') {
			global $base_path;

			if(isset(static::$controller) && is_object(static::$controller)) {
				$url_base = static::$controller->get_url_base();
				if(strpos($url_base, '?') === false) {
					$url_base .= '?';
				}
				if((substr($url, 0, 1) == '&') && (substr($url_base, -1) == '&')) {
					return $url_base.substr($url, 1);
				} else {
					return $url_base.$url;
				}
			} else {
				if(substr($url, 0, 1) == '&') {
					return $base_path.'/catalog.php?'.substr($url, 1);
				} else {
					return $base_path.'/catalog.php?'.$url;
				}
			}
		}

		//Récupération de la no_image
		public static function get_picture_url_no_image($niveau_biblio, $typdoc) {
		    //On passe le paramètre $use_opac_url_base de get_url_icon à 1 car pas de no_image en gestion
			$picture_url = get_url_icon("no_image_".$niveau_biblio.$typdoc.".jpg", 1);
			if(!file_exists($picture_url)) {
				$picture_url = get_url_icon("no_image_".$niveau_biblio.".jpg", 1);
				if(!file_exists($picture_url)) {
					$picture_url = get_url_icon("no_image.jpg", 1);
				}
			}
			return $picture_url;
		}

		/**
		 * magic getter
		 * @param string $name
		 */
		public function __get($name) {
			$return = $this->look_for_attribute_in_class($this, $name);
			return $return;
		}

		private function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
			if (is_object($class) && isset($class->{$attribute})) {
				return $class->{$attribute};
			} else if (method_exists($class, $attribute)) {
				return call_user_func_array(array($class, $attribute), $parameters);
			} else if (method_exists($class, "get_".$attribute)) {
				return call_user_func_array(array($class, "get_".$attribute), $parameters);
			} else if (method_exists($class, "is_".$attribute)) {
				return call_user_func_array(array($class, "is_".$attribute), $parameters);
			}
			return null;
		}

		public function get_detail() {
			if (isset($this->detail)) {
				return $this->detail;
			}
			$this->detail = '';
			switch ($this->biblio_level) {
				case 'm' :
				case 'b' :
					// notice de monographie ou de bulletin
					global $maglobal;
					$maglobal = true;
					$display = new mono_display($this->id, 6);
					$maglobal = false;

					$this->detail = $display->isbd;
					break;
				case 's' :
				case 'a' :
					// on a affaire à un périodique ou à un article
					$display = new serial_display($this->id);
					$this->detail = $display->isbd;
					break;
			}
			return $this->detail;
		}

		public function get_detail_tooltip($target_node_id) {
			$html = '
			<script type="text/javascript">
				require(["dijit/Tooltip", "dojo/dom", "dojo/on", "dojo/mouse", "dojo/domReady!"], function(Tooltip, dom, on, mouse) {
					var node = dom.byId("'.$target_node_id.'");
					on(node, mouse.enter, function(){
						Tooltip.show("'.addslashes(str_replace(array("\n", "\t", "\r"), '', $this->get_detail())).'", node);
						on.once(node, mouse.leave, function(){
							Tooltip.hide(node);
						});
					});
				})
			</script>';
			return $html;
		}

		protected function get_display_mode_selector($name) {
			return selector_model::get_instance($name)->get_display_mode('record');
		}

		protected function is_force_dialog($name) {
			$display_mode = $this->get_display_mode_selector($name);
			if($display_mode == 'dialog') {
				return 1;
			}
			return 0;
		}

		protected function is_force_popup($name) {
			$display_mode = $this->get_display_mode_selector($name);
			if($display_mode == 'popup') {
				return 1;
			}
			return 0;
		}

		public static function update_index($id, $datatype = 'all') {
		    indexation_stack::push($id, TYPE_NOTICE, $datatype);
		}

		public static function get_notice_explnum_list($id) {
			$explnums = array();
			if(intval($id)) {
				$query = "SELECT explnum_id FROM explnum WHERE explnum_notice = $id";
				$result = pmb_mysql_query($query);
				while($row = pmb_mysql_fetch_assoc($result)) {
					$explnums[] = $row['explnum_id'];
				}
			}
			return $explnums;
		}
	}