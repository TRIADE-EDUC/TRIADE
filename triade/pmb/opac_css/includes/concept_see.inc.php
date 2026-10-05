<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_see.inc.php,v 1.9 2024/03/22 15:31:02 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour un titre uniforme
require_once($class_path."/skos/skos_page_concept.class.php");

$id = intval($id);
if ($id) {
	$authority_page = new skos_page_concept($id);
	$authority_page->proceed('concepts');
}