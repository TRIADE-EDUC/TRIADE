<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: launch.php,v 1.9 2017-10-23 10:13:00 ngantier Exp $

//Page de lancement d'un sauvegarde
$base_path="../..";
$base_auth="SAUV_AUTH|ADMINISTRATION_AUTH";
$base_title="\$msg[sauv_launch_titre]";
require($base_path."/includes/init.inc.php");

require_once ($include_path."/templates/launch_sauvegarde.tpl.php");

print "<div id=\"contenu-frame\">\n";
print "<h1>".$msg["sauv_launch_titre"]."</h1>\n";
//Récupération de l'id utilisateur
$requete="select userid from users where username='".SESSlogin."'";
$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());

$userid=pmb_mysql_result($resultat,0,0);

$requete="select sauv_sauvegarde_id, sauv_sauvegarde_nom, sauv_sauvegarde_users from sauv_sauvegardes";
$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());

$sauvegardes=array();
while ($res=pmb_mysql_fetch_object($resultat)) {
	$users=explode(",",$res->sauv_sauvegarde_users);
	$as=array_search($userid,$users);
	if (($as!==false)||($as!==null)) {
		$sauv=array();
		$sauv["NAME"]=$res->sauv_sauvegarde_nom;
		$sauv["ID"]=$res->sauv_sauvegarde_id;
		$sauvegardes[]=$sauv;
	}
}

for ($i=0; $i<count($sauvegardes); $i++) {
$tree.= "<input type=\"checkbox\" 
			name=\"sauvegardes[]\" 
			value=\"".$sauvegardes[$i]["ID"]."\" 
			 />&nbsp;".$sauvegardes[$i]["NAME"]."<br />";
}
$container=str_replace("!!sauvegardes_tree!!",$tree,$container);

echo $container;

echo "<script>self.focus();</script>\n";
echo "</div>";
?>