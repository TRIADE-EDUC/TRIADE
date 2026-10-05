<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section_edit.inc.php,v 1.4 2018/10/31 10:34:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_section.class.php");

if(!isset($num_parent)) $num_parent = 0;
if($id != "new"){
	$section = new cms_section($id);
}else if ($num_parent){
	$section = new cms_section(0,$num_parent);
}else{
	$section = new cms_section();
}

print $section->get_form("cms_section_edit","cms_section_edit");

$entity_locking = new entity_locking($id, TYPE_CMS_SECTION);
$entity_locking->lock_entity();