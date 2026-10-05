<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_form.tpl.php,v 1.12 2019/05/27 10:24:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $class_path, $encodage_fic_source, $form, $current_module, $msg, $charset;

require_once($class_path."/import/import_entities.class.php");

//Gestion de l'encodage du fichier à convertir (pour les fichiers unimarc iso)
if(isset($encodage_fic_source)){
	$_SESSION["encodage_fic_source"]=$encodage_fic_source;
}elseif(isset($_SESSION["encodage_fic_source"])){
	$encodage_fic_source=$_SESSION["encodage_fic_source"];
} else {
	$encodage_fic_source='';
}

// template pour le formulaire d'import
$form="
<form class='form-$current_module' name=\"import_form\" action=\"start_import.php?bidon=1\" method=\"post\" enctype=\"multipart/form-data\">
<h3>".$msg["ie_import_running"]."</h3>
<div class='form-contenu'>
<div class='row'>
	<div class='colonne3'>
		<label class='etiquette'>".$msg["ie_file_to_import"]." :</label>
		</div>
	<div class='colonne_suite'>
		<input type=\"file\" name=\"import_file\" class='saisie-80em'>
		</div>
	</div>
	<br />
<div class='row'>
".$msg["ie_import_msg1"]."<br />
".sprintf($msg["ie_import_msg2"],"convert".(defined("LOCATION")?"_".constant("LOCATION"):"").".fic")."<br />
".$msg["ie_import_msg3"]."<br />
".$msg["ie_import_msg4"]."

</div>
<br />
<div class='row'>
	<div class='colonne3'>
		<label class='etiquette'>$msg[ie_import_TypConversion]</label>
	</div>
	<div class='colonne_suite'>
		!!import_type!!
	</div>
</div>
<div class='row'>
	<div class='colonne3'>
		<label class=\"etiquette\" for=\"encodage_fic_source\" id=\"text_desc_encodage_fic_source\" name=\"text_desc_encodage_fic_source\">".htmlentities($msg["admin_import_encodage_fic_source"],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='colonne_suite'>
		".import_entities::get_encoding_selector()."
	</div>
</div>
<div class='row'> </div>
	</div>
<div class='row'>
	<input type=\"submit\" class='bouton' value=\"".$msg["ie_import_start"]."\">
	</div>
</form>
";
