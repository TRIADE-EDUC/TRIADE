<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bibloto.class.php,v 1.45.2.2 2024/10/16 09:16:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;

require_once $class_path . "/connecteurs_out.class.php";
require_once $class_path . "/connecteurs_out_sets.class.php";
require_once $class_path . "/external_services_converters.class.php";
require_once $class_path . "/encoding_normalize.class.php";

class bibloto extends connecteur_out
{

    public function get_config_form()
    {
    	return $this->msg["bibloto_no_configuration_required"];
    }


    public function update_config_from_form()
    {
        return;
    }


    public function instantiate_source_class($source_id)
    {
        return new bibloto_source($this, $source_id, $this->msg);
    }


    public function process($source_id, $pmb_user_id)
    {
        global $opac_biblio_name, $opac_biblio_email;
        global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone;

        $source = new bibloto_source($this, $source_id, $this->msg);
        $param = $source->config;
        $param['biblio']['name'] = $opac_biblio_name;
        $param['biblio']['adr1'] = $biblio_adr1;
        $param['biblio']['adr2'] = $biblio_adr2;
        $param['biblio']['cp'] = $biblio_cp;
        $param['biblio']['town'] = $biblio_town;
        $param['biblio']['phone'] = $biblio_phone;
        $param['biblio']['email'] = $opac_biblio_email;
        if ( !empty($param['auth_password']) ) {
            $param['auth_password'] = md5($param['auth_password']);
        }
        $param['msg_config_printer_title'] = $this->msg['msg_config_printer_title'];
        $param['msg_config_printer_validate'] = $this->msg['msg_config_printer_validate'];
        $param['msg_config_printer_no_printer'] = $this->msg['msg_config_printer_no_printer'];
        echo encoding_normalize::json_encode($param);
    }
}


class bibloto_source extends connecteur_out_source
{

    public function __construct($connector, $id, $msg)
    {
        parent::__construct($connector, $id, $msg);
    }


    public function get_config_form()
    {
        global $charset, $pmb_url_base, $_tableau_databases, $_libelle_databases, $pmb_printer_name;

        /* Configuration nouvelle source */
        if (!$this->id) {

            /* adresse WS */
            $this->config['pmb_ws_url'] = "http://...pmb/ws/connector_out.php?source_id=1";

            /*Identifiant */
            $this->config['auth_login'] = "";
            /* Mot de passe */
            $this->config['auth_password'] = "";
            /* Phrase de connexion */
            $this->config['auth_connexion_phrase'] = "";

            /* URL Style */
            $this->config['style_url'] = "styles/bibloto.css";

            /* Activation pret de documents */
            $this->config['checkout_activate'] = 1;
            /* Validation du pret */
            $this->config['auto_checkout'] = 1;

            /* Activation retour documents */
            $this->config['checkin_activate'] = 1;
            /* Ignorer les notes des exemplaires au retour */
            $this->config['checkin_ignore_expl_msg'] = 0;

            /* Affichage reservations */
            $this->config['resa_activate'] = 1;

            /* Affichage du bouton d'envoi de mail */
            $this->config['email_activate'] = 1;

            /* Ecran par defaut */
            $this->config['default_action'] = 1;
            /* Ecran par defaut apres le pret et le retour */
            $this->config['default_action_end'] = 1;

            /* Activation des alertes sonores */
            $this->config['sound_activate'] = 1;

            /* Activation trombinoscope */
            $this->config['trombinoscope_enabled'] = 0;
            /* Activation trombinoscope  authentifié */
            $this->config['trombinoscope_auth'] = 0;
            /* URL des vignettes lecteurs */
            $this->config['thumbnail_url'] = 'http://website/thumbnails/!!empr_cb!!.jpg';

            /* forcer l'authentification externe */
            $this->config['force_ext_auth'] = 0;
            
            /* Activation RFID */
            $this->config['rfid_activate'] = 0;
            /* pour lecture carte lecteur */
            $this->config['rfid_activate_empr'] = 1;
            /* pour lecture tags exemplaires */
            $this->config['rfid_activate_expl'] = 1;
            /* Driver RFID */
            $this->config['rfid_driver'] = "3m";
            /* URL serveur RFID */
            $this->config['rfid_serveur_url'] = "http://localhost:30000";
            /* code bibliotheque */
            $this->config['rfid_library_code'] = "0123456789";
            /* Activation gestion antivol */
            $this->config['rfid_security_activate'] = 1;
            /* code AFI antivol actif */
            $this->config['rfid_afi_security_code_on'] = "07";
            /* code AFI antivol inactif */
            $this->config['rfid_afi_security_code_off'] = "C2";

            /* Activation impression tickets de pret */
            $this->config['printer_activate'] = 0;

            $this->config['nb_jours_retard'] = 7;
            $this->config['css'] = "";
            $this->config['msg_dialog_checkout_no_all'] = 1;


            /* Template de la page d'accueil */
            $this->config['home_tpl'] = "
<div class='templateContent'>
    <div class='TitleContent'>
        <h1>Automate de pr&ecirc;t</h1>
        <p><img border='0' class='align_middle' src='images/carte_adherent.jpg'></p>
        <p class='IntroMsg'>Placez votre carte de lecteur</p>
    </div>
</div>";
            /* Template de la fiche emprunteur */
            $this->config['empr_tpl'] = "
<div class='templateContent'>
    <div class='MainContent'>
        <h1>\${nom} \${prenom}</h1>
        <p class='itemContent'>\${adr1}</p>
        <p class='itemContent'>\${cp} \${ville}</p>
    </div>
</div>";
            /* Template du ticket de pret */
            $this->config['printer_tpl'] = "\x1B\x40\x1B\x21\x16{{biblio.name}}\x1B\x21\x04
{{biblio.adr1}}
{{biblio.town}}
{{biblio.phone}}
{{biblio.email}}

Imprime le \n
Emprunteur:
{% for empr in empr_list %}
 {{empr.name}} {{empr.fistname}}
{% endfor %}
{% for expl in expl_list %}

{{expl.tit}}
 {{expl.cb}}
 {{expl.location}} / {{expl.section}} / {{expl.cote}}
 Prêté le {{expl.date_pret}}. \x1B\x21\x14 A retourner le{{expl.date_retour}} \x1B\x21\x04
 ______________________________________
{% endfor %}
\x1D\x56\x41 \x1B\x40";


            /* Libelles des boutons */
            $this->config['msg_checkout_button'] = $this->msg['bibloto_msg_checkout_button_value'];
            $this->config['msg_checkout_valid_button'] = $this->msg['bibloto_msg_checkout_valid_button_value'];
            $this->config['msg_checkin_button'] = $this->msg['bibloto_msg_checkin_button_value'];
            $this->config['msg_resa_button'] = $this->msg['bibloto_msg_resa_button_value'];
            $this->config['msg_email_button'] = $this->msg['bibloto_msg_email_button_value'];
            $this->config['msg_exit_button'] = $this->msg['bibloto_msg_exit_button_value'];
            $this->config['msg_action_title'] = $this->msg['bibloto_msg_action_title_value'];
            $this->config['msg_checkout_title'] = $this->msg['bibloto_msg_checkout_title_value'];
            $this->config['msg_checkin_title'] = $this->msg['bibloto_msg_checkin_title_value'];
            $this->config['msg_resa_title'] = $this->msg['bibloto_msg_resa_title_value'];
            $this->config['msg_resa_date_title'] = $this->msg['bibloto_msg_resa_date_title_value'];
            $this->config['msg_resa_confirme_title'] = $this->msg['bibloto_msg_resa_confirme_title_value'];

            $this->config['msg_expl_checkout_list_title'] = $this->msg['bibloto_msg_expl_checkout_list_title_value'];
            $this->config['msg_expl_title'] = $this->msg['bibloto_msg_expl_title_value'];
            $this->config['msg_expl_statut'] = $this->msg['bibloto_msg_expl_statut_value'];
            $this->config['msg_expl_date_checkout'] = $this->msg['bibloto_msg_expl_date_checkout_value'];
            $this->config['msg_expl_date_checkin'] = $this->msg['bibloto_msg_expl_date_checkin_value'];
            $this->config['msg_expl_rendu'] = $this->msg['bibloto_msg_expl_rendu_value'];

            $this->config['msg_dialog_place_item_checkout'] = $this->msg['bibloto_msg_dialog_place_item_checkout_value'];
            $this->config['msg_dialog_place_item_checkin'] = $this->msg['bibloto_msg_dialog_place_item_checkin_value'];
            $this->config['msg_dialog_too_many_items'] = $this->msg['bibloto_msg_dialog_too_many_items_value'];
            $this->config['msg_dialog_item_cb_unknown'] = $this->msg['bibloto_msg_dialog_item_cb_unknown_value'];
            $this->config['msg_dialog_checkout_possible'] = $this->msg['bibloto_msg_dialog_checkout_possible_value'];
            $this->config['msg_dialog_checkout_ok'] = $this->msg['bibloto_msg_dialog_checkout_ok_value'];
            $this->config['msg_dialog_checkout_no'] = $this->msg['bibloto_msg_dialog_checkout_no_value'];
            $this->config['msg_dialog_checkin_ok'] = $this->msg['bibloto_msg_dialog_checkin_ok_value'];
            $this->config['msg_dialog_checkin_no_checkout'] = $this->msg['bibloto_msg_dialog_checkin_no_checkout_value'];
            $this->config['msg_dialog_antivol_error'] = $this->msg['bibloto_msg_dialog_antivol_error_value'];
            $this->config['msg_printer_exit'] = $this->msg['bibloto_msg_printer_exit_value'];
            $this->config['msg_dialog_exit'] = $this->msg['bibloto_msg_dialog_exit_value'];
            $this->config['msg_no_user_found'] = $this->msg['bibloto_msg_no_user_found_value'];
            $this->config['msg_search_title'] = $this->msg['bibloto_msg_search_title_value'];
            $this->config['timeout_disconnect'] = "60";
            $this->config['printer_activate'] = 0;
            $this->config['printer_name'] = "";
            $this->config['msg_printer_button'] = $this->msg['msg_printer_button'];
            $this->config['msg_bibloto_yes'] = $this->msg['bibloto_yes'];
            $this->config['msg_bibloto_no'] = $this->msg['bibloto_no'];
            $this->config['print_all_loans_activate'] = 0;
            $this->config['msg_print_all_loans'] = $this->msg['msg_print_all_loans_value'];
            $this->config['msg_trombinoscope_auth_error'] = $this->msg['msg_trombinoscope_auth_error'];
        }


        /* Generation formulaire */
        $form = parent::get_config_form();

        /* Adresse du WS */
        $form .= "<div class='row'><label class='etiquette' for='api_exported_functions'>".htmlentities($this->msg['bibloto_service_endpoint'], ENT_QUOTES, $charset)."</label><br />";
        if ($this->id) {
            $form .= "<a target='_blank' href='" . $pmb_url_base . "ws/connector_out.php?source_id=" . $this->id;
            $form .= (count($_tableau_databases) > 1) ? "&database=" . $_libelle_databases[array_search(LOCATION, $_tableau_databases)] : "";
            $form .= "'>" . $pmb_url_base . "ws/connector_out.php?source_id=" . $this->id;
            $form .= (count($_tableau_databases) > 1) ? "&database=" . $_libelle_databases[array_search(LOCATION, $_tableau_databases)] : "";
            $form .= "</a>";
        } else {
            $form .= htmlentities($this->msg["bibloto_service_endpoint_unrecorded"], ENT_QUOTES, $charset);
        }
        $form .= "</div>";

        /* Web service JSON-RPC permettant d'effectuer le pret */
        $form.= "<hr />
        <h2>" . htmlentities($this->msg['bibloto_pmb_ws'], ENT_QUOTES, $charset) . "</h2>
        <br />
        <div class='row'>
            <label class='etiquette' for='pmb_ws_url'>".htmlentities($this->msg['bibloto_pmb_ws_url'], ENT_QUOTES, $charset)."</label><br />
            <input type='text' class='saisie-80em' id='pmb_ws_url' name='pmb_ws_url' value='".$this->config['pmb_ws_url']."' />
        </div>
        <div class='row'>
            <label for='auth_login'>".htmlentities($this->msg["pmb_username"], ENT_QUOTES, $charset)."</label><br />
            <input type='text' name='auth_login' id='auth_login' value='".htmlentities($this->config['auth_login'], ENT_QUOTES, $charset)."' />
        </div>
        <div class='row'>
            <label for='auth_password'>".htmlentities($this->msg["pmb_password"], ENT_QUOTES, $charset)."</label><br />
            <input type='text' name='auth_password' id='auth_password' value='".htmlentities($this->config['auth_password'], ENT_QUOTES, $charset)."' />
		</div>
        <div class='row'>
            <label for='auth_connexion_phrase'>".htmlentities($this->msg["pmb_connexion_phrase"], ENT_QUOTES, $charset)."</label><br />
            <input type='text' name='auth_connexion_phrase' id='auth_connexion_phrase' class='saisie-80em' value='".htmlentities($this->config['auth_connexion_phrase'], ENT_QUOTES, $charset)."' />
        </div>";

        /* Parametrage fonctionnel */
        $form .= "<hr />
        <h2>" . htmlentities($this->msg['bibloto_functional_settings'], ENT_QUOTES, $charset) . "</h2><br />";

        /* Pret de document */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_checkout_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='checkout_activate' id='checkout_activate_1' value='1' ".($this->config["checkout_activate"] == "1" ? "checked" : "")." />
                <label for='checkout_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='checkout_activate' id='checkout_activate_0' value='0' ".(!$this->config["checkout_activate"] ? "checked" : "")." />
                <label for='checkout_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Validation du pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>" . htmlentities($this->msg['bibloto_auto_checkout'], ENT_QUOTES, $charset) . "</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='auto_checkout' id='auto_checkout_1' value='1' ".($this->config["auto_checkout"] == "1" ? "checked" : "")." />
                <label for='auto_checkout_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='auto_checkout' id='auto_checkout_0' value='0' ".(!$this->config["auto_checkout"] ? "checked" : "")." />
                <label for='auto_checkout_0' > ".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Retour de document */
        $form.= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_checkin_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                    <input type='radio' name='checkin_activate' id='checkin_activate_1' value='1' ".($this->config["checkin_activate"] == "1" ? "checked": "")." />
                    <label for='checkin_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                    <input type='radio' name='checkin_activate' id='checkin_activate_0' value='0' ".(!$this->config["checkin_activate"] ? "checked": "")." />
                    <label for='checkin_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Ignorer les notes des exemplaires au retour */
        $form.= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_checkin_ignore_expl_msg'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                    <input type='radio' name='checkin_ignore_expl_msg' id='checkin_ignore_expl_msg_1' value='1' ".($this->config["checkin_ignore_expl_msg"] == "1" ? "checked": "")." />
                    <label for='checkin_ignore_expl_msg_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                    <input type='radio' name='checkin_ignore_expl_msg' id='checkin_ignore_expl_msg_0' value='0' ".(!$this->config["checkin_ignore_expl_msg"] ? "checked": "")." />
                    <label for='checkin_ignore_expl_msg_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Affichage des reservations */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_resa_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='resa_activate' id='resa_activate_yes' value='1' ".($this->config["resa_activate"] == "1" ? "checked": "")." />
                <label for='resa_activate_yes' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='resa_activate' id='resa_activate_no' value='0' ".(!$this->config["resa_activate"] ? "checked" : "")." />
                <label for='resa_activate_no' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)." </label>
            </div>
        </div>";

        /* Affichage bouton mail */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_email_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='email_activate' id='email_activate_1' value='1' ".($this->config["email_activate"] == "1" ? "checked" : "")." />
                <label for='email_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='email_activate' id='email_activate_0' value='0' ".(!$this->config["email_activate"] ? "checked" : "")." />
                <label for='email_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)." </label>
            </div>
        </div>";

        /* Ecran par defaut */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_default_action'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='default_action' id='default_action_0' value='0' ".(!$this->config["default_action"] ? "checked" : "")." />
                <label for='default_action_0' >".htmlentities($this->msg['bibloto_default_action_default'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='default_action' id='default_action_1' value='1' ".($this->config["default_action"] == "1" ? "checked" : "")." />
                <label for='default_action_1' >".htmlentities($this->msg['bibloto_default_action_checkout'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='default_action' id='default_action_2' value='2' ".($this->config["default_action"] == "2" ? "checked" : "")." />
                <label for='default_action_2' >".htmlentities($this->msg['bibloto_default_action_checkin'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='default_action' id='default_action_3' value='3' ".($this->config["default_action"] == "3" ? "checked" : "")." />
                <label for='default_action_3' >".htmlentities($this->msg['bibloto_default_action_resa'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Ecran par defaut a la fin du pret et du retour */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_default_action_end'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='default_action_end' id='default_action_end_0' value='0' ".(!$this->config["default_action_end"] ? "checked" : "")." />
                <label for='default_action_end_0' >".htmlentities($this->msg['bibloto_default_action_default'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='default_action_end' id='default_action_end_1' value='1' ".($this->config["default_action_end"] == "1" ? "checked" : "")." />
                <label for='default_action_end_1' >".htmlentities($this->msg['bibloto_default_action_end_home'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Jouer les alertes sonores */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_sound_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='sound_activate' id='sound_activate_1' value='1' ".($this->config["sound_activate"] == "1" ? "checked" : "")." />
                <label for='sound_activate_1'>".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='sound_activate' id='sound_activate_0' value='0' ".(!$this->config["sound_activate"] ? "checked" : "")." />
                <label for='sound_activate_0'>".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)." </label>
            </div>
        </div>";

        /* Nb jours avant affichage alerte retour pret en retard */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='nb_jours_retard'>".htmlentities($this->msg['bibloto_nb_jours_retard'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='number' min='0' step='1' id='nb_jours_retard' name='nb_jours_retard' value='".$this->config['nb_jours_retard']."' />
            </div>
        </div>";

        /* Timeout deconnexion */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='timeout_disconnect'>".htmlentities($this->msg['bibloto_timeout_disconnect'], ENT_QUOTES, $charset)."</label><br />
            </div>
            <div class='colonne_suite' >
                <input type='number' min='0' step='1' id='timeout_disconnect' name='timeout_disconnect' value='".$this->config['timeout_disconnect']."' />
            </div>
        </div>";


        $form .= "<br />";
        /* Trombinoscope */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_enable_trombinoscope'], ENT_QUOTES, $charset)."</label><br />
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='trombinoscope_enabled' id='trombinoscope_enabled_1' value='1' ".($this->config["trombinoscope_enabled"] == "1" ? "checked" : "")." />
                <label for='trombinoscope_enabled_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='trombinoscope_enabled' id='trombinoscope_enabled_0' value='0' ".(!$this->config["trombinoscope_enabled"] ? "checked" : "")." />
                <label for='trombinoscope_enabled_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";
        /* Trombinoscope avec authentification*/
        global $opac_url_base;
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_trombinoscope_auth'], ENT_QUOTES, $charset)."</label><br />
            </div>
            <div class='colonne_suite'>
                <input type='radio' name='trombinoscope_auth' id='trombinoscope_auth_1' value='1' ".(isset($this->config["trombinoscope_auth"]) && $this->config["trombinoscope_auth"] == "1" ? "checked" : "")." />
                <label for='trombinoscope_auth_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='trombinoscope_auth' id='trombinoscope_auth_0' value='0' ".(isset($this->config["trombinoscope_auth"]) && $this->config["trombinoscope_auth"] ? "" : "checked")." />
                <label for='trombinoscope_auth_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
                <input type='hidden' name='opac_url' value='".$opac_url_base."' />
            </div>
        </div>";

        /* URL vignettes lecteurs */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='thumbnail_url'>".htmlentities($this->msg['bibloto_thumbnail_folder'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='thumbnail_url' name='thumbnail_url' value='".$this->config['thumbnail_url']."' />
            </div>
        </div>";
        
        $form .= "<br />";
        /* forcer la connexion externe */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_force_ext_auth'], ENT_QUOTES, $charset)."&nbsp;<i class='fa fa-info-circle' title='" . htmlentities($this->msg['bibloto_force_ext_auth_tooltip'], ENT_QUOTES, $charset) . "'></i>&nbsp;</label><br />
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='force_ext_auth' id='force_ext_auth_0' value='1' ".($this->config["force_ext_auth"] == "1" ? "checked" : "")." />
                <label for='force_ext_auth_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='force_ext_auth' id='force_ext_auth_0' value='0' ".(!$this->config["force_ext_auth"] ? "checked" : "")." />
                <label for='force_ext_auth_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>
        <div class='row'>
            <div class='colonne4' >
                <label for='logout_tab_ttl' class='etiquette'>".htmlentities($this->msg['bibloto_ext_auth_logout_tab_ttl'], ENT_QUOTES, $charset)."</label><br />
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='logout_tab_ttl' name='logout_tab_ttl' value='".($this->config['logout_tab_ttl'] ?? 3)."' />
            </div>
        </div>";

        /* Parametrage RFID */
        $form .= "<div class='row'></div>
        <hr />
        <h2>" . htmlentities($this->msg['bibloto_rfid_settings'], ENT_QUOTES, $charset) . "</h2><br />";

        /* Activation RFID */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='rfid_activate' id='rfid_activate_1' value='1' ".($this->config["rfid_activate"] == "1" ? "checked" : "")." />
                <label for='rfid_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='rfid_activate' id='rfid_activate_0' value='0' ".(!$this->config["rfid_activate"] ? "checked" : "")." />
                <label for='rfid_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Activer RFID pour lecture cartes lecteurs */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate_empr'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='rfid_activate_empr' id='rfid_activate_empr_1' value='1' ".($this->config["rfid_activate_empr"] == "1" ? "checked" : "")." />
                <label for='rfid_activate_empr_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='rfid_activate_empr' id='rfid_activate_empr_0' value='0' ".(!$this->config["rfid_activate_empr"] ? "checked" : "")." />
                <label for='rfid_activate_empr_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Activer RFID pour lecture tags documents */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_rfid_activate_expl'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='rfid_activate_expl' id='rfid_activate_expl_1' value='1' ".($this->config["rfid_activate_expl"] == "1" ? "checked" : "")." />
                <label for='rfid_activate_expl_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='rfid_activate_expl' id='rfid_activate_expl_0' value='0' ".(!$this->config["rfid_activate_expl"] ? "checked" : "")." />
                <label for='rfid_activate_expl_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Driver RFID */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_driver'>".htmlentities($this->msg['bibloto_rfid_driver'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='rfid_driver' name='rfid_driver' value='".$this->config['rfid_driver']."' />
            </div>
        </div>";

        /* URL Serveur RFID */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_serveur_url' >".htmlentities($this->msg['bibloto_rfid_serveur_url'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='rfid_serveur_url' name='rfid_serveur_url' value='".$this->config['rfid_serveur_url']."' />
            </div>
        </div>";

        /* Code bibliotheque */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_library_code'>".htmlentities($this->msg['bibloto_rfid_library_code'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='rfid_library_code' name='rfid_library_code' value='".$this->config['rfid_library_code']."' />
            </div>
        </div>";

        /* Activation de la gestion antivol */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_security_activate'>".htmlentities($this->msg['bibloto_rfid_security_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='rfid_security_activate' id='rfid_security_activate_1' value='1' ".($this->config["rfid_security_activate"] == "1" ? "checked" : "")." />
                <label for='rfid_security_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='rfid_security_activate' id='rfid_security_activate_0' value='0' ".(!$this->config["rfid_security_activate"] ? "checked" : "")." />
                <label for='rfid_security_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Code AFI antivol actif */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_afi_security_code_on'>".htmlentities($this->msg['bibloto_rfid_afi_security_code_on'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='rfid_afi_security_code_on' name='rfid_afi_security_code_on' value='".$this->config['rfid_afi_security_code_on']."' />
            </div>
        </div>";

        /* Code AFI antivol inactif */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='rfid_afi_security_code_off'>".htmlentities($this->msg['bibloto_rfid_afi_security_code_off'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='rfid_afi_security_code_off' name='rfid_afi_security_code_off' value='".$this->config['rfid_afi_security_code_off']."' />
            </div>
        </div>";


        /* Parametrage interface utilisateur */
        $form .= "<div class='row'></div>
        <hr />
        <h2>" . htmlentities($this->msg['bibloto_ui_settings'], ENT_QUOTES, $charset) . "</h2><br />";

        /* URL feuille de style */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='style_url'>".htmlentities($this->msg['bibloto_style_url'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='style_url' name='style_url' value='".$this->config['style_url']."' />
            </div>
        </div>";

        /* Template page accueil */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='home_tpl'>".htmlentities($this->msg['bibloto_home_tpl'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <textarea id='home_tpl' wrap='virtual' rows='8' cols='62' name='home_tpl'>".$this->config['home_tpl']."</textarea>
            </div>
        </div>";

        /* Template page emprunteur */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='empr_tpl'>".htmlentities($this->msg['bibloto_empr_tpl'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <textarea id='empr_tpl' wrap='virtual' rows='8' cols='62' name='empr_tpl'>".$this->config['empr_tpl']."</textarea>
            </div>
        </div>";

        /* Code CSS */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='css'>".htmlentities($this->msg['bibloto_css'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <textarea id='css' wrap='virtual' rows='8' cols='62' name='css'>".$this->config['css']."</textarea>
            </div>
        </div>";

        /* Paramétrage des messages */
        $form .= "<div class='row'></div>
        <hr />
        <h2>" . htmlentities($this->msg['bibloto_msg_settings'], ENT_QUOTES, $charset) . "</h2><br />";

        /* Libelle bouton pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_checkout_button'>".htmlentities($this->msg['bibloto_msg_checkout_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_checkout_button' name='msg_checkout_button' value='".htmlentities($this->config['msg_checkout_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton validation pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_checkout_valid_button'>".htmlentities($this->msg['bibloto_msg_checkout_valid_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_checkout_valid_button' name='msg_checkout_valid_button' value='".htmlentities($this->config['msg_checkout_valid_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton retour */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_checkin_button'>".htmlentities($this->msg['bibloto_msg_checkin_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_checkin_button' name='msg_checkin_button' value='".htmlentities($this->config['msg_checkin_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton resa */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_resa_button'>".htmlentities($this->msg['bibloto_msg_resa_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_resa_button' name='msg_resa_button' value='".htmlentities($this->config['msg_resa_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton envoi mail */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_email_button'>".htmlentities($this->msg['bibloto_msg_email_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_email_button' name='msg_email_button' value='".htmlentities($this->config['msg_email_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton retour accueil */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_exit_button'>".htmlentities($this->msg['bibloto_msg_exit_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_exit_button' name='msg_exit_button' value='".htmlentities($this->config['msg_exit_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle bouton terminer */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_exit'>".htmlentities($this->msg['bibloto_msg_dialog_exit'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_dialog_exit' name='msg_dialog_exit' value='".htmlentities($this->config['msg_dialog_exit'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle liste des prets en cours */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_action_title'>".htmlentities($this->msg['bibloto_msg_action_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_action_title' name='msg_action_title' value='".htmlentities($this->config['msg_action_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle liste des prets effectues */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_checkout_title'>".htmlentities($this->msg['bibloto_msg_checkout_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_checkout_title' name='msg_checkout_title' value='".htmlentities($this->config['msg_checkout_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle liste des retours effectues */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_checkin_title'>".htmlentities($this->msg['bibloto_msg_checkin_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text'  class='saisie-80em' id='msg_checkin_title' name='msg_checkin_title' value='".htmlentities($this->config['msg_checkin_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle liste des reservations */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_resa_title'>".htmlentities($this->msg['bibloto_msg_resa_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text'  class='saisie-80em' id='msg_resa_title' name='msg_resa_title' value='".htmlentities($this->config['msg_resa_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle "date" de la liste des reservations */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_resa_date_title'>".htmlentities($this->msg['bibloto_msg_resa_date_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text'  class='saisie-80em' id='msg_resa_date_title' name='msg_resa_date_title' value='".htmlentities($this->config['msg_resa_date_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Libelle "confirme" de la liste des reservations */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_resa_confirme_title'>".htmlentities($this->msg['bibloto_msg_resa_confirme_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text'  class='saisie-80em' id='msg_resa_confirme_title' name='msg_resa_confirme_title' value='".htmlentities($this->config['msg_resa_confirme_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle de la liste de prets */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_checkout_list_title'>".htmlentities($this->msg['bibloto_msg_expl_checkout_list_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_expl_checkout_list_title' name='msg_expl_checkout_list_title' value='".htmlentities($this->config['msg_expl_checkout_list_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle du titre des exemplaires */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_title'>".htmlentities($this->msg['bibloto_msg_expl_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_expl_title' name='msg_expl_title' value='".htmlentities($this->config['msg_expl_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle du statut des exemplaires */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_statut'>".htmlentities($this->msg['bibloto_msg_expl_statut'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_expl_statut' name='msg_expl_statut' value='".htmlentities($this->config['msg_expl_statut'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle de la date de pret des exemplaires */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_date_checkout'>".htmlentities($this->msg['bibloto_msg_expl_date_checkout'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_expl_date_checkout' name='msg_expl_date_checkout' value='".htmlentities($this->config['msg_expl_date_checkout'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle de la date de retour des exemplaires */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_date_checkin'>".htmlentities($this->msg['bibloto_msg_expl_date_checkin'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_expl_date_checkin' name='msg_expl_date_checkin' value='".htmlentities($this->config['msg_expl_date_checkin'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Libelle de l'etat "rendu" des exemplaires */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_expl_rendu'>".htmlentities($this->msg['bibloto_msg_expl_rendu'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text'  class='saisie-80em' id='msg_expl_rendu' name='msg_expl_rendu' value='".htmlentities($this->config['msg_expl_rendu'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Message de l'action poser un document pour le pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_place_item_checkout'>".htmlentities($this->msg['bibloto_msg_dialog_place_item_checkout'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_place_item_checkout' name='msg_dialog_place_item_checkout' value='".htmlentities($this->config['msg_dialog_place_item_checkout'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Message de l'action poser un document pour le retour */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_place_item_checkin'>".htmlentities($this->msg['bibloto_msg_dialog_place_item_checkin'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_place_item_checkin' name='msg_dialog_place_item_checkin' value='".htmlentities($this->config['msg_dialog_place_item_checkin'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message d'erreur trop de documents */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_too_many_items'>".htmlentities($this->msg['bibloto_msg_dialog_too_many_items'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_too_many_items' name='msg_dialog_too_many_items' value='".htmlentities($this->config['msg_dialog_too_many_items'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message d'erreur si document inconnu */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_item_cb_unknown'>".htmlentities($this->msg['bibloto_msg_dialog_item_cb_unknown'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_item_cb_unknown' name='msg_dialog_item_cb_unknown' value='".htmlentities($this->config['msg_dialog_item_cb_unknown'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message si exemplaire empruntable */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkout_possible'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_possible'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_checkout_possible' name='msg_dialog_checkout_possible' value='".htmlentities($this->config['msg_dialog_checkout_possible'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Message de pret effectue */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkout_ok'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_ok'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_checkout_ok' name='msg_dialog_checkout_ok' value='".htmlentities($this->config['msg_dialog_checkout_ok'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message d'erreur de pret. Appliquable pour tous les refus de prets ?  */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkout_no'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_no'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_checkout_no' name='msg_dialog_checkout_no' value='".htmlentities($this->config['msg_dialog_checkout_no'], ENT_QUOTES, $charset)."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkout_no_all'>".htmlentities($this->msg['bibloto_msg_dialog_checkout_no_all'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='msg_dialog_checkout_no_all' id='msg_dialog_checkout_no_all_1' value='1' ".($this->config["msg_dialog_checkout_no_all"] ? "checked" : "")." />
                <label for='msg_dialog_checkout_no_all_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='msg_dialog_checkout_no_all' id='msg_dialog_checkout_no_all_0' value='0' ".(!$this->config["msg_dialog_checkout_no_all"] ? "checked" : "")." />
                <label for='msg_dialog_checkout_no_all_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /* Message de retour effectue  */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkin_ok'>".htmlentities($this->msg['bibloto_msg_dialog_checkin_ok'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_checkin_ok' name='msg_dialog_checkin_ok' value='".htmlentities($this->config['msg_dialog_checkin_ok'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message d'erreur en retour si le document n'est pas en pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_checkin_no_checkout'>".htmlentities($this->msg['bibloto_msg_dialog_checkin_no_checkout'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_checkin_no_checkout' name='msg_dialog_checkin_no_checkout' value='".htmlentities($this->config['msg_dialog_checkin_no_checkout'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message d'erreur d'antivol */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_dialog_antivol_error'>".htmlentities($this->msg['bibloto_msg_dialog_antivol_error'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_dialog_antivol_error' name='msg_dialog_antivol_error' value='".htmlentities($this->config['msg_dialog_antivol_error'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /*  Message aucun utilisateur n'a ete trouve */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_no_user_found'>".htmlentities($this->msg['bibloto_msg_no_user_found'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_no_user_found' name='msg_no_user_found' value='".htmlentities($this->config['msg_no_user_found'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message affiché en titre de recherche */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_search_title'>".htmlentities($this->msg['bibloto_msg_search_title'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_search_title' name='msg_search_title' value='".htmlentities($this->config['msg_search_title'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message affiché en cas d'authentification sur le mauvais compte */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_trombinoscope_auth_error'>".htmlentities($this->msg['bibloto_msg_trombinoscope_auth_error'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_trombinoscope_auth_error' name='msg_trombinoscope_auth_error' value='".htmlentities($this->config['msg_trombinoscope_auth_error'] ?? $this->msg['msg_trombinoscope_auth_error'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Parametrage impression tickets de prêt */
        $form .= "<div class='row'></div>
        <hr />
        <h2>" . htmlentities($this->msg['bibloto_print_settings'], ENT_QUOTES, $charset) . "</h2><br />";

        /* Activer l'impression de tickets de pret */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['bibloto_activate_printer'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='printer_activate' id='printer_activate_0' value='0' ".(!$this->config["printer_activate"] ? "checked" : "")." />
                <label for='printer_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>

                <input type='radio' name='printer_activate' id='printer_activate_1' value='1' ".($this->config["printer_activate"] == "1" ? "checked" : "")." />
                <label for='printer_activate_1' >".htmlentities($this->msg['bibloto_rfid_activate_expl_yes_auto'], ENT_QUOTES, $charset)."</label>

                <input type='radio' name='printer_activate' id='printer_activate_2' value='2' ".($this->config["printer_activate"] == "2" ? "checked" : "")." />
                <label for='printer_activate_2' >".htmlentities($this->msg['bibloto_rfid_activate_expl_yes_manual'], ENT_QUOTES, $charset)."</label>

                <input type='radio' name='printer_activate' id='printer_activate_3' value='3' ".($this->config["printer_activate"] == "3" ? "checked" : "")." />
                <label for='printer_activate_3' >".htmlentities($this->msg['bibloto_rfid_activate_expl_yes_only_all_loans'], ENT_QUOTES, $charset)."</label>

                <input type='hidden' name='printer_name' value='$pmb_printer_name' />
            </div>
        </div>";

        /* Activation bouton d'impression de tous les prets */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette'>".htmlentities($this->msg['print_all_loans_activate'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='radio' name='print_all_loans_activate' id='print_all_loans_activate_1' value='1' ".($this->config["print_all_loans_activate"] == "1" ? "checked" : "")." />
                <label for='print_all_loans_activate_1' >".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."</label>
                <input type='radio' name='print_all_loans_activate' id='print_all_loans_activate_0' value='0' ".(!$this->config["print_all_loans_activate"] ? "checked" : "")." />
                <label for='print_all_loans_activate_0' >".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."</label>
            </div>
        </div>";

        /*  Libelle du bouton d'impression de tous les prets */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_print_all_loans' >".htmlentities($this->msg['msg_print_all_loans'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' id='msg_print_all_loans' name='msg_print_all_loans' value='".htmlentities($this->config['msg_print_all_loans'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        /* Message de demande d'impression (impression sur demande) */
        $form .= "<div class='row'>
            <div class='colonne4' >
                <label class='etiquette' for='msg_printer_button'>".htmlentities($this->msg['bibloto_msg_printer_button'], ENT_QUOTES, $charset)."</label>
            </div>
            <div class='colonne_suite' >
                <input type='text' class='saisie-80em' id='msg_printer_button' name='msg_printer_button' value='".htmlentities($this->config['msg_printer_button'], ENT_QUOTES, $charset)."' />
            </div>
        </div>";

        $form.="
        <hr/>
        <div class='row'></div>
        <div class='row'>
            <input type='hidden' id='msg_bibloto_yes' name='msg_bibloto_yes' value='".htmlentities($this->msg['bibloto_yes'], ENT_QUOTES, $charset)."' />
            <input type='hidden' id='msg_bibloto_no' name='msg_bibloto_no' value='".htmlentities($this->msg['bibloto_no'], ENT_QUOTES, $charset)."' />
        </div>";
        return $form;
    }


    public function update_config_from_form()
    {
        global $pmb_ws_url;
        global $auth_login;
        global $auth_password;
        global $auth_connexion_phrase;
        global $style_url;
        global $checkout_activate;
        global $auto_checkout;
        global $checkin_activate;
        global $checkin_ignore_expl_msg;
        global $resa_activate;
        global $email_activate;
        global $printer_activate;
        global $printer_name;
        global $printer_tpl;
        global $sound_activate;
        global $trombinoscope_enabled;
        global $trombinoscope_auth;
        global $opac_url;
        global $thumbnail_url;
        global $nb_jours_retard;
        global $rfid_activate;
        global $rfid_driver;
        global $rfid_activate_empr ;
        global $rfid_activate_expl;
        global $rfid_serveur_url;
        global $rfid_library_code;
        global $rfid_security_activate;
        global $rfid_afi_security_code_on ;
        global $rfid_afi_security_code_off ;
        global $msg_checkout_button;
        global $msg_checkout_valid_button;
        global $msg_checkin_button;
        global $msg_resa_button;
        global $msg_email_button;
        global $msg_exit_button;
        global $msg_action_title;
        global $msg_checkout_title;
        global $msg_checkin_title;
        global $msg_resa_title;
        global $msg_resa_date_title;
        global $msg_resa_confirme_title;
        global $msg_expl_checkout_list_title;
        global $msg_expl_title;
        global $msg_expl_statut;
        global $msg_expl_date_checkout;
        global $msg_expl_date_checkin;
        global $msg_expl_rendu;
        global $msg_dialog_place_item_checkout;
        global $msg_dialog_place_item_checkin;
        global $msg_dialog_too_many_items;
        global $msg_dialog_item_cb_unknown;
        global $msg_dialog_checkout_possible;
        global $msg_dialog_checkout_no;
        global $msg_dialog_checkout_no_all;
        global $msg_dialog_checkout_ok;
        global $msg_dialog_checkin_ok;
        global $msg_dialog_checkin_no_checkout;
        global $msg_dialog_antivol_error;
        global $msg_printer_button;
        global $msg_dialog_exit;
        global $msg_no_user_found;
        global $msg_search_title;
        global $timeout_disconnect;
        global $home_tpl;
        global $empr_tpl;
        global $default_action;
        global $default_action_end;
        global $css;
        global $printer_activate;
        global $printer_name;
        global $msg_bibloto_yes;
        global $msg_bibloto_no;
        global $print_all_loans_activate;
        global $msg_print_all_loans;
        global $msg_trombinoscope_auth_error;
        global $force_ext_auth;
        global $logout_tab_ttl;
        parent::update_config_from_form();

        $this->config = [];
        $this->config['pmb_ws_url'] = $pmb_ws_url;
        $this->config['auth_login'] = $auth_login;
        $this->config['auth_password'] = $auth_password;
        $this->config['auth_connexion_phrase'] = stripslashes($auth_connexion_phrase);
        $this->config['style_url'] = $style_url;
        $this->config['checkout_activate'] = $checkout_activate;
        $this->config['auto_checkout'] = $auto_checkout;
        $this->config['checkin_activate'] = $checkin_activate;
        $this->config['checkin_ignore_expl_msg'] = $checkin_ignore_expl_msg;
        $this->config['resa_activate'] = $resa_activate;
        $this->config['email_activate'] = $email_activate;
        $this->config['printer_activate'] = $printer_activate;
        $this->config['printer_name'] = stripslashes($printer_name);
        $this->config['printer_tpl'] = stripslashes($printer_tpl);
        $this->config['default_action'] = $default_action;
        $this->config['default_action_end'] = $default_action_end;
        $this->config['sound_activate'] = $sound_activate;
        $this->config['trombinoscope_enabled'] = $trombinoscope_enabled;
        $this->config['trombinoscope_auth'] = $trombinoscope_auth;
        $this->config['opac_url'] = $opac_url;
        $this->config['thumbnail_url'] = $thumbnail_url;
        $this->config['nb_jours_retard'] = $nb_jours_retard;
        $this->config['rfid_activate'] = $rfid_activate;
        $this->config['rfid_driver'] = $rfid_driver;
        $this->config['rfid_activate_empr'] = $rfid_activate_empr;
        $this->config['rfid_activate_expl'] = $rfid_activate_expl;
        $this->config['rfid_serveur_url'] = $rfid_serveur_url;
        $this->config['rfid_library_code'] = $rfid_library_code;
        $this->config['rfid_afi_security_code_on'] = $rfid_afi_security_code_on;
        $this->config['rfid_afi_security_code_off'] = $rfid_afi_security_code_off;
        $this->config['rfid_security_activate'] = $rfid_security_activate;

        $this->config['home_tpl'] = stripslashes($home_tpl);
        $this->config['empr_tpl'] = stripslashes($empr_tpl);
        $this->config['msg_checkout_button'] = stripslashes($msg_checkout_button);
        $this->config['msg_checkout_valid_button'] = stripslashes($msg_checkout_valid_button);
        $this->config['msg_checkin_button'] = stripslashes($msg_checkin_button);
        $this->config['msg_resa_button'] = stripslashes($msg_resa_button);
        $this->config['msg_email_button'] = stripslashes($msg_email_button);
        $this->config['msg_printer_button'] = stripslashes($msg_printer_button);
        $this->config['msg_exit_button'] = stripslashes($msg_exit_button);
        $this->config['msg_action_title'] = stripslashes($msg_action_title);
        $this->config['msg_checkout_title'] = stripslashes($msg_checkout_title);
        $this->config['msg_checkin_title'] = stripslashes($msg_checkin_title);
        $this->config['msg_resa_title'] = stripslashes($msg_resa_title);
        $this->config['msg_resa_date_title'] = stripslashes($msg_resa_date_title);
        $this->config['msg_resa_confirme_title'] = stripslashes($msg_resa_confirme_title);

        $this->config['msg_expl_checkout_list_title'] = stripslashes($msg_expl_checkout_list_title);
        $this->config['msg_expl_title'] = stripslashes($msg_expl_title);
        $this->config['msg_expl_statut'] = stripslashes($msg_expl_statut);
        $this->config['msg_expl_date_checkout'] = stripslashes($msg_expl_date_checkout);
        $this->config['msg_expl_date_checkin'] = stripslashes($msg_expl_date_checkin);
        $this->config['msg_expl_rendu'] = stripslashes($msg_expl_rendu);

        $this->config['msg_dialog_place_item_checkout'] = stripslashes($msg_dialog_place_item_checkout);
        $this->config['msg_dialog_place_item_checkin'] = stripslashes($msg_dialog_place_item_checkin);
        $this->config['msg_dialog_too_many_items'] = stripslashes($msg_dialog_too_many_items);
        $this->config['msg_dialog_item_cb_unknown'] = stripslashes($msg_dialog_item_cb_unknown);
        $this->config['msg_dialog_checkout_possible'] = stripslashes($msg_dialog_checkout_possible);
        $this->config['msg_dialog_checkout_ok'] = stripslashes($msg_dialog_checkout_ok);
        $this->config['msg_dialog_checkout_no'] = stripslashes($msg_dialog_checkout_no);
        $this->config['msg_dialog_checkout_no_all'] = stripslashes($msg_dialog_checkout_no_all);
        $this->config['msg_dialog_checkin_ok'] = stripslashes($msg_dialog_checkin_ok);
        $this->config['msg_dialog_checkin_no_checkout'] = stripslashes($msg_dialog_checkin_no_checkout);
        $this->config['msg_dialog_antivol_error'] = stripslashes($msg_dialog_antivol_error);
        $this->config['msg_dialog_exit'] = stripslashes($msg_dialog_exit);
        $this->config['msg_no_user_found'] = stripslashes($msg_no_user_found);
        $this->config['msg_search_title'] = stripslashes($msg_search_title);
        $this->config['timeout_disconnect'] = $timeout_disconnect;
        $this->config['css'] = stripslashes($css);
        $this->config['printer_activate'] = stripslashes($printer_activate);
        $this->config['printer_name'] = stripslashes($printer_name);
        $this->config['msg_bibloto_yes'] = stripslashes($msg_bibloto_yes);
        $this->config['msg_bibloto_no'] = stripslashes($msg_bibloto_no);
        $this->config['print_all_loans_activate'] = stripslashes($print_all_loans_activate);
        $this->config['msg_print_all_loans'] = stripslashes($msg_print_all_loans);
        $this->config['msg_trombinoscope_auth_error'] = stripslashes($msg_trombinoscope_auth_error);
        $this->config['force_ext_auth'] = $force_ext_auth;
        $this->config['logout_tab_ttl'] = $logout_tab_ttl;
    }
}
