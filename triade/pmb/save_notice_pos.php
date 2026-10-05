<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: save_notice_pos.php,v 1.10.6.1 2024/09/03 14:29:19 dgoron Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;


$base_auth = "ADMINISTRATION_AUTH";  
require_once("includes/init.inc.php");

global $grille_typdoc, $grille_niveau_biblio, $grille_location, $datas;

if (!$grille_typdoc) {
    $grille_typdoc='a' ;
}
if (!$grille_niveau_biblio) {
    $grille_niveau_biblio='m' ;
}
if (!empty($datas)) {
    // stripslashes et addslashes successifs pour securiser l'execution de la requete SQL sans injection
    $datas = stripslashes($datas);
    if (strpos($datas, '<script') === false) {
        $requete = "delete from grilles where grille_niveau_biblio='".addslashes($grille_niveau_biblio)."' and grille_typdoc='".addslashes($grille_typdoc)."' ".($grille_location?"and grille_localisation='".addslashes($grille_location)."' ":"");
        pmb_mysql_query($requete) or die("Big problem: <br />".pmb_mysql_error()."<br />$requete");
        $requete = "insert into grilles set grille_niveau_biblio='".addslashes($grille_niveau_biblio)."', grille_typdoc='".addslashes($grille_typdoc)."', ".($grille_location?"grille_localisation='".addslashes($grille_location)."', ":"")."descr_format='".addslashes($datas)."' ";
        pmb_mysql_query($requete) or die("Big problem: <br />".pmb_mysql_error()."<br />$requete");
        echo "OK";
    } else {
        echo "KO";
    }
} else {
    echo "KO";
}
