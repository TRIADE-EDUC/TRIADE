<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_del.inc.php,v 1.9 2019-04-12 12:42:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$user_encours=$_COOKIE["PhpMyBibli-LOGIN"];

if($id && $id !=1) {
	$requete = "select username from users where userid=$id ";
	$res=pmb_mysql_fetch_row( pmb_mysql_query($requete));
	$username_del=$res[0];
	$requete = "DELETE FROM users WHERE userid=$id and username<>'".addslashes($user_encours)."'";
	$res = pmb_mysql_query($requete);
	if ($res) {
		$requete = "DELETE FROM sessions WHERE login='".$username_del."'";
		$res = pmb_mysql_query($requete);
		$requete = "DELETE FROM es_methods_users WHERE num_user=$id ";
		$res = pmb_mysql_query($requete);
	}
	$requete = "OPTIMIZE TABLE users ";
	$res = pmb_mysql_query($requete);
}
show_users();
