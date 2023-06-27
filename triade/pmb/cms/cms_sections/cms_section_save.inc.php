<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_section_save.inc.php,v 1.2 2013-07-12 07:48:01 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_section.class.php");
require_once($class_path."/cms/cms_editorial_tree.class.php");

$section = new cms_section();
$section->get_from_form();
$section->save();

print cms_editorial_tree::get_listing();
