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


function RecupMoyenne(trim,idclasse,idchamps,anneeScolaire) {
	var requete = getRequete();
	document.getElementById(idchamps).innerHTML="<img src='image/commun/indicator.gif' />";
	var corps="saisie_trimestre="+encodeURIComponent(trim)+"&saisie_classe="+encodeURIComponent(idclasse)+"&anneeScolaire="+encodeURIComponent(anneeScolaire);
	if (requete != null) {
		requete.open("POST","recupMoyenne.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					if (requete.responseText != "") {
						document.getElementById(idchamps).innerHTML=requete.responseText;	
					}else{
						document.getElementById(idchamps).innerHTML="??";
					}	
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}

function RecupMoyenneEleve(trim,ideleve,idchamps,idclasse,anneeScolaire) {
	var requete = getRequete();
	document.getElementById(idchamps).innerHTML="<img src='image/commun/indicator.gif' />";
	var corps="saisie_trimestre="+encodeURIComponent(trim)+"&saisie_eleve="+encodeURIComponent(ideleve)+"&saisie_classe="+encodeURIComponent(idclasse)+"&anneeScolaire="+encodeURIComponent(anneeScolaire);
	if (requete != null) {
		requete.open("POST","recupMoyenneEleve.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					if (requete.responseText != "") {
						document.getElementById(idchamps).innerHTML=requete.responseText;	
					}else{
						document.getElementById(idchamps).innerHTML="??";
					}	
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}

