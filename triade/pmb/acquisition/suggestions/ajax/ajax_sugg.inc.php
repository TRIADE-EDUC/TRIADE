<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_sugg.inc.php,v 1.15 2021/12/28 10:46:12 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoifaire, $action, $object_type;

require_once($class_path."/suggestions_origine.class.php");

if(!isset($quoifaire)) $quoifaire = '';
switch($quoifaire){
	
	case 'ajout_origine':
	case 'suppr_origine':
		mod_origine();
		break;
	default:
		switch($action) {
			case "list":
				lists_controller::proceed_ajax($object_type, 'suggestions');
				break;
		}
		break;
}

function mod_origine(){
	global $id_sugg,$orig, $type_orig, $msg;
	global $quoifaire;
	
	
	$sug_ori = new suggestions_origine($orig,$id_sugg);
	if($sug_ori){
		if($type_orig) $sug_ori->type_origine = $type_orig;		
		switch ($quoifaire) {
			case 'ajout_origine':
				$sug_ori->save();
				break;
			case 'suppr_origine':
				suggestions_origine::delete($sug_ori->num_suggestion,$sug_ori->origine,$sug_ori->type_origine);
				break;
		}
	}
	$premier_user = "";
	$list_user = "";
	$req_select = suggestions_origine::listOccurences($id_sugg);
	$res = pmb_mysql_query($req_select);
	$nb_user = 0;
	while(($user = pmb_mysql_fetch_object($res))){
		switch($user->type_origine){
			default:
				case '0' :
				 	$requete_user = "SELECT userid, nom, prenom FROM users where userid = '".$user->origine."'";
					$res_user = pmb_mysql_query($requete_user);
					$row_user=pmb_mysql_fetch_row($res_user);
					$lib_orig = $row_user[1];
					if ($row_user[2]) $lib_orig.= ", ".$row_user[2];
					$suppr_click = "onClick=\"if(confirm('".$msg['confirm_suppr_origine']."')){ ajax_suppr_origine('".$user->origine."','".$user->type_origine."');}\"";					
					if(empty($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
				case '1' :
				 	$requete_empr = "SELECT id_empr, empr_nom, empr_prenom FROM empr where id_empr = '".$user->origine."'";
					$res_empr = pmb_mysql_query($requete_empr);
					$row_empr=pmb_mysql_fetch_row($res_empr);
					$lib_orig = $row_empr[1];
					if ($row_empr[2]) $lib_orig.= ", ".$row_empr[2];
					$suppr_click = "onClick=\"if(confirm('".$msg['confirm_suppr_origine']."')){ ajax_suppr_origine('".$user->origine."','".$user->type_origine."');}\"";
					if(empty($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
				case '2' :
					if($user->origine) $lib_orig = $user->origine;
					else $lib_orig = $msg['suggest_anonyme'];
					$suppr_click = "onClick=\"if(confirm('".$msg['confirm_suppr_origine']."')){ ajax_suppr_origine('".$user->origine."','".$user->type_origine."');}\"";
					if(empty($premier_user)) $premier_user = $lib_orig;
					else $list_user .= $lib_orig."<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["origine_suppr"]."\" alt=\"".$msg["origine_suppr"]."\" $suppr_click /><br />";
					break;
		}
		$nb_user++;
	}
	$ajout_create = "
		<input type='text' id='creator_lib_orig_ajax' name='creator_lib_orig' class='saisie-10emr'/>
		<input type='button' id='creator_btn_orig_ajax' class='bouton_small' value='...' onclick=\"openPopUp('./select.php?what=origine&caller=sug_modif_form&param1=orig&param2=creator_lib_orig_ajax&param3=typ&param4=&param5=&param6=&callback=ajax_origine&deb_rech='+".pmb_escape()."(document.getElementById('creator_lib_orig_ajax').value), 'selector')\" />";
	$list_user .= $ajout_create;
	  
	if(pmb_mysql_num_rows($res) > 1){
		$result = gen_plus('ori_ajax',$msg['suggest_creator']. " (".($nb_user-1).")",$list_user,0);
	} else if(pmb_mysql_num_rows($res) == 1){
		$result = $list_user;
	}
	
	ajax_http_send_response($result);
}
?>