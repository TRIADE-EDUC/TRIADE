<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records.class.php,v 1.26 2023/07/27 06:57:37 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ("$class_path/searcher/searcher_generic.class.php");

class searcher_records extends searcher_generic
{

    public $typdocs;

    // tableau des typdoc
    public $nb_explnum = 0;

    // nombre de documents numériques associés à la recherche...
    public $explnums = array();

    // tableau contenant les documents numériques associés à la recherche
    public $object_table = "";

    public function __construct($user_query)
    {
        parent::__construct($user_query);
        $this->object_table = "notices";
        $this->object_key = "notice_id";
        $this->object_index_key = "id_notice";
        $this->object_words_table = "notices_mots_global_index";
        $this->object_fields_table = "notices_fields_global_index";
    }

    protected function _get_search_type()
    {
        return "records";
    }

    protected function get_full_results_query()
    {
        return 'select notice_id as id_notice from notices ' . $this->_get_typdoc_filter(true);
    }

    protected static function _get_typdoc_filter($on_notice = false)
    {
        global $pmb_show_notice_id, $f_notice_id;
        global $typdoc_query;
        global $statut_query;
        global $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;

        $return = "";
        if ($date_parution_start_query) {
            $date_parution_start = detectFormatDate($date_parution_start_query);
        } else {
            $date_parution_start = '';
        }
        if ($date_parution_end_query) {
            $date_parution_end = detectFormatDate($date_parution_end_query);
        } else {
            $date_parution_end = '';
        }
        if ($on_notice) {
            if ($pmb_show_notice_id && $f_notice_id) {
                $return = " where notice_id = '" . $f_notice_id . "' ";
            } else {
                if (! empty($typdoc_query) && ! empty($statut_query) && ! empty($typdoc_query[0]) && ! empty($statut_query[0])) {
                    $return = " where typdoc in ('" . implode("','", $typdoc_query) . "') and statut in ('" . implode("','", $statut_query) . "') ";
                } else if (! empty($typdoc_query) && ! empty($typdoc_query[0])) {
                    $return = " where typdoc in ('" . implode("','", $typdoc_query) . "') ";
                } else if (! empty($statut_query) && ! empty($statut_query[0])) {
                    $return = " where statut in ('" . implode("','", $statut_query) . "') ";
                }
            }
            if ($return) {
                $where_and = ' and ';
            } else {
                $where_and = ' where ';
            }
            if ($date_parution_start && $date_parution_end) {
                $return .= $where_and . " date_parution >= '" . $date_parution_start . "' and date_parution <= '" . $date_parution_end . "' ";
            } else if ($date_parution_start && $date_parution_exact_query) {
                $return .= $where_and . " date_parution = '" . $date_parution_start . "' ";
            } else if ($date_parution_start) {
                $return .= $where_and . " date_parution >= '" . $date_parution_start . "' ";
            } else if ($date_parution_end) {
                $return .= $where_and . " date_parution <= '" . $date_parution_end . "' ";
            }
        } else {
            if (! empty($typdoc_query) && ! empty($statut_query) && ! empty($typdoc_query[0]) && ! empty($statut_query[0])) {
                $return = " join notices on id_notice = notice_id and typdoc in ('" . implode("','", $typdoc_query) . "') and statut in ('" . implode("','", $statut_query) . "') ";
            } else if (! empty($typdoc_query) && ! empty($typdoc_query[0])) {
                $return = " join notices on id_notice = notice_id and typdoc in ('" . implode("','", $typdoc_query) . "') ";
            } else if (! empty($statut_query) && ! empty($statut_query[0])) {
                $return = " join notices on id_notice = notice_id and statut in ('" . implode("','", $statut_query) . "') ";
            }
            if ($date_parution_start && $date_parution_end) {
                $return .= " join notices as notices_date_parution on id_notice = notices_date_parution.notice_id and notices_date_parution.date_parution >= '" . $date_parution_start . "' and date_parution <= '" . $date_parution_end . "' ";
            } else if ($date_parution_start && $date_parution_exact_query) {
                $return .= " join notices as notices_date_parution on id_notice = notices_date_parution.notice_id and notices_date_parution.date_parution = '" . $date_parution_start . "' ";
            } else if ($date_parution_start) {
                $return .= " join notices as notices_date_parution on id_notice = notices_date_parution.notice_id and notices_date_parution.date_parution >= '" . $date_parution_start . "' ";
            } else if ($date_parution_end) {
                $return .= " join notices as notices_date_parution on id_notice = notices_date_parution.notice_id and notices_date_parution.date_parution <= '" . $date_parution_end . "' ";
            }
        }
        return $return;
    }

    protected function _get_search_query()
    {
        $query = parent::_get_search_query();
        if ($this->user_query !== "*") {
            $query .= self::_get_typdoc_filter();
        }
        return $query;
    }

    protected function _get_pert($query = false, $with_explnum = false)
    {
        if ($query) {
            return $this->aq->get_pert($this->objects_ids, $this->field_restrict, false, $with_explnum, $query);
        } else {
            $this->table_tempo = $this->aq->get_pert($this->objects_ids, $this->field_restrict, false, $with_explnum, $query);
        }
    }

    protected function _get_sign($sorted = false)
    {
        global $typdoc_query, $statut_query;
        global $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;

        $sign = parent::_get_sign($sorted);
        $sign .= md5((! empty($typdoc_query) ? '&typdoc=' . implode(",", $typdoc_query) : '') . (! empty($statut_query) ? '&statut=' . implode(",", $statut_query) : '') . '&date_parution_start_query=' . $date_parution_start_query . '&date_parution_end_query=' . $date_parution_end_query . '&date_parution_exact_query=' . $date_parution_exact_query);
        return $sign;
    }

    public function get_typdocs()
    {
        if (! $this->typdocs) {
            if (! $this->objects_ids) {
                $this->get_result();
            }
            $this->typdocs = array();
            if ($this->objects_ids != "") {
                $query = "select distinct typdoc from notices where " . $this->object_key . " in (" . $this->objects_ids . ")";
                $res = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($res)) {
                    while ($row = pmb_mysql_fetch_object($res)) {
                        $this->typdocs[] = $row->typdoc;
                    }
                }
            }
        }
        return $this->typdocs;
    }

    protected function _filter_results()
    {
        $this->_get_objects_ids();

        if ($this->objects_ids != '') {
            $fr = new filter_results($this->objects_ids);
            $this->objects_ids = $fr->get_results();
        }
    }

    public function get_nb_explnums()
    {
        if (! $this->objects_ids) {
            $this->get_result();
        }
        $this->nb_explnum = 0;
        if ($this->objects_ids != "") {
            $query_noti = "select explnum_id from explnum where explnum_notice in (" . $this->objects_ids . ")";
            $query_issue = "select explnum_id from explnum join bulletins on explnum_bulletin!= 0 and explnum_bulletin = bulletin_id join notices on notice_id = num_notice and num_notice!=0 where notice_id in (" . $this->objects_ids . ")";
            $query = "select explnum_id from(" . $query_noti . " union " . $query_issue . ") as uni";
            $res = pmb_mysql_query($query);
            $this->nb_explnum = pmb_mysql_num_rows($res);
        }
        return $this->nb_explnum;
    }

    public function get_explnums($tri)
    {
        $this->explnums = array();
        $this->get_result();
        // $table = $this->_get_pert();
        $this->_get_pert();
        // liste complete des résultats..;
        if ($this->objects_ids != "") {
            $sort = new sort("notices", "session");
            $query = $sort->appliquer_tri_from_tmp_table($tri, $this->table_tempo, "notice_id", 0, 0);

            $explnum_noti = "select explnum_id," . $sort->table_tri_tempo . ".* from explnum join " . $sort->table_tri_tempo . " on explnum_notice!=0 and explnum_notice = " . $sort->table_tri_tempo . ".notice_id ";
            $rqt = "create temporary table explnum_list $explnum_noti";
            pmb_mysql_query($rqt);
            $explnum_issue = "select explnum_id," . $sort->table_tri_tempo . ".* from explnum join bulletins on explnum_bulletin!=0 and bulletin_id = explnum_bulletin join " . $sort->table_tri_tempo . " on num_notice != 0 and num_notice = " . $sort->table_tri_tempo . ".notice_id ";
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

    protected function _sort($start, $number)
    {
        if ($this->table_tempo != "") {
            $sort = new sort("notices", "session");
            $query = $sort->appliquer_tri_from_tmp_table($this->tri, $this->table_tempo, $this->object_key, $start, $number);
            $res = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($res)) {
                $this->result = array();
                while ($row = pmb_mysql_fetch_object($res)) {
                    $this->result[] = $row->{$this->object_key};
                }
            }
        } else {
            $this->result = array_slice(explode(',', $this->objects_ids), $start, $number);
        }
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

    public function get_full_query()
    {
        if ($this->user_query === "*") {
            return 'select notice_id as ' . $this->object_key . ', 100 as pert from notices ' . $this->_get_typdoc_filter(true);
        }
        if ($this->get_nb_results()) {
            $query = $this->_get_pert(true);
        } else {
            $query = "select " . $this->object_key . ", 100 as pert from notices where " . $this->object_key . " = 0";
        }
        return $query;
    }

    public static function get_full_query_from_authority($id)
    {
        $query = "select distinct notice_id, 100 as pert from notices ";
        if ($restrict = self::_get_typdoc_filter(true)) {
            $query .= $restrict . " and ";
        } else {
            $query .= "where ";
        }
        return $query;
    }

    public static function get_caddie_link()
    {
        global $msg;
        print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=" . $_SESSION['CURRENT'] . "&action=print_prepare','print_cart'); return false;\"><img src='" . get_url_icon('basket_small_20x20.gif') . "' style='border:0px' class='center' alt=\"" . $msg["histo_add_to_cart"] . "\" title=\"" . $msg["histo_add_to_cart"] . "\"></a>&nbsp;";
    }
}