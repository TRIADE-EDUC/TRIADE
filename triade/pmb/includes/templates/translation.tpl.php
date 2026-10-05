<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translation.tpl.php,v 1.4 2019/05/27 13:47:15 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $translation_tpl_form_javascript, $translation_tpl_form, $translation_tpl_line_form, $translation_tpl_form_input_small_text, $translation_tpl_form_input_text;

//*******************************************************************
// Définition des templates 
//*******************************************************************
$translation_tpl_form_javascript = "
<script type='text/javascript'>
	function translation_view(item) {
		var elt = document.getElementById(item);
		if (elt.style.display == 'none') elt.style.display = ''; else elt.style.display = 'none'; 
	}
</script>
";

$translation_tpl_form = "
<div class='row'>
	<label class='etiquette' for='!!field_name!!'>!!label!!</label>
	!!translation_button!!
</div>
<div class='row'>
	!!translation_form_input!!!!translation_button_no_label!!
</div>
<!--	traductions-->
<div id='lang_!!field_id!!' class='!!class_form!!' style='!!style_form!!'>
	!!lang_list!!
</div>
";

$translation_tpl_line_form = "
	<div class='row'>
		<label class='etiquette'>!!libelle_lang!!</label>
	</div>
	<div class='row'>
		!!translation_form_line_input!!
	</div>
";

$translation_tpl_form_input_small_text = "
<input class='!!class_saisie!!' id='!!lang!!!!field_id!!' name='!!lang!!!!field_name!!' value='!!field_value!!' type='text'>";


$translation_tpl_form_input_text = "
<textarea class='!!class_saisie!!' id='!!lang!!!!field_id!!' name='!!lang!!!!field_name!!'>!!field_value!!</textarea>";
