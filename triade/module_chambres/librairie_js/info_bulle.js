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

/************************************************************
Affichage d'une info bulle
Last updated: 11.08.2004  by  Eric Taesch
*************************************************************/

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

function InitBulle(ColTexte,ColFond,ColContour,NbPixel) {
	IB.ColTexte = ColTexte;
	IB.ColFond = ColFond;
	IB.ColContour = ColContour;
	IB.NbPixel = NbPixel;
	
	if (document.layers) {
		window.captureEvents(Event.MOUSEMOVE);
		window.onMouseMove = getMousePos;
		document.write('<layer name="bulle" top="0" left="0" visibility="hide"></layer>');
	}
	else if (document.all) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0; left:0; visibility:hidden;"></div>');
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.onmousemove = getMousePos;
		document.write('<div id="bulle" style="position:absolute; top:0px; left:0px; visibility:hidden;"></div>');
	}
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

// Le contenant est ajoute pour IE pour que les positions soient calulees correctement
function AffBulle3(strTitre,strIcone,texte,contenant) {
	var finalPosX;
	var finalPosY;
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
	
	xOffset = 10;
	yOffset = 5;
	if(!contenant) {
		contenant = '';
	}
	if (navigator.appName == "Microsoft Internet Explorer"){
		if(contenant != '') {
			//position = bulle_getAnchorPosition(contenant);
			position = bulle_get_dimensions(contenant);
			
			xOffset = -10;
			finalPosX = posX + position.x - position.scroll_x + xOffset;
			finalPosY = posY + position.y - position.scroll_y + yOffset;
		} else {
			finalPosX = posX - xOffset;
			finalPosY = posY + yOffset;
		}
	} else {
		finalPosX = posX - xOffset;
		finalPosY = posY + yOffset;
	}
//alert(posX + ' - ' + posY);
	
	
//alert(finalPosX + ' - ' + finalPosY);

	if (finalPosX<0) finalPosX = 0;

	if (document.layers) {
		document.layers["bulle"].document.write(contenu);
		document.layers["bulle"].document.close();
		document.layers["bulle"].top = finalPosY + 'px';
		document.layers["bulle"].left = finalPosX + 'px';
		document.layers["bulle"].visibility = "show";
		document.layers["bulle"].zIndex = 8000;
	}
	else if (document.all) {
		//var f=window.event;
		//doc=document.body.scrollTop;
		bulle.innerHTML = contenu;
		document.all["bulle"].style.top = finalPosY + 'px';
		document.all["bulle"].style.left = finalPosX + 'px';//f.x-xOffset;
		document.all["bulle"].style.visibility = "visible";
		document.all["bulle"].style.zIndex = 8000;
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (document.getElementById) {
		document.getElementById("bulle").innerHTML = contenu;
		document.getElementById("bulle").style.top = finalPosY + 'px';
		document.getElementById("bulle").style.left = finalPosX + 'px';
		document.getElementById("bulle").style.visibility = "visible";
		document.getElementById("bulle").style.zIndex = 8000;
	}

}


// getAnchorPosition(anchorname)
//   This function returns an object having .x and .y properties which are the coordinates
//   of the named anchor, relative to the page.
function bulle_getAnchorPosition(anchorname) {
	// This function will return an Object with x and y properties
	var useWindow=false;
	var coordinates=new Object();
	var x=0,y=0;
	// Browser capability sniffing
	var use_gebi=false, use_css=false, use_layers=false;
	if (document.getElementById) { use_gebi=true; }
	else if (document.all) { use_css=true; }
	else if (document.layers) { use_layers=true; }
	// Logic to find position
 	if (use_gebi && document.all) {
		x=bulle_getPageOffsetLeft(document.all[anchorname]);
		y=bulle_getPageOffsetTop(document.all[anchorname]);
		}
	else if (use_gebi) {
		var o=document.getElementById(anchorname);
		x=bulle_getPageOffsetLeft(o);
		y=bulle_getPageOffsetTop(o);
		}
 	else if (use_css) {
		x=bulle_getPageOffsetLeft(document.all[anchorname]);
		y=bulle_getPageOffsetTop(document.all[anchorname]);
		}
	else if (use_layers) {
		var found=0;
		for (var i=0; i<document.anchors.length; i++) {
			if (document.anchors[i].name==anchorname) { found=1; break; }
			}
		if (found==0) {
			coordinates.x=0; coordinates.y=0; return coordinates;
			}
		x=document.anchors[i].x;
		y=document.anchors[i].y;
		}
	else {
		coordinates.x=0; coordinates.y=0; return coordinates;
		}
	coordinates.x=x;
	coordinates.y=y;
	return coordinates;
}

function bulle_getPageOffsetLeft (el) {
	var ol=el.offsetLeft;
	while ((el=el.offsetParent) != null) { ol += el.offsetLeft; }
	return ol;
}

function bulle_getPageOffsetTop (el) {
	var ot=el.offsetTop;
	while((el=el.offsetParent) != null) { ot += el.offsetTop; }
	return ot;
}


 function bulle_get_dimensions (id) {
	 	var coordinates=new Object();
		var curleft = curtop = 0;
		var res=Array();
		
		var curleft = curtop = 0;
		var res=Array();
		
		var window_x;
		var window_y;
		var document_x;
		var document_y;
		var scroll_x;
		var scroll_y;
		
		if(id != "") {
			obj =document.getElementById(id);
	
			if(obj) {
				if (obj.offsetParent) {
					try {
						curleft_tmp = obj.offsetLeft;
					}
					catch(e) {
						curleft_tmp = 0;
					}
					curleft = curleft_tmp;
					try {
						curtop_tmp = obj.offsetTop;
					}
					catch(e) {
						curtop_tmp = 0;
					}
					curtop = curtop_tmp;
					valid = true;
					try {
						obj = obj.offsetParent;
					}
					catch(e) {
						valid = false;
					}
					
					while (valid) {
			
						try {
							curleft_tmp = obj.offsetLeft;
						}
						catch(e) {
							curleft_tmp = 0;
						}
						try {
							curtop_tmp = obj.offsetTop;
						}
						catch(e) {
							curtop_tmp = 0;
						}
			
			
						curleft += curleft_tmp;
						curtop += curtop_tmp;
						
						try {
							obj = obj.offsetParent;
						}
						catch(e) {
							valid = false;
						}
			
					}
				}
				
				try {
					var html_elemento = document.getElementById(id);
					curwidth = parseInt(html_elemento.offsetWidth,10);
				}
				catch(e) {
					curwidth = 0;
				}
				
				try {
					var html_elemento = document.getElementById(id);
					curheight = parseInt(html_elemento.offsetHeight,10);
				}
				catch(e) {
					curheight = 0;
				}
				
				try {
					
					if (navigator.appName == "Microsoft Internet Explorer"){
						window_x = document.body.clientWidth;
						window_y = document.body.clientHeight;
						document_x = document.body.clientWidth;
						document_y = document.body.clientHeight;
						if(window_x == 0) {
							window_x = document.documentElement.clientWidth;
							window_y = document.documentElement.clientHeight;
							document_x = document.body.clientWidth;
							document_y = document.body.clientHeight;		
						}
					} else {
						window_x = document.documentElement.clientWidth;
						window_y = document.documentElement.clientHeight;
						document_x = document.body.clientWidth;
						document_y = document.body.clientHeight;		
					}
					
					//alert("window_x=" + this.window_x);
				}
				catch(err) {
					window_x = 0;
					window_y = 0;
					document_x = 0;
					document_y = 0;
				}
			} else {
				window_x = 0;
				window_y = 0;
				document_x = 0;
				document_y = 0;
			}
		} else {
			window_x = 0;
			window_y = 0;
			document_x = 0;
			document_y = 0;
		}
		
		// Get maximum x dimension
		if(window_x>document_x) {
			res["window_width"]=document_x;
			res["document_width"]=window_x;
		} else {
			res["window_width"]=window_x;
			res["document_width"]=document_x;
		}
	
		// Get maximum y dimension
		if(window_y>document_y) {
			res["window_height"]=document_y;
			res["document_height"]=window_y;
		} else {
			res["window_height"]=window_y;
			res["document_height"]=document_y;
		}		

		if (navigator.appName == "Microsoft Internet Explorer"){
			scroll_x = document.documentElement.scrollLeft;
			if(scroll_x == 0) {
				scroll_x = document.body.scrollLeft;
			}
			scroll_y = document.documentElement.scrollTop;
			if(scroll_y == 0) {
				scroll_y = document.body.scrollTop;
			}
		} else { 
			scroll_x = window.pageXOffset; 
			scroll_y = window.pageYOffset; 
		}
		
		res["left"]=curleft;
		res["top"]=curtop;
		res["width"]= parseInt(curwidth,10);
		res["height"]= parseInt(curheight,10);
		
		res["scroll_width"] = scroll_x;
		res["scroll_height"] = scroll_y;
		
		coordinates.x = curleft;
		coordinates.y = curtop;
		coordinates.scroll_x = scroll_x;
		coordinates.scroll_y = scroll_y;
		return coordinates;
	}	