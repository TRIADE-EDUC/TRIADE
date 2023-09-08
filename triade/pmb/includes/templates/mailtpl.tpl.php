<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailtpl.tpl.php,v 1.18 2019-05-27 13:06:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $mailtpl_list_tpl, $msg, $charset, $mailtpl_list_line_tpl, $mailtpl_form_resavars, $mailtpl_form_selvars, $mailtpl_form_sel_img, $mailtpl_form_tpl, $pmb_javascript_office_editor;
global $current_module, $pdflettreresa_resa_prolong_email;

$mailtpl_list_tpl="	
<h1>".htmlentities($msg["admin_mailtpl_title"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_mailtpl_name"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_empr_button' value='".htmlentities($msg["admin_mailtpl_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=mailtpl&sub=build&action=form'\" />	
";

$mailtpl_list_line_tpl="
<tr  class='!!odd_even!!' onmousedown=\"document.location='./admin.php?categ=mailtpl&sub=build&action=form&id_mailtpl=!!id!!';\"  style=\"cursor: pointer\" 
onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		!!name!!
	</td> 	
	
</tr> 	
";

$mailtpl_form_resavars = "
	<select name='resavars_id' id='resavars_id'>
		<option value=!!new_date!!>".$msg["scan_request_date"]."</option>
		<option value=!!expl_title!!>".$msg["233"]."</option>
		<option value=!!record_permalink!!>".$msg["cms_editorial_form_permalink"]."</option>
	</select>
	<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars(document.getElementById('resavars_id'), document.getElementById('f_message')); return false; \" />
		";

$mailtpl_form_selvars="
<select name='selvars_id' id='selvars_id'>
	<optgroup label='".htmlentities($msg["selvars_empr_group_empr"],ENT_QUOTES, $charset)."'>
		<option value=!!empr_name!!>".htmlentities($msg["selvars_empr_name"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_first_name!!>".htmlentities($msg["selvars_empr_first_name"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_sexe!!>".htmlentities($msg["selvars_empr_civilite"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_cb!!>".htmlentities($msg["selvars_empr_cb"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_login!!>".htmlentities($msg["selvars_empr_login"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_mail!!>".htmlentities($msg["selvars_empr_mail"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loans!!>".htmlentities($msg["selvars_empr_loans"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loans_late!!>".htmlentities($msg["selvars_empr_loans_late"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_resas!!>".htmlentities($msg["selvars_empr_resas"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_name_and_adress!!>".htmlentities($msg["selvars_empr_name_and_adress"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_dated!!>".htmlentities($msg["selvars_empr_dated"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_datef!!>".htmlentities($msg["selvars_empr_datef"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_nb_days_before_expiration!!>".htmlentities($msg["selvars_empr_nb_days_before_expiration"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_all_information!!>".htmlentities($msg["selvars_empr_all_information"],ENT_QUOTES, $charset)."</option>
		<option value='".htmlentities("<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>",ENT_QUOTES, $charset)."'>".$msg["selvars_empr_auth_opac"]."</option>
		<option value='".htmlentities("<a href='".$opac_url_base."empr.php?lvl=renewal&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_subscribe_link"]."</a>",ENT_QUOTES, $charset)."'>".$msg["selvars_empr_auth_opac_subscribe_link"]."</option>
	</optgroup>
	<optgroup label='".htmlentities($msg["selvars_empr_group_loc"],ENT_QUOTES, $charset)."'>
		<option value=!!empr_loc_name!!>".htmlentities($msg["selvars_empr_loc_name"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_adr1!!>".htmlentities($msg["selvars_empr_loc_adr1"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_adr2!!>".htmlentities($msg["selvars_empr_loc_adr2"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_cp!!>".htmlentities($msg["selvars_empr_loc_cp"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_town!!>".htmlentities($msg["selvars_empr_loc_town"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_phone!!>".htmlentities($msg["selvars_empr_loc_phone"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_email!!>".htmlentities($msg["selvars_empr_loc_email"],ENT_QUOTES, $charset)."</option>
		<option value=!!empr_loc_website!!>".htmlentities($msg["selvars_empr_loc_website"],ENT_QUOTES, $charset)."</option>
	</optgroup>
	<optgroup label='".htmlentities($msg["selvars_empr_group_misc"],ENT_QUOTES, $charset)."'>
		<option value=!!day_date!!>".htmlentities($msg["selvars_empr_day_date"],ENT_QUOTES, $charset)."</option>
	</optgroup>
</select>
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars(document.getElementById('selvars_id'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>

	function insert_vars(theselector,dest){	
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;

		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE_execCommand('mceInsertContent',false,selvars);
		}
	}
	
	
</script>
";

$mailtpl_form_sel_img="
!!select_file!!
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_sel_img_insert"]." \" onClick=\"insert_img(document.getElementById('select_file'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>
	function insert_img(theselector,dest){	
		var href='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				href=theselector.options[i].value ;
				break;
			}
		}
		if(!href) return ;
		
		var sel_img='<img src=\"'+href+'\">';
		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+sel_img+end_text;
		}else{
			tinyMCE_execCommand('InsertHTML',false,sel_img);
		}
	}

</script>
";
$mailtpl_form_tpl="	
	$pmb_javascript_office_editor
	<script type='text/javascript' src='./javascript/tinyMCE_interface.js'></script>
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_mailtpl_name_error"]."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='mailtpl' name='mailtpl'  method='post' action=\"admin.php?categ=mailtpl&sub=build\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id_mailtpl' id='id_mailtpl' value='!!id_mailtpl!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_mailtpl_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='f_objet_mail'>$msg[empr_mailing_form_obj_mail]</label>
			<div class='row'>
				<input type='text' class='saisie-80em' id='f_objet_mail'  name='f_objet_mail' value='!!objet!!' />
			</div>
		</div>
		<div class='row'>
			<label class='etiquette' for='f_message'>".$msg["admin_mailtpl_form_tpl"]."</label>
			<div class='row'>
				<textarea id='f_message' name='f_message' cols='100' rows='20'>!!tpl!!</textarea>
			</div>
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg["admin_mailtpl_form_selvars"]."</label>
			<div class='row'>
				!!selvars!!
			</div>
		</div>";
		if($pdflettreresa_resa_prolong_email){
			$mailtpl_form_tpl.="
			<div class='row'>
				<label class='etiquette'>".$msg["admin_mailtpl_form_resa_prolong_selvars"]."</label>
				<div class='row'>
					!!resavars!!
				</div>
			</div>";
		}
		$mailtpl_form_tpl.="!!sel_img!!
		<div class='row'>
			<input type='hidden' id='auto_id_list' name='auto_id_list' value='!!id_check_list!!' >
			<label class='etiquette' for='form_comment'>$msg[procs_autorisations]</label>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
		</div>
		<div class='row'>
			!!autorisations_users!!
		</div>
		<div class='row'> 
		</div>
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['76']."'  onclick=\"document.location='./admin.php?categ=mailtpl&sub=build'\"  />
			<input type='button' class='bouton' value='".$msg['admin_mailtpl_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
";
