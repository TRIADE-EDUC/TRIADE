<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.6 2022/11/22 10:13:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants
require_once($class_path.'/modules/module_modelling.class.php');
switch($categ):
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("modelling",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'contribution_area':
		if ($pmb_contribution_area_activate) {
			$module_modelling = new module_modelling();
			if(!isset($id)) {
			    $id = 0; 
			} else {
			    $id = intval($id);
			}
			$module_modelling->set_object_id($id);
			$module_modelling->proceed_ajax_contribution_area();
		}
		break;
	case 'computed_fields':
		$module_modelling = new module_modelling();
		$module_modelling->proceed_ajax_computed_fields();
		break;
	case 'extended_search' :
	    require_once($class_path."/search.class.php");
	    if(!isset($search_xml_file)) $search_xml_file = '';
	    if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
	    
	    $sc=new search(true, $search_xml_file, $search_xml_file_full_path);
	    $sc->proceed_ajax();
	    break;
	case 'check_pmbname' :
	    $ontology_id = intval($ontology_id);
	    if($ontology_id>0){
	       $ontology = new ontology($ontology_id);
	       $query = 'select ?pmbname where { 
                ?s pmb:name ?pmbname .
                filter (regex(?pmbname, "^'.$pmbname.'$"))
            }';
	       $result = $ontology->exec_onto_query($query);
	       if(is_array($result) && count($result)){
	           print encoding_normalize::json_encode(['state'=>'ko']);
	       }else{
	           print encoding_normalize::json_encode(['state'=>'ok']);
	       }
	    }else{
	        print encoding_normalize::json_encode(['state'=>'ko']);
	    }
	    break;
	default:
		break;		
endswitch;
