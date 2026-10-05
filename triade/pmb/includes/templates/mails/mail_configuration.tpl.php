<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_configuration.tpl.php,v 1.10 2024/04/26 15:27:09 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $charset, $msg;
global $mail_configuration_authentification_user_pwd_content_form;
global $mail_configuration_authentification_type_settings_content_form, $mail_configuration_authentification_type_settings_xoauth2_content_form;
global $mail_configuration_allowed_overrides_content_form, $mail_configuration_is_valid ;

$mail_configuration_authentification_user_pwd_content_form = "
<div class='row'>
	<label class='etiquette' for='mail_configuration_user'>".$msg['mail_configuration_user']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='mail_configuration_user' id='mail_configuration_user' value='!!user!!' !!authentification_readonly!! />
</div>
<div class='row'>
	<label class='etiquette' for='mail_configuration_password'>".$msg['mail_configuration_password']."</label>
</div>
<div class='row'>
	<input type='password' class='saisie-50em' name='mail_configuration_password' id='mail_configuration_password' value='' placeholder='!!password_placeholder!!' !!authentification_readonly!! autocomplete='new-password' />
	<span class='fa fa-eye' onclick='toggle_password(this, \"mail_configuration_password\");'></span>
</div>";

$mail_configuration_authentification_type_settings_content_form = "
<div class='row' id='auth_type_settings' style='!!none_auth_type_display!!'>
	<div class='row'>
		<label class='etiquette' for='mail_configuration_authentification_type_settings'>"
			. htmlentities($msg['mail_configuration_authentification_type_settings'], ENT_QUOTES, $charset)
		. "</label>
	</div>
	<div class='row'>
		{$mail_configuration_authentification_user_pwd_content_form}
		<div id='cram-md5' class='row' style='!!cram-md5_display!!'></div>
		<div id='login' class='row' style='!!login_display!!'></div>
		<div id='plain' class='row' style='!!plain_display!!'></div>
		<div id='xoauth2' class='row' style='!!xoauth2_display!!'>
			<div class='row'>
				<label class='etiquette' for='xoauth2_provider'>"
					. htmlentities($msg['xoauth2_provider'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<select name='mail_configuration_authentification_type_settings[xoauth2_provider]'
					class='saisie-50em' id='xoauth2_provider' !!authentification_readonly!!>
					!!xoauth2_provider_options!!
				</select>
			</div>
			<div class='row'>
				<label class='etiquette' for='xoauth2_tenant_id'>"
					. htmlentities($msg['xoauth2_tenant_id'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<input type='password' class='saisie-50em' !!authentification_readonly!!
					name='mail_configuration_authentification_type_settings[xoauth2_tenant_id]'
					id='xoauth2_tenant_id' value='!!xoauth2_tenant_id!!' autocomplete='off' />
				<span class='fa fa-eye' onclick='toggle_password(this, \"xoauth2_tenant_id\");'></span>
			</div>
			<div class='row'>
				<label class='etiquette' for='xoauth2_client_id'>"
					. htmlentities($msg['xoauth2_client_id'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<input type='password' class='saisie-50em' !!authentification_readonly!!
					name='mail_configuration_authentification_type_settings[xoauth2_client_id]'
					id='xoauth2_client_id' value='!!xoauth2_client_id!!' autocomplete='off' />
				<span class='fa fa-eye' onclick='toggle_password(this, \"xoauth2_client_id\");'></span>
			</div>
			<div class='row'>
				<label class='etiquette' for='xoauth2_secret_value'>"
					. htmlentities($msg['xoauth2_secret_value'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<input type='password' class='saisie-50em' !!authentification_readonly!!
					name='mail_configuration_authentification_type_settings[xoauth2_secret_value]'
					id='xoauth2_secret_value' value='!!xoauth2_secret_value!!' autocomplete='off' />
				<span class='fa fa-eye' onclick='toggle_password(this, \"xoauth2_secret_value\");'></span>
			</div>
			<div class='row'>
				<label class='etiquette' for='xoauth2_refresh_token'>"
					. htmlentities($msg['xoauth2_refresh_token'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<input type='password' class='saisie-50em' !!authentification_readonly!!
					name='mail_configuration_authentification_type_settings[xoauth2_refresh_token]'
					id='xoauth2_refresh_token' value='!!xoauth2_refresh_token!!' autocomplete='off' />
				<span class='fa fa-eye' onclick='toggle_password(this, \"xoauth2_refresh_token\");'></span>
			</div>
			<div class='row'>
				<label class='etiquette' for='xoauth2_refresh_token_validity'>"
					. htmlentities($msg['xoauth2_refresh_token_validity'], ENT_QUOTES, $charset)
				."</label>
			</div>
			<div class='row'>
				<input type='date' class='saisie-50em' !!authentification_readonly!!
					name='mail_configuration_authentification_type_settings[xoauth2_refresh_token_validity]'
					id='xoauth2_refresh_token_validity' value='!!xoauth2_refresh_token_validity!!' />
			</div>
		</div>
	</div>
</div>
";

$mail_configuration_allowed_overrides_content_form = "
<div class='row'>
	<label class='etiquette' for='mail_configuration_allowed_hote_override'>".$msg['mail_configuration_allowed_hote_override']."</label>
</div>
<div class='row'>
	<input type='checkbox' name='mail_configuration_allowed_hote_override' id='mail_configuration_allowed_hote_override' value='1' !!allowed_hote_override!! />
</div>
";

$mail_configuration_allowed_unlock_form = "
<button type='button' onClick='unlockDomainForm(this)' class='bouton' id='mail_configuration_lock_button'>
    <i class='fa fa-lock' aria-hidden='true'></i>
</button>
<script>
    function unlockDomainForm(button) {
        let configHote = document.getElementById('mail_configuration_hote');
        let configPort = document.getElementById('mail_configuration_port');
        let configProtocol = document.getElementById('mail_configuration_secure_protocol');
        if (configHote.disabled) {
            configHote.disabled = false;
            configPort.disabled = false;
            configProtocol.disabled = false;
            configProtocol.readonly = false;
            button.innerHTML = '<i class=\'fa fa-unlock\' aria-hidden=\'true\'></i>';
        } else {
            configHote.disabled = true;
            configPort.disabled = true;
            configProtocol.disabled = true;
            configProtocol.readonly = true;
            button.innerHTML = '<i class=\'fa fa-lock\' aria-hidden=\'true\'></i>';
        }
    }
</script>";

$mail_configuration_is_valid['form'] = "
<div class='row'>
	<label class='etiquette'>"
		. htmlentities($msg['mail_configuration_is_valid'], ENT_QUOTES, $charset)
	."</label>
    <!-- value -->
</div>";
$mail_configuration_is_valid['yes'] = "<span class='fa fa-check fa-2x green' aria-hidden='true'></span>";
$mail_configuration_is_valid['no'] = "<span class='fa fa-close fa-2x red' aria-hidden='true'></span>";
