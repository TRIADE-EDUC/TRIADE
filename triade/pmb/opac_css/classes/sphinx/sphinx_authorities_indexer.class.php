<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_authorities_indexer.class.php,v 1.5 2024/04/26 15:58:45 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;

require_once "$class_path/sphinx/sphinx_indexer.class.php";

class sphinx_authorities_indexer extends sphinx_indexer
{
    
    protected $type;
    
    public function __construct()
    {
        $this->object_id = 'id_authority';
        $this->object_key = 'id_authority';
        $this->object_index_table = 'authorities_fields_global_index';
        $this->object_table = 'authorities';
        $this->filters = ['multi' => ['status']];
        parent::__construct();
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
        $rq = "SELECT $this->object_key FROM $this->object_table WHERE type_object = $this->type ";
        if(!empty($object_ids)) {
            $rq .= " and $this->object_key in (". implode(',',  $object_ids).")";
        }
        $res = pmb_mysql_query($rq);
        
        if ($res) {
            
            // if ($showProgression) {
            //     print ProgressBar::start(pmb_mysql_num_rows($res), "Index " . $this->default_index);
            // }
            
            $n = 0;
            while ($row = pmb_mysql_fetch_assoc($res)) {
                
                $id = $row[$this->object_key];
                
                // Construction de l'index
                $inserts = [];
                $rq = 'SELECT code_champ, code_ss_champ, lang, group_concat(value SEPARATOR "' . $separator . '") AS value ' .
                    ' FROM ' . $this->object_index_table .
                    ' WHERE ' . $this->object_id . ' = ' .$id . ' AND lang in ("' . $imploded_langs . '") GROUP BY code_champ, code_ss_champ, lang';
                $res_notice = pmb_mysql_query($rq);
                
                while ($champ = pmb_mysql_fetch_assoc($res_notice)) {
                    if (in_array($champ['lang'], $langs)) {
                        $code_champ = str_pad($champ['code_champ'], 3, "0", STR_PAD_LEFT);
                        $code_ss_champ = str_pad($champ['code_ss_champ'], 2, "0", STR_PAD_LEFT);
                        $field = 'f_' . $code_champ . '_' . $code_ss_champ;
                        
                        if (!empty($this->insert_index[$field])) {
                            $inserts[$this->insert_index[$field] . ($champ['lang'] ? '_' . $champ['lang'] : '')][$field] = addslashes(encoding_normalize::utf8_normalize($champ['value']));
                        }
                    }
                }
                $inserts = $this->getSpecificsFiltersValues($id, $inserts);
                foreach ($inserts as $table => $fields) {
                    $keys = $values = "";
                    foreach ($fields as $key => $value) {
                        if (!empty($keys)) {
                            $keys .= ",";
                            $values .= ",";
                        }
                        $keys .= $key;
                        if (substr($key, 0, 2) !== "f_") {
                            $values .= $value;
                        } else {
                            $values .= "'$value'";
                        }
                    }
                    $tab_values[$table][$keys][] = '(' . $id . ',' . $values . ')';
                }
                
                // if ($showProgression) {
                //     print ProgressBar::next();
                //}
                
                $n++;
                if($n > $this->packetSize) {
                    $this->insertIndexes($tab_values);
                    $tab_values = [];
                    $n = 0;
                }
            }
            
            $this->insertIndexes($tab_values);
            $tab_values = [];
            
            // if ($showProgression) {
            //     print ProgressBar::finish();
            // }
        }
    }
    
    
    protected function addSpecificsFilters($id, $filters = array())
    {
        $filters = parent::addSpecificsFilters($id, $filters);
        return $filters;
    }
}
