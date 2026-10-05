<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_version.tpl.php,v 1.2 2019/05/27 12:11:12 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $cms_version_list, $msg, $cms_version_list_item, $cms_version_form_tpl, $base_path, $cms_version_form_del_button_tpl;

$cms_version_list ="	
<script type='text/javascript'>	
	function cms_version_build_opac(id){

	}

</script>	
		
<h3>".$msg["cms_version_list_menu"]."</h3>
<table class='cms_version_list'>
	<tr class='cms_version_list_item'>
		<th>
			".$msg['cms_version_list_name']."		
		</th>
		<th>
			".$msg['cms_version_list_tag_publication']."		
		</th>
		<th>
			".$msg['cms_version_list_tag_build']."		
		</th>
		<th>
			".$msg['cms_version_list_version_build']."		
		</th>
		<th>
			
		</th>
	</tr>
	!!items!!
</table>
<input class='bouton' type='button' value=\" ".$msg['cms_version_list_new_button']." \" onClick=\"document.location='./cms.php?categ=version&sub=edit'\" />

";

$cms_version_list_item ="
	<tr class='cms_version_list_item'>
		<td>
			!!cms_version_name!!			
		</td>
		<td>
			!!cms_version_tag_publication!!
		</td>
		<td>
			!!cms_version_tag_build!!
		</td>
		<td>
			!!cms_version_evolution_build!!
		</td>

		<td>
			<input class='bouton' type='button' value=\" ".$msg['cms_version_list_build_button']." \" 
				onClick=\"cms_version_build_opac(!!id!!);\" />					
			<input class='bouton' type='button' value=\" ".$msg['cms_version_list_build_button']." \" 
				onClick=\"document.location='./cms.php?categ=version&sub=edit&id=!!id!!'\"/>		
		</td>		
	</tr>
";

$cms_version_form_tpl = "
	<script type='text/javascript'>
		function confirm_delete(){
			
			var sup = confirm('".$msg['cms_version_confirm_suppr']."');
			if(!sup) return false;
				
			document.location =\"$base_path/cms.php?categ=version&sub=del&id=!!id!!\";
			return true;	
		}
		function test_form(form) {	
	
			if(!form.name.value){
		    	alert('".$msg["cms_version_no_name"]."');
				return false;
		    }
	    }
	</script>

	<form name='cms_version_form' class='cms_version_form' action='./cms.php?categ=version&sub=save&id=!!id!!' method='post' >
		<h3>!!form_title!!</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='name'>".$msg['cms_version_form_title']."</label>
			</div>
			<div class='row'>
				<input type=text id='name' name='name' value=\"!!name!!\" class='saisie-50em' />
			</div>		
		
			<div class='row'>
				<label class='etiquette' for='comment'>".$msg['cms_version_form_comment']."</label>
			</div>
			<div class='row'>
				<textarea id='comment' name='comment' cols='120' rows='5'>!!comment!!</textarea>
			</div>
			
			<div class='row'>
				<label class='etiquette' for='version_tag'>".$msg['cms_version_form_version_tag']."</label>
			</div>	
			<div class='row'>				
				!!tag_list!!
			</div>				

			
			<div class='row'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='left'>
				<input type='submit' class='bouton' value='".$msg['cms_version_form_save']."'  onClick=' return test_form(this.form); ' />
			</div>
			<div class='right'>
				!!form_suppr!!
			</div>
		</div>
		<div class='row'></div>
	</form>
";

$cms_version_form_del_button_tpl ="
			<input type='button'  class='bouton' onclick='confirm_delete();' value='".$msg['cms_version_form_delete']."'/>
";


