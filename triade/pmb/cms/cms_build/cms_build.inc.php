<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.inc.php,v 1.8 2021/02/13 16:18:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($opac_id)) $opac_id = '';

require_once ("$include_path/cms/cms.inc.php");
require_once ("$include_path/templates/cms/cms_build.tpl.php");  
require_once("$class_path/cms/cms_build.class.php");

$cms_build=new cms_build($opac_id);

switch($sub) {			
	case 'block':
		if($action=='clean_cache'){
			cms_cache::clean_cache();
		}
		if($action=='clean_cache_img'){
			cms_cache::clean_cache_img();
		}
		if($action=='reset_all_css' && $build_id_version){
			cms_build::reset_all_css($build_id_version);
		}
		print $cms_build->get_form_block();
		break;
	default:
		print $cms_build->get_form_block();
		break;
}		
