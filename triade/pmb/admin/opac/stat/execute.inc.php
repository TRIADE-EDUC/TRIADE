<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: execute.inc.php,v 1.9 2017-06-02 10:05:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path,$include_path,$PMBuserid,$charset;//Comme ce script est appelé d'une fonction, il faut définir des globals
require_once ($class_path."/stat_query.class.php");
require_once ($class_path."/procs.class.php");

// include d'exécution d'une procédure

$requete = "SELECT * FROM statopac_request WHERE idproc=$id ";
$res = pmb_mysql_query($requete, $dbh);

$nbr_lignes = pmb_mysql_num_rows($res);
$urlbase = "./admin.php?categ=opac&sub=stat&section=view_list&act=final&id=$id";
if ($force_exec) $urlbase .= "&force_exec=$force_exec";

if($nbr_lignes) {

	// récupération du résultat
	$row = pmb_mysql_fetch_row($res);
	$idp = $row[0];
	$name = $row[1];
	if (!isset($code) || !$code)
		$code = $row[2];
	$commentaire = $row[3];
	
	//on remplace VUE par el nom de la table dynamique associée
	$num_vue = stat_query::get_vue_associee($id);
	$code = str_replace('VUE()','statopac_vue_'.$num_vue,$code);
	print "<br>
		<h3>".htmlentities($msg["procs_execute"]." ".$name, ENT_QUOTES, $charset)."</h3>
		<br/>".htmlentities($commentaire, ENT_QUOTES, $charset)."<hr/>
			<input type='button' class='bouton' value='$msg[62]'  onClick='document.location=\"./admin.php?categ=opac&sub=stat&section=query&act=update_request&id_req=$id\"' />";
		if (($pmb_procs_force_execution && $force_exec) || (($PMBuserid == 1) && $force_exec)) {
			print "<input type='button' id='procs_button_exec' class='bouton' value='".htmlentities($msg["procs_force_exec"], ENT_QUOTES, $charset)."' onClick='document.location=\"./admin.php?categ=opac&sub=stat&section=view_list&act=exec_req&id_req=$id&force_exec=1\"' />";
		} else {
			print "<input type='button' id='procs_button_exec' class='bouton' value='$msg[708]' onClick='document.location=\"./admin.php?categ=opac&sub=stat&section=view_list&act=exec_req&id_req=$id\"' />";
		}
	print "<br />";
	$report = procs::run_query($code);
	if($report['state'] == false && $report['message'] == 'explain_failed') {
		if ($pmb_procs_force_execution || ($PMBuserid == 1)) {
			print "
				<script type='text/javascript'>
					if (document.getElementById('procs_button_exec')) {
						var button_procs_exec = document.getElementById('procs_button_exec');
						button_procs_exec.setAttribute('value','".addslashes($msg["procs_force_exec"])."');
						button_procs_exec.setAttribute('onClick','document.location=\"./admin.php?categ=opac&sub=stat&section=view_list&act=exec_req&id_req=$id&force_exec=1\"');
					}
				</script>
			";
		}
	}
} else {
	print $msg["proc_param_query_failed"];
}
