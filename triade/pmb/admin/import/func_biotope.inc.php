<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_biotope.inc.php,v 1.3 2022/01/12 07:49:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//require_once("$class_path/categories.class.php");
function recup_noticeunimarc_suite($notice) {

	global $info_606_a,$info_900;

	$info_900=array();
	
	$record = new iso2709_record($notice, AUTO_UPDATE); 
	
	$info_606_a = $record->get_subfield_array_array ( "606", "a" );
	$info_900=$record->get_subfield("900","a","l","n");
	
} // fin recup_noticeunimarc_suite 
	
function import_new_notice_suite() {
	global $notice_id ;
	global $info_606_a;
	global $info_900;

	
	$ordre_categ = 0;
	for($i = 0; $i < count ( $info_606_a ); $i ++) {
		for($j = 0; $j < count ( $info_606_a [$i] ); $j ++) {
			$descripteur = $info_606_a [$i] [$j];
			//Recherche du terme
			//dans le thesaurus par defaut et dans la langue de l'interface
			$libelle = addslashes ( $descripteur );
			$categ_id = categories::searchLibelle ( $libelle );
			
			if ($categ_id) {
				$requete = "INSERT INTO notices_categories (notcateg_notice,num_noeud,ordre_categorie) values($notice_id,$categ_id,$ordre_categ)";
				pmb_mysql_query($requete);
				$ordre_categ ++;
			}
		}
	}
	
	
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
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($info_900[$i]['a'])."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".$value."')";
					pmb_mysql_query($requete);
				} else {
					$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_".$perso->datatype.") values($perso->idchamp,$notice_id,'".addslashes($info_900[$i]['a'])."')";
					pmb_mysql_query($requete);
				}
			}	
		}
	}	
} 