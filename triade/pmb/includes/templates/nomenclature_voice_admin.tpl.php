<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_voice_admin.tpl.php,v 1.8 2019-05-27 12:26:22 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_voice_list_tpl, $msg, $charset, $nomenclature_voice_list_line_tpl, $nomenclature_voice_form_tpl, $current_module;

$nomenclature_voice_list_tpl="	
<script type='text/javascript' src='./javascript/sorttable.js'></script>			
<table class='sortable'>
	<tr>		
		<th>	
		</th> 	
		<th>	".htmlentities($msg["admin_nomenclature_voice_code"], ENT_QUOTES, $charset)."			
		</th> 		
		<th>	".htmlentities($msg["admin_nomenclature_voice_name"], ENT_QUOTES, $charset)."			
		</th> 					 						 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_voice_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=voice&sub=voice&action=form'\" />	
";

$nomenclature_voice_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=voice&sub=voice&action=up&id=!!id!!'\" value='-'>
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=voice&sub=voice&action=down&id=!!id!!'\" value='+'>
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=voice&sub=voice&action=form&id=!!id!!';\" >				
		!!code!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=voice&sub=voice&action=form&id=!!id!!';\" >				
		!!name!!
	</td> 
</tr> 	
";


$nomenclature_voice_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_nomenclature_voice_form_name_error"]."');
			return false;
		}
		return true;
	}
	
</script>	
<form class='form-".$current_module."' id='nomenclature_voice_form' name='nomenclature_voice_form'  method='post' action=\"admin.php?categ=voice&sub=voice\" >
	<h3>!!msg_title!!</h3>	
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='code'>".$msg['admin_nomenclature_voice_form_code']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='code' id='code' value='!!code!!' />
		</div>		
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_voice_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>		
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_voice_form_exit']."'  onclick=\"document.location='./admin.php?categ=voice&sub=voice'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_voice_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
<script type='text/javascript'>
<!--
	document.forms['nomenclature_voice_form'].elements['code'].focus();
-->
</script>	
";
