<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param.inc.php,v 1.11.8.1 2025/03/04 15:50:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $visionneuse_path, $quoi, $action, $submenu;

require_once $visionneuse_path."/classes/mimetypes/".$quoi."/".$quoi.".class.php";

$current_class = new $quoi();

switch($action){
	case "update" :
	    if ($form_actif) {
	        update_params();
	    }
		$submenu.= show_form($action);
		break;
	default :
	    $submenu.= show_form('');
	    break;
}

function show_form($action=''){
	global $quoi,$msg;
	global $current_class;

	$i = 0;
	$message = '';
	if($action=="update") {
		$message = "<div class='erreur'>".$msg["visionneuse_admin_update"]."</div>";
	}

	//on récup les infos déjà existantes
	$params = $params_values = array();
	$rqt = "SELECT visionneuse_params_parameters FROM visionneuse_params WHERE visionneuse_params_class LIKE '".$quoi."'";
	if($res = pmb_mysql_query($rqt)){
		if(pmb_mysql_num_rows($res)) {
			$paramToUnserialize = htmlspecialchars_decode(pmb_mysql_result($res, 0, 0));
			$params_values = $current_class->unserializeParams($paramToUnserialize);
		}
		$current_class->getTabParam();
	}
	$form = $message."
		<form class='form-admin' name='".$quoi."Param' method='POST' action='./admin.php?categ=visionneuse&sub=class&quoi=$quoi&vue=param&action=update' >
			<h3>$quoi</h3>
			<table>
				<tr>
					<th>Nom</th>
					<th>Valeur</th>
					<th>Description</th>
				</tr>";
	foreach($current_class->tabParam as $key =>$tabParam){
		$form.="
				<tr class='".($i%2 ? "odd":"even")."'>
					<td>$key</td>";
		if ($tabParam['type'] == "radio") {
			$form.="<td>";
			foreach ($tabParam['value'] as $value => $label) {
				$form.="<input type='".$tabParam['type']."' name='".$tabParam['name']."' id='".$tabParam['name']."_".$value."' value='".$value."' ".($params_values[$key] == $value ? "checked='checked'" : "")."/><label for='".$tabParam['name']."_".$value."'>&nbsp;".$label."&nbsp;</label>";
			}
			$form.="</td>";
		} else {
			$form.="
					<td><input type='".$tabParam['type']."' name='".$tabParam['name']."' id='".$tabParam['name']."' value='".$tabParam['value']."' ".($tabParam['type'] == "checkbox" ? (isset($params_values[$key]) && $params_values[$key] == 1 ? "checked='checked'" : ""): "")."/></td>";
		}
			$form.="
					<td>".$tabParam['desc']."</td>
				</tr>";
		$i++;
	}
	$form.="
			</table>
			<input type='hidden' name='form_actif' value='1'>
			<input class='bouton' type='submit' value='". $msg["visionneuse_admin_save"] ."' />
		</form>
	";
	return $form;
}

function update_params(){
	global $quoi;
	global $current_class;
	global $charset;

	$paramsToSerialize =array();
	$current_class->getTabParam();
	foreach ($_POST as $key => $value){
		if(isset($current_class->tabParam[$key])){
			$paramsToSerialize[$key] = $value;
		}
	}
	$serializedParams = $current_class->serializeParams($paramsToSerialize);
	$rqt = "SELECT visionneuse_params_class FROM visionneuse_params WHERE visionneuse_params_class LIKE '$quoi'";
	if($res=pmb_mysql_query($rqt)){
		if(pmb_mysql_num_rows($res))
			$requete = "UPDATE visionneuse_params SET visionneuse_params_parameters='".htmlspecialchars($serializedParams,ENT_QUOTES,$charset)."' WHERE visionneuse_params_class='$quoi'";
		else $requete= "INSERT INTO visionneuse_params SET visionneuse_params_class='$quoi', visionneuse_params_parameters='".htmlspecialchars($serializedParams,ENT_QUOTES,$charset)."'";
	}
	$res = pmb_mysql_query($requete);
}

?>
