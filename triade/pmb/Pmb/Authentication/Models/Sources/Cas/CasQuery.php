<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CasQuery.php,v 1.2 2023/06/19 12:44:25 dbellamy Exp $

namespace Pmb\Authentication\Models\Sources\Cas;

if (stristr($_SERVER['REQUEST_URI'], "CasQuery.php"))
    die("no access");

use phpCAS;
use Exception;
use Pmb\Authentication\Common\AuthenticationCommons;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Authentication\Models\AuthenticationHandler;

class CasQuery extends AuthenticationCommons implements AuthenticationQueryInterface
{

    // Parametres
    protected $login_modes = [
        'redirect'
    ];

    protected $charset = 'utf-8';

    protected $redirect_uri = '';

    protected $host = 'https://localhost';

    protected $uri = '';

    protected $port = 443;

    protected $version = '3.0';

    const VERSIONS = [
        '1.0',
        '2.0',
        '3.0',
        'S1'
    ];

    protected $cas_server_ca_cert = '';

    protected $saml_validate_url = '';

    protected $login_attr = 'userPrincipalName';

    protected $debug = 0;

    protected $log_file = __DIR__ . "/../logs/authentication.log";

    protected $logout_allowed = 0;

    protected $redirect_uri_logout = '';

    protected $cas_server_logout_allow_from = array();

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
                    case 'version':
                        if (in_array($p_value, self::VERSIONS)) {
                            $this->{$p_name} = $p_value;
                            $valid_params[$p_name] = $p_value;
                        }
                        break;
                    case 'cas_server_ca_cert':
                        $this->{$p_name} = __DIR__ . '/certs/' . $p_value;
                        $valid_params[$p_name] = $p_value;
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

        try {

            $this->setCasDebug();

            \phpCAS::client($this->version, $this->host, (int) $this->port, $this->uri, true);

            $client = \phpCAS::getCasClient();
            $client->setURL($this->redirect_uri);

            if ($this->saml_validate_url) {
                $client->setServerSamlValidateURL($this->saml_validate_url);
            }
            if ($this->cas_server_ca_cert) {
                $client->setCasServerCACert($this->cas_server_ca_cert);
            } else {
                $client->setNoCasServerValidation();
            }

            $caller->closeMySQLConnexionBeforeRedirect();
            $caller->closeSessionBeforeRedirect();

            $client->forceAuthentication();
        } catch (Exception $e) {
            static::$logger->error($e->getMessage());
        }
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

        try {

            $this->setCasDebug();

            \phpCAS::client($this->version, $this->host, (int) $this->port, $this->uri, true);

            $client = \phpCAS::getCasClient();
            $client->setURL($this->redirect_uri);

            if ($this->saml_validate_url) {
                $client->setServerSamlValidateURL($this->saml_validate_url);
            }
            if ($this->cas_server_ca_cert) {
                $client->setCasServerCACert($this->cas_server_ca_cert);
            } else {
                $client->setNoCasServerValidation();
            }

            $response = $client->forceAuthentication();

            if ($response) {
                $this->external_user = $client->getUser();
                $this->external_attributes = $client->getAttributes();
            }
        } catch (Exception $e) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            static::$logger->error($e->getMessage());
            return false;
        }

        static::$logger->debug(__METHOD__ . " >> OK ");
        return true;
    }

    /**
     * Demarrage logs CAS
     *
     * @return void
     */
    protected function setCasDebug()
    {
        static::$logger->debug(__METHOD__);

        if ($this->debug && $this->log_file) {
            \phpCAS::setDebug($this->log_file);
        }
    }

    /**
     * Traitement logout (internal)
     *
     * @param AuthenticationHandler $caller
     *
     * @return bool
     */
    public function runInternalLogout(AuthenticationHandler $caller)
    {
        static::$logger->debug(__METHOD__);

        $this->caller = $caller;

        try {

            $this->setCasDebug();

            \phpCAS::client($this->version, $this->host, (int) $this->port, $this->uri, true);

            $client = \phpCAS::getCasClient();
            $client->setURL($this->redirect_uri);

            if ($this->saml_validate_url) {
                $client->setServerSamlValidateURL($this->saml_validate_url);
            }
            if ($this->cas_server_ca_cert) {
                $client->setCasServerCACert($this->cas_server_ca_cert);
            } else {
                $client->setNoCasServerValidation();
            }
            if ($this->logout_allowed) {
                if (is_array($this->cas_server_logout_allow_from) && count($this->cas_server_logout_allow_from)) {
                    $client->handleLogoutRequests(true, $this->cas_server_logout_allow_from);
                } else {
                    $client->handleLogoutRequests(false);
                }
                if ($this->redirect_uri_logout) {
                    \phpCAS::logoutWithRedirectService($this->redirect_uri_logout);
                } else {
                    $client->logout();
                }
            }
        } catch (Exception $e) {
            static::$logger->debug(__METHOD__ . " >> KO ");
            static::$logger->error($e->getMessage());
            return false;
        }
    }
}
