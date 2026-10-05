<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.tpl.php,v 1.4 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $msg, $cms_page_form_var_tpl_0, $cms_page_form_var_tpl, $cms_page_form_tpl, $base_path, $cms_page_form_del_button_tpl, $cms_page_form_ajax_tpl;

$cms_page_form_var_tpl_0="	

	<div class='row'>
		<input type='hidden' id='var_count' name='var_count' value='!!var_count!!' />
		<input type=text id='var_name_!!cpt!!' name='var_name_!!cpt!!' value=\"!!var_name!!\" class='saisie-50em' />	
		<input type=text id='var_comment_!!cpt!!' name='var_comment_!!cpt!!' value=\"!!var_comment!!\" class='saisie-50em' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"cms_page_raz_var(!!cpt!!);\" />
		<input class='bouton' type='button' onclick='cms_page_add_var();' value='+'>
	</div>				
";
$cms_page_form_var_tpl="
	<div class='row'>
		<input type=text id='var_name_!!cpt!!' name='var_name_!!cpt!!' value=\"!!var_name!!\" class='saisie-50em' />	
		<input type=text id='var_comment_!!cpt!!' name='var_comment_!!cpt!!' value=\"!!var_comment!!\" class='saisie-50em' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"cms_page_raz_var(!!cpt!!); \" />
	</div>	
";

$cms_page_form_tpl = "
	<script>
		function confirm_delete(){
			
			var sup = confirm('".$msg['cms_page_confirm_suppr']."');
			if(!sup) return false;
				
			document.location =\"$base_path/cms.php?categ=pages&sub=del&id=!!id!!\";
			return true;	
		}
		function test_form(form) {	
	
			if(!form.name.value){
		    	alert('".$msg["cms_page_no_name"]."');
				return false;
		    }
	    }
	</script>
	
	<script src='./javascript/cms/cms_pages.js'></script>
	<form name='cms_page_form' class='cms_page_form' action='./cms.php?categ=pages&sub=save&id=!!id!!' method='post' >
		<h3>!!form_title!!</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='name'>".$msg['cms_page_title_page']."</label>
			</div>
			<div class='row'>
				<input type=text id='name' name='name' value=\"!!name!!\" class='saisie-50em' />
			</div>		
		
			<div class='row'>
				<label class='etiquette' for='description'>".$msg['cms_page_description']."</label>
			</div>
			<div class='row'>
				<textarea id='description' name='description' cols='120' rows='5'>!!description!!</textarea>
			</div>
			
			<div class='row'>
				<label class='etiquette' for='description'>".$msg['cms_page_variables']."</label>
			</div>					
			!!var_list!!
			<div id='add_var'>
			</div>					
			<div class='row'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='left'>
				<input type='submit' class='bouton' value='".$msg['cms_page_form_save']."'  onClick=' return test_form(this.form); ' />
			</div>
			<div class='right'>
				!!cms_page_form_suppr!!
			</div>
		</div>
		<div class='row'></div>
	</form>
";

$cms_page_form_del_button_tpl ="
			<input type='button'  class='bouton' onclick='confirm_delete();' value='".$msg['cms_page_form_delete']."'/>
";


$cms_page_form_ajax_tpl = "

		<h3>!!form_title!!</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='name'>".$msg['cms_page_title_page']."</label>
			</div>
			<div class='row'>
				<input type=text id='name' name='name' value=\"!!name!!\" class='saisie-50em' />
			</div>		
		
			<div class='row'>
				<label class='etiquette' for='description'>".$msg['cms_page_description']."</label>
			</div>
			<div class='row'>
				<textarea id='description' name='description' cols='120' rows='5'>!!description!!</textarea>
			</div>
			
			<div class='row'>
				<label class='etiquette' for='description'>".$msg['cms_page_variables']."</label>
			</div>					
			!!var_list!!
			<div id='add_var'>
			</div>					
			<div class='row'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='left'>
				<input type='submit'  class='bouton' value='".$msg['cms_page_form_save']."'  
				onClick=\" 
				document.getElementById('cms_build_pages_list').innerHTML=cms_page_ajax_save(!!id!!);
				dijit.byId('cms_build_dialog').hide();
				 \" />
			</div>
		</div>
		<div class='row'></div>
	</form>";