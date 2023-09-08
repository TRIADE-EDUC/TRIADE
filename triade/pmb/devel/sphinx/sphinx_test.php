<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_test.php,v 1.1 2016-05-03 13:01:25 arenou Exp $

$base_path="../..";
$base_noheader = 1; 
$base_nocheck = 1;
$base_nobody = 1; 
$base_nosession = 1;


require_once $base_path.'/includes/init.inc.php';
require_once($class_path.'/analyse_query.class.php');

require_once $class_path.'/searcher/searcher_sphinx.class.php';

$ss = new searcher_sphinx(stripslashes($_GET['query']));
$ss->explain();