<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.52 2024/01/03 11:24:14 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $opac_scan_request_activate, $allow_scan_request;

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'misc':
		include('./ajax/misc/misc.inc.php');
		break;
	case 'liste_lecture' :
		include('./ajax/ajax_liste_lecture.inc.php');
		break;
	case 'demandes' :
		include('./ajax/ajax_demandes.inc.php');
		break;
	case "enrichment" :
		include("./ajax/ajax_enrichment.inc.php"); 
		break;
	case "search" :
		include("./ajax/ajax_search.inc.php"); 
		break;
	case "perio_a2z" :
		include("./ajax/perio_a2z.inc.php"); 
		break;
	case "avis" :
		include("./ajax/avis.inc.php"); 
		break;
	case "simili" :
		include("./ajax/simili_search.inc.php"); 
		break;
	case "level1" :
		include("./ajax/ajax_level1.inc.php"); 
		break;
	case "expl_voisin" :
		include("./ajax/expl_voisin.inc.php"); 
		break;
	case 'facettes':
		include('./ajax/facette.inc.php');
		break;
	case 'facettes_external':
		include('./ajax/facettes_external.inc.php');
		break;
	case 'print_docnum':
		include('./ajax/print_docnum.inc.php');
		break;
	case 'explnum_associate':
		include('./ajax/explnum_associate.inc.php');
		break;
	case "auth":
		require_once($class_path."/auth_popup.class.php");
		//print $popup_header;
		$auth_popup = new auth_popup();
		$auth_popup->process();
		break;
	//abacarisse
	case 'param_social_network' :
		include('./ajax/ajax_param_social_network.inc.php');
		break;
	//abacarisse
	case "extend" :
		if(file_exists("./ajax/misc/extend.inc.php")) include("./ajax/misc/extend.inc.php");
		break;
	case 'sort' :
		include('./ajax/sort.inc.php');
		break;
	case 'map' :
		include('./ajax/misc/map.inc.php');
		break;
	case 'notice' :
		include('./ajax/misc/notice.inc.php');
		break;
	case 'messages':
		include('./ajax/misc/messages.inc.php');
		break;
	case 'images':
		include('./ajax/misc/images.inc.php');
		break;
	case 'storage' :
		include('./ajax/storage.inc.php');
		break;
	case 'download_docnum':
		include('./ajax/download_docnum.inc.php');
		break;
	case 'log':
		include('./ajax/log.inc.php');
		break;
	case 'sugg':
		include('./ajax/sugg.inc.php');
		break;
	case 'scan_requests':
		if($_SESSION['id_empr_session'] && $opac_scan_request_activate && $allow_scan_request) {
			include('./ajax/ajax_scan_requests.inc.php');
		}
		break;
	case 'contact_form':
		include('./ajax/ajax_contact_form.inc.php');
		break;
	case 'extended_search':
		require_once('./ajax/ajax_extended_search.inc.php');
		break;
	case 'grid':
		require_once('./ajax/ajax_grid.inc.php');
		break;
	case 'contribution':
		require_once('./ajax/ajax_contribution.inc.php');
		break;
	case 'cart':
		require_once('./ajax/cart.inc.php');
		break;
	case 'entity_graph':
		include('./ajax/entity_graph.inc.php');
		break;
	case 'explnum':
		include('./ajax/explnum.inc.php');
		break;
	case 'search_universes':
		include('./ajax/search_universes.inc.php');
		break;
	case 'search_segment':
		include('./ajax/search_segment.inc.php');
		break;
	case 'pnb':
		include('./ajax/pnb.inc.php');
		break;
	case 'empr' :
		require_once './ajax/ajax_empr.inc.php';
		break;
	case 'connector_in' :
		require_once './ajax/misc/connector_in.inc.php';
		break;
	case 'chklnk':
	    include('./ajax/misc/chklnk.inc.php');
	    break;
	case 'get_notice_form_vedette':
	    include './ajax/notice_form_vedette.inc.php';
	    break;
	case 'session':
	    include './ajax/session.inc.php';
	    break;
	case "search_field" :
	    include './ajax/search/search_field.inc.php';
	    break;
	case "authentication":
		require_once './ajax/ajax_authentication.inc.php';
		break;
	case 'list':
		include('./ajax/misc/list.inc.php');
		break;
	case "collstate":
	    require_once './ajax/ajax_collstate.inc.php';
	    break;
	case "payments":
	    require_once './ajax/ajax_payments.inc.php';
	    break;
	default:
		break;
endswitch;
