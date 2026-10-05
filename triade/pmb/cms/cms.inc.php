<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.inc.php,v 1.19.4.2 2025/04/10 06:56:04 tsamson Exp $
use Pmb\CMS\Controller\CmsController;
use Pmb\CMS\Controller\ToolkitsController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

require_once("$include_path/cms/cms.inc.php");
require_once($class_path."/cms/cms_editorial.class.php");

if (!$pmb_editorial_dojo_editor && $pmb_javascript_office_editor) {
    print $pmb_javascript_office_editor ;
    print "<script type='text/javascript'>
        pmb_include('$base_path/javascript/tinyMCE_interface.js');
    </script>";
}

switch($categ) {
    case 'toolkits':
        if ($cms_active == 2 && (SESSrights & CMS_BUILD_AUTH)) {
            $controller = new ToolkitsController();
            $controller->proceed();
        } elseif ($cms_active) {
			echo "<script>document.location='$base_path/cms.php?categ=build&sub=block';</script>";
        }
        break;
    case 'portal':
        global $action;
        if ($cms_active == 2 && (SESSrights & CMS_BUILD_AUTH)) {
            $controller = new CmsController();
            if (!empty($action)) {
                $controller->proceedAction($action);
            } else {
                $controller->proceed();
            }
        } elseif ($cms_active) {
        	echo "<script>document.location='$base_path/cms.php?categ=build&sub=block';</script>";
        }
        break;
    case 'build':
        if ($cms_active == 1 && (SESSrights & CMS_BUILD_AUTH)) {
            require_once("./cms/cms_build/cms_build.inc.php");
        } elseif ($cms_active == 2) {
			echo "<script>document.location='$base_path/cms.php?categ=portal&sub=block';</script>";
        }
        break;
    case 'pages':
        if ($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
            require_once("./cms/cms_pages/cms_pages.inc.php");
        }
        break;
    case 'cadres':
        if ($cms_active && (SESSrights & CMS_BUILD_AUTH)) {
            cms_cadres_controller::proceed();
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
            require_once("./cms/cms_editorial/cms_editorial.inc.php");
        }
        break;
    case 'article':
        if ($cms_active) {
            require_once("./cms/cms_articles/cms_articles.inc.php");
        }
        break;
    case "collection" :
        if ($cms_active) {
            require_once("./cms/cms_collection/cms_collection.inc.php");
        }
        break;
    case 'manage':
        if ($cms_active) {
            require_once("./cms/cms_manage_module.inc.php");
        }
        break;
    default:
        break;
}

print $cms_layout_end;
