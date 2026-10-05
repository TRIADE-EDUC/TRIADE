<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_customfields.inc.php,v 1.7 2023/10/11 10:09:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function z_recup_noticeunimarc_suite($notice) {
	global $base_path;
	require_once($base_path."/admin/import/func_customfields.inc.php");
	recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	global $base_path;
	require_once($base_path."/admin/import/func_customfields.inc.php");
	import_new_notice_suite();
}

// Permet de mémoriser la valeur d'un import extern pour ensuite l'intégré dans un champ perso de la notice avec param_perso_form
function param_perso_prepare($record) {
	global $param_perso_900;
	
	$param_perso_900=$record->get_subfield("900","a","l","n");
	
}

function param_perso_form(&$p_perso) {
	global $param_perso_900;

	for($i=0;$i<count($param_perso_900);$i++){
	
		$req = " select idchamp, type, datatype from notices_custom where name='".$param_perso_900[$i]['n']."'";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			$perso = pmb_mysql_fetch_object($res);
			if($perso->idchamp){
				if($perso->type == 'list'){
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($param_perso_900[$i]['a'])."' and notices_custom_champ=$perso->idchamp";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=$perso->idchamp";
						$resultat=pmb_mysql_query($requete);
						$max=pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($param_perso_900[$i]['a'])."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$p_perso->values[$perso->idchamp][]=$value;
				} else {
					$p_perso->values[$perso->idchamp][]=$param_perso_900[$i]['a'];
				}
			}
		}
	}
}

function traite_concepts_for_form($tableau_606 = array()) {
}
	
?>