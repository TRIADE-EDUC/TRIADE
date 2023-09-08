enrNoteAjax = function (ideleve,note,sujet,date,coef,notevisible,notationsur,noteexamen,code_mat,prof_id,noteusa,idcl,gid,retourAffiche)
{
	var divid=retourAffiche;
	var myAjax = new Ajax.Request(
		"ajaxNoteEnr.php",
		{	method: "post",
			asynchronous: true,
			parameters: "ideleve="+ideleve+"&note="+note+"&sujet="+sujet+"&date="+date+"&coef="+coef+"&notevisible="+notevisible+"&notationsur="+notationsur+"&noteexamen="+noteexamen+"&code_mat="+code_mat+"&prof_id="+prof_id+"&noteusa="+noteusa+"&idcl="+idcl+"&gid="+gid,
			timeout: 5000,
			onComplete: function (request) {
				if ("ok" == request.responseText)  {
					$(divid).innerHTML="<i>Note enregistrée.</i>";
				}else{
					$(divid).innerHTML="<b>Note non enregistrée !!!</b>";
				}
			}
		}
	);
	
	var myGlobalHandlers = {
		onLoading: function(){$(divid).innerHTML = '&nbsp;&nbsp;&nbsp;<font class="T2" color="black">Sauvegarde en cours ...</font> <img src="./image/temps1.gif" align="center" />';}
	};
	Ajax.Responders.register(myGlobalHandlers);

}
