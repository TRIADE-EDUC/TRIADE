<!--
document.write("<td valign='top' width='123' align='right' height='527'>");
document.write("<table width='100%' border='0' cellspacing='1' cellpadding='1'><tr>");
document.write(" <td colspan='3'  id='coulTitre0' ><b><font id='menumodule1'>"+langtitre1+"</font></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3' height='38' id='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
if (modulenewspageviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./newsactualite.php' id='menumodule0' >"+langmenuadmin01A+"</a><br>"); }
if (modulenewsviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./actualiteetablissement.php' id='menumodule0' >"+langmenuadmin01B+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./newsdefil.php' id='menumodule0' >"+langmenuadmin01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./commaudio.php' id='menumodule0' >"+langmenuadmin05+"</a><br>");
document.write(" </p>");
document.write("</td>");
document.write("</tr>");

document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");

document.write("<tr id='coulTitre0' ><td colspan='3' ><b><font  id='menumodule1'>"+langmenuadmin6+"</font></b></td></tr>");
document.write("<tr>");
document.write("<td colspan='3' height='27' id='coulModule0'>");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./information.php'>"+langmenuprof29+"</a><br>");
if (modulevisaviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./visa_scolaire.php'>"+langmenuadmin69+"</a><br>"); }
if (modulenoteviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./note_scolaire.php'>"+langmenuadmin73+"</a> <br>"); }
if (noteenseignantviascolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./carnetnote.php'>"+langmenuadmin55+"</a> <br>"); }
if (moduleimptableauviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_tableaupp.php'>"+langmenuadmin66+"</a><br>"); }
if (modulebulletinviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_trimestre.php'>"+langmenuadmin63+"</a><br>"); }
if (moduleperiodeviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./imprimer_periode.php'>"+langmenuadmin64+"</a><br>"); }
if (modulevideoprojoviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('./video-proj-index.php','video','width=800,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\" >"+langmenuadmin65+"</a></td>"); }
document.write("</tr>");
document.write("<tr>");

if (moduleviescolairechambre == "oui") {
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

document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height='13' id='coulTitre0' ><b><font id='menumodule1'>"+langmenuscolaire1+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write("<p style='margin-left: 2px; margin-bottom:5px; margin-top:5px'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./listing.php'>"+langmenuadmin3+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./consult_classe.php'>"+langmenuscolaire11+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./ficheeleve.php'>"+langmenuprof32+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_groupe_mvs.php'>"+langmenuadmin54+"</a><br>");
if (moduleplanclasseviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./planclasse-visu-sco.php'>"+langmenuprof46+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertext_scolaire.php'>"+langmenuprof27+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./tronbinoscope0.php'>"+langmenuadmin518+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./edt.php'>"+langmenuscolaire12+"</a><br>");
if (modulevacationviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_vacation.php' >"+langmenuadmin525+"</a><br>"); }
if (modulehistoryviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./history_cmd.php' >"+langmenuadmin44+"</a><br>"); }
if (moduleresaviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./resr_admin.php' >"+langmenuadmin40+"</a><br>"); }
if (modulepreinscriptionviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./listepreinscription.php' id='menumodule0' >"+langmenuadmin102+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./emargement.php' >"+langmenuadmin97+"</a><br>");
if (moduleexportviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./export.php' id='menumodule0'>"+langmenuadmin96+"</a><br>"); }
document.write(" </td>");
document.write("</tr>");
document.write("<tr><td colspan='3' height='19'>&nbsp;</td></tr>");
document.write("</table> ");
document.write("</td>");
document.write("</tr>");
document.write("<tr valign='middle'>");
document.write("<td colspan='5' height='43'>");
document.write("<div align='center'>");
document.write("<br><p>"+langmenupied+"</p>");
document.write(img_logo_pied);
document.write("</div>");
document.write("</td></tr></table>");
document.write("</div>");
document.write("</td></tr></table>");
document.write("</center>");
-->
