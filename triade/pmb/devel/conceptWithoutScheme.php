<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: conceptWithoutScheme.php,v 1.1 2023/11/07 14:42:54 qvarin Exp $
$base_path = __DIR__ . '/..';
$base_noheader = 1;
$base_nocheck = 0;
$base_nobody = 1;
$base_nosession = 1;

require_once $base_path . '/includes/init.inc.php';

/**
 * Retourne la liste des concepts sans schema
 *
 * @return skos_concept[]
 */
function getConceptWithoutScheme()
{
	$query = "select ?uri where {
		?uri rdf:type <http://www.w3.org/2004/02/skos/core#Concept> .
		optional {
			?uri <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
		}
		filter (!bound(?scheme))
	}";

	$concepts = [];
	skos_datastore::query($query);
	if (skos_datastore::num_rows()) {
		$results = skos_datastore::get_result();
		foreach ($results as $concept) {
			$concepts[] = new skos_concept(0, $concept->uri);
		}
	}

	return $concepts;
}

/**
 * Retourne le nombre de concept sans schema
 *
 * @return skos_concept[]
 */
function countConceptWithoutScheme()
{
	$query = "select count(?uri) as nb where {
		?uri rdf:type <http://www.w3.org/2004/02/skos/core#Concept> .
		optional {
			?uri <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
		}
		filter (!bound(?scheme))
	}";

	$count = 0;
	skos_datastore::query($query);
	if (skos_datastore::num_rows()) {
		$results = skos_datastore::get_result();
		$count = intval($results[0]->nb);
	}

	return $count;
}

/**
 * Retourne la liste des schema
 *
 * @return skos_concept[]
 */
function getSchemes()
{
	$query = "select ?uri where {
            ?uri rdf:type <http://www.w3.org/2004/02/skos/core#ConceptScheme> .
        }";

	$schemes = [];
	skos_datastore::query($query);
	if (skos_datastore::num_rows()) {
		$results = skos_datastore::get_result();
		foreach ($results as $scheme) {
			$schemes[] = new skos_concept(0, $scheme->uri);
		}
	}
	return $schemes;
}

print "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<link rel='SHORTCUT ICON' href='../images/favicon.ico'>
    <title>Concept sans sch&eacute;ma</title>
	<style>
		button {
			background-color: #2A3391;
			border: none;
			border-radius: 5px;
			color: white;
			padding: 7px 15px;
			margin: 0px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			cursor: pointer;
		}

		button:disabled {
			cursor: not-allowed;
		}

		select {
			border: none;
			border-radius: 5px;
			padding: 7px 15px;
			margin: 0px 5px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			cursor: pointer;
		}
	</style>
</head>
<body>";


$schemeSelected = intval($_GET['scheme'] ?? 0);
if ($schemeSelected <= 0) {

	$options = '';
	$option = '<option value="%s">%s</option>';
	foreach (getSchemes() as $scheme) {
		$options .= sprintf($option, $scheme->get_id(), $scheme->get_display_label());
	}

	$count = countConceptWithoutScheme();
	$disabledBtn = "";
	if ($count <= 0) {
		$disabledBtn = "disabled='disabled'";
	}

	print "<p>Ce script vous permet d'associer un sch&eacute;ma, pour les concepts qui n'en poss&egrave;dent pas.</p>";
	print "<blockquote>";
	print "<p>" . sprintf("Vous avez <strong>%s concept(s)</strong> sans sch&eacute;ma.", $count) . "</p>";
	print "<form action='' method='GET'>";

	if (!empty($options)) {
		print "<label>Lier le(s) concept(s) au sch&eacute;ma :" . "<label>";
		print "<select name='scheme'>{$options}<select>";
		print "<button type='submit' {$disabledBtn}>Envoyer</button>";
	} else {
		print "<p style='color:red;'>Aucun sch&eacute;ma trouv&eacute;</p>";
	}
	print "</form>";
	print "</blockquote>";
	exit();
}


$uri = onto_common_uri::get_uri($schemeSelected);
$concepts = getConceptWithoutScheme();
if (!empty($uri) && !empty($concepts)) {
	$scheme = new skos_concept($schemeSelected);
	$schemes = [$scheme->get_id() => $scheme->get_display_label()];

	foreach ($concepts as $concept) {
		$concept->set_schemes($schemes);
		$concept->save();
	}
	print "<p style='color:green;'>Termin&eacute; (redirection)</p>";
} else {
	print "<p style='color:red;'>Op&eacute;ration refus&eacute;e (redirection)</p>";
}

print "<script>
setTimeout(function () {
	document.location = window.location.origin + window.location.pathname;
}, 2000);
</script>";

print "</body></html>";