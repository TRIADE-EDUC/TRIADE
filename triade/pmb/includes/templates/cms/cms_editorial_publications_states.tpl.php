<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_publications_states.tpl.php,v 1.5 2019-05-27 10:36:51 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cms_editorial_publication_state_form, $current_module, $msg;

$cms_editorial_publication_state_form ="
<form method='post' class='form-$current_module' name='cms_editorial_publication_state_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_publication_state_label'>".$msg['editorial_content_publication_state_label']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='cms_editorial_publication_state_label' value='!!label!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_publication_state_class_html'>".$msg['editorial_content_publication_state_class_html']."</label>
			</div>
			<div class='colonne_suite'>
				!!class_html!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_publication_state_visible'>".$msg['editorial_content_publication_state_visible']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='cms_editorial_publication_state_visible' value='1' !!visible!!/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_editorial_publication_state_visible_abo'>".$msg['editorial_content_publication_state_visible_abo']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='cms_editorial_publication_state_visible_abo' value='1' !!visible_abo!!/>
			</div>
		</div>			
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='cms_editorial_publication_state_id' value='!!id!!'/>
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
		if(form.cms_editorial_publication_state_label.value.length == 0){
			alert(\"".$msg[98]."\");
			return false;
		}
		return true;
	}
</script>";
