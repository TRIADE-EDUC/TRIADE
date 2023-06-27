enrCreditCantine = function (idpers,membre,date,credit,detail,retourAffiche)
{
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxCreditCantine.php",
		{	method: "post",
			asynchronous: true,
			parameters: "idpers="+idpers+"&membre="+membre+"&date="+date+"&credit="+credit+"&detail="+detail,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Crédit enregistré.</i>";
				}else{
					$(divid).innerHTML="<b>Crédit non enregistré !!!</b>";
				}
			}
		}
	);
	
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);

}
