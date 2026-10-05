// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parameter.js,v 1.1 2021/11/16 13:47:27 dgoron Exp $

function parameter_update(type_param, sstype_param, valeur_param) {
	var request = new http_request();
	var callback = function(response){
		response = JSON.parse(response);
		if(response.valeur_param) {
			document.getElementById('parameter_'+type_param+'_'+sstype_param+'_activated').style.display = 'block';
			document.getElementById('parameter_'+type_param+'_'+sstype_param+'_disabled').style.display = 'none';
		} else {
			document.getElementById('parameter_'+type_param+'_'+sstype_param+'_activated').style.display = 'none';
			document.getElementById('parameter_'+type_param+'_'+sstype_param+'_disabled').style.display = 'block';
		}
	}
	request.request('./ajax.php?module=admin&categ=param&action=update_value&type_param='+type_param+'&sstype_param='+sstype_param+'&valeur_param='+valeur_param, false, '', true, callback);
}