<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editeurs.tpl.php,v 1.56.4.1 2025/02/27 16:00:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $publisher_content_form, $collections_list_tpl, $publisher_replace_content_form, $msg;
global $charset;

// $publisher_content_form : form saisie éditeur
$publisher_content_form = "
!!element_ed_nom!!
!!element_ed_adr1!!
!!element_ed_adr2!!
<div id='el0Child_3' class='row'>
	!!element_ed_cp!!
	!!element_ed_ville!!
</div>
!!element_ed_pays!!
!!element_ed_web!!
!!element_lib_fou!!
!!element_ed_comment!!
!!concept_form!!
!!thumbnail_url_form!!
!!aut_pperso!!
<!-- aut_link -->
<div id='el0Child_8' movable='yes' class='row' title=\"".htmlentities($msg['136'], ENT_QUOTES, $charset)."\">
	!!liaisons_collections!!
</div>
";

$collections_list_tpl = "
<div id='el_0Parent' class='parent' >
	<h3>
        ".get_expandBase_button('el_0', 'categ_links')."
    	".$msg['136']."
    </h3>
</div>
<div id='el_0Child' class='child'>
    <!-- collections_list -->
</div>";

// $publisher_replace_content_form : form remplacement éditeur
$publisher_replace_content_form = "
<div class='row'>
	<label class='etiquette' for='par'>$msg[160]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50emr' id='ed_libelle' name='ed_libelle' value=\"\" completion=\"publishers\" autfield=\"ed_id\" autexclude=\"!!id!!\"
   	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=editeur&caller=publisher_replace&p1=ed_id&p2=ed_libelle&no_display=!!id!!', 'selector'); }\" />
	<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=editeur&caller=publisher_replace&p1=ed_id&p2=ed_libelle&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; \" />
	<input type='hidden' name='ed_id' id='ed_id'>
</div>
<div class='row'>
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";

