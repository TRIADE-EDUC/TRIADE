<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_search.inc.php,v 1.6 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($type)) $type = '';

session_write_close();

require_once($class_path."/affiliate_search.class.php");

switch($type){
	case "extended" :
		$as=new affiliate_search_extended($user_query);
		break;
	case "title" :
		$as=new affiliate_search_title($user_query);
		break;
	case "author" :
		$as=new affiliate_search_author($user_query,$search_type);
		break;
	case "collection" :
		$as=new affiliate_search_collection($user_query,$search_type);
		break;	
	case "subcollection" :
		$as=new affiliate_search_subcollection($user_query,$search_type);
		break;	
	case "category" :
		$as=new affiliate_search_category($user_query,$search_type);
		break;
	case "abstract" :
		$as=new affiliate_search_abstract($user_query);
		break;	
	case "keywords" :
		$as=new affiliate_search_keywords($user_query);
		break;
	case "indexint" :
		$as=new affiliate_search_indexint($user_query,$search_type);
		break;	
	case "titre_uniforme" :
		$as=new affiliate_search_titre_uniforme($user_query,$search_type);
		break;	
	case "publisher" :
		$as=new affiliate_search_publisher($user_query,$search_type);
		break;	
	case "concept" :
		$as=new affiliate_search_concept($user_query,$search_type);
		break;	
	case "all" :	
	default :
		$as=new affiliate_search_all($user_query);
		break;
}
$as->makeSearch();

if(!isset($wanted)) $wanted = '';
switch($wanted){
	case "results":
		$as->getResults();
		$return = array(
			'nb_results' => $as->getNbResults(),
			'results' => $as->results
		);
		break;
	default :
		$return = array(
			'nb_results' => $as->getNbResults()
		);
		break;
}
$return['affiliate_tabLabel'] = $msg['in_affiliate_source'];
$return['any_results_msg'] = $msg['affiliate_source_any_results'];

//On renvoie du JSON dans le charset de PMB...
if(!isset($debugtest) || !$debugtest){
	header("Content-Type:application/json; charset=$charset");
	$return = charset_pmb_normalize($return);
	print json_encode($return);
}else{
	highlight_string(print_r($return,true));
}

function charset_pmb_normalize($mixed){
	global $charset;
	$is_array = is_array($mixed);
	$is_object = is_object($mixed);
	if($is_array || $is_object){
		foreach($mixed as $key => $value){
			 if($is_array) $mixed[$key]=charset_pmb_normalize($value);
			 else $mixed->$key=charset_pmb_normalize($value);
		}
	}elseif ($charset!="utf-8") {
		$mixed =encoding_normalize::utf8_normalize($mixed);	
	} 
	return $mixed;
}
?>