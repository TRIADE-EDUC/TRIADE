<?php 

/* get-meteo.php, le demandeur le consulte en mode GET (sans vérification ni session PHP) */
/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */

/* Mettre le script pour extraires les données du XML (de l'API TRIADE Météo) afin de lee mettre dans la réponse JSON (ci-dessous) */

header('Content-Type: application/json; charset=utf-8');

setlocale(LC_TIME, "fr_FR"); // ou "fr"
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../common/lib_patch.php");
statTriade();

$partner = "";
$ville = METEOID;
//$vname="Paris";
$jours = 2;
$datedujour=dateDMY2();
$url = "https://www.triade-educ.org/accueil/weather.php?ref=".METEOID."&date=$datedujour";

$vname = $date0 = $min0 = $max0 = $jour0 = $nuit0 = $imgjour0 = $imgnuit0 = '';
$date1 = $min1 = $max1 = $jour1 = $nuit1 = $imgjour1 = $imgnuit1 = '';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$xmlStr = curl_exec($ch);
curl_close($ch);
$data = ($xmlStr !== false && $xmlStr !== '') ? @simplexml_load_string($xmlStr) : false;
if ($data !== false) {
        $vname=$data->forecast->ville;
        $date0=$data->forecast->date;
        $min0=$data->forecast->min;
        $max0=$data->forecast->max;
        $jour0=$data->forecast->jour;
        $nuit0=$data->forecast->nuit;
        $imgjour0=$data->forecast->imgjour;
        $imgnuit0=$data->forecast->imgnuit;

        $date1=$data->forecast[1]->date;
        $min1=$data->forecast[1]->min;
        $max1=$data->forecast[1]->max;
        $jour1=$data->forecast[1]->jour;
        $nuit1=$data->forecast[1]->nuit;
        $imgjour1=$data->forecast[1]->imgjour;
        $imgnuit1=$data->forecast[1]->imgnuit;
}


$meteo = [
    "ville" => "$vname", // Connecter la variable
    "ajd" => [ // Aujourd'hui
        "temp-min" => "$min0", // Connecter la variable
        "temp-max" => "$max0", // Connecter la variable
        "prevision" => "$imgjour0", // Code météo (fourni tel quel par l'API Triade Météo), connecter la variable
    ],
    "demain" => [
        "temp-min" => "$min1", // Connecter la variable
        "temp-max" => "$max1", // Connecter la variable
        "prevision" => "$imgjour1", // Code météo (fourni tel quel par l'API Triade Météo), connecter la variable
    ],
];

/* Exemple type : */
/*
$meteo = [
    "ville" => "Bordeaux", 
    "ajd" => [ 
        "temp-min" => 15,
        "temp-max" => 20,
        "prevision" => 30,
    ],
    "demain" => [
        "temp-min" => 28,
        "temp-max" => 37,
        "prevision" => 36,
    ],
];
 */

echo json_encode($meteo);

?>
