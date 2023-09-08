
//parameters: "ideleve="+ideleve+"&commentaire="+commentaire+"&tri="+tri,
enrVisaDir = function (ideleve,commentaire,tri,retourAffiche,mention,typebulletin,anneeScolaire)
{
	var divid=retourAffiche;
	commentaire=filtreAjax(commentaire);
	var myAjax = new Ajax.Request(
		"ajaxVisaDir.php",
		{	method: "post",
			asynchronous: true,
			parameters: { ideleve:ideleve, commentaire:commentaire, tri:tri, typebulletin:typebulletin, mention:mention, anneeScolaire:anneeScolaire },
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Commentaire enregistré.</i>";
				}else{
					$(divid).innerHTML="<b>Commentaire non enregistré !!!</b>";
				}
			}
		}
	);
	
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);

}


// parameters: "ideleve="+ideleve+"&commentaire="+commentaire+"&tri="+tri,
enrVisaProfp = function (ideleve,commentaire,tri,retourAffiche,mention,typebulletin,anneeScolaire)
{
	var divid=retourAffiche;
	commentaire=filtreAjax(commentaire);
	var myAjax = new Ajax.Request(
		"ajaxVisaProfp.php",
		{	method: "post",
			asynchronous: true,
			parameters: { ideleve:ideleve, commentaire:commentaire, tri:tri, typebulletin:typebulletin, mention:mention, anneeScolaire:anneeScolaire },
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Commentaire enregistré.</i>";
				}else{
					$(divid).innerHTML="<b>Commentaire non enregistré !!!</b>";
				}
			}
		}
	);

	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);
}

afficheDevoir = function (idDevoir,type) {
	document.getElementById('recupinfo').value="";
	var myAjax = new Ajax.Request(
		"ajaxRecupDevoir.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idDevoir="+idDevoir+"&type="+type,
			timeout: 5000,
			onComplete: function (request) {
				document.getElementById('devoir').value=idDevoir;
				document.getElementById('recupinfo').value=request.responseText;				
			}
		}
	);
	setTimeout("recupInfoDevoir()",2000);
}

afficheDevoir2 = function (idDevoir,type)  {
	document.getElementById('recupinfo').value="";
	var myAjax = new Ajax.Request(
		"ajaxRecupDevoir.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idDevoir="+idDevoir+"&type="+type,
			timeout: 5000,
			onComplete: function (request) {
				document.getElementById('devoir').value=idDevoir;
				document.getElementById('devoirvisu').value=type;
				document.getElementById('recupinfo').value=request.responseText;
			}
		}
	);
	setTimeout("recupInfoDevoir()",2000);
}


function recupInfoDevoir() {
	info=document.getElementById('recupinfo').value;
//	info=info.replace(/'/g,"\\'");
	info=info.replace(/"/g,'\\"');
	info=info.replace(/\n/g," ");
	tinyMCE.get('elm1').setContent(info) ;
}

enrvisacahiertexte = function  (idclasse,idmatiere,datedebut,datefin,retourAffiche,classorgrp,idprof) {
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxVisaCahierText.php",
		{	method: "post",
			asynchronous: true,
			parameters: { idclasse:idclasse, idmatiere:idmatiere, datedebut:datedebut, datefin:datefin, classorgrp:classorgrp, idprof:idprof },
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<font id='color3' ><i> ---> Visa enregistré.</i></font>";
				}else{
					$(divid).innerHTML="<b>Visa non enregistré !!!</b>";
				}
			}
		}
	);

	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


enrDevoir  = function (idDevoir,commentaire,retourAffiche,type)
{
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxSaveDevoir.php",
		{	method: "post",
			asynchronous: true,
			parameters: { idDevoir:idDevoir, commentaire:commentaire, type:type },
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Commentaire enregistré.</i>";
				}else{
					$(divid).innerHTML="<b>Commentaire non enregistré !!!</b>";
				}
			}
		}
	);

	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);
}


supprDevoir  = function (idDevoir,retourAffiche,type)
{
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxSuppDevoir.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idDevoir="+idDevoir+"&type="+type,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Information supprimée.</i>";
				}else{
					$(divid).innerHTML="<b>Information non supprimée !!!</b>";
				}
			}
		}
	);

	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Traitement en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);
}
