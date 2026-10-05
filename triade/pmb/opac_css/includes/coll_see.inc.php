<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: coll_see.inc.php,v 1.74 2024/03/22 15:31:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour une collection
require_once($class_path."/authorities/page/authority_page_collection.class.php");

$id = intval($id);
if($id) {
	$authority_page = new authority_page_collection($id);
	$authority_page->proceed('collections');
}
