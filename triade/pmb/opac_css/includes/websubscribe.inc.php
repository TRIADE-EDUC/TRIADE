<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: websubscribe.inc.php,v 1.39.4.1 2025/04/11 10:10:10 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) {
    die("no access");
}
use Pmb\Common\Models\EmprModel;

define('PBINSC_OK'		,    0);
define('PBINSC_MAIL'	,    1);
define('PBINSC_LOGIN'	,    2);
define('PBINSC_BDD'		,    3);
define('PBINSC_INVALID'	,    5);
define('PBINSC_INCONNUE',    6);
define('PBINSC_CLE'		,    7);
define('PBINSC_PARAM'	,    8);

global $base_path, $class_path;

require_once $base_path . "/includes/templates/websubscribe.tpl.php";
require_once $base_path . "/includes/bannette_func.inc.php";
require_once "$class_path/emprunteur.class.php";
require_once "$class_path/emprunteur_display.class.php";


function generate_form_inscription() {

    global $ext_auth_args;
    if(empty($ext_auth_args)) {
        $ext_auth_args = [];
    } else {
        $ext_auth_args = unserialize(base64_decode($ext_auth_args));
    }
    return emprunteur_display::get_display_profil(0, $ext_auth_args);
}


function test_form_fields($fields) {
    $ok = true;
    foreach ($fields as $field_name) {
        global ${$field_name};
        if (${$field_name} != strip_tags(${$field_name} ?? "")) {
        	${$field_name} = '';
            $ok = false;
        }
    }
    return $ok;
}


function verif_validite_compte() {

    global $msg, $opac_default_lang;
    global $subscribe_form_fields;
    global $base_path, $opac_websubscribe_num_carte_auto;
    global $f_nom, $f_prenom, $f_email, $f_login, $f_password, $f_consent_message, $f_year;
    global $opac_websubscribe_password_regexp;
    global $pe_emprcb;

        // On commence par l'email, car cela peut etre un tableau, le array_map retournera null pour cette entree
    if (!empty($subscribe_form_fields["empr_mail"])) {
        if (is_array($subscribe_form_fields["empr_mail"])) {
            $f_email = strip_tags($subscribe_form_fields["empr_mail"][0]);
        } else {
            // Si on est dans ce cas la, a l'enregistrement on ne prend pas en compte email...
            $f_email = strip_tags($subscribe_form_fields["empr_mail"]);
        }
        //Non le strip_tags sur un tableau ne retourne pas null mais ne passe plus en PHP8 donc on enlève sinon couic
        unset($subscribe_form_fields["empr_mail"]);
    }
    $subscribe_form_fields = array_map("strip_tags", $subscribe_form_fields ?? []);

    // On l'utilise en global plus loin... Houppi... On prend le partie de le remettre en tableau car plus loin
    // Dans emprunteur_data on va boucler dessus quoi qu'il arrive.
    $subscribe_form_fields["empr_mail"] = [$f_email];

    if (!empty($subscribe_form_fields["empr_nom"])) {
        $f_nom = $subscribe_form_fields["empr_nom"];
    }
    if (!empty($subscribe_form_fields["empr_prenom"])) {
        $f_prenom = $subscribe_form_fields["empr_prenom"];
    }
    if (!empty($subscribe_form_fields["empr_login"])) {
        $f_login = $subscribe_form_fields["empr_login"];
    }
    if (!empty($subscribe_form_fields["empr_password"])) {
        $f_password = $subscribe_form_fields["empr_password"];
    }
    if (!empty($subscribe_form_fields["empr_consent_message"])) {
        $f_consent_message = $subscribe_form_fields["empr_consent_message"];
    }
    if (!empty($subscribe_form_fields["empr_year"])) {
        $f_year = $subscribe_form_fields["empr_year"];
    }
    $ret = array();

        // langue:
    if (isset($_COOKIE['PhpMyBibli-LANG'])) {
        $lang = $_COOKIE['PhpMyBibli-LANG'];
    }
    if (!isset($lang)) {
        if ($opac_default_lang) {
            $lang = $opac_default_lang;
        } else {
            $lang = "fr_FR";
        }
    }
    $form_values = [
        'login' => $f_login,
        'year' => $f_year
    ];
    $check_password_rules = emprunteur::check_password_rules(0, $f_password, $form_values, $lang);
    if (!$check_password_rules['result']) {
        $ret[0] = PBINSC_INVALID;
        $ret[1] = $msg['empr_password_bad_security'] . generate_form_inscription();
        $ret[2] = 'error_bad_password';
        return $ret;
    }
    if (!isset($f_consent_message) || !$f_consent_message) {
        $ret[0] = PBINSC_INVALID;
        $ret[1] = $msg['subs_form_consent_message_mandatory'] . generate_form_inscription();
        $ret[2] = 'error_consent_message';
        return $ret;
    }

    // a mon avis ça ne fonctionne pas...
    if (!test_form_fields($subscribe_form_fields)) {
        $ret[0] = PBINSC_INVALID;
        $ret[1] = $msg['subs_pb_tags'] . generate_form_inscription();
        $ret[2] = 'error_tags_not_allowed';
        return $ret;
    }

    $rqt = "select id_empr from empr where empr_mail like '%" . $f_email . "%' ";
    $res = pmb_mysql_query($rqt);
    if (pmb_mysql_num_rows($res) > 0) {
        $ret[0] = PBINSC_MAIL;
        $ret[1] = str_replace("!!email!!", urlencode($f_email), $msg['subs_pb_email']);
        $ret[2] = 'error_mail_used';
        return $ret;
    }

    $check_login = emprunteur::check_login_uniqueness($f_login);
    if (!$check_login) {
        $suggested_login = emprunteur::get_suggested_login($f_prenom, $f_nom);
        $bad_login = $f_login;
        $f_login = $suggested_login;
        $ret[0] = PBINSC_LOGIN;
        $ret[1] = str_replace("!!f_login!!", $bad_login, $msg['subs_pb_login']) . generate_form_inscription();
        $ret[2] = 'error_login_used';
        return $ret;
    }

    //Mise en conformité de l'identifiant
    $converted_login = convert_diacrit(pmb_strtolower($f_login));
    $converted_login = pmb_alphabetic('^a-z0-9\.\_\-\@', '', $converted_login);
    if ($converted_login != $f_login) {
        $bad_login = $f_login;
        $f_login = $converted_login;
        $ret[0] = PBINSC_LOGIN;
        $ret[1] = str_replace("!!f_login!!", $bad_login, $msg['subs_pb_invalid_login']) . generate_form_inscription();
        $ret[2] = 'error_login_invalid';
        return $ret;
    }

    // préparation des données:

    // paramétrage :
    global $opac_websubscribe_empr_status, $opac_websubscribe_empr_categ, $opac_websubscribe_empr_stat, $opac_websubscribe_valid_limit ;
    $opac_websubscribe_empr_status_array=explode(",", $opac_websubscribe_empr_status);

    if (!$opac_websubscribe_empr_categ) {
        $ret[0] = PBINSC_PARAM;
        $ret[1] = $msg['subs_pb_empr_categ'];
        $ret[2] = 'error_no_categ';
        return $ret;
    }
    if (!$opac_websubscribe_empr_stat) {
        $ret[0] = PBINSC_PARAM;
        $ret[1] = $msg['subs_pb_empr_codestat'];
        $ret[2] = 'error_no_codestat';
        return $ret;
    }

    // codes-barres emprunteur bidon :
    $pe_emprcb = 'wwwtmp' . rand(0, 100000);
    // durée d'adhésion de la categ web
    $rqt = "select duree_adhesion from empr_categ where id_categ_empr='" . $opac_websubscribe_empr_categ . "' ";
    $res = pmb_mysql_query($rqt);
    $obj = pmb_mysql_fetch_object($res);
    $duree_adhesion = $obj->duree_adhesion;
    if (!$duree_adhesion) {
        $duree_adhesion = 365; //Valeur choisie par défaut pour éviter tout problème de paramétrage
    }

    global $pmb_lecteurs_localises, $opac_websubscribe_show_location;
    global $opac_websubscribe_empr_location;
    if ($pmb_lecteurs_localises && $opac_websubscribe_show_location) {
        global $empr_location_id;
        $websubscribe_empr_location = ($empr_location_id ? $empr_location_id : $opac_websubscribe_empr_location);
    } else {
        $websubscribe_empr_location = $opac_websubscribe_empr_location;
    }
    // clé de validation :
    $alphanum = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $cle_validation = substr(str_shuffle($alphanum), 0, 20);

    $subscription_action = get_others_informations_from_globals();
    //champs par defaut
    $rqt = "insert into empr set ";
    $rqt .= "id_empr=0, ";
    $rqt .= "empr_cb ='" . $pe_emprcb . "', ";
    $rqt .= "empr_login ='" . $f_login . "', ";
    $rqt .= "empr_mail='" . $f_email . "', ";
    $rqt .= "empr_nom='" . $f_nom . "', ";
    $rqt .= "empr_prenom='" . $f_prenom . "', ";
    $rqt .= "empr_password='" . $f_password . "', ";
    $rqt .= "empr_creation=sysdate(), ";
    $rqt .= "empr_modif=sysdate(), ";
    $rqt .= "empr_date_adhesion=sysdate(), ";
    $rqt .= "empr_date_expiration=date_add(sysdate(), INTERVAL $duree_adhesion DAY), ";
    $rqt .= "empr_lang='" . $lang . "', ";
    $rqt .= "empr_statut='" . $opac_websubscribe_empr_status_array[0] . "', ";
    $rqt .= "empr_location='" . $websubscribe_empr_location . "', ";
    $rqt .= "empr_categ='" . $opac_websubscribe_empr_categ . "', ";
    $rqt .= "empr_codestat='" . $opac_websubscribe_empr_stat . "', ";
    $rqt .= "cle_validation='" . $cle_validation . "' ";
    if (count($subscription_action)) {
        $rqt .= ",empr_subscription_action = '" . addslashes(serialize($subscription_action)) . "'";
    } else {
        $rqt .= ",empr_subscription_action = '" . addslashes(serialize(array())) . "'";
    }

    $res = pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");
    $id_empr = pmb_mysql_insert_id();

    //donnees supplémentaires du formulaire
    $emprunteur_datas = emprunteur_display::get_emprunteur_datas($id_empr);
    $emprunteur_datas->set_from_form();
    $emprunteur_datas->save();

    emprunteur::update_digest($f_login, $f_password);
    emprunteur::hash_password($f_login, $f_password);
    if ($id_empr) {
        $pe_emprcb = EmprModel::redefineBarcodeOnWebRegistration($id_empr);
        $rqt = "UPDATE empr SET empr_cb='$pe_emprcb' WHERE id_empr='$id_empr'";
        pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");

        // envoyer le mail de demande de confirmation
        $mail_opac_reader_registration = new mail_opac_reader_registration();
        $mail_opac_reader_registration->set_mail_to_id($id_empr);
        $mail_opac_reader_registration->set_empr(new emprunteur($id_empr));
        $res_envoi = $mail_opac_reader_registration->send_mail();

        if (!$res_envoi) {
            $ret[0] = PBINSC_MAIL;
            $ret[1] = str_replace("!!f_email!!", $f_email, $msg['subs_pb_mail']);
            $ret[2] = 'error_mail_not_sent';
            return $ret;
        }
        
        $ret[0] = PBINSC_OK;
        $ret[1] = str_replace("!!f_email!!", $f_email, $msg['subs_ok_inscrit']);
        $ret[1] = str_replace("!!nb_h_valid!!", $opac_websubscribe_valid_limit, $ret[1]);
        $ret[2] = 'ok';

        //alerte pour les utilisateurs
        $query_users = "select userid, nom, prenom, user_email from users where user_email like('%@%') and user_alert_subscribemail=1";
        $result_users = pmb_mysql_query($query_users);
        if ($result_users) {
            if (pmb_mysql_num_rows($result_users) > 0) {
                while ($user = pmb_mysql_fetch_object($result_users)) {
                    $mail_opac_user_registration = new mail_opac_user_registration();
                    $mail_opac_user_registration->set_mail_to_id($user->userid);
                    $mail_opac_user_registration->send_mail();
                }
            }
        }
        return $ret;
    } else {
        $ret[0] = PBINSC_BDD;
        $ret[1] = $msg['subs_pb_bdd'];
        $ret[2] = 'error_bdd';
        return $ret;
    }
}

function verif_validation_compte()
{
    global $msg, $charset;
    global $login, $cle_validation, $form_access_compte;
    global $opac_websubscribe_empr_status, $opac_websubscribe_valid_limit;
    $opac_websubscribe_empr_status_array = explode(",", $opac_websubscribe_empr_status);

    $ret = array();
    $login = htmlentities($login, ENT_QUOTES, $charset);
    $rqt = "select id_empr, if(date_add(empr_creation, INTERVAL $opac_websubscribe_valid_limit HOUR)>=sysdate(),1,0) as not_depasse, empr_password, cle_validation, empr_subscription_action from empr where empr_login ='" .
        $login . "' and empr_statut='" . $opac_websubscribe_empr_status_array[0] . "' ";
    $res = pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");
    if (pmb_mysql_num_rows($res) > 0) {
        // trouvé !
        $obj = pmb_mysql_fetch_object($res);
        if ($obj->not_depasse) {
            // on vérifie si la clé de validation est toujours renseignée
            if ($obj->cle_validation && $cle_validation) {
                // validation pas dépassée
                if ($obj->cle_validation == $cle_validation) {
                    $subscription_action = unserialize($obj->empr_subscription_action);
                    $suite = get_html_subscription_action($subscription_action);
                    $rqt = "update empr set cle_validation='', empr_subscription_action= '', empr_statut='" . $opac_websubscribe_empr_status_array[1] . "' where empr_login='" . $login . "' ";
                    pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");
                    $ret[0] = PBINSC_OK;
                    if ($suite) {
                        //on connecte avec une mini feinte...
                        global $emprlogin;
                        $emprlogin = $login;
                        global $encrypted_password;
                        $encrypted_password = $obj->empr_password;
                        $log_ok = connexion_empr();
                        if ($log_ok) {
                            $ret[1] = str_replace("!!form_access_compte!!", $suite, $msg['subs_ok_validation']);
                        } else {
                            $form_access_compte = str_replace("!!login!!", $login, $form_access_compte);
                            $form_access_compte = str_replace("!!encrypted_password!!", $obj->empr_password, $form_access_compte);
                            $ret[1] = str_replace("!!form_access_compte!!", $form_access_compte, $msg['subs_ok_validation']);
                        }
                    } else {
                        $form_access_compte = str_replace("!!login!!", $login, $form_access_compte);
                        $form_access_compte = str_replace("!!encrypted_password!!", $obj->empr_password, $form_access_compte);
                        $ret[1] = str_replace("!!form_access_compte!!", $form_access_compte, $msg['subs_ok_validation']);
                    }
                    return $ret;
                } else {
                    // login Ok mais clé pas valide
                    $rqt = "delete from empr where empr_login='" . $login . "' ";
                    pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");
                    $ret[0] = PBINSC_CLE;
                    $ret[1] = $msg['subs_pb_cle'];
                    return $ret;
                }
            } else {
                // compte déjà validé
                $ret[0] = PBINSC_OK;
                $form_access_compte = str_replace("!!login!!", $login, $form_access_compte);
                $form_access_compte = str_replace("!!encrypted_password!!", $obj->empr_password, $form_access_compte);
                $ret[1] = str_replace("!!form_access_compte!!", $form_access_compte, $msg['subs_ok_already_validated']);
                return $ret;
            }
        } else {
            // dépassée
            $rqt = "delete from empr where empr_login='" . $login . "' ";
            pmb_mysql_query($rqt) or die(pmb_mysql_error() . "<br /><br />$rqt");
            $ret[0] = PBINSC_INVALID;
            $ret[1] = $msg['subs_pb_invalid'];
            return $ret;
        }
    }
    // n'existe même pas !
    $ret[0] = PBINSC_INCONNUE;
    $ret[1] = str_replace("!!login!!", $login, $msg['subs_pb_inconnue']);
    return $ret;
}

function get_others_informations_from_globals() {
    
    global $lvl;
    $subscription_action = array();
    if ($lvl) {
        $subscription_action['lvl'] = $lvl;
        switch ($lvl) {
            case "resa":
                global $id_notice, $id_bulletin;
                $subscription_action['id_notice'] = $id_notice;
                $subscription_action['id_bulletin'] = $id_bulletin;
                break;
            case "bannette_gerer":
                global $tab, $enregistrer, $bannette_abon;
                $subscription_action['tab'] = $tab;
                $subscription_action['enregistrer'] = $enregistrer;
                $subscription_action['bannette_abon'] = $bannette_abon;
        }
    }
    return $subscription_action;
}

function prepare_post_others_informations()
{
    global $opac_websubscribe_show, $lvl;
    $others_informations = "";
    if ($opac_websubscribe_show == 2 && $lvl) {
        $others_informations .= "
        <input type='hidden' name='lvl' value='" . $lvl . "' />";
        switch ($lvl) {
            case "resa":
                global $id_notice, $id_bulletin;
                $others_informations .= "
                    <input type='hidden' name='id_notice' value='" . ($id_notice * 1) . "' />
                    <input type='hidden' name='id_bulletin' value='" . ($id_bulletin * 1) . "' />";
                break;
            case "resa_cart":
                break;
            case "bannette_gerer":
                global $bannette_abon;
                $others_informations .= "
                    <input type='hidden' name='enregistrer' value='PUB'/>
                    <input type='hidden' name='tab' value='dsi'/>
                    <input type='hidden' name='new_connexion' value='1'/>";
                if (is_array($bannette_abon)) {
                    foreach ($bannette_abon as $id => $value) {
                        $others_informations .= "
                            <input type='hidden' name='bannette_abon[" . $id . "]' value='1'/>";
                    }
                }
                break;
        }
    }
    return $others_informations;
}

function get_html_subscription_action($others_informations)
{
    global $opac_websubscribe_show;
    global $msg;

    $html = "";
    if ($opac_websubscribe_show == 2) {

        switch ($others_informations['lvl']) {
            case "resa":
                $html = "
                    <div>
                        <h3>" . $msg['websubscribe_resa_action'] . "</h3>
                        <div class='row'>&nbsp;</div>
                        " . aff_notice($others_informations['id_notice'], 1, 1, 0, "", 0, 0, 1) . "
                    </div>";
                break;
            case "bannette_gerer":
                $id_bannette = 0;
                foreach ($others_informations['bannette_abon'] as $id => $v) {
                    $id_bannette = $id;
                }
                $html = "
                    <div>
                        <h3>" . $msg['websubscribe_bannette_action'] . "</h3>
                        <div class='row'>&nbsp;</div>
                        " . affiche_public_bannette($id_bannette) . "
                    </div>";
                break;
        }
    }
    return $html;
}
