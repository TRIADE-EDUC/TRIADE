<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_article_save.inc.php,v 1.3 2019/10/25 06:51:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");
require_once($class_path."/cms/cms_editorial_tree.class.php");

$article = new cms_article();
$article->get_from_form();
$article->save();

print cms_editorial_tree::get_listing();
print cms_editorial_tree::set_tree_selected_item($article->get_id(), 'article');