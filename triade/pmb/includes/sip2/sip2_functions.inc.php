<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sip2_functions.inc.php,v 1.33.4.3.2.1 2025/05/23 14:14:28 qvarin Exp $

global $class_path;
require_once($class_path."/emprunteur.class.php");
require_once($class_path."/expl.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/ajax_pret.class.php");
require_once("$class_path/ajax_retour_class.php");
require_once("$class_path/quotas.class.php");
require_once("$class_path/expl_to_do.class.php");
// pour debug faire: header("Log-message: toto");

define('SIP2_CIRCULATION_STATUS_OTHER', '01');
define('SIP2_CIRCULATION_STATUS_ON_ORDER', '02');
define('SIP2_CIRCULATION_STATUS_AVAILABLE', '03');
define('SIP2_CIRCULATION_STATUS_CHARGED', '04');
define('SIP2_CIRCULATION_STATUS_CHARGED_NOT_BE_RECALLED', '05');
define('SIP2_CIRCULATION_STATUS_IN_PROCESS', '06');
define('SIP2_CIRCULATION_STATUS_RECALLED', '07');
define('SIP2_CIRCULATION_STATUS_WAITING_ON_HOLD_SHELF', '08');
define('SIP2_CIRCULATION_STATUS_WAITING_TO_BE_RESHELVED', '09');
define('SIP2_CIRCULATION_STATUS_IN_TRANSIT', '10');
define('SIP2_CIRCULATION_STATUS_CLAIMED_RETURNED', '11');
define('SIP2_CIRCULATION_STATUS_LOST', '12');
define('SIP2_CIRCULATION_STATUS_MISSING', '13');
define('SIP2_CIRCULATION_STATUS_LOANED_TODAY', '14');

function _login_response_($values) {
	return [ 'OK' => '0' ];
}

function _acs_status_($values) {
    global $id,$opac_pret_prolongation,$deflt_docs_location;
    $_SESSION[$id]["STATUS"] = $values;

	$ret=[];
    $ret["ON_LINE_STATUS"] = "Y";
    $ret["CHECKIN_OK"] = "Y";
    $ret["CHECKOUT_OK"] = "Y";
    //if ($opac_pret_prolongation) $ret["ACS_RENEWAL_POLICY"]="Y"; else $ret["ACS_RENEWAL_POLICY"]="N";
    $ret["ACS_RENEWAL_POLICY"] = "Y";
    $ret["STATUS_UPDATE_OK"] = "Y";
    $ret["OFF_LINE_OK"] = "N";
    $ret["TIMEOUT_PERIOD"] = "300";
    $ret["RETRIES_ALLOWED"] = "003";
    $ret["DATE_TIME_SYNC"] = date("Ymd    His", time());
    $ret["PROTOCOL_VERSION"] = "2.00";
    //Champs variables
    $requete = "select location_libelle from docs_location where idlocation=".$deflt_docs_location;
    $resultat = pmb_mysql_query($requete);
    if (pmb_mysql_num_rows($resultat)) {
        $ret["INSTITUTION_ID"][0] = pmb_mysql_result($resultat, 0, 0);
    } else {
        $ret["INSTITUTION_ID"][0] = $deflt_docs_location;
    }
    $requete = "select location_libelle from docs_location where idlocation=".$deflt_docs_location;
    $resultat = pmb_mysql_query($requete);
    if ($resultat) {
        $ret["LIBRARY_NAME"][0] = pmb_mysql_result($resultat, 0, 0);
    }
    if($opac_pret_prolongation) {//Modification de l'avant dernier champ BX à Y si prolongation possible ->3M
        $ret["SUPPORTED_MESSAGES"][0] = "YYYYYYNYYNYYYYYN";
    } else {
        $ret["SUPPORTED_MESSAGES"][0] = "YYYYYYNYYNYYYYNN";
    }
    return $ret;
}

function _patron_information_response_($values) {
	global $id,$lang,$opac_resa,$msg,$pmb_gestion_devise;
    global $see_all_pret;
    global $selfservice_pret_carte_invalide_msg;

    $ret = [];
    $see_all_pret = 1;

    (string)$rep_lang = (string)$values["LANGUAGE"];
    switch ((string)$values["LANGUAGE"]) {
        case "001":
            $lang = "en_UK";
            break;
        case "002":
            $lang = "fr_FR";
            break;
        case "008":
            $lang = "es_ES";
            break;
        case "004":
            $lang = "it_IT";
            break;
        default:
            (string)$rep_lang = "000";
            break;
    }

    //Recherche dans la localisation
    $localisation = $values["INSTITUTION_ID"][0];
    $empr_cb = $values["PATRON_IDENTIFIER"][0];
    $empr_pwd = $values["PATRON_PASSWORD"][0];

    $requete = "select id_empr from empr where empr_cb='".addslashes($empr_cb)."'";

    $resultat = pmb_mysql_query($requete);
    if (pmb_mysql_num_rows($resultat)) {
        $id_empr = pmb_mysql_result($resultat, 0, 0);
        $empr = new emprunteur($id_empr, '', '', 1);

        if (!$localisation) {
            $localisation = $empr->empr_location_l;
        }

        //Calcul des summary
        //print_r($empr);
        $ret["CHARGED_ITEMS_COUNT"] = str_pad(count($empr->prets), 4, "0", STR_PAD_LEFT); //str_pad($empr->nb_reservations,4,"0",STR_PAD_LEFT);
        $ret["OVERDUE_ITEMS_COUNT"] = str_pad($empr->retard, 4, "0", STR_PAD_LEFT);
        $nb_total_resa = $empr->nb_reservations;
        $rqt_resas = pmb_mysql_query("select count(id_resa) as nb from resa where resa_idempr=$id_empr and resa_confirmee=0 group by resa_confirmee");
        if (pmb_mysql_num_rows($rqt_resas)) {
            $nb_resa_non_confirmes = pmb_mysql_result($rqt_resas, 0, 0);
        } else {
            $nb_resa_non_confirmes = 0;
        }

        $ret["HOLD_ITEMS_COUNT"] = str_pad($nb_total_resa - $nb_resa_non_confirmes, 4, "0", STR_PAD_LEFT);
        if($nb_total_resa - $nb_resa_non_confirmes) {// En test avec 3M
            $ret["SCREEN_MESSAGE"][0] = $msg["selfservice_resa_dispo"];
        }

		if ($empr->nb_amendes) {
            $ret["FINE_ITEMS_COUNT"] = str_pad($empr->nb_amendes, 4, "0", STR_PAD_LEFT);
        } else {
            $ret["FINE_ITEMS_COUNT"] = "    ";
        }

		$ret["RECALL_ITEMS_COUNT"] = "    ";
        $ret["UNAVAILABLE_HOLDS_COUNT"] = str_pad($nb_resa_non_confirmes, 4, "0", STR_PAD_LEFT);

		$pret = ($empr->blocage_retard || $empr->blocage_amendes || $empr->blocage_abt || (!$empr->allow_loan) ? "Y" : " ");
        $patron_status = " ".($empr->allow_prol ? " " : "Y")." ".$pret."  ";
        $patron_status .= ($empr->blocage_retard ? "Y Y" : "   ")." ".($empr->blocage_amendes ? "Y" : " ").(($empr->blocage_abt || $empr->blocage_tarifs) ? "Y" : " ")."  ";

		$ret["PATRON_IDENTIFIER"][0] = $empr->cb;
        $ret["PERSONAL_NAME"][0] = $empr->prenom." ".$empr->nom;
		$ret["VALID_PATRON"][0] = 'Y';

		add_open_plus_field($ret, $empr->id);

		if ($empr->pwd == $empr_pwd) {
            $ret["VALID_PATRON_PASSWORD"][0] = "Y";
        } else {
            $ret["VALID_PATRON_PASSWORD"][0] = "N";
        }

		if ($total = $empr->compte_amendes + $empr->amendes_en_cours) {
            $ret["FEE_AMOUNT"][0] = $total;
        }

		$ret["HOME_ADDRESS"][0] = $empr->adr1."\n".$empr->adr2."\n".$empr->cp." ".$empr->ville;
        if ($empr->tel1) {
            $ret["HOME_PHONE_NUMBER"][0] = $empr->tel1;
        }

		if ($empr->mail) {
            $ret["EMAIL_ADDRESS"][0] = $empr->mail;
        }

        //Envoie des infos exemplaires selon demande
        $p = strpos($values["SUMMARY"], "Y");
        if ($p !== false) {
            switch ($p) {
                case 0:
                    //Ouvrages réservés dispos
                    $rqt_resa = "select resa_cb from resa where resa_idempr=$id_empr and resa_confirmee=1";
                    $res_resa = pmb_mysql_query($rqt_resa);
                    $nb_resa = pmb_mysql_num_rows($res_resa);
                    $resas = [];
                    while ($resa = pmb_mysql_fetch_object($res_resa)) {
                        $resas[] = $resa->resa_cb;
                    }
                    $n = 0;
                    if ($values["START_ITEM"][0]) {
                        $start = $values["START_ITEM"][0] - 1;
                    } else {
                        $start = 0;
                    }
                    if ($values["END_ITEM"][0]) {
                        $end = $values["END_ITEM"][0];
                    } else {
                        $end = $nb_resa;
                    }
                    for ($i = $start; $i < $end; $i++) {
                        //$ret["CHARGED_ITEMS"][$n]="retour le : ".$empr->prets[$i]["date_retour"].": ".$empr->prets[$i]["libelle"];
                        $ret["HOLD_ITEMS"][$n] = $resas[$i];
                        $n++;
                    }
                    break;
                case 2:
                    //Ouvrages en prêt
                    $n = 0;
                    if ($values["START_ITEM"][0]) {
                        $start = $values["START_ITEM"][0] - 1;
                    } else {
                        $start = 0;
                    }
                    //Modification à la demande de Nedap pour le passage à l'UHF
                    if ($values["END_ITEM"][0]) {
                        if($values["END_ITEM"][0] > count($empr->prets)) {
                            $end = count($empr->prets);
                        } else {
                            $end = $values["END_ITEM"][0];
                        }
                    } else {
                        $end = count($empr->prets);
                    }
                    for ($i = $start; $i < $end; $i++) {
                        //$ret["CHARGED_ITEMS"][$n]="retour le : ".$empr->prets[$i]["date_retour"].": ".$empr->prets[$i]["libelle"];
                        $ret["CHARGED_ITEMS"][$n] = $empr->prets[$i]["cb"];
                        $n++;
                    }
                    break;
                case 1:
                    //Ouvrages en retard
                    $n = 0;
                    if ($values["START_ITEM"][0]) {
                        $start = $values["START_ITEM"][0] - 1;
                    } else {
                        $start = 0;
                    }
                    if ($values["END_ITEM"][0]) {
                        $end = $values["END_ITEM"][0];
                    } else {
                        $end = $empr->retard;
                    }
                    for ($i = 0; $i < count($empr->prets); $i++) {
                        if ($empr->prets[$i]["pret_retard"]) {
                            if (($n == $start) && ($start < $end)) {
                                //$ret["OVERDUE_ITEMS"][$n]="retour le : ".$empr->prets[$i]["date_retour"].": ".$empr->prets[$i]["libelle"];
                                $ret["OVERDUE_ITEMS"][$n] = $empr->prets[$i]["cb"];
                                $n++;
                                $start++;
                            }
                        }
                    }
                    break;
                case 3:
                    //Ouvrages en amende
                    break;
                case 5:
                    //Ouvrages réservés non dispos
                    $rqt_resa = "select resa_idnotice,resa_idbulletin from resa where resa_idempr=$id_empr and resa_confirmee=0";
                    $res_resa = pmb_mysql_query($rqt_resa);
                    $nb_resa = pmb_mysql_num_rows($res_resa);
                    $resas = [];
                    while ($resa = pmb_mysql_fetch_object($res_resa)) {
                        if ($resa->resa_idnotice) {
                            //Récupération d'un exemplaire au hasard de la notice
                            $rqt_expl = "select expl_cb from exemplaires where expl_notice=".$resa->resa_idnotice." limit 1";
                            $resa_cb = pmb_mysql_result(pmb_mysql_query($rqt_expl), 0, 0);
                        } else {
                            //Récupération d'un exemplaire au hasard d'un bulletin
                            $rqt_expl = "select expl_cb from exemplaires where expl_bulletin=".$resa->resa_idbbulletin." limit 1";
                            $resa_cb = pmb_mysql_result(pmb_mysql_query($rqt_expl), 0, 0);
                        }
                        $resas[] = $resa_cb;
                    }
                    $n = 0;
                    if ($values["START_ITEM"][0]) {
                        $start = $values["START_ITEM"][0] - 1;
                    } else {
                        $start = 0;
                    }
                    if ($values["END_ITEM"][0]) {
                        $end = $values["END_ITEM"][0];
                    } else {
                        $end = $nb_resa;
                    }
                    for ($i = $start; $i < $end; $i++) {
                        //$ret["CHARGED_ITEMS"][$n]="retour le : ".$empr->prets[$i]["date_retour"].": ".$empr->prets[$i]["libelle"];
                        $ret["UNAVAILABLE_HOLD_ITEMS"][$n] = $resas[$i];
                        $n++;
                    }
                    break;
            }
        }

        if($ret["FEE_AMOUNT"][0]) {
            $aff = str_replace("!!solde!!", $ret["FEE_AMOUNT"][0]." ".$pmb_gestion_devise, $msg["selfservice_pret_carte_amendes"]);
            $ret["SCREEN_MESSAGE"][0] = $aff;
        }

    } else {
        $patron_status = "              ";
        $ret["PATRON_IDENTIFIER"][0] = $empr_cb;
        $ret["PERSONAL_NAME"][0] = " ";
        $ret["VALID_PATRON"][0] = "N";
        //Calcul des summary
        $ret["HOLD_ITEMS_COUNT"] = "    ";
        $ret["OVERDUE_ITEMS_COUNT"] = "    ";
        $ret["CHARGED_ITEMS_COUNT"] = "    ";
        $ret["FINE_ITEMS_COUNT"] = "    ";
        $ret["RECALL_ITEMS_COUNT"] = "    ";
        $ret["UNAVAILABLE_HOLDS_COUNT"] = "    ";
        $ret["SCREEN_MESSAGE"][0] = $selfservice_pret_carte_invalide_msg;
    }
    $ret["PATRON_STATUS"] = $patron_status;
    /*
    patron réponse: (14 caractères) Vu avec 3M
    1er si Y -> Pas d'emprunt (Dans PMB cela ce fait au niveau du prêt)
    2ème si Y -> Pas de prolongation
    3ème -> Toujours à vide
    4éme si Y -> Réservation
    5ème -> Toujours à vide
    6ème -> Toujours à vide
    7éme à Y si trop de retard
    8ème -> Toujours à vide
    9éme à Y si trop de retard
    10ème  -> Toujours à vide
    11éme à Y si trop d'amande
    12éme à Y si pas payé abonnement
    13ème  -> Toujours à vide
    14ème  -> Toujours à vide
    */
    $ret["LANGUAGE"] = $rep_lang;
    $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
    $ret["INSTITUTION_ID"][0] = $localisation;
    return $ret;
}

function _patron_status_response_($values) {
	global $id,$lang,$opac_resa,$msg,$pmb_gestion_devise;
    global $see_all_pret;
    global $selfservice_pret_carte_invalide_msg;

    $ret = [];
    $see_all_pret = 1;

    (string)$rep_lang = (string)$values["LANGUAGE"];
    switch ((string)$values["LANGUAGE"]) {
        case "001":
            $lang = "en_UK";
            break;
        case "002":
            $lang = "fr_FR";
            break;
        case "008":
            $lang = "es_ES";
            break;
        case "004":
            $lang = "it_IT";
            break;
        default:
            (string)$rep_lang = "000";
            break;
    }
    //Recherche
    $localisation = $values["INSTITUTION_ID"][0];
    $empr_cb = $values["PATRON_IDENTIFIER"][0];
    $empr_pwd = $values["PATRON_PASSWORD"][0];

    $requete = "select id_empr from empr where empr_cb='".addslashes($empr_cb)."'";
    $resultat = pmb_mysql_query($requete);
    if (pmb_mysql_num_rows($resultat)) {
        $id_empr = pmb_mysql_result($resultat, 0, 0);
        $empr = new emprunteur($id_empr, '', '', 1);
        if (!$localisation) {
            $localisation = $empr->empr_location_l;
        }
        $pret = ($empr->blocage_retard || $empr->blocage_amendes || $empr->blocage_abt || (!$empr->allow_loan) ? "Y" : " ");
        $patron_status = " ".($empr->allow_prol ? " " : "Y")."Y".$pret."  ";
        $patron_status .= ($empr->blocage_retard ? "Y Y" : "   ")." ".($empr->blocage_amendes ? "Y" : " ").(($empr->blocage_abt || $empr->blocage_tarifs) ? "Y" : " ")."  ";

        $ret["PATRON_IDENTIFIER"][0] = $empr->cb;
        $ret["PERSONAL_NAME"][0] = $empr->prenom." ".$empr->nom;
        $ret["VALID_PATRON"][0] = "Y";

		add_open_plus_field($ret, $empr->id);

        if ($empr->pwd == $empr_pwd) {
            $ret["VALID_PATRON_PASSWORD"][0] = "Y";
        } else {
            $ret["VALID_PATRON_PASSWORD"][0] = "N";
        }
        if ($total = $empr->compte_amendes + $empr->amendes_en_cours) {
            $ret["FEE_AMOUNT"][0] = $total;
        }

        if($ret["FEE_AMOUNT"][0]) {
            $aff = str_replace("!!solde!!", $ret["FEE_AMOUNT"][0]." ".$pmb_gestion_devise, $msg["selfservice_pret_carte_amendes"]);
            $ret["SCREEN_MESSAGE"][0] = $aff;
        }
    } else {
        $patron_status = "              ";
        $ret["PATRON_IDENTIFIER"][0] = $empr_cb;
        $ret["PERSONAL_NAME"][0] = " ";
        $ret["VALID_PATRON"][0] = "N";
        $ret["SCREEN_MESSAGE"][0] = $selfservice_pret_carte_invalide_msg;
    }
    $ret["PATRON_STATUS"] = $patron_status;
    $ret["LANGUAGE"] = $rep_lang;
    $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
    $ret["INSTITUTION_ID"][0] = $localisation;
    return $ret;
}

function _end_session_response_($values) {
	global $id,$lang,$opac_resa;

    $ret = [];
    $localisation = $values["INSTITUTION_ID"][0];
    $empr_cb = $values["PATRON_IDENTIFIER"][0];

    $requete = "select id_empr from empr where empr_cb='".addslashes($empr_cb)."'";
    $resultat = pmb_mysql_query($requete);
    if (pmb_mysql_num_rows($resultat)) {
        $id_empr = pmb_mysql_result($resultat, 0, 0);
        $empr = new emprunteur($id_empr, '', '', 1);
        if (!$localisation) {
            $localisation = $empr->empr_location_l;
        }
        $ret["PATRON_IDENTIFIER"][0] = $empr->cb;
    } else {
        $ret["PATRON_IDENTIFIER"][0] = $empr_cb;
    }
    $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
    $ret["INSTITUTION_ID"][0] = $localisation;
    $ret["END_SESSION"] = "Y";
    return $ret;
}

function _item_information_response_($values) {
    global $msg;
    $expl_cb = $values["ITEM_IDENTIFIER"][0];
    global $selfservice_pret_carte_invalide_msg;
    global $selfservice_pret_pret_interdit_msg;
    global $selfservice_pret_deja_prete_msg;
    global $selfservice_pret_deja_reserve_msg;
    global $selfservice_pret_quota_bloc_msg;
    global $selfservice_pret_non_pretable_msg;
    global $selfservice_pret_expl_inconnu_msg;

    $ret = [];
    $requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, ";
    $requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
    $requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
    $requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
    $requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
    $requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
    $requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
    $requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
    $requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
    $requete .= " WHERE expl_cb='".addslashes($expl_cb)."' ";
    $requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";

    $resultat = pmb_mysql_query($requete);
    if (pmb_mysql_num_rows($resultat)) {
        //Calcul du statut
        $expl = pmb_mysql_fetch_object($resultat);
        if ($expl->pret_flag) {

            if($expl->pret_retour) {
                $now = new DateTime();
                $date = new DateTime($expl->pret_date);

                if ($date->format('Ymd') != $now->format('Ymd')) {
                    // Le document deja prete, on met une erreur
                    $statut = SIP2_CIRCULATION_STATUS_OTHER;
                    $error = true;
                    $error_message = $selfservice_pret_deja_prete_msg;
                } else {
                    // Le document a ete prete aujourd'hui
                    $statut = SIP2_CIRCULATION_STATUS_LOANED_TODAY;
                }
            } else {
                // tester si réservé
                $result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($expl->expl_cb)."' ");
                $reserve = @pmb_mysql_num_rows($result_resa);
                if ($reserve) {
                    $statut = SIP2_CIRCULATION_STATUS_WAITING_ON_HOLD_SHELF;
                    $error = true;
                    $error = $selfservice_pret_deja_reserve_msg;
                } else {
                    $statut = SIP2_CIRCULATION_STATUS_AVAILABLE;
                }
            }
        } else {
            $statut = SIP2_CIRCULATION_STATUS_OTHER;
            $error = true;
            $error_message = $selfservice_pret_non_pretable_msg;
        }
        $hold_queue = @pmb_mysql_num_rows($result_resa) * 1;
        $ret["CIRCULATION_STATUS"] = $statut;
        $ret["SECURITY_MARKER"] = "00";
        $ret["FEE_TYPE"] = "01";
        $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
        $ret["HOLD_QUEUE_LENGTH"][0] = $hold_queue;
        if ($expl->pret_retour) {
            $ret["DUE_DATE"][0] = $expl->aff_pret_retour;
        }
        $ret["ITEM_IDENTIFIER"][0] = $expl_cb;
        if ($expl->expl_bulletin) {
            $isbd = new bulletinage_display($expl->expl_bulletin);
            $ret["TITLE_IDENTIFIER"][0] = pmb_substr($isbd->display, 0, 150);
        } else {
            $isbd = new mono_display($expl->expl_notice, 1);
            $ret["TITLE_IDENTIFIER"][0] = pmb_substr($isbd->header_texte, 0, 150);
        }
    } else {
        $ret["CIRCULATION_STATUS"] = SIP2_CIRCULATION_STATUS_OTHER;
        $ret["SECURITY_MARKER"] = "00";
        $ret["FEE_TYPE"] = "01";
        $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
        $ret["ITEM_IDENTIFIER"][0] = $expl_cb;
        $ret["TITLE_IDENTIFIER"][0] = $expl_cb." : document inconnu";
        $error = true;
        $error_message = $selfservice_pret_expl_inconnu_msg;
    }
    //if ($error) $ret["SCREEN_MESSAGE"][0]=$error_message;
    if ($expl_cb == "0000000000000000") {
        $ret = [];
    }
    return $ret;
}

function _checkout_response_($values) {
    global $pmb_antivol,$msg;
    global $see_all_pret;
    global $selfservice_pret_carte_invalide_msg;
    global $selfservice_pret_pret_interdit_msg;
    global $selfservice_pret_deja_prete_msg;
    global $selfservice_pret_deja_reserve_msg;
    global $selfservice_pret_quota_bloc_msg;
    global $selfservice_pret_non_pretable_msg;
    global $selfservice_pret_expl_inconnu_msg;

    $ret = [];
    $see_all_pret = 1;
    //Transaction obligatoire car déjà effectuée !
    //$force_checkout=($values["NO_BLOCK"]=="Y"?true:false);
    $localisation = $values["INSTITUTION_ID"][0];
    $empr_cb = $values["PATRON_IDENTIFIER"][0];
    $expl_cb = $values["ITEM_IDENTIFIER"][0];
    $fee_ack = ($values["FEE_ACKNOWLEDGED"][0] == "Y" ? true : false);
    $cancel = ($values["CANCEL"][0] == "Y" ? true : false);

    $magnetic = "N";
    $desensitize = "N";//Pour demande de désactiver l'antivole
    $titre = $expl_cb;
    $due_date = "";

    //Recherche de l'exemplaire
    $requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, ";
    $requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
    $requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
    $requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
    $requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
    $requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
    $requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
    $requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
    $requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
    $requete .= " WHERE expl_cb='".addslashes($expl_cb)."' ";
    $requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
    $resultat = pmb_mysql_query($requete);

    if (pmb_mysql_num_rows($resultat)) {
        $expl = pmb_mysql_fetch_object($resultat);

        //Recherche de l'emprunteur
        $requete = "select id_empr from empr where empr_cb='".addslashes($empr_cb)."'";
        $resultat = pmb_mysql_query($requete);
        if (!pmb_mysql_num_rows($resultat)) {
            $error = true;
            $error_message = $selfservice_pret_carte_invalide_msg;
            $ok = 0;
        } else {
            $id_empr = pmb_mysql_result($resultat, 0, 0);
            $empr = new emprunteur($id_empr, '', '', 1);
            $pret = ($empr->blocage_retard || $empr->blocage_amendes || $empr->blocage_abt || (!$empr->allow_loan) ? false : true);
            if (!$pret) {
                $ok = 0;
                $error = true;
                $error_message = $selfservice_pret_pret_interdit_msg;
            }/* elseif ($empr->empr_msg){ //#74002 non pertiant
                $ok=0;
                $error=true;
                $error_message=$empr->empr_msg;
            }*/ else {
                if ($expl->pret_flag) {
                    if ($expl->expl_bulletin) {
                        $isbd = new bulletinage_display($expl->expl_bulletin);
                        $titre = pmb_substr($isbd->display, 0, 150);
                    } else {
                        $isbd = new mono_display($expl->expl_notice, 1);
                        $titre = pmb_substr($isbd->header_texte, 0, 150);
                    }
                    if($expl->pret_retour) {
					    if (intval($id_empr) == intval($expl->pret_idempr)) {
					        global $selfservice_already_loaned;
                        	$error = true;
    						$error_message = $selfservice_already_loaned;
    						$ok = 0;
					    } else {
    						$error=true;
                        	$error_message = $selfservice_pret_deja_prete_msg;
                        	$ok = 0;
					    }
                    } else {
                        // tester si réservé
                        $result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($expl->expl_cb)."' and resa_idempr!='".addslashes($id_empr)."'");
                        $reserve = @pmb_mysql_num_rows($result_resa);
                        if ($reserve) {
                            $error = true;
                            $error_message = $selfservice_pret_deja_reserve_msg;
                            $ok = 0;
                        } else {
                            //On fait le prêt
                            $pret = new do_pret();
                            $pret->check_pieges($empr_cb, $id_empr, $expl_cb, $expl->expl_id, 0);
                            if (!$pret->status) {
                                $ok = 1;
                                $desensitize = "Y";//Pour demander de désactiver l'antivole
                                $pret->confirm_pret($id_empr, $expl->expl_id, 0, 'borne_rfid');
                                //Recherche de la date de retour
                                $requete = "select date_format(pret_retour, '".$msg["format_date"]."') as retour from pret where pret_idexpl=".$expl->expl_id;
                                $resultat = pmb_mysql_query($requete);
                                $error = true;
                                //Modification vu avec 3M -> Si on laisse le $error_message alors le titre est présent 2 fois
                                //$error_message=$titre." / retour le : ".@pmb_mysql_result($resultat,0,0);
                                $due_date = @pmb_mysql_result($resultat, 0, 0);
                            } else {
                                $ok = 0;
                                $error = true;
                                $error_message = $selfservice_pret_quota_bloc_msg;
                                $ret["SCREEN_MESSAGE"][1] = $pret->error_message;
                            }
                            //Est-ce un support magnétique
                            if ($pmb_antivol) {
                                if ($expl->type_antivol == 2) {
                                    $magnetic = "Y";
                                }
                            }

                        }
                    }
                } else {
                    $error = true;
                    $error_message = $selfservice_pret_non_pretable_msg;
                    $ok = 0;
                }
            }
        }
    } else {
        $error = true;
        $error_message = $selfservice_pret_expl_inconnu_msg;
        $titre = $expl_cb;
        $ok = 0;
    }
    $ret["OK"] = $ok;
    $ret["RENEWAL_OK"] = "N";
    $ret["MAGNETIC_MEDIA"] = $magnetic;
    $ret["DESENSITIZE"] = $desensitize;
    $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
    $ret["INSTITUTION_ID"][0] = $localisation;
    $ret["PATRON_IDENTIFIER"][0] = $empr_cb;
    $ret["ITEM_IDENTIFIER"][0] = $expl_cb;
    $ret["TITLE_IDENTIFIER"][0] = $titre;
    $ret["DUE_DATE"][0] = $due_date;
    if ($error) {
        $ret["SCREEN_MESSAGE"][0] = $error_message;
    }
    return $ret;
}

/**
 * Permet de faire le retour d'un document
 *
 * @param array $values
 * @return array
 */
function _checkin_response_($values) {
    global $pmb_antivol,$protocol_prolonge;
    global $selfservice_pret_expl_inconnu_msg;

    $ret = [];
    $localisation = $values["INSTITUTION_ID"][0];
    $expl_cb = $values["ITEM_IDENTIFIER"][0];
    $cancel = ($values["CANCEL"][0] == "Y" ? true : false);

    $magnetic = "N";
    $resensitize = "N";
    $ok = 0;
    $titre = $expl_cb;

    $requete = "select expl_id,expl_bulletin,expl_notice,type_antivol,empr_cb from exemplaires join pret on (expl_id=pret_idexpl) join empr on (pret_idempr=id_empr) where expl_cb='".addslashes($expl_cb)."'";
    $resultat = pmb_mysql_query($requete);
    if (!$resultat) {
        $ok = 0;
        $error = true;
        $ret["SCREEN_MESSAGE"][0] = $selfservice_pret_expl_inconnu_msg;
    } else {
        $expl = pmb_mysql_fetch_object($resultat);
        $empr_cb = $expl->empr_cb;

        if ($expl->expl_bulletin) {
            $isbd = new bulletinage_display($expl->expl_bulletin);
            $titre = pmb_substr($isbd->display, 0, 150);
        } else {
            $isbd = new mono_display($expl->expl_notice, 1);
            $titre = pmb_substr($isbd->header_texte, 0, 150);
        }

        if ($pmb_antivol && $expl->type_antivol == 2) {
            $magnetic = "Y";
        }

        $retour = new expl_to_do($expl_cb);
		// Ici on va gérer les messages d'erreurs sur les statuts de l'exemplaire
		// Si faut le reporté dans bibloto, on déplace le code dans do_retour_selfservice
		global $selfservice_expl_status;
		$resa_expl_status = encoding_normalize::json_decode($selfservice_expl_status, true);
		if (!empty($resa_expl_status[intval($retour->expl->expl_statut)])) {
			$ret["SCREEN_MESSAGE"][0] = stripslashes($resa_expl_status[intval($retour->expl->expl_statut)]);
			$ret["OK"]=$ok;
			$ret["RESENSITIZE"]=$resensitize;
			$ret["MAGNETIC_MEDIA"]=$magnetic;
			$ret["ALERT"]="N";
			$ret["TRANSACTION_DATE"]=date("Ymd    His", time());
			$ret["INSTITUTION_ID"][0]=$localisation;
			$ret["ITEM_IDENTIFIER"][0]=$expl_cb;
			$ret["PERMANENT_LOCATION"][0]=$localisation;
			$ret["TITLE_IDENTIFIER"][0]=$titre;
			$ret["PATRON_IDENTIFIER"][0]=$empr_cb;

			return $ret;
		}

        // Fonction qu effectue le retour d'un document
        $retour->do_retour_selfservice('borne_rfid');

        if ($retour->status == -1) {
            //Problème
            $ok = 0;
        } else {
            //Pas de problème
            $ok = 1;
            $resensitize = "Y";
        }

        /*
        $ret["SCREEN_MESSAGE"][0]=$retour->message_loc;
        $ret["SCREEN_MESSAGE"][1]=$retour->message_resa;
        $ret["SCREEN_MESSAGE"][2]=$retour->message_retard;
        $ret["SCREEN_MESSAGE"][3]=$retour->message_amende;
        */

        if($retour->message_loc || $retour->message_resa || $retour->message_retard || $retour->message_amende || $retour->message_blocage || $retour->expl->expl_note) {
            $ret["SCREEN_MESSAGE"][0] = trim($retour->message_loc." ".$retour->message_resa." ".$retour->message_retard." ".$retour->message_amende." ".$retour->message_blocage." ".$retour->expl->expl_note);
            //$ok=0;
            //Attention, pour les deux lignes suivantes, cela dépend d'un paramètre NEDAP ou IDENT
            if ($protocol_prolonge == "3M" || $protocol_prolonge == "Ident") {
                //On ne change pas le statut
            } elseif ($protocol_prolonge) {
                $ok = 0;
            }
        }

        if ($retour->message_loc) {
            $ret["SORT_BIN"][0] = 1;
        } elseif ($retour->message_resa) {//Attention il peut n'y avoir qu'un espace dans ce champ pour passer ici mais sans message
            $ret["SORT_BIN"][0] = 2;
        } elseif ($retour->message_retard) {
            $ret["SORT_BIN"][0] = 3;
        } elseif ($retour->message_amende) {
            $ret["SORT_BIN"][0] = 4;
        } else {
            $ret["SORT_BIN"][0] = 0;
        }
    }

    $ret["OK"] = $ok;
    $ret["RESENSITIZE"] = $resensitize;
    $ret["MAGNETIC_MEDIA"] = $magnetic;
    $ret["ALERT"] = "N";
    $ret["TRANSACTION_DATE"] = date("Ymd    His", time());
    $ret["INSTITUTION_ID"][0] = $localisation;
    $ret["ITEM_IDENTIFIER"][0] = $expl_cb;
    $ret["PERMANENT_LOCATION"][0] = $localisation;
    $ret["TITLE_IDENTIFIER"][0] = $titre;
    $ret["PATRON_IDENTIFIER"][0] = $empr_cb;

    return $ret;
}

function _request_sc_resend_($values) {
    //Wath ever the values !
    return [];
}

/**
 * Permet de prolonger un pret
 *
 * @param array $values
 * @return array
 */
function _renew_response_($values) {

    $explCb = $values["ITEM_IDENTIFIER"][0];

    $result = exemplaire::self_renew($explCb, 0, 1);
    $title = $result["title"] ?? $explCb;

    $ok = $result["status"] ?? 0;
    $screenMessage = [];

    // Attention : exemplaire::self_renew(), retourne un message si la prolongation a ete effectuee
    // Il ne faut pas tester l'entree "message" du retour de la fonction
    if (!$ok) {
        global $selfservice_pret_prolonge_non_msg;

        $ok = 0;
        $screenMessage = [
            $result["message"] ?? $selfservice_pret_prolonge_non_msg
        ];
    }

    return [
        "SCREEN_MESSAGE" => $screenMessage,
        "OK" => $ok,
        "RENEWAL_OK" => $ok == 1 ? "Y" : "N",
        "MAGNETIC_MEDIA" => "N",
        "DESENSITIZE" => "N",
        "TRANSACTION_DATE" => date("Ymd    His", time()),
        "INSTITUTION_ID" => [
            $values["INSTITUTION_ID"][0]
        ],
        "PATRON_IDENTIFIER" => [
            $values["PATRON_IDENTIFIER"][0]
        ],
        "ITEM_IDENTIFIER" => [
            $explCb
        ],
        "TITLE_IDENTIFIER" => [
            $title
        ],
        "DUE_DATE" => [
            $result["due_date"] ?? ""
        ]
    ];
}

function sql_value($rqt) {
    if (($result = pmb_mysql_query($rqt))) {
        if(($row = pmb_mysql_fetch_row($result))) {
            return $row[0];
        }
    }
    return '';
}

/**
 * Ajout le champ 'OPEN_PLUS' dans la trame SIP2
 *
 * @param array $ret
 * @param integer $empr_id
 * @return void
 */
function add_open_plus_field(array &$ret, int $empr_id) {
	$p_perso = new parametres_perso("empr");
	$p_perso->get_values($empr_id);

	$valid_patron_p_perso = false;
	foreach ($p_perso->t_fields as $id => $field) {
		if ('cp_sip_open_plus' === $field['NAME']) {
			$valid_patron_p_perso = $id;
		}
	}

    global $protocol;
    if (!isset($protocol->identifiers['OP'])) {
        // ici, on n'a pas de champ 'OP' (OPEN_PLUS) requis pour les trames SIP2.
        // Donc on ajoute pas le champ 'OPEN_PLUS' dans la trame.
        return;
    }

	if ($valid_patron_p_perso && $p_perso->values[$valid_patron_p_perso]) {
		// Si on a le champ perso 'cp_sip_open_plus' et qu'il n'est pas vide
		// On regarde si on a la valeur 'Y' (peut importe les autres valeurs) OPEN_PLUS=Y
		// Si on a pas de valeur 'Y', OPEN_PLUS=N
		$index = array_search('Y', array_map('trim', $p_perso->values[$valid_patron_p_perso]));
		$value = (false === $index ? 'N' : 'Y');
	} else {
        // Si on n'a pas le champ perso 'cp_sip_open_plus'
        // Ou si les valeur du champ perso  est vide. OPEN_PLUS=N
        $value = 'N';
    }

    $ret['OPEN_PLUS'][0] = $value;
}