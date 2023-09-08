ajaxActualisePieceJointe = function (idpiecejointe,retourAffiche) {
	divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxActualisePieceJointe.php",
		{	method: "post",
			parameters : "idpiecejointe="+idpiecejointe,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	);
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		onLoaded: function(){$(divid).innerHTML = '<font class="T2">Chargement terminé</font>';},
		onTimeout: function(){ show_loader(false);
		           $(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText = function (request)
{
	$(divid).innerHTML = request.responseText;
}


show_loader = function (display)
{
	$(divid).style.display = (display == true)?'inline':'none';
}


suppPieceJointe = function (ficmd5,retourAffiche,idpiecejointe) {
	divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxSuppPieceJointe.php",
		{	method: "post",
			parameters : "ficmd5="+ficmd5+"&idpiecejointe="+idpiecejointe,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	);
	show_loader(true);
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		onLoaded: function(){$(divid).innerHTML = '<font class="T2">Chargement terminé</font>';},
		onTimeout: function(){ show_loader(false);
		           $(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
