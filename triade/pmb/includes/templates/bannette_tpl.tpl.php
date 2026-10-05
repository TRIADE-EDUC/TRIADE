<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.tpl.php,v 1.8 2019/05/27 14:47:21 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $bannette_tpl_content_form, $msg;

$bannette_tpl_content_form = "
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
		    pmbDojo.aceManager.getEditor('content').insert(selvars);
		} else {
			if(typeof(tinyMCE)== 'undefined'){
				var start = dest.selectionStart;
			    var start_text = dest.value.substring(0, start);
			    var end_text = dest.value.substring(start);
			    dest.value = start_text+selvars+end_text;
			}else{
				tinyMCE_execCommand('mceInsertContent',false,selvars);
			}
		}
	}
</script>
<div class='row'>	
	<label class='etiquette' for='content'>".$msg['template_content']."</label>!!fields_options!!
	<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('content')); return false; \" value=' ".$msg['template_insert']." ' >			
</div>
<div class='row'>
	<textarea type='text' name='content' id='content' class='saisie-50em' rows='20' cols='50' >!!content!!</textarea>
</div>
<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
 	pmbDojo.aceManager.initEditor('content');
</script>";