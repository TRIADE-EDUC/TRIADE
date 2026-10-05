<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: netbase_records.class.php,v 1.16.2.4 2024/09/18 12:59:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/explnum.class.php");

class netbase_records {
	
	protected static $cleaned_records = array();
	
	protected static $cleaned_authorities = array();
	
	protected static $indexation_records;
	
	protected static $indexation_by_fields = false;
	
	protected static $step_position = 0;
	
	public static function proceed() {
	    global $spec;
	    
	    if ($spec & INDEX_GLOBAL){
	        static::proceed_reindex();
	    } elseif ($spec & INDEX_SPHINX_RECORDS){
	        static::proceed_reindex_sphinx();
	    }
	}
	
	public static function proceed_reindex() {
	    
	}
	
	public static function proceed_reindex_sphinx() {
	    
	}
	
	public static function global_index_from_query($query) {
		$result = pmb_mysql_query($query);
		$nb_indexed = pmb_mysql_num_rows($result);
		if ($nb_indexed) {
			notice::set_deleted_index(true);
			while($mesNotices = pmb_mysql_fetch_assoc($result)) {
				// Mise à jour de tous les index de la notice
				$info=notice::indexation_prepare($mesNotices['id']);
// 				notice::majNotices($mesNotices['notice_id']); //réalisée de l'indexation des champs de recherche
				notice::majNoticesGlobalIndex($mesNotices['id']);
				notice::indexation_restaure($info);
			}
			pmb_mysql_free_result($result);
		}
		return $nb_indexed;
	}
	
	public static function index_from_query($query) {
		$result = pmb_mysql_query($query);
		$nb_indexed = pmb_mysql_num_rows($result);
		if ($nb_indexed) {
			notice::set_deleted_index(true);
			while($mesNotices = pmb_mysql_fetch_assoc($result)) {
				// Mise à jour de tous les index de la notice
				$info=notice::indexation_prepare($mesNotices['id']);
				notice::majNoticesMotsGlobalIndex($mesNotices['id']);
				notice::indexation_restaure($info);
			}
			pmb_mysql_free_result($result);
		}
		return $nb_indexed;
	}
	
	public static function raz_index() {
	    $indexation_records = static::get_indexation_records();
	    $indexation_records->raz_fields_table();
	    $indexation_records->raz_words_table();
	    $indexation_records->disable_fields_table_keys();
	    $indexation_records->disable_words_table_keys();
	    netbase_entities::clean_files($indexation_records->get_directory_files());
	}
	
	public static function get_index_query_count() {
	    return "SELECT count(1) FROM notices";
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
	    return "SELECT notice_id as id from notices order by notice_id LIMIT $start, $lot";
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
	
	public static function index_from_interval($start, $count) {
	    $lot = static::get_lot($count);
	    if(static::$indexation_by_fields) {
	        $indexation_records = static::get_indexation_records();
	        $steps_fields_number = $indexation_records->get_steps_fields_number();
	        $next = 0;
	        if(static::$step_position) {
	            $objects_ids = static::get_objects_ids_index($start, $lot);
	            $nb_objects_ids = count($objects_ids);
	            if($nb_objects_ids) {
	                indexation_records::set_objects_mode('ids');
	                $indexation_records->set_objects_ids($objects_ids);
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
	        $nb_indexed = static::index_from_query($query);
	        if($nb_indexed) {
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
	        $indexation_records = static::get_indexation_records();
	        $steps_fields_number = $indexation_records->get_steps_fields_number();
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
	    print netbase::get_display_final_progress();
	    return 0;
	}
	
	public static function index_sphinx_from_interval($start, $count) {
	    $lot = static::get_lot($count);
	    $si = indexation_record::get_sphinx_indexer();
	    if(is_object($si)) {
	        $objects_ids = static::get_objects_ids_index($start, $lot);
	        $nb_objects_ids = count($objects_ids);
	        if($nb_objects_ids) {
	            $si->fillIndexes($objects_ids);
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
	
	public static function index($object_type=0) {
	    $indexation_records = static::get_indexation_records();
	    $indexation_records->launch_indexation();
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
		$indexation_records = static::get_indexation_records();
		return $indexation_records->maj_by_step($step);
	}
	
	public static function index_by_fields($step='', $fields=[]) {
	    if (!empty($step) && !empty($fields)) {
    	    $indexation_records = static::get_indexation_records();
    	    return $indexation_records->maj_by_fields($step, $fields);
	    }
	    return 0;
	}
	
	public static function import_bdd() {
	    $indexation_records = static::get_indexation_records();
	    $indexation_records->maj_bdd_from_files();
	}
	
	public static function enable_index() {
	    $indexation_records = static::get_indexation_records();
	    $indexation_records->enable_fields_table_keys();
	    $indexation_records->enable_words_table_keys();
	}
	
	public static function get_steps_fields() {
	    $indexation_records = static::get_indexation_records();
	    return $indexation_records->get_steps_fields();
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
        return count(indexation_records::$steps);    
	}
	
	public static function clean_thumbnail() {
		global $opac_url_base;
		
		if(thumbnail::is_valid_folder('record')) {
			$query = "select notice_id, thumbnail_url from notices where thumbnail_url like 'data:image%'";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					$created = thumbnail::create_from_base64($row->notice_id, 'records', $row->thumbnail_url);
					if($created) {
						$thumbnail_url = $opac_url_base."getimage.php?noticecode=&vigurl=";
						$thumbnail_url .= "&notice_id=".$row->notice_id;
						$query = "update notices set thumbnail_url = '".addslashes($thumbnail_url)."', update_date=update_date where notice_id = ".$row->notice_id;
						pmb_mysql_query($query);
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public static function clean_docnum_thumbnail($limit = 0) {
		if(thumbnail::is_valid_folder('docnum')) {
			$query = "SELECT explnum_id, explnum_vignette FROM explnum WHERE length(explnum_vignette) > 1000";
			$limit = intval($limit);
			if (!empty($limit)) {
			    $query .= " LIMIT $limit";
			}
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_assoc($result)) {
					explnum::upload_thumbnail($row["explnum_vignette"], $row["explnum_id"]);
				}
			}
			return true;
		}
		return false;
	}
	
	protected static function clean_field_data($field='') {
		global $charset;
		
		if(empty($field)) {
			return false;
		}
		$query = "SELECT notice_id, ".$field." FROM notices";
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$decoded_field = html_entity_decode($row->{$field}, ENT_QUOTES, $charset);
				if($row->{$field} != $decoded_field) {
					$query = "UPDATE notices SET ".$field." = '".addslashes($decoded_field)."', update_date=update_date WHERE notice_id =".$row->notice_id;
					pmb_mysql_query($query);
					if(!in_array($row->notice_id, static::$cleaned_records)) {
						static::$cleaned_records[] = $row->notice_id;
					}
				}
			}
		}
	}
	
	public static function clean_data() {
		//Nettoyons les résumés
		static::clean_field_data('n_resume');
		//Nettoyons les notes de contenu
		static::clean_field_data('n_contenu');
		//Nettoyons les notes générales
		static::clean_field_data('n_gen');
		return true;
	}
	
	public static function get_cleaned_records() {
		return static::$cleaned_records;
	}
	
	public static function get_cleaned_authorities() {
		return static::$cleaned_authorities;
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
	
	public static function get_indexation_records() {
	    global $include_path;
	    
	    if(!isset(static::$indexation_records) || static::$indexation_records == null) {
	        static::$indexation_records = new indexation_records($include_path."/indexation/notices/champs_base.xml", 'notices');
	    }
	    return static::$indexation_records;
	}
	
	public static function unset_indexation_records() {
	    static::$indexation_records = null;
	}
	
	public static function get_display_progress_title() {
	    $step_fields = static::get_step_fields_from_position(static::$step_position);
	    if (!empty($step_fields['labels'])) {
	       return netbase::get_display_progress_subtitle(implode(' - ', $step_fields['labels']));
	    }
	    return '';
	}
}
