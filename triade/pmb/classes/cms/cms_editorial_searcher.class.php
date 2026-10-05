<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_searcher.class.php,v 1.5.4.1 2025/03/17 08:15:42 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/searcher/searcher_generic.class.php");

class cms_editorial_searcher extends searcher_generic
{

    public $type_obj;
    public $object_table;
    public $notices_ids;

    // type éditorial (rubrique/article)
    public function __construct($user_query, $type_obj = "article")
    {
        parent::__construct($user_query);
        $this->type_obj = $type_obj;
        $this->field_restrict[] = array(
            'field' => 'type',
            'values' => $this->type_obj,
            'op' => "and",
            'not' => false
        );
        $this->object_table = 'cms_' . $this->type_obj . 's';
        $this->object_key = 'id_' . $this->type_obj;
        $this->object_index_key = "num_obj";
        $this->object_words_table = "cms_editorial_words_global_index";
        $this->object_fields_table = "cms_editorial_fields_global_index";
    }

    protected function _get_search_type()
    {
        return "editorial_all_fields";
    }

    protected function _get_notices_ids()
    {
        if (! $this->searched) {
            $this->notices_ids = "";
            $query = $this->_get_search_query();
            if ($query) {
                $res = pmb_mysql_query($query);
                if ($res && pmb_mysql_num_rows($res)) {
                    while ($row = pmb_mysql_fetch_object($res)) {
                        if ($this->notices_ids != "") {
                            $this->notices_ids .= ",";
                        }
                        $this->notices_ids .= $row->num_obj;
                    }
                    pmb_mysql_free_result($res);
                }
            }
            $this->searched = true;
        }
        return $this->notices_ids;
    }

    protected function _get_search_query()
    {
        $this->_calc_query_env();
        if ($this->user_query !== "*") {
            $query = $this->aq->get_query_mot($this->object_index_key, $this->object_words_table, $this->object_words_value, $this->object_fields_table, $this->object_fields_value, $this->field_restrict);
        } else {
            $query = " select " . $this->object_key . " from cms_" . $this->object_table;
        }
        return $query;
    }

    public function get_result()
    {
        $this->_get_notices_ids();
        return $this->notices_ids;
    }

    public function get_sorted_result($sort = "pert", $sort_order = "desc", $limit = 20)
    {
        $this->get_result();

        $query = $this->_get_pert(true);

        if ($sort != 'pert') {
            $query = "select uni.*,$sort from (" . $query . ") as uni join cms_" . $this->type_obj . "s on id_" . $this->type_obj . " = num_obj ";
        }
        if (! $query) {
            $this->result = array();
            return $this->result;
        }
        $query .= " order by " . $sort . " " . $sort_order;
        if ($limit > 0) {
            $query .= " limit " . $limit;
        }
        $result = pmb_mysql_query($query);
        $this->result = array();
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->result[] = $row->num_obj;
            }
        }
        return $this->result;
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_generic::_get_pert()
     */
    public function _get_pert($query = false, $with_explnum = false)
    {
        global $opac_allow_term_troncat_search;
        global $opac_exclude_fields, $restrict, $all_fields;

        $empty_word = get_empty_words();
        $query_pert_explnum = "";
        $troncat = "";
        if ($opac_allow_term_troncat_search) {
            $troncat = "%";
        }
        $terms = $this->aq->get_positive_terms_obj($this->aq->tree);
        $words = array();
        $literals = array();
        $queries = array();
        $return_query = '';
        if ($this->notices_ids) {
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    if (! $term->literal) {
                        if (! in_array($term, $words))
                            $words[] = $term;
                    } else
                        $literals[] = $term;
                }
            }
            if ($this->aq->input !== "*") {
                $query_pert_explnum = "";
                if (count($words)) {
                    $pert_query_words = "";
                    $pert_query_words .= "select num_obj, sum(!!pert!!) as pert from cms_editorial_words_global_index join words on num_word = id_word where type='" . $this->type_obj . "' and ";
                    $where = "";
                    foreach ($words as $term) {
                        if ($where != "")
                            $where .= " or ";
                        $crit = "word ";
                        if (strpos($term->word, "*") !== false || $opac_allow_term_troncat_search) {
                            if (strpos($term->word, "*") === false) {
                                // Si c'est un mot vide, on ne troncature pas
                                if (in_array($term->word, $empty_word) === false) {
                                    if ($term->not)
                                        $crit .= "not ";
                                    $crit .= "like '" . addslashes($term->word . $troncat) . "'";
                                } else {
                                    if ($term->not)
                                        $crit .= "! ";
                                    $crit .= "= '" . addslashes($term->word) . "'";
                                }
                            } else {
                                if ($term->not)
                                    $crit .= "not ";
                                $crit .= "like '" . addslashes(str_replace("*", "%", $term->word)) . "'";
                            }
                        } else {
                            if ($term->not)
                                $crit .= "!";
                            $crit .= "= '" . addslashes($term->word) . "'";
                        }
                        $where .= " " . $crit;
                        $pert_query_words = str_replace("!!pert!!", "((" . $crit . ") * pond *" . $term->pound . ")+!!pert!!", $pert_query_words);
                    }
                    $where .= ((is_array($restrict) && (count($restrict) > 0)) ? " and " . $this->aq->get_field_restrict($restrict, $neg_restrict) : "");
                    $pert_query_words = str_replace("!!pert!!", 0, $pert_query_words);
                    if ($all_fields && $opac_exclude_fields != "") {
                        $where .= " and code_champ not in (" . $opac_exclude_fields . ")";
                    }
                    $queries[] = $pert_query_words . $where . " group by num_obj ";
                }
                if (count($literals)) {
                    $pert_query_literals = "select distinct num_obj, sum(!!pert!!) as pert from cms_editorial_fields_global_index where type='" . $this->type_obj . "' and ";
                    $where = "";
                    foreach ($literals as $term) {
                        // on n'ajoute pas une clause dans le where qui parcours toute la base...
                        if ($where != "")
                            $where .= " or ";
                        $crit = "value ";
                        if ($term->not)
                            $crit .= "not ";
                        $crit .= "like '" . ($term->start_with == 0 ? "%" : "") . addslashes(str_replace("*", "%", $term->word)) . "%'";
                        $where .= " " . $crit;
                        $crit = str_replace("%%", "%", $crit);
                        $pert_query_literals = str_replace("!!pert!!", "((" . $crit . ") * pond *" . $term->pound . ")+!!pert!!", $pert_query_literals);
                    }
                    $where .= ((is_array($restrict) && (count($restrict) > 0)) ? " and " . $this->aq->get_field_restrict($restrict, $neg_restrict) : "");
                    $pert_query_literals = str_replace("!!pert!!", 0, $pert_query_literals);
                    if ($all_fields && $opac_exclude_fields != "") {
                        $where .= " and code_champ not in (" . $opac_exclude_fields . ")";
                    }
                    $queries[] = $pert_query_literals . $where . " group by num_obj ";
                }
                $return_query = "select distinct num_obj, sum(pert) as pert from ((" . implode(") union all (", $queries) . ")) as uni where num_obj in (" . $this->notices_ids . ") group by num_obj";
            } else {
                $return_query = "select id_" . $this->type_obj . " as num_obj, 100 as pert from cms_" . $this->type_obj . "s where id_" . $this->type_obj . " in (" . $this->notices_ids . ")";
            }
        }

        if ($query) {
            return $return_query;
        } else {
            $table = $this->get_temporary_table_name();
            $rqt = "create temporary table " . $table . " $return_query";
            $res = pmb_mysql_query($rqt) or die(pmb_mysql_error());
            pmb_mysql_query("alter table " . $table . " add index i_id(num_obj)");
            return $table;
        }
    }
}