<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2022/11/25 14:59:27 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($categ) {
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("semantic",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'grid' :
	    grid::proceed($datas);
	    break;
    case 'extended_search':
        if(!isset($search_xml_file)) $search_xml_file = 'search_field_ontology';
        $ontology =  new ontology($ontology_id);//::get_ontology_by_pmbname($ontoname);
        $sc=new search_ontology(true, $search_xml_file,'',$ontology->get_handler()->get_ontology());
        $sc->proceed_ajax();
        break;
	default:
		break;		
}