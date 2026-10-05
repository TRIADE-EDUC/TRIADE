<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_authorities.class.php,v 1.16.2.7 2024/11/20 13:22:52 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/indexations_collection.class.php");

class netbase_authorities {
    
    protected static $object_type = 0;
    
    protected static $indexation_authorities = [];
    
    protected static $indexation_by_fields = false;
    
    protected static $step_position = 0;
    
    public static function get_index_suffix_code() {
        global $index_quoi;
        
        switch ($index_quoi) {
            case 'SUBCOLLECTIONS':
                return 'SUB_COLLECTIONS';
            case 'DEWEY':
                return 'INDEXINT';
            default:
                return strtoupper($index_quoi);
        }
    }
    
    public static function get_next_index_quoi() {
        global $index_quoi;
        
        switch ($index_quoi) {
            case 'AUTHORS':
                return 'PUBLISHERS';
            case 'PUBLISHERS':
                return 'CATEGORIES';
            case 'CATEGORIES':
                return 'COLLECTIONS';
            case 'COLLECTIONS':
                return 'SUBCOLLECTIONS';
            case 'SUBCOLLECTIONS':
                return 'SERIES';
            case 'SERIES':
                return 'DEWEY';
            case 'DEWEY':
                return 'TITRES_UNIFORMES';
            case 'TITRES_UNIFORMES':
                return 'AUTHPERSO';
            case 'AUTHPERSO':
                return 'FINI';
        }
    }
    
    public static function proceed() {
        global $spec;
        
        if ($spec & INDEX_AUTHORITIES){
            static::proceed_reindex();
        } elseif ($spec & INDEX_SPHINX_AUTHORITIES){
            static::proceed_reindex_sphinx();
        }
    }
    
    public static function proceed_reindex() {
        global $msg;
        global $v_state, $spec;
        global $start, $count;
        global $index_quoi;
        
        if (!isset($count) || !$count) {
            $count = static::get_count_index();
            //On controle qu'il n'y a pas d'autorité à enlever
            static::delete_unrelated_authorities();
        }
        
        $index_suffix_code = static::get_index_suffix_code();
        print netbase::get_display_progress_title($msg["nettoyage_reindex_".strtolower($index_suffix_code)]);
        $next = static::index_from_interface($start, $count);
        $next_position = static::get_step_position();
        if($next || $next_position) {
            print netbase::get_current_state_form($v_state, $spec, $index_quoi, $next, $count, '', $next_position);
        } else {
            $v_state .= netbase::get_display_progress_v_state($msg["nettoyage_reindex_".strtolower($index_suffix_code)], $count." ".$msg["nettoyage_res_reindex_".strtolower($index_suffix_code)]);
            print netbase::get_current_state_form($v_state, $spec, static::get_next_index_quoi());
        }
    }
    
    public static function proceed_reindex_sphinx() {
        global $msg;
        global $v_state, $spec;
        global $start, $count;
        global $index_quoi;
        
        if (!isset($count) || !$count) {
            $count = static::get_count_index();
        }
        $index_suffix_code = static::get_index_suffix_code();
        print netbase::get_display_progress_title("[Sphinx] ".$msg["nettoyage_reindex_".strtolower($index_suffix_code)]);
        $next = static::index_sphinx_from_interface($start, $count);
        if($next) {
            print netbase::get_current_state_form($v_state, $spec, $index_quoi, $next, $count);
        } else {
            $v_state .= netbase::get_display_progress_v_state("[Sphinx] ".$msg["nettoyage_reindex_".strtolower($index_suffix_code)], $count." ".$msg["nettoyage_res_reindex_".strtolower($index_suffix_code)]);
            print netbase::get_current_state_form($v_state, $spec, static::get_next_index_quoi());
        }
    }
    
    public static function index_from_query($query, $object_type=0) {
        $result = pmb_mysql_query($query);
        $nb_indexed = pmb_mysql_num_rows($result);
        if ($nb_indexed) {
            $indexation_authority = indexations_collection::get_indexation($object_type);
            $indexation_authority->set_deleted_index(true);
            authorities_collection::setOptimizer(authorities_collection::OPTIMIZE_MEMORY);
            while (($row = pmb_mysql_fetch_object($result))) {
                $indexation_authority->maj($row->id);
            }
            pmb_mysql_free_result($result);
        }
        return $nb_indexed;
    }
    
    public static function raz_index() {
        $indexation_authorities = static::get_indexation_authorities();
        $indexation_authorities->raz_fields_table();
        $indexation_authorities->raz_words_table();
        if(empty(static::$object_type)) {
            $indexation_authorities->disable_fields_table_keys();
            $indexation_authorities->disable_words_table_keys();
        }
        $indexation_directory = authority::get_indexation_directory(static::$object_type);
        netbase_entities::clean_files($indexation_authorities->get_directory_files(), $indexation_directory);
    }
    
    public static function get_index_query_count() {
        switch (static::$object_type) {
            case AUT_TABLE_AUTHORS:
                return "SELECT count(1) FROM authors";
            case AUT_TABLE_PUBLISHERS:
                return "SELECT count(1) FROM publishers";
            case AUT_TABLE_CATEG:
                return "SELECT count(distinct num_noeud) FROM categories";
            case AUT_TABLE_COLLECTIONS:
                return "SELECT count(1) FROM collections";
            case AUT_TABLE_SUB_COLLECTIONS:
                return "SELECT count(1) FROM sub_collections";
            case AUT_TABLE_SERIES:
                return "SELECT count(1) FROM series";
            case AUT_TABLE_INDEXINT:
                return "SELECT count(1) FROM indexint";
            case AUT_TABLE_TITRES_UNIFORMES:
                return "SELECT count(1) FROM titres_uniformes";
        }
    }
    
    public static function get_count_index() {
        $elts = pmb_mysql_query(static::get_index_query_count());
        return pmb_mysql_result($elts, 0, 0);
    }
    
    public static function get_lot($count) {
        if(static::$indexation_by_fields) {
            $lot = REINDEX_BY_FIELDS_PAQUET_SIZE; // defini dans ./params.inc.php
            $step_fields = static::get_step_fields_from_position(static::$step_position);
            if (!empty($step_fields) && $step_fields['step_name'] == 'callables') {
                $lot = REINDEX_BY_CALLABLES_FIELDS_PAQUET_SIZE; // defini dans ./params.inc.php
            }
            if($count && ($lot + 5000) > $count) {
                //On s'accorde une souplesse pour ne pas refaire une passe dans le vide pour une taille proche du compteur
                $lot = $count;
            }
            return $lot;
        } else {
            return REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php
        }
    }
    
    public static function get_index_query($start, $lot) {
        $start = intval($start);
        $lot = intval($lot);
        switch (static::$object_type) {
            case AUT_TABLE_AUTHORS:
                return "SELECT author_id as id from authors LIMIT $start, $lot";
            case AUT_TABLE_PUBLISHERS:
                return "SELECT ed_id as id from publishers LIMIT $start, $lot";
            case AUT_TABLE_CATEG:
                return "select distinct num_noeud as id from categories limit $start, $lot ";
            case AUT_TABLE_COLLECTIONS:
                return "SELECT collection_id as id from collections LIMIT $start, $lot";
            case AUT_TABLE_SUB_COLLECTIONS:
                return "SELECT sub_coll_id as id from sub_collections LIMIT $start, $lot";
            case AUT_TABLE_SERIES:
                return "SELECT serie_id as id from series LIMIT $start, $lot";
            case AUT_TABLE_INDEXINT:
                return "SELECT indexint_id as id from indexint LIMIT $start, $lot";
            case AUT_TABLE_TITRES_UNIFORMES:
                return "SELECT tu_id as id from titres_uniformes LIMIT $start, $lot";
                
        }
    }
    
    public static function get_objects_ids_index($start, $lot) {
        $objects_ids = [];
        $query = static::get_index_query($start, $lot);
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            while($row = pmb_mysql_fetch_array($result)) {
                $objects_ids[] = $row['id'];
            }
        }
        return $objects_ids;
    }
    
    protected static function get_indexation_authority() {
        return new indexation_authority(indexations_collection::get_xml_file_path(static::$object_type), "authorities", static::$object_type);
    }
    
    public static function index_from_interval($start, $count) {
        $lot = static::get_lot($count);
        if(static::$indexation_by_fields) {
            $indexation_authorities = static::get_indexation_authorities();
            $steps_fields_number = $indexation_authorities->get_steps_fields_number();
            $next = 0;
            if(static::$step_position) {
                $objects_ids = static::get_objects_ids_index($start, $lot);
                $nb_objects_ids = count($objects_ids);
                if($nb_objects_ids) {
                    indexation_authorities::set_objects_mode('ids');
                    $indexation_authorities->set_objects_ids($objects_ids);
                    if(static::$step_position <= $steps_fields_number) {
                        static::index_step_fields_from_position(static::$step_position);
                    }
                    if(($start + $lot) >= $count) {
                        if(static::$step_position == $steps_fields_number) {
                            static::$step_position = 0;
                        } else {
                            static::$step_position++;
                        }
                    } else {
                        $next = $start + $lot;
                    }
                } else {
                    static::$step_position++;
                }
            } else {
                static::$step_position++;
            }
            if(static::$step_position && static::$step_position <= $steps_fields_number) {
                return $next;
            }
        } else {
            $query = static::get_index_query($start, $lot);
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $indexation_authority = static::get_indexation_authority();
                $indexation_authority->set_deleted_index(true);
                while($row = pmb_mysql_fetch_object($result)) {
                    $indexation_authority->maj($row->id);
                }
                pmb_mysql_free_result($result);
                return ($start + $lot);
            }
        }
        return 0;
    }
    
    public static function index_from_interface($start, $count) {
        if ( empty($count)) {
            // mise à jour de l'affichage de la jauge
            print netbase::get_display_final_progress();
            return 0;
        }
        $next = static::index_from_interval($start, $count);
        if(static::$indexation_by_fields) {
            print static::get_display_progress_title();
            $indexation_authorities = static::get_indexation_authorities();
            $steps_fields_number = $indexation_authorities->get_steps_fields_number();
            if(static::$step_position && static::$step_position <= $steps_fields_number) {
                $progress_start = static::$step_position;
                $progress_in_step = 0;
                if($next && static::$step_position) {
                    $progress_in_step = ($next/$count)*((static::$step_position)/$steps_fields_number);
                    if($progress_start+$progress_in_step <= $steps_fields_number) {
                        $progress_start += $progress_in_step;
                    }
                }
                print netbase::get_display_progress($progress_start, $steps_fields_number);
                return $next;
            }
        } else {
            if($next) {
                print netbase::get_display_progress($start, $count);
                return $next;
            }
        }
        // mise à jour de l'affichage de la jauge
        print netbase::get_display_final_progress();
        return 0;
    }
    
    protected static function get_authorities_ids($objects_ids) {
        $authorities_ids = [];
        $query = "SELECT id_authority AS id FROM authorities WHERE num_object IN (".implode(',', $objects_ids).") AND type_object= ".static::$object_type;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            while($row = pmb_mysql_fetch_array($result)) {
                $authorities_ids[] = $row['id'];
            }
        }
        return $authorities_ids;
    }
    
    public static function index_sphinx_from_interval($start, $count) {
        $lot = static::get_lot($count);
        $si = indexation_authority::get_sphinx_indexer(static::$object_type);
        if(is_object($si)) {
            $objects_ids = static::get_objects_ids_index($start, $lot);
            $nb_objects_ids = count($objects_ids);
            if($nb_objects_ids) {
                $si->fillIndexes(static::get_authorities_ids($objects_ids));
                return ($start + $lot);
            }
        }
        return 0;
    }
    
    public static function index_sphinx_from_interface($start, $count) {
        if ( empty($count)) {
            // mise à jour de l'affichage de la jauge
            print netbase::get_display_final_progress();
            return 0;
        }
        $next = static::index_sphinx_from_interval($start, $count);
        if($next) {
            print netbase::get_display_progress($start, $count);
            return $next;
        }
        // mise à jour de l'affichage de la jauge
        print netbase::get_display_final_progress();
        return 0;
    }
    
    public static function index() {
        $indexation_authorities = static::get_indexation_authorities();
		$indexation_authorities->launch_indexation();
    }
    
    public static function index_steps_fields() {
        $steps_fields = static::get_steps_fields();
        foreach ($steps_fields as $step_key=>$step_fields) {
            foreach ($step_fields as $fields) {
                if (!empty($fields[0])) {
                    $by_fields = array();
                    foreach ($fields as $field) {
                        $by_fields[] = $field['champ'];
                    }
                    if (!empty($by_fields)) {
                        $indexed = static::index_by_fields($step_key, $by_fields);
                    }
                } else {
                    $indexed = static::index_by_fields($step_key, [$fields['champ']]);
                }
            }
        }
        return $indexed;
    }
    
    public static function get_step_fields_from_position($position=0) {
        $steps_fields_position = 1;
        $steps_fields = static::get_steps_fields();
        foreach ($steps_fields as $step_name=>$step_fields) {
            foreach ($step_fields as $fields) {
                if($steps_fields_position == $position) {
                    if (!empty($fields[0])) {
                        $by_fields = array();
                        $labels = array();
                        foreach ($fields as $field) {
                            $by_fields[] = $field['champ'];
                            $labels[] = $field['label'];
                        }
                        if (!empty($by_fields)) {
                            return [
                                'step_name' => $step_name,
                                'fields' => $by_fields,
                                'labels' => $labels
                            ];
                        }
                    } else {
                        return [
                            'step_name' => $step_name,
                            'fields' => [$fields['champ']],
                            'labels' => [$fields['label']]
                        ];
                    }
                }
                $steps_fields_position++;
            }
        }
    }
    
    public static function index_step_fields_from_position($position=0) {
        if($position == 0) {
            return 1;
        }
        $step_fields = static::get_step_fields_from_position($position);
        if (!empty($step_fields)) {
            return static::index_by_fields($step_fields['step_name'], $step_fields['fields']);
        }
        return 0;
    }
    
    public static function index_by_step($step='') {
        $indexation_authorities = static::get_indexation_authorities();
    	return $indexation_authorities->maj_by_step($step);
    }
    
    public static function index_by_fields($step='', $fields=[]) {
        if (!empty($step) && !empty($fields)) {
            $indexation_authorities = static::get_indexation_authorities();
            return $indexation_authorities->maj_by_fields($step, $fields);
        }
        return 0;
    }
    
    public static function import_bdd() {
        $indexation_authorities = static::get_indexation_authorities();
        $indexation_authorities->maj_bdd_from_files();
    }
    
    public static function enable_index() {
        $indexation_authorities = static::get_indexation_authorities();
        $indexation_authorities->enable_fields_table_keys();
        $indexation_authorities->enable_words_table_keys();
    }
    
    public static function get_steps_fields() {
        $indexation_authorities = static::get_indexation_authorities();
        return $indexation_authorities->get_steps_fields();
    }
    
    public static function get_number_steps_fields() {
        $number = 0;
        $steps_fields = static::get_steps_fields();
        foreach ($steps_fields as $step_fields) {
            $number += count($step_fields);
        }
        return $number;
    }
    
    public static function get_nb_steps() {
        return count(indexation_authorities::$steps);
    }
    
    public static function get_query_unrelated_authorities() {
        switch (static::$object_type) {
            case AUT_TABLE_AUTHORS:
                return "SELECT id_authority FROM authorities LEFT JOIN authors ON num_object=author_id WHERE type_object='".static::$object_type."' AND author_id IS NULL";
            case AUT_TABLE_PUBLISHERS:
                return "SELECT id_authority FROM authorities LEFT JOIN publishers ON num_object=ed_id WHERE type_object='".static::$object_type."' AND ed_id IS NULL";
            case AUT_TABLE_CATEG:
                return "SELECT id_authority FROM authorities LEFT JOIN categories ON num_object=num_noeud WHERE type_object='".static::$object_type."' AND num_noeud IS NULL";
            case AUT_TABLE_COLLECTIONS:
                return "SELECT id_authority FROM authorities LEFT JOIN collections ON num_object=collection_id WHERE type_object='".static::$object_type."' AND collection_id IS NULL";
            case AUT_TABLE_SUB_COLLECTIONS:
                return "SELECT id_authority FROM authorities LEFT JOIN sub_collections ON num_object=sub_coll_id WHERE type_object='".static::$object_type."' AND sub_coll_id IS NULL";
            case AUT_TABLE_SERIES:
                return "SELECT id_authority FROM authorities LEFT JOIN series ON num_object=serie_id WHERE type_object='".static::$object_type."' AND serie_id IS NULL";
            case AUT_TABLE_INDEXINT:
                return "SELECT id_authority FROM authorities LEFT JOIN indexint ON num_object=indexint_id WHERE type_object='".static::$object_type."' AND indexint_id IS NULL";
            case AUT_TABLE_TITRES_UNIFORMES:
                return "SELECT id_authority FROM authorities LEFT JOIN titres_uniformes ON num_object=tu_id WHERE type_object='".static::$object_type."' AND tu_id IS NULL";
                
        }
    }
    
    public static function delete_unrelated_authorities() {
        $query = static::get_query_unrelated_authorities();
        $result = pmb_mysql_query($query);
        if($result && pmb_mysql_num_rows($result)){
            while($aut = pmb_mysql_fetch_row($result)){
                $authority = new authority($aut[0]);
                $authority->delete();
            }
        }
    }
    
    public static function set_object_type($object_type) {
        static::$object_type = $object_type;
    }
    
    public static function set_indexation_by_fields($indexation_by_fields) {
        static::$indexation_by_fields = $indexation_by_fields;
    }
    
    public static function get_step_position() {
        return static::$step_position;
    }
    
    public static function set_step_position($step_position) {
        static::$step_position = intval($step_position);
    }
    
    public static function get_indexation_authorities() {
        if(!isset(static::$indexation_authorities[static::$object_type]) || static::$indexation_authorities[static::$object_type] == null) {
            static::$indexation_authorities[static::$object_type] = new indexation_authorities(indexations_collection::get_xml_file_path(static::$object_type), "authorities", static::$object_type);
        }
        return static::$indexation_authorities[static::$object_type];
    }
    
    public static function unset_indexation_authorities() {
        static::$indexation_authorities[static::$object_type] = null;
    }
    
    public static function get_display_progress_title() {
        $step_fields = static::get_step_fields_from_position(static::$step_position);
        if (!empty($step_fields['labels'])) {
            return netbase::get_display_progress_subtitle(implode(' - ', $step_fields['labels']));
        }
        return '';
    }
}