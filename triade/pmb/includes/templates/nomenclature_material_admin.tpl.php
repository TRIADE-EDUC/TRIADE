<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_material_admin.tpl.php,v 1.4 2021/03/10 12:58:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nomenclature_material_form_tpl, $current_module, $msg, $charset;

$nomenclature_material_form_tpl="
<script type='text/javascript' src='javascript/ajax.js'></script>
<form class='form-".$current_module."' id='nomenclature_material_form' name='nomenclature_material_form'  method='post' action=\"admin.php?categ=material&sub=material&action=save\" >
	<div class='form-contenu'>
		<div class='row'>
			<label for='music_concept_before'>".htmlentities($msg['nomenclature_material_music_concept_before'],ENT_QUOTES,$charset)."</label>	
		</div>
		<div class='row'>
			<input id='music_concept_before' class='saisie-80emr' type='text' autocomplete='off' autfield='music_concept_before_value' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' completion='onto' value='!!music_concept_before_label!!' name='music_concept_before'>
			<input class='bouton' type='button' onclick='openPopUp(\"select.php?what=ontology&caller=nomenclature_material_form&param1=music_concept_before_value&param2=music_concept_before&element=concept\", \"selector_ontology\")' value='...'>
			<input id='music_concept_before_purge' class='bouton' type='button' onclick='document.getElementById(\"music_concept_before\").value = \"\"; document.getElementById(\"music_concept_before_value\").value = \"\";' value='X'>
			<input id='music_concept_before_value' type='hidden' value='!!music_concept_before_value!!' name='music_concept_before_value'>
			<input id='music_concept_before_type' type='hidden' value='http://www.w3.org/2004/02/skos/core#Concept' name='music_concept_before_type'>			
		</div>
		<div class='row'>
			<label for='music_concept_after'>".htmlentities($msg['nomenclature_material_music_concept_after'],ENT_QUOTES,$charset)."</label>	
		</div>			
		<div class='row'>
			<input id='music_concept_after' class='saisie-80emr' type='text' autocomplete='off' autfield='music_concept_after_value' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' completion='onto' value='!!music_concept_after_label!!' name='music_concept_after'>
			<input class='bouton' type='button' onclick='openPopUp(\"select.php?what=ontology&caller=nomenclature_material_form&param1=music_concept_after_value&param2=music_concept_after&element=concept\", \"selector_ontology\")' value='...'>
			<input id='music_concept_after_purge' class='bouton' type='button' onclick='document.getElementById(\"music_concept_after\").value = \"\"; document.getElementById(\"music_concept_after_value\").value = \"\";' value='X'>
			<input id='music_concept_after_value' type='hidden' value='!!music_concept_after_value!!' name='music_concept_after_value'>
			<input id='music_concept_after_type' type='hidden' value='http://www.w3.org/2004/02/skos/core#Concept' name='music_concept_after_type'>			
		</div>
		<div class='row'>
			<label for='music_concept_blank'>".htmlentities($msg['nomenclature_material_music_concept_blank'],ENT_QUOTES,$charset)."</label>	
		</div>			
		<div class='row'>
			<input id='music_concept_blank' class='saisie-80emr' type='text' autocomplete='off' autfield='music_concept_blank_value' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' completion='onto' value='!!music_concept_blank_label!!' name='music_concept_blank'>
			<input class='bouton' type='button' onclick='openPopUp(\"select.php?what=ontology&caller=nomenclature_material_form&param1=music_concept_blank_value&param2=music_concept_blank&element=concept\", \"selector_ontology\")' value='...'>
			<input id='music_concept_blank_purge' class='bouton' type='button' onclick='document.getElementById(\"music_concept_blank\").value = \"\"; document.getElementById(\"music_concept_blank_value\").value = \"\";' value='X'>
			<input id='music_concept_blank_value' type='hidden' value='!!music_concept_blank_value!!' name='music_concept_blank_value'>
			<input id='music_concept_blank_type' type='hidden' value='http://www.w3.org/2004/02/skos/core#Concept' name='music_concept_blank_type'>			
		</div>
		<div class='row'>
			<label for='music_children_relation'>".htmlentities($msg['nomenclature_material_music_children_relation'],ENT_QUOTES,$charset)."</label>	
		</div>			
		<div class='row'>
			!!music_children_relation_select!!
		</div>
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_instrument_form_save']."' />
		</div>
		<div class='right'>
		</div>
	</div>
	<div class='row'></div>
	<script type='text/javascript'>
		ajax_parse_dom();
	</script>
</form>		
";