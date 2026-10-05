<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mimetype.inc.php,v 1.9.2.1.2.2 2025/03/04 15:50:01 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $action, $form_actif;

switch ($action) {
	case "update":
	    if (!empty($form_actif)) {
	        update_mimetypeConf();
	    }
		show_form($action);
		break;
    default :
		show_form("");
		break;
}

function show_form($action = '') {
	global $msg;
	global $class_param;
	global $mimetypeConf, $mimetypeConfByDefault;

	$message = '';
	if ($action == "update") {
		$message = "<div class='erreur'>".$msg["visionneuse_admin_update"]."</div>";
	}
	$form = "$message
	<form class='form-admin' name='modifParam' method='post' action='./admin.php?categ=visionneuse&sub=mimetype&action=update'>
		<h3>".$msg["visionneuse_admin_mimetype"]."</h3>
		<table>
			<tr>
				<th>".$msg["visionneuse_param_mimetype"]."</th>
				<th>".$msg["visionneuse_param_class_dispo"]."</th>
			</tr>";
	$i = 0;
	foreach ($class_param->mimetypeClasses as $mimetype => $classes) {
		$form .= "
		    <tr class='".($i%2 ? "odd":"even")."'>
				<td>$mimetype</td>
				<td>
					<br />
					<input type='hidden' name='mime_in[$i]' value='$mimetype' />
					<select name='mime_sel[$i]'>";
		foreach ($classes as $class) {

			$currentMimetypeConf = $mimetypeConf[$mimetype] ?? "";
			$currentDefaultMimetypeConf = $mimetypeConfByDefault[$mimetype] ?? "";

			if (is_countable($mimetypeConf) && sizeof($mimetypeConf) > 0) {
				$form .= "
				<option value='$class'".($currentMimetypeConf==$class ? " selected='selected'":"").">$class</option>";
			} else {
				$form .= "
				<option value='$class'".($currentDefaultMimetypeConf==$class ? " selected='selected'":"").">$class</option>";
			}
		}
		$form .= "
					</select>
					<br />
					<br />
				</td>
			</tr>";
		$i++;
	}
	$form .= "
		</table>
		<input type='hidden' name='form_actif' value='1'>
		<input class='bouton' type='submit' value='". $msg["visionneuse_admin_save"] ."' />
	</form>
	";
	print $form;
}

function update_mimetypeConf() {
	global $charset;
	global $mime_in, $mime_sel, $mimetypeConf;
	foreach ($mime_sel as $k => $value) {
		$mimetypeConf[$mime_in[$k]] = $value;
	}
	$rqt ="UPDATE parametres SET valeur_param = '".htmlspecialchars(addslashes(serialize($mimetypeConf)), ENT_QUOTES, $charset)."' WHERE type_param LIKE 'opac' AND sstype_param LIKE 'visionneuse_params' ";
	$res = pmb_mysql_query($rqt);
}