<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_test.php,v 1.1.18.1 2024/10/17 08:22:51 rtigero Exp $

$base_path = __DIR__ . '/../..';
$class_path = $base_path . '/classes';
$include_path = $base_path . '/includes';
$base_noheader = 1;
$base_nocheck = 1;
$base_nobody = 1;
$base_nosession = 1;

$_SERVER['REQUEST_URI'] = '';
$_SERVER['HTTP_USER_AGENT'] = '';

require_once $include_path . '/init.inc.php';

//On inclut les messages pour éviter les warnings dans les inclusions de fichiers qui utilisent $msg
global $msg, $lang;

$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

require_once($class_path . '/analyse_query.class.php');

require_once $class_path . '/searcher/searcher_sphinx.class.php';

$test_query = stripslashes($_GET['query']);
$mode = $_GET['mode'];

switch ($mode) {
    case 'records':
        $ss = new searcher_sphinx_records($test_query);
        break;
    case 'authors':
        $ss = new searcher_sphinx_authors($test_query);
        break;
    case 'titres_uniformes':
        $ss = new searcher_sphinx_titres_uniformes($test_query);
        break;
    case 'categories':
        $ss = new searcher_sphinx_categories($test_query);
        break;
    case 'publishers':
        $ss = new searcher_sphinx_publishers($test_query);
        break;
    case 'collections':
        $ss = new searcher_sphinx_collections($test_query);
        break;
    case 'subcollections':
        $ss = new searcher_sphinx_subcollections($test_query);
        break;
    case 'series':
        $ss = new searcher_sphinx_series($test_query);
        break;
    case 'indexint':
        $ss = new searcher_sphinx_indexint($test_query);
        break;
    case 'authpersos':
        if (! isset($_GET["id_authperso"])) {
            exit;
        }
        $ss = new searcher_sphinx_authperso($test_query, intval($_GET["id_authperso"]));
        break;
    case 'concept':
        $ss = new searcher_sphinx_concepts($test_query);
        break;
    case 'authorities':
        $ss = new searcher_sphinx_authorities($test_query);
        break;
    default:
        exit;
}

$ss->explain(true, $mode, true);
