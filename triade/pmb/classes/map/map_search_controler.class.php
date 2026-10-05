<?php

// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_search_controler.class.php,v 1.35.4.1 2025/04/24 14:45:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");
global $class_path;
require_once($class_path . "/map/map_hold.class.php");
require_once($class_path . "/map/map_model.class.php");
require_once($class_path . "/map/map_objects_controler.class.php");
require_once($class_path . "/search.class.php");
require_once($class_path . "/searcher.class.php");
require_once($class_path . "/analyse_query.class.php");
require_once "$class_path/encoding_normalize.class.php";

/**
 * class map_search_controler
 * Controlleur de notre super dev
 */
class map_search_controler {

    /**
     *
     * @access protected
     */
    protected $model;

    /**
     *
     * @access protected
     */
    protected $mode;

    public $editable = false;
    public $ajax = false;
    public $objects = [];

    /**
     * Constructeur.
     *
     * Il joue à  aller chercher les infos utiles pour le modèle (listes d'ids des
     * objets liés,...)
     *
     * @param map_hold map_hold Emprise courante de la carte
     * @param int mode Mode de récupération des éléments
     * @return void
     * @access public
     */
    public function __construct($map_hold, $mode, $max_hold, $force_ajax = false, $cluster = "true") {
        $this->editable = false;
        $this->set_mode($mode);
        $this->ajax = $force_ajax;
        $this->objects = array();
        $this->objects = $this->get_objects();

        if (count($this->objects)) {
            $this->model = new map_model($map_hold, $this->objects, $max_hold, $cluster);
            $this->model->set_mode("search");
        } else {
            //la recherche n'est pas encore enregistré...
            $this->ajax = true;
        }
    }

    /**
     * Modifie le mode
     *
     * @return void
     * @access public
     */
    public function set_mode($mode) {

        $this->mode = $mode;
    }

    /**
     * Retourne le mode
     *
     * @return string
     * @access public
     */
    public function get_mode() {

        return $this->mode;
    }

    /**
     *
     * @return array
     * @access public
     */
    public function get_objects() {
        global $search;

        $objects = array();

        $current_search = $this->get_mode();
        if (isset($_SESSION["session_history"][$current_search]) && isset($_SESSION["session_history"][$current_search]["NOTI"]) && $_SESSION["session_history"][$current_search]["NOTI"]["GET"]["mode"] != "") {
            $mode_search = $_SESSION["session_history"][$current_search]["NOTI"]["GET"]["mode"];
            switch ($mode_search) {
                case 1 :
                case 2 :
                case 9 :
                    if (isset($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"]) && count($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"])) {
                		foreach($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"] as $query) {
                			@pmb_mysql_query($query);
                		}
                	}
                    $requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
                    $result = pmb_mysql_query($requete);
                    $notices_ids = array();
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $notices_ids[] = $row->notice_id;
                    }
                    $objects[] = array(
                        'layer' => "record",
                        'ids' => $notices_ids
                    );
                    if (count($notices_ids)) {
                        $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $categ_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $categ_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority",
                                'type' => 2,
                                'ids' => $categ_ids
                            );
                        }
                        $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $concept_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $concept_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority_concept",
                                'type' => 10,
                                'ids' => $concept_ids
                            );
                        }
                    }
                    break;
                case 0 :
                case 11 :
                    if ($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"]) {
                        if (!empty($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"]) && count($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"])) {
                    		foreach($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"] as $query) {
                    			@pmb_mysql_query($query);
                    		}
                    	}
                        $requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
                        $notices_ids = array();
                        if($requete){
	                        $result = pmb_mysql_query($requete);
	                        while ($row = pmb_mysql_fetch_object($result)) {
	                            $notices_ids[] = $row->notice_id;
	                        }
                        }
                        $objects[] = array(
                            'layer' => "record",
                            'ids' => $notices_ids
                        );
                        if (count($notices_ids)) {
                            $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
                            $result = pmb_mysql_query($requete);
                            if (pmb_mysql_num_rows($result)) {
                                $categ_ids = array();
                                while ($row = pmb_mysql_fetch_object($result)) {
                                    $categ_ids[] = $row->map_emprise_obj_num;
                                }
                                $objects[] = array(
                                    'layer' => "authority",
                                    'type' => 2,
                                    'ids' => $categ_ids
                                );
                            }
                            $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
                            $result = pmb_mysql_query($requete);
                            if (pmb_mysql_num_rows($result)) {
                                $concept_ids = array();
                                while ($row = pmb_mysql_fetch_object($result)) {
                                    $concept_ids[] = $row->map_emprise_obj_num;
                                }
                                $objects[] = array(
                                    'layer' => "authority_concept",
                                    'type' => 10,
                                    'ids' => $concept_ids
                                );
                            }
                        }
                    }
                    break;
                case 3 :
                    $requete = "SELECT object_id FROM caddie_content where caddie_id='" . $_SESSION["session_history"][$current_search]["NOTI"]["GET"]["idcaddie"] . "' ";
                    $result = pmb_mysql_query($requete);
                    $notices_ids = array();
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $notices_ids[] = $row->object_id;
                    }
                    $objects[] = array(
                        'layer' => "record",
                        'ids' => $notices_ids
                    );
                    if (count($notices_ids)) {
                        $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $categ_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $categ_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority",
                                'type' => 2,
                                'ids' => $categ_ids
                            );
                        }
                        $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $concept_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $concept_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority_concept",
                                'type' => 10,
                                'ids' => $concept_ids
                            );
                        }
                    }
                    break;
                case 6 :
                    //Récupération et mise en variables globales des valeurs de l'historique
                    if ($_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"][0]) {
                        $search = $_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"];
                        //Pour chaque champ
                        for ($i = 0; $i < count($search); $i++) {

                            //Récupération de l'opérateur
                            $op = "op_" . $i . "_" . $search[$i];
                            global ${$op};
                            ${$op} = $_SESSION["session_history"][$current_search]["QUERY"]["POST"][$op];

                            //Récupération du contenu de la recherche
                            $field_ = "field_" . $i . "_" . $search[$i];
                            global ${$field_};
                            ${$field_} = $_SESSION["session_history"][$current_search]["QUERY"]["POST"][$field_];
                            $field = ${$field_};

                            //Récupération de l'opérateur inter-champ
                            $inter = "inter_" . $i . "_" . $search[$i];
                            global ${$inter};
                            if(isset($_SESSION["session_history"][$current_search]["QUERY"]["POST"][$inter])) {
                            	${$inter} = $_SESSION["session_history"][$current_search]["QUERY"]["POST"][$inter];
                            }
                            //Récupération des variables auxiliaires
                            $fieldvar_ = "fieldvar_" . $i . "_" . $search[$i];
                            global ${$fieldvar_};
                            if(isset($_SESSION["session_history"][$current_search]["QUERY"]["POST"][$fieldvar_])) {
                            	${$fieldvar_} = $_SESSION["session_history"][$current_search]["QUERY"]["POST"][$fieldvar_];
                            }
                            $fieldvar = ${$fieldvar_};
                        }
                    }

                    //on instancie la classe search avec le nom de la nouvelle table temporaire
                    if ($_SESSION["session_history"][$current_search]["QUERY"]["POST"]["search"][0]) {
                        $sc = new search(false);
                    } else {
                        $sc = new search(false, "search_simple_fields");
                    }

                    $table_tempo = $sc->make_search("tempo_" . $current_search);
                    $requete = "select * from " . $table_tempo;
                    $result = pmb_mysql_query($requete);
                    $notices_ids = array();
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $notices_ids[] = $row->notice_id;
                    }
                    $objects[] = array(
                        'layer' => "record",
                        'ids' => $notices_ids
                    );
                    if (count($notices_ids)) {
                        $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $categ_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $categ_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority",
                                'type' => 2,
                                'ids' => $categ_ids
                            );
                        }
                        $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
                        $result = pmb_mysql_query($requete);
                        if (pmb_mysql_num_rows($result)) {
                            $concept_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $concept_ids[] = $row->map_emprise_obj_num;
                            }
                            $objects[] = array(
                                'layer' => "authority_concept",
                                'type' => 10,
                                'ids' => $concept_ids
                            );
                        }
                    }
                    break;
                default:
                    // authpersos
                    if ($mode_search > 1000) {
                        if ($_SESSION["session_history"][$current_search]["NOTI"]["POST"]) {
                        	if (count($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"])) {
                        		foreach($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_LIST_QUERY"] as $query) {
                        			@pmb_mysql_query($query);
                        		}
                        	}
                            $requete = substr($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], 0, strpos($_SESSION["session_history"][$current_search]["NOTI"]["TEXT_QUERY"], "limit"));
                            $result = pmb_mysql_query($requete);
                            $notices_ids = array();
                            while ($row = pmb_mysql_fetch_object($result)) {
                                $notices_ids[] = $row->notice_id;
                            }
                            $objects[] = array(
                                'layer' => "record",
                                'ids' => $notices_ids
                            );
                            if (count($notices_ids)) {
                                $requete = "select distinct map_emprise_obj_num from map_emprises join notices_categories on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2 and notices_categories.notcateg_notice in (" . implode(",", $notices_ids) . ")";
                                $result = pmb_mysql_query($requete);
                                if (pmb_mysql_num_rows($result)) {
                                    $categ_ids = array();
                                    while ($row = pmb_mysql_fetch_object($result)) {
                                        $categ_ids[] = $row->map_emprise_obj_num;
                                    }
                                    $objects[] = array(
                                        'layer' => "authority",
                                        'type' => 2,
                                        'ids' => $categ_ids
                                    );
                                }
                                $requete = "select distinct map_emprise_obj_num from map_emprises
                                join index_concept on map_emprises.map_emprise_obj_num=index_concept.num_concept and index_concept.type_object = 1
                                where index_concept.type_object = 1 and index_concept.num_object in (" . implode(",", $notices_ids) . ")";
                                $result = pmb_mysql_query($requete);
                                if (pmb_mysql_num_rows($result)) {
                                    $concept_ids = array();
                                    while ($row = pmb_mysql_fetch_object($result)) {
                                        $concept_ids[] = $row->map_emprise_obj_num;
                                    }
                                    $objects[] = array(
                                        'layer' => "authority_concept",
                                        'type' => 10,
                                        'ids' => $concept_ids
                                    );
                                }
                            }
                        }
                    }
                    break;
            }
        } elseif (isset($_SESSION["session_history"][$current_search]) && $_SESSION["session_history"][$current_search]["AUT"]["GET"]["mode"] != "") {
            $mode_search = $_SESSION["session_history"][$current_search]["AUT"]["GET"]["mode"];
            switch ($mode_search) {
                case 0 :
                    if ($_SESSION["session_history"][$current_search]["AUT"]["POST"]) {
                        foreach ($_SESSION["session_history"][$current_search]["AUT"]["POST"] as $key => $valeur) {
                            global ${$key};
                            ${$key} = $valeur;
                        }
                        // Recherche sur l'auteur uniquement :
                        $aq = new analyse_query(stripslashes($author_query), 0, 0, 1, 1);
                        $restrict = '';
                        if ($typdoc_query)
                            $restrict = "and typdoc='" . $typdoc_query . "' ";
                        if ($statut_query)
                            $restrict.= "and statut='" . $statut_query . "' ";
                        if ($typdoc_query || $statut_query || $acces_j) {

                            $restrict = "and responsability_author=author_id and responsability_notice=notice_id " . $restrict . " ";
                            $members = $aq->get_query_members("authors", "concat(author_name,', ',author_rejete)", "index_author", "author_id");

                            $requete_count = "select count(distinct author_id) from authors, responsability, notices ";
                            $requete_count.= $acces_j;
                            $requete_count.= "where " . $members["where"] . " ";
                            $requete_count.= $restrict;

                            $requete = "select author_id," . $members["select"] . " as pert from authors, responsability, notices ";
                            $requete.= $acces_j;
                            $requete.= "where " . $members["where"] . " ";
                            $requete.= $restrict . " group by author_id order by pert desc,author_name, author_rejete,author_numero , author_subdivision ";
                        } else {
                            $requete_count = $aq->get_query_count("authors", "concat(author_name,', ',author_rejete)", "index_author", "author_id");
                            $t_query = $aq->get_query_members("authors", "concat(author_name,', ',author_rejete)", "index_author", "author_id");
                            $requete = "select author_id," . $t_query["select"] . " as pert from authors where " . $t_query["where"] . " group by author_id order by pert desc,author_name, author_rejete, author_numero , author_subdivision ";
                        }
                        $aut_ids = array();
                        $t_query = pmb_mysql_query($requete);
                        while ($row = pmb_mysql_fetch_object($t_query)) {
                            $aut_ids[] = $row->author_id;
                        }
                        $objects[] = array(
                            'layer' => "authority",
                            'ids' => $aut_ids
                        );
                    }
                    break;
            }
        }
        return $objects;
    }

    public function have_results() {
        if (!$this->model) {
            return false;
        } else {
            return $this->model->have_results();
        }
    }

    public function get_holds_json_informations($indice) {
        $json = array();
        if ($this->model) {
            $json = $this->model->get_holds_informations($this->objects[$indice]['layer']);
        }
        return encoding_normalize::json_encode($json);
    }

    public function get_json_informations() {
        global $pmb_url_base;
        global $pmb_map_base_layer_type;
        global $pmb_map_base_layer_params;
        global $pmb_map_bounding_box;

        $layer_params = json_decode($pmb_map_base_layer_params, true);
        $baselayer = "baseLayerType: dojox.geo.openlayers.BaseLayerType." . $pmb_map_base_layer_type;
        if (is_array($layer_params) && count($layer_params)) {
            if ($layer_params['name'])
                $baselayer.=",baseLayerName:\"" . $layer_params['name'] . "\"";
            if ($layer_params['url'])
                $baselayer.=",baseLayerUrl:\"" . $layer_params['url'] . "\"";
            if ($layer_params['options'])
                $baselayer.=",baseLayerOptions:" . json_encode($layer_params['options']);
        }

        if ($this->ajax) {
            return "mode:\"search_result\", searchId: " . $this->mode . "," . $baselayer . ",layers_url: \"" . $pmb_url_base . "ajax.php?module=ajax&categ=map&sub=search&action=get_layers\"";
        } else if ($this->model) {
            $json = array();
            $map_hold = $this->get_bounding_box();
            if ($map_hold) {
                $coords = $map_hold->get_coords();
                $json = array(
                    'initialFit' => explode(',', map_objects_controler::get_coord_initialFit($coords)),
                    'layers' => $this->model->get_json_informations(true, $pmb_url_base, false)
                );
            } else {
            	if( $pmb_map_bounding_box) {
            		$map_bounding_box = $pmb_map_bounding_box;
            	} else {
            		$map_bounding_box = '-5 50,9 50,9 40,-5 40,-5 50';
            	}
            	$map_hold = new map_hold_polygon("bounding", 0, "polygon((".$map_bounding_box."))");
            	if ($map_hold) {
            		$coords = $map_hold->get_coords();
            		$initialFit = explode(',', map_objects_controler::get_coord_initialFit($coords));
            	} else{
            		$initialFit = array(0, 0, 0, 0);
            	}
                $json = array(
                    'initialFit' => $initialFit,
                    'layers' => array(
                        array(
                            'type' => "record",
                            'name' => "record",
                            'holds' => array(),
                            'ajax' => false
                        )
                    )
                );
            }
            return json_encode($json);
        }
    }

    public function get_bounding_box() {
        return $this->model->get_bounding_box();
    }
}
