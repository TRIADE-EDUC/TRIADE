<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_collection.tpl.php,v 1.4 2020/06/22 07:41:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cms_collection_form, $current_module, $msg;

$cms_collection_form ="
<form method='post' class='form-$current_module' name='cms_collection_form' action='!!action!!'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_collection_title'>".$msg['cms_collection_title']."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='cms_collection_title' value='!!label!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_collection_description'>".$msg['cms_collection_description']."</label>
			</div>
			<div class='colonne-suite'>
				<textarea name='cms_collection_description' rows='5' >!!comment!!</textarea>
			</div>
		</div>
	</div>
	<hr/>
	!!storage_form!!
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='cms_collection_id' value='!!id!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"history.go(-1)\">&nbsp;
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
		if(form.cms_collection_title.value.length == 0){
			alert(\"".$msg[98]."\");
			return false;
		}
		if(form.storage_method.value == 0){
			alert(pmbDojo.messages.getMessage('cms', 'storage_method_not_selected'));
			return false;
		}
		return true;
	}
</script>";
