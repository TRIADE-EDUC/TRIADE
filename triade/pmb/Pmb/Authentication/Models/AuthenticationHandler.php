<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationHandler.php,v 1.13 2023/08/29 15:31:35 dbellamy Exp $

namespace Pmb\Authentication\Models;

use Psr\Log\NullLogger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Formatter\LineFormatter;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

global $base_path;
global $ext_auth, $empty_pwd;
global $valid_user, $user;

class AuthenticationHandler
{

    protected static $configs = [
        'opac' => null,
        'gestion' => null,
    ];

    protected static $logger = null;

    protected $log = 0;

    protected $log_file = "";

    protected $log_level = Logger::DEBUG;

    protected $log_format = "[%datetime%] %level_name% > %message% \n";

    protected $conf_file = '';

    protected $id = '';

    // Environnement d'execution
    protected $env = 'opac';

    protected $authentication_params = [];

    protected $authentication_class = null;

    protected $authentication_result = [];

    protected $empr_logged = false;

    protected $ignore = false;

    protected $cms_build_detected = false;

    protected $empr_internal_login_allowed = true;

    protected $empr_internal_login_request_detected = false;

    protected $empr_ajax_login_request_detected = false;

    protected $empr_internal_logout_detected = false;

    protected $empr_internal_login_result = false;

    protected $empr_auto_login_detected = false;

    protected $empr_unique_login_detected = false;

    protected $empr_external_login_allowed = true;

    protected $empr_external_login_forced = false;

    protected $empr_external_submit_login_request_detected = false;

    protected $empr_external_login_return_detected = false;

    protected $empr_external_logout_return_detected = false;

    protected $empr_incoming_logout_detected = false;

    protected $empr_external_login_result = false;

    protected $pmb_id_empr = 0;

    protected $pmb_empr_login = '';

    protected $user_internal_login_allowed = true;

    protected $user_external_submit_login_request_detected = false;

    protected $user_external_redirect_login_request_detected = false;

    protected $user_external_login_return_detected = false;

    protected $user_external_logout_return_detected = false;

    protected $user_external_login_result = false;

    protected $pmb_userid = 0;

    protected $pmb_username = '';

    protected $login_mode = 'redirect';

    protected $current_http_request = [];

    protected $external_user = '';

    protected $external_attributes = [];

    protected $context_id = 0;

    protected $ignore_context = false;

    protected $ext_charset = 'utf-8';

    protected $config_id = 0;

    protected $token_id = '';

    /**
     * Constructeur
     *
     * @param array $params : [
     *      'env'       => opac|gestion
     *      'config'    => []
     *      'log'       => 0|1
     * ]
     * :
     * @return boolean
     * @throws \Exception
     */
    public function __construct($params)
    {
        global $base_path;

        if( !empty($params['env']) && in_array($params['env'], ['opac', 'gestion'] )) {
            $this->env = $params['env'];
        }
        if ( empty($params['config']) ) {
            throw new \ErrorException('no config');
        }
        if ( !empty($params['log']) ) {
            $this->log_file = $base_path . '/temp/authentication.log';
            $this->log = 1;
        }

        $this->setLogger();
        static::$logger->debug("____________________");

        $this->setEnv();

        $this->loadAuthenticationParams($params['config']);
        if (empty($this->authentication_params)) {
            static::$logger->error(__METHOD__ . " (" . __LINE__ . ") >> KO");
            throw new \Exception();
        }
        static::$logger->debug(print_r($this->authentication_params, true));
        $this->loadAuthenticationClass();
        if (is_null($this->authentication_class)) {
            static::$logger->error(__METHOD__ . " (" . __LINE__ . ") >> KO");
            throw new \Exception();
        }

        $this->ext_charset = $this->authentication_class->getCharset();

        if (! empty($this->authentication_params['params']['ignore_context']) && '1' == $this->authentication_params['params']['ignore_context']) {
            $this->ignore_context = true;
        }
        return true;
    }


    /**
     * Initialise l'environnement
     *
     * @return void
     */
    protected function setEnv()
    {
        global $base_path;

        switch ($this->env) {
            case 'gestion':
                if (! class_exists('encoding_normalize')) {
                    require_once $base_path . '/classes/encoding_normalize.class.php';
                }
                require_once $base_path . '/includes/mysql_functions.inc.php';
                break;

            case 'opac':
            default:
                if (! class_exists('encoding_normalize')) {
                    require_once $base_path . '/opac_css/classes/encoding_normalize.class.php';
                }
                if (! class_exists('shorturl_type_authenticate')) {
                    require_once $base_path . '/opac_css/classes/shorturl/shorturl_type_authenticate.class.php';
                }
                require_once $base_path . '/includes/mysql_functions.inc.php';
                break;
        }
    }

    /**
     * Chargement configurations
     *
     * @param string $env : opac|gestion
     * @return array
     */
    public static function getConfigs(string $env = 'opac')
    {
        if(!in_array($env, ['opac', 'gestion'])) {
            return [];
        }
        if( ! is_null(static::$configs[$env]) ) {
            return static::$configs[$env];
        }
        static::$configs[$env] = [];
        $tmp_configs = [];
        switch($env) {
            case 'gestion' :
                $tmp_configs = AuthenticationConfig::getConfigs(AuthenticationConfig::GESTION_MODEL, 'ranking');
                break;
            case 'opac' :
                $tmp_configs = AuthenticationConfig::getConfigs(AuthenticationConfig::OPAC_MODEL, 'ranking');
            default :
                break;
        }

        foreach($tmp_configs as $k_config => $config) {

            static::$configs[$env][$k_config]['id'] = $config['id'];
            static::$configs[$env][$k_config]['name'] = $config['name'];
            static::$configs[$env][$k_config]['class'] = $config['source_name']."Query";
            $settings = json_decode($config['settings'], JSON_OBJECT_AS_ARRAY);
            if(is_array($settings['params'])) {
                foreach($settings['params'] as $k_param => $param) {
                    static::$configs[$env][$k_config]['params'][$k_param] = $param;
                }
            }
            static::$configs[$env][$k_config]['params']['force_login_on_query_param'] = 'force_login='.$config['id'];
            static::$configs[$env][$k_config]['params']['attrs'] = (empty($settings['attrs'])) ? '' : $settings['attrs'] ;
            static::$configs[$env][$k_config]['params']['login_attr'] = (empty($settings['login_attr'])) ? '' : $settings['login_attr'] ;

            //Association attributs externes / internes
            static::$configs[$env][$k_config]['params']['association_process'] = [];
            //Lecteur
            if( ('opac' == $env) &&  is_array($settings['emprData']) ) {
                $i = 0;
                foreach($settings['emprData'] as $data) {
                    if( !empty($data['emprField']) ) {
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['attr'] = $data['attr'];
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['field'] = $data['emprField'] ;
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['transfoClass'] = empty($data['transfoClass']) ? '' : $data['transfoClass'];
                        static::$configs[$env][$k_config]['params']['field_from_attr'][$data['emprField']] = $data['attr'];
                        $i++;
                    }
                }
            }
            //Utilisateur
            if( ('gestion' == $env) &&  is_array($settings['userData']) ) {
                $i=0;
                foreach($settings['userData'] as $data) {
                    if( !empty($data['userField']) ) {
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['attr'] = $data['attr'];
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['field'] = $data['userField'] ;
                        static::$configs[$env][$k_config]['params']['association_process'][$i]['transfoClass'] = empty($data['transfoClass']) ? '' : $data['transfoClass'];
                        static::$configs[$env][$k_config]['params']['field_from_attr'][$data['userField']] = $data['attr'];
                        $i++;
                    }
                }
            }

            //Processus de recherche
            static::$configs[$env][$k_config]['params']['search_process'] = [];
            //Lecteur
            if( ('opac' == $env) && is_array($settings['emprSearchClass']) ) {
                foreach($settings['emprSearchClass'] as $search_class) {
                    static::$configs[$env][$k_config]['params']['search_process'][] = $search_class;
                }
            }
            //Utilisateur
            if( ('gestion' == $env) && is_array($settings['userSearchClass']) ) {
                foreach($settings['userSearchClass'] as $search_class) {
                    static::$configs[$env][$k_config]['params']['search_process'][] = $search_class;
                }
            }

            //Processus de creation
            static::$configs[$env][$k_config]['params']['create_process'] = [];
            if( ('opac' == $env) && !empty($settings['emprCreateClass']) ) {
                static::$configs[$env][$k_config]['params']['create_process'] = $settings['emprCreateClass'];
            }
            //Utilisateur
            if( ('gestion' == $env) && !empty($settings['userCreateClass']) ) {
                static::$configs[$env][$k_config]['params']['create_process'] = $settings['userCreateClass'];
            }

            //TODO Processus de mise a jour
            static::$configs[$env][$k_config]['params']['update_process'] = [];

            //Template
            $settings['template'] = str_replace('{{ link }}', './index.php?force_login='.$config['id'], $settings['template']);
            $settings['template'] = str_replace('{{ name }}', $config['name'], $settings['template']);
            static::$configs[$env][$k_config]['params']['template'] = $settings['template'];
        }

        $empr_internal_login_allowed = true;
        $user_internal_login_allowed = true;

        return static::$configs[$env];
    }


    /**
     * Definition Logger
     *
     * @return void
     */
    protected function setLogger()
    {
        if (! $this->log) {
            static::$logger = new NullLogger();
            return;
        }

        if (! is_writeable(dirname($this->log_file))) {
            static::$logger = new NullLogger();
            return;
        }

        $logger = new Logger('authentication');

        $processor = new PsrLogMessageProcessor();
        $formatter = new LineFormatter($this->log_format, null, true, true);

        $stream_handler = new StreamHandler($this->log_file, $this->log_level);

        $stream_handler->setFormatter($formatter);

        $logger->pushHandler($stream_handler);
        $logger->pushProcessor($processor);

        static::$logger = $logger;
    }

    /**
     * Chargement config authentification
     *
     * @return void
     */
    protected function loadAuthenticationParams($config)
    {
        static::$logger->debug(__METHOD__);

        if( !empty($config['id']) ) {
            $this->config_id = $config['id'];
        }

        if( !empty($config['params']) && is_array($config['params']) ) {
            foreach($config['params'] as $k_param => $v_param) {
                $this->authentication_params['params'][$k_param] = $v_param;
            }
        }

        if( !empty($config['class']) && is_string($config['class']) ) {
            $this->authentication_params['class'] = $config['class'];
        }

        return;
    }

    /**
     * Chargement classe authentification
     *
     * @return boolean
     */
    protected function loadAuthenticationClass()
    {
        static::$logger->debug(__METHOD__);

        $folder = explode("Query", $this->authentication_params['class'])[0];
        $class_name = 'Pmb\\Authentication\\Models\\Sources\\' . $folder . '\\' . $this->authentication_params['class'];

        if (! class_exists($class_name)) {
            static::$logger->error(__METHOD__ . " (" . __LINE__ . ") >> Classe $class_name inexistante");
            return false;
        }

        $class = new $class_name(static::$logger);

        if (! $class instanceof AuthenticationQueryInterface) {
            static::$logger->error(__METHOD__ . " (" . __LINE__ . ") >> Classe $class_name non valide");
            return false;
        }

        if (! empty($this->authentication_params['params'])) {
            $class->setParams($this->authentication_params['params']);
        }

        if ($class->getError()) {
            static::$logger->error(__METHOD__ . " (" . __LINE__ . ") >> Erreur d'initialisation");
            return false;
        }

        $this->authentication_class = $class;
        static::$logger->debug(__METHOD__ . " >> Classe  = $class_name");
        return true;
    }

    /**
     * Lance l'authentification
     *
     * @return array : [
     *         'user' =>
     *         'attrs => []
     *         ]
     */
    public function run()
    {
        static::$logger->debug(__METHOD__);

        $this->getCurrentHttpRequest();

        switch ($this->env) {

            case 'gestion':

                $this->runAsUser();
                break;

            case 'opac':
            default:

                $this->runAsEmpr();
                break;
        }
    }

    /**
     * Lance l'authentification lecteur
     */
    protected function runAsEmpr()
    {
        static::$logger->debug(__METHOD__);

        // Lecteur deja connecte ?
        $this->isEmprLogged();

        if ($this->empr_logged) {
            // Detection demande de deconnexion depuis serveur externe
            $this->detectEmprIncomingLogout();
            if ($this->empr_incoming_logout_detected) {
                $this->runEmprIncomingLogout();
                return;
            }
        }

        // Detection retour de deconnexion externe
        $this->detectEmprExternalLogoutReturn();
        if($this->empr_external_logout_return_detected) {
            $this->runEmprExternalLogoutReturn();
            return;
        }

        // Detection deconnexion interne
        $this->detectEmprInternalLogout();
        if ($this->empr_internal_logout_detected) {
            $this->runEmprExternalLogout();
            return;
        }

        // Detection requete ajax
        $this->detectAjaxRequest();
        if ($this->ignore) {
            return;
        }

        // Detection requete getimage
        $this->detectGetImageRequest();
        if ($this->ignore) {
            return;
        }

        // Detection autres requetes a ignorer
        $this->detectOtherRequestsToIgnore();
        if ($this->ignore) {
            return;
        }

        // Detection authentification automatique
        $this->detectEmprAutoLogin();
        if ($this->empr_auto_login_detected) {
            $this->runEmprInternalLogin();
            return;
        }

        // Detection authentification unique
        $this->detectEmprUniqueLogin();
        if ($this->empr_unique_login_detected) {
            $this->runEmprInternalLogin();
            return;
        }

        // Detection portail en construction
        $this->detectCmsBuild();

        // Authentification externe autorisee en construction de portail ?
        if ($this->cms_build_detected) {
            $this->isEmprExternalLoginAllowedOnCmsBuild();
        }

        // Traitement authentification externe (partie 1)
        if ($this->empr_external_login_allowed) {

            // Detection retour d'authentification externe (redirection)
            $this->detectEmprExternalLoginReturn();

            // Traitement retour d'authentification externe (redirection)
            if ($this->empr_external_login_return_detected) {
                $this->runEmprExternalLoginReturn();
                return;
            }

            // Authentification externe forcee ?
            $this->isEmprExternalLoginForced();
            if ($this->empr_external_login_forced) {

                // Si oui on desactive l'authentification interne
                $this->empr_internal_login_allowed = false;
            }
        }
        // Traitement authentification interne
        if ($this->empr_internal_login_allowed) {

            $this->detectEmprInternalLoginRequest();

            if ($this->empr_internal_login_request_detected) {
                $this->runEmprInternalLogin();
            }

            if (true === $this->empr_internal_login_result) {
                return;
            }
        }

        // Traitement authentification externe (partie 2)
        if ($this->empr_external_login_allowed) {

            $this->saveContext();

            // Recuperation du mode d'authentification externe
            $this->getLoginMode();

            if ('redirect' == $this->login_mode && $this->empr_external_login_forced) {
                $this->runEmprExternalLogin();
                return;
            }

            if ('submit' == $this->login_mode) {

                // Detection authentification externe en mode submit
                $this->detectEmprExternalSubmitLoginRequest();
                if ($this->empr_external_submit_login_request_detected) {
                    $this->runEmprExternalLogin();
                    return;
                }
            }
        }

        $this->saveContext();
    }

    /**
     * Lance l'authentification utilisateur
     */
    protected function runAsUser()
    {
        static::$logger->debug(__METHOD__);

        // Detection retour d'authentification externe (redirection)
        $this->detectUserExternalLoginReturn();

        // Traitement retour d'authentification externe (redirection)
        if ($this->user_external_login_return_detected) {
            $this->runUserExternalLoginReturn();
            return;
        }

        // Recuperation du mode d'authentification externe
        $this->getLoginMode();

        switch ($this->login_mode) {

            // Mode Redirect
            case ('redirect'):

                // Detection authentification externe en mode redirect
                $this->detectUserExternalRedirectLoginRequest();

                // Traitement authentification externe en mode redirect
                if ($this->user_external_redirect_login_request_detected) {

                    if (method_exists($this->authentication_class, 'runExternalLoginRedirect')) {
                        $this->authentication_class->runExternalLoginRedirect($this);
                    }
                }
                break;

            // Mode submit
            case ('submit'):

                // Detection authentification externe en mode submit
                $this->detectUserExternalSubmitLoginRequest();

                // Traitement authentification externe en mode submit
                if ($this->user_external_submit_login_request_detected) {
                    if (method_exists($this->authentication_class, 'runExternalLoginSubmit')) {
                        $this->user_external_login_result = $this->authentication_class->runExternalLoginSubmit($this, $_POST['user'], $_POST['password']);
                        $this->runUserExternalLoginReturn(true);
                    }
                }
                break;

            default:
                break;
        }
    }

    /**
     * Verifie si un lecteur est connecte
     *
     * @return void
     */
    protected function isEmprLogged()
    {
        static::$logger->debug(__METHOD__);

        $this->empr_logged = (! empty($_SESSION["user_code"]) ? true : false);
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_logged) ? "O" : "N"));
    }

    /**
     * Detecte une demande de deconnexion lecteur depuis serveur externe
     *
     * @return void
     */
    protected function detectEmprIncomingLogout()
    {
        static::$logger->debug(__METHOD__);
        // TODO
        // $this->empr_incoming_logout_detected = true;
        // $this->ignore_context = true;
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_incoming_logout_detected) ? "O" : "N"));
    }

    /**
     * Detecte une demande de deconnexion lecteur depuis PMB
     *
     * @return void
     */
    protected function detectEmprInternalLogout()
    {
        static::$logger->debug(__METHOD__);

        if (stripos($_SERVER['REQUEST_URI'], "index.php?logout=1") !== false) {
            $this->ignore_context = true;
            $this->empr_internal_logout_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_internal_logout_detected) ? "O" : "N"));
    }


    /**
     * Detection requete ajax
     *
     * @return void
     */
    protected function detectAjaxRequest()
    {
        // on desactive l'authentification sur les requetes ajax sauf authentification documents numeriques
        if ((false !== stripos($_SERVER['REQUEST_URI'], 'ajax.php')) && (false === stripos($_SERVER['REQUEST_URI'], 'ajax.php?module=ajax&categ=auth&action=check_auth'))) {
            $this->ignore_context = true;
            $this->ignore = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->ignore) ? "O" : "N"));
    }

    /**
     * Detection requete getimage
     *
     * @return void
     */
    protected function detectGetImageRequest()
    {
        static::$logger->debug(__METHOD__);

        // on desactive l'authentification sur les requetes getimage
        if (false !== stripos($_SERVER['REQUEST_URI'], 'getimage.php')) {
            $this->ignore_context = true;
            $this->ignore = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->ignore) ? "O" : "N"));
    }

    /**
     * Detection autres requetes a ignorer
     *
     * @return void
     */
    protected function detectOtherRequestsToIgnore()
    {
        static::$logger->debug(__METHOD__);

        // term_search.php?
        if (false !== stripos($_SERVER['REQUEST_URI'], 'term_search.php')) {
            $this->ignore_context = true;
            $this->ignore = true;
        }
        // term_show.php?
        if (false !== stripos($_SERVER['REQUEST_URI'], 'term_show.php')) {
            $this->ignore_context = true;
            $this->ignore = true;
        }
        // cart_info.php?
        if (false !== stripos($_SERVER['REQUEST_URI'], 'cart_info.php')) {
            $this->ignore_context = true;
            $this->ignore = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->ignore) ? "O" : "N"));
    }

    /**
     * Detection demande authentification automatique
     *
     * @return void
     */
    protected function detectEmprAutoLogin()
    {
        static::$logger->debug(__METHOD__);

        global $code, $emprlogin, $date_conex;

        if (! empty($code) && ! empty($emprlogin) && ! empty($date_conex)) {
            $this->ignore_context = true;
            $this->empr_auto_login_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_auto_login_detected) ? "O" : "N"));
    }

    /**
     * Detection demande authentification unique
     *
     * @return void
     */
    protected function detectEmprUniqueLogin()
    {
        static::$logger->debug(__METHOD__);

        global $password_key, $emprlogin;

        if (! empty($password_key) && ! empty($emprlogin)) {
            $this->ignore_context = true;
            $this->empr_unique_login_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_unique_login_detected) ? "O" : "N"));
    }

    /**
     * Detection portail en construction
     *
     * @return void
     */
    protected function detectCmsBuild()
    {
        static::$logger->debug(__METHOD__);

        if (! empty($_SESSION['cms_build_activate']) || ! empty($_GET['cms_build_activate'])) {
            $this->cms_build_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->cms_build_detected) ? "O" : "N"));
    }

    /**
     * Verifie si on autorise l'authentification externe en construction de portail
     *
     * @return void
     */
    protected function isEmprExternalLoginAllowedOnCmsBuild()
    {
        static::$logger->debug(__METHOD__);

        if (isset($this->authentication_params['params']['allow_cms_build'])
            && ("0" === $this->authentication_params['params']['allow_cms_build'])) {
            $this->empr_external_login_allowed = false;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_external_login_allowed) ? "O" : "N"));
    }

    /**
     * Detection demande authentification interne lecteur
     *
     * @return void
     */
    protected function detectEmprInternalLoginRequest()
    {
        static::$logger->debug(__METHOD__);

        global $login, $password;
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && ! empty($login) && ! empty($password)) {
            $this->ignore_context = true;
            $this->empr_internal_login_request_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_internal_login_request_detected) ? "O" : "N"));

        if ($this->empr_internal_login_request_detected && (false !== stripos($_SERVER['REQUEST_URI'], 'ajax.php?module=ajax&categ=auth&action=check_auth'))) {
            $this->empr_ajax_login_request_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> ajax = " . (($this->empr_ajax_login_request_detected) ? "O" : "N"));
    }

    /**
     * Detection demande authentification externe lecteur (mode submit)
     *
     * @return void
     */
    protected function detectEmprExternalSubmitLoginRequest()
    {
        static::$logger->debug(__METHOD__);

        global $login, $password;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ! empty($login) && ! empty($password)) {
            $this->ignore_context = true;
            $this->empr_external_submit_login_request_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_external_submit_login_request_detected) ? "O" : "N"));

        if ($this->empr_external_submit_login_request_detected && (false !== stripos($_SERVER['REQUEST_URI'], 'ajax.php?module=ajax&categ=auth&action=check_auth'))) {
            $this->empr_ajax_login_request_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> ajax = " . (($this->empr_ajax_login_request_detected) ? "O" : "N"));
    }

    /**
     * Traitement authentification externe lecteur
     *
     * @return void
     */
    protected function runEmprExternalLogin()
    {
        static::$logger->debug(__METHOD__);

        switch ($this->login_mode) {

            // Mode Redirect
            case ('redirect'):
                if (method_exists($this->authentication_class, 'runExternalLoginRedirect')) {
                    $this->setSessionKey('ext_auth_config_id', $this->config_id);
                    $this->authentication_class->runExternalLoginRedirect($this);
                }
                break;

            // Mode submit
            case ('submit'):

                if (method_exists($this->authentication_class, 'runExternalLoginSubmit')) {
                    $this->empr_external_login_result = $this->authentication_class->runExternalLoginSubmit($this, $_POST['login'], $_POST['password']);
                    $this->runEmprExternalLoginReturn(true);
                }
                break;

            default:
                break;
        }
    }

    /**
     * Traitement deconnexion externe lecteur
     */
    public function runEmprExternalLogout()
    {
        static::$logger->debug(__METHOD__);

        // Reecriture contexte en session
        $this->setSessionKey('ext_auth_context_id', $this->context_id);

        //Reecriture config d'authentification en session
        $this->setSessionKey('ext_auth_config_id', $this->config_id);

        if (method_exists($this->authentication_class, 'runExternalLogout')) {
            $this->authentication_class->runExternalLogout($this);
        }
    }

    /**
     * Traitement authentification interne lecteur
     *
     * @return void
     */
    protected function runEmprInternalLogin()
    {
        static::$logger->debug(__METHOD__);

        // Recuperation contexte
        $this->context_id = $this->getContextId();

        if (function_exists("\connexion_empr")) {
            $this->empr_internal_login_result = \connexion_empr();
        }

        // Reecriture contexte en session
        $this->setSessionKey('ext_auth_context_id', $this->context_id);

        //Reecriture config d'authentification en session
        $this->setSessionKey('ext_auth_config_id', $this->config_id);

        if (! $this->empr_internal_login_result) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            return;
        }
        static::$logger->debug(__METHOD__ . " >> OK ");

        // Si appel ajax et mode submit, pas de redirection, on supprime le contexte
        if ($this->empr_ajax_login_request_detected) {
            $this->deleteContext();
            $this->unsetSessionKey('ext_auth_context_id');
            $this->unsetSessionKey('ext_auth_config_id');
            return;
        }

        // Restauration contexte
        $this->restoreContext();
    }

    /**
     * Traitement requete de deconnexion lecteur depuis serveur externe
     */
    protected function runEmprIncomingLogout()
    {
        static::$logger->debug(__METHOD__);
        // TODO
    }

    /**
     * Verifie si l'authentification externe lecteur est forcee
     *
     * @return void
     */
    protected function isEmprExternalLoginForced()
    {
        // toujours forcee
        if (! empty($this->authentication_params['params']['force_login']) && ("1" === $this->authentication_params['params']['force_login'])) {
            $this->ignore_context = false;
            $this->empr_external_login_forced = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }

        // forcee sur parametre dans l'URL
        if (! empty($this->authentication_params['params']['force_login_on_query_param']) && (stripos($_SERVER['QUERY_STRING'], $this->authentication_params['params']['force_login_on_query_param']) !== false)) {
            $this->ignore_context = true;
            $this->empr_external_login_forced = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }

        static::$logger->debug(__METHOD__ . " >> N");
    }

    /**
     * Recuperation du mode d'authentification
     *
     * @return string : redirect|submit
     */
    protected function getLoginMode()
    {
        static::$logger->debug(__METHOD__);

        $login_mode = $this->login_mode;

        if (! empty($this->authentication_params['params']['login_mode'])) {
            $login_mode = $this->authentication_params['params']['login_mode'];
        }
        $login_modes_available = $this->authentication_class->getLoginModes();
        if (in_array($login_mode, $login_modes_available)) {
            $this->login_mode = $login_mode;
        } else {
            $this->login_mode = $login_modes_available[0];
        }
        static::$logger->debug(__METHOD__ . " >> {$this->login_mode}");
    }


    /**
     * Detection retour d'authentification lecteur externe (redirection)
     *
     * @return void
     */
    protected function detectEmprExternalLoginReturn()
    {
        static::$logger->debug(__METHOD__);

        // On verifie que l'URI est bien celle definie pour le retour
        $this->empr_external_login_return_detected = $this->detectExternalLoginRedirectURI();
        if (! $this->empr_external_login_return_detected) {
            static::$logger->debug(__METHOD__ . "N");
            return;
        }
        // Selon la classe d'authentification, on verifie que les parametres requis sont presents
        if (method_exists($this->authentication_class, 'detectExternalLoginReturn')) {
            $this->empr_external_login_return_detected = $this->authentication_class->detectExternalLoginReturn($this);
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_external_login_return_detected) ? "O" : "N"));
    }


    /**
     * Detection retour de deconnexion lecteur externe
     *
     * @return void
     */
    protected function detectEmprExternalLogoutReturn()
    {
        static::$logger->debug(__METHOD__);

        // On verifie que l'URI est bien celle definie pour le retour
        $this->empr_external_logout_return_detected = $this->detectExternalLogoutRedirectURI();
        if (! $this->empr_external_logout_return_detected) {
            static::$logger->debug(__METHOD__ . " >> N");
            return;
        }
        // Selon la classe d'authentification, on verifie que les parametres requis sont presents
        if (method_exists($this->authentication_class, 'detectExternalLogoutReturn')) {
            $this->empr_external_logout_return_detected = $this->authentication_class->detectExternalLogoutReturn($this);
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->empr_external_logout_return_detected) ? "O" : "N"));
    }


    /**
     * Traitement retour authentification lecteur
     *
     * @param bool $from_runEmprExternalLogin
     *            : true si appel depuis methode runEmprExternalLogin
     * @return void
     */
    protected function runEmprExternalLoginReturn(bool $from_runEmprExternalLogin = false)
    {
        static::$logger->debug(__METHOD__);

        if (! $from_runEmprExternalLogin && method_exists($this->authentication_class, 'runExternalLoginReturn')) {
            $this->empr_external_login_result = $this->authentication_class->runExternalLoginReturn($this);
        }
        if (! $this->empr_external_login_result) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            return;
        }

        // Recuperation des identifiants et attributs
        $this->external_user = \encoding_normalize::charset_normalize($this->authentication_class->getUser(), $this->ext_charset);
        $this->external_attributes = \encoding_normalize::charset_normalize($this->authentication_class->getAttributes(), $this->ext_charset);

        $this->setSessionKey('ext_auth_attrs', $this->external_attributes);

        // Recherche lecteur
        $this->searchEmpr();

        // On s'occupe de la transformation
        $this->transformation_args();
        // Si le lecteur a ete trouve
        if ($this->pmb_id_empr) {
            $this->updateEmpr();
        } else {
            $this->createEmpr();
        }

        if (! $this->pmb_id_empr) {
            static::$logger->debug(__METHOD__ . " (" . __LINE__ . ") >> KO");
            return;
        }
        // Recuperation contexte
        $this->context_id = $this->getContextId();

        // Association lecteur / identifiant externe et traitement authentification interne
        global $ext_auth, $empty_pwd;
        $ext_auth = true;
        $empty_pwd = true;
        $_POST['login'] = $this->pmb_empr_login;

        if (function_exists("\connexion_empr")) {
            $this->empr_internal_login_result = \connexion_empr();
        }

        // Reecriture contexte en session
        $this->setSessionKey('ext_auth_context_id', $this->context_id);
        // Reecriture attributs en session
        $this->setSessionKey('ext_auth_attrs', $this->external_attributes);

        $ext_auth = false;
        $empty_pwd = false;

        if (! $this->empr_internal_login_result) {
            static::$logger->debug(__METHOD__ . " (" . __LINE__ . ") >> KO");
            return;
        }

        static::$logger->debug(__METHOD__ . " >> OK ");

        // Si appel ajax et mode submit, pas de redirection, on supprime le contexte
        if ($this->empr_ajax_login_request_detected) {
            $this->deleteContext();
            $this->unsetSessionKey('ext_auth_context_id');
            return;
        }

        // Restauration contexte
        $this->restoreContext();
    }


    /**
     * Traitement retour deconnexion lecteur
     *
     * @return void
     */
    protected function runEmprExternalLogoutReturn()
    {
        static::$logger->debug(__METHOD__);

        // Recuperation id contexte
        $this->context_id = $this->getContextId();
        // Restauration contexte
        $this->restoreContext();
    }


    /**
     * Recherche lecteur a partir des infos retournees lors de l'authentification
     *
     * @return void
     */
    protected function searchEmpr()
    {
        static::$logger->debug(__METHOD__);

        if ( empty($this->authentication_params['params']['search_process']) ) {
            static::$logger->error(__METHOD__ . " >> No search process defined");
            return;
        }
        foreach ($this->authentication_params['params']['search_process'] as $item) {
            $item_class =  "\\Pmb\\Authentication\\Helpers\\Empr\\" . $item;
            if (class_exists($item_class)) {
                //Instance
                $obj = new $item_class(static::$logger);
                //Arguments
                $req_args = [];
                $args = [];
                $req_args = $obj->getArgs();
                foreach ($req_args as $arg_name) {
                    $external_attribute_name = empty($this->authentication_params['params']['field_from_attr'][$arg_name]) ? '' : ($this->authentication_params['params']['field_from_attr'][$arg_name]);
                    $args[$arg_name] = empty($this->external_attributes[$external_attribute_name]) ? '' : ($this->external_attributes[$external_attribute_name]);
                }
                //Et hop
                $this->pmb_id_empr = $obj->search($args);
                if ($this->pmb_id_empr) {
                    $this->pmb_empr_login = (! empty($obj->search_result['empr_login']) ? $obj->search_result['empr_login'] : '');
                    static::$logger->debug(__METHOD__ . " >> id_empr = " . $this->pmb_id_empr . ", empr_login = " . $this->pmb_empr_login);
                    return;
                }
            }
        }

        static::$logger->debug(__METHOD__ . " >> id_empr = 0");
    }

    /**
     * Creation emprunteur a partir des infos retournees lors de l'authentification
     *
     * @return void
     */
    protected function createEmpr()
    {
        static::$logger->debug(__METHOD__);

        if (empty($this->authentication_params['params']['create_process'])) {
            static::$logger->error(__METHOD__ . " >> No create process defined");
            return;
        }
        $item = $this->authentication_params['params']['create_process'];
        $item_class =  "\\Pmb\\Authentication\\Helpers\\Empr\\" . $item;
        if (class_exists($item_class)) {
            //Instance
            $obj = new $item_class(static::$logger);
            //Arguments
            $req_args = [];
            $args = [];
            $req_args = $obj->getArgs();
            foreach ($req_args as $arg_name) {
                $external_attribute_name = empty($this->authentication_params['params']['field_from_attr'][$arg_name]) ? '' : ($this->authentication_params['params']['field_from_attr'][$arg_name]);
                $args[$arg_name] = empty($this->external_attributes[$external_attribute_name]) ? '' : ($this->external_attributes[$external_attribute_name]);
            }
            //Et hop
            $this->pmb_id_empr = $obj->onAuthenticationCreate($this, $args);
            if ($this->pmb_id_empr) {
                $this->pmb_empr_login = $obj->empr_login;
                static::$logger->debug(__METHOD__ . " >> pmb_empr_login = " . $this->pmb_empr_login);
                static::$logger->debug(__METHOD__ . " >> id = " . $this->pmb_id_empr . ", login = " . $this->pmb_empr_login);
                return;
            }
        }
        static::$logger->debug(__METHOD__ . " >> KO");
    }

//TODO a reprendre (cf. createEmpr)
    /**
     * Mise a jour emprunteur
     *
     * @return void
     */
    protected function updateEmpr()
    {
        if (empty($this->authentication_params['params']['update_process'])) {
            static::$logger->error(__METHOD__ . " >> No update process defined");
            return;
        }

        $item = $this->authentication_params['params']['update_process'];
        $item = str_replace("::", ",", $item);
        $item = str_replace(" ", "", $item);

        $item_tokens = explode(",", $item);
        $item_class = array_shift($item_tokens);
        $item_class = "\\Synchro\\" . $item_class;
        $item_method = array_shift($item_tokens);
        $item_args = [
            'id_empr' => $this->pmb_id_empr,
            'user' => $this->external_user,
            'attributes' => $this->external_attributes
        ];

        if (class_exists($item_class) && method_exists($item_class, $item_method) && ! empty($item_args)) {
            $obj = new $item_class(static::$logger);
            $this->pmb_id_empr = call_user_func_array([
                $obj,
                $item_method
            ], $item_args);
            if ($this->pmb_id_empr) {
                $this->pmb_empr_login = $obj->empr_login;
                static::$logger->debug(__METHOD__ . " >> id = " . $this->pmb_id_empr . ", login = " . $this->pmb_empr_login);
                return;
            }
        }
        static::$logger->debug(__METHOD__ . " >> KO");
    }

    // +-------------------------------------------------+
    // Methodes utilisateur

    /**
     * Detection retour d'authentification utilisateur externe (redirection)
     *
     * @return void
     */
    protected function detectUserExternalLoginReturn()
    {
        static::$logger->debug(__METHOD__);

        // On verifie que l'URI est bien celle definie pour le retour
        $this->user_external_login_return_detected = $this->detectExternalLoginRedirectURI();
        if (! $this->user_external_login_return_detected) {
            static::$logger->debug(__METHOD__ . " >> " . (($this->user_external_login_return_detected) ? "O" : "N"));
            return;
        }
        // Selon la classe d'authentification, on verifie que les parametres requis sont presents
        if (method_exists($this->authentication_class, 'detectExternalLoginReturn')) {
            $this->user_external_login_return_detected = $this->authentication_class->detectExternalLoginReturn($this);
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->user_external_login_return_detected) ? "O" : "N"));
    }

    /**
     * Recherche utilisateur a partir des infos retournees lors de l'authentification
     *
     * @return void
     */
    protected function searchUser()
    {
        static::$logger->debug(__METHOD__);

        if ( empty($this->authentication_params['params']['search_process']) ) {
            static::$logger->error(__METHOD__ . " >> No search process defined");
            return;
        }
        foreach ($this->authentication_params['params']['search_process'] as $item) {
            $item_class =  "\\Pmb\\Authentication\\Helpers\\Empr\\" . $item;
            if (class_exists($item_class)) {
                //Instance
                $obj = new $item_class(static::$logger);
                //Arguments
                $req_args = [];
                $args = [];
                $req_args = $obj->getArgs();
                foreach ($req_args as $arg_name) {
                    $external_attribute_name = empty($this->authentication_params['params']['field_from_attr'][$arg_name]) ? '' : ($this->authentication_params['params']['field_from_attr'][$arg_name]);
                    $args[$arg_name] = empty($this->external_attributes[$external_attribute_name]) ? '' : ($this->external_attributes[$external_attribute_name]);
                }
                //Et hop
                $this->pmb_userid = $obj->search($args);
                if ($this->pmb_userid) {
                    $this->pmb_username = (! empty($obj->search_result['username']) ? $obj->search_result['username'] : '');
                    static::$logger->debug(__METHOD__ . " >> userid = " . $this->pmb_userid . ", username = " . $this->pmb_username);
                    return;
                }
            }
        }

        static::$logger->debug(__METHOD__ . " >> userid = 0");
    }

    /**
     * Traitement retour authentification utilisateur
     *
     * @param bool $from_runAsUser
     *            : true si appel depuis methode runAsUser
     *
     * @return void
     */
    protected function runUserExternalLoginReturn(bool $from_runAsUser = false)
    {
        static::$logger->debug(__METHOD__);

        if (! $from_runAsUser && method_exists($this->authentication_class, 'runExternalLoginReturn')) {
            $this->user_external_login_result = $this->authentication_class->runExternalLoginReturn($this);
        }

        if (! $this->user_external_login_result) {
            static::$logger->debug(__METHOD__ . " (" . __LINE__ . ") >> KO ");
            return;
        }

        // Recuperation des identifiants et attributs
        $this->external_user = \encoding_normalize::charset_normalize($this->authentication_class->getUser(), $this->ext_charset);
        $this->external_attributes = \encoding_normalize::charset_normalize($this->authentication_class->getAttributes(), $this->ext_charset);

        // On s'occupe de la transformation
        $this->transformation_args();

        // Recherche utilisateur
        $this->searchUser();

        // Si l'utilisateur a ete trouve
        if ($this->pmb_userid) {
            $this->updateUser();
        } else {
            $this->createUser();
        }

        if (! $this->pmb_userid) {
            static::$logger->debug(__METHOD__ . " (" . __LINE__ . ") >> KO");
            return;
        }

        // Association utilisateur / identifiant externe
        global $valid_user, $user;
        $valid_user = 1;
        $user = $this->pmb_username;

        static::$logger->debug(__METHOD__ . " >> OK ");
    }

    /**
     * Detection demande authentification externe utilisateur (mode submit)
     *
     * @return void
     */
    protected function detectUserExternalSubmitLoginRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && ! empty($_POST['user']) && ! empty($_POST['password'])
            && (false !== stripos($_SERVER['REQUEST_URI'], 'main.php'))) {
            $this->user_external_submit_login_request_detected = true;
        }
        static::$logger->debug(__METHOD__ . " >> " . (($this->user_external_submit_login_request_detected) ? "O" : "N"));
    }

    /**
     * Detection demande authentification externe utilisateur (mode redirect)
     *
     * @return void
     */
    protected function detectUserExternalRedirectLoginRequest()
    {
        // toujours forcee
        if (! empty($this->authentication_params['params']['force_login'])
            && ("1" === $this->authentication_params['params']['force_login'])) {
            $this->user_external_redirect_login_request_detected = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }

        // Sur appel uri
        if (! empty($this->authentication_params['params']['force_login_on_uri'])
            && ($_SERVER['REQUEST_URI'] == $this->authentication_params['params']['force_login_on_uri'])) {
            $this->user_external_redirect_login_request_detected = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }

        // Sur parametre dans l'URL
        if (! empty($this->authentication_params['params']['force_login_on_query_param'])
            && (stripos($_SERVER['QUERY_STRING'], $this->authentication_params['params']['force_login_on_query_param']) !== false)) {
            $this->user_external_redirect_login_request_detected = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }

        // Sur appel POST main.php sans identifiant ni mot de passe
        if (! empty($this->authentication_params['params']['force_login_on_empty_connexion_post'])
            && ("1" === $this->authentication_params['params']['force_login_on_empty_connexion_post'])
            && isset($_POST['user']) && ("" === $_POST['user'])
            && isset($_POST['password']) && ("" === $_POST['password'])
            && (false !== stripos($_SERVER['REQUEST_URI'], 'main.php'))) {
            $this->user_external_redirect_login_request_detected = true;
            static::$logger->debug(__METHOD__ . " >> O");
            return;
        }
        static::$logger->debug(__METHOD__ . " >> N");
    }

    /**
     * Creation utilisateur
     *
     * @return void
     */
    protected function createUser()
    {
        static::$logger->debug(__METHOD__);
        // TODO
    }

    /**
     * Mise a jour utilisateur
     *
     * @return void
     */
    protected function updateUser()
    {
        static::$logger->debug(__METHOD__);
        if (empty($this->authentication_params['params']['update_process'])) {
            static::$logger->error(__METHOD__ . " >> No update process defined");
            return;
        }
        $item = $this->authentication_params['params']['update_process'];
        $item = str_replace("::", ",", $item);
        $item = str_replace(" ", "", $item);

        $item_tokens = explode(",", $item);
        $item_class = array_shift($item_tokens);
        $item_class = "\\Synchro\\" . $item_class;
        $item_method = array_shift($item_tokens);

        $item_args = [
            'userid' => $this->pmb_userid,
            'user' => $this->external_user,
            'attributes' => $this->external_attributes
        ];

        if (class_exists($item_class) && method_exists($item_class, $item_method) && ! empty($item_args)) {
            $obj = new $item_class(static::$logger);
            $this->pmb_userid = call_user_func_array([
                $obj,
                $item_method
            ], $item_args);
            if ($this->pmb_userid) {
                $this->pmb_username = $obj->user_login;
                static::$logger->debug(__METHOD__ . " >> id = " . $this->pmb_userid . ", login = " . $this->pmb_username);
                return;
            }
        }
        static::$logger->debug(__METHOD__ . " >> KO");
    }

    // +-------------------------------------------------+
    // XXX Methodes communes

    /**
     * Detection de l'URI de retour de connexion
     *
     * @return boolean
     */
    protected function detectExternalLoginRedirectURI()
    {
        if (empty($this->authentication_params['params']['redirect_uri'])) {
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        $redirect_uri = $this->authentication_params['params']['redirect_uri'];
        $current_uri = $this->current_http_request['full_request_uri'];

        if (0 !== stripos($current_uri, $redirect_uri)) {
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        $ext_auth_config_id = $this->getSessionKey('ext_auth_config_id');
        if($this->config_id !== $ext_auth_config_id) {
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        static::$logger->debug(__METHOD__ . " >> O");
        return true;
    }

    /**
     * Detection de l'URI de retour de deconnexion
     *
     * @return boolean
     */
    protected function detectExternalLogoutRedirectURI()
    {
        if (empty($this->authentication_params['params']['logout_redirect_uri'])) {
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        $logout_redirect_uri = $this->authentication_params['params']['logout_redirect_uri'];
        $current_uri = $this->current_http_request['full_request_uri'];

        if (0 !== stripos($current_uri, $logout_redirect_uri)) {
            static::$logger->debug(__METHOD__ . " >> current_uri != logout_redirect_uri ");
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        $ext_auth_config_id = $this->getSessionKey('ext_auth_config_id');
        if($this->config_id !== $ext_auth_config_id) {
            static::$logger->debug(__METHOD__ . " >> config_id = ". $this->config_id);
            static::$logger->debug(__METHOD__ . " >> ext_auth_config_id = ". $ext_auth_config_id);
            static::$logger->debug(__METHOD__ . " >> config_id != ext_auth_config_id ");
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        static::$logger->debug(__METHOD__ . " >> O");
        return true;
    }

    /**
     * Recuperation des elements de la requete http (uri, method, query_string, params)
     *
     * @return void
     */
    protected function getCurrentHttpRequest()
    {
        if (! empty($this->current_http_request)) {
            static::$logger->debug(__METHOD__ . " >> " . print_r($this->current_http_request));
            return;
        }
        $secure = false;
        switch (true) {

            case (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])):
                $secure = (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
                break;

            case (isset($_SERVER['HTTPS'])):
                $secure = (strtolower($_SERVER['HTTPS']) === 'on') || ($_SERVER['HTTPS'] == 1);
                break;

            case (isset($_SERVER['HTTP_SSL_HTTPS'])):
                $secure = (strtolower($_SERVER['HTTP_SSL_HTTPS']) === 'on') || ($_SERVER['HTTP_SSL_HTTPS'] == 1);
                break;
        }
        $scheme = $secure ? 'https://' : 'http://';
        $port = '';
        switch (true) {
            case ($secure && isset($_SERVER['HTTP_X_FORWARDED_PORT']) && ($_SERVER['HTTP_X_FORWARDED_PORT'] != "443")):
                $port = $_SERVER['HTTP_X_FORWARDED_PORT'];
                break;
            case ($secure && isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != "443")):
                $port = $_SERVER['SERVER_PORT'];
                break;
            case (! $secure && isset($_SERVER['HTTP_X_FORWARDED_PORT']) && ($_SERVER['HTTP_X_FORWARDED_PORT'] != "80")):
                $port = $_SERVER['HTTP_X_FORWARDED_PORT'];
                break;
            case (! $secure && isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != "80")):
                $port = $_SERVER['SERVER_PORT'];
                break;
        }

        $full_script_uri = $scheme . $_SERVER['HTTP_HOST'] . (($port) ? ":$port" : "") . $_SERVER['SCRIPT_NAME'];
        $this->current_http_request['full_script_uri'] = $full_script_uri;
        $this->current_http_request['full_request_uri'] = $full_script_uri . '?' . $_SERVER['QUERY_STRING'];
        $this->current_http_request['script_name'] = $_SERVER['SCRIPT_NAME'];
        $this->current_http_request['request_uri'] = $_SERVER['REQUEST_URI'];
        $this->current_http_request['query_string'] = $_SERVER['QUERY_STRING'];
        $this->current_http_request['method'] = $_SERVER['REQUEST_METHOD'];
        $this->current_http_request['params'] = $_REQUEST;
        static::$logger->debug(__METHOD__ . " >> " . print_r($this->current_http_request, true));
    }

    /**
     * Enregistrement contexte
     *
     * @return void
     */
    protected function saveContext()
    {
        if ($this->ignore_context) {
            static::$logger->debug(__METHOD__ . " >> context ignored");
            return;
        }
        $context_id = 0;
        $previous_context_id = $this->getContextId();
        $context = [
            'target' => 'opac',
            'duration' => 300,
        ];

        if ($previous_context_id) {
            $su = \shorturl_type_authenticate::get_by_hash($previous_context_id);
            if ($su && $su instanceof \shorturl_type_authenticate) {
                $context_id = $su->generate_callback($context, true);
            }
        }
        if (! $context_id) {
            $su = new \shorturl_type_authenticate();
            $context_id = $su->generate_callback($context);
        }
        $this->context_id = $context_id;

        $this->setSessionKey('ext_auth_context_id', $this->context_id);
        static::$logger->debug(__METHOD__ . " >> context_id = " . $this->context_id);
        $this->setSessionKey('ext_auth_config_id', $this->config_id);
        static::$logger->debug(__METHOD__ . " >> config_id = " . $this->config_id);
    }

    /**
     * Definition d'un contexte a partir d'une URL
     */
    public function defineContext($url = '')
    {
        $context_id = 0;
        $previous_context_id = $this->getContextId();
        $context = [
            'target' => 'opac',
            'duration' => 300,
            'url' => $url,
        ];

        if ($previous_context_id) {
            $su = \shorturl_type_authenticate::get_by_hash($previous_context_id);
            if ($su && $su instanceof \shorturl_type_authenticate) {
                $context_id = $su->generate_callback_from_url($context, true);
            }
        }
        if (! $context_id) {
            $su = new \shorturl_type_authenticate();
            $context_id = $su->generate_callback_from_url($context);
        }
        $this->context_id = $context_id;
        static::$logger->debug(__METHOD__ . " >> url = " . $url);

        $this->setSessionKey('ext_auth_context_id', $this->context_id);
        static::$logger->debug(__METHOD__ . " >> context_id = " . $this->context_id);
        $this->setSessionKey('ext_auth_config_id', $this->config_id);
        static::$logger->debug(__METHOD__ . " >> config_id = " . $this->config_id);

    }

    /**
     * Recuperation id contexte
     *
     * @return string
     */
    protected function getContextId()
    {
        $context_id = $this->getSessionKey('ext_auth_context_id');
        if (! $context_id) {
            $context_id = '';
        }
        static::$logger->debug(__METHOD__ . " >> " . $context_id);
        return $context_id;
    }

    /**
     * Restauration contexte
     *
     * @return void
     */
    protected function restoreContext()
    {
        if (! $this->context_id) {
            static::$logger->warning(__METHOD__ . " >> no context_id");
            return;
        }

        $su = \shorturl_type_authenticate::get_by_hash($this->context_id);
        if ($su && $su instanceof \shorturl_type_authenticate) {
            $callback_url = $su->get_callback_url();
            static::$logger->debug(__METHOD__ . " >> Redirect to {$callback_url}");
            header('Location: ' . $callback_url, 302);
            exit(0);
        }
        static::$logger->error(__METHOD__ . " >> context_id error");
        return;
    }

    /**
     * Suppression contexte
     *
     * @return void
     */
    protected function deleteContext()
    {
        if (! $this->context_id) {
            static::$logger->warning(__METHOD__ . " >> no context_id");
            return;
        }
        \shorturl_type_authenticate::delete_callback_by_hash($this->context_id);
        static::$logger->error(__METHOD__ . " >> context_id {$this->context_id} deleted");
        return;
    }

    /**
     * Fermeture connexion mysql
     *
     * @return void
     */
    public function closeMySQLConnexionBeforeRedirect()
    {
        static::$logger->debug(__METHOD__);

        \pmb_mysql_close();
    }

    /**
     * Fermeture session
     *
     * @return void
     */
    public function closeSessionBeforeRedirect()
    {
        static::$logger->debug(__METHOD__);

        \session_write_close();
    }

    /**
     * Demarrage session
     *
     * @return void
     */
    public function startSession()
    {
        static::$logger->debug(__METHOD__);

        if ((PHP_SESSION_DISABLED !== session_status()) && (PHP_SESSION_ACTIVE !== session_status())) {
            \session_start();
        }
    }

    /**
     * Enregistrement valeur en session
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function setSessionKey($key, $value)
    {
        static::$logger->debug(__METHOD__ . " key = $key");
        static::$logger->debug(__METHOD__ . " value = $value");

        $this->startSession();
        $_SESSION[$key] = $value;
    }

    /**
     * Lecture valeur en session
     *
     * @param string $key
     * @return string|boolean
     */
    public function getSessionKey($key)
    {
        static::$logger->debug(__METHOD__ . " key = $key");

        $this->startSession();
        if (array_key_exists($key, $_SESSION)) {
            static::$logger->debug(__METHOD__ . " value = ".$_SESSION[$key]);
            return $_SESSION[$key];
        }
        static::$logger->debug(__METHOD__ . " value = NULL");
        return false;
    }

    /**
     * Suppression valeur en session
     *
     * @param string $key
     *
     * @return void
     */
    public function unsetSessionKey($key)
    {
        static::$logger->debug(__METHOD__ . " key = $key");

        $this->startSession();
        unset($_SESSION[$key]);
    }

    /**
     * Generation chaine arbitraire
     *
     * @return string
     */
    public function generateRandomString()
    {
        static::$logger->debug(__METHOD__);

        try {
            return \bin2hex(\random_bytes(16));
        } catch (\Error $e) {
            return \uniqid('', true);
        } catch (\Exception $e) {
            return \uniqid('', true);
        }
    }
    
    /**
     * Gestion de la transformation des arguments
     *
     */
    public function transformation_args()
    {
        global $charset;
        
        $association_process = $this->authentication_params["params"]["association_process"];
        foreach ($association_process as $params) {
            $transfo_class =  "\\Pmb\\Authentication\\Helpers\\Transfo\\" . $params["transfoClass"];
            if (class_exists($transfo_class)) {
                //Instance
                $transfo = new $transfo_class(static::$logger);
                
                //Arguments
                $req_args = [
                    "value" => $this->external_attributes[$params["attr"]],
                    "charset" => $charset
                ];
                
                $this->external_attributes[$params["attr"]] = $transfo->transfo($req_args);
            }
        }
    }

    public function getAuthenticationClass()
    {
        return $this->authentication_class;
    }

}

