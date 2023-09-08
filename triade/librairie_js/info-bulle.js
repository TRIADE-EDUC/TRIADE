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
/*
 Dans la page php mettre ceci :
 
	<a href='#' 
		onMouseOver="AffBulle('
				<span style=\"font-family: Verdana; font-size: 0.9em\">
				 <span style=\"color: red; font-weight: bold\">I</span>ndiquez
				 le message dans la bulle
				</span>');
				window.status=''; return true;"
		onMouseOut='HideBulle()'>

		<img src='./image/help.gif' style="width: 15px; height: 15px; border: 0;" />

	</a>

ainsi que ceci (ds le head par exemple) :
	InitBulle(couleur de texte, couleur de fond, couleur de contour, taille contour)
	<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
*/

var N=navigator.appName; var V=navigator.appVersion;

var version="?"; var nom=N; var os="?"; var langue="?";
if (N=="Microsoft Internet Explorer") {
	langue=navigator.systemLanguage
	version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf(";",V.indexOf("MSIE",0)));
	if (V.indexOf("Win",0)>0) {
		if ( V.indexOf(";",V.indexOf("Win",0)) > 0 ) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		} else {
			os=V.substring(V.indexOf("Win",0),V.indexOf(")",V.indexOf("Win",0)));
		}
	}
	if (V.indexOf("Mac",0)>0) {
		os="Macintosh";
		version=V.substring(V.indexOf("MSIE",0)+5,V.indexOf("?",V.indexOf("MSIE",0)));
	}
}
if (N=="Opera") {
	langue=navigator.language;
	version=V.substring(0,V.indexOf("(",0));
	os=V.substring(V.indexOf("(",0)+1,V.indexOf(";",0));
}		
if (N=="Netscape") {
	langue=navigator.language;
	if (navigator.vendor=="") { // Mozilla
		version=(V.substring(0,V.indexOf("(",0)));
		nom="Mozilla";
		if (V.indexOf("Mac",0)>0) {
			os="Macintosh";
		}
		if (V.indexOf("Linux",0)>0) {
			os="Linux";
		}
		if (V.indexOf("Win",0)>0) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		}
		if (version==5) {
			version="1";
		}
		if (navigator.oscpu) {os=navigator.oscpu;}
	} else {	// NS 4 ou 6
		version=(V.substring(0,V.indexOf("(",0)));
		if (V.indexOf("Mac",0)>0) {
			os="Macintosh";
		}
		if (V.indexOf("Linux",0)>0) {
			os="Linux";
		}
		if (V.indexOf("Win",0)>0) {
			os=V.substring(V.indexOf("Win",0),V.indexOf(";",V.indexOf("Win",0)));
		}
		if (version==5) {
			version="6.0";
			if (navigator.vendorSub!="") {version=navigator.vendorSub;}
		}
		if (navigator.oscpu) {os=navigator.oscpu;}
	}
}


var IB = new Object;
var posX = 0;
var posY = 0;
var xOffset = 10;
var yOffset = 10;

function AffBulle(texte) {
	contenu  = '<table style="border: 0;" cellspacing="0" cellpadding="'+IB.NbPixel+'" >';
	contenu +=  '<tr style="background-color: '+IB.ColContour+'">';
	contenu +=   '<td>';
	contenu +=    '<table style="border: 0; background-color: '+IB.ColFond+';" cellpadding="2" cellspacing="0" width=100% >';
	contenu +=     '<tr>';
	contenu +=      '<td style="font-family: arial; font-size: 0.9em; color:'+IB.ColTexte+'">'+texte+'</td>';
	contenu +=     '</tr>';
	contenu +=    '</table>';
	contenu +=   '</td>';
	contenu +=  '</tr>';
	contenu += '</table>&nbsp;';

	var finalPosX=posX-xOffset;

	if (finalPosX<0) finalPosX=0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset;
		document.layers["bulle"].left = finalPosX;
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset;
		document.all["bulle"].style.left = finalPosX;//f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
  else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset;
		document.getElementById("bulle").style.left = finalPosX;
		document.getElementById("bulle").style.visibility = "visible";
	}

}


function AffBulleV2(texte) {
	contenu  = '<table style="border: 0;" cellspacing="0" cellpadding="'+IB.NbPixel+'" >';
	contenu +=  '<tr style="background-color: '+IB.ColContour+'">';
	contenu +=   '<td>';
	contenu +=    '<table style="border:0; background-color:'+IB.ColFond+';" cellpadding="2" cellspacing="0" width=100% >';
	contenu +=     '<tr>';
	contenu +=      '<td style="font-family: arial; font-size: 0.9em; color:'+IB.ColTexte+'">'+texte+'</td>';
	contenu +=     '</tr>';
	contenu +=    '</table>';
	contenu +=   '</td>';
	contenu +=  '</tr>';
	contenu += '</table>&nbsp;';
	document.getElementById("bulle").innerHTML = contenu;
	document.getElementById("bulle").style.top = posY +"px";
	document.getElementById("bulle").style.left = posX +"px"; 
	document.getElementById("bulle").style.display = "block";
}


function getMousePos(e) {
	if (document.all) {
		posX = event.x + document.body.scrollLeft; //modifs CL 09/2001 - IE : regrouper l'évènement
		posY = event.y + document.body.scrollTop;
	}
	else {
		posX = e.pageX; //modifs CL 09/2001 - NS6 : celui-ci ne supporte pas e.x et e.y
		posY = e.pageY; 
	}
}

function HideBulle() {
	if (document.layers) { document.layers["bulle"].visibility = "hide"; }
	else if (document.all) { document.all["bulle"].style.visibility = "hidden"; }
	else if (document.getElementById) { document.getElementById("bulle").style.visibility = "hidden"; }
}

function HideBulleV2() {
	document.getElementById("bulle").style.display = "none"; 
}


function HideBulleP() {
	if (document.layers) { document.layers["bullep"].visibility = "hide"; }
	else if (document.all) { document.all["bullep"].style.visibility = "hidden"; }
	else if (document.getElementById) { document.getElementById("bullep").style.visibility = "hidden"; }
}

function InitBulle(ColTexte,ColFond,ColContour,NbPixel) {
	IB.ColTexte = ColTexte;
	IB.ColFond = ColFond;
	IB.ColContour = ColContour;
	IB.NbPixel = NbPixel;

	if (document.layers) {
		window.captureEvents(Event.MOUSEMOVE);
		window.onMouseMove = getMousePos;
		document.write('<layer name="bulle" top="0" left="0" visibility="hide"></layer>');
		document.write('<layer name="bullep" top="0" left="0" visibility="hide"></layer>');
	}
	else if (document.all) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0; left:0; z-index:1000000; visibility:hidden;"></div>');
		document.write('<div id="bullep" style="position:absolute; top:0; left:0; z-index:1000000; visibility:hidden;"></div>');
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0px; left:0px; z-index:1000000;  visibility:hidden;"></div>');
		document.write('<div id="bullep" style="position:absolute; top:0px; left:0px; z-index:1000000;  visibility:hidden;"></div>');
	}
}

function InitBulleV2(ColTexte,ColFond,ColContour,NbPixel) {
	IB.ColTexte = ColTexte;
	IB.ColFond = ColFond;
	IB.ColContour = ColContour;
	IB.NbPixel = NbPixel;
	document.onmousemove = getMousePos;
	document.write('<div id="bulle" style="position:absolute; top:0px; left:0px; z-index:1000000;  display:none;"></div>');
	document.write('<div id="bullep" style="position:absolute; top:0px; left:0px; z-index:1000000;  display:none;"></div>');
	document.getElementById("bulle").style.top = 0;
	document.getElementById("bulle").style.left = 0;
}



function AffBulle2(strTitre,strIcone,texte) {
	// titre, image , texte

	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 

	//la bulle fait L 10px+305px+10px = 325px
	//              H 30px+nx15px+10px

	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(../image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(../image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';

	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td>';
		contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}

	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';

	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(../image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';

	var finalPosX = posX - xOffset;

	if (finalPosX<0) finalPosX = 0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset + "px";
		document.layers["bulle"].left = finalPosX + "px";
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML=contenu;
		document.all["bulle"].style.top = posY + yOffset + "px";
		document.all["bulle"].style.left = finalPosX + "px"; //f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset + "px";
		document.getElementById("bulle").style.left = finalPosX + "px";
		document.getElementById("bulle").style.visibility = "visible";
	}
}

function AffBulle3(strTitre,strIcone,texte) {
	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 

	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';

	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}

	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';

	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';

	var finalPosX = posX - xOffset;

	if (finalPosX<0) finalPosX = 0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset;
		document.layers["bulle"].left = finalPosX;
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset;
		document.all["bulle"].style.left = finalPosX;//f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset;
		document.getElementById("bulle").style.left = finalPosX;
		document.getElementById("bulle").style.visibility = "visible";
	}

}


function AffBullePrompt(strTitre,texte,id) {
	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 

	var motif=eval("document.formulaire.saisie_motif_"+id+".value");

	var strIcone="image/commun/info.jpg";

	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';

	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}
	val="val";
	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '<br><input type="text" value="'+motif+'" size="30" onBlur="document.formulaire.saisie_motif_'+id+'.value=(this.value == \'\') ? \'inconnu\' : this.value " /> <input type="button" value="ok" onclick="HideBulleP()" /></div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';

	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';

	var finalPosX = posX - xOffset;

	if (finalPosX<0) finalPosX = 0;

	if (N != "Microsoft Internet Explorer") yOffset=-20;
	if (N == "Microsoft Internet Explorer") yOffset=-5;

	if (document.layers) {
		document.layers["bullep"].document.write(contenu);
		document.layers["bullep"].document.close();
		document.layers["bullep"].top = posY + yOffset;
		document.layers["bullep"].left = finalPosX;
		document.layers["bullep"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bullep.innerHTML = contenu;
		document.all["bullep"].style.top = posY + yOffset;
		document.all["bullep"].style.left = finalPosX;//f.x-xOffset;
		document.all["bullep"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bullep").innerHTML = contenu;
		document.getElementById("bullep").style.top = posY + yOffset;
		document.getElementById("bullep").style.left = finalPosX;
		document.getElementById("bullep").style.visibility = "visible";
	}

}



function AffBulleEDT(strTitre,strIcone,texte) {
	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 
	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';
	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<table width=100% ><tr><td>';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td><td width="5%" ><a href="javascript:HideBulle()" ><img src="image/commun/quitter.gif" border="0" ></a></td></tr></table></td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}
	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';
	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';
	var finalPosX = posX - xOffset;
	if (finalPosX<0) finalPosX = 0;
	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset + "px";
		document.layers["bulle"].left = finalPosX + "px";
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset + 100 + "px";
		document.all["bulle"].style.left = finalPosX + "px";       //f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
//alert(document.getElementById("bulle").style.top);
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset +"px";
		document.getElementById("bulle").style.left = finalPosX +"px";
		document.getElementById("bulle").style.visibility = "visible";
	}
}

function AffBulleEDT2(strTitre,strIcone,texte) {
        // image/commun/stop.jpg
        // image/commun/info.jpg
        // image/commun/warning.jpg
        var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
        contenu += '<tr style="height: 30px;">';
        contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
        contenu +=  '<td style="width: 30px; background: url(../image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
        contenu +=  '<td style="width: 285px; background: url(../image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
        contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
        contenu += '</tr>';
        if ( strTitre != "" ){
                contenu += '<tr style="height: 30px;">';
                contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
                contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
                contenu +=   '<table width=100% ><tr><td>';
                contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
                contenu +=   '<b>' + strTitre + '</b>';
                contenu +=  '</td><td width="5%" ><a href="javascript:HideBulle()" ><img src="../image/commun/quitter.gif" border="0" ></a></td></tr></table></td>';
                contenu +=  '<td style="width: 10px; background: url(../image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
                contenu += '</tr>';
        }
        contenu +=  '<tr> ';
        contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
        contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '</div></td>';
        contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
        contenu +=  '</tr>';
        contenu +=  '<tr style="height: 10px;">';
        contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
        contenu +=   '<td colspan="2" style="width: 305px; background: url(../image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
        contenu +=   '<td style="width: 10px; background: url(../image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
        contenu +=  '</tr>';
        contenu += '</table>';
        var finalPosX = posX - xOffset;
        if (finalPosX<0) finalPosX = 0;
        if (document.layers) {
                document.layers["bulle"].document.write(contenu);
                document.layers["bulle"].document.close();
                document.layers["bulle"].top = posY + yOffset + "px";
                document.layers["bulle"].left = finalPosX + "px";
                document.layers["bulle"].visibility = "show";
        }
        else if (document.all) {
                //var f=window.event;
                //doc=document.body.scrollTop;
                bulle.innerHTML = contenu;
                document.all["bulle"].style.top = posY + yOffset + 100 + "px";
                document.all["bulle"].style.left = finalPosX + "px";       //f.x-xOffset;
                document.all["bulle"].style.visibility = "visible";
        }
        //modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
        else if (document.getElementById) {
//alert(document.getElementById("bulle").style.top);
                document.getElementById("bulle").innerHTML = contenu;
                document.getElementById("bulle").style.top = posY + yOffset +"px";
                document.getElementById("bulle").style.left = finalPosX +"px";
                document.getElementById("bulle").style.visibility = "visible";
        }
}



function AffBulleAvecQuit(strTitre,strIcone,texte) {
	// image/commun/stop.jpg 
	// image/commun/info.jpg 
	// image/commun/warning.jpg 

	var contenu = '<table Id="HelpTable" style="width: 335px;" cellspacing="0" cellpadding="0">';
	contenu += '<tr style="height: 30px;">';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HG.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 30px; background: url(./image/commun/Bulle_HC1.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '<td style="width: 285px; background: url(./image/commun/Bulle_HC2.gif); background-repeat: repeat-x;"></td>';
	contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_HD.gif); background-repeat: no-repeat;"></td>';
	contenu += '</tr>';

	if ( strTitre != "" ){
		contenu += '<tr style="height: 30px;">';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
		contenu +=  '<td colspan="2" style="width: 305px; text-align: left; vertical-align: middle; background: #FBFFD9; font-size: 14px; font-family: Tahoma;">';
		contenu +=   '<table width=100% ><tr><td>';
		contenu +=   '<img src="' + strIcone + '" style="border: 0; width: 15px; height: 15px; margin-right: 10px;" alt="">';
		contenu +=   '<b>' + strTitre + '</b>';
		contenu +=  '</td><td width="5%" ><a href="javascript:HideBulle()" ><img src="image/commun/quitter.gif" border="0" ></a></td></tr></table></td>';
		contenu +=  '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
		contenu += '</tr>';
	}

	contenu +=  '<tr> ';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CG.gif); background-repeat: repeat-y;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: #FBFFD9; font-family: Arial; font-size: 10px;"><div id="id1" style="overflow:auto; width: 300px;">' + texte + '</div></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_CD.gif); background-repeat: repeat-y;"></td>';
	contenu +=  '</tr>';

	contenu +=  '<tr style="height: 10px;">';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BG.gif); background-repeat: no-repeat;"></td>';
	contenu +=   '<td colspan="2" style="width: 305px; background: url(./image/commun/Bulle_BC.gif); background-repeat: repeat-x;"></td>';
	contenu +=   '<td style="width: 10px; background: url(./image/commun/Bulle_BD.gif); background-repeat: no-repeat;"></td>';
	contenu +=  '</tr>';
	contenu += '</table>';

	var finalPosX = posX - xOffset;

	if (finalPosX<0) finalPosX = 0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = posY + yOffset + "px";
		document.layers["bulle"].left = finalPosX + "px";
		document.layers["bulle"].visibility = "show";
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML=contenu;
		document.all["bulle"].style.top = posY + yOffset + "px";
		document.all["bulle"].style.left = finalPosX + "px"; //f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset + "px";
		document.getElementById("bulle").style.left = finalPosX + "px";
		document.getElementById("bulle").style.visibility = "visible";
	}
}
