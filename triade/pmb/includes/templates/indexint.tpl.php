<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.tpl.php,v 1.46.10.1 2025/02/27 15:41:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $indexint_content_form;
global $indexint_replace_content_form;
global $msg, $charset;

$indexint_content_form = "
!!element_indexint_pclassement!!
!!element_indexint_nom!!
!!element_indexint_comment!!
!!concept_form!!
!!thumbnail_url_form!!
!!aut_pperso!!
<!-- aut_link -->
";

// $indexint_replace_content_form : form remplacement Indexation interne
$indexint_replace_content_form = "
<div class='row'>
	<label class='etiquette' for='par'>$msg[160]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50emr' id='indexint_libelle' name='indexint_libelle' value=\"\" completion=\"indexint\" autfield=\"n_indexint_id\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=indexint&caller=indexint_replace&param1=n_indexint_id&param2=indexint_libelle&no_display=!!id!!&id_pclass=!!id_pclass!!', 'selector'); }\" />

	<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=indexint&caller=indexint_replace&param1=n_indexint_id&param2=indexint_libelle&no_display=!!id!!&id_pclass=!!id_pclass!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.indexint_libelle.value=''; this.form.n_indexint_id.value='0'; \" />
	<input type='hidden' name='n_indexint_id' id='n_indexint_id' value='0' />
</div>
<div class='row'>		
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";

