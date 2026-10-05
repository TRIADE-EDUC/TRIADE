function getRequete222() {
	if (window.XMLHttpRequest) { 
        	result = new XMLHttpRequest();     // Firefox, Safari, ...
	}else { 
	      if (window.ActiveXObject)  {
	      result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer 
	      }
       	}
	return result;
}

function InfoCentralTriade(autorise,productidclient,productidserveur) {
	var requete = getRequete222();
	var corps="autorise="+encodeURIComponent(autorise)+"&productidclient="+encodeURIComponent(productidclient)+"&productidserveur="+encodeURIComponent(productidserveur);
	if (requete != null) {
		requete.open("POST","http://support.triade-educ.com/centralestage/ModifDroitCentralStage.php",true);
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					// rien
				}
  			};
		} 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}
}
