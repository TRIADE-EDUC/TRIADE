<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form.tpl.php,v 1.8 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $contact_form_form_tpl, $base_path, $msg, $charset, $contact_form_recipients_tpl;

$contact_form_form_tpl="
<script type='text/javascript'>
	function contact_form_send(form) {
		require([
			'dojo/request/xhr',
			'dojo/dom-form'
		], function(xhr, domForm){
			xhr.post('./ajax.php?module=ajax&categ=contact_form&sub=form&action=send_mail',{
					handleAs: 'json',
					data: {form_fields : domForm.toJson(contact_form)}
				}
			).then(function(response){
				if(response) {
					if(response.sended) {
						var h3_node = document.createElement('h3');
						h3_node.setAttribute('class', 'contact_form_title');
						h3_node.innerHTML = \"!!title!!\";
						var response_node = document.createElement('p');
						response_node.setAttribute('class', 'contact_form_response');
						response_node.innerHTML = response.messages.join('<br />');
						document.getElementById('contact_form_content').innerHTML = '';
						document.getElementById('contact_form_content').appendChild(h3_node);
						document.getElementById('contact_form_content').appendChild(response_node);
					} else {
						document.getElementById('contact_form_message').innerHTML = response.messages.join('<br />');
						document.getElementById('contact_form_imageverifcode').setAttribute('src', '".$base_path."/includes/imageverifcode.inc.php?'+new Date());
					}
				}
			})
		});
	}
</script>
<div id='contact_form_content'>
	<h3 class='contact_form_title'>!!title!!</h3>
	<p class='contact_form_introduction'>".$msg['contact_form_introduction']."</p><br />
	<form id='contact_form' name='contact_form' method='post' action='' data-dojo-type='dijit/form/Form'>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne2'>
			</div>
			<div class='colonne2' id='contact_form_message'>
			</div>
		</div>
		!!recipients!!
		!!fields!!
		<div class='contact_form_objects'>
			<div class='colonne2'>
				<label>!!objects_label!!</label>
			</div>
			<div class='colonne2'>
				!!objects_selector!!
			</div>
		</div>
		<div class='contact_form_separator'>&nbsp;</div>
		<div class='contact_form_text'>
			<div class='colonne2'>
				<label for='contact_form_text'>".htmlentities($msg['contact_form_text'], ENT_QUOTES, $charset)."</label>
					".htmlentities($msg['contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset)."
			</div>
			<div class='colonne2'>
				<textarea id='contact_form_text' name='contact_form_text' class='saisie-50em' rows='5' cols='35' data-dojo-type='dijit/form/Textarea' required='true'>
				</textarea>
			</div>
		</div>
		<div class='contact_form_separator'>&nbsp;</div>
		<div class='contact_form_code'>
			<div class='colonne2'>
				&nbsp;
			</div>
			<div class='colonne2'>
				<span class='contact_form_text_verif'>".$msg['subs_txt_codeverif']."</span><br />
				<img src='$base_path/includes/imageverifcode.inc.php' id='contact_form_imageverifcode'><br />
				<div class='contact_form_verifcode'>
					<h4><span class='contact_form_text_verifcode'>".$msg['subs_f_verifcode']."</span></h4><br />
					<input type='text' class='subsform' name='contact_form_verifcode' data-dojo-type='dijit/form/TextBox' value='' required='true' />
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='colonne2'>
			&nbsp;
		</div>
		<div class='colonne2'>
			<input type='submit' class='bouton' value=\"".$msg['contact_form_button_send']."\" onclick=\"if(dijit.byId('contact_form').validate()) { contact_form_send(); } return false;\" />
		</div>
	</div>
	</form>
</div>
";

$contact_form_recipients_tpl= "
<div class='contact_form_recipients'>
	<div class='colonne2'>
		<label>!!recipients_label!!</label>
	</div>
	<div class='colonne2'>
		!!recipients_selector!!
	</div>
</div>
<div class='contact_form_separator'>&nbsp;</div>";