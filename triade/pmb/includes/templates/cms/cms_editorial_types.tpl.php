<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_types.tpl.php,v 1.3 2019-05-27 10:36:18 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cms_editorial_type_form, $current_module, $msg, $cms_editorial_type_form_std_label, $cms_editorial_type_form_generic_label;

$cms_editorial_type_form ="
<form method='post' class='form-$current_module' name='cms_editorial_type_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_type_label'>".$msg['editorial_content_type_label']."</label>
			</div>
			<div class='colonne-suite'>
				!!cms_editorial_label!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_type_comment'>".$msg['editorial_content_type_comment']."</label>
			</div>
			<div class='colonne-suite'>
				<textarea name='cms_editorial_type_comment' rows='5' >!!comment!!</textarea>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_type_page_selector'>".$msg['dsi_docwatch_datasource_link_select_cms_page']."</label>
			</div>
			<div class='colonne-suite'>
				<select id='cms_editorial_type_page_selector' name='cms_editorial_type_page_selector'>
				!!cms_page_options!!		
				</select>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_type_page_var_selector'>".$msg['cms_page_variable']."</label>
			</div>
			<div class='colonne-suite'>
				<select id='cms_editorial_type_page_var_selector' name='cms_editorial_type_page_var_selector'>
				!!cms_env_var_options!!
				</select>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='cms_editorial_type_id' value='!!id!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='!!action!!'\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</form>
<script type='text/javascript'>
	function test_form(form){
		if(form.cms_editorial_type_label.value.length == 0){
			alert(\"".$msg[98]."\");
			return false;
		}
		return true;
	}
	require(['dojo/ready', 'dojo/dom', 'dojo/on', 'dojo/request/xhr'], function(ready, dom, on, xhr){
	     ready(function(){
			on(dom.byId('cms_editorial_type_page_selector'), 'change', function(evt){
				xhr('./ajax.php?module=admin&categ=cms&sub=editorial&action=get_env_var&page_id='+evt.target.value, {
					sync: false,
					handleAs: 'text',
				}).then(function(options){
					dom.byId('cms_editorial_type_page_var_selector').innerHTML = options; 
				});
			});
	     });
	});
	
</script>";

$cms_editorial_type_form_std_label ="
<input type='text' name='cms_editorial_type_label' value='!!label!!'/>";


$cms_editorial_type_form_generic_label ="
<label>!!label!!</label>
<input type='hidden' name='cms_editorial_type_label' value='!!label!!' />";

