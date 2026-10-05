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
	document.write("<div align='center'><a href='#' onclick=\"open('besoin_daide.php','_self','')\" class='m'>"+langtitre0+"</a></div>");
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
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes')\"   class='m'>"+langtitre3+"</a></div>");
document.write("</td>");
document.write("<td width='20%' height='20'>");
document.write("<div align='center'><a href='#'   onclick='quitter_session()';  class='m'>"+langtitre5+"</a>");
document.write("&nbsp;&nbsp;&nbsp;&nbsp;<a href='./verrou.php'><img src='image/commun/img_ssl_mini.png' border='0' align='center' alt='verrouiller' /></a> </div>");
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
	if ((webrad == "oui") && (moduleradio == "oui"))  { 
		document.write("<tr>");
		document.write("<td colspan='3' id='coulTitre0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' ><a href='#' onMouseOver=\"AffBulleRadioAvecQuit('','','<div id=affradio ><iframe src=https://www.triade-educ.org/webradio/infomusic-2.php  height=100 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=NO ></iframe></div>'); window.status=''; return true;\" onMouseOut='HideBulleRadio()' onclick=\"open('webradio.php','webradio','width=329,height=195');return false\" ><img src='image/commun/webradio.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); ' /></a></td>"); 
		document.write("</tr>");
		document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
	}else{
		document.write("<tr>");
		document.write("</tr>");
	}
} 
document.write("<tr>");
document.write("<td colspan='3'  id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>&#128274; "+langmenuadmin07+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3'  id='coulModule0' >");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<span data-title=\"Espace Privé\" data-step='1' data-intro =\"Configuration de votre compte, accès à votre agenda privé, de votre stockages, d'une messagerie instantanée, etc... \" >");
document.write("<span data-title=\"<font color=red>Important !!</font>\" data-step='2' data-intro =\"A consulter en premier, configuration de vos informations. \" ><img src='./image/cube.gif' width='4' height='4'> <a href='gescompte.php' id='menumodule0'  >"+langmenugeneral01+"</a></span><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='memo.php' id='menumodule0'  >"+langmenugeneral01a+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./triade-phone.php' id='menumodule0' >Triade-Phone</a><br>");
if (moduleagendaprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' id='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop','');\" >"+langmenuadmin00+"</a><br>"); }
if (modulestockageprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500')\" id='menumodule0' >"+langmenuadmin06+"</a><br>"); }
if (moduleintramsnprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./intra-msn.php' id='menumodule0' >Triade-Msn</a><br>"); }
if (modulefluxrssprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./flux.php' id='menumodule0' >"+langmenuadmin521+"</a><br>"); }
if (modulecantineprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cantine_consulte.php' >"+langmenupersonnel2+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
document.write(" </p>");
document.write("</span></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
if (modulemessagerieprof == "oui") {
	document.write("<tr>");
	document.write(" <td colspan='3' id='coulTitre0'  style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font  id='menumodule1'>&#9993; "+langmenuprof1+"</font></b></td>");
	document.write("</tr>");
	document.write(" <tr>");
	document.write("<td colspan='3'  id='coulModule0'>");
	document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
	document.write("<span data-title=\"Messagerie interne\" data-step='3' data-intro =\"Gestion de la messagerie, permettant d'envoyer, recevoir des messages internes aux utilisateurs Triade de cet établissement.\" ><img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_reception.php'>"+langmenuprof12+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_envoi.php'>"+langmenuprof11+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./corbeille_message.php' id='menumodule0'>"+langmenuadmin023+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_suppression.php'>"+langmenuprof13+"</a><br>");
	document.write("</span></p>");
	document.write("</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
}
document.write("<tr>");
document.write("<td colspan='3' id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px'><b><font id='menumodule1'>&#128249; Visioconf&#233;rence</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./tvisio.php?p=rooms'>Salles actives</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./tvisio.php?p=create'>Cr&#233;er une salle</a><br>");
document.write("</p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulTitre0'  style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font  id='menumodule1'>&#128221; "+langmenuprof2+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write(" <p style='margin-left: 2; margin-top:5; margin-bottom:5'><span data-title=\"Gestion des notes\" data-step='4' data-intro =\"Ces modules vous permet de saisir, modifier ou encore consulter vos notes et devoir concernant vos étudiants. Ainsi que la saisie des commentaires dans les bulletins avec l'aide de l'IA si vous le souhaitez.\" >");
if (modulenotesprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./noteajout.php'>"+langmenuprof21+"</a><br>"); }
if (modulenotesprof == "oui") {	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notemodif.php'>"+langmenuprof23+"</a><br>"); }
if (modulesuppdevoirprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notesupp.php'>"+langmenuprof25+"</A><br>"); }
if (modulevisudevoirprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./devoirvisu.php'>"+langmenuprof24+"</A><br>"); }
if (modulenotesprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./notevisu.php'>"+langmenuprof22+"</a><br>"); }
if (modulecahiertextprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertext.php'>"+langmenuprof27+"</a><br>"); }
if (modulebulletinprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./bulletincomprof.php'>"+langmenuprof28+"</a><br>"); }
document.write("</span></p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<td colspan='3' id='coulTitre0'  style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font  id='menumodule1'>&#128101; "+langmenuprof3+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write("<p style='margin-left: 2; margin-top:5; margin-bottom:5'>");
document.write("<span data-title=\"Gestion scolaire\" data-step='5' data-intro =\"Saisissez les absences, retards, consulter les fiches de vos étudiants, mettez en place un coaching pour vos élèves, et plein d'autres modules...\" >");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./retardprof.php'>"+langmenuprof30+"</a><br>");
if (modulesanctionprof == "oui" ) { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./discipline_prof.php'>"+langmenuprof45+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./profp.php'>"+langmenuprof31+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./coach-config.php'>Coaching Config</a><br>");
if (moduleprofgestionsavoiretre == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./savoiretre.php'>"+langmenuadmin912+"</A><br>");
if (moduleficheeleveprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./ficheeleve.php'>"+langmenuprof32+"</a><br>"); }
if (modulelisteleveprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./consult_classe_prof.php'>"+langmenuscolaire11+"</a><br>"); }
if (moduleprofemargement == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./emargement.php'>"+langmenuadmin97+"</a><br>"); }
if (moduleplanprof == "oui" ) { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./planclasse-visu.php'>"+langmenuprof46+"</a><br>"); }
document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_delegue_impr.php'>"+langmenuparent36+"</a><br>");
if (modulestageproprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_stage_visu_eleve.php?nc'>"+langmenuprof47+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./trombi-prof.php'>"+langmenuadmin518+"</a><br>");
if (moduledokeosprof == "oui") { 
	if (moduleelearning == "dokeos") {
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./acces_dokeos.php' target='_blank' >"+langmenuadmin103+"</a><br>");
	}else{
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./moodle/' target='_blank' >"+langmenuadmin103+"</a><br>");
	}

}

//document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='triade-pedago.php' target='_blank'  >Triade-Pedago</a><br>");

document.write("</span></p>");
document.write("</td>");
document.write("</tr>");
document.write(" <tr>");
document.write("  <td colspan='3' height=19>&nbsp;</td>");
document.write(" </tr>");
document.write(" <tr>");
document.write("  <td colspan='3' height='13' id='coulTitre0'  style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font  id='menumodule1'>&#127963; "+langmenuprof4+"</font></b></td>");
document.write("  </tr>");
document.write(" <tr>");
document.write("  <td colspan='3' id='coulModule0'>");
document.write("    <p style='margin-left: 2; margin-bottom:5; margin-top:5'>");
document.write("<span data-title=\"Administratif\" data-step='6' data-intro =\"Consulter les informations de l'établissement scolaire, l'emploi du temps, les devoirs sur table, le calendrier des évènements, etc...  \" >");

//document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('./organigramme/index.php','orga','width=800,height=700,resizable=yes,scrollbars=auto')\" id='menumodule0' >"+langmenuadmin528+"</a><br>");
if (modulepacteprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./profppacte.php'>Pacte Enseignant</a><br>"); }
if (moduleresaprof == "oui") { document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./resa_prof.php'>"+langmenuprof40+"</a><br>"); }
document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onclick=\"open('edt_visu_prof.php','edt_prof','width=1050,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')\">"+langmenuprof41+"</a><br>");
if (modulecomptaprof == "oui") {
	document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_vacation_ens3.php'>"+langmenuadmin525+"</a><br>");
	document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_vacation_ens_vers.php'>"+langmenuadmin525bis+"</a><br>");
}

if (modulecirculaireprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./circulaire_liste.php'>"+langmenuprof42+"</a><br>"); }
if (moduleinformationprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./information.php'>"+langmenuprof29+"</a><br>"); }
if (moduleplanningprof == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_evenement_visu_readonly.php'>"+langmenuparent34bis+"</a><br>");}
if (modulesdstprofacces == "oui") { document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_dst_visu_readonly.php'>"+langmenuprof44+"</a><br>"); }
document.write("</span> </td>");
document.write("</tr>");
document.write("</table> ");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='160'> ");


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

function GetId(id) {
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
                                                     icon: '/image/commun/triade-ico.gif',
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
