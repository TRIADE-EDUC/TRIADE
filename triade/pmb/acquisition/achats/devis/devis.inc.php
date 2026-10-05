<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: devis.inc.php,v 1.64 2023/08/02 07:36:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id, $id_dev, $id_exer, $id_bibli, $class_path, $include_path, $base_path, $msg, $charset, $chk, $sugchk, $by_mail;

if(!isset($id)) {
	$id = 0;
}
$id = intval($id);
if(!isset($id_dev)) {
	$id_dev = 0;
}
$id_dev=intval($id_dev);
if(!isset($id_exer)) {
	$id_exer = 0;
}
$id_exer = intval($id_exer);
if(!isset($id_bibli)) {
	$id_bibli = 0;
}
$id_bibli = intval($id_bibli);

// gestion des devis
require_once("$class_path/entites.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/liens_actes.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$include_path/templates/actes.tpl.php");
require_once("$include_path/templates/devis.tpl.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");
require_once("$class_path/suggestions.class.php");
require_once("$class_path/suggestions_map.class.php");
require_once("$base_path/acquisition/suggestions/func_suggestions.inc.php");
require_once ("$class_path/notice.class.php");
require_once("$class_path/sel_display.class.php");
require_once("$class_path/lettre_devis.class.php");
require_once($class_path.'/accounting/accounting_devis_controller.class.php');
require_once("$class_path/user.class.php");

//Affiche la liste des devis pour un etablissement
function show_list_dev($id_bibli) {
	global $accounting_devis_ui_user_input;
	global $accounting_devis_ui_status;

	$filters = array();
	$filters['user_input'] = stripslashes($accounting_devis_ui_user_input ?? "");
	$filters['status'] = $accounting_devis_ui_status;

	$list_accounting_devis_ui = new list_accounting_devis_ui($filters);
	print $list_accounting_devis_ui->get_display_list();
}

//Affiche le formulaire de création/modification de devis
function show_dev($id_bibli, $id_dev) {
	global $msg, $charset;
	global $modif_dev_form,  $bt_enr, $bt_dup, $bt_sup, $bt_cde, $bt_imp;
	global $pmb_gestion_devise;
	global $PMBuserid;
	global $pmb_type_audit, $bt_audit;

	//Recuperation etablissement
	$bibli = new entites($id_bibli);
	$lib_bibli = htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset);

	//Prise en compte des adresses utilisateurs par défaut
	$tab1 = explode('|', user::get_param($PMBuserid, 'speci_coordonnees_etab'));
	$tab_adr=array();
	foreach ($tab1 as $v) {
		$tab2=explode(',', $v);
		$tab_adr[$tab2[0]]['id_adr_fac']=$tab2[1];
		$tab_adr[$tab2[0]]['id_adr_liv']=$tab2[2];
	}
	$def_id_adr_fac = 0;
	$def_id_adr_liv = 0;
	if(isset($tab_adr[$id_bibli]['id_adr_fac'])) {
		$def_id_adr_fac=$tab_adr[$id_bibli]['id_adr_fac'];
	}
	if(isset($tab_adr[$id_bibli]['id_adr_liv'])) {
		$def_id_adr_liv=$tab_adr[$id_bibli]['id_adr_liv'];
	}

	$form = $modif_dev_form;

	if(!$id_dev) {	//nouveau devis
		$titre = htmlentities($msg['acquisition_dev_cre'], ENT_QUOTES, $charset);
		$date_cre = formatdate(today());
		//$numero = calcNumero($id_bibli, TYP_ACT_DEV);
		$statut = STA_ACT_ENC;
		$sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
		$sel_statut.=htmlentities($msg['acquisition_dev_enc'], ENT_QUOTES, $charset);
		$id_fou = '0';
		$lib_fou = '';
		$id_adr_fou = '0';
		$adr_fou = '';
		if ($def_id_adr_fac) {
			$id_adr_fac = $def_id_adr_fac;
			$coord = new coordonnees($def_id_adr_fac);
		} else {
			$coord_fac = entites::get_coordonnees($id_bibli, '1');
			if (pmb_mysql_num_rows($coord_fac) != 0) {
				$coord = pmb_mysql_fetch_object($coord_fac);
				$id_adr_fac = $coord->id_contact;
			} else {
				$id_adr_fac='0';
			}
		}
		if ($id_adr_fac) {
			$adr_fac = coordonnees::get_formatted_address_form_coord($coord);
		} else {
			$adr_fac = '';
		}
		if ($def_id_adr_liv) {
			$id_adr_liv = $def_id_adr_liv;
			$coord = new coordonnees($def_id_adr_liv);
		} else {
			$coord_liv = entites::get_coordonnees($id_bibli, '2');
			if (pmb_mysql_num_rows($coord_liv) != 0) {
				$coord = pmb_mysql_fetch_object($coord_liv);
				$id_adr_liv = $coord->id_contact;
			} else {
				$id_adr_liv='0';
			}
		}
		if ($id_adr_liv) {
			$adr_liv = coordonnees::get_formatted_address_form_coord($coord);
		} else {
			$id_adr_liv = $id_adr_fac;
			$adr_liv = $adr_fac;
		}
		$comment = '';
		$comment_i = '';
		$liens_cde = '';
		$ref = '';
		$devise = $pmb_gestion_devise;

		$bt_dup='';
		$bt_cde='';
		$bt_imp = '';
		$bt_audit = '';
		$bt_sup = '';
		$numero = '';
		$lignes = array(0=>0, 1=>'');

	} else {		// modification de devis

		$dev = new actes($id_dev);

		$titre = htmlentities($msg['acquisition_dev_mod'], ENT_QUOTES, $charset);
		$date_cre = formatdate($dev->date_acte);
		$numero = htmlentities($dev->numero, ENT_QUOTES, $charset);
		$statut = $dev->statut;
		if (($statut & STA_ACT_ARC) == STA_ACT_ARC) {
			$statut=STA_ACT_ARC;
		}

		//Creation selecteur statut
		$sel_statut = "<select class='saisie-25em' id='statut' name='statut' >";
		$list_statut = actes::getStatelist(TYP_ACT_DEV, false);
		foreach($list_statut as $k=>$v){
			$sel_statut.="<option value='".$k."'>".htmlentities($v, ENT_QUOTES, $charset)."</option>";
		}
		$sel_statut.= "</select>";
		$id_fou = $dev->num_fournisseur;
		$fou = new entites($id_fou);
		$lib_fou = htmlentities($fou->raison_sociale, ENT_QUOTES, $charset);
		$coord = entites::get_coordonnees($fou->id_entite, '1');
		if (pmb_mysql_num_rows($coord) != 0) {
			$coord = pmb_mysql_fetch_object($coord);
			$id_adr_fou = $coord->id_contact;
			$adr_fou = coordonnees::get_formatted_address_form_coord($coord);
		} else {
			$id_adr_fou = '0';
			$adr_fou = '';
		}
		$id_adr_fac = $dev->num_contact_fact;
		if ($id_adr_fac) {
			$coord_fac = new coordonnees($id_adr_fac);
			$adr_fac = $coord_fac->get_formatted_address();
		} else {
			$id_adr_fac = '0';
			$adr_fac = '';
		}
		$id_adr_liv = $dev->num_contact_livr;
		if ($id_adr_liv) {
			$coord_liv = new coordonnees($id_adr_liv);
			$adr_liv = $coord_liv->get_formatted_address();
		} else {
			$id_adr_liv = '0';
			$adr_liv = '';
		}
		$comment = htmlentities($dev->commentaires, ENT_QUOTES, $charset);
		$comment_i = htmlentities($dev->commentaires_i, ENT_QUOTES, $charset);
		$tab_liens = liens_actes::getChilds($id_dev);
		$liens_cde = '';
		while (($row_liens = pmb_mysql_fetch_object($tab_liens))) {
			if( ($row_liens->type_acte) == TYP_ACT_CDE ) {
				$liens_cde.= "<br /><a href=\"./acquisition.php?categ=ach&sub=cmde&action=modif&id_bibli=".$id_bibli."&id_cde=".$row_liens->num_acte_lie."\">".$row_liens->numero."</a>";
			}
		}
		$ref = htmlentities($dev->reference, ENT_QUOTES, $charset);
		$devise = htmlentities($dev->devise, ENT_QUOTES, $charset);

		if (!$pmb_type_audit) {
			$bt_audit = '';
		}
		$lignes = show_lig_dev($id_dev);
	}

	//complement formulaire
	$form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
	$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
	$form = str_replace('<!-- bouton_dup -->', $bt_dup, $form);
	$form = str_replace('<!-- bouton_cde -->', $bt_cde, $form);
	$form = str_replace('<!-- bouton_imp -->', $bt_imp, $form);
	$form = str_replace('<!-- bouton_audit -->', $bt_audit, $form);
	$form = str_replace('<!-- bouton_sup -->', $bt_sup, $form);
	$form = str_replace('!!act_nblines!!', $lignes[0], $form);
	$form = str_replace('<!-- lignes -->', $lignes[1], $form);

	//Remplissage formulaire
	$form = str_replace('!!form_title!!', $titre, $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', $lib_bibli, $form);
	$form = str_replace('!!id_dev!!', $id_dev, $form);
	$form = str_replace('!!date_cre!!', $date_cre, $form);
	$form = str_replace('!!numero!!', $numero, $form);
	$form = str_replace('!!statut!!', $statut, $form);
	$form = str_replace('!!id_fou!!', $id_fou, $form);
	$form = str_replace('!!lib_fou!!', $lib_fou, $form);
	$form = str_replace('!!id_adr_fou!!', $id_adr_fou, $form);
	$form = str_replace('!!adr_fou!!', $adr_fou, $form);
	$form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
	$form = str_replace('!!adr_liv!!', $adr_liv, $form);
	$form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
	$form = str_replace('!!adr_fac!!', $adr_fac, $form);
	$form = str_replace('!!comment!!', $comment, $form);
	$form = str_replace('!!comment_i!!', $comment_i, $form);
	$form = str_replace('!!ref!!', $ref, $form);
	$form = str_replace('!!devise!!', $devise, $form);
	$form = str_replace('!!liens_cde!!', $liens_cde, $form);

	print $form;
}

//Affiche les lignes d'un devis
function show_lig_dev($id_dev) {
	global $charset;
	global $acquisition_gestion_tva;
	global $modif_dev_row_form;

	$form = "";
	$i=0;
	if (!$id_dev) {
		$t = array(0=>$i, $form);
		return $t;
	}
	$lignes = actes::getLignes($id_dev);
	while (($row = pmb_mysql_fetch_object($lignes))) {
		$i++;
		$form.= $modif_dev_row_form;

		$form = str_replace('!!no!!', $i, $form);
		$form = str_replace('!!code!!', htmlentities($row->code, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!lib!!', htmlentities($row->libelle, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!qte!!', $row->nb, $form);
		$form = str_replace('!!prix!!', $row->prix, $form);
		if ($row->num_type) {
			$tp = new types_produits($row->num_type);
			$form = str_replace('!!typ!!', $tp->id_produit, $form);
			$form = str_replace('!!lib_typ!!', htmlentities($tp->libelle, ENT_QUOTES, $charset), $form);
		} else {
			$form = str_replace('!!typ!!', '0', $form);
			$form = str_replace('!!lib_typ!!', '', $form);
		}
		if ($acquisition_gestion_tva) {
			$form = str_replace('!!tva!!', $row->tva , $form);
		}
		$form = str_replace('!!rem!!', $row->remise, $form);
		$form = str_replace('!!id_sug!!', $row->num_acquisition, $form);
		$form = str_replace('!!id_lig!!', $row->id_ligne, $form);
		$form = str_replace('!!typ_lig!!', $row->type_ligne, $form);
		$form = str_replace('!!id_prod!!', $row->num_produit, $form);

	}
	$t = array(0=>$i, 1=>$form);
	return $t;
}

//Affiche la liste des etablissements pour choix depuis suggestions
function show_list_biblio_from_sug($sugchk) {
	global $msg, $charset;
	global $tab_bib, $nb_bib;
	global $current_module;
	$sugchk = rawurlencode(serialize($sugchk));

	//Affiche la liste des etablissements auxquels a acces l'utilisateur si > 1
	if ($nb_bib == '1') {
		show_dev_from_sug($tab_bib[0][0], $sugchk);
		exit;
	}

	$def_bibli=entites::getSessionBibliId();
	if (in_array($def_bibli, $tab_bib[0])) {
		show_dev_from_sug($def_bibli, $sugchk);
		exit;
	}

	$aff = "<form class='form-".$current_module."' id='list_biblio_form' name='list_biblio_form' method='post' action=\"\" >";
	$aff.= "<input type='hidden' id='sugchk' name='sugchk' value='".$sugchk."' />";
	$aff.= "<h3>".htmlentities($msg['acquisition_menu_chx_ent'], ENT_QUOTES, $charset)."</h3><div class='row'></div>";
	$aff.= "<table>";

	$parity=1;
	foreach($tab_bib[0] as $k=>$v) {
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" onmousedown=\"document.forms['list_biblio_form'].setAttribute('action','./acquisition.php?categ=ach&sub=devi&action=from_sug_next&id_bibli=".$v."');document.forms['list_biblio_form'].submit(); \" ";
		$aff.= "<tr class='".$pair_impair."' ".$tr_javascript." style='cursor: pointer'><td><i>".htmlentities($tab_bib[1][$k], ENT_QUOTES, $charset)."</i></td></tr>";
	}
	$aff.= "</table></form>";
	print $aff;
}

//Affiche le formulaire de creation de devis depuis suggestions
function show_dev_from_sug($id_bibli, $sugchk) {
	global $msg, $charset;
	global $modif_dev_form, $bt_enr;
	global $pmb_gestion_devise;
	global $PMBuserid;

	//Recuperation etablissement
	$bibli = new entites($id_bibli);
	$lib_bibli = htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset);

	//Prise en compte des adresses utilisateurs par défaut
	$tab1 = explode('|', user::get_param($PMBuserid, 'speci_coordonnees_etab'));
	$tab_adr=array();
	foreach ($tab1 as $v) {
		$tab2=explode(',', $v);
		$tab_adr[$tab2[0]]['id_adr_fac']=$tab2[1];
		$tab_adr[$tab2[0]]['id_adr_liv']=$tab2[2];
	}
	$def_id_adr_fac=(isset($tab_adr[$id_bibli]['id_adr_fac']) ? $tab_adr[$id_bibli]['id_adr_fac'] : '');
	$def_id_adr_liv=(isset($tab_adr[$id_bibli]['id_adr_liv']) ? $tab_adr[$id_bibli]['id_adr_liv'] : '');

	$form = $modif_dev_form;

	//$numero = calcNumero($id_bibli, TYP_ACT_DEV);
	$statut = STA_ACT_ENC;
	$sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
	$sel_statut.=htmlentities($msg['acquisition_dev_enc'], ENT_QUOTES, $charset);
	if ($def_id_adr_fac) {
		$id_adr_fac = $def_id_adr_fac;
		$coord = new coordonnees($def_id_adr_fac);
	} else {
		$coord_fac = entites::get_coordonnees($id_bibli, '1');
		if (pmb_mysql_num_rows($coord_fac) != 0) {
			$coord = pmb_mysql_fetch_object($coord_fac);
			$id_adr_fac = $coord->id_contact;
		} else {
			$id_adr_fac='0';
		}
	}
	if ($id_adr_fac) {
		$adr_fac = coordonnees::get_formatted_address_form_coord($coord);
	} else {
		$adr_fac = '';
	}

	if ($def_id_adr_liv) {
		$id_adr_liv = $def_id_adr_liv;
		$coord = new coordonnees($def_id_adr_liv);
	} else {
		$coord_liv = entites::get_coordonnees($id_bibli, '2');
		if (pmb_mysql_num_rows($coord_liv) != 0) {
			$coord = pmb_mysql_fetch_object($coord_liv);
			$id_adr_liv = $coord->id_contact;
		} else {
			$id_adr_liv='0';
		}
	}
	if ($id_adr_liv) {
		$adr_liv = coordonnees::get_formatted_address_form_coord($coord);
	} else {
		$id_adr_liv = $id_adr_fac;
		$adr_liv = $adr_fac;
	}
	$lignes = show_lig_dev_from_sug($sugchk);

	$id_dev=0;

	//complement formulaire
	$form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
	$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
	$form = str_replace('<!-- bouton_dup -->', '', $form);
	$form = str_replace('<!-- bouton_cde -->', '', $form);
	$form = str_replace('<!-- bouton_imp -->', '', $form);
	$form = str_replace('<!-- bouton_audit -->', '', $form);
	$form = str_replace('!!act_nblines!!', $lignes[0], $form);
	$form = str_replace('<!-- lignes -->', $lignes[1], $form);

	//Remplissage formulaire
	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_dev_cre'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', $lib_bibli, $form);
	$form = str_replace('!!id_dev!!', $id_dev, $form);
	$form = str_replace('!!date_cre!!', formatdate(today()), $form);
	$form = str_replace('!!numero!!', "", $form);
	$form = str_replace('!!statut!!', $statut, $form);
	$form = str_replace('!!id_fou!!', 0, $form);
	$form = str_replace('!!lib_fou!!', '', $form);
	$form = str_replace('!!id_adr_fou!!', 0, $form);
	$form = str_replace('!!adr_fou!!', '', $form);
	$form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
	$form = str_replace('!!adr_liv!!', $adr_liv, $form);
	$form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
	$form = str_replace('!!adr_fac!!', $adr_fac, $form);
	$form = str_replace('!!comment!!', '', $form);
	$form = str_replace('!!comment_i!!', '', $form);
	$form = str_replace('!!ref!!', '', $form);
	$form = str_replace('!!devise!!', $pmb_gestion_devise, $form);
	$form = str_replace('!!liens_cde!!', '', $form);

	print $form;
}

//Affiche les lignes de devis depuis les suggestions
function show_lig_dev_from_sug($sugchk) {
	global $charset;
	global $acquisition_gestion_tva;
	global $modif_dev_row_form;

	$form = "";
	$i=0;

	$arrchk = unserialize(rawurldecode(stripslashes($sugchk)));
	foreach($arrchk as $value) {
		$i++;

		$sug = new suggestions($value);
		$form.=$modif_dev_row_form;

		$code="";
		$taec="";
		$prix='0';
		$nb='none';

		if ($sug->num_notice) {
			$q = "select niveau_biblio from notices where notice_id='".$sug->num_notice."' ";
			$r = pmb_mysql_query($q);
			if(pmb_mysql_num_rows($r)) {
				$nb=pmb_mysql_result($r,0,0);
			}
		}
		switch($nb) {
			case 'a' :
				$typ_lig = 1;
				$notice=new sel_article_display($sug->num_notice,'');
				$notice->getData();
				$notice->responsabilites = get_notice_authors($sug->num_notice);
				$notice->doHeader();
				$taec= $notice->titre;
				if($notice->auteur1) {
					$taec.="\n".$notice->auteur1;
				}
				if($notice->in_bull) {
					$taec.="\n".$notice->in_bull;
				}
				$prix=$notice->prix;
				break;
			case 'm' :
				$typ_lig = 1;
				$notice=new sel_mono_display($sug->num_notice,'');
				$notice->getData();
				$notice->responsabilites = get_notice_authors($sug->num_notice);
				$notice->doHeader();
				$code = $notice->code;
				$taec= $notice->titre;
				if($notice->auteur1) {
					$taec.="\n".$notice->auteur1;
				}
				if ($notice->editeur1) {
					$taec.= "\n".$notice->editeur1;
				}
				if ($notice->editeur1 && $notice->ed_date) {
					$taec.= ", ".$notice->ed_date;
				} elseif ($notice->ed_date){
					$taec.= $notice->ed_date;
				}
				if ($notice->collection) {
					$taec.= "\n".$notice->collection;
				}
				$prix=$notice->prix;
				break;
			default :
				$typ_lig = 0;
				$code = htmlentities($sug->code, ENT_QUOTES, $charset);
				$taec= htmlentities($sug->titre,ENT_QUOTES,$charset);
				if ($sug->auteur!="") $taec.= "\n".htmlentities($sug->auteur,ENT_QUOTES,$charset);
				if ($sug->editeur != "") $taec.= "\n".htmlentities($sug->editeur,ENT_QUOTES,$charset);
				$prix=htmlentities($sug->prix, ENT_QUOTES, $charset);
				break;
		}

		$form = str_replace('!!no!!', $i, $form);
		$form = str_replace('!!code!!', $code, $form);
		$form = str_replace('!!lib!!', $taec, $form);
		$form = str_replace('!!qte!!', $sug->nb, $form);
		$form = str_replace('!!prix!!', $prix,$form);
		if ($acquisition_gestion_tva) {
			$form = str_replace('!!tva!!', '0.00', $form);
		}
		$form = str_replace('!!typ!!', '0', $form);
		$form = str_replace('!!lib_typ!!', '', $form);
		$form = str_replace('!!rem!!', '0.00', $form);
		$form = str_replace('!!id_sug!!', $sug->id_suggestion, $form);
		$form = str_replace('!!id_lig!!', '0', $form);
		$form = str_replace('!!id_prod!!', $sug->num_notice, $form);
	}
	$t = array(0=>$i, 1=>$form);
	return $t;
}

//Sauvegarde devis
function update_dev() {
	global $id_bibli, $id_dev, $num_dev, $statut;
	global $id_fou;
	global $id_adr_liv, $id_adr_fac;
	global $comment, $comment_i, $ref, $devise;
	global $code, $lib, $qte, $prix, $typ, $tva, $rem, $id_sug, $id_lig, $typ_lig, $id_prod;
	global $acquisition_gestion_tva;

	//Recuperation des lignes valides
	$tab_lig=array();
	if (count($id_lig)){
		foreach($id_lig as $k=>$v) {
			$code[$k] = trim($code[$k]);
			$lib[$k] = trim($lib[$k]);
			if ($code[$k] !='' || $lib[$k]!='') {
				$tab_lig[$k]=$v;
			}
		}
	}
	if (!$id_dev) {		//Creation de devis
		$dev = new actes();
		$dev->type_acte = TYP_ACT_DEV;
		$dev->num_entite = $id_bibli;
		$dev->statut=STA_ACT_ENC;
		$dev->num_fournisseur = $id_fou;
		$dev->num_contact_livr = $id_adr_liv;
		$dev->num_contact_fact = $id_adr_fac;
		$dev->commentaires = trim($comment);
		$dev->commentaires_i = trim($comment_i);
		$dev->reference = trim($ref);
		$dev->devise = trim($devise);
		$dev->save();

		$id_dev= $dev->id_acte;

		//Creation des lignes de devis
		foreach($tab_lig as $k=>$v) {
			$lig_dev = new lignes_actes();
			$lig_dev->type_ligne = $typ_lig[$k];
			$lig_dev->num_acte = $id_dev;
			$lig_dev->num_produit = $id_prod[$k];
			$lig_dev->num_acquisition = $id_sug[$k];
			$lig_dev->num_type = $typ[$k];
			$lig_dev->code = $code[$k];
			$lig_dev->libelle = $lib[$k];
			$lig_dev->prix = $prix[$k];
			if ($acquisition_gestion_tva) {
				$lig_dev->tva = $tva[$k];
			} else {
				$lig_dev->tva = '0.00';
			}
			$lig_dev->remise = $rem[$k];
			$lig_dev->nb = round($qte[$k]);
			$lig_dev->date_cre = today();
			$lig_dev->save();
		}

		//Mise à jour du statut des suggestions et envoi email suivi de suggestion
		$sug_map = new suggestions_map();
		$sug_map->doTransition('ESTIMATED', $id_sug);
	} else {		//Modification de devis
		$dev = new actes($id_dev);
		$old_statut=($dev->statut & ~STA_ACT_ARC);
		if ($old_statut != STA_ACT_ENC && $old_statut != STA_ACT_REC) {
			$old_statut=STA_ACT_ENC;
		}
		if ($statut == STA_ACT_ARC) {
			$rec_statut = ($old_statut | STA_ACT_ARC);
		} else {
			$rec_statut = $statut;
		}
		$dev->statut = $rec_statut;
		$dev->num_fournisseur = $id_fou;
		$dev->num_contact_livr = $id_adr_liv;
		$dev->num_contact_fact = $id_adr_fac;
		$dev->commentaires = trim($comment);
		$dev->commentaires_i = trim($comment_i);
		$dev->reference = trim($ref);
		$dev->devise = trim($devise);
		$dev->save();

		//maj des lignes de devis
		foreach($tab_lig as $k=>$v) {
			$lig_dev = new lignes_actes($v);
			$lig_dev->type_ligne = $typ_lig[$k];
			$lig_dev->num_acte = $id_dev;
			$lig_dev->num_produit = $id_prod[$k];
			$lig_dev->num_acquisition = $id_sug[$k];
			$lig_dev->num_type = $typ[$k];
			$lig_dev->code = $code[$k];
			$lig_dev->libelle = $lib[$k];
			$lig_dev->prix = $prix[$k];
			if ($acquisition_gestion_tva) {
				$lig_dev->tva = $tva[$k];
			} else {
				$lig_dev->tva = '0.00';
			}
			$lig_dev->remise = $rem[$k];
			$lig_dev->nb = round($qte[$k]);
			$lig_dev->date_cre = today();
			$lig_dev->save();
			if($v==0) $tab_lig[$k]=$lig_dev->id_ligne;
		}
		//suppression des lignes non reprises
		$dev->cleanLignes($id_dev, $tab_lig);
	}
}

//Duplication de devis
function duplicate_dev($id_bibli, $id_dev) {
	global $msg, $charset;
	global $modif_dev_form,  $bt_enr;

	$bibli = new entites($id_bibli);

	$form = $modif_dev_form;

	$dev = new actes($id_dev);

	$numero = calcNumero($id_bibli, TYP_ACT_DEV);
	$statut = STA_ACT_ENC;
	$sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
	$sel_statut.= htmlentities($msg['acquisition_dev_enc'], ENT_QUOTES, $charset);
	$id_fou = $dev->num_fournisseur;
	$form = str_replace('!!id_fou!!', $id_fou, $form);
	$fou = new entites($id_fou);
	$lib_fou = htmlentities($fou->raison_sociale, ENT_QUOTES, $charset);
	$coord = entites::get_coordonnees($fou->id_entite, '1');
	if (pmb_mysql_num_rows($coord) != 0) {
		$coord = pmb_mysql_fetch_object($coord);
		$id_adr_fou = $coord->id_contact;
		$adr_fou = coordonnees::get_formatted_address_form_coord($coord);
	} else {
		$id_adr_fou = '0';
		$adr_fou = '';
	}
	$id_adr_fac = $dev->num_contact_fact;
	if ($id_adr_fac) {
		$coord_fac = new coordonnees($id_adr_fac);
		$adr_fac = $coord_fac->get_formatted_address();
	} else {
		$id_adr_fac = '0';
		$adr_fac = '';
	}
	$id_adr_liv = $dev->num_contact_livr;
	if ($id_adr_liv) {
		$coord_liv = new coordonnees($id_adr_liv);
		$adr_liv = $coord_liv->get_formatted_address();
	} else {
		$id_adr_liv = '0';
		$adr_liv = '';
	}

	$lignes = show_lig_dev($id_dev);

	$id_dev=0;

	//complement formulaire
	$form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
	$form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
	$form = str_replace('<!-- bouton_dup -->', '', $form);
	$form = str_replace('<!-- bouton_cde -->', '', $form);
	$form = str_replace('<!-- bouton_imp -->', '', $form);
	$form = str_replace('<!-- bouton_audit -->', '', $form);
	$form = str_replace('<!-- bouton_sup -->', '', $form);
	$form = str_replace('!!act_nblines!!', $lignes[0], $form);
	$form = str_replace('<!-- lignes -->', $lignes[1], $form);

	//Remplissage formulaire
	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_dev_cre'], ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_bibli!!', $id_bibli, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!id_dev!!', $id_dev, $form);
	$form = str_replace('!!date_cre!!', formatdate(today()), $form);
	$form = str_replace('!!numero!!', $numero, $form);
	$form = str_replace('!!statut!!', $statut, $form);
	$form = str_replace('!!id_fou!!', $id_fou, $form);
	$form = str_replace('!!lib_fou!!', $lib_fou, $form);
	$form = str_replace('!!id_adr_fou!!', $id_adr_fou, $form);
	$form = str_replace('!!adr_fou!!', $adr_fou, $form);
	$form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
	$form = str_replace('!!adr_liv!!', $adr_liv, $form);
	$form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
	$form = str_replace('!!adr_fac!!', $adr_fac, $form);
	$form = str_replace('!!comment!!', '', $form);
	$form = str_replace('!!comment_i!!', htmlentities($dev->commentaires_i, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!ref!!', '', $form);
	$form = str_replace('!!devise!!', htmlentities($dev->devise, ENT_QUOTES, $charset), $form);
	$form = str_replace('!!liens_cde!!', '', $form);
	print $form;
}

function print_dev($id_bibli=0, $id_dev=0, $by_mail=false) {
	global $charset, $base_path, $acquisition_pdfdev_print, $msg;
	global $acquisition_pdfdev_obj_mail, $acquisition_pdfdev_text_mail;
	global $acquisition_pdfdev_by_mail,$PMBuseremailbcc;

	if (!($id_bibli && $id_dev)) return;

	$bib = new entites($id_bibli);
	$bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_bibli,1));

	$dev = new actes($id_dev);

	$id_fou = $dev->num_fournisseur;
	$fou = new entites($id_fou);
	$fou_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_fou,1));

	$no_mail=false;
	if ( $by_mail==false || !($acquisition_pdfdev_by_mail && strpos($bib_coord->email,'@') && strpos($fou_coord->email,'@')) ) {
		$no_mail=true;
	} else {
	    $mail_accounting_devis = new mail_accounting_devis();
	    $mail_accounting_devis->set_id_bibli($id_bibli);
	    $mail_accounting_devis->set_id_acte($id_dev);
	    $res_envoi = $mail_accounting_devis->send_mail();
		if (!$res_envoi) {
			$no_mail=true;
		}
		if (!$no_mail) {
		    print "<h3>".sprintf($msg["acquisition_print_emailsucceed"],$mail_accounting_devis->get_dest_mail())."</h3>";
		} else {
		    print "<h3>".sprintf($msg["acquisition_print_emailfailed"],$mail_accounting_devis->get_dest_mail())."</h3>";
		}
	}
	if ($no_mail) {
		print "
			<form name='print_dev' action='pdf.php?pdfdoc=devi' target='lettre' method='post'>
				<input type='hidden' name='id_bibli' value='".$id_bibli."'/>
				<input type='hidden' name='id_dev' value='".$id_dev."'/>
				<script type='text/javascript'>
					openPopUp('','lettre');
					document.print_dev.submit();
				</script>
			</form>";
	}
}

accounting_devis_controller::set_id_bibli($id_bibli);
accounting_devis_controller::set_id_acte($id_dev);
accounting_devis_controller::proceed($id);
