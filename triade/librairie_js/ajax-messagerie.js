/***************************************************************************
*                              T.R.I.A.D.E
*                            ---------------
*
*   begin                : Janvier 2000
*   copyright            : (C) 2000 E. TAESCH - T. TRACHET
*   Site                 : http://www.triade-educ.com
*
*
***************************************************************************
***************************************************************************
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
***************************************************************************/
function getRequete() {
	if (window.XMLHttpRequest) { 
        	result = new XMLHttpRequest();     // Firefox, Safari, ...
	}else { 
	      if (window.ActiveXObject)  {
	      result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer 
	      }
       	}
	return result;
}


function PieceJointe(nb) {
	var requete = getRequete();
	var corps="id="+encodeURIComponent(nb);
	var ref;
	var rs;
	var fichier;
	//var corps="sujet="+encodeURIComponent(item)+"&date="+encodeURIComponent(date)+"&idg="+encodeURIComponent(idclasse)+"&idc="+encodeURIComponent(idgroupe)+"&idm="+encodeURIComponent(idmatiere);
	if (requete != null) {
		requete.open("POST","verifPieceJointe.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					rs = requete.responseText.indexOf('/');
					rs++;
					ref = requete.responseText.substring(rs,requete.responseText.lenght);
					rs = rs - 1;
					fichier=requete.responseText.substring(0,rs);
					if ((fichier != "-1") && (fichier != "-2") && (fichier != "")) {
						document.getElementById('fjoint2').style.visibility="visible";
						document.getElementById('fjoint').style.visibility="hidden";
						document.getElementById("fjoint2").innerHTML="<font class=T2>Fichier : "+fichier+" </font> &nbsp;&nbsp; <img src='image/commun/stat1.gif' align='center' />";	
						
						return;
					}else{
						if (fichier == "-1")  {
							document.getElementById('fjoint').style.visibility="visible";
							document.getElementById('fjoint2').style.visibility="hidden";
							alert("Fichier non conforme !");	
							suppRefPieceJointe(ref);
							return;
						}else{
							setTimeout(PieceJointe(nb),2000);
						}
					}	
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}

function suppRefPieceJointe(nb) {
	var requete = getRequete();
	var corps="id="+encodeURIComponent(nb);
	if (requete != null) {
		requete.open("POST","deleteRefPieceJointe.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) { if(requete.status == 200) { return; } };
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}

