<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SspQuery.php,v 1.3 2023/07/13 13:14:57 dbellamy Exp $

namespace Pmb\Authentication\Models\Sources\Ssp;

use Exception;
use Pmb\Authentication\Common\AbstractQuery;
use Pmb\Authentication\Interfaces\AuthenticationQueryInterface;
use Pmb\Authentication\Models\AuthenticationHandler;
use \SimpleSAML\Auth\Simple;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SspQuery extends AbstractQuery implements AuthenticationQueryInterface
{

    // Parametres
    protected $login_modes = [
        'redirect'
    ];

    protected $charset = 'utf-8';

    protected $autoload_file = "/var/www/html/ssp/lib/_autoload.php";

    protected $service_provider = '';

    protected $redirect_uri = '';

    protected $redirect_uri_logout = '';

    protected $login_attr = 'userPrincipalName';

    /**
     * AuthenticationQuery implementation *
     */

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

        if (! is_readable($this->autoload_file)) {
            static::$logger->error(__METHOD__ . " >> autoload simplesamlphp");
            return;
        }
        if (empty($this->service_provider)) {
            static::$logger->error(__METHOD__ . " >> service provider undefined");
            return;
        }

        $this->caller = $caller;

        $caller->closeMySQLConnexionBeforeRedirect();
        $caller->closeSessionBeforeRedirect();

        try {

            require_once $this->autoload_file;

            $ssp = new \SimpleSAML\Auth\Simple($this->service_provider);
            if ($ssp->isAuthenticated()) {
                header('Location: ' . $this->redirect_uri, 302);
                die('Redirect');
            }
            $ssp->requireAuth([
                'ReturnTo' => $this->redirect_uri
            ]);
            static::$logger->debug(print_r($ssp->getAttibutes()));
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

        if (! is_readable($this->autoload_file)) {
            static::$logger->error(__METHOD__ . " >> autoload simplesamlphp");
            return;
        }
        if (empty($this->service_provider)) {
            static::$logger->error(__METHOD__ . " >> service provider undefined");
            return;
        }

        $this->caller = $caller;

        try {

            require_once $this->autoload_file;

            $ssp = new \SimpleSAML\Auth\Simple($this->service_provider);
            if ($ssp->isAuthenticated()) {
                $this->external_attributes = $ssp->getAttributes();

                if (empty($this->external_user)) {
                    $this->external_user = trim($ssp->getAuthData("saml:sp:NameID")->getValue());
                }
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
     * Traitement logout (internal)
     *
     * @param AuthenticationHandler $caller
     *
     * @return bool
     */
    public function runInternalLogout(AuthenticationHandler $caller)
    {
        static::$logger->debug(__METHOD__);

        if (! is_readable($this->autoload_file)) {
            static::$logger->error(__METHOD__ . " >> autoload simplesamlphp");
            return;
        }

        if (empty($this->service_provider)) {
            static::$logger->error(__METHOD__ . " >> service provider undefined");
            return;
        }

        $this->caller = $caller;

        try {

            require_once $this->autoload_file;

            $ssp = new \SimpleSAML\Auth\Simple($this->service_provider);
            $ssp->logout([
                'ReturnTo' => $this->redirect_uri_logout,
                'ReturnStateParam' => 'LogoutState',
                'ReturnStateStage' => 'LoggedOut'
            ]);
        } catch (Exception $e) {

            static::$logger->debug(__METHOD__ . " >> KO ");
            static::$logger->error($e->getMessage());
            return false;
        }
    }
}
