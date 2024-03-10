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
include_once("./common/config2.inc.php");
include_once("../librairie_php/timezone.php");

$partner = "";
$ville = METEOID; 
//$vname="Paris";
$jours = 2;
$datedujour=dateDMY2();
$url = "https://www.triade-educ.org/accueil/weather.php?ref=".METEOID."&date=$datedujour";
//print $url;
/*
<TRIADE>
	<forecast>
		<date>2022-11-19</date>
		<ville>Rennes</ville>
		<min>5</min>
		<max>11</max>
		<jour>3</jour>
		<nuit>40</nuit>
	</forecast>
</TRIADE>
*/

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

<table class=meteofond>
<tr><td class=meteotitre colspan=2>&nbsp;&nbsp;&nbsp;Prévision sur <?php print $vname?></td></tr> 
<tr>
      <td class=meteocorps>
	<table>
      <tr>
         <td colspan=3 class=meteosstitre><strong>
            <?php 
	print dateForm("$date0");
		?>
         </strong></td>
      </tr>
      <tr>
         <td>Max:<br> <?php print "$max0"."°C"?></td>
         <td class=meteosstitre><?php print LANGMETEO1 ?></td>
         <td class=meteosstitre><?php print LANGMETEO2 ?></td>
      </tr>
      <tr>
         <td>Min:<br> <?php print "$min0"."°C"?></td>
         <td rowspan=2><img src="./meteo/img/<?php print $imgjour0 ?>.png"
            width=40 ></td>
         <td rowspan=2><img src="./meteo/img/<?php print $imgnuit0 ?>.png"
            width=40 ></td>
      </tr>
      <!-- <tr>
         <td>H%: <?php print $xml["hmid"][$i]?></td>
      </tr>
	-->
      </table></td>


      <td class=meteocorps>
	<table>
      <tr>
         <td colspan=3 class=meteosstitre><strong>
            <?php 
	print dateForm("$date1");
		?>
         </strong></td>
      </tr>
      <tr>
         <td>Max:<br> <?php print "$max1"."°C"?></td>
         <td class=meteosstitre><?php print LANGMETEO1 ?></td>
         <td class=meteosstitre><?php print LANGMETEO2 ?></td>
      </tr>
      <tr>
         <td>Min:<br> <?php print "$min1"."°C"?></td>
         <td rowspan=2><img src="./meteo/img/<?php print $imgjour1; ?>.png"
            width=40 alt=""></td>
         <td rowspan=2><img src="./meteo/img/<?php print $imgnuit1; ?>.png"
            width=40 alt=""></td>
      </tr>
      <!-- <tr>
         <td>H%: <?php print $xml["hmid"][$i]?></td>
      </tr>
	-->
      </table></td>





</tr>
  
</table>
