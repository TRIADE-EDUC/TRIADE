<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sparql.class.php,v 1.3 2020/09/25 14:29:36 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_sparql extends opac_searcher_generic {

    protected $datastore = null;
    protected $objects_uris = [];
    
    public function __construct($user_query = '')
    {
        parent::__construct($user_query);
    }
    
    public function init_datastore($store_name = 'ontology_pmb')
    {
        if (! isset($this->datastore)) {
            $store_config = array(
                'db_name' => DATA_BASE,
                'db_user' => USER_NAME,
                'db_pwd' => USER_PASS,
                'db_host' => SQL_SERVER,
                'store_name' => $store_name,
                'max_errors' => 100,
                'store_strip_mb_comp_str' => 0
            );
            $tab_namespaces = array(
                'dc' => 'http://purl.org/dc/elements/1.1',
                'dct' => 'http://purl.org/dc/terms/',
                'owl' => 'http://www.w3.org/2002/07/owl#',
                'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
                'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
                'xsd' => 'http://www.w3.org/2001/XMLSchema#',
                'pmb' => 'http://www.pmbservices.fr/ontology#',
                'ca' => 'http://www.pmbservices.fr/ca/'
            );
            $this->datastore = new onto_store_arc2_extended($store_config);
            $this->datastore->set_namespaces($tab_namespaces);
        }
    }
    
    protected function _get_search_type()
    {
        return 'sparql';
    }
    
    protected function _get_objects_ids()
    {
        if (empty($this->searched)) {
            $this->objects_ids = '';
            $this->objects_uris = [];
            $query = $this->_get_search_query();
            if ($this->datastore->query($query)) {
                $row = $this->datastore->get_result();
                $nb_results = count($row);
                for ($i = 0; $i < $nb_results; $i++) {
                    if (!in_array($row[$i]->uri, $this->objects_uris)) {
                        $this->objects_uris[] = $row[$i]->uri;
                    }
                }
                $this->objects_ids = implode(',', $this->objects_uris);
            }
            $this->searched = true;
        }
        return $this->objects_ids;
    }
    
    public function get_datastore()
    {
        return $this->datastore;
    }
}