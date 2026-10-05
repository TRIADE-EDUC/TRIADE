<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_benchmark.php,v 1.1.16.1 2024/10/17 08:22:51 rtigero Exp $

$base_path = "../..";
$base_auth = "ADMINISTRATION_AUTH";
$base_title = "\$msg[7]";
$base_use_dojo = 1;


require_once $base_path . '/includes/init.inc.php';

session_write_close();
require_once $class_path . '/analyse_query.class.php';


$test_query = '';
$entity_type = 'notices';
if (isset($_GET['entity_type'])) {
	$entity_type =	$_GET['entity_type'];
}
if (isset($_GET['user_query'])) {
	$test_query = stripslashes($_GET['user_query']);
}
$engine = 'sphinx';
if (isset($_GET['engine'])) {
	$engine =	$_GET['engine'];
}
$display = true;
if (isset($_GET['display'])) {
	$display = stripslashes($_GET['display']);
}

$modes = array(
	'notices',
	'authors',
	'titres_uniformes',
	'categories',
	'publishers',
	'collections',
	'subcollections',
	'series',
	'indexint',
	'ontology_skos_menu',
	'authperso'
);

print '
<div id="navbar">
	<ul>';
for ($i = 0; $i < count($modes); $i++) {
	$current = false;
	if ($entity_type == $modes[$i]) {
		$current = true;
	}
	print '
		<li ' . ($current ? 'class="current"' : "") . '><a ' . ($current ? 'class="current"' : "") . ' href="?entity_type=' . $modes[$i] . '">' . $msg[$modes[$i]] . '</a></li>';
}
print '
	</ul>
</div>';

$id_authperso_form = "";
if ($entity_type == "authperso") {
	$id_authperso_form = '
		<div class="form-contenu">
			<label for="user_query">Identifiant de l\'authperso : </label>
			<input type="text" name="id_authperso" value="' . addslashes(htmlentities($id_authperso, ENT_QUOTES, $charset)) . '">
		</div>';
}

print '
<div>
	<form action="" class="form-sphinx" method="get">
		<h3>Comparaison entre la recherche Native et Sphinx</h3>
		<div class="form-contenu">
			<label for="user_query">Rechercher : </label>
			<input type="text" name="user_query" value="' . addslashes(htmlentities($test_query, ENT_QUOTES, $charset)) . '">
		</div>
		' . $id_authperso_form . '
		<div class="row">
			<div class="left">
				<input type="hidden" name="entity_type" value="' . addslashes(htmlentities($entity_type, ENT_QUOTES, $charset)) . '"/>
				<input type="hidden" name="engine" value="' . addslashes(htmlentities($engine, ENT_QUOTES, $charset)) . '"/>
				<input type="hidden" name="display" value="' . addslashes(htmlentities($display, ENT_QUOTES, $charset)) . '"/>
				<input class="bouton" type="submit" value="Lancer la recherche"/>
			</div>
		</div>
		<div class="row"></div>
	</form>
</div>
';
if ($test_query) {
	if ($engine == 'sphinx') {
		switch ($entity_type) {
			case 'notices':
				$benchmark = new searcher_sphinx_records($test_query);
				break;
			case 'authors':
				$benchmark = new searcher_sphinx_authors($test_query);
				break;
			case 'titres_uniformes':
				$benchmark = new searcher_sphinx_titres_uniformes($test_query);
				break;
			case 'categories':
				$benchmark = new searcher_sphinx_categories($test_query);
				break;
			case 'publishers':
				$benchmark = new searcher_sphinx_publishers($test_query);
				break;
			case 'collections':
				$benchmark = new searcher_sphinx_collections($test_query);
				break;
			case 'subcollections':
				$benchmark = new searcher_sphinx_subcollections($test_query);
				break;
			case 'series':
				$benchmark = new searcher_sphinx_series($test_query);
				break;
			case 'indexint':
				$benchmark = new searcher_sphinx_indexint($test_query);
				break;
			case 'authperso':
				$benchmark = new searcher_sphinx_authperso($test_query, $id_authperso);
				break;
			case 'ontology_skos_menu':
				$benchmark = new searcher_sphinx_concepts($test_query);
				break;
			case 'authorities':
				$benchmark = new searcher_sphinx_authorities($test_query);
				break;
		}
	} else {
		switch ($entity_type) {
			case 'notices':
				$benchmark = new searcher_records_all_fields($test_query);
				break;
			case 'authors':
				$benchmark = new searcher_authorities_authors($test_query);
				break;
			case 'titres_uniformes':
				$benchmark = new searcher_authorities_titres_uniformes($test_query);
				break;
			case 'categories':
				$benchmark = new searcher_authorities_categories($test_query);
				break;
			case 'publishers':
				$benchmark = new searcher_authorities_publishers($test_query);
				break;
			case 'collections':
				$benchmark = new searcher_authorities_collections($test_query);
				break;
			case 'subcollections':
				$benchmark = new searcher_authorities_subcollections($test_query);
				break;
			case 'series':
				$benchmark = new searcher_authorities_series($test_query);
				break;
			case 'indexint':
				$benchmark = new searcher_authorities_indexint($test_query);
				break;
			case 'authperso':
				$benchmark = new searcher_authorities_authpersos($test_query);
				break;
			case 'ontology_skos_menu':
				$benchmark = new searcher_authorities_concepts($test_query);
				break;
			case 'authorities':
				$benchmark = new searcher_autorities($test_query);
				break;
		}
	}
	$benchmark->explain($display, $entity_type, false);
}
