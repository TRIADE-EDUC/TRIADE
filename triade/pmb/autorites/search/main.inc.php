<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $mode, $action;

require_once($class_path.'/searcher_tabs.class.php');

//onglets de recherche autorites
$searcher_tabs = new searcher_tabs('authorities');
$searcher_tabs->proceed($mode, $action);