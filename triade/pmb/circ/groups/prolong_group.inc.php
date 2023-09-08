<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: prolong_group.inc.php,v 1.1 2013-10-24 08:24:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$group = new group($groupID);
$group->update_members();

include('./circ/groups/show_group.inc.php');

