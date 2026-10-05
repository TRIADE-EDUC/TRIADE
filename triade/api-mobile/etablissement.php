<?php 

/* etablissement.php, le demandeur le consulte en mode GET (sans vérification ni session PHP) */


/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
$cnx=cnx();

$data=visu_paramViaIdSite("");
// nom_ecole,adresse,postal,ville,tel,email,directeur,urlsite,academie,pays,departement,annee_scolaire
for($i=0;$i<count($data);$i++) {
	$nom_etablissement=trim($data[$i][0]);
	$adresse=trim($data[$i][1]);
	$postal=trim($data[$i][2]);
	$ville=trim($data[$i][3]);
	$tel=trim($data[$i][4]);
        $mail=trim($data[$i][5]);
	$directeur=trim($data[$i][6]);
	$url=trim($data[$i][7]);
	$academi=trim($data[$i][8]);
	$pays=trim($data[$i][9]);
	$departement=trim($data[$i][10]);
	$annee_scolaire=trim($data[$i][11]);
}

header('Content-Type: application/json; charset=utf-8');

$etb = [
    "nometb"        => "$nom_etablissement",
    "directeur"     => "$directeur",
    "academie"      => "$academi",
    "annee_scolaire"=> "$annee_scolaire",
    "adresse"       => "$adresse",
    "codepostal"    => "$postal",
    "ville"         => "$ville",
    "departement"   => "$departement",
    "pays"          => "$pays",
    "telephone"     => "$tel",
    "email"         => "$mail",
    "siteweb"       => "$url",
];

/* Exemple :
$etb = [
    "nometb" => "Etablissement TRIADE",
    "academie" => "Paris", 
    "adresse" => "1 rue de la paix",
    "codepostal" => "75002",
    "ville" => "Paris",
    "pays" => "France",
    "telephone" => "01.02.03.04.05",
    "email" => "etablissement@yopmail.com",
    "siteweb" => "https://perdu.com/",
];




*/

echo json_encode($etb);

Pgclose();
?>
