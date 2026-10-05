<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: websubscribe.tpl.php,v 1.21 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $subs_form_create;
global $opac_websubscribe_show_location;
global $pmb_lecteurs_localises;
global $form_access_compte;
global $msg, $base_path;

$subs_form_create="
<div id='subs_form'><h3><span>".$msg['subs_titre_form']."</span></h3>
<script>
<!--
function test_inscription(form) {
	if (!form.f_consent_message.checked) {
		alert(reverse_html_entities(\"".$msg['subs_form_consent_message_mandatory']."\"));
		return false;
	}
	if ((form.f_nom.value.length==0) || (form.f_prenom.value.length==0) || (form.f_mail.value.length==0) || (form.f_login.value.length==0) || (form.f_password.value.length==0) || (form.f_passwordv.value.length==0) || (form.f_verifcode.value.length<5)) {
		alert(reverse_html_entities(\"".$msg['subs_form_obligatoire']."\"));
		return false;
	} else if (form.f_password.value!=form.f_passwordv.value) {
		alert(reverse_html_entities(\"".$msg['subs_form_bad_passwords']."\"));
		return false;
	} else return true;
   }
-->
</script>
<form name='subs_form' method='post' action='./subscribe.php?subsact=inscrire'>
	<table class='websubscribe_form'>
		<tr class='websubscribe_tr_nom'><td class='align_right' style='width:20%'><h4><span>".$msg['subs_f_nom']." : </span></h4></td><td style='width:25%'><input type='text' class='subsform' name='f_nom' tabindex='1' value='!!f_nom!!' /></td>
			<td rowspan=5 style='width:8%'>&nbsp;</td>
			<td rowspan=6 class='center'>".$msg['subs_txt_codeverif']."<br />
				<img src='$base_path/includes/imageverifcode.inc.php'><br />
				<h4><span class='subscribe_field_mandatory'>".$msg['subs_f_verifcode']."</span> : </h4><br />
				<input type='text' class='subsform' name='f_verifcode' value='' /><br />
				<input type='checkbox' name='f_consent_message' value='1' /> 
				<span class='websubscribe_consent_message'> ".$msg['subs_f_consent_message']."</span>
			</td>
		</tr>
		<tr class='websubscribe_tr_prenom'><td class='align_right'><h4><span>".$msg['subs_f_prenom']." : </span></h4></td><td><input type='text' class='subsform' name='f_prenom'  tabindex='3' value='!!f_prenom!!' /></td>
		</tr>
		<tr class='websubscribe_tr_email'><td class='align_right'><h4><span>".$msg['subs_f_email']." : </span></h4></td><td><input type='email' class='subsform' name='f_mail'  tabindex='4' value='!!f_mail!!' required /></td>
		</tr>
		<tr class='websubscribe_tr_login'><td class='align_right'><h4><span>".$msg['subs_f_login']." : </span></h4></td><td><input type='text' class='subsform' name='f_login' tabindex='5' value='!!f_login!!' /></td>
		</tr>
		<tr class='websubscribe_tr_password'><td class='align_right'><h4><span>".$msg['subs_f_password']." : </span></h4></td><td><input type='password' class='subsform' name='f_password' tabindex='6' value='!!f_password!!' /></td>
		</tr>
		<tr class='websubscribe_tr_passwordv'><td class='align_right'><h4><span>".$msg['subs_f_passwordv']." : </span></h4></td><td><input type='password' class='subsform' name='f_passwordv' tabindex='7' value='!!f_passwordv!!' /></td>
		</tr>
		<tr class='websubscribe_tr_bouton'><td class='align_right'>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class='center'><input type=submit class='bouton' value=\"".$msg['subs_bouton_form']."\" onClick=\"if (test_inscription(this.form)) this.form.submit();\" /></td>
		</tr>
		<tr class='websubscribe_tr_adr1'><td class='align_right'><span>".$msg['subs_f_adr1']." : </span></td><td><input type='text' class='subsform' name='f_adr1' tabindex='10' value='!!f_adr1!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_adr2'><td class='align_right'><span>".$msg['subs_f_adr2']." : </span></td><td><input type='text' class='subsform' name='f_adr2' tabindex='11' value='!!f_adr2!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_cp'><td class='align_right'><span>".$msg['subs_f_cp']." ".$msg['subs_f_ville']." : </span></td><td><input type='text' class='subsform' name='f_cp' tabindex='12' value='!!f_cp!!' style='width:50px;'/> <input type='text' class='subsform' name='f_ville'  tabindex='13' value='!!f_ville!!' style='width:136px;'/></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_pays'><td class='align_right'><span>".$msg['subs_f_pays']." : </span></td><td><input type='text' class='subsform' name='f_pays' tabindex='14' value='!!f_pays!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_tel1'><td class='align_right'><span>".$msg['subs_f_tel1']." : </span></td><td><input type='text' class='subsform' name='f_tel1' tabindex='15' value='!!f_tel1!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_msg'><td class='align_right'><span>".$msg['subs_f_msg']." : </span></td><td><input type='text' class='subsform' name='f_msg' tabindex='16' value='!!f_msg!!' /></td><td>&nbsp;</td><td></td>
		</tr>
		".(($opac_websubscribe_show_location && $pmb_lecteurs_localises) ? "<tr><td class='align_right'>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td></td>
		</tr>
		<tr class='websubscribe_tr_loc'><td class='align_right'><span>".$msg['subs_f_loc']." : </span></td><td>!!f_loc!!</td><td>&nbsp;</td><td></td>
		</tr>" : "")."
	</table>
	!!others_informations!!
	</form>	
</div>
";


$form_access_compte="<form action='empr.php' method='post' name='myform'>
				<input type='hidden' name='login' value=\"!!login!!\" />
				<input type='hidden' name='encrypted_password' value='!!encrypted_password!!' />
				<input type='submit' name='ok' value=\"".$msg['subs_bouton_acces_compte']."\" class='bouton'>
			</form>";
