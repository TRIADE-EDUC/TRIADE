<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_instrument_admin.tpl.php,v 1.10 2019-05-27 15:09:40 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_instrument_list_tpl, $msg, $charset, $nomenclature_instrument_list_line_tpl, $nomenclature_instrument_form_tpl, $current_module;

$nomenclature_instrument_list_tpl="	
<script type='text/javascript' src='./javascript/sorttable.js'></script>	
<table class='sortable'>
	<tr>			
		<th>	".htmlentities($msg["admin_nomenclature_instrument_code"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_instrument_name"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_instrument_musicstand"], ENT_QUOTES, $charset)."	
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_instrument_musicstand_family"], ENT_QUOTES, $charset)."	
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_instrument_standard"], ENT_QUOTES, $charset)."	
		</th> 				 						 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_instrument_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form'\" />	
";
/*
 * Exemple de test de la complétion Ajax des instruments
 	// $param1 : id du pupitre préféré. si 0 on retourne tous les instruments
	// $param2 = 0: Instruments du pupitre préféré seulement
	// $param2 = 1: Instruments du pupitre préféré en premier, puis les autres
	 
<input type='text' completion='instruments' callback='mis_en_forme_instrument' param1='3' param2='1' class='saisie-50em' name='code' id='code' value='' />
	
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
	
	function mis_en_forme_instrument(id){
		var str=document.getElementById(id).value;
		var res = str.split(' - ');
		if(res[0]) document.getElementById(id).value=res[0];
	}
</script>
 */

$nomenclature_instrument_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=!!id!!';\" >				
		!!code!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=!!id!!';\" >				
		!!name!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=!!id!!';\" >				
		!!musicstand!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=!!id!!';\" >				
		!!family!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=!!id!!';\" >				
		!!standard!!
	</td>
</tr> 	
";


$nomenclature_instrument_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if(form.code.value.length == 0){
			alert('".addslashes($msg["admin_nomenclature_instrument_form_code_error"])."');
			return false;
		}
		if(form.name.value.length == 0){
			alert('".addslashes($msg["admin_nomenclature_instrument_form_name_error"])."');
			return false;
		}
		return true;
	}
	
</script>
<form class='form-".$current_module."' id='nomenclature_instrument_form' name='nomenclature_instrument_form'  method='post' action=\"admin.php?categ=instrument&sub=instrument\" >
	<h3>!!msg_title!!</h3>		
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='code'>".$msg['admin_nomenclature_instrument_form_code']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='code' id='code' value='!!code!!' />
		</div>				
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_instrument_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>					
		<div class='row'>
			<label class='etiquette' for='musicstand'>".$msg['admin_nomenclature_instrument_form_musicstand']."</label>
		</div>
		<div class='row'>
			!!musicstand!!
		</div>				
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_instrument_form_standard']."</label>
		</div>
		<div class='row'>
			<input type='checkbox' name='standard' id='standard' value='1' !!checked!!/>
		</div>		
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_instrument_form_exit']."'  onclick=\"document.location='./admin.php?categ=instrument&sub=instrument'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_instrument_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
<script type='text/javascript'>
<!--
	document.forms['nomenclature_instrument_form'].elements['code'].focus();
-->
</script>	
";


