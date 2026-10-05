<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: account.tpl.php,v 1.32 2024/01/05 11:17:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $account_js_script_layout, $msg, $account_form, $current_module, $user_acquisition_adr_form, $charset;

$account_js_script_layout = "
<script type=\"text/javascript\">
<!--
function setValue(f_element, factor) {
    var maxv = 50;
    var minv = 1;

    var vl = document.forms['account_form'].elements[f_element].value;
    if((vl < maxv) && (factor == 1))
       vl++;
    if((vl > minv) && (factor == -1))
        vl--;
    document.forms['account_form'].elements[f_element].value = vl;
}
function test_pwd(form, status) {
	if(form.form_pwd.value.length != 0) {
		if(form.form_pwd.value != form.passw2.value) {
			alert(\"$msg[80]\");
			return false;
		}
    }
	return true;
}

function account_calcule_section(selectBox) {
	for (i=0; i<selectBox.options.length; i++) {
		id=selectBox.options[i].value;
	    list=document.getElementById(\"docloc_section\"+id);
	    list.style.display=\"none\";
	}

	id=selectBox.options[selectBox.selectedIndex].value;
	list=document.getElementById(\"docloc_section\"+id);
	list.style.display=\"block\";
}
-->
</script>
";

$account_form ="
<form class='form-$current_module' id='account_form' name=\"account_form\" method=\"post\" action=\"./account.php\">
<!--	Form contenu	-->
<div class='form-contenu'>
<!--	Langue	-->
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label for='user_lang' class='etiquette'>$msg[user_langue]</label>
		</div>
		<div class='row'>
			!!combo_user_lang!!
		</div>
	</div>
	<div class='colonne_suite'>
	<!--	Style/thème	-->
		<div class='row'>
			<label class='etiquette' for='form_style'>$msg[935]</label>
		</div>
		<div class='row'>
			!!combo_user_style!!
		</div>
	</div>
	<hr />
</div>

<!-- email -->
<div class='row'>
	<div class='colonne3'>
		<label class='etiquette' for='form_user_email'>".$msg['email']." &nbsp;</label><br />
		<input type='text' class='saisie-20em' id='form_user_email' name='form_user_email' value='!!user_email!!' autocomplete='off' />
	</div>
    <hr />
</div>

<div class='row'>
	<div class='colonne4'>
		<div class='row'>
			<label class='etiquette'>$msg[nb_enreg_par_page]</label>
		</div>
	</div>
	<div class='colonne4'>
		<!--	Nombre d'enregistrements par page en recherche	-->
		<div class='row'>
			<label class='etiquette' for='form_nb_per_page_search'>$msg[900]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-5em' id='form_nb_per_page_search' name='form_nb_per_page_search' value='!!nb_per_page_search!!' size='4' />
		</div>
	</div>	
	<div class='colonne4'>
		<!--	Nombre d'enregistrements par page en sélection d'autorités	-->
		<div class='row'>
			<label class='etiquette' for='form_nb_per_page_select'>{$msg[901]}</label>
		</div>
		<div class='row'>
			<input class='saisie-5em' type='text' id='form_nb_per_page_select' name='form_nb_per_page_select' value='!!nb_per_page_select!!' size='4' />
		</div>
	</div>	
	<div class='colonne4'>
		<div class='row'>
			<label class='etiquette' for='form_nb_per_page_gestion'>{$msg[902]}</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-5em' id='form_nb_per_page_gestion' name='form_nb_per_page_gestion' value='!!nb_per_page_gestion!!' size='4' />
		</div>
	</div>
</div>


<div class='row allUserParam-container'>
	!!all_user_param!!
</div>

<input type='hidden' id='modified' name='modified' value='1' />

</div>

<!--	Bouton d'envoi	-->
<div class='row'>
	<input class='bouton' type='submit' value='$msg[77]' onClick=\"return test_pwd(this.form)\" />
	<!--<input class='bouton' type='button' value='$msg[76]' onClick=\"document.location='./main.php'\" />-->
</div>

</form>

</div>
</div>
";


$user_acquisition_adr_form = "
<div class='row'>
	<div class='child'>
		<div class='colonne2'><label for='adr_liv[!!id_bibli!!]' style='all:unset'>".htmlentities($msg['acquisition_adr_liv'], ENT_QUOTES, $charset)."</label></div>
		<div class='colonne2'><label for='adr_fac[!!id_bibli!!]' style='all:unset'>".htmlentities($msg['acquisition_adr_fac'], ENT_QUOTES, $charset)."</label></div>
	</div>
</div>
<div class='row'>
	<div class='child'>
		<div class='colonne2'>
			<div class='colonne' >					
				<input type='hidden' id='id_adr_liv[!!id_bibli!!]' name='id_adr_liv[!!id_bibli!!]' value='!!id_adr_liv!!' />
				<textarea  id='adr_liv[!!id_bibli!!]' name='adr_liv[!!id_bibli!!]' class='saisie-30emr' readonly='readonly' cols='50' rows='6' wrap='virtual'>!!adr_liv!!</textarea>&nbsp;
			</div>
			<div class='colonne_suite' >
				<input type='button' class='bouton_small' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=!!form_name!!&param1=id_adr_liv[!!id_bibli!!]&param2=adr_liv[!!id_bibli!!]&id_bibli=!!id_bibli!!', 'selector'); \" />&nbsp;
				<input type='button' class='bouton_small' tabindex='1' value='X' onclick=\"document.getElementById('id_adr_liv[!!id_bibli!!]').value='0';document.getElementById('adr_liv[!!id_bibli!!]').value='';\" />
			</div>
		</div>
		<div class='colonne2'>
			<div class='colonne'>
				<input type='hidden' id='id_adr_fac[!!id_bibli!!]' name='id_adr_fac[!!id_bibli!!]' value='!!id_adr_fac!!' />
				<textarea id='adr_fac[!!id_bibli!!]' name='adr_fac[!!id_bibli!!]'  class='saisie-30emr' readonly='readonly' cols='50' rows='6' wrap='virtual'>!!adr_fac!!</textarea>&nbsp;
			</div>
			<div class='colonne_suite'>
				<input type='button' class='bouton_small' tabindex='1' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=coord&caller=!!form_name!!&param1=id_adr_fac[!!id_bibli!!]&param2=adr_fac[!!id_bibli!!]&id_bibli=!!id_bibli!!', 'selector'); \" />&nbsp;
				<input type='button' class='bouton_small' tabindex='1' value='X' onclick=\"document.getElementById('id_adr_fac[!!id_bibli!!]').value='0';document.getElementById('adr_fac[!!id_bibli!!]').value='';\" />
			</div>
		</div>
	</div>
</div>
";

