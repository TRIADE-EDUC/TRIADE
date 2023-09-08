ActiveCentraleStage = function () {
	document.getElementById('supp').value='non';
	var myAjax = new Ajax.Request("valideCentralStage.php", { method: "post", asynchronous: true, timeout: 5000 } );
	document.forms['formulaire'].submit();
}

DesactiveCentraleStage = function () {
	document.getElementById('supp').value='oui';
	new Ajax.Request("desactiveCentralStage.php",{method: "post",asynchronous: true,timeout: 5000});
	document.forms['formulaire'].submit();
}
