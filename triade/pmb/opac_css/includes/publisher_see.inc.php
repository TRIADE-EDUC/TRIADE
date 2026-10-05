<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publisher_see.inc.php,v 1.73 2024/03/22 15:31:02 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour un auteur

// inclusion de classe utiles
require_once($class_path."/authorities/page/authority_page_publisher.class.php");
require_once($base_path.'/includes/templates/publisher.tpl.php');

$id = intval($id);
if($id) {
	$authority_page = new authority_page_publisher($id);
	$authority_page->proceed('publishers');
}