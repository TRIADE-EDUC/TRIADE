<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_articles.inc.php,v 1.2 2015-03-10 17:12:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");


switch($sub) {			
	case 'list':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_articles_list"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_articles/cms_articles_list.inc.php");
		break;
	case 'edit':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".($id!= "new" ? $msg["cms_article_form_title"]:$msg["cms_new_article_form_title"]), $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_articles/cms_article_edit.inc.php");
		break;
	case 'save':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_articles_add"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_articles/cms_article_save.inc.php");
		break;
	case 'del':
		$cms_layout =str_replace('!!menu_sous_rub!!', " > ".$msg["cms_menu_editorial_articles_delete"], $cms_layout);
		print $cms_layout;
		require_once($base_path."/cms/cms_articles/cms_article_delete.inc.php");
		break;
	default:
		$cms_layout =str_replace('!!menu_sous_rub!!', "", $cms_layout);
		print $cms_layout;
		print "gestion articles";
		//include_once("$include_path/messages/help/$lang/portail_rubriques.txt");
		break;
}		