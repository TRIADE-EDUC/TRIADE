<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_comparator.php,v 1.1 2017-04-07 12:38:06 apetithomme Exp $

$base_path="../..";
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]"; 
$base_use_dojo = 1;   


require_once $base_path.'/includes/init.inc.php';
require_once $class_path.'/analyse_query.class.php';

// require_once $class_path.'/searcher/searcher_sphinx_ql.class.php';
// require_once $class_path.'/searcher/searcher_sphinx_authorities.class.php';
// require_once $class_path.'/searcher/searcher_sphinx_records.class.php';


// require_once $class_path.'/searcher/searcher_factory.class.php';

$test_query = '';
$mode = 'records';
if(isset($_GET['mode'])){
	$mode =	$_GET['mode'];
}
if(isset($_GET['user_query'])){
	$test_query = stripslashes($_GET['user_query']);
}

$modes = array(
	'records',
	'authors',
	'titres_uniformes'	
);

$msg['records'] = "Notices";
$msg['authors'] = "Auteurs";
$msg['titres_uniformes'] = "Oeuvres";




print '
<div id="navbar">
	<ul>';
for($i=0 ; $i< count($modes) ; $i++){
	$current = false;
	if($mode == $modes[$i]){
		$current = true;
	}
	print '
		<li '.($current ? 'class="current"' : "").'><a '.($current ? 'class="current"' : "").' href="?mode='.$modes[$i].'">'.$msg[$modes[$i]].'</a></li>';
}
print '
	</ul>
</div>';
print '
<div>		
	<form action="" class="form-sphinx" method="get">
		<h3>Comparaison entre la recherche Native et Sphinx</h3>
		<div class="form-contenu">
			<label for="user_query">Rechercher : </label>
			<input type="text" name="user_query" value="'.addslashes(htmlentities($test_query, ENT_QUOTES, $charset)).'">
		</div>
		<div class="row">
			<div class="left">
				<input type="hidden" name="mode" value="'.addslashes(htmlentities($mode,ENT_QUOTES,$charset)).'"/>
				<input class="bouton" type="submit" value="Lancer la recherche"/>
			</div>
		</div>
		<div class="row"></div>
	</form>
</div>	
';
if($test_query){
	switch($mode){
		case 'records' :
			$ss = new searcher_sphinx($test_query);
			$sn = new searcher_records_all_fields($test_query);
// 			$sn = new searcher_records_title($test_query);
			break;
		case 'authors' :
			require_once($class_path.'/searcher/searcher_sphinx_authors.class.php');
			$ss = new searcher_sphinx_authors($test_query);
			$sn = new searcher_authorities_authors($test_query);
			break;
		case 'titres_uniformes' : 
			$ss = new searcher_sphinx_titres_uniformes($test_query,7);
			$sn = new searcher_authorities_titres_uniformes($test_query);
			break;
		case 'categories' : 
			$ss = new searcher_sphinx_authorities($test_query,2);
			$sn = new searcher_authorities_categories($test_query);
			break;
		case 'publishers' : 
			$ss = new searcher_sphinx_authorities($test_query,3);
			$sn = new searcher_authorities_publishers($test_query);
			break;
		case 'collections' : 
			$ss = new searcher_sphinx_authorities($test_query,4);
			$sn = new searcher_authorities_collections($test_query);
			break;
		case 'subcollections' : 
			$ss = new searcher_sphinx_authorities($test_query,5);
			$sn = new searcher_authorities_subcollections($test_query);
			break;
		case 'series' : 
			$ss = new searcher_sphinx_authorities($test_query,6);
			$sn = new searcher_authorities_series($test_query);
			break;
		case 'indexint' : 
			$ss = new searcher_sphinx_authorities($test_query,8);
			$sn = new searcher_authorities_indexint($test_query);
			break;
		case 'authpersos' : 
			$ss = new searcher_sphinx_authorities($test_query,9);
			$sn = new searcher_authorities_authpersos($test_query);
			break;
		case 'concept' : 
			$ss = new searcher_sphinx_authorities($test_query,10);
			$sn = new searcher_authorities_concepts($test_query);
			break;
		case 'authorities' : 
			$ss = new searcher_sphinx_authorities($test_query);
			$sn = new searcher_autorities($test_query);
			break;

	}
	if($sn && $ss){
		$ss->explain();
		$sn->explain($mode);
	}
}