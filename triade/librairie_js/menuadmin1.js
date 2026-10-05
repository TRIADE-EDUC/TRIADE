// onclick='pasdispo1();'
document.write("</div></td>");
document.write("</tr>");
document.write("<tr> </tr>");
document.write("<tr> </tr>");
document.write("</table>");
document.write("<nav aria-label=\"Menu d'accès rapide\" class='menu-top-nav'>");
document.write("<table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' height='20' class='coulBar0'>");
document.write("<tr class='coulBar0'>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./acces2.php' class='m'>"+langtitre1+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
if (lienassist == '0' ) {
	document.write("<div align='center'><a href='./besoin_daide.php' class='m' data-title=\"IA à portée de main\"  data-step='7' data-intro =\"Vous disposez d'une intelligence artificiel via ce module ainsi que tous les modules s'appyant sur la technologie <b>OpenAI</b> \"   >"+langtitre0+"</a></div>");
}else{
	document.write("<div align='center'><a href='"+lienassist+"' class='m' target='_blank' rel='noopener noreferrer' aria-label=\""+langtitre0+" (nouvelle fenêtre)\" >"+langtitre0+"</a></div>");
}
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./acces2.php' target='_blank' rel='noopener noreferrer' aria-label=\""+langtitre2bis+" (nouvelle fenêtre)\" class='m'>"+langtitre2bis+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes'); return false;\" role='button' class='m' >"+langtitre3+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./"+REPADMIN+"/index.php' class='m' target='_blank' rel='noopener noreferrer' aria-label=\""+langtitre7+" (nouvelle fenêtre)\" data-title=\"A consulter !!\"  data-step='8' data-intro =\"Accès au compte administateur permettant de configurer Triade.\"  >"+langtitre7+"</a>");
document.write("</div>");
document.write("</td>");
document.write("<td height='20'width='16%'>");
document.write("<div align='center'><a href='#'  onclick='quitter_session(); return false;' role='button' class='m'>"+langtitre5+"</a>");
document.write(" &nbsp;&nbsp;&nbsp;&nbsp;<a href='./verrou.php' title='Mise en veille de la session' aria-label='Mise en veille de la session'><img src='image/commun/img_ssl_mini.png' border='0' align='center' alt='Mise en veille' /></a> </div>");
document.write("</td>");
document.write(" </form>");
document.write(" </tr>");
document.write(" </table>");
document.write("</nav>");
document.write("</div>");
document.write("<div align='left'>");
document.write("<table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' height='379'>");
document.write("<tr valign='top'>");
document.write("<td colspan='5' height='20'><img src='./image/inc/omb.gif' width='100%' height='6' alt='' aria-hidden='true'></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td valign='top' width='123' height='160'>");
document.write("<nav aria-label=\"Menu des fonctions Direction\" class='menu-side-nav'>");
document.write("<table role='presentation' width='100%' border='0' cellspacing='1' cellpadding='1' height='83'>");

if ((GRAPH == '20') || (GRAPH == '21'))  {
	document.write("<tr>");
	document.write("<td colspan='3' align='center' ><a href='#' onClick=\"open('http://www.pigier.tv','pigiertv','width=960,height=500'); return false;\" role='button' ><img src='image/commun/logopigiertv.jpg' align='center' border='0' alt='Pigier TV' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' /></a></td>");
	document.write("</tr>");
	document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
}else{	
	if ((webrad == "oui") && (moduleradio == "oui"))   { 
		document.write("<tr>");
		document.write("<td colspan='3' class='coulTitre0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' ><a href='#' onMouseOver=\"AffBulleRadioAvecQuit('','','<div id=affradio ><iframe src=https://www.triade-educ.org/webradio/infomusic-2.php  height=100 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=NO ></iframe></div>'); window.status=''; return true;\" onMouseOut='HideBulleRadio()' onclick=\"open('webradio.php','webradio','width=329,height=195');return false\" role='button' ><img src='image/commun/webradio.jpg' align='center' border='0' alt='Webradio' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); ' /></a></td>"); 
		document.write("</tr>");
		document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
	}else{
		document.write("<tr>");
		document.write("</tr>");
	}
}	
document.write("<tr>");
document.write("<td colspan='3' class='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><span class='menumodule1'><span aria-hidden='true'>&#128274; </span>"+langmenuadmin07+"</span></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3' height='28' class='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
document.write("<span data-title=\"Espace Privé\" data-step='9' data-intro =\"Configuration de votre compte, accès à votre agenda privé, de votre stockages, d'une messagerie instantanée, etc... \" ><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='gescompte.php' class='menumodule0' data-title=\"Espace Privé\" >"+langmenugeneral01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='memo.php' class='menumodule0'  >"+langmenugeneral01a+"</a><br>");
if (moduleagendaadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='#' class='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop',''); return false;\" role='button' >"+langmenuadmin00+"</a><br>"); }
if (modulestockageadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500'); return false;\" role='button' class='menumodule0' >"+langmenuadmin06+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./triade-phone.php' class='menumodule0' >Triade-Phone</a><br>"); 
if (moduleintramsnadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./intra-msn.php' class='menumodule0' >"+langmenuadmin100+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
if (modulefluxrssadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./flux.php' class='menumodule0' >"+langmenuadmin521+"</a><br>"); }
if (modulecantineadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./cantine_consulte.php' >"+langmenupersonnel2+"</a></span><br>"); }
document.write(" </p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");
document.write(" <td colspan='3' class='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><span class='menumodule1'><span aria-hidden='true'>&#9993; </span>"+langmenuadmin0+"</span></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3' class='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
if (modulemessagerieadmin == "oui") {
	document.write("<span data-title=\"Messagerie interne\" data-step='11' data-intro =\"Gestion de la messagerie, permettant d'envoyer, recevoir des messages internes aux utilisateurs Triade de cet établissement.\" ><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./messagerie_reception.php' class='menumodule0'>"+langmenuadmin03+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./messagerie_envoi.php' class='menumodule0'>"+langmenuadmin02+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./corbeille_message.php' class='menumodule0'>"+langmenuadmin023+"</a><br>");
//	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./messagerie_brouillon.php' class='menumodule0'>"+langmenuadmin022+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./messagerie_suppression.php' class='menumodule0'>"+langmenuadmin04+"</a><br>");
}
document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./sms-mess0.php' class='menumodule0' >"+langmenuadmin522+"</a><br>");
document.write(" </span></p>");
document.write("</td>");
document.write("</tr>");

if (rubriquegestion != "non") {
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' class='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><span class='menumodule1'><span aria-hidden='true'>&#9881; </span>"+langmenuadmin1+"</span></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='10' class='coulModule0' >");
	document.write(" <p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");

	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' data-step='1' data-title=\"Au commencement...\"  data-intro=\"Configuration des dates de début et de fin de l'année scolaire\"  href='./configannee.php'>"+langmenuadmin529+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' data-step='2' data-intro =\"Configuration des coordonnées de l\'établissement. Possibilité d'enregistrer plusieurs sites. <font color=red>L'année scolaire en cours doit être aussi indiquée !</font>\" href='./param.php'>"+langmenuadmin61+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' data-step='3' data-intro =\"Configuration des dates trimestrielles ou semestrielles pour toutes les classes.\" href='./definir_trimestre.php'>"+langmenuadmin62+"</a> <br>");

	document.write("<span data-title=\"Gestion utilisateurs\"  data-step='4' data-intro =\"Création des différents comptes. Possibilité de faire des imports pour chaque rubrique.\"><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_admin.php' class='menumodule0'>"+langmenuadmin11+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_scolaire.php' class='menumodule0'>"+langmenuadmin12+"</a><BR>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_prof.php' class='menumodule0'>"+langmenuadmin13+"</A><br>");
	if (moduleadminsuppleant == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_suppleant.php' class='menumodule0'>"+langmenuadmin14+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_tuteur.php' class='menumodule0'>"+langmenuadmin20+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_personnel.php' class='menumodule0'>"+langmenuadmin104+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_eleve.php' class='menumodule0'>"+langmenuadmin15+"</a><br></span>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./gestion_groupe.php' class='menumodule0'>"+langmenuadmin16+"</a><br>");
	document.write("<span data-step='5' data-intro =\"Création des classes et des matières. Possibilité de faire des imports au format excel.\"  ><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_classe.php' class='menumodule0'>"+langmenuadmin17+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_matiere.php' class='menumodule0'>"+langmenuadmin18+"</a><br>");
	if (moduleadminsousmatiere == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./creat_sousmatiere.php' class='menumodule0'>"+langmenuadmin19+"</a></span><br>");
	if (modulepacteadmin == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./pacte_enseignant.php' class='menumodule0'>Pacte Enseignant</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./tronbinoscope0.php' class='menumodule0' >"+langmenuadmin518+"</a><br>");
	if (moduleadmingestiondelegue == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./gestion_delegue.php'>"+langmenuadmin41bis+"</a><br>");
	//document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='#' onclick=\"open('./organigramme/admin/index.php','orga','width=800,height=700,resizable=yes,scrollbars=auto')\" class='menumodule0' >"+langmenuadmin528+"</a><br>");
	if (moduleadminpreinscription == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./listepreinscription.php' class='menumodule0' >"+langmenuadmin102+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='reglement.php' >"+langmenuadmin101+"</a><br>");
	if (moduleadmingestionsms == "oui") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./sms.php'>"+langmenuadmin515+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./codebar0.php'>"+langmenuadmin519+"</a><br>");
	document.write("</p>");
	document.write("</td>");
	document.write("</tr>");
}


if ((moduledroitscolariteadmin == "oui") ||  (modulevacationadmin == "oui")) {
	document.write("</tr><tr><td colspan='3' height=19>&nbsp;</td></tr>");
	document.write("<tr class='coulTitre0'><td colspan='3' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><span class='menumodule1'><span aria-hidden='true'>&#128176; </span>"+langmenuadmin90+"</span></b></td></tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='27' class='coulModule0'>");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	if (moduledroitscolariteadmin == "oui") {
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./comptaetat.php'>"+langmenuadmin91bis+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./comptavers.php' >"+langmenuadmin92bis+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./compta_consulte_retard.php'>"+langmenuadmin93+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./comptaconfig.php'>"+langmenuadmin94+"</a><br />");
	}
	if (modulevacationadmin == "oui") { 
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./gestion_vacation.php' >"+langmenuadmin525+"</a><br>"); 
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./gestion_entretient_enseignant.php' >"+langmenuadmin399+"</a><br>"); 
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./quantification.php' >"+langmenuadmin95+"</a><br>");
	}
	if (moduleboursieradmin == "oui") { 
		document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a class='menumodule0' href='./boursier.php' >"+langmenuadmin1011+"</a><br>");
	}
	document.write("</tr>");
}


if (rubriqueaffectation != "non") { 
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='13' class='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><span class='menumodule1'><span aria-hidden='true'>&#128203; </span>"+langmenuadmin3+"</span></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' class='coulModule0'>");
	document.write("<p style='margin-left: 2px; margin-bottom:5px; margin-top:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./listing.php' class='menumodule0'>"+langmenuadmin37+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./edt.php' class='menumodule0'>"+langmenuadmin31+"</a><br>");
	if (moduleadminprofp != "non")	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./profpcreat.php' class='menumodule0'>"+langmenuadmin32+"</a><br>");
	document.write("<span data-title=\"Le coeur de Triade !!\"  data-step='6' data-intro =\"<font color='red'>Rubrique important !!</font><br>Ces modules vous permet d'assigner un enseignant à une classe et sa matière. Sans ce module renseigné, aucune action ne sera possible par vos utilisateurs.\"><img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./affectation_creation_key.php' class='menumodule0'>"+langmenuadmin33+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./affectation_visu.php' class='menumodule0'>"+langmenuadmin34+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./affectation_modif_key.php' class='menumodule0'>"+langmenuadmin35+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./suppression_affectation.php' class='menumodule0'>"+langmenuadmin36+"</a></span><br>");
	if (moduleadminconfignoteusa != "non") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./gestionnoteusa.php' class='menumodule0'>"+langmenuadmin38+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./vatel_gestion_ue.php' class='menumodule0'>"+langmenuadmin98+"</a><br>");
	document.write("</td>");
	document.write("</tr>");
}	

if (rubriqueetablissement != "non") { 
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<td colspan='3' height='2' class='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><span class='menumodule1'><span aria-hidden='true'>&#127979; </span>"+langmenuadmin8+"</span></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='2' class='coulModule0' >  ");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./base_de_donne_importation.php' class='menumodule0'>"+langmenuadmin81+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./export.php' class='menumodule0'>"+langmenuadmin96+"</a><br>");
	if (moduleadminarchivage != "non") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./archivage.php' class='menumodule0' >"+langmenuadmin84+"</a><br />");
	if (moduleadminpurgerinfo != "non") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./purge.php' class='menumodule0'>"+langmenuadmin92+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./elevesansclasse.php' class='menumodule0'>"+langmenuadmin83+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./chgmentClas.php' class='menumodule0'>"+langmenuadmin91+"</a><br>");
	langmenuadmin911 = langmenuadmin911.charAt(0).toUpperCase() + langmenuadmin911.slice(1);
	document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./historyEtudiant.php' class='menumodule0'>"+langmenuadmin911+"</a><br>");
	if (moduleadminnouvelleannee != "non") document.write("<img src='./image/cube.gif' width='4' height='4' alt='' aria-hidden='true'> <a href='./newannee.php' class='menumodule0'  >"+langmenuadmin99+"</a><br />");
	document.write("</p></td></tr>");
}

document.write("</table> ");
document.write("</nav>");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='160'>");

document.write("<div id='curseur' style='position:absolute;visibility:hidden;border: 1px solid Black;padding: 10px;font-family: Verdana, Arial;font-size: 10px;background-color: #FFFFCC;' ></div>");
//onclick='pasdispo1();'

function getRequete2() {
	if (window.XMLHttpRequest) { 
        	result = new XMLHttpRequest();     // Firefox, Safari, ...
	}else { 
	      if (window.ActiveXObject)  {
	      result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer 
	      }
       	}
	return result;
}

function alertSessionClose() {
//	montre();
}

function GetId(id)
{
return document.getElementById(id);
}
var i=false; // La variable i nous dit si la bulle est visible ou non
 
function move(e) {
  if(i) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
    if (navigator.appName!="Microsoft Internet Explorer") { // Si on est pas sous IE
    GetId("curseur").style.left=e.pageX + 5+"px";
    GetId("curseur").style.top=e.pageY + 10+"px";
    }
    else { // Modif proposé par TeDeum, merci à  lui
    if(document.documentElement.clientWidth>0) {
GetId("curseur").style.left=20+event.x+document.documentElement.scrollLeft+"px";
GetId("curseur").style.top=10+event.y+document.documentElement.scrollTop+"px";
    } else {
GetId("curseur").style.left=20+event.x+document.body.scrollLeft+"px";
GetId("curseur").style.top=10+event.y+document.body.scrollTop+"px";
         }
    }
  }
}
 
function montre() {
  if(i==false) {
  GetId("curseur").style.visibility="visible"; // Si il est cacher (la verif n'est qu'une securité) on le rend visible.
  GetId("curseur").innerHTML = "essai"; // on copie notre texte dans l'élément html
  i=true;
  }
}
function cache() {
if(i==true) {
GetId("curseur").style.visibility="hidden"; // Si la bulle est visible on la cache
i=false;
}
}
document.onmousemove=move; // dès que la souris bouge, on appelle la fonction move pour mettre à jour la position de la bulle.
//-->



function CnxEnCours() {
	var requete = getRequete2();
	var corps="nb="+encodeURIComponent(nb);
	if (requete != null) {
		requete.open("POST","verifConnex2.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					if (requete.responseText == "2") {
						alertSessionClose();
					}
					if (requete.responseText == "1") {
						location.href='verrou.php';
					}	
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}


function VerifNotif() {
        var requete = getRequete2();
        if (requete != null) {
                requete.open("POST","verifNotification.php",true);
                requete.onreadystatechange = function() {
                        if(requete.readyState == 4) {
                                if(requete.status == 200) {
					if (requete.responseText != "") {
						var notification = new Notification('TRIADE-NOTIF', {
//        	   				     icon: '/image/commun/triade-ico.gif',
						     icon: '/image/commun/icone-triade.png',
						     body: requete.responseText 
					        });
						notification.onclick = function () {
				                   // window.open('http://');
				                };
					}
                                }
                        };
                }
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send();
        }
}

var nb=0;
function CnxAjax() {
	CnxEnCours(nb);
	VerifNotif();
	nb++;
	window.setTimeout("CnxAjax()","300000"); //300000 -> 5 minutes
}
CnxAjax();
