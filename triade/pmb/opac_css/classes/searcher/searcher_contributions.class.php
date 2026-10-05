<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_contributions.class.php,v 1.8 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class searcher_contributions extends searcher_sparql
{

    protected $type = '';

    protected $empr_id = 0;

    protected $rdf_type = '';

    public function __construct($user_query = '')
    {
        parent::__construct($user_query);
        $this->init_datastore('contribution_area_datastore');
    }

    protected function _get_search_query()
    {
        global $param2, $from_contrib;

        $query = "";
        $query = "SELECT ?uri ?prop ?obj WHERE {
            ?uri <http://www.pmbservices.fr/ontology#has_contributor> '" . $this->empr_id . "' . ";
        if ($param2 && ! $from_contrib) {
            $query .= "?uri pmb:area $param2 . ";
        }
        $query .= "?uri rdf:type <$this->rdf_type> . ";

        foreach ($this->user_query as $key => $user_queries) {
            foreach ($user_queries as $user_query) {
                $termSearch = "";
                $termSearchUri = "";

                if (! empty($user_query['values'][0])) {
                    $termSearch = $user_query['values'][0];
                } else {
                    $termSearch = (! empty($user_query['values']['values'][0]) ? $user_query['values']['values'][0] : "");
                    $termSearchUri = (! empty($user_query['values']['id'][0]) ? $user_query['values']['id'][0] : "");
                }

                if (! empty($termSearch)) {
                    if (! empty($user_query['fieldcontribution'][0]['values'][0]) && ($user_query['fieldcontribution'][0]['values'][0] == "all_fields")) {
                        // On fait une recherche "Tous les champs"
                        $query .= "?uri ?prop ?value . ";
                        if (addslashes(substr($termSearch, 0, 1)) != '*') {
                            $query .= "filter regex(?value, '" . addslashes($termSearch) . "','i') .";
                        } else {
                            $query .= "filter regex(?value, '" . addslashes(substr($termSearch, 1)) . "','i') .";
                        }
                    } else {
                        $filters = array();
                        $properties = array();
                        $query_temp = "";
                        if (! empty($user_query['fieldcontribution'][0]['values'])) {
                            foreach ($user_query['fieldcontribution'][0]['values'] as $key => $searchProperty) {
                                // $searchProperty est une uri complète

                                if (0 < $key)
                                    $query .= "optional { ";

                                if (stristr($searchProperty, '#')) {
                                    $properties = explode("#", $searchProperty);
                                    $query_temp .= "?uri <$searchProperty> ?$properties[1] . ";
                                } else {
                                    // $searchProperty est une uri avec un namespace
                                    $properties = explode(":", $searchProperty);
                                    $query_temp .= "?uri $searchProperty ?$properties[1] . ";
                                }

                                if (0 < $key)
                                    $query .= "} .";

                                if ($properties[1] == "has_concept") {
                                    if (! empty($termSearchUri)) {
                                        $identifier = onto_common_uri::get_id($termSearchUri);
                                        $query_temp .= "?$properties[1] <http://www.pmbservices.fr/ontology#identifier> '$identifier' . ";
                                    } else {
                                        // Si on a pas de $termSearchUri on fait pas de recherche
                                        $query_temp = "";
                                    }
                                } elseif (addslashes(substr($termSearch, 0, 1)) != '*') {
                                    $filters[] .= "regex(?$properties[1], '" . addslashes($termSearch) . "','i')";
                                } else {
                                    $filters[] .= "regex(?$properties[1], '" . addslashes(substr($termSearch, 1)) . "','i')";
                                }
                            }
                        }

                        if (! empty($filters)) {
                            $query_temp .= "filter (";
                            foreach ($filters as $key => $filter) {
                                if ($key == 0) {
                                    $query_temp .= " $filter ";
                                } else {
                                    if ($user_query['fieldcontribution'][0]['op'] == 'or') {
                                        $query_temp .= "|| $filter ";
                                    } else {
                                        $query_temp .= "&& $filter ";
                                    }
                                }
                            }
                            $query_temp .= ") . ";
                        }

                        $query .= $query_temp;
                    }
                }
            }
        }
        $query .= "
            ?uri ?prop ?obj . 
        }
        GROUP BY ?uri";
        return $query;
    }

    protected function _get_search_type()
    {
        return 'contributions';
    }

    protected function _filter_results()
    {
        $contribution_area_store = new contribution_area_store();
        $ids = explode(",", $this->objects_ids);
        $ids_filter = array();
        foreach ($ids as $uri) {
            // On récupère pas les contribions les contribution Validée et/ou Brouillon
            if ($contribution_area_store->get_pmb_identifier_from_uri($uri) == 0 && $contribution_area_store->is_draft_from_uri($uri) != true) {
                $ids_filter[] = $uri;
            }
        }
        $this->objects_ids = implode(",", $ids_filter);
        return $this->objects_ids;
    }

    protected function _analyse()
    {
        // Pas besoin d'analyse, on attaque dans le store
    }

    /**
     *
     * {@inheritdoc}
     * @see opac_searcher_generic::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        // Pas besoin de pertinence non plus
    }

    public function get_result()
    {
        $this->_delete_old_objects();
        $this->_analyse();
        $cache_result = $this->_get_in_cache();
        if ($cache_result === false) {
            $this->_get_objects_ids();
            $this->_filter_results();
            // Ecretage : on le supprime
            $this->_set_in_cache();
        } else {
            $this->objects_ids = $cache_result;
        }
        return $this->objects_ids;
    }

    protected function _sort($start, $number)
    {
        if (! empty($this->objects_ids)) {
            $objects_ids = explode(',', $this->objects_ids);
            for ($i = $start; $i < $number; $i ++) {
                if (isset($objects_ids[$i])) {
                    $this->result[] = $objects_ids[$i];
                }
            }
        }
    }

    public function set_empr_id($empr_id)
    {
        $this->empr_id = $empr_id;
    }

    public function set_rdf_type($rdf_type)
    {
        $this->rdf_type = $rdf_type;
    }
}