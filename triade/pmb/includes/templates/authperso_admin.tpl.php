<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_admin.tpl.php,v 1.14 2023/11/15 07:50:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $authperso_js_form_tpl;
global $pmb_javascript_office_editor;

$authperso_js_form_tpl="
<script type='text/javascript'>
	var javascript_office_editor_cleaned = '".strip_empty_chars($pmb_javascript_office_editor)."';
	function insert_vars(theselector,dest){
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;
		if(typeof(tinyMCE)== 'undefined' || parseInt(javascript_office_editor_cleaned.indexOf(dest.id)) == -1){
			var start = dest.selectionStart;
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE_execCommand('mceInsertContent',false,selvars);
		}
	}
</script>
";