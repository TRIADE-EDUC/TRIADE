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


function impr_periode(idclasse) {
alert(idclasse);

	var requete = getRequete();
	var corps="idclasse="+encodeURIComponent(idclasse);
	//var corps="sujet="+encodeURIComponent(item)+"&date="+encodeURIComponent(date)+"&idg="+encodeURIComponent(idclasse)+"&idc="+encodeURIComponent(idgroupe)+"&idm="+encodeURIComponent(idmatiere);


	if (requete != null) {
		requete.open("POST","impr_periode_ajax.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					if (requete.responseText != "") {
						alert(requete.responseText);
						//document.getElementById("info-time").innerHTML=requete.responseText;	
						document.formulaire.=requete.responseText;	
					}
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}


