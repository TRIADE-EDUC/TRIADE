<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: commandes.inc.php,v 1.121.4.1 2025/03/13 16:40:33 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id, $id_cde, $id_exer, $id_bibli, $class_path, $include_path, $base_path, $msg, $charset, $action, $id_exercice, $id_dev, $chk, $sugchk, $by_mail;

$id = intval($id);
$id_cde = intval($id_cde);
$id_exer = intval($id_exer);
$id_bibli = intval($id_bibli);

// gestion des commandes
require_once("$class_path/entites.class.php");
require_once("$class_path/exercices.class.php");
require_once("$class_path/paiements.class.php");
require_once("$class_path/frais.class.php");
require_once("$class_path/types_produits.class.php");
require_once("$class_path/rubriques.class.php");
require_once("$class_path/offres_remises.class.php");
require_once("$class_path/actes.class.php");
require_once("$class_path/lignes_actes.class.php");
require_once("$class_path/lignes_actes_statuts.class.php");
require_once("$class_path/liens_actes.class.php");
require_once("$class_path/suggestions.class.php");
require_once("$class_path/notice.class.php");
require_once("$class_path/sel_display.class.php");
require_once("$class_path/suggestions_map.class.php");
require_once("$include_path/templates/actes.tpl.php");
require_once("$include_path/templates/commandes.tpl.php");
require_once("$base_path/acquisition/achats/func_achats.inc.php");
require_once("$base_path/acquisition/suggestions/func_suggestions.inc.php");
require_once("$base_path/acquisition/achats/commandes/lettre_commande.class.php");
require_once($class_path.'/accounting/accounting_commandes_controller.class.php');
require_once("$class_path/user.class.php");

//Affiche la liste des commandes pour une bibliotheque
function show_list_cde($id_bibli, $id_exercice = 0) {

    global $accounting_commandes_ui_user_input;
    global $accounting_commandes_ui_status;
    global $statut;

    $filters = array();
    $filters['user_input'] = stripslashes($accounting_commandes_ui_user_input);
    $filters['status'] = ($statut ? $statut : $accounting_commandes_ui_status);

    $list_accounting_commandes_ui = new list_accounting_commandes_ui($filters);
    print $list_accounting_commandes_ui->get_display_list();
}

//Affiche les exercices actifs pour création commande
function show_list_exercices($id_bibli, $fct, $url, $id_dev=0) {

    global $msg, $charset;
    global $current_module;

    $q =  entites::getCurrentExercices($id_bibli);
    $r = pmb_mysql_query($q);
    $n = pmb_mysql_num_rows($r);
    switch ($n) {
        case 0 :
            //Pas d'exercice actif pour la bibliothèque
            $error_msg = htmlentities($msg["acquisition_err_exer"],ENT_QUOTES, $charset)."<div class='row'></div>";
            error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
            die;
            break;
        case 1 :
            //1 seul exercice actif pour la bibliotheque
            $row = pmb_mysql_fetch_object($r);
            eval($fct."(".$id_bibli.", ".$row->id_exercice.", ".$id_dev.");");
            break;
        default :
            $aff = "<form class='form-".$current_module."' id='list_exe_form' name='list_exe_form' method='post' action=\"\" >";
            $aff.= "<h3>".htmlentities($msg['acquisition_menu_chx_exe'], ENT_QUOTES, $charset)."</h3><div class='row'></div>";
            $aff.= "<table>";
            $parity=1;
            while(($row=pmb_mysql_fetch_object($r))) {
                if ($parity % 2) {
                    $pair_impair = "even";
                } else {
                    $pair_impair = "odd";
                }
                $parity += 1;
                $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" onmousedown=\"document.forms['list_exe_form'].setAttribute('action','".$url."&id_exer=".$row->id_exercice."');document.forms['list_exe_form'].submit(); \" ";
                $aff.= "<tr class='".$pair_impair."' ".$tr_javascript." style='cursor: pointer'><td><i>".htmlentities($row->libelle, ENT_QUOTES, $charset)."</i></td></tr>";
            }
            $aff.=" </table></form>";
            print $aff;
            break;
    }
}

//Affiche le formulaire de création/modification de commande
function show_cde($id_bibli, $id_exer, $id_cde) {

    global $msg, $charset;
    global $modif_cde_form, $valid_cde_form;
    global $bt_enr, $bt_enr_valid, $bt_val, $bt_dup, $bt_rec, $bt_fac, $bt_audit, $bt_sol, $bt_arc, $bt_sup, $bt_imp, $bt_mail;
    global $sel_date_liv_mod, $sel_date_liv_fix, $sel_date_pay_mod;
    global $pmb_gestion_devise;
    global $PMBuserid;
    global $pmb_type_audit;
    global $modif_cde_sel_typ_for_checked, $modif_cde_sel_rub_for_checked;

    // Recuperation etablissement
    $bibli = new entites($id_bibli);

    //Prise en compte des adresses utilisateurs par defaut
    $tab1 = explode('|', user::get_param($PMBuserid, 'speci_coordonnees_etab'));
    $tab_adr=array();
    foreach ($tab1 as $value) {
        $tab2=explode(',', $value);
        $tab_adr[$tab2[0]]['id_adr_fac']=$tab2[1];
        $tab_adr[$tab2[0]]['id_adr_liv']=$tab2[2];
    }
    $def_id_adr_fac=(isset($tab_adr[$id_bibli]['id_adr_fac']) ? $tab_adr[$id_bibli]['id_adr_fac'] : '');
    $def_id_adr_liv=(isset($tab_adr[$id_bibli]['id_adr_liv']) ? $tab_adr[$id_bibli]['id_adr_liv'] : '');

    $date_liv = '';
    $lien_dev = '';

    //Recuperation commande
    $id_cde = intval($id_cde);
    $cde = new actes($id_cde);
    $fou = new entites($cde->num_fournisseur);

    if(!$id_cde) {		//creation de commmande
        //Recuperation exercice
        $exer = new exercices($id_exer);
        $titre = htmlentities($msg['acquisition_cde_cre'], ENT_QUOTES, $charset);
    } else {
        $exer = new exercices($cde->num_exercice);
        $titre = htmlentities($msg['acquisition_cde_mod'], ENT_QUOTES, $charset);
    }

    if(!$id_cde) {		//creation de commmande
        $form = $modif_cde_form;
        $statut = STA_ACT_AVA;
        $sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
        $sel_statut.=htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
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
        $lien_dev = '';
        $liens_liv = '';
        $liens_fac = '';
        $sel_date_pay = $sel_date_pay_mod;
        $date_pay = '';
        $date_pay_lib = $msg['parperso_nodate'];
        $num_pay = '';
        $sel_date_liv=$sel_date_liv_mod;
        $date_liv = '';
        $date_liv_lib = $msg['parperso_nodate'];
        $id_dev = '0';
        $devise = $pmb_gestion_devise;

        $bt_enr_valid = '';
        $bt_dup = '';
        $bt_rec = '';
        $bt_fac = '';
        $bt_sol = '';
        $bt_imp = '';
        $bt_mail = '';
        $bt_audit = '';
        $bt_arc = '';
        $bt_sup = '';

        $lignes= show_lig_new_cde();

    } else {		//visualisation ou modification de commmande
        //elements communs
        $coord = entites::get_coordonnees($fou->id_entite, '1');
        if (pmb_mysql_num_rows($coord) != 0) {
            $coord = pmb_mysql_fetch_object($coord);
            $id_adr_fou = $coord->id_contact;
            $adr_fou = coordonnees::get_formatted_address_form_coord($coord);
        } else {
            $id_adr_fou = '0';
            $adr_fou = '';
        }
        $id_adr_fac = $cde->num_contact_fact;
        if ($id_adr_fac) {
            $coord_fac = new coordonnees($id_adr_fac);
            $adr_fac = $coord_fac->get_formatted_address();
        } else {
            $id_adr_fac = '0';
            $adr_fac = '';
        }
        $id_adr_liv = $cde->num_contact_livr;
        if ($id_adr_liv) {
            $coord_liv = new coordonnees($id_adr_liv);
            $adr_liv = $coord_liv->get_formatted_address();
        } else {
            $id_adr_liv = '0';
            $adr_liv = '';
        }
        $id_dev = liens_actes::getDevis($id_cde);
        if ($id_dev) {
            $dev=new actes($id_dev);
            $lien_dev = "<a href=\"./acquisition.php?categ=ach&sub=devi&action=modif&id_bibli=".$id_bibli."&id_dev=".$id_dev."\">".$dev->numero."</a>";
        }
        $tab_liens = liens_actes::getChilds($id_cde);
        $liens_liv = '';
        $liens_fac = '';
        while(($row_liens = pmb_mysql_fetch_object($tab_liens))) {
            if( ($row_liens->type_acte) == TYP_ACT_LIV ) {
                $liens_liv.= "<br /><a href=\"./acquisition.php?categ=ach&sub=livr&action=modif&id_bibli=".$id_bibli."&id_liv=".$row_liens->num_acte_lie."\">".$row_liens->numero."</a>";
            }
            if( ($row_liens->type_acte) == TYP_ACT_FAC ) {
                $liens_fac.= "<br /><a href=\"./acquisition.php?categ=ach&sub=fact&action=modif&id_bibli=".$id_bibli."&id_fac=".$row_liens->num_acte_lie."\">".$row_liens->numero."</a>";
            }
        }
        $sel_date_pay = $sel_date_pay_mod;
        if ($cde->date_paiement=='0000-00-00') {
            $date_pay = '';
            $date_pay_lib = $msg['parperso_nodate'];
        } else {
            $date_pay = $cde->date_paiement;
            $date_pay_lib = formatdate($cde->date_paiement);
        }
        $num_pay = htmlentities($cde->num_paiement, ENT_QUOTES, $charset);

        if (!$pmb_type_audit) {
            $bt_audit = '';
        }
        $devise = htmlentities($cde->devise, ENT_QUOTES, $charset);

        if ($cde->statut != STA_ACT_AVA) {	//Commande non modifiable
            $form = $valid_cde_form;
            $statut = $cde->statut;
            $st = ( ($cde->statut) & ~(STA_ACT_FAC | STA_ACT_PAY | STA_ACT_ARC) );
            switch ($st) {
                case STA_ACT_AVA :
                    $sel_statut = htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
                    break;
                case STA_ACT_ENC :
                    $sel_statut = htmlentities($msg['acquisition_cde_enc'], ENT_QUOTES, $charset);
                    break;
                case STA_ACT_REC :
                    $sel_statut = htmlentities($msg['acquisition_cde_liv'], ENT_QUOTES, $charset);
                    break;
                default :
                    $sel_statut = htmlentities($msg['acquisition_cde_enc'], ENT_QUOTES, $charset);
                    break;
            }
            if( ($cde->statut & STA_ACT_PAY) == STA_ACT_PAY ) {
                $statut_fac = htmlentities($msg['acquisition_act_pay'], ENT_QUOTES, $charset);
            } elseif( ($cde->statut & STA_ACT_FAC) == STA_ACT_FAC ) {
                $statut_fac = htmlentities($msg['acquisition_act_fac'], ENT_QUOTES, $charset);
            } else {
                $statut_fac = '';
            }
            if ($statut_fac) $sel_statut.='&nbsp;/&nbsp;'.$statut_fac;
            if( ($cde->statut & STA_ACT_ARC) == STA_ACT_ARC ) $sel_statut = '<s>'.$sel_statut.'</s>';
            $sel_statut.= "<input type='hidden' id='statut' name='statut' value='".$statut."' />";

            $sel_date_liv=$sel_date_liv_fix;
            if ($cde->date_ech=='0000-00-00') {
                $date_liv_lib = $msg['parperso_nodate'];
            } else {
                $date_liv_lib = formatdate($cde->date_ech);
            }

            if (($cde->statut & STA_ACT_ARC) == STA_ACT_ARC ){	//Commande archivee
                $bt_enr = '';
                $bt_enr_valid = '';
                $bt_val = '';
                $bt_rec = '';
                $bt_fac = '';
                $bt_sol = '';
                $bt_arc = '';
            } else {	//Commande non archivee
                $bt_enr = '';
                $bt_val = '';

                if (($cde->statut & STA_ACT_REC) == STA_ACT_REC) {	//Commande soldee
                    $bt_rec = '';
                    $bt_sol = '';
                }
                if (($cde->statut & STA_ACT_FAC) == STA_ACT_FAC) {	//Commande facturee
                    $bt_fac = '';
                }
                if (($cde->statut & STA_ACT_REC) != STA_ACT_REC) { //Commande non soldee
                    $bt_arc = '';
                }
            }
            $lignes= show_lig_cde($id_cde, false);
        } else {	//Commande modifiable
            $form = $modif_cde_form;

            $statut = STA_ACT_AVA;
            $sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
            $sel_statut.=htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);

            $sel_date_liv = $sel_date_liv_mod;
            if ($cde->date_ech=='0000-00-00') {
                $date_liv = '';
                $date_liv_lib = $msg['parperso_nodate'];
            } else {
                $date_liv = $cde->date_ech;
                $date_liv_lib = formatdate($cde->date_ech);
            }
            $bt_enr_valid = '';
            $bt_rec = '';
            $bt_fac = '';
            $bt_sol = '';

            $lignes= show_lig_cde($id_cde);

        }
    }
    //Date validation
    if (isset($cde) && $cde->date_valid != '0000-00-00') {
        $form = str_replace('!!date_valid_label!!',htmlentities($msg['acquisition_cde_date_val'], ENT_QUOTES, $charset),$form);
        $form = str_replace('!!date_valid!!',formatdate($cde->date_valid),$form);
    } else {
        $form = str_replace('!!date_valid_label!!','',$form);
        $form = str_replace('!!date_valid!!','',$form);
    }

    //complement formulaire
    $form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
    $form = str_replace('<!-- sel_date_liv -->', $sel_date_liv, $form);
    $form = str_replace('<!-- sel_date_pay -->', $sel_date_pay, $form);
    $form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
    $form = str_replace('<!-- bouton_enr_valid -->', $bt_enr_valid, $form);
    $form = str_replace('<!-- bouton_val -->', $bt_val, $form);
    $form = str_replace('<!-- bouton_dup -->', $bt_dup, $form);
    $form = str_replace('<!-- bouton_rec -->', $bt_rec, $form);
    $form = str_replace('<!-- bouton_fac -->', $bt_fac, $form);
    $form = str_replace('<!-- bouton_imp -->', $bt_imp, $form);
    $form = str_replace('<!-- bouton_audit -->', $bt_audit, $form);
    $form = str_replace('<!-- bouton_arc -->', $bt_arc, $form);
    $form = str_replace('<!-- bouton_sol -->', $bt_sol, $form);
    $form = str_replace('<!-- bouton_sup -->', $bt_sup, $form);
    $form = str_replace('!!act_nblines!!', $lignes[0], $form);
    $form = str_replace('<!-- lignes -->', $lignes[1], $form);

    //selecteurs de type de produit et de budget pour affectation aux lignes cochees
    if ($cde->statut == STA_ACT_AVA) {
        $form = str_replace('<!-- sel_type_for_checked -->', $modif_cde_sel_typ_for_checked, $form);
        $form = str_replace('<!-- sel_budget_for_checked -->', $modif_cde_sel_rub_for_checked, $form);
    }

    //Remplissage formulaire
    $form = str_replace('!!form_title!!', $titre, $form);
    $form = str_replace('!!id_bibli!!', $id_bibli, $form);
    $form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
    $form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_cde!!', $id_cde, $form);
    $form = str_replace('!!date_cre!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def_lib!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def!!', preg_replace('/-/', '', today()), $form);
    $form = str_replace('!!numero!!', htmlentities($cde->numero, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!nom_acte!!', htmlentities($cde->nom_acte,ENT_QUOTES,$charset), $form);
    $form = str_replace('!!statut!!', $statut, $form);
    $form = str_replace('!!id_fou!!', $cde->num_fournisseur, $form);
    $form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_adr_fou!!', $id_adr_fou, $form);
    $form = str_replace('!!adr_fou!!', $adr_fou, $form);
    $form = str_replace('!!date_liv!!', $date_liv, $form);
    $form = str_replace('!!date_liv_lib!!', $date_liv_lib, $form);
    $form = str_replace('!!date_pay!!', $date_pay, $form);
    $form = str_replace('!!date_pay_lib!!', $date_pay_lib, $form);
    $form = str_replace('!!num_pay!!', $num_pay, $form);
    $form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
    $form = str_replace('!!adr_liv!!', $adr_liv, $form);
    $form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
    $form = str_replace('!!adr_fac!!', $adr_fac, $form);
    $form = str_replace('!!comment!!', htmlentities($cde->commentaires, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!comment_i!!', htmlentities($cde->commentaires_i, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!ref!!', htmlentities($cde->reference, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_dev!!', $id_dev, $form);
    $form = str_replace('!!devise!!', $devise, $form);
    $form = str_replace('!!lien_dev!!', $lien_dev, $form);
    $form = str_replace('!!liens_fac!!', $liens_fac, $form);
    $form = str_replace('!!liens_liv!!', $liens_liv, $form);

    //ajout ajax pour recherche budgets
    if($id_cde) {
        $form.="<script type='text/javascript' src='./javascript/ajax.js'/>";
        $form.="<script type='text/javascript'>ajax_parse_dom();</script>";
    }


    print $form;
}


//Affiche les lignes d'une commande
function show_lig_new_cde() {

    global $charset,$msg;
    global $acquisition_gestion_tva;
    global $modif_cde_row_form;
    global $first_applicant_line;
    global $applicants_common_tpl;
    global $deflt3lgstatcde, $deflt3rubrique;

    $form = "
    <script type='text/javascript'>
        acquisition_force_ttc='".$msg["acquisition_force_ttc"]."';
        acquisition_force_ht='".$msg["acquisition_force_ht"]."';
    </script>";

    $i=0;
    $row_form = $modif_cde_row_form;
    $lgstat_form=lgstat::getHtmlSelect(array(), false, array('id'=>'lg_statut[!!i!!]', 'name'=>'lg_statut[!!i!!]'));
    $rows_frais = frais::getFraisForNewOrder();

    //Ligne de frais
    if(count($rows_frais)) {

        foreach($rows_frais as $row) {

            $i++;
            $form.= $row_form;

            $std_applicants = str_replace('!!applicant_label!!', '', $first_applicant_line);
            $std_applicants = str_replace('!!applicant_id!!', '', $std_applicants);
            $std_applicants = str_replace('!!std_applicants!!', $std_applicants, $applicants_common_tpl);
            $form = str_replace('!!applicants_tr!!', $std_applicants, $form);
            $form = str_replace('!!applicants_visibility!!', 'style="display:none;"', $form);

            $form = str_replace('!!no!!', $i, $form);
            $form = str_replace('!!code!!', '', $form);
            $form = str_replace('!!lib!!', htmlentities($row['libelle_frais'], ENT_QUOTES, $charset), $form);

            $form = str_replace('!!qte!!', '1', $form);
            $form = str_replace('!!prix!!', $row['montant_frais'], $form);
            $convert_prix = '';
            if ($acquisition_gestion_tva) {
                $form = str_replace('!!tva!!', $row['taux_tva'] , $form);

                if ($acquisition_gestion_tva==1 ) {
                    $prix_ttc=round($row['montant_frais']+($row['montant_frais']/100*$row['taux_tva']),2);
                    $onchange_tva="
                        onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                        ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                        thresholds_notification();
                        ' ";
                    $convert_prix="
                        onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                        ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                        thresholds_notification();
                        ' ";
                    $convert_ht_ttc="
                    <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                        onclick='
                            document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                            document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                            document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                        '
                    >".$prix_ttc."</span>
                    <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value=''
                        onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                        onChange='document.getElementById(\"prix[$i]\").value=
                        ttc_to_ht(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                        document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                        document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                        thresholds_notification();
                        '
                    />";

                } elseif ($acquisition_gestion_tva==2 ) {
                    $prix=$row['montant_frais'];
                    $tva=$row['taux_tva'];
                    $prix_ht=round( $prix / (($tva/100)+1),2);
                    $onchange_tva="
                        onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                        ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                        thresholds_notification();
                        ' ";
                    $convert_prix="
                        onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                        ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                        thresholds_notification();
                        ' ";
                    $convert_ht_ttc="
                    <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                        onclick='
                            document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                            document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                            document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                        '
                    >".$prix_ht."</span>
                    <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value='$prix_ttc'
                        onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                        onChange='document.getElementById(\"prix[$i]\").value=
                        ht_to_ttc(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                        document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                        document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                        thresholds_notification();
                        '
                    />";
                }

                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='2' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                    onclick='
                        if(document.getElementById(\"force_debit[$i]\").value==2){
                            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
                            document.getElementById(\"force_debit[$i]\").value=1;
                        }else{
                            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
                            document.getElementById(\"force_debit[$i]\").value=2;
                        }
                    '
                >".$msg["acquisition_force_ttc"]."</span>";
            }
            if ( '' == $convert_prix ) {
                $convert_prix = "onChange='thresholds_notification();'";
            }
            $form = str_replace('!!onchange_tva!!', $onchange_tva, $form);
            $form = str_replace('!!convert_prix!!', $convert_prix, $form);
            $form = str_replace('!!convert_ht_ttc!!', $convert_ht_ttc, $form);
            $form = str_replace('!!force_ht_ttc!!', $force_ht_ttc, $form);
            $form = str_replace('!!rem!!', '0.00', $form);
            $lgstat=str_replace('!!i!!',$i,$lgstat_form);
            $lgstat=str_replace("value='{$deflt3lgstatcde}' ","value='{$deflt3lgstatcde}' selected='selected' ",$lgstat);
            $form=str_replace('!!lgstat!!',$lgstat,$form);
            $form=str_replace('!!comment_lg!!',htmlentities($row->commentaires_gestion,ENT_QUOTES,$charset),$form);
            $form=str_replace('!!comment_lo!!',htmlentities($row->commentaires_opac,ENT_QUOTES,$charset),$form);
            $form=str_replace('!!id_cde!!',0,$form);

            $form = str_replace('!!typ!!', '0', $form);
            $form = str_replace('!!lib_typ!!', '', $form);

            if ($deflt3rubrique) {
                $rub = new rubriques($deflt3rubrique);
                $bud = new budgets($rub->num_budget);
                $form = str_replace('!!rub!!', $rub->id_rubrique, $form);

                $tab_rub = rubriques::listAncetres($rub->id_rubrique, true);
                $lib_rub = '';
                foreach ($tab_rub as $value) {
                    $lib_rub.= $value[1];
                    if($value[0] != $row->num_rubrique) $lib_rub.= ":";
                }

                $form = str_replace('!!lib_rub!!', htmlentities($bud->libelle.":".$lib_rub, ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!rub!!', '0', $form);
                $form = str_replace('!!lib_rub!!', '', $form);
            }

            $form = str_replace('!!id_sug!!', '0', $form);
            $form = str_replace('!!id_lig!!', '0', $form);
            $form = str_replace('!!typ_lig!!', '3', $form); //typ_lig : type de ligne frais annexe
            $form = str_replace('!!id_prod!!', $row['id_frais'], $form); // id_prod = id frais annexe

        }
    }

    $t = array(0=>$i, 1=>$form);
    return $t;
}

//Affiche les lignes d'une commande
function show_lig_cde($id_cde, $mod=true) {

    global $charset,$msg;
    global $acquisition_gestion_tva;
    global $modif_cde_row_form, $valid_cde_row_form;
    global $first_applicant_line;
    global $others_applicants_line;
    global $applicants_common_tpl;

    $form = "
    <script type='text/javascript'>
        acquisition_force_ttc='".$msg["acquisition_force_ttc"]."';
        acquisition_force_ht='".$msg["acquisition_force_ht"]."';
    </script>
    ";
    $i=0;
    if (!$id_cde) {
        $t = array(0=>$i, $form);
        return $t;
    }

    if ($mod) {
        $row_form = $modif_cde_row_form;
    } else {
        $row_form = $valid_cde_row_form;
    }
    $lgstat_form=lgstat::getHtmlSelect(array(), false, array('id'=>'lg_statut[!!i!!]', 'name'=>'lg_statut[!!i!!]'));
    $lignes = actes::getLignes($id_cde);
    while (($row = pmb_mysql_fetch_object($lignes))) {
        $i++;
        $form.= $row_form;
        if($row->type_ligne == 1 || $row->type_ligne == 2){
            $applicants_array = lignes_actes::getApplicantsFromId($row->id_ligne);
            $applicants = '';
            if(count($applicants_array)){
                $j = 0;
                foreach ($applicants_array as $id_empr) {
                    if($j == 0){ //Premier tour !
                        $empr_label = emprunteur::getName(array($id_empr));
                        $applicants = str_replace('!!applicant_label!!', $empr_label[$id_empr], $first_applicant_line);
                        $applicants = str_replace('!!applicant_id!!', $id_empr, $applicants);
                    }else{
                        $empr_label = emprunteur::getName(array($id_empr));
                        $others_applicants = str_replace('!!applicant_label!!', $empr_label[$id_empr], $others_applicants_line);
                        $others_applicants = str_replace('!!nb!!', $j, $others_applicants);
                        $others_applicants = str_replace('!!applicant_id!!', $id_empr, $others_applicants);
                        $applicants.= $others_applicants;
                    }
                    $j++;
                }
                $applicants = str_replace('!!std_applicants!!', $applicants, $applicants_common_tpl);
                $form = str_replace('!!applicants_tr!!', $applicants, $form);
            } else {
                $std_applicants = str_replace('!!applicant_label!!', '', $first_applicant_line);
                $std_applicants = str_replace('!!applicant_id!!', '', $std_applicants);
                $std_applicants = str_replace('!!std_applicants!!', $std_applicants, $applicants_common_tpl);
                $form = str_replace('!!applicants_tr!!', $std_applicants, $form);
            }
            $form = str_replace('!!applicants_visibility!!', 'style="display:table-row;"', $form);
        } else {
            $std_applicants = str_replace('!!applicant_label!!', '', $first_applicant_line);
            $std_applicants = str_replace('!!applicant_id!!', '', $std_applicants);
            $std_applicants = str_replace('!!std_applicants!!', $std_applicants, $applicants_common_tpl);
            $form = str_replace('!!applicants_tr!!', $std_applicants, $form);
            $form = str_replace('!!applicants_visibility!!', 'style="display:none;"', $form);
        }

        $form = str_replace('!!no!!', $i, $form);
        $form = str_replace('!!code!!', htmlentities($row->code, ENT_QUOTES, $charset), $form);
        if ($mod) {
            $form = str_replace('!!lib!!', htmlentities($row->libelle, ENT_QUOTES, $charset), $form);
        } else {
            $form = str_replace('!!lib!!', nl2br(htmlentities($row->libelle, ENT_QUOTES, $charset)), $form);
        }
        $form = str_replace('!!qte!!', $row->nb, $form);
        $form = str_replace('!!prix!!', $row->prix, $form);
        $convert_prix = '';
        $convert_ht_ttc = '';
        $onchange_tva = '';
        $force_ht_ttc = '';
        if ($acquisition_gestion_tva) {
            $form = str_replace('!!tva!!', $row->tva , $form);

            if ($acquisition_gestion_tva == 1) {
                $prix_ttc = round($row->prix + ($row->prix / 100 * $row->tva), 2);
                $onchange_tva = "
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                thresholds_notification();
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                thresholds_notification();
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ttc."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value=''
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ttc_to_ht(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                thresholds_notification();
                '
                />";
            } elseif ($acquisition_gestion_tva == 2) {
                $prix_ttc = $row->prix;
                $tva = $row->tva;
                $prix_ht = round($prix_ttc / (($tva / 100) + 1), 2);
                $onchange_tva = "
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                thresholds_notification();
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                thresholds_notification();
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ht."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value='$prix_ttc'
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ht_to_ttc(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                thresholds_notification();
                '
                />";
            }
            if ($row->debit_tva==1 ) {
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='1' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==1){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
                document.getElementById(\"force_debit[$i]\").value=2;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
            document.getElementById(\"force_debit[$i]\").value=1;
            }
            '
            >".$msg["acquisition_force_ht"]."</span>";
            }else{
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='2' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==2){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
                document.getElementById(\"force_debit[$i]\").value=1;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
            document.getElementById(\"force_debit[$i]\").value=2;
            }
            '
            >".$msg["acquisition_force_ttc"]."</span>";
            }
        }
        if ( '' == $convert_prix ) {
            $convert_prix = "onChange='thresholds_notification();'";
        }
        $form = str_replace('!!onchange_tva!!', $onchange_tva, $form);
        $form = str_replace('!!convert_prix!!', $convert_prix, $form);
        $form = str_replace('!!convert_ht_ttc!!', $convert_ht_ttc, $form);
        $form = str_replace('!!force_ht_ttc!!', $force_ht_ttc, $form);
        $form = str_replace('!!rem!!', $row->remise, $form);
        $lgstat=str_replace('!!i!!',$i,$lgstat_form);
        $lgstat=str_replace("value='$row->statut' ","value='$row->statut' selected='selected' ",$lgstat);
        $form=str_replace('!!lgstat!!',$lgstat,$form);
        $form=str_replace('!!comment_lg!!',htmlentities($row->commentaires_gestion,ENT_QUOTES,$charset),$form);
        $form=str_replace('!!comment_lo!!',htmlentities($row->commentaires_opac,ENT_QUOTES,$charset),$form);
        $form=str_replace('!!id_cde!!',$id_cde,$form);

        if ($mod) {
            if ($row->num_type) {
                $tp = new types_produits($row->num_type);
                $form = str_replace('!!typ!!', $tp->id_produit, $form);
                $form = str_replace('!!lib_typ!!', htmlentities($tp->libelle, ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!typ!!', '0', $form);
                $form = str_replace('!!lib_typ!!', '', $form);
            }
            if ($row->num_rubrique) {
                $rub = new rubriques($row->num_rubrique);
                $bud = new budgets($rub->num_budget);
                $form = str_replace('!!rub!!', $rub->id_rubrique, $form);

                $tab_rub = rubriques::listAncetres($rub->id_rubrique, true);
                $lib_rub = '';
                foreach ($tab_rub as $dummykey=>$value) {
                	$lib_rub.= $value[1];
                	if($value[0] != $row->num_rubrique) $lib_rub.= ":";
                }
                $form = str_replace('!!lib_rub!!', htmlentities($bud->libelle.":".$lib_rub, ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!rub!!', '0', $form);
                $form = str_replace('!!lib_rub!!', '', $form);
            }
            $form = str_replace('!!id_sug!!', $row->num_acquisition, $form);
            $form = str_replace('!!id_lig!!', $row->id_ligne, $form);
            $form = str_replace('!!typ_lig!!', $row->type_ligne, $form);
            $form = str_replace('!!id_prod!!', $row->num_produit, $form);
        } else {
            //recherche des lignes de livraison
            $lig_liv = lignes_actes::getLivraisons($row->id_ligne);
            $rec = 0;
            while (($row_liv = pmb_mysql_fetch_object($lig_liv))) {
                $rec = $rec + $row_liv->nb;
            }
            $form = str_replace('!!rec!!', $rec, $form);
            if ($row->num_type) {
                $tp = new types_produits($row->num_type);
                $form = str_replace('!!lib_typ!!', htmlentities($tp->libelle, ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!lib_typ!!', '', $form);
            }
            if ($row->num_rubrique) {
                $rub = new rubriques($row->num_rubrique);
                $bud = new budgets($rub->num_budget);

                $tab_rub = rubriques::listAncetres($rub->id_rubrique, true);
                $lib_rub = '';
                foreach ($tab_rub as $dummykey=>$value) {
                    $lib_rub.= $value[1];
                    if($value[0] != $row->num_rubrique) $lib_rub.= ":";
                }

                $form = str_replace('!!lib_rub!!', htmlentities($bud->libelle.":".$lib_rub, ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!lib_rub!!', '', $form);
            }
            $form = str_replace('!!id_lig!!', $row->id_ligne, $form);
        }
    }
    $t = array(0=>$i, 1=>$form);
    return $t;
}

//Affiche le formulaire de création de commande à partir d'un devis
function show_cde_from_dev($id_bibli, $id_exer, $id_dev) {

    global $msg, $charset;
    global $modif_cde_form;
    global $bt_enr, $bt_val;
    global $sel_date_liv_mod, $sel_date_pay_mod;

    //Recuperation etablissement
    $bibli = new entites($id_bibli);

    //Recuperation devis
    $dev = new actes($id_dev);

    //Recuperation exercice
    $exer = new exercices($id_exer);

    $form = $modif_cde_form;

    //$numero = calcNumero($id_bibli, TYP_ACT_CDE);
    $statut = STA_ACT_AVA;
    $sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
    $sel_statut.=htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
    $fou = new entites($dev->num_fournisseur);
    $coord = entites::get_coordonnees($fou->id_entite, '1');
    if (pmb_mysql_num_rows($coord) != 0) {
        $coord = pmb_mysql_fetch_object($coord);
        $id_adr_fou = $coord->id_contact;
        $adr_fou = coordonnees::get_formatted_address_form_coord($coord);
    } else {
        $id_adr_fou = '0';
        $adr_fou = '';
    }
    if ($dev->num_contact_fact) {
        $coord_fac = new coordonnees($dev->num_contact_fact);
        $adr_fac = $coord_fac->get_formatted_address();
    } else {
        $adr_fac = '';
    }
    if ($dev->num_contact_livr) {
        $coord_liv = new coordonnees($dev->num_contact_livr);
        $adr_liv = $coord_liv->get_formatted_address();
    } else {
        $adr_liv = '';
    }
    $lien_dev = "<a href=\"./acquisition.php?categ=ach&sub=devi&action=modif&id_bibli=".$id_bibli."&id_dev=".$id_dev."\">".$dev->numero."</a>";

    $lignes = show_lig_cde_from_dev($id_dev);

    //complement formulaire
    $form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
    $form = str_replace('<!-- sel_date_liv -->', $sel_date_liv_mod, $form);
    $form = str_replace('<!-- sel_date_pay -->', $sel_date_pay_mod, $form);
    $form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
    $form = str_replace('<!-- bouton_val -->', $bt_val, $form);
    $form = str_replace('<!-- bouton_imp -->', '', $form);
    $form = str_replace('<!-- bouton_dup -->', '', $form);
    $form = str_replace('<!-- bouton_rec -->', '', $form);
    $form = str_replace('<!-- bouton_fac -->', '', $form);
    $form = str_replace('<!-- bouton_audit -->', '', $form);
    $form = str_replace('<!-- bouton_sol -->', '', $form);
    $form = str_replace('<!-- bouton_sup -->', '', $form);
    $form = str_replace('!!act_nblines!!', $lignes[0], $form);
    $form = str_replace('<!-- lignes -->', $lignes[1], $form);

    //Remplissage formulaire
    $form = str_replace('!!form_title!!', htmlentities($msg['acquisition_cde_cre'], ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_bibli!!', $id_bibli, $form);
    $form = str_replace('!!lib_bibli!!', htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
    $form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_cde!!', 0, $form);
    $form = str_replace('!!date_cre!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def_lib!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def!!', preg_replace('/-/', '', today()), $form);
    $form = str_replace('!!numero!!', "", $form);
    $form = str_replace('!!nom_acte!!', '', $form);
    $form = str_replace('!!statut!!', $statut, $form);
    $form = str_replace('!!id_fou!!', $dev->num_fournisseur, $form);
    $form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_adr_fou!!', $id_adr_fou, $form);
    $form = str_replace('!!adr_fou!!', $adr_fou, $form);
    $form = str_replace('!!date_liv!!', '', $form);
    $form = str_replace('!!date_liv_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!date_pay!!', '', $form);
    $form = str_replace('!!date_pay_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!num_pay!!', '', $form);
    $form = str_replace('!!id_adr_liv!!', $dev->num_contact_livr, $form);
    $form = str_replace('!!adr_liv!!', $adr_liv, $form);
    $form = str_replace('!!id_adr_fac!!', $dev->num_contact_fact, $form);
    $form = str_replace('!!adr_fac!!', $adr_fac, $form);
    $form = str_replace('!!comment!!', '', $form);
    $form = str_replace('!!comment_i!!', htmlentities($dev->commentaires_i, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!ref!!', htmlentities($dev->reference, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_dev!!', $id_dev, $form);
    $form = str_replace('!!devise!!', htmlentities($dev->devise, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!lien_dev!!', $lien_dev, $form);
    $form = str_replace('!!liens_fac!!', '', $form);
    $form = str_replace('!!liens_liv!!', '', $form);

    print $form;
}

//Affiche les lignes d'une commande a partir d'un devis
function show_lig_cde_from_dev($id_dev) {

    global $charset,$msg;
    global $acquisition_gestion_tva;
    global $modif_cde_row_form,$deflt3lgstatcde;

    $form = "
    <script type='text/javascript'>
        acquisition_force_ttc='".$msg["acquisition_force_ttc"]."';
        acquisition_force_ht='".$msg["acquisition_force_ht"]."';
    </script>
    ";
    $i=0;
    if (!$id_dev) {
        $t = array(0=>$i, $form);
        return $t;
    }
    $lgstat_form=lgstat::getHtmlSelect(array(0=>$deflt3lgstatcde), false, array('id'=>'lg_statut[!!i!!]','name'=>'lg_statut[!!i!!]'));
    $lignes = actes::getLignes($id_dev);
    while (($row = pmb_mysql_fetch_object($lignes))) {
        $i++;
        $form.= $modif_cde_row_form;
        $form = str_replace('!!no!!', $i, $form);
        $form = str_replace('!!code!!', htmlentities($row->code, ENT_QUOTES, $charset), $form);
        $form = str_replace('!!lib!!', htmlentities($row->libelle, ENT_QUOTES, $charset), $form);
        $form = str_replace('!!qte!!', $row->nb, $form);
        $form = str_replace('!!prix!!', $row->prix, $form);
        $convert_prix = '';
        if ($acquisition_gestion_tva) {
            $form = str_replace('!!tva!!', $row->tva , $form);
            if ($acquisition_gestion_tva==1 ) {
                $prix_ttc=round($row->prix+($row->prix/100*$row->tva),2);
                $onchange_tva="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ttc."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value=''
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ttc_to_ht(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                '
                />";
            } elseif ($acquisition_gestion_tva==2 ) {
                $prix=$row->prix;
                $tva=$row->tva;
                $prix_ht=round( $prix / (($tva/100)+1),2);
                $onchange_tva="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ht."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value='$prix_ttc'
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ht_to_ttc(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                '
                />";
            }
            if ($row->debit_tva==1 ) {
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='1' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==1){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
                document.getElementById(\"force_debit[$i]\").value=2;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
            document.getElementById(\"force_debit[$i]\").value=1;
            }
            '
            >".$msg["acquisition_force_ht"]."</span>";
            } else {
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='2' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==2){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
                document.getElementById(\"force_debit[$i]\").value=1;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
            document.getElementById(\"force_debit[$i]\").value=2;
            }
            '
            >".$msg["acquisition_force_ttc"]."</span>";
            }
        }
        if ( '' == $convert_prix ) {
            $convert_prix = "onChange='thresholds_notification();'";
        }
        $form = str_replace('!!onchange_tva!!', $onchange_tva ?? "", $form);
        $form = str_replace('!!convert_prix!!', $convert_prix ?? "", $form);
        $form = str_replace('!!convert_ht_ttc!!', $convert_ht_ttc ?? "", $form);
        $form = str_replace('!!force_ht_ttc!!', $force_ht_ttc ?? "", $form);
        $form = str_replace('!!rem!!', $row->remise ?? "", $form);
        $lgstat=str_replace('!!i!!',$i ?? "",$lgstat_form);
        $form=str_replace('!!lgstat!!',$lgstat ?? "",$form);
        $form=str_replace('!!comment_lg!!',htmlentities($row->commentaires_gestion ?? "",ENT_QUOTES,$charset),$form);
        $form=str_replace('!!comment_lo!!',htmlentities($row->commentaires_opac ?? "",ENT_QUOTES,$charset),$form);

        if ($row->num_type) {
            $tp = new types_produits($row->num_type);
            $form = str_replace('!!typ!!', $tp->id_produit, $form);
            $form = str_replace('!!lib_typ!!', htmlentities($tp->libelle, ENT_QUOTES, $charset), $form);
        } else {
            $form = str_replace('!!typ!!', '0', $form);
            $form = str_replace('!!lib_typ!!', '', $form);
        }
        $form = str_replace('!!rub!!', '0', $form);
        $form = str_replace('!!lib_rub!!', '', $form);
        $form = str_replace('!!id_sug!!', $row->num_acquisition, $form);
        $form = str_replace('!!id_lig!!', $row->id_ligne, $form);
        $form = str_replace('!!typ_lig!!', $row->type_ligne, $form);
        $form = str_replace('!!id_prod!!', $row->num_produit, $form);
    }
    $t = array(0=>$i, 1=>$form);
    return $t;
}

//Affiche la liste des etablissements et exercices pour choix depuis suggestions
function show_list_biblio_from_sug($sugchk) {

    global $msg, $charset;
    global $tab_bib;
    global $current_module;
    $sugchk = rawurlencode(serialize($sugchk));

    //Affichage de la liste des etablissements et exercices auxquels a acces l'utilisateur
    $te = array();
    foreach($tab_bib[0] as $k=>$v) {
        $qe =  entites::getCurrentExercices($v);
        $re = pmb_mysql_query($qe);
        while (($rowe=pmb_mysql_fetch_object($re))) {
            $te[$rowe->id_exercice][0]=$v;
            $te[$rowe->id_exercice][1]=$tab_bib[1][$k];
            $te[$rowe->id_exercice][2]=$rowe->libelle;
        }
    }
    switch (count($te)) {
        case 0 :
            //Pas d'exercice actif
            $error_msg = htmlentities($msg["acquisition_err_exer"],ENT_QUOTES, $charset)."<div class='row'></div>";
            error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
            die;
            break;
        default:
            $aff = "<form class='form-".$current_module."' id='list_biblio_form' name='list_biblio_form' method='post' action=\"\" >";
            $aff.= "<input type='hidden' id='sugchk' name='sugchk' value='".$sugchk."' />";
            $aff.= "<h3>".htmlentities($msg['acquisition_menu_chx_ent_exe'], ENT_QUOTES, $charset)."</h3><div class='row'></div>";
            $aff.= "<table>";
            $parity = 1;
            $tb = array();
            foreach($te as $key=>$value) {
                if (in_array($value[0], $tb)===false) {
                    $tb[]=$value[0];
                    if ($parity % 2) {
                    	$pair_impair = "even";
                    } else {
            		$pair_impair = "odd";
                    }
                    $parity += 1;
                    $aff.= "<tr class='".$pair_impair."' ><th>".htmlentities($value[1], ENT_QUOTES, $charset)."</th></tr>";
                }
                if ($parity % 2) {
                    $pair_impair = "even";
                } else {
                    $pair_impair = "odd";
                }
                $parity += 1;
                $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" onmousedown=\"document.forms['list_biblio_form'].setAttribute('action','./acquisition.php?categ=ach&sub=cmde&action=from_sug_next&id_bibli=".$value[0]."&id_exer=".$key."');document.forms['list_biblio_form'].submit(); \" ";
                $aff.= "<tr class='".$pair_impair."' ".$tr_javascript." style='cursor: pointer' ><td><i>".htmlentities($value[2], ENT_QUOTES, $charset)."</i></td></tr>";
            }
            $aff.=" </table></form>";
            if (count($te) == '1') $aff.= "<script type='text/javascript'>document.forms['list_biblio_form'].setAttribute('action','./acquisition.php?categ=ach&sub=cmde&action=from_sug_next&id_bibli=".$value[0]."&id_exer=".$key."');document.forms['list_biblio_form'].submit();</script>";
            print $aff;
            break;
    }
}

//Affiche le formulaire de création de commande depuis suggestions
function show_cde_from_sug($id_bibli, $id_exer, $sugchk) {
    global $msg, $charset;
    global $modif_cde_form;
    global $bt_enr, $bt_val;
    global $sel_date_liv_mod, $sel_date_pay_mod;
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
    $def_id_adr_fac=$tab_adr[$id_bibli]['id_adr_fac'];
    $def_id_adr_liv=$tab_adr[$id_bibli]['id_adr_liv'];

    $form = $modif_cde_form;

    //Recuperation exercice
    $exer = new exercices($id_exer);

    //$numero = calcNumero($id_bibli, TYP_ACT_CDE);
    $statut = STA_ACT_AVA;
    $sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
    $sel_statut.=htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
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

    $lignes = show_lig_cde_from_sug($sugchk);

    //complement formulaire
    $form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
    $form = str_replace('<!-- sel_date_liv -->', $sel_date_liv_mod, $form);
    $form = str_replace('<!-- sel_date_pay -->', $sel_date_pay_mod, $form);
    $form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
    $form = str_replace('<!-- bouton_val -->', $bt_val, $form);
    $form = str_replace('<!-- bouton_dup -->', '', $form);
    $form = str_replace('<!-- bouton_rec -->', '', $form);
    $form = str_replace('<!-- bouton_fac -->', '', $form);
    $form = str_replace('<!-- bouton_imp -->', '', $form);
    $form = str_replace('<!-- bouton_audit -->', '', $form);
    $form = str_replace('<!-- bouton_sol -->', '', $form);
    $form = str_replace('<!-- bouton_sup -->', '', $form);
    $form = str_replace('!!act_nblines!!', $lignes[0], $form);
    $form = str_replace('<!-- lignes -->', $lignes[1], $form);

    //Remplissage formulaire
    $form = str_replace('!!form_title!!', htmlentities($msg['acquisition_cde_cre'], ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_bibli!!', $id_bibli, $form);
    $form = str_replace('!!lib_bibli!!', $lib_bibli, $form);
    $form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
    $form = str_replace('!!lib_exer!!', htmlentities($exer->libelle, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_cde!!', 0, $form);
    $form = str_replace('!!date_cre!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def_lib!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def!!', preg_replace('/-/', '', today()), $form);
    $form = str_replace('!!numero!!', "", $form);
    $form = str_replace('!!nom_acte!!', '', $form);
    $form = str_replace('!!statut!!', $statut, $form);
    $form = str_replace('!!id_fou!!', 0, $form);
    $form = str_replace('!!lib_fou!!', '', $form);
    $form = str_replace('!!id_adr_fou!!', 0, $form);
    $form = str_replace('!!adr_fou!!', '', $form);
    $form = str_replace('!!date_liv!!', '', $form);
    $form = str_replace('!!date_liv_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!date_pay!!', '', $form);
    $form = str_replace('!!date_pay_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!num_pay!!', '', $form);
    $form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
    $form = str_replace('!!adr_liv!!', $adr_liv, $form);
    $form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
    $form = str_replace('!!adr_fac!!', $adr_fac, $form);
    $form = str_replace('!!comment!!', '', $form);
    $form = str_replace('!!comment_i!!', '', $form);
    $form = str_replace('!!ref!!', '', $form);
    $form = str_replace('!!id_dev!!', 0, $form);
    $form = str_replace('!!devise!!', $pmb_gestion_devise, $form);
    $form = str_replace('!!lien_dev!!', '', $form);
    $form = str_replace('!!liens_fac!!', '', $form);
    $form = str_replace('!!liens_liv!!', '', $form);
    print $form;

}

function get_empr_origins($origin_array){
    if($origin_array['type_origine'] == 1){
        return 1;
    }
    return 0;
}

//Affiche les lignes de commande depuis les suggestions
function show_lig_cde_from_sug($sugchk) {
    global $msg,$charset;
    global $acquisition_gestion_tva;
    global $modif_cde_row_form,$deflt3lgstatcde;
    global $others_applicants_line;
    global $first_applicant_line;
    global $applicants_common_tpl;

    $form = "
    <script type='text/javascript'>
        acquisition_force_ttc='".$msg["acquisition_force_ttc"]."';
        acquisition_force_ht='".$msg["acquisition_force_ht"]."';
    </script>
    ";
    $i=0;

    $lgstat_form=lgstat::getHtmlSelect(array(0=>$deflt3lgstatcde), false, array('id'=>'lg_statut[!!i!!]', 'name'=>'lg_statut[!!i!!]'));
    $arrchk = unserialize(rawurldecode(stripslashes($sugchk)));
    foreach($arrchk as $value) {
        $i++;
        $sug = new suggestions($value);
        $sugg_origins = $sug->getOrigines();
        $sugg_origins = array_filter($sugg_origins, 'get_empr_origins');

        $form.= $modif_cde_row_form;

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
                if ($sug->editeur != ""){
                    $taec.= "\n".htmlentities($sug->editeur,ENT_QUOTES,$charset);
                    if ($sug->date_publi != "0000-00-00") {
                        $taec.= ",".htmlentities($sug->date_publi,ENT_QUOTES,$charset);
                    }
                }
                $prix=htmlentities($sug->prix, ENT_QUOTES, $charset);
                break;
        }
        $applicants = '';
        if($typ_lig == 1 || $typ_lig == 2 || count($sugg_origins)){
            if(count($sugg_origins)){
                $j = 0;
                foreach($sugg_origins as $array_origin_details){
                    if($j == 0){ //Premier tour !
                        $empr_label = emprunteur::getName(array($array_origin_details['origine']));
                        $applicants = str_replace('!!applicant_label!!', $empr_label[$array_origin_details['origine']], $first_applicant_line);
                        $applicants = str_replace('!!applicant_id!!', $array_origin_details['origine'], $applicants);
                    }else{
                        $empr_label = emprunteur::getName(array($array_origin_details['origine']));
                        $others_applicants = str_replace('!!applicant_label!!', $empr_label[$array_origin_details['origine']], $others_applicants_line);
                        $others_applicants = str_replace('!!nb!!', $j, $others_applicants);
                        $others_applicants = str_replace('!!applicant_id!!', $array_origin_details['origine'], $others_applicants);
                        $applicants.= $others_applicants;
                    }
                    $j++;
                }
                $applicants = str_replace('!!std_applicants!!', $applicants, $applicants_common_tpl);
                $form = str_replace('!!applicants_tr!!', $applicants, $form);
            }else{
                $std_applicants = str_replace('!!applicant_label!!', '', $first_applicant_line);
                $std_applicants = str_replace('!!applicant_id!!', '', $std_applicants);
                $std_applicants = str_replace('!!std_applicants!!', '', $applicants_common_tpl);
                $form = str_replace('!!applicants_tr!!', $std_applicants, $form);
            }
            $form = str_replace('!!applicants_visibility!!', 'style="display:table-row;"', $form);
        }else{
            $std_applicants = str_replace('!!applicant_label!!', '', $first_applicant_line);
            $std_applicants = str_replace('!!applicant_id!!', '', $std_applicants);
            $std_applicants = str_replace('!!std_applicants!!', $std_applicants, $applicants_common_tpl);
            $form = str_replace('!!applicants_tr!!', $std_applicants, $form);
            $form = str_replace('!!applicants_visibility!!', 'style="display:none;"', $form);
        }
        $form = str_replace('!!no!!', $i, $form);
        $form = str_replace('!!code!!', $code, $form);
        $form = str_replace('!!lib!!', $taec, $form);
        $form = str_replace('!!qte!!', $sug->nb, $form);
        $form = str_replace('!!prix!!', $prix, $form);

        $onchange_tva = "";
        $convert_prix = "";
        $convert_ht_ttc = "";
        $force_ht_ttc = "";
        if ($acquisition_gestion_tva) {
            $form = str_replace('!!tva!!', $row->tva , $form);
            if ($acquisition_gestion_tva==1 ) {
                $prix_ttc=round($row->prix+($row->prix/100*$row->tva),2);
                $onchange_tva="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ht_to_ttc(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ttc."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value=''
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ttc_to_ht(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                '
                />";
            }elseif ($acquisition_gestion_tva==2 ) {
                $prix = floatval($row->prix);
                $tva = floatval($row->tva);
                $prix_ht = round( $prix / (($tva/100)+1), 2);
                $onchange_tva="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_prix="
                onChange='document.getElementById(\"convert_ht_ttc_$i\").innerHTML=
                ttc_to_ht(document.getElementById(\"prix[$i]\").value,document.getElementById(\"tva[$i]\").value);
                ' ";
                $convert_ht_ttc="
                <span class='convert_ht_ttc' id='convert_ht_ttc_$i'
                onclick='
                document.getElementById(\"input_convert_ht_ttc_$i\").value=\"\";
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"visible\";
                document.getElementById(\"input_convert_ht_ttc_$i\").focus();
                '
                >".$prix_ht."</span>
                <input style='visibility:hidden' type='text' id='input_convert_ht_ttc_$i' name='convert_ht_ttc_$i' value='$prix_ttc'
                onBlur='document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";'
                onChange='document.getElementById(\"prix[$i]\").value=
                ht_to_ttc(document.getElementById(\"input_convert_ht_ttc_$i\").value,document.getElementById(\"tva[$i]\").value);
                document.getElementById(\"input_convert_ht_ttc_$i\").style.visibility=\"hidden\";
                document.getElementById(\"convert_ht_ttc_$i\").innerHTML=document.getElementById(\"input_convert_ht_ttc_$i\").value;
                '
                />";
            }
            if ($row->debit_tva==1 ) {
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='1' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==1){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
                document.getElementById(\"force_debit[$i]\").value=2;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
            document.getElementById(\"force_debit[$i]\").value=1;
            }
            '
            >".$msg["acquisition_force_ht"]."</span>";
            }else{
                $force_ht_ttc="<br />
                <input type='hidden' id='force_debit[$i]' name='force_debit[$i]' value='2' />
                <span class='force_ht_ttc' id='force_ht_ttc_$i'
                onclick='
                if(document.getElementById(\"force_debit[$i]\").value==2){
                document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ht"]."\";
                document.getElementById(\"force_debit[$i]\").value=1;
            }else{
            document.getElementById(\"force_ht_ttc_$i\").innerHTML=\"".$msg["acquisition_force_ttc"]."\";
            document.getElementById(\"force_debit[$i]\").value=2;
            }
            '
            >".$msg["acquisition_force_ttc"]."</span>";
            }
        }
        $form = str_replace('!!onchange_tva!!', $onchange_tva, $form);
        $form = str_replace('!!convert_prix!!', $convert_prix, $form);
        $form = str_replace('!!convert_ht_ttc!!', $convert_ht_ttc, $form);
        $form = str_replace('!!force_ht_ttc!!', $force_ht_ttc, $form);
        $form = str_replace('!!rem!!', '0.00', $form);
        $lgstat=str_replace('!!i!!',$i,$lgstat_form);
        $form=str_replace('!!lgstat!!',$lgstat,$form);
        $form=str_replace('!!comment_lg!!',htmlentities($row->commentaires_gestion,ENT_QUOTES,$charset),$form);
        $form=str_replace('!!comment_lo!!',htmlentities($row->commentaires_opac,ENT_QUOTES,$charset),$form);

        $form = str_replace('!!typ!!', '0', $form);
        $form = str_replace('!!lib_typ!!', '', $form);

        $form = str_replace('!!rub!!', '0', $form);
        $form = str_replace('!!lib_rub!!', '', $form);
        $form = str_replace('!!id_sug!!', $sug->id_suggestion, $form);
        $form = str_replace('!!id_lig!!', '0', $form);
        $form = str_replace('!!typ_lig!!', $typ_lig, $form);
        $form = str_replace('!!id_prod!!', $sug->num_notice, $form);
    }
    $t = array(0=>$i, 1=>$form);
    return $t;
}

//Solde la commande en l'état
function sold_cde() {
    global $id_cde, $comment, $ref, $date_pay, $num_pay;

    if(!$id_cde) return;

    $cde = new actes($id_cde);

    //Commande considérée comme soldée
    $cde->statut = ($cde->statut & (~STA_ACT_ENC));
    $cde->statut = ($cde->statut | STA_ACT_REC);

    //Les quantites livrees sur la commande sont-elles entierement facturees
    //Si oui statut commande >> facture
    $tab_cde = actes::getLignes($id_cde);
    $facture = true;
    while(($row_cde = pmb_mysql_fetch_object($tab_cde))) {
        $tab_liv = lignes_actes::getLivraisons($row_cde->id_ligne);
        $tab_fac = lignes_actes::getFactures($row_cde->id_ligne);
        $nb_liv = 0;
        while (($row_liv = pmb_mysql_fetch_object($tab_liv))) {
            $nb_liv = $nb_liv + $row_liv->nb;
        }
        $nb_fac = 0;
        while(($row_fac = pmb_mysql_fetch_object($tab_fac))) {
            $nb_fac = $nb_fac + $row_fac->nb;
        }
        if ($nb_liv > $nb_fac) {
            $facture = false;
            break;
        }
    }

    if ($facture) {
        $cde->statut = ($cde->statut | STA_ACT_FAC); //Pas de reste à facturer >>Statut commande = facturée

        //Si de plus toutes les factures sont payées, Statut commande=payé
        $tab_pay = liens_actes::getChilds($id_cde, TYP_ACT_FAC);
        $paye= true;
        while (($row_pay = pmb_mysql_fetch_object($tab_pay))) {
            if(($row_pay->statut & STA_ACT_PAY) != STA_ACT_PAY){
                $paye = false;
                break;
            }
        }
        if ($paye) $cde->statut = ($cde->statut | STA_ACT_PAY);
    } else {
        $cde->statut = ($cde->statut & (~STA_ACT_FAC));	//Reste à facturer >>Statut commande = non facturée
    }
    $cde->numero=addslashes($cde->numero);
    $cde->commentaires = trim($comment);
    $cde->commentaires_i = addslashes($cde->commentaires_i);
    $cde->reference = trim($ref);
    $cde->date_paiement = $date_pay;
    $cde->num_paiement = trim($num_pay);
    $cde->devise = addslashes($cde->devise);
    $cde->save();
}

//Archive la commande
function arc_cde(){
    global $id_cde;

    if(!$id_cde) return;

    $cde = new actes($id_cde);

    //Commande archivée
    $cde->statut = ($cde->statut | STA_ACT_ARC);
    $cde->update_statut();

    //Archivage des factures et bl correspondants
    $list_childs = liens_actes::getChilds($id_cde);
    while (($row = pmb_mysql_fetch_object($list_childs))) {
        $act = new actes($row->num_acte_lie);
        $act->statut = ($act->statut | STA_ACT_ARC);
        $act->update_statut();
    }
}

//Sauvegarde commande
function update_cde() {
    global $id_bibli, $id_exer, $id_cde, $num_cde, $nom_acte, $id_dev, $statut;
    global $id_fou;
    global $id_adr_liv, $id_adr_fac;
    global $comment, $comment_i, $ref, $date_pay, $num_pay, $date_liv, $devise;
    global $code, $lib, $qte, $prix, $typ, $tva, $rem, $rub, $id_sug, $id_lig, $typ_lig, $id_prod, $applicants;
    global $acquisition_gestion_tva;
    global $action;
    global $force_debit;
    global $lg_statut,$comment_lg,$comment_lo;

    if (!$id_cde) {		//Creation de commande
        $cde = new actes();
        $cde ->type_acte = TYP_ACT_CDE;
        $cde->num_entite = $id_bibli;
        $cde->num_exercice = $id_exer;
        $cde->nom_acte = trim($nom_acte);
        if ($action == 'valid') {
            $cde->statut = STA_ACT_ENC;
        } else {
            $cde->statut = STA_ACT_AVA;
        }
        $cde->num_fournisseur = $id_fou;
        $cde->num_contact_livr = $id_adr_liv;
        $cde->num_contact_fact = $id_adr_fac;
        $cde->commentaires = trim($comment);
        $cde->commentaires_i = trim($comment_i);
        $cde->reference = trim($ref);
        $cde->date_paiement = $date_pay;
        $cde->num_paiement = trim($num_pay);
        $cde->date_ech = $date_liv;
        $cde->devise = trim($devise);
        $cde->save();

        $id_cde= $cde->id_acte;

        //creation des liens entre actes
        if ($id_dev) {
            $la = new liens_actes($id_dev, $id_cde);
        }

        //Recuperation des lignes valides
        $tab_lig=array();
        if (count($id_lig)) {
            foreach($id_lig as $k=>$v) {
                $code[$k] = trim($code[$k]);
                $lib[$k] = trim($lib[$k]);
                $comment_lg[$k] = trim($comment_lg[$k]);
                $comment_lo[$k] = trim($comment_lo[$k]);
                if ($code[$k] !='' || $lib[$k]!='') {
                    $tab_lig[$k]=$v;
                }
            }
        }

        //creation des lignes de commande
        foreach($tab_lig as $k=>$v) {
            $lig_cde = new lignes_actes();
            $lig_cde->type_ligne = $typ_lig[$k];
            $lig_cde->num_acte = $id_cde;
            $lig_cde->num_rubrique = $rub[$k];
            $lig_cde->num_produit = $id_prod[$k];
            $lig_cde->num_acquisition = $id_sug[$k];
            $lig_cde->num_type = $typ[$k];
            $lig_cde->code = trim($code[$k]);
            $lig_cde->libelle = trim($lib[$k]);
            $lig_cde->prix = $prix[$k];
            if ($acquisition_gestion_tva) {
                $lig_cde->tva = $tva[$k];
            } else {
                $lig_cde->tva = '0.00';
            }
            $lig_cde->debit_tva= $force_debit[$k];
            $lig_cde->remise = $rem[$k];
            $lig_cde->nb = round($qte[$k]);
            $lig_cde->date_ech = $date_liv;
            $lig_cde->date_cre = today();
            $lig_cde->statut = $lg_statut[$k];
            $lig_cde->commentaires_gestion=$comment_lg[$k];
            $lig_cde->commentaires_opac=$comment_lo[$k];
            $lig_cde->treatApplicants($applicants[$k]);
            $lig_cde->save();
            $lig_cde->saveApplicants();
        }

        //Mise à jour du statut des suggestions et envoi email suivi de suggestion
        $sug_map = new suggestions_map();
        $sug_map->doTransition('ORDERED', $id_sug);
    } else {	//modification de commande
        $cde = new actes($id_cde);
        $old_statut=$statut;

        if ($old_statut != STA_ACT_ENC) {	//Commande a valider
            $cde->nom_acte = trim($nom_acte);
            if ($action == 'valid') {
                $cde->date_valid = date("Y-m-d");
                $cde->statut = STA_ACT_ENC; //Statut commande = A valider->en cours
            }
            $cde->num_fournisseur = $id_fou;
            $cde->num_contact_livr = $id_adr_liv;
            $cde->num_contact_fact = $id_adr_fac;
            $cde->commentaires = trim($comment);
            $cde->commentaires_i = trim($comment_i);
            $cde->reference = trim($ref);
            $cde->date_paiement = $date_pay;
            $cde->num_paiement = trim($num_pay);
            $cde->date_ech = $date_liv;
            $cde->devise = trim($devise);
            $cde->save();

            //Recuperation des lignes valides
            $tab_lig=array();
            if (count($id_lig)) {
                foreach($id_lig as $k=>$v) {
                    $code[$k] = trim($code[$k]);
                    $lib[$k] = trim($lib[$k]);
                    $comment_lg[$k] = trim($comment_lg[$k]);
                    $comment_lo[$k] = trim($comment_lo[$k]);
                    if ($code[$k] !='' || $lib[$k]!='') {
                        $tab_lig[$k]=$v;
                    }
                }
            }

            //maj des lignes de commande
            foreach($tab_lig as $k=>$v) {
                $lig_cde = new lignes_actes($v);
                $lig_cde->type_ligne = $typ_lig[$k];
                $lig_cde->num_acte = $id_cde;
                $lig_cde->num_rubrique = $rub[$k];
                $lig_cde->num_produit = $id_prod[$k];
                $lig_cde->num_acquisition = $id_sug[$k];
                $lig_cde->num_type = $typ[$k];
                $lig_cde->code = trim($code[$k]);
                $lig_cde->libelle = trim($lib[$k]);
                $lig_cde->prix = $prix[$k];
                if ($acquisition_gestion_tva) {
                    $lig_cde->tva = $tva[$k];
                } else {
                    $lig_cde->tva = '0.00';
                }
                $lig_cde->debit_tva= $force_debit[$k];
                $lig_cde->remise = $rem[$k];
                $lig_cde->nb = round($qte[$k]);

                $lig_cde->date_ech = $date_liv;
                $lig_cde->date_cre = today();
                $lig_cde->statut = $lg_statut[$k];
                $lig_cde->commentaires_gestion=$comment_lg[$k];
                $lig_cde->commentaires_opac=$comment_lo[$k];
                $lig_cde->treatApplicants($applicants[$k]);
                $lig_cde->save();
                $lig_cde->saveApplicants();
                if($v==0) $tab_lig[$k]=$lig_cde->id_ligne;
            }
            //suppression des lignes non reprises
            $cde->cleanLignes($id_cde, $tab_lig);
        } else {	//Commande validee
            $cde->numero=addslashes($cde->numero);
            $cde->commentaires = trim($comment);
            $cde->commentaires_i = addslashes($cde->commentaires_i);
            $cde->reference = trim($ref);
            $cde->date_paiement = $date_pay;
            $cde->num_paiement = trim($num_pay);
            $cde->devise = addslashes($cde->devise);
            $cde->save();

            //Recuperation des lignes
            $tab_lig=array();
            if (count($id_lig)) {
                foreach($id_lig as $k=>$v) {
                    $comment_lg[$k] = trim($comment_lg[$k]);
                    $comment_lo[$k] = trim($comment_lo[$k]);
                    $tab_lig[$k]=$v;
                }
            }

            //maj des lignes de commande
            foreach($tab_lig as $k=>$v) {

                $lig_cde = new lignes_actes($v);
                $lig_cde->code = addslashes($lig_cde->code);
                $lig_cde->libelle = addslashes($lig_cde->libelle);
                $lig_cde->statut = $lg_statut[$k];
                $lig_cde->commentaires_gestion=$comment_lg[$k];
                $lig_cde->commentaires_opac=$comment_lo[$k];
                $lig_cde->treatApplicants($applicants[$k]);
                $lig_cde->save();
                $lig_cde->saveApplicants();
            }
        }
    }
}

//Duplication de commande
function duplicate_cde($id_bibli, $id_cde) {
    global $msg, $charset;
    global $modif_cde_form;
    global $bt_enr, $bt_val;
    global $sel_date_liv_mod, $sel_date_pay_mod;

    //Recuperation etablissement
    $bibli = new entites($id_bibli);
    $lib_bibli = htmlentities($bibli->raison_sociale, ENT_QUOTES, $charset);

    $form = $modif_cde_form;

    //Recuperation commande
    $cde = new actes($id_cde);
    $exer = new exercices($cde->num_exercice);
    $lib_exer = htmlentities($exer->libelle, ENT_QUOTES, $charset);

    $numero = calcNumero($id_bibli, TYP_ACT_CDE);
    $statut = STA_ACT_AVA;
    $sel_statut = "<input type='hidden' id='statut' name='statut' value='".$statut."' />";
    $sel_statut.=htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
    $id_fou = $cde->num_fournisseur;
    $fou = new entites($id_fou);
    $coord = entites::get_coordonnees($fou->id_entite, '1');
    if (pmb_mysql_num_rows($coord) != 0) {
        $coord = pmb_mysql_fetch_object($coord);
        $id_adr_fou = $coord->id_contact;
        $adr_fou = coordonnees::get_formatted_address_form_coord($coord);
    } else {
        $id_adr_fou = '0';
        $adr_fou = '';
    }
    $id_adr_fac = $cde->num_contact_fact;
    if ($id_adr_fac) {
        $coord_fac = new coordonnees($id_adr_fac);
        $adr_fac = $coord_fac->get_formatted_address();
    } else {
        $id_adr_fac = '0';
        $adr_fac = '';
    }
    $id_adr_liv = $cde->num_contact_livr;
    if ($id_adr_liv) {
        $coord_liv = new coordonnees($id_adr_liv);
        $adr_liv = $coord_liv->get_formatted_address();
    } else {
        $id_adr_liv = '0';
        $adr_liv = '';
    }
    $id_dev = liens_actes::getDevis($id_cde);
    if ($id_dev) {
        $dev=new actes($id_dev);
        $lien_dev = "<a href=\"./acquisition.php?categ=ach&sub=devi&action=modif&id_bibli=".$id_bibli."&id_dev=".$id_dev."\">".$dev->numero."</a>";
    }

    $lignes= show_lig_cde($id_cde);

    $id_cde=0;

    //complement formulaire
    $form = str_replace('<!-- sel_statut -->', $sel_statut, $form);
    $form = str_replace('<!-- sel_date_liv -->', $sel_date_liv_mod, $form);
    $form = str_replace('<!-- sel_date_pay -->', $sel_date_pay_mod, $form);
    $form = str_replace('<!-- bouton_enr -->', $bt_enr, $form);
    $form = str_replace('<!-- bouton_val -->', $bt_val, $form);
    $form = str_replace('<!-- bouton_dup -->', '', $form);
    $form = str_replace('<!-- bouton_rec -->', '', $form);
    $form = str_replace('<!-- bouton_fac -->', '', $form);
    $form = str_replace('<!-- bouton_imp -->', '', $form);
    $form = str_replace('<!-- bouton_audit -->', '', $form);
    $form = str_replace('<!-- bouton_sol -->', '', $form);
    $form = str_replace('<!-- bouton_sup -->', '', $form);
    $form = str_replace('!!act_nblines!!', $lignes[0], $form);
    $form = str_replace('<!-- lignes -->', $lignes[1], $form);

    //Remplissage formulaire
    $form = str_replace('!!form_title!!', htmlentities($msg['acquisition_cde_cre'], ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_bibli!!', $id_bibli, $form);
    $form = str_replace('!!lib_bibli!!', $lib_bibli, $form);
    $form = str_replace('!!id_exer!!', $exer->id_exercice, $form);
    $form = str_replace('!!lib_exer!!', $lib_exer, $form);
    $form = str_replace('!!id_cde!!', $id_cde, $form);
    $form = str_replace('!!date_cre!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def_lib!!', formatdate(today()), $form);
    $form = str_replace('!!dat_def!!', preg_replace('/-/', '', today()), $form);
    $form = str_replace('!!numero!!', $numero, $form);
    $form = str_replace('!!nom_acte!!', htmlentities($cde->nom_acte, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!statut!!', $statut, $form);
    $form = str_replace('!!id_fou!!', $id_fou, $form);
    $form = str_replace('!!lib_fou!!', htmlentities($fou->raison_sociale, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_adr_fou!!', $id_adr_fou, $form);
    $form = str_replace('!!adr_fou!!', $adr_fou, $form);
    $form = str_replace('!!date_liv!!', '', $form);
    $form = str_replace('!!date_liv_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!date_pay!!', '', $form);
    $form = str_replace('!!date_pay_lib!!', $msg['parperso_nodate'], $form);
    $form = str_replace('!!num_pay!!', '', $form);
    $form = str_replace('!!id_adr_liv!!', $id_adr_liv, $form);
    $form = str_replace('!!adr_liv!!', $adr_liv, $form);
    $form = str_replace('!!id_adr_fac!!', $id_adr_fac, $form);
    $form = str_replace('!!adr_fac!!', $adr_fac, $form);
    $form = str_replace('!!comment!!', '', $form);
    $form = str_replace('!!comment_i!!', htmlentities($cde->commentaires_i, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!ref!!', htmlentities($cde->reference, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!id_dev!!', $id_dev, $form);
    $form = str_replace('!!devise!!', htmlentities($cde->devise, ENT_QUOTES, $charset), $form);
    $form = str_replace('!!lien_dev!!', $lien_dev, $form);
    $form = str_replace('!!liens_fac!!', '', $form);
    $form = str_replace('!!liens_liv!!', '', $form);
    print $form;
}

//TODO Verifier que budgets saisis si obligatoires
//TODO A transferer depuis frame
//Vérification dépassement de budget
function verif_bud() {
    global $msg;
    global $max_lig;
    global $qte, $prix, $rem, $rub;
    global $error, $error_msg;
    global $acquisition_budget;

    if ($acquisition_budget) {
        $tot_rub = array();
        $tot_bud = array();
        for ($i=1;$i<=$max_lig;$i++) {
            $tot_rub[$rub[$i]] = 0;
        }

        //récupère le total de la commande par rubrique
        for ($i=1;$i<=$max_lig;$i++) {
            $tot_rub[$rub[$i]] = $tot_rub[$rub[$i]] + ( $qte[$i]*$prix[$i]*(1 - ($rem[$i]/100) ) );
        }

        //récupère le total de la commande par budget
        foreach ($tot_rub as $key=>$value) {
            $r = new rubriques($key);
            if (!array_key_exists($r->num_budget, $tot_bud)) $tot_bud[$r->num_budget] = 0;
            $tot_bud[$r->num_budget] = $tot_bud[$r->num_budget] + $value;
        }

        //Vérifie que les budgets affectés par rubrique ne sont pas dépassés
        foreach ($tot_rub as $key=>$value) {
            $r = new rubriques($key);
            $b = new budgets($r->num_budget);
            if ( $b->type_budget == TYP_BUD_RUB ) {	//Budget affecté par rubrique
                $mnt_rub = $r->montant;
                $eng_rub = rubriques::calcEngagement($key) + $value;
                //Budget rubrique dépassé ?
                if ($eng_rub > $mnt_rub) {
                    $error = true;
                    $tab_rub = rubriques::listAncetres($key, true);
                    $lib_rub = $b->libelle.":";
                    foreach ($tab_rub as $value) {
                        $lib_rub.= $value[1];
                        if($value[0] != $key) $lib_rub.= ":";
                    }
                    $error_msg = $msg['acquisition_rub']." :\\n\\n ".$lib_rub."\\n\\n".$msg['acquisition_act_bud_dep'];
                    break;
                }
            }
        }

        //Vérifie que les budgets affectés globalement ne sont pas dépassés
        foreach ($tot_bud as $key=>$value) {
            $b = new budgets($key);
            if ( $b->type_budget == TYP_BUD_GLO ) {
                $mnt_bud = $b->montant_global;
                $eng_bud = budgets::calcEngagement($b->id_budget) + $value;
                //Budget dépassé ?
                if ($eng_bud > $mnt_bud) {
                    $error = true;
                    $error_msg = $msg['acquisition_act_tab_bud']." : ".$b->libelle."\\n\\n".$msg['acquisition_act_bud_dep'];
                    break;
                }
            }
        }
    }
}

function print_cde($id_bibli=0, $id_cde=0, $by_mail=false) {
    global $msg, $charset, $base_path, $acquisition_pdfcde_print;
    global $acquisition_pdfcde_obj_mail, $acquisition_pdfcde_text_mail;
    global $acquisition_pdfcde_by_mail,$PMBuseremailbcc;

    if (!($id_bibli && $id_cde)) return;

    $bib = new entites($id_bibli);
    $bib_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_bibli,1));

    $cde = new actes($id_cde);

    $id_fou = $cde->num_fournisseur;
    $fou = new entites($id_fou);
    $fou_coord = pmb_mysql_fetch_object(entites::get_coordonnees($id_fou,1));

    $no_mail=false;
    if ( $by_mail==false || !($acquisition_pdfcde_by_mail && strpos($bib_coord->email,'@') && strpos($fou_coord->email,'@')) ) {
        $no_mail=true;
    } else {
        $mail_accounting_commande = new mail_accounting_commande();
        $mail_accounting_commande->set_id_bibli($id_bibli);
        $mail_accounting_commande->set_id_acte($id_cde);
        $res_envoi = $mail_accounting_commande->send_mail();
        if (!$res_envoi) {
            $no_mail=true;
        }
        if (!$no_mail) {
            print "<h3>".sprintf($msg["acquisition_print_emailsucceed"],$mail_accounting_commande->get_dest_mail())."</h3>";
        } else {
            print "<h3>".sprintf($msg["acquisition_print_emailfailed"],$mail_accounting_commande->get_dest_mail())."</h3>";
        }
    }
    if ($no_mail) {
        print "
            <form name='print_cde' action='pdf.php?pdfdoc=cmde' target='lettre' method='post'>
                <input type='hidden' name='id_bibli' value='".$id_bibli."'/>
                <input type='hidden' name='id_cde' value='".$id_cde."'/>
                <script type='text/javascript'>
                    openPopUp('','lettre');
                    document.print_cde.submit();
                </script>
            </form>";
    }
}

accounting_commandes_controller::set_id_bibli($id_bibli);
if(!empty($id_exer)) {
    accounting_commandes_controller::set_id_exercice($id_exer);
} else {
    accounting_commandes_controller::set_id_exercice($id_exercice);
}
accounting_commandes_controller::set_id_acte($id_cde);
accounting_commandes_controller::proceed($id);
?>