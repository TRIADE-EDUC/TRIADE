<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_autorities.class.php,v 1.37.2.2.2.1 2025/05/05 12:55:35 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/searcher_generic.class.php');

// un jour ca sera utile
class searcher_autorities extends searcher_generic
{

    protected $authority_type = 0;

    protected $object_table = '';

    protected $object_table_key;

    /**
     * Tableau des instances de searcher_authorities
     *
     * @var searcher_autorities
     */
    protected $searcher_authorities_instances;

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

    /**
     * Jointure externes SQL pour les besoins des filtres
     */
    protected function _get_query_join_filters()
    {
        return '';
    }

    protected function _get_authorities_filters()
    {
        global $authority_statut, $no_display, $module_from;

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

        if (is_string($module_from) && (strpos($module_from, 'catalog') !== false)) {
			$field = 'authorities_statuts_searcher_autority';
			if (strpos($module_from, 'autocomplete') !== false) {
				$field = 'authorities_statuts_autocomplete';
			}

            $filters[] = "authorities.num_statut NOT IN (
                SELECT id_authorities_statut FROM authorities_statuts WHERE {$field} = 0
            )";
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
            $query .= $this->_get_query_join_filters();
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
            if ($query) {
                $filters[] = 'id_authority in (' . $query . ')';
            }
            if (count($filters)) {
                $query = 'select id_authority from authorities ' . $this->_get_query_join_filters() . ' where ' . implode(' and ', $filters);
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

    protected function _get_in_cache($sorted = false)
    {
        global $last_param;

        // non utilisation du cache pour un affichage des derniers elements
        if (! empty($last_param)) {
            return false;
        }
        return parent::_get_in_cache($sorted);
    }

    // à réécrire au besoin...
    protected function _sort($start, $number)
    {
        global $last_param, $tri_param, $limit_param;
        global $pmb_nb_max_tri;

        $sort_index = "";
        $entity_type = $this->get_sort_entity_type();
        if (! empty($entity_type)) {
            $sort_index = "tri_" . $entity_type;
        }
        $apply_sort = false;
        if (! empty($sort_index) && ! empty($_SESSION[$sort_index])) {
            $authority_sort = $_SESSION[$sort_index];
        }
        // On vérifie si on peut appliquer le tri selon le paramètre $pmb_nb_max_tri
        $query = "SELECT COUNT(id_authority) as nb_results FROM authorities WHERE id_authority in (" . $this->_get_search_query() . ")";
        if ($this->table_tempo != "") {
            $query = "SELECT count(id_authority) as nb_results FROM " . $this->table_tempo;
        }
        $res = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($res)) {
            $row = pmb_mysql_fetch_object($res);
        }
        if ($row->nb_results <= $pmb_nb_max_tri) {
            $apply_sort = true;

            if (!empty($entity_type)) {
                $sort = new sort($entity_type, 'base');
            }
        }

        if ($this->table_tempo != "") {
            if ($apply_sort) {
                $query = $sort->appliquer_tri($authority_sort, "SELECT * FROM " . $this->table_tempo, 'id_authority', $start, $number);
            } else {
                $authority_tri = $this->get_authority_tri();
                $join = '';
                if ($this->authority_type && $this->object_table && $authority_tri) {
                    $join = ' join authorities on ' . $this->table_tempo . '.' . $this->object_key . ' = authorities.id_authority
    						join ' . $this->object_table . ' on authorities.num_object = ' . $this->object_table_key . ' and authorities.type_object = ' . $this->authority_type;
                }
                $query = 'select * from ' . $this->table_tempo . $join . ' order by pert desc' . ($authority_tri ? ', ' . $authority_tri : '') . ', ' . $this->table_tempo . '.' . $this->object_key . ' asc limit ' . $start . ',' . $number;
            }
        } else {
            if ($apply_sort) {
                $query = $sort->appliquer_tri($authority_sort, $this->_get_search_query(), 'id_authority', $start, $number);
            } else {
                if ($last_param) {
                    $query = $this->_get_search_query() . ' ' . $tri_param . ' ' . $limit_param;
                } else {
                    $authority_tri = $this->get_authority_tri();
                    $query = $this->_get_search_query() . ($authority_tri ? ' order by ' . $authority_tri : '') . ' limit ' . $start . ', ' . $number;
                }
            }
        }
        if (! empty($query)) {
            $res = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($res)) {
                $this->result = array();
                while ($row = pmb_mysql_fetch_object($res)) {
                    $this->result[] = $row->id_authority;
                }
            }
        }
    }

    public function get_authority_tri()
    {
        // à surcharger si besoin
        return '';
    }

    protected function _sort_result($start, $number)
    {
        if ($this->user_query != '*') {
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
        if ($this->get_result()) {
            $pert = $this->_get_pert($query);
        } else {
            $this->table_tempo = $this->get_temporary_table_name();
            $pert = $this->get_full_query();
            pmb_mysql_query("create temporary table {$this->table_tempo} {$pert}");
        }
        if ($query) {
            return $pert;
        }
        return $this->table_tempo;
    }

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
        if (! $return_query){
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

        return "<br />
        <br />
        <div class='row'>
            <h3>" . $this->get_nb_results() . " " . $label . " " . htmlentities($user_input, ENT_QUOTES, $charset) . "</h3>
            " . entities_authorities_controller::get_caddie_link() . entities_authorities_controller::get_sort_link($this->get_nb_results(), $this->get_sort_entity_type()) . "
		</div>
        <script type='text/javascript' src='./javascript/sorttable.js'></script>
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

    protected function _get_no_display()
    {
        global $no_display;

        if ($no_display) {
            $authority = new authority(0, $no_display, $this->authority_type);
            return $authority->get_id();
        }
        return 0;
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

    public function get_authority_ids_from_result($search_result = [])
    {
        $result = [];
        if (empty($search_result) || ! is_array($search_result)) {
            return $result;
        }
        $rqt = "select id_authority from authorities join " . $this->object_fields_table . " on num_object=" . $this->object_index_key;
        $rqt .= " where code_champ=1 and type_object=" . AUT_TABLE_CONCEPT . " and " . $this->object_index_key . " in (" . implode(',', $search_result) . ") ";
        echo $rqt . "<br/>";
        $res = pmb_mysql_query($rqt);
        echo $rqt;
        if (pmb_mysql_num_rows($res)) {
            while ($row = pmb_mysql_fetch_row($res)) {
                $result[] = $row[0];
            }
        }
        return $result;
    }

    protected function get_sort_entity_type($authority_type = 0)
    {
        if ($authority_type === 0) {
            $authority_type = $this->get_authority_type();
        }
        switch ($authority_type) {
            case AUT_TABLE_CATEG:
                return "categories";
            case AUT_TABLE_AUTHPERSO:
                return "authperso";
            case AUT_TABLE_CONCEPT:
                return "concepts";
            case AUT_TABLE_COLLECTIONS:
                return "collections";
            case AUT_TABLE_TITRES_UNIFORMES:
                return "titres_uniformes";
            case AUT_TABLE_SUB_COLLECTIONS:
                return "subcollections";
            case AUT_TABLE_SERIES:
                return "series";
            case AUT_TABLE_PUBLISHERS:
                return "publishers";
            case AUT_TABLE_INDEXINT:
                return "indexint";
            case AUT_TABLE_AUTHORS:
                return "authors";
            default:
                // Si on n'a rien on regarde dans searcher_authorities_instances
                if (! empty($this->searcher_authorities_instances) && count($this->searcher_authorities_instances) == 1) {
                    if (! empty($this->searcher_authorities_instances[0])) {
                        $authority_type = $this->searcher_authorities_instances[0]->get_authority_type();
                        if ($authority_type != 0) {
                            return $this->get_sort_entity_type($authority_type);
                        }
                    }
                }
                return "mixed";
        }
    }
}