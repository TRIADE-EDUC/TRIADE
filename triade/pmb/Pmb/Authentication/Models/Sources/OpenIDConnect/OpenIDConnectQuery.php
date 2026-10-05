<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpenIDConnectQuery.php,v 1.6 2024/02/14 10:45:42 tsamson Exp $

namespace Pmb\Authentication\Models\Sources\OpenIDConnect;

use Exception;
use Pmb\Authentication\Common\AbstractQuery;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Authentication\Models\AuthenticationHandler;
use Pmb\Authentication\Models\Sources\OpenIDConnect\OpenIDConnectClient;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class OpenIDConnectQuery extends AbstractQuery implements AuthenticationQueryInterface
{

    // Parametres
    protected $login_modes = [
        'redirect'
    ];

    protected $charset = 'utf-8';

    protected $client_id = '';

    protected $client_secret = '';

    protected $provider_url = '';

    protected $authorization_endpoint = '';

    protected $token_endpoint = '';

    protected $userinfo_endpoint = '';

    protected $logout_endpoint = '';

    protected $redirect_uri = '';

    protected $redirect_uri_relay = '';

    protected $logout_redirect_uri = '';

    protected $logout_redirect_uri_relay = '';

    protected $response_type = '';

    // Scopes
    protected $scopes = [
        "profile"
    ];

    // Revendications name=value
    protected $claims = [];

    // Methodes d'authentification possibles
    public const ALLOWED_AUTHENTICATION_METHODS = [
        'client_secret_basic',
        'private_key_jwt',
        'client_secret_jwt',
    ];

    // Methode d'authentification definie
    protected $authentication_method = 'client_secret_basic';

    // Verification de la signature des jetons
    protected $check_signatures = true;

    // Nom de l'identifiant externe
    protected $login_attr = 'userPrincipalName';


    /**
     * AuthenticationQuery implementation *
     */

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\Authentication\Interfaces\AuthenticationQueryInterface::setParams()
     */
    public function setParams($params = array())
    {
        $valid_params = [];
        if (! is_array($params) || empty($params)) {
            static::$logger->debug(__METHOD__ . " >> " . print_r($valid_params, true));
            return;
        }

        foreach ($params as $p_name => $p_value) {

            if (property_exists($this, $p_name)) {

                switch ($p_name) {
                    case 'claims' :
                        $tokens = explode(",", $p_value);
                        foreach($tokens as $token) {
                            $claim = explode('=', $token);
                            $this->{$p_name}[$claim[0]] = $claim[1];
                            $valid_params[$p_name][$claim[0]] = $claim[1];
                        }
                        break;
                    case 'scopes':
                        $tokens = explode(",", $p_value);
                        $this->{$p_name} = $tokens;
                        $valid_params[$p_name] = $tokens;
                        break;
                    default:
                        $this->{$p_name} = $p_value;
                        $valid_params[$p_name] = $p_value;
                        break;
                }
            }
        }
        static::$logger->debug(__METHOD__ . " >> " . print_r($valid_params, true));
    }

    /**
     * Lancement authentification (mode redirect)
     *
     * @param AuthenticationHandler $caller
     *
     * @return void
     */
    public function runExternalLoginRedirect(AuthenticationHandler $caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;

        $client = new OpenIDConnectClient($this->provider_url, $this->client_id, $this->client_secret);
        if ($this->authorization_endpoint) {
            $client->providerConfigParam([
                'authorization_endpoint' => $this->authorization_endpoint
            ]);
        }
        if ($this->token_endpoint) {
            $client->providerConfigParam([
                'token_endpoint' => $this->token_endpoint
            ]);
        }

        if ($this->redirect_uri_relay) {
            $client->setRedirectURL($this->redirect_uri_relay);
        } elseif ($this->redirect_uri) {
            $client->setRedirectURL($this->redirect_uri);
        }
        if ($this->response_type) {
            $client->setResponseTypes($this->response_type);
        } else {
            $client->setResponseTypes("code");
        }
        $client->addScope($this->scopes);

        $client->addAuthParam($this->claims);

        $this->caller->closeSessionBeforeRedirect();
        $this->caller->closeMySQLConnexionBeforeRedirect();

        try {
            $client->authenticate();
        } catch (Exception $e) {
            static::$logger->error($e->getMessage());
        }
    }

    /**
     * Detection retour d'authentification (redirect)
     *
     * @param AuthenticationHandler $caller
     *
     * @return bool
     */
    public function detectExternalLoginReturn(AuthenticationHandler $caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;

        if (empty($_GET['code']) || empty($_GET['state'])) {
            static::$logger->debug(__METHOD__ . " >> N");
            return false;
        }
        static::$logger->debug(__METHOD__ . " >> O");
        return true;
    }


    /**
     * Traitement retour authentification (redirect)
     *
     * @param AuthenticationHandler $caller
     *
     * @return bool
     */
    public function runExternalLoginReturn(AuthenticationHandler $caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;

        $client = new OpenIDConnectClient($this->provider_url, $this->client_id, $this->client_secret);
        if ($this->authorization_endpoint) {
            $client->providerConfigParam([
                'authorization_endpoint' => $this->authorization_endpoint
            ]);
        }
        if ($this->token_endpoint) {
            $client->providerConfigParam([
                'token_endpoint' => $this->token_endpoint
            ]);
        }

        if ($this->userinfo_endpoint) {
            $client->providerConfigParam([
                'userinfo_endpoint' => $this->userinfo_endpoint
            ]);
        }

        if (empty($this->check_signatures) ) {
            $client->checkSignatures(false);
        }

        if (isset($this->authentication_method) && in_array($this->authentication_method, static::ALLOWED_AUTHENTICATION_METHODS) ) {
            $client->setTokenEndpointAuthMethodsSupported($this->authentication_method);
        }

        if ($this->redirect_uri_relay) {
            $client->setRedirectURL($this->redirect_uri_relay);
        } elseif ($this->redirect_uri) {
            $client->setRedirectURL($this->redirect_uri);
        }

        if ($this->response_type) {
            $client->setResponseTypes($this->response_type);
        } else {
            $client->setResponseTypes("code");
        }

        $client->addScope($this->scopes);

        try {

            $client->authenticate();

            //Recuperation infos utilisateur
            $response = (array) $client->requestUserInfo();

            if (! empty($response)) {

                $this->external_attributes = $response;
                if ($response[$this->login_attr]) {
                    $this->external_user = $response[$this->login_attr];
                }
            }

            //Lecture Id token pour la deconnexion
            $this->external_attributes['id_token'] = $client->getIdToken();

        } catch (Exception $e) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            static::$logger->error(__METHOD__ . " >> " . $e->getMessage());
            return false;
        }

        static::$logger->debug(__METHOD__ . " >> OK ");
        return true;
    }

    /**
     * Lancement logout (mode redirect)
     *
     * @param AuthenticationHandler $caller
     *
     * @return void
     */
    public function runExternalLogout($caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;

        //Pour se deconnecter, on recupere l'id_token fourni lors de la connexion
        $this->external_attributes = $caller->getSessionKey('ext_auth_attrs');
        if( empty($this->external_attributes['id_token']) ) {
            return false;
        }
        $id_token = $this->external_attributes['id_token'];

        //URL de redirection apres deconnexion
        $logout_redirect_uri = null;
        if ($this->logout_redirect_uri_relay) {
            $logout_redirect_uri = $this->logout_redirect_uri_relay;
        } elseif ($this->logout_redirect_uri) {
            $logout_redirect_uri = $this->logout_redirect_uri;
        }

        $client = new OpenIDConnectClient($this->provider_url, $this->client_id, $this->client_secret);
        if($this->logout_endpoint) {
            $client->providerConfigParam([
                'end_session_endpoint' => $this->logout_endpoint,
            ]);
        }

        $this->caller->closeSessionBeforeRedirect();
        $this->caller->closeMySQLConnexionBeforeRedirect();

        try {
            $client->signOut($id_token, $logout_redirect_uri);
        } catch (Exception $e) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            static::$logger->error(__METHOD__ . " >> " . $e->getMessage());
            return false;
        }
    }

    public function runExternalLogoutReturn($caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;
    }
}

