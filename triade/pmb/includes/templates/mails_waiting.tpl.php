<?php

global $mails_waiting_form_tpl, $current_module, $base_path, $charset, $msg;

$mails_waiting_form_tpl="
<form class='form-".$current_module."' id='mails_waiting_form' name='mails_waiting_form'  method='post' action=\"".$base_path."/admin.php?categ=mails_waiting&action=save\" >
	<h3>".htmlentities($msg['mails_waiting'], ENT_QUOTES, $charset)."</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='mails_waiting_attachments'>".$msg['mails_waiting_attachments']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='mails_waiting_attachments' id='mails_waiting_attachments' value='!!attachments!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='mails_waiting_max_by_send'>".$msg['mails_waiting_max_by_send']."</label>
		</div>
		<div class='row'>
			<input type='number' class='saisie-5em' name='mails_waiting_max_by_send' id='mails_waiting_max_by_send' value='!!max_by_send!!' />
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg['77']."' />
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['mails_waiting_form'].elements['mails_waiting_attachments'].focus();
</script>
";