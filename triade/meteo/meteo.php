<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/


setlocale(LC_TIME, "fr_FR"); // ou "fr"
if (file_exists("./common/config2.inc.php"))      include_once("./common/config2.inc.php");
if (file_exists("../librairie_php/timezone.php")) include_once("../librairie_php/timezone.php");
if (file_exists("../common/config2.inc.php"))      include_once("../common/config2.inc.php");
if (file_exists("./librairie_php/timezone.php")) include_once("./librairie_php/timezone.php");

$partner = "";
$ville = METEOID;
$jours = 2;
$datedujour=dateDMY2();
$url = "https://www.triade-educ.org/accueil/weather.php?ref=".METEOID."&date=$datedujour";

$data=simplexml_load_file($url);
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

?>

<div class="meteo-wrap">
    <div class="meteo-header">&#9728; Prévisions météo &ndash; <?php print $vname ?></div>
    <div class="meteo-days">

        <div class="meteo-day">
            <div class="meteo-date"><?php print dateForm("$date0") ?></div>
            <div class="meteo-cols">
                <div class="meteo-col">
                    <img class="meteo-img" src="./meteo/img/<?php print $imgjour0 ?>.png" alt="">
                    <span class="meteo-label"><?php print LANGMETEO1 ?></span>
                    <span class="meteo-temp"><?php print $max0 ?>°C</span>
                </div>
                <div class="meteo-col">
                    <img class="meteo-img" src="./meteo/img/<?php print $imgnuit0 ?>.png" alt="">
                    <span class="meteo-label"><?php print LANGMETEO2 ?></span>
                    <span class="meteo-temp"><?php print $min0 ?>°C</span>
                </div>
            </div>
        </div>

        <div class="meteo-day">
            <div class="meteo-date"><?php print dateForm("$date1") ?></div>
            <div class="meteo-cols">
                <div class="meteo-col">
                    <img class="meteo-img" src="./meteo/img/<?php print $imgjour1 ?>.png" alt="">
                    <span class="meteo-label"><?php print LANGMETEO1 ?></span>
                    <span class="meteo-temp"><?php print $max1 ?>°C</span>
                </div>
                <div class="meteo-col">
                    <img class="meteo-img" src="./meteo/img/<?php print $imgnuit1 ?>.png" alt="">
                    <span class="meteo-label"><?php print LANGMETEO2 ?></span>
                    <span class="meteo-temp"><?php print $min1 ?>°C</span>
                </div>
            </div>
        </div>

    </div>
</div>
