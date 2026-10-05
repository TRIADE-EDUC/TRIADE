<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_articles_list.inc.php,v 1.1 2011/09/14 08:44:12 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//require_once($class_path."/cms/cms_editorial_tree.class.php");
require_once($class_path."/cms/cms_articles.class.php");

print cms_articles::get_listing();
