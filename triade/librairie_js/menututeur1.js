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
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes')\" class='m'>"+langtitre3+"</a></div>");
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
document.write("<td colspan='3' height='38' id='coulModule0' >");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='gescompte.php' id='menumodule0'  >"+langmenugeneral01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' id='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop','resizable=yes,width=1000,height=700');\" >"+langmenuadmin00+"</a><br>");
// document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500')\" id='menumodule0' >"+langmenuadmin06+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./flux.php' id='menumodule0' >"+langmenuadmin521+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
document.write(" </p>");
document.write("</td>");
document.write("</tr>")
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
if (modulemessagerietuteur == "oui") {
	document.write("<tr>");
	document.write(" <td colspan='3' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenuparent1+"</font></b></td>");
	document.write("</tr>");
	document.write(" <tr>");
	document.write(" <td colspan='3'  id='coulModule0'>");
	document.write("  <p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
	document.write(" <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_reception.php'>"+langmenuparent12+"</a><br>");
	document.write(" <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_envoi.php'>"+langmenuparent11+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_suppression.php' id='menumodule0'>"+langmenuadmin04+"</a><br>");
	document.write(" </p>");
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
if (moduletuteurnote == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notesEleve.php' >"+langmenuparent21+"</a><br>"); }
if (moduletuteurabs == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_retard_parent.php' >"+langmenuparent22+"</a><br>"); }
if (moduletuteurabs == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_absence_parent.php' >"+langmenuparent23+"</a><br>"); }
if (moduletuteurgestionsavoiretre == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./savoiretrevisututeur.php'>"+langmenuadmin912+"</A><br>");
if (modulebulletinvisueleve == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_bulletin.php'>"+langmenuadmin6+"</a><br>"); }
if (moduletuteurdispence == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_dispence_parent.php' >"+langmenuparent24+"</a><br>"); }
if (moduletuteurdiscipline == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visu_discipline_parent.php' >"+langmenuparent25+"</a><br>"); }
if (moduletuteuredt == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick=\"open('edt_visu_el.php','edt_el','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')\" >"+langmenuparent31+"</a><br>"); }
if (moduletuteurcahierdetexte == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertext_visu.php' >"+langmenuparent27+"</a><br>"); }
if (moduletuteurcirculaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./circulaire_liste.php'  >"+langmenuparent32+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./information.php'>"+langmenuprof29+"</a><br>");
if (moduletuteurcalendrier == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_evenement_visu_readonly.php'>"+langmenuparent34+"</a><br>"); }
document.write("</p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("</table> ");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='160'> ");


-->
