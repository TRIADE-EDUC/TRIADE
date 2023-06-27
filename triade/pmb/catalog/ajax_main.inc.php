<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.25 2018-10-12 11:59:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ) {
	case 'collections_state':
		include('./catalog/serials/ajax/collections_state.inc.php');
	break;
	case 'caddie':
		include('./catalog/caddie/caddie_ajax.inc.php');
	break;
	case 'expand':
		include('./catalog/notices/search/expand_ajax.inc.php');
	break;
	case 'expand_block':
		include('./catalog/notices/search/expand_block_ajax.inc.php');
	break;	
	case 'avis':
		include('./catalog/notices/avis_ajax.inc.php');
	break;
	case 'explnum':
		include('./catalog/explnum/explnum_ajax.inc.php');
	break;
	case 'serialcirc_diff':
		include('./catalog/serialcirc_diff/serialcirc_diff_ajax.inc.php');
	break;
	case 'search_params':
		include('./catalog/notices/search/search_ajax.inc.php');
		break;
	case 'force_integer':
		include('./catalog/notices/search/external/ajax_integer.inc.php');
		break;
	case 'debloque_source':
		include('./catalog/notices/search/external/ajax_source.inc.php');
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;	
	case 'fill_form' :
		include("./catalog/fill_form/ajax_main.inc.php");
		break;	
	case 'get_notice_form_vedette':
		include('./catalog/notices/notice_form_vedette.inc.php');
		break;
	case 'get_expl_cb':
		require_once($class_path."/expl.class.php");
		$exemplaire = new exemplaire($id);
		print $exemplaire->gen_cb();
		break;
	case 'grid' :
		require_once($class_path."/grid.class.php");
		grid::proceed($datas);
		break;
	case 'extended_search' :
		require_once("./catalog/notices/search/extended/ajax.inc.php");
		break;
	case 'search_perso':
		require_once($class_path."/search_perso.class.php");
		$search_p= new search_perso(0, 'RECORDS');
		$search_p->proceed_ajax();
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("catalog",$plugin,$sub);
		if($file){
			include $file;
		}
		break;	
	default:
	//tbd
	break;		
}