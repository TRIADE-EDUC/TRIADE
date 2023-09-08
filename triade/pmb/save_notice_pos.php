<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: save_notice_pos.php,v 1.9 2016-01-05 09:17:03 ngantier Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;


$base_auth = "ADMINISTRATION_AUTH";  
require_once("includes/init.inc.php");

if (!$grille_typdoc) $grille_typdoc='a' ;
if (!$grille_niveau_biblio) $grille_niveau_biblio='m' ;

$requete = "delete from grilles where grille_niveau_biblio='$grille_niveau_biblio' and grille_typdoc='$grille_typdoc' ".($grille_location?"and grille_localisation='$grille_location' ":"");
$res = pmb_mysql_query($requete, $dbh) or die("Big problem: <br />".pmb_mysql_error()."<br />$requete");
$requete = "insert into grilles set grille_niveau_biblio='$grille_niveau_biblio', grille_typdoc='$grille_typdoc', ".($grille_location?"grille_localisation='$grille_location', ":"")."descr_format='".$datas."' ";
$res = pmb_mysql_query($requete, $dbh) or die("Big problem: <br />".pmb_mysql_error()."<br />$requete");
echo "OK";
