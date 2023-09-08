document.write("</div></td>");
document.write("</tr>");
document.write("</table>");
document.write("<table border='0' cellpadding='0' cellspacing='0' width='100%' height='20' bgcolor='#175216'>");
document.write("<tr id='coulBar0' >");
document.write("<td height='20' width='20%'>");
if (lienassist == '0' ) {
	document.write("<div align='center'><a href='#' onclick=\"open('http://doc.triade-educ.org','_blank','')\" class='m'>"+langtitre0+"</a></div>");
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
document.write(" <td height='20' width='20%'>");
document.write("<div align='center'><a href='#' onclick=\"open('"+forum+"','"+forumtarget+"','directories=no,location=no,menubar=no,toolbar=no,status=no,scrollbars=no,resizable=yes,width=500,height=358,noresize=yes')\"  class='m'>"+langtitre3+"</a></div>");
document.write("</td>");
document.write(" <td width='121' height='20'>");
document.write("<div align='center'><a href='#'   onclick='quitter_session()';  class='m'>"+langtitre5+"</a>");
document.write("&nbsp;&nbsp;&nbsp;&nbsp;<a href='./verrou.php'><img src='image/commun/img_ssl_mini.png' border='0' align='center' alt='Mise en veille' /></a> </div>");
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
		document.write("<td colspan='3' id='coulTitre0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px;' ><a href='#' onMouseOver=\"AffBulleRadioAvecQuit('','','<div id=affradio ><iframe src=https://www.triade-educ.org/webradio/infomusic.php  height=100 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=NO ></iframe></div>'); window.status=''; return true;\" onMouseOut='HideBulleRadio()' onclick=\"open('webradio.php','webradio','width=329,height=195');return false\" ><img src='image/commun/webradio.jpg' align='center' border='0' style='-webkit-border-radius: 15px;-moz-border-radius: 15px;border-radius: 15px; box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); moz-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); -webkit-box-shadow: 0px 0px 10px 4px rgba(119, 119, 119, 0.75); ' /></a></td>"); 
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
document.write("<td colspan='3' id='coulModule0' >");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='gescompte.php' id='menumodule0'  >"+langmenugeneral01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='memo.php' id='menumodule0'  >"+langmenugeneral01a+"</a><br>");
if (modulestockageviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('stockage.php','stockage','scrollbars=yes,resizable=yes,width=850,height=500')\" id='menumodule0' >"+langmenuadmin06+"</a><br>"); }
//if (moduleintramsnviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./intra-msn.php' id='menumodule0' >"+langmenuadmin100+"</a><br>"); }
if (moduleagendaviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' id='menumodule0' onclick=\"open('./agenda/phenix/index.php','timecop','');\" >"+langmenuadmin00+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./parametrage.php' >"+langmenuadmin46+"</a><br>");
if (modulefluxrssviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./flux.php' >"+langmenuadmin521+"</a><br>"); }
if (modulecantineviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cantine_consulte.php' >"+langmenupersonnel2+"</a><br>"); }
if (lan == "oui") {
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='#' onClick=\"open('http://support.triade-educ.com/support/triade-shop.php','triadeshop','width=1024,height=765,resizable=no,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');\" >"+langmenuadmin526+"</a><br>");
}
document.write(" </p>");
document.write("</td>");
document.write("</tr>")
document.write("<tr>");
document.write("<td colspan='3' height=19>&nbsp;</td>");
document.write(" </tr>")
if (modulemessagerieviescolaire == "oui") {
	document.write("<tr>");
	document.write("<td colspan='3' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenuadmin0+"</font></b></td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3'  id='coulModule0'>");
	document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_reception.php'>"+langmenuadmin03+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_envoi.php'>"+langmenuadmin02+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='./corbeille_message.php' id='menumodule0'>"+langmenuadmin023+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./messagerie_suppression.php'>"+langmenuadmin04+"</a><br>");
	document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./sms-mess0.php'  >"+langmenuadmin522+"</a><br>");
	document.write("</p>");
	document.write("</td>");
	document.write("</tr>");
	document.write("<tr>");
	document.write("<td colspan='3' height=19>&nbsp;</td>");
	document.write("</tr>");
}


document.write("<tr>");
document.write("<td colspan='3' id='coulTitre0' ><b><font  id='menumodule1'>"+langmenuscolaire0+"</font></b></td>");
document.write("</tr>");
document.write("<tr>");
document.write("<td colspan='3' id='coulModule0'>");
document.write("<p style='margin-left: 2px; margin-top:5px; margin-bottom:5px'>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_abs_retard_du_jour.php'>"+langmenuscolaire01+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_abs_retard.php'>"+langmenuscolaire02+"</a><br>");
if (moduleviescolairegestionsavoiretre == "oui") document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./savoiretre.php'>"+langmenuadmin912+"</A><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_dispence.php'>"+langmenuscolaire03+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_sanction_du_jour.php'>"+langmenuadmin523+"</A><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_discipline_du_jour.php'>"+langmenuscolaire04+"</A><br>");
if (moduleviescolairecahierdetexte == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./cahiertextesadmin.php'>"+langmenuparent27+"</a><br>"); }
document.write("  <img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_delegue_impr.php'>"+langmenuparent36+"</a><br>");
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_discipline.php'>"+langmenuscolaire05+"</A><br>");
if (moduleetudeviescolaire == "oui") {  document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_etude.php'>"+langmenuadmin520+"</a><br>"); }
document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_config_evenement1.php'>"+langmenuscolaire14+"</a><br>");
//document.write("<img src='./image/cube.gif' width='4' height='4'> <a href='#' onclick=\"open('./organigramme/index.php','orga','width=800,height=700,resizable=yes,scrollbars=auto')\" id='menumodule0' >"+langmenuadmin528+"</a><br>");

if (modulecirculaireviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./circulaire_admin.php'>"+langmenuscolaire15+"</a><br>"); }
if (moduledstviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./calendrier_config_dst1.php'>"+langmenuscolaire16+"</a><br>"); }
if (modulestageviescolaire == "oui") { document.write("<img src='./image/cube.gif' width='4' height='4'> <a id='menumodule0' href='./gestion_stage.php'>"+langmenuadmin517+"</a><br>"); }
document.write("</p>");
document.write("</td>");
document.write("</tr>");
document.write("</td></tr>");
document.write("</table> ");
document.write("</td>");
document.write("<td valign='top' style='padding:0 20 0 20' height='160'>");

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

function CnxEnCours() {

	var requete = getRequete2();
	var corps="nb="+encodeURIComponent(nb);

	if (requete != null) {
		requete.open("POST","verifConnex2.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					if (requete.responseText == "1") {
						location.href="verrou.php";
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
