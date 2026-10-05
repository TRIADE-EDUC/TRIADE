<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_procs.class.php,v 1.24.8.1 2025/03/20 09:08:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path."/procs.class.php");
require_once ($include_path."/templates/cart.tpl.php");
require_once ($class_path."/parameters.class.php");

// définition de la classe de gestion des procédures de paniers

class caddie_procs extends procs {
	
	public static $module = 'catalog';
	public static $table = 'caddie_procs';
	
	public static function get_display_list() {
		global $base_path, $msg;
		global $PMBuserid;
		
		$display = "<hr /><table>";
		
		// affichage du tableau des procédures
		if ($PMBuserid!=1) $where=" where (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid' or autorisations_all=1) ";
		else $where="";
		$query = "SELECT idproc, type, name, requete, comment, autorisations, autorisations_all FROM ".static::$table." $where ORDER BY type, name ";
		$result = pmb_mysql_query($query);
		if($result) {
			$parity=1;
			while($row = pmb_mysql_fetch_object($result)) {
				$autorisations=explode(" ",$row->autorisations);
				if ($row->autorisations_all || array_search ($PMBuserid, $autorisations)!==FALSE || $PMBuserid == 1) {
					if ($parity % 2) {
						$pair_impair = "even";
					} else {
						$pair_impair = "odd";
					}
					$parity += 1;
					$action=" onmousedown=\"document.location='".static::format_url("&action=modif&id=".$row->idproc)."';\"";
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					$display .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
					if ($row->type != "ACTION"){
						$display .= "	<td style='width:10px'><input class='bouton' type='button' value=' ".$msg['procs_options_tester_requete']." ' onClick=\"document.location='".static::format_url("&action=execute&id=".$row->idproc)."'\" />";
					}else{
						$display .= "	<td style='width:10px' $action>&nbsp;";
					}
					$display .= "</td>
							<td style='width:80px' $action>".$row->type."</td>
							<td $action>
								<strong>".$row->name."</strong><br />
								<small>".$row->comment."&nbsp;</small>
							</td>";
					if (preg_match_all("|!!(.*)!!|U",$row->requete,$query_parameters)){
						$display .= "<td><a href='".$base_path."/".static::$module.".php?categ=caddie&sub=gestion&quoi=procs&action=configure&id_query=".$row->idproc."'>".$msg['procs_options_config_param']."</a>";
					}else{
						$display .= "<td $action>&nbsp;";
					}
					$display .= "</td>
						<td>" ;
					if(static::$module == 'catalog') {
						$display .= "<input class='bouton' type='button' value=\"".$msg['procs_bt_export']."\" onClick=\"document.location='".$base_path."/export.php?quoi=procs&sub=caddie&id=".$row->idproc."'\" />";
					} elseif(static::$module == 'circ') {
						$display .= "<input class='bouton' type='button' value=\"".$msg['procs_bt_export']."\" onClick=\"document.location='".$base_path."/export.php?quoi=procs&sub=empr_caddie&id=".$row->idproc."'\" />";
					} elseif(static::$module == 'autorites') {
						$display .= "<input class='bouton' type='button' value=\"".$msg['procs_bt_export']."\" onClick=\"document.location='".$base_path."/export.php?quoi=procs&sub=authorities_caddie&id=".$row->idproc."'\" />";
					}
					$display .= "</td>
							</tr>";
				}
			}
		}
		$display .= "</table><hr />
		<input class='bouton' type='button' value=' ".$msg['704']." ' onClick=\"document.location='".$base_path."/".static::$module.".php?categ=caddie&sub=gestion&quoi=procs&action=add'\" />
		<input class='bouton' type='button' value=' ".$msg['procs_bt_import']." ' onClick=\"document.location='".$base_path."/".static::$module.".php?categ=caddie&sub=gestion&quoi=procs&action=import'\" />";
		
		return $display;
	}
	
	public static function create() {
		global $msg;
		global $f_proc_type;
		global $f_proc_name;
		global $f_proc_code;
		global $f_proc_comment;
		global $autorisations;
		global $autorisations_all;
		
		if($f_proc_name && $f_proc_code) {
			$query = "SELECT count(1) FROM ".static::$table." WHERE name='$f_proc_name' ";
			$result = pmb_mysql_query($query);
			$nbr_lignes = pmb_mysql_result($result, 0, 0);
			if(!$nbr_lignes) {
				if (is_array($autorisations)) {
					$autorisations=implode(" ",$autorisations);
				} else {
					$autorisations='';
				}
				$autorisations_all = intval($autorisations_all);
				$param_name=parameters::check_param($f_proc_code);
				if ($param_name!==true) {
					error_message_history($param_name, sprintf($msg["proc_param_check_field_name"],$param_name), 1);
					exit();
				}
				$query = "INSERT INTO ".static::$table." (idproc,type,name,requete,comment,autorisations,autorisations_all) VALUES ('', '$f_proc_type', '$f_proc_name', '$f_proc_code', '$f_proc_comment', '$autorisations', '$autorisations_all' ) ";
				pmb_mysql_query($query);
			} else {
				print "<script language='Javascript'>alert(\"$msg[709]\");</script>";
				print "<script language='Javascript'>history.go(-1);</script>";
			}
		}
	}
	
	public static function update($id) {
		global $msg;
		global $f_proc_name;
		global $f_proc_code;
		global $f_proc_comment;
		global $autorisations;
		global $autorisations_all;
		
		$id = intval($id);
		if($id) {
			if (is_array($autorisations)) {
				$autorisations=implode(" ",$autorisations);
			} else {
				$autorisations="";
			}
			$autorisations_all = intval($autorisations_all);
			$param_name=parameters::check_param($f_proc_code);
			if ($param_name!==true) {
				error_message_history($param_name, sprintf($msg["proc_param_check_field_name"],$param_name), 1);
				exit();
			}
			$query = "UPDATE ".static::$table." SET name='$f_proc_name',requete='$f_proc_code',comment='$f_proc_comment' , autorisations='$autorisations' , autorisations_all='$autorisations_all' WHERE idproc=$id ";
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public static function get_query_data($id=0) {
	    return "SELECT idproc, name, requete, comment, autorisations, autorisations_all, type
            FROM ".static::$table." WHERE idproc=".$id;
	}
	
	public static function get_example_code() {
	    global $msg;
	    
	    switch (static::$table) {
	        case 'empr_caddie_procs':
	            return $msg['cart_ex_selection']." select id_empr as <b>object_id</b> from empr where ...<br />
					".$msg['cart_ex_action']." update empr set empr_statut=!!nouveau_statut!! where id_empr in (CADDIE(<b>EMPR</b>))";
	        case 'authorities_caddie_procs':
	            return $msg['cart_ex_selection']." select id_authority as <b>object_id</b>, 'AUTHORS' as object_type from authorities JOIN <b>authors</b> ON <b>author_id</b>=authorities.num_object and authorities.type_object = 1 where ...<br />
				".$msg['cart_ex_action']." update authorities set num_statut=!!nouveau_statut!! where id_authority in (CADDIE(<b>AUTHORS</b>))<br />
				MIXED / AUTHORS / CATEGORIES / PUBLISHERS / COLLECTIONS / SUBCOLLECTIONS / SERIES / TITRES_UNIFORMES / INDEXINT / AUTHPERSO";
	        case 'caddie_procs':
	        default:
	            return $msg['cart_ex_selection']." select notice_id as <b>object_id</b>, <b>'NOTI'</b> as object_type from notices where ...<br />
					'NOTI' / 'EXPL' / 'BULL'<br />
					".$msg['cart_ex_action']." update exemplaires set expl_statut=!!nouveau_statut!! where expl_id in (CADDIE(<b>EXPL</b>))<br />
					EXPL / NOTI / BULL";
	    }
	}
	
	public static function get_proc_content_form($id=0, $data=[]) {
	    global $msg;
	    global $num_classement;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    
	    if ($id) {
	        $interface_content_form->add_element('f_proc_type', 'caddie_procs_type')
	        ->add_html_node($msg["caddie_procs_type_".$data['type']]);
	        
	    } else {
	        $options = ['SELECT' => $msg['caddie_procs_type_SELECT'], 'ACTION' => $msg['caddie_procs_type_ACTION']];
	        $interface_content_form->add_element('f_proc_type', 'caddie_procs_type')
	        ->add_select_node($options);
	    }
	    $interface_content_form->add_element('f_proc_name', '705')
	    ->add_input_node('text', $data['name'])
	    ->set_maxlength(255);
	    
	    if($id) {
	        $num_classement = $data['num_classement'];
	    } else {
	        $num_classement = intval($num_classement);
	    }
	    $element = $interface_content_form->add_element('f_proc_code', '706');
	    $element->add_textarea_node($data['requete'], 70, 10);
	    $element->add_html_node(static::get_example_code());
	    
	    $interface_content_form->add_element('f_proc_comment', '707')
	    ->add_input_node('text', $data['comment'])
	    ->set_maxlength(255);
	    
	    $interface_content_form->add_element('autorisations_all', 'procs_autorisations_all', 'flat')
	    ->add_input_node('boolean', $data['autorisations_all']);
	    $interface_content_form->add_inherited_element('permissions_users', 'autorisations', 'procs_autorisations')
	    ->set_autorisations($data['autorisations'])
	    ->set_on_create(($id ? 0 : 1));
	    
	    return $interface_content_form->get_display();
	}
	    
	protected static function get_interface_form_instance() {
	    return new interface_catalog_form('maj_proc');
	}
	
	protected static function has_form_execute_button($id=0, $type='ACTION') {
	    if ($id && $type != "ACTION") {
	        return true;
	    }
	    return false;
	}
	
	public static function get_form_after_execution($id, $name, $code, $commentaire, $is_external = false) {
		global $msg;
		
		$form = '';
		if (!$is_external) {
			$form .= "
					<h3>
					$msg[procs_execute] \" $name \"
					<input type='button' class='bouton' value='$msg[62]'  onClick='document.location=\"".static::format_url("&action=modif&id=".$id)."\"' />
					<input type='button' class='bouton' value='$msg[708]' id='procs_button_exec' onClick='document.location=\"".static::format_url("&action=execute&id=".$id)."\"' />&nbsp;
					</h3>
					<br /><strong>$name</strong> : $commentaire<hr />";
		} else {
			$form .= "<br />
			<h3>".$msg["remote_procedures_executing"]." $name</h3>
					<br />$commentaire<hr />
					<input type='button' class='bouton' value='$msg[remote_procedures_back]' onClick='document.location=\"./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs\"' />
					<input type='button' class='bouton' value='$msg[708]' id='procs_button_exec' onClick='document.location=\"./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=execute_remote&id=$id\"' />
					<input type='button' class='bouton' value='$msg[remote_procedures_import]' onClick='document.location=\"./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=import_remote&id=$id\"' />
					<br /><br />";
		}
		return $form;
	}
	
	// affichage du tableau des procédures
	public static function get_display_list_from_caddie($idcaddie, $args_url = 'categ=&sub=&quelle=', $type='ACTION', $action = "add_item") {
		global $msg,$charset;
		global $PMBuserid;
		
		$display = "<hr />".$msg['caddie_select_proc']."<br /><table>";
		
		if ($PMBuserid!=1) $where=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		else $where="";
		$query = "SELECT idproc, type, name, requete, comment, autorisations, autorisations_all, parameters FROM ".static::$table." WHERE type='".$type."' $where ORDER BY name ";
		$result = pmb_mysql_query($query);
		$n_proc=0;
		if($result) {
			$parity=1;
			while($row = pmb_mysql_fetch_object($result)) {
				$autorisations=explode(" ",$row->autorisations);
				if (($row->autorisations_all || array_search ($PMBuserid, $autorisations)!==FALSE || $PMBuserid == 1)&&($type != 'ACTION' || static::is_for_cart($idcaddie, $row->requete))) {
					$n_proc++;
					if ($parity % 2) {
						$pair_impair = "even";
					} else {
						$pair_impair = "odd";
					}
					$parity += 1;
					if (preg_match_all("|!!(.*)!!|U",$row->requete,$query_parameters)) {
						$action = "form_proc" ;
					}
					// 					else $action = "add_item" ;
					if(static::$module == 'circ') {
						$link_suffix = "&idemprcaddie=".$idcaddie;
					} else {
						$link_suffix = "&idcaddie=".$idcaddie;
					}
					switch ($type) {
						case 'ACTION':
							$onmousedown = "if (confirm('".addslashes(str_replace("\"","&quot;",sprintf($msg["caddie_action_proc_confirm"],$row->name)))."')) { url='./".static::$module.".php?".$args_url."&action=$action&id=".$row->idproc.$link_suffix."'; if (document.maj_proc.elt_flag.checked) url+='&elt_flag='+document.maj_proc.elt_flag.value; if (document.maj_proc.elt_no_flag.checked) url+='&elt_no_flag='+document.maj_proc.elt_no_flag.value; document.location=url; }";
							break;
						case 'SELECT':
							$onmousedown = "document.location='./".static::$module.".php?".$args_url."&action=$action&id=".$row->idproc.$link_suffix."';";
							break;
					}
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"".$onmousedown."\" ";
					$display .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
							<td>
								<strong>".htmlentities($row->name,ENT_QUOTES,$charset)."</strong><br />
								<small>".htmlentities($row->comment,ENT_QUOTES,$charset)."&nbsp;</small>
							</td>
						</tr>";
				}
			}
		}
		$display .= "</table>";
		if ($n_proc==0) {
			switch ($type) {
				case 'ACTION':
					$display .= $msg["caddie_no_action_proc"];
					break;
				case 'SELECT':
					$display .= $msg["caddie_no_select_proc"];
					break;
			}
		}
		return $display;
	}
	
	public static function is_for_cart($idcaddie, $requete) {
		if (preg_match("/CADDIE\(([^\)]*)\)/",$requete,$match)) {
			$m=explode(",",$match[1]);
			if(static::$table == 'empr_caddie_procs') {
				$myCart = empr_caddie_controller::get_object_instance($idcaddie);
			} elseif(static::$table == 'authorities_caddie_procs') {
				$myCart = authorities_caddie_controller::get_object_instance($idcaddie);
			} else {
				$myCart = caddie_controller::get_object_instance($idcaddie);
			}
			$as=array_search($myCart->type,$m);
			if (($as!==NULL)&&($as!==false)) return true; else return false;
		} else return false;
	}
	
	public static function check_rights($id) {
		global $PMBuserid;
	
		if ($id) {
			$requete = "SELECT autorisations, autorisations_all FROM ".static::$table." WHERE idproc='$id' ";
			$result = @pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				if($temp->autorisations_all) return 1;
				$rqt_autorisation=explode(" ",$temp->autorisations);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return 1 ;
			}
		}
		return 0 ;
	}
	
	public static function proceed() {
		global $msg;
		global $action;
		global $id_query;
		global $id;
		global $f_proc_name;
		global $f_proc_code;
		global $import_proc_tmpl;
	
		print "
		<script type='text/javascript'>
		function test_form(form) {
			if(form.f_proc_name.value.length == 0) {
				alert(\"$msg[702]\");
				form.f_proc_name.focus();
				return false;
			}
			if(form.f_proc_code.value.length == 0) {
				alert(\"$msg[703]\");
				form.f_proc_code.focus();
				return false;
			}
			return true;
		}
		</script>";
	
		switch($action) {
			case 'configure':
				$hp=new parameters($id_query,static::$table);
				$hp->show_config_screen(static::format_url("&action=update_config"),static::format_url());
				break;
			case 'update_config':
				$hp=new parameters($id_query,static::$table);
				$hp->update_config(static::format_url());
				break;
			case 'final':
				static::final_execute();
				break;
			case 'execute':
				// form pour params et validation
				static::run_form($id);
				break;
			case 'modif':
				if($id) {
					if($f_proc_name && $f_proc_code) {
						// faire la modification
						static::update($id);
						print static::get_display_list();
					} else {
						// afficher le form avec les bonnes valeurs
						print static::get_proc_form($id);
					}
				} else {
					print static::get_display_list();
				}
				break;
			case 'add':
				if($f_proc_name && $f_proc_code) {
					static::create();
					print static::get_display_list();
				} else {
					print static::get_proc_form();
				}
				break;
			case 'import':
				$import_proc_tmpl = str_replace("!!action!!", static::format_url("&action=importsuite"), $import_proc_tmpl);
				print $import_proc_tmpl ;
				break;
			case 'importsuite':
				static::importsuite(static::format_url("&action=modif&id=!!id!!"), static::format_url("&action=import"));
				break;
			case 'del':
				if($id) {
					static::delete($id);
				}
				print static::get_display_list();
				break;
			default:
				print static::get_display_list();
				break;
		}
	}
	
	public static function final_execute() {
		global $msg;
		global $id_query;
		global $query_parameters;
		global $execute_external;
		global $id;
		global $execute_external_procedure;
		global $PMBuserid;
		global $force_exec;
		global $current_module;
		
		$is_external = isset($execute_external) && $execute_external;
		if ($is_external) {
			$nbr_lignes = 1;
			$idp = $id;
			$name = $execute_external_procedure->name;
			$code = $execute_external_procedure->sql;
			$commentaire = $execute_external_procedure->comment;
		} else {
			if(!$id_query) $id_query = 0;
			$hp=new parameters($id_query,static::$table);
			$param_proc_hidden="";
			if (is_object($hp->proc) && preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters)) {
				$hp->get_final_query();
				$code=$hp->final_query;
				$id=$id_query;
				$param_proc_hidden=$hp->get_hidden_values();//Je mets les paramêtres en champ caché en cas de forçage
				$param_proc_hidden.="<input type='hidden' name='id_query'  value='".$id_query."' />";
			} else {
				$code = '';
			}
			if ($PMBuserid!=1) {
				$where=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
			} else {
				$where="";
			}
			$requete = "SELECT idproc, name, requete, comment FROM ".static::$table." WHERE idproc=$id $where ";
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_num_rows($res);
			if($nbr_lignes) {
				$row = pmb_mysql_fetch_object($res);
				$idp = $row->idproc;
				$name = $row->name;
				if (!$code) $code = $row->requete;
				$commentaire = $row->comment;
			}
			$urlbase = static::format_url("&action=final&id=$id");
		}
		
		if($nbr_lignes) {
			print "<form class='form-".$current_module."' id='formulaire' name='formulaire' action='' method='post'>";
			print $param_proc_hidden;
			if($force_exec){
				print "<input type='hidden' name='force_exec'  value='".$force_exec."' />";//On a forcé la requete
			}
			print static::get_form_after_execution($idp, $name, $code, $commentaire, $is_external);
			// récupération du résultat
			$report = static::run_query($code);
			if($report['state'] == false && $report['message'] == 'explain_failed') {
				static::final_explain_failed($id);
			}
			print "</form>";
		} else {
			print $msg["proc_param_query_failed"];
		}
	}
	
	public static function get_parameters_remote() {
		//utilisées dans la classe remote_procedure en globale pour le module catalog
		global $allowed_proc_types;
		global $types_selectaction;
		global $testable_types;
		global $type_titles;
		
		$allowed_proc_types = array("PNS", "PNA", "PES", "PEA", "PBS", "PBA");
		$types_selectaction = array("PNS" => "SELECT",
				"PNA" => 'ACTION',
				"PEA" => 'ACTION',
				"PES" => "SELECT",
				"PBS" => "SELECT",
				"PBA" => 'ACTION');
		$testable_types = array("PNS" => true,
				"PNA" => false,
				"PEA" => false,
				"PES" => true,
				"PBS" => true,
				"PBA" => false);
		$type_titles = array("PNS" => "remote_procedures_catalog_caddienotice_select",
				"PNA" => "remote_procedures_catalog_caddienotice_action",
				"PEA" => "remote_procedures_catalog_caddieexpl_action",
				"PES" => "remote_procedures_catalog_caddieexpl_select",
				"PBS" => "remote_procedures_catalog_caddiebull_select",
				"PBA" => "remote_procedures_catalog_caddiebull_action");
		return array(
			'allowed_proc_types' => $allowed_proc_types,
			'types_selectaction' => $types_selectaction,
			'testable_types' => $testable_types,
			'type_titles' => $type_titles
		);
	}
	
	public static function get_display_remote_list($type="") {
		global $pmb_procedure_server_credentials, $pmb_procedure_server_address;
		global $msg;
		global $charset;
	
		$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
		if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
			$aremote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
			$procedures = $aremote_procedure_client->get_procs($type);
	
			if ($procedures) {
				$parameters_remote = static::get_parameters_remote();
				if ($procedures->error_information->error_code) {
					$buf_contenu=$msg["remote_procedures_error_server"].":<br><i>".$procedures->error_information->error_string."</i>";
					print $buf_contenu;
				} else if (isset($procedures->elements)) {
					$current_set="";
					$buf_contenu="";
					foreach ($procedures->elements as $aprocedure) {
						if ($aprocedure->current_attached_set != $current_set) {
							$parity=0;
							$current_set = $aprocedure->current_attached_set;
							$buf_contenu .= '<tr><th colspan=4>'.htmlentities($current_set, ENT_QUOTES, $charset).'</th>';
						}
						if ($parity % 2) {$pair_impair = "even"; } else {$pair_impair = "odd";}
						$parity += 1;
							
						$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=view_remote&id=$aprocedure->id&remote_type=$type';\" ";
						$buf_contenu.="\n<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
						<td style='width:80px'>
						".($parameters_remote['testable_types'][$type] ? "<input class='bouton' type='button' value=' ".$msg['procs_options_tester_requete']." ' onClick=\"document.location='./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=execute_remote&id=$aprocedure->id&remote_type=$type'\" />" : "")."
								</td>
							<td>
								".($aprocedure->untested ? "[<i>".$msg["remote_procedures_procedure_non_validated"]."</i>]&nbsp;&nbsp;" : '')."<strong>$aprocedure->name</strong><br/>
									<small>$aprocedure->comment&nbsp;</small>
									</td>";
						$buf_contenu.="<td><input class='bouton' type='button' value=\"".$msg['remote_procedures_import']."\" onClick=\"document.location='./".static::$module.".php?categ=caddie&sub=gestion&quoi=remote_procs&action=import_remote&id=$aprocedure->id&remote_type=$type'\" /></td>
						</tr>";
					}
					$title = $msg[$parameters_remote['type_titles'][$type]];
					$buf_contenu="<h1>".$title."</h1>"."<table></tr>".$buf_contenu."</table><br>";
					print $buf_contenu;
				} else {
					$title = $msg[$parameters_remote['type_titles'][$type]];
					$buf_contenu="<h1>".$title."</h1>".$msg['remote_procedures_no_procs']."<br><br>";
					print $buf_contenu;
				}
			}
		}
	}
	
	public static function get_display_remote_lists() {
		static::get_display_remote_list("PNS");
		static::get_display_remote_list("PNA");
		static::get_display_remote_list("PES");
		static::get_display_remote_list("PEA");
		static::get_display_remote_list("PBS");
		static::get_display_remote_list("PBA");
	}
	
	public static function format_url($url='') {
		global $base_path;
	
		return $base_path."/".static::$module.".php?categ=caddie&sub=gestion&quoi=procs".$url;
	}
}