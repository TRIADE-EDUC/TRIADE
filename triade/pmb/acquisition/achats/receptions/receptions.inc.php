<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions.inc.php,v 1.26 2023/05/04 10:35:19 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $base_path, $include_path, $msg, $charset, $action, $chk, $id_cde;

// gestion des receptions
require_once("$class_path/entites.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/lignes_actes_statuts.class.php");
require_once("$class_path/exercices.class.php");
require_once("$class_path/emprunteur.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$class_path/receptions.class.php");
require_once("$class_path/receptions_relances.class.php");
require_once("$base_path/admin/users/users_func.inc.php");
require_once("$include_path/templates/receptions.tpl.php");
require_once("$include_path/misc.inc.php");
require_once("$include_path/user_error.inc.php");
require_once("$include_path/mail.inc.php");
require_once("$class_path/z3950_notice.class.php");
require_once($class_path."/notice_doublon.class.php");

//Affiche la liste des receptions pour une bibliotheque
function show_list_recept() {
	global $msg, $charset,$tab_bib;
	global $recept_search_form,$recept_list_form,$recept_search_form_suite, $recept_hrow_form,$recept_row_form,$sel_fou_form,$sel_dem_form,$sel_rub_form,$sel_date_form;
	global $bt_app,$bt_rel,$bt_chk, $link_not, $link_bull, $link_art, $link_sug, $bt_cat;
	global $user_userid;
	global $lgstat_filter;
	global $id_bibli,$id_exer;
	global $f_fou_code,$f_dem_code,$t_dem,$f_rub_code;
	global $cde_query,$all_query,$recept_query;
	global $chk_dev;
	global $lgstat_all,$comment_lg_all,$comment_lo_all;
	global $page,$nb_per_page,$nbr_lignes;
	global $date_inf, $date_sup;

	//verifications
	if(!$id_bibli) {
		$id_bibli=entites::getSessionBibliID();
	}
	if(!$id_bibli) {
		$id_bibli=$tab_bib[0][0];
	}
	entites::setSessionBibliId($id_bibli);
	$id_exer=exercices::getSessionExerciceId($id_bibli,$id_exer);
	
	//Affichage form de recherche
	$titre = htmlentities($msg['recherche'].' : '.$msg['acquisition_recept'], ENT_QUOTES, $charset);
	$recept_form=$recept_search_form;
	$recept_form = str_replace('!!form_title!!', $titre, $recept_form);

	$serialized_search = rawurlencode (serialize (array(	'id_bibli'	=>	$id_bibli,
									'id_exer'	=>	$id_exer,
									'f_fou_code'=> 	$f_fou_code,
									'f_dem_code'=>	$f_dem_code,
									't_dem'		=>	$t_dem,
									'f_rub_code'=>	$f_rub_code,
									'cde_query'	=>	stripslashes($cde_query ?? ""),
									'all_query'	=>	stripslashes($all_query ?? ""),
									'chk_dev'	=>	$chk_dev,
									'lgstat_filter'=>	$lgstat_filter
				)));
	$recept_form = str_replace('!!serialized_search!!', $serialized_search, $recept_form);				
	
	//Affichage selecteur etablissement
	$sel_bibli=entites::getBibliHtmlSelect(SESSuserid, $id_bibli, FALSE, array('class'=>'saisie-50em','id'=>'id_bibli','name'=>'id_bibli','onChange'=>'submit();'));
	$recept_form=str_replace('<!-- sel_bibli -->', $sel_bibli, $recept_form);
	
	//Affichage selecteur exercice 
	$sel_exer= exercices::getHtmlSelect($id_bibli,$id_exer,FALSE,array('id'=>'id_exer','name'=>'id_exer','onChange'=>'submit();'));
	$recept_form=str_replace('<!-- sel_exer -->', $sel_exer, $recept_form);
	
	//Affichage fournisseurs
	$i=0;
	$tab_fou2=array();
	if (is_array($f_fou_code) && count($f_fou_code)) {
		$tab_fou=entites::getRaisonSociale($f_fou_code,$id_bibli);
		foreach($f_fou_code as $v) {
			if($v && $tab_fou[$v]) {
				$tab_fou2[$v]=$tab_fou[$v];
				if($i>0) {
					$recept_form=str_replace('<!-- sel_fou -->',$sel_fou_form.'<!-- sel_fou -->',$recept_form);
					$recept_form=str_replace('!!i!!',$i,$recept_form);
				}
				$recept_form=str_replace('!!f_fou_code!!',$v,$recept_form);
				$recept_form=str_replace('!!f_fou!!',htmlentities($tab_fou[$v],ENT_QUOTES,$charset),$recept_form);
				$i++;
			}
		}
		$recept_form=str_replace('!!max_fou!!',(($i>0)?$i:'1'),$recept_form);
	}
	if(!$i) {
		$recept_form=str_replace('!!f_fou_code!!','0',$recept_form);
		$recept_form=str_replace('!!f_fou!!','',$recept_form);
		$recept_form=str_replace('!!max_fou!!','1',$recept_form);
	}
	
	//Affichage demandeurs
	$i=0;
	$tab_empr = array();
	$tab_user = array();
	if (is_array($f_dem_code) && count($f_dem_code) && is_array($t_dem) && count($t_dem)) {
		
		foreach($f_dem_code as $k=>$v) {
			if ($t_dem[$k]) {
				$tab_empr[]=$v;
			}else{
				$tab_user[]=$v;
			}
		}
		$tab_empr=emprunteur::getName($tab_empr);
		$tab_user=getUserName($tab_user);
		
		foreach($f_dem_code as $k=>$v) {
			if($v && ( (($t_dem[$k]==='0') && $tab_user[$v]) || (($t_dem[$k]==='1') && $tab_empr[$v]) ) ) {
				if($i>0) {
					$recept_form=str_replace('<!-- sel_dem -->',$sel_dem_form.'<!-- sel_dem -->',$recept_form);
					$recept_form=str_replace('!!i!!',$i,$recept_form);
				}
				$recept_form=str_replace('!!f_dem_code!!',$v,$recept_form);
				$recept_form=str_replace('!!t_dem!!',$t_dem[$k],$recept_form);
				if ($t_dem[$k]) {
					$recept_form=str_replace('!!f_dem!!',htmlentities($tab_empr[$v],ENT_QUOTES,$charset),$recept_form);
				} else {
					$recept_form=str_replace('!!f_dem!!',htmlentities($tab_user[$v],ENT_QUOTES,$charset),$recept_form);
				}
				$i++;
			}
		}
		$recept_form=str_replace('!!max_dem!!',(($i>0)?$i:'1'),$recept_form);
	}
	if (!$i) {
		$recept_form=str_replace('!!f_dem_code!!','0',$recept_form);
		$recept_form=str_replace('!!t_dem!!','0',$recept_form);
		$recept_form=str_replace('!!f_dem!!','',$recept_form);
		$recept_form=str_replace('!!max_dem!!','1',$recept_form);
	}
	
	//Affichage rubriques budgetaires
	$i=0;
	$tab_rub2=array();
	if (is_array($f_rub_code) && count($f_rub_code)) {
		$tab_rub=rubriques::getLibelle($f_rub_code,$id_bibli,$id_exer,SESSuserid);
		foreach($f_rub_code as $v) {
			if($v && $tab_rub[$v]) {
				$tab_rub2[$v]=$tab_rub[$v];
				if($i>0) {
					$recept_form=str_replace('<!-- sel_rub -->',$sel_rub_form.'<!-- sel_rub -->',$recept_form);
					$recept_form=str_replace('!!i!!',$i,$recept_form);
				}
				$recept_form=str_replace('!!f_rub_code!!',$v,$recept_form);
				$recept_form=str_replace('!!f_rub!!',htmlentities($tab_rub[$v],ENT_QUOTES,$charset),$recept_form);
				$i++;
			}
		}
		$recept_form=str_replace('!!max_rub!!',(($i>0)?$i:'1'),$recept_form);
	}
	if(!$i) {
		$recept_form=str_replace('!!f_rub_code!!','0',$recept_form);
		$recept_form=str_replace('!!f_rub!!','',$recept_form);
		$recept_form=str_replace('!!max_rub!!','1',$recept_form);
	}
	
	//Affichage zone commande
	if (!isset($chk_dev)) $chk_dev=TYP_ACT_CDE;
	if ($chk_dev) {
		$recept_form=str_replace('!!dev_checked!!',"checked='checked'",$recept_form);
		$recept_form=str_replace('!!cde_checked!!','',$recept_form);
	} else {
		$recept_form=str_replace('!!dev_checked!!','',$recept_form);
		$recept_form=str_replace('!!cde_checked!!',"checked='checked'",$recept_form);
	}

	$recept_form=str_replace('!!cde_query!!',htmlentities(stripslashes($cde_query ?? ""),ENT_QUOTES,$charset),$recept_form);

	//Affichage selecteur dates
	$sel_date_form[0] = str_replace('!!msg!!', htmlentities($msg['acquisition_recept_date'],ENT_QUOTES,$charset), $sel_date_form[0]);
	if($date_inf) {
		$date_inf_lib = formatdate($date_inf);
	} else {
		$date_inf_lib=$msg['parperso_nodate'];
	}
	$sel_date_form[1] = str_replace('!!date_inf!!',$date_inf ?? "",$sel_date_form[1]);
	$sel_date_form[1] = str_replace('!!date_inf_lib!!',$date_inf_lib ?? "",$sel_date_form[1]);
	if($date_sup) {
		$date_sup_lib = formatdate($date_sup);
	} else {
		$date_sup_lib=$msg['parperso_nodate'];
	}
	$sel_date_form[2] = str_replace('!!date_sup!!',$date_sup ?? "",$sel_date_form[2]);
	$sel_date_form[2] = str_replace('!!date_sup_lib!!',$date_sup_lib ?? "",$sel_date_form[2]);
	$sel_date_form[0] = sprintf($sel_date_form[0], $sel_date_form[1] ?? "",$sel_date_form[2]);
	$recept_form = str_replace('<!-- sel_date -->', $sel_date_form[0] ?? "", $recept_form); 
	
	//Creation selecteur statut de lignes de commandes
	if (!(is_array($lgstat_filter) && count($lgstat_filter))) {
		$lgstat_filter=array();
	}
	$sel_lgstat=lgstat::getHtmlSelect($lgstat_filter, FALSE, array('id'=>'lgstat_filter[]', 'name'=>'lgstat_filter[]','multiple'=>'multiple','size'=>'5'));
	$recept_form=str_replace('<!-- sel_lgstat -->', $sel_lgstat, $recept_form);
	
	//Affichage zone tous les champs 
	$recept_form=str_replace('!!all_query!!',htmlentities(stripslashes($all_query ?? ""),ENT_QUOTES,$charset),$recept_form);
	
	//Prise en compte du formulaire de recherche
	// nombre de références par pages
	if (!$nb_per_page) $nb_per_page = 10;
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;
	
	//La recherche ici
	$recept = new receptions($id_bibli,$id_exer);
	//filtre
	$filtres = $recept->setFiltres(array_keys($tab_fou2), array_keys($tab_empr), array_keys($tab_user), array_keys($tab_rub2), $chk_dev, $cde_query, $lgstat_filter, $date_inf, $date_sup);
	// comptage
	if(!$nbr_lignes) {
		$nbr_lignes=$recept->calcNbLignes($all_query);
		$err=$recept->getError();				
	}

	// liste
	if($nbr_lignes) {
		$t_list = $recept->getLignes();
		
		//Affichage des lignes
		$recept_form.= $recept_list_form;
		
		//Affichage zone de reception
		$recept_form=str_replace('!!recept_query!!',htmlentities(stripslashes($recept_query ?? ""),ENT_QUOTES,$charset),$recept_form);
		
		$tab_aff=array();
		$lgstat_form=lgstat::getHtmlSelect(array(0=>0), FALSE, array('id'=>'sel_lgstat_!!id_lig!!', 'onchange'=>'recept_upd_lgstat(this.getAttribute("id"));'));
		$act_form='';
		$i=1;
		foreach($t_list as $id_acte=>$t_row) {
			//Affichage lignes à recevoir
			foreach ($t_row as $id_ligne=>$row) { 
				if(!in_array($id_acte,$tab_aff)) {
					$tab_aff[] = $id_acte;
					$recept_form=str_replace('<!-- actes -->',$act_form.'<!-- actes -->',$recept_form);
					if(!isset($row['type_acte'])) $row['type_acte'] = '';
					$act_form=str_replace('!!lib_acte!!',
						htmlentities($msg['acquisition_recept_fou'], ENT_QUOTES, $charset)
						."&nbsp;<a href=\"./acquisition.php?categ=ach&sub=fourn&action=modif&id_bibli=".$id_bibli.'&id='.$row['num_fournisseur']."\">".htmlentities($row['raison_sociale'],ENT_QUOTES,$charset)."</a>"
						.'&nbsp;'.(htmlentities((($row['type_acte'])?($msg['acquisition_act_num_dev']):($msg['acquisition_act_num_cde'])),ENT_QUOTES,$charset)
						."<a href=\"./acquisition.php?categ=ach&sub=".(($row['type_acte'])?'devi':'cmde')."&action=modif&id_bibli=".$id_bibli.(($row['type_acte'])?'&id_dev=':'&id_cde=').$id_acte."\">".htmlentities($row['numero'],ENT_QUOTES,$charset)."</a>")
						.'&nbsp;'.(htmlentities($msg['653'],ENT_QUOTES, $charset)).'&nbsp;'. formatdate($row['date_acte']),
						$recept_hrow_form);
				}
				$row_form=$recept_row_form;
				$row_form=str_replace('!!code!!',htmlentities($row['code'],ENT_QUOTES,$charset),$row_form);
				$row_form=str_replace('!!lib!!',nl2br(htmlentities($row['libelle'],ENT_QUOTES,$charset)),$row_form);
				$row_form=str_replace('!!qte_cde!!',$row['nb_cde'],$row_form);
				$row_form=str_replace('!!qte_liv!!',$row['nb_liv'],$row_form);
				$row_form=str_replace('!!qte_sol!!',$row['nb_sol'],$row_form);		
				$lgstat_row_form=str_replace("value='".$row['statut']."'","value='".$row['statut']."' selected='selected' ",$lgstat_form);
				$row_form=str_replace('!!lgstat!!',$lgstat_row_form,$row_form);
				$row_form=str_replace('!!comment_lg!!',nl2br(htmlentities($row['commentaires_gestion'],ENT_QUOTES,$charset)),$row_form);
				$row_form=str_replace('!!comment_lo!!',nl2br(htmlentities($row['commentaires_opac'],ENT_QUOTES,$charset)),$row_form);
				$row_form=str_replace('!!date_last_relance!!',lignes_actes::getLastDayRelance($id_ligne),$row_form);
				$row_form=str_replace('!!id_lig!!',$id_ligne,$row_form);
				$row_form=str_replace('!!typ_lig!!',$row['type_ligne'],$row_form);
				if ($row['num_produit']) {
					switch ($row['type_ligne']) {
						case '1' : //notice
							$row_form=str_replace('<!-- link_cat -->',$link_not,$row_form);
							break;
						case '2' : //bulletin
							$row_form=str_replace('<!-- link_cat -->',$link_bull,$row_form);
							break;
						case '5': //article
							$id_bull = analysis::getBulletinIdFromAnalysisId($row['num_produit']);
							if ($id_bull) {
								$row_form=str_replace('<!-- link_cat -->',$link_art,$row_form);
								$row_form =str_replace('!!id_bull!!',$id_bull,$row_form);
							}
							break;
						default :
							break;	
					}
				} else {
					$tmp_bt_cat = str_replace('!!id_lig!!',$id_ligne,$bt_cat);
					$row_form = str_replace('<!-- bt_cat -->', $tmp_bt_cat,$row_form);
				}
				if ($row['num_acquisition']) {
					$row_form=str_replace('<!-- link_sug -->',$link_sug,$row_form);
					$row_form =str_replace('!!id_sug!!',$row['num_acquisition'],$row_form);
				}
				$row_form=str_replace('!!id_prod!!',$row['num_produit'],$row_form);
				$row_form=str_replace('!!no!!',$i,$row_form);
				
				$tab_rel = array();
				$tab_rel = lignes_actes::getRelances($id_ligne);
				
				$row_form = str_replace('!!nb_relances!!',htmlentities(sprintf($msg['acquisition_recept_hist'],count($tab_rel)),ENT_QUOTES,$charset),$row_form);  
				if (count($tab_rel)) {
					$row_form = str_replace('<!-- relances -->',implode('&nbsp;-&nbsp; ',$tab_rel) , $row_form);
				}
				
				$act_form=str_replace('<!-- lignes -->',$row_form.'<!-- lignes -->',$act_form);
				$i++;
			}
		}	
		$recept_form=str_replace('<!-- actes -->',$act_form.'<!-- actes -->',$recept_form);
		$recept_form=str_replace('!!max_no!!', $i*1-1, $recept_form);
		
		//Affichage commentaires
		$recept_form =	str_replace('!!comment_lg_all!!',htmlentities(stripslashes($comment_lg_all ?? ""),ENT_QUOTES,$charset),$recept_form);
		$recept_form =	str_replace('!!comment_lo_all!!',htmlentities(stripslashes($comment_lo_all ?? ""),ENT_QUOTES,$charset),$recept_form);
		
		//boutons
		$lgstat_all = lgstat::getHtmlSelect(array(0=>0), $msg['acquisition_recept_lgstat_none'], array('id'=>'sel_lgstat_all', 'name'=>'sel_lgstat_all'));
		$recept_form = str_replace('<!-- sel_lgstat_all -->', $lgstat_all, $recept_form);
		$recept_form = str_replace('<!-- bt_app -->', $bt_app, $recept_form);
		$recept_form = str_replace('<!-- bt_rel -->', $bt_rel, $recept_form);
		$recept_form = str_replace('<!-- bt_chk -->', $bt_chk, $recept_form);
		
		$recept_form.=$recept_search_form_suite;
		print $recept_form;
		
	} elseif ($err) {
		//erreur dans la recherche
		$recept_form.=$recept_search_form_suite;
		print $recept_form;
		print $err;
	} else {
		// pas de resultat
		$recept_form.=$recept_search_form_suite;
		print $recept_form;
		$cle=array();
		if ($cde_query) $cle[]=htmlentities($msg['acquisition_recept_act_search'].' '.stripslashes($cde_query),ENT_QUOTES,$charset);
		if ($all_query) $cle[]=htmlentities($msg['acquisition_recept_global_search'].' '.stripslashes($all_query),ENT_QUOTES,$charset);
		error_message($msg['acquisition_recept_rech'], str_replace('!!cle!!', implode(',',$cle), $msg['acquisition_recept_rech_error']), 0);
	}
}

//Effectue les modifications en masse
function apply_changes() {
	global $chk, $id_lig;
	global $sel_lgstat_all, $comment_lg_all, $comment_lo_all;
	global $lgstat_filter;
	
	$t_id=array();
	$t_f=array();
	if ($sel_lgstat_all) {
		$t_f['statut']=$sel_lgstat_all;
		$lgstat_filter=array(0=>$sel_lgstat_all);
	}
	$comment_lg_all=trim($comment_lg_all);
	if($comment_lg_all!=='') {
		$t_f['commentaires_gestion']=$comment_lg_all;
	}
	$comment_lo_all=trim($comment_lo_all);
	if($comment_lo_all!=='') {
		$t_f['commentaires_opac']=$comment_lo_all;
	}

	if (is_array($chk) && count($chk) && count($t_f)) {
		foreach($chk as $v) {
			if($id_lig[$v]) $t_id[]=$id_lig[$v];
		}
		if (count($t_id)) lignes_actes::updateFields($t_id,$t_f);
	}
}

//Effectue l'envoi de relances
function do_relances() {
	global $msg, $charset;
	global $id_bibli, $chk, $id_lig;
	global $acquisition_pdfrel_obj_mail, $acquisition_pdfrel_text_mail;
	global $acquisition_pdfrel_by_mail,$PMBuseremailbcc;
	
	//recuperation des lignes a relancer
	$tab_lig=array();
	foreach($chk as $v) {
		if($id_lig[$v]) {
			$tab_lig[]=$id_lig[$v];
		}
	}
	
	$tab_fou=array();
	$q = lignes_actes::getLines($tab_lig, true);
	if ($q) {
		$r=pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			while($row=pmb_mysql_fetch_object($r)) {
				if (!array_key_exists($row->num_fournisseur,$tab_fou)) {
					$tab_fou[$row->num_fournisseur]=array();
				}
				if (!array_key_exists($row->id_acte,$tab_fou[$row->num_fournisseur])) {
					$tab_fou[$row->num_fournisseur][$row->id_acte]=array();
				}
				$tab_fou[$row->num_fournisseur][$row->id_acte][]=$row->id_ligne;
			}
		}
	}

	$bib = new entites($id_bibli);
	$bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_bibli,1));
	
	$tab_no_mail=array();
	if ( !($acquisition_pdfrel_by_mail && strpos($bib_coord->email,'@')) ) {
		$tab_no_mail=$tab_fou;
	} else {	
		if (count($tab_fou)){
			foreach($tab_fou as $id_fou=>$tab_act) {
				$fou = new entites($id_fou);
				$fou_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_fou,1));
				
				//Si on peut relancer par mail
				if (!empty($fou_coord) && strpos($fou_coord->email,'@')) {
					$dest_name='';
					if($fou_coord->libelle) {
						$dest_name = $fou_coord->libelle;
					} else {
						$dest_name = $fou->raison_sociale;
					}
					if($fou_coord->contact) $dest_name.=" ".$fou_coord->contact;
					$dest_mail=$fou_coord->email;
					$obj_mail = $acquisition_pdfrel_obj_mail; 
					$text_mail = $acquisition_pdfrel_text_mail;
					$bib_name = $bib_coord->raison_sociale; 
					$bib_mail = $bib_coord->email;
					
					$lettre = lettreRelance_PDF_factory::make();
					$lettre->doLettre($bib, $bib_coord,$fou, $fou_coord, $tab_act);
					$piece_jointe=array();
					$piece_jointe[0]['contenu']=$lettre->getLettre('S');
					$piece_jointe[0]['nomfichier']=$lettre->getFileName();
					
					//         mailpmb($to_nom="", $to_mail,   $obj="",   $corps="",  $from_name="", $from_mail, $headers, $copie_CC="", $copie_BCC="", $faire_nl2br=0, $pieces_jointes=array())
					$res_envoi=mailpmb($dest_name, $dest_mail, $obj_mail, $text_mail ,$bib_name, $bib_mail, "Content-Type: text/plain; charset=\"$charset\"", '', $PMBuseremailbcc, 1, $piece_jointe);
					if (!$res_envoi) {
						$tab_no_mail[$id_fou]=$tab_act;
					} else {
					    print display_notification(sprintf($msg["acquisition_print_emailsucceed"],$dest_mail));
					}
				} else {
					$tab_no_mail[$id_fou]=$tab_act;
				}
			}
		}
	}	

	if (count($tab_no_mail)) {
		print "	
		<form name='print_liste_relances' action='pdf.php?pdfdoc=listrecept' target='lettre' method='post'>		
			<input type='hidden' name='id_bibli' value='".$id_bibli."'/>
			<input type='hidden' name='tab_no_mail' value='".rawurlencode(serialize($tab_no_mail))."'/>
			<script type='text/javascript'>
				openPopUp('','lettre');
				document.print_liste_relances.submit();
			</script>
		</form>";
	}
	lignes_actes::setRelances($tab_lig);
}

function show_from_cde() {
	global $id_cde;
	global $id_bibli, $id_exer, $f_fou_code, $f_dem_code, $f_rub_code;
	global $chk_dev, $chk_cde, $cde_query, $all_query, $recept_query, $lgstat_filter;
	
	$act = new actes($id_cde);
	$id_bibli = $act->num_entite;
	$id_exer = $act->num_exercice;
	$f_fou_code = array(0=>$act->num_fournisseur);
	$f_dem_code = array();
	$f_rub_code = array();
	$chk_dev=0;
	$chk_cde='1';
	$cde_query = $act->numero;
	$all_query = '';
	$lgstat_filter = lgstat::getList('ARRAY_VALUES');
	$recept_query = '';
}

function catalog() {
	global $msg, $charset, $id_lig, $serialized_search, $value_deflt_fonction, $value_deflt_lang, $xmlta_doctype;
	
	$lg = new lignes_actes($id_lig);
	$taec = explode("\r\n", $lg->libelle);
	$z = new z3950_notice('from_scratch');
	$z->libelle_form = '';
	$z->bibliographic_level = 'm';
	$z->hierarchic_level = '0';
	$z->aut_array = array();
	$z->editors = array();
	$z->collection = array();
	
	//titre sur 1ere ligne
	$z->titles = array(0 => $taec[0]);
	$z->serie = '';
	$z->nbr_in_serie = '';
	
	//Auteur sur 2eme ligne (Entree, rejete)
	if (isset($taec[1])) {
    	$taec_a = explode(',', $taec[1]);
    	$z->aut_array[0] = array(	
    	    'entree'		 =>	$taec_a[0],
    	    'rejete'		 =>	(isset($taec_a[1]) ? $taec_a[1] : ''),
			'date'			 =>	'',
			'type_auteur'	 =>	'70',
			'fonction'		 =>	$value_deflt_fonction,
			'id'			 =>	0,
			'responsabilite' =>	0
		);
	}
	
	//Editeur sur 3eme ligne (Ville : Nom, Annee)
	if (isset($taec[2])) {
    	$taec_e = explode(':', $taec[2]);
    	if (isset($taec_e[1])) {		
    		$taec_e1 = explode(',', $taec_e[1]);
    		$z->editors[0] = array(
    		    'name'		 =>	trim($taec_e1[0]),
				'ville'		 =>	trim($taec_e[0]),
				'id'		 =>	0
			);
    	} else {
    		//(Nom, Annee)
    		$taec_e1 = explode(',', $taec[2]);
    		$z->editors[0] = array(	
    		    'name'		 =>	trim($taec_e1[0]),
				'ville'		 =>	'',
				'id'		 =>	0
    		);
    	}
	}
	
	//Collection sur 4eme ligne
	if (isset($taec[3])) {
    	$z->collection = array(	
    	    'name'			 =>	trim($taec[3]), 
    		'id'			 =>	0
		);
	}
	
	$z->nbr_in_collection = '';
	$z->year = (isset($taec_e1[1]) ? trim($taec_e1[1]) : '');
	$z->mention_edition = '';
	$z->isbn = $lg->code;
	$z->page_nbr = '';
	$z->illustration = '';
	$z->prix = $lg->prix;
	$z->accompagnement = '';
	$z->size = '';
	$z->general_note = '';
	$z->content_note = '';
	$z->abstract_note = '';
	$z->dewey = array();
	$z->free_index = '';
	$z->tu_500 = array();
	$z->language_code = array(0 => $value_deflt_lang);
	$z->original_language_code = array();
	$z->link_url = '';
	$z->link_format = '';
	$z->document_type = $xmlta_doctype;
	$z->perio_titre = array();
	$z->perio_issn = array();
	$z->bull_mention = array();
	$z->bull_titre = array();
	$z->bull_num = array();
	$z->bt_integr_value = $msg[77];
	$z->bt_undo_value = $msg[76];
	$z->bt_undo_action = 'history.go(-1);';
	$z->message_retour = $msg[654];
	
	$form = $z->get_form("acquisition.php?categ=ach&sub=recept&action=record", 0, false);
	$form = str_replace("<!--!!form_title!!-->", "<h3>".htmlentities($msg[270], ENT_QUOTES, $charset)."</h3>", $form);
	$form = str_replace("<!--form_suite-->", "<input type='hidden' name='id_lig' value='".$id_lig."' /><!--form_suite-->", $form);
	$form = str_replace("<!--form_suite-->", "<input type='hidden' name='serialized_search' value='".stripslashes($serialized_search)."' /><!--form_suite-->", $form);
	print $form;
}

function record() {
	global $pmb_notice_controle_doublons;
	global $recept_cat_error_form;
	global $integre, $serialized_post, $existant_notice_id, $existant_b_level, $existant_h_level, $signature, $id_lig, $serialized_search;

	$recorded = false;
	
	switch ($integre) { 
		case 'new' :
			$unserialized_post = unserialize(rawurldecode(stripslashes($serialized_post)));
			foreach($unserialized_post as $key => $val){
				add_sl($val);
				$GLOBALS[$key] = $val;
				global ${$key};
			}
			$z=new z3950_notice("form");
			$z->signature = $signature;
			$ret=$z->insert_in_database();
			$notice_id = $ret[1];
			$recorded = true;
			break;
		case 'existant' :
			$notice_id = $existant_notice_id;
			$b_level = $existant_b_level;
			$h_level = $existant_h_level;
			$recorded = true;
			break;
		default :
			$duplicate = array();
			$signature = '';
			$r = new stdClass();
	
			if($pmb_notice_controle_doublons != 0){
				$sign = new notice_doublon(true);
				$signature = $sign->gen_signature();
				$r = $sign->getDuplicate();
			}
			if (!empty($r->notice_id)) {
			
				if ($r->niveau_biblio =='a' && $r->niveau_hierar== 2) { //article
					
					$serial = new serial_display (	$r->notice_id,		// $id = id de la notice à afficher 
													6,					// $level :
																		// 0 : juste le header (titre  / auteur principal avec le lien si applicable)
																		// 6 : cas général détaillé avec notes, categ, langues, indexation... + boutons 
													'',					// $action_serial = URL à atteindre si la notice est une notice chapeau
													'', 				// $action_analysis = URL à atteindre si la notice est un dépouillement
																		// note dans ces deux variables, '!!id!!' sera remplacé par l'id de cette notice
																		// les deux liens s'excluent mutuellement, bien sur. 
													'', 				// $action_bulletin
													'', 				// $lien_suppr_cart = lien de suppression de la notice d'un caddie
													'', 				// $lien_explnum
													0,					// $bouton_explnum
													2,					// $print
													1, 					// $show_explnum
													0, 					// $show_statut=
													1, 					// $show_opac_hidden_fields=
													0,					// $draggable
													0, 					// $ajax_mode
													'',					// $anti_loop
													0					// $no_link
									);
					$notice_display =  $serial->result;
					
				} elseif ($r->niveau_biblio=='m' && $r->niveau_hierar== 0) { 	//monographie
					
					$display = new mono_display(	$r->notice_id, 		// $id = id de la notice à afficher
													6, 					// $level :
																		//	0 : juste le header (titre  / auteur principal avec le lien si applicable) 
																		//	1 : ISBD seul, pas de note, bouton modif, expl, explnum et résas
																		// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
													'', 				// $action = URL associée au header
													1, 					// $expl -> affiche ou non les exemplaires associés
													'', 				// $expl_link
													'', 				// $lien_suppr_cart
													'',					// $explnum_link
													0,					// $show_resa
													2, 					// $print
													1, 					// $show_explnum
													1,					// $show_statut
													'',					// $anti_loop
													0,					// $draggable
													0,					// $no_link
													1,					// $show_opac_hidden_fields
													0					// $ajax_mode
												);
					$notice_display = $display->result;
					
		        }
			
				$form = $recept_cat_error_form;
				$form = str_replace('!!serialized_post!!', rawurlencode(serialize($_POST)), $form);
				$form = str_replace('!!existant_notice_id!!', $r->notice_id, $form);
				$form = str_replace('!!existant_b_level!!', $r->niveau_biblio, $form);
				$form = str_replace('!!existant_h_level!!', $r->niveau_hierar, $form);
				$form = str_replace('!!signature!!', $signature, $form);
				$form = str_replace('!!id_lig!!', $id_lig, $form);
				$form = str_replace('!!serialized_search!!', stripslashes($serialized_search), $form);
				$form = str_replace('<!-- notice_display -->', $notice_display, $form);
								
				print $form;
				return false;
			
			} else {
				
				$z=new z3950_notice("form");
				$z->signature = $signature;
				$ret=$z->insert_in_database();
				$notice_id=$ret[1];
				$recorded = true;
			}
			break;
	}	
	
	if ($recorded) {
		global $id_bibli, $id_exer, $f_fou_code, $f_dem_code, $t_dem, $f_rub_code, $cde_query, $all_query, $chk_dev, $lgstat_filter;
		
		$unserialized_search = unserialize(rawurldecode(stripslashes($serialized_search)));
		$id_bibli = $unserialized_search['id_bibli'];
		$id_exer = $unserialized_search['id_exer'];
		$f_fou_code = $unserialized_search['f_fou_code'];
		$f_dem_code = $unserialized_search['f_dem_code'];
		$t_dem = $unserialized_search['t_dem'];
		$f_rub_code = $unserialized_search['f_rub_code'];
		$cde_query = $unserialized_search['cde_query'];
		$all_query = $unserialized_search['all_query'];
		$chk_dev = $unserialized_search['chk_dev'];
		$lgstat_filter = $unserialized_search['lgstat_filter'];
		
		if ($notice_id) {
			$typ_lig = 1;
			if ($b_level=='a' && $h_level==2) $typ_lig = 5;
			lignes_actes::updateFields(array(0=>$id_lig), array('num_produit'=>$notice_id, 'type_ligne'=>$typ_lig));
		}	
	}
	return $recorded;
}

//Traitement des actions
switch($action) {
	case 'apply_changes' :
		apply_changes();
		show_list_recept();
		break;
	case 'do_relances' :
		if (count($chk)) {
			do_relances();
		}
		show_list_recept();
		break;
	case 'from_cde' :
		if ($id_cde) {
			show_from_cde();
		}
		show_list_recept();
		break;
	case 'catalog' :
		catalog();
		break;
	case 'record' :
		if (record()){
			show_list_recept();
		}
		break;		
	case 'list':
	default:
		show_list_recept();
		break;
}
?>
