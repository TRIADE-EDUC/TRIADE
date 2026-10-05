<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_controller.class.php,v 1.1.4.8 2024/12/03 08:01:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/indexation_stack.class.php");

class indexation_controller extends lists_controller {
	
// 	protected static $model_class_name = 'indexation_model';
	
	protected static $list_ui_class_name = 'list_indexation_ui';
	
	protected static $netbase_class_name = '';
	
	public static function get_reindex_title() {
	    global $msg;
	    global $name;
	    
	    switch (pmb_strtoupper($name)) {
	        case 'NOTICES':
	            return $msg["nettoyage_reindex_global"];
	        case 'SUBCOLLECTIONS':
	            return $msg["nettoyage_reindex_sub_collections"];
	        case 'CONCEPTS':
	            return $msg["nettoyage_reindex_concept"];
	        default:
	            return $msg["nettoyage_reindex_".strtolower($name)];
	    }
	    
	}
	
	public static function initialization_reindex() {
	    global $name, $lot, $count;
	    
	    // la taille d'un paquet de notices
	    $lot = REINDEX_PAQUET_SIZE; // defini dans ./params.inc.php
	    
	    switch (pmb_strtoupper($name)) {
	        case 'AUTHORS':
	            netbase_authorities::set_object_type(AUT_TABLE_AUTHORS);
	            break ;
	        case 'PUBLISHERS':
	            netbase_authorities::set_object_type(AUT_TABLE_PUBLISHERS);
	            break ;
	        case 'CATEGORIES':
	            netbase_authorities::set_object_type(AUT_TABLE_CATEG);
	            break ;
	        case 'COLLECTIONS':
	            netbase_authorities::set_object_type(AUT_TABLE_COLLECTIONS);
	            break ;
	        case 'SUBCOLLECTIONS':
	            netbase_authorities::set_object_type(AUT_TABLE_SUB_COLLECTIONS);
	            break ;
	        case 'SERIES':
	            netbase_authorities::set_object_type(AUT_TABLE_SERIES);
	            break ;
	        case 'INDEXINT':
	            netbase_authorities::set_object_type(AUT_TABLE_INDEXINT);
	            break ;
	        case 'TITRES_UNIFORMES':
	            netbase_authorities::set_object_type(AUT_TABLE_TITRES_UNIFORMES);
	            break ;
	        case 'AUTHPERSO':
	            netbase_authorities::set_object_type(AUT_TABLE_AUTHPERSO);
	            break ;
	    }
	    
	    if (!isset($count) || !$count) {
	        switch (pmb_strtoupper($name)) {
	            case 'NOTICES':
	                if (!isset($count) || !$count) {
	                    $notices = pmb_mysql_query("SELECT count(1) FROM notices");
	                    $count = pmb_mysql_result($notices, 0, 0);
	                }
	                break;
	            case 'AUTHPERSO':
	                $count = netbase_authperso::get_count_index();
	                break;
	            case 'CONCEPTS':
	                $count = netbase_concepts::get_count_index();
	                break;
	            default :
	                $count = netbase_authorities::get_count_index();
	                break;
	        }
	    }
	}
	
	public static function proceed_reindex_entities() {
	    global $name;
	    global $start, $count, $step_position;
	    
	    $netbase_class_name = static::$netbase_class_name;
	    if (!empty($netbase_class_name) && class_exists($netbase_class_name)) {
	        // initialisation de la borne de départ
	        if (!isset($start) && empty($step_position)) {
	            $start=0;
	            //remise a zero de la table au début
	            $netbase_class_name::raz_index();
	        }
	        if(!empty($step_position)) {
	            $netbase_class_name::set_step_position($step_position);
	        }
	        
	        print netbase::get_display_progress_title(static::get_reindex_title());
	        $next = $netbase_class_name::index_from_interface($start, $count);
	        $next_position = $netbase_class_name::get_step_position();
	        if($next || $next_position) {
	            print netbase::get_current_state_form('', 0, $name, $next, $count, '', $next_position);
	        } else {
	            //reactivation des cles
	            $netbase_class_name::enable_index();
	            
	            static::redirect_display_list();
	        }
	    }
	}
	
	public static function proceed_reindex_sphinx_entities() {
	    global $name;
	    global $start, $count;
	    
	    $netbase_class_name = static::$netbase_class_name;
	    if (!empty($netbase_class_name) && class_exists($netbase_class_name)) {
	        // initialisation de la borne de départ
	        if (!isset($start)) {
	            $start=0;
	        }
	        
	        print netbase::get_display_progress_title("[Sphinx] ".static::get_reindex_title());
	        $next = $netbase_class_name::index_sphinx_from_interface($start, $count);
	        if($next) {
	            print netbase::get_current_state_form('', 0, $name, $next, $count);
	        } else {
	            static::redirect_display_list();
	        }
	    }
	}
	
	public static function proceed_reindex() {
	    global $name, $id, $action, $pmb_clean_mode;
	    global $id_authperso;
	    
	    $id = intval($id);
	    netbase::set_controller_url_base(static::get_url_base()."&name=".$name.($id ? "&id=".$id : "")."&action=".$action);
	    switch ($name) {
	        case 'notices':
	            static::$netbase_class_name = 'netbase_records';
	            break;
	        case 'authors':
	        case 'publishers':
	        case 'categories':
	        case 'collections':
	        case 'subcollections':
	        case 'series':
	        case 'indexint':
	        case 'titres_uniformes':
	            static::$netbase_class_name = 'netbase_authorities';
	            break;
	        case 'authperso':
	            static::$netbase_class_name = 'netbase_authperso';
	            $id_authperso = intval($id_authperso);
	            netbase_authperso::set_id_authperso($id_authperso);
	            break;
	        case 'concepts':
	            static::$netbase_class_name = 'netbase_concepts';
	            break;
	    }
	    // Indexation par champ activée ? (sera activée par défaut par la suite))
	    if(!empty($pmb_clean_mode)) {
	        if (!empty(static::$netbase_class_name)) {
	            $netbase_class_name = static::$netbase_class_name;
	            $netbase_class_name::set_indexation_by_fields(true);
	        }
	    }
	    static::initialization_reindex();
	    if ($id) {
	        print "@TODO : indexation by field";
// 	        netbase_records::index_by_fields($step, array($id));
	    } else {
	        if ($action == 'reindex_sphinx') {
	            static::proceed_reindex_sphinx_entities();
	        } else {
	            static::proceed_reindex_entities();
	        }
	    }
	}
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		switch ($action) {
		    case 'reindex':
		    case 'reindex_sphinx':
		        static::proceed_reindex();
		        break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    global $name;
	    global $field;
	    global $id;
	    
	    $entity_type = 0;
	    if (!empty($name)) {
	        switch ($name) {
	            case 'notices':
	                $entity_type = TYPE_NOTICE;
	                break;
	            default:
	                $const = authority::get_const_type_object($name);
	                if (array_search($const, authority::$type_table) !== false) {
	                    $entity_type = array_search($const, authority::$type_table);
	                }
	                break;
	        }
	    }
	    $field = intval($field);
	    $id = intval($id);
	    switch (static::$list_ui_class_name) {
	        case 'list_indexation_fields_ui':
	            global $indexation_fields_ui_entity_type;
	            if (!empty($indexation_fields_ui_entity_type)) {
	                $entity_type = $indexation_fields_ui_entity_type;
	                list_indexation_fields_ui::set_indexation_name(entities::get_string_from_const_type($entity_type));
	                
	            } else {
	                list_indexation_fields_ui::set_indexation_name($name);
	            }
	            return new static::$list_ui_class_name(array('entity_type' => $entity_type));
	        case 'list_indexation_entities_ui':
	            global $indexation_entities_ui_entity_type;
	            if (!empty($indexation_entities_ui_entity_type)) {
	                $entity_type = $indexation_entities_ui_entity_type;
	                list_indexation_entities_ui::set_indexation_name(entities::get_string_from_const_type($entity_type));
	            } else {
	                list_indexation_entities_ui::set_indexation_name($name);
	            }
	            return new static::$list_ui_class_name(array('entity_type' => $entity_type, 'field' => $field, 'id' => $id));
	        default:
	            return new static::$list_ui_class_name();
	    }
	}
}