<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: procs.class.php,v 1.21 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des procédures

require_once($class_path."/remote_procedure_client.class.php");
require_once($class_path."/remote_procedure.class.php");
require_once($include_path."/templates/procs_exp_imp.tpl.php");

class procs {
	
	static $module = 'admin';
	static $table = 'procs';
	
	public function __construct() {
	}
	
	public static function get_display_list() {
		global $base_path, $msg;
		global $javascript_path;
		global $PMBuserid;
		
		$display = "
		<script type=\"text/javascript\" src=\"".$javascript_path."/tablist.js\"></script>
		<span class='item-expand'>
			<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' style='border:0px' id=\"expandall\"></a>
			<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' style='border:0px' id=\"collapseall\"></a>
		</span>
		";
		// affichage du tableau des procédures
		$query = "SELECT idproc, name, requete, comment, autorisations, autorisations_all, libproc_classement, num_classement FROM procs left join procs_classements on idproc_classement=num_classement ORDER BY libproc_classement,name ";
		$result = pmb_mysql_query($query);
		$class_prec=$msg['proc_clas_aucun'];
		$buf_tit="";
		$buf_contenu="";
		$buf_class=0;
		$parity=1;
		while($row = pmb_mysql_fetch_object($result)) {
			$rqt_autorisation=explode(" ",$row->autorisations);
			if ((static::$module=='admin' && ($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE))
				|| (static::$module=='edit' && ($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) && pmb_strtolower(pmb_substr(trim($row->requete),0,6))=='select')) {
				$classement=$row->libproc_classement;
				if ($class_prec!=$classement) {
					if (!$row->libproc_classement) $row->libproc_classement=$msg['proc_clas_aucun'];
					if ($buf_tit) {
						$buf_contenu="<table><tr><th colspan=4>".$buf_tit."</th></tr>".$buf_contenu."</table>";
						$display .= gen_plus("procclass".$buf_class,$buf_tit,$buf_contenu);
						$buf_contenu="";
					}
					$buf_tit=$row->libproc_classement;
					$buf_class=$row->num_classement;
					$class_prec=$classement;
				}
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
				$buf_contenu.="\n<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
				
				if(static::$module=='edit') {
					$action = "onmousedown=\"document.location='./edit.php?categ=procs&sub=&action=execute&id_proc=".$row->idproc."';\"";
					$buf_contenu.="
						<td $action>
							<strong>".$row->name."</strong><br />
							<small>".$row->comment."&nbsp;</small>
						</td>";
				} else {
					$action = "onmousedown=\"document.location='".static::format_url("&action=modif&id=".$row->idproc)."';\"";
					$buf_contenu.="
						<td style='width:10px'>
						<input class='bouton' type='button' value=' $msg[708] ' onClick=\"document.location='".static::format_url("&action=execute&id=".$row->idproc)."'\" />
						</td>
						<td $action>
							<strong>".$row->name."</strong><br />
							<small>".$row->comment."&nbsp;</small>
						</td>";
					if (preg_match_all("|!!(.*)!!|U",$row->requete,$query_parameters)) {
						$buf_contenu.="<td>
									<a href='admin.php?categ=proc&sub=proc&action=configure&id_query=".$row->idproc."'>".$msg["procs_options_config_param"]."</a>";
					} else {
						$buf_contenu.="<td $action>";
					}
					$buf_contenu.="</td>";
					$buf_contenu.="<td><input class='bouton' type='button' value=\"".$msg['procs_bt_export']."\" onClick=\"document.location='./export.php?quoi=procs&sub=actionsperso&id=".$row->idproc."'\" /></td>";
				}
				$buf_contenu.="</tr>";
			}
		}
		$buf_contenu="<table><tr><th colspan=4>".$buf_tit."</th></tr>".$buf_contenu."</table>";
		$display .= gen_plus("procclass".$buf_class,$buf_tit,$buf_contenu);
		
		return $display;
	}
	
	public static function create() {
		global $msg;
		global $f_proc_name;
		global $f_proc_code;
		global $f_proc_comment;
		global $userautorisation;
		global $autorisations_all;
		global $form_classement;
		global $form_notice_tpl;
		global $form_notice_tpl_field;
		
		if($f_proc_name && $f_proc_code) {
			$query = "SELECT count(1) FROM ".static::$table." WHERE name='$f_proc_name' ";
			$result = pmb_mysql_query($query);
			$nbr_lignes = pmb_mysql_result($result, 0, 0);
			if(!$nbr_lignes) {
				if (is_array($userautorisation)) {
					$autorisations=implode(" ",$userautorisation);
				} else {
					$autorisations='';
				}
				$autorisations_all += 0;
				$param_name=parameters::check_param($f_proc_code);
				if ($param_name!==true) {
					error_message_history($param_name, sprintf($msg["proc_param_check_field_name"],$param_name), 1);
					exit();
				}
				$query = "INSERT INTO ".static::$table." (idproc,name,requete,comment,autorisations,autorisations_all,num_classement, proc_notice_tpl, proc_notice_tpl_field) VALUES ('', '$f_proc_name', '$f_proc_code', '$f_proc_comment', '$autorisations', '".$autorisations_all."', '$form_classement', '$form_notice_tpl', '$form_notice_tpl_field' ) ";
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
		global $userautorisation;
		global $autorisations_all;
		global $form_classement;
		global $form_notice_tpl;
		global $form_notice_tpl_field;
		
		$id += 0;
		if($id) {
			if (is_array($userautorisation)) {
				$autorisations=implode(" ",$userautorisation);
			} else {
				$autorisations="";
			}
			$autorisations_all += 0;
			$param_name=parameters::check_param($f_proc_code);
			if ($param_name!==true) {
				error_message_history($param_name, sprintf($msg["proc_param_check_field_name"],$param_name), 1);
				exit();
			}
			$query = "UPDATE ".static::$table." SET name='$f_proc_name',requete='$f_proc_code',comment='$f_proc_comment' , autorisations='$autorisations', autorisations_all='".$autorisations_all."', num_classement='$form_classement', proc_notice_tpl='$form_notice_tpl', proc_notice_tpl_field='$form_notice_tpl_field' WHERE idproc=$id ";
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public static function get_proc_form($id=0) {
		global $base_path, $msg;
		global $admin_proc_form;
		global $charset;
		global $PMBuserid;
	
		$id += 0;
		$form = $admin_proc_form;
		$autorisations = array();
		$num_classement = 0;
		if($id) {
			$query = "SELECT idproc, name, requete, comment, autorisations, autorisations_all, num_classement, proc_notice_tpl, proc_notice_tpl_field FROM ".static::$table." WHERE idproc=".$id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$autorisations_donnees=explode(" ",$row->autorisations);
				$query_users = "SELECT userid, username FROM users order by username ";
				$result_users = pmb_mysql_query($query_users);
				$all_users=array();
				while (list($all_userid,$all_username)=pmb_mysql_fetch_row($result_users)) {
					$all_users[]=array($all_userid,$all_username);
				}
				for ($i=0 ; $i<count($all_users) ; $i++) {
					if (array_search ($all_users[$i][0], $autorisations_donnees)!==FALSE) $autorisations[$i][0]=1;
					else $autorisations[$i][0]=0;
					$autorisations[$i][1]= $all_users[$i][0];
					$autorisations[$i][2]= $all_users[$i][1];
				}
				$form = str_replace('!!form_title!!', $msg["procs_modification"], $form);
				$form = str_replace('!!action!!', static::format_url("&action=modif&id=".$id), $form);
	
				$form = str_replace('!!name!!', htmlentities($row->name,ENT_QUOTES, $charset), $form);
				$form = str_replace('!!name_suppr!!', htmlentities(addslashes($row->name),ENT_QUOTES, $charset), $form);
				$form = str_replace('!!code!!', htmlentities($row->requete,ENT_QUOTES, $charset), $form);
				$form = str_replace('!!comment!!', htmlentities($row->comment,ENT_QUOTES, $charset), $form);
				$sel_notice_tpl="<input type='text' class='saisie-15em' name='form_notice_tpl_field' value='".$row->proc_notice_tpl_field."' >";
				$form = str_replace('!!notice_tpl!!',$sel_notice_tpl, $form);
				$num_classement = $row->num_classement;
			}
		} else {
			$query_users = "SELECT userid, username FROM users order by username ";
			$result_users = pmb_mysql_query($query_users);
			$all_users=array();
			while (list($all_userid,$all_username)=pmb_mysql_fetch_row($result_users)) {
				if($all_userid == $PMBuserid) {
					//On autorise l'utilisateur courant par défaut
					$autorisations[]=array(1, $all_userid,$all_username);
				} else {
					$autorisations[]=array(0, $all_userid,$all_username);
				}
			}
			
			$form = str_replace('!!form_title!!', $msg[704], $form);
			$form = str_replace('!!action!!', static::format_url("&action=add"), $form);
				
			$form = str_replace('!!name!!', '', $form);
			$form = str_replace('!!name_suppr!!', '', $form);
			$form = str_replace('!!code!!', '', $form);
			$form = str_replace('!!comment!!', '', $form);
			$sel_notice_tpl="<input type='text' class='saisie-15em' name='form_notice_tpl_field' value='' >";
			$form = str_replace('!!notice_tpl!!',$sel_notice_tpl, $form);
		}
		$form = str_replace('!!id!!', $id, $form);
	
		$autorisations_users="";
		$id_check_list='';
		foreach ($autorisations as $row_number => $row_data) {
			$id_check="auto_".$row_data[1];
			if($id_check_list)$id_check_list.='|';
			$id_check_list.=$id_check;
			if ($row_data[0]) $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='userautorisation[]' id='$id_check' value='".$row_data[1]."' checked class='checkbox'><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;&nbsp;";
			else $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='userautorisation[]' id='$id_check' value='".$row_data[1]."' class='checkbox'><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;&nbsp;";
		}
		$autorisations_users.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='$id_check_list' >";
		$form = str_replace('!!autorisations_users!!', $autorisations_users, $form);
		
		$form = str_replace('!!autorisations_all!!', ($row->autorisations_all ? "checked='checked'" : ""), $form);
	
		$combo_clas= gen_liste ("SELECT idproc_classement,libproc_classement FROM procs_classements ORDER BY libproc_classement ", "idproc_classement", "libproc_classement", "form_classement", "", $num_classement, 0, $msg['proc_clas_aucun'],0, $msg['proc_clas_aucun']) ;
		$form = str_replace('!!classement!!', $combo_clas, $form);
		
		$form .= confirmation_delete(static::format_url("&action=del&id="));
		return $form;
	}
	
	public static function delete($id) {
		$id += 0;
		if($id) {
			$query = "DELETE FROM ".static::$table." WHERE idproc=".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public static function optimize() {
		$query = "OPTIMIZE TABLE ".static::$table;
		pmb_mysql_query($query);
	}
	
	public static function run_form($id) {
		global $msg;
		global $charset;
		global $force_exec;
		$hp=new parameters($id,static::$table);
		if (preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters))
			$hp->gen_form(static::format_url("&action=final&id=".$id."&force_exec=".$force_exec));
		else echo "<script>document.location='".static::format_url("&action=final&id=".$id."&force_exec=".$force_exec)."'</script>";
	}
	
	public static function get_form_after_execution($id, $name, $code, $commentaire, $is_external = false) {
		global $msg, $charset;
	
		$form = '';
		if (!$is_external) {
			$form .= "
			<h3>".htmlentities($msg["procs_execute"]." ".$name, ENT_QUOTES, $charset)."</h3>
			<br /><strong>$name</strong> : $commentaire<hr />
			<input type='button' class='bouton' value='$msg[62]' onClick='this.form.action=\"".static::format_url("&action=modif&id=".$id)."\";this.form.submit();'/>
			<input type='button' id='procs_button_exec' class='bouton' value='$msg[708]' onClick='this.form.action=\"".static::format_url("&action=execute&id=".$id)."\";this.form.submit();'/>
			<br />";
		} else {
			$form .= "<br />
			<h3>".htmlentities($msg["remote_procedures_executing"]." ".$name, ENT_QUOTES, $charset)."</h3>
				<br />".htmlentities($commentaire, ENT_QUOTES, $charset)."<hr />
				<input type='button' id='procs_button_exec' class='bouton' value='$msg[708]' onClick='this.form.action=\"".static::format_url("&action=execute_remote&id=".$id)."\";this.form.submit();' />
				<br />";
		}
		return $form;
	}
	
	public static function run_query($query_code) {
		global $msg;
		global $pmb_procs_force_execution;
		global $force_exec, $PMBuserid;
		global $urlbase;
		global $erreur_explain_rqt;
		global $sortfield;
		
		$linetemp = explode(";", $query_code);
		for ($i=0;$i<count($linetemp);$i++) if (trim($linetemp[$i])) $line[]=trim($linetemp[$i]);
		$do_reindexation=false;
		foreach ($line as $cle => $valeur) {
			if($valeur) {
				// traitement tri des colonnes
				if ($sortfield != "") {
					// on cherche à trier sur le champ $trifield
					// compose la chaîne de tri
					$tri = $sortfield;
					if ($desc == 1) $tri .= " DESC";
					else $tri .= " ASC";
					// on enlève les doubles espaces dans la procédure
					$valeur = preg_replace("/\s+/", " ", $valeur);
					// supprime un éventuel ; à la fin de la requête
					$valeur = preg_replace("/;$/", "", $valeur);
					// on recherche la première occurence de ORDER BY
					$s = stristr($valeur, "order by");
					if ($s) {
						// y'a déjà une clause order by... moins facile...
						// il faut qu'on sache si on aura besoin de mettre une virgule ou pas
						if ( preg_match("#,#", $s) ) {
							$virgule = true;
						} else if ( ! preg_match("${sortfield}", $s)) {
							$virgule = true;
						} else {
							$virgule = false;
						}
						if ($virgule) {
							$tri .= ", ";
						}
						// regarde si le champ est déjà dans la liste des champs à trier et le remplace si besoin
						$new_s = preg_replace("/$sortfield, /", "", $s);
						$new_s = preg_replace("/$sortfield/", "", $new_s);
						// ajoute la clause order by correcte
						$new_s = preg_replace("/order\s+by\s+/i", "order by $tri", $new_s);
						// replace l'ancienne chaîne par la nouvelle
						$valeur = str_replace($s, $new_s, $valeur);
					} else {
						$valeur .= " order by $tri";
					}
				}
	
				print "<strong>".$msg['procs_ligne']." ".$cle." </strong>:&nbsp;".$valeur."<br /><br />";
				
				if(static::$module != 'admin') {
					if ( (pmb_strtolower(pmb_substr($valeur,0,6))=="select") || (pmb_strtolower(pmb_substr($valeur,0,6))=="create") ) {
					} else {
						print "rqt=".$valeur."=<br />" ;
						error_message_history("Requête invalide","Vous ne pouvez tester que des requêtes de sélection",1);
						return array('state' => false, 'message' => 'invalid_query');
					}
				}
				
				if (($pmb_procs_force_execution && $force_exec) || (($PMBuserid == 1) && $force_exec) || explain_requete($valeur)) {
					$res = @pmb_mysql_query($valeur);
					print pmb_mysql_error();
					$nbr_lignes = @pmb_mysql_num_rows($res);
					$nbr_champs = @pmb_mysql_num_fields($res);
						
					if($nbr_lignes) {
						print "<table >";
						for($i=0; $i < $nbr_champs; $i++) {
							// ajout de liens pour trier les pages
							$fieldname = pmb_mysql_field_name($res, $i);
							$sortasc = "<a href='${urlbase}&sortfield=".($i+1)."&desc=0'>asc</a>";
							$sortdesc = "<a href='${urlbase}&sortfield=".($i+1)."&desc=1'>desc</a>";
							print("<th>${fieldname}</th>");
						}
			
						for($i=0; $i < $nbr_lignes; $i++) {
							$row = pmb_mysql_fetch_row($res);
							print "<tr>";
							foreach($row as $dummykey=>$col) {
								if(trim($col)=='') $col="&nbsp;";
								print "<td>".$col."</td>";
							}
							print "</tr>";
						}
						print "</table><hr />";
					} else {
						$ligne_affected=pmb_mysql_affected_rows();
						print "<br /><span style='color:#ff0000'>".$msg['admin_misc_lignes']." ".$ligne_affected;
						$err = pmb_mysql_error();
						if ($err){
							print "<br />$err";
						}else{
							if($ligne_affected){
								$do_reindexation=true;
							}
						}
						print "</span><hr />";
					}
				} else {
					print "<br /><br />".$valeur."<br /><br />".$msg["proc_param_explain_failed"]."<br /><br />".$erreur_explain_rqt;
					return array('state' => false, 'message' => 'explain_failed');
				}
			}
		} // fin while
		if((static::$module == 'admin') && $do_reindexation){
			print "<span style='color:#ff0000'><h2>".$msg['admin_proc_reindex']."</h2></span><br/>";
		}
		return array('state' => true, 'message' => '');
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
						show_procs();
					} else {
						// afficher le form avec les bonnes valeurs
						print static::get_proc_form($id);
					}
				} else {
					show_procs();
				}
				break;
			case 'add':
				if($f_proc_name && $f_proc_code) {
					static::create();
					show_procs();
				} else {
					print static::get_proc_form();
				}
				break;
			case 'import':
				$import_proc_tmpl = str_replace("!!action!!", static::format_url("&action=importsuite"), $import_proc_tmpl);
				print $import_proc_tmpl ;
				break;
			case 'importsuite':
				static::importsuite(static::format_url("&action=modif&id=!!id!!"), static::format_url("&action=importsuite")) ;
				break;
			case 'del':
				if($id) {
					static::delete($id);
					static::optimize();
				}
				show_procs();
				break;
			default:
				show_procs();
				break;
		}
	}
	
	public static function proceed_remote() {
		global $msg;
		global $action;
		global $do_import;
		global $id;
		global $pmb_procedure_server_address;
	
		switch($action) {
			case 'view_remote':
				if ($id) {
					$remote_procedure = new remote_procedure($id, static::$module, static::$table);
					$remote_procedure->display();
				}
				break;
			case 'import_remote':
				if ($id) {
					if($do_import) {
						$remote_procedure = new remote_procedure($id, static::$module, static::$table);
						$remote_procedure->import();
						if(static::class == 'procs') {
							show_procs();
						} else {
							static::get_display_remote_lists();
						}
					} else {
						$remote_procedure = new remote_procedure($id, static::$module, static::$table);
						print $remote_procedure->get_import_form();
					}
				}
				break;
			case 'execute_remote':
				if ($id) {
					$remote_procedure = new remote_procedure($id, static::$module, static::$table);
					$remote_procedure->execute();
				}
				break;
			case 'final_remote':
				if ($id) {
					$remote_procedure = new remote_procedure($id, static::$module, static::$table);
					$remote_procedure->final_execution();
						
					//$execute_external <=> globale dans remote_procedure->final_execution
					//$execute_external_procedure <=> globale dans remote_procedure->final_execution
					//$param_proc_hidden <=> paramêtres en champ caché en cas de forçage
					static::final_execute();
				}
				break;
			default:
				if (!$pmb_procedure_server_address) {
					echo $msg["remote_procedures_error_noaddress"];
					break;
				}
				if(static::class == 'procs') {
					show_procs();
				} else {
					static::get_display_remote_lists();
				}
				break;
		}
	}
	
	public static function importsuite($retour, $retour_erreur) {
		global $msg, $current_module, $charset;
		global $PMBuserid;
	
		print "<div class=\"row\">
		<h1>".$msg['procs_title_form_import']."</h1>";
	
		$erreur=0;
		$userfile_name = $_FILES['f_fichier']['name'];
		$userfile_temp = $_FILES['f_fichier']['tmp_name'];
		$userfile_moved = basename($userfile_temp);
	
		$userfile_name = preg_replace("/ |'|\\|\"|\//m", "_", $userfile_name);
	
		// création
		if (move_uploaded_file($userfile_temp,'./temp/'.$userfile_moved)) {
			$fic=1;
		}
	
		if (!$fic) {
			$erreur=$erreur+10;
		}
	
		if ($fic) {
			$fp = fopen('./temp/'.$userfile_moved , "r" );
			$contenu = fread ($fp, filesize('./temp/'.$userfile_moved));
			if (!$fp || $contenu=="") $erreur=$erreur+100; ;
			fclose ($fp) ;
		}
	
		//import avec encodage taggé
		if(strpos($contenu,'#charset=iso-8859-1')!==false && $charset=='utf-8'){
			//mise à jour de l'encodage du contenu
			$contenu = utf8_encode($contenu);
			//mise à jour de l'entête des paramètres
			$contenu = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $contenu) ;
		}elseif(strpos($contenu,'#charset=utf-8')!==false && $charset=='iso-8859-1'){
			//mise à jour de l'encodage du contenu
			$contenu = utf8_decode($contenu);
			//mise à jour de l'entête des paramètres
			$contenu = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $contenu) ;
		}
	
		if ($userfile_name) {
			unlink('./temp/'.$userfile_moved);
		}
	
		$pos = strpos($contenu,'INSERT INTO '.static::$table.' set ');
		if (($pos === false) || ($pos>0)) {
			$erreur=$erreur+1000; ;
		}
	
		if (!$erreur) {
			// ajouter les droits pour celui qui importe
			if ($PMBuserid!=1) $contenu = str_replace("autorisations='1'", "autorisations='1 ".$PMBuserid."'", $contenu) ;
	
			pmb_mysql_query($contenu) ;
			if (pmb_mysql_error()) {
				echo pmb_mysql_error()."<br /><br />".htmlentities($contenu,ENT_QUOTES, $charset)."<br /><br />" ;
				die ();
			}
	
			$new_proc_id = pmb_mysql_insert_id();
			$retour = str_replace("!!id!!",$new_proc_id,$retour);
			print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" >
			<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
			</form>";
			print "<script type=\"text/javascript\">document.dummy.submit();</script>";
	
		} else {
			print "<h1>".$msg['procs_import_invalide']."</h1>
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour_erreur\" >
			Error code = $erreur
			<input type='submit' class='bouton' name=\"id_form\" value=\"Ok\" />
			</form>";
		}
		print "</div>";
	}
	
	public static function final_execute() {
		global $msg, $charset;
		global $id_query;
		global $query_parameters;
		global $execute_external;
		global $id;
		global $execute_external_procedure;
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
			if (isset($hp->proc) && preg_match_all("|!!(.*)!!|U",$hp->proc->requete,$query_parameters)) {
				$hp->get_final_query();
				$code=$hp->final_query;
				$id=$id_query;
				$param_proc_hidden=$hp->get_hidden_values();//Je mets les paramêtres en champ caché en cas de forçage
				$param_proc_hidden.="<input type='hidden' name='id_query'  value='".$id_query."' />";
			} else {
				$code = '';
			}
			$requete = "SELECT * FROM ".static::$table." WHERE idproc=$id ";
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
			// récupération du résultat
			print "<form class='form-".$current_module."' id='formulaire' name='formulaire' action='' method='post'>";
			print $param_proc_hidden;
			if($force_exec){
				print "<input type='hidden' name='force_exec'  value='".$force_exec."' />";//On a forcé la requete
			}
			print static::get_form_after_execution($idp, $name, $code, $commentaire, $is_external);
			$report = static::run_query($code);
			if($report['state'] == false && $report['message'] == 'explain_failed') {
				static::final_explain_failed($id);
			}
			print "</form>";
		} else {
			print $msg["proc_param_query_failed"];
		}
	}
	
	public static function final_explain_failed($id) {
		global $msg;
		global $execute_external;
		global $pmb_procs_force_execution;
		global $PMBuserid;
		
		if ($pmb_procs_force_execution || ($PMBuserid == 1)) {
			$is_external = isset($execute_external) && $execute_external;
			if(!$is_external){
				$lien_force= static::format_url("&action=final&id=".$id."&force_exec=1");
			}else{
				$lien_force= static::format_url("&action=final_remote&id=".$id."&force_exec=1");
			}
			print "
				<script type='text/javascript'>
					if (document.getElementById('procs_button_exec')) {
						var button_procs_exec = document.getElementById('procs_button_exec');
						button_procs_exec.setAttribute('value','".addslashes($msg["procs_force_exec"])."');
						button_procs_exec.setAttribute('onClick','this.form.action=\"".$lien_force."\";this.form.submit();');
					}
				</script>
			";
		}
	}
	
	public static function get_parameters_remote() {
		$allowed_proc_types = array("AP");
		$types_selectaction = array(
				"AP" => '');
		$testable_types = array(
				"AP" => true
		);
		$type_titles = array(
				"AP" => "remote_procedures"
		);
		return array(
				'allowed_proc_types' => $allowed_proc_types,
				'types_selectaction' => $types_selectaction,
				'testable_types' => $testable_types,
				'type_titles' => $type_titles
		);
	}
	
	public static function get_display_remote_list($type="AP") {
		global $pmb_procedure_server_credentials, $pmb_procedure_server_address;
		global $msg;
		global $charset;
		
		$display = '';
		$pmb_procedure_server_credentials_exploded = explode("\n", $pmb_procedure_server_credentials);
		if ($pmb_procedure_server_address && (count($pmb_procedure_server_credentials_exploded) == 2)) {
			$aremote_procedure_client = new remote_procedure_client($pmb_procedure_server_address, trim($pmb_procedure_server_credentials_exploded[0]), trim($pmb_procedure_server_credentials_exploded[1]));
			$procedures = $aremote_procedure_client->get_procs($type);
		
			if ($procedures) {
				$buf_contenu="";
				if ($procedures->error_information->error_code) {
					$buf_contenu=$msg['remote_procedures_error_server'].":<br><i>".$procedures->error_information->error_string."</i>";
					$display .= gen_plus("procclass_remote",$msg["remote_procedures"],$buf_contenu);
				} else if (isset($procedures->elements)){
					$current_set="";
					foreach ($procedures->elements as $aprocedure) {
						if ($aprocedure->current_attached_set != $current_set) {
							$parity=0;
							$current_set = $aprocedure->current_attached_set;
							$buf_contenu .= '<tr><th colspan=4>'.htmlentities($current_set, ENT_QUOTES, $charset).'</th>';
						}
						if ($parity % 2) {$pair_impair = "even"; } else {$pair_impair = "odd";}
						$parity += 1;
						$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
						$buf_contenu.="\n<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
						<td style='width:10px'>
						<input class='bouton' type='button' value=' $msg[708] ' onClick=\"document.location='".static::format_url("&action=execute_remote&id=".$aprocedure->id)."'\" />
						</td>
						<td onmousedown=\"document.location='".static::format_url('&action=view_remote&id='.$aprocedure->id)."';\">
						".($aprocedure->untested ? "[<i>".$msg["remote_procedures_procedure_non_validated"]."</i>]&nbsp;&nbsp;" : '')."<strong>$aprocedure->name</strong><br/>
						<small>$aprocedure->comment&nbsp;</small>
						</td>
						<td>";
						//if (preg_match_all("|!!(.*)!!|U",$row[2],$query_parameters)) $buf_contenu.="<a href='admin.php?categ=proc&sub=proc&action=configure&id_query=".$row[0]."'>".$msg["procs_options_config_param"]."</a>";
						$buf_contenu.="</td>";
						$buf_contenu.="<td><input class='bouton' type='button' value=\"".$msg['remote_procedures_import']."\" onClick=\"document.location='".static::format_url('&action=import_remote&id='.$aprocedure->id)."'\" /></td>
						</tr>";
					}
					$buf_contenu="<table></tr>".$buf_contenu."</table>";
					$display .= gen_plus("procclass_remote",$msg["remote_procedures"],$buf_contenu);
				} else {
					$buf_contenu="<br>".$msg["remote_procedures_no_procs"]."<br><br>";
					$display .= gen_plus("procclass_remote",$msg["remote_procedures"],$buf_contenu);
				}
			}
		}
		print $display;
	}
	
	public static function get_display_remote_lists() {
		static::get_display_remote_list();
	}
	
	public static function format_url($url='') {
		global $base_path;
		
		return $base_path."/".static::$module.".php?categ=proc&sub=proc".$url;
	}
	
	public static function get_name($id) {
		$query = "SELECT name FROM ".static::$table." WHERE idproc=".$id;
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
}