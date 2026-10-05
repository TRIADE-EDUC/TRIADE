<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_tpl.tpl.php,v 1.8 2021/02/01 14:03:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $serialcirc_tpl_content_form, $base_path, $msg;

$serialcirc_tpl_content_form = "
<script type='text/javascript' src='".$base_path."/javascript/serialcirc_tpl_diff.js'></script>
<script type='text/javascript' src='".$base_path."/javascript/circdiff_tpl_drop.js'></script>
<script type='text/javascript'>
	function insert_vars(theselector,dest){	
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;
	
		if(pmbDojo.aceManager.getEditor(dest.id)){
		    pmbDojo.aceManager.getEditor('piedpage').insert(selvars);
		} else {
			var start = dest.selectionStart;		   
			var start_text = dest.value.substring(0, start);
			var end_text = dest.value.substring(start);
			dest.value = start_text+selvars+end_text;
		}
	}
</script>
<!-- 	Format de la liste de circulation -->
<div class='row'>
	<label class='etiquette'>".$msg["serialcirc_tpl_format"]."</label>
</div>
<div class='row'>
	!!format_serialcirc!!
</div>			
<div class='row'>	
	<label class='etiquette' for='piedpage'>".$msg['serialcirc_diff_option_form_fiche_pied_page']."</label>!!fields_options!!
	<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('piedpage')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' >			
</div>
<div class='row'>
	<textarea type='text' name='piedpage' id='piedpage' class='saisie-50em' rows='4' cols='50' >!!pied_page!!</textarea>
</div>
<input type='hidden' id='id_tpl' name='id_tpl' value='!!id_tpl!!' />
<input type='hidden' id='order_tpl' name='order_tpl' value='!!order_tpl!!' />
<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
 	pmbDojo.aceManager.initEditor('piedpage');
</script>";
