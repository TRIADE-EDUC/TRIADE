function verifMailExist(email,retourAffiche,elem,idpers) {

	if (email == "") return ;
	
	var divid=retourAffiche;
	var divid2=elem;
	var myAjax = new Ajax.Request(
		"ajaxVerifEmailExist.php",
		{	method: "post",
			asynchronous: true,
			parameters: {email:email,idpers:idpers},
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					document.getElementById(divid).innerHTML="&nbsp;<img src='image/commun/stat1.gif' />";
				}else{
					document.getElementById(divid).innerHTML="&nbsp;<img src='image/commun/stat2.gif' /> <font class='color2'><b>Email affecté</b></font>";
					document.getElementById(divid2).value="";
				}
			}
		}
	);
	
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;<img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);
	
}