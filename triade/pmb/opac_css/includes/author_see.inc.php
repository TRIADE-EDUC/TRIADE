<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author_see.inc.php,v 1.89 2024/03/22 15:31:02 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/authorities/page/authority_page_author.class.php");
require_once($base_path.'/includes/templates/author.tpl.php');
require_once($base_path."/includes/explnum.inc.php");

$id = intval($id);
if ($id) {
	$authority_page = new authority_page_author($id);
	$authority_page->proceed('authors');
}