<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Auth.php,v 1.9.4.2 2025/05/20 14:00:08 qvarin Exp $

namespace Pmb\Security\Library;

if (stristr($_SERVER['REQUEST_URI'], '/'.basename(__FILE__))) {
    die("no access");
}

use Pmb\Security\Models\GestionLoginAttemptModel;
use Pmb\Security\Models\IpBlackListModel;
use Pmb\Security\Models\IpWhiteListModel;
use Pmb\Security\Models\OpacLoginAttemptModel;
use Pmb\Common\Helper\IPTools;

class Auth
{
    /**
     * Singleton
     *
     * @var Auth|null
     */
    private static $instance = null;

    public const REJECTION_CAUSE_BLACKLIST = 'blacklist';

    public const REJECTION_CAUSE_TOO_MANY_ATTEMPTS = 'too_many_attempts';

    public const DEFAULT_CONFIG = [
        'active_log_login_attempts' => false,
        'log_retention' => 1,
        'block_after_failures' => 5,
        'block_duration' => 180,
        'notify_after_failures' => 5,
    ];

    /**
     * Tentatives de connexion
     *
     * @var LoginAttemptInterface
     */
    private $loginAttempt;

    /**
     * Liste blanche
     *
     * @var ListInterface
     */
    private $whiteList;

    /**
     * Liste noire
     *
     * @var ListInterface
     */
    private $blackList;

    /**
     * Adresse IP
     *
     * @var string
     */
    private $ip;

    /**
     * Login
     *
     * @var string
     */
    private $login;

    /**
     * Active le log des tentatives
     *
     * @var bool
     */
    private $activeLogLoginAttempts = Auth::DEFAULT_CONFIG['active_log_login_attempts'];

    /**
     * Duree de conservation des log en mois
     *
     * @var integer
     */
    private $logRetention = Auth::DEFAULT_CONFIG['log_retention'];

    /**
     * Nombre de tentatives de tentatives en echec avant de bloquer
     *
     * @var integer
     */
    private $blockAfterFailures = Auth::DEFAULT_CONFIG['block_after_failures'];

    /**
     * Duree de blocage (en secondes)
     *
     * @var integer
     */
    private $blockDuration = Auth::DEFAULT_CONFIG['block_duration'];

    /**
     * Nombre de tentatives de tentatives en echec avant de notifier
     *
     * @var integer
     */
    private $notifyAfterFailures = Auth::DEFAULT_CONFIG['notify_after_failures'];

    /**
     * Indicateur de rejet
     *
     * @var string : (vide) | blacklist | too_many_attempts
     */
    private $rejectionCause = '';

    /**
     * Constructeur
     *
     * @param LoginAttemptInterface $loginAttempt
     * @param ListInterface $whiteList
     * @param ListInterface $blackList
     * @param string $login
     * @param string $ip
     */
    private function __construct(
        LoginAttemptInterface $loginAttempt,
        ListInterface $whiteList,
        ListInterface $blackList,
        string $login,
        string $ip
    ) {

        $this->loginAttempt = $loginAttempt;
        $this->whiteList = $whiteList;
        $this->blackList = $blackList;

        $this->login = $login;
        $this->ip = !empty($ip) ? $ip : IPTools::getIP();

        $this->loadParameters();
        $this->loginAttempt->cleanLogs($this->logRetention);
        $this->isAuthorized();
    }

    /**
     * Mise a jour du login
     *
     * @param string $login
     * @return void
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
        $this->isAuthorized();
    }
    /**
     * Chargement des parametres
     *
     * @return void
     */
    private function loadParameters()
    {
        if (defined('GESTION')) {

            global $pmb_active_log_login_attempts, $pmb_log_retention;
            global $pmb_block_after_failures, $pmb_block_duration, $pmb_notify_after_failures;

            if (isset($pmb_log_retention)) {
                $this->activeLogLoginAttempts = boolval($pmb_active_log_login_attempts);
            }
            if (isset($pmb_log_retention)) {
                $this->logRetention = intval($pmb_log_retention);
            }
            if (isset($pmb_block_after_failures)) {
                $this->blockAfterFailures = intval($pmb_block_after_failures);
            }
            if (isset($pmb_block_duration)) {
                $this->blockDuration = intval($pmb_block_duration);
            }
            if (isset($pmb_notify_after_failures)) {
                $this->notifyAfterFailures = intval($pmb_notify_after_failures);
            }

        } else {

            global $opac_active_log_login_attempts, $opac_log_retention;
            global $opac_block_after_failures, $opac_block_duration, $opac_notify_after_failures;

            if (isset($opac_active_log_login_attempts)) {
                $this->activeLogLoginAttempts = boolval($opac_active_log_login_attempts);
            }
            if (isset($opac_log_retention)) {
                $this->logRetention = intval($opac_log_retention);
            }
            if (isset($opac_block_after_failures)) {
                $this->blockAfterFailures = intval($opac_block_after_failures);
            }
            if (isset($opac_block_duration)) {
                $this->blockDuration = intval($opac_block_duration);
            }
            if (isset($opac_notify_after_failures)) {
                $this->notifyAfterFailures = intval($opac_notify_after_failures);
            }
        }
    }

    /**
     * Constructeur
     *
     * @param string|null $login
     * @param string $ip
     * @return Auth
     */
    public static function getInstance(?string $login = '', string $ip = ''): Auth
    {
        $login = $login ?? '';

        if (! is_null(self::$instance)) {
            self::$instance->setLogin($login);
            return self::$instance;
        }

        if (defined('GESTION')) {
            $loginAttempt = new GestionLoginAttemptModel();
        } else {
            $loginAttempt = new OpacLoginAttemptModel();
        }

        $whiteList = new IpWhiteListModel();
        $blackList = new IpBlackListModel();
        self::$instance = new Auth(
            $loginAttempt,
            $whiteList,
            $blackList,
            $login,
            $ip
        );
        return self::$instance;
    }

    /**
     * Verifie si l'IP est en liste noire
     *
     * @return boolean
     */
    public function isInBlackList(): bool
    {
        if (!$this->activeLogLoginAttempts) {
            return false;
        }
        $r = $this->blackList->isInList($this->ip);
        if ($r) {
            $this->rejectionCause = Auth::REJECTION_CAUSE_BLACKLIST;
        }
        return $r;
    }

    /**
     * Verifie si l'IP est en liste blanche
     *
     * @return boolean
     */
    public function isInWhiteList(): bool
    {
        if (!$this->activeLogLoginAttempts) {
            return true;
        }
        return $this->whiteList->isInList($this->ip);
    }

    /**
     * Verifie si l'utilisateur est autorise
     *
     * @return boolean
     */
    public function isAuthorized(): bool
    {
        // Verification non activee ou Liste blanche
        if (!$this->activeLogLoginAttempts || $this->isInWhiteList()) {
            return true;
        }

        // Liste noire ou trop d'essais
        if ($this->isInBlackList()) {
            $this->rejectionCause = Auth::REJECTION_CAUSE_BLACKLIST;
            return false;
        }

        if ($this->hasToManyFailedAttempts()) {
            $this->rejectionCause = Auth::REJECTION_CAUSE_TOO_MANY_ATTEMPTS;
            return false;
        }
        return true;
    }

    /**
     * Verifie si l'utilisateur est rejete
     *
     * @return boolean
     */
    public function isRejected(): bool
    {
        return !empty($this->rejectionCause);
    }

    /**
     * Renvoie la cause du rejet
     *
     * @return string
     */
    public function getRejectCause(): string
    {
        global $msg;

        switch ($this->rejectionCause) {
            case Auth::REJECTION_CAUSE_BLACKLIST:
                return $msg["login_rejected_blacklist"];
            case Auth::REJECTION_CAUSE_TOO_MANY_ATTEMPTS:
                return sprintf($msg["login_rejected_too_many_attempts"], $this->blockDuration);
            default:
                return '';
        }
    }

    /**
     * Indique si le nombre de tentatives en echec est superieur au parametre
     *
     * @return bool
     */
    private function hasToManyFailedAttempts(): bool
    {
        if ($this->loginAttempt->countFailedForLogin($this->ip, $this->login, $this->blockDuration) >= $this->blockAfterFailures) {
            return true;
        }
        return false;
    }

    /**
     * Renvoie le nombre de tentatives restantes
     *
     * @return int
     */
    public function getRemainingAttempts(): int
    {
        $count = $this->blockAfterFailures - $this->loginAttempt->countFailedForLogin($this->ip, $this->login, $this->blockDuration);
        return ($count <= 0 ? 0 : $count);
    }

    /**
     * Renvoie le message de tentatives restantes
     *
     * @return string
     */
    public function getRemainingAttemptsMessage(): string
    {
        global $msg;

        if ($this->getRemainingAttempts() == 0) {
            return $msg["last_login_remaining_attempts"];
        }
        return sprintf($msg["login_remaining_attempts"], $this->getRemainingAttempts());
    }

    /**
     * Envoie une notification si le nombre de tentatives en echec est superieur au parametre
     *
     * @return void
     */
    private function notify()
    {
        if (!$this->activeLogLoginAttempts) {
            return;
        }

        $countFailedAttempts = $this->loginAttempt->countFailedForLogin($this->ip, $this->login, $this->blockDuration);
        if ($countFailedAttempts >= $this->notifyAfterFailures) {
            $result = pmb_mysql_query('SELECT userid, user_lang FROM users WHERE user_email != "" AND param_notify_login_failed = 1');
            if (pmb_mysql_num_rows($result)) {

                while ($user = pmb_mysql_fetch_assoc($result)) {
                    $user['user_lang'] = empty($user['user_lang']) ? 'fr_FR' : $user['user_lang'];

                    $mail_user = \mail_user_auth::get_instance();
                    $mail_user->set_mail_to_id($user['userid']);
                    $mail_user->load_message($user['user_lang']);

                    $msg = \mail_user_auth::get_language_messages($user['user_lang']);
                    $mail_user->set_mail_object($msg['notify_login_failed_title']);
                    $mail_user->set_mail_content(str_replace(
                        [ '!!login!!', '!!ip!!', '!!where!!', ],
                        [ $this->login, $this->ip, defined('GESTION') ? $msg['gestion'] : $msg['opac'], ],
                        $msg['notify_login_failed_content']
                    ));

                    $mail_user->send_mail();
                }
            }

            if (defined('GESTION')) {
                $result = pmb_mysql_query('SELECT userid, user_lang FROM users WHERE user_email != "" AND username = "' . addslashes($this->login) . '"');
                if (pmb_mysql_num_rows($result)) {
                    $user = pmb_mysql_fetch_assoc($result);
                    $user['user_lang'] = empty($user['user_lang']) ? 'fr_FR' : $user['user_lang'];

                    $mail_user = \mail_user_auth::get_instance();
                    $mail_user->set_mail_to_id($user['userid']);
                    $mail_user->load_message($user['user_lang']);

                    $msg = \mail_user_auth::get_language_messages($user['user_lang']);
                    $mail_user->set_mail_object($msg['notify_login_failed_title_for_user']);
                    $mail_user->set_mail_content($msg['notify_login_failed_content_for_user']);

                    $mail_user->send_mail();
                }
            } else {
                $result = pmb_mysql_query('SELECT id_empr, empr_lang FROM empr WHERE empr_mail != "" AND empr_login = "' . addslashes($this->login) . '"');
                if (pmb_mysql_num_rows($result)) {
                    $empr = pmb_mysql_fetch_assoc($result);
                    $empr['empr_lang'] = empty($empr['empr_lang']) ? 'fr_FR' : $empr['empr_lang'];

                    $mail_empr = \mail_empr_auth::get_instance();
                    $mail_empr->set_mail_to_id($empr['id_empr']);
                    $mail_empr->load_message($empr['empr_lang']);

                    $msg = \mail_empr_auth::get_language_messages($empr['empr_lang']);
                    $mail_empr->set_mail_object($msg['notify_login_failed_title_for_user']);
                    $mail_empr->set_mail_content($msg['notify_login_failed_content_for_user']);
                    $mail_empr->send_mail();
                }
            }
        }
    }

    /**
     * Enregistre une tentative de connexion.
     *
     * @param boolean $success
     * @return void
     */
    public function logAttempt(bool $success): void
    {
        if ($this->activeLogLoginAttempts) {
            $this->loginAttempt->log($this->ip, $this->login, $success);

            $this->notify();
        }
    }

}
