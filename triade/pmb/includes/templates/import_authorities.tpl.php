<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_authorities.tpl.php,v 1.13 2019/05/27 13:34:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $encodage_fic_source, $authorites_import_form, $base_path, $authorites_import_form_content, $charset, $msg, $thesaurus_concepts_active, $authorities_import_preload_form, $current_module, $authorities_import_afterupload_form;

//Gestion de l'encodage du fichier d'import
if(isset($encodage_fic_source)){
	$_SESSION["encodage_fic_source"]=$encodage_fic_source;
}elseif(isset($_SESSION["encodage_fic_source"])){
	$encodage_fic_source=$_SESSION["encodage_fic_source"];
}else{
	$encodage_fic_source='';
}

$authorites_import_form ="
	<iframe src='".$base_path."/autorites/import/iimport_authorities.php' name='iimport_authorities' style='width:100%;height:650px;border:none;'/>";

$authorites_import_form_content = "
	<form class='form-$current_module' enctype='multipart/form-data' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<h3>".htmlentities($msg['import_authorities'],ENT_QUOTES,$charset)."</h3>
		<div class='form-contenu'>
			<div class='row'>
				<label class='etiquette' for='authorities_type'>".htmlentities($msg['import_authorities_type'],ENT_QUOTES,$charset)."</label><br />
				<select name='authorities_type'>
					<option value='all'>".htmlentities($msg['import_authorities_type_all'],ENT_QUOTES,$charset)."</option>
					<option value='author'>".htmlentities($msg['import_authorities_type_author'],ENT_QUOTES,$charset)."</option>
					<option value='uniform_title'>".htmlentities($msg['import_authorities_type_uniform_title'],ENT_QUOTES,$charset)."</option>
					<option value='collection'>".htmlentities($msg['import_authorities_type_collection'],ENT_QUOTES,$charset)."</option>
					<option id='category_option' value='category' ".($thesaurus_concepts_active ? "style='display:none;'" : "").">".htmlentities($msg['import_authorities_type_category'],ENT_QUOTES,$charset)."</option>
					<option value='subcollection'>".htmlentities($msg['import_authorities_type_subcollection'],ENT_QUOTES,$charset)."</option>";
if ($thesaurus_concepts_active) {
$authorites_import_form_content.= "
					<option id='concept_option' value='concept'>".htmlentities($msg['import_authorities_type_concept'],ENT_QUOTES,$charset)."</option>";
}
$authorites_import_form_content.= "
				</select>
			</div>
			<div class='row'>&nbsp;</div>";
if ($thesaurus_concepts_active) {
	$authorites_import_form_content.= "
			<div class='row'>
			<label class='etiquette'>".htmlentities($msg['import_authorities_category_or_concept'], ENT_QUOTES, $charset)."</label><br />
				<input type='radio' name='category_or_concept' id='category_or_concept_concept' value='concept' checked='checked'/>
				".htmlentities($msg['import_authorities_type_concept'], ENT_QUOTES, $charset)."
				<input type='radio' name='category_or_concept' id='category_or_concept_category' value='category'/>
				".htmlentities($msg['import_authorities_type_category'], ENT_QUOTES, $charset)."
			</div>
			<div class='row'>&nbsp;</div>
			<div id='import_concept_block' class='row'>
				<label class='etiquette' for='scheme_uri'>".htmlentities($msg['import_authorities_scheme'], ENT_QUOTES, $charset)."</label><br />
				!!schemes!!
			</div>
			<script type='text/javascript'>
				document.getElementById('category_or_concept_concept').addEventListener('click', function(){
					if (this.checked) {
						document.getElementById('import_concept_block').style = '';
						document.getElementById('concept_option').style = '';
						document.getElementById('import_category_block').style = 'display:none;';
						document.getElementById('category_option').style = 'display:none;';
					}
				});
				document.getElementById('category_or_concept_category').addEventListener('click', function(){
					if (this.checked) {
						document.getElementById('import_category_block').style = '';
						document.getElementById('category_option').style = '';
						document.getElementById('import_concept_block').style = 'display:none;';
						document.getElementById('concept_option').style = 'display:none;';
					}
				});
			</script>";
}
$authorites_import_form_content.= "
			<div id='import_category_block' class='row' ".($thesaurus_concepts_active ? "style='display:none;'" : "").">
				<label class='etiquette' for='id_authority'>".htmlentities($msg['import_authorities_thesaurus'], ENT_QUOTES, $charset)."</label><br />
				!!thesaurus!!
			</div>";
if ($thesaurus_concepts_active) {
	$authorites_import_form_content.= "
			<script type='text/javascript'>
				document.getElementById('import_category_block').style = 'display:none;';
				document.getElementById('category_option').style = 'display:none;';
			</script>";
}
$authorites_import_form_content.= "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='type_link'>".htmlentities($msg['import_authorities_type_link_subcollection'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='type_link[subcollection]' value='1' />".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='type_link[subcollection]' value='0' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='create_link'>".htmlentities($msg['import_authorities_create_link'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='create_link' value='1' />".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='create_link' value='0' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div class='row'>
				<label class='etiquette' for='type_link'>".htmlentities($msg['import_authorities_type_link'],ENT_QUOTES,$charset)."</label><br />
				<input type='checkbox' name='type_link[rejected]' value='1' checked='checked'/>".htmlentities($msg['import_authorities_type_link_rejected'],ENT_QUOTES,$charset)."<br />
				<input type='checkbox' name='type_link[associated]' value='1' checked='checked'/>".htmlentities($msg['import_authorities_type_link_associated'],ENT_QUOTES,$charset)."
			</div> 
			<div class='row'>
				<label class='etiquette' for='create_link_spec'>".htmlentities($msg['import_authorities_create_link_spec'],ENT_QUOTES,$charset)."</label><br />
				<!--<input type='radio' name='create_link_spec' value='0' checked='checked'/><br />-->
				<input type='radio' name='create_link_spec' value='1' checked='checked'/>".htmlentities($msg['import_authorities_create_link_internal'],ENT_QUOTES,$charset)."<br />
				<input type='radio' name='create_link_spec' value='2' />".htmlentities($msg['import_authorities_create_link_all'],ENT_QUOTES,$charset)."<br />
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='force_update'>".htmlentities($msg['import_authorities_force_update'],ENT_QUOTES,$charset)."</label><br />
				<input type='radio' name='force_update' value='1' title='".htmlentities($msg['import_authorities_force_update_yes'],ENT_QUOTES,$charset)."'/>".htmlentities($msg[40],ENT_QUOTES,$charset)." <input type='radio' name='force_update' value='0' title='".htmlentities($msg['import_authorities_force_update_no'],ENT_QUOTES,$charset)."' checked='checked'/>".htmlentities($msg[39],ENT_QUOTES,$charset)."
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' for='userfile'>".htmlentities($msg[501],ENT_QUOTES,$charset)."</label><br />
				<input type='file' size='60' class='saisie-80em' name='userfile'/>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class=\"etiquette\" for=\"encodage_fic_source\" id=\"text_desc_encodage_fic_source\" name=\"text_desc_encodage_fic_source\">".htmlentities($msg["admin_import_encodage_fic_source"],ENT_QUOTES,$charset)."</label></br>
				!!import_encoding_selector!!
			</div>
		</div>
		<div class='row'>
			<input type='hidden' name='action' value='upload'/>
			<input type='submit' class='bouton' value='".htmlentities($msg['502'],ENT_QUOTES,$charset)."'/> 
		</div>
	</form>";

$authorities_import_preload_form = "
	<form class='form-$current_module' name='afterupload' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<input name='action' type='hidden' value=\"load\" />
		<input type='hidden' name='file_submit' value='!!file_submit!!' />
		<input type='hidden' name='from_file' value='!!from_file!!' />
		<input type='hidden' name='create_link' value='!!create_link!!' />
		<input type='hidden' name='create_link_spec' value='!!create_link_spec!!' />
		<input type='hidden' name='force_update' value='!!force_update!!' />
 		<input type='hidden' name='reload' value='!!reload!!' />
 		<input type='hidden' name='authorities_type' value='!!authorities_type!!' />
 		<input type='hidden' name='category_or_concept' value='!!category_or_concept!!' />
 		<input type='hidden' name='type_link' value='!!type_link!!' />
 		<input type='hidden' name='id_thesaurus' value='!!id_thesaurus!!' />
 		<input type='hidden' name='scheme_uri' value='!!scheme_uri!!' />
 		<input type='hidden' name='encodage_fic_source' value='!!encodage_fic_source!!' />
	</form>
	<script>setTimeout(\"document.afterupload.submit()\",1000);</script>";


$authorities_import_afterupload_form = "
	<form class='form-$current_module' name='import' method='post' action='".$base_path."/autorites/import/iimport_authorities.php'>
		<input name='action' type='hidden' value=\"import\" />
		<input type='hidden' name='file_submit' value='!!file_submit!!' />
		<input type='hidden' name='from_file' value='!!from_file!!' />
		<input type='hidden' name='create_link' value='!!create_link!!' />
		<input type='hidden' name='create_link_spec' value='!!create_link_spec!!' />
		<input type='hidden' name='force_update' value='!!force_update!!' />
		<input type='hidden' name='total' value='!!total!!' />
		<input type='hidden' name='nb_notices' value='!!nb_notices!!' />
		<input type='hidden' name='nb_notices_import' value='!!nb_notices_import!!' />
		<input type='hidden' name='nb_notices_rejetees' value='!!nb_notices_rejetees!!' />
		<input type='hidden' name='authorities_type' value='!!authorities_type!!' />
		<input type='hidden' name='category_or_concept' value='!!category_or_concept!!' />
		<input type='hidden' name='type_link' value='!!type_link!!' />
		<input type='hidden' name='id_thesaurus' value='!!id_thesaurus!!' />
		<input type='hidden' name='scheme_uri' value='!!scheme_uri!!' />
		<input type='hidden' name='encodage_fic_source' value='!!encodage_fic_source!!' />
	</form>
	<script>setTimeout(\"document.import.submit()\",1000);</script>";

