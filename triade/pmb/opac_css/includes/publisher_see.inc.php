<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publisher_see.inc.php,v 1.72 2017-05-05 17:14:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour un auteur

// inclusion de classe utiles
require_once($class_path."/authorities/page/authority_page_publisher.class.php");
require_once($base_path.'/includes/templates/publisher.tpl.php');

$id += 0;
if($id) {
	$authority_page = new authority_page_publisher($id);
	$authority_page->proceed('publishers');
}