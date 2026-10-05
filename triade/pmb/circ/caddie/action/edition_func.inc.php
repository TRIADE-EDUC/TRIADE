<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edition_func.inc.php,v 1.15 2022/03/21 12:54:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/editions_datasource.class.php");

// Affichage tabulaire du contenu d'un caddie
function afftab_empr_cart_objects ($idcaddie=0, $flag="" , $no_flag = "" ) {
global $msg;
global $worksheet ;
global $myCart ;
global $dest ;
global $entete_bloc;
global $max_aut ;
global $max_perso;
global $res_compte1 ;

global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermï¿½s

if (($flag=="") && ($no_flag=="")) {
	$no_flag = 1;
	$flag = 1;
	}

$requete = "SELECT empr_caddie_content.* FROM empr_caddie_content where empr_caddie_id='".$idcaddie."' ";
if ($flag && $no_flag ) $complement_clause = "";
if (!$flag && $no_flag ) $complement_clause = " and (flag is null or flag='') ";
if ($flag && !$no_flag ) $complement_clause = " and flag is not null ";
if (!$flag && !$no_flag ) return ;
$requete .= $complement_clause." order by object_id";

$liste=array();
$result = pmb_mysql_query($requete) or die($requete."<br />".pmb_mysql_error());
if(pmb_mysql_num_rows($result)) {
	while ($temp = pmb_mysql_fetch_object($result)) 
		$liste[] = array('object_id' => $temp->object_id, 'flag' => $temp->flag ) ;
	} else return;

switch($dest) {
	case "TABLEAU":
		break;
	case "TABLEAUHTML":
	default:
		echo pmb_bidi("<h1>".$msg['panier_num']." $idcaddie / ".$myCart->name."</h1>");
		echo pmb_bidi($myCart->comment."<br />");
		
		break;
	}

	// calcul du nombre max de colonnes pour les champs perso
	$rqt_compte1 = "select idchamp, titre from empr_custom order by ordre " ;
	$res_compte1 = pmb_mysql_query($rqt_compte1) ; 
	$max_perso = pmb_mysql_num_rows($res_compte1) ;
		
	// boucle de parcours des exemplaires trouvés
	$entete=0;
	foreach ($liste as $cle => $empr) {
		$rqt_tout = "select id_empr,empr_cb,empr_nom,empr_prenom,empr_adr1,empr_adr2,empr_cp,empr_ville,empr_pays,empr_mail,empr_tel1,empr_tel2,empr_prof,empr_year,";
		$rqt_tout .=" empr_categ.libelle as categ,";
		$rqt_tout .=" empr_codestat.libelle as code_stat,statut_libelle,location_libelle,type_abt_libelle,";
		$rqt_tout .=" empr_creation,empr_modif,empr_sexe,empr_login,empr_date_adhesion,empr_date_expiration,empr_msg,empr_lang,empr_ldap,last_loan_date,date_fin_blocage,total_loans, group_concat(libelle_groupe separator ' ; ') as 'libelle_groupe'";
		$rqt_tout .=" from empr left join empr_groupe on id_empr = empr_id left join groupe on id_groupe = groupe_id left join type_abts on id_type_abt=type_abt, empr_categ, empr_codestat, empr_statut, docs_location";
		$rqt_tout .=" where id_empr='".$empr['object_id']."' and empr_categ=id_categ_empr and empr_codestat=idcode and empr_statut=idstatut and empr_location=idlocation";
		if (!$entete) {
			extrait_info_empr($rqt_tout, 1, $empr['flag']);
			$entete=1;
			} else extrait_info_empr($rqt_tout, 0, $empr['flag']);
		} // fin de liste
return;
}

function extrait_info_empr ($sql="", $entete=1, $flag="") {
	global $dest ;
	global $worksheet ;
	global $entete_bloc;
	global $msg;
	global $charset;
	global $debligne_excel;
	global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermés
	
	global $max_perso;
	global $res_compte1 ;
	
	if (!$debligne_excel) $debligne_excel = 0 ;
	
	$res = pmb_mysql_query($sql);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$nbr_champs = pmb_mysql_num_fields($res);
             		
	if ($nbr_lignes) {
		$editions_datasource = array();
		if($nbr_lignes) {
			$editions_datasource['lenders'] = new editions_datasource('lenders');
		}
		switch($dest) {
			case "TABLEAU":
				if ($entete) {
// 					$worksheet->write_string((1+$debligne_excel),0,$msg["caddie_mess_edition_".$entete_bloc]);
					$debligne_excel++ ;
					$worksheet->write_string((1+$debligne_excel),0,$msg['caddie_action_marque']);
					for($i=0; $i < $nbr_champs; $i++) {
						// entête de colonnes
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['lenders']->struct_format['empr_'.$fieldname])) {
							$worksheet->write_string((1+$debligne_excel),($i+1),$editions_datasource['lenders']->struct_format['empr_'.$fieldname]['label']);
						} else {
							$worksheet->write_string((1+$debligne_excel),($i+1),$fieldname);
						}
					}
					$worksheet->write_string((1+$debligne_excel),($nbr_champs+1),"DESCR");
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte1) ;
						$worksheet->write_string((1+$debligne_excel),($nbr_champs+2+$i),$perso->titre);
					}
					$debligne_excel++ ;
				}
				for($i=0; $i < $nbr_lignes; $i++) {
					$debligne_excel++;
					$row = pmb_mysql_fetch_row($res);
					$id_notice = $row[0] ;
					if ($flag) $worksheet->write_string(($i+$debligne_excel),0,"X");
					$j=0;
					foreach($row as $col) {
						if(!$col) $col=" ";
						$worksheet->write_string(($i+$debligne_excel),($j+1),$col);
						$j++;
					}
					$p_perso=new parametres_perso("empr");
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields($id_notice);
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							$worksheet->write_string($debligne_excel,($nbr_champs+2+$i),html_entity_decode($p["AFF"],ENT_QUOTES|ENT_COMPAT,$charset));
						}
					}
				}
				break;
			case "TABLEAUHTML":
				if ($entete) {
					if ($etat_table) echo "\n</table>";
// 					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class='align_left'>".$msg['caddie_action_marque']."</th>";
					$etat_table = 1 ;
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['lenders']->struct_format['empr_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['lenders']->struct_format['empr_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
					print "<th class='align_left'>DESCR</th>" ;
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte1) ;
						print "<th class='align_left'>".$perso->titre."</th>" ;
					}
				}
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
					$id_notice = $row[0] ;
					echo "<tr>";
					if ($flag) print "<td>X</td>"; else print "<td>&nbsp;</td>";
					foreach($row as $col) {
						if (is_numeric($col)){
 							$col = "'".$col ;
							}
						if(!$col) $col="&nbsp;";
						print pmb_bidi("<td>$col</td>");
					}
					print "<td>&nbsp;</td>" ;
					$p_perso=new parametres_perso("empr");
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields($id_notice);
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							print "<td>".$p["AFF"]."</td>" ;
						}
					}
					echo "</tr>";
				}
				break;
			default:
				if ($entete) {
					if ($etat_table) echo "\n</table>";
// 					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class='align_left'>".$msg['caddie_action_marque']."</th>";
					$etat_table = 1 ;
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['lenders']->struct_format['empr_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['lenders']->struct_format['empr_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
					print "<th class='align_left'>DESCR</th>" ;
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte1) ;
						print "<th class='align_left'>".$perso->titre."</th>" ;
					}
				}
				$odd_even=0;
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
					$id_notice = $row[0] ;
					if ($odd_even==0) {
						echo "	<tr class='odd'>";
						$odd_even=1;
					} else if ($odd_even==1) {
						echo "	<tr class='even'>";
						$odd_even=0;
					}
					if ($flag) print "<td>X</td>"; else print "<td>&nbsp;</td>";
					foreach($row as $col) {
						if(!$col) $col="&nbsp;";
						print pmb_bidi("<td>$col</td>");
					}
					print "<td>&nbsp;</td>" ;
					$p_perso=new parametres_perso("empr");
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields($id_notice);
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							print "<td>".$p["AFF"]."</td>" ;
						}
					}
					echo "</tr>";
				}
				break;
			} // fin switch
		} // fin if nbr_lignes
	} // fin fonction extrait_info

	
