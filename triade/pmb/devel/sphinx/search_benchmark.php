<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_benchmark.php,v 1.1 2017-04-24 09:26:37 arenou Exp $

$base_path="../..";
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]"; 
$base_use_dojo = 1;   


require_once $base_path.'/includes/init.inc.php';

session_write_close();
require_once $class_path.'/analyse_query.class.php';


$test_query = '';
$mode = 'records';
if(isset($_GET['mode'])){
	$mode =	$_GET['mode'];
}
if(isset($_GET['user_query'])){
	$test_query = stripslashes($_GET['user_query']);
}
$engine = 'sphinx';
if(isset($_GET['engine'])){
	$engine =	$_GET['engine'];
}
$display = true;
if(isset($_GET['display'])){
	$display = stripslashes($_GET['display']);
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
				<input type="hidden" name="engine" value="'.addslashes(htmlentities($engine,ENT_QUOTES,$charset)).'"/>
				<input type="hidden" name="display" value="'.addslashes(htmlentities($display,ENT_QUOTES,$charset)).'"/>
				<input class="bouton" type="submit" value="Lancer la recherche"/>
			</div>
		</div>
		<div class="row"></div>
	</form>
</div>	
';
if($test_query){
	if ($engine == 'sphinx') {
		switch($mode){
			case 'records' :
				$benchmark = new searcher_sphinx($test_query);
				break;
			case 'authors' :
	
				$benchmark = new searcher_sphinx_authors($test_query);
				break;
			case 'titres_uniformes' : 
				$benchmark = new searcher_sphinx_titres_uniformes($test_query,7);
				break;
			case 'categories' : 
				$benchmark = new searcher_sphinx_authorities($test_query,2);
				break;
			case 'publishers' : 
				$benchmark = new searcher_sphinx_authorities($test_query,3);
				break;
			case 'collections' : 
				$benchmark = new searcher_sphinx_authorities($test_query,4);
				break;
			case 'subcollections' : 
				$benchmark = new searcher_sphinx_authorities($test_query,5);
				break;
			case 'series' : 
				$benchmark = new searcher_sphinx_authorities($test_query,6);
				break;
			case 'indexint' : 
				$benchmark = new searcher_sphinx_authorities($test_query,8);
				break;
			case 'authpersos' : 
				$benchmark = new searcher_sphinx_authorities($test_query,9);
				break;
			case 'concept' : 
				$benchmark = new searcher_sphinx_authorities($test_query,10);
				break;
			case 'authorities' : 
				$benchmark = new searcher_sphinx_authorities($test_query);
				break;
	
		}
	} else {
		switch($mode){
			case 'records' :
				$benchmark = new searcher_records_all_fields($test_query);
				break;
			case 'authors' :
				$benchmark = new searcher_authorities_authors($test_query);
				break;
			case 'titres_uniformes' : 
				$benchmark = new searcher_authorities_titres_uniformes($test_query);
				break;
			case 'categories' : 
				$benchmark = new searcher_authorities_categories($test_query);
				break;
			case 'publishers' : 
				$benchmark = new searcher_authorities_publishers($test_query);
				break;
			case 'collections' : 
				$benchmark = new searcher_authorities_collections($test_query);
				break;
			case 'subcollections' : 
				$benchmark = new searcher_authorities_subcollections($test_query);
				break;
			case 'series' : 
				$benchmark = new searcher_authorities_series($test_query);
				break;
			case 'indexint' : 
				$benchmark = new searcher_authorities_indexint($test_query);
				break;
			case 'authpersos' : 
				$benchmark = new searcher_authorities_authpersos($test_query);
				break;
			case 'concept' : 
				$benchmark = new searcher_authorities_concepts($test_query);
				break;
			case 'authorities' : 
				$benchmark = new searcher_autorities($test_query);
				break;
		}
	}
$benchmark->explain($display,$mode,false);
}