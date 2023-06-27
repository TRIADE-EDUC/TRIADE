
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


function removeOptionSelected(id) {
  var elSel = document.getElementById(id);
  var i;
  for (i = elSel.length -1; i>=0; i--) {
       elSel.remove(i);
  }
}


AjaxAffectEntreprise = function (idperiode,url,p,productid) {
	var requete = getRequete();
	removeOptionSelected('ident');
	var corps="idperiode="+escape(idperiode)+"&p="+escape(p)+"&productid="+escape(productid);
	if (requete != null) {
		urlf=url+"ajaxListEntreprise.php";
		requete.open("POST",urlf,true); 
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					var tab=unserialize(requete.responseText); 
					// // id,datedemande,identreprise,sexe,service,observation,nbdemande,nomentreprisen,s.adresse,s.ville,s.code_p,s.contact,s.tel,s.fax,s.email,s.info_plu,idproductreserv,null,salaire,logement
					var newOption = document.createElement("option");
					newOption.setAttribute("value",'0');
					newOption.setAttribute("id","select0");
					newOption.innerHTML="Choix...";
					document.getElementById('lieu').value='';
					document.getElementById('ville').value='';
					document.getElementById('postal').value='';
					document.getElementById('responsable').value='';
					document.getElementById('tel').value='';
					document.getElementById('pays').value='';

			

					document.getElementById('ident').appendChild(newOption); 
					for(var i=0;i<tab.length;i++) {
						var newOption = document.createElement("option");
						newOption.setAttribute("value",tab[i][2]);
						newOption.setAttribute("id","select1");
						newOption.innerHTML=tab[i][7];
						document.getElementById('ident').appendChild(newOption);
					}	
				}
  			}
		}; 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	
}

AjaxDatePeriode = function (url,p,productid) {
	removeOptionSelected('periode');
	var script = document.createElement('script');
	    script.src = url+'ajaxPeriodeCentraleStage.php?';
	    script.src += 'p=' + p;
	    script.src += '&productid=' + productid;
	    script.id = 'requestMultiplier';
	    script.type = 'text/javascript';
 
	// Et injection dans le DOM :
    	document.body.appendChild(script);
}

/*
					var tab=unserialize(requete.responseText); 

					// datedebut,datefin,id,nomstage
					var newOption = document.createElement("option");
					newOption.setAttribute("value",'0');
					newOption.setAttribute("id","select0");
					newOption.innerHTML="Choix...";
					document.getElementById('lieu').value='';
					document.getElementById('ville').value='';
					document.getElementById('postal').value='';
					document.getElementById('responsable').value='';
					document.getElementById('tel').value='';
					document.getElementById('periode').appendChild(newOption); 
					for(var i=0;i<tab.length;i++) {
						var newOption = document.createElement("option");
						newOption.setAttribute("value",tab[i][2]);
						newOption.setAttribute("id","select1");
						newOption.innerHTML="("+tab[i][3].") "+tab[i][0]+" - "+tab[i][1]+"</option>";
						document.getElementById('periode').appendChild(newOption);
					}	

				}
  			}
		}; 
	//	requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(); 
	}	
}

*/

AjaxAffectDateStage = function (idperiode,url,p,productid) {
	var requete = getRequete();
	document.getElementById('debutdate').value='';
	document.getElementById('findate').value='';
	document.getElementById('num').value='';
	document.getElementById('nom_stage').value='';
	var corps="idperiode="+escape(idperiode)+"&p="+escape(p)+"&productid="+escape(productid);
	if (requete != null) {
		urlf=url+"ajaxDateStage.php";
		requete.open("POST",urlf,true); 
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) { //  nomstage,datedebut,datefin,id
					var tab=unserialize(requete.responseText); 
					document.getElementById('debutdate').value=tab[0][1];
					document.getElementById('findate').value=tab[0][2];
					document.getElementById('num').value="-"+tab[0][3];
					document.getElementById('nom_stage').value=tab[0][0];
				}
  			}
		}; 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	

}
