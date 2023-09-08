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


function verifSujet(item,date,idclasse,idgroupe,idmatiere) {
	divid="info";
	var myAjax = new Ajax.Request(
		"verifSujetNote.php",
		{	method: "post",
			parameters : "sujet="+item+"&date="+date+"&idg="+idclasse+"&idc="+idgroupe+"&idm="+idmatiere,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color=black >Vérification en cours...</font> <img src="./image/temps1.gif" align="center" /><br />';},
		onLoaded: function(){$(divid).innerHTML = '';},
		onTimeout: function(){ show_loader(false);
				 	$(divid).innerHTML = "<font class='T1'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText2 = function (request) {
	if (request.responseText != "") {
		document.getElementById("info").innerHTML=request.responseText;	
		document.form11.validation.disabled=true;
		document.form11.valideSaisie.disabled=true;
		document.form11.valideSaisie.checked=false;
	}else{
		document.getElementById("info").innerHTML="";
		document.form11.validation.disabled=false;
		document.form11.valideSaisie.disabled=false;
	}
}


show_loader = function (display)
{
	$(divid).style.display = (display == true)?'inline':'none';
}


var divid1;
function AfficheTrimestreAjax(date1,idclasse,infodiv) {
	divid1=infodiv;
	var myAjax = new Ajax.Request(
		"AjaxRecupTrimestreNote.php",
		{	method: "post",
			parameters : "idclasse="+idclasse+"&date="+date1,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText3
		}
	);
	show_loader2(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid1).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color=black >Vérification en cours...</font> <img src="./image/temps1.gif" align="center" /><br /><br />';},
		onLoaded: function(){$(divid1).innerHTML = '';},
		onTimeout: function(){ show_loader(false);
				 	$(divid1).innerHTML = "<font class='T1'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText3 = function (request) {
	if (request.responseText != "") {
 		// crée un nouveau noeud d'élément vide
 		// sans aucun ID, attribut ou contenu
 		var sp1 = document.createElement("div");

 		// lui donne un attribut id appelé 'newSpan'
 		sp1.setAttribute("id", "newSpan");

 		sp1.setAttribute("style", "font-weight:italic;font-size:15px");


 		// crée un peu de contenu pour cet élément.
 		var sp1_content = document.createTextNode(request.responseText);

 		// ajoute ce contenu au nouvel élément
 		sp1.appendChild(sp1_content);

 		var sp2 = document.getElementById(divid1);
 		var parentDiv = sp2.parentNode;

 		// insère le nouvel élément dans le DOM avant sp2
 		parentDiv.insertBefore(sp1, sp2);
	}
}

show_loader2 = function (display)
{
	$(divid1).style.display = (display == true)?'inline':'none';
}
