<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_profile.tpl.php,v 1.8 2023/07/13 11:49:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $admin_explnum_checkbox_template, $admin_explnum_licence_quotation_variable_selector, $admin_explnum_licence_quotation_variable_selector_option;

$admin_explnum_checkbox_template = "
		<input type='checkbox' id='explnum_licence_profile_rights_!!admin_explnum_right_id!!' value='!!admin_explnum_right_id!!' !!admin_explnum_right_checked!! name='explnum_licence_profile_rights[]'/>
		<label for='explnum_licence_profile_rights_!!admin_explnum_right_id!!'>!!admin_explnum_right_label!!</label>
		";

$admin_explnum_licence_quotation_variable_selector = "
<select id='explnum_licence_quotation_variable_selector'>
	<option value=''>".$msg['dsi_docwatch_datasource_link_constructor_page_var']."</option>
	!!variable_selector_options!!
</select>
<script type='text/javascript'>
	function explnum_licence_add_variable_in_quotation() {
		var value = '{{ ' + this.selectedOptions[0].value + ' }}';
		var template_area = document.getElementById('explnum_licence_profile_quotation_rights');
		var curpos = template_area.selectionStart;
		var before = template_area.value.substr(0, curpos);
		var after = template_area.value.substr(curpos);
		template_area.value = before + value + after;
		template_area.focus();
		template_area.setSelectionRange(curpos + value.length, curpos + value.length);
		this.value = '';
	}
	document.getElementById('explnum_licence_quotation_variable_selector').addEventListener('change', explnum_licence_add_variable_in_quotation);
</script>";

$admin_explnum_licence_quotation_variable_selector_option = "
	<option value='!!option_value!!'>!!option_label!!</option>";