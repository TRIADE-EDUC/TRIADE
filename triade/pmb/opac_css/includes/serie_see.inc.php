<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie_see.inc.php,v 1.41 2017-05-05 17:14:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour une serie
require_once($class_path."/authorities/page/authority_page_serie.class.php");

$id += 0;
if($id) {
	$authority_page = new authority_page_serie($id);
	$authority_page->proceed('series');
}