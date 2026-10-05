// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.js,v 1.9.4.1 2025/02/27 14:05:54 dgoron Exp $

function show_scan_request(record_id, record_type) {
	var ajax_scan_request = new http_request();
	var url = './ajax.php?module=ajax&categ=scan_requests&sub=form&action=edit&record_id='+record_id+'&record_type='+record_type;
	ajax_scan_request.request(url,0,'',1,function(data) {
		document.getElementById('frame_notice_preview').innerHTML=data;
		var tags = document.getElementById('frame_notice_preview').getElementsByTagName('script');
   		for(var i=0;i<tags.length;i++){
			window.eval(tags[i].text);
    	}
   		if(document.body.className.indexOf('tundra') === -1) {
   			document.body.className += ' tundra';
   		}
		dojo.parser.parse(document.getElementById('frame_notice_preview'));
		if (typeof focus_trap == 'function') {
			focus_trap(document.getElementById('frame_notice_preview'));
		}

		document.addEventListener('keydown', function(event){
			if(event.key === 'Escape'){
				kill_scan_request_frame();
			}
		})

	},0,0);
}

function kill_scan_request_frame() {
	var scan_request_view=document.getElementById('frame_notice_preview');
	if (scan_request_view) {
		dojo.forEach(dijit.findWidgets(dojo.byId('frame_notice_preview')), function(w) {
		    w.destroyRecursive();
		});
		scan_request_view.parentNode.removeChild(scan_request_view);
	}
	document.querySelector('*[onclick*="show_layer(); show_scan_request"]').focus();
}

function create_scan_request_in_record(id_suffix, record_type, record_id) {
	var title = document.getElementById('scan_request_title' + id_suffix).value;
	var desc = document.getElementById('scan_request_desc' + id_suffix).value;
	var nb_scanned_pages = document.getElementById('scan_request_nb_scanned_pages' + id_suffix).value;
	var num_location = 0;
	if(document.getElementById('scan_request_num_location' + id_suffix)) {
		num_location = document.getElementById('scan_request_num_location' + id_suffix).value;
	}
	var priority = document.getElementById('scan_request_priority' + id_suffix).value;
	var date = document.getElementById('scan_request_date' + id_suffix).value;
	var wish_date = document.getElementById('scan_request_wish_date' + id_suffix).value;
	var deadline_date = document.getElementById('scan_request_deadline_date' + id_suffix).value;
	var status = document.getElementById('scan_request_status' + id_suffix).value;
	var record_comment = document.getElementById('scan_request_linked_records_' + record_type + '_' + record_id + '_comment' + id_suffix).value;
	
	var params = '&scan_request_title='+title
		+'&scan_request_desc='+desc
		+'&scan_request_nb_scanned_pages='+nb_scanned_pages
		+'&scan_request_num_location='+num_location
		+'&scan_request_priority='+priority
		+'&scan_request_date='+date
		+'&scan_request_wish_date='+wish_date
		+'&scan_request_deadline_date='+deadline_date
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
		if(img) img.src = img.src.replace("nomgif=plus","nomgif=moins");
	} else {
		element.style.display = "none";
		if(img) img.src = img.src.replace("nomgif=moins","nomgif=plus");
	}
}

function expand_scan_request_records(record_id, record_type) {
	var element = document.getElementById("scan_request_" + record_type + "_" + record_id + "_child");
	var img = document.getElementById("scan_request_" + record_type + "_" + record_id + "_img");
	var imgParent = img.parentNode;
	if (element.style.display) {
		element.style.display = "";
		if (img) img.src = img.src.replace("nomgif=plus", "nomgif=moins");
		if (imgParent.hasAttribute('aria-expanded')) {
			imgParent.setAttribute('aria-expanded', 'true');
		}
	} else {
		element.style.display = "none";
		if (img) img.src = img.src.replace("nomgif=moins", "nomgif=plus");
		if (imgParent.hasAttribute('aria-expanded')) {
			imgParent.setAttribute('aria-expanded', 'false');
		}
	}
}