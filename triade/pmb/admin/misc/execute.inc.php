<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: execute.inc.php,v 1.16 2022/01/04 08:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id;

// include d'exécution d'une procédure
require_once ($class_path."/procs.class.php");

$id = intval($id);
$requete = "SELECT * FROM procs WHERE idproc=$id ";
$res = pmb_mysql_query($requete);

$nbr_lignes = pmb_mysql_num_rows($res);
$urlbase = "./admin.php?categ=misc&sub=proc&action=final&id=$id";

if($nbr_lignes) {

	// récupération du résultat
	$row = pmb_mysql_fetch_row($res);
	$idp = $row[0];
	$name = $row[1];
	if (!$code)
		$code = $row[2];
	$commentaire = $row[3];
	print "<br />
		<h3>".$msg["procs_execute"]." $name</h3>
		<br />
			<input type='button' class='bouton' value='$msg[62]'  onClick='document.location=\"./admin.php?categ=misc&sub=proc&action=modif&id=$id\"' />
			<input type='button' class='bouton' value='$msg[708]' onClick='document.location=\"./admin.php?categ=misc&sub=proc&action=execute&id=$id\"' />
		<br />$commentaire<hr />";
	procs::run_query($code);
} else {
	print $msg["proc_param_query_failed"];
}
