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
	contenu = '<div style="'
		+ 'background:' + IB.ColFond + ';'
		+ 'color:' + IB.ColTexte + ';'
		+ 'border:' + IB.NbPixel + 'px solid ' + IB.ColContour + ';'
		+ 'border-radius:10px;'
		+ 'box-shadow:0 6px 20px rgba(0,0,0,0.22);'
		+ 'font-family:Electrolize,Trebuchet MS,Arial,sans-serif;'
		+ 'font-size:12px;'
		+ 'line-height:1.6;'
		+ 'padding:10px 14px;'
		+ 'max-width:340px;'
		+ '">' + texte + '</div>';

	var finalPosX = posX - xOffset;
	if (finalPosX < 0) finalPosX = 0;

	if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = posY + yOffset + "px";
		document.getElementById("bulle").style.left = finalPosX + "px";
		document.getElementById("bulle").style.visibility = "visible";
	} else if (document.all) {
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = posY + yOffset + "px";
		document.all["bulle"].style.left = finalPosX + "px";
		document.all["bulle"].style.visibility = "visible";
	}
}


function AffBulleV2(texte) {
	contenu = '<div style="'
		+ 'background:' + IB.ColFond + ';'
		+ 'color:' + IB.ColTexte + ';'
		+ 'border:' + IB.NbPixel + 'px solid ' + IB.ColContour + ';'
		+ 'border-radius:10px;'
		+ 'box-shadow:0 6px 20px rgba(0,0,0,0.22);'
		+ 'font-family:Electrolize,Trebuchet MS,Arial,sans-serif;'
		+ 'font-size:12px;'
		+ 'line-height:1.6;'
		+ 'padding:10px 14px;'
		+ 'max-width:340px;'
		+ '">' + texte + '</div>';
	document.getElementById("bulle").innerHTML = contenu;
	document.getElementById("bulle").style.top = posY + "px";
	document.getElementById("bulle").style.left = posX + "px";
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
	var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
	if (strTitre != "") {
		contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
		contenu += '<img src="' + strIcone + '" style="width:15px;height:15px;border:0" alt="">';
		contenu += '<span style="font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
		contenu += '</div>';
	}
	contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '</div>';
	contenu += '</div>';

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
	var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
	if (strTitre != "") {
		contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
		contenu += '<img src="' + strIcone + '" style="width:15px;height:15px;border:0" alt="">';
		contenu += '<span style="font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
		contenu += '</div>';
	}
	contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '</div>';
	contenu += '</div>';

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

	var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
	if (strTitre != "") {
		contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
		contenu += '<span style="font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
		contenu += '</div>';
	}
	var val="val";
	contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '<br><input type="text" value="'+motif+'" size="30" onBlur="document.formulaire.saisie_motif_'+id+'.value=(this.value == \'\') ? \'inconnu\' : this.value " style="margin-top:6px;border:1px solid #c5cae9;border-radius:4px;padding:3px 6px" /> <input type="button" value="ok" onclick="HideBulleP()" style="margin-left:4px;background:#080A66;color:#fff;border:none;border-radius:4px;padding:3px 10px;cursor:pointer" /></div>';
	contenu += '</div>';

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
	var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
	if (strTitre != "") {
		contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
		contenu += '<img src="' + strIcone + '" style="width:15px;height:15px;border:0" alt="">';
		contenu += '<span style="flex:1;font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
		contenu += '<a href="javascript:HideBulle()" style="font-size:16px;color:#888;text-decoration:none;line-height:1" title="Fermer">&times;</a>';
		contenu += '</div>';
	}
	contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '</div>';
	contenu += '</div>';
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
        var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
        if (strTitre != "") {
                contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
                contenu += '<img src="' + strIcone + '" style="width:15px;height:15px;border:0" alt="">';
                contenu += '<span style="flex:1;font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
                contenu += '<a href="javascript:HideBulle()" style="font-size:16px;color:#888;text-decoration:none;line-height:1" title="Fermer">&times;</a>';
                contenu += '</div>';
        }
        contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '</div>';
        contenu += '</div>';
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
	var contenu = '<div style="background:#fff;border:1px solid #c5cae9;border-radius:10px;box-shadow:0 4px 20px rgba(8,10,102,.18);overflow:hidden;min-width:330px;max-width:360px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif">';
	if (strTitre != "") {
		contenu += '<div style="display:flex;align-items:center;gap:8px;padding:7px 12px;background:#eef0f8;border-bottom:1px solid #dde0f0">';
		contenu += '<img src="' + strIcone + '" style="width:15px;height:15px;border:0" alt="">';
		contenu += '<span style="flex:1;font-size:12px;font-weight:700;color:#080A66">' + strTitre + '</span>';
		contenu += '<a href="javascript:HideBulle()" style="font-size:16px;color:#888;text-decoration:none;line-height:1" title="Fermer">&times;</a>';
		contenu += '</div>';
	}
	contenu += '<div style="padding:10px 14px;font-size:11px;line-height:1.6;color:#333">' + texte + '</div>';
	contenu += '</div>';

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
