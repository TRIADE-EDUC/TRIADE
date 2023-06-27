<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: taches.tpl.php,v 1.6 2019-05-27 13:43:57 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $planificateur_id, $subaction, $template_result, $base_path, $msg, $admin_planificateur_global_params, $planificateur_form;

if(!isset($planificateur_id)) $planificateur_id = 0;
if(!isset($subaction)) $subaction = '';

$template_result = "
<form name='planificateur_form_del' action='$base_path/admin.php?categ=planificateur&sub=manager&task=task_del&planificateur_id=".$planificateur_id."&confirm=1' method='post' class='form-$current_module'>
	<h3>".$msg["planificateur_task_type_task"]." : !!libelle_type_task!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			!!BODY!!
		</div>
	</div>
</form>
";

$admin_planificateur_global_params="
!!script_js!!
<form name='planificateur_global_form' action='$base_path/admin.php?categ=planificateur&sub=manager&act=update&type_task_id=!!id!!' method='post' class='form-$current_module'>
	<h3>".$msg["planificateur_properties"]." !!comment!!</h3>
	<div class='form-contenu'>
		!!div_upload!!
		<div class='row'>
			<div class='colonne3'><label for='timeout'/>".$msg["planificateur_timeout"]."</label></div><div class='colonne_suite'><input type='text' name='timeout' id='timeout' value='!!timeout!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='histo_day'/>".$msg["planificateur_conserv_histo_day"]."</label></div><div class='colonne_suite'><input type='text' name='histo_day' id='histo_day' value='!!histo_day!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='histo_number'/>".$msg["planificateur_histo_number_conserv"]."</label></div><div class='colonne_suite'><input type='text' name='histo_number' id='histo_number' value='!!histo_number!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='restart_on_failure'/>".$msg["planificateur_restart_on_failure"]."</label></div><div class='colonne_suite'><input type='checkbox' name='restart_on_failure' id='restart_on_failure' value='1' !!restart_on_failure_checked!! /></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='alert_mail_on_failure'/>".$msg["planificateur_alert_mail_on_failure"]."</label></div><div class='colonne_suite'><input type='checkbox' name='alert_mail_on_failure' id='alert_mail_on_failure' value='1' !!alert_mail_on_failure_checked!! /></div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div><div class='colonne_suite'>".$msg["planificateur_mail_on_failure"]."<input type='text' name='mail_on_failure' id='mail_on_failure' value='!!mail_on_failure!!' class='saisie-30em'/></div>
		</div>
		<div class='row'>	
			!!special_form!!
		</div>
	</div>
	<div class='row'><input type='button' value='".$msg["76"]."' class='bouton' onClick='history.go(-1);'/>&nbsp;<input type='submit' value='".$msg["77"]."' class='bouton'/></div> 
</form>";

//template form planificateur
$planificateur_form="
!!script_js!!
<form name='planificateur_form' action='!!bt_save!!' method='post' onSubmit='!!submit_action!!' class='form-$current_module' enctype='multipart/form-data'>
	<h3>".$msg["planificateur_task_type_task"]." : !!libelle_type_task!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'><label for='task_name'/>".$msg["planificateur_task_name"]."</label></div><div class='colonne_suite'><input type='text' name='task_name' id='task_name' value='!!task_name!!' class='saisie-30em'/></div>
		</div>
		<div class='row'>
		<div class='row'>
			<div class='colonne3'><label for='task_desc'/>".$msg["planificateur_task_desc"]."</label></div><div class='colonne_suite'><textarea name='task_desc' id='task_desc' class='saisie-30em'/>!!task_desc!!</textarea></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='form_users'/>".$msg["planificateur_task_users"]."</label></div><div class='colonne_suite'>!!task_users!!</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='task_active'/>".$msg["planificateur_task_active"]."</label></div><div class='colonne_suite'>!!task_statut!!</div>
		</div>
		!!div_upload!!
		<div class='row'>
			&nbsp;
		</div>
		<div class='row'>
			<hr>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='choice_perio'/>".$msg["planificateur_task_choice_perio"]."</label></div>			
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				".$msg["planificateur_task_msg_perio"]."
				!!task_perio_heure!!
				".$msg["planificateur_task_heure"]."
				!!task_perio_min!!
				".$msg["planificateur_task_minute"]."
				!!help!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='space'/>&nbsp;</label></div>
			<div class='colonne_suite'>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				".$msg["planificateur_task_quotidien"]."
			</div>
			<div class='colonne_suite'>
				!!task_perio_quotidien!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				".$msg["planificateur_task_msg_perio_2"]."
			</div>
			<div class='colonne_suite'>
				!!task_perio_hebdo!!
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='space'/>&nbsp;</label></div>
			<div class='colonne_suite'>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				".$msg["planificateur_task_msg_perio_3"]."
			</div>
			<div class='colonne_suite'>
				!!task_perio_mensuel!!
			</div>
		</div>
		<div class='row'>
			&nbsp;
		</div>
		<div class='row'>
			<hr>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='timeout'/>".$msg["planificateur_timeout"]."</label></div><div class='colonne_suite'><input type='text' name='timeout' id='timeout' value='!!timeout!!' class='saisie-5em'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='choice_histo_exec'/>&nbsp;</label></div>
				<div class='colonne_suite'>
					<input type='radio' name='radio_histo_day_number' id='radio_histo_day' value='!!histo_day!!' !!histo_day_checked!! onchange='changeHisto();' />".$msg["planificateur_conserv_histo_day"]."
					<input type='radio' name='radio_histo_day_number' id='radio_histo_number' value='!!histo_number!!' !!histo_number_checked!!  onchange='changeHisto();' />".$msg["planificateur_histo_number_conserv"]."
				</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='histo_day'/>".$msg["planificateur_conserv_histo_day"]."</label></div>
				<div class='colonne_suite'>
					<input type='text' name='histo_day' id='histo_day' value='!!histo_day!!' class='saisie-5em' !!histo_day_visible!! />
				</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='histo_number'/>".$msg["planificateur_histo_number_conserv"]."</label></div>
			<div class='colonne_suite'>
				<input type='text' name='histo_number' id='histo_number' value='!!histo_number!!' class='saisie-5em' !!histo_number_visible!! />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='restart_on_failure'/>".$msg["planificateur_restart_on_failure"]."</label></div>
			<div class='colonne_suite'>
				<input type='checkbox' name='restart_on_failure' id='restart_on_failure' value='1' !!restart_on_failure_checked!! />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='alert_mail_on_failure'/>".$msg["planificateur_alert_mail_on_failure"]."</label></div>
			<div class='colonne_suite'>
				<input type='checkbox' name='alert_mail_on_failure' id='alert_mail_on_failure' value='1' !!alert_mail_on_failure_checked!! />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>&nbsp;</div>
			<div class='colonne_suite'>
				".$msg["planificateur_mail_on_failure"]."<input type='text' name='mail_on_failure' id='mail_on_failure' value='!!mail_on_failure!!' class='saisie-30em'/>
			</div>
		</div>
		<div class='row'>
			&nbsp;
		</div>
		<div class='row'>
			<hr>
		</div>	
		<div class='row'>&nbsp;</div>
			!!specific_form!!
	<div class='row'>
	<input type='hidden' id='subaction' name='subaction' value='".$subaction."' />
	<input type='button' value='".$msg["76"]."' class='bouton' onClick='history.go(-1);'/>&nbsp;<input type='submit' name='save_task' value='".$msg["77"]."' class='bouton' />&nbsp;!!bt_duplicate!!<div class='right'>!!bt_supprimer!!</div>
	</div>
</form>
<script type='text/javascript'>document.forms['planificateur_form'].elements['task_name'].focus();</script>
<script type='text/javascript'>
	if (document.getElementById('radio_histo_day').checked) {
		document.getElementById('histo_day').disabled = false;
		document.getElementById('histo_number').disabled = true;
	} else {
		document.getElementById('histo_day').disabled = true;
		document.getElementById('histo_number').disabled = false;
	}
</script>
<script type='text/javascript'>
		function changeStatut(){
			if (document.getElementById('task_active').value == '1') {
				document.getElementById('task_active').value = '0';
			} else {
				document.getElementById('task_active').value = '1';
			}
		}
		function changeHisto(){
			if (document.getElementById('radio_histo_day').checked) {
				document.getElementById('histo_day').disabled = false;
				document.getElementById('histo_number').disabled = true;
			} else {
				document.getElementById('histo_day').disabled = true;
				document.getElementById('histo_number').disabled = false;
			}
		}
		function changePerio(i,chkbx_tab, nb_value){
			if ((i != '*')) { 
				if (document.getElementById(chkbx_tab+'_'+i).checked == true) {
					var nb=0;
					for (j=1; j<=nb_value; j++) {
						if (document.getElementById(chkbx_tab+'_'+j).checked) {
							nb++;
						}
					}
					if (nb == nb_value) {
						document.getElementById(chkbx_tab+'_0').checked = true;
					} else {
						document.getElementById(chkbx_tab+'_0').checked = false;
					}
				} else {
					document.getElementById(chkbx_tab+'_0').checked = false;
				}
			} else {
				if (document.getElementById(chkbx_tab+'_0').checked == true) {
					for (i=1; i<=nb_value; i++) {
						document.getElementById(chkbx_tab+'_'+i).checked = true;
					}	
				} else {
					var nb=0;
					for (j=1; j<=nb_value; j++) {
						if (document.getElementById(chkbx_tab+'_'+j).checked) {
							nb++;
						}
					}
					if (nb == nb_value) {
						document.getElementById(chkbx_tab+'_0').checked = true;
					}
				}
			}
		}
		function checkForm() {
			document.getElementById('subaction').value='save';
			var heure = document.getElementById('task_perio_heure').value;
			var min = document.getElementById('task_perio_min').value;
			var reg_horaire_fixe = new RegExp('^[0-9]{1,2}$');
			var reg_horaire_intervalle = new RegExp('^[0-9]{1,2}[-]{1}[0-9]{1,2}$');
			var reg_horaire_intervalle_repeat = new RegExp('^[0-9]{1,2}[-]{1}[0-9]{1,2}[{]{1}[0-9]{1,2}[}]{1}');
			var reg_number = new RegExp('^[0-9]{1,6}$');
			
			if (document.getElementById('task_name').value == '') {
				alert(\"$msg[planificateur_alert_name]\");
				return false;
			}
			if (document.forms[0].form_users.value == '') {
				alert(\"$msg[planificateur_alert_user]\");
				return false;
			}
			if (document.getElementById('path')) {
				if (document.getElementById('path').value == '') {
					alert(\"$msg[planificateur_alert_upload]\");
					return false;
				}
			}
			if ((heure != '') && (heure != '*')) {
				if (reg_horaire_fixe.test(heure)) {
					if ((heure < 0) || (heure > 23)) {
						alert(\"$msg[planificateur_alert_heure]\");
						return false;
					}
				} else if (reg_horaire_intervalle.test(heure)) {
					var heure_exp = heure.split('-'); 
					if ((heure_exp[0] < 0) || (heure_exp[0] > 23) || (heure_exp[1] < 0) || (heure_exp[1] > 23)) {
						alert(\"$msg[planificateur_alert_heure]\");
						return false;
					}
				} else if (reg_horaire_intervalle_repeat.test(heure)) {
					var reg_h=new RegExp('[-{}]+');
					var heure_exp = heure.split(reg_h);
					if ((heure_exp[0] < 0) || (heure_exp[0] > 23) || (heure_exp[1] < 0) || (heure_exp[1] > 23)) {
						alert(\"$msg[planificateur_alert_heure]\");
						return false;
					}
				} else {
					alert(\"$msg[planificateur_alert_heure]\");
					return false;
				}
			}
			if ((min != '') && (min != '*')) {
				if (reg_horaire_fixe.test(min)) {
					if ((min < 0) || (min > 59)) {
						alert(\"$msg[planificateur_alert_min]\");
						return false;
					}
				} else if (reg_horaire_intervalle.test(min)) {
					var min_exp = min.split('-'); 
					if ((min_exp[0] < 0) || (min_exp[0] > 59) || (min_exp[1] < 0) || (min_exp[1] > 59)) {
						alert(\"$msg[planificateur_alert_min]\");
						return false;
					}
				} else if (reg_horaire_intervalle_repeat.test(min)) {
					var reg_m=new RegExp('[-{}]+');
					var min_exp = min.split(reg_m);
					if ((min_exp[0] < 0) || (min_exp[0] > 59) || (min_exp[1] < 0) || (min_exp[1] > 59)) {
						alert(\"$msg[planificateur_alert_min]\");
						return false;
					}
				} else {
					alert(\"$msg[planificateur_alert_min]\");
					return false;
				}
			}
			if (document.getElementById('timeout').value != '') {
				if (reg_number.test(document.getElementById('timeout').value) == false) {
					alert(\"$msg[planificateur_alert_timeout]\");
					return false;
				}
			}
			if (document.getElementById('histo_day')) {
				if (document.getElementById('histo_day').value != '') {
					if (reg_number.test(document.getElementById('histo_day').value) == false) {
						alert(\"$msg[planificateur_alert_histoday]\");
						return false;
					}
				}
			}
			if (document.getElementById('histo_number')) {
				if (document.getElementById('histo_number').value != '') {
					if (reg_number.test(document.getElementById('histo_number').value) == false) {
						alert(\"$msg[planificateur_alert_histonumber]\");
						return false;
					}
				}
			}
		}		
		</script>		
";

?>
