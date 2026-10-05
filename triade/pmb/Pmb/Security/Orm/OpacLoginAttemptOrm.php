<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpacLoginAttemptOrm.php,v 1.3.4.2 2025/05/20 14:00:08 qvarin Exp $

namespace Pmb\Security\Orm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use DateTime;
use Pmb\Common\Orm\Orm;

class OpacLoginAttemptOrm extends Orm
{
    public static $instances = [];

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     *
     * @var string
     */
    public static $tableName = "opac_login_attempt";

    /**
     *
     * @var string
     */
    public static $idTableName = "id_opac_login_attempt";

    public $id_opac_login_attempt = 0;

    public $opac_login_attempt_ip = '';

    public $opac_login_attempt_login = '';

    public $opac_login_attempt_time = '';

    public $opac_login_attempt_success = 0;

    public const FORMAT_ATTEMPT_TIME = 'Y-m-d H:i:s';

    /**
     * Compte le nombre d'essais d'un utilisateur
     *
     * @param string $ip
     * @param DateTime $date
     * @return integer
     */
    public function countFailed(string $ip, DateTime $date): int
    {
        $result = pmb_mysql_query(
            'SELECT COUNT(' . self::$idTableName . ') FROM opac_login_attempt
                WHERE opac_login_attempt_ip = "'. addslashes($ip) .'"
                AND opac_login_attempt_success = 0
                AND opac_login_attempt_time > "'. $date->format(static::FORMAT_ATTEMPT_TIME) .'"
                AND opac_login_attempt_time <= now()
                '
        );

        if (!$result) {
            return 0;
        }
        return pmb_mysql_result($result, 0, 0);
    }

    /**
     * Compte le nombre d'essai d'un utilisateur
     *
     * @param string $ip
     * @param string $login
     * @param DateTime $date
     * @return integer
     */
    public function countFailedForLogin(string $ip, string $login, DateTime $date): int
    {
        $result = pmb_mysql_query(
            'SELECT COUNT(' . self::$idTableName . ') FROM opac_login_attempt
                WHERE opac_login_attempt_login = "'. addslashes($login) . '"
                AND opac_login_attempt_ip = "'. addslashes($ip) .'"
                AND opac_login_attempt_success = 0
                AND opac_login_attempt_time > "'. $date->format(static::FORMAT_ATTEMPT_TIME) .'"
                AND opac_login_attempt_time <= now()
                '
        );

        if (!$result) {
            return 0;
        }
        return pmb_mysql_result($result, 0, 0);
    }

    /**
     * Recupere le dernier essai
     *
     * @param string $ip
     * @return OpacLoginAttemptOrm
     */
    public function fetchLastFailed(string $ip): OpacLoginAttemptOrm
    {
        $result = pmb_mysql_query(
            'SELECT ' . self::$idTableName . ' FROM opac_login_attempt WHERE opac_login_attempt_ip = "'. addslashes($ip) .'"
                ORDER BY opac_login_attempt_time DESC LIMIT 1'
        );

        if (pmb_mysql_num_rows($result)) {
            $id = pmb_mysql_result($result, 0, 0);
        } else {
            $id = 0;
        }

        return new OpacLoginAttemptOrm($id);
    }

    /**
     * Nettoyage des logs
     *
     * @param DateTime $date
     * @return void
     */
    public function cleanLogs(DateTime $date)
    {
        pmb_mysql_query('DELETE FROM opac_login_attempt WHERE opac_login_attempt_time < "' . $date->format('Y-m-d H:i:s') . '"');
    }
}
