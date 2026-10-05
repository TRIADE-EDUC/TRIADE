// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.js,v 1.5 2019/06/20 10:12:38 dgoron Exp $

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

function scan_requests_expand_all(context) {
	if ((context == undefined) || !context) context = document;
	var tempCollCommentsChild = context.querySelectorAll('tr[class~="scan_requests_children"]');
	var tempColl = Array.prototype.slice.call(tempCollCommentsChild);
	var tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		if (tempColl[i].previousElementSibling.style.display != 'none')
			tempColl[i].style.display = 'table-row';
		var callback = tempColl[i].getAttribute("callback");
		if(callback){
			window[callback]();
		}
		if(typeof ajax_resize_elements == "function"){
			ajax_resize_elements();
		}
	}
	tempColl    = context.querySelectorAll('img[class~="scan_requests_img_plus"]');
	tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		tempColl[i].src = imgOpened.src;
	}
}

function scan_requests_collapse_all(context) {
	if ((context == undefined) || !context) context = document;
	var tempCollCommentsChild = context.querySelectorAll('tr[class~="scan_requests_children"]');
	var tempColl = Array.prototype.slice.call(tempCollCommentsChild);
	  
	var tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		tempColl[i].style.display = 'none';
	}
	tempColl    = context.querySelectorAll('img[class~="scan_requests_img_plus"]');
	tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		//on teste sur 2 niveaux
//		if(Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('parent')!= -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('parent') != -1) {
			tempColl[i].src = imgClosed.src;
//		}
	}
}