<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.tpl.php,v 1.9 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $form_dialog_note, $msg, $charset;

$form_dialog_note="
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

<form action=\"./empr.php?tab=request&lvl=list_dmde#fin\" method=\"post\" name=\"modif_notes\"> 
	<h3>".htmlentities($msg['demandes_note_liste'], ENT_QUOTES, $charset)."</h3>
	<input type='hidden' name='sub' id='sub' />
	<input type='hidden' name='idaction' id='idaction' value='!!idaction!!'/>
	<input type='hidden' name='iddemande' id='iddemande' value='!!iddemande!!'/>
	<input type='hidden' name='redirectto' id='redirectto' value='!!redirectto!!'/>
	<input type='hidden' name='idnote' id='idnote'/>
	<div id='dialog_wrapper'>
		!!dialog!!	
	</div>
	<textarea name='contenu_note'></textarea>
	<div>		
		<input type='checkbox' name='ck_vue' id='ck_vue' value='1' checked/>
		<label for='ck_vue' class='etiquette'>".$msg['demandes_note_vue']."</label>
	</div>	
	<input type='button' class='bouton' value='".$msg['demandes_note_add']."' onclick=\"this.form.sub.value='add_note';document.forms['modif_notes'].submit();\"/>
</form>
";

?>