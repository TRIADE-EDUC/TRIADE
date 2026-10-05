<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_concepts.class.php,v 1.11.2.1 2024/10/17 08:22:51 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/searcher_autorities.class.php');

class searcher_authorities_concepts extends searcher_autorities
{

    /**
     *
     * @var searcher_autorities_skos_concepts
     */
    protected $searcher_authorities_skos_concept;

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->authority_type = AUT_TABLE_CONCEPT;
        $this->searcher_authorities_skos_concept = new searcher_autorities_skos_concepts($user_query);
        $this->searcher_authorities_skos_concept->add_fields_restrict($this->field_restrict);
        $this->object_index_key = "id_item";
        $this->object_words_table = "skos_words_global_index";
        $this->object_fields_table = "skos_fields_global_index";
    }

    public function _get_search_type()
    {
        return parent::_get_search_type() . "_concepts";
    }

    public function get_raw_query()
    {
        return 'select ' . $this->object_key . ' from (' . $this->searcher_authorities_skos_concept->get_raw_query() . ') as uni join authorities on uni.' . $this->searcher_authorities_skos_concept->object_key . ' = authorities.num_object and type_object = ' . $this->authority_type;
    }

    public function get_pert_result($query = false)
    {
        $this->table_tempo = '';
        if ($this->searcher_authorities_skos_concept->get_result() && ($this->user_query != '*')) {
            $pert_result = $this->searcher_authorities_skos_concept->get_pert_result($query);
            if ($query) {
                return 'select ' . $this->object_key . ', pert from (' . $pert_result . ') as uni join authorities on uni.' . $this->searcher_authorities_skos_concept->object_key . ' = authorities.num_object and type_object = ' . $this->authority_type;
            }
            $this->table_tempo = 'search_result' . md5(microtime(true));
            $pert_result = 'select authorities.' . $this->object_key . ', pert from ' . $pert_result . ' join authorities on ' . $pert_result . '.' . $this->searcher_authorities_skos_concept->object_key . ' = authorities.num_object and type_object = ' . $this->authority_type;
            $rqt = 'create temporary table ' . $this->table_tempo . ' ' . $pert_result;
            pmb_mysql_query($rqt);
            pmb_mysql_query('alter table ' . $this->table_tempo . ' add index i_id(' . $this->object_key . ')');
        }
        return $this->table_tempo;
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_autorities::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        return $this->get_pert_result($query);
    }

    protected function _sort($start, $number)
    {
        if ($this->table_tempo != "") {
            $query = "select " . $this->table_tempo . "." . $this->object_key;
            $query .= " from " . $this->table_tempo . " join authorities on authorities." . $this->object_key . " = " . $this->table_tempo . "." . $this->object_key;
            $query .= " join " . $this->object_fields_table . " on authorities.num_object = " . $this->object_fields_table . "." . $this->object_index_key;
            $query .= " where code_champ= 1 order by pert desc," . $this->object_fields_table . "." . $this->object_fields_value . " asc limit " . $start . "," . $number;
        } else {
            $query = "select " . $this->searcher_authorities_skos_concept->object_key . " from " . $this->object_fields_table . " where code_champ= 1 and code_ss_champ = 1 and " . $this->object_fields_table . "." . $this->object_index_key . " in (" . $this->get_result() . ") order by " . $this->object_fields_table . "." . $this->object_fields_value . " asc limit " . $start . "," . $number;
        }
        $result = pmb_mysql_query($query) or die(pmb_mysql_error());
        if (pmb_mysql_num_rows($result)) {
            $this->result = array();
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->result[] = $row->{$this->object_key};
            }
        }
    }

    public function get_full_results_query()
    {
        global $concept_scheme;
        if ($this->object_table) {
            return 'select id_authority from authorities join ' . $this->object_table . ' on authorities.num_object = ' . $this->object_table_key;
        }
        $query = 'select id_authority from authorities';

        $filters = $this->_get_authorities_filters();
        $filters[] = 'type_object = ' . AUT_TABLE_CONCEPT;
        $filters[] = 'num_object in (' . $query . ')';
        if (! is_array($concept_scheme)) {
            if (($concept_scheme !== '') && is_string($concept_scheme)) {
                $concept_scheme = explode(',', $concept_scheme);
            } else {
                $concept_scheme = [];
            }
        }
        $query = 'select id_authority  from authorities';
        if (count($concept_scheme) > 0 && $concept_scheme[0] == 0) {
            // On cherche dans les concepts sans schéma
            $query .= ' left join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
            $filters[] = 'authority_num is null';
        } else if (count($concept_scheme) && ($concept_scheme[0] != - 1)) {
            $query .= ' join skos_fields_global_index on authorities.num_object = skos_fields_global_index.id_item and code_champ = 4 ';
            $filters[] = 'authority_num in (' . implode(",", $concept_scheme) . ')';
        }

        if (count($filters)) {
            $query .= ' where ' . implode(' and ', $filters);
        }
        return $query;
    }
}