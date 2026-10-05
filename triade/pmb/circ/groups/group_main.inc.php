<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: group_main.inc.php,v 1.13 2021/10/22 13:11:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
global $group_header, $group_footer, $groupID;

require_once("$class_path/group.class.php");
require_once("$include_path/templates/group.tpl.php");

print pmb_bidi($group_header);

require_once($class_path."/groups/groups_controller.class.php");
groups_controller::proceed($groupID);

print $group_footer;
