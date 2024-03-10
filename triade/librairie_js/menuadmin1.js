// onclick='pasdispo1();'
document.write("</div></td>");
document.write("</tr>");
document.write("<tr> </tr>");
document.write("<tr> </tr>");
document.write("</table>");
document.write("<table border='0' cellpadding='0' cellspacing='0' width='100%' height='20' bgcolor='#175216'>");
document.write("<tr id='coulBar0' >");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./acces2.php' class='m'>"+langtitre1+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
if (lienassist == '0' ) {
	document.write("<div align='center'><a href='./besoin_daide.php' class='m'>"+langtitre0+"</a></div>");
}else{
	document.write("<div align='center'><a href='"+lienassist+"' class='m' target='_blank' >"+langtitre0+"</a></div>");
}
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./acces2.php' target='_blank' class='m'>"+langtitre2bis+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes')\"   class='m' >"+langtitre3+"</a></div>");
document.write("</td>");
document.write("<td height='20' width='16%'>");
document.write("<div align='center'><a href='./"+REPADMIN+"/index.php' class='m' target='_blank'>"+langtitre7+"</font>");
document.write("</div>");
document.write("</td>");
document.write("<td height='20'width='16%'>");
document.write("<div align='center'><a href='#'  onclick='quitter_session()'; class='m'>"+langtitre5+"</a>");
document.write(" &nbsp;&nbsp;&nbsp;&nbsp;<a href='./verrou.php'><img src='image/commun/img_ssl_mini.png' border='0' align='center' alt='Mise en veille' /></a> </div>");
document.write("</td>");
document.write(" </form>");
document.write(" </tr>");
document.write(" </table>");
document.write("</div>");
document.write("<div align='left'>");
document.write("<table border='0' cellpadding='0' cellspacing='0' width='100%' height='379'>");
document.write("<tr valign='top'>");
document.write("<td colspan='5' height='20'><img src='./image/inc/omb.gif' width='100%' height='6'></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td valign='top' width='123' height='160'>");
document.write("<table width='100%' border='0' cellspacing='1' cellpadding='1' height='83'>");

if ((GRAPH == '20') || (GRAPH == '21'))  {
	document.write("<tr>");
	document.write("<td colspan='3' align='center' ><a href='#' onClick=\"open('http://www.pigier.tv','pigiertv','width=960,height=500');\"  ><img src='image/commun/logopigiertv.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' /></a></td>");
	document.write("</tr>");
	document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
}else{	
	if ((webrad == "oui") && (moduleradio == "oui"))   { 
		document.write("<tr>");
		document.write("<td colspan='3' id='coulTitre0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' ><a href='#' onMouseOver=\"AffBulleRadioAvecQuit('','','<div id=affradio ><iframe src=https://www.triade-educ.org/webradio/infomusic.php  height=100 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=NO ></iframe></div>'); window.status=''; return true;\" onMouseOut='HideBulleRadio()' onclick=\"open('webradio.php','webradio','width=329,height=195');return false\" ><img src='image/commun/webradio.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); ' /></a></td>"); 
		document.write("</tr>");
		document.write("<tr><td colspan='3' height=19>&nbsp;</td></tr>");
	}else{
		document.write("<tr>");
		document.write("</tr>");
	}
}	
document.write("<tr>");
document.write("<td colspan='3'  id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>"+langmenuadmin07+"</font></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3' height='28' id='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='gescompte.php' id='menumodule0'  >"+langmenugeneral01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='memo.php' id='menumodule0'  >"+langmenugeneral01a+"</a><br>");
if (moduleagendaadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' id='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop','');\" >"+langmenuadmin00+"</a><br>"); }
if (modulestockageadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500')\" id='menumodule0' >"+langmenuadmin06+"</a><br>"); }
if (moduleintramsnadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./intra-msn.php' id='menumodule0' >"+langmenuadmin100+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
if (modulefluxrssadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./flux.php' id='menumodule0' >"+langmenuadmin521+"</a><br>"); }
if (modulecantineadmin == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cantine_consulte.php' >"+langmenupersonnel2+"</a><br>"); }
document.write(" </p>");
document.write("</td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write("</tr>");
document.write("<tr>");
document.write(" <td colspan='3'  id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>"+langmenuadmin0+"</font></b></td>");
document.write("</tr>");
document.write(" <tr>");
document.write("<td colspan='3'  id='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
if (modulemessagerieadmin == "oui") {
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_reception.php' id='menumodule0'>"+langmenuadmin03+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_envoi.php' id='menumodule0'>"+langmenuadmin02+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./corbeille_message.php' id='menumodule0'>"+langmenuadmin023+"</a><br>");
//	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_brouillon.php' id='menumodule0'>"+langmenuadmin022+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./messagerie_suppression.php' id='menumodule0'>"+langmenuadmin04+"</a><br>");
}
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./sms-mess0.php' id='menumodule0' >"+langmenuadmin522+"</a><br>");
document.write(" </p>");
document.write("</td>");
document.write("</tr>");

if (rubriquegestion != "non") {
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>"+langmenuadmin1+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='10' id='coulModule0' >");
	document.write(" <p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");

	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./configannee.php'>"+langmenuadmin529+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./param.php'>"+langmenuadmin61+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./definir_trimestre.php'>"+langmenuadmin62+"</a> <br>");

	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_admin.php' id='menumodule0'>"+langmenuadmin11+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_scolaire.php' id='menumodule0'>"+langmenuadmin12+"</a><BR>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_prof.php' id='menumodule0'>"+langmenuadmin13+"</A><br>");
	if (moduleadminsuppleant == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_suppleant.php' id='menumodule0'>"+langmenuadmin14+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_tuteur.php' id='menumodule0'>"+langmenuadmin20+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_personnel.php' id='menumodule0'>"+langmenuadmin104+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_eleve.php' id='menumodule0'>"+langmenuadmin15+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./gestion_groupe.php' id='menumodule0'>"+langmenuadmin16+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_classe.php' id='menumodule0'>"+langmenuadmin17+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_matiere.php' id='menumodule0'>"+langmenuadmin18+"</a><br>");
	if (moduleadminsousmatiere == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./creat_sousmatiere.php' id='menumodule0'>"+langmenuadmin19+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./tronbinoscope0.php' id='menumodule0' >"+langmenuadmin518+"</a><br>");
	if (moduleadmingestiondelegue == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_delegue.php'>"+langmenuadmin41bis+"</a><br>");
	//document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('./organigramme/admin/index.php','orga','width=800,height=700,resizable=yes,scrollbars=auto')\" id='menumodule0' >"+langmenuadmin528+"</a><br>");
	if (moduleadminpreinscription == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./listepreinscription.php' id='menumodule0' >"+langmenuadmin102+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='reglement.php' >"+langmenuadmin101+"</a><br>");
	if (moduleadmingestionsms == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./sms.php'>"+langmenuadmin515+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./codebar0.php'>"+langmenuadmin519+"</a><br>");
	document.write("</p>");
	document.write("</td>");
	document.write("</tr>");
}


if ((moduledroitscolariteadmin == "oui") ||  (modulevacationadmin == "oui")) {
	document.write("</tr><tr><td colspan='3' height=19>&nbsp;</td></tr>");
	document.write("<tr id='coulTitre0'><td colspan='3' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>"+langmenuadmin90+"</font></b></td></tr>");
	document.write(" <tr>");
	document.write("<td colspan='3' height='27' id='coulModule0'>");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	if (moduledroitscolariteadmin == "oui") {
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./comptaetat.php'>"+langmenuadmin91bis+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./comptavers.php' >"+langmenuadmin92bis+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./compta_consulte_retard.php'>"+langmenuadmin93+"</a><br />");
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./comptaconfig.php'>"+langmenuadmin94+"</a><br />");
	}
	if (modulevacationadmin == "oui") { 
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_vacation.php' >"+langmenuadmin525+"</a><br>"); 
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_entretient_enseignant.php' >"+langmenuadmin399+"</a><br>"); 
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./quantification.php' >"+langmenuadmin95+"</a><br>");
	}
	if (moduleboursieradmin == "oui") { 
		document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./boursier.php' >"+langmenuadmin1011+"</a><br>");
	}
	document.write("</tr>");
}


if (rubriqueaffectation != "non") { 
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='13' id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px' ><b><font id='menumodule1'>"+langmenuadmin3+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3'  id='coulModule0'>");
	document.write("<p style='margin-left: 2px; margin-bottom:5px; margin-top:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./listing.php' id='menumodule0'>"+langmenuadmin37+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./edt.php' id='menumodule0'>"+langmenuadmin31+"</a><br>");
	if (moduleadminprofp != "non")	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./profpcreat.php' id='menumodule0'>"+langmenuadmin32+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./affectation_creation_key.php' id='menumodule0'>"+langmenuadmin33+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./affectation_visu.php' id='menumodule0'>"+langmenuadmin34+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./affectation_modif_key.php' id='menumodule0'>"+langmenuadmin35+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./suppression_affectation.php' id='menumodule0'>"+langmenuadmin36+"</a><br>");
	if (moduleadminconfignoteusa != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./gestionnoteusa.php' id='menumodule0'>"+langmenuadmin38+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./vatel_gestion_ue.php' id='menumodule0'>"+langmenuadmin98+"</a><br>");
	document.write("</td>");
	document.write("</tr>");
}	

if (rubriqueetablissement != "non") {
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
	document.write("<td colspan='3' height='2' id='coulTitre0' style='border-radius: 5px 5px 0px 0px; padding-left:5px'  ><b><font id='menumodule1' >"+langmenuadmin8+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height='2' id='coulModule0' >  ");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'> ");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./base_de_donne_importation.php' id='menumodule0'>"+langmenuadmin81+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./export.php' id='menumodule0'>"+langmenuadmin96+"</a><br>");
	if (moduleadminarchivage != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./archivage.php' id='menumodule0' >"+langmenuadmin84+"</a><br />");
	if (moduleadminpurgerinfo != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./purge.php' id='menumodule0'>"+langmenuadmin92+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./elevesansclasse.php' id='menumodule0'>"+langmenuadmin83+"</a><br />");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./chgmentClas.php' id='menumodule0'>"+langmenuadmin91+"</a><br>");
	langmenuadmin911 = langmenuadmin911.charAt(0).toUpperCase() + langmenuadmin911.slice(1);
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./historyEtudiant.php' id='menumodule0'>"+langmenuadmin911+"</a><br>");
	if (moduleadminnouvelleannee != "non") document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./newannee.php' id='menumodule0'  >"+langmenuadmin99+"</a><br />");
	document.write("</p></td></tr>");
}


document.write("</table> ");
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

var nb=0;
function CnxAjax() {
	CnxEnCours(nb);
	nb++;
	window.setTimeout("CnxAjax()","300000"); //300000 -> 5 minutes
}
CnxAjax();
