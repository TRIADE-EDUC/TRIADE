verifPass = function (pass,level,retourAffiche)
{
	divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxVerifPass.php",
		{	method: "post",
			parameters : "pass="+pass+"&level="+level,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText
		}
	)
	document.getElementById(divid).style.display = 'inline' ;
	var myGlobalHandlers = {
		onLoading: function(){document.getElementById(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<img src="image/commun/indicator.gif" >';},
		onLoaded: function(){document.getElementById(divid).innerHTML = '<font class="T2">Vérification terminé</font>';},
		onTimeout: function(){ document.getElementById(divid).style.display = 'none' ;
				 	document.getElementById(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText = function (request)
{
	resultat = request.responseText;
	if (resultat == 1) {
		document.getElementById(divid).innerHTML = "&nbsp;&nbsp;&nbsp<img src='image/commun/stat1.gif' >";
		document.formulaire.confirm_passwd.disabled=false;
	//	document.formulaire.passwd.readonly=true;
	}else{
		document.getElementById(divid).innerHTML = "&nbsp;&nbsp;&nbsp<img src='image/commun/stat2.gif' >";
		document.formulaire.suiteavecpass.disabled=true;
		document.formulaire.passwd_confirm.value="";
		document.formulaire.confirm_passwd.disabled=true;
	}
}

show_loader = function (display)
{
//	$(divid).style.display = (display == true)?'inline':'none';
}


verifPass2 = function (pass1,pass2,level,retourAffiche)
{
	divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxVerifPass.php",
		{	method: "post",
			parameters : "pass1="+pass1+"&pass2="+pass2+"&level="+level,
			asynchronous: true,
			timeout: 5000,
			onComplete: displayText2
		}
	)
	document.getElementById(divid).style.display = 'inline' ;
	var myGlobalHandlers = {
		onLoading: function(){document.getElementById(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<img src="image/commun/indicator.gif" >';},
		onLoaded: function(){document.getElementById(divid).innerHTML = '<font class="T2">Vérification terminé</font>';},
		onTimeout: function(){ document.getElementById(divid).style.display ='none';
				 	document.getElementById(divid).innerHTML = "<font class='T2'><img src='./image/commen/warning.png' align='center' />  Erreur d'accès</font>";
		}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


displayText2 = function (request)
{
	resultat = request.responseText;
	if (resultat == 1) {
		document.getElementById(divid).innerHTML = "&nbsp;&nbsp;&nbsp<img src='image/commun/stat1.gif' >";
		document.formulaire.suiteavecpass.disabled=false;
		document.getElementById('passwd').readOnly=true;
		document.getElementById('confirm_passwd').readOnly=true;
		document.formulaire.passok.value="1";
	}else{
		document.getElementById(divid).innerHTML = "&nbsp;&nbsp;&nbsp<img src='image/commun/stat2.gif' >";
		document.formulaire.suiteavecpass.disabled=true;
		document.formulaire.passok.value="0";
	}
}

