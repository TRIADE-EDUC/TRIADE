<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_level1.inc.php,v 1.9 2024/01/04 08:58:14 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path;
global $autolevel1, $segment_id;

require_once($class_path."/level1_search.class.php");
require_once($class_path."/facette_search.class.php");
require_once ("$base_path/includes/error_report.inc.php");
require_once($class_path."/search_universes/search_segment.class.php");

//On empêche l'abyme...
if ($autolevel1) { 
	$autolevel1=0; 
	$mode="tous";
}

if (isset($segment_id) && $segment_id) {
    $segment = new search_segment($segment_id);
    print $segment->get_rebound_form(); 
} else {
    //Recherches du niveau 1
    $level1=new level1_search();
    $nbresults=$level1->make_search();
    
    $n=$_SESSION["nb_queries"];
    $_SESSION["level1".$n]=(!empty($_SESSION["level1"]) ? $_SESSION["level1"] : '');
    $_SESSION["lq_level1"]=(!empty($_SESSION["level1"]) ? $_SESSION["level1"] : '');
    
    //On génère le bloc !
    $result=facettes::do_level1();
    
    session_write_close();
    
    print encoding_normalize::utf8_normalize($result);
}
