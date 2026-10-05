<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.tpl.php,v 1.25.2.1 2025/01/21 16:16:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// templates pour la gestion des paniers

global $etagere_content_form, $msg, $etagere_constitution_content_form;

// template pour le form de création d'une étagère
$etagere_content_form = "
<div class='row'>
	<a href=# onClick=\"document.getElementById('history').src='./sort.php?action=0&caller=etagere'; document.getElementById('history').style.display='';return false;\" alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\">
		<img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'>
	</a>
	<input type='hidden' value='!!tri!!' name='tri'/>
	<span id='etagere_sort'>
		!!tri_name!!
	</span>
	<script type='text/javascript'>
		function getSort(id,name){
			document.forms.etagere_form.tri.value=id;
            if(typeof(reverse_html_entities) == 'function') {
                var name = document.createTextNode(reverse_html_entities(name));
            } else {
                var name = document.createTextNode(name);
            }
			var span = document.getElementById('etagere_sort');
			while(span.firstChild){
				span.removeChild(span.firstChild);
			}
			span.appendChild(name);
			
		}
	</script>
</div>
<div class='row'>
	<label class='etiquette' for='classementGen_!!object_type!!'>".$msg['etagere_classement_list']."</label>
</div>
<div class='row'>
	<select data-dojo-type='dijit/form/ComboBox' id='classementGen_!!object_type!!' name='classementGen_!!object_type!!'>
		!!classements_liste!!
	</select>
</div>
";

// template pour le form de constitution d'une étagère
$etagere_constitution_content_form = "
<div class='row'>
	!!constitution!!
</div>
<input type='hidden' name='idetagere' value='!!idetagere!!' />
";

