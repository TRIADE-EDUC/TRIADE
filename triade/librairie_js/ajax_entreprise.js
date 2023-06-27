searchEntreprise = function (idEntreprise)
{
	var myAjax = new Ajax.Request(
		"ajaxInfoEntreprise.php",
		{	method: "post",
			parameters : "idEntreprise="+idEntreprise,
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
