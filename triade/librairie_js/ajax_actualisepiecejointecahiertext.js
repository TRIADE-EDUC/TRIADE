var divid1="";
ajaxActualisePieceJointeCahierText1 = function (idpiecejointe,retourAffiche,number) {
	divid1=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxActualisePieceJointeCahierText.php",
		{	method: "post",
			parameters : "idpiecejointe="+idpiecejointe+"&number="+number,
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
var divid2="";
ajaxActualisePieceJointeCahierText2 = function (idpiecejointe,retourAffiche,number) {
	divid2=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxActualisePieceJointeCahierText.php",
		{	method: "post",
			parameters : "idpiecejointe="+idpiecejointe+"&number="+number,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	);
	show_loader2(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid2).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader2(false);
		           document.getElementById(divid2).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText2 = function (request) { document.getElementById(divid2).innerHTML = request.responseText; }
show_loader2 = function (display) { document.getElementById(divid2).style.display = (display == true)?'inline':'none'; }
// ---------------------------------------------------------------------------
var divid3="";
ajaxActualisePieceJointeCahierText3 = function (idpiecejointe,retourAffiche,number) {
	divid3=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxActualisePieceJointeCahierText.php",
		{	method: "post",
			parameters : "idpiecejointe="+idpiecejointe+"&number="+number,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText3
		}
	);
	show_loader3(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid3).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader3(false);
		           document.getElementById(divid3).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText3 = function (request) { document.getElementById(divid3).innerHTML = request.responseText; }
show_loader3 = function (display) { document.getElementById(divid3).style.display = (display == true)?'inline':'none'; }
//-----------------------------------------------------------------------------------------------------------------------------------------------
//
//
var divid11="";
suppPieceJointeCahierText11 = function (ficmd5,retourAffiche,idpiecejointe,number) {
	divid11=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxSuppPieceJointeCahierText.php",
		{	method: "post",
			parameters : "ficmd5="+ficmd5+"&idpiecejointe="+idpiecejointe+"&number="+number,
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
//
//
var divid22="";
suppPieceJointeCahierText22 = function (ficmd5,retourAffiche,idpiecejointe,number) {
	divid22=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxSuppPieceJointeCahierText.php",
		{	method: "post",
			parameters : "ficmd5="+ficmd5+"&idpiecejointe="+idpiecejointe+"&number="+number,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText22
		}
	);
	show_loader22(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid22).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader22(false);
		           document.getElementById(divid22).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText22 = function (request) { document.getElementById(divid22).innerHTML = request.responseText; updatefichier("ok"); }
show_loader22 = function (display) { document.getElementById(divid22).style.display = (display == true)?'inline':'none'; }
//-----------------------------------------------------------------------------------------------------------------------------------------------
//
//
var divid33="";
suppPieceJointeCahierText33 = function (ficmd5,retourAffiche,idpiecejointe,number) {
	divid33=retourAffiche+number;
	var myAjax = new Ajax.Request(
		"ajaxSuppPieceJointeCahierText.php",
		{	method: "post",
			parameters : "ficmd5="+ficmd5+"&idpiecejointe="+idpiecejointe+"&number="+number,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText33
		}
	);
	show_loader33(true);
	var myGlobalHandlers = {
		onLoading: function(){ document.getElementById(divid33).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T1" color="red">Recherche en cours... <img src="./image/temps1.gif" align="center" />';},
		
		onTimeout: function(){ show_loader33(false);
		           document.getElementById(divid33).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
displayText33 = function (request) { document.getElementById(divid33).innerHTML = request.responseText; updatefichier("ok");  }
show_loader33 = function (display) { document.getElementById(divid33).style.display = (display == true)?'inline':'none'; }



