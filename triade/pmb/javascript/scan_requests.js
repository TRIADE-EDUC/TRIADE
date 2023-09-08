// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.js,v 1.4 2018-12-06 12:27:17 dgoron Exp $

function show_scan_request(id_suffix) {
	var div = document.getElementById('scan_request'+id_suffix);
	if(div.style.display == 'block'){
		div.style.display = 'none';
	} else {
		div.style.display = 'block';
	}
}

function create_scan_request_in_record(id_suffix, record_type, record_id) {
	var title = document.getElementById('scan_request_title' + id_suffix).value;
	var desc = document.getElementById('scan_request_desc' + id_suffix).value;
	var num_location = 0;
	if(document.getElementById('scan_request_num_location' + id_suffix)) {
		num_location = document.getElementById('scan_request_num_location' + id_suffix).value;
	}
	var priority = document.getElementById('scan_request_priority' + id_suffix).value;
	var date = document.getElementById('scan_request_date' + id_suffix).value;
	var wish_date = document.getElementById('scan_request_wish_date' + id_suffix).value;
	var deadline_date = document.getElementById('scan_request_deadline_date' + id_suffix).value;
	var comment = document.getElementById('scan_request_comment' + id_suffix).value;
	var status = document.getElementById('scan_request_status' + id_suffix).value;
	var record_comment = document.getElementById('scan_request_linked_records_' + record_type + '_' + record_id + '_comment' + id_suffix).value;
	var params = '&scan_request_title='+title
		+'&scan_request_desc='+desc
		+'&scan_request_num_location='+num_location
		+'&scan_request_priority='+priority
		+'&scan_request_date='+date
		+'&scan_request_wish_date='+wish_date
		+'&scan_request_deadline_date='+deadline_date
		+'&scan_request_comment='+comment
		+'&scan_request_status='+status
		+'&scan_request_linked_records_'+record_type+'['+record_id+'][comment]='+record_comment;
	var req = new http_request();
	req.request('ajax.php?module=ajax&categ=scan_requests&sub=form&action=create', true, params, true, function(data){
		document.getElementById('scan_request'+id_suffix).innerHTML=data;
	});
}

function expand_scan_request(id_scan_request) {
	var element = document.getElementById("scan_request_" + id_scan_request + "_child");
	var img = document.getElementById("scan_request_" + id_scan_request + "_img");
	if (element.style.display) {
		element.style.display = "";
		if(img) img.src = img.src.replace("plus","minus");
	} else {
		element.style.display = "none";
		if(img) img.src = img.src.replace("minus","plus");
	}
}

function scan_request_search_order(what) {
	if(!document.getElementById("scan_request_order_by")) return;
	if(!document.forms["search"]) return;
	
	if (document.getElementById("scan_request_order_by").value != what) {
		document.getElementById("scan_request_order_by").value = what;
		document.getElementById("scan_request_order_by_sens").value = 'asc';
	} else {
		if(document.getElementById("scan_request_order_by_sens")){
			if(document.getElementById("scan_request_order_by_sens").value == 'asc'){
				document.getElementById("scan_request_order_by_sens").value = 'desc';
			}else{
				document.getElementById("scan_request_order_by_sens").value = 'asc';			
			}
		}
	}
	document.forms["search"].submit();
}