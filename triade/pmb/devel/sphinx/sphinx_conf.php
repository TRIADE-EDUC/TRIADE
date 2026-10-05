<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_conf.php,v 1.10.14.1 2024/10/17 08:22:51 rtigero Exp $

$base_path = __DIR__ . '/../..';
$class_path = $base_path . '/classes';
$include_path = $base_path . '/includes';
$base_noheader = 1;
$base_nocheck = 1;
$base_nobody = 1;
$base_nosession = 1;
//ini_set('log_errors', 1);
//ini_set('error_log', '/tmp/pmb_log');

$_SERVER['REQUEST_URI'] = '';
$_SERVER['HTTP_USER_AGENT'] = '';

require_once $include_path . '/init.inc.php';

//On inclut les messages pour éviter les warnings dans les inclusions de fichiers qui utilisent $msg
global $msg, $lang;

$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

require_once $class_path . '/parametres_perso.class.php';
require_once $class_path . '/sphinx/sphinx_indexer.class.php';

$sconf = new sphinx_records_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_categories_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_titres_uniformes_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_publishers_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_authors_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_collections_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_subcollections_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_indexint_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_series_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_authperso_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_concepts_indexer();
print $sconf->getIndexConfFile();

$sconf = new sphinx_explnums_indexer();
print $sconf->getIndexConfFile();

// TODO FULLTEXT EXPLNUMS
