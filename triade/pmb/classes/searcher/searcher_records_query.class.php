<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_query.class.php,v 1.6 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ("$class_path/searcher/searcher_generic.class.php");

class searcher_records_query extends searcher_records
{

    protected $query = "";

    public function __construct($user_query)
    {
        parent::__construct($user_query);
    }

    public function set_query($query)
    {
        $this->query = $query;
    }

    protected function _analyse()
    {}

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

    protected function _get_search_type()
    {
        return "records_query";
    }

    public function get_raw_query()
    {
        return $this->_get_search_query();
    }

    protected function _get_search_query()
    {
        $query = $this->query;
        if ($this->_get_user_query() !== "*") {
            $query = str_replace('!!p!!', pmb_mysql_escape_string($this->_get_user_query()), $this->query);
            $query .= self::_get_typdoc_filter(true);
            return $query;
        }
        return "select 0 as id_notice";
    }

    protected function _get_pert($query = false, $with_explnum = false)
    {
        $this->table_tempo = $this->get_temporary_table_name("_pert");
        pmb_mysql_query("create temporary table $this->table_tempo (notice_id int(11) not null primary key, pert int(11) not null default 0)");
        if ($this->objects_ids != "") {
            pmb_mysql_query("insert into $this->table_tempo (notice_id) values (" . implode("),(", explode(",", $this->objects_ids)) . ")");
        }
        if ($query) {
            return "select * from $this->table_tempo";
        }
        return $this->table_tempo;
    }

    protected function _get_sign($sorted = false)
    {
        $sign = parent::_get_sign($sorted);
        $sign .= md5('&query=' . $this->query);
        return $sign;
    }
}