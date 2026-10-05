<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lib_func_customfields.inc.php,v 1.3 2022/04/25 14:43:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


function func_customfields_recup_noticeunimarc_suite($notice) {
	global $info_900;

	$info_900=array();

	$record = new iso2709_record($notice, AUTO_UPDATE); 
	
	$info_900=$record->get_subfield("900","a","l","n");
	
} // fin recup_noticeunimarc_suite 
	
function func_customfields_import_new_notice_suite() {
	global $notice_id ;
	
	global $info_900;
	
	for($i=0;$i<count($info_900);$i++){		
		
		$req = " select idchamp, type, datatype from notices_custom where name='".$info_900[$i]['n']."'";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			$perso = pmb_mysql_fetch_object($res);
			if($perso->idchamp){						
				if($perso->type == 'list'){
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($info_900[$i]['a'])."' and notices_custom_champ=$perso->idchamp";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=$perso->idchamp";
						$resultat=pmb_mysql_query($requete);
						$max=pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($info_900[$i]['a'])."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$req="SELECT 1 FROM notices_custom_values WHERE notices_custom_champ='".$perso->idchamp."' AND notices_custom_origine='".$notice_id."' AND notices_custom_".$perso->datatype."='".$value."'";
					if(($res=pmb_mysql_query($req)) && !pmb_mysql_num_rows($res)){//Pour éviter d'importer deux fois la même chose (c'était le cas en z39.50 ou connecteur lorsque l'on importe les notices une à une 
						$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".$value."')";
						pmb_mysql_query($requete);
					}
				} else {
					$req="SELECT 1 FROM notices_custom_values WHERE notices_custom_champ='".$perso->idchamp."' AND notices_custom_origine='".$notice_id."' AND notices_custom_".$perso->datatype."='".addslashes($info_900[$i]['a'])."'";
					if(($res=pmb_mysql_query($req)) && !pmb_mysql_num_rows($res)){//Pour éviter d'importer deux fois la même chose (c'était le cas en z39.50 ou connecteur lorsque l'on importe les notices une à une 
						$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".addslashes($info_900[$i]['a'])."')";
						pmb_mysql_query($requete);
					}
				}
			}	
		}
	}	
} 