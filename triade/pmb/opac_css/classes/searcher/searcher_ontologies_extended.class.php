<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_ontologies_extended.class.php,v 1.4 2024/03/06 10:02:29 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class searcher_ontologies_extended extends searcher_ontologies
{

    protected $id_ontology = 0;

    protected $serialized_query;

    // recherche sérialisée
    protected $with_make_search;

    // Savoir si on peut avoir la pertinance ou non
    public $table;

    // table tempo de la multi
    public function __construct($serialized_query = "")
    {
        $this->with_make_search = false;
        $this->serialized_query = $serialized_query;
        parent::__construct("");
    }

    public function _get_search_type()
    {
        return "extended_ontologies";
    }

    protected function get_instance()
    {
        global $es;
        $ontology = new ontology(ontologies::get_ontology_id_from_class_uri(onto_common_uri::get_uri($this->class_id))); // ::get_ontology_by_pmbname($ontoname);
        if (is_object($es)) {
            if (get_class($es) != "search_fields_ontology") {
                $es = new search_ontology("search_fields_ontology_gestion", $ontology->get_handler()->get_ontology());
            }
            if ($es->fichier_xml == "search_fields_ontology") {
                $es = new search_ontology("search_fields_ontology_gestion", $ontology->get_handler()->get_ontology());
            }
            return $es;
        }
        $es = new search_ontology("search_fields_ontology_gestion", $ontology->get_handler()->get_ontology());
        return $es;
    }

    protected function _get_user_query()
    {
        $es = $this->get_instance();
        return $es->serialize_search();
    }

    protected function _get_search_query()
    {
        $es = $this->get_instance();
        if ($this->serialized_query) {
            $es->unserialize_search($this->serialized_query);
        }
        $this->with_make_search = true;
        $this->table = $es->make_search($this->get_temporary_table_name("_" . rand(0, 10) . "_"));
        if (!empty($this->table)) {
            return "select " . $this->table . "." . $this->object_index_key . ", pert from " . $this->table;
        }
        return "";
    }

    /**
     *
     * {@inheritdoc}
     * @see searcher::_get_pert()
     */
    protected function _get_pert($query = false, $with_explnum = false)
    {
        if (! $this->objects_ids) {
            return;
		}
        if ($this->with_make_search) {
            $this->table_tempo = $this->get_temporary_table_name('get_pert');
            $rqt = "create temporary table " . $this->table_tempo . " select * from " . $this->table . " where " . $this->object_index_key . " in(" . $this->objects_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(" . $this->object_index_key . ")");

            $this->_add_pert($this->table_tempo);
        } else {
            $this->table_tempo = $this->get_temporary_table_name('get_pert_2');
            $rqt = "create temporary table " . $this->table_tempo . " select " . $this->object_key . " as " . $this->object_index_key . ",100 as pert from onto_uri where " . $this->object_key . " in(" . $this->objects_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table_tempo . " add index i_id(" . $this->object_index_key . ")");
        }
    }

    protected function _add_pert($table_name)
    {
        if ((! pmb_mysql_num_rows(pmb_mysql_query('show columns from ' . $table_name . ' like "pert"')))) {
            $query = "alter table " . $table_name . " add pert decimal(16,1) default 1";
            @pmb_mysql_query($query);
        }
    }

    public function get_result()
    {
        $cache_result = $this->_get_in_cache();
        if ($cache_result === false) {
            $this->_get_objects_ids();
            $this->_filter_results();
            $this->_set_in_cache();
            if ($this->objects_ids) {
                $_SESSION['tab_result'] = $this->objects_ids;
            }
        } else {
            $this->objects_ids = $cache_result;
            if (! $this->objects_ids) {
                return array();
			}
            $this->table = $this->get_temporary_table_name('get_result');
            $rqt = "create temporary table " . $this->table . " engine=memory select " . $this->object_key . " as " . $this->object_index_key . " from onto_uri where " . $this->object_key . " in(" . $this->objects_ids . ")";
            pmb_mysql_query($rqt);
            pmb_mysql_query("alter table " . $this->table . " add index i_id(" . $this->object_index_key . ")");
            if (! empty($this->pert)) {
                $query = "alter table " . $this->table . " add pert decimal(16,1) default 1";
                pmb_mysql_query($query);
                foreach ($this->pert as $id => $pert) {
                    $query = "UPDATE " . $this->table . " SET pert = $pert WHERE " . $this->object_index_key . " = $id";
                    pmb_mysql_query($query);
                }
            }
        }
        return $this->objects_ids;
    }
}