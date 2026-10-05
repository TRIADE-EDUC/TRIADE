<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.49.2.1.2.1 2025/01/24 13:29:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $categ, $plugin, $sub, $class_path, $search_xml_file, $search_xml_file_full_path;

//En fonction de $categ, il inclut les fichiers correspondants

switch ($categ) {
	case 'misc':
		include('./ajax/misc/misc.inc.php');
		break;
	case 'alert':
		include('./ajax/misc/alert.inc.php');
		break;
	case 'dashboard':
		include('./ajax/misc/dashboard.inc.php');
		break;
	case 'menuhide':
		include('./ajax/misc/menuhide.inc.php');
		break;
	case 'tri':
		include('./ajax/misc/tri.inc.php');
		break;
	case 'chklnk':
		include('./ajax/misc/chklnk.inc.php');
		break;
	case 'isbn':
		include('./ajax/misc/isbn.inc.php');
		break;
	case 'planificateur':
		include('./ajax/misc/planificateur.inc.php');
		break;
	case 'expand':
		include('./ajax/misc/expand_ajax.inc.php');
		break;
	case 'expand_block':
		include('./ajax/misc/expand_block_ajax.inc.php');
		break;
	case 'mailtpl':
		include('./ajax/misc/mailtpl.inc.php');
		break;
	case 'user':
		include('./ajax/misc/user.inc.php');
		break;
	case 'storage' :
		include('./ajax/misc/storage.inc.php');
		break;
	case 'map' :
		include('./ajax/misc/map.inc.php');
		break;
	case 'notice' :
		include('./ajax/misc/notice.inc.php');
		break;
	case 'nomenclature' :
		include('./ajax/misc/nomenclature.inc.php');
		break;
	case 'messages':
		include('./ajax/misc/messages.inc.php');
		break;
	case 'images':
		include('./ajax/misc/images.inc.php');
		break;
	case 'classementGen':
		include('./ajax/misc/classementGen.inc.php');
		break;
	case 'session' :
		include('./ajax/misc/session.inc.php');
		break;
	case 'extend' :
		if(file_exists('./ajax/misc/extend.inc.php')){
			include('./ajax/misc/extend.inc.php');
		}
		break;
	case 'sticks_sheets' :
		include('./ajax/misc/sticks_sheets.inc.php');
		break;
	case 'facettes' :
		include('./ajax/misc/facette.inc.php');
		break;
	case 'facettes_external' :
		include('./ajax/misc/facettes_external.inc.php');
		break;
	case 'entity_graph':
		include('./ajax/misc/entity_graph.inc.php');
		break;
	case 'visits_statistics' :
		include('./ajax/misc/visits_statistics.inc.php');
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("ajax",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'notice_tpl':
		include('./ajax/misc/notice_tpl.inc.php');
		break;
	case 'calendrier':
		include('./ajax/misc/calendrier.inc.php');
		break;
	case 'contribution':
		require_once('./ajax/misc/contribution.inc.php');
		break;
	case 'extended_search' :
		require_once($class_path."/search.class.php");
	
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
		if(!empty($ontology_id)) {
		    $ontology =  new ontology(intval($ontology_id));//::get_ontology_by_pmbname($ontoname);
		    $sc=new search_ontology(true, $search_xml_file,'',$ontology->get_handler()->get_ontology());
		}else{
            $sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		}
		$sc->proceed_ajax();
		break;
	case 'indexation':
		session_write_close();
		require_once('./ajax/misc/indexation.inc.php');
		break;
	case 'list':
	    require_once './ajax/misc/list.inc.php';
		break;
	case 'translations':
		include('./ajax/misc/translations.inc.php');
		break;
	case 'pnb':
		include('./ajax/misc/pnb.inc.php');
		break;
	case 'chat':
	    include('./ajax/misc/chat.inc.php');
	    break;
	case 'entity_locking':
	    require_once('./ajax/misc/entity_locking.inc.php');
	    break;
	case 'concepts_selector' :
	    require_once './ajax/misc/concepts_selector.inc.php';
	    break;
    case 'entities' :
    	require_once './ajax/misc/entities.inc.php';
    	break;
    case 'empr':
        require_once './ajax/misc/empr.inc.php';
        break;
    case 'connector_in' :
    	require_once './ajax/misc/connector_in.inc.php';
    	break;
	case 'aceEditorCompletion' :
    	require_once './ajax/misc/ace_editor_completion.inc.php';
        break;
	case 'folders_selector' :
	    require_once './ajax/misc/folders_selector.inc.php';
	    break;
	case 'search_universes' :
	    require_once './ajax/misc/search_universes.inc.php';
	    break;
	case 'elements_list':
	    require_once './ajax/misc/elements_list.inc.php';
	    break;
    default:
		break;
}