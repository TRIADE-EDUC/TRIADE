<!--
document.write("</div></td>");
document.write("</tr>");
document.write("<tr> </tr>");
document.write("<tr> </tr>");
document.write("</table>");
document.write("<table border='0' cellpadding='0' cellspacing='0' width='100%' height='20' bgcolor='#175216'>");
document.write("<tr id='coulBar0' >");
document.write("<td height='20' width='20%'>");
if (lienassist == '0' ) {
	document.write("<div align='center'><a href='#' onclick=\"open('http://doc.triade-educ.com','_blank','')\" class='m'>"+langtitre0+"</a></div>");
}else{
	document.write("<div align='center'><a href='"+lienassist+"' class='m' target='_blank' >"+langtitre0+"</a></div>");
}
document.write("</td>");
document.write("<td height='20' width='20%'>");
document.write("<div align='center'><a href='./acces2.php' class='m'>"+langtitre1+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='20%'>");
document.write("<div align='center'><a href='./acces2.php' target='_blank' class='m'>"+langtitre2bis+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='20%'>");
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes')\"  class='m'>"+langtitre3+"</a></div>");
document.write("</td>");
document.write("<td width='20%' height='20'>");
document.write("<div align='center'><a href='#'   onclick='quitter_session()';  class='m'>"+langtitre5+"</a>");
document.write("</div>");
document.write("</td>");
document.write("</tr>");
document.write("</table>");
document.write("</div>");
document.write("<div align='left'>");
document.write("<table border='0' cellpadding='0' cellspacing='0' width='100%' height='379'>");
document.write("<tr valign='top'>");
document.write("<td colspan='5' height='20'><img src='./image/inc/omb.gif' width='100%' height='6'></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td valign='top' width='123' height='160'>");
document.write("<table width='100%' border='0' cellspacing='1' cellpadding='1' >");

if ((GRAPH == '20') || (GRAPH == '21'))  {
	document.write("<tr>");
	document.write("<td colspan='3' align='center' ><a href='#' onClick=\"open('http://www.pigier.tv','pigiertv','width=960,height=500');\"  ><img src='image/commun/logopigiertv.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' /></a></td>");
	document.write("</tr>");
	document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
}else{	
	if ((webrad == "oui") && (moduleradio == "oui")) { 
		document.write("<tr>");
		document.write("<td colspan='3' id='coulTitre0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' ><a href='#' onMouseOver=\"AffBulleRadioAvecQuit('','','<div id=affradio ><iframe src=http://www.triade-educ.com/webradio/infomusic.php  height=100 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=NO ></iframe></div>'); window.status=''; return true;\" onMouseOut='HideBulleRadio()' onclick=\"open('webradio.php','webradio','width=329,height=195');return false\" ><img src='image/commun/webradio.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); ' /></a></td>"); 
		document.write("</tr>");
		document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
	}else{
		document.write("<tr>");
		document.write("</tr>");
	}
}
document.write("<tr>");
document.write(" <td colspan='3'  id='coulTitre0' ><b><font id='menumodule1'>"+langmenuadmin07+"</font></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3' height='13' id='coulModule0' >");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='gescompte.php' id='menumodule0'  >"+langmenugeneral01+"</a><br>");
if (moduleeleveagenda == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' id='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop','resizable=yes,width=1000,height=700');\" >"+langmenuadmin00+"</a><br>"); }
if (moduleelevestockage == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500')\" id='menumodule0' >"+langmenuadmin06+"</a><br>"); }
//if (moduleelevemsn == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./intra-msn.php' id='menumodule0' >"+langmenuadmin100+"</a><br>"); }
if (moduleelevecompta == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='compta_consulte3.php' >"+langmenuadmin90+"</a><br>"); }
if (moduleeleverss == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./flux.php' id='menumodule0' >"+langmenuadmin521+"</a><br>"); }
if (moduleelevecantine == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cantine_consulte.php' >"+langmenupersonnel2+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
if (lan == "oui") {
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('http://support.triade-educ.com/support/triade-shop.php','triadeshop','width=1024,height=765,resizable=no,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\" >"+langmenuadmin526+"</a><br>");
}
document.write(" </p>");
document.write("</td>");
document.write("</tr>")
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write(" </tr>");
if (modulemessagerieeleve == "oui") {
	document.write("<tr>");
	document.write("<td colspan='3' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenuparent1+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3'  id='coulModule0'>");
	document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_reception.php'>"+langmenuparent12+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_envoi.php'>"+langmenuparent11+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_suppression.php' id='menumodule0'>"+langmenuadmin04+"</a><br>");
	document.write("</p>");
	document.write("</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write(" </tr>");
}
document.write(" <tr>");
document.write(" <td colspan='3' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenuparent2+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notesEleve.php'>"+langmenuparent21+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_retard_parent.php'>"+langmenuparent22+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_absence_parent.php'>"+langmenuparent23+"</a><br>");
if (moduleelevegestionsavoiretre == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./savoiretrevisu.php'>"+langmenuadmin912+"</A><br>");
if (moduledispence == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_dispence_parent.php'>"+langmenuparent24+"</a><br>");  }
if (modulediscipline == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_discipline_parent.php'>"+langmenuparent25+"</a><br>"); }
if (modulebulletinvisueleve == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_bulletin.php'>"+langmenuadmin6+"</a><br>"); } 
document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick=\"open('edt_visu_el.php','edt_el','width=1050,height=850,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')\">"+langmenuparent31+"</a><br>");
if (modulecahierdetexte == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertext_visu.php'>"+langmenuparent27+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./circulaire_liste.php'>"+langmenuparent32+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./information.php'>"+langmenuprof29+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./trombi-eleve.php'>"+langmenuadmin518+"</a><br>");
if (moduleplandeclasse == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick=\"open('planclasse-visu-pe.php','planclasse','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')\">"+langmenuprof46+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./list_delegue.php'>"+langmenuparent36+"</a><br>");
if (moduleplanningeleve == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_evenement_visu_readonly.php'>"+langmenuparent34bis+"</a><br>");}
if (moduledst == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_dst_visu_readonly.php'>"+langmenuparent35+"</a><br>"); }
if (moduleelevestage == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_stage_el.php'>"+langmenueleve517+"</a><br>"); }
document.write("</p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height='13' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenueleve1+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0' >");
document.write("<p style='margin-left: 2; margin-bottom:5; margin-top:5'>");
//document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='soutienscolaire.php' >Soutien Scolaire</a><br>");
if (moduledokeoseleve == "oui") { 
	if (moduleelearning == "dokeos") {
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./acces_dokeos.php' target='_blank' >"+langmenuadmin103+"</a><br>");
	}else{
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./moodle/login/index.php' target='_blank' >"+langmenuadmin103+"</a><br>");
	}
}
if (moduleelevecdi != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./pmb/opac_css/' target='_blank' >C.D.I.</a><br>");
document.write(" </td>");
document.write("</tr>");
document.write("</table> ");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='160'>");
-->
