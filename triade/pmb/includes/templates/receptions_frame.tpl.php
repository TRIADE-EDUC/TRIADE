<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions_frame.tpl.php,v 1.15 2020/05/11 09:26:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $recept_deliv_form, $msg, $charset, $current_module, $recept_deliv_form_notice, $recept_deliv_form_bull, $applicants_tpl, $pmb_rfid_activate, $pmb_rfid_serveur_url, $recept_deliv_form_expl, $recept_deliv_form_expl_auto, $recept_deliv_form_explnum,$recept_deliv_form_sugg,  $recept_deliv_form_suite, $recept_form_qte_liv, $recept_bt_update, $recept_bt_undo, $recept_bt_next;

//	------------------------------------------------------------------------------
//	$recept_form : template de reception/exemplarisation pour les receptions
//	------------------------------------------------------------------------------

$recept_deliv_form = "
<div class='right'><a href='#' onclick='parent.recept_killFrame();'>".htmlentities($msg['197'],ENT_QUOTES,$charset)."</a></div>
<form class='form-".$current_module."' id='recept_deliv_form' name='recept_deliv_form' method='post' enctype='multipart/form-data' action=\"\">
	<h3>!!lib_acte!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		<div class='erreur' id='msg_client' style='display:none;text-align:center;'>!!msg_client!!</div>
		<!-- info commande -->
		<div class='row'>
			<table class='act_cell' >
				<tbody id='act_tab' >
					<tr>
						<th width='10%'>".htmlentities($msg['acquisition_act_tab_code'], ENT_QUOTES, $charset)."</th>
						<th width='40%'>".htmlentities($msg['acquisition_act_tab_lib'], ENT_QUOTES, $charset)."</th>
						<th width='10%'>".htmlentities($msg['acquisition_qte_cde'], ENT_QUOTES, $charset)."</th>
						<th width='10%'>".htmlentities($msg['acquisition_qte_liv'], ENT_QUOTES, $charset)."</th>
						<th width='10%'>".htmlentities($msg['acquisition_qte_sol'], ENT_QUOTES, $charset)."</th>
						<th width='20%'>".htmlentities($msg['acquisition_lgstat'], ENT_QUOTES, $charset)."</th>
					</tr>
					<tr>
						<td>
							<div class='in_cell_ld' >!!code!!</div>
						</td>
						<td>
							<div class='in_cell_ld' >!!lib!!</div>
						</td>
						<td>
							<div class='in_cell_rd' id='qte_cde' >!!qte_cde!!</div>
						</td>
						<td>
							<div class='in_cell_rd' id='qte_rec' >!!qte_rec!!</div>
						</td>
						<td>
							<div class='in_cell_rd' id='qte_sol' >!!qte_sol!!</div>
						</td>
						<td>
							!!lgstat!!
							<input type='hidden' id='id_lig' name='id_lig' value='!!id_lig!!' /> 
							<input type='hidden' id='typ_lig' name='typ_lig' value='!!typ_lig!!' /> 	
							<input type='hidden' id='id_prod' name='id_prod' value='!!id_prod!!' />
							<input type='hidden' id='no' name='no' value='!!no!!' />
							<input type='hidden' id='previous' name='previous' value='!!previous!!' />
						</td>
					</tr>	
					
					<tr>
						<td colspan='6'>
							<table>
								<tr>
									<td width='20%' >".htmlentities($msg['acquisition_comment_lg'],ENT_QUOTES,$charset)."
										<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('b_edit.png')."' onclick=\"recept_mod_comment('comment_lg_!!id_lig!!');\" >
										<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('cross.png')."' onclick=\"recept_del_comment('comment_lg_!!id_lig!!');\">
									</td>
									<td width='30%'>
										<div id='comment_lg_!!id_lig!!'>!!comment_lg!!</div>
										<div style='display:none;'>
											<textarea id='comment_lg_!!id_lig!!_mod' class='in_cell' rows='2' wrap='virtual' ></textarea>
											<input type='button' value='".$msg['76']."' onclick=\"recept_undo_comment('comment_lg_!!id_lig!!');\" />
											<input type='button' value='".$msg['77']."' onclick=\"recept_upd_comment('comment_lg_!!id_lig!!');\" />
										</div>
									</td>
									<td width='20%'>".htmlentities($msg['acquisition_comment_lo'],ENT_QUOTES,$charset)."
										<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('b_edit.png')."' onclick=\"recept_mod_comment('comment_lo_!!id_lig!!');\" >
										<img style='padding-left:5px;cursor:pointer;width:10px;vertical-align:middle;' src='".get_url_icon('cross.png')."' onclick=\"recept_del_comment('comment_lo_!!id_lig!!');\">
									</td>
									<td width='30%'>
										<div id='comment_lo_!!id_lig!!'>!!comment_lo!!</div>
										<div style='display:none;'>
											<textarea id='comment_lo_!!id_lig!!_mod' class='in_cell' rows='2' wrap='virtual' ></textarea>
											<input type='button' value='".$msg['76']."' onclick=\"recept_undo_comment('comment_lo_!!id_lig!!');\" />
											<input type='button' value='".$msg['77']."' onclick=\"recept_upd_comment('comment_lo_!!id_lig!!');\" />
										</div>
									</td>
								</tr>
								!!applicants_tr!!
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='right'>
				<!-- bt_undo -->
				<!-- qte_liv -->
				<!-- bt_update -->
				<!-- bt_next -->
			</div>
		<br />
		";

$recept_deliv_form_notice ="
		<hr />!!notice!!
";		

$recept_deliv_form_bull ="
		<hr />!!bulletin!!
";

$applicants_tpl = "<tr>
					<td colspan='2'>".htmlentities($msg['acquisition_applicants'],ENT_QUOTES,$charset)."</td>
					<td colspan='2'>
						!!std_applicants!!
					</td>
				   </tr>";		

		
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
				var nbparts=1; 
				
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


$recept_deliv_form_expl = "
		$rfid_script_catalog
		<hr /><h3>".htmlentities($msg[376], ENT_QUOTES, $charset)."</h3>
		
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_cb'>".htmlentities($msg[291],ENT_QUOTES,$charset)."</label>
				<br />
				<input type='text' class='saisie-20em' id='f_ex_cb' value='!!cb!!' name='f_ex_cb' />
				<!-- option_num_auto -->
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_cote'>".htmlentities($msg[296],ENT_QUOTES,$charset)."</label>
				<br />
				<input type='text' class='saisie-20em' id='f_ex_cote' name='f_ex_cote' value='!!cote!!' !!expl_ajax_cote!! />
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_typdoc'>".htmlentities($msg[294],ENT_QUOTES,$charset)."</label>
				<br />!!type_doc!!
			</div>
		</div>
		
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_location'>".htmlentities($msg[298],ENT_QUOTES,$charset)."</label>
				<br />!!localisation!!
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_section'>".htmlentities($msg[295],ENT_QUOTES,$charset)."</label>
				<br />!!section!!
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_owner'>".htmlentities($msg[651],ENT_QUOTES,$charset)."</label> 
				<br />!!owner!!
			</div>
		</div>
		
		<div class='row'>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_statut'>".htmlentities($msg[297],ENT_QUOTES,$charset)."</label>
				<br />!!statut!!
			</div>
			<div class='colonne3'>
				<label class='etiquette' for='f_ex_cstat'>".htmlentities($msg[299],ENT_QUOTES,$charset)."</label>
				<br />!!codestat!!
			</div>
		</div>
		
		<div class='row'>
			<div class='colonne2'>
				<label class='etiquette' for='f_ex_note'>".htmlentities($msg['expl_message'],ENT_QUOTES,$charset)."</label>
				<br /><textarea name='f_ex_note' id='f_ex_note' class='saisie-80em'></textarea>		
			</div>
			<div class='colonne2'>
				<label class='etiquette' for='f_ex_comment'>".htmlentities($msg['expl_zone_comment'],ENT_QUOTES,$charset)."</label>
				<br /><textarea name='f_ex_comment' id='f_ex_comment' class='saisie-80em'></textarea>
			</div>
		</div>

		<div class='row'>
			<label class='etiquette' for='f_ex_prix'>".htmlentities($msg['expl_price'],ENT_QUOTES,$charset)."</label>
			<br />
			<input type='text' class='text' name='f_ex_prix' id='f_ex_prix' value='!!prix!!' />
		</div>
		<div class='row'>!!champs_perso!!</div>
		<div class='row'>
			<div class='right'>
				$rfid_program_button <input type='button' class='bouton_small' value='".htmlentities($msg['acquisition_recept_explnum_add'],ENT_QUOTES, $charset)."' onclick='recept_add_expl(this.form);' />
			</div>
		</div>
		<br />
";		

$recept_deliv_form_expl_auto = "
&nbsp;<input type='checkbox' name='option_num_auto' id='option_num_auto' value='1' !!checked!! />
&nbsp;<label class='etiquette' for='option_num_auto' >".htmlentities($msg['acquisition_recept_auto'],ENT_QUOTES, $charset)."</label>
";

$recept_deliv_form_explnum = "		
		<hr /><h3>".htmlentities($msg['acquisition_recept_explnum'],ENT_QUOTES, $charset)."</h3>
		<div class='row'> 
			<div class='colonne4'>
				<label class='etiquette' for='f_fichier'>".htmlentities($msg['explnum_fichier'], ENT_QUOTES,$charset)."</label>
				<br /><label class='etiquette' for='f_url'>".htmlentities($msg['explnum_url'], ENT_QUOTES,$charset)."</label>
				<br /><label class='etiquette' for='f_explnum_statut'>".htmlentities($msg['acquisition_recept_explnum_statut'], ENT_QUOTES,$charset)."</label>
			</div>
			<div class='colonne2'>
				<input type='file' size='50' class='saisie-80em' name='f_fichier' id='f_fichier' />
				<br /><input type='text' class='saisie-80em' name='f_url' id='f_url' />
				<br />!!statut_list!!
			</div>
			<div class='colonne4'>
				<div class='right'>
					<br /><input type='button' class='bouton_small' value='".htmlentities($msg['acquisition_recept_explnum_add'],ENT_QUOTES, $charset)."' onclick='recept_upload_file(this.form);' />
				</div>
			</div>
		</div>
		<div class='row'></div>		
";

$recept_deliv_form_sugg = "		
		<hr /><h3>".htmlentities($msg['acquisition_sug'], ENT_QUOTES, $charset)."</h3>
		<div class='row'>
			<div class='colonne2'><!-- origines --></div>
			<div class='colonne2'>".htmlentities($msg['acquisition_recept_chg_stat_sug'], ENT_QUOTES, $charset)."&nbsp;<!-- sel_sugstat -->&nbsp;<input type='button' onclick='recept_update_sug(this.form);' value=".htmlentities($msg['acquisition_recept_bt_upd_sug'],ENT_QUOTES, $charset)." class='bouton_small'></div>
		</div>
		<div class='row'></div>	
		<input type='hidden' id='id_sug' name='id_sug' value='!!id_sug!!' />
";		

$recept_deliv_form_suite = "	
	</div>
</form>
<script type='text/javascript' src='./../../../javascript/ajax.js'></script>
<script type='text/javascript' src='./../../../javascript/tablist.js'></script>
<script type='text/javascript' src='./../../../javascript/receptions_frame.js'></script>
<script type='text/javascript' >
	var msg_error_cb_cote = '".addslashes($msg[304])."';
	var msg_error_cb = '".addslashes($msg[302])."';
	var msg_acquisition_recept_qte_err = '".addslashes($msg['acquisition_recept_qte_err'])."';
	ajax_parse_dom();
</script>	
";

$recept_form_qte_liv = "<input type='text' class='saisie-5em' style='text-align:right;' id='qte_liv' name='qte_liv' value='!!qte_sol!!' />&nbsp;";

$recept_bt_update = "<input type='button' class='bouton_small' value='".htmlentities($msg['acquisition_recept_deliv_save'],ENT_QUOTES, $charset)."' onclick='recept_update(this.form);' />";

$recept_bt_undo = "<input type='button' class='bouton_small' value='".htmlentities($msg['acquisition_recept_deliv_undo'],ENT_QUOTES, $charset)."' onclick='recept_undo(this.form);' />&nbsp;";

$recept_bt_next = "<input type='button' class='bouton_small' value='".htmlentities($msg[502],ENT_QUOTES, $charset)."' onclick='recept_next(1);' />&nbsp;";
