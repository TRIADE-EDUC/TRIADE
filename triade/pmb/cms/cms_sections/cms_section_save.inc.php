<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section_save.inc.php,v 1.3 2019/10/25 06:51:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_section.class.php");
require_once($class_path."/cms/cms_editorial_tree.class.php");

$section = new cms_section();
$section->get_from_form();
$section->save();

print cms_editorial_tree::get_listing();
print cms_editorial_tree::set_tree_selected_item($section->get_id(), 'section');
