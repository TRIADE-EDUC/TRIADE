<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_duplicate.inc.php,v 1.2 2019-05-13 13:29:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/user.class.php');

$requete = "SELECT username FROM users WHERE userid='$id' LIMIT 1 ";
$res = pmb_mysql_query($requete);
$nbr = pmb_mysql_num_rows($res);
if ($nbr) {
	$usr=pmb_mysql_fetch_object($res);
} else die ('Unknown user');

$param_default = user::get_form($id, 'userform');
$param_default .= "<input type='hidden' id='duplicate_from_userid' name='duplicate_from_userid' value='".$id."' />";

echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);

$user = new user($id);
$user->set_duplicate_from_userid($id);
$user->set_userid(0);
print $user->get_user_form($param_default);

echo form_focus('userform', 'form_nom');
