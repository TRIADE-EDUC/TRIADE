<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_articles.inc.php,v 1.3 2021/02/13 16:19:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");


switch($sub) {			
	case 'list':
		require_once($base_path."/cms/cms_articles/cms_articles_list.inc.php");
		break;
	case 'edit':
		require_once($base_path."/cms/cms_articles/cms_article_edit.inc.php");
		break;
	case 'save':
		require_once($base_path."/cms/cms_articles/cms_article_save.inc.php");
		break;
	case 'del':
		require_once($base_path."/cms/cms_articles/cms_article_delete.inc.php");
		break;
	default:
		print "gestion articles";
		//include_once("$include_path/messages/help/$lang/portail_rubriques.txt");
		break;
}		