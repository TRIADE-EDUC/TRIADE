<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_tab.class.php,v 1.22 2023/07/27 06:57:37 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/searcher_autorities.class.php');

class searcher_authorities_tab extends searcher_autorities
{

    /**
     * Tableau des instances de searcher_authorities
     *
     * @var searcher_autorities
     */
    protected $searcher_authorities_instances;

    protected $global_filters_setted;

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->search_noise_limit_type = false;
        $this->global_filters_setted = false;
    }

    public function _get_search_type()
    {
        return parent::_get_search_type() . "_tab";
    }

    protected function _analyse()
    {
        $this->_get_searcher_authorities_instances();
        $this->_set_global_filters();
    }

    protected function _get_search_query()
    {
        if (count($this->searcher_authorities_instances) == 1) {
            return $this->searcher_authorities_instances[0]->get_raw_query();
        } else {
            $query = '';
            if (count($this->searcher_authorities_instances)) {
                foreach ($this->searcher_authorities_instances as $searcher_authority) {
                    $created_table = 'search_result' . md5(microtime(true));
                    $rqt = 'create temporary table ' . $created_table . ' ' . $searcher_authority->get_raw_query();
                    pmb_mysql_query($rqt);
                    pmb_mysql_query('alter table ' . $created_table . ' add index i_id(' . $this->object_index_key . ')');
                    if (! $query) {
                        $reference = $created_table;
                        $query = 'select ' . $reference . '.' . $this->object_index_key . ' from ' . $reference;
                    } else {
                        $query .= ' join ' . $created_table . ' on ' . $reference . '.' . $this->object_index_key . ' = ' . $created_table . '.' . $this->object_index_key;
                    }
                }
                $query .= ' group by ' . $reference . '.' . $this->object_index_key;
                return $query;
            }
        }
        return '';
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_autorities::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if (count($this->searcher_authorities_instances) == 1) {
            $pert_result = $this->searcher_authorities_instances[0]->get_pert_result($query);
            if ($query) {
                return $pert_result;
            }
            $this->table_tempo = $pert_result;
        } else {
            $pert = '';
            foreach ($this->searcher_authorities_instances as $searcher_authority) {
                $searcher_table_tempo = $searcher_authority->get_pert_result();
                if (! $pert) {
                    $reference = $searcher_table_tempo;
                    $pert = 'select ' . $reference . '.' . $this->object_key . ', sum(' . $reference . '.pert) as pert from ' . $reference;
                } else {
                    $join_table = $searcher_table_tempo;
                    $pert .= ' join ' . $join_table . ' on ' . $reference . '.' . $this->object_key . ' = ' . $join_table . '.' . $this->object_key;
                }
            }
            $pert .= ' group by ' . $reference . '.' . $this->object_key;
            if ($query) {
                return $pert;
            }
            $this->table_tempo = 'search_result' . md5(microtime(true));
            $rqt = 'create temporary table ' . $this->table_tempo . ' ' . $pert;
            pmb_mysql_query($rqt);
            pmb_mysql_query('alter table ' . $this->table_tempo . ' add index i_id(' . $this->object_key . ')');
        }
    }

    protected function _get_user_query()
    {
        return serialize($this->user_query);
    }

    /**
     * Initialise le tableau d'instances de searcher_authorities
     *
     * @return searcher_autorities
     */
    protected function _get_searcher_authorities_instances()
    {
        if (! $this->searcher_authorities_instances) {
            $this->searcher_authorities_instances = array();
            foreach ($this->user_query['SEARCHFIELDS'] as $searchfield) {
                $instance = null;
                if (isset($searchfield['values']['id'])) {
                    if (! empty($searchfield['values']['id'][0])) {
                        $instance = searcher_factory::get_searcher($searchfield['type'], 'query', $searchfield['values']);
                        $instance->set_query($searchfield['queryid']);
                        if (! empty($searchfield['queryfilter'])) {
                            $instance->set_filter($searchfield['queryfilter']);
                        }
                    } else {
                        $searchfield['values'] = $searchfield['values']['values'];
                    }
                }
                if (isset($searchfield['values'][0]) && is_string($searchfield['values'][0])) {
                    $searchfield['values'][0] = stripslashes($searchfield['values'][0]);
                }
                if (isset($searchfield['values'][0]) && $searchfield['values'][0] && $searchfield['type']) {
                    $instance = searcher_factory::get_searcher($searchfield['type'], $searchfield['mode'], $searchfield['values'][0]);
                } else if (isset($searchfield['values'][0]) && $searchfield['values'][0] && $searchfield['class']) {
                    $instance = new $searchfield['class']($searchfield['values'][0]);
                }
                if (is_object($instance)) {
                    if (isset($searchfield['fieldrestrict']) && is_array($searchfield['fieldrestrict'])) {
                        $instance->add_fields_restrict($searchfield['fieldrestrict']);
                    }
                    $instance->add_restrict_no_display();
                    if (isset($searchfield['query']) && $searchfield['mode'] == "query") {
                        $instance->set_query($searchfield['query']);
                    }
                    if (! empty($searchfield['details'])) {
                        $instance->set_details($searchfield['details']);
                    }
                    if (! empty($this->context_parameters) && method_exists($instance, 'set_context_parameters')) {
                        $instance->set_context_parameters($this->context_parameters);
                    }
                    $this->searcher_authorities_instances[] = $instance;
                }
            }
        }
        return $this->searcher_authorities_instances;
    }

    /**
     * Valorise les globales nécessaires aux searcher_authorities pour les filtres
     */
    protected function _set_global_filters()
    {
        if (! $this->global_filters_setted) {
            if (isset($this->user_query['FILTERFIELDS'])) {
                foreach ($this->user_query['FILTERFIELDS'] as $filterfield) {
                    if (isset($filterfield['values'][0]) && $filterfield['values'][0] && $filterfield['globalvar']) {
                        global ${$filterfield['globalvar']};
                        if (isset($filterfield['multiple']) && $filterfield['multiple']) {
                            ${$filterfield['globalvar']} = $filterfield['values'];
                        } else {
                            ${$filterfield['globalvar']} = $filterfield['values'][0];
                        }
                    } else {
                        // cas bizarre on on recolle le filtre comme élément de la recherche
                        $instance = null;
                        if (isset($filterfield['values'][0]) && $filterfield['values'][0] && $filterfield['type']) {
                            $instance = searcher_factory::get_searcher($filterfield['type'], $filterfield['mode'], stripslashes($filterfield['values'][0]));
                        } else if (isset($filterfield['values'][0]) && $filterfield['values'][0] && $filterfield['class']) {
                            $instance = new $filterfield['class'](stripslashes($filterfield['values'][0]));
                        }
                        if (is_object($instance)) {
                            if (isset($filterfield['fieldrestrict']) && is_array($filterfield['fieldrestrict'])) {
                                $instance->add_fields_restrict($filterfield['fieldrestrict']);
                            }
                            if (isset($filterfield['query']) && $filterfield['mode'] == "query") {
                                $instance->set_query($filterfield['query']);
                            }
                            if (! empty($filterfield['details'])) {
                                $instance->set_details($filterfield['details']);
                            }
                            if (! empty($filterfield['keep_empty_words']) && method_exists($instance, 'set_empty_words')) {
                                $instance->set_empty_words($filterfield['keep_empty_words']);
                            }
                            $this->searcher_authorities_instances[] = $instance;
                        }
                    }
                }
                $this->global_filters_setted = true;
            }
        }
    }

    public function get_authority_tri()
    {
        if (count($this->searcher_authorities_instances) == 1) {
            if (! $this->searcher_authorities_instances[0]->get_authority_type()) {
                return "";
            }
            $this->authority_type = $this->searcher_authorities_instances[0]->get_authority_type();
            $this->object_key = $this->searcher_authorities_instances[0]->get_object_key();
            $this->object_table = $this->searcher_authorities_instances[0]->get_object_table();
            $this->object_table_key = $this->searcher_authorities_instances[0]->get_object_table_key();
            return $this->searcher_authorities_instances[0]->get_authority_tri();
        }
        return '';
    }
}