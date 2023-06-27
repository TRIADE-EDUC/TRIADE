suppModif = function (id,idenr1,idsupp1,idmodif1,trsupp1,retourAffiche) {
	idenr=idenr1;
	idsupp=idsupp1;
	idmodif=idmodif1;
	trsupp=trsupp1;
	divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxComptaModifModele.php",
		{	method: "post",
			parameters : "idsupp="+id,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	)
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="red">Suppression en cours ...</font> <img src="./image/temps1.gif" align="center" />';},
		onLoaded: function(){$(divid).innerHTML = '<font class="T2">Chargement terminé</font>';},
		onTimeout: function(){ show_loader(false);
				 	$(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText2 = function (request) {
	$(divid).innerHTML = request.responseText;
	$(idenr).style.visibility='hidden';
	$(idsupp).style.visibility='hidden';
	$(idmodif).style.visibility='hidden';
	$(trsupp).style.visibility='hidden';
}


show_loader = function (display)
{
	$(divid).style.display = (display == true)?'inline':'none';
}
