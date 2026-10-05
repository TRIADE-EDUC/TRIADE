<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.tpl.php,v 1.15.4.1 2025/04/16 08:15:53 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $js_dialog_note, $msg, $content_dialog_note, $charset, $form_dialog_note, $form_table_note;

$js_dialog_note="
<script src='./javascript/tablist.js' type='text/javascript'></script>
<script type='text/javascript' src='./javascript/demandes_form.js'></script>
<script type='text/javascript'>
	var msg_demandes_note_confirm_demande_end='".addslashes($msg['demandes_note_confirm_demande_end'])."'; 
	var msg_demandes_actions_nocheck='".addslashes($msg['demandes_actions_nocheck'])."'; 
	var msg_demandes_confirm_suppr = '".addslashes($msg['demandes_confirm_suppr'])."';
	var msg_demandes_note_confirm_suppr = '".addslashes($msg['demandes_note_confirm_suppr'])."';
</script>
";

$content_dialog_note="
<div class='form_modif_notes'>
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='redirectto_!!idaction!!' id='redirectto_!!idaction!!' value='!!redirectto!!'/>
	<div id='dialog_wrapper'>
		!!dialog!!	
	</div>
	<textarea name='contenu_note_!!idaction!!'></textarea>
	<div>
		<input type='checkbox' name='ck_prive_!!idaction!!' id='ck_prive_!!idaction!!' value='1'/>
		<label for ='ck_prive_!!idaction!!' class='etiquette'>".$msg['demandes_note_privacy']."</label>	
		<input type='checkbox' name='ck_rapport_!!idaction!!' id='ck_rapport_!!idaction!!' value='1'/>
		<label for='ck_rapport_!!idaction!!' class='etiquette'>".$msg['demandes_note_rapport']."</label>
	</div>
	<input type='checkbox' name='ck_vue_!!idaction!!' id='ck_vue_!!idaction!!' value='1' checked/>
	<label for='ck_vue_!!idaction!!' class='etiquette'>".$msg['demandes_note_vue']."</label>
	<input type='submit' class='bouton' value='".$msg['demandes_note_add']."' onclick='!!change_action_form!!; setup_note_form_from_dialog(this.form, !!idaction!!); this.form.act.value=\"add_note\"'/>
</div>
";

$content_dialog_note_in_action="
<div class='form_modif_notes'>
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='idaction' id='idaction' value='!!idaction!!'/>
	<input type='hidden' name='redirectto' id='redirectto' value='!!redirectto!!'/>
	<input type='hidden' name='idnote' id='idnote'/>
	<div id='dialog_wrapper'>
		!!dialog!!	
	</div>
	<textarea name='contenu_note'></textarea>
	<div>
		<input type='checkbox' name='ck_prive' id='ck_prive' value='1'/>
		<label for ='ck_prive' class='etiquette'>".$msg['demandes_note_privacy']."</label>	
		<input type='checkbox' name='ck_rapport' id='ck_rapport' value='1'/>
		<label for='ck_rapport' class='etiquette'>".$msg['demandes_note_rapport']."</label>
	</div>
	<input type='checkbox' name='ck_vue' id='ck_vue' value='1' checked/>
	<label for='ck_vue' class='etiquette'>".$msg['demandes_note_vue']."</label>
	<input type='submit' class='bouton' value='".$msg['demandes_note_add']."' onclick='!!change_action_form!!this.form.act.value=\"add_note\"'/>
</div>
";

$form_dialog_note="
	<form action=\"./demandes.php?categ=notes#fin\" method=\"post\" name=\"modif_notes\"> 
	<input type='hidden' name='act' id='act' />
	".$content_dialog_note_in_action."	
	</form>
";

$form_table_note ="
<script src='./javascript/tablist.js' type='text/javascript'></script>
<script type='text/javascript'>
function confirm_delete()
{
	phrase = \"".$msg['demandes_note_confirm_suppr']."\";
	result = confirm(phrase);
	if(result){
		return true;
	}
	return false;
}
</script>
<form action=\"./demandes.php?categ=notes\" method=\"post\" name=\"modif_notes\" onsubmit=\"if(document.forms['modif_notes'].act.value == 'suppr_note') return confirm_delete();\"> 
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='act' id='act' />
	<input type='hidden' name='idaction' id='idaction' value='!!idaction!!'/>
	<input type='hidden' name='idnote' id='idnote'/>
	<div class='form-contenu'>
		!!liste_notes!!
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='".$msg['demandes_note_add']."' onclick='this.form.act.value=\"add_note\"'/>
	</div>
</form>
";

