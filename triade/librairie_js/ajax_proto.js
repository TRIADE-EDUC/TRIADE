searchCours = function (idpers,idclasse,dateDebut,dateFin)
{
	var myAjax = new Ajax.Request(
		"ajaxListingCours.php",
		{	method: "post",
			parameters : "idpers="+idpers+"&idclasse="+idclasse+"&dateDebut="+dateDebut+"&dateFin="+dateFin,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
		onLoaded: function(){$('id1').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
		onTimeout: function(){ show_loader(false);
				 $('id1').innerHTML = "<center><font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font></center>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}

show_loader = function (display)
{
	$('id1').style.display = (display == true)?'inline':'none';
}


displayText = function (request)
{
	$('id1').innerHTML = request.responseText;
}


searchCours2 = function (idpers,idclasse,dateDebut,dateFin)
{
	var myAjax = new Ajax.Request(
		"ajaxListingCours2.php",
		{	method: "post",
			parameters : "idpers="+idpers+"&idclasse="+idclasse+"&dateDebut="+dateDebut+"&dateFin="+dateFin,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
		onLoaded: function(){$('id1').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
		onTimeout: function(){ show_loader(false);
				 	$('id1').innerHTML = "<center><font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font></center>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}

var divid;
AjuteEDTHoraire = function (id,heure,duree,iddiv,nbcol)
{
	divid=iddiv;
	var myAjax = new Ajax.Request(
		"ajaxAjoutEDTHoraire.php",
		{	method: "post",
			parameters : "id="+id+"&heure="+heure+"&duree="+duree,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	)
	show_loader2(true,divid);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '<tr><td colspan="'+nbcol+'" ><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></td></tr>';},
		onLoaded: function(){$(divid).innerHTML = '<tr><td colspan="'+nbcol+'" ><font class="T2">Chargement terminé</font></center></td></tr>';},
		onTimeout: function(){ show_loader2(false,divid);
				 	$(divid).innerHTML = '<tr><td colspan="'+nbcol+'" ><font class="T2"><img src="./image/commen/warning.png" align="center" />  Erreur d\'accès</font></td></tr>';
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}

displayText2 = function (request)
{
	$(divid).innerHTML = request.responseText;
}

show_loader2 = function (display,divid)
{
	$(divid).style.display = (display == true)?'inline':'none';
}




searchCoursDate = function (idpers,idclasse,dateDebut,dateFin)
{
	var myAjax = new Ajax.Request(
	/*	"ajaxListingCours.php",
		{     method:'post', 
		      parameters : "idpers="+idpers ,  
			onSuccess: function(transport){       
				var response = transport.responseText || "no response text";       
				alert("Success! \n\n" + response);     
		},     
		onFailure: function(){ alert('Something went wrong...') }   }); 
	*/
		"ajaxListingCoursDate.php",
		{	method: "post",
			parameters : "idpers="+idpers+"&idclasse="+idclasse+"&dateDebut="+dateDebut+"&dateFin="+dateFin,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
		onLoaded: function(){$('id1').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
		onTimeout: function(){ show_loader(false);
				 	$('id1').innerHTML = "<center><font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font></center>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


searchCoursDate2 = function (idpers,idclasse,dateDebut,dateFin)
{
	var myAjax = new Ajax.Request(
		"ajaxListingCoursDate2.php",
		{	method: "post",
			parameters : "idpers="+idpers+"&idclasse="+idclasse+"&dateDebut="+dateDebut+"&dateFin="+dateFin,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
		onLoaded: function(){$('id1').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
		onTimeout: function(){ show_loader(false);
				 	$('id1').innerHTML = "<center><font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font></center>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
