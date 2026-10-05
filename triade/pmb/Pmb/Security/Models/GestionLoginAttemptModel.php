<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GestionLoginAttemptModel.php,v 1.6.4.2 2025/05/20 14:00:07 qvarin Exp $

namespace Pmb\Security\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use DateTime;
use Pmb\Security\Library\LoginAttemptInterface;
use Pmb\Security\Orm\GestionLoginAttemptOrm;

class GestionLoginAttemptModel implements LoginAttemptInterface
{
    /**
     * Id de l'utilisateur
     *
     * @var int
     */
    private $id;

    /**
     * ORM
     *
     * @var GestionLoginAttemptOrm
     */
    private $orm;

    /**
     * Constructeur
     *
     * @param integer $id
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->orm = new GestionLoginAttemptOrm($this->id);
    }

    /**
     * Enregistre le login
     *
     * @param string $ip
     * @param string $login
     * @param boolean $success
     * @return void
     */
    public function log(string $ip, string $login, bool $success): void
    {
        $this->orm->gestion_login_attempt_ip = $ip;
        $this->orm->gestion_login_attempt_login = $login;
        $this->orm->gestion_login_attempt_success = $success ? 1 : 0;
        $this->orm->gestion_login_attempt_time = date('Y-m-d H:i:s');

        $this->orm->save();
    }

    /**
     * Compte le nombre de tentatives
     *
     * @param string $ip
     * @param integer $second
     * @return integer
     */
    public function countFailed(string $ip, int $second): int
    {
        $datetime = new DateTime();
        $datetime->sub(new \DateInterval('PT' . $second . 'S'));
        $datetime->setTime($datetime->format('H'), $datetime->format('i'), 0);

        return $this->orm->countFailed($ip, $datetime);
    }

    /**
     * Compte le nombre de tentatives
     *
     * @param string $ip
     * @param string $login
     * @param integer $second
     * @return integer
     */
    public function countFailedForLogin(string $ip, string $login, int $second): int
    {
        $datetime = new DateTime();
        $datetime->sub(new \DateInterval('PT' . $second . 'S'));
        $datetime->setTime($datetime->format('H'), $datetime->format('i'), 0);

        return $this->orm->countFailedForLogin($ip, $login, $datetime);
    }

    /**
     * Retourne le dernier login
     *
     * @param string $ip
     * @return LoginAttemptInterface
     */
    public function getLastFailed(string $ip): LoginAttemptInterface
    {
        $orm = $this->orm->fetchLastFailed($ip);
        return new GestionLoginAttemptModel($orm->id_gestion_login_attempt);
    }

    /**
     * Retourne le timestamp du login
     *
     * @return integer
     */
    public function getTimestamp(): int
    {
        $datetime = new DateTime($this->orm->gestion_login_attempt_time);
        return $datetime->getTimestamp();
    }

    /**
     * Nettoie les logs
     *
     * @param integer $month
     * @return void
     */
    public function cleanLogs(int $month): void
    {
        $datetime = new DateTime();
        $datetime->sub(new \DateInterval('P'.$month.'M'));

        $this->orm->cleanLogs($datetime);
    }

}
