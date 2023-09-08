<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest_profil_import.tpl.php,v 1.4 2019-05-27 14:36:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $harvest_list_tpl, $msg, $charset, $harvest_list_line_tpl, $harvest_form_tpl, $current_module, $harvest_form_elt_tpl;

$harvest_list_tpl="	
<h1>".htmlentities($msg["admin_harvest_profil_title"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_harvest_profil_name"], ENT_QUOTES, $charset)."			
		</th> 					
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton'  value='".htmlentities($msg["admin_harvest_profil_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=harvest&sub=profil&action=form'\" />	
";
$harvest_list_line_tpl="
<tr class='!!odd_even!!' onmousedown=\"document.location='./admin.php?categ=harvest&sub=profil&action=form&id_profil=!!id!!';\"  style=\"cursor: pointer\" 
onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style='vertical-align:top'>				
		!!name!!
	</td> 	
</tr> 	
";

$harvest_form_tpl="	
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".addslashes($msg["admin_harvest_profil_name_error"])."');
			return false;
		}
		return true;
	}
</script>

<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='harvest' name='harvest'  method='post' action=\"admin.php?categ=harvest&sub=profil\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id_profil' id='id_harvest' value='!!id_profil!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_harvest_profil_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>		

		<div class='row'>
			<table border='0' width='100%'>	
				<th>".$msg['admin_harvest_profil_form_field_title']."
				</th>
				<th>".$msg['admin_harvest_profil_form_field_no']."
				</th>
				<th>".$msg['admin_harvest_profil_form_field_replace']."
				</th>
				<th>".$msg['admin_harvest_profil_form_field_add']."
				</th>
				!!elt_list!!
			</table>
		</div>
		<div class='row'> 
		</div>
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_harvest_profil_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_harvest_profil_exit']."'  onclick=\"document.location='./admin.php?categ=harvest&sub=profil'\"  />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
	";

$harvest_form_elt_tpl="		
<tr>
	<td>
		<label class='etiquette' >!!pmb_field_msg!!</label>
	</td>
	<td>	
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_0!! value='0' /> 
	</td>
	<td>
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_1!! value='1' /> 
	</td>
	<td>
		<input type='radio'  name='flagtodo_!!id!!'  !!flagtodo_checked_2!! value='2' /> 
	</td>
</tr>

";
