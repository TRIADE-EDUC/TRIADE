<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_autorities.class.php,v 1.19.2.2 2024/10/17 08:22:50 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/opac_searcher_generic.class.php');

// un jour ca sera utile
class searcher_autorities extends opac_searcher_generic
{

    protected $authority_type = 0;

    public $object_table = '';

    public $object_table_key;

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->object_index_key = "id_authority";
        $this->object_words_table = "authorities_words_global_index";
        $this->object_fields_table = "authorities_fields_global_index";
        $this->object_key = 'id_authority';
        if ($this->authority_type) {
            $this->field_restrict[] = array(
                'field' => "type",
                'values' => array(
                    $this->authority_type
                ),
                'op' => "and",
                'not' => false
            );
        }
    }

    public function _get_search_type()
    {
        return "authorites";
    }

    protected function get_full_results_query()
    {
        if ($this->object_table) {
            return 'select id_authority from authorities join ' . $this->object_table . ' on authorities.num_object = ' . $this->object_table_key;
        }
        return 'select id_authority from authorities';
    }

    protected function _get_authorities_filters()
    {
        global $authority_statut, $no_display;
        $filters = array();
        if ($this->authority_type) {
            $filters[] = 'authorities.type_object = ' . $this->authority_type;
        }
        if ($authority_statut) {
            $filters[] = 'authorities.num_statut = "' . $authority_statut . '"';
        }
        if ($no_display) {
            $filters[] = 'authorities.num_object != "' . $no_display . '"';
        }
        return $filters;
    }

    protected function _get_search_query()
    {
        $query = parent::_get_search_query();

        if ($this->authority_type && $this->object_table) {
            $filters = $this->_get_authorities_filters();
            $filters[] = $this->object_key . ' in (' . $query . ')';
            if ($this->user_query !== "*") {
                $query = 'select id_authority from authorities join ' . $this->object_table . ' on authorities.num_object = ' . $this->object_table_key;
            }
            if (count($filters)) {
                $query .= ' where ' . implode(' and ', $filters);
            }
        } else if (get_class($this) == self::class) {

            if ($this->user_query !== "*") {
                // Si cette classe est appelée directement, on cherche dans toutes les autorités donc on va chercher les concepts
                $searcher_authorities_concepts = new searcher_authorities_concepts($this->user_query);
                $query = 'select id_authority from ((' . $query . ') union (' . $searcher_authorities_concepts->get_raw_query() . ')) as search_query_concepts';
            }

            $filters = $this->_get_authorities_filters();
            $filters[] = 'id_authority in (' . $query . ')';
            if (count($filters)) {
                $query = 'select id_authority from authorities where ' . implode(' and ', $filters);
            }
        }
        return $query;
    }

    protected function _get_sign_elements($sorted = false)
    {
        global $authority_statut;
        $str_to_hash = parent::_get_sign_elements($sorted);
        $str_to_hash .= "&authority_statut=" . $authority_statut;
        return $str_to_hash;
    }

    // à réécrire au besoin...
    protected function _sort($start, $number)
    {
        global $last_param, $tri_param, $limit_param;

        $order = "";
        $limit = ' LIMIT ' . $start . ',' . $number;
        if ($this->table_tempo != "") {
            $authority_tri = $this->get_authority_tri();
            $join = '';
            if ($this->authority_type && $this->object_table && $authority_tri) {
                $join = ' join authorities on ' . $this->table_tempo . '.' . $this->object_key . ' = authorities.id_authority
						join ' . $this->object_table . ' on authorities.num_object = ' . $this->object_table_key . ' and authorities.type_object = ' . $this->authority_type;
            }
            $query = 'select * from ' . $this->table_tempo . $join;
            $order = ' order by pert desc' . ($authority_tri ? ', ' . $authority_tri : '') . ', ' . $this->table_tempo . '.' . $this->object_key . ' asc';
        } else {
            if (! empty($last_param)) {
                $query = $this->_get_search_query();
                $order = ' ' . $tri_param;
                $limit = ' ' . $limit_param;
            } else {
                $query = $this->_get_search_query() . $this->get_authority_join();
                $authority_tri = $this->get_authority_tri();
                $order = ($authority_tri ? ' ORDER BY ' . $authority_tri : '');
            }
        }
        if ($this->filtered_result) {
            $objects_ids = $this->objects_ids;
            // pour eviter les erreurs de requetes sql
            if (empty($this->objects_ids)) {
                $objects_ids = 0;
            }
            if (empty($join)) {
                $query .= " WHERE id_authority IN ($objects_ids)";
            } else {
                $query .= " AND authorities.id_authority IN ($objects_ids)";
            }
        }
        $res = pmb_mysql_query($query . $order . $limit);
        if (pmb_mysql_num_rows($res)) {
            $this->result = array();
            while ($row = pmb_mysql_fetch_object($res)) {
                $this->result[] = $row->id_authority;
            }
        }
    }

    public function get_authority_tri()
    {
        // à surcharger si besoin
        if (! empty($this->table_tempo)) {
            return ' ' . $this->table_tempo . '.id_authority desc ';
        }
        return ' id_authority desc ';
    }

    protected function _sort_result($start, $number)
    {
        if ($this->user_query != '*' && $this->user_query !== '') {
            $this->_get_pert();
        }
        $this->_sort($start, $number);
    }

    public function get_raw_query()
    {
        $this->_analyse();
        return $this->_get_search_query();
    }

    public function get_pert_result($query = false)
    {
        $pert = '';
        if ($this->get_result() && ($this->user_query != '*')) {
            $pert = $this->_get_pert($query);
        }
        if ($query) {
            return $pert;
        }
        return $this->table_tempo;
    }

    /**
     *
     * {@inheritdoc}
     * @see opac_searcher_generic::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        $return_query = parent::_get_pert(true);
        if (get_class($this) == self::class) {
            // Si cette classe est appelée directement, on cherche dans toutes les autorités donc on va chercher les concepts
            $searcher_authorities_concepts = new searcher_authorities_concepts($this->user_query);
            $concepts_pert_result = $searcher_authorities_concepts->get_pert_result(true);
            if ($concepts_pert_result) {
                $return_query = 'select ' . $this->object_key . ', sum(pert) as pert from ((' . $return_query . ') union all (' . $concepts_pert_result . ')) as search_query_concepts group by ' . $this->object_key;
            }
        }
        if ($query) {
            return $return_query;
        }
        if (! $return_query) {
            return; // Pas de résultat en recherche
        }

        $this->table_tempo = 'search_result' . md5(microtime(true));
        $rqt = 'create temporary table ' . $this->table_tempo . ' ' . $return_query;
        pmb_mysql_query($rqt);
        pmb_mysql_query('alter table ' . $this->table_tempo . ' add index i_id(' . $this->object_key . ')');
    }

    public function get_results_list_from_search($label, $user_input, $list, $navbar)
    {
        global $charset;

        return "
			<br />
			<br />
			<div class='row'>
				<h3>" . $this->get_nb_results() . " " . $label . " " . htmlentities($user_input, ENT_QUOTES, $charset) . "</h3>
                " . entities_authorities_controller::get_caddie_link() . "
			</div>
			<script src='./javascript/sorttable.js'></script>
			<table class='sortable'>
				" . $list . "
			</table>
			<div class='row'>
				" . $navbar . "
			</div>";
    }

    public static function has_authorities_sources($authority_type)
    {
        $authorities_sources = false;
        $query = "SELECT id_authority_source FROM authorities_sources WHERE authority_type='" . $authority_type . "' AND TRIM(authority_number) !='' LIMIT 1";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            $authorities_sources = true;
        }
        return $authorities_sources;
    }

    public static function get_display_authorities_sources($num_authority, $authority_type)
    {
        global $charset;

        $display = '';
        $query = "SELECT authority_number,origin_authorities_name, origin_authorities_country FROM authorities_sources JOIN origin_authorities ON num_origin_authority=id_origin_authorities WHERE authority_type='" . $authority_type . "' AND num_authority='" . $num_authority . "' AND TRIM(authority_number) !='' GROUP BY authority_number,origin_authorities_name,origin_authorities_country ORDER BY authority_favorite DESC, origin_authorities_name";
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            $first = true;
            while ($row = pmb_mysql_fetch_object($result)) {
                if (! $first)
                    $display .= ", ";
                $display .= htmlentities($row->authority_number, ENT_QUOTES, $charset);
                if (trim($row->origin_authorities_name)) {
                    $display .= htmlentities(" (" . $row->origin_authorities_name . ")", ENT_QUOTES, $charset);
                }
                $first = false;
            }
        }
        return $display;
    }

    /**
     * Ajoute des restriction au tableau $field_restrict
     *
     * @param array $fields_restrict
     *            Tableau des restrictions à ajouter
     */
    public function add_fields_restrict($fields_restrict = array())
    {
        if (count($fields_restrict)) {
            $tab = array();
            $tab[] = array(
                'op' => "and",
                'sub' => $fields_restrict
            );
            $this->field_restrict = array_merge($this->field_restrict, $tab);
        }
    }

    public function get_object_key()
    {
        return $this->object_key;
    }

    public function get_object_table()
    {
        return $this->object_table;
    }

    public function get_object_table_key()
    {
        return $this->object_table_key;
    }

    public function get_authority_type()
    {
        return $this->authority_type;
    }

    public function get_human_query()
    {
        global $msg, $charset;

        $human_query = '';
        $human_queries = $this->_get_human_queries();
        if (count($human_queries)) {
            foreach ($human_queries as $element) {
                if ($human_query) {
                    $human_query .= ', ';
                }
                $human_query .= '<b>' . $element['name'] . '</b> ' . htmlentities($element['value'], ENT_QUOTES, $charset);
            }
        }
        $nb_results = $this->get_nb_results();
        if ($nb_results) {
            $human_query .= " => " . sprintf($msg["searcher_results"], $nb_results);
        } else {
            $human_query .= " => " . sprintf($msg['1915'], $nb_results);
        }
        return "<div class='othersearchinfo'>" . $human_query . "</div>";
    }

    protected function _get_human_queries()
    {
        global $authority_statut, $msg;

        $human_queries = array();
        if ($this->user_query) {
            $human_queries[] = array(
                'name' => $msg['global_search'],
                'value' => $this->user_query
            );
        }
        if ($authority_statut) {
            $authority_statut_label = pmb_mysql_result(pmb_mysql_query('select authorities_statut_label from authorities_statuts where id_authorities_statut = ' . $authority_statut), 0, 0);
            $human_queries[] = array(
                'name' => $msg['authorities_statut_label'],
                'value' => $authority_statut_label
            );
        }

        return $human_queries;
    }

    protected function get_authority_join()
    {
        return "";
    }
}