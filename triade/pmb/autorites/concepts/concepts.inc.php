<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concepts.inc.php,v 1.4 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $aut_concepts_menu, $msg, $sub, $page, $limit, $object_uri, $uri, $action;

require_once("$class_path/rdf/ontology.class.php");
require_once("$include_path/templates/concepts.tpl.php");

//construction du sous-menu
$aut_concepts_menu = str_replace('!!menu_sous_rub!!', $msg['ontology_skos_'.$sub], $aut_concepts_menu);
$tpl = $aut_concepts_menu;

$op = new ontology_parser("$class_path/rdf/skos_pmb.rdf");
$sh = new skos_handler($op);

$page = intval($page);
if(!$page) $page=1;	
$limit=$nb_per_page_gestion;

$object_uri='';
if ($uri) {
	$object_uri=rawurldecode(stripslashes($uri));
}


switch ($action) {
	
	case 'edit' :
	case 'add' :
		switch ($sub) {
			case 'concept' :
				$t = array(	'object'		=> 'skos:Concept',
							'object_uri'	=> $object_uri,
							
							'url_base'		=> './autorites.php?categ=concepts&sub=concept&action=',
				);
				break;
			case 'collection' :
				$t = array(	'object'		=> 'skos:Collection',
							'object_uri'	=> $object_uri,
							
							'url_base'		=> './autorites.php?categ=concepts&sub=collection&action=',
				);
				break;
			case 'orderedcollection' :
				$t = array(	'object'		=> 'skos:OrderedCollection',
							'object_uri'	=> $object_uri,
							
							'url_base'		=> './autorites.php?categ=concepts&sub=orderedcollection&action=',
				);
				break;
			case 'conceptscheme' :
				$t = array(	'object'		=> 'skos:ConceptScheme',
							'object_uri'	=> $object_uri,
							
							'url_base'		=> './autorites.php?categ=concepts&sub=conceptscheme&action=',
				);
				break;
		}
		$sh->showform($t);
		$tpl.= $sh->result;
		break;
	
	case 'update' :
		switch ($sub) {
			case 'concept' :
				break;
			case 'collection' :
				break;
			case 'orderedcollection' :
				break;
			case 'conceptscheme' :
				break;
		}
		$sh->recform($t);
		break;
		
	case 'delete' :
		switch ($sub) {
			case 'concept' :
				break;
			case 'collection' :
				break;
			case 'orderedcollection' :
				break;
			case 'conceptscheme' :
				break;
		}
		$sh->delform($t);
		break;
	
	default :
		switch ($sub) {
			case 'concept' :
				$t = array(	'object'		=> 'skos:Concept',
				
							'page'			=> $page,
							'limit'			=> $limit,
							
							'url_base'		=> './autorites.php?categ=concepts&sub=concept&action=',
				);
				break;
			case 'collection' :
				$t = array(	'object'		=> 'skos:Collection',
				
							'page'			=> $page,
							'limit'			=> $limit,
								
							'url_base'		=> './autorites.php?categ=concepts&sub=collection&action=',
							);
				break;
			case 'orderedcollection' :
				$t = array(	'object'		=> 'skos:OrderedCollection',
				
							'page'			=> $page,
							'limit'			=> $limit,
		
							'url_base'		=> './autorites.php?categ=concepts&sub=orderedcollection&action=',
							);
				break;
			case 'conceptscheme' :
			default :
				$t = array(	'object'		=>	'skos:ConceptScheme',
				
							'page'			=> $page,
							'limit'			=> $limit,
								
							'url_base'		=> './autorites.php?categ=concepts&sub=conceptscheme&action=',
		 					);
				break;
		}
		$sh->showlist($t);
		$tpl.= $sh->result;
		break;
}

print $tpl;


//print "<br />----------------------------------------------------------<br />";


// print "--> Concept<br />";
// $t = array(	'object'=>'skos:Concept',
// 			'limit'=>5,
// );
// $sh->genlist($t);
// print "<br />----------------------------------------------------------<br />";


// print "--> Collection<br />";
// $t = array(	'object'=>'skos:Collection',
// 		'limit'=>5,
// );
// $sh->genlist($t);
// print "<br />----------------------------------------------------------<br />";


// print "--> OrderedCollection<br />";
// $t = array(	'object'=>'skos:OrderedCollection',
// 		'limit'=>5,
// );
// $sh->genlist($t);
// print "<br />----------------------------------------------------------<br />";


//$sh->genlist(array('object'=>'skos:Concept'));

// $oh->get_list('skos:ConceptScheme');

// print '<br /><br /><br />';
// print "--> resources : <br />";
// highlight_string(print_r($op->t_resources,true));

// print "--> properties : <br />";
// highlight_string(print_r($op->t_properties,true));

// print "--> objects : <br />";
// highlight_string(print_r($op->t_objects,true));

// print "--> nodeids : <br />";
// highlight_string(print_r($op->t_nodeids,true));

// foreach($r as $k=>$v) {
// 	print 'ConceptScheme : '.$v.'<br /><br />';
	
// 	print 'Liste Concept<br />';
// 	$r1 = $ss->list_Concept($v,10); 
// 	if (count($r1)) {
// 		highlight_string(print_r($r1,true));
// 	}
// 	print '<br /><br />';
	
// 	print 'Liste Collection<br />';
// 	$r2 = $ss->list_Collection($v);
// 	if(count($r2)) {
// 		highlight_string(print_r($r2,true));
// 	}
// 	print '<br /><br />';
	
// 	print 'Liste OrderedCollection<br />';
// 	$r3 = $ss->list_OrderedCollection($v);
// 	if (count($r3)) {
// 		highlight_string(print_r($r3,true));
// 	}
// 	print '<br /><br />';
	
// }


