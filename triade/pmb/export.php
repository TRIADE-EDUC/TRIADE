<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.php,v 1.11 2023/05/04 09:22:14 rtigero Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH|CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
$base_nosession=1;
require_once ("$base_path/includes/init.inc.php");  

global $sub, $quoi, $charset, $id, $id_req;

$id = intval($id);
$id_req = intval($id_req);
switch($quoi) {
	// Export de procédures
	case "procs":
		switch($sub) {
			case "caddie" :
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"caddie_proc_".$id.".sql\"");
				
				$req="select type, name, requete, comment, autorisations, parameters from caddie_procs where idproc='$id' ";
				$res = pmb_mysql_query($req);
				if ($p=pmb_mysql_fetch_object($res)) {
					$exp="INSERT INTO caddie_procs set type='".addslashes($p->type)."', name='".addslashes($p->name)."', requete='".addslashes($p->requete)."', comment='".addslashes($p->comment)."', autorisations='1', parameters='".addslashes($p->parameters)."' ";
					//nettoyage de l'entête des paramètres, pour les anciennes procédures
					if($charset=='utf-8'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $exp) ;
					}elseif($charset=='iso-8859-1'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $exp) ;
					}
					//tag pour l'encodage du contenu
					$exp .= "\n#charset=".$charset;
					echo $exp ;
					}			
				break;
			case "empr_caddie" :
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"empr_caddie_proc_".$id.".sql\"");
				
				$req="select type, name, requete, comment, autorisations, parameters from empr_caddie_procs where idproc='$id' ";
				$res = pmb_mysql_query($req);
				if ($p=pmb_mysql_fetch_object($res)) {
					$exp="INSERT INTO empr_caddie_procs set type='".addslashes($p->type)."', name='".addslashes($p->name)."', requete='".addslashes($p->requete)."', comment='".addslashes($p->comment)."', autorisations='1', parameters='".addslashes($p->parameters)."' ";
					//nettoyage de l'entête des paramètres, pour les anciennes procédures
					if($charset=='utf-8'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $exp) ;
					}elseif($charset=='iso-8859-1'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $exp) ;
					}
					//tag pour l'encodage du contenu
					$exp .= "\n#charset=".$charset;
					echo $exp ;
					}			
				break;
			case "authorities_caddie" :
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"authorities_caddie_proc_".$id.".sql\"");
			
				$req="select type, name, requete, comment, autorisations, parameters from authorities_caddie_procs where idproc='$id' ";
				$res = pmb_mysql_query($req);
				if ($p=pmb_mysql_fetch_object($res)) {
					$exp="INSERT INTO authorities_caddie_procs set type='".addslashes($p->type)."', name='".addslashes($p->name)."', requete='".addslashes($p->requete)."', comment='".addslashes($p->comment)."', autorisations='1', parameters='".addslashes($p->parameters)."' ";
					//nettoyage de l'entête des paramètres, pour les anciennes procédures
					if($charset=='utf-8'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $exp) ;
					}elseif($charset=='iso-8859-1'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $exp) ;
					}
					//tag pour l'encodage du contenu
					$exp .= "\n#charset=".$charset;
					echo $exp ;
				}
				break;
			case "actionsperso" :
				header("Content-Type: application/download\n");
				header("Content-Disposition: atachement; filename=\"admin_proc_".$id.".sql\"");
				
				$req="select name, requete, comment, autorisations, parameters from procs where idproc='$id' ";
				$res = pmb_mysql_query($req);
				if ($p=pmb_mysql_fetch_object($res)) {
					$exp="INSERT INTO procs set name='".addslashes($p->name)."', requete='".addslashes($p->requete)."', comment='".addslashes($p->comment)."', autorisations='1', parameters='".addslashes($p->parameters)."' ";
					//nettoyage de l'entête des paramètres, pour les anciennes procédures
					if($charset=='utf-8'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $exp) ;
					}elseif($charset=='iso-8859-1'){
						$exp = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $exp) ;
					}
					//tag pour l'encodage du contenu
					$exp .= "\n#charset=".$charset;
					echo $exp ;
					}			
				break;
				
			}			
		break;
	case "stat" :
		header("Content-Type: application/download\n");
		header("Content-Disposition: atachement; filename=\"admin_stat_".$id_req.".sql\"");
		
		$req="SELECT * from statopac_request WHERE idproc='$id_req' ";
		$res = pmb_mysql_query($req);
		if ($p=pmb_mysql_fetch_object($res)) {
			$requete=$p->requete;
			$exp="INSERT INTO statopac_request set name='".addslashes($p->name)."', requete='".addslashes($requete)."', comment='".addslashes($p->comment)."', parameters='".addslashes($p->parameters)."' ";
			//nettoyage de l'entête des paramètres, pour les anciennes procédures
			if($charset=='utf-8'){
				$exp = str_replace('<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', '<?xml version=\"1.0\" encoding=\"utf-8\"?>', $exp) ;
			}elseif($charset=='iso-8859-1'){
				$exp = str_replace('<?xml version=\"1.0\" encoding=\"utf-8\"?>', '<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>', $exp) ;
			}
			//Colonnes vue
			$req2="SELECT nom_col, expression, filtre, datatype FROM statopac_vues_col WHERE num_vue=".$p->num_vue." ORDER BY LENGTH(nom_col) DESC";
			$res2 = pmb_mysql_query($req2);
			while ($p2=pmb_mysql_fetch_object($res2)) {
				if(preg_match('`[^a-zA-Z0-9\_]'.$p2->nom_col.'[^a-zA-Z0-9\_]`',$requete)){
					$arrayTmp=array();
					$arrayTmp[]=$p2->nom_col;
					$arrayTmp[]=$p2->expression;
					$arrayTmp[]=$p2->filtre;
					$arrayTmp[]=$p2->datatype;
					$exp .= "\n#col=".serialize($arrayTmp);
				}				
			}
			//tag pour l'encodage du contenu
			$exp .= "\n#charset=".$charset;
			echo $exp ;
		}
		break;
	case "notice_tpl" :
		header("Content-Type: application/download\n");
		header("Content-Disposition: atachement; filename=\"notice_tpl_".$id.".tpl\"");
		
		$exp = '';
		$array_loc=array();
		$req="SELECT * from notice_tpl WHERE notpl_id='$id' ";
		$res = pmb_mysql_query($req);
		if ($p=pmb_mysql_fetch_object($res)) {
			$requete=$p->requete ?? "";
			$array_tmp=array();
			$array_tmp[]=array("field"=>'notpl_name',"value"=>$p->notpl_name);
			$array_tmp[]=array("field"=>'notpl_code',"value"=>$p->notpl_code);
			$array_tmp[]=array("field"=>'notpl_comment',"value"=>$p->notpl_comment);
			if (trim($exp)) {
				$exp .= "\n";
			}
			$exp .= "#main#=".serialize($array_tmp);
			$req2="SELECT * from notice_tplcode WHERE num_notpl='$id' ";
			$res2 = pmb_mysql_query($req2);
			$array_sub=array();
			while ($p2=pmb_mysql_fetch_object($res2)) {
				$array_tmp=array();
				$array_tmp[]=array("field"=>'notplcode_localisation',"value"=>$p2->notplcode_localisation);
				$array_tmp[]=array("field"=>'notplcode_typdoc',"value"=>$p2->notplcode_typdoc);
				$array_tmp[]=array("field"=>'notplcode_niveau_biblio',"value"=>$p2->notplcode_niveau_biblio);
				$array_tmp[]=array("field"=>'notplcode_niveau_hierar',"value"=>$p2->notplcode_niveau_hierar);
				$array_tmp[]=array("field"=>'nottplcode_code',"value"=>$p2->nottplcode_code);
				$array_sub[]=$array_tmp;
				if($p2->notplcode_localisation){
					if(!count($array_loc)||!in_array($p2->notplcode_localisation,$array_loc)){
						$array_loc[]=$p2->notplcode_localisation;
					}
				}
			}
			$exp .= "\n#sub#=".serialize($array_sub);
			//localisations
			if(count($array_loc)){
				$array_tmp=array();
				foreach($array_loc as $id_loc){
					$res=pmb_mysql_query("SELECT location_libelle FROM docs_location WHERE idlocation=".$id_loc);
					$row=pmb_mysql_fetch_object($res);
					$array_tmp[]=array("id_location"=>$id_loc,"lib_location"=>$row->location_libelle);
					
				}
				$exp .= "\n#locations#=".serialize($array_tmp);
			}
			//tag pour l'encodage du contenu
			$exp .= "\n#charset#=".$charset;
			echo $exp ;
		}
		break;
	}
	
pmb_mysql_close();
