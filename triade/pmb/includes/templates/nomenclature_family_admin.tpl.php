<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_family_admin.tpl.php,v 1.11 2019-05-27 13:34:31 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_family_list_tpl, $msg, $charset, $nomenclature_family_list_line_tpl, $nomenclature_family_form_tpl, $current_module, $nomenclature_family_musicstand_list_tpl;
global $nomenclature_family_musicstand_list_line_tpl, $nomenclature_family_musicstand_form_tpl;

$nomenclature_family_list_tpl="	
<script type='text/javascript' src='./javascript/sorttable.js'></script>			
<table class='sortable'>
	<tr>		
		<th>	
		</th> 	
		<th>	".htmlentities($msg["admin_nomenclature_family_name"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_family_pupitres"], ENT_QUOTES, $charset)."	
		</th> 
		<th>	
		</th> 				 						 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_family_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=family&sub=family&action=form'\" />	
";

$nomenclature_family_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=family&sub=family&action=up&id=!!id!!'\" value='-'>
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=family&sub=family&action=down&id=!!id!!'\" value='+'>
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=form&id=!!id!!';\" >				
		!!name!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=form&id=!!id!!';\">				
		!!musicstands_display!!
	</td> 
	<td style='vertical-align:top'>				
		<input type='button' class='bouton' value='".$msg['admin_nomenclature_musicstand_edition']."'  
			onclick=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_list&id=!!id!!'\"  />
	</td> 	
</tr> 	
";


$nomenclature_family_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_nomenclature_family_form_name_error"]."');
			return false;
		}
		return true;
	}
	
</script>
<form class='form-".$current_module."' id='nomenclature_family_form' name='nomenclature_family_form'  method='post' action=\"admin.php?categ=family&sub=family\" >
	<h3>!!msg_title!!</h3>		
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_family_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>		
		<div class='row'>
			<label class='etiquette'>".$msg['admin_nomenclature_family_pupitres']."</label>
		</div>
		<div class='row'>
			!!musicstands!!
		</div>	
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_family_form_exit']."'  onclick=\"document.location='./admin.php?categ=family&sub=family'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_family_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>	
<script type='text/javascript'>
<!--
	document.forms['nomenclature_family_form'].elements['name'].focus();
-->
</script>	
";


$nomenclature_family_musicstand_list_tpl="	
<h1>".htmlentities($msg["admin_nomenclature_family_musicstand"], ENT_QUOTES, $charset)."</h1>	
<script type='text/javascript' src='./javascript/sorttable.js'></script>					
<table class='sortable'>
	<tr>		
		<th>	
		</th> 		
		<th>	".htmlentities($msg["admin_nomenclature_family_musicstand_form_name"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_family_musicstand_form_instruments"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_family_musicstand_form_division"], ENT_QUOTES, $charset)."	
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_family_musicstand_form_workshop"], ENT_QUOTES, $charset)."	
		</th> 			 						 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' value='".$msg['admin_nomenclature_family_form_exit']."'  onclick=\"document.location='./admin.php?categ=family&sub=family&id=!!id!!'\"  />
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_family_musicstand_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=family&sub=family&id=!!id!!&action=musicstand_form'\" />	
";

$nomenclature_family_musicstand_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_up&id=!!id!!&id_musicstand=!!id_musicstand!!'\" value='-'>
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_down&id=!!id!!&id_musicstand=!!id_musicstand!!'\" value='+'>
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=!!id!!&id_musicstand=!!id_musicstand!!';\" >				
		!!name!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=!!id!!&id_musicstand=!!id_musicstand!!';\">				
		!!instruments!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=!!id!!&id_musicstand=!!id_musicstand!!';\">				
		!!division!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_form&id=!!id!!&id_musicstand=!!id_musicstand!!';\">				
		!!workshop!!
	</td> 
	
</tr> 	
";

$nomenclature_family_musicstand_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_nomenclature_family_musicstand_form_name_error"]."');
			return false;
		}
		return true;
	}
	
</script>	
<form class='form-".$current_module."' id='nomenclature_family_musicstand_form' name='nomenclature_family_musicstand_form'  method='post' action=\"admin.php?categ=family&sub=family\" >
	<h3>!!msg_title!!</h3>	
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<input type='hidden' name='id_musicstand' id='id_musicstand' value='!!id_musicstand!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_family_musicstand_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>		
		<div class='row'>
			<label class='etiquette' for='division'>".$msg['admin_nomenclature_family_musicstand_form_division']."</label>
		</div>
		<div class='row'>
			<input type='checkbox' name='division' id='division' value='1' !!checked!!/>
		</div>			
		<div class='row'>
			<label class='etiquette' for='workshop'>".$msg['admin_nomenclature_family_musicstand_form_workshop']."</label>
		</div>
		<div class='row'>
			<input type='checkbox' name='workshop' id='workshop' value='1' !!workshop_checked!!/>
		</div>					
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_family_musicstand_form_instruments']."</label>
		</div>
		<div class='row'>
			!!instruments!!
		</div>	
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_family_musicstand_form_exit']."'  onclick=\"document.location='./admin.php?categ=family&sub=family&action=musicstand_list&id=!!id!!'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_family_musicstand_form_save']."' onclick=\"document.getElementById('action').value='musicstand_save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
<!--
	document.forms['nomenclature_family_musicstand_form'].elements['name'].focus();
-->
</script>			
";

