<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_article_edit.inc.php,v 1.5 2018/09/19 15:19:18 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");

if($id != "new"){
	$article = new cms_article($id);
}else if (isset($num_parent) && $num_parent) {
	$article = new cms_article(0,$num_parent);
}else{
	$article = new cms_article();
}

print $article->get_form("cms_article_edit","cms_article_edit", "");
$entity_locking = new entity_locking($id, TYPE_CMS_ARTICLE);
$entity_locking->lock_entity();