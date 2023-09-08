<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.inc.php,v 1.11 2019-06-06 09:56:29 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (PHP_MAJOR_VERSION == "5") @ini_set("zend.ze1_compatibility_mode", "0");

require_once ("$include_path/cms/cms.inc.php");
require_once($class_path."/cms/cms_editorial.class.php");

if (!$pmb_editorial_dojo_editor && $pmb_javascript_office_editor){
	print $pmb_javascript_office_editor ;
	print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
}

switch($categ) {			
	case 'build':
		if ($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
			require_once("./cms/cms_build/cms_build.inc.php");
		}
		break;
	case 'pages':
		if ($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
			$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
			require_once("./cms/cms_pages/cms_pages.inc.php");
		}
		break;
	case 'frbr_pages':
		if (SESSrights & CMS_BUILD_AUTH) {
			require_once("./cms/frbr_pages/frbr_pages.inc.php");
		}
		break;
	case 'section':
		if ($cms_active) {
			require_once("./cms/cms_sections/cms_section.inc.php");
		}
		break;
	case 'editorial':
		if ($cms_active) {
			$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
			require_once("./cms/cms_editorial/cms_editorial.inc.php");
		}
		break;
	case 'article':
		if ($cms_active) {
			$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
			require_once("./cms/cms_articles/cms_articles.inc.php");
		}
		break;
	case "collection" :
		if ($cms_active) {
			$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
			require_once("./cms/cms_collection/cms_collection.inc.php");
		}
		break;
	case 'manage':
		if ($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
			require_once("./cms/cms_manage_module.inc.php");
		}
		break;
	default:
		$cms_layout = str_replace("!!menu_contextuel!!","",$cms_layout);
		print $cms_layout;
		break;
}		

print $cms_layout_end;