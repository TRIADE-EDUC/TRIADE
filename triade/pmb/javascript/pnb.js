// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.js,v 1.2 2018/06/28 12:34:27 ngantier Exp $

function pnb_get_loans_completed_number(record_id, line_id) {
	var request = new http_request();
	var callback = function(response){
		response = JSON.parse(response);		
		document.getElementById('nb_loans_' + response.line_id).innerHTML = response.loans_completed_number;		
	}
	request.request('./ajax.php?module=ajax&categ=pnb&sub=offer&line_id=' + line_id + '&record_id=' + record_id + '&action=get_loans_completed_number', false, '', true, callback);
}

function pnb_get_loans_completed_number_by_line_id(line_id) {
	var request = new http_request();
	var callback = function(response){
		response = JSON.parse(response);		
		document.getElementById('nb_loans_' + response.line_id).innerHTML = response.loans_completed_number;		
	}
	request.request('./ajax.php?module=ajax&categ=pnb&sub=offer&line_id=' + line_id + '&action=get_loans_completed_number_by_line_id', false, '', true, callback);
}