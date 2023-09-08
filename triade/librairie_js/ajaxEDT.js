
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


function removeOptionSelected(id)
{
  var elSel = document.getElementById(id);
  var i;
  for (i = elSel.length -1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}



visugroupe = function (idClasse)
{
	var requete = getRequete();
	removeOptionSelected('groupeID');
	var corps="idclasse="+escape(idClasse);
	if (requete != null) {
		requete.open("POST","ajaxEDTGroupe.php",true); 
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					var tab=unserialize(requete.responseText); //pers_id, civ, nom, prenom, identifiant, offline
					var newOption = document.createElement("option");
					newOption.setAttribute("value",'0');
					newOption.setAttribute("id","select0");
					newOption.innerHTML="Aucun";
					document.getElementById('groupeID').appendChild(newOption); 
					for(var i=0;i<tab.length;i++) {
						var newOption = document.createElement("option");
						newOption.setAttribute("value",tab[i][0]);
						newOption.setAttribute("id","select1");
						newOption.innerHTML=tab[i][1];
						document.getElementById('groupeID').appendChild(newOption);
	
					}	
				}
  			}
		}; 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	

}
