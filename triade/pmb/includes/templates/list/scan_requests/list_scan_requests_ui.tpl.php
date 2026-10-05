<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scan_requests_ui.tpl.php,v 1.1 2019/06/20 10:12:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $list_scan_requests_ui_search_form_tpl;
global $msg;

$list_scan_requests_ui_search_form_tpl = "
<script type='text/javascript'>
	function scan_request_save_ajax(){
   		var scan_request_elapsed_time = document.getElementById('scan_request_elapsed_time').value;
   		var scan_request_nb_scanned_pages = document.getElementById('scan_request_nb_scanned_pages').value;
   		var scan_request_status = document.getElementById('scan_request_status').value;
   		var scan_request_comment = document.getElementById('scan_request_comment').value;
   		var id = document.getElementById('scan_request_id').value;
		var xhrArgs = {
			url : './ajax.php?module=circ&categ=scan_request&sub=save&scan_request_elapsed_time='+scan_request_elapsed_time+'&scan_request_nb_scanned_pages='+scan_request_nb_scanned_pages+'&scan_request_status='+scan_request_status+'&scan_request_comment='+scan_request_comment+'&num_request='+id,
			handleAs: 'json',
			load: function(data){
				if(document.getElementById('scan_request_img_statut_part_'+data.id)){
					document.getElementById('scan_request_img_statut_part_'+data.id).className=data.statut_class_html;
				}
				if(document.getElementById('scan_request_statut_part_'+data.id)){
					document.getElementById('scan_request_statut_part_'+data.id).innerHTML=data.statut_label;
				}
				if(document.getElementById('scan_request_elapsed_time_part_'+data.id)){
					document.getElementById('scan_request_elapsed_time_part_'+data.id).innerHTML=data.elapsed_time;
				}
				if(document.getElementById('scan_request_comment_part_'+data.id)){
					document.getElementById('scan_request_comment_part_'+data.id).innerHTML=data.comment;
				}
			
				dijit.byId('scan_request_layer').hide();
			}
		};
		dojo.xhrPost(xhrArgs);
	}

	function test_form(form){
		return true;
	}
	require(['dijit/registry', 'apps/pmb/PMBDialog', 'dojo/topic'], function (registry, Dialog, topic) {
		window.scan_request_show_form = function(id){
	     	if(!registry.byId('scan_request_layer')){
	        	var myDijit = new Dialog({title: '".$msg["scan_request_popup_title"]."',executeScripts:true, id:'scan_request_layer', style:{width:'85%'}});
			}else{
				var myDijit = registry.byId('scan_request_layer');
			}
	        var path = './ajax.php?module=circ&categ=scan_request&sub=edit&num_request='+id;      
	        myDijit.attr('href', path);
	     	myDijit.startup();
	        myDijit.show();
		},
		
		window.record_title_copy = function(title) {
			var record_title_for_copy = document.getElementById('record_title_for_copy');
		    record_title_for_copy.style.display = 'block';
			record_title_for_copy.value = title;
			try {
				record_title_for_copy.select();
	        	
				var copy_success = document.execCommand('copy');
				if (copy_success) {
	        		
					topic.publish('dGrowl', '".addslashes($msg['scan_request_record_title_copy_success'])."');
				}
			} catch (e) {
				prompt('".addslashes($msg['scan_request_record_title_copy_prompt'])."', record_title_for_copy.value);
			}
			record_title_for_copy.value = '';
		    record_title_for_copy.style.display = 'none';
		}
     });
</script>
<script type='text/javascript'>
	if(document.forms['!!objects_type!!_search_form'].elements['!!objects_type!!_user_input']) {
        document.forms['!!objects_type!!_search_form'].elements['!!objects_type!!_user_input'].focus();
    }
</script>
";
