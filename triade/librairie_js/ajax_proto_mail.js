listingGroupeMail = function (idgroupe) {
	var myAjax = new Ajax.Request(
		"ajaxListingGroupeMail.php",
		{	method: "post",
			parameters : "idgroupe="+idgroupe,
			asynchronous: true,
			timeout: 5000,
			onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
                        onComplete: function (request) {
				$('id1').innerHTML = request.responseText;
                        }

		}
	)
	show_loader(true);
}

show_loader = function (display) { $('id1').style.display = (display == true)?'inline':'none'; }

listingGroupeMailEleve = function (idgroupe) {
	var myAjax = new Ajax.Request(
		"ajaxListingGroupeMailEleve.php",
		{	method: "post",
			parameters : "idgroupe="+idgroupe,
			asynchronous: true,
			timeout: 5000,
			onLoading: function(){$('id1').innerHTML = '<center><font class="T2">Chargement en cours ...</font> <img src="./image/temps1.gif" align="center" /></center>';},
			onComplete: function (request) {
				$('id1').innerHTML = request.responseText;
                        }
		}
	)
	show_loader(true);
}


