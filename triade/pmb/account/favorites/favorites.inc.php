<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: favorites.inc.php,v 1.8 2024/03/12 13:51:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], '.inc.php')) die('no access');

global $modified, $PMBuserid, $account_form, $stylesheet, $user_lang, $pmb_url_base, $msg;
global $form_pwd, $form_nb_per_page_search, $form_nb_per_page_select, $form_nb_per_page_gestion, $form_style, $form_user_email, $form_deflt_thesaurus, $form_deflt_docs_location;

use Pmb\Common\Helper\MySQL;

if (empty($modified)) {
    $user_params = get_account_info(SESSlogin);

    $param_default = user::get_form($PMBuserid, 'account_form');

    $account_form = str_replace('!!all_user_param!!', $param_default, $account_form);
    // fin gestion des paramètres personalisés du user

    $account_form = str_replace('!!combo_user_style!!', make_user_style_combo($stylesheet), $account_form);
    $account_form = str_replace('!!combo_user_lang!!', make_user_lang_combo($user_params->user_lang), $account_form);
    $account_form = str_replace('!!user_email!!', $user_params->user_email, $account_form);
    $account_form = str_replace('!!nb_per_page_search!!', $user_params->nb_per_page_search, $account_form);
    $account_form = str_replace('!!nb_per_page_select!!', $user_params->nb_per_page_select, $account_form);
    $account_form = str_replace('!!nb_per_page_gestion!!', $user_params->nb_per_page_gestion, $account_form);
    print $account_form;

} else {
    $names = array();
    $values = array();
    // Mise à jour - Constitution des variables MySQL

    $names[] = 'last_updated_dt';
    $values[] = "'".today()."'";

    $names[] = 'user_lang';
    $values[] = "'$user_lang'";

    if (!empty($form_pwd)) {
        $names[] = 'pwd';
        $values[] = "'".MySQL::password($form_pwd)."'";
        $names[] = 'user_digest';
        $values[]= "'".md5(SESSlogin.":".md5($pmb_url_base).":".$form_pwd)."'";
    }

    if ($form_nb_per_page_search >= 1) {
        $names[] = 'nb_per_page_search';
        $values[] = "'$form_nb_per_page_search'";
    }

    if ($form_nb_per_page_select >= 1) {
        $names[] = 'nb_per_page_select';
        $values[] = "'$form_nb_per_page_select'";
    }

    if ($form_nb_per_page_gestion >= 1) {
        $names[] = 'nb_per_page_gestion';
        $values[] = "'$form_nb_per_page_gestion'";
    }

    if (strcmp($form_style, $stylesheet)) {
        $names[] = 'deflt_styles';
        $values[] = "'$form_style'";
    }

    $names[] = 'user_email';
    $values[] = "'$form_user_email'";

    if ($form_deflt_thesaurus) {
        thesaurus::setSessionThesaurusId($form_deflt_thesaurus);
    }

    $requete_param = "SELECT * FROM users WHERE userid='$PMBuserid' LIMIT 1 ";
    $res_param = pmb_mysql_query($requete_param);
    $dummy = array();
    $i = 0;
    while ($i < pmb_mysql_num_fields($res_param)) {
        $field = pmb_mysql_field_name($res_param, $i);
        $field_deb = substr($field, 0, 6);
        switch ($field_deb) {
            case 'deflt_':
                switch ($field) {
                    case 'deflt_styles':
                        $dummy[$i+8] = "$field='$form_style'";
                        break;
                    case 'deflt_docs_section':
                        $formlocid = 'f_ex_section' . $form_deflt_docs_location;
                        global ${$formlocid};
                        $dummy[$i+8] = "$field='" . ${$formlocid} . "'";
                        break;
                    default:
                        $var_form = "form_$field";
                        global ${$var_form};
                        $dummy[$i+8] = "$field='" . ${$var_form} . "'";
                        break;
                }
                break;
            case 'deflt2':
            case 'param_':
            case 'value_':
            case 'xmlta_':
            case 'deflt3':
                $var_form = "form_$field";
                global ${$var_form};
                $dummy[$i+8] = "$field='" . ${$var_form} . "'";
                break;
            case "speci_":
                $speci_func = substr($field, 6);
                eval('$dummy[$i+8] = set_'.$speci_func.'();');
                break;
            default:
                break;
        }
        $i++;
    }
    $set = '';
    if (!empty($dummy)) {
        $set = implode(', ', $dummy);
        $set = " , $set";
    }

    if (count($names) == count($values)) {
        $n_values = '';
        foreach ($names as $cle => $valeur) {
            if ($n_values) {
                $n_values .= ", $valeur={$values[$cle]}";
            } else {
                $n_values = "$valeur={$values[$cle]}";
            }
        }
        $requete = "UPDATE users SET $n_values $set , last_updated_dt=curdate() WHERE username='".SESSlogin."' ";
        $result = @pmb_mysql_query($requete);
        if ($result) {
            print $msg["937"] . " <!-- back to main page --> ";
            go_first_tab();
        } else {
            // c'est parti en vrac : erreur MySQL
            warning($msg["281"], $msg["936"]);
        }
    }
}