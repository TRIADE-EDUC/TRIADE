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


function verifChamps(table,champs,valeur,soumission) {
	var requete = getRequete();
	var corps="table="+encodeURIComponent(table)+"&champs="+encodeURIComponent(champs)+"&valeur="+encodeURIComponent(valeur);
	if (requete != null) {
		requete.open("POST","verifChamps.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					resultat=requete.responseText;
					if (resultat == "1") {
						soumission.disabled=true;
						alert("Cette information est déjà attribué.");	
					}else{
						soumission.disabled=false;
					}
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}


function afficheEleve(idclasse) {
	var requete = getRequete();
	var corps="idclasse="+encodeURIComponent(idclasse);
	document.getElementById('saisie_eleve').options.length=0;
	if (requete != null) {
		requete.open("POST","AjaxAfficheSelectEleve.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					tab=unserialize(requete.responseText); //elev_id,nom,prenom
					var select = document.getElementById("saisie_eleve");
					for(var i=0;i<tab.length;i++) {
						element = document.createElement("option");
				            	element.setAttribute('value',tab[i][0]);
				            	element.setAttribute('id','select1');
          
				          	text = document.createTextNode(tab[i][1]+" "+tab[i][2]); 
						element.appendChild(text);
          
				          	select.appendChild(element);
					} 
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}



