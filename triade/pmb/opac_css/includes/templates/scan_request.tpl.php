<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.tpl.php,v 1.13 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_request_form;
global $scan_request_form_content;
global $scan_request_linked_record;
global $scan_request_link_in_record;
global $scan_request_form_in_record;
global $scan_request_form_in_record_scripts;
global $opac_scan_request_location_activate;
global $msg, $base_path;

$scan_request_form ='
<form method="post" name="scan_request_form" action="./empr.php?tab=scan_requests&lvl=scan_request&sub=save&id=!!id!!">
	<h3>!!form_title!!</h3>
	<div class="form-contenu">
		!!form_content!!
	</div>
	<div class="row">&nbsp;</div>
	<div class="row">
		<div class="left">
			<input class="bouton" type="button" value=" '.$msg['76'].' " onClick="history.go(-1);">
			<input class="bouton" type="submit" value=" '.$msg['77'].' " onClick="return scan_request_test_form(this.form);">
		</div>
		<div class="right-clear-right">
		</div>
	</div>
	<div class="row">&nbsp;</div>
</form>
<script type="text/javascript">
	function scan_request_test_form(form){
		if(form.scan_request_title.value.trim().length == 0){
			alert("'.addslashes($msg['scan_request_test_form_title_mandatory']).'");
			return false;
		}
		if(form.scan_request_date.value > form.scan_request_wish_date[1].value){
			alert("'.addslashes($msg['scan_request_test_form_wish_greater_than_date']).'");
			return false;
		}
		if(form.scan_request_wish_date[1].value > form.scan_request_deadline_date[1].value){
			alert("'.addslashes($msg['scan_request_test_form_deadline_greater_than_wish']).'");
			return false;
		}
		return true;
	}
</script>';

$scan_request_form_location_content = '';
if($opac_scan_request_location_activate) {
	$scan_request_form_location_content .= '
	<div class="row">
		<div class="row">
			<label for="scan_request_location!!id_suffix!!">'.$msg["scan_request_location"].'</label>
		</div>
		<div class="row">
			!!location_selector!!
		</div>
	</div>';
}

$scan_request_form_content = '
		<div class="row">
			<div class="row">
				<label for="scan_request_title!!id_suffix!!">'.$msg["scan_request_title"].'</label>
			</div>
			<div class="row">
				<input type="text" id="scan_request_title!!id_suffix!!" name="scan_request_title" value="!!title!!"/>			
			</div>
		</div>
		<div class="row">
			<div class="row">		
				<label for="scan_request_desc!!id_suffix!!">'.$msg["scan_request_desc"].'</label>
			</div>
			<div class="row">
				<textarea id="scan_request_desc!!id_suffix!!" name="scan_request_desc" rows="4" cols="55" wrap="virtual">!!desc!!</textarea>				
			</div>
		</div>
		'.$scan_request_form_location_content.'
		<div class="row">&nbsp;</div>
		<hr />
		<div class="row">&nbsp;</div>
		<div class="row">
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_priority!!id_suffix!!">'.$msg["scan_request_priority"].'</label>
				</div>
				<div class="row">
					<select id="scan_request_priority!!id_suffix!!" name="scan_request_priority">
						!!priority!!
					</select>	
				</div>		
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_wish_date!!id_suffix!!">'.$msg["scan_request_wish_date"].'</label>
				</div>
				<div class="row">
					<input type="text" name="scan_request_wish_date" id="scan_request_wish_date!!id_suffix!!" value="!!wish_date!!"  data-dojo-type="dijit/form/DateTextBox" required="true" style="width: 10em;"/>
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_deadline_date!!id_suffix!!">'.$msg["scan_request_deadline_date"].'</label>
				</div>
				<div class="row">
					<input type="text" name="scan_request_deadline_date" id="scan_request_deadline_date!!id_suffix!!" value="!!deadline_date!!"  data-dojo-type="dijit/form/DateTextBox" required="true"  style="width: 10em;"/>
				</div>
			</div>
		</div>
		<div class="row">&nbsp;</div>
		<hr />
		<div class="row">&nbsp;</div>
		<div class="row">
				<label>'.$msg["scan_request_linked_records"].'</label>
		</div>
		!!linked_records!!
		<div class="row">&nbsp;</div>
		<input type="hidden" name="scan_request_date" id="scan_request_date!!id_suffix!!" value="!!date!!"/>
		<input type="hidden" name="scan_request_status" id="scan_request_status!!id_suffix!!" value="!!status!!"/>';

$scan_request_linked_record = '
	<div class="row">
		<div id="scan_request_!!linked_record_type!!_!!linked_record_id!!_parent" class="scan-request-linked-record-parent">
			<img !!expand_invisible!! class="img_plus" src=\'./getgif.php?nomgif=plus\' name=\'imEx\' id=\'scan_request_!!linked_record_type!!_!!linked_record_id!!_img\' title=\''.$msg['expandable_notice'].'\' border=\'0\' onClick=\'expand_scan_request_records(!!linked_record_id!!, "!!linked_record_type!!"); return false;\' hspace=\'3\' />
			!!linked_record_display!!
		</div>
		<div id=\'scan_request_!!linked_record_type!!_!!linked_record_id!!_child\' class=\'scan-request-linked-record-child\' style=\'margin-bottom:6px;display:none;\'>
			<div class="row">
				<label>'.$msg["scan_request_linked_record_comment"].'</label>
			</div>
			<div class="row">
				<textarea id="scan_request_linked_records_!!linked_record_type!!_!!linked_record_id!!_comment!!id_suffix!!" name="scan_request_linked_records_!!linked_record_type!![!!linked_record_id!!][comment]" rows="4" cols="55" wrap="virtual">!!linked_record_comment!!</textarea>
				<input type="hidden" value="scan_request_linked_records_!!linked_record_type!!_!!linked_record_id!!" name="scan_request_linked_records!!id_suffix!![]"/>
			</div>
		</div>
	</div>';

$scan_request_link_in_record = "
		<script type='text/javascript' src='".$base_path."/includes/javascript/select.js'></script>
		<span id='scan_request!!id_suffix!!'>
			<a href='#' onClick='show_layer(); show_scan_request(\"!!record_id!!\",\"!!record_type!!\");return false;'>
				".$msg['do_scan_request_on_document']."
			</a>
			!!scan_requests_already_exist!!
		</span>";

$scan_request_form_in_record = "
		<!--bouton close-->		
		<h3>!!form_title!!</h3>
		<div id='scan_request_form!!id_suffix!!'>
			!!form_content!!
			<div class='row'>
				<div class='left'>
					<input class='bouton' type='button' value='".$msg['76']."' onClick='parent.kill_scan_request_frame();return false;'>
					<input class='bouton' type='button' value='".$msg['77']."' onClick='if (scan_request_test_form(\"!!id_suffix!!\")) {create_scan_request_in_record(\"!!id_suffix!!\", \"!!record_type!!\", !!record_id!!); kill_scan_request_frame();}'>
				</div>
			</div>
			<div class='row'>&nbsp;</div>
		</div>";
		
$scan_request_form_in_record_scripts = '
<script type="text/javascript">
	function scan_request_test_form(id_suffix){
		var title = document.getElementById("scan_request_title" + id_suffix).value;
		var date = document.getElementById("scan_request_date" + id_suffix).value;
		var wish_date = document.getElementById("scan_request_wish_date" + id_suffix).nextElementSibling.value;
		var deadline_date = document.getElementById("scan_request_deadline_date" + id_suffix).nextElementSibling.value;
				
		if(title.trim().length == 0){
			alert("'.addslashes($msg['scan_request_test_form_title_mandatory']).'");
			return false;
		}
		if(date > wish_date){
			alert("'.addslashes($msg['scan_request_test_form_wish_greater_than_date']).'");
			return false;
		}
		if(wish_date > deadline_date){
			alert("'.addslashes($msg['scan_request_test_form_deadline_greater_than_wish']).'");
			return false;
		}
		return true;
	}
</script>';