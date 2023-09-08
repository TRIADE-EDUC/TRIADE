<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_conf.php,v 1.10 2017-07-25 15:27:41 vtouchard Exp $

$base_path= __DIR__.'/../..';
$base_noheader = 1; 
$base_nocheck = 1;
$base_nobody = 1; 
$base_nosession = 1;
//ini_set('log_errors', 1);
//ini_set('error_log', '/tmp/pmb_log');

$_SERVER['REQUEST_URI'] = '';

require_once $base_path.'/includes/init.inc.php';
require_once $class_path.'/parametres_perso.class.php';
require_once $class_path.'/sphinx/sphinx_indexer.class.php';

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


