enrModif = function (id,libelle,montant,dateM,retourAffiche,idenr1)
{

	divid=retourAffiche;
	idenr=idenr1;
	var myAjax = new Ajax.Request(
		"ajaxComptaModifModele.php",
		{	method: "post",
			parameters : "id="+id+"&libelle="+libelle+"&montant="+montant+"&date="+dateM,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="red">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';},
		onLoaded: function(){$(divid).innerHTML = '<font class="T2">Chargement terminé</font>';},
		onTimeout: function(){ show_loader(false);
			 	$(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};

	Ajax.Responders.register(myGlobalHandlers);
}


displayText = function (request)
{
	$(divid).innerHTML = request.responseText;
	$(idenr).style.visibility='hidden';
}

show_loader = function (display)
{
	$(divid).style.display = (display == true)?'inline':'none';
}




searchVersement = function (ideleve,id,dateVersOr,montantavers) {
	var myAjax = new Ajax.Request(
		"ajaxComptaVisuModele.php",
		{	method: "post",
			parameters : "id="+id+"&ideleve="+ideleve+"&dateversor="+dateVersOr+"&montantavers="+montantavers,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	)
	show_loader2(true);
	var myGlobalHandlers = {
		onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
		onLoaded: function(){$('id1').innerHTML = '<center><font class="T2">Chargement terminé</font></center>';},
		onTimeout: function(){ show_loader(false);
				 	$('id1').innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}

show_loader2 = function (display)
{
	$('id1').style.display = (display == true)?'inline':'none';
}


displayText2 = function (request)
{
	$('id1').innerHTML = request.responseText;
}

