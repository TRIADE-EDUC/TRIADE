<?php 
function getMonth($valeur){
    return substr($valeur, 5, 2);
} 

function getYear($valeur){
    return substr($valeur, 0, 4);
} 

function monthNumToName($mois){
    $tableau = Array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
    return (intval($mois) > 0 && intval($mois) < 13) ? $tableau[intval($mois)] : "Indéfini";
} 
?>