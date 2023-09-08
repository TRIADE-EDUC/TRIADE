<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest.tpl.php,v 1.4 2019-05-27 14:43:15 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $harvest_list_tpl, $msg, $charset, $harvest_list_line_tpl, $harvest_form_tpl, $current_module, $harvest_form_elt_tpl, $harvest_form_elt_src_tpl, $harvest_form_elt_ajax_tpl;

$harvest_list_tpl="	
<h1>".htmlentities($msg["admin_harvest_build_title"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_harvest_build_name"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_empr_button' value='".htmlentities($msg["admin_harvest_build_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=harvest&sub=build&action=form'\" />	
";
$harvest_list_line_tpl="
<tr  class='!!odd_even!!' onmousedown=\"document.location='./admin.php?categ=harvest&sub=build&action=form&id_harvest=!!id!!';\"  style=\"cursor: pointer\" 
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
			alert('".$msg["admin_harvest_build_name_error"]."');
			return false;
		}
		return true;
	}
	
	function add_harvest_field_form(id_field){
		var nb = document.getElementById('unimarcfieldnumber_'+id_field).value *1;	
		nb++;
	
	
		var mydiv = document.getElementById('add_zone_harvest_'+id_field);
    	var newcontent = document.createElement('div');    	
		var nom_id = 'unimarcfield_'+id_field+'_'+nb;	
		newcontent.setAttribute('id',nom_id);
		var harvest_field_form_add =document.getElementById('harvest_field_form_add_'+id_field);
		
		var form=harvest_field_form_add.innerHTML;
		
		// replave !!nb!! par nb
		while (form.search('!!nb!!') != -1) form = form.replace('!!nb!!',nb); 
    	newcontent.innerHTML = form;
		mydiv.appendChild(newcontent);
 		document.getElementById('unimarcfieldnumber_'+id_field).value=nb;
	}
	function del_harvest_field_form(id_field,nb){
		var nom_id = 'unimarcfield_'+id_field+'_'+nb;	
		var mydiv = document.getElementById('add_zone_harvest_'+id_field);
		mydiv.removeChild(document.getElementById(nom_id));
	}
</script>

<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='harvest' name='harvest'  method='post' action=\"admin.php?categ=harvest&sub=build\" >

	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id_harvest' id='id_harvest' value='!!id_harvest!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_harvest_build_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_harvest_build_form_src_list']."</label>
		</div>
		<div class='row'>
			<table>	
				<tr>			
					<th>	".htmlentities($msg["admin_harvest_build_srce"], ENT_QUOTES, $charset)."			
					</th> 			
					<th>	".htmlentities($msg["admin_harvest_build_code"], ENT_QUOTES, $charset)."			
					</th> 			
				</tr>		
				!!src_list!!
			</table>
		</div>
		
		!!elt_list!!
	
		<div class='row'> 
		</div>
	</div>
	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_harvest_build_save']."' onclick=\"document.getElementById('action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_harvest_build_exit']."'  onclick=\"document.location='./admin.php?categ=harvest&sub=build'\"  />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
	";

$harvest_form_elt_tpl="		
<div class='row'>
	<label class='etiquette' >!!pmb_field_msg!!</label>
	<input type='checkbox'  name='firstfound_!!id!!' id='firstfound_!!id!!' !!first_flagchecked!! value='1' /> ".$msg['admin_harvest_build_form_first_found']."
</div>
<div class='row'>			
	!!unimarcfield!! !!subfield!!	!!sources!! !!pmb_unimarc_select!!
	<input type='button' class='bouton' value='+' onclick=\"add_harvest_field_form(!!id!!);\" /> 
	
	<div id='add_zone_harvest_!!id!!'> 
		!!add_zone_harvest!!
	</div>	
	<input type='hidden' name='unimarcfieldnumber_!!id!!' id='unimarcfieldnumber_!!id!!' value='!!nb!!' /> 
</div>
!!harvest_field_form_add!!
";
$harvest_form_elt_src_tpl="		
<div  id='unimarcfield_!!id!!_!!nb!!' >
<div class='row'>		
	!!unimarcfield!! !!subfield!!	!!sources!! !!pmb_unimarc_select!!	!!onlylastempty!!	
	<input type='button' class='bouton' value='X' onclick=\"del_harvest_field_form(!!id!!,!!nb!!);\" />
</div>
</div>
";
$harvest_form_elt_ajax_tpl="		
<div  id='harvest_field_form_add_!!id!!' style='display:none;'>
<div class='row'>		
	!!unimarcfield!! !!subfield!!	!!sources!! !!pmb_unimarc_select!!	!!onlylastempty!!	
	<input type='button' class='bouton' value='X' onclick=\"del_harvest_field_form(!!id!!,!!nb!!);\" />
</div>
</div>
";