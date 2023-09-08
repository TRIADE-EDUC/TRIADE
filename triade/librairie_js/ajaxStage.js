
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

function checkListMulti(identreprise,urlcentral,p,productid,idfen) {
	document.getElementById('patient_'+idfen).innerHTML="&nbsp;&nbsp;Chargement en cours... <img src='image/commun/indicator.gif' align='center' />";
	document.getElementById('nom_entreprise_via_central_'+idfen).value='';
	document.getElementById('lieu_'+idfen).value='';
	document.getElementById('ville_'+idfen).value='';
	document.getElementById('postal_'+idfen).value='';
	document.getElementById('responsable_'+idfen).value='';
	document.getElementById('tel_'+idfen).value='';
	document.getElementById('pays_'+idfen).value='';
	document.getElementById('fax_'+idfen).value='';
//	removeOptionSelected('idtuteur');
	if (identreprise.search("CS:") != '-1') {
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nom_entreprise_via_central_'+idfen,'nom_entreprise_via_central');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'registrecommerce_'+idfen,'registrecommerce');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siren_'+idfen,'siren');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siret_'+idfen,'siret');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'formejuridique_'+idfen,'formejuridique');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'secteureconomique_'+idfen,'secteureconomique');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'INSEE_'+idfen,'INSEE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'NAFAPE_'+idfen,'NAFAPE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'NACE_'+idfen,'NACE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'typeorganisation_'+idfen,'typeorganisation');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'contact_'+idfen,'contact');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'fonction_'+idfen,'fonction');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'adressesiege_'+idfen,'adressesiege');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite_'+idfen,'activite');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite2_'+idfen,'activite2');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite3_'+idfen,'activite3');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activiteprin_'+idfen,'activiteprin');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'grphotelier_'+idfen,'grphotelier');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nbetoile_'+idfen,'nbetoile');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nbchambre_'+idfen,'nbchambre');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'email_'+idfen,'email');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siteweb_'+idfen,'siteweb');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'information_'+idfen,'information');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'lieu_'+idfen,'lieu');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'ville_'+idfen,'ville');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'postal_'+idfen,'postal');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'responsable_'+idfen,'responsable');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'tel_'+idfen,'tel');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'pays_'+idfen,'pays');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'fax_'+idfen,'fax');	 
		alert('Procédure de recherche en cours...\n\nVeuillez patientez quelques instants avant de cliquer sur "OK" ');
	}else{ 
		AdressEntreprise(identreprise,'lieu_'+idfen,'lieu');
		AdressEntreprise(identreprise,'ville_'+idfen,'ville');
		AdressEntreprise(identreprise,'postal_'+idfen,'postal');
		AdressEntreprise(identreprise,'responsable_'+idfen,'responsable');
		AdressEntreprise(identreprise,'tel_'+idfen,'tel');
		AdressEntreprise(identreprise,'pays_'+idfen,'pays');
		AdressEntreprise(identreprise,'fax_'+idfen,'fax');
		AdressEntreprise(identreprise,'nom_entreprise_via_central_'+idfen,'nom');
	}
	document.getElementById("aff_"+idfen).style.display='block';
	document.getElementById('patient_'+idfen).innerHTML="";
}

function checkList(identreprise,urlcentral,p,productid) {
	document.getElementById('nom_entreprise_via_central').value='';
	document.getElementById('lieu').value='';
	document.getElementById('ville').value='';
	document.getElementById('postal').value='';
	document.getElementById('responsable').value='';
	document.getElementById('tel').value='';
	document.getElementById('pays').value='';
	document.getElementById('fax').value='';
	removeOptionSelected('idtuteur');
	if (identreprise.search("CS:") != '-1') {
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nom_entreprise_via_central','nom_entreprise_via_central');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'registrecommerce','registrecommerce');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siren','siren');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siret','siret');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'formejuridique','formejuridique');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'secteureconomique','secteureconomique');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'INSEE','INSEE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'NAFAPE','NAFAPE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'NACE','NACE');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'typeorganisation','typeorganisation');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'contact','contact');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'fonction','fonction');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'adressesiege','adressesiege');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite','activite');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite2','activite2');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activite3','activite3');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'activiteprin','activiteprin');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'grphotelier','grphotelier');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nbetoile','nbetoile');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'nbchambre','nbchambre');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'email','email');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'siteweb','siteweb');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'information','information');
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'lieu','lieu');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'ville','ville');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'postal','postal');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'responsable','responsable');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'tel','tel');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'pays','pays');	
		AdressEntrepriseCS(urlcentral,p,productid,identreprise,'fax','fax');	 
		alert('Procédure de recherche en cours...\n\nVeuillez patientez quelques instants avant de cliquer sur "OK" ');
	}else{ 
		AdressEntreprise(identreprise,'lieu','lieu');
		AdressEntreprise(identreprise,'ville','ville');
		AdressEntreprise(identreprise,'postal','postal');
		AdressEntreprise(identreprise,'responsable','responsable');
		AdressEntreprise(identreprise,'tel','tel');
		AdressEntreprise(identreprise,'pays','pays');
		AdressEntreprise(identreprise,'fax','fax');
		TuteurEntreprise(identreprise);
	}
}

function checkListModif(identreprise) {
	TuteurEntreprise(identreprise);
}

function timeo() { alert('Time off : CODE 0A05'); }

function AdressEntrepriseCS(urlcentral,p,productid,identreprise,retourAffiche,info) {        
	var isIE8 = window.XDomainRequest ? true : false;
	if(isIE8) {
		var xdr = new XDomainRequest();
		var dividxdr=retourAffiche;
		var _info=info;
		if (xdr) {
	    		xdr.onerror = function() { 
			if (_info == 'lieu') { 
				alert("Probleme d'acces a la centrale de stage"); 
				}	
			};
		   	xdr.ontimeout = timeo ;
		     	xdr.onload = function() { document.getElementById(dividxdr).value=xdr.responseText; } ; 
	    		xdr.timeout = 10000;
		    	xdr.open("POST",urlcentral+"ajaxStageCS.php");
		   	xdr.send('p='+p+'&productid='+productid+'&identreprise='+identreprise+'&info='+info);
		}else{
	    		alert('CODE ERROR : 0A05 - Navigateur non compatible ');
		}	
	}else{
		var request = new XMLHttpRequest();
		if (request) {
			var dividxdr=retourAffiche;
			request.open('POST', urlcentral+"ajaxStageCS.php", true);
			request.onreadystatechange = function (evtXHR) {
				if (request.readyState == 4) {
            				if (request.status == 200) {
						document.getElementById(dividxdr).value=request.responseText;
					}	
				}
			}
			request.send('p='+p+'&productid='+productid+'&identreprise='+identreprise+'&info='+info);
		}else{
			alert('CODE ERROR : 0A05bis - Navigateur non compatible ');
		}
	}
}


AdressEntreprise = function (identreprise,retourAffiche,info) {
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxStage.php",
		{	method: "post",
			parameters : "identreprise="+identreprise+"&info="+info,
			asynchronous: true,
			timeout: 5000,
			onComplete: function(transport) {  
				if (200 == transport.status)  {
					document.getElementById(divid).value =transport.responseText;
				}else{
					document.getElementById(divid).value ='';
				}
			} 
		}
	);
	var myGlobalHandlers = {
		onLoading: function(){document.getElementById(divid).value = 'Recherche....';}
	};
	if (retourAffiche.search("_") == '-1') Ajax.Responders.register(myGlobalHandlers);
}




function removeOptionSelected(id) {
  var elSel = document.getElementById(id);
  var i;
  for (i = elSel.length -1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}



TuteurEntreprise = function (identreprise) {
	var requete = getRequete();
	removeOptionSelected('idtuteur');
	var corps="identreprise="+escape(identreprise);
	if (requete != null) {
		requete.open("POST","ajaxStageTuteur.php",true); 
		requete.onreadystatechange = function() { 
	    		if(requete.readyState == 4) {
	       			if(requete.status == 200) {
					var tab=phpUnserialize(requete.responseText);
//alert(requete.responseText);
					var newOption = document.createElement("option");
					newOption.setAttribute("value",'0');
					newOption.setAttribute("id","select0");
					newOption.innerHTML="Aucun";
					document.getElementById('idtuteur').appendChild(newOption); 
					for(var i=0;i<tab.length;i++) {
						var newOption = document.createElement("option");
						newOption.setAttribute("value",tab[i][0]);
						newOption.setAttribute("id","select1");
						newOption.innerHTML=tab[i][2]+" "+tab[i][3];
						document.getElementById('idtuteur').appendChild(newOption);
					}	
		
				}
  			}
		}; 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	
}
