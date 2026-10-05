<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_requests.tpl.php,v 1.20 2021/04/21 20:49:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_requests_list, $msg, $current_module, $pmb_scan_request_location_activate;

$scan_requests_list = "
<script type='text/javascript'>
	function test_form(form){
		if(form.user_input.value.length == 0){
//			alert(\"$msg[141]\");
//			return false;
		}
		return true;
	}
</script>
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
<form class='form-$current_module' name='search' method='post' action='!!action!!' style='width:100%;'>
	<h3>".$msg["scan_request_list_search"]."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<select name='status_search' id='status_search'>
					!!option_status_search!!
				</select>
				<select name='priority_search' id='priority_search'>
					!!option_priority_search!!
				</select>		
				<span class='ui-inlineblock'>
				<label for='scan_request_user_only'>".$msg['scan_request_user_only']."</label>
				<input type='checkbox' name='scan_request_user_only' value='1' !!scan_request_user_only!!/>
				</span>
			</div>
			<div class='colonne3'>
				<input type='text' class='saisie-50em' name='user_input' value='!!user_input!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<div class='row'>		
					<label for='scan_request_date'>".$msg['scan_request_form_date']."</label>
				</div>
				<div class='row'>
					<input type='date' name='scan_request_date_start' id='scan_request_date_start' value='!!scan_request_date_start!!' />
					 - <input type='date' name='scan_request_date_end' id='scan_request_date_end' value='!!scan_request_date_end!!' />
				</div>
			</div>
			<div class='colonne3'>
				<div class='row'>		
					<label for='scan_request_wish_date'>".$msg['scan_request_form_wish_date']."</label>
				</div>
				<div class='row'>
					<input type='date' name='scan_request_wish_date_start' id='scan_request_wish_date_start' value='!!scan_request_wish_date_start!!' />
					 - <input type='date' name='scan_request_wish_date_end' id='scan_request_wish_date_end' value='!!scan_request_wish_date_end!!' />
				</div>
			</div>
			<div class='colonne3'>
				<div class='row'>		
					<label for='scan_request_deadline_date'>".$msg['scan_request_form_deadline_date']."</label>
				</div>
				<div class='row'>
					<input type='date' name='scan_request_deadline_date_start' id='scan_request_deadline_date_start' value='!!scan_request_deadline_date_start!!' />
					 - <input type='date' name='scan_request_deadline_date_end' id='scan_request_deadline_date_end' value='!!scan_request_deadline_date_end!!' />
				</div>
			</div>
			".(isset($pmb_scan_request_location_activate) && $pmb_scan_request_location_activate ?
			"<div class='row'>
				<div class='row'>
					<label for='scan_request_num_location'>".$msg['scan_request_location_search']."</label>
				</div>
				<div class='row'>
					!!scan_request_location_selector!!
				</div>
			</div>"
			: "")
			."
		</div>
		<div class='row'></div>				
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='scan_request_order_by' id='scan_request_order_by' value='!!scan_request_order_by!!'/>
			<input type='hidden' name='scan_request_order_by_sens' id='scan_request_order_by_sens' value='!!scan_request_order_by_sens!!'/>
			<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
			<input class='bouton' type='button' value='".$msg["scan_request_add"]."' onClick=\"document.location='./circ.php?categ=scan_request&sub=request&action=edit'\" />
		</div>
	</div>
	<div class='row'></div>	
</form>
					
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
!!scan_requests_list!!
";
