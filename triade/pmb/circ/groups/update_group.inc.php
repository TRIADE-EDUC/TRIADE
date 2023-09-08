<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: update_group.inc.php,v 1.11 2018-04-25 11:27:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!$libelle_resp) $respID = 0;

$group = new group($groupID);

if (!isset($lettre_rappel)) $lettre_rappel = '';
if (!isset($mail_rappel)) $mail_rappel = '';
if (!isset($lettre_rappel_show_nomgroup)) $lettre_rappel_show_nomgroup = '';
if(!isset($group_add_resp)) $group_add_resp = 0;

$group->set($group_name, $respID, $lettre_rappel, $mail_rappel, $lettre_rappel_show_nomgroup);
$group->update();
if ($respID && $group_add_resp) {
	$group->add_member($respID);
}

if ($group->id && $group->libelle) {
    $groupID = $group->id;
    include('./circ/groups/show_group.inc.php');
} else {
	error_message($msg[919], $msg[923], 1, './circ.php?categ=groups');
}

?>

