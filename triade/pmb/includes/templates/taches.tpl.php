<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: taches.tpl.php,v 1.12 2024/03/12 13:13:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $planificateur_js_before_form, $planificateur_js_after_form;
global $subaction, $template_result, $base_path, $msg, $current_module;

if(!isset($subaction)) $subaction = '';

$template_result = "
<form name='planificateur_form_del' action='$base_path/admin.php?categ=planificateur&sub=manager&action=delete&id=!!id!!&confirm=1' method='post' class='form-$current_module'>
	<h3>".$msg["planificateur_task_type_task"]." : !!libelle_type_task!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			!!BODY!!
		</div>
	</div>
</form>
";

$planificateur_js_before_form="
<script type='text/javascript' src='./javascript/select.js'></script>
<script type='text/javascript' src='./javascript/upload.js'></script>
";

$planificateur_js_after_form="
<script type='text/javascript'>
	if (document.getElementById('radio_histo_day').checked) {
		document.getElementById('histo_day').disabled = false;
		document.getElementById('histo_number').disabled = true;
	} else {
		document.getElementById('histo_day').disabled = true;
		document.getElementById('histo_number').disabled = false;
	}
	function changeHisto(){
		if (document.getElementById('radio_histo_day').checked) {
			document.getElementById('histo_day').disabled = false;
			document.getElementById('histo_number').disabled = true;
		} else {
			document.getElementById('histo_day').disabled = true;
			document.getElementById('histo_number').disabled = false;
		}
	}
	function changePerio(node, i,chkbx_tab, nb_value){
		if ((i != '*')) { 
			if (document.getElementById(chkbx_tab+'_'+i).checked == true) {
				var nb=0;
				for (j=1; j<=nb_value; j++) {
					if (document.getElementById(chkbx_tab+'_'+j).checked) {
						nb++;
					}
				}
				if (nb == nb_value) {
					document.getElementById(chkbx_tab+'_0').checked = true;
				} else {
					document.getElementById(chkbx_tab+'_0').checked = false;
				}
			} else {
				document.getElementById(chkbx_tab+'_0').checked = false;
			}
		} else {
			if (document.getElementById(chkbx_tab+'_0').checked == true) {
				for (i=1; i<=nb_value; i++) {
					document.getElementById(chkbx_tab+'_'+i).checked = true;
				}	
			} else {
				var nb=0;
				if(node.id == chkbx_tab+'_0') {
					for (j=1; j<=nb_value; j++) {
						document.getElementById(chkbx_tab+'_'+j).checked = false;
					}
				} else {
					for (j=1; j<=nb_value; j++) {
						if (document.getElementById(chkbx_tab+'_'+j).checked) {
							nb++;
						}
					}
					if (nb == nb_value) {
						document.getElementById(chkbx_tab+'_0').checked = true;
					}
				}
			}
		}
	}
	</script>
";
