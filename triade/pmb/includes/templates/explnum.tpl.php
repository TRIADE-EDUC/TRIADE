<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.tpl.php,v 1.56.6.2.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pmb_docnum_in_directory_allow, $explnum_form, $msg, $charset, $current_module, $pmb_docnum_in_database_allow, $explnum_drop_zone, $explnum_popup_edition_script;

// on teste si des répertoires de stockages sont paramétrés
$result_upload_repertoire = pmb_mysql_query("select repertoire_id from upload_repertoire ");
if(pmb_mysql_num_rows($result_upload_repertoire)){
	$pmb_docnum_in_directory_allow = 1;
	pmb_mysql_free_result($result_upload_repertoire);
} else {
	$pmb_docnum_in_directory_allow = 0;
}

// les deux paramètres pour savoir si on peut stocker de la GED sont donc : 
// $pmb_docnum_in_directory_allow
// $pmb_docnum_in_database_allow

// $expl_form :form de saisie/modif exemplaire numérique
$explnum_form ="
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit('catalog', 'explnum');
	     });
	});
</script>
<script type='text/javascript'>
<!--
	function test_form(form) {
		if((form.f_nom.value.length == 0) && (form.f_fichier.value.length == 0) && (form.f_url.value.length == 0)) {
			alert(\"".htmlentities($msg['explnum_error_creation'], ENT_QUOTES, $charset)."\");
			return false;
		}
		if((form.f_fichier.value.length != 0) && (form.f_url.value.length == 0) && (document.getElementById('upload').checked==true) && (document.getElementById('id_rep').value==0)) {
			alert(\"".htmlentities($msg['explnum_error_rep_upload'], ENT_QUOTES, $charset)."\");
			return false;
		}
		
        var ck_sign = document.getElementById('ck_sign');
        if(ck_sign && ck_sign.checked){
            if(!confirm(\"".$msg['explnum_digital_signature_confirm']."\")){
                return false;
            }
        }
		return check_form();
	}
	
-->

//Test si le fichier est déjà uploadé au meme endroit
function ecraser_fichier(filename){

	var res = confirm(\"".htmlentities($msg['docnum_ecrase_file'], ENT_QUOTES, $charset)." \"+filename+\".\\n".htmlentities($msg['agree_question'], ENT_QUOTES, $charset)."\");
	if(res) {
		document.getElementById('f_new_name').value = filename;
		return true;
	}
	document.getElementById('f_new_name').value = '';
	return false;	
	
}";

$explnum_form .= "
	const tabTokens_explnum = !!tokens_csrf!!;
	function chklnk_f_url(element){
		if(element.value != ''){
			var wait = document.createElement('img');
			wait.setAttribute('src','".get_url_icon('patience.gif')."');
			wait.setAttribute('align','top');
			while(document.getElementById('f_url_check').firstChild){
				document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
			}
			document.getElementById('f_url_check').appendChild(wait);
			var csrf_token = tabTokens_explnum[0];
			tabTokens_explnum.splice(0, 1);
			var testlink = encodeURIComponent(element.value);
			var check = new http_request();
			if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout=0&link='+testlink+'&csrf_token='+csrf_token)){
				alert(check.get_text());
			}else{
				var result = check.get_text();
				var type_status=result.substr(0,1);
				var img = document.createElement('img');
				var src='';
				if(type_status == '2' || type_status == '3'){
					if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
					//impec, on print un petit message de confirmation
					src = '".get_url_icon('tick.gif')."';
				}else{
					//problème...
					src = '".get_url_icon('error.png')."';
					img.setAttribute('style','height:1.5em;');
				}
				img.setAttribute('src',src);
				img.setAttribute('align','top');
				while(document.getElementById('f_url_check').firstChild){
					document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
				}
				document.getElementById('f_url_check').appendChild(img);
			}
		}
	}
	</script>
";

$explnum_form .= "
<script src=\"./javascript/http_request.js\" type='text/javascript'></script>
<script src=\"./javascript/ajax.js\" type='text/javascript'></script>
<script src=\"./javascript/select.js\" type='text/javascript'></script>
<script src=\"./javascript/upload.js\" type='text/javascript'></script>

<form id='explnum_form' class='form-$current_module' ENCTYPE='multipart/form-data' name='explnum' method='post' action='!!action!!' onsubmit='!!submit_action!!'>
<div class='left'>
	<h3>".htmlentities($msg['explnum_data_doc'], ENT_QUOTES, $charset)."</h3>
</div>
<div class='right'>";
global $PMBuserid, $pmb_form_explnum_editables;
if ($PMBuserid==1 && $pmb_form_explnum_editables==1){
    $explnum_form.="<input type='button' class='bouton_small' value='".htmlentities($msg["catal_edit_format"], ENT_QUOTES, $charset)."' id=\"bt_inedit\"/>";
}
if ($pmb_form_explnum_editables==1) {
    $explnum_form.="<input type='button' class='bouton_small' value=\"".htmlentities($msg["catal_origin_format"], ENT_QUOTES, $charset)."\" id=\"bt_origin_format\"/>";
}
$explnum_form.="</div>
<div class='form-contenu' >
	<div id='zone-container'>
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['explnum_nom'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_nom'>".htmlentities($msg['explnum_nom'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='text' id='f_nom' name='f_nom' class='saisie-80em'  value='!!nom!!' />
				<!-- explnum_statut -->
			</div>
		</div>
		<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg['explnum_vignette'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_vignette'>".htmlentities($msg['explnum_vignette'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='file' id='f_vignette' name='f_vignette' class='saisie-80em' size='65' />
			</div>
			<div class='row'>
				<label class='etiquette' for='f_url_vignette'>".htmlentities($msg['explnum_url_vignette'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='text' id='f_url_vignette' name='f_url_vignette' class='saisie-80em' />
				<div>!!mimetype_list!!</div>
			</div>
			<div class='row'>
				!!vignette_existante!!
			</div>
		</div>
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg['empr_location'], ENT_QUOTES, $charset)."\">
			!!location_explnum!!
		</div>
		<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg[651], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_vignette'>".htmlentities($msg[651], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				!!lenders!!
			</div>
			<div class='row'>&nbsp;</div>
		</div>
";

if ($pmb_docnum_in_directory_allow || $pmb_docnum_in_database_allow) {
	$explnum_form .="
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['explnum_fichier'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_fichier'>".htmlentities($msg['explnum_fichier'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<input type='file' id='f_fichier' name='f_fichier' class='saisie-80em' size='65' />
				<div><input type='checkbox' id='multi_ck' name='multi_ck' value='1'/><label for='multi_ck'>".htmlentities($msg['upload_repertoire_multifile'], ENT_QUOTES, $charset)."</label></div>
			</div>
			<div class='row'>
				<!-- !!scan_button!! -->
			</div>
			!!div_upload!!
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' >".htmlentities($msg['explnum_ou'], ENT_QUOTES, $charset)." :</label>
			</div>";

} else {

	$explnum_form .="
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['explnum_url'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<i>".htmlentities($msg['explnum_no_storage_allowed'], ENT_QUOTES, $charset)."</i>
			</div>
			<div class='row'>&nbsp;</div>";
}
$explnum_form.="
			<div class='row'>
				<label class='etiquette' for='f_url'>".htmlentities($msg['explnum_url'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				<div id='f_url_check' style='display:inline'></div>
				<input type='text' id='f_url' name='f_url' class='saisie-80em' onchange='chklnk_f_url(this);' value='!!url!!' !!disabled_url!! />
			</div>
			<div class='row'>&nbsp;</div>
		</div>
		<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg['docnum_statut_gestion'], ENT_QUOTES, $charset)."\">
			<div class='row'>
			    <label class='etiquette'>".htmlentities($msg['docnum_statut_gestion'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='row'>
				!!statut_list!!
			</div>
			<div class='row'>&nbsp;</div>
		</div>
		<!-- el0Child_5 utilisé par les licences -->
		!!explnum_licence_selectors!!			    		
		
		<!-- el0Child_6 utilisé par la signature electronique-->
		!!sign_docnum!!
		
		!!index_concept_form!!
		!!champs_perso!!
		!!ck_indexation!!
		!!ck_diarization!!
		!!fct_conf_diarize_again!!		
		!!rights_form!!
	</div>
</div>
<div class='row'>
	<input type='button' id='cancel_button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\" />
	<input type='submit' class='bouton' value='".$msg['77']."' onClick=\"return test_form(this.form);\" />
	!!associate_speakers!!
	!!link_audit!!
	<div class='right' >
		!!supprimer!!
	</div>
	<input type='hidden' name='f_explnum_id' value='!!explnum_id!!' />
	<input type='hidden' name='f_bulletin' value='!!bulletin!!' />
	<input type='hidden' name='f_notice' value='!!notice!!' />
	<input type='hidden' name='f_new_name' id='f_new_name' value='' />
</div>
</form>
<script type=\"text/javascript\">
	document.forms['explnum'].elements['f_nom'].focus();
	ajax_parse_dom();
</script>";


$explnum_drop_zone = "
               <div id='document_drop_zone_item_!!entity_id!!' class='row document_drop_zone_item'>
                  <div id='dropTarget_!!entity_id!!' class='dropTarget document_item'>
                        <p style='pointer-events: none;'>".htmlentities($msg['drag_files_here'],ENT_QUOTES,$charset)."</p>
                  </div>
                  <div id='document_list_!!entity_id!!'></div>
    			<link href='./javascript/dojo/snet/fileUploader/resources/uploader.css' rel='stylesheet' type='text/css'/>
    			<script type='text/javascript'>
    				require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader', 'apps/pmb/PMBDojoxDialogSimple', 'dojo/query', 'dojo/dom-construct', 'apps/pmb/ExplnumUpload', 'apps/pmb/ExplnumPopupEdit'], function(kernel, ready, Uploader, Dialog, query, domConstruct, ExplnumUpload, ExplnumPopupEdit) {
                        new ExplnumUpload(\"!!entity_id!!\", \"!!entity_type!!\", \"!!bul_id!!\");
    				});
    			</script>
              </div>";

$explnum_popup_edition_script = "<script type='text/javascript'>
                                    require(['dojo/_base/kernel', 'dojo/ready', 'snet/fileUploader/Uploader', 'apps/pmb/PMBDojoxDialogSimple', 'dojo/query', 'dojo/dom-construct', 'apps/pmb/ExplnumUpload', 'apps/pmb/ExplnumPopupEdit'], function(kernel, ready, Uploader, Dialog, query, domConstruct, ExplnumUpload, ExplnumPopupEdit) {
                                        new ExplnumPopupEdit();
                    				});
                                 </script>";
