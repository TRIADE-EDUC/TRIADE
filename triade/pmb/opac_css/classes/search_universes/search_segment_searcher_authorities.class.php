<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_searcher_authorities.class.php,v 1.5 2023/07/27 06:57:38 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

require_once $class_path . '/searcher/searcher_authorities_extended.class.php';

// un jour ca sera utile
class search_segment_searcher_authorities extends searcher_authorities_extended
{

    const PREFIX_TEMPO = 'segment_auth';

    public function __construct($serialized_query = "")
    {
        parent::__construct($serialized_query);
        global $user_query, $universe_query;
        $this->user_query = $user_query;

        if (empty($user_query) && ! empty($universe_query)) {
            $this->user_query = $universe_query;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher_authorities_extended::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        parent::_get_pert();
    }

    public function get_authority_tri()
    {
        if (empty($this->authority_type) || empty($this->object_table)) {
            if (! empty($this->table_tempo)) {
                return ' ' . $this->table_tempo . '.id_authority desc ';
            }
            if (! empty($this->table)) {
                return ' ' . $this->table . '.id_authority desc ';
            }
            return ' id_authority desc ';
        }
        $prefix = entities::get_table_prefix_from_const($this->authority_type);
        $index = '';
        switch ($this->authority_type) {
            case AUT_TABLE_CONCEPT:
                $index = '';
                break;
            case AUT_TABLE_CATEG:
                $index = 'index_' . $prefix;
                break;
            case AUT_TABLE_PUBLISHERS:
                $index = 'index_' . $prefix;
                break;
            case AUT_TABLE_COLLECTIONS:
                $index = 'index_coll';
                break;
            case AUT_TABLE_SERIES:
                $index = 'serie_index';
                break;
            case AUT_TABLE_AUTHPERSO:
                $index = 'authperso_index_infos_global';
                break;
            default:
                $index = 'index_' . $prefix;
                break;
        }
        return " $index asc ";
    }

    public function init_authority_param($authority_type)
    {
        $this->authority_type = $authority_type;
        $this->object_table = entities::get_authority_table_from_const($authority_type);
        $this->object_table_key = entities::get_authority_id_from_const($authority_type);
    }

    protected function get_authority_join()
    {
        if (! empty($this->object_table) && ! empty($this->object_table_key) && ! empty($this->table)) {
            return " JOIN authorities ON " . $this->table . ".id_authority = authorities.id_authority
	               JOIN " . $this->object_table . " ON authorities.num_object = " . $this->object_table_key . " AND authorities.type_object = " . $this->authority_type . " ";
        }
        return "";
    }

    protected function get_instance()
    {
        global $es;
        if (is_object($es)) {
            if (get_class($es) != "search_authorities") {
                $es = new search_authorities("search_fields_authorities_gestion");
            }
            if ($es->fichier_xml == "search_fields_authorities") {
                $es = new search_authorities("search_fields_authorities_gestion");
            }
            return $es;
        }
        $es = new search_authorities("search_fields_authorities_gestion");
        return $es;
    }
}