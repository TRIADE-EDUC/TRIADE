<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edition_func.inc.php,v 1.64 2024/03/22 15:31:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $codes_auteurs;

require_once("$class_path/thesaurus.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once("$class_path/notice_tpl_gen.class.php");
require_once($class_path."/editions_datasource.class.php");

$codes_auteurs = array();

// Affichage tabulaire du contenu d'un caddie
function afftab_cart_objects ($idcaddie=0, $flag="" , $no_flag = "",$notice_tpl=0) {
	global $msg,$charset;
	global $worksheet ;
	global $myCart ;
	global $dest ;
	global $entete_bloc;
	global $max_aut ;
	global $max_perso;
	global $res_compte3 ;

	global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermés

	$contents = '';
	
	if (($flag=="") && ($no_flag=="")) {
		$no_flag = 1;
		$flag = 1;
	}

	$caddie_type = $myCart->type ;
        	
	// Afin de trier les éditions :
	switch ($caddie_type) {
		case 'NOTI' :
			$fromc = " left join notices on object_id=notice_id " ;
			$orderc = ", niveau_hierar desc " ;
			break;
		case 'EXPL' :
			$fromc = " left join exemplaires on object_id=expl_id " ;
			$orderc = ", expl_notice desc, expl_bulletin " ;
			break;
		case 'BULL' :
			$fromc = " left join bulletins on object_id=bulletin_id " ;
			$orderc = ", date_date " ;
			break;
	}	

	$requete = "SELECT caddie_content.* FROM caddie_content $fromc where caddie_id='".$idcaddie."' ";
	if ($flag && $no_flag ) $complement_clause = "";
	if (!$flag && $no_flag ) $complement_clause = " and (flag is null or flag='') ";
	if ($flag && !$no_flag ) $complement_clause = " and (flag is not null and flag!='') ";
	if (!$flag && !$no_flag ) return ;
	$requete .= $complement_clause." order by blob_type, content $orderc, object_id";

	$liste=array();
	$result = pmb_mysql_query($requete) or die($requete."<br />".pmb_mysql_error());
	if($dest=="EXPORT_NOTI"){
		$noti_tpl=new notice_tpl_gen($notice_tpl);		
	}
	if(pmb_mysql_num_rows($result)) {
		$flag_notice_id = array();
		while ($temp = pmb_mysql_fetch_object($result)) {		
			if($dest=="EXPORT_NOTI"){
				if ($caddie_type=="EXPL"){
					$rqt_test = "select expl_notice as id from exemplaires where expl_id='".$temp->object_id."' ";
					$res_notice = pmb_mysql_query($rqt_test);
					$obj_notice = pmb_mysql_fetch_object($res_notice) ;
					if (!$obj_notice->id) {
						$rqt_test = "select num_notice as id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_id='".$temp->object_id."' ";
						$res_notice = pmb_mysql_query($rqt_test);
						$obj_notice = pmb_mysql_fetch_object($res_notice) ;
					}
					if((!isset($flag_notice_id[$obj_notice->id]) || !$flag_notice_id[$obj_notice->id]) && $obj_notice->id){
						$flag_notice_id[$obj_notice->id]=1;
						$contents.=$noti_tpl->build_notice($obj_notice->id);
					}		
				} elseif ($caddie_type=="NOTI") $contents.=$noti_tpl->build_notice($temp->object_id);	
				if ($caddie_type=="BULL"){
					$rqt_test = $rqt_tout = "select num_notice as id from bulletins where bulletin_id = '".$temp->object_id."' ";			
					$res_notice = pmb_mysql_query($rqt_test);
					$obj_notice = pmb_mysql_fetch_object($res_notice);
					if((!isset($flag_notice_id[$obj_notice->id]) || !$flag_notice_id[$obj_notice->id]) && $obj_notice->id){
						$flag_notice_id[$obj_notice->id]=1;
						$contents.=$noti_tpl->build_notice($obj_notice->id);
					}		
				}
			} else 
				$liste[] = array('object_id' => $temp->object_id, 'content' => $temp->content, 'blob_type' => $temp->blob_type, 'flag' => $temp->flag ) ;
		}
	} else return;


	switch($dest) {
		case "TABLEAU":
			break;
		case "EXPORT_NOTI":
			return 
			"<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head>
				<body>".$contents."</body></html>";
			break;	
		case "TABLEAUHTML":
		default:
			echo pmb_bidi("<h1>".$msg['panier_num']." $idcaddie / ".$myCart->name."</h1>");
			echo pmb_bidi($myCart->comment."<br />");
			
			break;
	}
	
	// en fonction du type de caddie on affiche ce qu'il faut
	if ($caddie_type=="NOTI") {
		// calcul du nombre max de colonnes pour les auteurs
		pmb_mysql_query("drop table if exists tmp_compte1");
		$rqt_compte1 = "create temporary table tmp_compte1 ENGINE=memory as select count(*) as comptage from caddie_content join notices on object_id=notice_id left join responsability on responsability_notice=notice_id where caddie_id=$idcaddie group by notice_id" ;
		pmb_mysql_query($rqt_compte1) ; 
		$rqt_compte2 = "select max(comptage) as max_aut from tmp_compte1 " ;
		$res_compte2 = pmb_mysql_query($rqt_compte2) ; 
		$compte2 = pmb_mysql_fetch_object($res_compte2) ;
		$max_aut = $compte2->max_aut ;
		
		// calcul du nombre max de colonnes pour les champs perso
		$rqt_compte3 = "select idchamp, titre from notices_custom order by ordre " ;
		$res_compte3 = pmb_mysql_query($rqt_compte3) ; 
		$max_perso = pmb_mysql_num_rows($res_compte3) ;
			
		// boucle de parcours des notices trouvées
		// inclusion du javascript de gestion des listes dépliables
		// début de liste
		$entete_bloc_prec="";
		foreach ($liste as $cle => $object) {
			if ($object['content']=="") {
				//On regarde le type de notice
				$requete="select niveau_biblio, niveau_hierar FROM notices WHERE notice_id='".$object['object_id']."' ";
				$mon_res=pmb_mysql_query($requete);
				$sel=" ,'' as Periodique, '' as ISSN, '' as bulletin_numero, '' as mention_date, '' as date_date, '' as bulletin_titre, '' as bulletin_cb";
				$tabl="";
				if(pmb_mysql_result($mon_res,0,0) == "a" && pmb_mysql_result($mon_res,0,1) == "2"){
					$sel=" ,n2.tit1 as 'Periodique', n2.code as ISSN, b.bulletin_numero, b.mention_date, b.date_date, b.bulletin_titre, b.bulletin_cb ";
					$tabl=" JOIN analysis ON n1.notice_id=analysis_notice JOIN bulletins b ON analysis_bulletin=b.bulletin_id JOIN notices n2 ON n2.notice_id=bulletin_notice ";
				} elseif(pmb_mysql_result($mon_res,0,0) == "b" && pmb_mysql_result($mon_res,0,1) == "2"){
					$sel=" ,n2.tit1, n2.code as ISSN, b.bulletin_numero, b.mention_date, b.date_date, b.bulletin_titre, b.bulletin_cb ";
					$tabl=" JOIN bulletins b ON n1.notice_id=b.num_notice JOIN notices n2 ON n2.notice_id=bulletin_notice ";
				}
				$rqt_tout = "SELECT n1.notice_id, n1.typdoc, n1.tit1, n1.tit2, n1.tit3, n1.tit4, serie_name, n1.tnvol, p1.ed_name, p1.ed_ville, collection_name, sub_coll_name, n1.year, n1.nocoll, n1.mention_edition, p2.ed_name as '2nd editeur', p2.ed_ville as 'ville 2nd editeur', n1.code as ISBN, n1.npages, n1.ill, n1.size, n1.accomp, n1.n_gen, n1.n_contenu, n1.n_resume, n1.lien, n1.eformat, n1.index_l, indexint_name, n1.niveau_biblio, n1.niveau_hierar, n1.prix, n1.statut, n1.commentaire_gestion, n1.thumbnail_url, n1.create_date, n1.update_date ".$sel." FROM notices n1";
				$rqt_tout.= " left join series on serie_id=n1.tparent_id ";
				$rqt_tout.= " left join publishers p1 on p1.ed_id=n1.ed1_id ";
				$rqt_tout.= " left join publishers p2 on p2.ed_id=n1.ed2_id ";
				$rqt_tout.= " left join collections on n1.coll_id=collection_id ";
				$rqt_tout.= " left join sub_collections on n1.subcoll_id=sub_coll_id ";
				$rqt_tout.= " left join indexint on n1.indexint=indexint_id ";
				$rqt_tout.=$tabl;
				$rqt_tout.= " WHERE n1.notice_id='".$object['object_id']."' ";
				//echo "requete :".$rqt_tout."\n";
				$entete_bloc="MONO";
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_info_notice($rqt_tout, 1, $object['flag']);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_info_notice($rqt_tout, 0, $object['flag']);
			} else {
				$entete_bloc="BLOB";
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_blob($object['blob_type']." ".$object['content'],1, $object['flag']);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_blob($object['blob_type']." ".$object['content'],0, $object['flag']);;
			}
		} // fin de liste
	} // fin si NOTI
	// si EXPL
	if ($caddie_type=="EXPL") {
		// calcul du nombre max de colonnes pour les auteurs
		//auteurs associés aux exemplaires de notices
		pmb_mysql_query("drop table if exists tmp_compte1");
		$rqt_compte1 = "create temporary table tmp_compte1 ENGINE=memory as select count(*) as comptage from caddie_content join exemplaires on expl_id = object_id join notices on notice_id=expl_notice and expl_notice <> 0 left join responsability on responsability_notice=notice_id where caddie_id=$idcaddie group by notice_id" ;
		pmb_mysql_query($rqt_compte1) ; 
		$rqt_compte2 = "select max(comptage) as max_aut from tmp_compte1 " ;
		$res_compte2 = pmb_mysql_query($rqt_compte2) ; 
		$compte2 = pmb_mysql_fetch_object($res_compte2) ;
		$max_aut = $compte2->max_aut ;
		
		//auteurs associés aux périodiques des exemplaires de bulletins
		pmb_mysql_query("drop table if exists tmp_compte1");
		$rqt_compte1 = "create temporary table tmp_compte1 ENGINE=memory as select count(*) as comptage from caddie_content join exemplaires on expl_id = object_id join bulletins on bulletin_id=expl_bulletin and expl_bulletin <> 0 join notices on notices.notice_id = bulletins.bulletin_notice left join responsability on responsability_notice=notice_id where caddie_id=$idcaddie group by notice_id" ;
		pmb_mysql_query($rqt_compte1) ;
		$rqt_compte2 = "select max(comptage) as max_aut from tmp_compte1 " ;
		$res_compte2 = pmb_mysql_query($rqt_compte2) ;
		$compte2 = pmb_mysql_fetch_object($res_compte2) ;
		if($compte2->max_aut > $max_aut) {
			$max_aut = $compte2->max_aut ;
		}
		
		// calcul du nombre max de colonnes pour les champs perso
		$rqt_compte3 = "select idchamp, titre from expl_custom order by ordre " ;
		$res_compte3 = pmb_mysql_query($rqt_compte3) ; 
		$max_perso = pmb_mysql_num_rows($res_compte3) ;
		
		// boucle de parcours des exemplaires trouvés
		$entete_bloc_prec="";
		foreach ($liste as $cle => $expl) {
			if (!$expl["content"]) {
				$rqt_test = "select expl_bulletin from exemplaires where expl_id='".$expl['object_id']."' ";
				$result_test = pmb_mysql_query($rqt_test);
				$obj_test = pmb_mysql_fetch_object($result_test) ;
				if ($obj_test->expl_bulletin==0) {
					// expl de mono
					$rqt_tout  = "SELECT e.*, t.*, s.*, st.*, l.location_libelle, stat.*, n.notice_id, n.typdoc, n.tit1, n.tit2, n.tit3, n.tit4, serie_name, n.tnvol, p1.ed_name, p1.ed_ville, collection_name, sub_coll_name, n.year, n.nocoll, n.mention_edition, p2.ed_name as '2nd editeur', p2.ed_ville as 'ville 2nd editeur', n.code as ISBN, n.npages, n.ill, n.size, n.accomp, n.n_gen, n.n_contenu, n.n_resume, n.lien, n.eformat, n.index_l, indexint_name, n.niveau_biblio, n.niveau_hierar, n.prix, n.statut, n.commentaire_gestion, n.thumbnail_url, n.create_date, n.update_date";
					$rqt_tout .= " FROM exemplaires e";
					$rqt_tout .= ", docs_type t";
					$rqt_tout .= ", docs_section s";	
					$rqt_tout .= ", docs_statut st";	
					$rqt_tout .= ", docs_location l";	
					$rqt_tout .= ", docs_codestat stat";
					$rqt_tout .= ", notices n left join series on serie_id=n.tparent_id 
		left join publishers p1 on p1.ed_id=n.ed1_id 
		left join publishers p2 on p2.ed_id=n.ed2_id 
		left join collections on n.coll_id=collection_id 
		left join sub_collections on n.subcoll_id=sub_coll_id 
		left join indexint on n.indexint=indexint_id";
					$rqt_tout .= " WHERE e.expl_id='".$expl['object_id']."'";
					$rqt_tout .= " AND e.expl_typdoc=t.idtyp_doc";
					$rqt_tout .= " AND e.expl_section=s.idsection";
					$rqt_tout .= " AND e.expl_statut=st.idstatut";
					$rqt_tout .= " AND e.expl_location=l.idlocation";
					$rqt_tout .= " AND e.expl_codestat=stat.idcode";
					$rqt_tout .= " AND e.expl_notice=n.notice_id";
					$entete_bloc="EXPLMONO" ;
				} else {
					// expl de bulletin
					$rqt_tout  = "SELECT e.*, t.*, s.*, st.*, l.location_libelle, stat.*, n.notice_id, n.typdoc, n.tit1, n.tit2, n.tit3, n.tit4, serie_name, n.tnvol, p1.ed_name, p1.ed_ville, collection_name, sub_coll_name, n.year, n.nocoll, n.mention_edition, p2.ed_name as '2nd editeur', p2.ed_ville as 'ville 2nd editeur', n.code as ISBN, n.npages, n.ill, n.size, n.accomp, n.n_gen, n.n_contenu, n.n_resume, n.lien, n.eformat, n.index_l, indexint_name, n.niveau_biblio, n.niveau_hierar, n.prix, n.statut, n.commentaire_gestion, n.thumbnail_url, n.create_date, n.update_date, b.*";
					$rqt_tout .= " FROM exemplaires e";
					$rqt_tout .= ", docs_type t";
					$rqt_tout .= ", docs_section s";	
					$rqt_tout .= ", docs_statut st";	
					$rqt_tout .= ", docs_location l";	
					$rqt_tout .= ", docs_codestat stat";
					$rqt_tout .= ", notices n left join series on serie_id=n.tparent_id 
		left join publishers p1 on p1.ed_id=n.ed1_id 
		left join publishers p2 on p2.ed_id=n.ed2_id 
		left join collections on n.coll_id=collection_id 
		left join sub_collections on n.subcoll_id=sub_coll_id 
		left join indexint on n.indexint=indexint_id";
					$rqt_tout .= ", bulletins b";
					$rqt_tout .= " WHERE e.expl_id='".$expl['object_id']."'";
					$rqt_tout .= " AND e.expl_typdoc=t.idtyp_doc";
					$rqt_tout .= " AND e.expl_section=s.idsection";
					$rqt_tout .= " AND e.expl_statut=st.idstatut";
					$rqt_tout .= " AND e.expl_location=l.idlocation";
					$rqt_tout .= " AND e.expl_codestat=stat.idcode";
					$rqt_tout .= " AND e.expl_bulletin=b.bulletin_id";
					$rqt_tout .= " AND n.notice_id=b.bulletin_notice";
					$entete_bloc="EXPLBULL";
				}
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_info_notice($rqt_tout, 1, $expl["flag"]);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_info_notice($rqt_tout, 0, $expl["flag"]);
			} else {
				$entete_bloc="BLOB";
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_blob($expl["blob_type"]." ".$expl["content"],1, $expl["flag"]);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_blob($expl["blob_type"]." ".$expl["content"],0, $expl["flag"]);
			}
		} // fin de liste
	} // fin si EXPL
	if ($caddie_type=="BULL") {			
		// boucle de parcours des bulletins trouvés
		// inclusion du javascript de gestion des listes dépliables
		// début de liste
		$entete_bloc_prec="";
		foreach ($liste as $cle => $expl) {
			if (!$expl["content"]) {
				$rqt_tout = "select * from bulletins where bulletin_id = '".$expl['object_id']."' ";
				$entete_bloc="BULL";
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_info($rqt_tout, 1, $expl["flag"]);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_info($rqt_tout, 0, $expl["flag"]);
			} else {
				$entete_bloc="BLOB";
				if ($entete_bloc!=$entete_bloc_prec) {
					extrait_blob($expl["blob_type"]." ".$expl["content"],1, $expl["flag"]);
					$entete_bloc_prec=$entete_bloc ;
				} else extrait_blob($expl["blob_type"]." ".$expl["content"],0, $expl["flag"]);
			}
		} // fin de liste
	} // fin si BULL
	return;
}



function extrait_info ($sql="", $entete=1, $flag="") {
	global $dest ;
	global $worksheet ;
	global $entete_bloc;
	global $msg;
	
	global $debligne_excel;
	global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermés
	
	global $max_aut ; // le nombre max de colonnes d'auteurs
	
	if (!$debligne_excel) $debligne_excel = 0 ;
	
	$res = pmb_mysql_query($sql);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$nbr_champs = pmb_mysql_num_fields($res);
             		
	if ($nbr_lignes) {
		$editions_datasource = array();
		if ($entete) {
			$editions_datasource['notices'] = new editions_datasource('notices');
		}
		switch($dest) {
			case "TABLEAU":
				if ($entete) {
					$worksheet->write_string((1+$debligne_excel),0,$msg["caddie_mess_edition_".$entete_bloc]);
					$debligne_excel++ ;
				}
				for($i=0; $i < $nbr_champs; $i++) {
					// entête de colonnes
					$fieldname = pmb_mysql_field_name($res, $i);
					if ($entete) {
						$worksheet->write_string((1+$debligne_excel),0,$msg['caddie_action_marque']);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							$worksheet->write_string((1+$debligne_excel),($i+1),$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']);
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							$worksheet->write_string((1+$debligne_excel),($i+1),$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']);
						} else {
							$worksheet->write_string((1+$debligne_excel),($i+1),$fieldname);
						}
					}
				}
				if ($entete) $debligne_excel++ ;
             		        		
				for($i=0; $i < $nbr_lignes; $i++) {
					$debligne_excel++;
					$row = pmb_mysql_fetch_row($res);
					if ($flag) $worksheet->write_string(($i+$debligne_excel),0,"X");
					$j=0;
					foreach($row as $col) {
						if(!$col) $col=" ";
						$worksheet->write_string(($i+$debligne_excel),($j+1),$col);
						$j++;
					}
				}
				break;
			case "TABLEAUHTML":
				if ($entete) {
					if ($etat_table) echo "\n</table>";
					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class='align_left'>".$msg['caddie_action_marque']."</th>";
					$etat_table = 1 ;
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']."</th>");
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
				}
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
					echo "<tr>";
					if ($flag) print "<td>X</td>"; else print "<td>&nbsp;</td>";
					foreach($row as $col) {
						if (is_numeric($col)){
 							$col = "'".$col ;
						}
						if(!$col) $col="&nbsp;";
						print pmb_bidi("<td>$col</td>");
					}
					echo "</tr>";
				}
				break;
			default:
				if ($entete) {
					if ($etat_table) echo "\n</table>";
					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class='align_left'>".$msg['caddie_action_marque']."</th>";
					$etat_table = 1 ;
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']."</th>");
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
				}
				$odd_even=0;
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
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
					echo "</tr>";
				}
				break;
		} // fin switch
	} // fin if nbr_lignes
} // fin fonction extrait_info

	
function extrait_info_notice ($sql="", $entete=1, $flag="") {
	global $dest ;
	global $worksheet ;
	global $myCart ;
	global $entete_bloc;
	global $msg, $charset;
	
	global $debligne_excel;
	global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermés
	
	global $max_aut ; // le nombre max de colonnes d'auteurs
	
	global $thesaurus_mode_pmb;
	global $thesaurus_defaut;
	global $lang;
	global $pmb_keyword_sep;
	
	global $max_perso;
	global $res_compte3 ;

	if (!$debligne_excel) $debligne_excel = 0 ;
	
	$res = pmb_mysql_query($sql);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$nbr_champs = pmb_mysql_num_fields($res);
	$nbr_languages = 2;
	
	if ($nbr_lignes) {
		$editions_datasource = array();
		// Pour les champs personnalisés
		$caddie_type = $myCart->type ;
		switch ($caddie_type) {
			case 'EXPL' :
				$libelle_caddie_type = "expl" ;
				if ($entete) {
					$editions_datasource['notices'] = new editions_datasource('notices');
					$editions_datasource['items'] = new editions_datasource('items');
				}
				break;
			case 'NOTI' :
			default :
				$libelle_caddie_type = "notices" ;
				if ($entete) {
					$editions_datasource['notices'] = new editions_datasource('notices');
				}
				break;
			
		}
		switch($dest) {
			case "TABLEAU":
				if ($entete) {
					$worksheet->write_string((1+$debligne_excel),0,$msg["caddie_mess_edition_".$entete_bloc]);
					$debligne_excel++ ;
					$worksheet->write_string((1+$debligne_excel),0,$msg['caddie_action_marque']);
					for($i=0; $i < $nbr_champs; $i++) {
						// entête de colonnes
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							$worksheet->write_string((1+$debligne_excel),($i+1),$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']);
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							$worksheet->write_string((1+$debligne_excel),($i+1),$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']);
						} else {
							$worksheet->write_string((1+$debligne_excel),($i+1),$fieldname);
						}
					}
					
					$worksheet->write_string((1+$debligne_excel),($nbr_champs+1),$msg['710']);
					$worksheet->write_string((1+$debligne_excel),($nbr_champs+2),$msg['711']);
					
					for($i=0; $i < $max_aut; $i++) {
						$worksheet->write_string((1+$debligne_excel),($i*6+1+$nbr_champs+$nbr_languages),"aut_entree_$i");
						$worksheet->write_string((1+$debligne_excel),($i*6+2+$nbr_champs+$nbr_languages),"aut_rejete_$i");
						$worksheet->write_string((1+$debligne_excel),($i*6+3+$nbr_champs+$nbr_languages),"aut_dates_$i");
						$worksheet->write_string((1+$debligne_excel),($i*6+4+$nbr_champs+$nbr_languages),"aut_fonction_$i");
						$worksheet->write_string((1+$debligne_excel),($i*6+5+$nbr_champs+$nbr_languages),"aut_type_$i");
						$worksheet->write_string((1+$debligne_excel),($i*6+6+$nbr_champs+$nbr_languages),"aut_resp_type_$i");
					}
					$worksheet->write_string((1+$debligne_excel),($max_aut*6+$nbr_champs+$nbr_languages+1),"DESCR");
					reset($res_compte3);
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte3);
						if(!empty($perso) && is_object($perso)) {
							$worksheet->write_string((1+$debligne_excel),($max_aut*6+$nbr_champs+$nbr_languages+2+$i),$perso->titre);
						}
					}
					$debligne_excel++;
				}
				
				for($i=0; $i < $nbr_lignes; $i++) {
					$debligne_excel++;
					$row = pmb_mysql_fetch_row($res);
					switch ($caddie_type) {
						case 'EXPL' :
							$id_notice = $row[2] ;
							$id_expl = $row[0];
							break;
						case 'NOTI' :
						default :
							$id_notice = $row[0] ;
							$id_expl = 0;
							break;
						
					}
					if ($flag) $worksheet->write_string($debligne_excel,0,"X");
					$j=0;
					foreach($row as $col) {
						if(!$col) $col=" ";
						$worksheet->write_string($debligne_excel,($j+1),$col);
						$j++;
					}
					
					$worksheet->write_string($debligne_excel,($nbr_champs+1),get_languages_edition($id_notice));
					$worksheet->write_string($debligne_excel,($nbr_champs+2),get_languages_edition($id_notice, 1));
					
					$authors = get_authors_editions($id_notice);
					
					for($iaut=0; $iaut < $max_aut; $iaut++) {
						if (isset($authors[$iaut])) {
							$worksheet->write_string($debligne_excel,($iaut*6+1+$nbr_champs+$nbr_languages),$authors[$iaut]['name']);
							$worksheet->write_string($debligne_excel,($iaut*6+2+$nbr_champs+$nbr_languages),$authors[$iaut]['rejete']);
							$worksheet->write_string($debligne_excel,($iaut*6+3+$nbr_champs+$nbr_languages),$authors[$iaut]['date']);
							$worksheet->write_string($debligne_excel,($iaut*6+4+$nbr_champs+$nbr_languages),$authors[$iaut]['function']);
							$worksheet->write_string($debligne_excel,($iaut*6+5+$nbr_champs+$nbr_languages),$authors[$iaut]['lib_type_aut']);
							$worksheet->write_string($debligne_excel,($iaut*6+6+$nbr_champs+$nbr_languages),$authors[$iaut]['lib_resp_type']);
						}
					}
					
					$lib_desc = get_categs_edition($id_notice, $lang);
					$worksheet->write_string($debligne_excel,($max_aut*6+$nbr_champs+$nbr_languages+1),$lib_desc);

					$p_perso=new parametres_perso($libelle_caddie_type);
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields(($libelle_caddie_type=='notices'?$id_notice:$id_expl));
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							$worksheet->write_string($debligne_excel,($max_aut*6+$nbr_champs+$nbr_languages+2+$i),strip_tags(html_entity_decode($p["AFF"],ENT_QUOTES|ENT_COMPAT,$charset)));
						}
					}
				}
				break;
			case "TABLEAUHTML":
				if ($entete) {
					if ($etat_table) echo "\n</table>";
					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class'align_left'>".$msg['caddie_action_marque']."</th>";
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']."</th>");
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
					
					print "<th class='align_left'>".$msg['710']."</th>";
					print "<th class='align_left'>".$msg['711']."</th>";
					
					for($i=0; $i < $max_aut; $i++) {
						print pmb_bidi("<th class='align_left'>aut_entree_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_rejete_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_dates_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_fonction_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_type_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_resp_type_$i</th>") ;
					}
		
					print "<th class='align_left'>DESCR</th>" ;
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte3) ;
						print "<th class='align_left'>".$perso->titre."</th>" ;
					}
					$etat_table = 1 ;
				}
				
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
					switch ($caddie_type) {
						case 'EXPL' :
							$id_notice = $row[2] ;
							$id_expl = $row[0];
							break;
						case 'NOTI' :
						default :
							$id_notice = $row[0] ;
							$id_expl = 0;
							break;
						
					}
					echo "<tr>";
					if ($flag) print "<td>X</td>"; else print "<td>&nbsp;</td>";
					foreach($row as $col) {
						if (is_numeric($col)){
 							$col = "'".$col ;
						}
						if(!$col) $col="&nbsp;";
						print pmb_bidi("<td>$col</td>");
					}
					
					print "<td>".get_languages_edition($id_notice)."</td>";
					print "<td>".get_languages_edition($id_notice, 1)."</td>";
					
					$authors = get_authors_editions($id_notice);
						
					for($iaut=0; $iaut < $max_aut; $iaut++) {
						if (isset($authors[$iaut])) {
							print pmb_bidi("<td>".$authors[$iaut]['name']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['rejete']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['date']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['function']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['lib_type_aut']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['lib_resp_type']."</td>");
						} else {
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
						}
					}

					$lib_desc = get_categs_edition($id_notice, $lang);
					print pmb_bidi("<td>".$lib_desc."</td>" );
					
					$p_perso=new parametres_perso($libelle_caddie_type);
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields(($libelle_caddie_type=='notices'?$id_notice:$id_expl));
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							print "<td>".strip_tags($p["AFF"])."</td>" ;
						}
					}
					echo "</tr>";
				}
				break;
			default:
				if ($entete) {
					if ($etat_table) echo "\n</table>";
					echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
					echo "\n<table><th class='align_left'>".$msg['caddie_action_marque']."</th>";
					for($i=0; $i < $nbr_champs; $i++) {
						$fieldname = pmb_mysql_field_name($res, $i);
						if(isset($editions_datasource['notices']->struct_format['notices_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['notices']->struct_format['notices_'.$fieldname]['label']."</th>");
						} elseif(isset($editions_datasource['items']->struct_format['exemplaires_'.$fieldname])) {
							print("<th class='align_left'>".$editions_datasource['items']->struct_format['exemplaires_'.$fieldname]['label']."</th>");
						} else {
							print("<th class='align_left'>".$fieldname."</th>");
						}
					}
					
					print "<th class='align_left'>".$msg['710']."</th>";
					print "<th class='align_left'>".$msg['711']."</th>";
					
					for($i=0; $i < $max_aut; $i++) {
						print pmb_bidi("<th class='align_left'>aut_entree_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_rejete_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_dates_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_fonction_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_type_$i</th>") ;
						print pmb_bidi("<th class='align_left'>aut_resp_type_$i</th>") ;
					}
					print "<th class='align_left'>DESCR</th>" ;
					for($i=0; $i < $max_perso; $i++) {
						$perso = pmb_mysql_fetch_object($res_compte3) ;
						print "<th class='align_left'>".$perso->titre."</th>" ;
					}
					$etat_table = 1 ;
				}
					
				$odd_even=0;
				for($i=0; $i < $nbr_lignes; $i++) {
					$row = pmb_mysql_fetch_row($res);
					switch ($caddie_type) {
						case 'EXPL' :
							$id_notice = $row[2] ;
							$id_expl = $row[0];
							break;
						case 'NOTI' :
						default :
							$id_notice = $row[0] ;
							$id_expl = 0;
							break;
						
					}
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
					
					print "<td>".get_languages_edition($id_notice)."</td>";
					print "<td>".get_languages_edition($id_notice, 1)."</td>";
					
					$authors = get_authors_editions($id_notice);
					
					for($iaut=0; $iaut < $max_aut; $iaut++) {
						if (isset($authors[$iaut])) {
							print pmb_bidi("<td>".$authors[$iaut]['name']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['rejete']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['date']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['function']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['lib_type_aut']."</td>");
							print pmb_bidi("<td>".$authors[$iaut]['lib_resp_type']."</td>");
						} else {
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
							print pmb_bidi("<td></td>");
						}
					}

					$lib_desc = get_categs_edition($id_notice, $lang);
					print pmb_bidi("<td>".$lib_desc."</td>") ;
					
					$p_perso=new parametres_perso($libelle_caddie_type);
					//Champs personalisés
					if (!$p_perso->no_special_fields) {
						$perso_=$p_perso->show_fields(($libelle_caddie_type=='notices'?$id_notice:$id_expl));
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							print "<td>".strip_tags($p["AFF"])."</td>" ;
						}
					}
					echo "</tr>";
				}
				break;
			} // fin switch
		} // fin if nbr_lignes
	} // fin fonction extrait_info_notice
	
function extrait_blob ($blob="", $entete=1, $flag="") {
	global $dest ;
	global $worksheet ;
	global $entete_bloc;
	global $msg;
	
	global $debligne_excel;
	global $etat_table ; // permet de savoir si les tag table sont ouverts ou fermés
	
	if (!$debligne_excel) $debligne_excel = 0 ;
	
	switch($dest) {
		case "TABLEAU":
			if ($entete) {
				$worksheet->write_string((1+$debligne_excel),0,$msg["caddie_mess_edition_".$entete_bloc]);
				$debligne_excel++ ;
			}
			if ($flag) $worksheet->write_string((1+$debligne_excel),0,"X");
			$worksheet->write_string((1+$debligne_excel),1,$blob);
			$debligne_excel++ ;
			break;
		case "TABLEAUHTML":
		default:
			if ($etat_table) echo "\n</table>";
			if ($entete) echo "<h3>".$msg["caddie_mess_edition_".$entete_bloc]."</h3>";
			if ($flag) print "<strong>X</strong>&nbsp;"; else "<strong>&nbsp;</strong>&nbsp;";
			print pmb_bidi("$blob<br />");
			break;
	} // fin switch
} // fin fonction extrait_info

function get_functions_authors() {
	global $include_path;
	global $lang;
	global $codes_auteurs;
	
	if (!count($codes_auteurs)) {
		if (file_exists($include_path."/marc_tables/".$lang."/function.xml")) {
			$file_name=$include_path."/marc_tables/".$lang."/function.xml";
		} else if (file_exists($include_path."/marc_tables/fr_FR/function.xml")) {
			$file_name=$include_path."/marc_tables/fr_FR/function.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			$codes_auteurs=$xmllist->table;			
		}
	}
	
	return $codes_auteurs;
} // fin fonction get_functions_authors

function get_categs_edition($id_notice, $lang){
	global $thesaurus_mode_pmb, $thesaurus_defaut, $pmb_keyword_sep;
	global $default_tmp_storage_engine;
	
	$lib_desc = "";
	
	$q = "drop table if exists catlg ";
	pmb_mysql_query($q);
	$q = "CREATE TEMPORARY TABLE catlg ENGINE={$default_tmp_storage_engine} as ";
	$q.= "SELECT categories.num_noeud, categories.libelle_categorie ";
	$q.= "FROM noeuds, categories, notices_categories ";
	$q.= "WHERE notices_categories.notcateg_notice = '".$id_notice."' ";
	$q.= "AND categories.langue = '".$lang."' ";
	$q.= "AND categories.num_noeud = notices_categories.num_noeud " ;
	$q.= "AND categories.num_noeud = noeuds.id_noeud ";
	$q.= "ORDER BY ordre_categorie";
	pmb_mysql_query($q) ;
	
	$q = "DROP TABLE IF EXISTS catdef ";
	pmb_mysql_query($q);
	
	$q = "CREATE TEMPORARY TABLE catdef ( ";
	$q.= "num_noeud int(9) unsigned not null default '0', ";
	$q.= "num_thesaurus int(3) unsigned not null default '0', ";
	$q.= "libelle_categorie text not null ) ENGINE={$default_tmp_storage_engine} ";
	pmb_mysql_query($q);
		
	$thes_list = thesaurus::getThesaurusList();
	$q = '';
	foreach($thes_list as $id_thesaurus=>$libelle_thesaurus) {
		$thes = thesaurus::get_instance($id_thesaurus);
		$q = "INSERT INTO catdef ";
		$q.= "SELECT categories.num_noeud, noeuds.num_thesaurus, categories.libelle_categorie ";
		$q.= "FROM noeuds, categories, notices_categories ";
		$q.= "WHERE noeuds.num_thesaurus=$id_thesaurus and notices_categories.notcateg_notice = '".$id_notice."' ";
		$q.= "AND categories.langue = '".$thes->langue_defaut."' ";
		$q.= "AND categories.num_noeud = notices_categories.num_noeud " ;
		$q.= "AND categories.num_noeud = noeuds.id_noeud ";
		$q.= "ORDER BY ordre_categorie";
		pmb_mysql_query($q);
	}
	
	$q = "select catdef.num_thesaurus as num_thesaurus, ";
	$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as libelle_categorie ";
	$q.= "from catdef left join catlg on catdef.num_noeud = catlg.num_noeud ";
	if (!$thesaurus_mode_pmb)
		$q.= "where catdef.num_thesaurus = '".$thesaurus_defaut."' ";
	
	$res_desc = pmb_mysql_query($q);
	
	while ($desc = pmb_mysql_fetch_object($res_desc)) {
		$lib_desc.=($lib_desc?$pmb_keyword_sep:"");
		if ($thesaurus_mode_pmb) {
			$thesaurus =  thesaurus::get_instance($desc->num_thesaurus);
			$lib_desc .= '['.$thesaurus->getLibelle().'] ';
		}
		$lib_desc .= $desc->libelle_categorie ;
	}
	
	return $lib_desc;
}

function get_authors_editions($id_notice) {
	global $msg;
	
	$authors = array();
	
	$rqt_aut = "SELECT author_name, author_rejete, author_date, responsability_fonction, author_type, responsability_type ";
	$rqt_aut .= "FROM responsability JOIN authors ON responsability_author=author_id ";
	$rqt_aut .= "WHERE responsability_notice=$id_notice " ;
	$rqt_aut .= "ORDER BY responsability_type ASC, responsability_ordre ASC";
	$res_aut = pmb_mysql_query($rqt_aut);
	
	if ($res_aut && pmb_mysql_num_rows($res_aut)) {
		$codes_auteurs = get_functions_authors();
		while ($row = pmb_mysql_fetch_object($res_aut)) {
			$aut = array();
			$aut['name'] = $row->author_name;
			$aut['rejete'] = $row->author_rejete;
			$aut['date'] = $row->author_date;
			$aut['function'] = (isset($codes_auteurs[$row->responsability_fonction]) ? $codes_auteurs[$row->responsability_fonction] : '');
			$lib_type_aut = $row->author_type;
			if ($lib_type_aut == "70") {
				$lib_type_aut = $msg['203'];
			} elseif ($lib_type_aut == "71") {
				$lib_type_aut = $msg['204'];
			} elseif ($lib_type_aut == "72") {
				$lib_type_aut = $msg["congres_libelle"];
			}
			$aut['lib_type_aut'] = $lib_type_aut;
			$lib_resp_type = "";
			if ($row->author_name) {
				if ($row->responsability_type == 0) {
					$lib_resp_type = $msg["export_main_author"];
				} elseif ($row->responsability_type == 1) {
					$lib_resp_type = $msg["export_other_author"];
				} elseif ($row->responsability_type == 2) {
					$lib_resp_type = $msg["export_secondary_author"];
				}
			}
			$aut['lib_resp_type'] = $lib_resp_type;
			
			$authors[] = $aut;
		}
	}

	return $authors;
}

function get_languages_edition($id_notice, $type=0) {
	global $marc_liste_langues;
	global $pmb_keyword_sep;

	if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

	$id_notice = intval($id_notice);
	$type = intval($type);

	$languages = array();
	$query = "select code_langue from notices_langues where type_langue =".$type." and num_notice = ".$id_notice." order by ordre_langue";
	$result = pmb_mysql_query($query);
	while($row = pmb_mysql_fetch_object($result)) {
		$languages[] = $marc_liste_langues->table[$row->code_langue]." (".$row->code_langue.")";
	}
	return implode($pmb_keyword_sep, $languages);
}