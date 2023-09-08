var divid1="";
ajax_actualisepiecejointeCompta = function (idpiecejointe,retourAffiche) {
	divid1=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxActualisePieceJointeCompta.php",
		{	method: "post",
			parameters : "idpiecejointe="+idpiecejointe,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText1
		}
	);
	show_loader1(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid1).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader1(false);
		           document.getElementById(divid1).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText1 = function (request) { document.getElementById(divid1).innerHTML = request.responseText; }
show_loader1 = function (display) { document.getElementById(divid1).style.display = (display == true)?'inline':'none'; }

// ---------------------------------------------------------------------------
var divid11="";
suppPieceJointeCompta = function (ficmd5,retourAffiche,idpiecejointe) {
	divid11=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxSuppPieceJointeCompta.php",
		{	method: "post",
			parameters : "ficmd5="+ficmd5+"&idpiecejointe="+idpiecejointe,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText11
		}
	);
	show_loader11(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid11).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader11(false);
		           document.getElementById(divid11).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText11 = function (request) { document.getElementById(divid11).innerHTML = request.responseText; updatefichier("ok"); }
show_loader11 = function (display) { document.getElementById(divid11).style.display = (display == true)?'inline':'none'; }
//-----------------------------------------------------------------------------------------------------------------------------------------------
