<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export_form.tpl.php,v 1.10 2019/05/27 12:57:01 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
require_once("$include_path/templates/export_param.tpl.php");

global $form, $msg, $current_module;

// template pour le formulaire d'export
$form="
<script>
function show_list(selectBox) {
	for (i=0; i<selectBox.options.length; i++) {
		id=selectBox.options[i].value;
	    list=document.getElementById(\"dtypdoc\"+id);
	    list.style.display=\"none\";
	    list=document.getElementById(\"dstatut\"+id);
	    list.style.display=\"none\";
	}
	
	id=selectBox.options[selectBox.selectedIndex].value;
	list=document.getElementById(\"dtypdoc\"+id);
	list.style.display=\"block\";
	id=selectBox.options[selectBox.selectedIndex].value;
	list=document.getElementById(\"dstatut\"+id);
	list.style.display=\"block\";
}
</script>
<form class='form-$current_module' name=\"export_form\" action=\"start_export.php\" method=\"post\">
<h3>".$msg['admin_ExpPara']."</h3>
<div class='form-contenu'>
  <div class='row'>
	<div class='colonne3'>
		<label class='etiquette'>".$msg['admin_Expvers']."</label>
	</div>
	<div class='colonne_suite'>
		!!export_type!!
	</div>
  </div>
  <div class='row'>
    <div class='colonne3'>
		<label class='etiquette'>".$msg['admin_ExpProp']."</label>
	</div>
	<div class='colonne_suite'>
		!!lenders!!
	</div>
  </div>
  <br /><br />
  <div class='row'>
    <div class='colonne3'>
       <b>".$msg['17']."</b><br />
       <i>".$msg['admin_ExpSelect']."</i><br />
       !!typ_doc_lists!!
    </div>
    <div class='colonne10'>
       &nbsp;
    </div>
    <div class='colonne_suite'>
       <b>".$msg['admin_ExpStatut']."</b><br />
       <i>".$msg['admin_ExpSelect']."</i><br />
       !!statut_lists!!
    </div>
  </div>
  <div class='row'>
  	<input type=\"checkbox\" id=\"keep_expl\" name=\"keep_expl\" value=\"1\">
  	<label for=\"keep_expl\">".$msg['caddie_Conserver995']."</label>
  </div>
  <div class='row'>
	<input type='checkbox' value='1' id='keep_explnum' name='keep_explnum'> 
	<label for='keep_explnum'>".$msg['caddie_export_keep_explnum']."</label>
  </div>
  <div class='row'> !!form_param!!</div>
  
</div>

<div class='row'>
	<input type=\"submit\" class='bouton' value=\"".$msg['admin_Lance']."\">
	</div>
</form>
";
