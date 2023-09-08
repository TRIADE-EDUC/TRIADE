<?php

include("lib_admin.php"); // REPADMIN
include("lib_acces2_inc.php"); //WEBROOT 

// Fr: Affiche le nombre de visiteur.
$COUNT_FILE = WEBROOT."/".REPADMIN."/data/compteur/compteur_demo_acces.txt";
        // Fr: Chemin absolu (complet) et Nom du fichier compteur.

$IMG_DIR_URL = "/".REPADMIN."/image/digits/";
$NB_DIGITS = 8;

$fp = fopen("$COUNT_FILE", "r");
flock($fp, 1);
$count = fgets($fp, 4096);
flock($fp, 3);
fclose($fp);

chop($count);
$nb_digits = max(strlen($count), $NB_DIGITS);
$count = substr("0000000000".$count, -$nb_digits);

$digits = preg_split("//", $count);

for($i = 0; $i <= $nb_digits; $i++) {
        if ($digits[$i] != "") {
                $html_result_demo.="<IMG SRC=\"$IMG_DIR_URL$digits[$i].gif\" align='center'>";
        }
}
// Fr: Fin code PHP
?>
