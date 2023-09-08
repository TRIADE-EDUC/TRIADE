<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
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
include_once("timezone.php");

$fic="../../../common/maintenance.txt";
$disabled="";
if (file_exists($fic)) {
	$fichier=fopen("../../../common/maintenance.txt","r");
	$message=fread($fichier,"100");
	fclose($fichier);
	list($date,$time1,$time2)= preg_split ('/:/', $message, 3);
	$time2=preg_replace('/\n/','',$time2);
	$message="<b><font size=2 color=red>ATTENTION</font></b><br><br>";
	$message.= LANGMAINT0 . " <br><br>" ;
	$message.= LANGMAINT1 ."<b>$date</b> <br><br> ".LANGMAINT2."  <b>$time1</b> ".LANGMAINT3." <b>$time2</b></font>";
	$message.= "<br><br><font size=1>[<a href=\'#\' onclick=\'ejs_al_close()\'>Fermer</a>]</font>";
	list($heure1,$minute1)=preg_split ("/h/", $time1,2);
	list($heure2,$minute2)=preg_split ("/h/", $time2,2);
	if (($date == dateDMY()) && (dateH() >= $heure1  ) && ( dateH() <= $heure2) ) { $disabled="disabled"; }
?>

<SCRIPT LANGUAGE=JavaScript>
 // PARAMETRES
var ejs_al_mess = '<?php print trim($message)?>';
var ejs_al_background = '#CCCC00' // CC0000
var ejs_al_bordure = '#000000'
var ejs_al_police = 'Verdana'
var ejs_al_police_taille = '11'
var ejs_al_police_color = '#000000' //FFFFFF
var ejs_al_largeur = 450
var ejs_al_box2 = 0;
var ejs_al_hauteur = 170;
var vtplus=2;
if ( screen.width == 800 ) {
	vtplus=1.5;
}

// CADRES
<?php
if (GRAPH == 0) {
?>
 	ejs_al_classe = 'border-color:'+ejs_al_bordure+';border-style:solid;border-width:1px; background-image:url(\'./image/inc/01/bg.png\'); font-family:'+ejs_al_police+';font-size:'+ejs_al_police_taille+'px;color:'+ejs_al_police_color;
<?php
}else{
?>
    ejs_al_classe = 'border-color:'+ejs_al_bordure+';border-style:solid;border-width:1px;background:'+ejs_al_background+';font-family:'+ejs_al_police+';font-size:'+ejs_al_police_taille+'px;color:'+ejs_al_police_color;
<?php } ?>
 if(document.getElementById)
     {
     document.write('<DIV ID=ejs_al_box1 ></DIV>');
     document.write('<DIV ID=ejs_al_box2 style="border-radius: 5px 5px 5px 5px; position:absolute;visibility:hidden; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);'+ejs_al_classe+';width:'+ejs_al_box2+';height:'+ejs_al_hauteur+';z-index:5"');
     if(document.all)
         document.write(';padding:10');
     document.write('"></DIV>');
     document.write('<DIV ID=ejs_al_box3 ></DIV>');
     // TAILLE DE L'ECRAN
     ejs_al_Y = document.body.clientHeight;
     ejs_al_X = document.body.clientWidth;
     ejs_al_posX = Math.round(ejs_al_X/2);
     ejs_al_posY = Math.round(ejs_al_Y/vtplus)-Math.round(ejs_al_hauteur/vtplus);
     }

 function ejs_al_deplace()
     {
     // PLACEMENT
     document.getElementById("ejs_al_box1").style.left = ejs_al_posX-20-(ejs_al_box2/2);
     document.getElementById("ejs_al_box3").style.left = ejs_al_posX+(ejs_al_box2/2);
     document.getElementById("ejs_al_box2").style.left = ejs_al_posX-(ejs_al_box2/2)-5;
     document.getElementById("ejs_al_box2").style.width = ejs_al_box2+10;
     ejs_al_box2 += 5;
     if(ejs_al_box2<ejs_al_largeur)
         setTimeout("ejs_al_deplace()",10);
     else
         {
         document.getElementById("ejs_al_box2").innerHTML = '<CENTER><br />'+ejs_al_mess+'<BR><BR><B><a HREF=javascript:ejs_al_close()><FONT COLOR='+ejs_al_police_color+'></FONT></a></B></CENTER>' // '>ok</font>
         }
     }

 function ejs_al_start()
     {
     if(document.getElementById)
         {
         document.getElementById("ejs_al_box1").style.visibility = 'visible'
         document.getElementById("ejs_al_box2").style.visibility = 'visible'
         document.getElementById("ejs_al_box3").style.visibility = 'visible'
         document.getElementById("ejs_al_box1").style.top = ejs_al_posY-10
         document.getElementById("ejs_al_box2").style.top = ejs_al_posY
         document.getElementById("ejs_al_box3").style.top = ejs_al_posY-10
         ejs_al_deplace();
         }
     }

 function ejs_al_close()
     {
     if(document.getElementById)
         {
         document.getElementById("ejs_al_box1").style.visibility = 'hidden'
         document.getElementById("ejs_al_box2").style.visibility = 'hidden'
         document.getElementById("ejs_al_box3").style.visibility = 'hidden'
         document.getElementById("ejs_al_box1").style.top = -600
         document.getElementById("ejs_al_box2").style.top = -600
         document.getElementById("ejs_al_box3").style.top = -600
         ejs_al_deplace();
         }
     }

 window.onload = ejs_al_start;
 </SCRIPT>


 <?php

 }

 ?>
