<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: proc_planning.class.php,v 1.4.8.1 2025/04/25 14:16:44 qvarin Exp $

global $class_path, $include_path;
require_once($include_path.'/fields.inc.php');
require_once($class_path.'/scheduler/scheduler_planning.class.php');
require_once($class_path.'/parameters.class.php');
require_once($class_path.'/remote_procedure_client.class.php');

if(!defined('INTERNAL')) {define ('INTERNAL',1);}
if(!defined('EXTERNAL')) {define ('EXTERNAL',2);}

class proc_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $msg, $charset;
		global $subaction,$aff_list;
		global $pmb_procedure_server_credentials, $pmb_procedure_server_address;


		if ($subaction == 'change') {
			global $type_proc, $form_procs, $form_procs_remote;
			global $tocsv_checked, $tocsv_sep, $tocsv_filepath, $tocsv_enclosure;
			$tocsv_sep=stripslashes($tocsv_sep);
			$tocsv_enclosure=stripslashes($tocsv_enclosure);
			$tocsv_filepath=stripslashes($tocsv_filepath);
		} else {
			$type_proc = '';
			$form_procs = '';
			if (is_array($param)) {
				foreach ($param as $aparam=>$aparamv) {
					if (is_array($aparamv)) {
						foreach ($aparamv as $sparam=>$sparamv) {
							global ${$sparam};
							${$sparam} = $sparamv;
						}
					} else {
						global ${$aparam};
						${$aparam} = $aparamv;
					}
				}
			}

			$tocsv_checked=(isset($param['tocsv']['checked']) ? $param['tocsv']['checked'] : '');
			$tocsv_sep=(isset($param['tocsv']['sep']) ? $param['tocsv']['sep'] : '');
			$tocsv_filepath=(isset($param['tocsv']['filepath']) ? $param['tocsv']['filepath'] : '');
			$tocsv_enclosure=(isset($param['tocsv']['enclosure']) ? $param['tocsv']['enclosure'] : '');
		}

		$form_task =
		"<script type='text/javascript'>
			function reload_type_proc(obj) {
					document.getElementById('subaction').value='change';
					obj.form.submit();

			}
			function reload(obj) {
					document.getElementById('subaction').value='change';
					obj.form.submit();
			}
		</script>";

		// Procédure interne ou Procédure distante ??
		$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='proc'>".$this->msg['planificateur_proc_type']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='radio' id='type_proc' name='type_proc' value='internal' ".($type_proc == 'internal' ? 'checked' : '')." onchange='reload_type_proc(this);' />".$this->msg['planificateur_proc_internal']."
				<input type='radio' id='type_proc' name='type_proc' value='remote' ".($type_proc == 'remote' ? 'checked' : '')." onchange='reload_type_proc(this);' />".$this->msg['planificateur_proc_remote']."
			</div>
		</div>
		<div class='row'>&nbsp;</div>";

		//procédure interne
		if ($type_proc == 'internal') {
			//Choix d'une procédure
			$form_task .= "
		<div class='row'>
			<div class='colonne3'>
				<label for='proc'>".$this->msg['planificateur_proc_perso']."</label>
			</div>
			<div class='colonne_suite'>
				<select id='form_procs' class='saisie-60em' name='form_procs' onchange='reload(this);'>
					<option value='' >".$this->msg['planificateur_proc_choice']."</option>";
						$requete = "SELECT idproc, name FROM procs order by name";
						$result = pmb_mysql_query($requete);
						while ($row = pmb_mysql_fetch_object($result)) {
							$form_task .=
					"<option value='".$row->idproc."' ".($form_procs == $row->idproc ? 'selected=\'selected\'' : '' ).">".$row->name."</option>";
						}
			$form_task .=
				"</select>
			</div>
		</div>
		<div class='row'>&nbsp;</div>";

			if ($form_procs) {
				$form_task .=
		"<div class='row'>
			<div class='colonne3'>
				<label for='source'>&nbsp;</label>
			</div>
			<div class='colonne_suite' id='param_proc' >";
						$hp=new parameters($form_procs,"procs");
						if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters))
						$form_task .= $hp->gen_form_plann();
				$form_task .=
			"</div>
		</div>";
			}
		} else if ($type_proc == 'remote') {
			$form_task .=
		"<div class='row'>
			<div class='colonne3'>
				<label for='proc'>".$this->msg['planificateur_proc_perso']."</label>
			</div>
			<div class='colonne_suite'>";

			//Procédures Externes
			$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
			if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
				$aremote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
				$procedures = $aremote_procedure_client->get_procs('AP');

				if ($procedures) {
					if ($procedures->error_information->error_code) {
						$form_task .= $msg['remote_procedures_error_server'].":<br /><i>".$procedures->error_information->error_string."</i>";
					} else if (isset($procedures->elements)){
						$form_task .=
				"<select id='form_procs_remote' class='saisie-60em' name='form_procs_remote' onchange='reload(this);'>";
						foreach ($procedures->elements as $aprocedure) {
						    $form_task .=
					"<option value='".$aprocedure->id."' ".($form_procs_remote == $aprocedure->id ? "selected" : "").">".($aprocedure->untested ? "[<i>".$msg['remote_procedures_procedure_non_validated']."</i>]&nbsp;&nbsp;" : '')."<strong>$aprocedure->name</strong></option>";
						}
						$form_task .=
				"</select>";
					} else {
						$form_task .="<br />".$msg['remote_procedures_no_procs']."<br /><br />";
					}
				}
				$form_task .=
			"</div>
		</div>
		<div class='row'>&nbsp;</div>";

				if ($form_procs_remote) {
					$id = $form_procs_remote;
					$procedure = $aremote_procedure_client->get_proc($id,'AP');

					$form_task .=
		"<div class='row'>
			<div class='colonne3'>
				<label for='source'>&nbsp;</label>
			</div>
			<div class='colonne_suite' id='param_proc_remote' >";

					if ($procedure['error_message']) {
						$form_task .= htmlentities($msg['remote_procedures_error_server'], ENT_QUOTES, $charset).":<br /><i>".$procedure['error_message']."</i>";
					} else {
						$the_procedure = $procedure['procedure'];
						if ($the_procedure->params && ($the_procedure->params != "NULL")) {
							$sql = "CREATE TEMPORARY TABLE remote_proc LIKE procs";
							pmb_mysql_query($sql) or die(pmb_mysql_error());

							$sql = "INSERT INTO remote_proc (idproc, name, requete, comment, autorisations, parameters, num_classement) VALUES (0, '".pmb_mysql_escape_string($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".pmb_mysql_escape_string($the_procedure->comment)."', '', '".pmb_mysql_escape_string($the_procedure->params)."', 0)";
							pmb_mysql_query($sql) or die(pmb_mysql_error());
							$idproc = pmb_mysql_insert_id();

							$hp=new parameters($idproc,"remote_proc");
							if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters)) {
								$form_task .= $hp->gen_form_plann();
							}
						}
					}
					$form_task .=
			"</div>
		</div>";
				}
			} else {
				$form_task .=
		"</div>";
			}
		}

		// Export CSV
		$form_task .= "
		<div class='row' ><hr /></div>
		<div class='row'>
			<div class='colonne3'>
				<label for='tocsv_checked'>".$this->msg['planificateur_proc_tocsv']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' id='tocsv_checked' name='tocsv_checked' value='1' ".(($tocsv_checked)? 'checked="checked"' : '')." />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='tocsv_sep'>".$this->msg['planificateur_proc_tocsv_sep']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' id='tocsv_sep' name='tocsv_sep' class='saisie-2em' size='1' value='".htmlentities((($tocsv_sep)?$tocsv_sep:$this->msg['planificateur_proc_tocsv_default_sep']),ENT_QUOTES,$charset)."' />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='tocsv_enclosure'>".$this->msg['planificateur_proc_tocsv_enclosure']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' id='tocsv_enclosure' name='tocsv_enclosure' class='saisie-2em' size='1' value='".htmlentities((($tocsv_enclosure)?$tocsv_enclosure:$this->msg['planificateur_proc_tocsv_default_enclosure']),ENT_QUOTES,$charset)."' />
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='tocsv_filepath'>".$this->msg['planificateur_proc_tocsv_filepath']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' id='tocsv_filepath' name='tocsv_filepath' class='saisie-50em' value='".htmlentities((($tocsv_filepath)?$tocsv_filepath:''),ENT_QUOTES,$charset)."' />
			</div>
		</div>
		<div class='row'>&nbsp;</div>";

		return $form_task;
	}

	public function make_serialized_task_params() {

    	global $type_proc, $form_procs, $form_procs_remote;
    	global $tocsv_checked, $tocsv_sep, $tocsv_filepath, $tocsv_enclosure;
    	global $pmb_procedure_server_credentials, $pmb_procedure_server_address;

		$t = parent::make_serialized_task_params();

		$t['type_proc'] = stripslashes($type_proc);
		$t['form_procs'] = stripslashes($form_procs);
		$t['form_procs_remote'] = stripslashes($form_procs_remote ?? '');

		$t['tocsv']['checked'] = $tocsv_checked;
		$t['tocsv']['sep'] = stripslashes($tocsv_sep);
		$t['tocsv']['filepath'] = stripslashes($tocsv_filepath);
		$t['tocsv']['enclosure'] = stripslashes($tocsv_enclosure);

		if ($form_procs) {
			$hp=new parameters($form_procs,'procs');
			$t['envt']=$hp->make_serialized_parameters_params();
		} else if ($form_procs_remote) {
			$id = $form_procs_remote;

			$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
			if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
				$aremote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
				$procedure = $aremote_procedure_client->get_proc($id,"AP");
				if (!$procedure['error_message']) {
					$the_procedure = $procedure['procedure'];
					if ($the_procedure) {
						$sql = "CREATE TEMPORARY TABLE remote_proc LIKE procs";
						pmb_mysql_query($sql) or die(pmb_mysql_error());

						$sql = "INSERT INTO remote_proc (idproc, name, requete, comment, autorisations, parameters, num_classement) VALUES (0, '".pmb_mysql_escape_string($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".pmb_mysql_escape_string($the_procedure->comment)."', '', '".pmb_mysql_escape_string($the_procedure->params)."', 0)";
						pmb_mysql_query($sql) or die(pmb_mysql_error());
						$idproc = pmb_mysql_insert_id();

						$hp=new parameters($idproc,"remote_proc");
						$t['envt']=$hp->make_serialized_parameters_params();
					}
				}
			}
		}
		return serialize($t);
	}
}