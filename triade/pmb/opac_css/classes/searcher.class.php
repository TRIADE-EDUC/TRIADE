<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher.class.php,v 1.133.2.7 2025/01/03 15:40:44 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/analyse_query.class.php");
require_once ($class_path . "/search.class.php");
require_once ($class_path . "/filter_results.class.php");
require_once ($class_path . "/sort.class.php");
// il vaut mieux inclure les recherches perso dans index_includes.inc.php, quand les parametres des vues sont chargés
// if($opac_search_other_function){
// require_once($include_path."/".$opac_search_other_function);
// }
require_once ($class_path . "/map/map_search_controler.class.php");

// classe devant piloter les recherches (quelle idée...)
class searcher
{

    protected $searched;

    // booléen pour éviter de tourner en rond...
    public $user_query;

    // recherche saisie
    protected $aq;

    // analyse query
    public $notices_ids;

    // liste de notices sous forme de chaine
    public $typdocs;

    // tableau des typdoc
    protected $pert;

    // tableau de pertinance...
    protected $tri = "default";

    // tri à utiliser
    protected $field_restrict;

    // ids des champs à utiliser (restriction)
    protected $keep_empty = 0;

    // flag pour les mots vides
    public $nb_explnum = 0;

    // nombre de documents numériques associés à la recherche...
    public $explnums = array();

    // tableau contenant les documents numériques associés à la recherche
    public $table_tempo;

    // table temporaire contenant les résultats filtrés triés..;
    public $map_emprises_query;

    // recherche map emprises
    public $result;

    protected $var_table = array();

    public function __construct($user_query, $map_emprises = array())
    {
        $this->searched = false;
        $this->user_query = $user_query;
        $this->map_emprises_query = $map_emprises;
        $this->_analyse();
        $this->_delete_old_objects();
    }

    protected function _analyse()
    {
        global $opac_stemming_active;
        if ($this->user_query) {
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, $opac_stemming_active);
        }
    }

    protected function _calc_query_env()
    {
        // appeler avant ma génération de la requete de recherche...
    }

    protected function _get_search_query()
    {
        $this->_calc_query_env();
        if (is_object($this->aq) && $this->user_query !== "*") {
            $query = $this->aq->get_query_mot("id_notice", "notices_mots_global_index", "word", "notices_fields_global_index", "value", $this->field_restrict);
            if ($this->_get_typdoc_filter() != "") {
                $query = "select id_notice from ($query) as q1" . $this->_get_typdoc_filter();
            }
        } else {
            $query = " select notice_id as id_notice from notices";
            if ($this->_get_typdoc_filter(true) != "") {
                $query .= $this->_get_typdoc_filter(true);
            }
        }

        $this->_get_filter_by_custom_search($query);
        return $query;
    }

    /**
     *
     * @param boolean $query
     * @param boolean $with_explnum
     * @return string
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if ($query) {
            if(is_object($this->aq)) {
            	return $this->aq->get_pert($this->notices_ids, $this->field_restrict, false, $with_explnum, $query);
            }
        } else {
            if(is_object($this->aq)) {
                $this->table_tempo = $this->aq->get_pert($this->notices_ids, $this->field_restrict, false, $with_explnum, $query);
            }
        }
    }

    protected function _get_notices_ids()
    {
        if (! $this->searched) {
            $this->notices_ids = "";
            $query = $this->_get_search_query();
            if ($query) {
                $res = pmb_mysql_query($query);
                $this->pert = [];
                if ($res) {
                    if (pmb_mysql_num_rows($res)) {
                        while ($row = pmb_mysql_fetch_object($res)) {
                            if ($this->notices_ids != "")
                                $this->notices_ids .= ",";
                            $this->notices_ids .= $row->id_notice;
                            if (isset($row->pert)) {
                                $this->pert[$row->id_notice] = $row->pert;
                            }
                        }
                    }
                    pmb_mysql_free_result($res);
                }
            }
            $this->searched = true;
        }
        return $this->notices_ids;
    }

    public function set_notices_ids($notices_ids)
    {
        if (is_array($notices_ids)) {
            $this->notices_ids = implode(',', $notices_ids);
        } else {
            $this->notices_ids = $notices_ids;
        }
        $this->searched = true;
        return $this;
    }

    protected function _delete_old_objects()
    {
        $delete = "delete from search_cache where delete_on_date < NOW()";
        pmb_mysql_query($delete);
    }

    protected function _get_user_query()
    {
        return $this->user_query;
    }

    protected function _get_sign($sorted = false)
    {
        global $opac_search_other_function;
        global $typdoc;
        global $page;
        global $lang;
        global $dont_check_opac_indexation_docnum_allfields;
        global $multi_crit_indexation_docnum_allfields;
        global $nb_per_page_custom;

        $str_to_hash = session_id();
        $str_to_hash .= "&opac_view=" . (isset($_SESSION['opac_view']) ? $_SESSION['opac_view'] : '');
        $str_to_hash .= $_SESSION['user_code'];
        $str_to_hash .= "&lang=" . $lang;
        $str_to_hash .= "&type_search=" . $this->_get_search_type();
        $str_to_hash .= "&user_query=" . $this->_get_user_query();
        $str_to_hash .= "&map_emprises_query=" . serialize($this->map_emprises_query);
        $str_to_hash .= "&typdoc=" . $typdoc;
        $str_to_hash .= "&dont_check_opac_indexation_docnum_allfields=" . $dont_check_opac_indexation_docnum_allfields;
        //$str_to_hash .= "&mutli_crit_indexation_docnum_allfields=" . $multi_crit_indexation_docnum_allfields;

        if ($opac_search_other_function) {
            $str_to_hash .= "&perso=" . serialize(search_other_function_get_values());
        }
        if ($sorted) {
            $str_to_hash .= "&tri=" . $this->tri;
            $str_to_hash .= "&page=$page";
        }
        if ($nb_per_page_custom) {
            $str_to_hash .= "&nb_per_page_custom=" . $nb_per_page_custom;
        }
        return md5($str_to_hash);
    }

    protected function _get_in_cache($sorted = false)
    {
        $read = "select value from search_cache where object_id='" . $this->_get_sign($sorted) . "'";
        $res = pmb_mysql_query($read);
        if (pmb_mysql_num_rows($res) > 0) {
            $row = pmb_mysql_fetch_object($res);
            $value = unserialize($row->value);
            $cache = $value["objects_ids"] ?? false;
            $this->pert = $value["pert"] ?? [];
            return $cache;
        } else {
            return false;
        }
    }

    protected function _set_in_cache($sorted = false)
    {
        global $opac_search_cache_duration;
        if ($sorted == false) {
            $str_to_cache = serialize([
                "objects_ids" => $this->notices_ids,
                "pert" => $this->pert ?? []
            ]);
        } else {
            $str_to_cache = serialize([
                "objects_ids" => $this->result,
                "pert" => $this->pert ?? []
            ]);
        }
        if (! pmb_mysql_num_rows(pmb_mysql_query('select 1 from search_cache where object_id = "' . addslashes($this->_get_sign($sorted)) . '" limit 1'))) {
            $insert = "insert into search_cache set object_id ='" . addslashes($this->_get_sign($sorted)) . "', value ='" . addslashes($str_to_cache) . "', delete_on_date = now() + interval " . $opac_search_cache_duration . " second";
            pmb_mysql_query($insert);
        }
    }

    public function get_nb_results()
    {
        if (! $this->notices_ids) {
            $this->get_result();
        }
        if ($this->notices_ids == "") {
            return 0;
        } else {
            return substr_count($this->notices_ids, ",") + 1;
        }
    }

    protected function _sort($start, $number)
    {
        if ($this->table_tempo != "") {
            $sort = new sort("notices", "session");

            $query = $sort->appliquer_tri_from_tmp_table($this->tri, $this->table_tempo, "notice_id", $start, $number);
            $res = pmb_mysql_query($query);
            if ($res && pmb_mysql_num_rows($res)) {
                $this->result = array();
                while ($row = pmb_mysql_fetch_object($res)) {
                    $this->result[] = $row->notice_id;
                }
            }
        }
    }

    public function get_result()
    {
        global $opac_search_noise_limit_type;

        $cache_result = $this->_get_in_cache();
        if ($cache_result === false) {
            $this->_get_notices_ids();
            $this->_filter_results();
            // Ecretage
            if ($opac_search_noise_limit_type && $this->user_query !== "*") {
                $limit = 0;
                // calcul pertinance
                $this->_get_pert();
                // calcul du seuil.

                switch (substr($opac_search_noise_limit_type, 0, 1)) {
                    // moyenne - ecart_type
                    case 1:
                        $query = "select (avg(pert)-stddev_pop(pert)) as seuil from " . $this->table_tempo;
                        break;
                    // moyenne - % ecart_type
                    case 2:
                        $ratio = substr($opac_search_noise_limit_type, 2);
                        $query = "select (avg(pert)-(stddev_pop(pert))*" . $ratio . ") as seuil from " . $this->table_tempo;
                        break;
                    // %max
                    case 3:
                        $ratio = substr($opac_search_noise_limit_type, 2);
                        $query = "select (max(pert)*" . $ratio . ") as seuil from " . $this->table_tempo;
                        break;
                }
                $result = pmb_mysql_query($query) or die(pmb_mysql_error());
                if (pmb_mysql_num_rows($result)) {
                    $limit = pmb_mysql_result($result, 0, 0);
                }
                if ($limit) {
                    $query = "delete from " . $this->table_tempo . " where pert < " . $limit;
                    pmb_mysql_query($query);
                    $query = "select distinct notice_id from " . $this->table_tempo;
                    $result = pmb_mysql_query($query) or die(pmb_mysql_error());

                    if (pmb_mysql_num_rows($result)) {
                        $this->notices_ids = "";
                        while ($row = pmb_mysql_fetch_object($result)) {
                            if ($this->notices_ids) {
                                $this->notices_ids .= ",";
                            }
                            $this->notices_ids .= $row->notice_id;
                        }
                    }
                }
            }
            if ($this->map_emprises_query) {
                $queries = array();
                $restriction_emprise = " and (
                    (notices.notice_id IN (select distinct map_emprise_obj_num FROM map_emprises where map_emprise_type=11))
                    or (notices.notice_id IN (select distinct notcateg_notice from notices_categories join map_emprises on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2))
                    or (notices.notice_id IN (select distinct num_object from index_concept join map_emprises on map_emprise_type = 10 where type_object = 1 and map_emprise_obj_num = num_concept))
                )";
                foreach ($this->map_emprises_query as $map_emprise_query) {
                    // récupération des emprise de notices correspondantes
                    $query_notice = "select map_emprise_obj_num as notice_id from map_emprises where map_emprise_type=11 and contains(geomfromtext('$map_emprise_query'),map_emprise_data) = 1  ";
                    // récupérations des notices indexés avec une categorie
                    $query_categories = "select notcateg_notice as notice_id from notices_categories join map_emprises on num_noeud = map_emprises.map_emprise_obj_num where map_emprise_type = 2 and contains(geomfromtext('$map_emprise_query'),map_emprise_data) = 1";
                    // dans les concepts
                    $query_concepts = "select num_object as notice_id from index_concept join map_emprises on map_emprise_type = 10 and contains(geomfromtext('$map_emprise_query'), map_emprise_data) = 1 where type_object = 1 and map_emprise_obj_num = num_concept";
                    $queries[] = "select * from (" . $query_notice . " union " . $query_categories . " union " . $query_concepts . ") as uni"; // TODO-> faire le mapage et mettre le tout dans $queries...
                }
                $from = "";
                $select_pert = "";
                for ($i = 0; $i < count($queries); $i ++) {
                    if ($i == 0) {
                        $from = "(" . $queries[$i] . ") as t" . $i;
                    } else {
                        $from .= " inner join (" . $queries[$i] . ") as t" . $i . " on t" . $i . ".notice_id = t" . ($i - 1) . ".notice_id";
                    }
                }
                $restriction_query = '';
                if ($this->user_query != "" && $this->user_query != "*") {
                    $restriction_query = "and notices.notice_id in (" . $this->notices_ids . ")";
                }
                $text_query = "select t0.notice_id from " . $from . " join notices on t0.notice_id = notices.notice_id " . $restriction_emprise . " $restriction_query group by t0.notice_id  order by notices.index_sew ";
                $result = pmb_mysql_query($text_query);
                $nbresults = pmb_mysql_num_rows($result);
                $this->notices_ids = "";
                if ($result) {
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_object($result)) {
                            if ($this->notices_ids != "") {
                                $this->notices_ids .= ",";
                            }
                            $this->notices_ids .= $row->notice_id;
                        }
                    }
                    pmb_mysql_free_result($result);
                    $this->_filter_results();
                }
            }

            $this->_set_in_cache();
        } else {
            $this->notices_ids = $cache_result;
        }

        return $this->notices_ids;
    }

    public function get_sorted_result($tri = "default", $start = 0, $number = 20)
    {
        $this->tri = $tri;
        $cache_result = $this->_get_in_cache(true);
        if ($cache_result === false) {
            $cache_result = $this->_get_in_cache();
            if ($cache_result !== false) {
                $this->notices_ids = $cache_result;
            } else {
                $this->_get_notices_ids();
                $this->_filter_results();
                $this->_set_in_cache();
            }
            $this->_sort_result($start, $number);
            $this->_set_in_cache(true);
        } else {
            $this->result = $cache_result;
        }
        return $this->result;
    }

    public function get_sorted_cart_result($tri = "default", $start = 0, $number = 20)
    {
        $this->tri = $tri;
        $cache_result = $this->_get_in_cache(true);
        $this->_get_notices_ids();
        $this->_filter_results();
        $this->_sort_result($start, $number);

        return $this->result;
    }

    public function get_typdocs()
    {
        if (! $this->typdocs) {
            if (! $this->notices_ids) {
                $this->get_result();
            }
            $this->typdocs = array();
            if ($this->notices_ids != "") {
                $this->typdocs = self::get_typdocs_from_notices_ids($this->notices_ids);
            }
        }
        return $this->typdocs;
    }

    public static function get_typdocs_from_notices_ids($notices_ids = '')
    {
        $typdocs = array();
        if ($notices_ids != "") {
            $query = "select distinct typdoc from notices " . gen_where_in('notice_id', $notices_ids);
            $res = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($res)) {
                while ($row = pmb_mysql_fetch_object($res)) {
                    $typdocs[] = $row->typdoc;
                }
            }
        }
        return $typdocs;
    }

    public function get_nb_explnums($limit_one = 1)
    {
        if (! $this->notices_ids) {
            $this->get_result();
        }
        $this->nb_explnum = 0;
        if ($this->notices_ids != "") {
            $this->nb_explnum = self::get_nb_explnums_from_notices_ids($this->notices_ids, $limit_one);
        }
        return $this->nb_explnum;
    }

    /**
     * Retourne les jointures des droits d'acces sur la visibilite des documents numeriques
     *
     * @return array{acces_explnum_join: string, acces_notice_join: string}
     */
    protected static function get_explnum_access_join() {
    	global $opac_show_links_invisible_docnums;

    	if ($opac_show_links_invisible_docnums) {
    		return [
    			'acces_explnum_join' => '',
    			'acces_notice_join' => ''
    		];
    	}

    	$dom_notice_join = null;
    	$dom_explnum_join = null;

    	global $gestion_acces_active, $gestion_acces_empr_docnum, $gestion_acces_empr_notice;
    	if (1 == $gestion_acces_active && ($gestion_acces_empr_docnum || $gestion_acces_empr_notice)) {
    		$acces = new acces();
    		if (1 == $gestion_acces_empr_notice) {
    			/**
    			 * @var domain $dom_2
    			 */
    			$dom_2 = $acces->setDomain(2);

    			// On test de rights 16 qui correspond a la visibilite des documents numeriques sur la notice
    			$dom_notice_join = $dom_2->getJoin($_SESSION['id_empr_session'], 16, 'notice_id');
    		}

    		if (1 == $gestion_acces_empr_docnum) {
    			/**
    			 * @var domain $dom_3
    			 */
    			$dom_3 = $acces->setDomain(3);

    			// On test de rights 16 qui correspond a la visibilite des documents numeriques dans la visionneuse
    			$dom_explnum_join = $dom_3->getJoin($_SESSION['id_empr_session'], 16, 'explnum_id');
    		}
    	}

    	$acces_notice_join = [];
    	$acces_explnum_join = [];

    	// LEFT JOIN, car ici on veux recuperer les bulletins et les notices lies a exemplaire numeriques
    	$acces_notice_join[] = 'LEFT JOIN bulletins ON bulletins.bulletin_id = explnum.explnum_bulletin AND explnum_bulletin != 0';
    	$acces_notice_join[] = 'JOIN notices ON (notices.notice_id = explnum.explnum_notice AND explnum_notice != 0) OR (notices.notice_id = bulletins.num_notice AND bulletins.num_notice != 0)';

    	// Si les droits d'acces ne sont pas actives, on verifie les droits sur les statuts de notices
    	if (empty($dom_notice_join)) {
    		$acces_notice_clause = 'notice_statut.explnum_visible_opac=1 AND notice_statut.explnum_visible_opac_abon=0';
    		if ($_SESSION["user_code"]) {
    			$acces_notice_clause = $acces_notice_clause . ' OR (notice_statut.explnum_visible_opac_abon=1 AND notice_statut.explnum_visible_opac=1)';
    		}

    		$notice_statut_join = 'JOIN notice_statut ON notice_statut.id_notice_statut = notices.statut';
    		$notice_statut_join .= ' AND (' . $acces_notice_clause . ')';

    		$acces_notice_join[] = $notice_statut_join;
    	} else {
    		$acces_notice_join[] = $dom_notice_join;
    	}

    	// Si les droits d'acces ne sont pas actives, on verifie les droits sur les statuts d'exemplaires
    	if (empty($dom_explnum_join)) {
    		$acces_explnum_clause = 'explnum_statut.explnum_visible_opac=1 AND explnum_statut.explnum_visible_opac_abon=0';
    		if ($_SESSION["user_code"]) {
    			$acces_explnum_clause = $acces_explnum_clause . ' OR (explnum_statut.explnum_visible_opac_abon=1 AND explnum_statut.explnum_visible_opac=1)';
    		}

    		$explnum_statut_join = 'JOIN explnum_statut ON explnum_statut.id_explnum_statut = explnum.explnum_docnum_statut';
    		$explnum_statut_join .= ' AND (' . $acces_explnum_clause . ')';

    		$acces_explnum_join[] = $explnum_statut_join;
    	} else {
    		$acces_explnum_join[] = $dom_explnum_join;
    	}

    	return [
    		'acces_explnum_join' => implode(' ', $acces_explnum_join),
    		'acces_notice_join' => implode(' ', $acces_notice_join)
    	];
    }

    /**
     * Retourne le nombre d'exemplaires numeriques liees aux notices
     *
     * @param string $notices_ids IDs des notices sperares par une virgule
     * @param integer $limit_one 0 ou 1
     * @return integer
     */
    public static function get_nb_explnums_from_notices_ids($notices_ids = '', $limit_one = 1) {
    	if (empty($notices_ids)) {
    		return 0;
    	}

    	global $opac_photo_filtre_mimetype;
    	if ($opac_photo_filtre_mimetype) {
    		// $filter_explnum_mimetype = gen_where_in_string('explnum_mimetype', $opac_photo_filtre_mimetype);
    		$filter_explnum_mimetype = gen_where_in('explnum_mimetype', explode(',', $opac_photo_filtre_mimetype));
    	} else {
    		$filter_explnum_mimetype = '';
    	}

    	// #165782 - Du a un probleme de lenteur, on ne verifie pas les droits d'acces sur les documents numeriques
    	// Cela reprend le mecanisme sans sphinx.
    	// $acces_join = static::get_explnum_access_join();
    	$acces_join = [
    		'acces_notice_join' => '',
    		'acces_explnum_join' => '',
    	];

    	$query = 'SELECT COUNT(DISTINCT explnum.explnum_id) FROM explnum';
    	$query .= ' ' . $acces_join['acces_notice_join'];
    	$query .= ' ' . $acces_join['acces_explnum_join'];
    	$query .= ' ' . gen_where_in('explnum.explnum_notice', $notices_ids);
    	$query .= ' ' . $filter_explnum_mimetype;
    	if ($limit_one) {
    		$query .= ' LIMIT 1';
    	}

    	$nb_explnums = 0;
    	$result = pmb_mysql_query($query);
    	if (pmb_mysql_num_rows($result)) {
    		if ($limit_one) {
    			$nb_explnums = 1;
    		} else {
    			$nb_explnums = pmb_mysql_result($result, 0, 0);
    			$nb_explnums = intval($nb_explnums);
    		}

    		pmb_mysql_free_result($result);
    	}

    	return $nb_explnums;
    }

    protected function _sort_result($start, $number)
    {
        $this->_get_pert();
        $this->_sort($start, $number);
    }

    protected function _filter_results()
    {
        $this->_get_notices_ids();

        if ($this->notices_ids != '') {
            $fr = new filter_results($this->notices_ids);
            $this->notices_ids = $fr->get_results();
        }
    }

    protected function _get_filter_by_custom_search(&$query)
    {
        global $opac_search_other_function;
        $custom_query = '';
        if ($opac_search_other_function) {
            $custom_query = search_other_function_clause();
            if ($custom_query) {
                $query = 'select id_notice from (' . $query . ') as q2 where id_notice in (' . $custom_query . ')';
            }
        }
        return;
    }

    protected function _get_typdoc_filter($on_notice = false)
    {
        global $typdoc;
        $return = "";
        if ($on_notice) {
            if ($typdoc) {
                $return = " where typdoc = '" . $typdoc . "'";
            } else {
                $return = " where 1 ";
            }
        } else {
            if ($typdoc) {
                $return = " join notices on id_notice = notice_id and typdoc = '" . $typdoc . "'";
            } else {
                $return = " join notices on id_notice = notice_id ";
            }
        }
        // $return = $this->_get_filter_by_custom_search($return);
        return $return;
    }

    public function get_full_query()
    {
        $this->get_result();
        return $this->_get_pert(true, false);
    }

    public function get_explnums($tri)
    {
        global $gestion_acces_active;
        global $gestion_acces_empr_notice;
        global $gestion_acces_empr_docnum;
        global $opac_show_links_invisible_docnums;
        global $opac_explnum_order;

        $this->explnums = array();
        $this->get_result();
        // $table = $this->_get_pert();
        $this->_get_pert();
        // liste complete des résultats..;
        if ($this->notices_ids != "") {
            $sort = new sort("notices", "session");
            // $query = $sort->appliquer_tri_from_tmp_table($tri,$table,"notice_id",0,0);
            $query = $sort->appliquer_tri_from_tmp_table($tri, $this->table_tempo, "notice_id", 0, 0);
            // vérification de la visibilité des documents numériques
            $join = '';
            $join_explnum = '';
            if (! $opac_show_links_invisible_docnums) {
                if ($gestion_acces_active == 1 && ($gestion_acces_empr_docnum || $gestion_acces_empr_notice)) {
                    $ac = new acces();
                    if ($gestion_acces_empr_notice == 1) {
                        $dom_2 = $ac->setDomain(2);
                        $join = $dom_2->getJoin($_SESSION['id_empr_session'], 16, 'notice_id');
                    }
                    if ($gestion_acces_empr_docnum == 1) {
                        $dom_3 = $ac->setDomain(3);
                        $join_explnum = $dom_3->getJoin($_SESSION['id_empr_session'], 16, 'explnum_id');
                    }
                }
                if (! $join) {
                    $join = "join notices on " . $sort->table_tri_tempo . ".notice_id = notices.notice_id join notice_statut on notices.statut=id_notice_statut and ((notice_statut.explnum_visible_opac=1 and notice_statut.explnum_visible_opac_abon=0)" . ($_SESSION["user_code"] ? " or (notice_statut.explnum_visible_opac_abon=1 and notice_statut.explnum_visible_opac=1)" : "") . ")";
                }
                if (! $join_explnum) {
                    $join_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0)" . ($_SESSION["user_code"] ? " or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)" : "") . ")";
                }
            }
            $explnum_noti = "select explnum_id," . $sort->table_tri_tempo . ".* from explnum join " . $sort->table_tri_tempo . " on explnum_notice!=0 and explnum_notice = " . $sort->table_tri_tempo . ".notice_id $join $join_explnum";
            if ($opac_explnum_order) {
                $explnum_noti .= " order by " . $opac_explnum_order;
            } else {
                $explnum_noti .= " order by explnum_mimetype, explnum_nom, explnum_id ";
            }
            $rqt = "create temporary table explnum_list $explnum_noti";
            pmb_mysql_query($rqt);
            $explnum_issue = "select explnum_id," . $sort->table_tri_tempo . ".* from explnum join bulletins on explnum_bulletin!=0 and bulletin_id = explnum_bulletin join " . $sort->table_tri_tempo . " on num_notice != 0 and num_notice = " . $sort->table_tri_tempo . ".notice_id $join $join_explnum";
            if ($opac_explnum_order) {
                $explnum_issue .= " order by " . $opac_explnum_order;
            } else {
                $explnum_issue .= " order by explnum_mimetype, explnum_nom, explnum_id ";
            }
            $rqt = "insert ignore into explnum_list $explnum_issue";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table explnum_list order by " . $sort->get_order_by($tri));
            $rqt = "select explnum_id from explnum_list order by " . $sort->get_order_by($tri);
            $res = pmb_mysql_query($rqt);
            // si get_order_by renvoit une valeur nulle, on ne s'occupe pas du tri.
            if (! $res) {
                $rqt = "select explnum_id from explnum_list";
                $res = pmb_mysql_query($rqt);
            }
            if ($res) {
                if (pmb_mysql_num_rows($res)) {
                    while ($row = pmb_mysql_fetch_object($res)) {
                        $this->explnums[] = $row->explnum_id;
                    }
                }
            }
        }
        return $this->explnums;
    }

    public static function get_current_search_map($mode_search = 0)
    {
        global $opac_map_activate;
        $map = "";
        if ($opac_map_activate == 1 || $opac_map_activate == 2) {
            $map = "<div id='map_container'><div id='map_search' ></div></div>";
        }
        return $map;
    }

    public static function check_emprises()
    {
        global $opac_map_activate;
        global $opac_map_max_holds;
        global $opac_map_size_search_result;
        $map = "";
        $size = explode("*", $opac_map_size_search_result);
        if (count($size) != 2) {
            $map_size = "width:800px; height:480px;";
        } else {
            if (is_numeric($size[0]))
                $size[0] .= 'px';
            if (is_numeric($size[1]))
                $size[1] .= 'px';
            $map_size = "width:" . $size[0] . "; height:" . $size[1] . ";";
        }
        $current_search = $_SESSION['nb_queries'];
        $map_search_controler = new map_search_controler(null, $current_search, $opac_map_max_holds, false);
        $json = $map_search_controler->get_json_informations();
        // Obligatoire pour supprimer les {}
        $json = substr($json, 1, strlen($json) - 2);
        if ($map_search_controler->have_results()) {
            $map .= "<script>
						require(['dojo/ready', 'dojo/dom-attr', 'dojo/parser', 'dojo/dom'], function(ready, domAttr, parser, dom){
							ready(function(){
								domAttr.set('map_search', 'data-dojo-type', 'apps/map/map_controler');
								domAttr.set('map_search', 'data-dojo-props','searchId: " . $current_search . ", mode:\"search_result\", " . $json . "');
								domAttr.set('map_search', 'style', '$map_size');
								parser.parse('map_container');
							});
						});
			</script>";
        } else {
            $map .= "<script>
						require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
							ready(function(){
								domConstruct.destroy('map_container');
							});
						});
			</script>";
        }
        print $map;
    }

    public function add_fields_restrict($fields_restrict = array())
    {
        $this->field_restrict = array_merge($this->field_restrict, $fields_restrict);
    }

    public function set_fields_restrict($fields_restrict = array())
    {
        $this->field_restrict = $fields_restrict;
    }

    public function init_fields_restrict($mode)
    {
        return false;
    }

    public function get_temporary_table_name($suffix = '')
    {
        return static::class . substr(md5(microtime(true)), 0, 16) . $suffix;
    }

    protected function _get_no_display()
    {
        global $no_display;
        return $no_display;
    }

    public function add_restrict_no_display()
    {
        $no_display = $this->_get_no_display();
        if ($no_display) {
            $fields_restrict = array(
                array(
                    'field' => 'id_notice',
                    'values' => array(
                        $no_display
                    ),
                    'op' => "and",
                    'not' => true
                )
            );
            $this->add_fields_restrict($fields_restrict);
        }
    }

    public function add_var_table(array $var_table)
    {
        $this->var_table = array_merge($var_table, $this->var_table);
    }

    /**
     * Création d'une table temporaire avec le calcul des pertinences
     *
     * @return string|false Nom de la table temporaire ou false si pas de pertinences
     */
    public function make_temporary_table_with_pert() {
    	if (empty($this->pert) || empty($this->notices_ids) ) {
    		return false;
    	}

    	$table_tempo = $this->get_temporary_table_name('_pert');
    	pmb_mysql_query("CREATE TEMPORARY TABLE $table_tempo (notice_id int(11) NOT NULL PRIMARY KEY, pert int(11) NOT NULL DEFAULT 0)");

    	$query = "INSERT INTO $table_tempo (notice_id, pert) VALUES ";

    	// On prend les identifians de $this->notices_ids, car ils sont filtre par droits
    	$notice_ids = explode(',', $this->notices_ids);
    	foreach ($notice_ids as $notice_id) {
    		$query .= "(" . $notice_id . ", " . $this->pert[$notice_id] . "),";
    	}

    	pmb_mysql_query(trim($query, ","));

    	return $table_tempo;
    }
}

class searcher_all_fields extends searcher
{

    protected $members_explnum_noti;

    // éléments de la requete sur les docnums de notices
    protected $members_explnum_bull;

    // éléments de la requete sur les docnums de bulletins
    protected $aq_wew;

    // modelisation de la recherche en conservant les mots vides
    public function __construct($user_query, $map_emprises = array())
    {
        global $opac_stemming_active;
        global $opac_search_all_keep_empty_words;

        parent::__construct($user_query, $map_emprises);

        if ($this->user_query && $opac_search_all_keep_empty_words) {
            $this->aq_wew = new analyse_query($this->user_query, 0, 0, 1, 1, $opac_stemming_active);
        }
        // on va l'utiliser pour gérer la recherche "tous les champs sauf les autorités" et "toutes les autorités"
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                18,
                19,
                20,
                21,
                23,
                24,
                25,
                26
            ),
            'op' => "or",
            'not' => false
        );
    }

    protected function _get_search_type()
    {
        return "all_fields";
    }

    public function get_full_query()
    {
        $this->get_result();
        return $this->_get_pert(true, false);
    }

    // spécialement pour la recherche tous les champs (histoire de mots vides et d'autorités...)
    protected function _get_all_fields_search_query()
    { // Je vois pas comment ça marche...
        global $lang;
        global $opac_search_all_keep_empty_words;

        // on applique la recherche à "tous les champs sauf les autorités"
        if ($opac_search_all_keep_empty_words && is_object($this->aq) && count($this->aq->tree) != count($this->aq_wew->tree)) {
            $restrict = $this->field_restrict;
            // $restrict[] = array(
            // 'field' => "lang",
            // 'values' => array("",$lang),
            // 'op' => "and",
            // 'not' => true
            // );
            $query_without_authories = $this->aq->get_query_mot("id_notice", "notices_mots_global_index", "word", "notices_fields_global_index", "value", $restrict, true, true);
            $restrict = $this->field_restrict;
            // $restrict[] = array(
            // 'field' => "lang",
            // 'values' => array("",$lang),
            // 'op' => "and",
            // 'not' => false
            // );
            $query_authorities = $this->aq_wew->get_query_mot("id_notice", "notices_mots_global_index", "word", "notices_fields_global_index", "value", $restrict, false);
            $query = "select distinct id_notice from (($query_without_authories) union ($query_authorities)) as q1";
            if ($this->_get_typdoc_filter() != "") {
                $query .= $this->_get_typdoc_filter();
            }
        } else {
            $restrict = array();
            // $restrict[] = array(
            // 'field' => "lang",
            // 'values' => array("",$lang),
            // 'op' => "and",
            // 'not' => false
            // );
            if(is_object($this->aq)) {
                $query = $this->aq->get_query_mot("id_notice", "notices_mots_global_index", "word", "notices_fields_global_index", "value", $restrict, false, true);
            } else {
                $query = "";
            }
            if ($this->_get_typdoc_filter() != "") {
                $query = "select distinct id_notice from ($query) as q1" . $this->_get_typdoc_filter();
            }
        }
        return $query;
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        global $opac_indexation_docnum_allfields, $dont_check_opac_indexation_docnum_allfields, $multi_crit_indexation_docnum_allfields;
        global $opac_search_all_keep_empty_words;

        if ($multi_crit_indexation_docnum_allfields) { // On est dans le cas de la recherche mutli-critères
            if ($multi_crit_indexation_docnum_allfields > 0) {
                $with_explnum = true;
            }
        } elseif (($opac_indexation_docnum_allfields && ! $dont_check_opac_indexation_docnum_allfields)) {
            $with_explnum = true;
        }
        if ($opac_search_all_keep_empty_words && $this->user_query !== "*" && (count($this->aq->tree) != count($this->aq_wew->tree))) {
            $without_empty = $this->aq->get_pert($this->notices_ids, $this->field_restrict, true, $with_explnum, true, true);
            $with_empty = $this->aq_wew->get_pert($this->notices_ids, $this->field_restrict, false, $with_explnum, true, true);
            $get_query = "select notice_id, max(pert) as pert from (($without_empty) union all($with_empty))as q1 group by notice_id";
            if ($query) {
                return $get_query;
            } else {
                $this->table_tempo = $this->get_temporary_table_name('get_pert');
                pmb_mysql_query("create temporary table " . $this->table_tempo . " " . $get_query);
                pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(notice_id)");
            }
        } else {
            if (is_object($this->aq)) {
                if ($query) {
                    return $this->aq->get_pert($this->notices_ids, array(), false, $with_explnum, true, true);
                }
                $this->table_tempo = $this->aq->get_pert($this->notices_ids, array(), false, $with_explnum, false, true);
            }
        }
    }

    // la surcharge de la fonction
    protected function _get_search_query()
    {
        global $opac_indexation_docnum_allfields, $dont_check_opac_indexation_docnum_allfields, $multi_crit_indexation_docnum_allfields;

        $this->_calc_query_env();
        if ($this->user_query !== "*") {
            // $opac_indexation_docnum_allfields -> Paramétre Opac pour savoir si en recherche simple on cherche ou non dans les documents numériques
            // $dont_check_opac_indexation_docnum_allfields -> Paramètre utilisé dans les fonctions de recherche simple perso pour choisir si on cherche dans les doc numériques
            // $multi_crit_indexation_docnum_allfields -> Paramètre utilisé dans les fonctions de recherche multicritère pour choisir si on cherche dans les doc numériques
            $pass = false;
            if ($multi_crit_indexation_docnum_allfields) { // On est dans le cas de la recherche mutli-critères
                if ($multi_crit_indexation_docnum_allfields > 0) {
                    $pass = true;
                }
            } elseif (($opac_indexation_docnum_allfields && ! $dont_check_opac_indexation_docnum_allfields)) {
                $pass = true;
            }
            if ($pass) {
                // si la recherche dans les documents numériques est incluse dans la recherche tous les champs, on doit le prendre en compte
                $this->_get_explnum_members();
                $query_noti = $this->_get_all_fields_search_query();
                $query_explnum_noti = "select distinct explnum_notice as id_notice from explnum " . $this->_get_explnum_filter("notice", "explnum_notice") . " " . $this->_get_explnum_docnum_filter("docnum", "explnum_id") . " " . $this->_get_explnum_where() . " and explnum_notice !=0 and explnum_bulletin=0 "; // .$this->_get_explnum_end("notice");
                $query_explnum_bull = "select distinct num_notice as id_notice from explnum join bulletins on num_notice!= 0 and explnum_bulletin = bulletin_id " . $this->_get_explnum_filter("bulletin", "num_notice") . " " . $this->_get_explnum_docnum_filter("docnum", "explnum_id") . " " . $this->_get_explnum_where() . " and explnum_bulletin !=0 and explnum_notice=0 "; // .$this->_get_explnum_end();
                if ($this->_get_typdoc_filter() != "") {
                    $query_explnum_noti = "select distinct id_notice from ($query_explnum_noti) as q2 " . $this->_get_typdoc_filter();
                    $query_explnum_bull = "select distinct id_notice from ($query_explnum_bull) as q3 " . $this->_get_typdoc_filter();
                }
                $query = "select distinct id_notice from (($query_noti) union ($query_explnum_noti) union ($query_explnum_bull))as uni ";
            } else {
                $query = $this->_get_all_fields_search_query();
            }
        } else {
            $query = "select distinct notice_id as id_notice from notices";
            if ($this->_get_typdoc_filter(true) != "") {
                $query .= $this->_get_typdoc_filter(true);
            }
        }
        $this->_get_filter_by_custom_search($query);

        return $query;
    }

    protected function _get_explnum_end($type)
    {
        if ($type == "notice") {
            return $this->members_explnum_noti['post'];
        } else {
            return $this->members_explnum_bull['post'];
        }
    }

    protected function _get_explnum_members()
    {
        if (is_object($this->aq)) {
            $this->members_explnum_noti = $this->aq->get_query_members("explnum", "explnum_index_wew", "explnum_index_sew", "explnum_notice", "", 0, 0, true);
            $this->members_explnum_bull = $this->aq->get_query_members("explnum", "explnum_index_wew", "explnum_index_sew", "id_notice", "", 0, 0, true);
        }
    }

    protected function _get_explnum_pert()
    {
        return $this->members_explnum_noti['select'] . " as pert";
    }

    protected function _get_explnum_where()
    {
        $where = "where ((" . $this->members_explnum_noti['where'] . ")) ";
        // if($this->view_restrict) $where.=" and ".$this->view_restrict;
        return $where;
    }

    protected function _get_explnum_filter($type = "notice", $field = "")
    {
        global $gestion_acces_active;
        global $gestion_acces_empr_notice;
        if ($gestion_acces_active == 1 && $gestion_acces_empr_notice == 1) {
            $ac = new acces();
            $dom_2 = $ac->setDomain(2);
            $join = $dom_2->getJoin($_SESSION['id_empr_session'], 16, $field);
        } else {
            $join = '';
        }
        if (! $join) {
            switch ($type) {
                case "notice":
                    $join = "join notices on explnum_notice = notice_id and explnum.explnum_bulletin = 0 join notice_statut on notices.statut= id_notice_statut and (notice_statut.explnum_visible_opac=1 and notice_statut.explnum_visible_opac_abon=0)" . ($_SESSION["user_code"] ? " or (notice_statut.explnum_visible_opac_abon=1 and notice_statut.explnum_visible_opac=1)" : "");
                    break;
                case "bulletin":
                    $join = "join notices on notices.notice_id = bulletins.num_notice and explnum.explnum_notice = 0 join notice_statut on notices.statut= id_notice_statut and (notice_statut.explnum_visible_opac=1 and notice_statut.explnum_visible_opac_abon=0)" . ($_SESSION["user_code"] ? " or (notice_statut.explnum_visible_opac_abon=1 and notice_statut.explnum_visible_opac=1)" : "");
                    break;
            }
        }
        return $join;
    }

    protected function _get_explnum_docnum_filter($type = "docnum", $field = "")
    {
        global $gestion_acces_active;
        global $gestion_acces_empr_docnum;
        if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
            $ac = new acces();
            $dom_3 = $ac->setDomain(3);
            $join = $dom_3->getJoin($_SESSION['id_empr_session'], 16, $field);
        } else {
            $join = '';
        }
        if (! $join) {
            switch ($type) {
                case "docnum":
                    $join = "join explnum_statut on explnum_docnum_statut= id_explnum_statut and (explnum_statut.explnum_visible_opac=1 and explnum_statut.explnum_visible_opac_abon=0)" . ($_SESSION["user_code"] ? " or (explnum_statut.explnum_visible_opac_abon=1 and explnum_statut.explnum_visible_opac=1)" : "");
                    break;
            }
        }
        return $join;
    }
}

class searcher_title extends searcher
{

    public function __construct($user_query)
    {
        global $multi_crit_indexation_oeuvre_title;
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                1,
                2,
                3,
                4,
                6,
                23
            ),
            'op' => "and",
            'not' => false
        );

        if ($multi_crit_indexation_oeuvre_title == 1) {
            $this->field_restrict[] = array(
                'field' => "code_champ",
                'values' => array(
                    26
                ),
                'op' => "or",
                'not' => false,
                'sub' => array(
                    array(
                        'sub_field' => "code_ss_champ",
                        'values' => 1,
                        'op' => "and",
                        'not' => false
                    )
                )
            );
        }
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "title";
    }
}

class searcher_keywords extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 17,
            'op' => "and",
            'not' => false
        );
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "keywords";
    }
}

class searcher_tags extends searcher
{

    public function __construct($user_query)
    {
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "tags";
    }

    protected function _get_search_query()
    {
        global $pmb_keyword_sep;

        $query = "SELECT notice_id AS id_notice
				FROM notices
				WHERE index_l like '" . addslashes($this->user_query) . "'
				OR index_l like '" . addslashes($this->user_query . $pmb_keyword_sep) . "%'
				OR index_l like '%" . addslashes($pmb_keyword_sep . $this->user_query . $pmb_keyword_sep) . "%'
				OR index_l like '%" . addslashes($pmb_keyword_sep . $this->user_query) . "'";
        return $query;
    }
}

class searcher_notes extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                12,
                13,
                14
            ),
            'op' => "and",
            'not' => false
        );
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "notes";
    }
}

class searcher_abstract extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 14,
            'op' => "and",
            'not' => false
        );
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "abstract";
    }
}

class searcher_general_note extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 12,
            'op' => "and",
            'not' => false
        );
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "general_note";
    }
}

class searcher_contents_note extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 13,
            'op' => "and",
            'not' => false
        );
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "contents_note";
    }
}

class searcher_external_extended extends searcher_extended
{

    protected function _get_search_type()
    {
        return "external_extended";
    }

    protected function _filter_results()
    {
        $this->notices_ids = implode(',', array_unique(explode(',', $this->notices_ids)));
        // on derive la methode pour ne pas filtrer les notices externes en fonction des droits ou de la visibilite
    }

    // public function set_selected_sources($sources) {
    // global $selected_sources;
    // $selected_sources = implode(',', $sources);
    // $_SESSION['checked_sources'] = $sources;
    // }
    protected function get_search_instance()
    {
        return new search('search_fields_unimarc_gestion');
    }

    public function get_result()
    {
        // A revoir
        $this->_get_notices_ids();
        $this->_filter_results();
        return $this->notices_ids;
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_extended::_get_pert($query, $with_explnum)
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if (! $this->notices_ids) {
            return;
        }
        if ($this->with_make_search) {
            $this->table_tempo = $this->get_temporary_table_name("get_pert");
            $rqt = "create temporary table " . $this->table_tempo . " select * from " . $this->table . " where notice_id in(" . $this->notices_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(notice_id)");
        } else {
            $this->table_tempo = $this->get_temporary_table_name("get_pert_2");
            $rqt = "create temporary table " . $this->table_tempo . " select rid as notice_id,100 as pert from external_count where rid in(" . $this->notices_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(notice_id)");
        }
    }

    public function get_sorted_result($tri = "default", $start = 0, $number = 20)
    {
        // A revoir derivation
        $this->tri = $tri;
        $this->_get_notices_ids();
        $this->_filter_results();
        $this->_sort_result($start, $number);
        return $this->result;
    }
}

class searcher_extended extends searcher
{

    protected $serialized_query;

    // recherche sérialisée
    protected $with_make_search;

    // Savoir si on peut avoir la pertinance ou non
    public $table;

    // table tempo de la multi
    public function __construct($serialized_query = "")
    {
        $this->with_make_search = false;
        $this->serialized_query = $serialized_query;
        parent::__construct("");
    }

    public function get_raw_query()
    {
        if (!empty($this->table)) {
            return "SELECT * FROM $this->table";
        }
        return "";
    }

    protected function _get_search_type()
    {
        return "extended";
    }

    protected function _get_user_query()
    {
        global $es;
        if (! is_object($es))
            $es = $this->get_search_instance();
        return $es->serialize_search(true);
    }

    protected function _get_search_query()
    {
        global $es, $msg;

        if (! is_object($es))
            $es = $this->get_search_instance();
        if ($this->serialized_query) {
            $es->unserialize_search($this->serialized_query);
        } else {
            global $search;
            // Vérification des champs vides
            if (is_array($search) && count($search)) {
                for ($i = 0; $i < count($search); $i ++) {
                    if ($i == 0) { // On supprime le premier opérateur inter (il est renseigné pour les recherches prédéfinies avec plusieurs champs et une recherche avec le premier champ vide
                        $inter = "inter_" . $i . "_" . $search[$i];
                        global ${$inter};
                        ${$inter} = "";
                    }
                    $op = "op_" . $i . "_" . $search[$i];
                    global ${$op};
                    $field_ = "field_" . $i . "_" . $search[$i];
                    global ${$field_};
                    $field = ${$field_};
                    $s = explode("_", $search[$i]);
                    if ($s[0] == "f") {
                        $champ = $es->fixedfields[$s[1]]["TITLE"];
                    } elseif ($s[0] == "s") {
                        $champ = $es->specialfields[$s[1]]["TITLE"];
                    } elseif ($s[0] == 'authperso') {
                        // TO DO
                    } elseif ($s[0] == "u") {
                        $champ = search::get_instance("search_universes_fields")->universesfields[$s[1]]["TITLE"] ?? "";
                    } else {
                        $champ = $es->pp->t_fields[$s[1]]["TITRE"];
                    }
                    if (! is_array($field[0]) && ((string) $field[0] == "") && (! $es->op_empty[${$op}])) {
                        // TO DO ?
                        $search_error_message = sprintf($msg["extended_empty_field"], $champ);
                        $flag = true;
                        break;
                    }
                }
            }
        }
        // $es->remove_forbidden_fields();
        $this->with_make_search = true;

        $this->table = $es->make_search($this->get_temporary_table_name("_" . rand(0, 10) . "_"));
        if (!empty($this->table)) {
            return "select notice_id as id_notice, pert from " . $this->table;
        }
        return "";
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher::_get_pert($query, $with_explnum)
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if (! $this->notices_ids) {
            return;
        }
        if ($this->with_make_search) {
            $this->table_tempo = $this->get_temporary_table_name("get_pert");
            $rqt = "create temporary table " . $this->table_tempo . " select * from " . $this->table . " where notice_id in(" . $this->notices_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(notice_id)");
        } else {
            $this->table_tempo = $this->get_temporary_table_name("get_pert_2");
            $rqt = "create temporary table " . $this->table_tempo . " select notice_id,100 as pert from notices where notice_id in(" . $this->notices_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(notice_id)");
        }
    }

    public function get_result()
    {
        $cache_result = $this->_get_in_cache();
        if ($cache_result === false) {
            $this->_get_notices_ids();
            $this->_filter_results();
            $this->_set_in_cache();
        } else {
            $this->notices_ids = $cache_result;
            if (! $this->notices_ids) {
                return array();
            }
            $this->table = $this->get_temporary_table_name('get_result');
            $rqt = "create temporary table " . $this->table . " engine=memory select notice_id from notices where notice_id in(" . $this->notices_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table . " add index i_id(notice_id)");
            if (! empty($this->pert)) {
                $query = "alter table " . $this->table . " add pert decimal(16,1) default 1";
                pmb_mysql_query($query);
                //Adaptation du tableau pour ne plus réaliser un UPDATE par identifiant
                $reverse_pert = array();
                foreach ($this->pert as $id => $pert) {
                    if(empty($reverse_pert[$pert])) {
                        $reverse_pert[$pert] = array();
                    }
                    $reverse_pert[$pert][] = $id;
                }
                foreach ($reverse_pert as $pert => $records_ids) {
                    $query = "UPDATE " . $this->table . " SET pert = $pert WHERE notice_id IN (".implode(',', $records_ids).")";
                    pmb_mysql_query($query);
                }
            }
        }
        return $this->notices_ids;
    }

    protected function get_search_instance()
    {
        return new search();
    }

    public function reset_search()
    {
        $this->searched = false;
        $this->serialized_query = "";
        $this->user_query = "";
    }
}

class searcher_authors extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                27,
                28,
                29
            ),
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "authors";
    }

    protected function _analyse()
    {
        if ($this->user_query) {
            // on veut pas du stemming pour les auteurs
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, false);
        }
    }
}

class searcher_publishers extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 19,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "publishers";
    }

    protected function _analyse()
    {
        if ($this->user_query) {
            // on veut pas du stemming pour les éditeurs
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, false);
        }
    }
}

class searcher_indexint extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 20,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "indexint";
    }
}

class searcher_collection extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 21,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "collection";
    }

    protected function _analyse()
    {
        if ($this->user_query) {
            // on veut pas du stemming pour les collections
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, false);
        }
    }
}

class searcher_subcollection extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 24,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "subcollection";
    }

    protected function _analyse()
    {
        if ($this->user_query) {
            // on veut pas du stemming pour les sous-collections
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, false);
        }
    }
}

class searcher_serie extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 23,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "serie";
    }

    protected function _analyse()
    {
        if ($this->user_query) {
            // on veut pas du stemming pour les séries
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, false);
        }
    }
}

class searcher_uniform_title extends searcher
{

    public function __construct($user_query)
    {
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 26,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "uniform_title";
    }
}

class searcher_categorie extends searcher
{

    public function __construct($user_query)
    {
        global $lang;
        $this->field_restrict = array();
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 25,
            'op' => "and",
            'not' => false
        );
        $this->field_restrict[] = array(
            'field' => "lang",
            'values' => $lang,
            'op' => "and",
            'not' => false
        );
        $this->keep_empty = 1;
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "categorie";
    }
}

class searcher_pfield extends searcher
{

    protected $id;

    public function __construct($user_query, $id = 0)
    {
        $this->field_restrict = array();
        $this->id = $id;
        $sub = array();
        if ($this->id > 0) {
            $sub[] = array(
                'sub_field' => "code_ss_champ",
                'values' => $this->id,
                'op' => "and",
                'not' => false
            );
        }
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 100,
            'op' => "and",
            'not' => false,
            'sub' => $sub
        );

        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "pfield";
    }

    protected function _get_sign($sorted = false)
    {
        $sign = parent::_get_sign($sorted);
        $sign .= md5('&id=' . $this->id);
        return $sign;
    }
}

class searcher_generic extends searcher
{

    public function __construct($user_query, $fields = "")
    {
        $this->fields_list_gen = $fields;
        $this->field_restrict = array();
        $tmp = explode(",", $fields); // On récupère la liste des champs
        if (count($tmp)) {
            if (! preg_match("/;/", $fields)) { // Si je n'ai pas de ; alors je n'ai qu'une liste de champ
                if (count($tmp) == 1) { // Je n'ai qu'un champ
                    $this->field_restrict[] = array(
                        'field' => "code_champ",
                        'values' => $fields,
                        'op' => "and",
                        'not' => false
                    );
                } else {
                    $this->field_restrict[] = array(
                        'field' => "code_champ",
                        'values' => $tmp,
                        'op' => "and",
                        'not' => false
                    );
                }
            } else {
                foreach ($tmp as $value) {
                    $matches = [];
                    if (preg_match("/^([0-9]+?);([0-9;]+)$/", $value, $matches)) {
                        // J'ai un ou des sous champs
                        if (preg_match("/;/", $matches[2])) {
                            $list_sub = explode(";", $matches[2]);
                        } else {
                            $list_sub = $matches[2];
                        }
                        $sub = array();
                        $sub[] = array(
                            'sub_field' => "code_ss_champ",
                            'values' => $list_sub, // La liste des sous champs
                            'op' => "and",
                            'not' => false
                        );
                        $this->field_restrict[] = array(
                            'field' => "code_champ",
                            'values' => $matches[1], // Le champ
                            'op' => "or",
                            'not' => false,
                            'sub' => $sub
                        );
                    } else {
                        $this->field_restrict[] = array(
                            'field' => "code_champ",
                            'values' => $value,
                            'op' => "or",
                            'not' => false
                        );
                    }
                }
            }
        }
        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "generic_" . $this->fields_list_gen;
    }
}

class searcher_authperso extends searcher
{

    public function __construct($user_query, $id = 0)
    {
        $this->field_restrict = array();
        $sub = array();
        if ($id > 0) {
            $sub[] = array(
                'sub_field' => "code_ss_champ",
                'values' => $id,
                'op' => "and",
                'not' => false
            );
        }
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => 100,
            'op' => "and",
            'not' => false,
            'sub' => $sub
        );

        parent::__construct($user_query);
    }

    protected function _get_search_type()
    {
        return "authperso";
    }
}

class searcher_concept extends searcher
{

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                36
            ),
            'op' => "and",
            'not' => false
        );
    }

    protected function _get_search_type()
    {
        return "concept";
    }
}

class searcher_concept_autopostage extends searcher
{

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->field_restrict[] = array(
            'field' => "code_champ",
            'values' => array(
                36,
                129
            ),
            'op' => "and",
            'not' => false
        );
    }

    protected function _get_search_type()
    {
        return "concept";
    }
}