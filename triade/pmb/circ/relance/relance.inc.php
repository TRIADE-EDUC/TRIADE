<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: relance.inc.php,v 1.97 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($act)) $act = '';

require_once($include_path."/mail.inc.php") ;
require_once ($include_path."/mailing.inc.php");
require_once ("$include_path/notice_authors.inc.php");  

//Gestion des relances
require_once($class_path."/amende.class.php");
require_once($class_path."/comptes.class.php");
require_once($class_path."/serie.class.php");
require_once ("$class_path/author.class.php");  
require_once("$class_path/progress_bar.class.php");	

function get_action($id_empr,$niveau,$niveau_normal) {
	global $msg, $charset, $pmb_recouvrement_auto;
	$action="<input type='hidden' name='empr[]' value='".$id_empr."'>
	<select name='action_".$id_empr."'>
	";
	$action.="<option value='-1'>".$msg["relance_do_nothing"]."</option>\n";

	//if ((($niveau==$niveau_normal)||(($niveau==3)&&($niveau_normal==4)))&&($niveau!=0)) {
	//	$action.="<option value='edit'>Editer la lettre</option>";
	//}
	if ($niveau>$niveau_normal) {
		$action.="<option value='$niveau_normal' ";
		$action.="selected";
		$action.=">".sprintf($msg["relance_back_level"],$niveau_normal)."</option>\n";
	}
	if ($niveau<$niveau_normal) {
		if ($niveau_normal==4) $nn=3; else $nn=$niveau_normal;
		if ($niveau==4) $nd=3; else $nd=$niveau+1;
		for ($i=$nd; $i<=$nn; $i++) {
			$action.="<option value='$i' ";
			if ($i==$nn) $action.="selected";
			$action.=">".sprintf($msg["relance_change_level"],$i)."</option>\n";
		}
		if ($niveau_normal==4) {
			$action.="<option value='4' ";
			if (($niveau==3) && ($pmb_recouvrement_auto)) $action.=" selected";
			$action.=">".$msg["relance_go_recouvr"]."</option>\n";
		}
	}
	$action.="</select>
	";
	return $action;
}

function do_action($id_empr) {
	global $pmb_gestion_amende, $lang, $include_path;
	global $finance_recouvrement_lecteur_statut;
	$action="action_".$id_empr;
	global ${$action},$msg,$charset,$finance_statut_perdu;
	$act=${$action};
	
	//Récupération du solde du compte
	$frais_relance=0;
	$id_compte=comptes::get_compte_id_from_empr($id_empr,2);
	if ($id_compte) {
		$cpte=new comptes($id_compte);
		$frais_relance=$cpte->summarize_transactions("","",0,$realisee=-1);
		if (($frais_relance)&&($frais_relance<0)) {
			$frais_relance=-$frais_relance;
		} else $frais_relance=0;
	}
	
	//Si action différent de -1, alors changement
	$quatre=false;
	if ($act!=-1) {
		//Récupération de la liste des prêts
		$amende=new amende($id_empr);
		// on efface le cache pour qu'il soit remis à jour au prochain accès
		$req="delete from cache_amendes where id_empr=$id_empr ";
		pmb_mysql_query($req);
		
		$montant_total=0;
		for ($j=0; $j<count($amende->t_id_expl); $j++) {
			$params=$amende->t_id_expl[$j];
			//Si c'est juste un changement de niveau
			if ($act<4) {
				//Si il y a attente de changement d'état
				if ($params["amende"]["niveau_relance"]<$params["amende"]["niveau"]) {
					//Si le niveau attendu est supérieur ou égal au niveau demandé
					if ($params["amende"]["niveau"]>=$act) {
						//On passe au niveau demandé
						$niveau=$act;
					} else {
						//Sinon on passe au niveau prévu
						$niveau=$params["amende"]["niveau"];
					}
					//Enregistrement du changement de niveau
					$requete="update pret set niveau_relance=$niveau, date_relance=now(), printed=0 where pret_idempr=$id_empr and pret_idexpl=".$params["id_expl"];
					
					pmb_mysql_query($requete);
				}
				//Si le niveau supposé est inférieur au dernier niveau validé (ex : prolongations,...), on peut revenir..
				if ($params["amende"]["niveau"]<$params["amende"]["niveau_relance"]) {
					if ($params["amende"]["niveau_relance"]>=$act) {
						//On passe au niveau demandé
						$niveau=$act;
					} else {
						//Sinon on passe au niveau prévu
						$niveau=$params["amende"]["niveau"];
					}
					//Enregistrement du changement de niveau
					$requete="update pret set niveau_relance=$niveau, date_relance=now(), printed=0 where pret_idempr=$id_empr and pret_idexpl=".$params["id_expl"];
					
					pmb_mysql_query($requete);
				}
			} else {
				//Sinon, c'est plus grave, on passe en recouvrement !!
				$quatre=true;
				//Si niveau prévu = 4
				if ($params["amende"]["niveau"]==4) {
					//Passage des ouvrages en statut perdu
					$requete="update exemplaires set expl_statut=$finance_statut_perdu where expl_id=".$params["id_expl"];
					pmb_mysql_query($requete);						
					//Débit du compte lecteur + tarif des relances
					$debit=$amende->get_amende($params["id_expl"]);
					$debit=$debit["valeur"];
					$id_compte=comptes::get_compte_id_from_empr($id_empr,2);
					if ($id_compte) { //&&($debit)
						$compte=new comptes($id_compte);
						//Enregistrement transaction
						$id_transaction=$compte->record_transaction("",$debit,-1,sprintf($msg["relance_recouvr_transaction"],$params["id_expl"]),0);
						//Validation
						$compte->validate_transaction($id_transaction);
						$montant_total+=$debit;
						
						$requete="select pret_date from pret where pret_idexpl=".$params["id_expl"];
						$resultat=pmb_mysql_query($requete);
						$r=pmb_mysql_fetch_object($resultat);
						$req_pret_date= ", date_pret='".$r->pret_date."' ";
						
						$requete="select  log.date_log as date_log, log.niveau_reel as niv
						from log_expl_retard as expl,log_retard as log 
						where expl.num_log_retard=log.id_log and  log.idempr=$id_empr and expl.expl_id=".$params["id_expl"] ." order by log.date_log limit 3";
						
						$res=pmb_mysql_query($requete);
						$req_date_relance="";
						$i=1;
						while($log = pmb_mysql_fetch_object($res)){
							$req_date_relance.= ", date_relance".$i++."='".$log->date_log."' ";
						}
						
						$requete="insert into recouvrements set empr_id=$id_empr, id_expl=".$params["id_expl"].", date_rec= now(), libelle='',recouvr_type=0, montant='$debit' $req_pret_date $req_date_relance";
						pmb_mysql_query($requete);
											
						// Essayer de retrouver le prix de l'exemplaire 
						$requete="select expl_prix, prix from exemplaires, notices where (notice_id=expl_notice or notice_id=expl_bulletin) and expl_id =".$params["id_expl"];
						$resultat=pmb_mysql_query($requete);
						$prix=0;
						if($r=pmb_mysql_fetch_object($resultat)) {
							$tmp_expl_prix = str_replace(',','.',$r->expl_prix);
							$tmp_prix = str_replace(',','.',$r->prix);
							if(!$prix=1*($tmp_expl_prix)) $prix=1*($tmp_prix); 			
						}
						$requete="insert into recouvrements set empr_id=$id_empr, id_expl=".$params["id_expl"].", date_rec=now(), libelle='', recouvr_type=1, montant='$prix' $req_pret_date $req_date_relance";
						pmb_mysql_query($requete);
						
						// on modifie le status du lecteur si demandé
						if($finance_recouvrement_lecteur_statut){
							$requete="update empr set empr_statut=$finance_recouvrement_lecteur_statut where id_empr=$id_empr";
							pmb_mysql_query($requete);
						}	
					}

					//Supression du pret
					$requete="delete from pret where pret_idexpl=".$params["id_expl"];
					pmb_mysql_query($requete);
					$requete="update exemplaires set expl_note=concat(expl_note,' ','".$msg["relance_non_rendu_expl"]."'),expl_lastempr='".$id_empr."' where expl_id=".$params["id_expl"];
					pmb_mysql_query($requete);
					$requete="update empr set empr_msg=trim(concat(empr_msg,' ','".addslashes($msg["relance_recouvrement"])."')) where id_empr=".$id_empr;
					pmb_mysql_query($requete);
				}
			}
			
			//Ajout solde du compte amendes
			if ($quatre) {
				if ($frais_relance) {
					$requete="insert into recouvrements (empr_id,id_expl,date_rec,libelle,montant) values($id_empr,0,now(),'".$msg["relance_frais_relance"]."',".$frais_relance.")";
					pmb_mysql_query($requete);
					$montant_total+=$frais_relance;
				}
						
				//Passage en perte pour la bibliothèque
				//Débit sur le compte 0
				//if ($montant_total) {
				//	$requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,date_effective,montant,sens,realisee,commentaire,encaissement) values(0,$PMBuserid,'".$PMBusername."','".$_SERVER["REMOTE_ADDR"]."', now(), now(), now(), ".($montant_total*1).", -1, 1,'Recouvrement lecteur : ".$params["id_expl"]."',0)";
				//	pmb_mysql_query($requete);
				//}
			}
		}
		//Traitement des frais
		$niveau_min=$act;
		$the_frais = 0;
		if ($pmb_gestion_amende == 1) {
			$frais="finance_relance_".$niveau_min;
			global ${$frais};
			$the_frais = ${$frais};						
		}
		else {
			$quota_name = "";
			switch ($niveau_min) {
				case 1:
					$quota_name="AMENDERELANCE_FRAISPREMIERERELANCE";
					break;
				case 2:
					$quota_name="AMENDERELANCE_FRAISDEUXIEMERELANCE";
					break;
				case 3:
					$quota_name="AMENDERELANCE_FRAISTROISIEMERELANCE";
					break;
				default:
					break;
			}
			$qt = new quota($quota_name, "$include_path/quotas/own/$lang/finances.xml");
			$struct["READER"] = $id_empr;
			$the_frais = $qt -> get_quota_value($struct);
			if ($the_frais == -1) $the_frais = 0;
		}
		
		if($the_frais){
			if ($id_compte) { 
				$compte=new comptes($id_compte);
				//Enregistrement transaction
				$cpte->record_transaction("",$the_frais,-1,sprintf($msg["relance_frais_relance_level"],$niveau_min));
			}
		}
	}
}

function send_mail($id_empr, $relance) {
	global $pmb_gestion_devise,$msg,$charset;
	global $biblio_name,$biblio_email,$biblio_phone, $PMBuseremailbcc;
	global $mailretard_hide_fine; 
	
	// l'objet du mail
	$var = "mailretard_".$relance."objet";
	global ${$var};
	eval ("\$objet=\"".${$var}."\";");

	// la formule de politesse du bas (le signataire)
	$var = "mailretard_".$relance."fdp";
	global ${$var};
	eval ("\$fdp=\"".${$var}."\";");

	// le texte après la liste des ouvrages en retard
	$var = "mailretard_".$relance."after_list";
	global ${$var};
	eval ("\$after_list=\"".${$var}."\";");

	// le texte avant la liste des ouvrges en retard
	$var = "mailretard_".$relance."before_list";
	global ${$var};
	eval ("\$before_list=\"".${$var}."\";");

	// le "Madame, Monsieur," ou tout autre truc du genre "Cher adhérent,"
	$var = "mailretard_".$relance."madame_monsieur";
	global ${$var};
	eval ("\$madame_monsieur=\"".${$var}."\";");

	if($madame_monsieur) $texte_mail.=$madame_monsieur."\r\n\r\n";
	if($before_list) $texte_mail.=$before_list."\r\n\r\n";

	//Récupération des exemplaires
	$rqt = "select pret_idempr, expl_id, expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_retour < curdate() and pret_idexpl=expl_id order by pret_date " ;
	$req = pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error()); 

	$i=0;
	$total_amendes=0;
	
	//Calcul des frais de relance
	$id_compte=comptes::get_compte_id_from_empr($id_empr,2);
	if ($id_compte) {
		$cpte=new comptes($id_compte);
		$frais_relance=$cpte->summarize_transactions("","",0,$realisee=-1);
		if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
	}

	while ($data = pmb_mysql_fetch_array($req)) {
		//Calcul des amendes
		$valeur=0;
		$amende=new amende($data["pret_idempr"]);
		$amd=$amende->get_amende($data["expl_id"]);
		if ($amd["valeur"]) {
			$valeur=$amd["valeur"];
			$total_amendes+=$valeur;
		}
	
		/* Récupération des infos exemplaires et prêt */
		$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, pret_date, pret_retour, tdoc_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
		$requete.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ; 
		$requete.= "FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type , pret ";
		$requete.= "WHERE expl_cb='".addslashes($data['expl_cb'])."' and expl_typdoc = idtyp_doc and pret_idexpl = expl_id  ";
		
		$res = pmb_mysql_query($requete);
		$expl = pmb_mysql_fetch_object($res);
		
		$responsabilites=array() ;
		$header_aut = "" ;
		$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
		$header_aut = gen_authors_header($responsabilites);
		$header_aut ? $auteur=" / ".$header_aut : $auteur="";

		// récupération du titre de série
		$tit_serie="";
		if ($expl->tparent_id && $expl->m_id) {
			$parent = new serie($expl->tparent_id);
			$tit_serie = $parent->name;
			if ($expl->tnvol)
				$tit_serie .= ', '.$expl->tnvol;
			}
		if ($tit_serie) {
			$expl->tit = $tit_serie.'. '.$expl->tit;
			}

		$texte_mail.=$expl->tit.$auteur."\r\n";
		$texte_mail.="    -".sprintf($msg["relance_mail_retard_dates"],$expl->aff_pret_date,$expl->aff_pret_retour);
		if ($valeur && !$mailretard_hide_fine) $texte_mail.=" ".sprintf($msg["relance_mail_retard_amende"],comptes::format_simple($valeur));
		$texte_mail.="\r\n";
		$i++;
	}
	if (!$mailretard_hide_fine) {
		if ($total_amendes) $texte_mail.="\r\n".sprintf($msg["relance_mail_retard_total_amendes"],comptes::format_simple($total_amendes));
		
		if ($frais_relance) $texte_mail.="\r\n".$msg["relance_lettre_retard_frais_relance"].comptes::format_simple($frais_relance);
		
		if (($frais_relance)&&($total_amendes)) $texte_mail.="\r\n".$msg["relance_lettre_retard_total_du"].comptes::format_simple($total_amendes+$frais_relance);
	}

	$texte_mail.="\r\n\r\n";
	if($after_list) $texte_mail.=$after_list."\r\n\r\n";
	if($fdp) $texte_mail.=$fdp."\r\n\r\n";
	$texte_mail.=mail_bloc_adresse();
	
	//Tableau contenant le destinataire (emprunteur) ou les destinataires (tous les responsables de groupe dont l'emprunteur est membre)
	$to_nom = array();
	$to_mail = array();
	
	/* Récupération du nom, prénom et mail du lecteur concerné */
	$requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_cb from empr where id_empr=$id_empr";
	$res=pmb_mysql_query($requete);
	$coords=pmb_mysql_fetch_object($res);
	$to_nom[0] = $coords->empr_prenom." ".$coords->empr_nom;
	$to_mail[0] = $coords->empr_mail;
	
	//Si mail de rappel affecté au responsable du groupe : on envoie à tous les responsables des groupes (concernés par l'emprunteur)
	$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=$id_empr and resp_groupe and mail_rappel";
	$res=pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res) > 0) {
		$qt_to = 0;
		while ($row = pmb_mysql_fetch_object($res)) {
			$requete="select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr='".$row->resp_groupe."'";
			$result=pmb_mysql_query($requete);
			$coords_dest=pmb_mysql_fetch_object($result);
			$to_nom[$qt_to] = $coords_dest->empr_prenom." ".$coords_dest->empr_nom;
			$to_mail[$qt_to] = $coords_dest->empr_mail;
			$qt_to++;
		}
	}
		
	//remplacement nom et prenom
	$texte_mail=str_replace("!!empr_name!!", $coords->empr_nom,$texte_mail); 
	$texte_mail=str_replace("!!empr_first_name!!", $coords->empr_prenom,$texte_mail); 

	// function mailpmb($to_nom="", $to_mail, $obj="", $corps="", $from_name="", $from_mail, $headers, $copie_CC="", $copie_BCC="", $faire_nl2br=0, $pieces_jointes=array()) {
	$flag_res = false;
	//On boucle si plusieurs destinataires
	foreach ($to_nom as $key=>$dummy_value) {
		if(mailpmb($to_nom[$key], $to_mail[$key], $objet." : ".$coords->empr_prenom." ".mb_strtoupper($coords->empr_nom,$charset)." (".$coords->empr_cb.")",$texte_mail,$biblio_name, $biblio_email,"Content-Type: text/plain; charset=\"$charset\"\n", "", $PMBuseremailbcc, 1)){
			$flag_res = true;
		}
	}
	//Il faut au moins un email bien envoyé pour retourner true.
	return $flag_res;
}

function print_relance($id_empr,$mail=true) {
	global $mailretard_priorite_email, $mailretard_priorite_email_2, $mailretard_priorite_email_3;
	global $dbh,$charset, $msg, $pmb_gestion_financiere, $pmb_gestion_amende;
	global $mail_sended;
	
	$mail_sended=0;
	$not_mail=0;
	if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
		$req="delete from cache_amendes where id_empr=".$id_empr;
		pmb_mysql_query($req);
		$amende=new amende($id_empr);
		$level=$amende->get_max_level();
		$niveau_min=$level["level_min"];
		$id_expl=$level["level_min_id_expl"];
		$total_amende = $amende->get_total_amendes();
	}
	
	//Si mail de rappel affecté au groupe, on envoi au responsable
	$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=$id_empr and resp_groupe and mail_rappel limit 1";
	$res=pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($res) > 0) {
		$requete="select empr_mail from empr where id_empr='".pmb_mysql_result($res,0,1)."'";
		$res=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($res)) {
			list($empr_mail)=pmb_mysql_fetch_row($res);
		}
	} else {
		$requete="select empr_mail from empr where id_empr=$id_empr";
		$resultat=pmb_mysql_query($requete);
		if (@pmb_mysql_num_rows($resultat)) {
			list($empr_mail)=pmb_mysql_fetch_row($resultat);
		}
	}
	
	if ($niveau_min) {
		//Si c'est un mail
		//JP 05/06/2017 : je passe par un flag car l'imbrication de conditions se complique...
		$flag_print=false;
		if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($empr_mail))&&( ($niveau_min<3)||($mailretard_priorite_email_3) )&&($mail)) {
			$flag_print=true;
			if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
				//On force en lettre
				$flag_print=false;
			}
		}
		
		if ($flag_print) {
			if (send_mail($id_empr,$niveau_min)) {
				$requete="update pret set printed=1 where pret_idexpl=".$id_expl;
				pmb_mysql_query($requete,$dbh);		
				$mail_sended=1;			
			}
		} else {
			$requete="update pret set printed=2 where pret_idexpl=".$id_expl;
			pmb_mysql_query($requete,$dbh);
			$not_mail=1;
		}
	}
	$req="delete from cache_amendes where id_empr=".$id_empr;
	pmb_mysql_query($req);
	//On loggue les infos de la lettre
	$niveau_courant = $niveau_min;
	
	if($niveau_courant){
		
		$niveau_suppose = $level["level_normal"];
		$cpt_id=comptes::get_compte_id_from_empr($id_empr,2);
		$cpt=new comptes($cpt_id);
		$solde=$cpt->update_solde();
		$frais_relance=$cpt->summarize_transactions("","",0,$realisee=-1);
		if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
	
		$req="insert into log_retard (niveau_reel,niveau_suppose,amende_totale,frais,idempr,log_printed,log_mail) values('".$niveau_courant."','".$niveau_suppose."','".$total_amende."','".$frais_relance."','".$id_empr."', '".$not_mail."', '".$mail_sended."')";
		pmb_mysql_query($req,$dbh);		
		$id_log_ret = pmb_mysql_insert_id();

		$reqexpl = "select pret_idexpl as expl from pret where pret_retour<	CURDATE() and pret_idempr=$id_empr";
		$resexple=pmb_mysql_query($reqexpl,$dbh);
		while(($liste = pmb_mysql_fetch_object($resexple))){			
			$dates_resa_sql = " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour " ;
			$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, pret_idempr, expl_id, expl_cb,expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date!='', concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql.", " ;
			$requete.= " notices_m.tparent_id, notices_m.tnvol " ; 
			$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
			$requete.= " WHERE expl_id='".$liste->expl."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
			$res_det_expl = pmb_mysql_query($requete) ;
			$expl = pmb_mysql_fetch_object($res_det_expl);
			if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {		
				$amd = $amende->get_amende($liste->expl);
			}
			$req_ins="insert into log_expl_retard (titre,expl_id,expl_cb,date_pret,date_retour,amende,num_log_retard) values('".addslashes($expl->tit)."','".$expl->expl_id."','".$expl->expl_cb."','".$expl->pret_date."','".$expl->pret_retour."','".$amd["valeur"]."','".$id_log_ret."')";
			pmb_mysql_query($req_ins,$dbh);	
		}
	}			
	return $not_mail;
}


function filter_niveau($liste_ids,$champ,$selected="",$sort=false) {
	global $all_level,$late_ids;
	$ret="";
	$t=array();
	$v=array();

	//Recherche des lecteurs en retard
	if (!$late_ids) {
		$requete="select distinct pret_idempr from pret where pret_retour<	CURDATE() and pret_idempr in (".implode(",",$liste_ids).")";
		$res_id=pmb_mysql_query($requete);
		if (($res_id)&&(pmb_mysql_num_rows($res_id))) {
			while ($r=pmb_mysql_fetch_object($res_id)) {
				$late_ids[$r->pret_idempr]=1;
			}
		} else $late_ids=array();
	}
	
	for ($i=0;$i<=count($liste_ids)-1;$i++) {
		if (isset($late_ids[$liste_ids[$i]])) {
			$amende=new amende($liste_ids[$i]);
			$level=$amende->get_max_level();
			$t[$liste_ids[$i]]=$level[$champ];
			$v[$liste_ids[$i]]=$level;
		}
	}
	if ($all_level) {
		$liste_ids=array_keys($all_level);
		//afin de gérer les filtres combinés..
		if (($selected)&&(is_array($selected))) {
			if (!(count($selected) == 1 && ($selected[0] == -1))) {
				$all_level=array(0);
			}
		}
	}
	for ($i=0;$i<=count($liste_ids)-1;$i++) {
		if (($selected)&&(is_array($selected))) {
    		$as=array_search($v[$liste_ids[$i]][$champ],$selected);
    		if (($as!==FALSE)&&($as!==NULL)) $all_level[$liste_ids[$i]]=$v[$liste_ids[$i]];
    	}
	}
	if ($sort==true) sort($t[$champ],SORT_NUMERIC);	
	$result=array_unique($t);
	sort($result,SORT_NUMERIC);
	for ($i=0;$i<=count($result)-1;$i++) {
		if ($result[$i]!=0) {
			$ret.="<option value='".$result[$i]."'";
			if (($selected)&&(is_array($selected))) {
    			$as=array_search($result[$i],$selected);
    			if (($as!==FALSE)&&($as!==NULL)) $ret.=" selected";	
    		}				
			$ret.=">".$result[$i]."</option>";		
		}
	}
	return $ret;
}

function filter_loc_expl($liste_ids,$champ,$selected="",$sort=false){
	global $dbh,$charset;

	global $all_level;
	$ret="";
	
	if((($selected)&&(is_array($selected))) && !(count($selected) == 1 && ($selected[0] == -1))){
		if(is_array($all_level) && count($all_level)){//Je repart des bons lecteurs
			$liste_ids=array_keys($all_level);
		}
	
		$requete="SELECT pret_idempr FROM pret JOIN exemplaires ON pret_idexpl=expl_id JOIN docs_location ON expl_location=idlocation WHERE location_libelle IN ('".implode("','",$selected)."') ";
		if(count($liste_ids)){
			$requete.="AND pret_idempr IN (".implode(",",$liste_ids).")";
		}
		$requete.=" GROUP BY pret_idempr";
		$res=pmb_mysql_query($requete,$dbh);
		if($res && pmb_mysql_num_rows($res)){
			$new_all_level=array(0);
			while ($empr=pmb_mysql_fetch_object($res)) {
				if($all_level[$empr->pret_idempr]){
					$new_all_level[$empr->pret_idempr]=$all_level[$empr->pret_idempr];
				}else{
					$amende=new amende($empr->pret_idempr);
					$new_all_level[$empr->pret_idempr]=$amende->get_max_level();
				}
				
			}
			$all_level=$new_all_level;
		}else{
			$all_level=array(0);
		}
	}
	
	if(!is_array($selected)){
		$selected=array();
	}
	$requete="SELECT idlocation, location_libelle FROM docs_location GROUP BY location_libelle ORDER BY location_libelle";
	$res=pmb_mysql_query($requete,$dbh);
	if($res && pmb_mysql_num_rows($res)){
		while ($ligne=pmb_mysql_fetch_object($res)) {
			$ret.="<option value='".htmlentities($ligne->location_libelle,ENT_QUOTES,$charset)."'";
			if(in_array($ligne->location_libelle,$selected)) $ret.=" selected";	
			$ret.=">".htmlentities($ligne->location_libelle,ENT_QUOTES,$charset)."</option>";	
		}
	}
	return $ret;
}

// Pour localiser les relances : $deflt2docs_location, $pmb_lecteurs_localises, $empr_location_id ;
$loc_filter = "";
if ($pmb_lecteurs_localises) {
	$empr_location_id = $deflt2docs_location;
	$loc_filter = "and empr_location = '".$empr_location_id."' ";
}

//Traitement avant affichage
switch ($act) {
	case 'solo':
		$id_empr=$relance_solo;
		do_action($id_empr);
		break;
	case 'solo_print':
		$id_empr=$relance_solo;
		print_relance($id_empr,false);
		break;
	case 'solo_mail':
		$id_empr=$relance_solo;
		print_relance($id_empr);
		break;
	case 'valid':
		for ($i=0; $i<count($empr); $i++) {
			$id_empr=$empr[$i];
			do_action($id_empr);
		}
		break;
	case 'print':
		$not_all_mail = array();
		if ($empr) {
			$requete = "select id_empr from empr, pret, exemplaires where 1 ";
			$requete.=" and id_empr in (".implode(",",$empr).") ";
			//$requete.= $loc_filter;
			$requete.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
			$resultat=pmb_mysql_query($requete);
			$not_mail = 0;
			$mail_sended = 0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_mail = print_relance($r->id_empr);
					if (($not_mail == 1) || (!$mail_sended) ||($mailretard_priorite_email==2 && $niveau_min < 3)) { //mail_sended <=> globale
						$not_all_mail[] = $r->id_empr;
					}
				}
			}
		}

		if (count($not_all_mail) > 0) {
			print "
			<form name='print_empr_ids' action='pdf.php?pdfdoc=lettre_retard' target='lettre' method='post'>
			";		
			for ($i=0; $i<count($not_all_mail); $i++) {
				print "<input type='hidden' name='empr_print[]' value='".$not_all_mail[$i]."'/>";
			}	
			print "	<script>openPopUp('','lettre');
				document.print_empr_ids.submit();
				</script>
			</form>
			";
		}
		//Fermeture de la fenêtre d'impression si tout est parti par mail
		break;
	case 'export':
		$req="TRUNCATE TABLE cache_amendes";
		pmb_mysql_query($req);
		$requete = "select id_empr from empr, pret, exemplaires where 1 ";
		$requete.=" and id_empr in (".implode(",",$empr).") ";
		//$requete.= $loc_filter;
		$requete.= "and pret_retour< CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
		$resultat=pmb_mysql_query($requete);
		$not_all_mail=0;
		while ($r=pmb_mysql_fetch_object($resultat)) {
			$amende=new amende($r->id_empr);
			$level=$amende->get_max_level();
			$niveau_min=$level["level_min"];
			$printed=$level["printed"];
			if ((!$printed)&&($niveau_min)) {
				$not_all_mail+=print_relance($r->id_empr);		
			}
		}
		
		print "
		<form name='print_empr_ids' action='./circ/relance/relance_export.php';' target='lettre' method='post'>
			<script>openPopUp('','lettre');
			document.print_empr_ids.submit();
			</script>
		</form>";
		
		//Fermeture de la fenêtre d'impression si tout est parti par mail
		break;	
	case 'raz_printed':
		$req="TRUNCATE TABLE cache_amendes";
		pmb_mysql_query($req);
		$requete="update pret set printed=0 where printed!=0";
		if ($printed_cd) {
			$requete.=" and date_relance='".stripslashes($printed_cd)."'";
		}
		pmb_mysql_query($requete);
		break;
}


echo "<h1>".$msg["relance_menu"]."&nbsp;:&nbsp;".$msg["relance_to_do"]."&nbsp;<span id='nb_relance_to_do'>&nbsp;</span></h1>";

// Juste pour la progress bar , on execute ceci:
$req ="select id_empr  from empr, pret, exemplaires, empr_categ where 1 ";
$req.= $loc_filter;
$req.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr";
$res=pmb_mysql_query($req);

$nb=pmb_mysql_num_rows($res);
if($nb>2){	
	$progress_bar=new progress_bar($msg["relance_progress_bar"],$nb,3);		
}

// Calendrier activé : Est-il bien paramétré sur le site de gestion par défaut des lecteurs ?
if ($pmb_utiliser_calendrier) {
	$req_date_calendrier = "select count(num_location) as nb from ouvertures where date_ouverture >=curdate() and ouvert=1 and num_location=".$deflt2docs_location;
	$res_date_calendrier = pmb_mysql_query($req_date_calendrier);
	if ($res_date_calendrier) {
		if (!pmb_mysql_result($res_date_calendrier, 0, "nb")) {
			warning("", "<span class='erreur'>".$msg["calendrier_active_and_empty"]."</span>");
		}
	}
}

$requete ="select id_empr, empr_nom, empr_prenom, empr_cb, count(pret_idexpl) as empr_nb, empr_codestat, empr_mail, libelle from empr, pret, exemplaires, empr_categ where 1 ";
$requete.= $loc_filter;
$requete.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id and id_categ_empr=empr_categ group by id_empr order by empr_nom, empr_prenom";

if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
	require_once($class_path."/filter_list.class.php");
	if ($pmb_lecteurs_localises) $localisation=",l";
	$p_perso=new pret_parametres_perso("pret");
	$filter_p_perso = "";
	if(count($p_perso->t_fields)) {
		foreach ($p_perso->t_fields as $id=>$field) {
			if($field["FILTERS"]) {
					$filter_p_perso.= ",#p".$id;
			}
		}
	}
	$filter=new filter_list("empr","empr_list","b,n,c,cs,g","c".$localisation.",13,2,3".$filter_p_perso.($empr_filter_relance_rows?",".$empr_filter_relance_rows:""),"n,g");
	if ($pmb_lecteurs_localises) {
		$lo="f".$filter->fixedfields["l"]["ID"];
		global ${$lo};
		if (!${$lo}) {
			$tableau=array();
			$tableau[0]=$deflt2docs_location;
			${$lo}=$tableau;
		}
	}
	$filter->fixedcolumns="b,n,c";
	$filter->original_query=$requete;
	$filter->multiple=1;
	$t=array();
	$t["table"]="";
	$t["row_even"]="even";
	$t["row_odd"]="odd";
	$t["cols"][0]="";
	$filter->css=$t;
	$filter->select_original="table_filter_tempo.empr_nb,empr_mail";
	$filter->original_query="select id_empr,count(pret_idexpl) as empr_nb from empr,pret where pret_retour<CURDATE() and pret_idempr=id_empr group by empr.id_empr";
	$filter->from_original="";
	$filter->activate_filters();
	if (!$filter->error) {
		$aff_filters="<script type='text/javascript' src='./javascript/tablist.js'></script><form class='form-$current_module' id='form_filters' name='form_filters' method='post' action='".$PHP_SELF."?categ=relance&sub=todo'><h3>".$msg["filters_tris"]."</h3>";
		$aff_filters.="<div class='form-contenu'><div id=\"el1Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" name=\"imEx\" class=\"img_plus\" id=\"el1Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el1', true); return false;\">
   								<b>".$msg["filters"]."</b></div>
						<div id=\"el1Child\" style=\"margin-left:7px;display:none;\">";
		$aff_filters.=$filter->display_filters();
		$aff_filters.="</div><div class='row'></div><div id=\"el2Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" name=\"imEx\" class=\"img_plus\" id=\"el2Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el2', true); return false;\">
							<b>".$msg["tris_dispos"]."</b></div>
							<div id=\"el2Child\" style=\"margin-left:7px;display:none;\">";
		$aff_filters.=$filter->display_sort();
		$aff_filters.="</div></div><div class='row'></div><input type='submit' class='bouton' value='".$msg["empr_sort_filter_button"]."'></form>";
		$aff_filters.=$filter->make_human_filters();
		$aff_filters.="<script>
						function envoi() {
							var formulaire=document.form_filters;
							var j=0;
							for (i=0;i<formulaire.elements.length;i++) {
								var values=new Array();
								if (formulaire.elements[i].type=='select-multiple') {
									for (j=0; j<formulaire.elements[i].options.length; j++) {
										if (formulaire.elements[i].options[j].selected) {
											values[values.length]=formulaire.elements[i].options[j].value;
										}
									}
								} else values[0]=formulaire.elements[i].value;
								if (values.length) {
									for (j=0; j<values.length; j++) {
										var nouvelelement=document.createElement('input');
										nouvelelement.setAttribute('type','hidden');
										nouvelelement.setAttribute('name',formulaire.elements[i].name);
										nouvelelement.value=values[j];
										document.relance_action.appendChild(nouvelelement);	
									}
								}
							}
							document.relance_action.submit();
						}
					</script>";
		print $aff_filters;
		if ($all_level) {
			$pos=strpos($filter->query,"where");
			$requete=substr($filter->query,0,$pos+6);
			$requete.=$filter->params["REFERENCE"][0]['value'].".".$filter->params["REFERENCEKEY"][0]['value']." in (".implode(",",array_keys($all_level)).") and ";
			$requete.=substr($filter->query,$pos+6,strlen($filter->query)-($pos+6));
		} else $requete=$filter->query;
		$colonnes=$filter->display_columns();
		$script="envoi();";
	} else print $filter->error_message;
} else {
	$script="this.form.submit();";
	$colonnes="<th>".$msg["relance_code_empr"]."</th><th>".$msg["relance_name_empr"]."</th><th>".$msg["59"]."</th><th>".$msg["codestat_empr"]."</th><th>".$msg["groupe_empr"]."</th>";
}
echo "<form name='relance_action' action='./circ.php?categ=relance&sub=todo' method='post'>
<input type='hidden' name='relance_solo' value=''/>
<input type='hidden' name='act' value=''/>
<input type='hidden' name='printed_cd' value=''/>";

echo "<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<table style='width:100%' class='sortable'>";
echo "<tr>".$colonnes."<th>".$msg["relance_nb_retard"]."</th><th>".$msg["relance_dernier_niveau"]."</th><th>".$msg["relance_date_derniere"]."</th><th>".$msg["relance_imprime"]."</th><th>".$msg["relance_niveau_suppose"]."</th><th>".$msg["relance_action_prochaine"]."</th><th>&nbsp;</th></tr>";

$resultat=pmb_mysql_query($requete);
$pair=false;
//Nombre de relances à faire
$nb_relances = 0;
$list_dates_sort = array();
$list_dates_relance = array();
while ($r=pmb_mysql_fetch_array($resultat)) {
	if (!$pair) $pair_impair = "odd"; else $pair_impair = "even";
	$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
	if ($all_level[$r["id_empr"]]) $level=$all_level[$r["id_empr"]];	
	else {
		$amende=new amende($r["id_empr"]);
		$level=$amende->get_max_level();
	}
	if (($level["level_normal"])||($level["level_min"])) {
		$pair=!$pair;
		print "<tr id='relance_empr_".$r["id_empr"]."' class='$pair_impair' $tr_javascript>";
		print "<td>".htmlentities($r["empr_cb"],ENT_QUOTES,$charset)."</td>";
		print "<td><a href='./circ.php?categ=pret&id_empr=".$r["id_empr"]."'>".htmlentities($r["empr_nom"]." ".$r["empr_prenom"],ENT_QUOTES,$charset)."</a></td>";
		print "<td>".htmlentities($r["libelle_categ"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["libelle_codestat"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["group_name"],ENT_QUOTES,$charset)."</td>";
		print "<td>".htmlentities($r["empr_nb"],ENT_QUOTES,$charset)."</td>";
		$niveau=$level["level"];
		$niveau_min=$level["level_min"];
		$niveau_normal=$level["level_normal"];
		$printed=$level["printed"];
		$date_relance=$level["level_min_date_relance"];
		$list_dates[$date_relance]=format_date($date_relance);
		if ($printed) {
			$list_dates_relance[$date_relance]=$list_dates[$date_relance];
			$dr=explode("-",$date_relance);
			$list_dates_sort[$date_relance]=mktime(0,0,0,$dr[1],$dr[2],$dr[0]);
		}
		//Tri des dates
		if (count($list_dates_sort)) {
			arsort($list_dates_sort);
		}
		print "<td>$niveau_min</td>";
		print "<td>".$list_dates[$date_relance]."</td>";
		print "<td>".($printed?"x":"")."</td>";
		print "<td>$niveau_normal</td>";
		print "<td>".get_action($r["id_empr"],$niveau_min,$niveau_normal)."</td>";
		print "<td><input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$r["id_empr"]."'; this.form.act.value='solo'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>&nbsp;";
		
		//Si mail de rappel affecté au responsable du groupe
		$requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$r["id_empr"]." and resp_groupe and mail_rappel limit 1";
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res) > 0) {
			$requete="select id_empr, empr_mail from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
			$result=pmb_mysql_query($requete);
			$has_mail = (pmb_mysql_result($result, 0,1) ? 1 : 0);
		} else {
			$has_mail = ($r["empr_mail"] ? 1 : 0); 
		}

		if ($niveau_min) {
			print "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$r["id_empr"]."&niveau=".$niveau_min."','lettre'); this.form.act.value='solo_print'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>";
			$flag_mail=false;
			if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($has_mail))&&(($niveau_min<3)||($mailretard_priorite_email_3==1 && $niveau_min>=3))) {
				$flag_mail=true;
				if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
					//On force en lettre
					$flag_mail=false;
				}
			}			
			if ($flag_mail) {
				print "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$r["id_empr"]."'; this.form.act.value='solo_mail'; this.form.relance_solo.value='".$r["id_empr"]."'; $script\"/>";
			}
		}
		print "</td>";
		print "</tr>\n";
		$nb_relances++;
	}
}
echo "</table>";
print "<div class='right'>";
print "<input type='button' class='bouton' value='".$msg["relance_valid_all"]."' onClick=\"this.form.act.value='valid'; this.form.relance_solo.value=''; $script\"/>&nbsp;";
print "<input type='button' class='bouton' value='".$msg["relance_print_nonprinted"]."' onClick=\"this.form.act.value='print'; this.form.relance_solo.value=''; $script\"/>&nbsp;";
print "<input type='button' class='bouton' value='".$msg["relance_export"]."' onClick=\"this.form.act.value='export'; this.form.relance_solo.value=''; $script\"/>&nbsp;";

if (count($list_dates_relance)) {
	print "<input type='button' value='".addslashes($msg["print_relance_clear"])."' onClick=\"if (confirm('".sprintf(addslashes($msg["confirm_print_relance_clear"]),"'+this.form.clear_date.options[this.form.clear_date.selectedIndex].text+' ?'").")) { this.form.act.value='raz_printed'; this.form.printed_cd.value=this.form.clear_date.options[this.form.clear_date.selectedIndex].value; $script }\" class='bouton'/>&nbsp;<select name='clear_date'>";
	print "<option value=''>".$msg["print_relance_clear_all"]."</option>\n";
	foreach ($list_dates_sort as $val=>$stamp) {
		$lib=$list_dates_relance[$val];
		print "<option value='$val'>".$lib."</option>\n";
	}
	print "</select>";
}
print "</div></form>";
print "<script type='text/javascript'>document.getElementById('nb_relance_to_do').innerHTML='(".$nb_relances.")';</script>";
if($progress_bar)$progress_bar->hide();
?>