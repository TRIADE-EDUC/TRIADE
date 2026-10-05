<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section_delete.inc.php,v 1.1 2015/03/10 17:12:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_section.class.php");
require_once($class_path."/cms/cms_editorial_tree.class.php");

if(!$cms_editorial_form_obj_id){
	return false;
}else{
	$section = new cms_section($cms_editorial_form_obj_id);
	$section->delete();
}
print cms_editorial_tree::get_listing();

