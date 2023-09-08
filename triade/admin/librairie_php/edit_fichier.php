<?php

function  banniere_edit($fichier,$image,$hauteur='') {

if ($image == "aucun") {
	$nomimage="";
	$classe="id='coulfond2'";
}else{
	$nomimage="background='$image' ";
	$classe="";
}

if (trim($hauteur) == "") { $hauteur="62"; }
if (($image == "../image/inc/09/banniere.png") ||  ($image == "./image/inc/09/banniere.png")) { $hauteur="132"; } 



$texte="/***************************************************************************\n";
$texte.="*                              T.R.I.A.D.E\n";
$texte.="*                            ---------------\n";
$texte.="*\n";
$texte.="*   begin                : Janvier 2000\n";
$texte.="*   copyright            : (C) 2000 E. TAESCH - T. TRACHET\n";
$texte.="*   Site                 : http://www.triade-educ.com\n";
$texte.="*\n";
$texte.="*\n";
$texte.="***************************************************************************\n";
$texte.="***************************************************************************\n";
$texte.="*\n";
$texte.="*   This program is free software; you can redistribute it and/or modify\n";
$texte.="*   it under the terms of the GNU General Public License as published by\n";
$texte.="*   the Free Software Foundation; either version 2 of the License, or\n";
$texte.="*   (at your option) any later version.\n";
$texte.="*\n";
$texte.="***************************************************************************/\n";
$texte.="<!--\n";
$texte.="document.write(\"<center><br /><table width='\"+largeurfen+\"' border='0' cellspacing='1' cellpadding='0' bgcolor='#000000' height='708' style='box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75);' >\");\n";
$texte.="document.write(\"<tr valign='top' id='coulfond1' >\");\n";
$texte.="document.write(\"<td height='717' id='bordure' >\");\n";
$texte.="document.write(\"<div align='left'>\");\n";
$texte.="document.write(\"<table $nomimage border='0' cellpadding='0' cellspacing='0' width='100%' height='$hauteur' $classe >\");\n";
$texte.="document.write(\"<tr>\");\n";
$texte.="document.write(\"<td valign='bottom' colspan='3' rowspan='3' style='padding-left:3px' >\");\n";
$texte.="-->\n";

$fp=fopen($fichier,"w");
fwrite($fp,"$texte");
fclose($fp);


}


?>
