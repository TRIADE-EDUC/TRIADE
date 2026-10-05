<?php
// +-------------------------------------------------+
// ? 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.tpl.php,v 1.104.4.1 2025/05/30 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cb, $id, $expl_new, $msg, $current_module, $expl_script, $antivol_form, $expl_form, $charset, $expl_pointage_base, $expl_pointage, $expl_cb_tmpl, $expl_cb_tmpl_recep, $expl_cb_retour_tmpl, $expl_pret, $expl_view_form, $expl_create_update_date_form, $expl_create_update_date_form, $expl_filing_return_date_form;
global $pmb_antivol;
global $pmb_rfid_activate, $pmb_rfid_driver, $pmb_rfid_serveur_url, $rfid_js_header;
global $pmb_numero_exemplaire_auto;
global $PMBuserid;
global $pmb_form_expl_editables;

if(!isset($cb)) $cb = '';
if(!isset($id)) $id = '';

//if($pmb_numero_exemplaire_auto>0) $num_exemplaire_test="if(eval(form.option_num_auto.checked == false ))";
if($pmb_numero_exemplaire_auto==1 || $pmb_numero_exemplaire_auto==2) {
	$num_exemplaire_test="var r=false;try { r=form.option_num_auto.checked;} catch(e) {};if(r==false) ";
} else {
	$num_exemplaire_test="";
}
// permet d'autoriser un no exemplaire vide en mode rfid
if ($pmb_rfid_activate==1 ) {
	$num_exemplaire_rfid_test="if(0)";	
} else {
	$num_exemplaire_rfid_test="";
}
// $expl_new : form pour creation d'un exemplaire
$expl_new = "
<script type='text/javascript'>
<!--
	function test_form(form) {
		$num_exemplaire_rfid_test 
		$num_exemplaire_test
		if(form.noex.value.replace(/^\s+$/g,'').length == 0 ) {
				alert(\"$msg[292]\");
				document.forms['addex'].elements['noex'].focus();
				return false;
			}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='addex' method='post' action='./catalog.php?categ=expl_create&id=!!id!!'>
<div class='row'>
	<h3>$msg[290]</h3>
</div>
<!--	Contenu du form	-->
<div class='form-contenu'>
	<div class='row'>
		!!etiquette!!
	</div>
	<div class='row'>
		!!saisie_num_expl!! 
	</div>
</div>
<div class='row'>
	!!btn_ajouter!!
	<input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='./catalog.php?categ=explnum_create&id=!!id!!'\" />
</div>
</form>
<script type='text/javascript'>
	if (document.forms['addex'].elements['noex']) document.forms['addex'].elements['noex'].focus();
</script>
";
 
// $expl_script : vérification du formulaire
$expl_script = "
<script type='text/javascript'>
<!--
function test_form(form) {
	if(form.form_cb_expl.value.replace(/^\s+$/g,'').length == 0) {
		alert(\"$msg[326]\");
		form.form_cb_expl.focus();
		return false;
	}
	return true;
}
-->
</script>
";

if($pmb_antivol) {
	$antivol_form="
		<!-- type antivol -->
		<label class='etiquette' for='type_antivol'>".$msg['type_antivol']."</label>
		<div class='row'>
			!!type_antivol!!
		</div>
	";
} else {
	$antivol_form="";
}
		
if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {

	if($pmb_rfid_driver=="ident")  $script_erase="init_rfid_erase(rfid_ack_erase);";
	else $script_erase="rfid_ack_erase(1);";

	$rfid_script_catalog="
		$rfid_js_header
		<script type='text/javascript'>
			var flag_cb_rfid=0;
			flag_program_rfid_ask=0;
			setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);;
			nb_part_readed=0;
			
			var msg_rfid_programmation_confirmation = '".addslashes($msg['rfid_programmation_confirmation'])."';
			var msg_rfid_etiquette_programmee_message = '".addslashes($msg['rfid_etiquette_programmee_message'])."';

			function program_rfid() {
				flag_semaphore_rfid=1;
				flag_program_rfid_ask=0;
				var nbparts = 0;
				if(document.getElementById('f_ex_nbparts')) {
					nbparts = document.getElementById('f_ex_nbparts').value;	
					if(nb_part_readed!= nbparts) {
						flag_semaphore_rfid=0;
						alert(\"".addslashes($msg['rfid_programmation_nbpart_error'])."\");
						return;
					}
				} else {
					nbparts = 1;
				}
				$script_erase
			}
		</script>
		<script type='text/javascript' src='".$base_path."/javascript/rfid.js'></script>
";
	$rfid_program_button="<input  type=button class='bouton' value=' ". $msg['rfid_configure_etiquette_button']." ' onClick=\"program_rfid_ask();\" />";	
}else {	
	$rfid_script_catalog="";
	$rfid_program_button="";
}

// $expl_form :form de saisie/modif exemplaire
$expl_form =jscript_unload_question();

$js_script_check_perso = "";
$js_script_import_perso = "";
global $pmb_expl_verif_js, $base_path;
if (!empty($pmb_expl_verif_js)) {
    $js_script_import_perso = "<script type='text/javascript' src='$base_path/javascript/$pmb_expl_verif_js'></script>";
    $js_script_check_perso = "
        if(typeof check_perso_form == 'function'){
			var check = check_perso_form(form);
			if (check == false) return false;
		}
    ";
}

$expl_form.="
$js_script_import_perso
$rfid_script_catalog
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit('catalog', '!!grid_type!!');
	     });
	});
</script>
<script type='text/javascript'>
<!--
	function test_form(form) {
        $js_script_check_perso

		!!questionrfid!!

		if((form.f_ex_cb.value.replace(/^\s+|\s+$/g, '').length == 0) || (form.f_ex_cote.value.replace(/^\s+/g, '').replace(/\s+$/g,'').length == 0)) {
			alert(\"$msg[304]\");
			return false;
		}
		if (typeof(form.f_ex_typdoc) == 'undefined') {
			alert(\"".$msg["expl_typdoc_mandatory"]."\");
			return false;
		}
		if(!form.f_ex_location.value) {
			alert(\"".$msg["expl_location_mandatory"]."\");
			return false;
		}
		if (typeof(form.f_ex_cstat) == 'undefined') {
			alert(\"".$msg["expl_codestat_mandatory"]."\");
			return false;
		}
		unload_off();
		return check_form();
	}
	function calcule_section(selectBox) {
		for (i=0; i<selectBox.options.length; i++) {
			id=selectBox.options[i].value;
		    list=document.getElementById(\"docloc_section\"+id);
		    list.style.display=\"none\";
		}
	
		id=selectBox.options[selectBox.selectedIndex].value;
		list=document.getElementById(\"docloc_section\"+id);
		list.style.display=\"block\";
	}
-->
</script>
<form class='form-$current_module' name='expl' id='expl-form' method='post' action='!!action!!'>
<div class='left'>
	<h3>$msg[300]</h3>
</div>
<div class='right'>";
if ($PMBuserid==1 && $pmb_form_expl_editables==1){
	$expl_form.="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
}
if ($pmb_form_expl_editables==1) {
	$expl_form.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
}
$expl_form.="</div>
<div class='form-contenu' >
	<div id='zone-container'>
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['291'], ENT_QUOTES, $charset)."\">
			<!-- code barre -->
			<label class='etiquette' for='f_ex_cb'>".$msg['291']."</label>
			<div class='row'>
				<input type='text' class='saisie-20emr' id=\"f_ex_cb\" value='!!cb!!' name='f_ex_cb' readonly='readonly' />
				<input type=button class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./catalog/expl/setcb.php?id=!!id!!', 'getcb')\" />".(file_exists("print_cb.php")?"<input type='button' value='".htmlentities($msg["print_print"],ENT_QUOTES,$charset)."' onClick='h=new http_request(); h.request(\"print_cb.php?cb=\"+document.getElementById(\"f_ex_cb\").value, false,\"\", false, function(){},function(){},\"impr_cb\")' class='bouton'/>":"")."
			</div>
		</div>
		<div id='el0Child_1' class='row'>
			<div id='el0Child_1_a' movable='yes' class='colonne3' title=\"".htmlentities($msg['296'], ENT_QUOTES, $charset)."\">
				<!-- cote -->
				<label class='etiquette' for='f_ex_cote'>$msg[296]</label>
				<div class='row'>
					<input type='text' class='saisie-20em' id=\"f_ex_cote\" name='f_ex_cote' value='!!cote!!' !!expl_ajax_cote!!/>
				</div>
			</div>
			<div id='el0Child_1_b' movable='yes' class='colonne3' title=\"".htmlentities($msg['294'], ENT_QUOTES, $charset)."\">
				<!-- type document -->
				<label class='etiquette' for='f_ex_typdoc'>$msg[294]</label>
				<div class='row'>
					!!type_doc!!
				</div>
			</div>
			<div id='el0Child_1_c' movable='yes' class='colonne3' title=\"".htmlentities($msg['expl_nbparts'], ENT_QUOTES, $charset)."\">
				<!-- Nombre de parties -->
				<label class='etiquette' for='f_ex_nbparts'>".$msg["expl_nbparts"]."</label>
				<div class='row'>
					<input type='text' class='saisie-5em' id=\"f_ex_nbparts\" value='!!nbparts!!' name='f_ex_nbparts' />
				</div>
			</div>
		</div>
		<div id='el0Child_2' class='row'>
			<div id='el0Child_2_a' movable='yes' class='colonne3' title=\"".htmlentities($msg['298'], ENT_QUOTES, $charset)."\">
				<!-- localisation -->
				<label class='etiquette' for='f_ex_location'>".$msg['298']."</label>
				<div class='row'>
					!!localisation!!
				</div>
			</div>
			<div id='el0Child_2_b' movable='yes' class='colonne3' title=\"".htmlentities($msg['295'], ENT_QUOTES, $charset)."\">
				<!-- section -->
				<label class='etiquette' for='f_ex_section'>".$msg['295']."</label>
				<div class='row'>
					!!section!!
				</div>
			</div>
			<div id='el0Child_2_c' movable='yes' class='colonne3' title=\"".htmlentities($msg['651'], ENT_QUOTES, $charset)."\">
				<!-- proprietaire -->
				<label class='etiquette' for='f_ex_owner'>".$msg['651']."</label> 
				<div class='row'>
					!!owner!!
				</div>
			</div>
		</div>
		<div id='el0Child_3' class='row'>
			<div id='el0Child_3_a' movable='yes' class='colonne3' title=\"".htmlentities($msg['297'], ENT_QUOTES, $charset)."\">
				<!-- statut -->
				<label class='etiquette' for='f_ex_statut'>".$msg['297']."</label>
				<div class='row'>
					!!statut!!
				</div>
			</div>
			<div id='el0Child_3_b' movable='yes' class='colonne3' title=\"".htmlentities($msg['299'], ENT_QUOTES, $charset)."\">
				<!-- code stat -->
				<label class='etiquette' for='f_ex_cstat'>".$msg['299']."</label>
				<div class='row'>
					!!codestat!!
				</div>
			</div>
			<div id='el0Child_3_c' movable='yes' class='colonne3' title=\"".htmlentities($msg['type_antivol'], ENT_QUOTES, $charset)."\">
				!!antivol_form!!
			</div>
		</div>
	
		<!-- notes -->
		<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg['expl_message'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_ex_note'>".$msg['expl_message']."</label>
			</div>
			<div class='row'>
				<textarea name='f_ex_note' id='f_ex_note' class='saisie-80em'>!!note!!</textarea>
			</div>
		</div>
		<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg['expl_zone_comment'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_ex_comment'>".$msg['expl_zone_comment']."</label>
			</div>
			<div class='row'>
				<textarea name='f_ex_comment' id='f_ex_comment' class='saisie-80em'>!!comment!!</textarea>
			</div>
		</div>
	
		<!-- prix et date -->
		<div id='el0Child_6' class='row'>
			<div id='el0Child_6_a' class='colonne3' movable='yes' title=\"".htmlentities($msg['expl_price'], ENT_QUOTES, $charset)."\">
				<div class='row'>
					<label class='etiquette' for='f_ex_prix'>".$msg['expl_price']."</label>
				</div>
				<div class='row'>
					<input type='text' class='text' name='f_ex_prix' id='f_ex_prix' value=\"!!prix!!\" />
				</div>
			</div>
			<div id='el0Child_6_b' class='colonne3' movable='yes' title=\"".htmlentities($msg['exp_cre_date'].' / '.$msg['exp_upd_date'], ENT_QUOTES, $charset)."\">
				!!create_update_date_form!!
			</div>
			<div id='el0Child_6_c' class='colonne3' movable='yes' title=\"".htmlentities($msg['filing_date'].' / '.$msg['return_date'], ENT_QUOTES, $charset)."\">
				!!filing_return_date_form!!
			</div>
		</div>
		<!-- index_concept_form -->
		!!champs_perso!!
		!!perio_circ_tpl!!
	</div>
	<div class='row'>&nbsp;</div>
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value=' $msg[76] ' onClick=\"unload_off();history.go(-1);\" />
		!!modifier!!
		$rfid_program_button
		!!dupliquer!!
		!!link_audit!!
		<input type=\"hidden\" name=\"id_form\" value=\"!!id_form!!\">
	</div>
	<div class='right'>
		!!supprimer!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript' src='./javascript/ajax.js' ></script>
<script type=\"text/javascript\">
    var dom_node_f_ex_cote = document.forms['expl'].elements['f_ex_cote']; 
	dom_node_f_ex_cote.focus();
    if(dom_node_f_ex_cote.value.length) {
        dom_node_f_ex_cote.setSelectionRange(dom_node_f_ex_cote.value.length, dom_node_f_ex_cote.value.length);
    }
	ajax_parse_dom();
</script>
";

//	$expl_pointage_base : ecran de pointage des exemplaires apres import
$expl_pointage_base = "
<script type='text/javascript' src='".$base_path."/javascript/expl_list.js'></script>
<script type='text/javascript'>
<!--
	function test_noex(soumission) {
		if (soumission == '1') {
			if(document.forms['pointage_expl'].noex.value.length == 0) {
				alert(\"".$msg[292]."\");
				document.forms['pointage_expl'].noex.focus();
				return false;
			}
		}
		document.forms['pointage_expl'].noex.value = document.forms['pointage_expl'].noex.value.replace(/ /g, '');
		document.forms['pointage_expl'].submit();
		return false;
	}
	function pointage_refresh_sections_from_location(location) {
		var num_location = location.value;
		var is_visible = is_visible_section_from_location(document.getElementById('book_section_id'), num_location);
		if(is_visible || confirm(parent.pmbDojo.messages.getMessage('pointage', 'pointage_expl_sections_refresh'))) { 
			refresh_sections_from_location(document.getElementById('book_section_id'), num_location, is_visible);
			pointage_expl_current_location = num_location;
			return true;
		}
		location.value = pointage_expl_current_location;
		return false;
	}
-->
</script>
<form class='form-$current_module' name='pointage_expl' method='post' action='./pointage_expl.php?categ=import&sub=pointage_expl'>
<h3>$msg[569]</h3>
<!--	Contenu du form	-->
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne4'>
			<!-- CB -->
			<label class='etiquette' for='f_ex_statut'>$msg[291]</label>
			<div class='row'>
				<input type='text' class='saisie-20em' name='noex' value=''>
			</div>
		</div>
		<div class='colonne4'>
			<!-- statut -->
			<label class='etiquette' for='f_ex_stat'>$msg[297]</label>
			<div class='row'>
				!!book_statut_id!!
			</div>
		</div>
		<div class='colonne4'>
			<!-- localisation -->
			<label class='etiquette' for='f_ex_location'>$msg[298]</label>
			<div class='row'>
				!!book_location_id!!
			</div>
		</div>
		<div class='colonne_suite'>
			<!-- section -->
			<label class='etiquette' for='f_ex_section'>$msg[295]</label>
			<div class='row'>
				!!book_section_id!!
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='colonne4'>
			<div class='row'>
				&nbsp;
			</div>
		</div>
		<div class='colonne4'>
			<!-- typdoc=support -->
			<label class='etiquette' for='f_ex_typdoc'>$msg[294]</label>
			<div class='row'>
				!!book_doctype_id!! 
			</div>
		</div>
		<div class='colonne4'>
			<!-- codestat -->
			<label class='etiquette' for='f_ex_cstat'>$msg[299]</label>
			<div class='row'>
				!!book_codestat_id!! 
			</div>
		</div>
		<div class='colonne_suite'>
			<!-- owner -->
			<label class='etiquette' for='f_ex_owner'>$msg[651]</label>
			<div class='row'>
				!!book_lender_id!! 
			</div>
		</div>
	</div>
	<div class='row'> </div>
</div>	
<input type='submit' class='bouton' value=' ".$msg[89]." ' onClick=\"return test_noex('1')\" />
<input type='hidden' name='action' value='pointage' />
<script type='text/javascript'>
	document.forms['pointage_expl'].elements['noex'].focus();
	if(document.getElementById('book_section_id') && document.getElementById('book_location_id')) {
        var pointage_expl_is_visible = is_visible_section_from_location(document.getElementById('book_section_id'), document.getElementById('book_location_id').value);
		refresh_sections_from_location(document.getElementById('book_section_id'), document.getElementById('book_location_id').value, pointage_expl_is_visible);
		var pointage_expl_current_location = document.getElementById('book_location_id').value;
	}
</script>
<div class='row'>
	!!explencoursdevalidation!!
</div>
</form>
";

//	$expl_pointage : ecran de pointage des exemplaires apres import
$expl_pointage = "
<hr />
!!notice!!
<hr />
<table width='100%'>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td> $msg[563]
			</td>
		</tr>
		<tr>
			<td>
				$msg[296] <!-- cote -->
			</td>
			<td><b>
				!!cote!! </b>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				$msg[294] <!-- type doc -->
			</td>
			<td>
				!!type_doc!!
			</td>
			<td>
			<span style='color:red'> !!nouveau_support!! </span>
			</td>
		</tr>
		<tr>
			<td>
				$msg[295] <!-- section -->
			</td>
			<td>
				!!section!!
			</td>
			<td>
				 <span style='color:red'> !!nouvelle_section!! </span>
			</td>
		</tr>
		<tr>
			<td>
				$msg[297] <!-- statut -->
			</td>
			<td>
				!!statut!!
			</td>
			<td>
				 <span style='color:red'> !!nouveau_statut!! </span>
			</td>
		</tr>
		<tr>
			<td>
				$msg[298] <!-- localisation -->
			</td>
			<td>
				!!localisation!!
			</td>
			<td>
				<span style='color:red'> !!nouvelle_location!! </span>
			</td>
		</tr>
		<tr>
			<td>
				$msg[651] <!-- owner -->
			</td>
			<td>
				!!owner!!
			</td>
			<td>
				<span style='color:red'> !!nouveau_proprio!! </span>
			</td>
		</tr>
		<tr>
			<td>
				$msg[299] <!-- code stat -->
			</td>
			<td>
				!!codestat!!
			</td>
			<td>
				<span style='color:red'> !!nouveau_codestat!! </span>
			</td>
		</tr>
		<tr>
			<td style='vertical-align:top'>
				$msg[expl_message] <!-- zone de notes -->
			</td>
			<td colspan='2' >
				<textarea name='expl_note' cols='35'>!!note!!</textarea>
				<input type='hidden' name='noex_valide' value='!!noex_valide!!' />
			</td>
		</tr>
		<tr>
			<td style='vertical-align:top'>
				$msg[expl_zone_comment] <!-- zone de commentaire non bloquant -->
			</td>
			<td colspan='2' >
				<textarea name='expl_comment' cols='35'>!!comment!!</textarea>
			</td>
		</tr>
</table>
<input type='button' class='bouton' value=' $msg[76] ' onClick=\"document.location='!!annuler_action!!';\" />
<input type='submit' class='bouton' value=' $msg[77] ' onClick=\"return test_noex('0')\" />
";


if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {	
	// $expl_cb_tmpl : template pour le form de saisie code-barre
	$expl_cb_tmpl = "
	!!script!!
	$rfid_js_header
	<script type='text/javascript'>
		var cb_lu =new Array();	
		setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);
		function f_expl(cb) {
			// il y a une ou plusieurs étiquette rfid
			if( cb.length>0) {	
				if(document.activeElement.getAttribute('id') == 'input_edit_cb' ){
					document.getElementById('input_edit_cb').value=cb[0];
					//Fonction définie dans expl_info.inc.php
					launchUpdateRequest();
				}else{
					if (document.getElementById('expl_cb').value != cb[0]) {	
						document.getElementById('form_cb_expl').value=cb[0];
						document.saisie_cb_ex.submit();
					}
				}
			} 
		}
	</script>
	<h1>!!title!!</h1>
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
	<h3>!!titre_formulaire!!</h3>
	<div class='form-contenu'>
		<!-- !!before!! -->
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
			<input type='hidden' id='expl_cb' name='expl_cb' value='!!expl_cb!!' />
			<input type='hidden' id='transfert_id_resa' name='transfert_id_resa' value='' />
		</div>
			
		<!-- !!suite!! -->
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[502]' />
	</div>
	</form>
	<script type='text/javascript'>
		document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
	</script>
	";	
	
} else {
	// $expl_cb_tmpl : template pour le form de saisie code-barre
	$expl_cb_tmpl = "
	!!script!!
	<h1>!!title!!</h1>
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
	<h3>!!titre_formulaire!!</h3>
	<div class='form-contenu'>
		<!-- !!before!! -->
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
				<input type='hidden' id='transfert_id_resa' name='transfert_id_resa' value='' />
		</div>
		<!-- !!suite!! -->	
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[502]' />
	</div>
	</form>
	<script type='text/javascript'>
		document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
	</script>
	";
}

if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {	
	// $expl_cb_tmpl : template pour le form de saisie code-barre
	$expl_cb_tmpl_recep = "
	!!script!!
	$rfid_js_header
	<script type='text/javascript'>
		var cb_lu =new Array();	
		setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);
		
		function f_expl(cb) {
			// il y a une ou plusieurs étiquette rfid
			if( cb.length>0) {					
				if (document.getElementById('expl_cb').value != cb[0]) {	
					document.getElementById('form_cb_expl').value=cb[0];
					document.saisie_cb_ex.submit();
				}
			} 
		}
	</script>
	<h1>!!title!!</h1>
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
	<h3>!!titre_formulaire!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
			<input type='hidden' id='expl_cb' name='expl_cb' value='!!expl_cb!!' />
		</div>
		<div class='row'>
			<div class='left'>
				<div class='row'>
					<label class='etiquette' for='statut_reception'>".$msg["transferts_circ_reception_lbl_statuts"]."</label>
				</div>
				<div class='row'>
					<select id='statut_reception' name='statut_reception'>!!liste_statuts!!</select>
				</div>
			</div>
			<div class='left'>&nbsp;&nbsp;</div>
			<div class='left'>
				<div class='row'>
					<label class='etiquette' for='section_reception'>".$msg["transferts_circ_reception_lbl_sections"]."</label>
				</div>
				<div class='row'>
					<select id='section_reception' name='section_reception'>!!liste_sections!!</select>
				</div>
			</div>
		</div>
		<div class='row'></div>
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[502]' />
		</div>
	</form>
	<script type='text/javascript'>
	document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
	</script>
	";	
	
} else {
	// $expl_cb_tmpl : template pour le form de saisie code-barre
	$expl_cb_tmpl_recep = "
	!!script!!
	<h1>!!title!!</h1>
	<form class='form-$current_module' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
	<h3>!!titre_formulaire!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
		</div>
		<div class='row'>
			<div class='left'>
				<div class='row'>
					<label class='etiquette' for='statut_reception'>".$msg["transferts_circ_reception_lbl_statuts"]."</label>
				</div>
				<div class='row'>
					<select id='statut_reception' name='statut_reception'>!!liste_statuts!!</select>
				</div>
			</div>
			<div class='left'>&nbsp;&nbsp;</div>
			<div class='left'>
				<div class='row'>
					<label class='etiquette' for='section_reception'>".$msg["transferts_circ_reception_lbl_sections"]."</label>
				</div>
				<div class='row'>
					<select id='section_reception' name='section_reception'>!!liste_sections!!</select>
				</div>
			</div>
		</div>
		<div class='row'></div>
	</div>
	<div class='row'>
		<input type='submit' class='bouton' value='$msg[502]' />
	</div>
	</form>
	<script type='text/javascript'>
	document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();
	</script>
	";
}


if ($pmb_rfid_activate==1  ) {	
	if(!$pmb_rfid_serveur_url)$pmb_rfid_antivol_activate=0;
	else $pmb_rfid_antivol_activate=1;
	$expl_cb_retour_tmpl = "
		!!script!!
		$rfid_js_header
		<script src='./javascript/rfid/rfid_pret.js'></script>
		<script type='text/javascript'>
			function retour_manuel() {
				var cb=new Array(); 
				cb[0]=document.getElementById('form_cb_expl').value;
				document.getElementById('form_cb_expl').value='';
				document.getElementById('form_cb_expl').focus();
				flag_antivol_retour=0;
				read_retour(cb);				
			}

		</script>
		
		<h1>!!title!!</h1>
		<form class='form-retour-expl' name='saisie_cb_ex' method='post' onSubmit=\"retour_manuel();return false\">
		<!--
			<h3>!!titre_formulaire!!</h3>
			<div class='form-contenu'>
		-->
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
			&nbsp;&nbsp;
			<input type='button' class='bouton' value='$msg[502]' onClick=\"retour_manuel();\"/>
		</div>
		</form>
		<div class='row'>
			<table id='table_retour_tmp' name='table_retour_tmp'>
			</table>
		</div>
		<script type='text/javascript'>
			document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();";
		if ($pmb_rfid_serveur_url) {	
			$expl_cb_retour_tmpl.= "
			init_rfid_retour();
			</script>
			";
		} else {
			$expl_cb_retour_tmpl.= "
			init_sans_rfid_retour();
			</script>
			";				
		}
} else
	$expl_cb_retour_tmpl = "
		!!script!!
		<h1>!!title!!</h1>
		<form class='form-retour-expl' name='saisie_cb_ex' method='post' action='!!form_action!!' onSubmit='return test_form(this)'>
		<!--
			<h3>!!titre_formulaire!!</h3>
			<div class='form-contenu'>
		-->
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>!!message!!</label>
		</div>
		<div class='row'>
			<input class='saisie-20em' type='text' id='form_cb_expl' name='form_cb_expl' value=''  />
			&nbsp;&nbsp;
			<input type='submit' class='bouton' value='$msg[502]' />
		</div>
		<!--
			</div>
			<div class='row'>
			<input type='submit' class='bouton' value='$msg[502]' />
			</div>
		-->
		</form>
		<script type='text/javascript'>
			document.forms['saisie_cb_ex'].elements['form_cb_expl'].focus();

            // Affichage de loader lors du submit du formulaire (#148428)
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementsByName('saisie_cb_ex')[0];
                if(form) {
                    form.addEventListener('submit', () => {
						if(form.form_cb_expl.value.replace(/^\s+$/g,'').length != 0) {
							pmb_show_loader('saisie_cb_ex');
							//form.form_cb_expl.disabled = true;
							form.setAttribute('onsubmit', '');
							const submit = form.querySelector('[type=submit]');
							if(submit) {
								submit.disabled = true;
							}
						}
                    });
                }
            });
		</script>";

//	$expl_pret :form d'affichage exemplaire pr?t?
$expl_pret ="
<table class='expl-form center' style='border-spacing: 3px'>
		<tr>
			<td colspan='2' class='listheader'>
				$msg[300]
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[233] <!-- Titre -->
			</td>
			<td class='align_left'>
				!!titre!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[234] <!-- Auteur -->
			</td>
			<td class='align_left'>
				!!auteur!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[66] <!-- code barre -->
			</td>
			<td class='align_left'>
				!!cb!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[296] <!-- cote -->
			</td>
			<td class='align_left'>
				!!cote!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[294] <!-- type doc -->
			</td>
			<td class='align_left'>
				!!type_doc!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[295] <!-- section -->
			</td>
			<td class='align_left'>
				!!section!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[298] <!-- localisation -->
			</td>
			<td class='align_left'>
				!!localisation!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				$msg[651] <!-- owner -->
			</td>
			<td class='align_left'>
				!!owner!!
			</td>
		</tr>
		<tr>
			<td class='align_right' style='vertical-align:top'>
style='vertical-align:top'l_message] <!-- zone de notes -->
			</td>
			<td class='align_left'>
				!!note!!
			</td>
		</tr>
		<tr>
			<td class='align_right' style='vertical-align:top'>
				$msg[expl_zone_comment] <!-- zone de commentaire non bloquant -->
			</td>
			<td class='align_left'>
				!!comment!!
			</td>
		</tr>
		<tr>
			<td colspan='2'>
						<table style='border: 0px; width: 100%; border-spacing: 0px'>
							<tr>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr>
								<td class='listheader'>
									$msg[349]
								</td>
							</tr>
							<tr>
								<td>
									!!pret_list!!
								</td>
							</tr>
						</table>
					</td>
		</tr>
	</table>
";

$expl_view_form="
<table class='expl-form center' style='border-spacing: 3px'>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>$msg[291]</label> <!-- code barres -->
			</td>
			<td class='align_left'>
				!!code_barre!!
			</td>			
		</tr>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>$msg[296]</label> <!-- cote -->
			</td>
			<td class='align_left'>
				!!cote!!
			</td>
			<td class='align_right'>
				<label class='etiquette'>$msg[294]</label>  <!-- support -->
			</td>
			<td class='align_left'>
				!!type_doc!!
			</td>			
		</tr>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>$msg[298]</label>  <!-- localisation -->
			</td>
			<td class='align_left'>
				!!localisation!!
			</td>
			<td class='align_right'>
				<label class='etiquette'>$msg[295]</label>  <!-- section -->
			</td>
			<td class='align_left'>
				!!section!!
			</td>			
		</tr>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>$msg[651]</label>  <!-- owner -->
			</td>
			<td class='align_left'>
				!!owner!!
			</td>
			<td class='align_right'>
				<label class='etiquette'>$msg[297]</label>  <!-- statut -->
			</td>
			<td class='align_left'>
				!!statut!!
			</td>			
		</tr>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>$msg[299]</label>  <!-- code statistique -->
			</td>
			<td class='align_left'>
				!!codestat!!
			</td>						
			<td class='align_right'>
				<label class='etiquette'>".$msg['expl_prets_nb']."</label>  <!-- nombre prets -->
			</td>
			<td class='align_left'>
				!!nb_prets!!
			</td>
		</tr>
		<tr>
			<td class='align_right'>
				<label class='etiquette'>".$msg['expl_message']."</label>  <!-- message exemplaire -->
			</td>
			<td class='align_left'>
				!!note!!
			</td>
			<td class='align_right'>
				<label class='etiquette'>".$msg['expl_zone_comment']."</label>  <!-- commentaire non bloquant -->
			</td>
			<td class='align_left'>
				!!comment!!
			</td>					
		</tr>
		<!-- exp_date_admin -->
		<!-- exp_date_filing -->
		!!champs_perso!!
	</table>
	<hr />
";

$expl_create_update_date_form = "
<div id='el0Child_6_b_0' class='colonne2'>
	<div class='row'>
		<label class='etiquette' >".htmlentities($msg['exp_cre_date'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!create_date!!
	</div>
</div>
<div id='el0Child_6_b_1' class='colonne2'>
	<div class='row'>
		<label class='etiquette' >".htmlentities($msg['exp_upd_date'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!update_date!!
	</div>
</div>";

$expl_filing_return_date_form = "
<div id='el0Child_6_c_0' class='colonne2'>
	<div class='row'>
		<label class='etiquette' >".htmlentities($msg['filing_date'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!filing_date!!
	</div>
</div>
<div id='el0Child_6_c_1' class='colonne2'>
	<div class='row'>
		<label class='etiquette' >".htmlentities($msg['return_date'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!return_date!!
	</div>
</div>";
