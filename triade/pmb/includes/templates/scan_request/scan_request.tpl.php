<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request.tpl.php,v 1.32 2024/02/14 08:55:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_request_location_form, $pmb_scan_request_location_activate, $current_module, $msg, $scan_request_form, $scan_request_ajax_form;
global $scan_request_associated_bulls_sub_template, $scan_request_concept_part, $pmb_scan_request_empr_mandatory;

$scan_request_location_form='';
if(isset($pmb_scan_request_location_activate) && $pmb_scan_request_location_activate){	
	$scan_request_location_form = '
		<div class="row">
			<div class="row">		
				<label for="scan_request_num_location">'.$msg["scan_request_location"].'</label>
			</div>
			<div class="row">
				!!scan_request_location_selector!!				
			</div>
		</div>';
}

$scan_request_form ='
<script type="text/javascript" src="javascript/ajax.js"></script>
<link type="text/css" rel="stylesheet" href="./javascript/dojo/snet/fileUploader/resources/uploader.css">
<form method="post" class="form-'.$current_module.'" name="scan_request_form" action="!!action!!&action=save">
	<h3>!!form_title!!</h3>
	<div class="form-contenu">
	
		<!-- Titre & description de la demande -->
		<div class="row">
			<div class="row">		
				<label for="scan_request_title">'.$msg["scan_request_form_title"].'</label>
			</div>
			<div class="row">
				<input type="text" id="scan_request_title" name="scan_request_title" value="!!title!!"/>			
			</div>
		</div>
		<div class="row">
			<div class="row">		
				<label for="scan_request_desc">'.$msg["scan_request_form_desc"].'</label>
			</div>
			<div class="row">
				<textarea id="scan_request_desc" name="scan_request_desc" rows="3" wrap="virtual">!!scan_request_desc!!</textarea>				
			</div>
		</div>

		<div class="row">
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_elapsed_time">'.$msg["scan_request_form_elapsed_time"].'</label>
				</div>
				<div class="row">
					<input type="text" id="scan_request_elapsed_time" name="scan_request_elapsed_time" value="!!scan_request_elapsed_time!!"/>			
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_nb_scanned_pages">'.$msg["scan_request_form_nb_scanned_pages"].'</label>
				</div>
				<div class="row">
					<input type="text" id="scan_request_nb_scanned_pages" name="scan_request_nb_scanned_pages" value="!!scan_request_nb_scanned_pages!!"/>			
				</div>
			</div>
		</div>						
		
		!!scan_request_concept_part!!
							
		<! -- Demande regroupée dans un dossier -->
		<div class="row">
			<input type="checkbox" id="scan_request_as_folder" name="scan_request_as_folder" !!scan_request_as_folder!!  !!scan_request_as_folder_disabled!!/>
			<label for="scan_request_as_folder">'.$msg["scan_request_form_as_folder"].'</label>			
		</div>
		<div class="row">&nbsp;</div>								
		<!-- Statut, destinataire & priorite de la demande -->
		<div class="row">
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_lib_dest_empr">'.$msg["scan_request_form_num_dest_empr"].'</label>
				</div>
				<div class="row">
					<input id="scan_request_lib_dest_empr" value="!!scan_request_lib_empr!!" class="saisie-30emr" autocomplete="off" completion="empr" autfield="scan_request_num_dest_empr" type="text" name="scan_request_lib_dest_empr"/>
					<input class="bouton" type="button" onclick="openPopUp(\'./select.php?what=emprunteur&caller=scan_request_form&param1=scan_request_num_dest_empr&param2=scan_request_lib_dest_empr&auto_submit=NO\', \'selector\')" value="...">
					<input class="bouton" type="button" onclick=\'this.form.scan_request_lib_dest_empr.value=""; this.form.scan_request_num_dest_empr.value="0"; \' value="X">
					<input type="hidden" value="!!scan_request_num_dest_empr!!" id="scan_request_num_dest_empr" name="scan_request_num_dest_empr">
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_status">'.$msg["scan_request_form_status"].'</label>
				</div>
				<div class="row">
					<select id="scan_request_status" name="scan_request_status">
						!!scan_request_status!!
					</select>	
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_priority">'.$msg["scan_request_form_priority"].'</label>
				</div>
				<div class="row">
					<select id="scan_request_priority" name="scan_request_priority">
						!!scan_request_priority!!
					</select>	
				</div>		
			</div>
		</div>
		
		<!--- Différentes dates associées à la demande -->
		<div class="row">
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_date">'.$msg["scan_request_form_date"].'</label>
				</div>
				<div class="row">
					<input type="date" name="scan_request_date" id="scan_request_date" value="!!scan_request_date!!" required />
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_wish_date">'.$msg["scan_request_form_wish_date"].'</label>
				</div>
				<div class="row">
					<input type="date" name="scan_request_wish_date" id="scan_request_wish_date" value="!!scan_request_wish_date!!" required />
				</div>
			</div>
			<div class="colonne3">
				<div class="row">		
					<label for="scan_request_deadline_date">'.$msg["scan_request_form_deadline_date"].'</label>
				</div>
				<div class="row">
					<input type="date" name="scan_request_deadline_date" id="scan_request_deadline_date" value="!!scan_request_deadline_date!!" required />
				</div>
			</div>
		</div>
		<input type="hidden" value="!!scan_request_status_editable!!" id="records_editable"/>
		<!-- Notices associées -->						
		<div id="associated_record_label_container" class="row">
			<label>'.$msg["scan_request_form_associated_records"].'</label>
		</div>
		<div id="record_container">
			<div data-dojo-props=\'mode:"record", elementsToLoad:!!associated_records!!\' data-dojo-type="apps/scan_request/ElementsContainer"></div>
		</div>
					
		<!-- Bulletins associés -->
		<div id="associated_bulletin_label_container" class="row">
			<label>'.$msg["scan_request_form_associated_buls"].'</label>
		</div>
		<div id="bul_container">
			<div data-dojo-props=\'mode:"bulletin", elementsToLoad:!!associated_buls!!\' data-dojo-type="apps/scan_request/ElementsContainer"></div>
		</div>		
		'.$scan_request_location_form.'							
		<div class="row">
			<div class="row">		
				<label for="scan_request_comment">'.$msg["scan_request_form_comment"].'</label>
			</div>
			<div class="row">
				<textarea id="scan_request_comment" name="scan_request_comment" rows="3" wrap="virtual">!!scan_request_comment!!</textarea>				
			</div>
		</div>
		<div class="row" id="expl_container">
				<div data-dojo-props=\'elementsData:!!all_explnum_datas!!\' data-dojo-type="apps/scan_request/CompleteExplnumList"></div>
		</div>
	</div>
	<div class="row">
		<div class="left">
			<input type="hidden" name="id" value="!!id!!"/>
			<input class="bouton" type="button" value=" '.$msg['76'].' " onClick=\'document.location="!!cancel_action!!"\'>&nbsp;
			<input class="bouton" id="scan_request_submit_button" type="submit" value=" '.$msg['77'].' " onClick=\'return test_form(this.form)\'>
		</div>
		<div class="right">
			!!bouton_supprimer!!
		</div>
	</div>
	<div class="row">&nbsp;</div>
</form>
<script type="text/javascript">
	function test_form(form) {
   		var parentDiv = document.getElementById("record_container");
		var flag = false;
		for (var i = 0; i < parentDiv.children.length; i++) {
			if (parseInt(document.getElementById("scan_request_record_code_"+i).value) > 0) {
                flag = true;
            }
		}

   		var parentDiv2 = document.getElementById("bul_container");
		for (var i = 0; i < parentDiv2.children.length; i++) {
			if (parseInt(document.getElementById("scan_request_bul_code_"+i).value) > 0) {
                flag = true;
            }
		}

		if (flag == false) {
			alert("'.$msg["scan_request_form_empty_linked_elements_error"].'");
			return false;
		}

		if (form.scan_request_title.value.length == 0) {
			alert("'.$msg[98].'");
			return false;
		}

		if (document.getElementsByName("scan_request_wish_date")[0].value.split("-").join("") < document.getElementsByName("scan_request_date")[0].value.split("-").join("")) {
			alert("'.$msg['scan_request_wish_date_error'].'");
			return false;
		}

		if (document.getElementsByName("scan_request_deadline_date")[0].value.split("-").join("") < document.getElementsByName("scan_request_wish_date")[0].value.split("-").join("")) {
			alert("'.$msg['scan_request_deadline_date_error'].'");
			return false;
		}
        if ('.intval($pmb_scan_request_empr_mandatory).' && !parseInt(document.getElementById("scan_request_num_dest_empr").value)) {
            alert("'.$msg['scan_request_empr_mandatory_error'].'");
            return false;
        }

		return true;
	}
	
	if((typeof ajax_parse_dom == "function")) ajax_parse_dom();
</script>
';



$scan_request_ajax_form ='
<link type="text/css" rel="stylesheet" href="./javascript/dojo/snet/fileUploader/resources/uploader.css">
<script type="text/javascript" src="javascript/ajax.js"></script>
<input type="text" id="record_title_for_copy" value="" style="display:none; position : absolute; top : -500px;"/>
<form method="post" class="form-'.$current_module.'" name="scan_request_form" action="!!action!!&action=save">
	<div class="form-contenu">

		<!-- Statut, destinataire & priorite de la demande -->
		
		<div class="row">
			<div class="colonne3">
				<div class="row">
					<div class="row">
						<label for="scan_request_elapsed_time">'.$msg["scan_request_form_elapsed_time"].'</label>
					</div>
					<div class="row">
						<input type="text" id="scan_request_elapsed_time" name="scan_request_elapsed_time" value="!!scan_request_elapsed_time!!"/>
					</div>
				</div>
			</div>
			<div class="colonne3">
				<div class="row">
					<div class="row">
						<label for="scan_request_nb_scanned_pages">'.$msg["scan_request_form_nb_scanned_pages"].'</label>
					</div>
					<div class="row">
						<input type="text" id="scan_request_nb_scanned_pages" name="scan_request_nb_scanned_pages" value="!!scan_request_nb_scanned_pages!!"/>
					</div>
				</div>
			</div>
			<div class="colonne_suite">
				<div class="row">
					<label for="scan_request_status">'.$msg["scan_request_form_status"].'</label>
				</div>
				<div class="row">
					<select id="scan_request_status" name="scan_request_status">
						!!scan_request_status!!
					</select>
				</div>
			</div>
		</div>
		<input type="hidden" name="scan_request_concept_uri_value" id="scan_request_concept_uri_value" value="!!scan_request_concept_uri_value!!"/>							
		!!scan_request_associated_records_sub_template!!
		!!scan_request_associated_bulls_sub_template!!
	</div>

	<div class="row">
		<div class="row">
			<label for="scan_request_comment">'.$msg["scan_request_form_comment"].'</label>
		</div>
		<div class="row">
			<textarea id="scan_request_comment" name="scan_request_comment" rows="3" wrap="virtual">!!scan_request_comment!!</textarea>
		</div>
	</div>

	<div class="row">
		<div class="left">
			<input type="hidden" id="scan_request_id" name="id" value="!!id!!"/>
			<input class="bouton" type="button" value=" '.$msg['76'].' " onClick=\'dijit.byId("scan_request_layer").hide();\'>&nbsp;
			<input class="bouton" type="button" value=" '.$msg['77'].' " onClick=\'if (test_form(this.form)) {scan_request_save_ajax();}\'>
		</div>
		<div class="right">
		</div>
	</div>
	<div class="row">&nbsp;</div>
</form>
<script type="text/javascript">
	if((typeof ajax_parse_dom == "function")) ajax_parse_dom();
</script>';

$scan_request_associated_bulls_sub_template = 
'<!-- Bulletins associés -->
<div class="row">
	<label>'.$msg["scan_request_form_associated_buls"].'</label>
	<a href="javascript:expandAll(document.getElementById(\'bul_container\'))"><img src="'.get_url_icon('expand_all.gif').'" id="expandall" style="border:0px"></a>
	<a href="javascript:collapseAll(document.getElementById(\'bul_container\'))"><img src="'.get_url_icon('collapse_all.gif').'" id="collapseall" style="border:0px"></a>
</div>
<div id="bul_container">
	<div data-dojo-props=\'mode:"bulletin", elementsToLoad:!!associated_buls!!, readOnly:"1"\' data-dojo-type="apps/scan_request/ElementsContainer"></div>
</div>';

$scan_request_associated_records_sub_template = 
'<!-- Notices associées -->
<div class="row">
	<label>'.$msg["scan_request_form_associated_records"].'</label>
	<a href="javascript:expandAll(document.getElementById(\'record_container\'))"><img src="'.get_url_icon('expand_all.gif').'" id="expandall" style="border:0px"></a>
	<a href="javascript:collapseAll(document.getElementById(\'record_container\'))"><img src="'.get_url_icon('collapse_all.gif').'" id="collapseall" style="border:0px"></a>
</div>
<div id="record_container">
	<div data-dojo-props=\'mode:"record", elementsToLoad:!!associated_records!!, readOnly:"1"\' data-dojo-type="apps/scan_request/ElementsContainer"></div>
</div>';

$scan_request_concept_part = '
		<div class="row">
			<label for="scan_request_concept_uri">'.$msg["scan_request_form_concept_uri"].'</label>	
		</div>			
		<div class="row">
			<input id="scan_request_concept_uri" class="saisie-30emr" type="text" autocomplete="off" autfield="scan_request_concept_uri_value" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept" completion="onto" value="!!scan_request_concept_label!!" name="scan_request_concept_uri">
			<input class="bouton" type="button" onclick="openPopUp(\'select.php?what=ontology&caller=scan_request_form&param1=scan_request_concept_uri_value&param2=scan_request_concept_uri&element=concept&grammar=music_explnum\', \'selector_ontology\')" value="...">
			<input id="scan_request_concept_uri_purge" class="bouton" type="button" onclick="document.getElementById(\'scan_request_concept_uri\').value = \'\'; document.getElementById(\'scan_request_concept_uri_value\').value = \'\';" value="X">
			<input id="scan_request_concept_uri_value" type="hidden" value="!!scan_request_concept_uri_value!!" name="scan_request_concept_uri_value">
			<input id="scan_request_concept_uri_type" type="hidden" value="http://www.w3.org/2004/02/skos/core#Concept" name="scan_request_concept_uri_type">			
		</div>';
