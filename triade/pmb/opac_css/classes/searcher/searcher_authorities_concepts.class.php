<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_concepts.class.php,v 1.9 2023/07/27 06:57:38 gneveu Exp $
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
        // TODO gérer un selecteur de schéma dans authorities.xml, pour l'instant on regarde dans tous les schémas
        global $concept_scheme;
        $concept_scheme = - 1;
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
            $this->table_tempo = $this->get_temporary_table_name();
            $pert_result = 'select ' . $this->object_key . ', pert from ' . $pert_result . ' join authorities on ' . $pert_result . '.' . $this->searcher_authorities_skos_concept->object_key . ' = authorities.num_object and type_object = ' . $this->authority_type;
            $rqt = 'create temporary table ' . $this->table_tempo . ' ' . $pert_result;
            pmb_mysql_query($rqt);
            pmb_mysql_query('alter table ' . $this->table_tempo . ' add index i_id(' . $this->object_key . ')');
        }
        return $this->table_tempo;
    }

    protected function _get_pert($query = false, $with_explnum = false)
    {
        return $this->get_pert_result($query);
    }

    protected function get_full_results_query()
    {
        return '
            SELECT DISTINCT id_authority 
            FROM authorities 
            JOIN skos_fields_global_index ON id_item = num_object 
            AND code_champ = 1 
            AND type_object = 10
            ORDER BY value
        ';
    }

    public function get_full_query()
    {
        if ($this->user_query !== '*' && $this->get_result()) {
            $query = $this->_get_pert(true);
        } else if ($this->user_query !== '*' && $this->get_result() === '') {
            return '
            SELECT DISTINCT id_authority
            FROM authorities
            JOIN skos_fields_global_index ON id_item = num_object
            AND code_champ = 1
            AND type_object = 10
            where id_authority = 0';
        } else {
            $query = $this->get_full_results_query();
        }
        return $query;
    }
}