<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_concepts_indexer.class.php,v 1.17.2.1.2.1 2025/05/30 09:07:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once $class_path.'/sphinx/sphinx_authorities_indexer.class.php';

class sphinx_concepts_indexer extends sphinx_authorities_indexer {
    
    public function __construct()
    {
        global $include_path;
        parent::__construct();
        $this->type = AUT_TABLE_CONCEPT;
        $this->default_index = "concepts";
        $this->object_id = 'id_item';
        $this->object_index_table = 'skos_fields_global_index';
        $this->filters = ['multi' => ['status', 'scheme']];
        //$this->setChampBaseFilepath($include_path."/indexation/authorities/authperso/champs_base.xml");
    }
    
    
    protected function addSpecificsFilters($id, $filters = array())
    {
        $filters = parent::addSpecificsFilters($id, $filters);
        
        //Recuperation du statut
        $query = "select num_statut, if(authority_num is not null, authority_num, 0) as scheme_num from authorities left join skos_fields_global_index on num_object = id_item and code_champ = 4 and code_ss_champ = 1 where id_authority = " .
            $id . " and type_object = " . $this->type;
            $result = pmb_mysql_query($query);
            $row = pmb_mysql_fetch_object($result);
            if (! array_key_exists('multi', $filters)) {
                $filters['multi'] = array();
            }
            if (is_object($row)) {
                $filters['multi']['status'] = $row->num_statut;
                $filters['multi']['scheme'] = $row->scheme_num;
            }
            return $filters;
    }
    
    
    protected function parse_file()
    {
        global $base_path;
        if (is_array($this->indexes) && count($this->indexes)) {
            return;
        }
        $onto_store_config = array(
            /* db */
            'db_name' => DATA_BASE,
            'db_user' => USER_NAME,
            'db_pwd' => USER_PASS,
            'db_host' => SQL_SERVER,
            /* store */
            'store_name' => 'ontology',
            /* stop after 100 errors */
            'max_errors' => 100,
            'store_strip_mb_comp_str' => 0
        );
        $data_store_config = array(
            /* db */
            'db_name' => DATA_BASE,
            'db_user' => USER_NAME,
            'db_pwd' => USER_PASS,
            'db_host' => SQL_SERVER,
            /* store */
            'store_name' => 'rdfstore',
            /* stop after 100 errors */
            'max_errors' => 100,
            'store_strip_mb_comp_str' => 0
        );
        $tab_namespaces = array(
            "skos"	=> "http://www.w3.org/2004/02/skos/core#",
            "dc"	=> "http://purl.org/dc/elements/1.1",
            "dct"	=> "http://purl.org/dc/terms/",
            "owl"	=> "http://www.w3.org/2002/07/owl#",
            "rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
            "rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
            "xsd"	=> "http://www.w3.org/2001/XMLSchema#",
            "pmb"	=> "http://www.pmbservices.fr/ontology#"
        );
        $onto_index = onto_index::get_instance("skos");
        $onto_index->load_handler($base_path . "/classes/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config, $tab_namespaces, 'http://www.w3.org/2004/02/skos/core#prefLabel');
        $onto_index->init();
        
        if (!count($onto_index->tab_code_champ)) {
            return;
        }
        
        // On initialise le tableau
        if (!isset($this->indexes[$this->default_index])) {
            $this->indexes[$this->default_index] = array(
                'fields' => array(),
                'attributes' => array('dummy')
            );
        }
        foreach ($onto_index->tab_code_champ as $index) {
            foreach ($index as $sub_index) {
                $field = 'f_' . str_pad($sub_index['champ'], 3, "0", STR_PAD_LEFT) . '_' . str_pad($sub_index['ss_champ'], 2, "0", STR_PAD_LEFT);
                $this->insert_index[$field] = $this->default_index;
                $this->fields_pond[$field] = $sub_index['pond'] * $this->multiple;
                $this->indexes[$this->default_index]['fields'][] = $field;
                // $this->indexes[$this->default_index]['attributes'][] = $field;
            }
        }
        $this->indexes[$this->default_index]['attributes'] = $this->filters;
    }
    
    
    /**
     * Remplissage d'un ensemble index
     *
     * @param [int] $object_ids : id objets a indexer. Si vide, remplissage de l'ensemble des index
     * @param boolean $showProgression : affichage progression en console
     *
     */
    public function fillIndexes($object_ids = [], $showProgression = false)
    {
        array_walk($object_ids, function(&$a) { $a = intval($a);});
        $showProgression = boolval($showProgression);
        
        $this->parse_file();
        $langs = $this->getAvailableLanguages();
        $imploded_langs = implode('","', $langs);
        $separator = $this->getSeparator();
        
        pmb_mysql_query('set session group_concat_max_len = 16777216');
        
        // Suppression index sphinx
        $this->deleteIndexes($object_ids);
        
        $tab_values = [];
        
        // Selection des objets a indexer
        $rq = 'select ' . $this->object_key . ', num_object from ' . $this->object_table . ' where type_object = ' . $this->type;
        if(!empty($object_ids)) {
            $rq .= ' and num_object in (' . implode(',', $object_ids).')';
        }
        $res = pmb_mysql_query($rq);
        
        if ($res) {
            
            if ($showProgression) {
                print ProgressBar::start(pmb_mysql_num_rows($res), "Index " . $this->default_index);
            }
            
            $n = 0;
            while ($row = pmb_mysql_fetch_assoc($res)) {
                
                $id = $row[$this->object_key];
                
                //Construction de l'index
                $inserts = [];
                $rq = 'select code_champ, code_ss_champ, lang, group_concat(value SEPARATOR "' . $separator . '") as value '.
                    ' from '.$this->object_index_table.
                    ' where code_champ < 100 and '.$this->object_id.'= '.$row['num_object'].' and lang in ("' . $imploded_langs . '") group by code_champ, code_ss_champ, lang';
                $res_notice = pmb_mysql_query($rq);
                
                while ($champ = pmb_mysql_fetch_assoc($res_notice)) {
                    if (in_array($champ['lang'], $langs)) {
                        $code_champ = str_pad($champ['code_champ'], 3, "0", STR_PAD_LEFT);
                        $code_ss_champ = str_pad($champ['code_ss_champ'], 2, "0", STR_PAD_LEFT);
                        $field = 'f_' . $code_champ . '_' . $code_ss_champ;
                        
                        if ($this->insert_index[$field]) {
                            $inserts[$this->insert_index[$field] . ($champ['lang'] ? '_' . $champ['lang'] : '')][$field] = addslashes(encoding_normalize::utf8_normalize($champ['value']));
                        }
                    }
                }
                $inserts = $this->getSpecificsFiltersValues($id, $inserts);
                foreach ($inserts as $table => $fields) {
                    $keys = $values = "";
                    foreach ($fields as $key => $value) {
                        if ($keys) {
                            $keys .= ",";
                            $values .= ",";
                        }
                        $keys .= $key;
                        if (substr($key, 0, 2) !== "f_") {
                            $values .= $value;
                        } else {
                            $values .= '\'' . $value . '\'';
                        }
                    }
                    
                    $tab_values[$table][$keys][] = '(' . $id . ',' . $values . ')';
                }
                
                if ($showProgression) {
                    print ProgressBar::next();
                }
                
                $n++;
                if($n > $this->packetSize) {
                    $this->insertIndexes($tab_values);
                    $tab_values = [];
                    $n = 0;
                }
            }
            
            $this->insertIndexes($tab_values);
            $tab_values = [];
            
            if ($showProgression) {
                print ProgressBar::finish();
            }
        }
    }
    
    /**
     * Suppression d'un ensemble d'index
     *
     * @param [int] $object_ids : ids index a supprimer. Si vide, suppression de l'ensemble des index
     *
     */
    public function deleteIndexes($object_ids = [])
    {
        //On appelle directement le parent
        if(empty($object_ids)) {
            parent::deleteIndexes($object_ids);
        }
        
        array_walk($object_ids, function(&$a) { $a = intval($a);});
        
        $this->parse_file();
        
        // Variante sur les concepts pour les identifiants à supprimer
        $rq = 'select ' . $this->object_key . ', num_object from ' . $this->object_table . ' where type_object = ' . $this->type;
        if(!empty($object_ids)) {
            $rq .= ' and num_object in (' . implode(',', $object_ids).')';
        }
        $res = pmb_mysql_query($rq);
        if ($res) {
            $new_object_ids = [];
            while ($row=pmb_mysql_fetch_object($res)) {
                $new_object_ids[] = $row->{$this->object_key};
            }
            if (!empty($new_object_ids)) {
                parent::deleteIndexes($new_object_ids);
            }
        }
    }
}
