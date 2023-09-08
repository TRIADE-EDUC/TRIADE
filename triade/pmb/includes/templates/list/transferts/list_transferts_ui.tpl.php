<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_ui.tpl.php,v 1.2 2019-05-27 10:12:16 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//*******************************************************************
// Définition des templates pour le parcours des listes de transfert
// en circulation
//*******************************************************************
global $list_transferts_ui_parcours_search_content_form_tpl, $msg, $list_transferts_ui_script_case_a_cocher, $list_transferts_ui_script_chg_date_retour, $list_transferts_ui_no_results, $list_transferts_ui_valid_list_tpl, $list_transferts_ui_reception_valid_list_tpl;

$list_transferts_ui_parcours_search_content_form_tpl = "
<div class='row'>
	<div class='left'>
		<input type='text' size=2 name='nb_per_page' value='!!nb_res!!' onkeyup=\"document.getElementById('!!objects_type!!_nb_per_page').value = this.value;\">&nbsp;".$msg["transferts_parcours_nb_resultats"]."&nbsp;
		!!filters!!
		<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
		<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
		<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
		<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />		
		<input type='submit' class='bouton' name='".$msg["transferts_parcours_bt_actualiser"]."' value='".$msg["transferts_parcours_bt_actualiser"]."'>
	</div>
	<div class='right'>!!edition_link!!</div>
</div>
<div class='row'>&nbsp;</div>
";

$list_transferts_ui_script_case_a_cocher = "
<script language='javascript'>
	var val_sel = false;
	function SelAll(formToCheck) {
		var nb;
		val_sel = !val_sel;
		nb = formToCheck.elements.length;
		for (var i=0;i<nb;i++) {
			var e = formToCheck.elements[i];

			if ((e.type == 'checkbox')&&(e.name.substr(0,4)=='sel_')) {
				e.checked = val_sel;
			}
		}
	}

	function check(cac) {
		cac.checked=!cac.checked;
	}

	function verifChk(formToCheck,valAction) {
		nb = formToCheck.elements.length;
		res = false;
		for (var i=0;i<nb;i++) {
			var e = formToCheck.elements[i];
			if ((e.type == 'checkbox')&&(e.name.substr(0,4)=='sel_'))
				if (e.checked == true) {
					res = true;
					break;
				}
		}
		if (res==true) {
            if(document.getElementById('statut_reception') && document.getElementById('statut_reception_list')) {
                var e = document.getElementById('statut_reception');
                document.getElementById('statut_reception_list').value = e.options[e.selectedIndex].value;
            }
            if(document.getElementById('section_reception') && document.getElementById('section_reception_list')) {
                var e = document.getElementById('section_reception');
                document.getElementById('section_reception_list').value = e.options[e.selectedIndex].value;
            }
			formToCheck.action.value = valAction;
			formToCheck.submit();
		} else {
			alert('".$msg["transferts_circ_pas_de_selection"]."');
		}
	}
</script>
";

$list_transferts_ui_script_chg_date_retour = "
<script language='javascript'>
	function chgDate(dt,idTrans) {
		var url= './ajax.php?module=circ&categ=transferts&action=date_retour&id=' + idTrans + '&dt=' + dt;
		var maj_date = new http_request();
		if(maj_date.request(url)){
			// Il y a une erreur. Afficher le message retourné
			alert ( '" . $msg["540"] . " : ' + maj_date.get_text() );			
		}
	}
</script>
";
$list_transferts_ui_no_results = "<br /><strong style='text-align: center;display:block;'>!!message!!</strong>";

$list_transferts_ui_valid_list_tpl = "
<form name='form_circ_trans' class='form-circ' method='post' action='!!submit_action!!'>
	!!valid_form_title!!
	<div class='form-contenu'>
		<table id='!!objects_type!!_list'>
			!!valid_list!!
		</table>
		!!motif!!
	</div>
	<input type='submit' class='bouton' name='".$msg["89"]."' value='".$msg["89"]."'>
	&nbsp;
	<input type='button' class='bouton' name='".$msg["76"]."' value='".$msg["76"]."' onclick='document.location=\"!!valid_action!!\"'>
	<input type='hidden' name='liste_transfert' value='!!ids!!'>
</form>";

$list_transferts_ui_reception_valid_list_tpl = "
<form name='form_circ_trans' class='form-circ' method='post' action='!!submit_action!!'>
	!!valid_form_title!!
	<div class='form-contenu'>
		<table id='!!objects_type!!_list'>
			!!valid_list!!
		</table>
		<hr />
		<div class='row'>
			<label class='etiquette' for='form_cb_expl'>".$msg["transferts_circ_reception_lbl_statuts"]."</label>
		</div>
		<div class='row'>
				<select id='statut_reception' name='statut_reception'>!!liste_statuts!!</select>
		</div>
	</div>
	<input type='submit' class='bouton' name='".$msg["89"]."' value='".$msg["89"]."'>
	&nbsp;
	<input type='button' class='bouton' name='".$msg["76"]."' value='".$msg["76"]."' onclick='document.location=\"!!valid_action!!\"'>
	<input type='hidden' name='liste_transfert' value='!!ids!!'>
	<input type='hidden' name='liste_section' value=''>
	<script type='text/javascript'>
		function sel_sections(listeM) {
			if (listeM.selectedIndex>0) {
				liste_sel = document.form_circ_trans_valide_reception.liste_transfert.value.split(',');
				nb = liste_sel.length;
				for(i=0;i<nb;i++)
					document.form_circ_trans_valide_reception['section_'+liste_sel[i]].selectedIndex = listeM.selectedIndex-1;
			}
		}
		function gen_liste_section() {
			liste_sel = document.form_circ_trans_valide_reception.liste_transfert.value.split(',');
			nb = liste_sel.length;
			frm_liste =	document.form_circ_trans_valide_reception.liste_section;
			frm_liste.value = '';
			for(i=0;i<nb;i++) {
				sel_en_cours = document.form_circ_trans_valide_reception['section_'+liste_sel[i]];
				//alert(sel_en_cours.options[sel_en_cours.selectedIndex].value);
				frm_liste.value = frm_liste.value + sel_en_cours.options[sel_en_cours.selectedIndex].value + ',';
			}
			frm_liste.value = frm_liste.value.substr(0,frm_liste.value.length-1);
		}
		gen_liste_section();
	</script>
</form>";