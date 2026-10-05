<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_tab.class.php,v 1.4 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/searcher_records.class.php');

class searcher_records_tab extends searcher_records
{

    /**
     * Tableau des instances de searcher_records
     *
     * @var searcher_records
     */
    protected $searcher_records_instances;

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->_analyse();
        $this->search_noise_limit_type = false;
    }

    public function _get_search_type()
    {
        return parent::_get_search_type() . "_tab";
    }

    protected function _analyse()
    {
        $this->_get_searcher_records_instances();
        $this->_set_global_filters();
    }

    protected function _get_search_query()
    {
        if (count($this->searcher_records_instances) == 1) {
            return $this->searcher_records_instances[0]->get_raw_query();
        } else {
            $query = '';
            foreach ($this->searcher_records_instances as $searcher_record) {
                $created_table = 'search_result' . md5(microtime(true));
                $rqt = 'create temporary table ' . $created_table . ' ' . $searcher_record->get_raw_query();
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
        return '';
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_records::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if (count($this->searcher_records_instances) == 1) {
            $pert_result = $this->searcher_records_instances[0]->get_pert_result($query);
            if ($query) {
                return $pert_result;
            }
            $this->table_tempo = $pert_result;
        } else {
            $pert = '';
            foreach ($this->searcher_records_instances as $searcher_record) {
                $searcher_table_tempo = $searcher_record->get_pert_result();
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
     * Initialise le tableau d'instances de searcher_records
     *
     * @return searcher_records
     */
    protected function _get_searcher_records_instances()
    {
        if (! $this->searcher_records_instances) {
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
                if (isset($searchfield['values'][0]) && $searchfield['values'][0] && $searchfield['type']) {
                    $instance = searcher_factory::get_searcher($searchfield['type'], $searchfield['mode'], stripslashes($searchfield['values'][0]));
                } else if (isset($searchfield['values'][0]) && $searchfield['values'][0] && $searchfield['class']) {
                    $instance = new $searchfield['class'](stripslashes($searchfield['values'][0]));
                }
                if (is_object($instance)) {
                    if (isset($searchfield['fieldrestrict']) && is_array($searchfield['fieldrestrict'])) {
                        $instance->add_fields_restrict($searchfield['fieldrestrict']);
                    }
                    $instance->add_restrict_no_display();
                    if (isset($searchfield['query']) && $searchfield['mode'] == "query") {
                        $instance->set_query($searchfield['query']);
                    }
                    $this->searcher_records_instances[] = $instance;
                }
            }
        }
        return $this->searcher_records_instances;
    }

    /**
     * Valorise les globales nécessaires aux searcher_records pour les filtres
     */
    protected function _set_global_filters()
    {
        foreach ($this->user_query['FILTERFIELDS'] as $filterfield) {
            if (isset($filterfield['values'][0]) && $filterfield['values'][0] && $filterfield['globalvar']) {
                global ${$filterfield['globalvar']};
                if (isset($filterfield['multiple']) && $filterfield['multiple']) {
                    ${$filterfield['globalvar']} = $filterfield['values'];
                } else {
                    ${$filterfield['globalvar']} = $filterfield['values'][0];
                }
            }
        }
    }

    public function get_sorted_result($tri = "default", $start = 0, $number = 20)
    {
        if (count($this->searcher_records_instances) == 1) {
            return $this->searcher_records_instances[0]->get_sorted_result($tri, $start, $number);
        }
        return parent::get_sorted_result($tri, $start, $number);
    }

    public function get_nb_results()
    {
        if (count($this->searcher_records_instances) == 1) {
            return $this->searcher_records_instances[0]->get_nb_results();
        }
        return parent::get_nb_results();
    }
}
?>