<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.tpl.php,v 1.53.4.1 2025/02/28 13:38:19 dgoron Exp $

// templates pour gestion des autorités collections

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $collection_content_form, $sub_collection_content_form, $collection_replace_content_form, $sub_coll_rep_content_form, $msg;
global $charset, $id;

//	----------------------------------
// $collection_content_form : form saisie collection

$collection_content_form = "
<div id='el0Child_0' class='row'>
	!!element_collection_nom!!
	!!element_issn!!
	<div class='row'></div>
</div>
<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg[164], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='ed_libelle'>$msg[164]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' id='ed_libelle' name='ed_libelle' value=\"!!ed_libelle!!\" completion=\"publishers\" autfield=\"ed_id\" autexclude=\"!!id!!\"
	    onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector'); }\" />
	
		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; \" />
		<input type='hidden' name='ed_id' id='ed_id' value='!!ed_id!!' />
		<input type='hidden' name='dcoll_id' />
		<input type='hidden' name='dcoll_lib' />
		<input type='hidden' name='dsubcoll_id' />
		<input type='hidden' name='dsubcoll_lib' />
	</div>
</div>
!!element_collection_web!!
!!element_comment!!
!!concept_form!!
!!thumbnail_url_form!!
!!aut_pperso!!
<!-- aut_link -->
";

//	----------------------------------

// $sub_collection_content_form : form saisie sous collection
$sub_collection_content_form = "
<div id='el0Child_0' class='row'>
	!!element_collection_nom!!
	!!element_issn!!
	<div class='row'></div>
</div>
<div id='el0Child_1' class='row'>
	<!-- collparent -->
	<div id='el0Child_1_a' class='colonne2' movable='yes' title=\"".htmlentities($msg[179], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='coll_libelle'>$msg[179]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30emr' size='34' name='coll_libelle' id='coll_libelle' value=\"!!coll_libelle!!\"  completion='collections'  autfield='coll_id' linkfield='ed_id' callback='f_coll_id_callback' />
			<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=collection&caller=saisie_sub_collection&p1=ed_id&p2=ed_libelle&p3=coll_id&p4=coll_libelle&p5=dsubcoll_id&p6=dsubcoll_lib', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.coll_id.value='0'; this.form.ed_id.value='0'; \" />
			<input type='hidden' name='coll_id' id='coll_id' value='!!coll_id!!' />
			<input type='hidden' name='dsubcoll_id' />
			<input type='hidden' name='dsubcoll_lib' />
		</div>
	</div>        
	<!-- colledparent -->
	<div id='el0Child_1_b' class='colonne2' movable='yes' title=\"".htmlentities($msg[164], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='ed_libelle'>$msg[164]</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-30emr' size='34' name='ed_libelle' id='ed_libelle' readonly value=\"!!ed_libelle!!\" />
			<input type='hidden' name='ed_id' id='ed_id' value='!!ed_id!!' />
		</div>
	</div>
</div>
!!element_subcollection_web!!
!!element_comment!!
!!concept_form!!
!!thumbnail_url_form!!
!!aut_pperso!!
<!-- aut_link -->
";

// $collection_replace_content_form : form remplacement collection
$collection_replace_content_form = "
<script type='text/javascript'>
    function f_coll_id_callback() {
		ajax_get_entity('get_publisher', 'collection', document.getElementById('by').value, 'ed_id', 'ed_libelle');
	}
</script>
<div class='row'>
	<label class='etiquette' for='par'>$msg[160]</label>
</div>
<div class='row'>
	<label class='etiquette' for='par'>$msg[186]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30emr' name='coll_libelle' id='coll_libelle' data-form-name='coll_libelle' value='' completion='collections'  autfield='by' autexclude='!!id!!'  linkfield='ed_id' callback='f_coll_id_callback' />
	<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=collection&caller=coll_replace&p1=ed_id&p2=ed_libelle&p3=by&p4=coll_libelle&p5=dsubcoll_id&p6=dsubcoll_lib&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.by.value='0'; this.form.ed_id.value='';\" />
	<input type='hidden' name='by' id='by' value=''>
</div>
<div class='row'>
	<label class='etiquette' for='par'>$msg[164]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30emr' name='ed_libelle' id='ed_libelle' readonly value='' />
	<input type='hidden' name='dsubcoll_id'>
	<input type='hidden' name='dsubcoll_lib'>
	<input type='hidden' name='ed_id' id='ed_id' value=''>
</div>
<div class='row'>
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";

// $sub_coll_rep_content_form : form remplacement sous collection
$sub_coll_rep_content_form = "
<script type='text/javascript'>
<!--
    function f_sub_coll_id_callback() {
		ajax_get_entity('get_collection', 'sub_coll_nom', document.getElementById('by').value, 'coll_id', 'coll_libelle', 'ajax_get_entity_response');
	}
    document.body.addEventListener('ajax_get_entity_response', function(e) {
        ajax_get_entity('get_publisher', 'coll_libelle', document.getElementById('coll_id').value, 'ed_id', 'ed_libelle');
	});
-->
</script>
<div class='row'>
	<label class='etiquette' for='par'>$msg[160]</label>
</div>
<div class='row'>
	<label class='etiquette' for='par'>$msg[192]</label>
</div>
<div class='row'>
	<input type='text' name='sub_coll_nom'  id='sub_coll_nom' data-form-name='sub_coll_nom' class='saisie-30emr' value='' completion='subcollections' autfield='by' autexclude='!!id!!'  linkfield='ed_id' callback='f_sub_coll_id_callback'/>
	<input type='hidden' name='by' id='by' value=''>
	<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=subcollection&caller=saisie_sub_collection&p1=ed_id&p2=ed_libelle&p3=coll_id&p4=coll_libelle&p5=by&p6=sub_coll_nom', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.sub_coll_nom.value=''; this.form.coll_libelle.value=''; this.form.ed_libelle.value=''; this.form.ed_id.value=''; this.form.coll_id.value=''; this.form.by.value='0'; \" />
</div>
<div class='row'>
	<label class='etiquette' for='par'>$msg[179]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30emr' name='coll_libelle' id='coll_libelle' readonly value='' />
	<input type='hidden' name='coll_id' id='coll_id' value=''/>
</div>
<div class='row'>
	<label class='etiquette' for='par'>$msg[164]</label>
</div>
<div class='row'>
	<input type='text' class='saisie-30emr' name='ed_libelle' id='ed_libelle' readonly value='' />
	<input type='hidden' name='ed_id' id='ed_id' value=''>
</div>
<div class='row'>
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";

