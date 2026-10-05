<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_datas_unimarc.class.php,v 1.14.6.1.2.1 2025/03/25 10:21:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $include_path, $class_path;

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
require_once($class_path."/authority.class.php");
require_once($class_path."/authorities_collection.class.php");

global $tdoc;
if (empty($tdoc)) $tdoc = new marc_list('doctype');
global $fonction_auteur;
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// definition de la classe d'affichage des notices
class record_datas_unimarc {
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
     * Libellé du statut de la notice
     * @var string
     */
    private $statut_notice = "";

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
    private $titres_uniformes = array();

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
     * identifiant de la source
     */
    private $source_id;

    /**
     * identifiant du connecteur
     */
    private $connector_id;

    /**
     * nom de la source
     */
    private $source_name;

    /**
     * entrepots des localisations
     */
    private $entrepots_localisations;

	public $details = array();

	public $year = '';
	public $perio_issn = '';
	public $auteurs_tous = '';

	public $parent_title = '';
	public $parent_numero = '';
	public $parent_aff_date_date = '';
	public $parent_date = '';

	public $exemplaires = [];
	public $docnums = [];

	// constructeur------------------------------------------------------------
	public function __construct($id, $entrepots_localisations = array()) {
	    $id = intval($id);
	  	if(!$id)
	  		return;
		else {
			$this->id = $id;
			$this->entrepots_localisations = $entrepots_localisations;
			$this->fetch_data();
		}
	}

	// récupération des valeurs en table---------------------------------------
	private function fetch_data() {
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);

		$requete="select * from entrepot_source_".$source_id." where recid='".addslashes($this->id)."' group by field_order,ufield,usubfield,subfield_order,value";
		$myQuery = pmb_mysql_query($requete);

		$notice= $this->get_notice_class();
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
				    $this->details[$l->ufield] = $l->value;
				} else {
				    $this->details[$l->ufield][$l->field_order][$l->usubfield][$l->subfield_order] = $l->value;
				}


// 				$this->unimarc[$l->ufield][$l->field_order][$l->usubfield][$l->subfield_order];
				switch ($l->ufield) {
					//dt
					case "dt":
						$notice->typdoc=$l->value;
						break;
					case "bl":
// 						if($l->value == 'a'){
// 							$notice->niveau_biblio=$l->value;
// 						} else $notice->niveau_biblio='m'; //On force le document au type monographie

							$notice->niveau_biblio=$l->value;

						break;
					case "hl":
						if($l->value == '2'){
							$notice->niveau_hierar=$l->value;
						} else $notice->niveau_hierar='0'; //On force le niveau à zéro
						break;
					//ISBN
					case "010":
						if ($l->usubfield=="a") $notice->code=$l->value;
						if ($l->usubfield=="d") $notice->prix=$l->value;
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
							case "h" :
							    $notice->perio_title = $l->value;
							    break;
							case "i" :
							    $notice->bull_num = $l->value;
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
								if(!$notice->coll)$notice->coll = new stdClass();
								$notice->coll->titre=$l->value;
								break;
							case "i":
								if(!$notice->subcoll)$notice->subcoll = new stdClass();
								$notice->subcoll->titre=$l->value;
								break;
							case "v":
								if(!$notice->coll)$notice->coll = new stdClass();
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
								if(!empty($notice->notice_pperso[$cpt_notice_pperso]['value'])){
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
		return pmb_mysql_num_rows($myQuery);
	} // fin fetch_data


	// recuperation des auteurs ---------------------------------------------------------------------
	// retourne $this->auteurs_principaux = ce qu'on va afficher en titre du resultat
	// retourne $this->auteurs_tous = ce qu'on va afficher dans l'isbd
	// NOTE: now we have two functions:
	// 		fetch_auteurs()  	the pmb-standard one

	public function fetch_auteurs() {
		global $fonction_auteur;

		$this->responsabilites  = array() ;
		$auteurs = array() ;
		$responsabilites = array();

		$res = array();
		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;

		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}

		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->id)."' and ufield like '7%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);

			$id_aut="";
			$n_aut=-1;
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
			$auteurs[$n_aut]['auteur_titre']=(!empty($auteurs[$n_aut]['rejete'])? $auteurs[$n_aut]['rejete'].' ' : '').$auteurs[$n_aut]['name'];
			if ($auteur['type']==2 && ($auteurs[$n_aut]['subdivision'] || $auteurs[$n_aut]['numero'] || $auteurs[$n_aut]['date'] || $auteurs[$n_aut]['lieu'])) {
				$c='';
				$c.=$auteurs[$n_aut]['subdivision'];
				$c.=($c && $auteurs[$n_aut]['numero'])?(', '.$auteurs[$n_aut]['numero']):($auteurs[$n_aut]['numero']);
				$c.=($c && $auteurs[$n_aut]['date'])?(', '.$auteurs[$n_aut]['date']):($auteurs[$n_aut]['date']);
				$c.=($c && $auteurs[$n_aut]['lieu'])?(', '.$auteurs[$n_aut]['lieu']):($auteurs[$n_aut]['lieu']);
				$auteurs[$n_aut]['auteur_titre'].=' ('.$c.')';
			}
			$auteurs[$n_aut]['auteur_isbd']=$auteurs[$n_aut]['auteur_titre'].(!empty($auteurs[$n_aut]['fonction_aff'])?' ,':'').(isset($auteurs[$n_aut]['fonction_aff']) ? $auteurs[$n_aut]['fonction_aff'] : '');
		}

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
	private function fetch_categories() {
		$this->categories = array();
		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}

		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->id)."' and ufield like '60%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);

			$id_categ="";
			$n_categ=-1;
			$categ_l=array();
			while ($l=pmb_mysql_fetch_object($res_sql)) {
				if ($l->field_order!=$id_categ) {
					if ($n_categ!=-1) {
						$categ_libelle = (!empty($categ_l["a"][0]) ? $categ_l["a"][0] : "").(!empty($categ_l["x"])?" - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"]) ?" - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"]) ?" - ".implode(" - ",$categ_l["z"]):"");
						$this->categories[] = $categ_libelle;
					}
					$categ_l=array();
					$n_categ++;
					$id_categ=$l->field_order;
				}
				$categ_l[$l->usubfield][]=$l->value;
			}
			if ($n_categ>=0) {
				$categ_libelle = (!empty($categ_l["a"][0]) ? $categ_l["a"][0] : "").(!empty($categ_l["x"])?" - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"]) ?" - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"]) ?" - ".implode(" - ",$categ_l["z"]):"");
				$this->categories[] =$categ_libelle;
			}
		}
	}

	private function fetch_langues() {
		global $marc_liste_langues ;
		if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

		if(empty($this->source_id)){
			$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->id);
			$myQuery = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($myQuery)) {
				$this->source_id = pmb_mysql_result($myQuery, 0, 0);
			}
		}

		$langues = array();
		$languesorg = array();
		if(!empty($this->source_id)){
			$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_".$this->source_id." where recid='".addslashes($this->id)."' and ufield like '101' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
			$res_sql=pmb_mysql_query($rqt);

			//$subfield=array("0"=>"a","1"=>"c");

			while ($l=pmb_mysql_fetch_object($res_sql)) {
				if ($l->usubfield == 'a') {
					if (isset($marc_liste_langues->table[$l->value])) {
						$langues[] = array(
								'code' => $l->value,
								'langue' => $marc_liste_langues->table[$l->value]
						) ;
					}
				}
				if ($l->usubfield == 'c') {
					if (isset($marc_liste_langues->table[$l->value])) {
						$languesorg[] = array(
								'code' => $l->value,
								'langue' => $marc_liste_langues->table[$l->value]
						) ;
					}
				}
			}
		}
		$this->langues['langues'] = $langues;
		$this->langues['languesorg'] = $languesorg;
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
	    return array();
	}

	/**
	 * Retourne le type de document
	 *
	 * @return string
	 */
	public function get_typdoc() {
	    if (!$this->notice->typdoc) $this->notice->typdoc='a';
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
	        if (!empty($this->notice->serie_name)) {
                $this->serie = array(
                    'name' => $this->notice->serie_name
                );
	        }
	    }
	    return $this->serie;
	}

	/**
	 * Retourne un tableau des auteurs
	 * @return array Tableaux des responsabilités = array(
	 'responsabilites' => array(),
	 'auteurs' => array()
	 );
	 */
	public function get_responsabilites() {
	    global $fonction_auteur;

	    if (!count($this->responsabilites)) {
	        $this->fetch_auteurs();
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
// 	        $as = array_search("0", $this->responsabilites["responsabilites"]);
// 	        if (($as !== FALSE) && ($as !== NULL)) {
// 	            $auteur_0 = $this->responsabilites["auteurs"][$as];
// 	            $this->auteurs_principaux = $auteur_0["auteur_titre"];
// 	        } else {
// 	            $as = array_keys($this->responsabilites["responsabilites"], "1" );
// 	            $aut1_libelle = array();
// 	            for ($i = 0; $i < count($as); $i++) {
// 	                $indice = $as[$i];
// 	                $auteur_1 = $this->responsabilites["auteurs"][$indice];
// 	                if($auteur_1["type"]==72 || $auteur_1["type"]==71) {
// 	                    $congres = authorities_collection::get_authority('author', $auteur_1["id"]);
// 	                    $aut1_libelle[] = $congres->display;
// 	                } else {
// 	                    $aut1_libelle[] = $auteur_1["auteur_titre"];
// 	                }
// 	            }
// 	            $auteurs_liste = implode(" ; ",$aut1_libelle);
// 	            if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste;
// 	        }
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
	                $aut2_libelle[] = $congres->display;
	            } else {
	                $aut2_libelle[] = $auteur_2["auteur_titre"];
	            }
	        }
	        $auteurs_liste = implode(" ; ",$aut2_libelle);
	        if ($auteurs_liste) $this->auteurs_secondaires = $auteurs_liste;
	    }
	    return $this->auteurs_secondaires;
	}

	/**
	 * Retourne les catégories de la notice
	 * @return categorie Tableau des catégories
	 */
	public function get_categories() {
	    if (!isset($this->categories)) {
	        $this->fetch_categories();
	    }
	    return $this->categories;
	}

	/**
	 * Retourne le titre uniforme
	 * @return tu_notice
	 */
	public function get_titres_uniformes() {
	    return $this->titres_uniformes;
	}

	/**
	 * Retourne le tableau des langues de la notices
	 * @return array $this->langues = array('langues' => array(), 'languesorg' => array())
	 */
	public function get_langues() {
	    if (!count($this->langues)) {
	        $this->fetch_langues();
	    }
	    return $this->langues;
	}

	/**
	 * Retourne le nombre de bulletins associés
	 * @return int
	 */
	public function get_nb_bulletins(){
	    return 0;
	}

	/**
	 * Retourne le tableau des bulletins associés à la notice
	 * @return array $this->bulletins[] = array('id', 'numero', 'mention_date', 'date_date', 'bulletin_titre', 'num_notice')
	 */
	public function get_bulletins(){
	    if (!count($this->bulletins) && $this->get_nb_bulletins()) {
	    }
	    return $this->bulletins;
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
	    if (!isset($this->notice->code)) {
	        $this->notice->code = "";
	    }
	    return $this->notice->code;
	}

	/**
	 * Retourne $this->notice->npages
	 */
	public function get_npages() {
	    if (!isset($this->notice->npages)) {
	        $this->notice->npages = 0;
	    }
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
	    return $this->publishers;
	}

	/**
	 * Retourne l'icone du type de document
	 * @return string
	 */
	public function get_icon_doc() {
	    if (!isset($this->icon_doc)) {
	        $icon_doc = marc_list_collection::get_instance('icondoc');
	        $this->icon_doc = "";
	        if (isset($icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc])) {
	        $this->icon_doc = $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc];
	    }
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
	        $this->tdoc = $tdoc->table[$this->get_typdoc()];
	    }
	    return $this->tdoc;
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
	        $this->indexint = $this->notice->indexint;
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
	 * @return string
	 */
	public function get_contenu() {
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

            // C3RB
            case (((! empty($_SESSION['id_empr_session'])) && ! empty($this->source)) && ($this->source['connector'] == 'c3rb')):
                $params = [
                    'source_id' => $this->source['source_id'],
                    'empr_id' => $_SESSION['id_empr_session'],
                    'link' => $this->lien,
                ];
                $this->lien = c3rb::get_resource_link($this->source['ref'], $params);
                break;

            default:
                break;
        }
        return $this->lien;
	}

	public function get_source_label() {
	    $label = '';
	    $query = "SELECT connectors_sources.name FROM external_count
			JOIN connectors_sources ON connectors_sources.source_id = external_count.source_id
			where external_count.rid = ".$this->id;
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)) {
	        $label .= pmb_mysql_result($result, 0, 'name');
	    }
	    return $label;
	}

	public function get_source() {
	    if (isset($this->source)) {
	        return $this->source;
	    }
	    $this->source =[];
	    $query = "SELECT recid FROM external_count WHERE rid = " . $this->id;
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        $recid = pmb_mysql_result($result, 0,0);
	        $data = explode(" ", $recid);
	        $this->source = array(
	            'recid' => $recid,
	            'connector' => $data[0],
	            'source_id' => $data[1],
	            'ref' => $data[2],
	            'label' => $this->get_source_label()
	        );
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
	    if (!$this->notice->nocoll && isset($this->notice->coll->num)) {
	        $this->notice->nocoll = $this->notice->coll->num;
	    }
	    return $this->notice->nocoll;
	}

	/**
	 * Retourne la collection
	 * @return collection
	 */
	public function get_collection() {
	    if (!$this->collection && $this->notice->coll) {
	        $this->collection = $this->notice->coll;
	    }
	    return $this->collection;
	}

	/**
	 * Retourne la sous-collection
	 * @return subcollection
	 */
	public function get_subcollection() {
	    if (!$this->subcollection && $this->notice->subcoll) {
	        $this->subcollection = $this->notice->subcoll;
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
	    return "";
	}

	/**
	 * Retourne les données d'exemplaires
	 * @return array
	 */
	public function get_expls_datas() {
	    if (!isset($this->expls_datas)) {
	        $this->expls_datas = array();

	        if (!$this->exemplaires)
	            return;

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
                if (!isset($alocation["priority"])) {
                    $alocation["priority"] = 1;
                }
                $alocation["content"] = $expl;
                $final_location[] = $alocation;
            }

            if (!count($final_location)) {
                return;
            }

            //trions
            usort($final_location, array("record_datas_unimarc", "sort_expl"));

            $this->expls_datas = $final_location;
	    }
	    return $this->expls_datas;
	}

	/**
	 * Retourne l'URL calculée de l'image
	 * @return string
	 */
	public function get_picture_url() {
	    if (!$this->picture_url && $this->get_code()) {
	        if ($this->get_parameter_value('show_book_pics')=='1' && $this->get_parameter_value('book_pics_url')) {
	            $this->picture_url=getimage_url($this->get_code(), "");
	        }
	    }
	    if (!$this->picture_url) {
	        $this->picture_url = notice::get_picture_url_no_image($this->get_niveau_biblio(), $this->get_typdoc());
	    }
	    return $this->picture_url;
	}

	/**
	 * Retourne le texte au survol de l'image
	 * @return string
	 */
	public function get_picture_title() {
	    if (!$this->picture_title && $this->get_code()) {
	        global $charset;
	        if ($this->get_parameter_value('show_book_pics')=='1' && $this->get_parameter_value('book_pics_url')) {
                $this->picture_title = htmlentities($this->get_parameter_value('book_pics_msg'), ENT_QUOTES, $charset);
	        }
	    }
	    return $this->picture_title;
	}

	/**
	 * Retourne le tableau des relations parentes
	 * @return array
	 */
	public function get_relations_up() {
	    if (!isset($this->relations_up)) {
	        $this->relations_up = array();
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
	    }
	    return $this->relations_both;
	}

	/**
	 * Retourne les dépouillements
	 * @return string Tableau des affichage des articles
	 */
	public function get_articles() {
	    if (!isset($this->articles)) {
	        $this->articles = array();
	    }
	    return $this->articles;
	}

	/**
	 * Retourne les informations de notice externe
	 */
	public function get_external_rec_id() {
	    if(!isset($this->external_rec_id)) {
	        $this->external_rec_id = array();
	        $query = "SELECT recid FROM external_count WHERE rid = " . $this->id;
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
	    return aff_notice_unimarc($this->id, 0, array(), AFF_ETA_NOTICES_REDUIT);
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
	    return "";
	}

	public function get_source_id() {
	    return $this->source_id;
	}

	public function get_connector_id() {
	    return $this->connector_id;
	}

	private function get_notice_class() {
	    $notice = new stdClass();
	    //$notice->id            = 0;   // id de la notice
	    $notice->typdoc        = '';  // type du document
	    $notice->typdocdisplay = '';  // type du document
	    $notice->tit1          = '';  // titre propre
	    $notice->tit2          = '';  // titre propre 2
	    $notice->tit3          = '';  // titre parallèle
	    $notice->tit4          = '';  // complément du titre
	    $notice->tparent_id    = 0;   // id du titre parent
	    $notice->tparent       = '';  // libellé du titre parent
	    $notice->tnvol         = '';  // numéro de partie
	    $notice->responsabilites =    array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	    $notice->ed1_id        = 0;   // id éditeur 1
	    $notice->ed1           = '';  // libellé éditeur 1
	    $notice->coll_id       = 0;   // id collection
	    $notice->coll          = '';  // libellé collection
	    $notice->subcoll_id    = 0;   // id sous collection
	    $notice->subcoll       = '';  // libellé sous collection
	    $notice->ed2_id        = 0;   // id éditeur 2
	    $notice->ed2           = '';  // libellé éditeur 2
	    $notice->code          = '';  // ISBN, code barre commercial ou no. commercial
	    $notice->npages        = '';  // importance matérielle (nombre de pages, d'éléments...)
	    $notice->ill           = '';  // mention d'illustration
	    $notice->size          = '';  // format
	    $notice->prix = '';            // prix du document
	    $notice->year          = '';  // année de publication
	    $notice->nocoll        = '';  // no. dans la collection
	    $notice->accomp        = '';  // matériel d'accompagnement
	    $notice->n_gen         = array();  // note générale
	    $notice->n_contenu     = array();  // note de contenu
	    $notice->n_resume      = array();  // resumé/extrait
	    $notice->categories =array(); // les categories
	    $notice->indexint =  array();        // indexation interne
	    $notice->index_l       = '';  // indexation libre
	    $notice->lien          = '';  // URL de la ressource électronique associée
	    $notice->eformat       = '';  // format de la ressource électronique associée
	    $notice->index_sew    = '';  // pseudo index titre strippé
	    $notice->index_wew    = '';  // pseudo index titre
	    $notice->index_serie   = '';  // pseudo index serie
	    $notice->statut         = ''; //statut de la notice
	    $notice->niveau_biblio = 'm'; //niveau biblio utilisé pour les périodiques : 'm' monographie 'a' article
	    $notice->niveau_hierar = '0'; //niveau hiérarchique utilisé pour les périodiques

	    $notice->validfields   = 0;   // champs valides
	    $notice->create_date   = "0000-00-00 00:00:00"; // date création
	    $notice->date_parution = "0000-00-00 00:00:00"; // date parution
	    $notice->thumbnail_url = '';
	    $notice->bull_num = '';
	    $notice->perio_title = '';

	    return $notice;
	}

	/**
	 * Retourne les paramètres persos
	 * @return array
	 */
	public function get_p_perso() {
	    if (!$this->p_perso && $this->notice->notice_pperso) {
	        $this->p_perso = $this->notice->notice_pperso;
	    }
	    return $this->p_perso;
	}

	/**
	 * Retourne le titre de periodique
	 * @return string
	 */
	public function get_perio_title() {
	    if (!empty($this->notice->perio_title)) {
	        return $this->notice->perio_title;
	    }
	    if(!empty($this->parent_title)) {
	        return $this->parent_title;
	    }
	    return '';
	}

	/**
	 * Retourne le numero de bulletin
	 * @return string
	 */
	public function get_bull_num() {
	    if (!empty($this->notice->bull_num)) {
	        return $this->notice->bull_num;
	    }
	    if (!empty($this->parent_numero)) {
	        return $this->parent_numero;
	    }
	    return '';
	}

	private static function sort_expl($a, $b) {
	    $c1 = isset($a["priority"]) ? $a["priority"] : "";
	    $c2 = isset($b["priority"]) ? $b["priority"] : "";
	    if ($c1 == $c2) {
	        $c1 = isset($a["content"]["v"]) ? $a["content"]["v"] : "";
	        $c2 = isset($b["content"]["v"]) ? $b["content"]["v"] : "";
	        return strcmp($c1, $c2);
	    }
	    return $c2-$c1;
	}

	/**
	 * Retourne $this->notice->thumbnail_url
	 */
	public function get_thumbnail_url() {
	    if (isset($this->notice->thumbnail_url)) {
    	    return $this->notice->thumbnail_url;
	    }
	    return "";
	}
}
