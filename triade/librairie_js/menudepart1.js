document.write("</div></td>");
document.write("</tr>");
document.write("<tr> </tr>");
document.write("<tr> </tr>");
document.write("</table>");
document.write("<nav aria-label=\"Liens et contacts de l'établissement\" class='menu-top-nav'>");
document.write("<table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' height='20' class='coulBar0'>");
document.write("<tr class='coulBar0'>");
if (urlnomcontact != "") {
	document.write("<td  align='center' >&nbsp;<font class='m1'><a href='"+urlcontact+"' target='_blank' rel='noopener noreferrer' aria-label=\""+urlnomcontact+" (nouvelle fenêtre)\" class='m'>"+urlnomcontact+"</A></font>&nbsp;</td>");
}else{
	document.write("<td  align='center' ><font class='m1'>&nbsp;</font></td>");
}
if (urlnomcontact2 != "") {
	document.write("<td align='center' >&nbsp;<font class='m1'><a href='"+urlcontact2+"' target='_blank' rel='noopener noreferrer' aria-label=\""+urlnomcontact2+" (nouvelle fenêtre)\" class='m'>"+urlnomcontact2+"</A></font>&nbsp;</td>");
}else{
	document.write("<td align='center' ><font class='m1'>&nbsp;</font></td>");
}
if (urlnomcontact3 != "") {
	document.write("<td align='center' >&nbsp;<font class='m1'><a href='"+urlcontact3+"' target='_blank' rel='noopener noreferrer' aria-label=\""+urlnomcontact3+" (nouvelle fenêtre)\" class='m'>"+urlnomcontact3+"</A></font>&nbsp;</td>");
}else{
	document.write("<td align='center' ><font class='m1'>&nbsp;</font></td>");
}
if (urlnomcontact4 != "") {
	document.write("<td align='center' >&nbsp;<font class='m1'><a href='"+urlcontact4+"' target='_blank' rel='noopener noreferrer' aria-label=\""+urlnomcontact4+" (nouvelle fenêtre)\" class='m'>"+urlnomcontact4+"</A></font>&nbsp;</td>");
}else{
	document.write("<td align='center' ><font class='m1'>&nbsp;</font></td>");
}
if (mailcontact != "") {
	document.write("<td width='5' align='right'>&nbsp;<font class='m1'><a href='mailto:"+mailcontact+"' class='m'>"+langtitredepart2+"</A></font>&nbsp;</td>");
}else{
	document.write("<td width='5'><font class='m1'>&nbsp;</td>");
}
document.write("</tr>");
document.write("</table>");
document.write("</nav>");
document.write("</div>");
document.write("<div align='left'>");
document.write("<table role='presentation' border='0' cellpadding='0' cellspacing='0'  width='100%' height='379'>");
document.write("<tr valign='top'><td colspan='5' height='20'><img src='./image/inc/omb.gif' width='100%' height='6' alt='' aria-hidden='true'></td></tr>");
document.write("<tr><td valign='top' width='123' height='527'>");
document.write("<nav aria-label=\"Menu de connexion\" class='menu-side-nav'>");
document.write("<table role='presentation' width='100%' border='0' cellspacing='1' cellpadding='1' height='83'>");

document.write("<tr><td colspan='3' height='7' class='coulTitre0'  style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><span class='menumodule1'><span aria-hidden='true'>&#128100; </span>"+langmenudepart0+"</span></b></td></tr>");
document.write("<tr><td colspan='3' height='4'  class='coulModule0' >");
document.write("<p><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=parent&saisie_titre="+langtitremembre4+"' >"+langmenudepart04+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=eleve&saisie_titre="+langtitremembre5+"' >"+langmenudepart05+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=administrateur&saisie_titre="+langtitremembre1+"' >"+langmenudepart01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=vie%20scolaire&saisie_titre="+langtitremembre2+"' >"+langmenudepart02+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=enseignant&saisie_titre="+langtitremembre3+"'>"+langmenudepart03+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=tuteurstage&saisie_titre="+langtitremembre6+"' >"+langmenudepart06+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./acces_depart.php?saisie_membre=personnel&saisie_titre="+langtitremembre7+"' >"+langmenudepart07+"</a><br>");
document.write("</p>");
document.write("</td></tr>");


if (preinscription == "oui") {
document.write("<tr><td colspan='3' height='4'>&nbsp;</td></tr>");
document.write("<tr><td colspan='3'  style='border-radius: 5px 5px 0px 0px; padding-left:5px'  class='coulTitre0' height='4'><b><span class='menumodule1'><span aria-hidden='true'>&#128221; </span>"+langmenudepart3+"</span></b></td></tr>");
document.write("<tr><td colspan='3' height='4'  class='coulModule0' >");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./preinscription_eleve.php'>"+langmenudepart30+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./ouverture_session_preinscription_eleve.php'>"+langmenudepart31+"</a><br>");
document.write("</td></tr>");
}

document.write("<tr><td colspan='3' height='4'>&nbsp;</td></tr>");
document.write("<tr><td colspan='3'  style='border-radius: 5px 5px 0px 0px; padding-left:5px' class='coulTitre0' height='4'><b><span class='menumodule1'><span aria-hidden='true'>&#127891; </span>"+langmenudepart1+"</span></b></td></tr>");
document.write("<tr><td colspan='3' height='4'  class='coulModule0' >");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./index1.php'>"+langmenudepart10+"</a><BR>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./a_propos.php'>"+langmenudepart11+"</a><BR>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./rgpd.php'>"+langmenudepart13+"</a><BR>");
document.write("</td></tr>");
document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>");
document.write("<table role='presentation' border=0 cellspacing=10 ><tr><td>");
document.write("<img src='./image/commun/edge.png' width=16 height=16 alt='' aria-hidden='true'></td><td><img src='./image/commun/firefox.png' width=16 height=16 alt='' aria-hidden='true'></td><td><img src='./image/commun/opera.png' width=16 height=16 alt='' aria-hidden='true'>");
document.write("</td><td><img src='./image/commun/chrome.png' width=16 height=16 alt='' aria-hidden='true'></td></tr></table>");
document.write("<table role='presentation' border=0 cellspacing=10><tr><td>");
document.write("<img src='./image/commun/linux.png' width=16 height=16 alt='' aria-hidden='true'></td><td><img src='./image/commun/windows.png' width=16 height=16 alt='' aria-hidden='true'></td><td><img src='./image/commun/MacOS.png' width=16 height=16 alt='' aria-hidden='true'></td><td><img src='./image/commun/android.png' width=16 height=16 alt='' aria-hidden='true'>");
document.write("</td></tr></table>");

document.write("<br/><table role='presentation' border=0 cellspacing=10 >");
document.write("<tr>");
document.write("<td align='center'>");
logo();
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='2'><br><center>");
document.write("<a href='https://www.triade-educ.org/accueil/triade-ia.php' target='_blank' rel='noopener noreferrer' aria-label=\"Triade IA (nouvelle fenêtre)\" style='text-decoration:none'>");
document.write("<button type='button' aria-label='Accéder à Triade IA avec OpenAI' style='display:inline-flex;align-items:center;background:#fff;border-radius:12px;padding:8px 10px;border:none;box-shadow:0 4px 12px rgba(0,0,0,.18),0 1px 3px rgba(0,0,0,.12);cursor:pointer;margin:3px;'><img src='./image/commun/openai-lockup.png' alt='OpenAI' height='20' width='auto' style='object-fit:contain;'></button>");
document.write("<button type='button' aria-label='Accéder à Triade IA avec Mistral AI' style='display:inline-flex;align-items:center;background:#fff;border-radius:12px;padding:8px 10px;border:none;box-shadow:0 4px 12px rgba(0,0,0,.18),0 1px 3px rgba(0,0,0,.12);cursor:pointer;margin:3px;'><img src='./image/commun/logo-mistral.png' alt='Mistral AI' height='20' width='auto' style='object-fit:contain;'></button>");
document.write("</a></center>");
document.write("</td>");
document.write("</tr>");



document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
document.write("<tr>");document.write("<td align='center' ><a href='https://play.google.com/store/apps/details?id=com.triade.educ.phone&hl=fr' target='_blank' rel='noopener noreferrer' aria-label=\"Application mobile Triade sur Google Play (nouvelle fenêtre)\" ><img border='0' src='./image/google-play.png' alt='Disponible sur Google Play' width='60%' /></a></td>");document.write("</tr>");

document.write("</table>");

document.write("</td></tr>");
document.write("</table>");
document.write("</nav>");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='527'>");
