flagImpMessage = function (id)
{
	var divid='info';
	var myAjax = new Ajax.Request(
		"ajaxImpressionMessage.php",
		{	method: "post",
			asynchronous: true,
			parameters: "id="+id,
			timeout: 5000,
			onComplete: displayText
		}
	);
}

displayText = function (request)
{
	$(divid).innerHTML = request.responseText;
}
