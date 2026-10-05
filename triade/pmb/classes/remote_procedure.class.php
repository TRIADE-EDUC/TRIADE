<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: remote_procedure.class.php,v 1.6 2024/03/22 15:31:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/remote_procedure_client.class.php");
require_once($class_path."/parameters.class.php");

// définition de la classe de gestion d'une procédure distante

class remote_procedure {
	
	protected $id;
	
	protected $module;
	
	protected $table;
	
	protected $the_procedure;
	
	protected $remote_procedure_client;
	
	public function __construct($id, $module, $table) {
		$this->id = $id;
		$this->module = $module;
		$this->table = $table;
	}
	
	public function instanciate_procedure() {
		global $msg, $charset;
		global $pmb_procedure_server_credentials, $pmb_procedure_server_address;
		global $remote_type;
		
		$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
		$the_procedure = 0;
		if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
			$this->remote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
			if($this->module == 'admin') {
				$procedure = $this->remote_procedure_client->get_proc($this->id, 'AP');
			} elseif($this->module == 'circ') {
				$procedure = $this->remote_procedure_client->get_proc($this->id);
			} else {
				$procedure = $this->remote_procedure_client->get_proc($this->id,$remote_type);
			}
			if ($procedure["error_message"]) {
				$buf_contenu=htmlentities($msg["remote_procedures_error_server"], ENT_QUOTES, $charset).":<br><i>".$procedure["error_message"]."</i>";
				print $buf_contenu;
			} else {
				$this->the_procedure = $procedure["procedure"];
				switch ($this->module) {
					case 'circ':
						break;
					case 'catalog':
						caddie_procs::get_parameters_remote();
						global $allowed_proc_types;
						if (!in_array($this->the_procedure->type, $allowed_proc_types)) {
							echo htmlentities($msg["remote_procedures_circ_nocatalogproc"],ENT_QUOTES, $charset);
							return false;
						}
						break;
				}
				return true;
			}
		}
		echo htmlentities($msg["remote_procedures_error_client"],ENT_QUOTES, $charset);
		return false;
	}

	public function display() {
		global $msg, $charset;
		global $admin_proc_view_remote;
		global $cart_proc_view_remote;
		global $type_list;
		
		if ($this->instanciate_procedure()) {
			$the_procedure = $this->get_the_procedure();
			
			if($this->module == 'admin') {
				$form = $admin_proc_view_remote;
			} else {
				$form = $cart_proc_view_remote;
			}
			$form = str_replace('!!id!!', $this->id, $form);
			$form = str_replace('!!form_title!!', htmlentities($msg["remote_procedures_detail_procedure_distante"],ENT_QUOTES, $charset), $form);
		
			$additional_information = $the_procedure->untested ? $msg["remote_procedures_procedure_non_validated_additional_information"] : "";
			$form = str_replace('!!additional_information!!', htmlentities($additional_information,ENT_QUOTES, $charset), $form);
			$form = str_replace('!!name!!', htmlentities($the_procedure->name,ENT_QUOTES, $charset), $form);
			$form = str_replace('!!name_suppr!!', htmlentities(addslashes($the_procedure->name),ENT_QUOTES, $charset), $form);
			if($this->module != 'admin') {
				$form = str_replace('!!ptype!!', htmlentities(($the_procedure->type == "PEMPS" ? $msg["caddie_procs_type_SELECT"] : $msg["caddie_procs_type_ACTION"]),ENT_QUOTES, $charset), $form);
			}
			$form = str_replace('!!code!!', htmlentities($the_procedure->sql,ENT_QUOTES, $charset), $form);
			$form = str_replace('!!comment!!', htmlentities($the_procedure->comment,ENT_QUOTES, $charset), $form);
		
			$parameters = $the_procedure->params;
			$parameters = $this->remote_procedure_client->parse_parameters($parameters);
			//	highlight_string(print_r($parameters, true));
			if ($parameters) {
				$form = str_replace('!!parameters_title!!', "<label class='etiquette' for='form_comment'>".htmlentities($msg["remote_procedures_procedure_parameters"],ENT_QUOTES, $charset)."</label>", $form);
				$parameters_display = '<table><tr><th>'.htmlentities($msg["remote_procedures_procedure_parameters_name"],ENT_QUOTES, $charset).'</th><th>'.htmlentities($msg["remote_procedures_procedure_parameters_title"],ENT_QUOTES, $charset).'</th><th>'.htmlentities($msg["remote_procedures_procedure_parameters_type"],ENT_QUOTES, $charset).'</th><th>'.htmlentities($msg["remote_procedures_procedure_parameters"],ENT_QUOTES, $charset).'</th></tr>';
				$parity = 0;
				foreach($parameters as $parametername => $parameter) {
					$pair_impair = $parity++ % 2 ? "even" : "odd";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					$parameters_display .= '<tr class="'.$pair_impair.'" '.$tr_javascript.'>';
					$parameters_display .= '<td class="center">'.htmlentities($parametername,ENT_QUOTES, $charset).'</td>';
					$parameters_display .= '<td class="center">'.htmlentities($parameter["title"]['value'],ENT_QUOTES, $charset).'</td>';
					$parameters_display .= '<td class="center">'.htmlentities($type_list[$parameter["type"]["value"]],ENT_QUOTES, $charset).'</td>';
					switch ($parameter["type"]["value"]) {
						case "query_list":
							$parameters_display .= '<td><ul><li>'.htmlentities($msg["procs_options_requete"], ENT_QUOTES, $charset).': '.htmlentities($parameter["options"]["QUERY"][0]["value"],ENT_QUOTES, $charset).'</li><li>'.htmlentities($msg["procs_options_liste_multi"], ENT_QUOTES, $charset).': '.($parameter["options"]["MULTIPLE"][0]["value"] == "yes" ? htmlentities($msg["40"], ENT_QUOTES, $charset) : htmlentities($msg["39"], ENT_QUOTES, $charset)).'</li></ul></td>';
							break;
						case "text":
							$parameters_display .= '<td><ul><li>'.htmlentities($msg["procs_options_text_taille"], ENT_QUOTES, $charset).': '.htmlentities($parameter["options"]["SIZE"][0]["value"],ENT_QUOTES, $charset).'</li><li>'.htmlentities($msg["procs_options_text_max"], ENT_QUOTES, $charset).': '.($parameter["options"]["MAXSIZE"][0]["value"]).'</li></ul></td>';
							break;
						case "list":
							$parameters_display .= '<td><ul>';
							$parameters_display .= '<li>'.htmlentities($msg["procs_options_liste_multi"], ENT_QUOTES, $charset).': '.($parameter["options"]["MULTIPLE"][0]["value"] == "yes" ? htmlentities($msg["40"], ENT_QUOTES, $charset) : htmlentities($msg["39"], ENT_QUOTES, $charset)).'</li>';
							$parameters_display .= '<li>'.htmlentities($msg["procs_options_choix_vide"], ENT_QUOTES, $charset).': '.(htmlentities($parameter["options"]["UNSELECT_ITEM"][0]["value"],ENT_QUOTES, $charset)).' ('.htmlentities($parameter["options"]["UNSELECT_ITEM"][0]["VALUE"],ENT_QUOTES, $charset).')</li>';
							$choix=array();
							foreach($parameter["options"]["ITEMS"][0]["ITEM"] as $achoix) {
								$choix[] = $achoix["value"]." (".$achoix["VALUE"].")";
							}
							$parameters_display .= '<li>'.htmlentities($msg["procs_options_liste_options"], ENT_QUOTES, $charset).': '.(htmlentities(implode("; ", $choix),ENT_QUOTES, $charset)).'</li>';
							$parameters_display .= '</ul></td>';
							break;
						case "date_box":
							$parameters_display .= '<td><br><br></td>';
							break;
						case "selector":
							$parameters_display .= '<td><ul>';
							$parameters_display .= '<li>'.htmlentities($msg["include_option_methode"], ENT_QUOTES, $charset).': '.($parameter["options"]["METHOD"][0]["value"] == "1" ? $msg['parperso_include_option_selectors_id'] : $msg['parperso_include_option_selectors_label']).'</li>';
							$id_captions=array($msg['133'], $msg['134'], $msg['135'], $msg['136'], $msg['137'], $msg['333'], $msg['indexint_menu']);
							$parameters_display .= '<li>'.htmlentities($msg["include_option_type_donnees"], ENT_QUOTES, $charset).': '.(htmlentities($id_captions[$parameter["options"]["DATA_TYPE"][0]["value"]], ENT_QUOTES, $charset)).'</li>';
							$parameters_display .= '</ul></td>';
							break;
						case "file_box":
							$parameters_display .= '<td><ul>';
							$parameters_display .= '<li>'.htmlentities($msg["include_option_methode"], ENT_QUOTES, $charset).': '.($parameter["options"]["METHOD"][0]["value"] == "1" ? htmlentities($msg["57"], ENT_QUOTES, $charset) : htmlentities($msg["include_option_table"], ENT_QUOTES, $charset)).'</li>';
							$parameters_display .= '<li>'.htmlentities($msg["include_option_nom_table"], ENT_QUOTES, $charset).': '.(htmlentities($parameter["options"]["TEMP_TABLE_NAME"][0]["value"],ENT_QUOTES, $charset)).'</li>';
							$parameters_display .= '<li>'.htmlentities($msg["include_option_type_donnees"], ENT_QUOTES, $charset).': '.($parameter["options"]["DATA_TYPE"][0]["value"] == "1" ? "Chaine" : "Entier").'</li>';
							$parameters_display .= '</ul></td>';
							break;
						default:
							break;
					}
						
					$parameters_display .= '</tr>';
				}
				$parameters_display .= '</table>';
				$form = str_replace('!!parameters_content!!', $parameters_display, $form);
			}
		
			$form = str_replace('!!parameters_title!!', "", $form);
			$form = str_replace('!!parameters_content!!', "", $form);
		
			if($this->module == 'admin') {
				print confirmation_delete("./admin.php?categ=proc&sub=proc&action=del&id=");
			} else {
				$form = str_replace('!!back_link!!', "./".$this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs", $form);
				$form = str_replace('!!import_remote_link!!', "./".$this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=import_remote&id=".$this->id, $form);
			}
			print $form;
		}
	}
	
	public function get_import_form() {
		global $msg, $charset;
		global $types_selectaction;
		global $num_classement;
		
		if($this->instanciate_procedure()) {
			$the_procedure = $this->get_the_procedure();
			
			//Regardons si on a déjà une procédure avec ce nom là dans la base de donnée
			switch ($this->module) {
				case 'admin':
					break;
				case 'circ':
					$type = $the_procedure->type == "PEMPA" ? 'ACTION' : 'SELECT';
					break;
				case 'catalog':
					$type = $types_selectaction[$the_procedure->type];
					break;
			}
			$form = "";
			if($this->module != 'admin') {
				$sql_test = "SELECT COUNT(*) FROM ".$this->table." WHERE type ='".$type."' AND name='".addslashes($the_procedure->name)."'";
				$count = pmb_mysql_result(pmb_mysql_query($sql_test), 0, 0);
				if ($count) {
					$form .= "
					<br/><div class='erreur'>$msg[remote_procedures_import_remote_already_exists_caution]</div>
					<script type='text/javascript' src='./javascript/tablist.js'></script>
					<div class='row'>
					<div class='colonne10'>
					<img src='".get_url_icon('error.gif')."' class='align_left'>
					</div>
					<div class='colonne80'>
					<strong>".$msg["remote_procedures_import_remote_already_exists"]."</strong>
								</div>
							</div><br><br>
							";
				}
				$form_action = $this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=import_remote&id=".$this->id."&do_import=1";
			} else {
				$form_action = "admin.php?categ=proc&sub=proc&action=import_remote&id=".$this->id."&do_import=1";
			}
			
			$form .= "<form class='form-$current_module' name='maj_proc' method='post' action='".$form_action."'>";
			$form .= "<h3><span onclick='menuHide(this,event)'>>".$msg["remote_procedures_import_remote"]."</span></h3>";
			$form .= "<div class='form-contenu'>";
			$form .= '<b>'.$msg["remote_procedures_procedure_name"].':</b><br><input name="imported_name" size="70" type="text" value="'.htmlentities($the_procedure->name, ENT_QUOTES, $charset).'" /><br><br>';
			$form .= '<b>'.$msg["caddie_procs_type"].':</b><br>'.htmlentities((in_array($the_procedure->type, array('PNS', 'PES', 'PEMPS', 'PBS')) ? $msg["caddie_procs_type_SELECT"] : $msg["caddie_procs_type_ACTION"]), ENT_QUOTES, $charset)."<br><br>";
			$form .= '<b>'.$msg["remote_procedures_procedure_comment"].':</b><br><input name="imported_comment" size="70" type="text" value="'.htmlentities($the_procedure->comment, ENT_QUOTES, $charset).'" /><br><br>';
			
			if($this->module == 'admin') {
				$combo_clas= gen_liste ("SELECT idproc_classement,libproc_classement FROM procs_classements ORDER BY libproc_classement ", "idproc_classement", "libproc_classement", "proc_classement", "", $num_classement, 0, $msg['proc_clas_aucun'],0, $msg['proc_clas_aucun']);
				$form .= '<b>'.$msg["remote_procedures_putin"].':</b><br>'.$combo_clas."<br><br>";
			}
			
			$requete_users = "SELECT userid, username FROM users order by username ";
			$res_users = pmb_mysql_query($requete_users);
			$all_users=array();
			while (list($all_userid,$all_username)=pmb_mysql_fetch_row($res_users)) {
				$all_users[]=array($all_userid,$all_username);
			}
			foreach($all_users as $a_user) {
				$id_check="auto_".$a_user[0];
				if($id_check_list)$id_check_list.='|';
				$id_check_list.=$id_check;
				$autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='userautorisation[]' id='$id_check' value='".$a_user[0]."' class='checkbox'><label for='$id_check' class='normlabel'>&nbsp;".$a_user[1]."</label></span>&nbsp;&nbsp;";
			}
			$autorisations_users.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='$id_check_list' >";
			$form .= "<div class='row'>
			<label class='etiquette' for='form_comment'>$msg[procs_autorisations]</label>
			<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
				<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
				</div>";
			$form .= $autorisations_users;
			
			$form .= '</div>';
			$form .= "<input type='button' class='bouton' value='$msg[76]' onClick='document.location=\"./".$this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs\"' />&nbsp;";
			$form .= "<input type='submit' class='bouton' value='".$msg["remote_procedures_import"]."' />&nbsp;";
			$form .= '</form>';
			return $form;
		}
	}
	
	public function import() {
		global $msg, $charset;
		global $proc_classement;
		global $userautorisation;
		global $imported_name;
		global $imported_comment;
		
		if ($this->instanciate_procedure()) {
			$the_procedure = $this->get_the_procedure();
			
			$proc_classement = intval($proc_classement);
			
			if (is_array($userautorisation)) $autorisations=implode(" ",$userautorisation);
			else $autorisations='';
			
			if ($imported_name)
				$the_procedure->name = $imported_name;
			else
				$the_procedure->name = pmb_mysql_escape_string($the_procedure->name);
			if ($imported_comment)
				$the_procedure->comment = $imported_comment;
			else
				$the_procedure->comment = pmb_mysql_escape_string($the_procedure->comment);
				
			$parameters=$the_procedure->params;
			//mise à jour de l'encodage de l'entête
			if($charset == 'utf-8') {
				$parameters = str_replace('<?xml version="1.0" encoding="iso-8859-1"?>', '<?xml version="1.0" encoding="utf-8"?>', $parameters) ;
			}
			switch ($this->module) {
				case 'admin':
					$query = "INSERT INTO ".$this->table." (name, requete, comment, autorisations, parameters, num_classement) VALUES ('".$the_procedure->name."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".$the_procedure->comment."', '".pmb_mysql_escape_string($autorisations)."', '".pmb_mysql_escape_string($parameters)."', ".pmb_mysql_escape_string($proc_classement).")";
					break;
				case 'circ':
					$type = $the_procedure->type == "PEMPA" ? 'ACTION' : 'SELECT';
					$query = "INSERT INTO ".$this->table." (type, name, requete, comment, autorisations, parameters) VALUES ('".$type."', '".($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".$the_procedure->comment."', '".pmb_mysql_escape_string($autorisations)."', '".pmb_mysql_escape_string($parameters)."')";
					break;
				case 'catalog':
					global $types_selectaction;
					$type = $types_selectaction[$the_procedure->type];
					$query = "INSERT INTO ".$this->table." (type, name, requete, comment, autorisations, parameters) VALUES ('".$type."', '".($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".$the_procedure->comment."', '".pmb_mysql_escape_string($autorisations)."', '".pmb_mysql_escape_string($parameters)."')";
					break;
			}
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function execute() {
		global $msg, $charset;
		global $testable_types;
		global $force_exec;
		
		if ($this->instanciate_procedure()) {
			$the_procedure = $this->get_the_procedure();
			switch ($this->module) {
				case 'admin':
					$base_url = "admin.php?categ=proc&sub=proc";
					break;
				case 'circ':
					$base_url = $this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs";
					if ($the_procedure->type != "PEMPS") {
						echo htmlentities($msg["remote_procedures_circ_noPEMPS"],ENT_QUOTES, $charset);
						return false;
					}
					break;
				case 'catalog':
					$base_url = $this->module.".php?categ=caddie&sub=gestion&quoi=remote_procs";
					if (!in_array($the_procedure->type, $testable_types)) {
						echo htmlentities($msg["remote_procedures_catalog_noSELECT"],ENT_QUOTES, $charset);
						return false;
					}
					break;
			}
			if ($the_procedure->params && ($the_procedure->params != "NULL")) {
	//			$sql = "DROP TABLE IF EXISTS remote_proc";
	//			pmb_mysql_query($sql) or die(pmb_mysql_error());
			
				$sql = "CREATE TEMPORARY TABLE remote_proc LIKE procs";
				pmb_mysql_query($sql) or die(pmb_mysql_error());
			
				$sql = "INSERT INTO remote_proc (idproc, name, requete, comment, autorisations, parameters, num_classement) VALUES (0, '".pmb_mysql_escape_string($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".pmb_mysql_escape_string($the_procedure->comment)."', '', '".pmb_mysql_escape_string($the_procedure->params)."', 0)";
				pmb_mysql_query($sql) or die(pmb_mysql_error());
				$idproc = pmb_mysql_insert_id();
			
				$hp=new parameters($idproc,"remote_proc");
				if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters))
					$hp->gen_form($base_url."&action=final_remote&id=".$this->id.($force_exec ? "&force_exec=".$force_exec : ""));
				else echo "<script>document.location='".$base_url."&action=final_remote&id=".$this->id.($force_exec ? "&force_exec=".$force_exec : "")."'</script>";
			}
			else echo "<script>document.location='".$base_url."&action=final_remote&id=".$this->id.($force_exec ? "&force_exec=".$force_exec : "")."'</script>";
		}
	}
	
	public function final_execution() {
		global $msg, $charset;
		global $execute_external;
		global $execute_external_procedure;
		global $param_proc_hidden;
		
		if ($this->instanciate_procedure()) {
			$the_procedure = $this->get_the_procedure();
			switch ($this->module) {
				case 'circ':
					if ($the_procedure->type != "PEMPS") {
						echo htmlentities($msg["remote_procedures_circ_noPEMPS"],ENT_QUOTES, $charset);
						return false;
					}
					break;
				case 'catalog':
					global $testable_types;
					if (!in_array($the_procedure->type, $testable_types)) {
						echo htmlentities($msg["remote_procedures_catalog_noSELECT"],ENT_QUOTES, $charset);
						return false;
					}
					break;
			}
			
			$query = "CREATE TEMPORARY TABLE remote_proc LIKE procs";
			pmb_mysql_query($query) or die(pmb_mysql_error());
			
			$query = "INSERT INTO remote_proc (idproc, name, requete, comment, autorisations, parameters, num_classement) VALUES (0, '".pmb_mysql_escape_string($the_procedure->name)."', '".pmb_mysql_escape_string($the_procedure->sql)."', '".pmb_mysql_escape_string($the_procedure->comment)."', '', '".pmb_mysql_escape_string($the_procedure->params)."', 0)";
			pmb_mysql_query($query) or die(pmb_mysql_error());
			$idproc = pmb_mysql_insert_id();
			
			$hp=new parameters($idproc,"remote_proc");
			$param_proc_hidden="";
			if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters)) {
				$hp->get_final_query();
				$the_procedure->sql = $hp->final_query;
				$param_proc_hidden=$hp->get_hidden_values();//Paramêtres en champ caché en cas de forçage
			}
			
			$execute_external = true;
			$execute_external_procedure = $the_procedure;
		}
	}
	
	public function get_the_procedure() {
		return $this->the_procedure;
	}
}