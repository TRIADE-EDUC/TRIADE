<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_authorities_authpersos_query.class.php,v 1.4 2023/07/27 06:57:37 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once ($class_path . '/searcher/searcher_autorities.class.php');

class searcher_authorities_authpersos_query extends searcher_authorities_authpersos
{

    protected $query = "";

    public function set_query($query)
    {
        $this->query = $query;
    }

    public function _get_search_type()
    {
        return parent::_get_search_type() . "query";
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

    public function get_raw_query()
    {
        return $this->_get_search_query();
    }

    protected function _get_search_query()
    {
        $user_query = $this->_get_user_query();
        if ($user_query !== "*") {
            $query = str_replace('!!p!!', pmb_mysql_escape_string($user_query), $this->query);
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