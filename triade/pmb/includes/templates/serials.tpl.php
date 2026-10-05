<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials.tpl.php,v 1.249.2.1.2.1 2025/03/12 15:39:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $user_query, $issn_query, $serial_header, $serial_footer, $serial_access_form, $current_module, $msg, $filter_abo_actif, $nb_onglets, $ptab, $charset, $ptab_bul;
global $serial_top_content_form, $base_path, $message_search, $serial_action_bar, $z3950_accessible, $acquisition_active;
global $scan_request_record_button, $pmb_scan_request_activate, $bul_action_bar, $serial_bul_content_form, $pdeptab, $analysis_top_content_form, $pmb_use_uniform_title, $notice_bulletin_form;
global $liste_script, $liste_debut, $liste_fin, $pmb_numero_exemplaire_auto, $num_exemplaire_test, $pmb_rfid_activate, $num_exemplaire_rfid_test, $bul_cb_form, $pmb_rfid_serveur_url;
global $pmb_rfid_driver, $script_erase, $rfid_script_catalog, $rfid_js_header, $rfid_program_button, $serial_edit_access, $serial_list_tmpl, $perio_replace, $deflt_notice_replace_links;
global $bulletin_replace, $rfid_script_bulletine, $expl_bulletinage_tpl, $bul_expl_form1, $analysis_type_form, $perio_replace_categories, $perio_replace_category;
global $bulletin_replace_categories, $bulletin_replace_category, $analysis_move, $bulletin_move;
global $pmb_form_editables;

if(!isset($user_query)) $user_query = '';
if(!isset($issn_query)) $issn_query = '';

$serial_header = "
	<h1>!!page_title!!</h1>";

$serial_footer = "";

$serial_access_form ="
<script type='text/javascript'>
<!--
	function test_form() {
		if (document.serial_search.user_query.value=='') {
			document.serial_search.user_query.value='*';
			}
		return true;
	}
-->
</script>

<form class='form-$current_module' name='serial_search' method='post' action='./catalog.php?categ=serials&sub=search' onSubmit='return test_form();' >
	<h3>".$msg["recherche"]." : $msg[771]</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette'>$msg[bulletin_mention_titre_court]</label>
		</div>
		<div class='row'>
			<input class='saisie-inline' id='user_query' type='text' size='36' name='user_query' value='!!user_query!!' />
		</div>
		<div class='row'>
			<span class='astuce'>$msg[155]
				<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
			</span>
		</div>
		<div class='row'>
			<label class='etiquette'>$msg[165]</label>
		</div>
		<div class='row'>
			<input class='saisie-inline' id='issn_query' type='text' size='36' name='issn_query' value='!!issn_query!!' />
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['abonnements_actif_search_filter']."</label>
		</div>
		<div class='row'>
			<input id='filter_abo_actif' type='checkbox' name='filter_abo_actif' ".(isset($filter_abo_actif) && $filter_abo_actif?"checked='checked'":"")." />
		</div>
	</div>
	<div class='row'>
		<input class='bouton' type='submit' value='$msg[142]' />
	</div>
</form>
<script type=\"text/javascript\">
	document.forms['serial_search'].elements['user_query'].focus();
</script>
";

$serial_access_form = str_replace('!!user_query!!', htmlentities(stripslashes($user_query ),ENT_QUOTES, $charset), $serial_access_form);
$serial_access_form = str_replace('!!issn_query!!', htmlentities(stripslashes($issn_query ),ENT_QUOTES, $charset), $serial_access_form);

// template pour le form de catalogage

// nombre de parties du form
$nb_onglets = 5;

//	----------------------------------------------------
// 	  $ptab[0] : contenu de l'onglet 0 (Titre)
$ptab[0] = "
<!-- onglet 0 -->
<div id='el0Parent' class='parent'>
	<h3>
		<img src='".get_url_icon('minus.gif')."' class='img_plus' class='align_top' name='imEx' id='el0Img' title='$msg[236]' border='0' onClick=\"expandBase('el0', true); return false;\" />
		$msg[712]
	</h3>
</div>

<div id='el0Child' class='child' etirable='yes' title='".htmlentities($msg[236],ENT_QUOTES, $charset)."' >
	!!element_tit1!!
	!!element_tit3!!
	!!element_tit4!!
</div>
";

$ptab_bul[0] = "
<!-- onglet 0 -->
<div id='el0Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el0', '236')."
		$msg[712]
	</h3>
</div>

<div id='el0Child' class='child' etirable='yes' title='".htmlentities($msg[236],ENT_QUOTES, $charset)."' >
	!!element_tit3!!
	!!element_tit4!!
</div>
";

//	----------------------------------------------------
// 	  $ptab[2] : contenu de l'onglet 2 Editeurs
//	----------------------------------------------------
$ptab[2] = "
<!-- onglet 2 -->
<div id='el2Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el2')."
		".$msg['serial_onglet_editeurs']."
	</h3>
</div>
<div id='el2Child' class='child' etirable='yes' title='".htmlentities($msg[249],ENT_QUOTES, $charset)."'>
	<div id='el2Child_0' title='".htmlentities($msg[164],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Editeur    -->
		<div id='el2Child_0a' class='row'>
			<label for='f_ed1' class='etiquette'>$msg[164]</label>
		</div>
		<div id='el2Child_0b' class='row'>
			<input type='text' completion='publishers' autfield='f_ed1_id' id='f_ed1' name='f_ed1' data-form-name='f_ed1' value=\"!!ed1!!\" class='saisie-30emr' />
			<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=editeur&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=dummy&p4=dummy&p5=dummy&p6=dummy&deb_rech='+".pmb_escape()."(this.form.f_ed1.value), '!!force_dialog_publisher!!', '!!force_popup_publisher!!')\" />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_ed1.value=''; this.form.f_ed1_id.value='0'; \" />
			<input type='hidden' name='f_ed1_id' id='f_ed1_id' data-form-name='f_ed1_id' value=\"!!ed1_id!!\" />
		</div>
	</div>
	<div id='el2Child_4' title='".htmlentities($msg[252],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Année    -->
			<div id='el2Child_4a' class='row'>
				<label for='f_year' class='etiquette'>$msg[252]</label>
			</div>
			<div id='el2Child_4b' class='row'>
				<input type='text' class='saisie-30em' id='f_year' name='f_year' value=\"!!year!!\" />
			</div>
		</div>
	<div id='el2Child_7' title='".htmlentities($msg[254],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Autre éditeur    -->
		<div id='el2Child_7a' class='row'>
			<label for='f_ed2' class='etiquette'>$msg[254]</label>
		</div>
		<div id='el2Child_7b' class='row'>
	    	<input type='text' completion='publishers' autfield='f_ed2_id' id='f_ed2' name='f_ed2' data-form-name='f_ed2' value=\"!!ed2!!\" class='saisie-30emr' />
	    	<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=editeur&caller=notice&p1=f_ed2_id&p2=f_ed2&p3=dummy&p4=dummy&p5=dummy&p6=dummy&deb_rech='+".pmb_escape()."(this.form.f_ed2.value), '!!force_dialog_publisher!!', '!!force_popup_publisher!!')\" />
	    	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_ed2.value=''; this.form.f_ed2_id.value='0'; \" />
	    	<input type='hidden' name='dummy' />
	    	<input type='hidden' name='f_ed2_id' id='f_ed2_id' data-form-name='f_ed2_id' value=\"!!ed2_id!!\" />
		</div>
	</div>
</div>
";

//	----------------------------------------------------
//	ISBN, EAN ou no. commercial
// 	  $ptab[30] : contenu de l'onglet 30
//	----------------------------------------------------
$ptab[30] = "
<!-- onglet 30 -->
<div id='el30Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el30', '255')."
		$msg[serial_ISSN]
	</h3>
</div>
<div id='el30Child' class='child' etirable='yes' title='".htmlentities($msg["serial_ISSN"],ENT_QUOTES, $charset)."'>
	<div id='el30Child_0' title='$msg[serial_ISSN]' movable='yes'>
		<!--	ISBN, EAN ou no. commercial	-->
		<div id='el30Child_0a' class='row'>
			<label for='f_cb' class='etiquette'>$msg[serial_ISSN]</label>
		</div>
		<div id='el30Child_0b' class='row'>
			<input class='saisie-20emr' id='f_cb' name='f_cb' data-form-name='f_cb' readonly value=\"!!cb!!\" />
		    <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./catalog/setcb.php?notice_id=!!notice_id!!', 'getcb')\" />
		    <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_cb.value=''; \" />
		</div>
	</div>
</div>
";

//    ----------------------------------------------------
//    Collation
//       $ptab[41] : contenu de l'onglet 41 (collation)
//    ----------------------------------------------------

$ptab[41] = "
<!-- onglet 41 -->
<div id='el41Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el41', '257')."
		$msg[258]
	</h3>
</div>
<div id='el41Child' class='child' etirable='yes' title='".htmlentities($msg[258],ENT_QUOTES, $charset)."'>
	<div id='el41Child_0' title='".htmlentities($msg[259],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Importance matérielle (nombre de pages, d'éléments...)    -->
		<div id='el41Child_0a' class='row'>
			<label for='f_npages' class='etiquette'>$msg[259]</label>
		</div>
		<div id='el41Child_0b' class='row'>
			<input type='text' class='saisie-80em' id='f_npages' name='f_npages' value=\"!!npages!!\" />
		</div>
	</div>
	<div id='el41Child_1' title='".htmlentities($msg[260],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Autres caractèristiques matérielle (ill., ...)    -->
		<div id='el41Child_1a' class='row'>
			<label for='f_ill' class='etiquette'>$msg[260]</label>
		</div>
		<div id='el41Child_1b' class='row'>
			<input type='text' class='saisie-80em' id='f_ill' name='f_ill' value=\"!!ill!!\" />
		</div>
	</div>
	<div id='el41Child_2' title='".htmlentities($msg[261],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Format    -->
		<div id='el41Child_2a' class='row'>
			<label for='f_size' class='etiquette'>$msg[261]</label>
		</div>
		<div id='el41Child_2b' class='row'>
			<input type='text' class='saisie-80em' id='f_size' name='f_size' value=\"!!size!!\" />
		</div>
	</div>
	<div id='el41Child_3' title='".htmlentities($msg[4050],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Prix    -->
		<div id='el41Child_3a' class='row'>
			<label for='f_prix' class='etiquette'>$msg[4050]</label>
		</div>
		<div id='el41Child_3b' class='row'>
			<input type='text' class='saisie-80em' id='f_prix' name='f_prix' value=\"!!prix!!\" />
		</div>
	</div>
	<div id='el41Child_4' title='".htmlentities($msg[262],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Matériel d'accompagnement    -->
		<div id='el41Child_4a' class='row'>
			<label for='f_accomp' class='etiquette'>$msg[262]</label>
		</div>
		<div id='el41Child_4b' class='row'>
			<input type='text' class='saisie-80em' id='f_accomp' name='f_accomp' value=\"!!accomp!!\" />
		</div>
	</div>
</div>
";

//	----------------------------------------------------
// 	  $form_notice : Nouveau périodique
//	----------------------------------------------------
$serial_top_content_form = "
!!tab0!!
<hr class='spacer' />
!!tab1!!
<hr class='spacer' />
!!tab2!!
<hr class='spacer' />
!!tab30!!
<hr class='spacer' />
!!tab3!!
<hr class='spacer' />
!!tab4!!
<hr class='spacer' />
!!tab5!!
<hr class='spacer' />
!!tab6!!
<hr class='spacer' />
!!tab7!!
<hr class='spacer' />
!!tab13!!
<hr class='spacer' />
!!tab14!!
<hr class='spacer' />
!!tab8!!
<hr class='spacer' />
!!authperso!!
";

$message_search = "
<div class='row'>
	<h2>".$msg[401]."</h2>
</div>
";

$serial_action_bar = "
<script type=\"text/javascript\">
	<!--

	var has_bulletin = !!nb_bulletins!!;
	var has_expl = !!nb_expl!!;
	var has_arti= !!nb_articles!!;
	var has_etat_coll = !!nb_etat_coll!!;
	var has_abo= !!nb_abo!!;

	function confirm_serial_delete()
	{
	phrase1 = \"$msg[serial_SupConfirm]\";
	phrase2 = \"$msg[serial_SupNbBulletin] \";
	phrase3 = \"$msg[serial_SupExemplaire]\";
	phrase4 = \"$msg[serial_SupArti]\";
	phrase5 = \"$msg[serial_SupEtatColl]\";
	phrase6 = \"$msg[serial_SupAbo]\";" .
			"
	if(!has_bulletin && !has_expl && !has_etat_coll && !has_abo) {
		result = confirm(phrase1);
	}else if(has_bulletin || has_etat_coll || has_abo){
		result = true;
		if(result && has_bulletin)
			result = confirm(phrase2 + has_bulletin + \"\\n\" + phrase1);
		if(result && has_expl)
			result = confirm(phrase3 + has_expl + \"\\n\" + phrase1);
		if(result && has_arti)
			result = confirm(phrase4 + has_arti + \"\\n\" + phrase1);
		if(result && has_etat_coll)
			result = confirm(phrase5 + has_etat_coll + \"\\n\" + phrase1);
		if(result && has_abo)
			result = confirm(phrase6 + has_abo + \"\\n\" + phrase1);
		if(result)
			result = confirm(phrase1);
	}
	if(result)
		document.location = './catalog.php?categ=serials&sub=delete&serial_id=!!serial_id!!';
	}
	-->
</script>
<div class='left'>
	<input type='button' class='bouton' onclick=\"document.location='./catalog.php?categ=serials&sub=serial_form&id=!!serial_id!!'\" value='$msg[62]' />&nbsp;";
if ($z3950_accessible){
	$serial_action_bar .= "<input type='button' class='bouton' value='$msg[notice_z3950_update_bouton]' onclick='document.location=\"./catalog.php?categ=z3950&id_notice=!!serial_id!!&issn=!!issn!!\"' />&nbsp;";
}
$serial_action_bar.="
	<input type='button' class='bouton' value='$msg[4002]' onClick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=bul_form&serial_id=!!serial_id!!&bul_id=0'\" />&nbsp;
	<input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=0'\">
	<input type='button' class='bouton' value='$msg[158]'  onClick=\"document.location='./catalog.php?categ=serials&sub=serial_replace&serial_id=!!serial_id!!'\" />&nbsp;
	<input type='button' class='bouton' value='$msg[serial_duplicate_bouton]'  onClick=\"document.location='./catalog.php?categ=serials&sub=serial_duplicate&serial_id=!!serial_id!!'\" />&nbsp;";
if ($acquisition_active) {
	$serial_action_bar.="<input type='button' class='bouton' value='".$msg["acquisition_sug_do"]."' onclick=\"document.location='./catalog.php?categ=sug&action=modif&id_bibli=0&id_notice=!!serial_id!!'\" />&nbsp;";
}
if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
	$serial_action_bar .= "<input type='button' class='bouton' value='".$msg["scan_request_record_button"]."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=!!serial_id!!\"' />";
}
global $pmb_type_audit;
if ($pmb_type_audit){
	$serial_action_bar.="<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=1&object_id=!!serial_id!!', 'audit_popup', 700, 500, -2, -2, 'sdg')\" title='$msg[audit_button]' value='$msg[audit_button]'/>";
}
$serial_action_bar.="</div>
<div class='right'>
	!!delete_serial_button!!
	</div>
<div class='row'></div>
";

$scan_request_record_button="";
if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
	$scan_request_record_button .= "<input type='button' class='bouton' value='".$msg["scan_request_record_button"]."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_bulletin=!!bul_id!!\"' />";
}
//<input type='button' class='bouton' onclick=\"confirm_serial_delete();\" value='$msg[63]' />
$bul_action_bar = "
<script type=\"text/javascript\">
	<!--

	var has_expl = !!nb_expl!!;

	function confirm_bul_delete()
	{
		phrase1 = \"$msg[serial_SupBulletin]\";
		phrase2 = \"$msg[serial_SupExemplaire]\";

		if(!has_expl) {
			result = confirm(phrase1);
		} else {
			result = confirm(phrase2 + has_expl + \"\\n\" + phrase1);
			if(result)
				result = confirm(phrase1);
		}

		if(result)
			document.location = './catalog.php?categ=serials&sub=bulletinage&action=delete&bul_id=!!bul_id!!';
		else
			document.forms['addex'].elements['noex'].focus();
	}
	-->
</script>
<div class='left'>
	<input type='button' class='bouton' onclick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=bul_form&bul_id=!!bul_id!!'\" value='$msg[62]' />&nbsp;
	<input type='button' class='bouton' onClick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=bul_duplicate&bul_id=!!bul_id!!';\" value='$msg[empr_duplicate_button]' />
	<input type='button' class='bouton' value='$msg[158]'  onClick=\"document.location='./catalog.php?categ=serials&sub=bulletin_replace&serial_id=!!serial_id!!&bul_id=!!bul_id!!'\" />&nbsp;
	".$scan_request_record_button;
	global $pmb_type_audit;
	if ($pmb_type_audit){
		$bul_action_bar.=  "<input class='bouton' type='button' onClick=\"openPopUp('./audit.php?type_obj=3&object_id=!!bul_id!!', 'audit_popup')\" title='$msg[audit_button]' value='$msg[audit_button]' />";
	};
	$bul_action_bar.="
</div>
<div class='right'>
	!!bulletin_delete_button!!
</div>
<div class='row'></div>
";

$serial_bul_content_form = "
<!-- onglet bul -->
<div id='elbulParent' class='parent'>
	<h3>
		<img src='".get_url_icon('minus.gif')."' class='img_plus' class='align_top' name='imEx' id='elbulImg' title=\"".$msg["perio_bull_form_info_bulletin"]."\" border='0' onClick=\"expandBase('elbul', true); return false;\"/>
		".$msg["perio_bull_form_info_bulletin"]."
	</h3>
</div>
<div id='elbulChild' class='child' etirable='yes' title='".htmlentities($msg["perio_bull_form_info_bulletin"],ENT_QUOTES, $charset)."' >
	<div id='elbulChild_0' class='colonne2' title='".htmlentities($msg[4025],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='elbulChild_0a' class='row'>
			<label class='etiquette' for='bul_no'>$msg[4025]</label>
		</div>
		<div id='elbulChild_0b' class='row'>
			<input type='text' id='bul_no' name='bul_no' value='!!bul_no!!' class='saisie-20em' />
			<input type='hidden' name='bul_id' value='!!bul_id!!' />
			<input type='hidden' name='serial_id' value='!!serial_id!!' />
		</div>
	</div>
	<div id='elbulChild_1' class='colonne_suite' title='".htmlentities($msg['bulletin_code_barre'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='elbulChild_1a' class='row'>
			<label class='etiquette' for='bul_cb'>$msg[bulletin_code_barre]</label>
		</div>
		<div id='elbulChild_1b' class='row'>
			<input class='saisie-20emr' id='bul_cb' name='bul_cb' readonly value=\"!!bul_cb!!\" />
			<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./catalog/setcb.php?formulaire_appelant=notice&objet_appelant=bul_cb&bulletin=1&notice_id=!!bul_id!!', 'getcb')\" />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.bul_cb.value=''; \" />
		</div>
	</div>
	<div id='elbulChild_2' class='".($pmb_form_editables == 2 ? 'colonne2' : 'colonne3')."' title='".htmlentities($msg['4026'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='elbulChild_2a' class='row'>
			<label class='etiquette' >$msg[4026]</label>
		</div>
		<div id='elbulChild_2b' class='row'>
			!!date_date!!
		</div>
	</div>
	<div id='elbulChild_3' class='colonne_suite' title='".htmlentities($msg['bulletin_mention_periode'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='elbulChild_3a' class='row'>
			<label class='etiquette' >$msg[bulletin_mention_periode]</label>
		</div>
		<div id='elbulChild_3b' class='row'>
			<input type='text' id='bul_date' name='bul_date' value='!!bul_date!!' class='saisie-50em' />
		</div>
	</div>
	<div id='elbulChild_4' class='row' title='".htmlentities($msg['bulletin_mention_titre'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='elbulChild_4a' class='row'>
			<label class='etiquette' >$msg[bulletin_mention_titre]</label>
		</div>
		<div id='elbulChild_4b' class='row'>
			<input type='text' id='bul_titre' name='bul_titre' value='!!bul_titre!!' class='saisie-50em' />&nbsp;!!create_notice_bul!!
		</div>
	</div>
</div>
<!-- Formulaire de notice de bulletin -->
!!tab0!!
<hr class='spacer' />
!!tab1!!
<!--<hr class='spacer' />
!!tab2!!-->
<hr class='spacer' />
!!tab30!!
<hr class='spacer' />
!!tab3!!
<hr class='spacer' />
!!tab4!!
<hr class='spacer' />
!!tab41!!
<hr class='spacer' />
!!tab5!!
<hr class='spacer' />
!!tab6!!
<hr class='spacer' />
!!tab7!!
<hr class='spacer' />
!!tab13!!
<hr class='spacer' />
!!tab14!!
<hr class='spacer' />
!!tab8!!
<hr class='spacer' />
!!authperso!!
";

/* à partir d'ici, template du forme de catalogage de dépouillement */
//	----------------------------------------------------
// 	  $pdeptab[0] : contenu de l'onglet 0 (zone de titre)

$pdeptab[0] = "
<!-- onglet 0 -->
<div id='el0Parent' class='parent'>
	<h3>
		<img src='".get_url_icon('minus.gif')."' class='img_plus' class='align_top' name='imEx' id='el0Img' title='$msg[236]' border='0' onClick=\"expandBase('el0', true); return false;\" />
		$msg[712]
	</h3>
</div>
<div id='el0Child' class='child' etirable='yes' title='".htmlentities($msg[236],ENT_QUOTES, $charset)."' >
	!!element_tit1!!
	!!element_tit3!!
	!!element_tit4!!
</div>
";

//	----------------------------------------------------
// 	  $pdeptab[2] : contenu de l'onglet 2 (pagination)

$pdeptab[2] = "
<!-- onglet 2 -->
<div id='el2Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el2', 'serial_Pagination')."
		$msg[serial_Pagination]
	</h3>
</div>
<div id='el2Child' class='child' etirable='yes' title='".htmlentities($msg["serial_Pagination"],ENT_QUOTES, $charset)."'>
	<div id='el2Child_0' title='".htmlentities($msg["serial_Pagination"],ENT_QUOTES, $charset)."' movable='yes'>
		<div  id='el2Child_0a' class='row'>
			<label class='etiquette' for='pagination'>".$msg["serial_Pagination"]."</label>
		</div>
		<div id='el2Child_0b' class='row'>
			<input type='text' class='saisie-80em' name='pages' value=\"!!pages!!\">
		</div>
	</div>
</div>
";

$analysis_top_content_form.= "
!!type_catal!!
!!tab0!!
<hr class='spacer' />
!!tab1!!
<hr class='spacer' />
!!tab2!!
<hr class='spacer' />
!!tab3!!
<hr class='spacer' />
!!tab4!!
<hr class='spacer' />
!!tab5!!
<hr class='spacer' />
!!tab6!!";
if ($pmb_use_uniform_title) $analysis_top_content_form .= "<hr class='spacer' />!!tab230!!";
$analysis_top_content_form .= "<hr class='spacer' />
<hr class='spacer' />
!!tab7!!
<hr class='spacer' />
!!tab13!!
<hr class='spacer' />
!!tab14!!
<hr class='spacer' />
!!tab8!!
<hr class='spacer' />
!!authperso!!
";

function notice_bul_form() {
}
$notice_bulletin_form = jscript_unload_question();
$notice_bulletin_form.="<div class='row'>
		<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id=\"expandall\"></a>
		<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id=\"collapseall\"></a>
		!!doc_type!! !!location!!
		<input type=\"hidden\" name=\"b_level\" value=\"!!b_level!!\">
		<input type=\"hidden\" name=\"h_level\" value=\"!!h_level!!\">
		<input type=\"hidden\" name=\"serial_id\" value=\"!!id!!\">
		<input type=\"hidden\" name=\"bul_id\" value=\"!!bul_id!!\">
		<input type=\"hidden\" name=\"analysis_id\" value=\"!!analysis_id!!\">
		<input type=\"hidden\" name=\"id_form\" value=\"!!id_form!!\">
	</div>
	!!serial_bul_form!!
	!!tab0!!
	<hr class='spacer' />
	!!tab1!!
	<hr class='spacer' />
	!!tab2!!
	<hr class='spacer' />
	!!tab3!!
	<hr class='spacer' />
	!!tab4!!
	<hr class='spacer' />
	!!tab5!!
	<hr class='spacer' />
	!!tab6!!
	<hr class='spacer' />
	!!tab7!!
	<hr class='spacer' />
	!!tab8!!
	<hr class='spacer' />
	!!authperso!!
	</div>
";

//$serial_bul_form=str_replace("!!serial_bul_form!!",$serial_bul_form,$notice_bulletin_form);

$liste_script ="
<script type=\"text/javascript\" src=\"./javascript/tablist.js\"></script>
";

// Modif ER suppression du form en liste_debut et liste_fin : <form class='form-$current_module' name=\"notice_list\" class=\"notice-bu\">
$liste_debut ="
<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id=\"expandall\"></a>
<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id=\"collapseall\"></a>
";

$liste_fin = "";

// template pour le form de saisie code barre (périodiques)
// création d'un exemplaire rattaché à un bulletin
//if($pmb_numero_exemplaire_auto>0) $num_exemplaire_test="if(eval(form.option_num_auto.checked == false ))";
if($pmb_numero_exemplaire_auto==1 || $pmb_numero_exemplaire_auto==3) {
	$num_exemplaire_test="var r=false;try { r=form.option_num_auto.checked;} catch(e) {};if(r==false) ";
} else {
	$num_exemplaire_test="";
}
if ($pmb_rfid_activate==1 ) {
	$num_exemplaire_rfid_test="if(0)";
} else {
	$num_exemplaire_rfid_test="";
}
$bul_cb_form = "
<script type='text/javascript'>
<!--
	function test_form(form) {
		$num_exemplaire_rfid_test
		$num_exemplaire_test
		if(form.noex.value.replace(/^\s+|\s+$/g, '').length == 0) {
			alert(\"$msg[292]\");
			document.forms['addex'].elements['noex'].focus();
			return false;
		}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='addex' method='post' action='./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=!!bul_id!!&expl_id=0'>
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
		<input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=0'\">
		!!btn_print_ask!!
	</div>
</form>
<script type='text/javascript'>
	document.forms['addex'].elements['noex'].focus();
</script>
";

//	----------------------------------
//	$bul_expl_form :form de saisie/modif exemplaire de bulletin
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
	$rfid_program_button="<input type=button class='bouton' value=' ". $msg['rfid_configure_etiquette_button']." ' onClick=\"program_rfid_ask();\">";
}else {
	$rfid_script_catalog="";
	$rfid_program_button="";
}

$serial_edit_access ="
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.user_query.value.replace(/^\s+|\s+$/g, '').length == 0){
			alert(\"$msg[141]\");
			form.user_query.focus();
			return false;
		}
		return true;
	}
-->
</script>
<form class='form-$current_module' name='serial_search' method='post' action='./edit.php?categ=serials&sub=!!etat!!'>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_cb'>!!message!!</label>
	</div>
	<div class='row'>
		<input class='saisie-80em' id='user_query' type='text' name='user_query' value='!!user_query!!' />
	</div>
</div>
<div class='row'>
	<input class='bouton' type='submit' value='$msg[89]' onClick='return test_form(this.form)' />
</div>
</form>
<script type=\"text/javascript\">
	document.forms['serial_search'].elements['user_query'].focus();
</script>
";
$serial_edit_access = str_replace('!!user_query!!', htmlentities(stripslashes($user_query ),ENT_QUOTES, $charset), $serial_edit_access);

$serial_list_tmpl = "
<h1>$msg[1152] \"<strong>!!cle!!</strong>\"</h1>
<table border='0' width='100%'>
!!list!!
</table>
<div class='row'>
!!nav_bar!!
</div>
";

// $perio_replace : form remplacement periodique
$perio_replace = "
<form class='form-".$current_module."' name='perio_replace' method='post' action='./catalog.php?categ=serials&sub=serial_replace&serial_id=!!serial_id!!'>
<h3>".$msg['159']." !!old_perio_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>".$msg['160']."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' value='' id='perio_libelle' name='perio_libelle' readonly>
		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=perio&caller=perio_replace&param1=by&param2=perio_libelle&no_display=!!serial_id!!', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
		<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.perio_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' value=''>
	</div>
	!!perio_replace_categories!!
	<div class='row'>
		<input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
		<input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
		<input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
	</div>
</div>
<div class='row'>
	<input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\">
	<input type='submit' class='bouton' value='".$msg['159']."'>
</div>
</form>
";
// $bulletin_replace : form remplacement bulletin
$bulletin_replace = "
<form class='form-".$current_module."' name='bulletin_replace' method='post' action='./catalog.php?categ=serials&sub=bulletin_replace&serial_id=!!serial_id!!&bul_id=!!bul_id!!'>
<h3>".$msg['159']." !!old_bulletin_libelle!! </h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='par'>".$msg['160']."</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-50emr' value='' id='bulletin_libelle' name='bulletin_libelle' readonly>
		<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=bulletin&caller=bulletin_replace&param1=by&param2=bulletin_libelle&no_display=!!bul_id!!', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
		<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.bulletin_libelle.value=''; this.form.by.value='0'; \" />
		<input type='hidden' name='by' value=''>
	</div>
	<div class='row'>
	!!del_depouillement!!
	</div>
	!!bulletin_replace_categories!!
	<div class='row'>
		<input type='radio' name='notice_replace_links' value='0' ".($deflt_notice_replace_links==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
		<input type='radio' name='notice_replace_links' value='1' ".($deflt_notice_replace_links==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
		<input type='radio' name='notice_replace_links' value='2' ".($deflt_notice_replace_links==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
	</div>
</div>

<div class='row'>
	<input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\">
	<input type='submit' class='bouton' value='".$msg['159']."'>
</div>
</form>
";
//	----------------------------------
//	$bul_expl_form1 :form de saisie/modif exemplaire bulletinage

if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
	if($pmb_rfid_driver=="ident") $script_erase="init_rfid_erase(rfid_ack_erase);";
	else $script_erase="rfid_ack_erase(1);";
	$rfid_script_bulletine="
		$rfid_js_header
		<script type='text/javascript'>
			var flag_cb_rfid=0;
			flag_program_rfid_ask=0;
			setTimeout(\"init_rfid_read_cb(0,f_expl);\",0);;

			var msg_rfid_programmation_confirmation = '".addslashes($msg['rfid_programmation_confirmation'])."';
			var msg_rfid_etiquette_programmee_message = '".addslashes($msg['rfid_etiquette_programmee_message'])."';

			function program_rfid() {
				flag_semaphore_rfid=1;
				flag_program_rfid_ask=0;
				var cb = document.getElementById('f_ex_cb').value;
				$script_erase
			}
		</script>
		<script type='text/javascript' src='".$base_path."/javascript/rfid.js'></script>
";

	$rfid_program_button="<input  type=button class='bouton_small' value=' ". $msg['rfid_configure_etiquette_button']." ' onClick=\"program_rfid_ask();\">";
}else {
	$rfid_script_bulletine="";
	$rfid_program_button="";
}


$expl_bulletinage_tpl="
$rfid_script_bulletine
<script type='text/javascript'>
<!--

function test_form(form){
	!!questionrfid!!
	if((form.f_ex_cb.value.replace(/^\s+|\s+$/g, '').length == 0) || (form.expl_cote.value.replace(/^\s+|\s+$/g, '').length == 0)){
		alert(\"$msg[304]\");
		return false;
	}

	if (typeof(form.expl_codestat) == 'undefined') {
			alert(\"".$msg["expl_codestat_mandatory"]."\");
			return false;
	}

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

	<h3>$msg[300]</h3>
	<div class='row'>
		<div class='colonne3'>
			<!-- code barre -->
			<label class='etiquette' for='f_ex_cb'>$msg[291]</label>
			<div class='row'>
				<input type='text' class='text' id=\"f_ex_cb\" value='!!cb!!' name='f_ex_cb' >
			</div>
		</div>
		<div class='colonne3'>
			<!-- cote -->
			<label class='etiquette' for='f_ex_cote'>$msg[296]</label>
			<div class='row'>
				<input type='text' class='text' id=\"f_ex_cote\" name='expl_cote' value='!!cote!!' />
			</div>
		</div>
		<div class='colonne3'>
			<!-- type document -->
			<label class='etiquette' for='expl_typdoc'>$msg[294]</label>
			<div class='row'>
				!!type_doc!!
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='colonne3'>
			<!-- localisation -->
			<label class='etiquette' for='expl_location'>$msg[298]</label>
			<div class='row'>
				!!localisation!!
			</div>
		</div>
		<div class='colonne3'>
			<!-- section -->
			<label class='etiquette' for='f_ex_section'>$msg[295]</label>
			<div class='row'>
				!!section!!
			</div>
		</div>
		<div class='colonne3'>
			<!-- propriétaire -->
			<label class='etiquette' for='expl_owner'>$msg[651]</label>
			<div class='row'>
				!!owner!!
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='colonne3'>
			<!-- statut -->
			<label class='etiquette' for='expl_statut'>$msg[297]</label>
			<div class='row'>
				!!statut!!
			</div>
		</div>
		<div class='colonne3'>
			<!-- code stat -->
			<label class='etiquette' for='expl_codestat'>$msg[299]</label>
			<div class='row'>
				!!codestat!!
			</div>
		</div>
		!!type_antivol!!
	</div>
	<!-- notes -->
	<div class='row'>
		<label class='etiquette' for='f_ex_note'>$msg[expl_message]</label>
	</div>
	<div class='row'>
		<textarea name='expl_note' id='f_ex_note' class='saisie-80em'>!!note!!</textarea>
	</div>
	<div class='row'>
		<label class='etiquette' for='f_ex_comment'>$msg[expl_zone_comment]</label>
	</div>
	<div class='row'>
		<textarea name='expl_comment' id='f_ex_comment' class='saisie-80em'>!!comment!!</textarea>
	</div>
	<!-- prix -->
	<div class='row'>
		<label class='etiquette' for='f_ex_prix'>" . $msg['expl_price'] . "</label>
	</div>
	<div class='row'>
		<input type='text' class='text' name='expl_prix' id='f_ex_prix' value=\"!!prix!!\" />
	</div>
	!!champs_perso!!
	<div class='row'></div>
	<hr />
";
$bul_expl_form1 ="

<form class='form-$current_module' name='expl' id='expl-form' method='post'  enctype='multipart/form-data' action='!!action!!'>
<div class='form-contenu'>

	!!expl_bulletinage_tpl!!

	<h3>".htmlentities($msg['abt_numeric_bulletinage_form'],ENT_QUOTES, $charset)."</h3>
	<div class='row'>

	<label class='etiquette' for='f_filename'>".htmlentities($msg['abt_numeric_bulletinage_form_filename'], ENT_QUOTES,$charset)."</label>
	<br /><input type='text' class='saisie-80em' name='f_filename' id='f_filename' />
	<br /><label class='etiquette' for='f_fichier'>".htmlentities($msg['abt_numeric_bulletinage_form_fichier'], ENT_QUOTES,$charset)."</label>
	<br /><input type='file' size='50' class='saisie-80em' name='f_fichier' id='f_fichier' />
	<br /><label class='etiquette' for='f_url'>".htmlentities($msg['abt_numeric_bulletinage_form_url'], ENT_QUOTES,$charset)."</label>
	<br /><input type='text' class='saisie-80em' name='f_url' id='f_url' />
	<br /><label class='etiquette' for='f_statut'>".htmlentities($msg['abt_numeric_bulletinage_form_statut'], ENT_QUOTES,$charset)."</label>
	<br />!!statut_list!!
	</div>
	<div class='row'></div>

	<hr />
	<h3>$msg[abonnements_titre_donnees_bulletin]</h3>
	<div class='row'>
		<div class='colonne3'>
				<label class='etiquette' for='bul_no'>$msg[4025]</label>
			<div class='row'>
				<input type='text' id='bul_no' name='bul_no' value='!!bul_no!!' class='saisie-20em' />
			</div>
		</div>
		<div class='colonne3'>

			<div class='row'>
			!!destinataire!!
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' >$msg[4026]</label>
		</div>
		<div class='row'>
			!!date_date!!
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' >$msg[bulletin_mention_periode]</label>
		</div>
		<div class='row'>
			<input type='text' id='bul_date' name='bul_date' value='!!bul_date!!' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' >$msg[bulletin_mention_titre]</label>
		</div>
		<div class='row'>
			<input type='text' id='bul_titre' name='bul_titre' value='!!bul_titre!!' class='saisie-50em' />&nbsp;!!create_notice_bul!!
		</div>
	</div>
</div>
	<div class='left'>
		<input type='submit' class='bouton_small' value=' $msg[77] ' name='bouton_enregistre'  />
		$rfid_program_button
	</div>
	<!-- chams de gestion -->
	<input type=\"hidden\" name=\"expl_bulletin\" value=\"!!bul_id!!\">
	<input type=\"hidden\" name=\"id_form\" value=\"!!id_form!!\">
	<input type=\"hidden\" name=\"org_cb\" value=\"!!org_cb!!\">
	<input type=\"hidden\" name=\"expl_id\" value=\"!!expl_id!!\">

	<input type=\"hidden\" name=\"serial_id\" value=\"!!serial_id!!\">
	<input type=\"hidden\" name=\"numero\" value=\"!!numero!!\">
</form>
!!focus!!
";

$analysis_type_form = "
		<div class='row' id='zone_article'>
		<input type='hidden' name='id_sug' value='!!id_sug!!' />
		<div class='colonne3'>
			<h3>".$msg['acquisition_catal_perio']."</h3>
			<input type=\"radio\" id=\"perio_type_use_existing\"  value=\"use_existing\" name=\"perio_type\"  !!perio_type_use_existing!!><label for=\"perio_type_use_existing\">".$msg["acquisition_catal_perio_exist"]."</label>
			<blockquote>
				<div class='row'>
					<label for='f_perio_existing' class='etiquette'>".$msg[233]."</label>
					<div class='row' >
						<input type='text' completion='perio' autfield='serial_id' id='f_perio_existing' class='saisie-30emr' name='f_perio_existing' value=\"\" />
						<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=perio&caller=notice&param1=serial_id&param2=f_perio_existing&deb_rech='+".pmb_escape()."(this.form.f_perio_existing.value), 'selector_notice');this.form.f_bull_existing.value=''; this.form.bul_id.value='0'; \" />
						<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_perio_existing.value=''; this.form.serial_id.value='0';this.form.f_bull_existing.value=''; this.form.bul_id.value='0'; \" />
					</div>
				</div>
			</blockquote>
			<input type=\"radio\" id=\"perio_type_new\"  value=\"insert_new\" name=\"perio_type\" !!perio_type_new!!><label for=\"perio_type_new\">".$msg["acquisition_catal_perio_new"]."</label>
			<blockquote>
				<div class='row'>
					<label for='f_perio_new' class='etiquette'>".$msg[233]."</label>
					<div class='row' >
						<input type='text' id='f_perio_new' class='saisie-50em' name='f_perio_new' value=''/>
					</div>
				</div>
				<div class='row'>
					<label for='f_perio_new_issn' class='etiquette'>".$msg["z3950_issn"]."</label>
					<div class='row' >
						<input type='text' id='f_perio_new_issn' class='saisie-50em' name='f_perio_new_issn' value=''/>
					</div>
				</div>
			</blockquote>
		</div>
		<div class='colonne3'>
			<h3>".$msg['acquisition_catal_bull']."</h3>
			<input type=\"radio\" id=\"bull_type_use_existing\" !!bull_type_use_existing!! value=\"use_existing\" name=\"bull_type\"><label for=\"bull_type_use_existing\">".$msg["acquisition_catal_bull_exist"]."</label>
			<blockquote>
				<div class='row'>
					<label for='f_bull_existing' class='etiquette'>".$msg['abonnements_titre_numerotation']."/".$msg[4026]."</label>
					<div class='row' >
						<input type='text' completion='bull' autfield='bul_id' id='f_bull_existing' class='saisie-30emr' name='f_bull_existing' linkfield='serial_id' value=\"\" ' />
						<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=bulletin&caller=notice&param1=bul_id&param2=f_bull_existing&no_display='+this.form.bul_id.value+'&deb_rech='+".pmb_escape()."(this.form.f_bull_existing.value)+'&idperio='+this.form.serial_id.value, 'selector_notice')\" />
						<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_bull_existing.value=''; this.form.bul_id.value='0'; \" />
					</div>
				</div>
			</blockquote>
			<input type=\"radio\" id=\"bull_type_new\" !!bull_type_new!! value=\"insert_new\" name=\"bull_type\"><label for=\"bull_type_new\">".$msg["acquisition_catal_bull_new"]."</label>
			<blockquote>
				<div class='row'>
					<div class='colonne2'>
						<div class='row'>
							<label for='f_bull_new_num' class='etiquette'>".$msg['abonnements_titre_numerotation']."</label>
						</div>
						<div class='row'>
							<input type='text' id='f_bull_new_num' class='saisie-20em' name='f_bull_new_num' value=''/>
						</div>
					 </div>
					 <div class='colonne2'>
						<div class='row' >
							<label for='f_bull_new_titre' class='etiquette'>".$msg[233]."</label>
						</div>
						<div class='row'>
							<input type='text' id='f_bull_new_titre' class='saisie-50em' name='f_bull_new_titre' value='' />
						</div>
					</div>
				</div>
				<div class='row'>
					<div class='colonne2'>
						<div class='row'>
							<label class='etiquette' >$msg[4026]</label>
						</div>
						<div class='row'>
							!!date_date!!
						</div>
					</div>
					<div class='colonne2'>
						<div class='row'>
							<label class='etiquette' >".$msg['bulletin_mention_periode']."</label>
						</div>
						<div class='row'>
							<input type='text' id='f_bull_new_mention' name='f_bull_new_mention' value='' class='saisie-50em' />
						</div>
					</div>
				</div>
			</blockquote>
		</div>
	</div>
";

$perio_replace_categories = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<label class='etiquette' for='keep_categories_label'>".$msg["perio_replace_keep_categories"]."</label>
</div>
<div class='row'>
	".$msg[39]." <input type='radio' name='keep_categories' value='0' checked='checked' onclick=\"document.getElementById('perio_replace_categories').setAttribute('style','display:none;');\" />
	".$msg[40]." <input type='radio' name='keep_categories' value='1' onclick=\"document.getElementById('perio_replace_categories').setAttribute('style','');\" />
</div>
<div class='row'>&nbsp;</div>
<div class='row' id='perio_replace_categories' style='display:none';>
	!!perio_replace_category!!
	<input type='hidden' id='f_nb_categ' name='f_nb_categ' value='!!nb_categ!!' />
</div>
		";
$perio_replace_category = "
<div class='row'>
	<input type='checkbox' id='f_categ!!icateg!!' name='f_categ!!icateg!!' checked='checked' />
	!!categ_libelle!!
	<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";

$bulletin_replace_categories = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<label class='etiquette' for='keep_categories_label'>".$msg["bulletin_replace_keep_categories"]."</label>
</div>
<div class='row'>
	".$msg[39]." <input type='radio' name='keep_categories' value='0' checked='checked' onclick=\"document.getElementById('bulletin_replace_categories').setAttribute('style','display:none;');\" />
	".$msg[40]." <input type='radio' name='keep_categories' value='1' onclick=\"document.getElementById('bulletin_replace_categories').setAttribute('style','');\" />
</div>
<div class='row'>&nbsp;</div>
<div class='row' id='bulletin_replace_categories' style='display:none';>
	!!bulletin_replace_category!!
	<input type='hidden' id='f_nb_categ' name='f_nb_categ' value='!!nb_categ!!' />
</div>
		";
$bulletin_replace_category = "
<div class='row'>
	<input type='checkbox' id='f_categ!!icateg!!' name='f_categ!!icateg!!' checked='checked' />
	!!categ_libelle!!
	<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";

// $analysis_move : form déplacement dépouillement
$analysis_move = "
<form class='form-$current_module' name='analysis_move' method='post' action='./catalog.php?categ=serials&sub=analysis&action=analysis_move&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!'>
<div class='form-contenu'>
<div class='row'>
<label class='etiquette'>".$msg['analysis_move_sel_perio']."</label>
</div>
<div class='row'>
<input type='text' class='saisie-50emr' value='' id='perio_libelle' name='perio_libelle' readonly>
<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=perio&caller=analysis_move&param1=to_perio&param2=perio_libelle', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.perio_libelle.value=''; this.form.to_perio.value='0'; \" />
<input type='hidden' id='to_perio' name='to_perio' value='0'>
</div>
<div class='row'>
<label class='etiquette'>".$msg['analysis_move_sel_bull']."</label>
</div>
<div class='row'>
<input type='text' class='saisie-50emr' value='' id='bulletin_libelle' name='bulletin_libelle' readonly>
<input class='bouton' type='button' onclick=\"var idperio=document.getElementById('to_perio').value; if(idperio!=0){ openPopUp('./select.php?what=bulletin&caller=analysis_move&param1=to_bul&param2=bulletin_libelle&idperio='+idperio, 'selector_notice'); }else{ alert('".$msg['analysis_move_sel_perio_choose']."'); }\" title='".$msg['157']."' value='".$msg['parcourir']."' />
<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.bulletin_libelle.value=''; this.form.to_bul.value='0'; \" />
<input type='hidden' id='to_bul' name='to_bul' value='0'>
</div>

<div class='row'>
<input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\">
<input type='button' class='bouton' value='".$msg['analysis_move_bouton']."' onClick=\"var to_bul=document.getElementById('to_bul').value; if(to_bul!=0){document.forms['analysis_move'].submit();}else{ alert('".$msg['analysis_move_sel_bull_choose']."'); }\">
</div>
</form>
";

// $bulletin_move : form déplacement bulletin
$bulletin_move = "
<form class='form-$current_module' name='bulletin_move' method='post' action='./catalog.php?categ=serials&sub=bulletinage&action=bul_move&bul_id=!!bul_id!!'>
<div class='form-contenu'>
<div class='row'>
<label class='etiquette'>".$msg['bulletin_move_sel_perio']."</label>
</div>
<div class='row'>
<input type='text' class='saisie-50emr' value='' id='serial_libelle' name='serial_libelle' readonly>
<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=perio&caller=analysis_move&param1=to_serial&param2=serial_libelle', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.serial_libelle.value=''; this.form.to_serial.value='0'; \" />
<input type='hidden' id='to_serial' name='to_serial' value='0'>
</div>
<div class='row'>
<input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\">
<input type='button' class='bouton' value='".$msg['bulletin_move_bouton']."' onClick=\"var to_serial=document.getElementById('to_serial').value; if(to_serial!=0){document.forms['bulletin_move'].submit();}else{ alert('".$msg['bulletin_move_sel_perio_choose']."'); }\">
</div>
</form>
";