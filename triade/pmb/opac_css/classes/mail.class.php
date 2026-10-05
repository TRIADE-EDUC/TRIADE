<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail.class.php,v 1.28.4.1 2025/05/09 12:24:17 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use Greew\OAuth2\Client\Provider\Azure;
use League\OAuth2\Client\Provider\Google;
use Pmb\Common\Library\Crypto\Crypto;
use Pmb\Common\Library\Mailer\AzureOAuth;

global $class_path;
require_once $class_path . "/mails/mail_configuration.class.php";

class mail
{

    protected $id = 0;

    protected $type = '';

    protected $to_name = '';

    protected $to_mail = [];

    protected $object = '';

    protected $content = '';

    protected $from_name = '';

    protected $from_mail = '';

    protected $headers = [];

    protected $copy_cc = [];

    protected $copy_bcc = [];

    protected $do_nl2br = 0;

    protected $attachments = [];

    protected $embedded_attachments = [];

    protected $reply_name = '';

    protected $reply_mail = '';

    protected $date = '';

    protected $sended = 0;

    protected $error = '';

    protected $from_uri = '';

    protected $num_campaign = 0;

    protected $is_mailing = false;

    // Variables de contexte PMB
    protected static $msg = [];

    protected $charset = 'utf-8';

    protected $display_errors = 0;

    protected $supervision_mails_active = 0;

    protected $mail_html_format = '';

    protected $mail_adresse_from = '';

    protected $has_mail_adresse_from_configuration = false;

    protected $mail_list_unsubscribe_mailto = '';

    protected $opac_contact_form = '';

    protected $opac_url_base = '';

    protected $opac_biblio_name = '';

    // Configuration mail_methode normalisee
    protected $mail_method = '';

    // Tableau des parametres chiffres
    protected $mail_method_is_encrypted = [];

    // Variable eventuellement definie dans config_local.inc.php / opac_config_local.inc.php
    protected $SMTPOptions;

    // Parametres de configuration possibles
    const MAIL_METHOD_KEYS = [
        'method',
        'host',
        'auth',
        'user',
        'pass',
        'secure',
        'encoding',
        'auth_type',
        'xoauth2_provider',
        'xoauth2_tenant_id',
        'xoauth2_client_id',
        'xoauth2_secret_value',
        'xoauth2_refresh_token',
        'xoauth2_refresh_token_validity'
    ];

    /**
     * Types d'authentification en SMTP
     */
    const SMTP_AUTH_TYPES = [
        'CRAM-MD5',
        'LOGIN',
        'PLAIN',
        'XOAUTH2'
    ];

    /**
     * Liste des fournisseurs pris en compte
     */
    const XOAUTH2_PROVIDER_LIST = [
        "azure",
        "google"
    ];

    /**
     * Liste des champs en fonction du type d'authentification
     */
    const SMTP_AUTH_TYPES_FIELDS = [
        'cram-md5' => [],
        'login' => [],
        'plain' => [],
        'xoauth2' => [
            'provider',
            'tenant_id',
            'client_id',
            'secret_value',
            'refresh_token',
            'refresh_token_validity'
        ]
    ];

    /**
     * Instance classe chiffrement/déchiffrement
     *
     * @var object
     */
    protected static $crypto_instance = null;

    public static $table_name = 'mails';

    protected static $server_configuration;

    protected static $purge_days = 14; // 2 semaines

    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->date = date('Y-m-d H:i:s');
        $this->loadContext();
        $this->fetch_data();
    }

    /**
     * Adaptation au contexte : Gestion / OPAC
     * Il ne devrait pas y avoir de differences entre la classe en gestion et la classe en OPAC sauf dans cette methode
     */
    protected function loadContext()
    {
        global $msg, $charset, $supervision_mails_active;
        global $opac_display_errors;
        global $opac_mail_methode, $opac_mail_html_format, $opac_mail_adresse_from;
        global $pmb_mail_list_unsubscribe_mailto, $opac_contact_form;
        global $opac_url_base, $opac_biblio_name;
        global $SMTPOptions;

        static::$msg = &$msg;
        $this->charset = $charset;
        $this->supervision_mails_active = $supervision_mails_active;
        // L'affichage des erreurs est desactive en OPAC
        // $this->display_errors = $opac_display_errors;

        $this->mail_html_format = $opac_mail_html_format;
        $this->mail_adresse_from = $opac_mail_adresse_from;
        $this->mail_list_unsubscribe_mailto = $pmb_mail_list_unsubscribe_mailto;
        $this->opac_contact_form = $opac_contact_form;
        $this->opac_url_base = $opac_url_base;
        $this->opac_biblio_name = $opac_biblio_name;

        $this->mail_method = $this->formatMailMethodParameters($opac_mail_methode);
        $this->SMTPOptions = $SMTPOptions;
    }

    /**
     * Formatage des parametres mail_method
     *
     * @param string|array $mail_method
     * @return string[]
     */
    protected function formatMailMethodParameters($mail_method = '')
    {
        $new_config = false;
        $format = 'string';

        if (is_array($mail_method)) {

            $new_config = true;
            $format = 'array';
        } else {

            $mail_method = trim($mail_method);
            $mail_method = str_replace([
                "\n",
                "\r"
            ], '', $mail_method);
            if ('method' == substr($mail_method, 0, 6)) {
                $new_config = true;
            }
        }

        // Ancien format chaine valeur,val...
        if ($format == 'string' && ! $new_config) {

            $tmp_mail_method = explode(",", $mail_method);

            $final_mail_method = array();
            $final_mail_method['method'] = (! empty($tmp_mail_method[0])) ? $tmp_mail_method[0] : '';
            $final_mail_method['host'] = (! empty($tmp_mail_method[1])) ? $tmp_mail_method[1] : '';
            $final_mail_method['auth'] = (! empty($tmp_mail_method[2])) ? $tmp_mail_method[2] : 0;
            $final_mail_method['user'] = (! empty($tmp_mail_method[3])) ? $tmp_mail_method[3] : '';
            $final_mail_method['pass'] = (! empty($tmp_mail_method[4])) ? $tmp_mail_method[4] : '';
            $final_mail_method['secure'] = (! empty($tmp_mail_method[5])) ? $tmp_mail_method[5] : '';
            $final_mail_method['auth_type'] = (! empty($tmp_mail_method[6])) ? $tmp_mail_method[6] : '';

            // Nouveau format chaine cle=valeur;...
        } elseif ($format == 'string' && $new_config) {

            $tmp_mail_method = explode(";", $mail_method);
            $final_mail_method = [];
            for ($i = 0; $i < count($tmp_mail_method); $i ++) {

                $key = '';
                $value = '';
                $pos = 0;

                $pos = stripos($tmp_mail_method[$i], "=");
                if ($pos) {
                    $key = strtolower(trim(substr($tmp_mail_method[$i], 0, $pos)));
                    if (in_array($key, mail::MAIL_METHOD_KEYS)) {
                        $value = trim(substr($tmp_mail_method[$i], $pos + 1));
                        $final_mail_method[$key] = $value;
                    }
                }
            }
            // Nouveau format tableau
        } else {
            $final_mail_method = $mail_method;
        }

        // Dechiffrement des parametres chiffres
        if (is_null(static::$crypto_instance)) {
            try {
                static::$crypto_instance = new Crypto();
                static::$crypto_instance->loadPMBRSAContext();
            } catch (\Exception $e) {
                static::$crypto_instance = null;
            }
        }
        if (! is_null(static::$crypto_instance)) {

            $l = strlen(Crypto::INDICATOR);

            foreach (mail::MAIL_METHOD_KEYS as $k) {

                if (Crypto::INDICATOR == substr($final_mail_method[$k], 0, $l)) {
                    $this->mail_method_is_encrypted[$k] = 1;
                    try {
                        $decrypted_data = static::$crypto_instance->decryptFromHexa($final_mail_method[$k]);
                        $final_mail_method[$k] = $decrypted_data;
                    } catch (\Exception $e) {}
                }
            }
        }

        // Formatage
        foreach (mail::MAIL_METHOD_KEYS as $k) {

            if (! isset($final_mail_method[$k])) {
                $final_mail_method[$k] = '';
                continue;
            }
            switch (true) {
                case ('method' == $k):
                case ('secure' == $k):
                case ('xoauth2_provider' == $k):
                    $final_mail_method[$k] = strtolower($final_mail_method[$k]);
                    break;
                case ('auth_type' == $k):
                    $final_mail_method[$k] = strtoupper($final_mail_method[$k]);
                    break;
            }
        }
        return $final_mail_method;
    }

    /**
     * Recuperation mail depuis table de log
     */
    protected function fetch_data()
    {
        if ($this->id) {
            $query = "select * from " . static::$table_name . " where id_mail = " . $this->id;
            $result = pmb_mysql_query($query);
            $row = pmb_mysql_fetch_assoc($result);
            $this->type = $row['mail_type'];
            $this->to_name = $row['mail_to_name'];
            $this->to_mail = explode(';', $row['mail_to_mail']);
            $this->object = $row['mail_object'];
            $this->content = $row['mail_content'];
            $this->from_name = $row['mail_from_name'];
            $this->from_mail = $row['mail_from_mail'];
            $this->headers = encoding_normalize::json_decode($row['mail_headers']);
            $this->copy_cc = explode(';', $row['mail_copy_cc']);
            $this->copy_bcc = explode(';', $row['mail_copy_bcc']);
            $this->do_nl2br = $row['mail_do_nl2br'];
            $this->attachments = encoding_normalize::json_decode($row['mail_attachments']);
            $this->reply_name = $row['mail_reply_name'];
            $this->reply_mail = $row['mail_reply_mail'];
            $this->date = $row['mail_date'];
            $this->sended = $row['mail_sended'];
            $this->error = $row['mail_error'];
            $this->from_uri = $row['mail_from_uri'];
            $this->num_campaign = $row['mail_num_campaign'];
        }
    }

    /**
     * Ajout mail dans table de logs
     */
    public function add()
    {
        if (! $this->table_exists() || ! $this->supervision_mails_active) {
            return false;
        }
        // Purge des anciens logs de mails
        static::purge();

        $query = "insert into " . static::$table_name . " set
            mail_type = '" . addslashes($this->type) . "',
            mail_to_name = '" . addslashes($this->to_name) . "',
            mail_to_mail = '" . addslashes(implode(';', $this->to_mail)) . "',
            mail_object = '" . addslashes($this->object) . "',";
        // Conservation du contenu si le renvoi du mail est autorisé
        if ($this->is_resend_allowed()) {
            $query .= "mail_content = '" . addslashes($this->content) . "',";
        }
        $query .= "mail_from_name = '" . addslashes($this->from_name) . "',
            mail_from_mail = '" . addslashes($this->from_mail) . "',
            mail_headers = '" . addslashes(encoding_normalize::json_encode($this->headers)) . "',
            mail_copy_cc = '" . addslashes(implode(';', $this->copy_cc)) . "',
            mail_copy_bcc = '" . addslashes(implode(';', $this->copy_bcc)) . "',
            mail_do_nl2br = '" . $this->do_nl2br . "',";
        // Conservation des pieces jointes si le renvoi du mail est autorisé
        if ($this->is_resend_allowed()) {
            $query .= "mail_attachments = '" . addslashes(encoding_normalize::json_encode($this->attachments)) . "',";
        } else {
            $query .= "mail_attachments = '[]',";
        }
        $query .= "mail_reply_name = '" . addslashes($this->reply_name) . "',
            mail_reply_mail = '" . addslashes($this->reply_mail) . "',
            mail_date = '" . addslashes($this->date) . "',
            mail_sended = '" . intval($this->sended) . "',
            mail_error = '" . addslashes($this->error) . "',
            mail_from_uri = '" . addslashes($this->from_uri) . "',
            mail_num_campaign = '" . intval($this->num_campaign) . "'";
        $result = pmb_mysql_query($query);
        if ($result) {
            $this->id = pmb_mysql_insert_id();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Suppression mail dans table de logs
     */
    public function delete()
    {
        $query = "delete from " . static::$table_name . " where id_mail = " . $this->id;
        pmb_mysql_query($query);
        if ($this->table_is_empty()) {
            $query = "ALTER TABLE " . static::$table_name . " AUTO_INCREMENT = 1";
            pmb_mysql_query($query);
        }
    }

    /**
     * Verifie si la table de logs est vide
     *
     * @return boolean
     */
    protected function table_is_empty()
    {
        $query = "select count(*) from " . static::$table_name;
        $result = pmb_mysql_query($query);
        if (pmb_mysql_result($result, 0, 0) == 0) {
            return true;
        }
        return false;
    }

    /**
     * Verifie si la table de logs existe
     *
     * @return boolean
     */
    protected function table_exists()
    {
        $query = "SHOW TABLES LIKE '" . static::$table_name . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            return true;
        }
        return false;
    }

    public function get_instance_PHPMailer($param)
    {
        $mail = new PHPMailer();

        // Valeurs possibles pour SMTP_DEBUG :
        // SMTP::DEBUG_OFF, SMTP::DEBUG_CLIENT, SMTP::DEBUG_SERVER, SMTP::DEBUG_CONNECTION, SMTP::DEBUG_LOWLEVEL
        // L'affichage des erreurs est desactive en OPAC
        if ($this->display_errors == 1) {
            $mail->SMTPDebug = SMTP::DEBUG_CLIENT;
        } elseif ($this->display_errors == 2) {
            $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
        }

        $mail->CharSet = $this->charset;
        $mail->Timeout = 10;
        $mail->SMTPAutoTLS = false;

        if (! empty($param['encoding'])) {
            $mail->Encoding = $param['encoding'];
        }
        switch ($param['method']) {
            case 'smtp':
                $mail->isSMTP();
                $mail->Host = $param['host'];
                if (! empty($this->SMTPOptions)) {
                    $mail->SMTPOptions = $this->SMTPOptions;
                }
                if (! empty($param['auth'])) {
                    $mail->SMTPAuth = true;
                    $mail->Username = $param['user'];
                    $mail->Password = $param['pass'];
                    if (! empty($param['secure'])) {
                        $mail->SMTPSecure = $param['secure'];
                        $mail->SMTPAutoTLS = true;
                    }
                    if (isset($param['auth_type']) && in_array($param['auth_type'], mail::SMTP_AUTH_TYPES)) {
                        $mail->AuthType = $param['auth_type'];
                    }
                    if ('XOAUTH2' == $param['auth_type']) {
                        switch ($param['xoauth2_provider']) {
                            // Authentification XOAUTH2 Azure
                            case 'azure':
                                // Create a new OAuth2 provider instance
                                $provider = new Azure([
                                    'clientId' => $param['xoauth2_client_id'],
                                    'clientSecret' => $param['xoauth2_secret_value'],
                                    'tenantId' => $param['xoauth2_tenant_id']
                                ]);
                                // Pass the OAuth provider instance to PHPMailer
                                $mail->setOAuth(new AzureOAuth($provider, $param));
                                // Set who the message is to be sent from
                                // For Outlook, this generally needs to be the same as the user you logged in as
                                $this->from_mail = $param['user'];
                                $this->mail_adresse_from = $param['user'];
                                break;
                            // Authentification XOAUTH2 Google
                            case 'google':
                                // Create a new OAuth2 provider instance
                                $provider = new Google([
                                    'clientId' => $param['xoauth2_client_id'],
                                    'clientSecret' => $param['xoauth2_secret_value']
                                ]);
                                // Pass the OAuth provider instance to PHPMailer
                                $mail->setOAuth(new OAuth([
                                    'provider' => $provider,
                                    'clientId' => $param['xoauth2_client_id'],
                                    'clientSecret' => $param['xoauth2_secret_value'],
                                    'refreshToken' => $param['xoauth2_refresh_token'],
                                    'userName' => $param['user']
                                ]));

                                // Set who the message is to be sent from
                                // For gmail, this generally needs to be the same as the user you logged in as
                                $this->from_mail = $param['user'];
                                $this->mail_adresse_from = $param['user'];
                                break;
                        }
                    }
                }
                break;
            default:
            case 'php':
                $mail->isMail();
                $this->to_name = "";
                break;
        }
        return $mail;
    }

    protected function get_log_label()
    {
        return "Object : " . $this->object . ". \r\nSender : " . $this->from_mail . ". \r\nRecipient(s) : " . implode(', ', $this->to_mail);
    }

    protected function get_configuration_method()
    {
        $this->has_mail_adresse_from_configuration = false;
        // On regarde le mail_adresse_from en premier
        if (trim($this->mail_adresse_from)) {
            $tmp_array_email = explode(';', $this->mail_adresse_from);
            $address_configuration = mail_configuration::get_address_configuration($tmp_array_email[0]);
            if (! empty($address_configuration)) {
                $this->has_mail_adresse_from_configuration = true;
                return $this->formatMailMethodParameters($address_configuration);
            } elseif (! empty($this->mail_method['method']) && ! empty($this->mail_method['host'])) {
                $this->has_mail_adresse_from_configuration = true;
                return $this->mail_method;
            }
        }
        // On regarde ensuite l'expéditeur du mail
        // Depuis mail_configuration
        $address_configuration = mail_configuration::get_address_configuration($this->from_mail);
        if (! empty($address_configuration)) {
            return $this->formatMailMethodParameters($address_configuration);
        } else {
            // Depuis parametre mail_method
            return $this->mail_method;
        }
    }

    /**
     * Envoi mail
     *
     * @return boolean
     */
    public function send()
    {
        // L'envoi du mail est-il long ?
        $uniqId = Mail_log::prepare_time($this->get_log_label(), $this->type);

        // Lecture des parametres
        $param = $this->get_configuration_method();
        $mail = $this->get_instance_PHPMailer($param);

        if ($this->mail_html_format) {
            $mail->isHTML(true);
        }

        if (trim($this->mail_adresse_from) && $this->has_mail_adresse_from_configuration == true) {
            $tmp_array_email = explode(';', $this->mail_adresse_from);
            if (! isset($tmp_array_email[1])) {
                $tmp_array_email[1] = '';
            }
            $mail->setFrom($tmp_array_email[0], $tmp_array_email[1]);
            // Le paramètre ci-dessous est utilisé comme destinataire pour les réponses automatiques (erreur de destinataire, validation anti-spam, ...)
            $mail->Sender = $this->from_mail;
        } else {
            $mail->setFrom($this->from_mail, $this->from_name);
        }

        for ($i = 0; $i < count($this->to_mail); $i ++) {
            $mail->addAddress($this->to_mail[$i], $this->to_name);
        }
        for ($i = 0; $i < count($this->copy_cc); $i ++) {
            if (trim($this->copy_cc[$i])) {
                $mail->addCC($this->copy_cc[$i]);
            }
        }
        for ($i = 0; $i < count($this->copy_bcc); $i ++) {
            if (trim($this->copy_bcc[$i])) {
                $mail->addBCC($this->copy_bcc[$i]);
            }
        }
        if ($this->reply_mail && $this->reply_name) {
            $mail->addReplyTo($this->reply_mail, $this->reply_name);
        } else {
            $mail->addReplyTo($this->from_mail, $this->from_name);
        }
        $mail->Subject = $this->object;
        if ($this->mail_html_format) {
            if ($this->do_nl2br) {
                $mail->Body = wordwrap(nl2br($this->content), 70);
            } else {
                $mail->Body = wordwrap($this->content, 70);
            }
            if ($this->mail_html_format == 2) {
                $mail->MsgHTML($mail->Body);
            }
        } else {
            $this->content = str_replace("<hr />", PHP_EOL . "*******************************" . PHP_EOL, $this->content);
            $this->content = str_replace("<br />", PHP_EOL, $this->content);
            $this->content = str_replace(PHP_EOL . PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL, $this->content);
            $this->content = strip_tags($this->content);
            $this->content = html_entity_decode($this->content, ENT_QUOTES, $this->charset);
            $mail->Body = wordwrap($this->content, 70);
        }

        for ($i = 0; $i < count($this->attachments); $i ++) {
            if ($this->attachments[$i]["contenu"] && $this->attachments[$i]["nomfichier"]) {
                $mail->addStringAttachment($this->attachments[$i]["contenu"], $this->attachments[$i]["nomfichier"]);
            }
        }

        for ($i = 0; $i < count($this->embedded_attachments); $i ++) {
            if ($this->embedded_attachments[$i]["content"] && $this->embedded_attachments[$i]["cid"] && $this->embedded_attachments[$i]["filename"] && $this->embedded_attachments[$i]["encoding"] && $this->embedded_attachments[$i]["mimetype"]) {

                $mail->addStringEmbeddedImage($this->embedded_attachments[$i]["content"], $this->embedded_attachments[$i]["cid"], $this->embedded_attachments[$i]["filename"], $this->embedded_attachments[$i]["encoding"], $this->embedded_attachments[$i]["mimetype"]);
            }
        }

        // Desactive en OPAC
        /*
         * if(!empty($this->is_mailing) && !empty($this->mail_list_unsubscribe_mailto)) {
         * // Lien de désinscription ?
         * $mailto = "mailto:".trim($this->mail_list_unsubscribe_mailto)."?subject=".rawurlencode($this->object);
         * if($this->opac_contact_form) {
         * $mail->addCustomHeader("List-Unsubscribe","<".$mailto.">, <".$this->opac_url_base."index.php?lvl=contact_form>");
         * } else {
         * $mail->addCustomHeader("List-Unsubscribe","<".$mailto.">");
         * }
         * if($this->opac_biblio_name) {
         * $mail->XMailer = trim(strip_tags(clean_string($this->opac_biblio_name)));
         * }
         * }
         */

        $retour = true;
        try {

            $retour = $mail->send();
            if (! $retour) {
                $this->error = $mail->ErrorInfo;
            }
        } catch (Exception $e) {

            $retour = false;
            $this->error = $e->getMessage();
        }

        if (! $retour) {
            Mail_log::register(Mail_log::prepare($this->get_log_label(), $this->error));

            // L'affichage des erreurs est desactive en OPAC
            if ($this->display_errors) {
                echo "Erreur SMTP: " . $mail->ErrorInfo . "<br/>";
                echo "Détail: <pre>" . print_r($mail, true) . "</pre>";
                echo "Arret du script, mail non envoyé";
                die();
            }
        } else {
            $this->date = date('Y-m-d H:i:s');
        }

        if ($param['method'] == 'smtp') {
            $mail->smtpClose();
        }
        unset($mail);

        Mail_log::register($uniqId);
        return $retour;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_to_name()
    {
        return $this->to_name;
    }

    public function get_to_mail()
    {
        return $this->to_mail;
    }

    public function get_object()
    {
        return $this->object;
    }

    public function get_from_name()
    {
        return $this->from_name;
    }

    public function get_from_mail()
    {
        return $this->from_mail;
    }

    public function get_copy_cc()
    {
        return $this->copy_cc;
    }

    public function get_copy_bcc()
    {
        return $this->copy_bcc;
    }

    public function get_reply_name()
    {
        return $this->reply_name;
    }

    public function get_reply_mail()
    {
        return $this->reply_mail;
    }

    public function get_date()
    {
        return $this->date;
    }

    public function get_sended()
    {
        return $this->sended;
    }

    public function get_error()
    {
        return $this->error;
    }

    public function get_from_uri()
    {
        return $this->from_uri;
    }

    public function get_num_campaign()
    {
        return $this->num_campaign;
    }

    public function is_resend_allowed()
    {
        // TODO : proposition d'evolution : autoriser le renvoi de mail par type de mail
        return false;
    }

    public function set_type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function set_to_name($to_name)
    {
        $this->to_name = $to_name;
        return $this;
    }

    public function set_to_mail($to_mail)
    {
        $this->to_mail = $to_mail;
        return $this;
    }

    public function set_object($object)
    {
        $this->object = $object;
        return $this;
    }

    public function set_content($content)
    {
        $this->content = $content;
        return $this;
    }

    public function set_from_name($from_name)
    {
        $this->from_name = $from_name;
        return $this;
    }

    public function set_from_mail($from_mail)
    {
        $this->from_mail = $from_mail;
        return $this;
    }

    public function set_headers($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function set_copy_cc($copy_cc)
    {
        $this->copy_cc = $copy_cc;
        return $this;
    }

    public function set_copy_bcc($copy_bcc)
    {
        $this->copy_bcc = $copy_bcc;
        return $this;
    }

    public function set_do_nl2br($do_nl2br)
    {
        $this->do_nl2br = $do_nl2br;
        return $this;
    }

    public function set_attachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    public function set_embedded_attachments($embedded_attachments)
    {
        $this->embedded_attachments = $embedded_attachments;
        return $this;
    }

    public function set_reply_name($reply_name)
    {
        $this->reply_name = $reply_name;
        return $this;
    }

    public function set_reply_mail($reply_mail)
    {
        $this->reply_mail = $reply_mail;
        return $this;
    }

    public function set_sended($sended)
    {
        $this->sended = $sended;
        return $this;
    }

    public function set_error($error)
    {
        $this->error = $error;
        return $this;
    }

    public function set_from_uri($from_uri)
    {
        $this->from_uri = $from_uri;
        return $this;
    }

    public function set_num_campaign($num_campaign)
    {
        $this->num_campaign = intval($num_campaign);
        return $this;
    }

    public function set_is_mailing($is_mailing)
    {
        $this->is_mailing = $is_mailing;
        return $this;
    }

    public static function set_server_configuration($server_configuration)
    {
        static::$server_configuration = $server_configuration;
    }

    public static function get_configuration_form($parameters = array())
    {}

    public static function get_list_types()
    {
        $types = array();
        $query = "SELECT DISTINCT mail_type FROM mails";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $types[$row->mail_type] = mails::get_message('mail_' . $row->mail_type);
            }
        }
        return $types;
    }

    public static function get_list_types_uri()
    {
        $types_uri = array();
        $types_uri['pdf.php?pdfdoc=mail_liste_pret'] = static::$msg['imprimer_liste_pret'];
        $types_uri['circ.php?categ=relance'] = static::$msg['relance_to_do'];
        return $types_uri;
    }

    public static function purge()
    {
        $query = "DELETE FROM mails where date_add(mail_date, INTERVAL " . static::$purge_days . " day)<sysdate()";
        pmb_mysql_query($query);
    }

    /**
     * Tranforme toutes les src des <img> du $html pour les faire pointer vers une pièce jointe
     * Retourne un tableau contenant les données de chaque images en pièce jointe
     *
     * @param string $html
     *
     * @return array
     */
    public static function transformBase64ImgToEmbeddedAttachments(&$html): array
    {
        $embeddedAttachments = [];

        if (empty($html)) {
            return $embeddedAttachments;
        }
        $dom = new \DOMDocument("1.0", "UTF-8");
        $loadedHTML = @$dom->loadHTML($html, LIBXML_ERR_NONE);
        if ($loadedHTML === false) {
            return $embeddedAttachments;
        }
        $xpath = new \DOMXpath($dom);

        // Expression Xpath pour trouver toutes les balises <img> avec une src commençant par 'data:'
        $imgElements = $xpath->query("//img[starts-with(@src, 'data:')]");
        if (! empty($imgElements)) {
            foreach ($imgElements as $imgElement) {
                $src = $imgElement->getAttribute("src");
                // Regex pour extraire le mime type, l'encodage et le base64
                $matches = [];
                if (preg_match('/^data:\s*(image\/\w+);(\w+),(.*)/', $src, $matches)) {
                    // Création d'un identifiant unique qui identifie une pièce jointe
                    $cid = uniqid();

                    $embeddedAttachments[] = [
                        "cid" => $cid,
                        "mimetype" => $matches[1],
                        "encoding" => $matches[2],
                        "content" => base64_decode($matches[3]),
                        "filename" => uniqid()
                    ];

                    // On pointe notre image vers une pièce jointe
                    $imgElement->setAttribute("src", "cid:$cid");
                }
            }

            if (! empty($embeddedAttachments)) {

                // On sauvegarde notre HTML par référence
                $html = $dom->saveHTML();
            }
        }

        return $embeddedAttachments;
    }
}
