<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_datas.class.php,v 1.175.2.5.2.3 2025/03/25 10:21:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkRecord;
use Pmb\Ark\Models\ArkModel;
use Pmb\Ark\Entities\ArkBulletin;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Helper\UrlEntities;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $base_path, $class_path;

require_once $class_path."/acces.class.php";
require_once $class_path."/map/map_objects_controler.class.php";
require_once $class_path."/map_info.class.php";
require_once $class_path."/map/map_locations_controler.class.php";
require_once $class_path."/parametres_perso.class.php";
require_once $class_path."/tu_notice.class.php";
require_once $class_path."/marc_table.class.php";
require_once $class_path."/collstate.class.php";
require_once $class_path."/enrichment.class.php";
require_once $class_path."/skos/skos_concepts_list.class.php";
require_once $class_path."/authorities_collection.class.php";
require_once $class_path."/avis.class.php";
require_once $class_path."/authority.class.php";
require_once $class_path."/notice_relations_collection.class.php";
require_once $class_path."/exemplaires.class.php";
require_once $base_path."/admin/connecteurs/in/cairn/cairn.class.php";
require_once $base_path."/admin/connecteurs/in/odilotk/odilotk.class.php";
require_once $base_path."/admin/connecteurs/in/divercities/divercities.class.php";
require_once $class_path."/notice.class.php";
require_once $class_path."/emprunteur.class.php";
require_once $class_path."/rdf_entities_conversion/rdf_entities_converter.class.php";
require_once $class_path."/contribution_area/contribution_area_form.class.php";
require_once $class_path."/pnb/dilicom.class.php";
require_once ($class_path . "/nomenclature/nomenclature_nomenclature.class.php");
require_once($class_path."/nomenclature/nomenclature_record_formations.class.php");
global $tdoc;
if (empty($tdoc)) $tdoc = new marc_list('doctype');

global $fonction_auteur;
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

/**
 * Classe qui représente les données d'une notice
 * @author apetithomme
 *
*/
class record_datas {

	/**
	 * Identifiant de la notice
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var domain
	 */
	private $dom_2 = null;

	/**
	 *
	 * @var domain
	 */
	private $dom_3 = null;

	/**
	 * Droits d'accès emprunteur/notice
	 * @var int
	 */
	private $rights = 0;

	/**
	 * Objet notice fetché en base
	 * @var stdClass
	 */
	private $notice;

	/**
	 * Tableau des informations du parent dans le cas d'un article
	 * @var array
	 */
	private $parent;

	/**
	 * Carte associée
	 * @var map_objects_controler
	*/
	private $map = null;

	/**
	 * Carte associée de localisation des exemplaires
	 * @var map_objects_controler
	 */
	private $map_location;

	/**
	 * Info de la carte associée
	 * @var map_info
	 */
	private $map_info = null;

	/**
	 * Paramètres persos
	 * @var parametres_perso
	 */
	private $p_perso = null;

	/**
	 * identifiant du statut de la notice
	 * @var string
	 */
	private $id_statut_notice = 0;

	/**
	 * Libellé du statut de la notice
	 * @var string
	 */
	private $statut_notice = "";

	/**
	 * classe html du statut de la notice
	 * @var string
	 */
	private $statut_notice_class_html = "";

	/**
	 * Visibilité de la notice à tout le monde
	 * @var int
	 */
	private $visu_notice = 1;

	/**
	 * Visibilité de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_notice_abon = 0;

	/**
	 * Visibilité des exemplaires de la notice à tout le monde
	 * @var int
	 */
	private $visu_expl = 1;

	/**
	 * Visibilité des exemplaires de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_expl_abon = 0;

	/**
	 * Visibilité des exemplaires numériques de la notice à tout le monde
	 * @var int
	 */
	private $visu_explnum = 1;

	/**
	 * Visibilité des exemplaires numériques de la notice aux abonnés uniquement
	 * @var int
	 */
	private $visu_explnum_abon = 0;

	/**
	 * Visibilité du lien de demande de numérisation
	 * @var int
	 */
	private $visu_scan_request = 1;

	/**
	 * Visibilité du lien de demande de numérisation aux abonnés uniquement
	 * @var int
	 */
	private $visu_scan_request_abon = 0;

	/**
	 * Tableau des auteurs
	 * @var array
	 */
	private $responsabilites = array();

	/**
	 * Auteurs principaux
	 * @var string
	*/
	private $auteurs_principaux;

	/**
	 * Auteurs auteurs_secondaires
	 * @var string
	 */
	private $auteurs_secondaires;

	/**
	 * Catégories
	 * @var categorie
	 */
	private $categories;

	/**
	 * Titre uniforme
	 * @var tu_notice
	 */
	private $titre_uniforme = null;

	/**
	 * Avis
	 * @var avis
	 */
	private $avis = null;

	/**
	 * Langues
	 * @var array
	 */
	private $langues = array();

	/**
	 * Nombre de bulletins associés
	 * @var int
	 */
	private $nb_bulletins;

	/**
	 * Tableau des bulletins associés
	 * @var array
	 */
	private $bulletins = array();

	/**
	 * Tableau de documents numériques associés aux bulletins
	 * @var array
	 */
	private $bulletins_docnums;

	/**
	 * Nombre de documents numériques associés aux bulletins
	 * @var int
	 */
	private $nb_bulletins_docnums;

	/**
	 * Indique si le pério est ouvert à la recherche
	 * @var int
	 */
	private $open_to_search;

	/**
	 * Editeurs
	 * @var publisher
	 */
	private $publishers = array();

	/**
	 * Etat de collections
	 * @var collstate
	 */
	private $collstate;

	/**
	 * Tous les états de collections
	 * @var collstate
	 */
	private $collstate_list;

	/**
	 * Autorisation des avis
	 * @var int
	 */
	private $avis_allowed;

	/**
	 * Autorisation des tags
	 * @var int
	 */
	private $tag_allowed;

	/**
	 * Autorisation des suggestions
	 * @var int
	 */
	private $sugg_allowed;

	/**
	 * Autorisation des listes de lecture
	 * @var int
	 */
	private $liste_lecture_allowed;

	/**
	 * Tableau des sources d'enrichissement actives pour cette notice
	 * @var array
	 */
	private $enrichment_sources;

	/**
	 * Icone du type de document
	 * @var string
	 */
	private $icon_doc;

	/**
	 * Libellé du niveau biblio
	 * @var string
	 */
	private $biblio_doc;

	/**
	 * Libellé du type de document
	 * @var string
	 */
	private $tdoc;

	/**
	 * Liste de concepts qui indexent la notice
	 * @var skos_concepts_list
	 */
	private $concepts_list = null;

	/**
	 * Tableau des mots clés
	 * @var array
	 */
	private $mots_cles;

	/**
	 * Indexation décimale
	 * @var indexint
	 */
	private $indexint = null;

	/**
	 * Collection
	 * @var collection
	 */
	private $collection = null;

	/**
	 * Sous-collection
	 * @var subcollection
	 */
	private $subcollection = null;

	/**
	 * Permalink
	 * @var string
	 */
	private $permalink;

	/**
	 * Tableau des ids des notices du même auteur
	 * @var array
	 */
	private $records_from_same_author;

	/**
	 * Tableau des ids des notices du même éditeur
	 * @var array
	 */
	private $records_from_same_publisher;

	/**
	 * Tableau des ids des notices de la même collection
	 * @var array
	 */
	private $records_from_same_collection;

	/**
	 * Tableau des ids des notices dans la même série
	 * @var array
	 */
	private $records_from_same_serie;

	/**
	 * Tableau des ids des notices avec la même indexation décimale
	 * @var array
	 */
	private $records_from_same_indexint;

	/**
	 * Tableau des ids de notices avec des catégories communes
	 * @var array
	 */
	private $records_from_same_categories;

	/**
	 * URL vers l'image de la notice
	 * @var string
	 */
	private $picture_url;

	/**
	 * Message au survol de l'image de la notice
	 * @var string
	 */
	private $picture_title;

	/**
	 * Disponibilité
	 * @var array
	 */
	private $availability;

	/**
	 * Paramètres du PNB
	 * @var array
	 */
	private $pnb_datas;

	/**
	 * Paramètres de réservation
	 * @var array
	 */
	private $resas_datas;

	/**
	 * Données d'exemplaires
	 * @var array
	 */
	private $expls_datas;

	/**
	 * Données de série
	 * @var array
	 */
	private $serie;

	/**
	 * Tableau des relations parentes
	 * @var array
	 */
	private $relations_up;

	/**
	 * Tableau des relations enfants
	 * @var array
	 */
	private $relations_down;

	/**
	 * Tableau des relations horizontales
	 * @var array
	 */
	private $relations_both;

	/**
	 * Tableau des dépouillements
	 * @var array
	 */
	private $articles;

	/**
	 * Données de demandes
	 * @var array
	 */
	private $demands_datas;

	/**
	 * Panier autorisé selon paramètres PMB et utilisateur connecté
	 * @var boolean
	 */
	private $cart_allow;

	/**
	 * La notice est-elle déjà dans le panier ?
	 * @var boolean
	 */
	private $in_cart;

	/**
	 * Informations de documents numériques associés
	 * @var array
	 */
	private $explnums_datas;

	/**
	 * Tableau des autorités persos associées à la notice
	 * @var authority $authpersos
	 */
	private $authpersos;

	/**
	 * Tableau des autorités persos classées associées à la notice
	 * @var authority $authpersos
	 */
	private $authpersos_ranked;

	/**
	 * Tableau des informations externes de la notice
	 * @var array $external_rec_id
	 */
	private $external_rec_id;

	/**
	 * Tableau des informations des onglets perso de la notice
	 * @var array $onglet_perso
	 */
	private $onglet_perso;

	/**
	 * Informations du périodique
	 * @var record_datas
	 */
	private $serial;

	/**
	 * Tableau parametres externes utilisable dans les templates ( issu d'un formulaire par exemple )
	 * @var array $external_parameters
	 */
	private $external_parameters;

	/**
	 * Lien vers ressource externe
	 * @var string $lien
	 */
	private $lien;

	/**
	 * Infos sur la source de la notice si elle est issue d'un connecteur (recid, connector, source_id et ref)
	 * @var array
	 */
	private $source;

	/**
	 * Lien de contribution pour un exemplaire de la notice
	 * @var string
	 */
	private $expl_contribution_link;

	/**
	 * Lien de contribution pour un exemplaire numérique de la notice
	 * @var string
	 */
	private $explnum_contribution_link;

	/**
	 * Tableau d'oeuvres associees
	 * @var array
	 */
	private $works_data;

	/**
	 * Lien ARK pointant vers la notice
	 * @var string
	 */
	private $ark_link;

	/**
	 * Informations sur la nomenclature
	 * @var array
	 */
	private $nomenclature;

	/**
	 * Informations détaillées sur la nomenclature
	 * @var array
	 */
	private $analyzed_nomenclature;

	public $to_print;

	private static $record_datas_instance = [];

	public function __construct($id) {
		global $to_print;

		$this->id = intval($id);
		if (!$this->id) return;
		$this->fetch_data();
		$this->fetch_visibilite();

		if ($to_print) {
			$this->avis_allowed = 0;
			$this->tag_allowed = 0;
			$this->sugg_allowed = 0;
			$this->liste_lecture_allowed = 0;
		} else {
			$this->avis_allowed = $this->get_parameter_value('avis_allow');
			$this->tag_allowed = $this->get_parameter_value('allow_add_tag');
			$this->sugg_allowed = $this->get_parameter_value('show_suggest_notice');
			$this->liste_lecture_allowed = $this->get_parameter_value('shared_lists');
		}

		$this->to_print = $to_print;
	}

	public static function get_instance($id) {
	    if (!isset(static::$record_datas_instance[$id])) {
	        static::$record_datas_instance[$id] = new record_datas($id);
	    }
	    return static::$record_datas_instance[$id];
	}

	/**
	 * Charge les infos présentes en base de données
	 */
	private function fetch_data() {
		if(is_null($this->dom_2)) {
			$query = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, statut, thumbnail_url, (opac_visible_bulletinage&0x1) as opac_visible_bulletinage, opac_serialcirc_demande, notice_is_new, notice_date_is_new, is_numeric, create_date, update_date ";
			$query.= "FROM notices WHERE notice_id='".$this->id."' ";
		} else {
			$query = "SELECT notice_id, typdoc, tit1, tit2, tit3, tit4, tparent_id, tnvol, ed1_id, ed2_id, coll_id, subcoll_id, year, nocoll, mention_edition,code, npages, ill, size, accomp, lien, eformat, index_l, indexint, niveau_biblio, niveau_hierar, origine_catalogage, prix, n_gen, n_contenu, n_resume, thumbnail_url, (opac_visible_bulletinage&0x1) as opac_visible_bulletinage, opac_serialcirc_demande, notice_is_new, notice_date_is_new, is_numeric, create_date, update_date ";
			$query.= "FROM notices ";
			$query.= "WHERE notice_id='".$this->id."'";
		}
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$this->notice = pmb_mysql_fetch_object($result);
		}
	}

	/**
	 * Retourne l'identifiant de la notice
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Retourne les infos de bulletinage
	 *
	 * @return array Informations de bulletinage si applicable, un tableau vide sinon<br />
	 * $this->parent = array('title', 'id', 'bulletin_id', 'numero', 'date', 'date_date', 'aff_date_date')
	 */
	public function get_bul_info() {
		if (!$this->parent) {
			global $msg;

			$this->parent = array();

			$query = "";
			if ($this->notice->niveau_hierar == 2) {
				if ($this->notice->niveau_biblio == 'a') {
					// récupération des données du bulletin et de la notice apparentée
					$query = "SELECT b.tit1,b.notice_id,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
					$query .= "from analysis a, notices b, bulletins c";
					$query .= " WHERE a.analysis_notice=".$this->id;
					$query .= " AND c.bulletin_id=a.analysis_bulletin";
					$query .= " AND c.bulletin_notice=b.notice_id";
					$query .= " LIMIT 1";
				} elseif ($this->notice->niveau_biblio == 'b') {
					// récupération des données du bulletin et de la notice apparentée
					$query = "SELECT tit1,notice_id,b.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
					$query .= "from bulletins b, notices";
					$query .= " WHERE num_notice=$this->id ";
					$query .= " AND  bulletin_notice=notice_id ";
					$query .= " LIMIT 1";
				}
				if ($query) {
					$result = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result)) {
						$parent = pmb_mysql_fetch_object($result);
						$this->parent['title'] = $parent->tit1;
						$this->parent['id'] = $parent->notice_id;
						$this->parent['bulletin_id'] = $parent->bulletin_id;
						$this->parent['bulletin_title'] = $parent->bulletin_titre;
						$this->parent['bulletin_num_notice'] = $parent->num_notice;
						$this->parent['numero'] = $parent->bulletin_numero;
						$this->parent['date'] = $parent->mention_date;
						$this->parent['date_date'] = $parent->date_date;
						$this->parent['aff_date_date'] = $parent->aff_date_date;
					}
				}
			}
		}
		return $this->parent;
	}

	/**
	 * Retourne le type de document
	 *
	 * @return string
	 */
	public function get_typdoc() {
		if (!isset($this->notice)) {
			return '';
		}
		if (!isset($this->notice->typdoc)) {
			$this->notice->typdoc='a';
		}
		return $this->notice->typdoc;
	}

	/**
	 * Retourne les données de la série si il y en a une
	 *
	 * @return array
	 */
	public function get_serie() {
		if (!isset($this->serie)) {
			$this->serie = array();
			if ($this->notice->tparent_id) {
				$query = "SELECT serie_name FROM series WHERE serie_id='".$this->notice->tparent_id."' ";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$serie = pmb_mysql_fetch_object($result);

					//$authority = new authority(0, $this->notice->tparent_id, AUT_TABLE_SERIES);
					$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $this->notice->tparent_id, 'type_object' => AUT_TABLE_SERIES]);

					$this->serie = array(
							'id' => $this->notice->tparent_id,
							'name' => $serie->serie_name,
							'p_perso' => $authority->get_p_perso()
					);
				}
			}
		}
		return $this->serie;
	}

	/**
	 * Charge les données de carthographie
	 */
	private function fetch_map() {
	    $ids = array();
		$this->map=new stdClass();
		$this->map_info=new stdClass();
		if($this->get_parameter_value('map_activate')==1 || $this->get_parameter_value('map_activate')==2){
			$ids[]=$this->id;
			$this->map=new map_objects_controler(TYPE_RECORD,$ids);
			$this->map_info=new map_info($this->id);
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

	/**
	 * Charge les données de carthographie de localisation des exemplaires
	 */
	private function fetch_map_location() {
		$this->map_location='';
		if($this->get_parameter_value('map_activate')==1 || $this->get_parameter_value('map_activate')==3){
			$this->get_expls_datas();
			$this->get_explnums_datas();
			$memo_expl = array();
			// mémorisation des exemplaires et de leur localisation
			if(count($this->expls_datas['expls'])) {
				foreach ($this->expls_datas['expls'] as $expl){
					$memo_expl['expl'][]=array(
							'expl_id' => $expl['expl_id'],
							'expl_location'	=> array( $expl['expl_location']),
							'id_notice' => $expl['id_notice'],
							'id_bulletin' => $expl['id_bulletin']
					);
				}
			}
			if(count($this->explnums_datas['explnums'])) {
				foreach ($this->explnums_datas['explnums'] as $expl){
					$memo_expl['explnum'][]=array(
							'expl_id' =>  $expl['id'],
							'expl_location'	=> $expl['expl_location'],
							'id_notice' => $expl['id_notice'],
							'id_bulletin' => $expl['id_bulletin']
					);
				}
			}
			$this->map_location=map_locations_controler::get_map_location($memo_expl,TYPE_LOCATION, 1);
		}
	}


	/**
	 * Retourne la carte associée de localisation des exemplaires
	 * @return map_objects_controler
	 */
	public function get_map_location() {
		if (!isset($this->map_location)) {
			$this->fetch_map_location();
		}
		return $this->map_location;
	}

	/**
	 * Retourne les paramètres persos
	 * @return array
	 */
	public function get_p_perso() {
		if (!$this->p_perso) {
			global $memo_p_perso_notices;

			$this->p_perso = array();

			if (!$memo_p_perso_notices) {
				$memo_p_perso_notices = new parametres_perso("notices");
			}
			$ppersos = $memo_p_perso_notices->show_fields($this->id);
			// Filtre ceux qui ne sont pas visibles à l'OPAC ou qui n'ont pas de valeur
			if(isset($ppersos['FIELDS']) && is_array($ppersos['FIELDS']) && count($ppersos['FIELDS'])){
				foreach ($ppersos['FIELDS'] as $pperso) {
				    if ($pperso['OPAC_SHOW'] && $pperso['AFF']) {
				        if ($pperso["TYPE"] !== 'html') {
				            $pperso['AFF'] = nl2br($pperso["AFF"]);
				        }
						$this->p_perso[$pperso['NAME']] = $pperso;
					}
				}
			}
		}
		return $this->p_perso;
	}

	/**
	 * Gestion des droits d'accès emprunteur/notice
	 */
	private function fetch_visibilite() {
		global $hide_explnum;
		global $gestion_acces_active,$gestion_acces_empr_notice, $gestion_acces_empr_docnum;

		if (isset($this->notice->statut)) {
			$query = "SELECT id_notice_statut, opac_libelle, class_html, notice_visible_opac, expl_visible_opac, notice_visible_opac_abon, expl_visible_opac_abon, explnum_visible_opac, explnum_visible_opac_abon, notice_scan_request_opac, notice_scan_request_opac_abon FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$statut_temp = pmb_mysql_fetch_object($result);

				$this->id_statut_notice = $statut_temp->id_notice_statut;
				$this->statut_notice =        $statut_temp->opac_libelle;
				$this->statut_notice_class_html =        $statut_temp->class_html;
				$this->visu_notice =          $statut_temp->notice_visible_opac;
				$this->visu_notice_abon =     $statut_temp->notice_visible_opac_abon;
				$this->visu_expl =            $statut_temp->expl_visible_opac;
				$this->visu_expl_abon =       $statut_temp->expl_visible_opac_abon;
				$this->visu_explnum =         $statut_temp->explnum_visible_opac;
				$this->visu_explnum_abon =    $statut_temp->explnum_visible_opac_abon;
				$this->visu_scan_request =		$statut_temp->notice_scan_request_opac;
				$this->visu_scan_request_abon =	$statut_temp->notice_scan_request_opac_abon;

				if ($hide_explnum) {
					$this->visu_explnum=0;
					$this->visu_explnum_abon=0;
				}
			}
		}
		if (($gestion_acces_active == 1) && (($gestion_acces_empr_notice == 1) || ($gestion_acces_empr_docnum == 1))) {
			$ac = new acces();
		}
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_notice == 1)) {
			$this->dom_2= $ac->setDomain(2);
			if ($hide_explnum) {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id,4);
			} else {
				$this->rights = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id);
			}
		}
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_docnum == 1)) {
			$this->dom_3 = $ac->setDomain(3);
		}
	}

	public function get_dom_2() {
		return $this->dom_2;
	}

	public function get_dom_3() {
		return $this->dom_3;
	}

	public function get_rights() {
		return $this->rights;
	}

	/**
	 * Retourne un tableau des auteurs
	 * @return array Tableaux des responsabilités = array(
	 'responsabilites' => array(),
	 'auteurs' => array()
	 );
	 */
	public function get_responsabilites() {
	    global $fonction_auteur, $pmb_authors_qualification;

		if (!count($this->responsabilites)) {
			$this->responsabilites = array(
					'responsabilites' => array(),
					'auteurs' => array()
			);

			$query = "SELECT id_responsability, author_id, responsability_fonction, responsability_type, author_type,author_name, author_rejete, author_type, author_date, author_see, author_web, author_isni ";
			$query.= "FROM responsability, authors ";
			$query.= "WHERE responsability_notice='".$this->id."' AND responsability_author=author_id ";
			$query.= "ORDER BY responsability_type, responsability_ordre " ;
			$result = pmb_mysql_query($query);
			while ($notice = pmb_mysql_fetch_object($result)) {
				$this->responsabilites['responsabilites'][] = $notice->responsability_type ;
				$info_bulle="";
				if($notice->author_type==72 || $notice->author_type==71) {
					$congres = authorities_collection::get_authority('author', $notice->author_id);
					$auteur_isbd=$congres->get_isbd();
					$auteur_titre=$congres->display;
					$info_bulle=" title='".$congres->info_bulle."' ";
				} else {
					if ($notice->author_rejete) $auteur_isbd = $notice->author_rejete." ".$notice->author_name ;
					else  $auteur_isbd = $notice->author_name ;
					// on s'arrête là pour auteur_titre = "Prénom NOM" uniquement
					$auteur_titre = $auteur_isbd ;
					// on complète auteur_isbd pour l'affichage complet
					if ($notice->author_date) $auteur_isbd .= " (".$notice->author_date.")" ;
				}

				//$authority = new authority(0, $notice->author_id, AUT_TABLE_AUTHORS);
				$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $notice->author_id, 'type_object' => AUT_TABLE_AUTHORS]);

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
				$this->responsabilites['auteurs'][] = array(
						'id' => $notice->author_id,
						'fonction' => $notice->responsability_fonction,
						'responsability' => $notice->responsability_type,
						'name' => $notice->author_name,
						'rejete' => $notice->author_rejete,
						'date' => $notice->author_date,
						'type' => $notice->author_type,
						'fonction_aff' => ($notice->responsability_fonction ? ($fonction_auteur[$notice->responsability_fonction] ?? ""): ''),
				        'qualification' => $qualification,
						'auteur_isbd' => $auteur_isbd,
						'auteur_titre' => $auteur_titre,
						'info_bulle' => $info_bulle,
						'web' => $notice->author_web,
				        'isni' => $notice->author_isni,
						'p_perso' => $authority->get_p_perso()
				);
			}
		}
		return $this->responsabilites;
	}

	/**
	 * Retourne les auteurs principaux
	 * @return string auteur1 ; auteur2 ...
	 */
	public function get_auteurs_principaux() {
		if (!$this->auteurs_principaux) {
			$this->get_responsabilites();
			// on ne prend que le auteur_titre = "Prénom NOM"
			$as = array_search("0", $this->responsabilites["responsabilites"]);
			if (($as !== FALSE) && ($as !== NULL)) {
				$auteur_0 = $this->responsabilites["auteurs"][$as];
				$this->auteurs_principaux = "<a href='".static::format_url("index.php?lvl=author_see&id=".$auteur_0['id'])."'>".$auteur_0["auteur_titre"]."</a>";
			} else {
				$as = array_keys($this->responsabilites["responsabilites"], "1" );
				$aut1_libelle = array();
				for ($i = 0; $i < count($as); $i++) {
					$indice = $as[$i];
					$auteur_1 = $this->responsabilites["auteurs"][$indice];
					if($auteur_1["type"]==72 || $auteur_1["type"]==71) {
						$congres = authorities_collection::get_authority('author', $auteur_1["id"]);
						$aut1_libelle[]="<a href='".static::format_url("index.php?lvl=author_see&id=".$auteur_1['id'])."'>".$congres->display."</a>";
					} else {
						$aut1_libelle[]= "<a href='".static::format_url("index.php?lvl=author_see&id=".$auteur_1['id'])."'>".$auteur_1["auteur_titre"]."</a>";
					}
				}
				$auteurs_liste = implode(" ; ",$aut1_libelle);
				if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste;
			}
		}
		return $this->auteurs_principaux;
	}

	/**
	 * Retourne les auteurs secondaires
	 * @return string auteur1 ; auteur2 ...
	 */
	public function get_auteurs_secondaires() {
		if (!$this->auteurs_secondaires) {
			$this->get_responsabilites();
			$as = array_keys($this->responsabilites["responsabilites"], "2" );
			$aut2_libelle = array();
			for ($i = 0; $i < count($as); $i++) {
				$indice = $as[$i];
				$auteur_2 = $this->responsabilites["auteurs"][$indice];
				if($auteur_2["type"]==72 || $auteur_2["type"]==71) {
					$congres = authorities_collection::get_authority('author', $auteur_2["id"]);
					$aut2_libelle[]="<a href='".static::format_url("index.php?lvl=author_see&id=".$auteur_2['id'])."'>".$congres->display."</a>";
				} else {
					$aut2_libelle[]="<a href='".static::format_url("index.php?lvl=author_see&id=".$auteur_2['id'])."'>".$auteur_2["auteur_titre"]."</a>";
				}
			}
			$auteurs_liste = implode(" ; ",$aut2_libelle);
			if ($auteurs_liste) $this->auteurs_secondaires = $auteurs_liste;
		}
		return $this->auteurs_secondaires;
	}

	/**
	 * Retourne l'identiiant du statut de la notice
	 *
	 * @return string
	 */
	public function get_id_statut_notice() {
		return $this->id_statut_notice;
	}

	/**
	 * Retourne le libellé du statut de la notice
	 *
	 * @return string
	 */
	public function get_statut_notice() {
		return $this->statut_notice;
	}

	/**
	 * Retourne la classe html du statut de la notice
	 *
	 * @return string
	 */
	public function get_statut_notice_class_html() {
		return $this->statut_notice_class_html;
	}

	/**
	 * Retourne la visibilité de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_notice() {
		return $this->visu_notice;
	}

	/**
	 * Retourne la visibilité de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_notice_abon() {
		return $this->visu_notice_abon;
	}

	/**
	 * Retourne la visibilité des exemplaires de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_expl() {
		return $this->visu_expl;
	}

	/**
	 * Retourne la visibilité des exemplaires de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_expl_abon() {
		return $this->visu_expl_abon;
	}

	/**
	 * Retourne la visibilité des exemplaires numériques de la notice à tout le monde
	 *
	 * @return int
	 */
	public function is_visu_explnum() {
		return $this->visu_explnum;
	}

	/**
	 * Retourne la visibilité des exemplaires numériques de la notice aux abonnés uniquement
	 *
	 * @return int
	 */
	public function is_visu_explnum_abon() {
		return $this->visu_explnum_abon;
	}

	/**
	 * Retourne la visibilité du lien de demande de numérisation
	 */
	public function is_visu_scan_request() {
		return $this->visu_scan_request;
	}

	/**
	 * Retourne la visibilité du lien de demande de numérisation aux abonnés uniquement
	 */
	public function is_visu_scan_request_abon() {
		return $this->visu_scan_request_abon;
	}

	/**
	 * Retourne les catégories de la notice
	 * @return categorie Tableau des catégories
	 */
	public function get_categories() {
		if (!isset($this->categories)) {
			global $opac_categories_affichage_ordre, $opac_categories_show_only_last;
			global $opac_categories_categ_path_sep;

			$this->categories = array();
			$used_thesaurus = [];
			// Tableau qui va nous servir à trier alphabétiquement les catégories
			if (!$opac_categories_affichage_ordre) $sort_array = array();

			$query = "select distinct num_noeud from notices_categories where notcateg_notice = ".$this->id." order by ordre_vedette, ordre_categorie";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
				    if (0 == intval($row->num_noeud)) {
				        continue;
				    }
					/* @var $object categorie */
					$object = authorities_collection::get_authority('category', $row->num_noeud);
					//On regarde la traduction du libellé de thésaurus
					if (is_object($object->thes)) {
					    $object->thes->libelle_thesaurus = $object->thes->get_translated_libelle_thesaurus();
					}

					$format_label = $object->libelle;

					// On recense les thesaurus utilises pour tri
					if( !isset($used_thesaurus[$object->thes->id_thesaurus]) ) {
					    $used_thesaurus[$object->thes->id_thesaurus] = strtoupper(convert_diacrit($object->thes->libelle_thesaurus));
					}
					// On ajoute les parents si nécessaire
					if (!$opac_categories_show_only_last) {
						$parent_id = $object->parent;
						while ($parent_id && ($parent_id != 1) && (!in_array($parent_id, array($object->thes->num_noeud_racine, $object->thes->num_noeud_nonclasses, $object->thes->num_noeud_orphelins)))) {
							$parent = authorities_collection::get_authority('category', $parent_id);
							$format_label = $parent->libelle.($opac_categories_categ_path_sep ? $opac_categories_categ_path_sep : ':').$format_label;
							$parent_id = $parent->parent;
						}
					}
					//$authority = new authority(0, $row->num_noeud, AUT_TABLE_CATEG);
					$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $row->num_noeud, 'type_object' => AUT_TABLE_CATEG]);

					$categorie = array(
							'object' => $object,
							'format_label' => $format_label,
							'p_perso' => $authority->get_p_perso()
					);
					if (!$opac_categories_affichage_ordre) {
						$sort_array[$object->thes->id_thesaurus][] = strtoupper(convert_diacrit($format_label));
					}
					$this->categories[$object->thes->id_thesaurus][] = $categorie;
				}

				// Tri par ordre alphabetique sur libelle thesaurus et libelle categorie
				if (!$opac_categories_affichage_ordre) {

                    //Tri des categories par libelle pour chaque thesaurus
					foreach ($this->categories as $thes_id => &$categories) {
						array_multisort($sort_array[$thes_id], $categories);
					}

    				// Tri des thesaurus utilises par libelle en conservant les cles
    			    asort($used_thesaurus);

    			    // Tri des categories par libelle de thesaurus
    			    $tmp_categories = [];
    			    foreach($used_thesaurus as $k => $v) {
    			        $tmp_categories[$k] = $this->categories[$k];
    			    }
    			    $this->categories = $tmp_categories;
    			    unset($tmp_categories);
		        }
			}
		}
		return $this->categories;
	}

	/**
	 * Retourne le titre uniforme
	 * @return tu_notice
	 */
	public function get_titre_uniforme() {
		if (!$this->titre_uniforme) {
			$this->titre_uniforme = new tu_notice($this->id);
		}
		return $this->titre_uniforme;
	}

	/**
	 * Retourne un tableau d'instances de titres uniformes
	 * @return array
	 */
	public function get_works_data() {
		if (empty($this->works_data)) {
			$this->works_data = array();
			$tu_notice = $this->get_titre_uniforme();
			foreach ($tu_notice->ntu_data as $work) {
				$this->works_data[] = new titre_uniforme($work->num_tu);
			}
		}
		return $this->works_data;
	}

	/**
	 * Retourne le tableau des langues de la notices
	 * @return array $this->langues = array('langues' => array(), 'languesorg' => array())
	 */
	public function get_langues() {
		if (!count($this->langues)) {
			global $marc_liste_langues;
			if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

			$this->langues = array(
					'langues' => array(),
					'languesorg' => array()
			);
			$query = "select code_langue, type_langue from notices_langues where num_notice=".$this->id." order by ordre_langue ";
			$result = pmb_mysql_query($query);
			while (($notice=pmb_mysql_fetch_object($result))) {
				if ($notice->code_langue) {
					$langue = array(
						'lang_code' => $notice->code_langue,
						'langue' => $marc_liste_langues->table[$notice->code_langue]
					);
					if (!$notice->type_langue) {
						$this->langues['langues'][] = $langue;
					} else {
						$this->langues['languesorg'][] = $langue;
					}
				}
			}
		}
		return $this->langues;
	}

	/**
	 * Retourne un tableau avec le nombre d'avis et la moyenne
	 * @return array Tableau $this->avis = array('moyenne', 'qte', 'avis' => array('note', 'commentaire', 'sujet'), 'nb_by_note' => array('{note}' => {nb_avis})
	 */
	public function get_avis() {
		if (!is_object($this->avis)) {
			$this->avis = new avis($this->id);
		}
		return $this->avis;
	}

	/**
	 * Retourne le nombre de bulletins associés
	 * @return int
	 */
	public function get_nb_bulletins(){
		if (!isset($this->nb_bulletins)) {
			$this->nb_bulletins = 0;

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
				$req="SELECT bulletin_id FROM bulletins WHERE bulletin_notice='".$this->id."' and num_notice=0";
				$res = pmb_mysql_query($req);
				if($res){
					$this->nb_bulletins+=pmb_mysql_num_rows($res);
				}

				//Bulletins avec notice
				$req="SELECT bulletin_id FROM bulletins
					JOIN notices ON notice_id=num_notice AND num_notice!=0
					".$acces_j." ".$statut_j."
					WHERE bulletin_notice='".$this->id."'
					".$statut_r."";
				$res = pmb_mysql_query($req);
				if($res){
					$this->nb_bulletins+=pmb_mysql_num_rows($res);
				}
			}
		}
		return $this->nb_bulletins;
	}

	/**
	 * Retourne le tableau des bulletins associés à la notice
	 * @return array $this->bulletins[] = array('id', 'numero', 'mention_date', 'date_date', 'bulletin_titre', 'num_notice')
	 */
	public function get_bulletins(){
		if (!count($this->bulletins) && $this->get_nb_bulletins()) {
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
				$req="SELECT * FROM bulletins WHERE bulletin_notice='".$this->id."' and num_notice=0";
				$res = pmb_mysql_query($req);
				if($res && pmb_mysql_num_rows($res)){
					while($r=pmb_mysql_fetch_object($res)){
						$this->bulletins[] = array(
								'id' => $r->bulletin_id,
								'numero' => $r->bulletin_numero,
								'mention_date' => $r->mention_date,
								'date_date' => $r->date_date,
								'bulletin_titre' => $r->bulletin_titre,
								'num_notice' => $r->num_notice
						);
					}
				}

				//Bulletins avec notice
				$req="SELECT bulletins.* FROM bulletins
				JOIN notices ON notice_id=num_notice AND num_notice!=0
				".$acces_j." ".$statut_j."
				WHERE bulletin_notice='".$this->id."'
				".$statut_r."";
				$res = pmb_mysql_query($req);
				if($res && pmb_mysql_num_rows($res)){
					while($r=pmb_mysql_fetch_object($res)){
						$this->bulletins[] = array(
								'id' => $r->bulletin_id,
								'numero' => $r->bulletin_numero,
								'mention_date' => $r->mention_date,
								'date_date' => $r->date_date,
								'bulletin_titre' => $r->bulletin_titre,
								'num_notice' => $r->num_notice
						);
					}
				}
			}
		}
		return $this->bulletins;
	}

	/**
	 * Retourne le nombre de documents numériques associés aux bulletins
	 * @return int
	 */
	public function get_nb_bulletins_docnums() {
		if (!isset($this->nb_bulletins_docnums)) {
			$this->get_bulletins_docnums();
			$this->nb_bulletins_docnums = count($this->bulletins_docnums);
		}
		return $this->nb_bulletins_docnums;
	}

	/**
	 * Retourne le nombre de documents numériques associés aux bulletins
	 * @return int
	 */
	public function get_bulletins_docnums() {
	    if (!isset($this->bulletins_docnums)) {
	        $this->bulletins_docnums = array();

	        $join_acces_explnum = "";
	        if (!$this->get_parameter_value('show_links_invisible_docnums')) {
	            if (!is_null($this->dom_3)) {
	                $join_acces_explnum = $this->dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
	            } else {
	                $join_acces_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)":"").")";
	            }
	        }
	        $sql_explnum = "SELECT explnum_id, explnum_nom, explnum_nomfichier, explnum_url, explnum_mimetype
								FROM explnum $join_acces_explnum JOIN bulletins ON explnum_bulletin=bulletin_id
								WHERE bulletin_notice = ".$this->id." order by explnum_id";
	        $explnums = pmb_mysql_query($sql_explnum);
	        $explnumscount = pmb_mysql_num_rows($explnums);

	        if ($this->get_parameter_value('show_links_invisible_docnums') || (is_null($this->dom_2) && $this->visu_explnum && (!$this->visu_explnum_abon || ($this->visu_explnum_abon && $_SESSION["user_code"])))  || ($this->rights & 16) ) {
	            if ($explnumscount) {
	                while($explnumrow = pmb_mysql_fetch_object($explnums)) {
	                    $visible = true;
	                    //vérification de la visibilité si non connecté
	                    if(!$_SESSION['id_empr_session'] && $this->get_parameter_value('show_links_invisible_docnums')){
	                        $visible = false;
	                        if (!is_null($this->dom_3)) {
	                            $right = $this->dom_3->getRights(0,$explnumrow->explnum_id,16);
	                            if($right == 16){
	                                $visible = true;
	                            }
	                        }else{
	                            $sql = "select explnum_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac= 1 and explnum_visible_opac_abon = 0 and explnum_id = ".$explnumrow->explnum_id;
	                            if(pmb_mysql_num_rows(pmb_mysql_query($sql))){
	                                $visible = true;
	                            }
	                        }
	                    }
	                    if ($visible) {
	                        $this->bulletins_docnums[] = $explnumrow;
	                    }
	                }
	            }
	        }
	    }
	    return $this->bulletins_docnums;
	}

	/**
	 * Retourne la requête SQL d'interrogation des bulletins du périodique
	 * @return int
	 */
	public function get_query_bulletins_list($restrict_num, $restrict_date, $bulletins_id = array()) {
	    global $opac_show_links_invisible_docnums;
	    global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;

	    $join_docnum_noti = $join_docnum_bull = "";
	    if (($gestion_acces_active == 1) && ($gestion_acces_empr_notice == 1)) {
	        $ac = new acces();
	        $dom_2= $ac->setDomain(2);
	        $join_noti = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.num_notice");
	        $join_bull = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.bulletin_notice");
	        if(!$opac_show_links_invisible_docnums){
	            $join_docnum_noti = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.num_notice");
	            $join_docnum_bull = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.bulletin_notice");
	        }
	    }else{
	        $join_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	        $join_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	        if(!$opac_show_links_invisible_docnums){
	            $join_docnum_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	            $join_docnum_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	    }
	    $join_docnum_explnum = "";
	    if(!$opac_show_links_invisible_docnums) {
	        if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	            $ac = new acces();
	            $dom_3= $ac->setDomain(3);
	            $join_docnum_explnum = $dom_3->getJoin($_SESSION["id_empr_session"],16,"explnum_id");
	        }else{
	            $join_docnum_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	    }

	    $restriction = " 1";
	    if (count($bulletins_id)) {
	        $restriction = " bulletins.bulletin_id in (".implode(",", $bulletins_id).")";
	    } else if ($this->id) {
	        $restriction = " bulletin_notice = ".$this->id;
	    }

	    $requete_docnum_noti = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_noti." where ".$restriction.") group by bulletin_id";
	    $requete_docnum_bull = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_bull." where ".$restriction.") group by bulletin_id";
	    $requete_noti = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_noti." left join (".$requete_docnum_noti.") as docnum_noti on bulletins.bulletin_id = docnum_noti.bulletin_id where bulletins.num_notice != 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";
	    $requete_bull = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_bull." left join ($requete_docnum_bull) as docnum_bull on bulletins.bulletin_id = docnum_bull.bulletin_id where bulletins.num_notice = 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";

	    return "select * from (".$requete_noti." union ".$requete_bull.") as uni where 1 ".$restrict_num." ".$restrict_date;
	}

	/**
	 * Un pério est ouvert à la recherche si il possède au moins un article ou une notice de bulletin
	 * @return int
	 */
	public function is_open_to_search(){
		if (!isset($this->open_to_search)) {
			$this->open_to_search = 0;

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

			//Articles
			$req="SELECT bulletin_id FROM bulletins
					JOIN analysis ON analysis_bulletin=bulletin_id
					JOIN notices ON analysis_notice=notice_id
					".$acces_j." ".$statut_j."
					WHERE bulletin_notice='".$this->id."'
					".$statut_r."";
			$res = pmb_mysql_query($req);
			if($res){
				$this->open_to_search+=pmb_mysql_num_rows($res);
			}

			//Notices de bulletin
			$req="SELECT bulletin_id FROM bulletins
					JOIN notices ON notice_id=num_notice AND num_notice!=0
					".$acces_j." ".$statut_j."
					WHERE bulletin_notice='".$this->id."'
					".$statut_r."";
			$res = pmb_mysql_query($req);
			if($res){
				$this->open_to_search+=pmb_mysql_num_rows($res);
			}
		}
		return $this->open_to_search;
	}

	/**
	 * Retourne $this->notice->niveau_biblio
	 */
	public function get_niveau_biblio() {
		return $this->notice->niveau_biblio;
	}

	/**
	 * Retourne $this->notice->niveau_hierar
	 */
	public function get_niveau_hierar() {
		return $this->notice->niveau_hierar;
	}

	/**
	 * Retourne $this->notice->tit1
	 */
	public function get_tit1() {
		return $this->notice->tit1;
	}

	/**
	 * Retourne $this->notice->tit2
	 */
	public function get_tit2() {
		return $this->notice->tit2;
	}

	/**
	 * Retourne $this->notice->tit3
	 */
	public function get_tit3() {
		return $this->notice->tit3;
	}

	/**
	 * Retourne $this->notice->tit4
	 */
	public function get_tit4() {
		return $this->notice->tit4;
	}

	/**
	 * Retourne $this->notice->code
	 */
	public function get_code() {
		return $this->notice->code;
	}

	/**
	 * Retourne $this->notice->npages
	 */
	public function get_npages() {
		return $this->notice->npages;
	}

	/**
	 * Retourne $this->notice->year
	 */
	public function get_year() {
		return $this->notice->year;
	}

	/**
	 * Retourne un tableau des éditeurs
	 * @return publisher Tableau des instances d'éditeurs
	 */
	public function get_publishers() {
		if((!isset($this->publishers) || !count($this->publishers)) && $this->notice->ed1_id){
			$publisher = authorities_collection::get_authority('publisher', $this->notice->ed1_id);
			$this->publishers[]=$publisher;

			if ($this->notice->ed2_id) {
				$publisher = authorities_collection::get_authority('publisher', $this->notice->ed2_id);
				$this->publishers[]=$publisher;
			}
		}
		return $this->publishers;
	}

	/**
	 * Retourne $this->notice->thumbnail_url
	 */
	public function get_thumbnail_url() {
		return $this->notice->thumbnail_url;
	}

	/**
	 * Retourne l'état de collection
	 * @return collstate
	 */
	public function get_collstate() {
		if (!$this->collstate) {
			if ($this->notice->niveau_biblio == 's') {
				$this->collstate = new collstate(0, $this->id);
			} else if ($this->notice->niveau_biblio == 'b') {
				$this->get_bul_info();
				$this->collstate = new collstate(0, 0, $this->parent['bulletin_id']);
			}
		}
		return $this->collstate;
	}

	/**
	 * Retourne tous les états de collection
	 * @return collstate
	 */
	public function get_collstate_list() {
		if (!$this->collstate_list) {
			$this->collstate_list = $this->get_collstate()->get_collstate_datas();
		}
		return $this->collstate_list;
	}

	/**
	 * Retourne l'autorisation des avis
	 * @return boolean
	 */
	public function get_avis_allowed() {
		global $allow_avis;
		if(($this->avis_allowed && ($this->avis_allowed != 2)) || ($_SESSION["user_code"] && ($this->avis_allowed == 2) && $allow_avis)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retourne l'autorisation des tags
	 * @return boolean
	 */
	public function get_tag_allowed() {
		global $allow_tag;
		if (($this->tag_allowed == 1) || (($this->tag_allowed == 2) && $_SESSION["user_code"] && $allow_tag)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retourne l'autorisation des suggestions
	 * @return boolean
	 */
	public function get_sugg_allowed() {
		global $allow_sugg;
		if (($this->sugg_allowed == 2) || ($_SESSION["user_code"] && ($this->sugg_allowed == 1) && $allow_sugg)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retourne l'autorisation des listes de lecture
	 * @return boolean
	 */
	public function get_liste_lecture_allowed() {
		global $allow_liste_lecture;
		if($this->liste_lecture_allowed == 1 && $_SESSION["user_code"] && $allow_liste_lecture) {
			return true;
		} else {
			return false;
		}
	}

	public function get_enrichment_sources() {
		if (!isset($this->enrichment_sources)) {
			$this->enrichment_sources = array();

			if($this->get_parameter_value('notice_enrichment')){
				$enrichment = new enrichment();
				if(!isset($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc])) {
					$enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc] = '';
				}
				if(!isset($enrichment->active[$this->notice->niveau_biblio])) {
					$enrichment->active[$this->notice->niveau_biblio] = '';
				}
				if($enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc]){
					$this->enrichment_sources = $enrichment->active[$this->notice->niveau_biblio.$this->notice->typdoc];
				}else if ($enrichment->active[$this->notice->niveau_biblio]){
					$this->enrichment_sources = $enrichment->active[$this->notice->niveau_biblio];
				}
			}
		}
		return $this->enrichment_sources;
	}

	/**
	 * Retourne l'icone du type de document
	 * @return string
	 */
	public function get_icon_doc() {
		if (!isset($this->icon_doc)) {
			$icon_doc = marc_list_collection::get_instance('icondoc');
			$this->icon_doc = $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc];
		}
		return $this->icon_doc;
	}

	/**
	 * Retourne le libellé du niveau biblio
	 * @return string
	 */
	public function get_biblio_doc() {
		if (!$this->biblio_doc) {
			$biblio_doc = marc_list_collection::get_instance('nivbiblio');
			$this->biblio_doc = $biblio_doc->table[$this->notice->niveau_biblio];
		}
		return $this->biblio_doc;
	}

	/**
	 * Retourne le libellé du type de document
	 * @return string
	 */
	public function get_tdoc() {
		if (!$this->tdoc) {
			global $tdoc;
			$this->tdoc = (!empty($tdoc->table[$this->get_typdoc()]) ? $tdoc->table[$this->get_typdoc()] : '');
		}
		return $this->tdoc;
	}

	/**
	 * Retourne la liste des concepts qui indexent la notice
	 * @return skos_concepts_list
	 */
	public function get_concepts_list() {
		if (!$this->concepts_list) {
			$this->concepts_list = new skos_concepts_list();
			$this->concepts_list->set_concepts_from_object(TYPE_NOTICE, $this->id);
		}
		return $this->concepts_list;
	}

	/**
	 * Retourne le tableau des mots clés
	 * @return array
	 */
	public function get_mots_cles() {
		if (!isset($this->mots_cles)) {
			global $pmb_keyword_sep;
			if (!$pmb_keyword_sep) $pmb_keyword_sep=" ";

			if (!trim($this->notice->index_l)) return "";

			$this->mots_cles = explode($pmb_keyword_sep,trim($this->notice->index_l)) ;
		}
		return $this->mots_cles;
	}

	/**
	 * Retourne l'indexation décimale
	 * @return indexint
	 */
	public function get_indexint() {
		if(!$this->indexint && $this->notice->indexint) {
			$this->indexint = authorities_collection::get_authority('indexint', $this->notice->indexint);
		}
		return $this->indexint;
	}

	/**
	 * Retourne le résumé
	 * @return string
	 */
	public function get_resume() {
		return $this->notice->n_resume;
	}

    /**
     * Retourne le contenu
     *
     * @return string
     */
    public function get_contenu()
    {
        return $this->notice->n_contenu;
    }

    /**
     * Retourne $this->notice->lien
     *
     * @return string
     */
    public function get_lien()
    {
        if (isset($this->lien)) {
            return $this->lien;
        }
        $this->lien = $this->notice->lien;
        $this->get_source();

        switch (true) {

            // Divercities
            case ((! empty($_SESSION['id_empr_session'])) && (! empty($this->source)) && ($this->source['connector'] == 'divercities')):

                $params = [
                    'source_id' => $this->source['source_id'],
                    'empr_id' => $_SESSION['id_empr_session'],
                ];
                $this->lien = divercities::get_resource_link($this->source['ref'], $params);
                break;

            // Cairn
            case (((! empty($this->source)) && ($this->source['connector'] == 'cairn')) || (strpos($this->lien, "cairn.info") !== false)):

                $cairn_connector = new cairn();
                $cairn_sso_params = $cairn_connector->get_sso_params();
                if ($cairn_sso_params && (strpos($this->lien, "?") === false)) {
                    $this->lien .= "?";
                    $cairn_sso_params = substr($cairn_sso_params, 1);
                }
                $this->lien .= $cairn_sso_params;
                break;

            // Odilotk
            case ((! empty($this->source)) && ($this->source['connector'] == 'odilotk')):
                $odilotk_connector = new odilotk();
                $this->lien = $odilotk_connector->get_odilotk_link($this->source['source_id'], $this->id);
                break;

            // C3RB
            case ((! empty($this->source)) && ($this->source['connector'] == 'c3rb')):
                $params = [
                    'source_id' => $this->source['source_id'],
                    'empr_id' => $_SESSION['id_empr_session'],
                    'link' => $this->lien,
                ];
                $this->lien = c3rb::get_resource_link($this->source['ref'], $params);
                break;

            default :
                break;
        }
        return $this->lien;
    }

	public function is_cairn_source() {
		// On gère un flag pour les cas particuliers des notices cairn qui ne seraient pas issue du connecteur
		$from_cairn_connector = false;
		$this->get_source();
		if (count($this->source)) {
			switch ($this->source['connector']) {
				case 'cairn' :
					$from_cairn_connector = true;
					break;
			}
		}
		if ($from_cairn_connector || (strpos($this->get_lien(), "cairn.info") !== false)) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return string
	 */
	public function get_source_label() {

		$this->get_source();
		if(empty($this->source['label'])) {
			return '';
		}
		return $this->source['label'];
	}

	/**
	 *
	 * @return array
	 */
	public function get_source() {

		if (isset($this->source)) {
			return $this->source;
		}
		$this->source = [];
		$q = "SELECT notices_externes.recid, connectors_sources.name ";
		$q.= "FROM notices_externes ";
		$q.= "JOIN external_count ON external_count.recid = notices_externes.recid ";
		$q.= "JOIN connectors_sources ON connectors_sources.source_id = external_count.source_id ";
		$q.= "WHERE notices_externes.num_notice = ". $this->id ." limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$recid = pmb_mysql_result($r, 0, 0);
			$label = pmb_mysql_result($r, 0, 1);
			$data = explode(" ", $recid);
			$this->source = [
					'recid' => $recid,
					'connector' => $data[0],
					'source_id' => $data[1],
					'ref' => $data[2],
					'label' => $label,
			];
		}
		return $this->source;
	}

	/**
	 * Retourne $this->notice->eformat
	 * @return string
	 */
	public function get_eformat() {
		return $this->notice->eformat;
	}

	/**
	 * Retourne $this->notice->tnvol
	 * @return string
	 */
	public function get_tnvol() {
		return $this->notice->tnvol;
	}

	/**
	 * Retourne $this->notice->mention_edition
	 * @return string
	 */
	public function get_mention_edition() {
		return $this->notice->mention_edition;
	}

	/**
	 * Retourne $this->notice->nocoll
	 * @return string
	 */
	public function get_nocoll() {
		return $this->notice->nocoll;
	}

	/**
	 * Retourne la collection
	 * @return collection
	 */
	public function get_collection() {
		if (!$this->collection && $this->notice->coll_id) {
			$this->collection = authorities_collection::get_authority('collection', $this->notice->coll_id);
		}
		return $this->collection;
	}

	/**
	 * Retourne la sous-collection
	 * @return subcollection
	 */
	public function get_subcollection() {
		if (!$this->subcollection && $this->notice->subcoll_id) {
			$this->subcollection = authorities_collection::get_authority('subcollection', $this->notice->subcoll_id);
		}
		return $this->subcollection;
	}

	/**
	 * Retourne $this->notice->ill
	 * @return string
	 */
	public function get_ill() {
		return $this->notice->ill;
	}

	/**
	 * Retourne $this->notice->size
	 * @return string
	 */
	public function get_size() {
		return $this->notice->size;
	}

	/**
	 * Retourne $this->notice->accomp
	 * @return string
	 */
	public function get_accomp() {
		return $this->notice->accomp;
	}

	/**
	 * Retourne $this->notice->prix
	 * @return string
	 */
	public function get_prix() {
		return $this->notice->prix;
	}

	/**
	 * Retourne $this->notice->n_gen
	 * @return string
	 */
	public function get_n_gen() {
		return $this->notice->n_gen;
	}

	/**
	 * Retourne le permalink
	 * @return string
	 */
	public function get_permalink() {
		if (!$this->permalink) {

		    $id = $this->id;
		    $url = UrlEntities::getPermalink(TYPE_NOTICE);

		    if ($this->notice->niveau_biblio == "b") {
		        $bull = $this->get_bul_info();
		        $id = $bull['bulletin_id'];
		        $url = UrlEntities::getPermalink(TYPE_BULLETIN);
		    }
		    $this->permalink = GlobalContext::urlBase() . str_replace("!!id!!", intval($id), $url);
		}
		return $this->permalink;
	}

	/**
	 * Retourne les données d'exemplaires
	 * @return array
	 */
	public function get_expls_datas() {
		if (!isset($this->expls_datas)) {
			$this->expls_datas = array();
			if((is_null($this->dom_2) && $this->get_parameter_value('show_exemplaires') && $this->is_visu_expl() && (!$this->is_visu_expl_abon() || ($this->is_visu_expl_abon() && $_SESSION["user_code"]))) || ($this->get_rights() & 8)) {
				$bull = $this->get_bul_info();
				if(isset($bull['bulletin_id'])) {
				    $bull_id = intval($bull['bulletin_id']);
				} else {
					$bull_id = 0;
				}
				$exemplaires = new exemplaires($this->get_id(), $bull_id, $this->get_niveau_biblio());
				$this->expls_datas = $exemplaires->get_data();
			}
		}
		return $this->expls_datas;
	}

	/**
	 * Retourne la disponibilité
	 * @return array $this->availibility = array('availibility', 'next-return')
	 */
	public function get_availability() {
		if (!$this->availability) {
			$expls_datas = $this->get_expls_datas();
			$next_return = "";
			$availability = "unavailable";
			if (isset($expls_datas['expls']) && count($expls_datas['expls'])) {
				foreach ($expls_datas['expls'] as $expl) {
					if ($expl['pret_flag']) { // Pretable
						if ($expl['flag_resa']) { // Réservé
							if(!$next_return) {
								$availability = "reserved";
							}
						} else if ($expl['pret_retour']) { // Sorti
							if (!$next_return || ($next_return > $expl['pret_retour'])) {
								$next_return = $expl['pret_retour'];
								$availability = "out";
							}
						} else {
							$availability = "available";
							break;
						}
					} else {
						$availability = "no_lendable";
					}
				}
			} else {
				// Pas d'exemplaires
				if($this->get_parameter_value('show_empty_items_block')) {
					$availability = "empty";
				} else {
					$availability = "none";
				}
			}
			$this->availability = array(
					'availability' => $availability,
					'next_return' => formatdate($next_return)
			);
		}
		return $this->availability;
	}

	/**
	 * Retourne la disponibilité d'un exemplaire numérique
	 */
	public function get_numeric_expl_availability() {
		return array(
				'availability' => 'available',
				//'next_return' => formatdate()
		);
	}

	/**
	 * Retourne le tableau des ids des notices du même auteur
	 * @return array
	 */
	public function get_records_from_same_author() {
		if (!isset($this->records_from_same_author)) {
			$this->records_from_same_author = array();

			$this->get_responsabilites();
			$as = array_search("0", $this->responsabilites["responsabilites"]);
			if (($as !== FALSE) && ($as !== NULL)) {
				$authors_ids = $this->responsabilites["auteurs"][$as]['id'];
			} else {
				$as = array_keys($this->responsabilites["responsabilites"], "1");
				$authors_ids = "";
				for ($i = 0; $i < count($as); $i++) {
					$indice = $as[$i];
					if ($authors_ids) $authors_ids .= ",";
					$authors_ids .= $this->responsabilites["auteurs"][$indice]['id'];
				}
			}

			if ($authors_ids) {
				$query = "select distinct responsability_notice from responsability where responsability_author in (".$authors_ids.") and responsability_notice != ".$this->id." order by responsability_type, responsability_ordre";
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_author[] = $record->responsability_notice;
					}
				}
			}
		}
		$filter = new filter_results($this->records_from_same_author);
		$this->records_from_same_author = explode(",",$filter->get_results());
		return $this->records_from_same_author;
	}

	/**
	 * Retourne le tableau des ids des notices du même éditeur
	 * @return array
	 */
	public function get_records_from_same_publisher() {
		if (!isset($this->records_from_same_publisher)) {
			$this->records_from_same_publisher = array();

			if ($this->notice->ed1_id) {
				$query = "select distinct notice_id from notices where ed1_id = ".$this->notice->ed1_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_publisher[] = $record->notice_id;
					}
				}
			}
		}
		$filter = new filter_results($this->records_from_same_publisher);
		$this->records_from_same_publisher = explode(",",$filter->get_results());
		return $this->records_from_same_publisher;
	}

	/**
	 * Retourne le tableau des ids des notices de la même collection
	 * @return array
	 */
	public function get_records_from_same_collection() {
		if (!isset($this->records_from_same_collection)) {
			$this->records_from_same_collection = array();

			if ($this->notice->coll_id) {
				$query = "select distinct notice_id from notices where coll_id = ".$this->notice->coll_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_collection[] = $record->notice_id;
					}
				}
			}
		}
		$filter = new filter_results($this->records_from_same_collection);
		$this->records_from_same_collection = explode(",",$filter->get_results());
		return $this->records_from_same_collection;
	}

	/**
	 * Retourne le tableau des ids des notices de la même série
	 * @return array
	 */
	public function get_records_from_same_serie() {
		if (!isset($this->records_from_same_serie)) {
			$this->records_from_same_serie = array();

			if ($this->notice->tparent_id) {
				$query = "select distinct notice_id from notices where tparent_id = ".$this->notice->tparent_id." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_serie[] = $record->notice_id;
					}
				}
			}
		}
		$filter = new filter_results($this->records_from_same_serie);
		$this->records_from_same_serie = explode(",",$filter->get_results());
		return $this->records_from_same_serie;
	}

	/**
	 * Retourne le tableau des ids des notices avec la même indexation décimale
	 * @return array
	 */
	public function get_records_from_same_indexint() {
		if (!isset($this->records_from_same_indexint)) {
			$this->records_from_same_indexint = array();

			if ($this->notice->indexint) {
				$query = "select distinct notice_id from notices where indexint = ".$this->notice->indexint." and notice_id != ".$this->id;
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($record = pmb_mysql_fetch_object($result)) {
						$this->records_from_same_indexint[] = $record->notice_id;
					}
				}
			}
		}
		$filter = new filter_results($this->records_from_same_indexint);
		$this->records_from_same_indexint = explode(",",$filter->get_results());
		return $this->records_from_same_indexint;
	}

	/**
	 * Retourne le tableau des ids de notices avec des catégories communes
	 * @return array
	 */
	public function get_records_from_same_categories() {
		if (!$this->records_from_same_categories) {
			$this->records_from_same_categories = array();

			$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories where num_noeud in (select num_noeud from notices_categories where notcateg_notice = ".$this->id.") group by notcateg_notice order by pert desc";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($record = pmb_mysql_fetch_object($result)) {
					$this->records_from_same_categories[] = $record->notcateg_notice;
				}
			}
		}
		$filter = new filter_results($this->records_from_same_categories);
		$this->records_from_same_categories = explode(",",$filter->get_results());
		return $this->records_from_same_categories;
	}

	/**
	 * Retourne l'URL calculée de l'image
	 * @return string
	 */
	public function get_picture_url() {
	    if (empty($this->picture_url)) {
	        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
	        $this->picture_url = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $this->id);
	    }
	    return $this->picture_url;
	}

	/**
	 * Retourne le texte au survol de l'image
	 * @return string
	 */
	public function get_picture_title() {

		if (!$this->picture_title && ($this->get_code() || $this->get_thumbnail_url())) {
			global $charset;
			if ($this->get_parameter_value('show_book_pics')=='1' && ($this->get_parameter_value('book_pics_url') || $this->get_thumbnail_url())) {
				if ($this->get_thumbnail_url()) {
					$this->picture_title = htmlentities($this->get_tit1(), ENT_QUOTES, $charset);
				} else {
					$this->picture_title = htmlentities($this->get_parameter_value('book_pics_msg'), ENT_QUOTES, $charset);
				}
			}
		}
		return $this->picture_title;
	}

	public function get_pnb_datas() {
	    // $allow_pnb = Droit à l'emprunt de document numérique
	    global $allow_pnb, $opac_pnb_loan_display_mode;

	    $this->pnb_datas = array(
	        'flag_pnb_visible' => false,
	        'href' => "#",
	        'onclick' => "",
	    );
	    $record_datas = record_display::get_record_datas($this->id);
	    if ($record_datas->is_numeric()) {
	        if ($record_datas->get_availability() && $_SESSION["user_code"] && $allow_pnb && dilicom::is_pnb_active()) {
	            $this->pnb_datas['flag_pnb_visible'] = true;
	            $this->pnb_datas['onclick'] ="pnb_post_loan_info(" . $this->id . ",".$opac_pnb_loan_display_mode.");return false;";
	        }
	    }
	    return $this->pnb_datas;
	}

	/**
	 * Retourne les informations de réservation
	 * @return array $this->resas_datas = array('nb_resas', 'href', 'onclick', 'flag_max_resa', 'flag_resa_visible')
	 */
	public function get_resas_datas() {
		if (!isset($this->resas_datas)) {
			global $msg;
			global $opac_resa ;
			global $opac_max_resa ;
			global $opac_show_exemplaires ;
			global $popup_resa ;
			global $opac_resa_popup ; // la résa se fait-elle par popup ?
			global $opac_resa_planning; // la résa est elle planifiée
			global $allow_book;
			global $opac_show_exemplaires_analysis;

			$this->resas_datas = array(
					'nb_resas' => 0,
					'href' => "#",
					'onclick' => "",
					'flag_max_resa' => false,
					'flag_resa_visible' => true,
					'flag_resa_possible' => true
			);
			$bul_info = $this->get_bul_info();
			if(isset($bul_info['bulletin_id'])) {
				$bulletin_id = $bul_info['bulletin_id'];
			} else {
				$bulletin_id = 0;
			}
			if ($bulletin_id) $requete_resa = "SELECT count(1) FROM resa WHERE resa_idbulletin='$bulletin_id' ";
			else $requete_resa = "SELECT count(1) FROM resa WHERE resa_idnotice='$this->id' ";
			$this->resas_datas['nb_resas'] = pmb_mysql_result(pmb_mysql_query($requete_resa), 0, 0) ;

			if ((is_null($this->dom_2) && $opac_show_exemplaires && $this->is_visu_expl() && (!$this->is_visu_expl_abon() || ($this->is_visu_expl_abon() && $_SESSION["user_code"]))) || ($this->get_rights() & 8)) {
				if (!$opac_resa_planning) {
					if($bulletin_id) $resa_check=check_statut(0,$bulletin_id) ;
					else $resa_check=check_statut($this->id, 0) ;
					// vérification si exemplaire réservable
					if ($resa_check) {
						if (($this->get_niveau_biblio()=="m" || $this->get_niveau_biblio()=="b" || ($this->get_niveau_biblio()=="a" && $opac_show_exemplaires_analysis)) && ($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
							if ($opac_max_resa==0 || $opac_max_resa>$this->resas_datas['nb_resas']) {
								if ($opac_resa_popup) {
									$this->resas_datas['onclick'] = "if(confirm('".$msg["confirm_resa"]."')){w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;}else return false;";
								} else {
									$this->resas_datas['href'] = "./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup";
									$this->resas_datas['onclick'] = "return confirm('".$msg["confirm_resa"]."')";
								}
							} else $this->resas_datas['flag_max_resa'] = true;
						} elseif (($this->get_niveau_biblio()=="m" || $this->get_niveau_biblio()=="b" || ($this->get_niveau_biblio()=="a" && $opac_show_exemplaires_analysis)) && !($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
							// utilisateur pas connecté
							// préparation lien réservation sans être connecté
							if ($opac_resa_popup) {
								$this->resas_datas['onclick'] = "if(confirm('".$msg["confirm_resa"]."')){w=window.open('./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;}else return false;";
							} else {
								$this->resas_datas['href'] = "./do_resa.php?lvl=resa&id_notice=".$this->id."&id_bulletin=".$bulletin_id."&oresa=popup";
								$this->resas_datas['onclick'] = "return confirm('".$msg["confirm_resa"]."')";
							}
						} else {
							$this->resas_datas['flag_resa_visible'] = false;
							$this->resas_datas['flag_resa_possible'] = false;
						}
					} else {
						$this->resas_datas['flag_resa_possible'] = false;
					} // fin if resa_check
				} else {
					// planning de réservations
					$this->resas_datas['nb_resas'] = resa_planning::count_resa($this->id);
					if (($this->get_niveau_biblio()=="m") && ($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
						if ($opac_max_resa==0 || $opac_max_resa>$this->resas_datas['nb_resas']) {
							if ($opac_resa_popup) {
								$this->resas_datas['onclick'] = "w=window.open('./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;";
							} else {
								$this->resas_datas['href'] = "./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup";
							}
						} else $this->resas_datas['flag_max_resa'] = true;
					} elseif (($this->get_niveau_biblio()=="m") && !($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
						// utilisateur pas connecté
						// préparation lien réservation sans être connecté
						if ($opac_resa_popup) {
							$this->resas_datas['onclick'] = "w=window.open('./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup','doresa','scrollbars=yes,width=500,height=600,menubar=0,resizable=yes'); w.focus(); return false;";
						} else {
							$this->resas_datas['href'] = "./do_resa.php?lvl=resa_planning&id_notice=".$this->id."&oresa=popup";
						}
					}
				}
			} else {
				$this->resas_datas['flag_resa_visible'] = false;
			}
		}
		return $this->resas_datas;
	}

	/**
	 * Retourne vrai si nouveauté, false sinon
	 * @return boolean
	 */
	public function is_new() {
		if ($this->notice->notice_is_new) {
			return true;
		}
		return false;
	}

	/**
	 * Retourne le tableau des relations parentes
	 * @return array
	 */
	public function get_relations_up() {
		if (!isset($this->relations_up)) {
			$this->relations_up = array();

			$notice_relations = notice_relations_collection::get_object_instance($this->id);
			$parents = $notice_relations->get_parents();
			foreach ($parents as $parents_relations) {
				foreach ($parents_relations as $parent) {
					if (!isset($this->relations_up[$parent->get_relation_type()]['label'])){
						$this->relations_up[$parent->get_relation_type()]['label'] = notice_relations::$liste_type_relation['up']->table[$parent->get_relation_type()];
						$this->relations_up[$parent->get_relation_type()]['relation_type'] = $parent->get_relation_type();
					}
					$this->relations_up[$parent->get_relation_type()]['parents'][] = $parent->get_linked_notice();
				}
			}

			foreach($this->relations_up as $key => $value){
				$filter = new filter_results($value['parents']);
				$this->relations_up[$key]['parents'] = explode(",",$filter->get_results());

				for($i = 0; $i < count($this->relations_up[$key]['parents']); $i++){
					if($this->relations_up[$key]['parents'][$i] == ''){
						unset($this->relations_up[$key]['parents'][$i]);
					}else{
						$this->relations_up[$key]['parents'][$i] = record_display::get_record_datas($this->relations_up[$key]['parents'][$i]);
					}
				}

				if(count($this->relations_up[$key]['parents']) == 0){
					unset($this->relations_up[$key]);
				}
			}
		}
		return $this->relations_up;
	}

	/**
	 * Retourne le tableau des relations enfants
	 * @return array
	 */
	public function get_relations_down() {
		if (!isset($this->relations_down)) {
			$this->relations_down = array();

			$notice_relations = notice_relations_collection::get_object_instance($this->id);
			$childs = $notice_relations->get_childs();
			foreach ($childs as $childs_relations) {
				foreach ($childs_relations as $child) {
					if (!isset($this->relations_down[$child->get_relation_type()]['label'])){
						$this->relations_down[$child->get_relation_type()]['label'] = notice_relations::$liste_type_relation['down']->table[$child->get_relation_type()];
						$this->relations_down[$child->get_relation_type()]['relation_type'] = $child->get_relation_type();
					}
					$this->relations_down[$child->get_relation_type()]['children'][] = $child->get_linked_notice();
				}
			}

			foreach($this->relations_down as $key => $value){
				$filter = new filter_results($value['children']);
				$this->relations_down[$key]['children'] = explode(",",$filter->get_results());

				for($i = 0; $i < count($this->relations_down[$key]['children']); $i++){
					if($this->relations_down[$key]['children'][$i] == ''){
						unset($this->relations_down[$key]['children'][$i]);
					}else{
						$this->relations_down[$key]['children'][$i] = record_display::get_record_datas($this->relations_down[$key]['children'][$i]);
					}
				}

				if(count($this->relations_down[$key]['children']) == 0){
					unset($this->relations_down[$key]);
				}
			}
		}
		return $this->relations_down;
	}

	/**
	 * Retourne le tableau des relations horizontales
	 * @return array
	 */
	public function get_relations_both() {
		if (!isset($this->relations_both)) {
			$this->relations_both = array();

			$notice_relations = notice_relations_collection::get_object_instance($this->id);
			$pairs = $notice_relations->get_pairs();
			foreach ($pairs as $pairs_relations) {
				foreach ($pairs_relations as $pair) {
					if (!isset($this->relations_both[$pair->get_relation_type()]['label'])){
						$this->relations_both[$pair->get_relation_type()]['label'] = notice_relations::$liste_type_relation['both']->table[$pair->get_relation_type()];
						$this->relations_both[$pair->get_relation_type()]['relation_type'] = $pair->get_relation_type();
					}
					$this->relations_both[$pair->get_relation_type()]['pairs'][] = $pair->get_linked_notice();
				}
			}

			foreach($this->relations_both as $key => $value){
				$filter = new filter_results($value['pairs']);
				$this->relations_both[$key]['pairs'] = explode(",",$filter->get_results());

				for($i = 0; $i < count($this->relations_both[$key]['pairs']); $i++){
					if($this->relations_both[$key]['pairs'][$i] == ''){
						unset($this->relations_both[$key]['pairs'][$i]);
					}else{
						$this->relations_both[$key]['pairs'][$i] = record_display::get_record_datas($this->relations_both[$key]['pairs'][$i]);
					}
				}

				if(count($this->relations_both[$key]['pairs']) == 0){
					unset($this->relations_both[$key]);
				}
			}
		}
		return $this->relations_both;
	}

	public function get_special() {
	   return record_display::get_special($this->id);
	}

	/**
	 * Retourne les dépouillements
	 * @return string Tableau des affichage des articles
	 */
	public function get_articles() {
		if (!isset($this->articles)) {
			$this->articles = array();

			$bul_info = $this->get_bul_info();
			$bulletin_id = $bul_info['bulletin_id'];

			$query = "SELECT analysis_notice FROM analysis, notices, notice_statut WHERE analysis_bulletin=".$bulletin_id." AND notice_id = analysis_notice AND statut = id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").") order by analysis_notice";
			$result = @pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while(($article = pmb_mysql_fetch_object($result))) {
					$this->articles[] = record_display::get_display_in_result($article->analysis_notice);
				}
			}
		}
		return $this->articles;
	}

	/**
	 * Retourne le nombre d'article associés à un bulletin
	 * @param number $bulletin_id
	 * @return number $nb_articles
	 */
	public function get_nb_articles($bulletin_id = 0) {

	    $acces_j = "";
	    $statut_j = "";
	    $statut_r = "";
	    $nb_articles = 0;

	    if (!$bulletin_id) {
    	    $bul_info = $this->get_bul_info();
    	    $bulletin_id = $bul_info['bulletin_id'];
	    }

	    //Droits d'accès
	    if (is_null($this->dom_2)) {

    	    $statut_a = "";
    	    if ($_SESSION["user_code"]) {
    	        $statut_a = " OR (notice_visible_opac_abon = 1 AND notice_visible_opac = 1)";
    	    }
	        $statut_j=', notice_statut';
	        $statut_r="AND statut=id_notice_statut AND ((notice_visible_opac = 1 AND notice_visible_opac_abon = 0)".$statut_a.")";
	    } else {
	        $acces_j = $this->dom_2->getJoin($_SESSION['id_empr_session'], 4, 'notice_id');
	    }

	    $query = "SELECT COUNT(*) FROM analysis, notices".$acces_j.$statut_j." WHERE analysis_bulletin=".$bulletin_id;
	    $query .= " AND notice_id = analysis_notice ".$statut_r;

	    $result = pmb_mysql_query($query);
	    if($result) {
			$nb_articles = intval(pmb_mysql_result($result, 0, 0));
	    }

	    return $nb_articles;
	}

	/**
	 * Retourne les données de demandes
	 * @return string Tableau des données ['themes' => ['id', 'label'], 'types' => ['id', 'label']]
	 */
	public function get_demands_datas() {
		if (!isset($this->demands_datas)) {
			$this->demands_datas = array(
					'themes' => array(),
					'types' => array()
			);

			// On va chercher les thèmes
			$query = "select id_theme, libelle_theme from demandes_theme";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($theme = pmb_mysql_fetch_object($result)) {
					$this->demands_datas['themes'][] = array(
							'id' => $theme->id_theme,
							'label' => $theme->libelle_theme
					);
				}
			}

			// On va chercher les types
			$query = "select id_type, libelle_type from demandes_type";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($theme = pmb_mysql_fetch_object($result)) {
					$this->demands_datas['types'][] = array(
							'id' => $theme->id_type,
							'label' => $theme->libelle_type
					);
				}
			}
		}
		return $this->demands_datas;
	}

	/**
	 * Retourne l'autorisation d'afficher le panier en fonction des paramètres opac et de la connexion de l'utilisateur
	 * @return boolean true si le panier est autoriser, false sinon
	 */
	public function is_cart_allow() {
		if (!isset($this->cart_allow)) {
			$this->cart_allow = ($this->get_parameter_value('cart_allow') && (!$this->get_parameter_value('cart_only_for_subscriber') || ($this->get_parameter_value('cart_only_for_subscriber') && $_SESSION["user_code"])));
		}
		return $this->cart_allow;
	}

	/**
	 * Retourne la présence ou non de la notice dans le panier
	 * @return boolean true si la notice est déjà dans le panier, false sinon
	 */
	public function is_in_cart() {
		if (!isset($this->in_cart)) {
			if(isset($_SESSION['cart']) && in_array($this->id, $_SESSION["cart"])) {
				$this->in_cart = true;
			} else {
				$this->in_cart = false;
			}
		}
		return $this->in_cart;
	}

	public function check_accessibility_explnum($explnum_id=0) {
		global $opac_show_links_invisible_docnums;

		$explnum_id = intval($explnum_id);

		//vérification de la visibilité si non connecté
		if(!$_SESSION['id_empr_session'] && $opac_show_links_invisible_docnums){
			$visible = false;
			if (!is_null($this->dom_3)) {
				$right = $this->dom_3->getRights(0,$explnum_id,16);
				if($right == 16){
					$visible = true;
				}
			}else{
				$sql = "select explnum_id from explnum join explnum_statut on id_explnum_statut = explnum_docnum_statut where explnum_visible_opac= 1 and explnum_visible_opac_abon = 0 and explnum_id = ".$explnum_id;
				if(pmb_mysql_num_rows(pmb_mysql_query($sql))){
					$visible = true;
				}
			}
		}
		if(!$_SESSION['id_empr_session'] && $opac_show_links_invisible_docnums && !$visible){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retourne les infos de documents numériques associés à la notice
	 * @return array
	 */
	public function get_explnums_datas() {
		if (!isset($this->explnums_datas)) {
			global $msg;
			global $charset;
			global $opac_url_base;
			global $opac_visionneuse_allow;
			global $opac_photo_filtre_mimetype;
			global $opac_explnum_order;
			global $opac_show_links_invisible_docnums;
			global $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;

			$allowed_mimetype  =array();

			$this->explnums_datas = array(
					'nb_explnums' => 0,
					'explnums' => array(),
					'visionneuse_script' => '
								<script>
									if(typeof(sendToVisionneuse) == "undefined"){
										var sendToVisionneuse = function (explnum_id){
											document.getElementById("visionneuseIframe").src = "visionneuse.php?"+(typeof(explnum_id) != "undefined" ? "explnum_id="+explnum_id : "");
										}
									}
								</script>'
			);

			//Ne pas lancer la requête SQL suivante si l'identifiant de la notice n'est pas connu
			if(!$this->id) {
				return $this->explnums_datas;
			}

			global $_mimetypes_bymimetype_, $_mimetypes_byext_ ;
			if (!is_array($_mimetypes_bymimetype_) || !count($_mimetypes_bymimetype_)) {
				create_tableau_mimetype();
			}

			$this->get_bul_info();

			// récupération du nombre d'exemplaires
			$query = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut,
				explnum_create_date,
				DATE_FORMAT(explnum_create_date,'".$msg['format_date']."') as formated_create_date,
				explnum_update_date, DATE_FORMAT(explnum_update_date,'".$msg['format_date']."') as formated_update_date,
				explnum_file_size
				FROM explnum WHERE ";
			if ($this->get_niveau_biblio() == 'b' && !empty($this->parent['bulletin_id'])) {
			    $query .= "explnum_bulletin='".$this->parent['bulletin_id']."' or explnum_notice='".$this->id."' ";
			} else {
			    $query .= "explnum_notice='".$this->id."' ";
			}

			$query.= "union SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut,
				explnum_create_date, DATE_FORMAT(explnum_create_date,'".$msg['format_date']."') as formated_create_date,
				explnum_update_date, DATE_FORMAT(explnum_update_date,'".$msg['format_date']."') as formated_update_date,
				explnum_file_size
				FROM explnum, bulletins
				WHERE bulletin_id = explnum_bulletin
				AND bulletins.num_notice='".$this->id."'";
			if ($this->get_parameter_value('explnum_order')) $query .= " order by ".$this->get_parameter_value('explnum_order');
			else $query .= " order by explnum_mimetype, explnum_nom, explnum_id ";
			$res = pmb_mysql_query($query);
			$nb_explnums = pmb_mysql_num_rows($res);

			$docnum_visible = true;
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$docnum_visible = $this->dom_2->getRights($_SESSION['id_empr_session'],$this->id,16);
			} else {
				$query = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices, notice_statut WHERE notice_id ='".$this->id."' and id_notice_statut=statut ";
				$result = pmb_mysql_query($query);
				if($result && pmb_mysql_num_rows($result)) {
					$statut_temp = pmb_mysql_fetch_object($result);
					if(!$statut_temp->explnum_visible_opac)	$docnum_visible=false;
					if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$docnum_visible=false;
				} else 	$docnum_visible=false;
			}

			if ($nb_explnums && ($docnum_visible || $this->get_parameter_value('show_links_invisible_docnums'))) {
				// on récupère les données des exemplaires
				global $search_terms;
				while (($expl = pmb_mysql_fetch_object($res))) {
					// couleur de l'img en fonction du statut
					if ($expl->explnum_docnum_statut) {
						$rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='".$expl->explnum_docnum_statut."' ";
						$Query_statut = pmb_mysql_query($rqt_st)or die ($rqt_st. " ".pmb_mysql_error()) ;
						$r_statut = pmb_mysql_fetch_object($Query_statut);
						$explnum_class = 'docnum_'.$r_statut->class_html;
						if ($expl->explnum_docnum_statut>1) {
							$explnum_opac_label = $r_statut->opac_libelle;
						} else $explnum_opac_label = '';
					} else {
						$explnum_class = 'docnum_statutnot1';
						$explnum_opac_label = '';
					}

					$explnum_docnum_visible = true;
					$explnum_docnum_consult = true;
					if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
						$explnum_docnum_visible = $this->dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,16);
						$explnum_docnum_consult = $this->dom_3->getRights($_SESSION['id_empr_session'],$expl->explnum_id,4);
					} else {
						$requete = "SELECT explnum_visible_opac, explnum_visible_opac_abon, explnum_consult_opac, explnum_consult_opac_abon FROM explnum, explnum_statut WHERE explnum_id ='".$expl->explnum_id."' and id_explnum_statut=explnum_docnum_statut ";
						$myQuery = pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($myQuery)) {
							$statut_temp = pmb_mysql_fetch_object($myQuery);
							if(!$statut_temp->explnum_visible_opac)	{
								$explnum_docnum_visible=false;
							}
							if(!$statut_temp->explnum_consult_opac)	{
								$explnum_docnum_consult=false;
							}
							if($statut_temp->explnum_visible_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_visible=false;
							if($statut_temp->explnum_consult_opac_abon && !$_SESSION['id_empr_session'])	$explnum_docnum_consult=false;
						} else {
							$explnum_docnum_visible=false;
						}
					}

					// mémorisation des localisations
					$locations = array();
					$ids_loc = array();
					$requete_loc = "SELECT num_location, location_libelle FROM explnum_location JOIN docs_location ON num_location=idlocation WHERE location_visible_opac = 1 AND num_explnum=".$expl->explnum_id;
					$result_loc = pmb_mysql_query($requete_loc);
					if (pmb_mysql_num_rows($result_loc)) {
						while($loc = pmb_mysql_fetch_object($result_loc)) {
							$locations[] = array(
									'id' => $loc->num_location,
							        'label' => translation::get_translated_text($loc->num_location, "docs_location", "location_libelle", $loc->location_libelle),
							);
							$ids_loc[] = $loc->num_location;
						}
					}
					if ($explnum_docnum_visible ||  $this->get_parameter_value('show_links_invisible_docnums')) {
						$this->explnums_datas['nb_explnums']++;
						$explnum_datas = array(
								'id' => $expl->explnum_id,
								'expl_location'	=> $ids_loc,
								'name' => $expl->explnum_nom,
								'mimetype' => $expl->explnum_mimetype,
								'url' => $expl->explnum_url,
								'filename' => $expl->explnum_nomfichier,
								'extension' => $expl->explnum_extfichier,
								'locations' => $locations,
								'statut' => $expl->explnum_docnum_statut,
								'consultation' => $explnum_docnum_consult,
								'create_date' => $expl->explnum_create_date,
								'formated_create_date' => $expl->formated_create_date,
								'update_date' => $expl->explnum_update_date,
								'formated_update_date' => $expl->formated_update_date,
								'file_size' => $expl->explnum_file_size,
								'id_notice' => $this->id,
								'id_bulletin' => (isset($this->parent['bulletin_id']) ? $this->parent['bulletin_id'] : ''),
								'lenders' => $this->get_lenders(false)
						);

					    $explnum_datas['has_vignette'] = true;
						$explnum_datas['thumbnail_url'] = $this->get_parameter_value('url_base').'vig_num.php?explnum_id='.$expl->explnum_id;

						$words_to_find="";
						if (($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
							if (is_array($search_terms)) {
								$words_to_find = "#search=\"".trim(str_replace('*','',implode(' ',$search_terms)))."\"";
							}
						}
						$explnum_datas['access_datas'] = array(
								'script' => '',
								'href' => '#',
								'onclick' => ''
						);
						//si l'affichage du lien vers les documents numériques est forcé et qu'on est pas connecté, on propose l'invite de connexion!
						if(!$explnum_docnum_visible && $this->get_parameter_value('show_links_invisible_docnums') && !$_SESSION['id_empr_session']){
							if ($this->get_parameter_value('visionneuse_allow')) {
								$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
							}
							if ($explnum_docnum_consult && $allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
								$explnum_datas['access_datas']['script'] = "
								<script>
									function sendToVisionneuse_".$expl->explnum_id."(){
										open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");
									}
								</script>";
								$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&callback_func=sendToVisionneuse_".$expl->explnum_id."');";
							}else{
								$explnum_datas['access_datas']['onclick'] = "auth_popup('./ajax.php?module=ajax&categ=auth&new_tab=1&callback_url=".rawurlencode($this->get_parameter_value('url_base')."doc_num.php?explnum_id=".$expl->explnum_id)."'); return false;";
							}
						}else{
							if ($this->get_parameter_value('visionneuse_allow'))
								$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
							if ($explnum_docnum_consult && $allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
								$explnum_datas['access_datas']['onclick'] = "open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;";
							} else {
								$explnum_datas['access_datas']['href'] = $this->get_parameter_value('url_base').'doc_num.php?explnum_id='.$expl->explnum_id;
							}
						}

						$explnum_datas['p_perso'] = explnum::get_p_perso($explnum_datas['id']);

						if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
						elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explnum_datas['mimetype_label'] = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
						else $explnum_datas['mimetype_label'] = $expl->explnum_mimetype ;

						// regime de licence
						$explnum_datas["licence"] = [];
						$query_licence = "SELECT * FROM explnum_licence_profile_explnums
                        JOIN explnum_licence_profiles ON id_explnum_licence_profile = explnum_licence_profile_explnums_profile_num
                        JOIN explnum_licence ON id_explnum_licence = explnum_licence_profile_explnum_licence_num
                        WHERE explnum_licence_profile_explnums_explnum_num = ".$expl->explnum_id;
						$result_licence = pmb_mysql_query($query_licence);
						if (pmb_mysql_num_rows($result_licence)) {
						    $row = pmb_mysql_fetch_assoc($result_licence);
						    $explnum_datas["licence"] = [
						        "id" => $row["id_explnum_licence_profile"],
						        "label" => $row["explnum_licence_profile_label"],
						        "logo" => $row["explnum_licence_profile_logo_url"],
						        "explanation" => $row["explnum_licence_profile_explanation"],
						        "id_licence" => $row["id_explnum_licence"],
						        "licence_label" => $row["explnum_licence_label"],
						    ];
						}
						$this->explnums_datas['explnums'][] = $explnum_datas;
					}
				}
			}
		}
		return $this->explnums_datas;
	}

	/**
	 * Retourne le tableau des autorités persos associées à la notice
	 * @return authority
	 */
	public function get_authpersos() {
		if (isset($this->authpersos)) {
			return $this->authpersos;
		}
		$query = 'select notice_authperso_authority_num, notice_authperso_order from notices_authperso
				JOIN authperso_authorities ON id_authperso_authority = notice_authperso_authority_num
				where notices_authperso.notice_authperso_notice_num = '.$this->id.'
				order by authperso_authority_authperso_num, notice_authperso_order';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				//$this->authpersos[] = new authority(0, $row->notice_authperso_authority_num, AUT_TABLE_AUTHPERSO);
				$this->authpersos[] = authorities_collection::get_authority('authority', 0, ['num_object' => $row->notice_authperso_authority_num, 'type_object' => AUT_TABLE_AUTHPERSO]);
			}
		}
		return $this->authpersos;
	}

	/**
	 * Retourne le tableau des autorités persos classées associées à la notice
	 * @return authority
	 */
	public function get_authpersos_ranked() {
		if (isset($this->authpersos_ranked)) {
			return $this->authpersos_ranked;
		}
		$this->authpersos_ranked = array();
		$query = 'select distinct authperso_authority_authperso_num, notice_authperso_authority_num, notice_authperso_order from notices_authperso
				JOIN authperso_authorities ON id_authperso_authority = notice_authperso_authority_num
				where notices_authperso.notice_authperso_notice_num = '.$this->id.'
				order by authperso_authority_authperso_num, notice_authperso_order';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				//$this->authpersos_ranked[$row->authperso_authority_authperso_num][] = new authority(0, $row->notice_authperso_authority_num, AUT_TABLE_AUTHPERSO);
				$this->authpersos_ranked[$row->authperso_authority_authperso_num][] = authorities_collection::get_authority('authority', 0, ['num_object' => $row->notice_authperso_authority_num, 'type_object' => AUT_TABLE_AUTHPERSO]);
			}
		}
		return $this->authpersos_ranked;
	}

	/**
	 * Retourne $this->notice->opac_serialcirc_demande
	 */
	public function get_opac_serialcirc_demande() {
		return $this->notice->opac_serialcirc_demande;
	}

	/**
	 * Retourne $this->notice->opac_visible_bulletinage
	 */
	public function get_opac_visible_bulletinage() {
		return $this->notice->opac_visible_bulletinage;
	}

	/**
	 * Retourne les informations de notice externe
	 */
	public function get_external_rec_id() {
		if(!isset($this->external_rec_id)) {
			$this->external_rec_id = array();
			$query = "SELECT recid FROM notices_externes WHERE num_notice = " . $this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$recid = pmb_mysql_result($result, 0,0);
				$data = explode(" ", $recid);
				$this->external_rec_id = array(
						'recid' => $recid,
						'connector' => $data[0],
						'source_id' => $data[1],
						'ref' => $data[2]
				);
			}
		}
		return $this->external_rec_id;
	}

	/**
	 * Retourne l'affichage réduit d'une notice
	 */
	public function get_aff_notice_reduit() {

		return aff_notice($this->id, 1, 1, 0, AFF_ETA_NOTICES_REDUIT);
	}

	/**
	 * Retourne les informations des onglets perso de la notice
	 */
	public function get_onglets_perso() {
		if (!isset($this->onglet_perso)) {
			$ids_tpl = array();
			$onglets = explode(",", $this->get_parameter_value('notices_format_onglets'));
			foreach($onglets as $id_tpl) {
				if(is_numeric($id_tpl)) {
					$ids_tpl[] = $id_tpl;
				}
			}
			$this->onglet_perso = new notice_onglets(implode(',', $ids_tpl));
			$this->onglet_perso->build_onglets($this->id, '');
		}
		return $this->onglet_perso->get_data_onglets_perso();
	}

	/**
	 * Retourne les informations du périodique
	 */
	public function get_serial() {
		if (!isset($this->serial)) {
			$this->serial = new stdClass();
			$query = "";
			if ($this->notice->niveau_hierar == 2) {
				if ($this->notice->niveau_biblio == 'a') {
					$query = "SELECT bulletin_notice FROM bulletins JOIN analysis ON analysis_bulletin = bulletin_id WHERE analysis_notice = ".$this->id;
				} elseif ($this->notice->niveau_biblio == 'b') {
					$query = "SELECT bulletin_notice FROM bulletins WHERE num_notice = ".$this->id;
				}
			}
			if ($query) {
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					$this->serial = record_display::get_record_datas($row->bulletin_notice);
				}
			}
		}
		return $this->serial;
	}

	/**
	 * Affecte $external_parameters
	 */
	public function set_external_parameters($external_parameters) {
		$this->external_parameters = $external_parameters;
	}

	/**
	 * Retourne $external_parameters
	 */
	public function get_external_parameters() {
		return $this->external_parameters;
	}

	public static function format_url($url) {
		global $base_path;
		global $use_opac_url_base, $opac_url_base;

		if($use_opac_url_base) return $opac_url_base.$url;
		else return $base_path.'/'.$url;
	}

	/**
	 * Retourne vrai si la notice est numérique, false sinon
	 * @return boolean
	 */
	public function is_numeric() {
		if ($this->notice->is_numeric) {
			return true;
		}
		return false;
	}

	/**
	 * Retourne la date de création de la notice
	 * @return string
	 */
	public function get_create_date() {
		return formatdate($this->notice->create_date);
	}

	/**
	 * Retourne la date de mise à jour de la notice
	 * @return string
	 */
	public function get_update_date() {
		return formatdate($this->notice->update_date);
	}

	public function get_contributor() {
		$contributor = new stdClass();
		$query = "SELECT id_empr
			FROM empr
			JOIN audit ON user_id = id_empr
			JOIN notices ON object_id = notice_id AND type_obj=1 AND type_modif=1 AND type_user=1
			WHERE notice_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$id_empr = pmb_mysql_result($result, 0, 'id_empr');
			$contributor = new emprunteur($id_empr);
		}
		return $contributor;
	}

	public function get_coins() {
		$coins = array();
		switch ($this->get_niveau_biblio()){
			case 's':// periodique
				/*
				$coins['rft.genre'] = 'book';
				$coins['rft.btitle'] = $this->get_tit1();
				$coins['rft.title'] = $this->get_tit1();
				if ($this->get_code()){
					$coins['rft.issn'] = $this->get_code();
				}
				if ($this->get_npages()) {
					$coins['rft.epage'] = $this->get_npages();
				}
				if ($this->get_year()) {
					$coins['rft.date'] = $this->get_year();
				}
				*/
				break;
			case 'a': // article
				$parent = $this->get_bul_info();
				$coins['rft.genre'] = 'article';
				$title = $this->get_tit1();
				if ($this->get_tit4()) {
					$title .= ' : '.$this->get_tit4();
				}
				$coins['rft.atitle'] = $title;
				$coins['rft.jtitle'] = $parent['title'];
				if ($parent['numero']) {
				    $coins['rft.issue'] = $parent['numero'];
				}

				if($parent['date']){
					$coins['rft.date'] = $parent['date'];
				}elseif($parent['date_date']){
					$coins['rft.date'] = $parent['date_date'];
				}
				if ($this->get_code()){
					$coins['rft.issn'] = $this->get_code();
				}
				if ($this->get_npages()) {
					$coins['rft.epage'] = $this->get_npages();
				}
				break;
			case 'b': //Bulletin
				/*
				$coins['rft.genre'] = 'issue';
				$coins_span.="&amp;rft.btitle=".rawurlencode($f($this->notice->tit1." / ".$this->parent_title));
				if ($this->get_code()){
					$coins['rft.isbn'] = $this->get_code();
				}
				if ($this->get_npages()) {
					$coins['rft.epage'] = $this->get_npages();
				}
				if($this->bulletin_date) $coins_span.="&amp;rft.date=".rawurlencode($f($this->bulletin_date));
				*/
				break;
			case 'm':// livre
			default:
				$coins['rft.genre'] = 'book';
				$coins['rft.btitle'] = $this->get_tit1();

				$title="";
				$serie = $this->get_serie();
				if(isset($serie['name'])) {
					$title .= $serie['name'];
					if($this->get_tnvol()) $title .= ', '.$this->get_tnvol();
					$title .= '. ';
				}
				$title .= $this->get_tit1();
				if ($this->get_tit4()) {
					$title .= ' : '.$this->get_tit4();
				}
				$coins['rft.title'] = $title;
				if ($this->get_code()){
					$coins['rft.isbn'] = $this->get_code();
				}
				if ($this->get_npages()) {
					$coins['rft.tpages'] = $this->get_npages();
				}
				if ($this->get_year()) {
					$coins['rft.date'] = $this->get_year();
				}
				break;
		}

		if($this->get_niveau_biblio() != "b"){
			$coins['rft_id'] = $this->get_lien();
		}

		$collection = $this->get_collection();
		$subcollection = $this->get_subcollection();
		if($subcollection) {
			$coins['rft.series'] = $subcollection->name;
		} elseif ($collection) {
			$coins['rft.series'] = $collection->name;
		}

		$publishers = $this->get_publishers();
		if (count($publishers)) {
			$coins['rft.pub'] = $publishers[0]->name;
			if($publishers[0]->ville) {
				$coins['rft.place'] = $publishers[0]->ville;
			}
		}

		if($this->get_mention_edition()){
			$coins['rft.edition'] = $this->get_mention_edition();
		}

		$responsabilites = $this->get_responsabilites();
		if (count($responsabilites["auteurs"])) {
			$coins['rft.au'] = array();
			foreach($responsabilites["auteurs"] as $responsabilite){
				$coins['rft.au'][] = ($responsabilite['rejete'] ? $responsabilite['rejete'].' ' : '').$responsabilite['name'];
				if(empty($coins['rft.aulast'])) {
					if($responsabilite['name']) {
						$coins['rft.aulast'] = $responsabilite['name'];
						if($responsabilite['rejete']) {
							$coins['rft.aufirst'] = $responsabilite['rejete'];
						} else {
							$coins['rft.aufirst'] = '';
						}
					}
				}
			}
		}
		return $coins;
	}

	public function get_locations() {
	    $locations = array();

	    //Localisations des exemplaires
	    $query = "SELECT distinct idlocation, location_libelle FROM exemplaires JOIN docs_location ON docs_location.idlocation = exemplaires.expl_location WHERE expl_notice = '".$this->id."'";
	    $query .= " AND docs_location.location_visible_opac=1";
	    $result = pmb_mysql_query($query);
	    while ($row = pmb_mysql_fetch_object($result)) {
	        $locations[] = array(
	            'label' => translation::get_translated_text($row->idlocation, "docs_location", "location_libelle", $row->location_libelle)
	        );
	    }
	    //Localisations des documents numériques
	    $query = "SELECT distinct idlocation, location_libelle FROM explnum JOIN explnum_location ON explnum_location.num_explnum = explnum.explnum_id JOIN docs_location ON docs_location.idlocation = explnum_location.num_location WHERE explnum_notice = '".$this->id."'";
	    $query .= " AND docs_location.location_visible_opac=1";
	    $result = pmb_mysql_query($query);
	    while ($row = pmb_mysql_fetch_object($result)) {
	        $locations[] = array(
	            'label' => translation::get_translated_text($row->idlocation, "docs_location", "location_libelle", $row->location_libelle)
	        );
	    }
	    return $locations;
	}

	public function get_lenders($with_expl_lender = true) {
	    $lenders = array();

	    //Localisations des exemplaires
	    if($with_expl_lender) {
		    $query = "SELECT distinct lender_libelle
            FROM exemplaires
            JOIN lenders ON lenders.idlender = exemplaires.expl_owner
            WHERE ";
		    if ($this->get_niveau_biblio() == 'b' && !empty($this->parent['bulletin_id'])) {
		        $query .= "expl_bulletin ='".$this->parent['bulletin_id']."' or expl_notice ='".$this->id."' ";
		    } else {
		        $query .= "expl_notice ='".$this->id."' ";
		    }
		    $result = pmb_mysql_query($query);
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $lenders[] = array(
		            'label' => $row->lender_libelle
		        );
		    }
	    }
	    //Localisations des documents numériques
	    $query = "SELECT distinct lender_libelle
        FROM explnum
        JOIN explnum_lenders ON explnum_lenders.explnum_lender_num_explnum = explnum.explnum_id
        JOIN lenders ON lenders.idlender = explnum_lenders.explnum_lender_num_lender
        WHERE ";
	    if ($this->get_niveau_biblio() == 'b' && !empty($this->parent['bulletin_id'])) {
	        $query .= "explnum_bulletin='".$this->parent['bulletin_id']."' or explnum_notice='".$this->id."' ";
	    } else {
	        $query .= "explnum_notice='".$this->id."' ";
	    }
	    $result = pmb_mysql_query($query);
	    while ($row = pmb_mysql_fetch_object($result)) {
	        $lenders[] = array(
        		'label' => $row->lender_libelle
	        );
	    }
	    return $lenders;
	}

	protected function get_parameter_value($name) {
		$parameter_name = 'opac_'.$name;
		global ${$parameter_name};
		return ${$parameter_name};
	}

	/**
	 * Renvoie le lien pour contribuer sur un exemplaire de la notice
	 * @return string
	 */
	public function get_expl_contribution_link() {
		if (isset($this->expl_contribution_link)) {
			return $this->expl_contribution_link;
		}
		$this->expl_contribution_link = '';
		global $opac_contribution_area_activate, $allow_contribution;
		if (!$opac_contribution_area_activate || !$allow_contribution) {
			return $this->expl_contribution_link;
		}
		$shorturl_type_contribution = new shorturl_type_contribution();
		$this->expl_contribution_link = $shorturl_type_contribution->get_shorturl('contribute', array(
				'sub' => 'expl',
				'default_fields' => array(
						'http://www.pmbservices.fr/ontology#has_record' => array(
								array(
										'display_label' => $this->get_tit1(),
										'value' => $this->id,
										'type' => 'http://www.pmbservices.fr/ontology#record'
								)
						)
				),
		        'typdoc' =>$this->get_typdoc()
		));
		return $this->expl_contribution_link;
	}

	/**
	 * Renvoie le lien pour contribuer sur un document numérique de la notice
	 * @return string
	 */
	public function get_explnum_contribution_link() {
	    if (isset($this->explnum_contribution_link)) {
	        return $this->explnum_contribution_link;
		}
		$this->explnum_contribution_link = '';
		global $opac_contribution_area_activate, $allow_contribution;
		if (!$opac_contribution_area_activate || !$allow_contribution) {
		    return $this->explnum_contribution_link;
		}
		$shorturl_type_contribution = new shorturl_type_contribution();
		$this->explnum_contribution_link = $shorturl_type_contribution->get_shorturl('contribute', array(
				'sub' => 'docnum',
				'default_fields' => array(
						'http://www.pmbservices.fr/ontology#has_record' => array(
								array(
										'display_label' => $this->get_tit1(),
										'value' => $this->id,
										'type' => 'http://www.pmbservices.fr/ontology#record'
								)
						)
				)
		));
		return $this->explnum_contribution_link;
	}

	public function get_edit_link () {
	    if (contribution_area_forms_controller::can_edit("record", $this->id)) {
    	    return "./index.php?lvl=contribution_area&sub=convert&action=edit_entity&entity_type=record&entity_id=$this->id";
	    }
	    return "";
	}

	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	public function get_display_expl_responsive_list() {
	    global $msg, $charset;
	    global $expl_list_header, $expl_list_footer;
	    global $opac_expl_data, $opac_expl_order, $opac_url_base;
	    global $pmb_transferts_actif,$transferts_statut_transferts;
	    global $memo_p_perso_expl;
	    global $opac_show_empty_items_block ;
	    global $opac_show_exemplaires_analysis;
	    global $expl_list_header_loc_tpl,$opac_aff_expl_localises;

	    $nb_expl_visible = 0;
	    $nb_expl_autre_loc=0;
	    $nb_perso_aff=0;

	    if(!$this->is_numeric()){
	        $type = $this->get_niveau_biblio();
	        $id = $this->get_id();
	        $bull = $this->get_bul_info();
	        $bull_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : 0);

	        // les dépouillements ou périodiques n'ont pas d'exemplaire
	        if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
	        if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
	        $header_found_p_perso=0;
	        $expls_datas = $this->get_expls_datas();
	        $expl_list_header_deb="<tr class='thead'>";

	        if (!empty($expls_datas['colonnesarray'])) {
	            foreach ($expls_datas['colonnesarray'] as $colonne) {
	                if (strstr($colonne, "#")) {
	                    if (!$memo_p_perso_expl->no_special_fields) {
	                        $id=substr($colonne,1);
	                        $expl_list_header_deb .= "<th class='expl_header_".$memo_p_perso_expl->t_fields[$id]['NAME']."' role='columnheader' scope='col'>".htmlentities($memo_p_perso_expl->t_fields[$id]['TITRE'],ENT_QUOTES, $charset)."</th>";
	                    }
	                } else {
	                    $expl_list_header_deb .= "<th class='expl_header_".$colonne."' role='columnheader' scope='col'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
	                }
	            }
	        }
	        $expl_list_header_deb.="<th class='expl_header_statut' role='columnheader' scope='col'>".$msg['statut']."</th>";
	        $expl_liste="";
	        $expl_liste_all="";
	        $header_perso_aff="";
	        $expl_liste_loc="";

	        if(!empty($expls_datas['expls']) && is_countable($expls_datas['expls']) && count($expls_datas['expls'])) {
	            $customization_expl_columns = array();
	            $special = record_display::get_special($this->id);
	            if(!empty($special) && method_exists($special, "get_customization_expl_columns")) {
	                $customization_expl_columns = $special->get_customization_expl_columns();
	            }
	            foreach ($expls_datas['expls'] as $expl) {
	                $expl_liste .= "<tr class='item_expl !!class_statut!!'>";

	                foreach ($expls_datas['colonnesarray'] as $colonne) {
	                    if(isset($customization_expl_columns[$colonne])) {
	                        $expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset) . "'>";
	                        if(isset($customization_expl_columns[$colonne]['htmlentities']) && $customization_expl_columns[$colonne]['htmlentities'] == false) {
	                            $expl_liste .=strip_tags($expl[$colonne], $customization_expl_columns[$colonne]['keep_tags']);
	                        } else {
	                            $expl_liste .=htmlentities($expl[$colonne],ENT_QUOTES, $charset);
	                        }
	                        $expl_liste .="</td>";
	                    } elseif (strstr($colonne, "#")) {
	                        if (!$memo_p_perso_expl->no_special_fields) {
	                            $id=substr($colonne,1);
	                            $expl_liste .="<td class='".htmlentities($memo_p_perso_expl->t_fields[$id]['TITRE'],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($memo_p_perso_expl->t_fields[$id]['TITRE'],ENT_QUOTES, $charset) . "'>".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</td>";
	                        }
	                    } elseif (($colonne == "location_libelle") && $expl['num_infopage']) {
	                        if ($expl['surloc_id'] != "0") {
	                            $param_surloc="&surloc=".$expl['surloc_id'];
	                        } else {
	                            $param_surloc="";
	                        }
	                        $expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset) . "'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</a></td>";
	                    } elseif($colonne == "expl_cb") {
	                        $expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset) . "'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
	                    } elseif ($colonne == "section_libelle") {
	                    	$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset) . "'>".htmlentities((!empty($expl['section_libelle_opac']) ? $expl['section_libelle_opac'] : $expl[$colonne]),ENT_QUOTES, $charset)."</td>";
	                    } else {
	                        $expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset) . "'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
	                    }
	                }

	                if ($expl['flag_resa']) {
	                    $class_statut = "expl_reserve";
	                } else {
	                    if ($expl['pret_flag']) {
	                        if($expl['pret_retour']) { // exemplaire sorti
	                            $class_statut = "expl_out";
	                        } else { // pas sorti
	                            $class_statut = "expl_available";
	                        }
	                    } else { // pas prêtable
	                        // exemplaire pas prêtable, on affiche juste "exclu du pret"
	                        if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
	                            $class_statut = "expl_transfert";
	                        } else {
	                            $class_statut = "expl_unavailable";
	                        }
	                    }
	                } // fin if else $flag_resa
	                $expl_liste .= "<td class='".$msg['statut']."' data-column-name='" . $msg['statut'] . "'>".record_display::get_display_situation($expl)." </td>";
	                $expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);

	                //Champs personalisés
	                $perso_aff = "" ;
	                if (!$memo_p_perso_expl->no_special_fields) {
	                    $perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
	                    for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
	                        $p=$perso_["FIELDS"][$i];
	                        if ($p['OPAC_SHOW'] && !in_array('#'.$p['ID'], $expls_datas['colonnesarray'])) {
	                            if(!$header_found_p_perso) {
	                                $header_perso_aff.="<th class='expl_header_tdoc_libelle' role='columnheader' scope='col'>".$p["TITRE_CLEAN"]."</th>";
	                                $nb_perso_aff++;
	                            }
	                            if( $p["AFF"] !== '')	{
	                            	$perso_aff.="<td class='".htmlentities($p["TITRE_CLEAN"],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($p["TITRE_CLEAN"],ENT_QUOTES, $charset) . "'>".$p["AFF"]."</td>";
	                            }
	                            else $perso_aff.="<td class='".htmlentities($p["TITRE_CLEAN"],ENT_QUOTES, $charset)."' data-column-name='" . htmlentities($p["TITRE_CLEAN"],ENT_QUOTES, $charset) . "'></td>";
	                        }
	                    }
	                }
	                $header_found_p_perso=1;
	                $expl_liste.=$perso_aff;

	                $expl_liste .="</tr>";
	                $expl_liste_all.= $expl_liste;

	                if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
	                    if($expl['expl_location']==$_SESSION["empr_location"]) {
	                        $expl_liste_loc.=$expl_liste;
	                    } else {
	                        $nb_expl_autre_loc++;
	                    }
	                }
	                $expl_liste="";
	                $nb_expl_visible++;
	            } // fin foreach
	        }
	        //S'il y a des titres de champs perso dans les exemplaires
	        if($header_perso_aff) {
	            $expl_list_header_deb.=$header_perso_aff;
	        }
	        $expl_list_header_deb.="</tr>";
	        if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
	            // affichage avec onglet selon la localisation
	            if(!$expl_liste_loc) {
	                $expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
	            }
	            $expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
	            $expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
	            $expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
	            $expl_liste_all=str_replace("!!id!!",$id + intval($bull_id),$expl_liste_all);
	        } else {
	            // affichage de la liste d'exemplaires calculée ci-dessus
	            if (!$expl_liste_all && $opac_show_empty_items_block==1) {
	                $expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(!empty($expls_datas['colonnesarray']) && count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
	            } elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
	                $expl_liste_all = "";
	            } else {
	                $expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
	            }
	        }
	        $expl_liste_all=str_replace("<!--nb_expl_visible-->",($nb_expl_visible ? " (".$nb_expl_visible.")" : ""),$expl_liste_all);
	        return $expl_liste_all;
	    }
	    return '';
	} // fin function get_display_expl_responsive_list

	/**
	 * Liste les methodes, utile pour les web
	 * @return []
	 */
	public function get_methods_infos() {
	    return entities::get_methods_infos($this);
	}

	/**
	 * Liste les proprietes, utile pour les web
	 * @return []
	 */
	public function get_properties_infos() {
	    return entities::get_properties_infos($this);
	}

	public function get_ark_link() {
		if (empty($this->ark_link)) {
			global $pmb_ark_activate;
			if ($pmb_ark_activate) {

				if ($this->notice->niveau_biblio == 'b') {
					$this->get_bul_info();
					$arkEntity = new ArkBulletin(intval($this->parent['bulletin_id']));
				} else {
					$arkEntity = new ArkRecord(intval($this->id));
				}

				$ark = ArkModel::getArkFromEntity($arkEntity);
				$this->ark_link = $ark->getArkLink();
			}
		}
		return $this->ark_link;
	}

	/**
	 * Retourne les informations de base sur la nomenclature de la notice
	 * @return array
	 */
	public function get_nomenclature()
	{
		if(isset($this->nomenclature)) {
			return $this->nomenclature;
		}
		$nomenclature = new nomenclature_record_formations($this->id);
		$this->nomenclature = $nomenclature->get_data();
		return $this->nomenclature;
	}

	/**
	 * Retourne un objet détaillant la nomenclature de la notice
	 * @return array
	 */
	public function get_analyzed_nomenclature()
	{
		if(isset($this->analyzed_nomenclature)){
			return $this->analyzed_nomenclature;
		}
		$this->analyzed_nomenclature = array();

		$formations = $this->get_nomenclature();

		foreach($formations as $formation) {
			$nomenclature_nomenclature = new nomenclature_nomenclature(true);
			$nomenclature_nomenclature->set_abbreviation($formation["abbreviation"]);

			$this->analyzed_nomenclature[$formation["id"]] = array();
			$this->analyzed_nomenclature[$formation["id"]]["abbreviation"] = $formation["abbreviation"];

			if($formation["nature"] == 1){
				//Cas des formations de type voix
				$nomenclature_nomenclature->analyze_voices();
				$this->analyzed_nomenclature[$formation["id"]]["voices"] = $nomenclature_nomenclature->get_voices();
				continue;
			}
			//Cas general
			$nomenclature_nomenclature->analyze();
			$formated_families = array();
			$families = $nomenclature_nomenclature->get_families();

			foreach($families as $family) {
				//Familles d'instruments
				$id = $family->get_id();
				$formated_families[$id] = array();
				$formated_families[$id]["name"] = $family->get_name();
				//Recuperation des notes sur la famille si elles existent
				if(array_key_exists($id, $formation["families_notes"]) && $formation["families_notes"][$id] != '') {
					$formated_families[$id]["notes"] = $formation["families_notes"][$id];
				}
				foreach($family->get_musicstands() as $musicstand) {
					//Pupitres
					$effective = $musicstand->get_effective();
					//Si aucun effectif on peut passer au pupitre suivant
					if($effective == 0 && $effective !== $nomenclature_nomenclature->get_indefinite_character() && $effective !== "nd") {
						continue;
					}
					$id_musicstand = $musicstand->get_id();
					$formated_families[$id]["musicstands"][$id_musicstand] = array();
					$formated_families[$id]["musicstands"][$id_musicstand]["name"] = $musicstand->get_name();
					$formated_families[$id]["musicstands"][$id_musicstand]["effective"] = $effective;

					foreach($musicstand->get_instruments(true) as $instrument) {
						//Instruments
						$id_instrument = $instrument->get_id();
						//Si l'instrument est deja defini alors on incremente juste l'effectif
						if(is_array($formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument])) {
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["effective"]++;
						} else {
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument] = array();
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["name"] = $instrument->get_name();
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["code"] = $instrument->get_code();
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["effective"] = $instrument->get_effective();
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["standard"] = $instrument->get_standard();
						}
						foreach($instrument->get_others_instruments() as $other_instrument) {
							//Instruments exotiques
							$id_other_instrument = $other_instrument->get_id();
							//Si l'instrument est deja defini alors on incremente juste l'effectif
							if(is_array($formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["others_instruments"][$id_other_instrument])) {
								$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["others_instruments"][$id_other_instrument]["effective"]++;
								continue;
							}
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["others_instruments"][$id_other_instrument]["name"] = $other_instrument->get_name();
							$formated_families[$id]["musicstands"][$id_musicstand]["instruments"][$id_instrument]["others_instruments"][$id_other_instrument]["effective"] = $other_instrument->get_effective();
						}
					}
				}
				//On supprime les familles vides
				if(! isset($formated_families[$id]["musicstands"])) {
					unset($formated_families[$id]);
				}
			}
			$this->analyzed_nomenclature[$formation["id"]]["families"] = $formated_families;

			//Ateliers
			foreach($formation["workshops"] as $workshop) {
				$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]] = array();
				$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["name"] = $workshop["label"];
				$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["instruments"] = array();
				foreach($workshop["instruments"] as $instrument) {
					$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["instruments"][$instrument["id"]] = array();
					$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["instruments"][$instrument["id"]]["name"] = $instrument["name"];
					$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["instruments"][$instrument["id"]]["code"] = $instrument["code"];
					$this->analyzed_nomenclature[$formation["id"]]["workshops"][$workshop["id"]]["instruments"][$instrument["id"]]["effective"] = $instrument["effective"];
				}
			}

			//Instruments non standards
			foreach($formation["instruments"] as $instrument) {
				$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]] = array();
				$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]]["name"] = $instrument["name"];
				$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]]["code"] = $instrument["code"];
				$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]]["effective"] = $instrument["effective"];
				foreach($instrument["other"] as $other_instrument) {
					$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]]["others_instruments"][$other_instrument["id"]] = array();
					$this->analyzed_nomenclature[$formation["id"]]["exotic_instruments"][$instrument["id"]]["others_instruments"][$other_instrument["id"]]["name"] = $other_instrument["name"];
				}
			}
		}
		return $this->analyzed_nomenclature;
	}

	static public function get_liens_opac() {
		return UrlEntities::getOPACLink();
	}

	/**
	 * recherche l'identifiant du bulletin associe a la notice
	 * @return number
	 */
	public function get_bull_id() {
	    $bull_id = 0;
	    $query = "SELECT bulletin_id FROM bulletins WHERE num_notice = $this->id";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        $row = pmb_mysql_fetch_assoc($result);
	        $bull_id = $row["bulletin_id"];
	    }
	    return $bull_id;
	}

	/**
	 * test l"existence de la notice
	 * @return boolean
	 */
	public function is_existing_record() {
	    return !empty($this->notice);
	}

	/**
	 * Retourne le nom de la source utilisé pour récupérer la vignette
	 */
	public function get_thumbnail_source_label(): string{
	    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
	    return $thumbnailSourcesHandler->getSourceLabel(1, $this->id);
	}
}