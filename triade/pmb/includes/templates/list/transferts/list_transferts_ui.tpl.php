<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_ui.tpl.php,v 1.4 2022/03/09 12:47:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//*******************************************************************
// Définition des templates pour le parcours des listes de transfert
// en circulation
//*******************************************************************
global $list_transferts_ui_parcours_search_content_form_tpl, $msg, $list_transferts_ui_script_case_a_cocher, $list_transferts_ui_script_chg_date_retour, $list_transferts_ui_no_results, $list_transferts_ui_valid_list_tpl, $list_transferts_ui_reception_valid_list_tpl;

$list_transferts_ui_parcours_search_content_form_tpl = "
<div class='row'>
	<input type='text' size=2 name='nb_per_page' value='!!nb_res!!' onkeyup=\"document.getElementById('!!objects_type!!_nb_per_page').value = this.value;\">&nbsp;".$msg["transferts_parcours_nb_resultats"]."&nbsp;
	<div id='!!objects_type!!_search_content' class='list_ui_search_content !!objects_type!!_search_content' style='display:!!unfolded_filters!!;'>
		!!list_search_content_form_tpl!!
	</div>
	!!list_options_content_form_tpl!!
	!!list_datasets_my_content_form_tpl!!
	!!list_datasets_shared_content_form_tpl!!
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='left'>
		<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
		<input type='hidden' id='!!objects_type!!_json_selected_columns' name='!!objects_type!!_json_selected_columns' value='!!json_selected_columns!!' />
		<input type='hidden' id='!!objects_type!!_json_applied_group' name='!!objects_type!!_json_applied_group' value='!!json_applied_group!!' />
		<input type='hidden' id='!!objects_type!!_json_applied_sort' name='!!objects_type!!_json_applied_sort' value='!!json_applied_sort!!' />
		<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
		<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
		<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />
		<input type='hidden' id='!!objects_type!!_selected_filters' name='!!objects_type!!_selected_filters' value='!!selected_filters!!' />
		<input type='hidden' id='!!objects_type!!_ancre' name='!!objects_type!!_ancre' value='!!ancre!!' />
		<input type='hidden' id='!!objects_type!!_go_directly_to_ancre' name='!!objects_type!!_go_directly_to_ancre' value='!!go_directly_to_ancre!!' />
		<input type='hidden' id='!!objects_type!!_initialization' name='!!objects_type!!_initialization' value='' />
		<input type='hidden' id='!!objects_type!!_applied_action' name='!!objects_type!!_applied_action' value='' />
		<input type='submit' class='bouton' name='".$msg["transferts_parcours_bt_actualiser"]."' value='".$msg["transferts_parcours_bt_actualiser"]."'>&nbsp;
		!!list_button_save!!
		!!list_button_initialization!!
		!!list_button_add!!
		!!list_buttons_extension!!
	</div>
	<div class='right'>!!edition_link!!</div>
</div>
<div class='row'>&nbsp;</div>
";

$list_transferts_ui_script_case_a_cocher = "
<script language='javascript'>
	function check(cac) {
		cac.checked=!cac.checked;
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