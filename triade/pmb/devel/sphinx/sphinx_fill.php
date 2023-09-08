<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_fill.php,v 1.11 2018-01-22 15:05:38 tsamson Exp $

$base_path= __DIR__.'/../..';
$base_noheader = 1; 
$base_nocheck = 1;
$base_nobody = 1; 
$base_nosession = 1;

$_SERVER['REQUEST_URI'] = '';

require_once $base_path.'/includes/init.inc.php';
require_once $class_path.'/parametres_perso.class.php';

require_once $class_path.'/sphinx/sphinx_records_indexer.class.php';
require_once $class_path.'/sphinx/sphinx_titres_uniformes_indexer.class.php';

require_once 'progress_bar.php';

if(count($argv) == 1){
	$entities = array(
			'records',
			'titres_uniformes',
			'series',
			'categories',
			'collections',
			'subcollections',
			'authperso',
			'indexint',
			'authors',
			'concepts',
			'explnums',
			'publishers'
	);
}else{
	$entities = $argv;
}

$flag = false;
foreach($entities as $entity){
	if(class_exists('sphinx_'.$entity.'_indexer')){
		$flag = true;
		$index_class = 'sphinx_'.$entity.'_indexer';
		$sconf = new $index_class();
		print $sconf->fillIndex();		
	}
}

if (!$flag) {
	print 'Aucune entite connue. ("records", "titres_uniformes", "series", "categories", "collections", "subcollections", "authperso", "indexint", "authors", "concepts", "explnums", "publishers")';
}