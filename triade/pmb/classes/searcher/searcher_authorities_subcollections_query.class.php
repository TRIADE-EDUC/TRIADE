<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_subcollections_query.class.php,v 1.6 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . '/searcher/searcher_autorities.class.php');

class searcher_authorities_subcollections_query extends searcher_authorities_subcollections
{

    protected $query = "";

    protected $filters = [];

    public function __construct($user_query)
    {
        parent::__construct($user_query);
    }

    protected function _analyse()
    {}

    public function _get_search_type()
    {
        return parent::_get_search_type() . "_query";
    }

    protected function _get_user_query()
    {
        if (is_string($this->user_query)) {
            $user_query = $this->user_query;
        } else if (! empty($this->user_query['id'][0])) {
            $user_query = $this->user_query['id'][0];
        } else {
            $user_query = $this->user_query[0];
        }
        return $user_query;
    }

    protected function _get_sign_elements($sorted = false)
    {
        $str_to_hash = parent::_get_sign_elements($sorted);
        $str_to_hash .= "&query=$this->query";
        return $str_to_hash;
    }

    public function set_query($query)
    {
        $this->query = $query;
    }

    public function set_filter($filters)
    {
        $this->filters = $filters;
    }

    protected function _get_search_query()
    {
        $user_query = $this->_get_user_query();
        if ($user_query !== "*") {
            $query = str_replace('!!p!!', pmb_mysql_escape_string($user_query), $this->query);
            foreach ($this->filters as $filterid => $restrict) {
                $filter_field_name = "filter_field_tab_$filterid";
                global ${$filter_field_name};
                if (! empty(${$filter_field_name})) {
                    if (is_array(${$filter_field_name})) {
                        if (${$filter_field_name}[0] != "") {
                            $query .= str_replace('!!filter!!', '"' . implode('","', ${$filter_field_name}) . '"', $restrict);
                        }
                    } else {
                        $query .= str_replace('!!filter!!', '"' . pmb_mysql_escape_string(${$filter_field_name}) . '"', $restrict);
                    }
                }
            }
            return $query;
        }
        return "select 0 as $this->object_table_key";
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_autorities::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        $this->table_tempo = $this->get_temporary_table_name("_pert");
        pmb_mysql_query("create temporary table $this->table_tempo (" . $this->object_key . " int(11) not null primary key, pert int(11) not null default 0)");
        if ($this->objects_ids != "") {
            pmb_mysql_query("insert into $this->table_tempo (" . $this->object_key . ") values (" . implode("),(", explode(",", $this->objects_ids)) . ")");
            print pmb_mysql_error();
        }
        if ($query) {
            return "select * from $this->table_tempo";
        }
        return $this->table_tempo;
    }
}