<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_see.inc.php,v 1.8 2017-10-11 14:22:16 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour un titre uniforme
require_once($class_path."/skos/skos_page_concept.class.php");

$id += 0;
if ($id) {
	$authority_page = new skos_page_concept($id);
	$authority_page->proceed('concepts');
}