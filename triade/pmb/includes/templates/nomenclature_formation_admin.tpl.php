<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_formation_admin.tpl.php,v 1.9 2019-05-27 14:55:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_formation_list_tpl, $msg, $charset, $nomenclature_formation_list_line_tpl, $nomenclature_formation_form_tpl, $current_module, $nomenclature_formation_type_list_tpl;
global $nomenclature_formation_type_list_line_tpl, $nomenclature_formation_type_form_tpl;

$nomenclature_formation_list_tpl="	
<script type='text/javascript' src='./javascript/sorttable.js'></script>			
<table class='sortable'>
	<tr>		
		<th>	
		</th> 	
		<th>	".htmlentities($msg["admin_nomenclature_formation_name"], ENT_QUOTES, $charset)."			
		</th> 
		<th>	".htmlentities($msg["admin_nomenclature_formation_types"], ENT_QUOTES, $charset)."	
		</th>  
		<th>	".htmlentities($msg["admin_nomenclature_formation_nature"], ENT_QUOTES, $charset)."	
		</th> 
		<th>	
		</th> 				 						 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_formation_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=form'\" />	
";

$nomenclature_formation_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=up&id=!!id!!'\" value='-'>
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=down&id=!!id!!'\" value='+'>
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=formation&sub=formation&action=form&id=!!id!!';\" >				
		!!name!!
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=formation&sub=formation&action=form&id=!!id!!';\">				
		!!types_display!!
	</td>  
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=formation&sub=formation&action=form&id=!!id!!';\">				
		!!nature!!
	</td> 
	<td style='vertical-align:top'>				
		<input type='button' class='bouton' value='".$msg['admin_nomenclature_type_edition']."'  
			onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=type_list&id=!!id!!'\"  />
	</td> 	
</tr> 	
";


$nomenclature_formation_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_nomenclature_formation_form_name_error"]."');
			return false;
		}
		return true;
	}
	
</script>		
<form class='form-".$current_module."' id='nomenclature_formation_form' name='nomenclature_formation_form'  method='post' action=\"admin.php?categ=formation&sub=formation\" >
	<h3>!!msg_title!!</h3>
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_formation_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg['admin_nomenclature_formation_form_nature']."</label>
		</div>
		<div class='row'>		
			<input type='radio' name='nature' value='0' !!nature_checked_0!! />
			".$msg['admin_nomenclature_formation_form_nature_instrument']."
		</div>				
		<div class='row'>	
			<input type='radio' name='nature' value='1' !!nature_checked_1!! />
			".$msg['admin_nomenclature_formation_form_nature_voice']."
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg['admin_nomenclature_formation_types']."</label>
		</div>
		<div class='row'>
			!!types!!
		</div>	
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_formation_form_exit']."'  onclick=\"document.location='./admin.php?categ=formation&sub=formation'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_formation_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>	
<script type='text/javascript'>
<!--
	document.forms['nomenclature_formation_form'].elements['name'].focus();
-->
</script>		
";


$nomenclature_formation_type_list_tpl="	
<h1>".htmlentities($msg["admin_nomenclature_formation_type"], ENT_QUOTES, $charset)."</h1>	
<script type='text/javascript' src='./javascript/sorttable.js'></script>					
<table class='sortable'>
	<tr>		
		<th>	
		</th> 		
		<th>	".htmlentities($msg["admin_nomenclature_formation_type_form_name"], ENT_QUOTES, $charset)."			
		</th> 			 						 			
	</tr>						
	!!list!!			
</table> 		
<input type='button' class='bouton' value='".$msg['admin_nomenclature_formation_form_exit']."'  onclick=\"document.location='./admin.php?categ=formation&sub=formation&id=!!id!!'\"  />			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_formation_type_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=formation&sub=formation&id=!!id!!&action=type_form'\" />	
";

$nomenclature_formation_type_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=type_up&id=!!id!!&id_type=!!id_type!!'\" value='-'>
		<input class='bouton_small' type='button' onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=type_down&id=!!id!!&id_type=!!id_type!!'\" value='+'>
	</td> 
	<td style=\"vertical-align:top; cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=formation&sub=formation&action=type_form&id=!!id!!&id_type=!!id_type!!';\" >				
		!!name!!
	</td> 
</tr> 	
";

$nomenclature_formation_type_form_tpl="		
<script type='text/javascript'>

	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_nomenclature_formation_type_form_name_error"]."');
			return false;
		}
		return true;
	}
	
</script>
<form class='form-".$current_module."' id='nomenclature_formation_type_form' name='nomenclature_formation_type_form'  method='post' action=\"admin.php?categ=formation&sub=formation\" >
	<h1>!!msg_title!!</h1>		
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<input type='hidden' name='id_type' id='id_type' value='!!id_type!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_nomenclature_formation_type_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>	
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_formation_type_form_exit']."'  onclick=\"document.location='./admin.php?categ=formation&sub=formation&action=type_list&id=!!id!!'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_formation_type_form_save']."' onclick=\"document.getElementById('action').value='type_save';if(!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>	
<script type='text/javascript'>
<!--
	document.forms['nomenclature_formation_type_form'].elements['name'].focus();
-->
</script>	
";

