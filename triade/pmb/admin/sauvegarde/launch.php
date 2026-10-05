<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: launch.php,v 1.11 2022/07/27 10:31:44 jparis Exp $

global $base_path, $base_auth, $base_title, $include_path, $msg, $tree, $container;
global $charset;

//Page de lancement d'un sauvegarde
$base_path = "../..";
$base_auth = "SAUV_AUTH|ADMINISTRATION_AUTH";
$base_title = "\$msg[sauv_launch_titre]";

require("$base_path/includes/init.inc.php");
require_once ("$include_path/templates/launch_sauvegarde.tpl.php");

print "<div id=\"contenu-frame\">\n";
print "<h1>".$msg["sauv_launch_titre"]."</h1>\n";
//Récupération de l'id utilisateur
$requete = "select userid from users where username='".SESSlogin."'";
$resultat = pmb_mysql_query($requete) or die(pmb_mysql_error());
$userid = pmb_mysql_result($resultat, 0, 0);

$requete = "select sauv_sauvegarde_id, sauv_sauvegarde_nom, sauv_sauvegarde_users from sauv_sauvegardes";
$resultat = pmb_mysql_query($requete) or die(pmb_mysql_error());

$sauvegardes = array();

while ($res = pmb_mysql_fetch_object($resultat)) {
	$users = explode(",", $res->sauv_sauvegarde_users);
	$as = array_search($userid, $users);
	if ($as !== false || $as !== null) {
		$sauv = array();
		$sauv["NAME"] = $res->sauv_sauvegarde_nom;
		$sauv["ID"] = $res->sauv_sauvegarde_id;
		$sauvegardes[] = $sauv;
	}
}

for ($i = 0; $i < count($sauvegardes); $i++) {
	$tree .= sprintf("<input type='checkbox' name='sauvegardes[]' value='%s' />&nbsp; %s<br />",
		htmlentities($sauvegardes[$i]["ID"], ENT_QUOTES, $charset),
		htmlentities($sauvegardes[$i]["NAME"], ENT_QUOTES, $charset));
}

$container = str_replace("!!sauvegardes_tree!!", $tree, $container);
echo $container;
echo "<script>self.focus();</script>\n</div>";
?>