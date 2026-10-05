<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fournisseurs.inc.php,v 1.52.8.2 2025/05/28 06:20:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "fournisseurs.inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $action, $id_bibli, $raison, $id, $num_cp, $max_coord;
global $mod_, $no_, $sel_prod, $force, $paiement, $id_prod, $rem;
global $chk_all_etablissements;

// gestion des coordonnées des fournisseurs
require_once("$class_path/entites.class.php");
require_once("$class_path/paiements.class.php");
require_once("$class_path/frais.class.php");
require_once("$class_path/types_produits.class.php");
require_once("$class_path/offres_remises.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/lignes_actes_statuts.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$include_path/templates/coordonnees.tpl.php");
require_once("$include_path/templates/fournisseurs.tpl.php");


if(isset($id_bibli)) {
    entites::setSessionBibliId($id_bibli);
}
$id_bibli = entites::getSessionBibliId();
$id_bibli = intval($id_bibli);

if(!isset($id)) {
    $id = 0;
}
$id = intval($id);

//Affiche la liste des fournisseurs pour un etablissement
function show_list_coord($id_bibli=0) {
	print list_accounting_suppliers_ui::get_instance(array('entite', $id_bibli))->get_display_list();
}


//Affiche le formulaire d'edition d'un fournisseur
function show_coord_form($id_bibli = 0, $id_fou= 0) {
	global $msg;
	global $charset;
	global $coord_content_form2;
	global $ptab, $script;

	$bibli_raison_sociale = $msg['acquisition_coord_all'];
	$id_bibli = intval($id_bibli);

	if($id_bibli && !$id_fou) {
	    $bibli = new entites($id_bibli);
	    $bibli_raison_sociale = $bibli->raison_sociale;
	}
	$fournisseur = new entites($id_fou);
	if($id_fou) {
	    if($fournisseur->num_bibli) {
	        $bibli = new entites($fournisseur->num_bibli);
	        $bibli_raison_sociale = $bibli->raison_sociale;
	        $id_bibli = $bibli->id_entite;
	    } else {
	        $id_bibli = 0;
	    }
	}

	$content_form = $coord_content_form2;
	$content_form = str_replace('!!id!!', $id_fou, $content_form);

	$interface_form = new interface_acquisition_form('coordform');
	if(!$id_fou){
		$interface_form->set_label($msg['acquisition_ajout_fourn']);
	}else{
		$interface_form->set_label($msg['acquisition_modif_fourn']);
	}

	$ptab[1] = $ptab[1].$ptab[11];
	$ptab[1] = str_replace('!!adresse!!', htmlentities($msg['acquisition_adr_fou'], ENT_QUOTES, $charset), $ptab[1]);
	$ptab[1] = str_replace('!!button_adr_fac!!', '', $ptab[1]);

	if(!$id_fou) {
		//creation
	    $sel_all = TRUE;
	    $sel_attr = [
	        'id'=>'id_bibli',
	        'name'=>'id_bibli',
	    ];
	    $sel_bibli = entites::get_hmtl_select_etablissements(SESSuserid, $id_bibli, $sel_all, $sel_attr);
	    $content_form = str_replace('!!lib_bibli!!', $sel_bibli, $content_form);

		$content_form = str_replace('!!raison!!', '', $content_form);
		$content_form = str_replace('!!num_cp!!', '', $content_form);

		$content_form = str_replace('!!contact!!', $ptab[1], $content_form);
		$content_form = str_replace('!!max_coord!!', '1', $content_form);

		$content_form = str_replace('!!lib_1!!', '', $content_form);
		$content_form = str_replace('!!id1!!', '0', $content_form);
		$content_form = str_replace('!!cta_1!!', '', $content_form);
		$content_form = str_replace('!!ad1_1!!', '', $content_form);
		$content_form = str_replace('!!ad2_1!!', '', $content_form);
		$content_form = str_replace('!!cpo_1!!', '', $content_form);
		$content_form = str_replace('!!vil_1!!', '', $content_form);
		$content_form = str_replace('!!eta_1!!', '', $content_form);
		$content_form = str_replace('!!pay_1!!', '', $content_form);
		$content_form = str_replace('!!te1_1!!', '', $content_form);
		$content_form = str_replace('!!te2_1!!', '', $content_form);
		$content_form = str_replace('!!fax_1!!', '', $content_form);
		$content_form = str_replace('!!ema_1!!', '', $content_form);
		$content_form = str_replace('!!com_1!!', '', $content_form);

		$content_form = str_replace('!!commentaires!!', '', $content_form);
		$content_form = str_replace('!!siret!!', '', $content_form);
		$content_form = str_replace('!!rcs!!', '', $content_form);
		$content_form = str_replace('!!naf!!', '', $content_form);
		$content_form = str_replace('!!tva!!', '', $content_form);
		$content_form = str_replace('!!site_web!!', '', $content_form);
	} else {
    	//modification
		$content_form = str_replace('!!lib_bibli!!', htmlentities($bibli_raison_sociale, ENT_QUOTES, $charset), $content_form);

		if($id_bibli) {
		    $chk_attr = [
		        'id'=>'chk_all_etablissements',
		        'name'=>'chk_all_etablissements',
		        'value'=>'1',
		    ];
		    $chk_checked = FALSE;
		    $chk_all_etablissements = entites::get_html_checkbox_all_etablissements($chk_checked, $chk_attr);
		} else {
		    $chk_all_etablissements = "<input type='hidden' id='chk_all_etablissements' name='chk_all_etablissements' value='1' />";
		}
		$content_form = str_replace('<!-- chk_all_etablissements -->', $chk_all_etablissements, $content_form);

		$content_form = str_replace('!!raison!!', htmlentities($fournisseur->raison_sociale,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!num_cp!!',htmlentities($fournisseur->num_cp_client, ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!contact!!', $ptab[1], $content_form);

		$row = pmb_mysql_fetch_object(entites::get_coordonnees($fournisseur->id_entite,'1'));
		if($row) {
			$content_form = str_replace('!!id1!!', $row->id_contact, $content_form);
			$content_form = str_replace('!!lib_1!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!cta_1!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ad1_1!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ad2_1!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!cpo_1!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!vil_1!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!eta_1!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!pay_1!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te1_1!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te2_1!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!fax_1!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ema_1!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!com_1!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);
		} else{
			$content_form = str_replace('!!lib_1!!', '', $content_form);
			$content_form = str_replace('!!id1!!', '0', $content_form);
			$content_form = str_replace('!!cta_1!!', '', $content_form);
			$content_form = str_replace('!!ad1_1!!', '', $content_form);
			$content_form = str_replace('!!ad2_1!!', '', $content_form);
			$content_form = str_replace('!!cpo_1!!', '', $content_form);
			$content_form = str_replace('!!vil_1!!', '', $content_form);
			$content_form = str_replace('!!eta_1!!', '', $content_form);
			$content_form = str_replace('!!pay_1!!', '', $content_form);
			$content_form = str_replace('!!te1_1!!', '', $content_form);
			$content_form = str_replace('!!te2_1!!', '', $content_form);
			$content_form = str_replace('!!fax_1!!', '', $content_form);
			$content_form = str_replace('!!ema_1!!', '', $content_form);
			$content_form = str_replace('!!com_1!!', '', $content_form);
		}
		$liste_coord = entites::get_coordonnees($fournisseur->id_entite,'0');
		$content_form = str_replace('!!max_coord!!', (pmb_mysql_num_rows($liste_coord)+1), $content_form);
		$i=2;
		while ($row = pmb_mysql_fetch_object($liste_coord)) {

			$content_form = str_replace('<!--coord_repetables-->', $ptab[2].'<!--coord_repetables-->', $content_form);
			$content_form = str_replace('!!no_X!!', $i, $content_form);
			$i++;
			$content_form = str_replace('!!idX!!', $row->id_contact, $content_form);
			$content_form = str_replace('!!lib_X!!', htmlentities($row->libelle,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!cta_X!!', htmlentities($row->contact,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ad1_X!!', htmlentities($row->adr1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ad2_X!!', htmlentities($row->adr2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!cpo_X!!', htmlentities($row->cp,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!vil_X!!', htmlentities($row->ville,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!eta_X!!', htmlentities($row->etat,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!pay_X!!', htmlentities($row->pays,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te1_X!!', htmlentities($row->tel1,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!te2_X!!', htmlentities($row->tel2,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!fax_X!!', htmlentities($row->fax,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!ema_X!!', htmlentities($row->email,ENT_QUOTES,$charset), $content_form);
			$content_form = str_replace('!!com_X!!', htmlentities($row->commentaires,ENT_QUOTES,$charset), $content_form);

		}
		$content_form = str_replace('!!commentaires!!', htmlentities($fournisseur->commentaires,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!siret!!', htmlentities($fournisseur->siret,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!rcs!!', htmlentities($fournisseur->rcs,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!naf!!', htmlentities($fournisseur->naf,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!tva!!', htmlentities($fournisseur->tva,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!site_web!!', htmlentities($fournisseur->site_web,ENT_QUOTES, $charset), $content_form);
	}

	print $script;
	$interface_form->set_object_id($id_fou)
	->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$fournisseur->raison_sociale." ?")
	->set_content_form($content_form)
	->set_table_name('entites')
	->set_field_focus('raison');
	print $interface_form->get_display();
}

//Affiche la liste des conditions fournisseurs
function show_list_cond($id_fou) {
	global $msg;
	global $charset;
	global $cond_form;
	global $frame, $bt_add;

	$id_fou = intval($id_fou);
	if(!$id_fou) {
	    return;
	}

	$bibli_raison_sociale = $msg['acquisition_coord_all'];
	$fournisseur = new entites($id_fou);
	if($fournisseur->num_bibli) {
	    $bibli = new entites($fournisseur->num_bibli);
	    $bibli_raison_sociale = $bibli->raison_sociale;
	}

	$cond_form = str_replace('!!id!!', $id_fou, $cond_form);
	$cond_form = str_replace('!!lib_bibli!!', htmlentities($bibli_raison_sociale, ENT_QUOTES, $charset), $cond_form);

	$fourn = new entites($id_fou);
	$cond_form = str_replace('!!raison!!', htmlentities($fourn->raison_sociale ,ENT_QUOTES,$charset), $cond_form);

	$cond_form = str_replace('!!form_title!!', htmlentities($msg['acquisition_cond_fourn'],ENT_QUOTES,$charset), $cond_form);

	$cond_form = str_replace('!!raison_suppr!!', htmlentities($fourn->raison_sociale,ENT_QUOTES, $charset), $cond_form);

	//Conditions de paiement
	$list_paie = paiements::listPaiements();
	$form_paie = "<select name='paiement' id='paiement'>";
	$form_paie.= "<option value='0' ";
	if (!$id_fou || !$fourn->num_paiement) $form_paie.= "selected='selected' ";
	$form_paie.= ">".htmlentities($msg['acquisition_fou_select'], ENT_QUOTES, $charset)."</option>";
	while ($row = pmb_mysql_fetch_object($list_paie)) {
		$form_paie.="<option value='".$row->id_paiement."' ";
		if ($fourn->num_paiement == $row->id_paiement) $form_paie.="selected='selected' ";
		$form_paie.= ">".htmlentities($row->libelle, ENT_QUOTES, $charset)."</option>";
	}
	$form_paie.= "</select>";
	$cond_form = str_replace('<!-- paiements -->', $form_paie, $cond_form);


	//offres de remises par types de produits
	$list_cond = entites::listOffres($id_fou);
	$list_no_cond = entites::listNoOffres($id_fou);

	// affichage des offres déjà saisies
	$lig = "";
	$i = 1;
	$parity=1;
	while($row=pmb_mysql_fetch_object($list_cond)){
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
		$dn_javascript=" onmousedown=\"document.forms['condform'].setAttribute('action', './acquisition.php?categ=ach&sub=fourn&action=modrem&id=".$id_fou."&id_prod=".$row->id_produit."'); document.forms['condform'].submit(); \" ";
        $lig.= "<tr class='".$pair_impair."' ".$tr_javascript." style='cursor: pointer' title='".htmlentities($row->condition_remise, ENT_QUOTES, $charset)."'>
					<td ".$dn_javascript.">".htmlentities($row->libelle, ENT_QUOTES, $charset)."</td>
					<td ".$dn_javascript." >
						<input type='hidden' id='idprod[".$i."]' name='idprod[".$i."]' value='".$row->id_produit."' />
						".$row->remise."&nbsp;%
					</td>
				</tr>";
	}

	$frame = str_replace('<!-- frames_rows -->', $lig, $frame);

	//Affichage bouton ajout remise
	if (pmb_mysql_num_rows($list_no_cond) != '0') {
	    $bt_add = str_replace('!!id_fou!!', $id_fou, $bt_add);
		$cond_form = str_replace('<!-- bt_add -->', $bt_add, $cond_form);
	}
	$cond_form = str_replace('<!-- frame -->', $frame , $cond_form);

	echo $cond_form;
}

function show_list_rel($id_fou) {
	global $msg, $charset;
	global $histrel_form, $histrel_hrow_form, $histrel_row_form;
	global $acquisition_gestion_tva;

	$id_fou = intval($id_fou);
	if(!$id_fou) {
	    return;
	}

	$bibli_raison_sociale = $msg['acquisition_coord_all'];
	$fournisseur = new entites($id_fou);
	if($fournisseur->num_bibli) {
	    $bibli = new entites($fournisseur->num_bibli);
	    $bibli_raison_sociale = $bibli->raison_sociale;
	}

	$tab_rel=array();
	$tab_rel = lignes_actes::getRelancesBySupplier($id_fou);

	$form = $histrel_form;

	$form = str_replace('!!form_title!!', htmlentities($msg['acquisition_hist_rel_fou'], ENT_QUOTES,$charset), $form);

	$form = str_replace('!!id!!', $id_fou, $form);
	$form = str_replace('!!lib_bibli!!', htmlentities($bibli_raison_sociale, ENT_QUOTES, $charset), $form);

	$fourn = new entites($id_fou);
	$form = str_replace('!!raison!!', htmlentities($fourn->raison_sociale ,ENT_QUOTES,$charset), $form);

	$date_rel='';
	$lg_form='';
	$i=0;
	foreach ($tab_rel as $rel) {
		$i++;
		if($rel['date_rel']!=$date_rel) {
			$date_rel = $rel['date_rel'];
			$form = str_replace('<!-- relances -->', $lg_form.'<!-- relances -->', $form);
			$lg_form = $histrel_hrow_form;
			$lg_form = str_replace('!!lib_rel!!', htmlentities(sprintf($msg['acquisition_hist_rel_du'], $date_rel), ENT_QUOTES, $charset),$lg_form);
		}
		$lg_form = str_replace('<!-- lignes -->',$histrel_row_form.'<!-- lignes -->', $lg_form);
		$lg_form = str_replace('!!numero!!',htmlentities($rel['numero'], ENT_QUOTES, $charset), $lg_form);
		$lg_form = str_replace('!!code!!',htmlentities($rel['code'], ENT_QUOTES, $charset), $lg_form);
		$lg_form = str_replace('!!lib!!',nl2br(htmlentities($rel['libelle'], ENT_QUOTES, $charset)), $lg_form);
		$lg_form = str_replace('!!qte!!',htmlentities($rel['nb'], ENT_QUOTES, $charset), $lg_form);
		$lg_form = str_replace('!!prix!!',htmlentities($rel['prix'], ENT_QUOTES, $charset), $lg_form);
		if ($rel['num_type']) {
			$tp = new types_produits($rel['num_type']);
			$lg_form = str_replace('!!lib_typ!!', htmlentities($tp->libelle,ENT_QUOTES,$charset),$lg_form);
		} else {
			$lg_form = str_replace('!!lib_typ!!', '',$lg_form);
		}
		if ($acquisition_gestion_tva) {
			$lg_form = str_replace('!!tva!!', $rel['tva'] , $lg_form);
		}
		$lg_form = str_replace('!!rem!!', $rel['remise'], $lg_form);
		if ($rel['num_rubrique']) {
			$rub = new rubriques($rel['num_rubrique']);
			$lg_form = str_replace('!!lib_rub!!', htmlentities($rub->libelle, ENT_QUOTES, $charset), $lg_form);
		} else {
			$lg_form = str_replace('!!lib_rub!!', '', $lg_form);
		}
		$lg_stat = new lgstat($rel['statut']);
		$lg_form = str_replace('!!lgstat!!', htmlentities($lg_stat->libelle, ENT_QUOTES, $charset), $lg_form);
		$lg_form = str_replace('!!comment_lg!!', nl2br(htmlentities($rel['commentaires_gestion'], ENT_QUOTES, $charset)),$lg_form);
		$lg_form = str_replace('!!comment_lo!!', nl2br(htmlentities($rel['commentaires_opac'], ENT_QUOTES, $charset)),$lg_form);
		$lg_form = str_replace('!!no!!', $i, $lg_form);
		$lg_form = str_replace('!!id_lig!!', $rel['num_ligne'], $lg_form);
	}
	$form = str_replace('<!-- relances -->', $lg_form,$form);
	print $form;
}

//Traitement des actions
switch($action) {
	case 'list':
		show_list_coord($id_bibli);
		break;
	case 'add':
	    show_coord_form($id_bibli, $id);
	    break;
	case 'modif':
	    if(!$id || !entites::is_a_fournisseur_id($id) ) {
	        show_list_coord($id_bibli);
	        break;
	    }
	    show_coord_form($id_bibli, $id);
	    break;
	case 'update':
		// vérification validité des données fournies.
	    $id_etablissement = $id_bibli;
	    if(isset($chk_all_etablissements) && $chk_all_etablissements == 1) {
	        $id_etablissement = 0;
	    }
	    if(entites::is_a_fournisseur_raison_sociale($raison, $id_etablissement, $id)) {
	        error_form_message($raison.$msg["acquisition_raison_already_used"]);
	        break;
	    }
	    if($id_bibli && !entites::is_a_etablissement_id($id_bibli)) {
	        show_coord_form($id_bibli, $id);
	        break;
	    }
	    $raison = trim($raison);
	    if($raison==='') {
	        show_coord_form($id_bibli, $id);
	        break;
	    }

	    $fourn = new entites($id);
		$fourn->type_entite = '0';

		$fourn->num_bibli = $id_etablissement;
		$fourn->num_cp_client = $num_cp;
		$fourn->set_properties_from_form();
		$fourn->save();
		$id = $fourn->id_entite;

		for($i=1; $i <= $max_coord; $i++) {
			switch ($mod_[$i]) {
				case '1' :
					$coord = new coordonnees($no_[$i]);
					$coord->num_entite = $id;
					if ($i == 1) $coord->type_coord = $i; else $coord->type_coord = 0;
					$coord->set_properties_from_form($i);
					$coord->save();
					break;
				case '-1' :
					if($no_[$i]) {
						$coord = new coordonnees($no_[$i]);
						$coord->delete($no_[$i]);
					}
					break;
				default :
					break;
			}
		}
		show_list_coord($id_bibli);
		break;
	case 'del':
	    if($id && entites::is_a_fournisseur_id($id)) {
	        if (!$force) {
	            $force = 0;
	        }
	        $total7 = entites::has_actes($id);
	        $total8 = entites::has_abonnements($id);
	        if ((($total7+$total8)==0) || $force) {
	            entites::staticDelete($id);
	            show_list_coord($id_bibli);
	        } else {
	            $msg_suppr_err = $msg['acquisition_fou_used'] ;
	            if ($total7) $msg_suppr_err .= "<br />- ".$msg['acquisition_fou_used_act'] ;
	            if ($total8) $msg_suppr_err .= "<br />- ".$msg['acquisition_fou_used_abt'] ;

	            if (!$total7 && $total8) {
	                box_confirm_message($msg['acquisition_entite_suppr'], $msg_suppr_err, 'acquisition.php?categ=ach&sub=fourn&action=del&id_bibli='.$id_bibli.'&id='.$id.'&force=1', 'acquisition.php?categ=ach&sub=fourn', $msg['acquisition_fou_suppr_forcage_button']);
	            } else {
	                error_message($msg[321], $msg_suppr_err, 1, 'acquisition.php?categ=ach&sub=fourn');
	            }
	        }
	    } else {
	        show_list_coord($id_bibli);
	    }
	    break;
	case 'cond':
	    if(!$id || !entites::is_a_fournisseur_id($id)) {
	        show_list_coord($id_bibli);
	        break;
	    }
		show_list_cond($id);
		break;
	case 'updatecond':
	    if(!$id || !entites::is_a_fournisseur_id($id)) {
	        show_list_coord($id_bibli);
	        break;
	    }
		$fourn = new entites($id);
		$fourn->num_paiement = $paiement;
		$fourn->save();
		show_list_coord($id_bibli);
		break;
	case 'modrem':
	    if(!$id || !entites::is_a_fournisseur_id($id)) {
	        show_list_coord($id_bibli);
	        break;
	    }
		$fourn = new entites($id);
		$fourn->num_paiement = $paiement;
		$fourn->raison_sociale = $fourn->raison_sociale;
		$fourn->commentaires = $fourn->commentaires;
		$fourn->siret = $fourn->siret;
		$fourn->naf = $fourn->naf;
		$fourn->rcs = $fourn->rcs;
		$fourn->tva = $fourn->tva;
		$fourn->site_web = $fourn->site_web;
		$fourn->save();
		$offres_remises = new offres_remises($id, $id_prod);
		print $offres_remises->get_form();
		break;
	case 'updaterem':
		$rem = str_replace(',','.',$rem);
		if( (!is_numeric($rem)) || ($rem < 0) || ($rem >= 100) ) {
			error_form_message($msg['acquisition_rem_err']);
			break;
		}
		if (!$id_prod) {
		    $id_prod = $sel_prod;
		}
		if ($id_prod) {
			$offre = new offres_remises($id, $id_prod);
			$offre->set_properties_from_form();
			$offre->save();
		}
		show_list_cond($id);
		break;
	case 'deleterem':
		offres_remises::delete($id, $id_prod);
		show_list_cond($id);
		break;
	case 'histrel':
	    if(!$id || !entites::is_a_fournisseur_id($id)) {
	        show_list_coord($id_bibli);
	    }
		show_list_rel($id);
		break;
	case 'deletehistrel':
		lignes_actes::deleteRelances($id);
		show_list_rel($id);
		break;
	default:
		show_list_coord($id_bibli);
		break;
}