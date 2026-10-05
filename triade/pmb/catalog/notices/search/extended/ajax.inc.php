<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.inc.php,v 1.8 2019/06/07 08:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $search_xml_file, $search_xml_file_full_path;

require_once($class_path."/search.class.php");

if(!isset($search_xml_file)) $search_xml_file = '';
if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';

$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
$sc->proceed_ajax();

