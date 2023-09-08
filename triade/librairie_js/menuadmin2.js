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
document.write("<p><br></p>");
document.write("</td>");
document.write("<td valign='top' width='123' align='right' height='160'>");
document.write("<table width='100%' border='0' cellspacing='1' cellpadding='1' height='232'>");


if (rubriqueactualite != "non") { 
	document.write("<tr>");
	document.write(" <td colspan='3'  id='coulTitre0' height='9' align='left' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1'>"+langtitre1+"</font></b></td>");
	document.write("</tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='38' id='coulModule0' align='left'  >");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./newsactualite.php' id='menumodule0' >"+langmenuadmin01A+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./actualiteetablissement.php' id='menumodule0' >"+langmenuadmin01B+"</a><br>");
	if (moduleadminnewsdefilant != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./newsdefil.php' id='menumodule0' >"+langmenuadmin01C+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./commaudio.php' id='menumodule0' >"+langmenuadmin05+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./commvideo.php' id='menumodule0' >"+langmenuadmin055+"</a><br>");
	document.write(" </p>");
	document.write("</td>");
	document.write("</tr>");
}


if (rubriqueetudiant != "non") { 
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr><td colspan='3' id='coulTitre0' align='left' style='border-radius:5px 5px 0px 0px; padding-left:5px'><b><font id='menumodule1'>"+langmenuadmin5+"</font></b></td></tr>");
	document.write("<tr><td colspan='3' id='coulModule0'  align='left' >");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./consult_classe.php'>"+langmenuadmin52+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./ficheeleve.php'>"+langmenuprof32+"</a><br>");
	if (moduleadminentretienindividuel != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./entretien.php'>"+langmenuadmin39+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./certificat.php'>"+langmenuadmin53+"</a><br>");
	if (moduleadminplanclasse == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./planclasse-visu-sco.php'>"+langmenuprof46+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./carnetnote.php'>"+langmenuadmin55+"</a><br>");
	if (moduleadmincarnetsuivi != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./carnet_admin.php' >"+langmenuadmin48+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertextesadmin.php'>"+langmenuparent27+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='emargement.php' >"+langmenuadmin97+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='publipostage.php' >"+langmenueleve518+"</a><br>");
	if (moduleadmindosmedical == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./ficheelevemedical.php'>"+langmenuadmin56+"</a><br>");
	document.write("</p></td></tr>");
}


if (rubriqueviescolaire != "non") { 
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr><td colspan='3' id='coulTitre0'  align='left' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1'>"+langmenuparent2+"</font></b></td></tr>");
	document.write("<tr><td colspan='3' id='coulModule0'  align='left' >");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_config_dst1.php'>"+langmenuadmin57+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_config_evenement1.php'>"+langmenuadmin58+"</a><br>");
	if (moduleadminabsrtd == "oui" )  {
	       	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_abs_retard_du_jour.php'>"+langmenuadmin59+"</a><br>");
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_abs_retard.php'>"+langmenuadmin510+"</A><BR> ");
	}
	if (moduleadmingestiondispense == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_dispence.php'>"+langmenuadmin511+"</a><br>");
	if (moduleadminsanctiondujour == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_sanction_du_jour.php'>"+langmenuadmin523+"</A><br>");
	if (moduleadminretenudj == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_discipline_du_jour.php'>"+langmenuadmin512+"</A><br>");
	if (moduleadmingestiondiscipline == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_discipline.php'>"+langmenuadmin513+"</A><br>");
	if (moduleadmingestionsavoiretre == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./savoiretre.php'>"+langmenuadmin912+"</A><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./circulaire_admin.php'>"+langmenuadmin514+"</a><br>");
	if (moduleadmingestionetude == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_etude.php'>"+langmenuadmin520+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_stage.php'>"+langmenuadmin517+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_central_stage.php'>"+langmenuadmin531+"</a><br>");
	document.write("</p></td>");
}


if (modulefinanciervateladmin == "oui") {
	document.write("</tr><tr><td colspan='3' height=19>&nbsp;</td></tr>");
	document.write("<tr id='coulTitre0'><td colspan='3'  align='left' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1'>"+langmenuadmin9000+"</font></b></td></tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='27' id='coulModule0'  align='left' >");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_financier/inscription_rechercher.php'>"+langmenuadmin9001+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_financier/parametrage.php'>"+langmenuadmin9002+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_financier/paiements.php'>"+langmenuadmin9003+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_financier/editions.php'>"+langmenuadmin9004+"</a><br />");
	document.write("</td>");
	document.write("</tr>");
}
if (modulechambrevateladmin == "oui") {
	document.write("</tr><tr><td colspan='3' height=19>&nbsp;</td></tr>");
	document.write("<tr id='coulTitre0'><td colspan='3'  align='left' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1'>"+langmenuadmin9100+"</font></b></td></tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='27' id='coulModule0'  align='left' >");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./planning_liste0.php'>"+langmenuadmin9101+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_chambres/reservation_liste.php'>"+langmenuadmin9102+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='module_chambres/parametrage.php'>"+langmenuadmin9103+"</a><br />");
	document.write("</td>");
	document.write("</tr>");
}

if (rubriquebulletin != "non") { 
	document.write("</tr><tr><td colspan='3' height=19>&nbsp;</td></tr>");
	document.write("<tr id='coulTitre0'><td colspan='3'  align='left' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1'>"+langmenuadmin6+"</font></b></td></tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='27' id='coulModule0'  align='left'>");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	if (moduleadminverifbulletin != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('./editer_bulletin.php','editer_bulletin','width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\">"+langmenuadmin67+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visa_direction.php'>"+langmenuadmin68+"</a> <br>");
	if (moduleadminnoteviescolaire != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./note_scolaire.php'>"+langmenuadmin73+"</a> <br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_tableaupp.php'>"+langmenuadmin66+"</a> <br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_trimestre.php'>"+langmenuadmin63+"</a><br>");
	if (moduleadminimprperiode != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_periode.php'>"+langmenuadmin64+"</a><br>");
	if (moduleadminexambrevet == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_examen.php'>"+langmenuadmin70+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('./video-proj-index.php','video','width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\" >"+langmenuadmin65+"</a></td>");
	document.write("</tr>");
}


if (rubriqueannexe != "non") {
	document.write("<tr><td colspan='3' height='19'>&nbsp;</td></tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='13' id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  align='left' ><b><font id='menumodule1'>"+langmenuadmin47+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td valign='top' colspan='3'  id='coulModule0'  align='left' >");
	document.write("<p style='margin-left: 2px; margin-bottom:5px; margin-top:5px'>");
	if (lan == "oui") {
		if (moduleadminfourniture == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('http://support.triade-educ.com/support/triade-shop.php','triadeshop','width=1024,height=760,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\" >"+langmenuadmin526+"</a><br>");
	}

	if (moduleelearning == "dokeos") {
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./acces_dokeos.php' target='_blank' >"+langmenuadmin103+"</a><br>");
	}else{
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./moodle/' target='_blank' >"+langmenuadmin103+"</a><br>");
	}

	if (modulehistoryadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./history_cmd.php'>"+langmenuadmin44+"</a><br>"); }
	if (moduleresaadmin == "oui") {
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./resr_admin.php#salle' >"+langmenuadmin40+"</a><br>");
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./resr_admin.php' >"+langmenuadmin40bis+"</a><br>");
	}
	

	if (moduleadminnotanet == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notanet.php' >"+langmenuadmin524+"</a><br>");
	//document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick='pasdispo();'><s>"+langmenuadmin516+"</s></a><br>");
	//document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick='pasdispo1();'><s>"+langmenuadmin41+"</s></a><br>");
	//document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick='pasdispo1();'><s>"+langmenuadmin42+"</s></a><br>");
	if (moduleadmincdi == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./pmb/index.php' target='_blank' >"+langmenuadmin43+"</a><br>"); 
	if (modulecantineadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cantine.php' >"+langmenupersonnel2+"</a><br>"); }
	if (moduleadminevalens == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='evalensadm.php' target='_blank' >"+langmenuadmin913+"</a><br>");

	document.write("</td></tr>");
}
	document.write("<tr><td colspan='3' height='19'>&nbsp;</td></tr>");
	document.write("</table>");
	document.write("</td>");
	document.write("</tr>");
	document.write("<tr valign='middle'>");
	document.write("<td colspan='5' height='43'>");
	document.write("<div align='center'>");
	document.write("<p>"+langmenupied+"</p>");
	document.write(img_logo_pied);
	document.write("</div>");
	document.write("</td></tr></table>");
	document.write("</div>");
	document.write("</td></tr></table>");
	document.write("</center>");
