<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes.tpl.php,v 1.91.4.1 2025/02/28 14:10:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $titre_uniforme_content_form, $oeuvre_expression_tpl, $oeuvre_expression_tpl_first, $oeuvre_expression_tpl_other, $other_link_tpl, $other_link_tpl_first, $other_link_tpl_other;
global $tu_authors_tpl, $tu_authors_all_tpl, $titre_uniforme_replace, $user_query_tpl, $oeuvre_event_tpl, $oeuvre_event_tpl_first, $oeuvre_event_tpl_other;
global $oeuvre_expression_from_tpl, $oeuvre_expression_from_tpl_first, $oeuvre_expression_from_tpl_other, $tu_notices_tpl, $tu_notices_tpl_first, $tu_notices_tpl_other;
global $pmb_authors_qualification, $pmb_autorites_verif_js, $value_deflt_fonction, $mapping_dojo_inclusion_tu, $base_path, $msg, $current_module, $pmb_form_authorities_editables;
global $aut_fonctions;


$titre_uniforme_content_form = "
<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_type"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='oeuvre_type'>".$msg["aut_oeuvre_form_oeuvre_type"]."</label>
	</div>
	<div class='row'>
		!!oeuvre_type!!
	</div>
</div>
<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_nature"], ENT_QUOTES, $charset)."\">			
	<div class='row'>
		<label class='etiquette' for='oeuvre_nature'>".$msg["aut_oeuvre_form_oeuvre_nature"]."</label>
	</div>
	<div class='row'>
		!!oeuvre_nature!!
	</div>
</div>
<!--	nom	-->
<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_nom"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_nom'>".$msg["aut_titre_uniforme_form_nom"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='form_nom' name='tu_name' value=\"!!nom!!\" data-form-name='tu_name' data-pmb-deb-rech='1'/>
	</div>
</div>
<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_expression"], ENT_QUOTES, $charset)."\">				
	<div class='row'>
		<label class='etiquette' >".$msg["aut_oeuvre_form_oeuvre_expression"]."</label>
	</div>
	<div class='row'>
		!!oeuvre_expression!!
	</div>	
</div>
<div id='el0Child_25' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_expression_from"], ENT_QUOTES, $charset)."\">				
	<div class='row'>
		<label class='etiquette' >".$msg["aut_oeuvre_form_oeuvre_expression_from"]."</label>
	</div>
	<div class='row'>
		!!oeuvre_expression_from!!
	</div>	
</div>
<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_other_link"], ENT_QUOTES, $charset)."\">				
	<div class='row'>
		<label class='etiquette' >".$msg["aut_oeuvre_form_other_link"]."</label>
	</div>
	<div class='row'>
	!!other_link!!
	</div>
</div>
<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_oeuvre_event"], ENT_QUOTES, $charset)."\">				
	<div class='row'>
		<label class='etiquette' >".$msg["aut_oeuvre_form_oeuvre_event"]."</label>
	</div>
	<div class='row'>			
		!!oeuvre_event!!
	</div>
</div>
!!authors!!

<!--	Forme de l'oeuvre	-->
<div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_forme"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_form'>".$msg["aut_oeuvre_form_forme"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='form_form' name='tu_form' data-form-name='tu_form' value='!!tu_form!!'>
	</div>	
</div>

<!--	Forme de l'oeuvre liste controlée -->
<div id='el0Child_7' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_forme_list"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_form'>".$msg["aut_oeuvre_form_forme_list"]."</label>
	</div>
	<div class='row'>
		<input type='text' completion='music_form' autfield='form_form_selector' id='music_form' class='saisie-30emr' name='music_form' data-form-name='music_form' value=\"!!music_form!!\" />
        <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=music_form&caller=saisie_titre_uniforme&p1=form_form_selector&p2=music_form&deb_rech='+".pmb_escape()."(this.form.music_form.value), 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.music_form.value=''; this.form.form_form_selector.value=''; \" />
        <input type='hidden' name='form_form_selector' data-form-name='form_form_selector' id='form_form_selector' value=\"!!music_form_id!!\" />  
	</div>
</div>

<!--	Date de l'oeuvre	-->
<div id='el0Child_8' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_date"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_dates'>".$msg["aut_oeuvre_form_date"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='form_dates' name='date' data-form-name='date' value='!!date!!'>
	</div>
</div>

<!--	Lieu d'origine de l'oeuvre	-->
<div id='el0Child_9' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_lieu"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_place'>".$msg["aut_oeuvre_form_lieu"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='form_place' name='place' data-form-name='place' value='!!place!!'>
	</div>
</div>

<!--	Sujet de l'oeuvre	-->
<div id='el0Child_10' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_sujet"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_subject'>".$msg["aut_oeuvre_form_sujet"]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='form_subject' name='subject' data-form-name='subject' cols='62' rows='4' wrap='virtual'>!!subject!!</textarea>
	</div>
</div>

<!--	Complétude visée de l'oeuvre	-->
<div id='el0Child_11' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_completude"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_completude'>".$msg["aut_oeuvre_form_completude"]."</label>
	</div>
	<div class='row'>
		<select id='form_intended_termination' name='intended_termination' data-form-name='intended_termination' class='saisie-20em'>
			<option value='0' !!intended_termination_0!!>--</option>\n
			<option value='1' !!intended_termination_1!!>".$msg['aut_oeuvre_form_completude_finished']."</option>\n
			<option value='2' !!intended_termination_2!!>".$msg['aut_oeuvre_form_completude_infinite']."</option>\n
		</select>	
	</div>
</div>

<!--	Public visé de l'oeuvre	-->
<div id='el0Child_12' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_public"], ENT_QUOTES, $charset)."\">		
	<div class='colonne_suite'>
		<label class='etiquette' for='form_intended_audience'>".$msg["aut_oeuvre_form_public"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30em' id='form_intended_audience' name='intended_audience' data-form-name='intended_audience' value='!!intended_audience!!'>
	</div>
</div>

<!--	Histoire de l'oeuvre	-->
<div id='el0Child_13' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_histoire"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_history'>".$msg["aut_oeuvre_form_histoire"]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='form_history' name='history'  data-form-name='history' cols='62' rows='4' wrap='virtual'>!!history!!</textarea>
	</div>
</div>

<!--	Contexte de l'oeuvre	-->
<div id='el0Child_14' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_contexte"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_context'>".$msg["aut_oeuvre_form_contexte"]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='form_context' name='context' data-form-name='context' cols='62' rows='4' wrap='virtual'>!!context!!</textarea>
	</div>
</div>
				
<div id='el0Child_15' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_distribution"], ENT_QUOTES, $charset)."\">					
	<!--	Distribution instrumentale et vocale (pour la musique)	-->
</div>
		
<div id='el0Child_16' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_ref_numerique"], ENT_QUOTES, $charset)."\">
	<!--	Référence numérique (pour la musique)	-->
</div>
		
<!--	Tonalité (Saisie Libre)	-->
<div id='el0Child_17' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_tonalite"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_tonalite'>".$msg["aut_titre_uniforme_form_tonalite"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='form_tonalite' name='tonalite' value='!!tonalite!!'>
	</div>
</div>

<!--	Tonalité (Liste controlée)	-->
<div id='el0Child_18' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_tonalite_list"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_tonalite'>".$msg["aut_titre_uniforme_form_tonalite_list"]."</label>
	</div>
	<div class='row'>
		<input type='text' completion='music_key' autfield='form_tonalite_selector' id='music_key' class='saisie-30emr' name='music_key' data-form-name='music_key' value=\"!!music_key!!\" />
        <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=music_key&caller=saisie_titre_uniforme&p1=form_tonalite_selector&p2=music_key&deb_rech='+".pmb_escape()."(this.form.music_key.value), 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.music_key.value=''; this.form.form_tonalite_selector.value=''; \" />
        <input type='hidden' name='form_tonalite_selector' data-form-name='form_tonalite_selector' id='form_tonalite_selector' value=\"!!music_key_id!!\" />  
	</div>
</div>

<!--	Coordonnées (oeuvre cartographique)	-->
<div id='el0Child_19' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_coordonnees"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_coordinates'>".$msg["aut_oeuvre_form_coordonnees"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='form_coordinates' name='coordinates' data-form-name='coordinates' value='!!coordinates!!'>
	</div>
</div>
		
<!--	Equinoxe (oeuvre cartographique)	-->
<div id='el0Child_20' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_equinoxe"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_equinox'>".$msg["aut_oeuvre_form_equinoxe"]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='form_equinox' name='equinox' data-form-name='equinox' value='!!equinox!!'>
	</div>
</div>

<div id='el0Child_21' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_form_subdivision_forme"], ENT_QUOTES, $charset)."\">
	<!-- Subdivision de forme -->
</div>
		
<!--	Autres caractéristiques distinctives de l'oeuvre	-->
<div id='el0Child_22' class='row' movable='yes' title=\"".htmlentities($msg["aut_oeuvre_form_caracteristique"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='form_carac'>".$msg["aut_oeuvre_form_caracteristique"]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='form_carac' name='characteristic' cols='62' rows='4' wrap='virtual'>!!characteristic!!</textarea>
	</div>
</div>
		
<!-- 	Commentaire -->
<div id='el0Child_23' class='row' movable='yes' title=\"".htmlentities($msg["aut_titre_uniforme_commentaire"], ENT_QUOTES, $charset)."\">
	<div class='row'>
		<label class='etiquette' for='comment'>".$msg["aut_titre_uniforme_commentaire"]."</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='comment' name='comment' data-form-name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
	</div>
</div>
				
!!concept_form!!
!!thumbnail_url_form!!				
!!aut_pperso!!
				
<div id='el0Child_24' class='row' movable='yes' title=\"".htmlentities($msg["authority_import_denied"], ENT_QUOTES, $charset)."\">	
	<div class='row'>
		<label class='etiquette' for='tu_import_denied'>".$msg['authority_import_denied']."</label> &nbsp;
		<input type='checkbox' id='tu_import_denied' name='tu_import_denied' value='1' data-form-name='tu_import_denied' !!tu_import_denied!!/>
	</div>
</div>

<!-- aut_link -->

<!-- tu_notices --> 		
<div id='el0Child_26' class='row' movable='yes' title=\"".htmlentities($msg["notice_relations"], ENT_QUOTES, $charset)."\">				
	!!tu_notices!!
</div>

";

$oeuvre_expression_tpl="
<input type='hidden' name='tab_oeuvre_expression_order' id='tab_oeuvre_expression_order' value='!!tab_oeuvre_expression_order!!' />
<input type='hidden' id='oeuvre_expression_new_order' name='oeuvre_expression_new_order' value=\"!!oeuvre_expression_new_order!!\" />
<script type='text/javascript'>

function fonction_selecteur_oeuvre_expression() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,19)+'_code'+name.substr(19);
	openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name, 'selector');
}
function add_oeuvre_expression() {
	templates.add_completion_qualified_field('f_oeuvre_expression', 'f_oeuvre_expression_code', 'titre_uniforme', 'f_oeuvre_expression_type', 'oeuvre_expression');

   	var element_name = 'oeuvre_expression';
    var new_order_element=document.getElementById(element_name+'_new_order');
	var new_order=parseInt(new_order_element.value)+1;
	new_order_element.value=new_order;
    var tab_oeuvre_expression_order = document.getElementById('tab_oeuvre_expression_order');
	tab_oeuvre_expression_order.value = tab_oeuvre_expression_order.value + ',' + new_order;
	init_drag();
	ajax_pack_element(document.getElementById('f_oeuvre_expression'+new_order));
	return true;
}

</script>";

$oeuvre_expression_tpl_first = "
<input type='button' class='bouton' value='$msg[parcourir]' 
	onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&field_id=f_oeuvre_expression_code&field_name_id=f_oeuvre_expression&dyn=3&max_field=max_oeuvre_expression&add_field=add_oeuvre_expression&callback=formMapperCallback', 'selector')\" />
<input type='button' class='bouton' value='+' onClick=\"add_oeuvre_expression();\"/>	
<div id='f_oeuvre_expression!!ioeuvre_expression!!' class='row' dragtype='oeuvre_expression' draggable='yes' recept='yes' recepttype='oeuvre_expression' handler='f_oeuvre_expression!!ioeuvre_expression!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!oeuvre_expression!!' downlight=\"oeuvre_expression_downlight\" highlight=\"oeuvre_expression_highlight\" order='!!ioeuvre_expression!!'  style='' >
 	<span id='f_oeuvre_expression!!ioeuvre_expression!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
	!!expression_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_expression!!ioeuvre_expression!!_display_label' name='f_oeuvre_expression!!ioeuvre_expression!!' value=\"!!oeuvre_expression!!\" completion=\"titre_uniforme\" autfield=\"f_oeuvre_expression_code!!ioeuvre_expression!!\" callback='formMapperCallback' data-form-name='f_oeuvre_expression' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_expression!!ioeuvre_expression!!.value=''; this.form.f_oeuvre_expression_code!!ioeuvre_expression!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_expression_code!!ioeuvre_expression!!'  data-form-name='f_oeuvre_expression_code'  id='f_oeuvre_expression_code!!ioeuvre_expression!!' value='!!oeuvre_expression_code!!' />
	!!button_add_oeuvre_expression!!
</div>
";
$oeuvre_expression_tpl_other = "
<div id='f_oeuvre_expression!!ioeuvre_expression!!' class='row' dragtype='oeuvre_expression' draggable='yes' recept='yes' recepttype='oeuvre_expression' handler='f_oeuvre_expression!!ioeuvre_expression!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!oeuvre_expression!!' downlight=\"oeuvre_expression_downlight\" highlight=\"oeuvre_expression_highlight\" order='!!ioeuvre_expression!!'  style='' >
 	<span id='f_oeuvre_expression!!ioeuvre_expression!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
	!!expression_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_expression!!ioeuvre_expression!!_display_label' name='f_oeuvre_expression!!ioeuvre_expression!!' value=\"!!oeuvre_expression!!\" completion=\"titre_uniforme\" autfield=\"f_oeuvre_expression_code!!ioeuvre_expression!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_expression!!ioeuvre_expression!!.value=''; this.form.f_oeuvre_expression_code!!ioeuvre_expression!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_expression_code!!ioeuvre_expression!!' id='f_oeuvre_expression_code!!ioeuvre_expression!!' value='!!oeuvre_expression_code!!' />
	!!button_add_oeuvre_expression!!
</div>
";


$other_link_tpl="
<input type='hidden' name='tab_other_link_order' id='tab_other_link_order' value='!!tab_other_link_order!!' />
<input type='hidden' id='other_link_new_order' name='other_link_new_order' value=\"!!other_link_new_order!!\" />
<script type='text/javascript'>

function fonction_selecteur_other_link() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,12)+'_code'+name.substr(12);
	openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name, 'selector');
}
function add_other_link() {
	templates.add_completion_qualified_field('f_other_link', 'f_other_link_code', 'titre_uniforme', 'f_oeuvre_other_link', 'other_link');

	var element_name = 'other_link';
    var new_order_element=document.getElementById(element_name+'_new_order');
	var new_order=parseInt(new_order_element.value)+1;
	new_order_element.value=new_order;
    var tab_other_link_order = document.getElementById('tab_other_link_order');
	tab_other_link_order.value = tab_other_link_order.value + ',' + new_order;
	init_drag();
	ajax_pack_element(document.getElementById('f_other_link'+new_order));
	return true;
}

</script>";

$other_link_tpl_first = "
<input type='button' class='bouton' value='$msg[parcourir]' 
	onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&field_id=f_other_link_code&field_name_id=f_other_link&dyn=3&max_field=max_other_link&add_field=add_other_link&myid=!!myid!!', 'selector')\" />
<input type='button' class='bouton' value='+' onClick=\"add_other_link();\"/>	
<div id='f_other_link!!iother_link!!' class='row' dragtype='other_link' draggable='yes' recept='yes' recepttype='other_link' handler='f_other_link!!iother_link!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!other_link!!' downlight=\"other_link_downlight\" highlight=\"other_link_highlight\" order='!!iother_link!!'  style='' >
 	<span id='f_other_link!!iother_link!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
	!!link_type!!
	<input type='text' class='saisie-30emr' id='f_other_link!!iother_link!!_display_label' data-form-name='f_other_link' name='f_other_link!!iother_link!!' value=\"!!other_link!!\" completion=\"titre_uniforme\" autfield=\"f_other_link_code!!iother_link!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_other_link!!iother_link!!.value=''; this.form.f_other_link_code!!iother_link!!.value=''; \" />
	<input type='hidden' name='f_other_link_code!!iother_link!!' data-form-name='f_other_link_code' id='f_other_link_code!!iother_link!!' value='!!other_link_code!!' />
	!!button_add_other_link!!
</div>
";
$other_link_tpl_other = "
<div id='f_other_link!!iother_link!!' class='row' dragtype='other_link' draggable='yes' recept='yes' recepttype='other_link' handler='f_other_link!!iother_link!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!other_link!!' downlight=\"other_link_downlight\" highlight=\"other_link_highlight\" order='!!iother_link!!'  style='' >
 	<span id='f_other_link!!iother_link!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
    !!link_type!!
	<input type='text' class='saisie-30emr' id='f_other_link!!iother_link!!_display_label' name='f_other_link!!iother_link!!' value=\"!!other_link!!\" completion=\"titre_uniforme\" autfield=\"f_other_link_code!!iother_link!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_other_link!!iother_link!!.value=''; this.form.f_other_link_code!!iother_link!!.value=''; \" />
	<input type='hidden' name='f_other_link_code!!iother_link!!' id='f_other_link_code!!iother_link!!' value='!!other_link_code!!' />
	!!button_add_other_link!!
</div>
";
if(!$pmb_authors_qualification) 
$tu_authors_tpl="
<div class='row'>
<!--	Auteurs de l'oeuvre	-->
<div style='float:left;margin-right:10px;'>
	<div class='row'>
        <label for='f_aut!!n!!' class='etiquette' style='!!title_display!!'>!!title!!</label>	
		<input type='button' style='!!bouton_add_display!!' class='bouton' value='+' onClick=\"add_aut(!!n!!);\"/>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut!!n!!_id!!iaut!!' id='f_aut!!n!!!!iaut!!' name='f_aut!!n!!!!iaut!!' data-form-name='f_aut!!n!!' value=\"!!aut!!n!!!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1=f_aut!!n!!_id!!iaut!!&param2=f_aut!!n!!!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut!!n!!!!iaut!!.value), 'selector')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut!!n!!!!iaut!!.value=''; this.form.f_aut!!n!!_id!!iaut!!.value='!!n!!'; \" />
		<input type='hidden' name='f_aut!!n!!_id!!iaut!!' data-form-name='f_aut!!n!!_id' id='f_aut!!n!!_id!!iaut!!' value=\"!!aut!!n!!_id!!\" />
	</div>
</div>
<!--    Fonction    -->
<div style='float:left'>
	<div class='row'>
        <label class='etiquette' style='!!title_display!!'>".$msg[245]."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-15emr' id='f_f!!n!!!!iaut!!' name='f_f!!n!!!!iaut!!' data-form-name='f_f!!n!!' completion='fonction' autfield='f_f!!n!!_code!!iaut!!' value=\"!!f!!n!!!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=function&caller=saisie_titre_uniforme&p1=f_f!!n!!_code!!iaut!!&p2=f_f!!n!!!!iaut!!', 'selector')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f!!n!!!!iaut!!.value=''; this.form.f_f!!n!!_code!!iaut!!.value='0'; \" />
		<input type='hidden' name='f_f!!n!!_code!!iaut!!' data-form-name='f_f!!n!!_code' id='f_f!!n!!_code!!iaut!!' value=\"!!f!!n!!_code!!\" />
		<input class='bouton' type='button' onclick='duplicate(!!n!!,!!iaut!!);' value='".$msg['duplicate']."'>
		!!button_add_aut!!
	</div>
</div>
</div>
";	
else
$tu_authors_tpl="
<div class='row'>
<!--	Auteurs de l'oeuvre	-->
<div style='float:left;margin-right:10px;'>
<div class='row'>
        <label for='f_aut!!n!!' class='etiquette' style='!!title_display!!'>!!title!!</label>
		<input type='button' style='!!bouton_add_display!!' class='bouton' value='+' onClick=\"add_aut(!!n!!);\"/>
	</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut!!n!!_id!!iaut!!' id='f_aut!!n!!!!iaut!!' name='f_aut!!n!!!!iaut!!' data-form-name='f_aut!!n!!' value=\"!!aut!!n!!!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1=f_aut!!n!!_id!!iaut!!&param2=f_aut!!n!!!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut!!n!!!!iaut!!.value), 'selector')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut!!n!!!!iaut!!.value=''; this.form.f_aut!!n!!_id!!iaut!!.value='0'; \" />
		<input type='hidden' name='f_aut!!n!!_id!!iaut!!' data-form-name='f_aut!!n!!_id' id='f_aut!!n!!_id!!iaut!!' value=\"!!aut!!n!!_id!!\" />
	</div>
</div>
<!--    Fonction    -->
<div style='float:left;margin-right:10px;'>
	<div class='row'>
        <label class='etiquette' style='!!title_display!!'>".$msg[245]."</label>	
	</div>
	<div class='row'>
		<input type='text' class='saisie-15emr' id='f_f!!n!!!!iaut!!' name='f_f!!n!!!!iaut!!' data-form-name='f_f!!n!!' completion='fonction' autfield='f_f!!n!!_code!!iaut!!' value=\"!!f!!n!!!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=function&caller=saisie_titre_uniforme&p1=f_f!!n!!_code!!iaut!!&p2=f_f!!n!!!!iaut!!', 'selector')\" />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f!!n!!!!iaut!!.value=''; this.form.f_f!!n!!_code!!iaut!!.value='0'; \" />
		<input type='hidden' name='f_f!!n!!_code!!iaut!!' data-form-name='f_f!!n!!_code' id='f_f!!n!!_code!!iaut!!' value=\"!!f!!n!!_code!!\" />		
	</div>	
</div>	
<div  style='float:left;'>
	<div class='row'>
		<label for='f_aut!!n!!' class='etiquette' style='!!title_display!!'>".$msg['notice_vedette_composee_author']."</label>	
	</div>
	<div class='row'>
		<img class='img_plus' hspace='3' border='0' onclick=\"expand_vedette(this,'vedette!!iaut!!_!!vedettetype!!'); return false;\" title='détail' name='imEx' src='".get_url_icon('plus.gif')."'>
		<input type='text' class='saisie-30emr'  readonly='readonly'  name='saisie_titre_uniforme_!!vedettetype!!_composed_!!iaut!!_vedette_composee_apercu_autre' id='saisie_titre_uniforme_!!vedettetype!!_composed_!!iaut!!_vedette_composee_apercu_autre'  data-form-name='vedette_composee_!!vedettetype!!' value=\"!!vedette_apercu!!\" />		
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"del_vedette('!!vedettetype!!',!!iaut!!);\" />	
		<input class='bouton' type='button' onclick='duplicate(!!n!!,!!iaut!!);' value='".$msg['duplicate']."'>
		!!button_add_aut!!
	</div>	
</div>	
<div class='row' id='vedette!!iaut!!_!!vedettetype!!' style='margin-bottom:6px;display:none'>
!!vedette_author!!
</div>
<script type='text/javascript'>
	vedette_composee_update_all('saisie_titre_uniforme_!!vedettetype!!_composed_!!iaut!!_vedette_composee_subdivisions');
</script>
</div>	
";

$aut_fonctions= marc_list_collection::get_instance('function');
$tu_authors_all_tpl = "
<script>
    function fonction_selecteur_auteur() {
        var name=this.getAttribute('id').substring(4);
        var name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), 'selector');
    }
    function fonction_selecteur_auteur_change(field) {
    	// id champ text = 'f_aut'+n+suffixe
    	// id champ hidden = 'f_aut'+n+'_id'+suffixe; 
    	// select.php?what=auteur&caller=saisie_titre_uniforme&param1=f_aut0_id&param2=f_aut0&deb_rech='+t
        var name=field.getAttribute('id');
        var name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUp('./select.php?what=auteur&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), 'selector');
    }
    function fonction_raz_auteur() {
        var name=this.getAttribute('id').substring(4);
        var name_id = name.substr(0,6)+'_id'+name.substr(6);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
    function fonction_selecteur_fonction() {
        var name=this.getAttribute('id').substring(4);
        var name_code = name.substr(0,4)+'_code'+name.substr(4);
        openPopUp('./select.php?what=function&caller=saisie_titre_uniforme&param1='+name_code+'&param2='+name+'&dyn=1', 'selector');
    }
    function fonction_raz_fonction() {
        var name=this.getAttribute('id').substring(4);
        var name_code = name.substr(0,4)+'_code'+name.substr(4);
        document.getElementById(name_code).value=0;
        document.getElementById(name).value='';
    }

	function add_aut(n) {
		var template = document.getElementById('addaut'+n);
		var aut=document.createElement('div');
		aut.className='row';
		
		// auteur
		var colonne=document.createElement('div');
		//colonne.className='colonne2';
        colonne.style.cssFloat = 'left';
        colonne.style.marginRight = '10px';
		var row=document.createElement('div');
		row.className='row';
		var suffixe = eval('document.saisie_titre_uniforme.max_aut'+n+'.value')
		var nom_id = 'f_aut'+n+suffixe
		var f_aut0 = document.createElement('input');
		f_aut0.setAttribute('name',nom_id);
		f_aut0.setAttribute('id',nom_id);
		f_aut0.setAttribute('type','text');
		f_aut0.className='saisie-30emr';
		f_aut0.setAttribute('value','');
		f_aut0.setAttribute('completion','authors');
		f_aut0.setAttribute('autfield','f_aut'+n+'_id'+suffixe);
		
		var sel_f_aut0 = document.createElement('input');
		sel_f_aut0.setAttribute('id','sel_f_aut'+n+suffixe);
		sel_f_aut0.setAttribute('type','button');
		sel_f_aut0.className='bouton';
		sel_f_aut0.setAttribute('readonly','');
		sel_f_aut0.setAttribute('value','$msg[parcourir]');
		sel_f_aut0.onclick=fonction_selecteur_auteur;
		
		var del_f_aut0 = document.createElement('input');
		del_f_aut0.setAttribute('id','del_f_aut'+n+suffixe);
		del_f_aut0.onclick=fonction_raz_auteur;
		del_f_aut0.setAttribute('type','button');
		del_f_aut0.className='bouton';
		del_f_aut0.setAttribute('readonly','');
		del_f_aut0.setAttribute('value','$msg[raz]');
		
		var f_aut0_id = document.createElement('input');
		f_aut0_id.name='f_aut'+n+'_id'+suffixe;
		f_aut0_id.setAttribute('type','hidden');
		f_aut0_id.setAttribute('id','f_aut'+n+'_id'+suffixe);
		f_aut0_id.setAttribute('value','');
		
		var duplicate = document.createElement('input');
		duplicate.setAttribute('onclick','duplicate('+n+','+suffixe+')');			
		duplicate.setAttribute('type','button');
		duplicate.className='bouton';
		duplicate.setAttribute('readonly','readonly');
		duplicate.setAttribute('value','".$msg["duplicate"]."');
				
		//f_aut0_content.appendChild(f_aut0);
		row.appendChild(f_aut0);
		space=document.createTextNode(' ');
		row.appendChild(space);
		row.appendChild(sel_f_aut0);
		space=document.createTextNode(' ');
		row.appendChild(space);
		row.appendChild(del_f_aut0);
		row.appendChild(f_aut0_id);
		colonne.appendChild(row);
		aut.appendChild(colonne);
				
		// fonction	
		var colonne=document.createElement('div');
		//colonne.className='colonne_suite';
        colonne.style.cssFloat = 'left';
        colonne.style.marginRight = '10px';
		row=document.createElement('div');
		row.className='row';
		suffixe = eval('document.saisie_titre_uniforme.max_aut'+n+'.value');
		nom_id = 'f_f'+n+suffixe;
		f_f0 = document.createElement('input');
		f_f0.setAttribute('name',nom_id);
		f_f0.setAttribute('id',nom_id);
		f_f0.setAttribute('type','text');
		f_f0.className='saisie-15emr';
		f_f0.setAttribute('value','".($value_deflt_fonction ? $aut_fonctions->table[$value_deflt_fonction] : '')."');
		f_f0.setAttribute('completion','fonction');
		f_f0.setAttribute('autfield','f_f'+n+'_code'+suffixe);
		
		sel_f_f0 = document.createElement('input');
		sel_f_f0.setAttribute('id','sel_f_f'+n+suffixe);
		sel_f_f0.setAttribute('type','button');
		sel_f_f0.className='bouton';
		sel_f_f0.setAttribute('readonly','');
		sel_f_f0.setAttribute('value','$msg[parcourir]');
		sel_f_f0.onclick=fonction_selecteur_fonction;
		
		del_f_f0 = document.createElement('input');
		del_f_f0.setAttribute('id','del_f_f'+n+suffixe);
		del_f_f0.onclick=fonction_raz_fonction;
		del_f_f0.setAttribute('type','button');
		del_f_f0.className='bouton';
		del_f_f0.setAttribute('readonly','readonly');
		del_f_f0.setAttribute('value','$msg[raz]');
				
		f_f0_code = document.createElement('input');
		f_f0_code.name='f_f'+n+'_code'+suffixe;
		f_f0_code.setAttribute('type','hidden');
		f_f0_code.setAttribute('id','f_f'+n+'_code'+suffixe);
		f_f0_code.setAttribute('value','$value_deflt_fonction');
		
		row.appendChild(f_f0);
		space=document.createTextNode(' ');
		row.appendChild(space);
		row.appendChild(sel_f_f0);
		space=document.createTextNode(' ');
		row.appendChild(space);
		row.appendChild(del_f_f0);
		row.appendChild(f_f0_code);				
		if(!('".$pmb_authors_qualification."'*1)){					
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(duplicate);		
		}		
		colonne.appendChild(row);		
		aut.appendChild(colonne);
	
		if('".$pmb_authors_qualification."'*1){		
	        var role_field='role';
	        if(n==1) role_field='role_autre';
	        if(n==2) role_field='role_secondaire';
	        
			var req = new http_request();	
			if(req.request('./ajax.php?module=autorites&categ=get_tu_form_vedette&role_field='+role_field+'&index='+suffixe,1)){
				// Il y a une erreur
				alert ( req.get_text() );			
			}else {
			 	vedette_form=req.get_text();
			 	var row_vedette=document.createElement('div');
				row_vedette.className='row';
				row_vedette.innerHTML=vedette_form;
			}
			row_vedette.setAttribute('id','vedette'+suffixe+'_'+role_field);		
			row_vedette.style.display='none';		
			
			colonne=document.createElement('div');
			//colonne.className='colonne_suite';
       		colonne.style.cssFloat = 'left';
			row=document.createElement('div');
			row.className='row';
			
			var img_plus = document.createElement('img');
			img_plus.name='img_plus'+suffixe;
			img_plus.setAttribute('id','img_plus'+suffixe+'_'+role_field);		
			img_plus.className='img_plus';
			img_plus.setAttribute('src','".get_url_icon('plus.gif')."');
			img_plus.setAttribute('onclick','expand_vedette(this, \"vedette'+suffixe+'_'+role_field+'\")');			
		
			var nom_id = 'saisie_titre_uniforme_'+role_field+'_composed_'+suffixe+'_vedette_composee_apercu_autre';
			apercu = document.createElement('input');
			apercu.setAttribute('name',nom_id);
			apercu.setAttribute('id',nom_id);
			apercu.setAttribute('type','text');
			apercu.className='saisie-30emr';
			apercu.setAttribute('readonly','readonly');
				
			var del_vedette = document.createElement('input');
			del_vedette.setAttribute('onclick','del_vedette(\"'+role_field+'\",'+suffixe+')');
			del_vedette.setAttribute('type','button');
			del_vedette.className='bouton';
			del_vedette.setAttribute('readonly','readonly');
			del_vedette.setAttribute('value','$msg[raz]');		
				
			var duplicate = document.createElement('input');
			duplicate.setAttribute('onclick','duplicate('+n+','+suffixe+')');			
			duplicate.setAttribute('type','button');
			duplicate.className='bouton';
			duplicate.setAttribute('readonly','readonly');
			duplicate.setAttribute('value','".$msg["duplicate"]."');
		
			var buttonAdd = document.getElementById('button_add_titre_uniforme_aut_composed_' + n);

			row.appendChild(img_plus);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(apercu);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(del_vedette);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(duplicate);
			row.appendChild(buttonAdd);
			colonne.appendChild(row);
			aut.appendChild(colonne);		
			
			template.appendChild(aut);
			template.appendChild(row_vedette);
			eval(document.getElementById('vedette_script_'+role_field+'_composed_'+suffixe).innerHTML);
		}else{		
			template.appendChild(aut);
		}		
		eval('document.saisie_titre_uniforme.max_aut'+n+'.value=suffixe*1+1*1');
		ajax_pack_element(f_aut0);
		ajax_pack_element(f_f0);
		init_drag();
	}
	
	function duplicate(n,suffixe){
		add_aut(n);		
		new_suffixe = eval('document.saisie_titre_uniforme.max_aut'+n+'.value')-1;
        document.getElementById('f_aut'+n+new_suffixe).value = document.getElementById('f_aut'+n+suffixe).value;
        document.getElementById('f_aut'+n+'_id'+new_suffixe).value = document.getElementById('f_aut'+n+'_id'+suffixe).value;
        document.getElementById('f_f'+n+new_suffixe).value = '';
        document.getElementById('f_f'+n+'_code'+new_suffixe).value = '';
	}
			
	function expand_vedette(el,what) {
		var obj=document.getElementById(what);
		if(obj.style.display=='none'){
			obj.style.display='block';
	    	el.src = '".get_url_icon('minus.gif')."';	    	
			init_drag();
		}else{
			obj.style.display='none';
	    	el.src =  '".get_url_icon('plus.gif')."';
		}
	}
	
	function del_vedette(role,index) {
		vedette_composee_delete_all('saisie_titre_uniforme_'+role+'_composed_'+index+'_vedette_composee_subdivisions');		
		init_drag();
	}

</script>
<div id='authors_list' title='".htmlentities($msg["tu_authors_list"],ENT_QUOTES, $charset)."' movable='yes' class='row'>
    <!--    auteurs    -->
     <input type='hidden' name='max_aut0' value=\"!!max_aut0!!\" />
    !!authors_list0!!
    <div class='row' id='addaut0'>	        
	</div>
</div> 
<div id='authors_list1' title='".htmlentities($msg["tu_interpreter_list"],ENT_QUOTES, $charset)."' movable='yes' class='row'>
	<!--    interpretes    -->
    <input type='hidden' name='max_aut1' value=\"!!max_aut1!!\" />
    !!authors_list1!!
    <div class='row' id='addaut1'>	        
	</div>
</div>        		
";

// $titre_uniforme_replace : form remplacement titre_uniforme
$titre_uniforme_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='titre_uniforme_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!' onSubmit=\"return false\" >
<h3>$msg[159] !!old_titre_uniforme_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='titre_uniforme_libelle'>$msg[160]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' id='titre_uniforme_libelle' name='titre_uniforme_libelle' value=\"\" completion=\"titre_uniforme\" autfield=\"by\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=titre_uniforme&caller=titre_uniforme_replace&param1=by&param2=titre_uniforme_libelle&no_display=!!id!!', 'selector'); }\" />

		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=titre_uniforme_replace&param1=by&param2=titre_uniforme_libelle&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.titre_uniforme_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' id='by' value=''>
	</div>
	<div class='row'>		
		<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
	</div>	
</div>
<div class='row'>
	<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"document.location='!!cancel_action!!';\">
	<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['titre_uniforme_replace'].elements['titre_uniforme_libelle'].focus();
</script>
";


$user_query_tpl = "
<script type='text/javascript'>
<!--
	function test_form(form) {
		if(form.user_input.value.length == 0) {
			alert(\"$msg[141]\");
			return false;
		}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='search' method='post' action='!!action!!'>
	<h3>!!user_query_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<!-- sel_oeuvres_type -->
				<!-- sel_authority_statuts -->
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-50em' name='user_input' value='!!user_input!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				<!-- sel_oeuvres_nature -->
			</div>
		</div>
		<div class='right'></div>
		<div class='row'></div>
	</div>
	<!-- sel_langue -->
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='$msg[142]' onClick=\"return test_form(this.form)\" />
			<input class='bouton' type='button' value='!!add_auth_msg!!' onClick=\"document.location='!!add_auth_act!!'\" />
		</div>
		<div class='right'>
			<!-- lien_classement --><!-- lien_derniers --><!-- lien_thesaurus --><!-- imprimer_thesaurus -->
		</div>
	</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['search'].elements['user_input'].focus();
</script>
<div class='row'></div>
";


$oeuvre_event_tpl="
<script type='text/javascript'>

function fonction_selecteur_oeuvre_event() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,14)+'_code'+name.substr(14);
	openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name, 'selector');
}

function add_oeuvre_event() {
    var event_type_value_default = '!!event_type_default!!';
    var selector_function = \"openPopUp('./select.php?what=oeuvre_event&caller=saisie_titre_uniforme&field_id=f_oeuvre_event_code&field_name_id=f_oeuvre_event&dyn=3&max_field=max_oeuvre_event&add_field=add_oeuvre_event&param1=\"+event_type_value_default+\"', 'selector')\";
    var attribute = {'name':'onchange','value':'onchange_oeuvre_type_event'};
	templates.add_completion_qualified_selection_fields('f_oeuvre_event', 'f_oeuvre_event_code', 'oeuvre_event', 'f_oeuvre_type_event', selector_function, attribute);
}

function onchange_oeuvre_type_event(n) {
    var selector = document.getElementById('f_oeuvre_type_event'+n);
    var oeuvre_event_type_value = selector.value;
    
    var completion_field = document.getElementById('f_oeuvre_event'+n);
    completion_field.setAttribute('param1', oeuvre_event_type_value);
    completion_field.value = '';
    
    var selection_button = document.getElementById('sel_f_oeuvre_event'+n);
    selection_button.setAttribute('onclick', \"openPopUp('./select.php?what=oeuvre_event&caller=saisie_titre_uniforme&field_id=f_oeuvre_event_code&field_name_id=f_oeuvre_event&dyn=3&max_field=max_oeuvre_event&add_field=add_oeuvre_event&param1=\"+oeuvre_event_type_value+\"', 'selector')\");
}

</script>";

$oeuvre_event_tpl_first = "	
<input type='button' class='bouton' value='+' onClick=\"add_oeuvre_event();\"/>	
<div class='row'>
    !!oeuvre_event_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_event!!ioeuvre_event!!' name='f_oeuvre_event!!ioeuvre_event!!' data-form-name='f_oeuvre_event' value=\"!!oeuvre_event!!\" completion=\"oeuvre_event\" autfield=\"f_oeuvre_event_code!!ioeuvre_event!!\" param1=\"!!oeuvre_event_type_value!!\" />
	<input type='button' class='bouton' id='sel_f_oeuvre_event!!ioeuvre_event!!' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=oeuvre_event&caller=saisie_titre_uniforme&field_id=f_oeuvre_event_code&field_name_id=f_oeuvre_event&dyn=3&max_field=max_oeuvre_event&add_field=add_oeuvre_event&param1=!!type!!', 'selector')\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_event!!ioeuvre_event!!.value=''; this.form.f_oeuvre_event_code!!ioeuvre_event!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_event_code!!ioeuvre_event!!' data-form-name='f_oeuvre_event_code' id='f_oeuvre_event_code!!ioeuvre_event!!' value='!!oeuvre_event_code!!' />
	!!button_add_oeuvre_event!!
</div>
";
$oeuvre_event_tpl_other = "
<div class='row'>
    !!oeuvre_event_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_event!!ioeuvre_event!!' name='f_oeuvre_event!!ioeuvre_event!!' value=\"!!oeuvre_event!!\" completion=\"oeuvre_event\" autfield=\"f_oeuvre_event_code!!ioeuvre_event!!\" param1=\"!!oeuvre_event_type_value!!\" />
	<input type='button' class='bouton' id='sel_f_oeuvre_event!!ioeuvre_event!!' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=oeuvre_event&caller=saisie_titre_uniforme&field_id=f_oeuvre_event_code&field_name_id=f_oeuvre_event&dyn=3&max_field=max_oeuvre_event&add_field=add_oeuvre_event&param1=!!type!!', 'selector')\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_event!!ioeuvre_event!!.value=''; this.form.f_oeuvre_event_code!!ioeuvre_event!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_event_code!!ioeuvre_event!!' id='f_oeuvre_event_code!!ioeuvre_event!!' value='!!oeuvre_event_code!!' />
	!!button_add_oeuvre_event!!
</div>
";

$oeuvre_expression_from_tpl="
<input type='hidden' name='tab_oeuvre_expression_from_order' id='tab_oeuvre_expression_from_order' value='!!tab_oeuvre_expression_from_order!!' />
<input type='hidden' id='oeuvre_expression_from_new_order' name='oeuvre_expression_from_new_order' value=\"!!oeuvre_expression_from_new_order!!\" />
<script type='text/javascript'>

function fonction_selecteur_oeuvre_expression_from() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,24)+'_code'+name.substr(24);
	openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name, 'selector');
}
function fonction_raz_oeuvre_expression_from() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,24)+'_code'+name.substr(24);
	document.getElementById(name_id).value=0;
	document.getElementById(name).value='';
}
function add_oeuvre_expression_from() {
	templates.add_completion_qualified_field('f_oeuvre_expression_from', 'f_oeuvre_expression_from_code', 'titre_uniforme', 'f_oeuvre_expression_from_type', 'oeuvre_expression_from');

	var element_name = 'oeuvre_expression_from';
    var new_order_element=document.getElementById(element_name+'_new_order');
	var new_order=parseInt(new_order_element.value)+1;
	new_order_element.value=new_order;
    var tab_oeuvre_expression_from_order = document.getElementById('tab_oeuvre_expression_from_order');
	tab_oeuvre_expression_from_order.value = tab_oeuvre_expression_from_order.value + ',' + new_order;
	init_drag();
	ajax_pack_element(document.getElementById('f_oeuvre_expression_from'+new_order));
	return true;
}

</script>";

$oeuvre_expression_from_tpl_first = "
<input type='button' class='bouton' value='$msg[parcourir]' 
	onclick=\"openPopUp('./select.php?what=titre_uniforme&caller=saisie_titre_uniforme&field_id=f_oeuvre_expression_from_code&field_name_id=f_oeuvre_expression_from&dyn=3&max_field=max_oeuvre_expression_from&add_field=add_oeuvre_expression_from', 'selector')\" />
<input type='button' class='bouton' value='+' onClick=\"add_oeuvre_expression_from();\"/>	
<div id='f_oeuvre_expression_from!!ioeuvre_expression_from!!' class='row' dragtype='oeuvre_expression_from' draggable='yes' recept='yes' recepttype='oeuvre_expression_from' handler='f_oeuvre_expression_from!!ioeuvre_expression_from!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!oeuvre_expression_from!!' downlight=\"oeuvre_expression_from_downlight\" highlight=\"oeuvre_expression_from_highlight\" order='!!ioeuvre_expression_from!!'  style='' >
 	<span id='f_oeuvre_expression_from!!ioeuvre_expression_from!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
	!!expression_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_expression_from!!ioeuvre_expression_from!!_display_label' name='f_oeuvre_expression_from!!ioeuvre_expression_from!!' data-form-name='f_oeuvre_expression_from' value=\"!!oeuvre_expression_from!!\" completion=\"titre_uniforme\" autfield=\"f_oeuvre_expression_from_code!!ioeuvre_expression_from!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_expression_from!!ioeuvre_expression_from!!.value=''; this.form.f_oeuvre_expression_from_code!!ioeuvre_expression_from!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_expression_from_code!!ioeuvre_expression_from!!'  data-form-name='f_oeuvre_expression_from_code'  id='f_oeuvre_expression_from_code!!ioeuvre_expression_from!!' value='!!oeuvre_expression_from_code!!' />
	!!button_add_oeuvre_expression_from!!
</div>
";
$oeuvre_expression_from_tpl_other = "
<div id='f_oeuvre_expression_from!!ioeuvre_expression_from!!' class='row' dragtype='oeuvre_expression_from' draggable='yes' recept='yes' recepttype='oeuvre_expression_from' handler='f_oeuvre_expression_from!!ioeuvre_expression_from!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!oeuvre_expression_from!!' downlight=\"oeuvre_expression_from_downlight\" highlight=\"oeuvre_expression_from_highlight\" order='!!ioeuvre_expression_from!!'  style='' >
 	<span id='f_oeuvre_expression_from!!ioeuvre_expression_from!!_handle' style='float:left;padding-right:7px;'>
         <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
     </span>
	!!expression_type!!
	<input type='text' class='saisie-30emr' id='f_oeuvre_expression_from!!ioeuvre_expression_from!!_display_label' name='f_oeuvre_expression_from!!ioeuvre_expression_from!!' value=\"!!oeuvre_expression_from!!\" completion=\"titre_uniforme\" autfield=\"f_oeuvre_expression_from_code!!ioeuvre_expression_from!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_oeuvre_expression_from!!ioeuvre_expression_from!!.value=''; this.form.f_oeuvre_expression_from_code!!ioeuvre_expression_from!!.value=''; \" />
	<input type='hidden' name='f_oeuvre_expression_from_code!!ioeuvre_expression_from!!' id='f_oeuvre_expression_from_code!!ioeuvre_expression_from!!' value='!!oeuvre_expression_from_code!!' />
	!!button_add_oeuvre_expression_from!!
</div>
";

$tu_notices_tpl="
<script type='text/javascript'>

function fonction_selecteur_tu_notices() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,14)+'_code'+name.substr(14);
	openPopUp('./select.php?what=notice&caller=saisie_titre_uniforme&param1='+name_id+'&param2='+name, 'selector');
}
		
function fonction_raz_tu_notices() {
	var name=this.getAttribute('id').substring(4);
	var name_id = name.substr(0,14)+'_code'+name.substr(14);
	document.getElementById(name_id).value=0;
	document.getElementById(name).value='';
}
		
function add_tu_notices() {
	var template = document.getElementById('add_tu_notices');
	var tu_notices=document.createElement('div');
	tu_notices.className='row';
	var suffixe = eval('document.saisie_titre_uniforme.max_tu_notices.value')
	
	var nom_id = 'f_tu_notices'+suffixe;
	var f_tu_notices = document.createElement('input');
	f_tu_notices.setAttribute('name',nom_id);
	f_tu_notices.setAttribute('id',nom_id);
	f_tu_notices.setAttribute('type','text');
	f_tu_notices.className='saisie-30emr';
	f_tu_notices.setAttribute('value','');
	f_tu_notices.setAttribute('completion','tu_notices');
	f_tu_notices.setAttribute('autfield','f_tu_notices_code'+suffixe);
	
	var del_f_tu_notices = document.createElement('input');
	del_f_tu_notices.setAttribute('id','del_f_tu_notices'+suffixe);
	del_f_tu_notices.onclick=fonction_raz_tu_notices;
	del_f_tu_notices.setAttribute('type','button');
	del_f_tu_notices.className='bouton';
	del_f_tu_notices.setAttribute('readonly','');
	del_f_tu_notices.setAttribute('value','".$msg['raz']."');
	
	var f_tu_notices_input = document.createElement('input');
	f_tu_notices_input.name='f_tu_notices_code'+suffixe;
	f_tu_notices_input.setAttribute('type','hidden');
	f_tu_notices_input.setAttribute('id','f_tu_notices_code'+suffixe);
	f_tu_notices_input.setAttribute('value','');
	
	
	var space=document.createTextNode(' ');
	tu_notices.appendChild(f_tu_notices);
	tu_notices.appendChild(space);
	tu_notices.appendChild(space.cloneNode(false));
	tu_notices.appendChild(del_f_tu_notices);
	tu_notices.appendChild(f_tu_notices_input);
	
	template.appendChild(tu_notices);
	
	document.saisie_titre_uniforme.max_tu_notices.value=suffixe*1+1*1 ;
	ajax_pack_element(f_tu_notices);
}

</script>";

$tu_notices_tpl_first = "
<input type='button' class='bouton' value='".$msg['parcourir']."'
onclick=\"openPopUp('./select.php?what=notice&caller=saisie_titre_uniforme&field_id=f_tu_notices_code&field_name_id=f_tu_notices&dyn=3&max_field=max_tu_notices&add_field=add_tu_notices&myid=!!myid!!', 'selector')\" />
<input type='button' class='bouton' value='+' onClick=\"add_tu_notices();\"/>
<div class='row'>
	<input type='text' class='saisie-30emr' id='f_tu_notices!!itu_notices!!' name='f_tu_notices!!itu_notices!!' data-form-name='f_tu_notices' value=\"!!tu_notices!!\" completion=\"tu_notices\" autfield=\"f_tu_notices_code!!itu_notices!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_tu_notices!!itu_notices!!.value=''; this.form.f_tu_notices_code!!itu_notices!!.value=''; \" />
	<input type='hidden' name='f_tu_notices_code!!itu_notices!!' data-form-name='f_tu_notices_code' id='f_tu_notices_code!!itu_notices!!' value='!!tu_notices_code!!' />
</div>
";
$tu_notices_tpl_other = "
<div class='row'>
	<input type='text' class='saisie-30emr' id='f_tu_notices!!itu_notices!!' name='f_tu_notices!!itu_notices!!' value=\"!!tu_notices!!\" completion=\"titre_uniforme\" autfield=\"f_tu_notices_code!!itu_notices!!\" />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_tu_notices!!itu_notices!!.value=''; this.form.f_tu_notices_code!!itu_notices!!.value=''; \" />
	<input type='hidden' name='f_tu_notices_code!!itu_notices!!' id='f_tu_notices_code!!itu_notices!!' value='!!tu_notices_code!!' />
</div>
";
